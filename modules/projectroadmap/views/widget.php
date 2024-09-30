<?php 
$this->load->model('projectroadmap/projectroadmap_model');
$projectroadmap_list = $this->projectroadmap_model->get_filter_widget(get_staff_user_id(),'projectroadmap');
?>
<?php foreach ($projectroadmap_list as $projectroadmap) { ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('projectroadmap'); ?>">
<div class="panel_s user-data">
  <div class="panel-body">
    <div class="widget-dragger"></div>
     <?php $data = $this->projectroadmap_model->view_projectroadmap_helper($projectroadmap['rel_id']); 
     ?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6">
        <?php if(isset($data['project']->charge_code)){
          $name = $data['project']->charge_code.' - '. $data['project']->name;
        }else{
          $name = $data['project']->name;
        } ?>
         <p class="text-dark text-uppercase bold"><?php echo _l('projectroadmap').': '.$name ?></p>
      </div>
         <div class="col-md-3 pull-right">
          <a href="Javascript:void(0);" class="pull-right btn btn-danger btn-icon" data-toggle="tooltip" title="" onclick="remove_projectroadmap_dashboard(<?php echo '' . $projectroadmap['id']; ?>)" data-original-title="<?php echo _l('remove_dashboard'); ?>"><i class="fa fa-compress"></i></a> 
         </div>
         <br>
         <hr class="mtop15" />
      </div>
     <?php $this->load->view('projectroadmap/view_projectroadmap_dashboard', $data); ?>
     </div>
    </div>
  </div>
</div>
<?php }
?>

 

