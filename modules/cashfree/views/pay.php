<?php
defined('BASEPATH') or exit('No direct script access allowed');
echo payment_gateway_head(_l('payment_for_invoice') . ' ' . format_invoice_number($invoice->id)); ?>
<body class="gateway-cashfree">
	<div class="container">
		<div class="col-md-8 col-md-offset-2 mtop30">
			<div class="mbot30 text-center">
				<?php echo payment_gateway_logo(); ?>
			</div>
			<div class="row">
				<div class="panel_s">
					<div class="panel-body">
						<h3 class="no-margin bold text-center">
						<?php echo _l('payment_for_invoice'); ?>
						<a href="<?php echo site_url('invoice/'.$invoice->id.'/'.$invoice->hash); ?>">
						<?php echo format_invoice_number($invoice->id); ?>
						</a>
						</h3>
						<h4 class="text-center">
						<?php echo _l('payment_total', app_format_money($amount, $invoice->currency_name)); ?>
						</h4>
          				<hr />
          				<h3 class="text-center"><?php echo _l('wait_text')?></h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>		
<?php echo payment_gateway_scripts(); ?>
<?php echo payment_gateway_footer(); ?>
<?php if($mode == 'test'){?>
<script type="text/javascript" src="https://sdk.cashfree.com/js/ui/2.0.0/cashfree.sandbox.js"></script>
<?php } else {?>
<script type="text/javascript" src="https://sdk.cashfree.com/js/ui/2.0.0/cashfree.prod.js"></script>
<?php }?>
<script>
const paymentSessionId = '<?php echo ($payment_session_id);?>'; 
</script>
<script src="<?php echo module_dir_url('cashfree','assets/js/cashfree_init.js'); ?>"></script>
