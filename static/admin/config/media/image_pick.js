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

        // Remove all primary actions
        $nos.each(appDesk.actions, function() {
            this.primary = false;
        });

        // Add "pick" as unique primary action
        appDesk.appdesk.grid.columns.actions.actions.unshift('pick');
        appDesk.appdesk.thumbnails.actions.unshift('pick');
        appDesk.appdesk.inspectors.preview.options.actions.unshift('pick');

        return {
            actions : {
                pick : {
                    label : appDesk.i18n('Pick'),
                    icon : 'check',
                    text : true,
                    primary : true,
                    action : function(item, $el) {
                        $el.closest('.ui-dialog-content').trigger('select_media', item);
                    }
                }
            },
            appdesk : {
                grid : {
                    id : 'nos_media_grid_image'
                },
                values: {
                    media_extension: ['image']
                },
                inspectors : {
                    extensions : {
                        hide : true
                    }
                }
                // Another solution is to remove the extensions inspector in the "Order" property
                //inspectorsOrder : 'folders,preview'
            }
        }
    }
});