<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

return array(
	'query' => array(
		'model' => 'Cms\Model_Media_Folder',
	),
	'dataset' => array(
		'id'    => 'medif_id',
		'title' => 'medif_title',
        'actions' => array(
            'edit' => function($item) {
                return $item->medif_parent_id != null;
            },
            'delete' => function($item) {
                return $item->medif_parent_id != null;
            },
        ),
	),
);