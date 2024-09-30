<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_asset_group(); return false;" class="btn btn-info pull-left display-block">
        <?php  echo htmlspecialchars(_l('new_asset_group')); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php  echo htmlspecialchars(_l('id')); ?></th>
    <th><?php  echo htmlspecialchars(_l('group_name')); ?></th>
    <th><?php  echo htmlspecialchars(_l('assets_amount')); ?></th>
    <th><?php  echo htmlspecialchars(_l('options')); ?></th>
 </thead>
 <tbody>
    <?php foreach ($asset_group as $c) { ?>
    <tr>
       <td><?php  echo htmlspecialchars($c['group_id']); ?></td>
       <td> <a href="#" onclick="edit_asset_group(this,<?php  echo htmlspecialchars($c['group_id']); ?>); return false" data-name="<?php  echo htmlspecialchars($c['group_name']); ?>"><?php  echo htmlspecialchars($c['group_name']); ?></td>
       <td></td>
       <td>
         <a href="#" onclick="edit_asset_group(this,<?php  echo htmlspecialchars($c['group_id']); ?>); return false" data-name="<?php  echo htmlspecialchars($c['group_name']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
          <a href="<?php echo admin_url('assets/delete_assets_group/'.$c['group_id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
       </td>
    </tr>
    <?php } ?>
 </tbody>
</table>       
<div class="modal fade" id="asset_group" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('assets/asset_group')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php  echo htmlspecialchars(_l('edit_asset_group')); ?></span>
                    <span class="add-title"><?php  echo htmlspecialchars(_l('new_asset_group')); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional"></div>   
                     <div class="form">     
                        <?php
                        echo render_input('group_name', 'group_name'); ?>

                    </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php  echo htmlspecialchars(_l('close')); ?></button>
                    <button type="submit" class="btn btn-info"><?php  echo htmlspecialchars(_l('submit')); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
    <script>       
        function new_asset_group(){
            $('#asset_group').modal('show');
            $('.edit-title').addClass('hide');
            $('.add-title').removeClass('hide');
            $('#additional').html('');
        }
        function edit_asset_group(invoker,id){
            $('#additional').append(hidden_input('id',id));
            $('#asset_group input[name="group_name"]').val($(invoker).data('name'));
            $('#asset_group').modal('show');
            $('.add-title').addClass('hide');
            $('.edit-title').removeClass('hide');
        }

    </script>
</body>
</html>
