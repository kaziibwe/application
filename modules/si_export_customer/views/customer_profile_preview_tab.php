<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
<li role="presentation">
	<a href="#customer_preview" aria-controls="customer_preview" role="tab" data-toggle="tab">
	<?php echo _l( 'si_customer_preview' ); ?>
	</a>
</li>
<?php } ?>