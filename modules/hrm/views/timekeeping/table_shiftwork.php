<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
<div class="clearfix"></div>
<h4><?php echo _l($title); ?></h4>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
  <div class="content hrm-fullwidth">
        <div class="row">      
		    <div class="col-md-12 mleft5" id="example">
		            	
		    </div>
        </div>
  </div>
</div>
<script>       
var dataObject = <?php echo json_encode($staff_row); ?>;

var hotElement = document.querySelector('#example');
var hotElementContainer = hotElement.parentNode;
var hotSettings = {	
  data: dataObject,
  columns: <?php echo ''.$set_col; ?>,

  licenseKey: 'non-commercial-and-evaluation',
  stretchH: 'all',
  width: '100%',
  autoWrapRow: true,
  rowHeights: 40,
  colWidths: 170,
  height:600,
  rowHeaders: true,
  cells: function(row, col, prop) {
      var cellProperties = {};
      
      if (row%2 == 1) {
        cellProperties.renderer = firstRowRenderer; // uses function directly
      }
      
      return cellProperties;
    },
  colHeaders: <?php echo ''.$day_by_month; ?>,
   columnSorting: {
    indicator: true
  },
  autoColumnSize: {
    samplingRatio: 23
  },
  dropdownMenu: true,
  mergeCells: true,
  contextMenu: true,
  manualRowMove: true,
  manualColumnMove: true,
  multiColumnSorting: {
    indicator: true
  },
  filters: true,
  manualRowResize: true,
  manualColumnResize: true
};
var hot = new Handsontable(hotElement, hotSettings);
function firstRowRenderer(instance, td, row, col, prop, value, cellProperties) {
  Handsontable.renderers.TextRenderer.apply(this, arguments);
  td.style.color = 'green';
  td.style.background = '#CEC';
}
</script>
</body>
</html>