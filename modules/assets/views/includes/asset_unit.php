<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_asset_unit(); return false;" class="btn btn-info pull-left display-block">
        <?php  echo htmlspecialchars(_l('new_asset_unit')); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php  echo htmlspecialchars(_l('id')); ?></th>
    <th><?php  echo htmlspecialchars(_l('asset_unit')); ?></th>
    <th><?php  echo htmlspecialchars(_l('options')); ?></th>
 </thead>
 <tbody>
    <?php foreach ($asset_unit as $c) { ?>
    <tr>
       <td><?php  echo htmlspecialchars($c['unit_id']); ?></td>
       <td> <a href="#" onclick="edit_asset_unit(this,<?php  echo htmlspecialchars($c['unit_id']); ?>); return false" data-name="<?php  echo htmlspecialchars($c['unit_name']); ?>"><?php  echo htmlspecialchars($c['unit_name']); ?></td>
       <td>
         <a href="#" onclick="edit_asset_unit(this,<?php  echo htmlspecialchars($c['unit_id']); ?>); return false" data-name="<?php  echo htmlspecialchars($c['unit_name']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
          <a href="<?php echo admin_url('assets/delete_asset_unit/'.$c['unit_id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
       </td>
    </tr>
    <?php } ?>
 </tbody>
</table>       
<div class="modal fade" id="asset_unit" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('assets/asset_unit')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php  echo htmlspecialchars(_l('edit_asset_unit')); ?></span>
                    <span class="add-title"><?php  echo htmlspecialchars(_l('new_asset_unit')); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional"></div>   
                     <div class="form">     
                        <?php
                        echo render_input('unit_name', 'unit_name'); ?>

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
        function new_asset_unit(){
            $('#asset_unit').modal('show');
            $('.edit-title').addClass('hide');
            $('.add-title').removeClass('hide');
            $('#additional').html('');
        }
        function edit_asset_unit(invoker,id){
            $('#additional').append(hidden_input('id',id));
            $('#asset_unit input[name="unit_name"]').val($(invoker).data('name'));
            $('#asset_unit').modal('show');
            $('.add-title').addClass('hide');
            $('.edit-title').removeClass('hide');
        }

    </script>
</body>
</html>
	