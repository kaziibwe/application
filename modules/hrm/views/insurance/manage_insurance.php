<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if(has_permission('hrm','','create')){ ?>
                        <div class="_buttons">
                            
                            <a href="<?php echo admin_url('hrm/insurance'); ?>" class="btn btn-info mright5 pull-left display-block"><?php echo _l('add_insurrance'); ?></a>

                        </div>

                        <div class="clearfix"></div>
                        <br>

                        <?php } ?>

                        <div class="row filter_by">

                           <?php  $pro = $this->hrm_model->get_staff();?>
                           <div  class="col-md-3 leads-filter-column pull-left">
                          
                                  <select name="staff[]" id="staff" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('als_staff'); ?>">
                                    <?php foreach($pro as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s['staffid']); ?>"><?php echo htmlspecialchars($s['firstname']); ?></option>
                                      <?php } ?>
                                  </select>
                            </div> 
                            
                        </div>
                        <br>
                        <div class="row">
                           <div class="col-md-12" id="small-table">
                              <div class="panel_s">
                                 <div class="panel-body">
                                <div class="clearfix"></div>

                                 <!-- if hrmcontract id found in url -->
                                 <div class="tab-content">
                                 <!-- start -->
                                <div role="tabpanel" class="tab-pane active" id="tab_list_insurance">
                                   
                                    <?php
                                    $table_data = array(
                                        _l('staff_id'),
                                        _l('clients_list_full_name'),

                                        _l('job_position'),
                                        _l('insurance_book_number'),
                                        _l('health_insurance_number'),                                                           
                                        );
                                    render_datatable($table_data,'table_insurance');
                                    ?>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="tab_statistic">
                                    
                                </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-7 small-table-right-col">
                              <div id="hrm_contract" class="hide">
                              </div>
                           </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>

$(function(){
    var tree_dep = $('#hrm_derpartment_tree').comboTree({
              source : <?php echo ''.$dep_tree; ?>
            });
    var ContractsServerParams = {
        "hrm_deparment": "input[name='hrm_deparment']",
        "hrm_staff"    : "select[name='staff[]']",
        "hrm_from_month"    : "select[name='from_month[]']",
     };

    table_insurance = $('table.table-table_insurance');
    initDataTable(table_insurance,admin_url + 'hrm/table_insurance', undefined, undefined, ContractsServerParams);

    $('#hrm_derpartment_tree').on('change', function() {
                $('#hrm_deparment').val(tree_dep.getSelectedItemsId());
                table_insurance.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
      });
    $('#staff').on('change', function() {
                table_insurance.DataTable().ajax.reload().columns.adjust().responsive.recalc();
      });
    $('#from_month').on('change', function() {
                table_insurance.DataTable().ajax.reload().columns.adjust().responsive.recalc();
      });

});

    
</script>
</body>
</html>
