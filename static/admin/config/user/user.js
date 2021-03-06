/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

define([
    'jquery-nos-appdesk'
], function($nos) {
    "use strict";
    return function(appDesk) {
        return {
            tab : {
                label : appDesk.i18n('Users'),
                iconUrl : 'static/novius-os/admin/novius-os/img/32/user.png'
            },
            actions : {
                edit : {
                    label : appDesk.i18n('Edit'),
                    icon : 'pencil',
                    primary : true,
                    action : function(item, ui) {
                        $nos(ui).tab({
                            url : 'admin/nos/user/form/edit/' + item.id,
                            label : item.title
                        });
                    }
                },
                'delete' : {
                    label : appDesk.i18n('Delete'),
                    icon : 'trash',
                    primary : true,
                    action : function(item, ui) {
                        $nos(ui).dialog({
                            contentUrl: 'admin/nos/user/user/delete_user/' + item.id,
                            ajax : true,
                            title: appDesk.i18n('Delete a user')._(),
                            width: 400,
                            height: 150
                        });
                    }
                }
            },
            appdesk : {
                adds : {
                    user : {
                        label : appDesk.i18n('Add a user'),
                        action : function(ui) {
                            $nos(ui).tab('add', {
                                url : 'admin/nos/user/form/add',
                                label : appDesk.i18n('Add a user')._()
                            });
                        }
                    }
                },
                grid : {
                    proxyUrl : 'admin/nos/user/list/json',
                    columns : {
                        user : {
                            headerText : appDesk.i18n('Name'),
                            dataKey : 'fullname',
                            sortDirection : 'ascending'
                        },
                        email : {
                            headerText : appDesk.i18n('Email'),
                            dataKey : 'email'
                        },
                        actions : {
                            actions : ['edit', 'delete']
                        }
                    }
                }
            }
        }
    }
});
