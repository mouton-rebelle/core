<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos;

class Controller_Admin_Media_Media extends Controller {

	public function action_add($folder_id = null) {

        // Find root folder ID
        if (empty($folder_id)) {
            $query = Model_Media_Folder::find();
            $query->where(array('medif_parent_id' => null));
            $root = $query->get_one();
            $folder_id = $root->medif_id;
            $hide_widget_media_path = false;
        } else {
            $hide_widget_media_path = true;
        }

		$folder = Model_Media_Folder::find($folder_id);
        $fields = \Config::load('nos::controller/admin/media/form_media', true);

        $fields = \Arr::merge($fields, array(
            'media_folder_id' => array(
                'widget' => $hide_widget_media_path ? null : 'Nos\Widget_Media_Folder',
		        'form' => array(
			        'value' => $folder->medif_id,
		        ),
	        ),
            'save' => array(
                'form' => array(
                    'value' => __('Add'),
                )
            ),
        ));

        $fieldset = \Fieldset::build_from_config($fields);

		return \View::forge('nos::admin/media/media_add', array(
            'fieldset' => $fieldset,
            'folder' => $folder,
            'hide_widget_media_path' => $hide_widget_media_path,
		), false);
	}

	public function action_edit($media_id = null) {

		$media = Model_Media::find($media_id);
        $pathinfo = pathinfo($media->media_file);
        $ext = $pathinfo['extension'];
        $filename = $pathinfo['filename'];

        $fields = \Config::load('nos::controller/admin/media/form_media', true);

        $fields = \Arr::merge($fields, array(
            'media_id' => array(
                'form' => array(
                    'type' => 'hidden',
                    'value' => $media->media_id,
                ),
            ),
            'media_folder_id' => array(
                'widget' =>  'Nos\Widget_Media_Folder',
                'form' => array(
                    'value' => $media->media_folder_id,
                ),
            ),
            'media' => array(
                'label' => __('Change the file:'),
            ),
            'media_title' => array(
                'form' => array(
                    'value' => $media->media_title,
                ),
            ),
            'slug' => array(
                'form' => array(
                    'value' => $filename,
                ),
            ),
        ));

        $fieldset = \Fieldset::build_from_config($fields);

		return \View::forge('nos::admin/media/media_edit', array(
            'fieldset' => $fieldset,
            'media' => $media,
            'checked' => $filename == Model_Media_Folder::friendly_slug($media->media_title),
		), false);
	}

	public function action_upload() {

        if (!static::check_permission_action('add', 'controller/admin/media/appdesk/list')) {
            throw new \Exception(__('Permission denied'));
        }

        $is_uploaded = isset($_FILES['media']) and is_uploaded_file($_FILES['media']['tmp_name']);

        try {
            if (!$is_uploaded) {
                throw new \Exception(__('Please pick a file from your hard drive.'));
            }

            $pathinfo = pathinfo(mb_strtolower($_FILES['media']['name']));

            $disallowed_extensions = \Config::get('upload.disabled_extensions', array('php'));
            if (in_array($pathinfo['extension'], $disallowed_extensions)) {
                throw new \Exception(__('This extension is not allowed due to security reasons.'));
            }

            $media = new Model_Media();

            $media->media_folder_id     = \Input::post('media_folder_id', 1);
            $media->media_application = \Input::post('media_application', null);

            $media->media_title = \Input::post('media_title', '');
            $media->media_file  = \Input::post('slug', '');

            // Empty title = auto-generated from file name
            if (empty($media->media_title)) {
                $media->media_title = static::pretty_title($pathinfo['basename']);
            }

            // Empty slug = auto-generated with title
            if (empty($media->media_file)) {
                $media->media_file  = $media->media_title;
            }
            if (!empty($pathinfo['extension'])) {
                $media->media_file .= '.'.$pathinfo['extension'];
            }

            if (false === $media->check_and_filter_slug()) {
                throw new \Exception(__('Generated media URL (SEO) was empty.'));
            }
            if (false === $media->refresh_path()) {
                throw new \Exception(__("The parent folder doesn't exists."));
            }

            $dest = APPPATH.$media->get_private_path();
            if (is_file($dest)) {
                throw new \Exception(__('A file with the same name already exists.'));
            }

            // Create the directory if needed
			$dest_dir = dirname($dest).'/';
            $base_dir = APPPATH.\Nos\Model_Media::$private_path;
            $remaining_dir = str_replace($base_dir, '', $dest_dir);
            // chmod  is 0777 here because it should be restricted with by the umask
			is_dir($dest_dir) or \File::create_dir($base_dir, $remaining_dir, 0777);

            if (!is_writeable($dest_dir)) {
                throw new \Exception(__('No write permission. This is not your fault, but rather a misconfiguration from the server admin. Tell her/him off!'));
            }

            // Move the file
            if (move_uploaded_file($_FILES['media']['tmp_name'], $dest)) {
                $media->save();
                chmod($dest, 0664);
            }

			$body = array(
				'notify' => 'File successfully added.',
				'closeDialog' => true,
				'dispatchEvent' => 'reload.nos_media',
                'replaceTab' => 'admin/nos/media/media/edit/'.$media->media_id,
			);
        } catch (\Exception $e) {
			$body = array(
				'error' => $e->getMessage(),
			);
		}

        \Response::json($body);
	}

	public function action_update() {

        if (!static::check_permission_action('add', 'controller/admin/media/appdesk/list')) {
            throw new \Exception(__('Permission denied'));
        }
        try {

            $media = Model_Media::find(\Input::post('media_id', -1));

            if (empty($media)) {
                throw new \Exception('Media not found.');
            }
            $old_media = clone $media;

            $is_uploaded = isset($_FILES['media']) and is_uploaded_file($_FILES['media']['tmp_name']);

            if ($is_uploaded) {
                $pathinfo = pathinfo(mb_strtolower($_FILES['media']['name']));

                $disallowed_extensions = \Config::get('upload.disabled_extensions', array('php'));
                if (in_array($pathinfo['extension'], $disallowed_extensions)) {
                    throw new \Exception(__('This extension is not allowed due to security reasons.'));
                }
            } else {
                $pathinfo = pathinfo(APPPATH.$media->get_private_path());
            }
            $media->media_title     = \Input::post('media_title', '');
            $media->media_file      = \Input::post('slug', '');
            $media->media_folder_id = \Input::post('media_folder_id', 1);

            // Empty title = auto-generated from file name
            if (empty($media->media_title)) {
                if (empty($pathinfo['basename'])) {
                    throw new \Exception('Please provide a title.');
                }
                $media->media_title = static::pretty_title($pathinfo['basename']);
            }

            // Empty slug = auto-generated with title
            if (empty($media->media_file)) {
                $media->media_file  = $media->media_title;
            }
            if (!empty($pathinfo['extension'])) {
                $media->media_file .= '.'.$pathinfo['extension'];
            }

            if (false === $media->check_and_filter_slug()) {
                throw new \Exception(__('Generated media URL (SEO) was empty.'));
            }
            if (false === $media->refresh_path()) {
                throw new \Exception(__("The parent folder doesn't exists."));
            }

            $dest = APPPATH.$media->get_private_path();
            if ($old_media->get_private_path() != $media->get_private_path()) {
                if (is_file($dest)) {
                    throw new \Exception(__('A file with the same name already exists.'));
                }

                if ($is_uploaded) {
                    $old_media->delete_from_disk();
                } else {
                    // Create the directory if needed
                    $dest_dir = dirname($dest);
                    $base_dir = APPPATH.\Nos\Model_Media::$public_path;
                    $remaining_dir = str_replace($base_dir, '', $dest_dir);
                    // chmod  is 0777 here because it should be restricted with by the umask
                    is_dir($dest_dir) or \File::create_dir($base_dir, $remaining_dir, 0777);

                    if (!is_writeable($dest_dir)) {
                        throw new \Exception(__('No write permission. This is not your fault, but rather a misconfiguration from the server admin. Tell her/him off!'));
                    }
                    \File::rename(APPPATH.$old_media->get_private_path(), $dest);
                }
                $old_media->delete_public_cache();
            }

            // Relace old file if needed
            if ($is_uploaded) {
                // Move the file
                if (move_uploaded_file($_FILES['media']['tmp_name'], $dest)) {
                    chmod($dest, 0664);
                }
            }
            $media->save();

			$body = array(
				'notify' => 'File successfully saved.',
				'dispatchEvent' => 'reload.nos_media',
			);
        } catch (\Exception $e) {
			$body = array(
				'error' => $e->getMessage(),
			);
		}

        \Response::json($body);
	}

    /**
     * @param string $file
     * @return string
     */
	protected static function pretty_title($file) {
		$file = mb_substr($file, 0, mb_strrpos($file, '.'));
		$file = preg_replace('`[\W_-]+`u', ' ', $file);
		$file = \Inflector::humanize($file, ' ');
		return $file;
	}
}