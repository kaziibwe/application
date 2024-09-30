<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="_filters _hidden_inputs hidden">
                        <?php
                       
                        echo form_hidden('draft');
                        echo form_hidden('valid');
                        echo form_hidden('invalid');
                        
                        foreach($staff as $s) { 
                            echo form_hidden('contracts_by_staff_'.$s['staffid']);
                        }
                        foreach($contract_type as $type){
                            echo form_hidden('contracts_by_type_'.$type['id_contracttype']);
                        }
                        foreach($duration as $d){
                            echo form_hidden('contracts_by_duration_'.$d['duration'].'_'.$d['unit']);
                        }
                    ?>
                </div>
                    <div class="panel-body">
                        <?php if(has_permission('hrm','','create')){ { ?>
                        <div class="_buttons">
                            <a href="<?php echo admin_url('hrm/contract'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_contract'); ?></a>
                        </div>
                        <?php } ?>
                        <div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left width300 height500">
                            
                            <li>
                                <a href="#" data-cview="all" onclick="dt_custom_view('','.table-table_contract',''); return false;">
                                    <?php echo _l('contracts_view_all'); ?>
                                </a>
                            </li>
                            <li class="filter-group" data-filter-group="status">
                                <a href="#" data-cview="draft"  onclick="dt_custom_view('draft','.table-table_contract','draft'); return false;">
                                    <?php echo _l('draft'); ?>
                                </a>
                            </li>
                            <li class="filter-group" data-filter-group="status">
                                <a href="#" data-cview="valid"  onclick="dt_custom_view('valid','.table-table_contract','valid'); return false;">
                                    <?php echo _l('valid'); ?>
                                </a>
                            </li>
                            <li class="filter-group" data-filter-group="status">
                                <a href="#" data-cview="invalid"  onclick="dt_custom_view('invalid','.table-table_contract','invalid'); return false;">
                                    <?php echo _l('invalid'); ?>
                                </a>
                            </li>
                        
                            <div class="clearfix"></div>
                            <li class="divider"></li>
                            <li class="dropdown-submenu pull-left">
                                <a href="#" tabindex="-1"><?php echo _l('staff'); ?></a>
                                <ul class="dropdown-menu dropdown-menu-left">
                                    <?php  foreach($staff as $s){ ?>
                                    <li><a href="#" data-cview="contracts_by_staff_<?php echo htmlspecialchars($s['staffid']); ?>" onclick="dt_custom_view('contracts_by_staff_<?php echo htmlspecialchars($s['staffid']); ?>','.table-table_contract','contracts_by_staff_<?php echo htmlspecialchars($s['staffid']); ?>'); return false;">
                                    <?php echo htmlspecialchars($s['firstname']); ?>
                                </a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <div class="clearfix"></div>
                            <?php if(count($contract_type) > 0){ ?>
                            <li class="divider"></li>
                            <?php foreach($contract_type as $type){ ?>
                            <li>
                                <a href="#" data-cview="contracts_by_type_<?php echo htmlspecialchars($type['id_contracttype']); ?>" onclick="dt_custom_view('contracts_by_type_<?php echo htmlspecialchars($type['id_contracttype']); ?>','.table-table_contract','contracts_by_type_<?php echo htmlspecialchars($type['id_contracttype']); ?>'); return false;">
                                    <?php echo htmlspecialchars($type['name_contracttype']); ?>
                                </a>
                            </li>
                            <?php } ?>
                            <?php } ?>
                            <div class="clearfix"></div>
                            <?php if(count($duration) > 0){ ?>
                                <li class="divider"></li>
                            <?php foreach($duration as $type){ ?>
                                <li class="filter-group" data-filter-group="duration">
                                    <a href="#" data-cview="contracts_by_duration_<?php echo htmlspecialchars($type['duration']).'_'.htmlspecialchars($type['unit']); ?>" onclick="dt_custom_view('contracts_by_duration_<?php echo htmlspecialchars($type['duration']).'_'.htmlspecialchars($type['unit']); ?>','.table-table_contract','contracts_by_duration_<?php echo htmlspecialchars($type['duration']).'_'.htmlspecialchars($type['unit']); ?>'); return false;">
                                    <?php echo htmlspecialchars($type['duration']).' '.htmlspecialchars($type['unit']); ?>
                                </a>
                                <li>
                            <?php } ?>
                        <?php } ?>
                        </ul>
                    </div>


                        <div class="clearfix"></div>
                        <br>
                        <?php } ?>

                        <div class="row">
  
                           <?php  $pro = $this->hrm_model->get_staff();?>
                           <div  class="col-md-3 leads-filter-column pull-left">
                          
                                  <select name="staff[]" id="staff" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('als_staff'); ?>">
                                    <?php foreach($pro as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s['staffid']); ?>"><?php echo htmlspecialchars($s['firstname']); ?></option>
                                      <?php } ?>
                                  </select>
                            </div> 

                            <div  class="col-md-2 leads-filter-column pull-right">
                                <div class="form-group" app-field-wrapper="validity_end_date">
                                    <div class="input-group date">
                                        <input type="text" id="validity_end_date" name="validity_end_date" class="form-control datepicker" value="" autocomplete="off" placeholder="<?php echo _l('validity_end_date') ?>">
                                            <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                    </div>
                                </div>
                            </div> 
                            <div  class="col-md-2 leads-filter-column pull-right">
                                <div class="form-group" app-field-wrapper="validity_start_date">
                                    <div class="input-group date">
                                        <input type="text" id="validity_start_date" name="validity_start_date" class="form-control datepicker" value="" autocomplete="off" placeholder="<?php echo _l('validity_start_date') ?>">
                                            <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                    </div>
                                </div>
                            </div> 

                            
                        </div>
                        <br>
                        <div class="row">
                           <div class="col-md-12" id="small-table">
                              <div class="panel_s">
                                 <div class="panel-body">
                                    <div class="clearfix"></div>
                                     <?php echo form_hidden('hrmcontractid',$hrmcontractid); ?>
                                      <!-- if hrmcontract id found in url -->
                                    <?php
                                    $table_data = array(
                                        _l('id'),
                                        _l('contract_code'),
                                        _l('name_contract'),
                                        _l('staff'),
                                        _l('start_valid'),
                                        _l('end_valid'),
                                        _l('contract_status'),

                                                          
                                        );
                                    $custom_fields = get_custom_fields('staff',array('show_on_table'=>1));
                                    foreach($custom_fields as $field){
                                        array_push($table_data,$field['name']);
                                    }
                                    render_datatable($table_data,'table_contract');
                                    ?>
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

    var tree_dep = $('#hrm_derpartment_tree').comboTree({
              source : <?php echo ''.$dep_tree; ?>
            });

     var ContractsServerParams = {
        "hrm_deparment": "input[name='hrm_deparment']",
        "hrm_staff"    : "select[name='staff[]']",
        "validity_start_date": "input[name='validity_start_date']",
        "validity_end_date": "input[name='validity_end_date']",
     };
        $.each($('._hidden_inputs._filters input'),function(){
            ContractsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
        });

    table_contract = $('table.table-table_contract');
    initDataTable(table_contract, admin_url+'hrm/table_contract', undefined, undefined, ContractsServerParams,<?php echo hooks()->apply_filters('contracts_table_default_order', json_encode(array(6,'asc'))); ?>);

    //combotree department
     $('#hrm_derpartment_tree').on('change', function() {
                $('#hrm_deparment').val(tree_dep.getSelectedItemsId());
                table_contract.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
     $('#staff').on('change', function() {
                table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
            });
    $('#validity_start_date').on('change', function() {
                    table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
                    });
    $('#validity_end_date').on('change', function() {
                    table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
                });
    init_hrm_contract();
    //init table contract view
    function init_hrm_contract(id) {
    load_small_table_item(id, '#hrm_contract', 'hrmcontractid', 'hrm/get_hrm_contract_data_ajax', '.table-table_contract');
    }


    
</script>
</body>
</html>
