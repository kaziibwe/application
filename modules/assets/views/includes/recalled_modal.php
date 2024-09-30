<div class="modal fade" id="recalled_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('assets/revoke_asset'), ['id'=>'revoke-form']); ?>
        <div class="modal-content modalwidth">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="add-title"><?php  echo htmlspecialchars(_l('recalled')); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('acction_code', 'recalled_code', ''); ?>
                        
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
                        <label for="acction_to"><?php  echo htmlspecialchars(_l('recalled_from')); ?></label>
                        <select name="acction_to" onchange="get_asset_allocation_by_staff(this); return false;" id="acction_to" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php  echo htmlspecialchars(_l('ticket_settings_none_assigned')); ?>">
                            <option value=""></option>
                            <?php foreach ($staffs as $s) { ?>
                            <option value="<?php  echo htmlspecialchars($s['staffid']); ?>"><?php  echo htmlspecialchars($s['staffid']).' '.htmlspecialchars($s['firstname']).' '.htmlspecialchars($s['lastname']); ?></option>
                              <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="acction_from"><?php  echo htmlspecialchars(_l('who_recalled')); ?></label>
                        <select name="acction_from" id="acction_from" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php  echo htmlspecialchars(_l('ticket_settings_none_assigned')); ?>">
                            <option value=""></option>
                            <?php foreach ($staffs as $s) { ?>
                            <option value="<?php  echo htmlspecialchars($s['staffid']); ?>" <?php if (htmlspecialchars($s['staffid']) == get_staff_user_id()) {
    echo 'selected';
} ?>><?php  echo htmlspecialchars($s['staffid']).' '.htmlspecialchars($s['firstname']).' '.htmlspecialchars($s['lastname']); ?></option>
                              <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="amount" class="control-label"><?php  echo htmlspecialchars(_l('amounts')); ?></label>
                        <input type="number" id="amount_revoke" name="amount" class="form-control" min="1" step="1" value="">
                    </div>
                    <div class="col-md-6">
                        <table class="table border table-striped margintop25">
                            <tbody>
                               <tr class="project-overview">
                                  <td class="bold"><?php  echo htmlspecialchars(_l('has_been_allocated')).':'; ?></td>
                                  <td id="total_allocated"></td>
                               </tr>
                           </tbody>
                       </table>
                    </div>
                    <div class="col-md-6">
                        <?php echo render_datetime_input('time_acction', 'recalled_time', _dt(date('Y-m-d H:i:s'))); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo render_input('asset_location', 'asset_location', get_asset_location($assets->asset_location)); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('acction_location', 'handover_location', ''); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_textarea('acction_reason', 'acction_reason', ''); ?>
                        <?php echo form_hidden('assets', $assets->id); ?>
                        <?php echo form_hidden('type', 'revoke'); ?>
                    </div>
                </div>
              
            </div>
                <div class="modal-footer">
                    <button type="
                    " class="btn btn-default" data-dismiss="modal"><?php  echo htmlspecialchars(_l('close')); ?></button>
                    <button type="submit" class="btn btn-info"><?php  echo htmlspecialchars(_l('submit')); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script>
        init_datepicker();
         appValidateForm($('#revoke-form'),{acction_to:'required',acction_from:'required',amount:'required',time_acction:'required',acction_location:'required',acction_code: {
               required: true,
               remote: {
                url: site_url + "admin/assets/acction_code_exists",
                type: 'post',
                data: {
                    assets_code: function() {
                        return $('input[name="acction_code"]').val();
                    }
                }
            }
           }});
        function get_asset_allocation_by_staff(invoker){
            assets = $('input[name="assets"]').val();
            $.post(admin_url+'assets/get_asset_allocation_by_staff/'+invoker.value+'/'+assets).done(function(response){
                response = JSON.parse(response);
                $('td[id="total_allocated"]').html('');
                $('td[id="total_allocated"]').append(response.total);
                $('input[id="amount_revoke"]').removeAttr('max');
                $('input[id="amount_revoke"]').attr('max',response.total);
            });
        }
        $('.selectpicker').selectpicker({
      });
    </script>