<div class="panel_s">
	<div class="panel-body">
	   <div class="col-md-12 project-overview-left">
       <div class="row">
        <div class="col-md-12"><h4><?php echo htmlspecialchars($assets->assets_name); ?></h4><hr/></div>
      </div>
       <?php if (has_permission('assets', '', 'edit') || is_admin()) { ?>
      <div class="row">
         
              <a href="#" onclick="allocation(); return false;" class="btn btn-info pull-left display-block mright5"><i class="fa fa-edit"></i><?php echo ' '.htmlspecialchars(_l('allocation')); ?></a>
              <a href="#" onclick="recalled(); return false;" class="btn btn-info pull-left display-block mright5"><i class="fa fa-sign-in"></i><?php echo ' '.htmlspecialchars(_l('recalled')); ?></a>
              <a href="#" onclick="additional(); return false;" class="btn btn-info pull-left display-block mright5"><i class="fa fa-plus"></i><?php echo ' '.htmlspecialchars(_l('additional')); ?></a>
              <a href="#" onclick="noti_lost(); return false;" class="btn btn-info pull-left display-block mright5"><i class="fa fa-close"></i><?php echo ' '.htmlspecialchars(_l('noti_lost')); ?></a>
              <a href="#" onclick="broken(); return false;" class="btn btn-info pull-left display-block mright5"><i class="fa fa-chain-broken"></i><?php echo ' '.htmlspecialchars(_l('noti_broken')); ?></a>
              <a href="#" onclick="liquidation(); return false;" class="btn btn-info pull-left display-block mright5"><i class="fa fa-sign-out"></i><?php echo ' '.htmlspecialchars(_l('liquidation')); ?></a>
              <a href="#" onclick="warranty(); return false;" class="btn btn-info pull-left display-block mright5"><i class="fa fa-gear"></i><?php echo ' '.htmlspecialchars(_l('warranty')); ?></a>  
              <a href="#" onclick="new_asset(); return false;" class="btn btn-info pull-left display-block mright5"><?php echo htmlspecialchars(_l('add_new')); ?></a>
          
            
      </div>
      <?php } ?>
	   	<div class="row">
	      	<div class="horizontal-scrollable-tabs preview-tabs-top">
              <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
              <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
              <div class="horizontal-tabs">
              	<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
              	  <li role="presentation" class="active">
                     <a href="#general_infor" aria-controls="general_infor" role="tab" data-toggle="tab" aria-controls="general_infor">
                     <?php echo htmlspecialchars(_l('general_infor')); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#inventory_history" aria-controls="inventory_history" role="tab" data-toggle="tab" aria-controls="inventory_history">
                     <?php echo htmlspecialchars(_l('inventory_history')); ?>
                     </a>
                  </li>

                  <li role="presentation">
                     <a href="#pending_withdrawing" aria-controls="pending_withdrawing" role="tab" data-toggle="tab" aria-controls="pending_withdrawing">
                     <?php echo htmlspecialchars(_l('pending_withdrawing_history')); ?>
                     </a>
                  </li>
              	</ul>
              </div>
	    	</div> 
	    	<div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="general_infor">
            	 <div class="panel panel-info">
                  <div class="panel-body">
                	<div class="row col-md-12">
                		<h4><?php echo htmlspecialchars(_l('asset_information')); ?></h4>
                		<hr/>
                	</div>
                  <div class="col-md-12">
                    <img alt='<?php echo module_dir_url('assets', 'uploads').'/'.$assets->asset_image; ?>' src='<?php echo module_dir_url('assets', 'uploads').'/'.$assets->asset_image; ?>' class='img-thumbnail img-responsive zoom pull-right' style="width: 150px; height: 150px;" onerror="this.src='<?php echo module_dir_url('assets', 'uploads'); ?>/image-not-available.png'">
                  </div>
                	<div class="col-md-6 noleftrightpadding">
                		<table class="table border table-striped nomargintop">
                        <tbody>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('asset_code')); ?></td>
                              <td><?php echo htmlspecialchars($assets->assets_code); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('asset_group')); ?></td>
                              <td><?php echo htmlspecialchars(get_asset_group($assets->asset_group)); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('supplier_address')); ?></td>
                              <td><?php echo htmlspecialchars($assets->supplier_address); ?></td>
                           </tr>
                           
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('date_buy')); ?></td>
                              <td><?php echo htmlspecialchars(_d($assets->date_buy)); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('series')); ?></td>
                              <td><?php echo htmlspecialchars($assets->series); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('asset_location')); ?></td>
                              <td><?php echo htmlspecialchars(get_asset_location($assets->asset_location)); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('allocation')); ?></td>
                              <td>
                                <?php
                                  $belongs_to = explode(',', $assets->belongs_to);
                                  $html       = '';
                                  foreach ($belongs_to as $value) {
                                      $html .= ' - <a href="'.admin_url('clients/client/'.$value).'">'.get_company_name($value).'</a><br>';
                                  }
                                  echo $html;
                                ?>
                              </td>
                           </tr>
	                        </tbody>
	                     </table>
                	</div>
                	<div class="col-md-6 noleftrightpadding">
                		<table class="table table-striped nomargintop">
                        <tbody>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('asset_name')); ?></td>
                              <td><?php echo htmlspecialchars($assets->assets_name); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('supplier_name')); ?></td>
                              <td><?php echo htmlspecialchars($assets->supplier_name); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('supplier_phone')); ?></td>
                              <td><?php echo htmlspecialchars($assets->supplier_phone); ?></td>
                           </tr>
                           
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('warranty_period')); ?></td>
                              <td><?php echo htmlspecialchars($assets->warranty_period.' '._l('month')); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('depreciation')); ?></td>
                              <td><?php echo htmlspecialchars($assets->depreciation.' '._l('month')); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('room_management')); ?></td>
                              <td><?php echo htmlspecialchars(get_asset_dpm($assets->department)); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('visible_to_client')); ?></td>
                              <td><?php echo htmlspecialchars(($assets->visible_to_client) ? 'Yes' : 'No'); ?></td>
                           </tr>
	                        </tbody>
	                     </table>
                	  </div>
                	  <div class="col-md-12" id="assets_pv_file">
                	  	<?php
                            $file_html = '';
                            if (count($asset_file) > 0) {
                                $file_html .= '<hr />
					                    <p class="bold text-muted">'._l('assets_files').'</p>';
                                foreach ($asset_file as $f) {
                                    $href_url = site_url(ASSETS_PATH.$f['rel_id'].'/'.$f['file_name']).'" download';
                                    if (!empty($f['external'])) {
                                        $href_url = $f['external_link'];
                                    }
                                    $file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="'.$f['id'].'">
					              <div class="col-md-8">
					                 <a name="preview-ase-btn" onclick="preview_asset_btn(this); return false;" rel_id = "'.$f['rel_id'].'" id = "'.$f['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left marginright5" data-toggle="tooltip" title data-original-title="'._l('preview_file').'"><i class="fa fa-eye"></i></a>
					                 <div class="pull-left"><i class="'.get_mime_class($f['filetype']).'"></i></div>
					                 <a href=" '.$href_url.'" target="_blank" download>'.$f['file_name'].'</a>
					                 <br />
					                 <small class="text-muted">'.$f['filetype'].'</small>
					              </div>
					              <div class="col-md-4 text-right">';
                                    if ($f['staffid'] == get_staff_user_id() || is_admin()) {
                                        $file_html .= '<a href="#" class="text-danger" onclick="delete_asset_attachment('.$f['id'].'); return false;"><i class="fa fa-times"></i></a>';
                                    }
                                    $file_html .= '</div></div>';
                                }
                                $file_html .= '<hr />';
                                echo $file_html;
                            }
                         ?>
                	  </div>
                  </div>
                 </div>    
                 <div class="panel panel-danger backgroundscroll">
                  <div class="panel-body">
                	<div class="row col-md-12">
                		<h4><?php echo htmlspecialchars(_l('asset_value')); ?></h4>
                		<hr/>
                	</div>
                	<div class="col-md-6 noleftrightpadding">
                		<table class="table border table-striped nomargintop">
                        <tbody>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('amount')); ?></td>
                              <td><?php echo htmlspecialchars($assets->amount); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('unit_price')); ?></td>
                              <td><?php echo htmlspecialchars(app_format_money($assets->unit_price, '')); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('amount_allocate')); ?></td>
                              <td><?php echo htmlspecialchars($assets->total_allocation); ?></td>
                           </tr>
                           
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('depreciation_value')); ?></td>
                              <td><?php
                               $m           = (strtotime(date('Y-m-d')) - strtotime($assets->date_buy)) / (60 * 60 * 24 * 31);
                               $d_per_month = ($assets->unit_price * $assets->amount) / $assets->depreciation;
                               echo htmlspecialchars(app_format_money($m * $d_per_month, ''));
                                ?></td>
                           </tr>
	                        </tbody>
	                     </table>
                	</div>
                	<div class="col-md-6 noleftrightpadding">
                		<table class="table table-striped nomargintop">
                        <tbody>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('unit')); ?></td>
                              <td><?php echo htmlspecialchars(get_asset_units($assets->unit)); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('original_price')); ?></td>
                              <td><?php echo htmlspecialchars(app_format_money($assets->unit_price * $assets->amount, '')); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('amount_rest')); ?></td>
                              <td><?php echo htmlspecialchars($assets->amount - $assets->total_allocation); ?></td>
                           </tr>
                           
                           <tr class="project-overview">
                              <td class="bold"><?php echo htmlspecialchars(_l('residual_value')); ?></td>
                              <td><?php echo htmlspecialchars(app_format_money($assets->unit_price * $assets->amount - $m * $d_per_month, '')); ?></td>
                           </tr>
	                        </tbody>
	                     </table>
                		</div>
                    </div>
                  </div>    
                </div>
                <div role="tabpanel" class="tab-pane" id="inventory_history">
                	<?php
                        $table_data = [
                            _l('time'),
                            _l('action'),
                            _l('inventory_begin'),
                            _l('inventory_end'),
                            _l('cost'),
                            ];
                        render_datatable($table_data, 'table_inventory_history');
                        ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="pending_withdrawing">
                	<?php
                        $table_data = [
                            _l('time'),
                            _l('asset_name'),
                            _l('action'),
                            _l('acction_code'),
                            _l('quantity_as_qty'),
                            _l('acction_from'),
                            _l('acction_to'),
                            ];
                        render_datatable($table_data, 'table_action');
                        ?>
                </div>
            </div>  
        </div>		
	</div>
</div>
<div id="asset_file_data"></div>
<?php include_once 'includes/allocation_modal.php'; ?>
<?php include_once 'includes/recalled_modal.php'; ?>
<?php include_once 'includes/additional_modal.php'; ?>
<?php include_once 'includes/noti_lost_modal.php'; ?>
<?php include_once 'includes/liquidation_modal.php'; ?>
<?php include_once 'includes/warranty_modal.php'; ?>
<?php include_once 'includes/broken_modal.php'; ?>
<script>
  init_datepicker();
  initDataTable('.table-table_inventory_history', admin_url+'assets/table_inventory_history/<?php echo htmlspecialchars($assets->id); ?>');
  initDataTable('.table-table_action', admin_url+'assets/table_action/<?php echo htmlspecialchars($assets->id); ?>');
	function delete_asset_attachment(id) {
    if (confirm_delete()) {
        requestGet('assets/delete_asset_attachment/' + id).done(function(success) {
            if (success == 1) {
                $("#assets_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
  }
  function allocation(){
    $('#allocation_modal').modal('show');
  }
  function recalled(){
    $('#recalled_modal').modal('show');
  }
  function additional(){
    $('#additional_modal').modal('show');
  }
  function noti_lost(){
    $('#noti_lost_modal').modal('show');
  }
  function liquidation(){
    $('#liquidation_modal').modal('show');
  }
  function warranty(){
    $('#warranty_modal').modal('show');
  }
  function broken(){
    $('#broken_modal').modal('show');
  }
$("input[data-type='currency']").on({
    keyup: function() {        
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
});
function formatNumber(n) {
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}
function formatCurrency(input, blur) {
  var input_val = input.val();
  if (input_val === "") { return; }
  var original_len = input_val.length;
  var caret_pos = input.prop("selectionStart");
  if (input_val.indexOf(".") >= 0) {
    var decimal_pos = input_val.indexOf(".");
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);
    left_side = formatNumber(left_side);
    right_side = formatNumber(right_side);
    right_side = right_side.substring(0, 2);
    input_val = left_side + "." + right_side;

  } else {
    input_val = formatNumber(input_val);
    input_val = input_val;
  }
  input.val(input_val);
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  input[0].setSelectionRange(caret_pos, caret_pos);
}
</script>