<div class="modal fade" id="noti_lost_modal" tabindex="-1" role="dialog">
<div class="modal-dialog">
<?php echo form_open_multipart(admin_url('assets/lost_asset'), ['id'=>'lost_assets-form']); ?>
<div class="modal-content modalwidth">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
            <span class="add-title"><?php  echo htmlspecialchars(_l('noti_lost')); ?></span>
        </h4>
    </div>
    <div class="modal-body">
        <div class="row">
        <div class="col-md-12">
            <?php echo render_input('acction_code', 'lost_code', ''); ?>
            <table class="table border table-striped nomargintop">
                <tbody>
                   <tr class="project-overview">
                      <td class="bold"><?php  echo htmlspecialchars(_l('asset_code')); ?></td>
                      <td><?php  echo htmlspecialchars($assets->assets_code); ?></td>
                      <td class="bold"><?php  echo htmlspecialchars(_l('asset_name')); ?></td>
                      <td><?php  echo htmlspecialchars($assets->assets_name); ?></td>
                   </tr>
               </tbody>
           </table>
        </div>
        <div class="col-md-6">
            <?php $attr = [];
            $attr       = ['min' => 1, 'max' => $assets->amount, 'step' => 1];
            echo render_input('amount', 'amounts', '', 'number', $attr); ?>
        </div>
        <div class="col-md-6">
            <?php echo render_datetime_input('time_acction', 'lost_time'); ?>
        </div>
        <div class="col-md-12">
            <?php echo render_textarea('description', 'description', ''); ?>
            <?php echo form_hidden('assets', $assets->id); ?>
            <?php echo form_hidden('type', 'lost'); ?>
        </div>
    </div>
      
    </div>
        <div class="modal-footer">
            <button type="
            " class="btn btn-default" data-dismiss="modal"><?php  echo htmlspecialchars(_l('close')); ?></button>
            <button id="sm_btn" type="submit" class="btn btn-info"><?php  echo htmlspecialchars(_l('submit')); ?></button>
        </div>
    </div><!-- /.modal-content -->
    <?php echo form_close(); ?>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
appValidateForm($('#lost_assets-form'),{amount:'required',time_acction:'required',acction_code: {
   required: true,
   remote: {
    url: site_url + "admin/assets/acction2_code_exists",
    type: 'post',
    data: {
        assets_code: function() {
            return $('input[name="acction_code"]').val();
        }
    }
}
}});
</script>