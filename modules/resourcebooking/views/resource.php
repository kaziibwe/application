<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-9 col-md-offset-1">

            <div class="panel_s">
               <div class="panel-body">
                  <?php 
                  $this->load->model('resourcebooking/resourcebooking_model');
                  $this->load->model('resourcebooking/resourcebooking_model');
                  
                  $group = $this->resourcebooking_model->get_resource_group_by_id($resource->resource_group);
                  if($resource->status == 'active'){ ?>
                  
                  <span class="label label-success mtop5 inline-block s-status invoice-status-2"><?php echo 'active'; ?></span>
                  <?php } else { ?>
                  <span class="label label-danger mtop5 inline-block s-status invoice-status-1"><?php echo 'deactive'; ?></span>
                  <?php } ?>
                  <br><br>
                  <div class="col-md-4">
                      <h4 class="no-margin font-bold" ><?php echo _l('generals_infor'); ?></h4>
                      <hr>
                  </div>
                  <div class="col-md-12">
                     <table class="table no-margin project-overview-table">
                        <tbody>
                           <tr class="project-overview paddedbooking">
                              <td class="bold"><?php echo _l('resource_name'); ?></td>
                              <td><?php echo htmlspecialchars($resource->resource_name); ?></td>
                           </tr>
                           <tr class="project-overview paddedbooking">
                              <td class="bold"><?php echo _l('resource_group'); ?></td>
                              <td><?php echo '<i class="fa '.$group->icon.'"></i> '.$group->group_name; ?></td>
                           </tr>
                           <tr class="project-overview paddedbooking">
                              <td class="bold"><?php echo _l('manager'); ?></td>
                              <td><a href="<?php echo admin_url('staff/profile/'.$resource->manager); ?>"> <?php echo staff_profile_image($resource->manager, ['staff-profile-image-small',]); ?> </a><a href=" <?php echo admin_url('staff/profile/'.$resource->manager); ?>"><?php echo get_staff_full_name($resource->manager); ?></a>
                              </td>
                           </tr>
                           <tr class="project-overview paddedbooking">
                              <td class="bold"><?php echo _l('color'); ?></td>
                              <td><span class="label label-tag tag-id-1" style="background-color: <?php echo htmlspecialchars($resource->color); ?>;">&nbsp;&nbsp;&nbsp;</span></td>
                           </tr>
                        </tbody>
                     </table>
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
