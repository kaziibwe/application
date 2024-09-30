<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-6 col-md-offset-1">

            <div class="panel_s">
               <div class="panel-body">
                  <?php 
                  $this->load->model('resourcebooking/resourcebooking_model');
                  $time = $this->resourcebooking_model->get_time_booking($booking->start_time,$booking->end_time);
                  $group = $this->resourcebooking_model->get_resource_group_by_id($resource->resource_group);
                   ?>
                  
                  <div class="col-md-3">
                      <h4 class="no-margin font-bold" ><?php echo htmlspecialchars($title); ?></h4>
                      <hr>
                  </div>
                  <div class="col-md-9">
                      <h4 class="no-margin font-bold" ><?php echo htmlspecialchars($booking->purpose); ?></h4>
                      <hr>
                  </div>
                  <div class="col-md-12">

                     <table class="table no-margin project-overview-table">
                        <tbody>
                           <tr class="project-overview paddedbooking">
                              <td class="bold"><?php echo _l('resource_name'); ?></td>
                              <td style="color:<?php echo htmlspecialchars($resource->color); ?>;"><?php echo htmlspecialchars($resource->resource_name); ?></td>
							  </tr>
                           <tr class="project-overview paddedbooking">
                              <td class="bold"><?php echo _l('resource_group'); ?></td>
                              <td><?php echo '<i class="fa '.$group->icon.'"></i> '.$group->group_name; ?></td>
                           </tr>
                           <tr class="project-overview paddedbooking">
                              <td class="bold"><?php echo _l('orderer'); ?></td>
                              <td><a href="<?php echo admin_url('staff/profile/'.$booking->orderer); ?>"> <?php echo staff_profile_image($booking->orderer, ['staff-profile-image-small',]); ?> </a><a href=" <?php echo admin_url('staff/profile/'.$booking->orderer); ?>"><?php echo get_staff_full_name($booking->orderer); ?></a></td>
                           </tr>
                           <tr class="project-overview paddedbooking">
                              <td class="bold"><?php echo _l('manager'); ?></td>
                              <td><a href="<?php echo admin_url('staff/profile/'.$resource->manager); ?>"> <?php echo staff_profile_image($resource->manager, ['staff-profile-image-small',]); ?> </a><a href=" <?php echo admin_url('staff/profile/'.$resource->manager); ?>"><?php echo get_staff_full_name($resource->manager); ?></a>
                              </td>
                           </tr>
                           <tr class="project-overview paddedbooking">
                              <td class="bold"><?php echo _l('description'); ?></td>
                              <td><?php echo htmlspecialchars($booking->description); ?></td>
                           </tr>
                           <tr class="project-overview paddedbooking">
                              <td class="bold"><?php echo _l('booktime'); ?></td>
                              <td><?php echo '' . $time; ?></td>
                           </tr>
                        </tbody>
                     </table>
                     <hr>
                     <?php if(($resource->approved != 0) && ($resource->manager != 0) && ($resource->manager == get_staff_user_id()) && ($booking->status == 1)){ ?>
                        <a href="<?php echo admin_url('resourcebooking/approve_booking/'.'3'.'/'.$booking->id); ?>" class="btn btn-warning pull-right mleft10 display-block">
                            <?php echo _l('reject'); ?>
                        </a>
                        <a href="Javascript:void(0);" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_booking(<?php echo htmlspecialchars($booking->id); ?>); return false;" class="btn btn-success pull-right display-block">
                           <?php echo _l('approve'); ?>
                        </a>
                     <?php } ?>
                  </div>

               </div>
            </div>
            <div class="panel_s">
               <div class="panel-body">
                  <div class="col-md-12">
                      <h4 class="no-margin font-bold" ><?php echo _l('comments_activity'); ?></h4>
                      <hr>
                  </div>
                  <div class="data task-modal-single" id="view_booking_comment">
                     <?php include_once('booking_comment.php') ?> 
                  </div>
              </div>
            </div>
          </div>
         <div class="col-md-4">
              <div class="panel_s">
               <div class="panel-body">
                  <div class="col-md-12">
                      <h4 class="no-margin font-bold" ><?php echo _l('follower'); ?></h4>
                      <hr>
                  </div>
                   <div class="row">
                        <div class="col-md-12">
                  <?php foreach($follower as $fol){ ?>
                        <a href="<?php echo admin_url('staff/profile/' . $fol['follower']); ?>"><?php echo staff_profile_image($fol['follower'], [
                'staff-profile-image-small'], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => get_staff_full_name($fol['follower']),
                ]); ?> </a>&nbsp;

                  <?php } ?>
                  </div>
                     </div>
               </div>
            </div>
            <div class="panel_s">
               <div class="panel-body">
                  <div class="col-md-12">
                      <h4 class="no-margin font-bold" ><?php echo _l('resource_detail'); ?></h4>
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
<div class="modal fade" id="list_sending" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('resourcebooking/reject_list_booking/'.$booking->id),array('id'=>'list_sending-form')); ?>
        <div class="modal-content rbfullwidth">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="add-title"><?php echo _l('list_sending'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
               <div class="notification bold"><?php echo _l('notification_approve_booking'); ?></div>
               <div id="list">
                  
               </div>
               <div id="input">
                  
               </div>
            </div>
                <div class="modal-footer">
                    <button type="
                    " class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                    <button id="sm_btn" type="submit" class="btn btn-info"><?php echo _l('continue'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
   <div class="modal fade" id="list_approve" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        
        <div class="modal-content rbfullwidth">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="add-title"><?php echo _l('list_approve'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="notification2 bold"><?php echo _l('notification_approve_booking2'); ?></div>
               <div id="approved">
                  
               </div>
               <div  id="list_id">
                  
               </div>
            </div>
                <div class="modal-footer">
                    <button type="
                    " class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php init_tail(); ?>
</body>
</html>
<script type="text/javascript" src="<?php echo module_dir_url('resourcebooking','assets/js/booking.js'); ?>"></script>
<script>
function init_new_booking_comment(manual) {

    if (tinymce.editors.booking_comment) {
        tinymce.remove('#booking_comment');
    }

    if (typeof(bookingCommentAttachmentDropzone) != 'undefined') {
        bookingCommentAttachmentDropzone.destroy();
    }

    $('#dropzoneBookingComment').removeClass('hide');
    $('#addBookingCommentBtn').removeClass('hide');
    var $booking = $('#view_booking_comment');
        
    bookingCommentAttachmentDropzone = new Dropzone("#booking-comment-form", appCreateDropzoneOptions({
        uploadMultiple: true,
        clickable: '#dropzoneBookingComment',
        previewsContainer: '.dropzone-booking-comment-previews',
        autoProcessQueue: false,
        addRemoveLinks: true,
        parallelUploads: 20,
        maxFiles: 20,
        paramName: 'file',
        sending: function(file, xhr, formData) {
            formData.append("booking", $('#addBookingCommentBtn').attr('data-comment-booking-id'));
            if (tinyMCE.activeEditor) {
                formData.append("content", tinyMCE.activeEditor.getContent());
            } else {
                formData.append("content", $('#booking_comment').val());
            }
        },
        success: function(files, response) {
            response = JSON.parse(response);
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                $booking.html(response.taskHtml);
                tinymce.remove('#booking_comment');
            }
        }
    }));
    var editorConfig = _simple_editor_config();
    if (typeof(manual) == 'undefined' || manual === false) {
        editorConfig.auto_focus = true;
    }
    var iOS = is_ios();
    // Not working fine on iOs
    if (!iOS) {
        init_editor('#booking_comment', editorConfig);
    }
}
</script>