<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                    <div class="col-md-2 border-right">
                      <h4 class="no-margin font-bold"><i class="fa fa-legal" aria-hidden="true"></i> <?php echo htmlspecialchars(_l($title)); ?></h4>
                      <hr />
                    </div>
                    <!-- <div class="col-md-4">
                      <select name="asset_groups[]" id="asset_group" class="selectpicker" multiple="true" data-actions-box="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo htmlspecialchars(_l('filter_by_asset_group')); ?>">
                            <?php foreach ($group as $s) { ?>
                              <option value="<?php echo htmlspecialchars($s['group_id']); ?>"><?php echo htmlspecialchars($s['group_name']); ?></option>
                              <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <select name="assetss[]" id="asset" class="selectpicker" multiple="true" data-actions-box="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo htmlspecialchars(_l('filter_by_asset')); ?>">
                            <?php foreach ($assets as $s) { ?>
                              <option value="<?php echo htmlspecialchars($s['id']); ?>"><?php echo htmlspecialchars($s['assets_name']); ?></option>
                              <?php } ?>
                      </select>
                    </div> -->
                  </div>
                   
                <?php
                        $table_data = [
                            _l('asset_code'),
                            _l('asset_name'),
                            _l('asset_group'),
                            _l('amounts'),
                            _l('unit_price'),
                            _l('original_price'),
                            _l('depreciation'),
                            _l('date_buy'),
                            _l('depreciation_value'),
                            _l('residual_value'),
                            ];
                        render_datatable($table_data, 'table_depreciation');
                        ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

</div>
<?php init_tail(); ?>
</body>
</html>
<script>

  var deprServerParams = {
        "groups": "[name='asset_groups[]']",
        "assets": "[name='assetss[]']",
    };

    table_depr = $('.table-table_depreciation');


    _table_api = initDataTable(table_depr, admin_url + 'assets/table_depreciation', '', '', deprServerParams);


    $.each(deprServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
         
            table_depr.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });
    

</script>
