/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
define('jquery-nos-preview',
    ['jquery', 'jquery-nos', 'jquery-ui.widget', 'wijmo.wijsuperpanel'],
    function($, $nos) {
        "use strict";
            var undefined = void(0);
        $.widget( "nos.preview", {
            options: {
                meta : {},
                actions : [],
                data : null,
                dataParser : null,
                texts : {
                    headerDefault : 'Preview',
                    selectItem : 'No item selected'
                }
            },

            data : null,

            _create: function() {
                var self = this,
                    o = self.options;

                self.element.addClass('nos-preview ui-widget ui-widget-content wijmo-wijgrid')
                    .parents('.nos-appdesk')
                    .bind('selectionChanged.appdesk', function(e, data) {
                        if ($.isPlainObject(data)) {
                            self.select(data);
                        } else {
                            self.unselect();
                        }
                    });
            },

            _init: function() {
                var self = this,
                    o = self.options;

                self.data = self.data || o.data;

                if ($.isPlainObject(self.data)) {
                    self.select(self.data);
                } else {
                    self.unselect();
                }
            },

            _uiHeader : function(title) {
                var self = this,
                    o = self.options;

                var table = $('<table cellspacing="0" cellpadding="0" border="0"><thead></thead></table>')
                        .addClass('nos-preview-header wijmo-wijsuperpanel-header wijmo-wijgrid-root wijmo-wijgrid-table')
                        .css({
                            borderCollapse : 'separate',
                            '-moz-user-select' : '-moz-none'
                        })
                        .appendTo(self.element);

                var tr = $('<tr></tr>').addClass('wijmo-wijgrid-headerrow')
                    .appendTo(table);

                $('<th><div><span></span></div></th>').addClass('wijgridth ui-widget wijmo-c1basefield ui-state-default wijmo-c1field')
                    .appendTo(tr)
                    .find('div')
                    .addClass('wijmo-wijgrid-innercell')
                    .find('span')
                    .addClass('wijmo-wijgrid-headertext')
                    .text(title);

                return self;
            },

            _uiFooter : function() {
                var self = this,
                    o = self.options;

                if (o.actions.length > 0) {

                    self.uiFooter = $('<div></div>')
                        .addClass('nos-preview-footer')
                        .appendTo(self.uiContainer);

                    $.each(o.actions, function() {
                        var action = this;
                        var iconClass = false;
                        if (action.iconClasses) {
                            iconClass = action.iconClasses;
                        } else if (action.icon) {
                            iconClass = 'nos-icon16 ui-icon ui-icon-' + action.icon;
                        }
                        var text;
                        if (action.primary) {
                            text = (iconClass ? '<span class="ui-button-icon-primary ' + iconClass +' wijmo-wijmenu-icon-left"></span>' : '');
                            text += '<span class="ui-button-text">' + action.label + '</span>';
                            $('<button></button>')
                                .addClass('ui-button ui-button-text' + (action.icon ? '-icon-primary' : '') + ' ui-widget ui-state-default ui-corner-all')
                                .css({
                                    marginBottom : '5px'
                                })
                                .appendTo(self.uiFooter)
                                .html(text)
                                .hover(function() {
                                    $(this).addClass('ui-state-hover');
                                }, function() {
                                    $(this).removeClass('ui-state-hover');
                                })
                                .click(function(e) {
                                    e.preventDefault();
                                    e.stopImmediatePropagation();
                                    action.action.apply(this, [self.data, $(this)]);
                                })
                        } else {
                            text = (iconClass ? '<span class="' + iconClass +'"></span> ' : '');
                            text += '<span class="ui-button-text">' + action.label + '</span>';
                            $('<a href="#"></a>')
                                .css({
                                    display : 'inline-block',
                                    marginBottom : '5px'
                                })
                                .appendTo(self.uiFooter)
                                .html(text)
                                .click(function(e) {
                                    e.preventDefault();
                                    e.stopImmediatePropagation();
                                    action.action.apply(this, [self.data, $(this)]);
                                })
                        }
                    });

                }

                return self;
            },

            _uiThumbnail : function(data) {
                var self = this,
                    o = self.options,
                    thumbnail = data.thumbnail.replace(/64-64/g, '256-256') || data.thumbnailAlternate;

                if (thumbnail) {
                    self._loadImg(data, thumbnail);
                }

                return self;
            },

            _loadImg : function(item, thumbnail) {
                var self = this,
                    o = self.options;

                $('<img />')
                    .error(function() {
                        $(this).remove();
                        if (thumbnail === item.thumbnail && item.thumbnailAlternate) {
                            self._loadImg(item, item.thumbnailAlternate);
                        }
                    })
                    .load(function() {
                        var img = $(this),
                            height = img.height();

                        var div = $('<div></div>')
                            .addClass('nos-preview-thumb')
                            .css({
                                backgroundImage :'url("' + img.attr('src') +'")',
                                height : (height <= 100 ? height : 100) + 'px'
                            })
                            .prependTo(self.uiContainer);

                            var action = null;
                            $.each(o.actions, function() {
                                if (this.name == o.actionThumbnail) {
                                    action = this;
                                }
                            });

                            if (action !== null) {
                                div
                                    .attr({
                                        title : action.label
                                    })
                                    .css({
                                        cursor : 'pointer'
                                    })
                                    .click(function(e) {
                                        e.preventDefault();
                                        e.stopImmediatePropagation();
                                        action.action.apply(this, [self.data]);
                                    });
                            }
                        img.remove();
                    })
                    .css({
                        position : 'absolute',
                        visibility : 'hidden'
                    })
                    .attr('src', thumbnail)
                    .appendTo('body');

                return self;
            },

            _uiMetaData : function(data) {
                var self = this,
                    o = self.options,
                    i = 0;

                var table = $('<table cellspacing="0" cellpadding="0" border="0"><tbody></tbody></table>')
                        .addClass('nos-preview-metadata wijmo-wijgrid-root wijmo-wijgrid-table')
                        .css({
                            borderCollapse : 'separate',
                            '-moz-user-select' : '-moz-none'
                        })
                        .appendTo(self.uiContainer)
                        .find('tbody')
                        .addClass('ui-widget-content wijmo-wijgrid-data');

                $.each(o.meta, function(key, meta) {
                    var tr = $('<tr></tr>').addClass('wijmo-wijgrid-row ui-widget-content wijmo-wijgrid-datarow' + (i%2 ? ' wijmo-wijgrid-alternatingrow' : ''))
                        .appendTo(table);

                    $('<th><div></div></th>').addClass('wijgridtd wijdata-type-string')
                        .appendTo(tr)
                        .find('div')
                        .addClass('wijmo-wijgrid-innercell')
                        .text(meta.label || '');

                    $('<td><div></div></td>').addClass('wijgridtd wijdata-type-string')
                        .appendTo(tr)
                        .find('div')
                        .addClass('wijmo-wijgrid-innercell')
                        .text(data[key] || '');
                    i++;
                });

                return self;
            },

            unselect : function() {
                var self = this,
                    o = self.options;

                self.element.wijsuperpanel('destroy')
                    .empty();

                self._uiHeader(o.texts.headerDefault);

                self.uiContainer = $('<div></div>')
                    .addClass('nos-preview-noitem')
                    .text(o.texts.selectItem)
                    .appendTo(self.element);

                self.element.wijsuperpanel({
                        showRounder : false
                    });

                return self;
            },

            select : function(data) {
                var self = this,
                    o = self.options;

                if (data === undefined) {
                    return self.data;
                } else {
                    self.data = data;

                    if ($.isFunction(o.dataParser)) {
                        data = o.dataParser(data);
                    }

                    self.element.wijsuperpanel('destroy')
                        .empty()
                        .css('height', '100%');

                    self._uiHeader(data.title);

                    self.uiContainer = $('<div></div>')
                        .addClass('nos-preview-container')
                        .appendTo(self.element);

                    self._uiThumbnail(data)
                        ._uiMetaData(data.meta)
                        ._uiFooter();

                    self.element.wijsuperpanel({
                            showRounder : false,
                            autoRefresh : true
                        });
                }

                return self;
            },

            resize : function() {
                var self = this;

                self._init();

                return self;
            }
        });
        return $nos;
    });
