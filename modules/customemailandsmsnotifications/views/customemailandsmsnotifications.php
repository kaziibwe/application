<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-6">
            <div class="panel_s">
               <div class="panel-body">
                  <h3><?php echo _l('sms_title'); ?></h3>
                  <br>
                  <div class="emailsmswrapper">
                  <form action="<?php print(admin_url('customemailandsmsnotifications/email_sms/sendEmailSms')) ?>" enctype='multipart/form-data' method="post">
                    <h5><?php echo _l('customer_or_leads'); ?></h5>
                    <select class="selectpicker"
		                  name="customer_or_leads"
		                  data-width="100%" id="customer_or_leads" onchange="show();">	      
	                    	<option value=""><?php echo _l('ceasn_none'); ?></option>
	                    	<option value="customers"><?php echo _l('ceasn_customers'); ?></option>
	                    	<option value="leads"><?php echo _l('ceasn_leads'); ?></option>
	                </select><br><br>
					<hr>
					<div class="customers" id="customers" style="display: none;">
						<div class="form-group select-placeholder">
							<label for="clientid" class="control-label"><h5><?php echo _l('select_customer'); ?></h5></label>
							<select id="clientid" name="select_customer[]" multiple="true" data-live-search="true" data-width="100%" class="ajax-search<?php if(isset($invoice) && empty($invoice->clientid)){echo ' customer-removed';} ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

							<?php $selected = (isset($invoice) ? $invoice->clientid : '');

							if($selected == ''){
								$selected = (isset($customer_id) ? $customer_id: '');
							}

							if($selected != ''){
								$rel_data = get_relation_data('customer',$selected);
								$rel_val = get_relation_values($rel_data,'customer');
								echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
							}?>
							
							</select>
						</div>
					</div>

	                <div id="leads" style="display: none;">
    	                <h5><?php echo _l('select_lead'); ?></h5>
                        <select class="selectpicker"
    		                  data-toggle="<?php echo $this->input->get('select_lead'); ?>"
    		                  name="select_lead[]"
    		                  multiple="true"
    		                  data-width="100%">	      
    	                     <?php foreach ($leads as $leads) { ?>
    							<option value="<?php print($leads->id) ?>"><?php print($leads->name) ?></option>
    		                 <?php } ?>
    	                </select>       	
	                </div>
	                <br>
			        <h5><?php echo _l('template_select_title'); ?></h5>
			        <select class="selectpicker"
		                  name="template"
		                  data-actions-box="true"
		                  data-width="100%" id="tempaltes">
	                     	<option value="">Nothing Selected</option>
	                     <?php foreach ($templates as $template) { ?>
							<option value="<?php print($template['id']) ?>"><?php print($template['template_name']) ?></option>
		                 <?php } ?>
	                  </select>
				    <br><br>

					  <div class="form-group">
					  	<h5><?php echo _l('subject'); ?> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="Supports {contact_firstname}, {contact_lastname} & {client_company}" data-original-title="" title=""></i></h5>
						<input type="text" class="form-control" name="subject">
					  </div>
						<br>
					  <h5><?php echo _l('write_your_notification'); ?></h5>
                      <script> function countChars(obj){ document.getElementById("charNum").innerHTML = '<i class="fa fa-calculator" aria-hidden="true"></i> '+obj.value.length; } </script>
	                  <textarea placeholder="<?php echo _l('sms_textarea_placeholder'); ?>" name="message" rows="10" onkeyup="countChars(this);" class="form-control" id="msg_content"></textarea>
	                <p id="charNum"><i class="fa fa-calculator" aria-hidden="true"></i> 0</p>

						<hr>
	                  <div>
	                  		<h5><?php echo _l('attachment_note'); ?></h5>
		                  <input name="file_mail" value="filemail" class="check_label radio" type="file">
	                  </div>
						
					  <div><br><br></div>
	                  <div class="check_div_mail" style="margin-top:0px;"><hr>
					  <h5><?php echo _l('notification_type'); ?></h5>
		                  <input name="mail_or_sms" value="mail" class="check_label radio" type="radio" checked style="display:inline-block"> <span class="mail-or-sms-choice"><?php echo _l('send_as_email'); ?></span>
	                  </div>
					  <div class="check_div_sms">
		                  <input name="mail_or_sms" value="sms" class="check_label radio" type="radio" style="display:inline-block"> <span class="mail-or-sms-choice"><?php echo _l('send_as_sms'); ?></span>
					  </div>

					  <div class="check_div_mail" style="margin-top:0px;"><hr>
	                  	<label for="custom_date"><?php echo _l('custom_date'); ?></label>
						<input type="date" class="form-control" name="custom_date" id="date">
						<br>
						<div id="custom_time_div">
    						<label for="custom_time"><?php echo _l('custom_time'); ?></label>
    						<input type="time" class="form-control timepicker" name="custom_time" id="custom_time">
						</div>
	                  </div>
	                  <br>
	                  <button class="btn-tr btn btn-info invoice-form-submit transaction-submit"><?php echo _l('send'); ?></button>
                  </form>
                 </div>
               </div>
				
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">

	function show(){
		var c_l = $('#customer_or_leads').val();
		
		if(c_l == 'customers'){
			$('#customers').show();
			$('#leads').hide();
		}else if(c_l == 'leads'){
			$('#leads').show();
			$('#customers').hide();
		}else{
			$('#leads').hide();
			$('#customers').hide();
		}
		
	}

	jQuery(document).ready(function($) {
		$('#tempaltes').change(function(e){
        	var template_info_url = "<?= base_url(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE.'/template/get_template_data'); ?>";
        	var template_id = $(this).val();
        	if (template_id === "") {
    			return false;
			}
			$.ajax({
				url: template_info_url,
				type: 'POST',
				dataType: 'json',
				data: {template_id:template_id},
				success:function(resJSON){
					$("#msg_content").html(resJSON[0].template_content);
				}
			});	
		});
		$('#custom_time_div').hide();
		 $('input[name="custom_date"]').change(function () {
            var customDate = $(this).val();
            if (customDate !== "") {
                $('#custom_time_div').show();
            } else {
                $('#custom_time_div').hide();
            }
        });
	});
</script>
</body>
</html>