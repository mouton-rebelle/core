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

class Controller_Admin_Media_Info extends Controller_Admin_Noviusos {

	public function action_media($id)
	{
		$media = Model_Media::find($id);

		if (!empty($media)) {
			\Config::load('nos::controller/admin/media/appdesk/list', true);
			$dataset = \Config::get('nos::controller/admin/media/appdesk/list.dataset', array());
			$item = array();
			foreach ($dataset as $key => $data)
			{
				$item[$key] = is_callable($data) ? $data($media) : $media->$data;
			}
		} else {
			$item = null;
		}

		\Response::json($item);
	}
}
