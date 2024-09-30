<?php
defined('BASEPATH') or exit('No direct script access allowed');

$dimensions    = $pdf->getPageDimensions();
$custom_fields = get_custom_fields('customers');

$main_heading = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_heading_text');
$custom_fields_heading = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_custom_fields_text');
$attachment_heading = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_attachment_text');
$specimen_sign_heading = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_specimen_sign_text');
$services_heading = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_services_text');

$profile_width = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_logo_size');
$specimen_signatures = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_specimen_sign');
$show_shipping_address = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_shipping_address');
$show_billing_address = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_billing_address');
$print_files_separate_page = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_files_separate_page');
$heading_color = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_heading_color');
$show_primary_contact = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_primary_contact');
$show_groups = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_groups');
$show_custom_fields = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_custom_fields');
$show_services = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_services');


//company logo
// Add logo
$info_left_column = '<img width="'.$profile_width.'px" src="'.$profile_logo.'" />';
$info_right_column = pdf_logo_url();

// Write top right logo and left column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);
// Like heading project name
if($profile_logo!='')
	$pdf->Ln(-15);
	
$html = '<h1 style="color:'.$heading_color.';" align="center">' . $main_heading .'</h1><hr />';
$pdf->writeHTML($html, true, false, false, false, '');

// company  Name heading
$html_left = '<h3>'._l('name').' : ' . $client->company . '</h3>';
$html_left .= $client->address . '<br/>';
if(!empty($client->city)) {
	$html_left .= $client->city;
}
if(!empty($client->zip)) {
	$html_left .= ' - ' . $client->zip;
}
if(!empty($client->state)) {
	$html_left .= '<br/>' . $client->state;
}
$country = get_country_name($client->country);
if(!empty($country)) {
	$html_left .= ', ' . $country.'<br/><br/>';
}
if(!empty($client->phonenumber)) {
	$html_left .= '<b>' . _l('client_phonenumber') . ' : </b>' . $client->phonenumber. '<br/>';
}
if(get_option('company_requires_vat_number_field') == 1 && !empty($client->vat)) {
	$html_left .= '<b>' . _l('client_vat_number') . ' : </b>' . $client->vat. '<br/>';
}
if(!empty($client->website)) {
	$html_left .= '<b>' . _l('client_website') . ' : </b>' . $client->website. '<br/>';
}
$html_right = '';
if(!empty($customer_groups) && $show_groups) {
$html_right .= '<br/>'. implode(",",$customer_groups).'<b> : '._l('customer_groups').'</b><br/><br/>';
}
if(!empty($contact) && $show_primary_contact)
{
$html_right .=  '<b>'.ucwords(_l('si_customer_primary_contact')).' :</b><br/>';
// primary contact name
$html_right .=  $contact->firstname.' '.$contact->lastname . '<br/>';
// primary contact phone
if(!empty($contact->phonenumber))
$html_right .=  $contact->phonenumber . '<br/>';
// primary contact email
if(!empty($contact->email))
$html_right .=  $contact->email . '<br/>';
}
pdf_multi_row($html_left, $html_right, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

//billing and shipping address
if($show_shipping_address || $show_billing_address)
{
	$html = '';
	if($show_billing_address)
	{
		$html .= '<b>'.ucwords(_l('billing_address')).' : </b>';
		$html .= $client->billing_street . ', ';
		if (!empty($client->billing_city)) {
			$html .= $client->billing_city;
		}
		if (!empty($client->billing_zip)) {
			$html .= ' - ' . $client->billing_zip;
		}
		if (!empty($client->billing_state)) {
			$html .= ', ' . $client->billing_state;
		}
		$country = get_country_name($client->billing_country);
		if (!empty($country)) {
			$html .= ', ' . $country.'<br/>';
		}
	}
	if($show_shipping_address)
	{
		$html .= '<b>'.ucwords(_l('shipping_address')).' : </b>';
		$html .= $client->shipping_street . ', ';
		if (!empty($client->shipping_city)) {
			$html .= $client->shipping_city;
		}
		if (!empty($client->shipping_zip)) {
			$html .= ' - ' . $client->shipping_zip;
		}
		if (!empty($client->shipping_state)) {
			$html .= ', ' . $client->shipping_state;
		}
		$country = get_country_name($client->shipping_country);
		if (!empty($country)) {
			$html .= ', ' . $country.'<br/>';
		}
	}
	$pdf->writeHTML($html, true, false, false, false, '');
}


// Check for custom fields
if (count($custom_fields) > 0 && $show_custom_fields){
	$html = '<style>'.file_get_contents(module_dir_url('si_export_customer','assets/css/si_export_customer_pdf_style.css')).'</style>';
	$html .= '<h3><u><b style="color:'.$heading_color.';">' . $custom_fields_heading . '</b></u></h3><br /><br />';
	$html .='<table class="bottom_border profile_custom_field_table" width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0" style="font-size:' . ($font_size + 4) . 'px"><tbody><tr>';
	$width=0;
	$total_cols=12;
	foreach ($custom_fields as $field){
	
		if(is_admin() || !$field['only_admin'])
		{
			if($width>=$total_cols)//new line if cols width is 12
			{
				$html .="</tr><tr>";
				$width=0;
			}
			$width += $field['bs_column'];
		
				$value = get_custom_field_value($client->userid, $field['id'], 'customers');
				$value = $value === '' ? '-' : $value;
				$html .= '<td width="'.(100 / ($total_cols/$field['bs_column'])).'%"><b>' . ucfirst($field['name']) . ' </b><br/>' . $value . '</td>';
		}	
	}
	$html .='</tr></tbody></table>';
	$pdf->writeHTML($html, true, false, false, false, '');
}

if($show_services)
{
	$selected_items = get_client_services_details($client->userid);
	if(!empty($selected_items))
	{
		$services = array();
		foreach($selected_items as $row)
		{
			$services[] = $row['description'];
		}
		//Print Services
		$html = '';
		$html .= '<h3><u><b style="color:'.$heading_color.';">' . $services_heading . '</b></u></h3><br /><br />';
		$html .= '<table width="100%" cellspacing="5" cellpadding="5" class="box_border"><tr><td>'.implode(', ',$services).'</td></tr></table>';
		$pdf->writeHTML($html, true, false, false, false, '');
	}
}

if(!empty($files))
{
	//Print Attachments
	$html = '<style>'.file_get_contents(module_dir_url('si_export_customer','assets/css/si_export_customer_pdf_style.css')).'</style>';
	$html .= '<h3><u><b style="color:'.$heading_color.';">' . $attachment_heading . '</b></u></h3><br /><br />';
	$html .= '<table width="100%" cellspacing="5" cellpadding="5" class="box_border"><tr><td>'.implode(', ',$files).'</td></tr></table>';
	$pdf->writeHTML($html, true, false, false, false, '');
}
//specimen signatures
$html = '<style>td{border:1px solid #ccc;}</style>';
$html .='<h3><u><b style="color:'.$heading_color.';">' . $specimen_sign_heading . '</b></u></h3><br /><br />';

if($specimen_signatures > 0)
{
	$html .='<table width="100%" cellspacing="10" cellpadding="30"><tr>';
	for($i=1;$i<=$specimen_signatures;$i++)
		$html .='<td class="box_border">Sign '.$i.'</td>';
	$html .='</tr></tbody></table>';
	$pdf->writeHTML($html, true, false, false, false, '');
}

//Print Documents
if(!empty($files_with_path))
{
	$html = '';
	foreach($files_with_path as $path)
	{
		if($print_files_separate_page)
			$html .= '<br pagebreak="true">';
		$html .= '<br/><img src="'.$path.'"/>';
	}	
	$pdf->writeHTML($html, true, false, false, false, '');
	
}

if(ob_get_length() > 0 && ENVIRONMENT == 'production'){
	ob_end_clean();
}
