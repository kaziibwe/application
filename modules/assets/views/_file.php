<div class="modal fade _project_file bigzindex" tabindex="-1" role="dialog" data-toggle="modal">
   <div class="modal-dialog full-screen-modal width65" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" onclick="close_modal_preview(); return false;"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo htmlspecialchars($file->file_name); ?></h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12 border-right project_file_area">
                  <?php if (!empty($file->external) && 'dropbox' == $file->external) { ?>
                  <a href="<?php echo htmlspecialchars($file->external_link); ?>" target="_blank" class="btn btn-info mbot20"><i class="fa fa-dropbox" aria-hidden="true"></i> <?php echo htmlspecialchars(_l('open_in_dropbox')); ?></a><br />
                  <?php } ?>
                  <?php
                     $path = ASSETS_UPLOAD_FOLDER.'/'.$file->rel_id.'/'.$file->file_name;
                     if (is_image($path)) { ?>
                  <img src="<?php echo htmlspecialchars(base_url(ASSETS_PATH.$file->rel_id.'/'.$file->file_name)); ?>" class="img img-responsive automargin">
                  <?php } elseif (!empty($file->external) && !empty($file->thumbnail_link)) { ?>
                  <img src="<?php echo htmlspecialchars(optimize_dropbox_thumbnail($file->thumbnail_link)); ?>" class="img img-responsive">
                  <?php } elseif (false !== strpos($file->file_name, '.pdf') && empty($file->external)) { ?>
                  <iframe src="<?php echo htmlspecialchars(base_url(ASSETS_PATH.$file->rel_id.'/'.$file->file_name)); ?>" height="100%" width="100%" frameborder="0"></iframe>
                  <?php } elseif (false !== strpos($file->file_name, '.xls') && empty($file->external)) { ?>
                  <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo htmlspecialchars(base_url(ASSETS_PATH.$file->rel_id.'/'.$file->file_name)); ?>' width='100%' height='100%' frameborder='0'>
                  </iframe>
                  <?php } elseif (false !== strpos($file->file_name, '.xlsx') && empty($file->external)) { ?>
                  <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo htmlspecialchars(base_url(ASSETS_PATH.$file->rel_id.'/'.$file->file_name)); ?>' width='100%' height='100%' frameborder='0'>
                  </iframe>
                  <?php } elseif (false !== strpos($file->file_name, '.doc') && empty($file->external)) { ?>
                  <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo htmlspecialchars(base_url(ASSETS_PATH.$file->rel_id.'/'.$file->file_name)); ?>' width='100%' height='100%' frameborder='0'>
                  </iframe>
                  <?php } elseif (false !== strpos($file->file_name, '.docx') && empty($file->external)) { ?>
                  <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo htmlspecialchars(base_url(ASSETS_PATH.$file->rel_id.'/'.$file->file_name)); ?>' width='100%' height='100%' frameborder='0'>
                  </iframe>
                  <?php } elseif (is_html5_video($path)) { ?>
                  <video width="100%" height="100%" src="<?php echo htmlspecialchars(site_url('download/preview_video?path='.protected_file_url_by_path($path).'&type='.$file->filetype)); ?>" controls>
                     Your browser does not support the video tag.
                  </video>
                  <?php } elseif (is_markdown_file($path) && $previewMarkdown = markdown_parse_preview($path)) {
                         echo htmlspecialchars($previewMarkdown);
                     } else {
                         echo '<p class="text-muted">'.htmlspecialchars(_l('no_preview_available_for_file')).'</p>';
                     } ?>
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" onclick="close_modal_preview(); return false;"><?php echo htmlspecialchars(_l('close')); ?></button>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php $discussion_lang = get_project_discussions_language_array(); ?>
<script>
   var discussion_id = '<?php echo htmlspecialchars($file->id); ?>';
   var discussion_user_profile_image_url = '<?php echo htmlspecialchars($discussion_user_profile_image_url); ?>';
   var current_user_is_admin = '<?php echo is_admin(); ?>';
   $('body').on('shown.bs.modal', '._project_file', function() {
     var content_height = ($('body').find('._project_file .modal-content').height() - 165);
     if($('iframe').length > 0){
       $('iframe').css('height',content_height);
     }
     if(!is_mobile()){
      $('.project_file_area,.project_file_discusssions_area').css('height',content_height);
    }
   });
   $('body').find('._project_file').modal({show:true, backdrop:'static', keyboard:false});
</script>
