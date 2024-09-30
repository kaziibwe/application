<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12 ">
            <?php if(isset($booking)){
               echo form_open(admin_url('resourcebooking/add_edit_booking/'.$booking->id),array('id'=>'add_edit_booking-form'));
            }else{
               echo form_open(admin_url('resourcebooking/add_edit_booking'),array('id'=>'add_edit_booking-form'));
            }
             ?>
            <div class="panel_s">
               <div class="panel-body">
                  <div class="col-md-12">
                      <h4 class="no-margin font-bold" ><?php echo htmlspecialchars($title); ?></h4>
                      <hr>
                  </div>
                  <?php if(isset($booking)) {?>
                  <div class="col-md-8">
                      <h4 class="no-margin font-bold" ><?php echo htmlspecialchars($booking->purpose); ?></h4>
                      <hr>
                  </div>
               <?php } ?>
                  <div class="col-md-12">
                      <div class="row">
                          <div class="col-md-12">
                              <div id="additional"></div>
                              <?php 
                              $purpose = (isset($booking) ? $booking->purpose : '');
                              echo render_input('purpose','purpose',$purpose); ?>
                              <?php echo form_hidden('orderer',get_staff_user_id()); ?>
                          </div>
                      </div>
                      <br>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="resource_group"><?php echo _l('resource_group'); ?></label>
                          <select name="resource_group" id="resource_group" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                            <option value=""></option>
                            <?php foreach($resource_group as $rg){ ?>
                            <option value="<?php echo htmlspecialchars($rg['id']); ?>" <?php if(isset($booking) && $booking->resource_group =$rg['id'] ){echo 'selected';} ?>><?php echo htmlspecialchars($rg['group_name']); ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="col-md-6">
                           <label for="resource"><?php echo _l('resource'); ?></label>
                          <select name="resource" id="resource" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                            <option value=""></option>
                            <?php foreach($resources as $rs){ ?>
                            <option value="<?php echo htmlspecialchars($rs['id']); ?>"<?php if(isset($booking) && $booking->resource = $rs['id'] ){echo 'selected';} ?>><?php echo htmlspecialchars($rs['resource_name']); ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>

                      <br>
                     <div class="row">
                        <div class="col-md-6">
                           <?php 
                           $start_time = (isset($booking) ? $booking->start_time : '');
                           echo render_datetime_input('start_time','start_time',$start_time); ?>
                        </div>
                        <div class="col-md-6">
                           <?php 
                           $end_time = (isset($booking) ? $booking->end_time : '');
                           echo render_datetime_input('end_time','end_time',$end_time); ?>
                        </div>
                    </div>
                <br>
                <div class="row">
                  <div class="col-md-12">
                  <label for="follower"><?php echo _l('follower'); ?></label>
                  <select name="follower[]" id="follower" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                    <?php foreach($staff as $s) { ?>
                      <option value="<?php echo htmlspecialchars($s['staffid']); ?>"><?php echo htmlspecialchars($s['firstname']); ?></option>
                      <?php } ?>
                  </select>
                  </div>
                </div>
                 <br>
                    <?php 
                    $description = (isset($booking) ? $booking->description : '');
                    echo render_textarea('description','description',$description); ?>
                    <hr>
                    <div class="notification danger"></div>
                    <button id="sm_btn" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="check_resourcebooking(); return false;" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                  </div>

               </div>
            </div>
            <?php echo form_close(); ?>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script src="<?php echo module_dir_url('resourcebooking','assets/js/add_edit_booking.js'); ?>"></script>
<script>
   function check_resourcebooking(){

      resource = $('#resource').val();
      start_time = $('#start_time').val();
      end_time = $('#end_time').val();
      $.post(admin_url+'resourcebooking/check_resourcebooking/'+resource+'/'+start_time+'/'+end_time).done(function(response){
         response = JSON.parse(response);
         if(response.check == true){
            $("#add_edit_booking-form").submit();
         }else{
            $('.notification').html('');
            $('.notification').append('<label class="danger"><?php echo _l('notification_check_resourcebooking'); ?></label');
         }
      });
   }
</script>