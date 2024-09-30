            <div class="col-md-6">
              <div class="row">
                  <div class="col-md-12">
                    <div class="panel_s">
                      <div class="panel-body padding-10">
                        <p class="padding-5 bold"><?php echo _l('overview'); ?></p>
                        <hr class="hr-panel-heading-dashboard">
                        <div class="relative pr-height360">
                          <div class="col-md-7">
                              <table class="table no-margin project-overview-table">
                                <tbody>
                                  <tr class="pr-project-overview-charge-code">
                                    <td class="bold"><?php echo _l('project_name'); ?></td>
                                    <td><a href="<?php echo admin_url(); ?>projects/view/<?php echo '' . $project->id; ?>"><?php if(isset($project->charge_code)){ echo '' . $project->charge_code. ' - ';} echo '' . $project->name; ?></a></td>
                                  </tr>
                                  <tr class="pr-project-overview-customer">
                                    <td class="bold"><?php echo _l('task_related'); ?></td>
                                    <td><a href="<?php echo admin_url(); ?>clients/client/<?php echo '' . $project->clientid; ?>"><?php echo '' . $project->client_data->company; ?></a></td>
                                  </tr>
                                     <tr class="pr-project-overview-status">
                                      <td class="bold"><?php echo _l('project_status'); ?></td>
                                      <td><?php echo '' . $project_status['name']; ?></td>
                                   </tr>
                                   <tr class="pr-project-overview-date-finished">
                                      <td class="bold"><?php echo _l('time'); ?></td>
                                      <td><?php echo _d($project->start_date); ?><?php if($project->deadline){ echo ' - '._d($project->deadline); }?></td>
                                   </tr>
                                    <tr class="pr-project-overview-total-logged-hours">
                                      <td class="bold"><?php echo _l('project_member'); ?></td>
                                      <td><?php echo count($members); ?></td>
                                   </tr>
                                </tbody>
                             </table>
                          </div>
                          <div class="col-md-5 text-center project-percent-col mtop10">
                             <p class="bold"><?php echo _l('project_progress'). ' '. _l('project'); ?></p>
                             <div class="project-progress relative mtop15" data-value="<?php echo '' . $percent_circle; ?>" data-size="150" data-thickness="22" data-reverse="true">
                                <strong class="project-percent"></strong>
                             </div>
                          </div>
                        </div>
                      </div>
                    </div>
                 </div>
              </div> 
            </div>
            <div class="col-md-6">
              <div class="panel_s">
                <div class="panel-body">
                  <div id="container_<?php echo '' . $project->id; ?>"></div>
                </div>
              </div> 
            </div>
            <div class="clearfix"></div>
            <div class="col-md-4">
              <div class="panel_s">
                <div class="panel-body">
                  
                  <div id="container_task_<?php echo '' . $project->id; ?>"></div>
                </div>
              </div> 
            </div>
            <div class="col-md-4">
              <div class="panel_s">
                <div class="panel-body">
                  <div id="container_time_<?php echo '' . $project->id; ?>" class="pr-classic-style"></div> 
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="panel_s">
                <div class="panel-body">
                  <div id="container_priority_<?php echo '' . $project->id; ?>" class="pr-classic-style"></div> 
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="panel_s">
                <div class="panel-body">
                  <h4><p class="padding-5 bold"><?php echo _l('list_members'); ?></p></h4>
                    <hr class="hr-panel-heading-dashboard">
                  <table class="table dt-table scroll-responsive">
                    <thead>
                        <th><?php echo _l('name'); ?></th>
                        <th><?php echo _l('total_task_assigned'); ?></th>
                        <th><?php echo _l('total_task_is_completed'); ?></th>
                        <th><?php echo _l('Total_task_late'); ?></th>
                        <th><?php echo _l('project_overview_total_logged_hours'); ?></th>
                        
                    </thead>
                    <tbody>
                     
                        <?php 
                         $list_member_id = [];
                        foreach($members as $member){
                          if(in_array($member["staff_id"], $list_member_id)){
                            continue;
                          }
                          $list_member_id[] = $member["staff_id"];
                        $where = '(' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . $member['staff_id'] . ') OR is_public = 1)';
                          $task = $this->projects_model->get_tasks($project->id,$where,false, false);
                          $task_finish = $this->projects_model->get_tasks($project->id,'status = 5 and '.$where,false, true);
                          $task_late = $this->projects_model->get_tasks($project->id,'status != 5 and duedate < "'.date('Y-m-d').'" and '.$where,false, true);
                            $sum = 0;

                          foreach ($task as $key => $value) {
                            $time = $this->tasks_model->get_timesheeets($value['id']);
            
                            foreach($time as $tm){
                                if($tm['time_spent'] == NULL){

                                   $str = sec2qty(time() - $tm['start_time']);
                                  } else {
                                   
                                   $str =  sec2qty($tm['time_spent']);
                                  }
                                $sum += $str;
                            }
                          }
                          ?>

                        <tr>
                            <td><a href="#" class="display-block" onclick="projectroadmap_tasks_by_member(<?php echo '' . $project->id; ?>,'<?php echo '' . $member['staff_id'];?>'); return false;"><?php echo '' . $member['full_name'];?></a></td>
                            <td><?php echo count($task); ?></td>
                            <td><?php echo '' . $task_finish; ?></td>
                            <td><?php echo '' . $task_late; ?></td>
                            <td><?php echo '' . $sum; ?></td>
                        </tr>
                      <?php } ?>
                  </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="panel_s">
                <div class="panel-body">
                  <h4><p class="padding-5 bold"><?php echo _l('list_milestones'); ?></p></h4>
                    <hr class="hr-panel-heading-dashboard">
                  <table class="table table-milestones dt-table scroll-responsive">
                    <thead>
                      <th><?php echo _l('name'); ?></th>
                        <th><?php echo _l('project_progress'); ?></th>
                        <th><?php echo _l('status'); ?></th>
                        <th><?php echo _l('task_single_start_date'); ?></th>
                        <th><?php echo _l('task_single_due_date'); ?></th>  
                    </thead>
                    <tbody>
                        <?php foreach($milestones as $milestone){
                        $where = 'milestone = '.$milestone['id'];
                          $task = $this->projects_model->get_tasks($project->id,$where,false, false);
                          $task_finish = $this->projects_model->get_tasks($project->id,'status = 5 and '.$where,false, true);
                          $milestone_status = _l('not_started');
                          $color = 'active';
                          $late = 0;
                          foreach ($task as $key => $value) {
                            if($value['status'] != 5 && $value['status'] != 1)
                            {
                              $milestone_status = _l('in_process');
                              $color = 'alert-info';
                            }
                            if($value['duedate'] < date('Y-m-d') && $value['status'] !== 5){
                              $late = 1;
                            }
                          }
                          if(count($task) == $task_finish)
                          {
                            $milestone_status = _l('complete');
                            $color = 'alert-success';
                          }
                          $progress = 0;
                          if ($task_finish >= floatval(count($task))) {
                              $progress = 100;
                          } else {
                              if (count($task) !== 0) {
                                  $progress = number_format(($task_finish * 100) / count($task), 2);
                              }
                          }
                          ?>

                        <tr class="<?php if($late == 1){echo 'alert-danger';}else{ echo '' . $color; } ?>">
                            <td><a href="#" class="display-block" onclick="projectroadmap_tasks_by_milestone(<?php echo '' . $project->id; ?>,'<?php echo '' . $milestone['id'];?>'); return false;"><?php echo '' . $milestone['name'];?></a></td>
                            <td>
                             <?php ob_start();
                              $progress_bar_percent = $progress / 100; ?>
                              <input type="hidden" value="<?php
                              echo '' . $progress_bar_percent; ?>" name="percent">
                              <div class="goal-progress" data-reverse="true">
                                 <strong class="goal-percent pr-goal-percent"><?php
                                  echo '' . $progress; ?>%</strong>
                              </div>
                              <?php
                              $progress_ = ob_get_contents();
                              ob_end_clean(); 
                              echo '' . $progress_;
                              ?>

                            </td>
                            <td><?php echo '' . $milestone_status; ?></td>
                            <td><?php echo _d($milestone['datecreated']); ?></td>
                            <td><?php echo _d($milestone['due_date']); ?></td>
                        </tr>
                      <?php } ?>
                  </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="panel_s">
                <div class="panel-body">
                     <h4><p class="padding-5 bold"><?php echo _l('list_tasks'); ?></p></h4>
                    <hr class="hr-panel-heading-dashboard">
                  <div class="row">
                  <div class="col-md-3">
                  <?php echo render_select('milestones_'.$project->id,$milestones,array('id','name'),'milestones_name','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
                  </div>
                  <div class="col-md-3">
                  <?php echo render_select('members_'.$project->id,$members,array('staff_id','full_name'),'task_single_assignees','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
                  </div>
                  <div class="col-md-3">
                  <?php 
                  $task_status = ['1' => ['id' => '1', 'name' => _l('not_started')],
                                    '2' => ['id' => '2', 'name' => _l('not_started(late)')],
                                    '3' => ['id' => '3', 'name' => _l('in_process')],
                                    '4' => ['id' => '4', 'name' => _l('in_process(late)')],
                                    '5' => ['id' => '5', 'name' => _l('complete')],
                                    '6' => ['id' => '6', 'name' => _l('complete(late)')]
                                    ]; 
                   echo render_select('task_status_'.$project->id,$task_status,array('id','name'),'task_status','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
                  </div>
                  

                </div>
                <br>
                <?php echo form_hidden('project_id',$project->id); ?>
                <?php echo form_hidden('member_id_'.$project->id); ?>
                <?php echo form_hidden('milestone_id_'.$project->id); ?>
                <input type="hidden" class="list_project_id" name="list_project_id[]" value="<?php echo '' . $project->id; ?>" />
                <!-- <?php echo form_hidden('list_project_id[]', $project->id); ?> -->
                  <table class="table table-projectroadmap_tasks_<?php echo '' . $project->id; ?> dt-table scroll-responsive">
                    <thead>
                        <th><?php echo _l('name'); ?></th>
                        <th><?php echo _l('milestones_name'); ?></th>
                        <th><?php echo _l('task_single_assignees'); ?></th>
                        <th><?php echo _l('task_single_start_date'); ?></th>
                        <th><?php echo _l('task_single_due_date'); ?></th>
                        <th><?php echo _l('project_overview_total_logged_hours'); ?></th>
                        <th><?php echo _l('task_status'); ?></th> 
                    </thead>
                    <tbody>
                        <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                  </tbody>
                  </table>
                </div>
              </div>
            </div>
              <script>
                app.options.tables_pagination_limit = 10;
Highcharts.chart('container_task_<?php echo '' . $project->id; ?>', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45
        }
    },

    title: {
        text: '<?php echo _l('statistics_by_task_status'); ?>'
    },
    plotOptions: {
        pie: {
            innerSize: 100,
            depth: 45
        }
    },
    credits: {
        enabled: false
    },
    series: [{
        innerSize: '20%',
        name: '<?php echo _l('total_task'); ?>',
        data: <?php echo '' . $tasks_status_stats; ?>    
      }]
});

Highcharts.chart('container_time_<?php echo '' . $project->id; ?>', {
    chart: {
        type: 'bar'
    },
    title: {
        text: '<?php echo _l('statistics_by_estimate_hour'); ?>'
    },
    xAxis: {
        categories: [''],
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: '<?php echo _l('hours'); ?>',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    tooltip: {
        valueSuffix: ' <?php echo _l('hours'); ?>'
    },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            }
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'top',
        x: -40,
        y: 80,
        floating: true,
        borderWidth: 1,
        backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
        shadow: true
    },
    credits: {
        enabled: false
    },
    series: <?php echo '' . $project_hour_stats; ?>
});

Highcharts.chart('container_priority_<?php echo '' . $project->id; ?>', {
    chart: {
        type: 'column'
    },
    title: {
        text:'<?php echo _l('statistics_by_task_priority'); ?>'
    },
    xAxis: {
        categories: [''],
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: '<?php echo _l('task'); ?>',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    tooltip: {
        valueSuffix: ' <?php echo _l('task'); ?>'
    },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            }
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'top',
        x: -40,
        y: 80,
        floating: true,
        borderWidth: 1,
        backgroundColor:
          Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
        shadow: true
    },
    credits: {
        enabled: false
    },
    series: <?php echo '' . $tasks_priority_stats; ?>
});

  Highcharts.chart('container_<?php echo '' . $project->id; ?>', {
    chart: {
        type: 'variablepie'
    },
    accessibility: {
        description: ''
    },
    title: {
        text: '<?php echo _l('statistics_by_milestone_status'); ?>'
    },
    tooltip: {
        headerFormat: '',
        pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
            '<?php echo _l('total_milestones'); ?>: <b>{point.y}</b><br/>' 
    },
    credits: {
        enabled: false
    },
    series: [{
        minPointSize: 1,
        innerSize: '20%',
        zMin: 0,
        name: 'countries',
        data: <?php echo '' . $milestones_status_stats; ?>
    }]
});
</script>