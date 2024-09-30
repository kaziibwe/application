<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">

     <?php hooks()->do_action('before_staff_myprofile'); ?>
     <div class="col-md-12">
      <div class="panel_s">

        <div class="panel-body">
        <h4 class="no-margin">
          <?php echo _l('staff_profile_string'); ?>
        </h4>
       <hr class="hr-panel-heading" />
          <?php if($member->active == 0){ ?>
          <div class="alert alert-danger text-center"><?php echo _l('staff_profile_inactive_account'); ?></div>
          <hr />
          <?php } ?>
          <div class="button-group mtop10 pull-right">
           <?php if(!empty($member->facebook)){ ?>
            <a href="<?php echo html_escape($member->facebook); ?>" target="_blank" class="btn btn-default btn-icon"><i class="fa-brands fa-facebook"></i></a>
            <?php } ?>
            <?php if(!empty($member->linkedin)){ ?>
            <a href="<?php echo html_escape($member->linkedin); ?>" class="btn btn-default btn-icon"><i class="fa-brands fa-linkedin"></i></a>
            <?php } ?>
            <?php if(!empty($member->skype)){ ?>
            <a href="skype:<?php echo html_escape($member->skype); ?>" data-toggle="tooltip" title="<?php echo html_escape($member->skype); ?>" target="_blank" class="btn btn-default btn-icon"><i class="fa-brands fa-skype"></i></a>
            <?php } ?>
            <?php if(has_permission('staff','','edit') && has_permission('staff','','view')){ ?>
            <a href="<?php echo admin_url('staff/member/'.$member->staffid); ?>" class="btn btn-default btn-icon" title="<?php echo _l('edit') ?>"><i class="fa fa-pencil-square"></i></a>
            <?php } ?>
          </div>

          
            <div class="col-md-4">
                <div class="row text-center">
              <?php echo staff_profile_image($member->staffid,array('staff-profile-image-thumb'),'thumb'); ?>
                </div>
              <div class="profile mtop20 display-inline-block">

                <h4 class="text-center">

                  <?php if($member->last_activity && $member->staffid != get_staff_user_id()){ ?>
                  <small>  <?php echo _l('last_active'); ?>:
                    <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($member->last_activity); ?>">
                      <?php echo time_ago($member->last_activity); ?>
                    </span>
                  </small>
                <?php } ?>
                </h4>
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="font-weight-bold">
                        <?php echo _l('full_name'); ?>
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <?php echo htmlspecialchars($member->firstname) . ' ' . htmlspecialchars($member->lastname); ?>
                    </div>
                </div>


                <p class="display-block"><i class="fa fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($member->email); ?>"><?php echo htmlspecialchars($member->email); ?></a></p>
                <?php if($member->phonenumber != ''){ ?>
                <p><i class="fa fa-phone-square"></i> <?php echo htmlspecialchars($member->phonenumber); ?></p>
                <?php } ?>
                <?php if(count($staff_departments) > 0) { ?>
                <div class="form-group mtop10">
                  <label for="departments" class="control-label"><?php echo _l('staff_profile_departments'); ?></label>
                  <div class="clearfix"></div>
                  <?php
                  foreach($departments as $department){ ?>
                  <?php
                  foreach ($staff_departments as $staff_department) {
                   if($staff_department['departmentid'] == $department['departmentid']){ ?>
                   <div class="chip-circle"><?php echo htmlspecialchars($staff_department['name']); ?></div>
                   <?php }
                 }
                 ?>
                 <?php } ?>
               </div>
               <?php } ?>
             </div>
            </div>
            <div class="col-md-8">
                <div class="col-md-6">
                    
                </div>

                <div class="col-md-6">
                    
                </div>
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
