<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-4 border-right">
                      <h4 class="no-margin font-bold"><i class="fa fa-edit" aria-hidden="true"></i> <?php echo htmlspecialchars(_l($title)); ?></h4>
                      <hr />
                    </div>
                  </div>
                   
                <?php
                        $table_data = [
                            _l('time'),
                            _l('asset_name'),
                            _l('acction_code'),
                            _l('action'),
                            _l('quantity_as_qty'),
                            _l('acction_from'),
                            _l('acction_to'),
                            ];
                        render_datatable($table_data, 'table_action');
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
  initDataTable('.table-table_action', admin_url+'assets/table_action_allocate/allocation');
</script>
