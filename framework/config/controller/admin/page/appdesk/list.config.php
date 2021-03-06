<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

use Nos\I18n;

I18n::load('page', 'nos_page');

return array(
	'tree' => array(
		'models' => array(
			array(
				'model' => 'Nos\Model_Page',
				'order_by' => 'page_sort',
				'childs' => array('Nos\Model_Page'),
				'dataset' => array(
					'id' => 'page_id',
					'title' => 'page_title',
					'url' => function($page) {
                        return $page->get_href();
                    },
                    'previewUrl' => function($page) {
                        return $page->get_preview_href(array('absolute' => true));;
                    },
                    'actions' => array(
                        'delete' => function($page) {
                            return $page->page_lock != $page::LOCK_DELETION;
                        }
                    ),
				),
			),
		),
		'roots' => array(
			array(
				'model' => 'Nos\Model_Page',
				'where' => array(array('page_parent_id', 'IS', \DB::expr('NULL'))),
				'order_by' => 'page_sort',
			),
		),
	),
	'query' => array(
		'model' => 'Nos\Model_Page',
		'related' => array(),
	),
	'search_text' => 'page_title',
    'selectedView' => 'default',
    'views' => array(
        'default' => array(
            'name' => __('Default view'),
            'json' => array('static/novius-os/admin/config/page/page.js'),
        )
    ),
    'i18n' => array(
        'Pages' => __('Pages'),
        'Add a Page' => __('Add a page'),
        'Add a root' => __('Add a root'),
        'Title' => __('Title'),
        'Roots' => __('Roots'),
        'Directories' => __('Directories'),
        'addDropDown' => __('Select an action'),
        'columns' => __('Columns'),
        'showFiltersColumns' => __('Filters column header'),
        'visibility' => __('Visibility'),
        'settings' => __('Settings'),
        'vertical' => __('Vertical'),
        'horizontal' => __('Horizontal'),
        'hidden' => __('Hidden'),
        'item' => __('page'),
        'items' => __('pages'),
        'showNbItems' => __('Showing {{x}} pages out of {{y}}'),
        'showOneItem' => __('Show 1 page'),
        'showNoItem' => __('No page'),
        'showAll' => __('Show all pages'),
        'views' => __('Views'),
        'viewGrid' => __('Grid'),
        'viewThumbnails' => __('Thumbnails'),
        'preview' => __('Preview'),
        'loading' => __('Loading...'),
    ),
	'dataset' => array(
		'id' => 'page_id',
		'title' => 'page_title',
        'url' => function($page) {
            return $page->get_href();
        },
        'previewUrl' => function($page) {
            return $page->get_preview_href(array('absolute' => true));
        },
        'actions' => array(
            'delete' => function($page) {
                return $page->page_lock != Nos\Model_Page::LOCK_DELETION;
            }
        ),
	),
	'inputs' => array(
		'root_id' => function($value, $query) {
			if ($value) {
				$query->where(array('page_root_id', '=', $value));
				//$query->where(array('page_level', '=', 1));
				$query->order_by('page_title');
			}
			return $query;
		},
	),
);