<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
    $id = uniqid('temp_');
?>
<table id="<?= $id ?>"></table>
<script type="text/javascript">
require([
		'jquery-nos-listgrid'
	], function( $nos, table, undefined ) {
		$nos(function() {
			var inspector = $nos('#<?= $id ?>').removeAttr('id'),
				parent = inspector.parent()
					.bind({
						widgetResize: function() {
                            inspector.noslistgrid('setSize', parent.width(), parent.height());
						}
					}),
                inspectorData = parent.data('inspector'),
				rendered = false;

            inspector.css({
                    height : '100%',
                    width : '100%'
                })
                .noslistgrid({
                    showFilter: false,
                    allowSorting: false,
                    scrollMode : 'auto',
                    allowPaging : false,
                    allowColSizing : false,
                    allowColMoving : false,
                    columns : inspectorData.grid.columns,
                    data: <?= $data ?>,
                    currentCellChanged: function (e) {
                        var row = $nos(e.target).noslistgrid("currentCell").row(),
                            data = row ? row.data : false;

                        if (data && rendered) {
                            inspectorData.selectionChanged(data.id, data.title);
                        }
                        inspector.noslistgrid("currentCell", -1, -1);
                    },
                    rendering : function() {
                        rendered = false;
                    },
                    rendered : function() {
                        rendered = true;
                        inspector.css('height', 'auto');
                    }
                });
		});
	});
</script>