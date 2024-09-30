<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
   $this->load->model('resourcebooking/resourcebooking_model');
   $me = get_staff_user_id();
   $total = $this->resourcebooking_model->get_myboking($me,'total');
   $sending = $this->resourcebooking_model->get_myboking($me,'sending');
   $approved = $this->resourcebooking_model->get_myboking($me,'approved');
   $reject = $this->resourcebooking_model->get_myboking($me,'reject');
   $recently_booking = $this->resourcebooking_model->get_recently_booking($me);
   $apr_bking = $this->resourcebooking_model->get_apr_booking($me);
?>
<?php if(count($total) > 0) {?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>">
   <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body padding-10">
                  <div class="widget-dragger"></div>
                  <p class="padding-5">
                     <i class="fa fa-vcard-o"></i><?php echo ' '._l('my_booking'); ?>
                  </p>
                  <ul class="nav nav-tabs" role="tablist">
                     <li role="presentation" class="active">
                        <a href="#statistical" aria-controls="statistical" role="tab" data-toggle="tab">
                        <i class="fa fa-list"></i><?php echo ' '._l('statistical'); ?>
                        </a>
                     </li>
                     <li role="presentation">
                        <a href="#recently_booking" aria-controls="recently_booking" role="tab" data-toggle="tab">
                        <i class="fa fa-clock-o"></i><?php echo ' '._l('recently_booking'); ?>
                        </a>
                     </li> 
                     <?php if(count($apr_bking) > 0){ ?>
                        <li role="presentation">
                        <a href="#apr_booking" aria-controls="apr_booking" role="tab" data-toggle="tab">
                       <i class="fa fa-envelope-o"></i>  <?php echo ' '._l('apr_booking'); ?><span class="badge menu-badge bg-warning"><?php echo count($apr_bking); ?></span>
                        </a>
                     </li> 
                     <?php } ?>
                  </ul>
                  <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="statistical">
                     <div class="row">
                     <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
                       <div class="top_stats_wrapper rbminheight85">
                           <a class="text-warning mbot15">
                           <p class="text-uppercase mtop5 rbminheight35"><i class="hidden-sm glyphicon glyphicon-edit"></i> <?php echo _l('total'); ?>
                           </p>
                              <span class="pull-right bold no-mtop rbfontsize24"><?php echo count($total); ?></span>
                           </a>
                           <div class="clearfix"></div>
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-warning no-percent-text not-dynamic .rbfullwidth" role="progressbar" aria-valuenow="<?php echo count($total); ?>" aria-valuemin="0" aria-valuemax="<?php echo count($total); ?>" data-percent="100%">
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
                       <div class="top_stats_wrapper rbminheight85">
                           <a class="text-info mbot15">
                           <p class="text-uppercase mtop5 rbminheight35"><i class="hidden-sm glyphicon glyphicon-envelope"></i> <?php echo _l('sending'); ?>
                           </p>
                              <span class="pull-right bold no-mtop rbfontsize24"><?php echo count($sending); ?></span>
                           </a>
                           <div class="clearfix"></div>
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo count($sending); ?>" aria-valuemin="0" aria-valuemax="<?php echo count($total); ?>" style="width: <?php echo (count($sending)/count($total))*100; ?>%" data-percent=" <?php echo (count($sending)/count($total))*100; ?>%">
                              </div>
                           </div>
                        </div>
                     </div> 
                    
                        <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
                             <div class="top_stats_wrapper rbminheight85">
                                 <a class="text-success mbot15">
                                 <p class="text-uppercase mtop5 rbminheight35"><i class="hidden-sm glyphicon glyphicon-ok"></i> <?php echo _l('approved'); ?>
                                 </p>
                                    <span class="pull-right bold no-mtop rbfontsize24"><?php echo count($approved); ?></span>
                                 </a>
                                 <div class="clearfix"></div>
                                 <div class="progress no-margin progress-bar-mini">
                                    <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo count($approved); ?>" aria-valuemin="0" aria-valuemax="<?php echo count($total); ?>" style="width: <?php echo (count($approved)/count($total))*100; ?>%" data-percent=" <?php echo (count($approved)/count($total))*100; ?>%">
                                    </div>
                                 </div>
                              </div>
                           </div>
                        <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
                          <div class="top_stats_wrapper rbminheight85">
                              <a class="text-danger mbot15">
                              <p class="text-uppercase mtop5 rbminheight35"><i class="hidden-sm glyphicon glyphicon-remove"></i> <?php echo _l('reject'); ?>
                              </p>
                                 <span class="pull-right bold no-mtop rbfontsize24"><?php echo count($reject); ?></span>
                              </a>
                              <div class="clearfix"></div>
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo count($reject); ?>" aria-valuemin="0" aria-valuemax="<?php echo count($total); ?>" style="width:  <?php echo (count($reject)/count($total))*100; ?>%" data-percent=" <?php echo (count($reject)/count($total))*100; ?>%">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="recently_booking">
                     <div class="btn-group">
                     <a href="<?php echo admin_url('resourcebooking/manage_booking');?>" class="btn"><i class="fa fa-mail-reply-all" data-toggle="tooltip" data-placement="bottom" data-original-title="<?php echo _l('home_widget_view_all'); ?>"></i></a>
                     </div>
                     <table class="table recently-overview dt-table scroll-responsive" id="recently-overview">
                     <thead>
                        <tr>
                           <th><?php echo _l('purpose'); ?></th>
                           <th><?php echo _l('resource'); ?></th>
                           <th><?php echo _l('start_time'); ?></th>
                           <th><?php echo _l('end_time'); ?></th>
                           <th><?php echo _l('status'); ?></th>
                        </tr>
                     </thead>
                     <tbody> 
                        <?php if(isset($recently_booking)){
                        foreach($recently_booking as $bk){ 
                           $resource = $this->resourcebooking_model->get_resource($bk['resource']);
                           if($bk['status'] == 1){
                               $_data = '<span class="label label inline-block project-status-color-completed">' . _l('sending') . '</span>';
                           }elseif($bk['status'] == 2){
                               $_data = '<span class="label label inline-block project-status-color-completed">' . _l('approved') . '</span>';
                           }elseif($bk['status'] == 3){
                               $_data = '<span class="label label inline-block project-status-color-completed">' . _l('reject') . '</span>';
                           }
                           ?>
                           <tr>
                             <td data-order="<?php echo htmlspecialchars($bk['purpose']); ?>"><a href="<?php echo admin_url('resourcebooking/booking/' . $bk['id']) ?>"><?php echo htmlspecialchars($bk['purpose']); ?></a></td>
                             <td data-order="<?php echo htmlspecialchars($resource->resource_name); ?>"><a href="<?php echo admin_url('resourcebooking/resource/' . $bk['resource']) ?>"><?php echo htmlspecialchars($resource->resource_name); ?></a></td>
                             <td data-order="<?php echo htmlspecialchars($bk['start_time']); ?>"><?php echo _dt($bk['start_time']); ?></td>
                             <td data-order="<?php echo htmlspecialchars($bk['end_time']); ?>"><?php echo _dt($bk['end_time']); ?></td>
                             <td data-order="<?php echo htmlspecialchars($bk['status']); ?>"><?php echo htmlspecialchars($_data); ?></td>
                           </tr>
                        <?php } }?>
                     </tbody>
                     </table>
                  </div>
                  <?php if(count($apr_bking) > 0){ ?>
                   <div role="tabpanel" class="tab-pane" id="apr_booking">
                     <table class="table apr-overview dt-table scroll-responsive" id="apr-overview">
                     <thead>
                        <tr>
                           <th><?php echo _l('purpose'); ?></th>
                           <th><?php echo _l('resource'); ?></th>
                           <th><?php echo _l('start_time'); ?></th>
                           <th><?php echo _l('end_time'); ?></th>
                           <th><?php echo _l('status'); ?></th>
                        </tr>
                     </thead>
                     <tbody> 
                        <?php 
                         foreach($apr_bking as $bk){ 
                           $resource = $this->resourcebooking_model->get_resource($bk['resource']);
                           if($bk['status'] == 1){
                               $_data = '<span class="label label inline-block project-status-color-completed">' . _l('sending') . '</span>';
                           }elseif($bk['status'] == 2){
                               $_data = '<span class="label label inline-block project-status-color-completed">' . _l('approved') . '</span>';
                           }elseif($bk['status'] == 3){
                               $_data = '<span class="label label inline-block project-status-color-completed">' . _l('reject') . '</span>';
                           }
                           ?>
                           <tr>
                             <td data-order="<?php echo htmlspecialchars($bk['purpose']); ?>"><a href="<?php echo admin_url('resourcebooking/booking/' . $bk['id']) ?>"><?php echo htmlspecialchars($bk['purpose']); ?></a></td>
                             <td data-order="<?php echo htmlspecialchars($resource->resource_name); ?>"><a href="<?php echo admin_url('resourcebooking/resource/' . $bk['resource']) ?>"><?php echo htmlspecialchars($resource->resource_name); ?></a></td>
                             <td data-order="<?php echo htmlspecialchars($bk['start_time']); ?>"><?php echo _dt($bk['start_time']); ?></td>
                             <td data-order="<?php echo htmlspecialchars($bk['end_time']); ?>"><?php echo _dt($bk['end_time']); ?></td>
                             <td data-order="<?php echo htmlspecialchars($bk['status']); ?>"><?php echo htmlspecialchars($_data); ?></td>
                           </tr>
                        <?php } ?>
                     </tbody>
                     </table>
                   </div>
                 <?php } ?>
                  </div>
               </div>
            </div>
         </div>
   </div>
</div>
<?php } ?>
