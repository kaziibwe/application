<?php if(count($booking_attachment) > 0){ ?>
 <div class="row task_attachments_wrapper">
    <div class="col-md-12" id="attachments">
       <hr />
       <h4 class="th font-medium mbot15"><?php echo _l('task_view_attachments'); ?></h4>
       <div class="row">
          <?php
             $i = 1;
             // Store all url related data here
             $comments_attachments = array();
             $attachments_data = array();
             $show_more_link_task_attachments = hooks()->apply_filters('show_more_link_task_attachments', 2);
             foreach($booking_attachment as $attachment){ ?>
          <?php ob_start(); ?>
          <div data-num="<?php echo $i; ?>" data-commentid="<?php echo $attachment['comment_file_id']; ?>" data-comment-attachment="<?php echo $attachment['task_comment_id']; ?>" data-task-attachment-id="<?php echo $attachment['id']; ?>" class="task-attachment-col col-md-6<?php if($i > $show_more_link_task_attachments){echo ' hide task-attachment-col-more';} ?>">
             <ul class="list-unstyled task-attachment-wrapper" data-placement="right" data-toggle="tooltip" data-title="<?php echo $attachment['file_name']; ?>" >
                <li class="mbot10 task-attachment<?php if(strtotime($attachment['dateadded']) >= strtotime('-16 hours')){echo ' highlight-bg'; } ?>">
                   <div class="mbot10 pull-right task-attachment-user">
                      <?php if($attachment['staffid'] == get_staff_user_id() || is_admin()){ ?>
                      <a href="#" class="pull-right" onclick="remove_booking_attachment(this,<?php echo $attachment['id']; ?>); return false;">
                      <i class="fa fa fa-times"></i>
                      </a>
                      <?php }
                         $externalPreview = false;
                         $is_image = false;
                         $path = RESOURCEBOOKING_MODULE_UPLOAD_FOLDER .'/' . $booking->id . '/'. $attachment['file_name'];
                         $href_url = site_url('download/file/taskattachment/'. $attachment['attachment_key']);
                         $isHtml5Video = is_html5_video($path);
                         if(empty($attachment['external'])){
                          $is_image = is_image($path);
                          $img_url = site_url('download/preview_image?path='.protected_file_url_by_path($path,true).'&type='.$attachment['filetype']);
                         } else if((!empty($attachment['thumbnail_link']) || !empty($attachment['external']))
                         && !empty($attachment['thumbnail_link'])){
                         $is_image = true;
                         $img_url = optimize_dropbox_thumbnail($attachment['thumbnail_link']);
                         $externalPreview = $img_url;
                         $href_url = $attachment['external_link'];
                         } else if(!empty($attachment['external']) && empty($attachment['thumbnail_link'])) {
                         $href_url = $attachment['external_link'];
                         }
                         if(!empty($attachment['external']) && $attachment['external'] == 'dropbox' && $is_image){ ?>
                      <a href="<?php echo $href_url; ?>" target="_blank" class="" data-toggle="tooltip" data-title="<?php echo _l('open_in_dropbox'); ?>"><i class="fa fa-dropbox" aria-hidden="true"></i></a>
                      <?php } else if(!empty($attachment['external']) && $attachment['external'] == 'gdrive'){ ?>
                      <a href="<?php echo $href_url; ?>" target="_blank" class="" data-toggle="tooltip" data-title="<?php echo _l('open_in_google'); ?>"><i class="fa fa-google" aria-hidden="true"></i></a>
                      <?php }
                         if($attachment['staffid'] != 0){
                           echo '<a href="'.admin_url('profile/'.$attachment['staffid']).'" target="_blank">'.get_staff_full_name($attachment['staffid']) .'</a> - ';
                         } 

                         echo '<span class="text-has-action" data-toggle="tooltip" data-title="'._dt($attachment['dateadded']).'">'.time_ago($attachment['dateadded']).'</span>';
                         ?>
                   </div>
                   <div class="clearfix"></div>
                   <div class="<?php if($is_image){echo 'preview-image';}else if(!$isHtml5Video){echo 'task-attachment-no-preview';} ?>">
                      <?php
                         // Not link on video previews because on click on the video is opening new tab
                         if(!$isHtml5Video){ ?>
                      
                         <?php } ?>
                         <?php if($is_image){ ?>
                         <img src="<?php echo $img_url; ?>" class="img img-responsive">
                         <?php } else if($isHtml5Video) { ?>
                         <video width="100%" height="100%" src="<?php echo site_url('download/preview_video?path='.protected_file_url_by_path($path).'&type='.$attachment['filetype']); ?>" controls>
                            Your browser does not support the video tag.
                         </video>
                         <?php } else { ?>
                         <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i>
                         <?php echo $attachment['file_name']; ?>
                         <?php } ?>
                         <?php if(!$isHtml5Video){ ?>
                      
                      <?php } ?>
                   </div>
                   <div class="clearfix"></div>
                </li>
             </ul>
          </div>
          <?php
             $attachments_data[$attachment['id']] = ob_get_contents();
             if($attachment['task_comment_id'] != 0) {
              $comments_attachments[$attachment['task_comment_id']][$attachment['id']] = $attachments_data[$attachment['id']];
             }
             ob_end_clean();
             echo $attachments_data[$attachment['id']];
             ?>
          <?php
             $i++;
             } ?>
       </div>
    </div>
    <div class="clearfix"></div>
    <?php if(($i - 1) > $show_more_link_task_attachments){ ?>
    <div class="col-md-12" id="show-more-less-task-attachments-col">
       <a href="#" class="task-attachments-more" onclick="slideToggle('.task_attachments_wrapper .task-attachment-col-more', task_attachments_toggle); return false;"><?php echo _l('show_more'); ?></a>
       <a href="#" class="task-attachments-less hide" onclick="slideToggle('.task_attachments_wrapper .task-attachment-col-more', task_attachments_toggle); return false;"><?php echo _l('show_less'); ?></a>
    </div>
    <?php } ?>
    <div class="col-md-12 text-center">
       <hr />
       <a href="<?php echo admin_url('resourcebooking/download_files/'.$booking->id); ?>" class="bold">
       <?php echo _l('download_all'); ?> (.zip)
       </a>
    </div>
 </div>
 <?php } ?>
 <hr />
<a href="#" id="taskCommentSlide" onclick="slideToggle('.tasks-comments'); return false;">
  <h4 class="mbot20 font-medium"><?php echo _l('task_comments'); ?></h4>
</a>
<div class="tasks-comments inline-block full-width simple-editor"<?php if(count($commentss) == 0){echo ' style="display:none"';} ?>>
  <?php echo form_open_multipart(admin_url('resourcebooking/add_booking_comment'),array('id'=>'booking-comment-form','class'=>'dropzone dropzone-manual','style'=>'min-height:auto;background-color:#fff;')); ?>
  <textarea name="comment" placeholder="<?php echo _l('task_single_add_new_comment'); ?>" id="booking_comment" rows="3" class="form-control ays-ignore"></textarea>
  <div id="dropzoneBookingComment" class="dropzoneDragArea dz-default dz-message hide task-comment-dropzone">
     <span><?php echo _l('drop_files_here_to_upload'); ?></span>
  </div>
  <div class="dropzone-booking-comment-previews dropzone-previews"></div>
  <button type="button" class="btn btn-info mtop10 pull-right hide" id="addBookingCommentBtn" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="add_booking_comment('<?php echo htmlspecialchars($booking->id); ?>');" data-comment-booking-id="<?php echo htmlspecialchars($booking->id); ?>">
  <?php echo _l('task_single_add_new_comment'); ?>
  </button>
  <?php echo form_close(); ?>
  <div class="clearfix"></div>
  <?php if(count($commentss) > 0){echo '<hr />';} ?>
  <div id="task-comments" class="mtop10">
     <?php
        $comments = '';
        $len = count($commentss);
        $i = 0;
        foreach ($commentss as $comment) {
          $comments .= '<div id="comment_'.$comment['id'].'" data-commentid="' . $comment['id'] . '" data-task-attachment-id="'.$comment['file_id'].'" class="tc-content task-comment'.(strtotime($comment['dateadded']) >= strtotime('-16 hours') ? ' highlight-bg' : '').'">';
          $comments .= '<a data-task-comment-href-id="'.$comment['id'].'" href="'.admin_url('resourcebooking/booking/'.$booking->id).'#comment_'.$comment['id'].'" class="task-date-as-comment-id"><small><span class="text-has-action inline-block mbot5" data-toggle="tooltip" data-title="'._dt($comment['dateadded']).'">' . time_ago($comment['dateadded']) . '</span></small></a>';
          if($comment['staffid'] != 0){
           $comments .= '<a href="' . admin_url('profile/' . $comment['staffid']) . '" target="_blank">' . staff_profile_image($comment['staffid'], array(
            'staff-profile-image-small',
            'media-object img-circle pull-left mright10'
         )) . '</a>';
        } elseif($comment['contact_id'] != 0) {
           $comments .= '<img src="'.contact_profile_image_url($comment['contact_id']).'" class="client-profile-image-small media-object img-circle pull-left mright10">';
        }
        if ($comment['staffid'] == get_staff_user_id() || is_admin()) {
           $comment_added = strtotime($comment['dateadded']);
           $minus_1_hour = strtotime('-1 hours');
           if(get_option('client_staff_add_edit_delete_task_comments_first_hour') == 0 || (get_option('client_staff_add_edit_delete_task_comments_first_hour') == 1 && $comment_added >= $minus_1_hour) || is_admin()){
             $comments .= '<span class="pull-right"><a href="#" onclick="remove_task_comment(' . $comment['id'] . '); return false;"><i class="fa fa-times text-danger"></i></span></a>';
             $comments .= '<span class="pull-right mright5"><a href="#" onclick="edit_task_comment(' . $comment['id'] . '); return false;"><i class="fa fa-pencil-square"></i></span></a>';
          }
        }
        $comments .= '<div class="media-body">';
        if($comment['staffid'] != 0){
         $comments .= '<a href="' . admin_url('profile/' . $comment['staffid']) . '" target="_blank">' . $comment['staff_full_name'] . '</a> <br />';
        } elseif($comment['contact_id'] != 0) {
         $comments .= '<span class="label label-info mtop5 mbot5 inline-block">'._l('is_customer_indicator').'</span><br /><a href="' . admin_url('clients/client/'.get_user_id_by_contact_id($comment['contact_id']) .'?contactid='.$comment['contact_id'] ) . '" class="pull-left" target="_blank">' . get_contact_full_name($comment['contact_id']) . '</a> <br />';
        }
        $comments .= '<div data-edit-comment="'.$comment['id'].'" class="hide edit-task-comment"><textarea rows="5" id="task_comment_'.$comment['id'].'" class="ays-ignore form-control">'.str_replace('[task_attachment]', '', $comment['content']).'</textarea>
        <div class="clearfix mtop20"></div>
        <button type="button" class="btn btn-info pull-right" onclick="save_edited_comment('.$comment['id'].','.$booking->id.')">'._l('submit').'</button>
        <button type="button" class="btn btn-default pull-right mright5" onclick="cancel_edit_comment('.$comment['id'].')">'._l('cancel').'</button>
        </div>';
        if($comment['file_id'] != 0){
        $comment['content'] = str_replace('[task_attachment]','<div class="clearfix"></div>'.$attachments_data[$comment['file_id']],$comment['content']);
        // Replace lightbox to prevent loading the image twice
        $comment['content'] = str_replace('data-lightbox="task-attachment"','data-lightbox="task-attachment-comment"',$comment['content']);
        } else if(count($comment['attachments']) > 0 && isset($comments_attachments[$comment['id']])) {
         $comment_attachments_html = '';
         foreach($comments_attachments[$comment['id']] as $comment_attachment) {
             $comment_attachments_html .= trim($comment_attachment);
         }
         $comment['content'] = str_replace('[task_attachment]','<div class="clearfix"></div>'.$comment_attachments_html,$comment['content']);
         // Replace lightbox to prevent loading the image twice
         $comment['content'] = str_replace('data-lightbox="task-attachment"','data-lightbox="task-comment-files"',$comment['content']);
         $comment['content'] .='<div class="clearfix"></div>';
         $comment['content'] .='<div class="text-center download-all">
         <hr class="hr-10" />
         <a href="'.admin_url('resourcebooking/download_files/'.$booking->id.'/'.$comment['id']).'" class="bold">'._l('download_all').' (.zip)
         </a>
         </div>';
        }
        $comments .= '<div class="comment-content mtop10">'.app_happy_text(check_for_links($comment['content'])) . '</div>';
        $comments .= '</div>';
        if ($i >= 0 && $i != $len - 1) {
        $comments .= '<hr class="task-info-separator" />';
        }
        $comments .= '</div>';
        $i++;
        }
        echo '' . $comments;
        ?>
  </div>
</div>