<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); 
///print_r($apiData);die;
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
					<div id="response_msg" style="display:none;"><div class="alert alert-success">
					Status updated succesfully.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
					</div> 
					</div>
					<div class="form-group select-placeholder">
                            <label for="status" class="control-label"><strong><?php echo 'Api Status'; ?></strong></label>
                            <select name="status" class="selectpicker" id="status">
                                <option value="1" <?php if($apiData[0]['status'] == '1'){ echo "selected"; } ?>>Active</option>
                                <option value="0" <?php if($apiData[0]['status'] == '0'){ echo "selected"; } ?>>Deactive</option>
                            </select>
                    </div>
					
					<div class="form-group">
                            <label for="install_at" class="control-label"><strong><?php echo 'Api Key'; ?></strong></label>
                           <span><?php echo $apiData[0]['key'];?></span>
                        </div>
					
						
						
						<div class="form-group">
                            <label for="install_at" class="control-label"><strong><?php echo 'Module Activate'; ?></strong></label>
                           <span><?php echo $apiData[0]['activate_at'];?></span>
                        </div>
					
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>

$(document).ready(function () {
	
    $('#status').on('change',function(){
            var status = $(this).val(); 
			//alert(status);
			if(status){
			 $.get( "<?= site_url('api/updateApi?status=') ?>"+status, function( data ) {
			 $('#response_msg').show();
			 setTimeout(location.reload.bind(location), 6000);
			 });
			}
			 $('#response_msg').hide();
        });
	
});

</script>
</body>
</html>
