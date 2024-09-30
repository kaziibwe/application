<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<ul class="nav nav-tabs" role="tablist">
	<li role="presentation"  class="active">
		<a href="#si_cashfree_settings_tab1" aria-controls="si_cashfree_settings_tab1" role="tab" data-toggle="tab"><?php echo _l('cashfree_settings'); ?></a>
	</li>
</ul>
<div class="tab-content mtop30">
	<div role="tabpanel" class="tab-pane  active" id="si_cashfree_settings_tab1">
		<?php if(!get_option(SI_CASHFREE_MODULE_NAME.'_activated') || get_option(SI_CASHFREE_MODULE_NAME.'_activation_code')==''){?>
		<div class="row" id="si_cashfree_validate_wrapper" data-wait-text="<?php echo '<i class=\'fa fa-spinner fa-pulse\'></i> '._l('wait_text'); ?>" data-original-text="<?php echo _l('cashfree_settings_validate'); ?>">
			<div class="col-md-9">
				<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('cashfree_settings_purchase_code_help'); ?>"></i>
				<?php echo render_input('settings['.SI_CASHFREE_MODULE_NAME.'_activation_code]','cashfree_settings_activation_code',get_option(SI_CASHFREE_MODULE_NAME.'_activation_code'),'text',array('data-toggle'=>'tooltip','data-title'=>_l('cashfree_settings_purchase_code_help'),'maxlength'=>60)); 
					echo form_hidden('settings['.SI_CASHFREE_MODULE_NAME.'_activated]',get_option(SI_CASHFREE_MODULE_NAME.'_activated'));
				?>
				<span><a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">
				<?php echo _l('setup_help'); ?></a>
				</span>
			</div>
			<div class="col-md-3 mtop25">
				<button id="si_cashfree_validate" class="btn btn-success"><?php echo _l('cashfree_settings_validate');?></button>
			</div>
			<div class="col-md-12" id="si_cashfree_validate_messages" class="mtop25 text-left"></div>
		</div>
		<?php } else {?>
		<div class="row">
			<div class="col-md-12">
					<p>You have activated the module successfully. <?php echo _l('view')?> --> <a href="<?php echo admin_url("settings?group=payment_gateways")?>"><?php echo _l('settings_group_online_payment_modes')?></a></p>
			</div>
		</div>
		<?php } ?>
		<hr/>
	</div>
</div>