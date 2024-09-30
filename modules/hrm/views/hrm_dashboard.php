<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
   $this->load->model('hrm/hrm_model');
   $data_dash = $this->hrm_model->get_hrm_dashboard_data();
   $staff_chart_by_age = json_encode($this->hrm_model->staff_chart_by_age());
   $contract_type_chart = json_encode($this->hrm_model->contract_type_chart());
?>

<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="clearfix"></div>
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">

<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('hrm'); ?>">

    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6">
         <p class="text-dark text-uppercase bold"><?php echo _l('hrm_dashboard');?></p>
      </div>
         <div class="col-md-3 pull-right">
         
         </div>
         <br>
         <hr class="mtop15" />
         <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
           <div class="top_stats_wrapper hrm-minheight85">
               <a class="text-success mbot15">
               <p class="text-uppercase mtop5 hrm-minheight35"><i class="hidden-sm glyphicon glyphicon-edit"></i> <?php echo _l('total_staff'); ?>
               </p>
                  <span class="pull-right bold no-mtop hrm-fontsize24"><?php echo htmlspecialchars($data_dash['total_staff']); ?></span>
               </a>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic hrm-fullwidth" role="progressbar" aria-valuenow="<?php echo htmlspecialchars($data_dash['total_staff']); ?>" aria-valuemin="0" aria-valuemax="<?php echo htmlspecialchars($data_dash['total_staff']); ?>" data-percent="100%">
                  </div>
               </div>
            </div>
         </div>
         <div class="quick-stats-invoices col-xs-12 col-md-2 col-sm-6">
           <div class="top_stats_wrapper hrm-minheight85">
               <a class="text mbot15">
               <p class="text-uppercase mtop5 hrm-colorpurple hrm-minheight35"><i class="hidden-sm glyphicon glyphicon-edit"></i> <?php echo _l('new_staff_for_month'); ?>
               </p>
                  <span class="pull-right bold no-mtop hrm-colorpurple hrm-fontsize24"><?php echo htmlspecialchars($data_dash['new_staff_in_month']); ?></span>
               </a>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar no-percent-text not-dynamic hrm-colorpurple" role="progressbar" aria-valuenow="<?php echo htmlspecialchars($data_dash['total_staff']); ?>" aria-valuemin="0" aria-valuemax="<?php echo htmlspecialchars($data_dash['total_staff']); ?>" style="width: <?php echo ($data_dash['new_staff_in_month']/$data_dash['total_staff'])*100; ?>%" data-percent="<?php echo ($data_dash['new_staff_in_month']/$data_dash['total_staff'])*100; ?>%">
                  </div>
               </div>
            </div>
         </div>
         <div class="quick-stats-invoices col-xs-12 col-md-2 col-sm-6">
           <div class="top_stats_wrapper hrm-minheight85">
               <a class="text-info mbot15">
               <p class="text-uppercase mtop5 hrm-minheight35"><i class="hidden-sm glyphicon glyphicon-envelope"></i> <?php echo _l('working'); ?>
               </p>
                  <span class="pull-right bold no-mtop hrm-fontsize24"><?php echo htmlspecialchars($data_dash['staff_working']); ?></span>
               </a>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo htmlspecialchars($data_dash['staff_working']); ?>" aria-valuemin="0" aria-valuemax="<?php echo htmlspecialchars($data_dash['total_staff']); ?>" style="width: <?php echo htmlspecialchars($data_dash['staff_working']/$data_dash['total_staff']*100); ?>%" data-percent=" <?php echo htmlspecialchars($data_dash['staff_working']/$data_dash['total_staff']*100); ?>%">
                  </div>
               </div>
            </div>
         </div> 
        
            <div class="quick-stats-invoices col-xs-12 col-md-2 col-sm-6">
              <div class="top_stats_wrapper hrm-minheight85">
                  <a class="text-danger mbot15">
                  <p class="text-uppercase mtop5 hrm-minheight35"><i class="hidden-sm glyphicon glyphicon-remove"></i> <?php echo _l('overdue_contract'); ?>
                  </p>
                     <span class="pull-right bold no-mtop hrm-fontsize24"><?php echo htmlspecialchars($data_dash['overdue_contract']); ?></span>
                  </a>
                  <div class="clearfix"></div>
                  <div class="progress no-margin progress-bar-mini">
                     <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo htmlspecialchars($data_dash['overdue_contract']); ?>" aria-valuemin="0" aria-valuemax="<?php echo htmlspecialchars($data_dash['total_staff']); ?>" style="width:  <?php echo ($data_dash['overdue_contract']/$data_dash['total_staff'])*100; ?>%" data-percent=" <?php echo ($data_dash['overdue_contract']/$data_dash['total_staff'])*100; ?>%">
                     </div>
                  </div>
               </div>
            </div>
            <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
              <div class="top_stats_wrapper hrm-minheight85">
                  <a class="text-muted  mbot15">
                  <p class="text-uppercase mtop5 hrm-minheight35"><i class="hidden-sm glyphicon glyphicon-remove"></i> <?php echo _l('contract_is_about_to_expire'); ?>
                  </p>
                     <span class="pull-right bold no-mtop hrm-fontsize24"><?php echo htmlspecialchars($data_dash['expire_contract']); ?></span>
                  </a>
                  <div class="clearfix"></div>
                  <div class="progress no-margin progress-bar-mini">
                     <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo htmlspecialchars($data_dash['expire_contract']); ?>" aria-valuemin="0" aria-valuemax="<?php echo htmlspecialchars($data_dash['total_staff']); ?>" style="width:  <?php echo ($data_dash['expire_contract']/$data_dash['total_staff'])*100; ?>%" data-percent=" <?php echo ($data_dash['expire_contract']/$data_dash['total_staff'])*100; ?>%">
                     </div>
                  </div>
               </div>
            </div>
      </div>
      <div class="col-md-6">
        <div id="staff_chart_by_age" class="hrm-marginauto hrm-minwidth310">
      </div>
      </div>
      <div class="col-md-6">
        <div id="contract_type_chart" class="hrm-marginauto hrm-minwidth310">
      </div>
      </div>
      <div class="col-md-12">
        <br>
              <div class="panel_s">
                <div class="panel-body">
                  <h4><p class="padding-5 bold"><?php echo _l('list_overdue_contract'); ?></p></h4>
                    <hr class="hr-panel-heading-dashboard">
                  <table class="table dt-table scroll-responsive">
                    <thead>
                        <th><?php echo _l('contract_code'); ?></th>
                        <th><?php echo _l('name_contract'); ?></th>
                        <th><?php echo _l('staff'); ?></th>
                        <th><?php echo _l('departments'); ?></th>
                        <th><?php echo _l('start_valid'); ?></th>
                        <th><?php echo _l('end_valid'); ?></th>
                        <th><?php echo _l('sign_day'); ?></th>
                    </thead>
                    <tbody>
                     
                        <?php 
                         $list_member_id = [];
                        foreach($data_dash['overdue_contract_data'] as $overdue_contract){
                          ?>

                        <tr>
                            <td><?php echo htmlspecialchars($overdue_contract['contract_code']); ?></td>
                            <td><a href="<?php echo admin_url('hrm/contract/' . $overdue_contract['id_contract']); ?>"><?php echo htmlspecialchars($overdue_contract['name_contract']); ?></a></td>
                            <td><?php echo get_staff_full_name($overdue_contract['staff']); ?></td>

                            <td> <?php
                            $departments = $this->departments_model->get_staff_departments($overdue_contract['staff']);
                              if(isset($departments[0])){
                $team = $this->hrm_model->get_department_name($departments[0]['departmentid']);
                $str = '';
                $j = 0;
                foreach ($team as $value) {
                    $j++;
                    $str .= '<span class="label label-tag tag-id-1"><span class="tag">'.$value['name'].'</span><span class="hide">, </span></span>&nbsp';
                    if($j%2 == 0){
                         $str .= '<br><br/>';
                    }
                   
                }
                echo ''.$str;
            }
            else{
                echo '';
            } ?>

                              </td>
                            <td><?php echo _d($overdue_contract['start_valid']); ?></td>
                            <td><?php echo _d($overdue_contract['end_valid']); ?></td>
                            <td><?php echo _d($overdue_contract['sign_day']); ?></td>
                        </tr>
                      <?php } ?>
                  </tbody>
                  </table>
                </div>
              </div>
              <div class="panel_s">
                <div class="panel-body">
                  <h4><p class="padding-5 bold"><?php echo _l('list_expire_contract'); ?></p></h4>
                    <hr class="hr-panel-heading-dashboard">
                  <table class="table dt-table scroll-responsive">
                    <thead>
                        <th><?php echo _l('contract_code'); ?></th>
                        <th><?php echo _l('name_contract'); ?></th>
                        <th><?php echo _l('staff'); ?></th>
                        <th><?php echo _l('departments'); ?></th>
                        <th><?php echo _l('start_valid'); ?></th>
                        <th><?php echo _l('end_valid'); ?></th>
                        <th><?php echo _l('sign_day'); ?></th>
                    </thead>
                    <tbody>
                     
                        <?php 
                         $list_member_id = [];
                        foreach($data_dash['expire_contract_data'] as $expire_contract){
                          ?>

                        <tr>
                            <td><?php echo htmlspecialchars($expire_contract['contract_code']); ?></td>
                            <td><a href="<?php echo admin_url('hrm/contract/' . $expire_contract['id_contract']); ?>"><?php echo htmlspecialchars($expire_contract['name_contract']); ?></a></td>
                            <td><?php echo get_staff_full_name($expire_contract['staff']); ?></td>
                          
                            <td> <?php
                            $departments = $this->departments_model->get_staff_departments($expire_contract['staff']);
                              if(isset($departments[0])){
                $team = $this->hrm_model->get_department_name($departments[0]['departmentid']);
                $str = '';
                $j = 0;
                foreach ($team as $value) {
                    $j++;
                    $str .= '<span class="label label-tag tag-id-1"><span class="tag">'.$value['name'].'</span><span class="hide">, </span></span>&nbsp';
                    if($j%2 == 0){
                         $str .= '<br><br/>';
                    }
                   
                }
                echo ''.$str;
            }
            else{
                echo '';
            } ?>

                              </td>
                            <td><?php echo _d($expire_contract['start_valid']); ?></td>
                            <td><?php echo _d($expire_contract['end_valid']); ?></td>

                            <td><?php echo _d($expire_contract['sign_day']); ?></td>
                        </tr>
                      <?php } ?>
                  </tbody>
                  </table>
                </div>
              </div>

              <div class="panel_s">
                <div class="panel-body">
                  <h4><p class="padding-5 bold"><?php echo _l('birthday_in_month'); ?></p></h4>
                    <hr class="hr-panel-heading-dashboard">
                  <table class="table dt-table scroll-responsive">
                    <thead>
                        <th><?php echo _l('hr_code'); ?></th>
                        <th><?php echo _l('staff_dt_name'); ?></th>
                        <th><?php echo _l('staff_dt_email'); ?></th>
                        <th><?php echo _l('department'); ?></th>
                        <th><?php echo _l('birthday'); ?></th>
                        <th><?php echo _l('sex'); ?></th>
                    </thead>
                    <tbody>
                     
                        <?php 
                         $list_member_id = [];
                        foreach($data_dash['staff_birthday'] as $staff){
                          ?>

                        <tr>
                            <td><?php echo htmlspecialchars($staff['staff_identifi']); ?></td>
                            <td><a href="<?php echo admin_url('admin/hrm/member/' . $staff['staffid']); ?>"><?php echo staff_profile_image($staff['staffid'], ['staff-profile-image-small',]); ?></a>
                              <a href="<?php echo admin_url('hrm/member/' . $staff['staffid']); ?>"><?php echo htmlspecialchars($staff['firstname'] . ' ' . $staff['lastname']); ?></a>
                            </td>
                            <td><?php echo htmlspecialchars($staff['email']); ?></td>
                            <td> 
                              <?php
                            $departments = $this->departments_model->get_staff_departments($staff['staffid']);
                              if(isset($departments[0])){
                $team = $this->hrm_model->get_department_name($departments[0]['departmentid']);
                $str = '';
                $j = 0;
                foreach ($team as $value) {
                    $j++;
                    $str .= '<span class="label label-tag tag-id-1"><span class="tag">'.$value['name'].'</span><span class="hide">, </span></span>&nbsp';
                    if($j%2 == 0){
                         $str .= '<br><br/>';
                    }
                   
                }
                echo ''.$str;
            }
            else{
                echo '';
            } ?>
                              </td>
                            <td><?php echo _d($staff['birthday']); ?></td>
                            <td><?php echo _l($staff['sex']); ?></td>

                        </tr>
                      <?php } ?>
                  </tbody>
                  </table>
                </div>
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

        staff_chart_by_age('staff_chart_by_age',<?php echo ''.$staff_chart_by_age; ?>, <?php echo json_encode(_l('staff_chart_by_age')); ?>);
        staff_chart_by_age('contract_type_chart',<?php echo ''.$contract_type_chart; ?>, <?php echo json_encode(_l('contract_type_chart')); ?>);
        //declare function variable radius chart
        function staff_chart_by_age(id, value, title_c){
            Highcharts.setOptions({
            chart: {
                style: {
                    fontFamily: 'inherit',
                    fontWeight:'normal'
                }
            }
           });
            Highcharts.chart(id, {
                chart: {
                    backgroundcolor: '#fcfcfc8a',
                    type: 'variablepie'
                },
                accessibility: {
                    description: null
                },
                title: {
                    text: title_c
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: '<span style="color:{series.color}">'+<?php echo json_encode(_l('invoice_table_quantity_heading')); ?>+'</span>: <b>{point.y}</b> <br/> <span>'+<?php echo json_encode(_l('invoice_table_percentage')); ?>+'</span>: <b>{point.percentage:.0f}%</b><br/>',
                    shared: true
                },
                 plotOptions: {
                    variablepie: {
                        dataLabels: {
                            enabled: false,
                            },
                        showInLegend: true        
                    }
                },
                series: [{
                    minPointSize: 10,
                    innerSize: '20%',
                    zMin: 0,
                    name: <?php echo json_encode(_l('invoice_table_quantity_heading')); ?>,
                    data: value,
                    point:{
                          events:{
                              click: function (event) {
                                 if(this.statusLink !== undefined)
                                 { 
                                   window.location.href = this.statusLink;

                                 }
                              }
                          }
                      }
                }]
            });
        }
</script> 