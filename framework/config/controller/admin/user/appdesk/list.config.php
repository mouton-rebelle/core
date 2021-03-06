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

I18n::load('user', 'nos_user');

return array(
	'query' => array(
		'model' => 'Nos\Model_User',
		'related' => array('roles'),
	),
	'search_text' => array(
		'user_firstname',
		'user_name',
		'user_email',
	),
    'hideLocales' => true,
    'selectedView' => 'default',
    'views' => array(
        'default' => array(
            'name' => __('Default view'),
            'json' => array('static/novius-os/admin/config/user/user.js'),
        )
    ),
    'i18n' => array(
        'Users' => __('Users'),
        'Add a user' => __('Add a user'),
        'User' => __('User'),
        'Email' => __('Email'),
        'Permissions' => __('Permissions'),

        'addDropDown' => __('Select an action'),
        'columns' => __('Columns'),
        'showFiltersColumns' => __('Filters column header'),
        'visibility' => __('Visibility'),
        'settings' => __('Settings'),
        'vertical' => __('Vertical'),
        'horizontal' => __('Horizontal'),
        'hidden' => __('Hidden'),
        'item' => __('user'),
        'items' => __('users'),
        'showNbItems' => __('Showing {{x}} users out of {{y}}'),
        'showOneItem' => __('Show 1 user'),
        'showNoItem' => __('No user'),
        'showAll' => __('Show all users'),
        'views' => __('Views'),
        'viewGrid' => __('Grid'),
        'viewThumbnails' => __('Thumbnails'),
        'preview' => __('Preview'),
        'loading' => __('Loading...'),
    ),
	'dataset' => array(
		'id' => 'user_id',
		'fullname' => array(
            'search_column' => \DB::expr('CONCAT(user_firstname, user_name)'),
            'value' => function($object) {
                return $object->fullname();
            },
        ),
		'email' => 'user_email',
		'id_permission' => function($object) {
			return $object->roles && reset($object->roles)->role_id ?: $object->user_id;
		}
	),
	'inputs' => array(),
);