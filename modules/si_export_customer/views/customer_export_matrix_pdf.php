<?php
defined('BASEPATH') or exit('No direct script access allowed');

$dimensions    = $pdf->getPageDimensions();
$main_heading = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_heading_text');
$heading_color = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_heading_color');
$services_heading = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_services_text');
$show_services = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_services');

$color_class = array('default'=>'#323a45','info'=>'#03a9f4','warning'=>'#ff6f00','danger'=>'#fc2d42','success'=>'#84c529','muted'=>'#cccccc');

$customer_tabs = filter_client_visible_tabs(get_customer_profile_tabs());
$counter = 0;
//company logo
// Add logo
$info_left_column = '';
$info_right_column = pdf_logo_url();

// Write top right logo and left column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);
$pdf->Ln(-15);
$html = '<h1 style="color:'.$heading_color.';" align="center">' . $main_heading .'</h1>';
$html .= '<h4 align="center"><b>( '. _l('statement_from_to',array($from,$to)).' )</b></h4><hr />';
$pdf->writeHTML($html, true, false, false, false, '');
// company  Name heading
$html_left = '<h3>'._l('name').' : ' . $client->company . '</h3>';
$html_left .= $client->address . '<br/>';
if (!empty($client->city)) {
	$html_left .= $client->city;
}
if (!empty($client->zip)) {
	$html_left .= ' - ' . $client->zip;
}
if (!empty($client->state)) {
	$html_left .= '<br/>' . $client->state;
}
$country = get_country_name($client->country);
if (!empty($country)) {
	$html_left .= ', ' . $country.'<br/><br/>';
}
if (!empty($client->phonenumber)) {
	$html_left .= '<b>' . _l('client_phonenumber') . ' : </b>' . $client->phonenumber. '<br/>';
}
if (get_option('company_requires_vat_number_field') == 1 && !empty($client->vat)) {
	$html_left .= '<b>' . _l('client_vat_number') . ' : </b>' . $client->vat. '<br/>';
}
if (!empty($client->website)) {
	$html_left .= '<b>' . _l('client_website') . ' : </b>' . $client->website. '<br/>';
}
$html_right = '';
if(!empty($customer_groups) && $show_groups) {
$html_right .= '<br/>'. implode(",",$customer_groups).'<b> : '._l('customer_groups').'</b><br/><br/>';
}
if (array_key_exists("statement",$customer_tabs))
{
$html_right .=  '<b>'.ucwords(_l('account_summary')).' :</b><br/><br/>';
// Account summary
$html_right .=  '<table width="100%" cellspacing="0" cellpadding="0" style="border-left:1px solid #ccc;">
			<tbody>
				<tr>
					<td width="60%">'. _l('statement_beginning_balance').'</td>
					<td width="2%">:</td>
					<td width="38%">'. app_format_money($statement['beginning_balance'], $statement['currency']).'</td>
				</tr>
				<tr>
					<td>'.  _l('invoiced_amount').'</td>
					<td>:</td>
					<td>'.app_format_money($statement['invoiced_amount'], $statement['currency']).'</td>
				</tr>
				<tr>
					<td>'. _l('amount_paid').'</td>
					<td>:</td>
					<td>'. app_format_money($statement['amount_paid'], $statement['currency']).'</td>
				</tr>
				</tbody>
				<tfoot>
				<tr>
					<td><b>'. _l('balance_due').'</b></td>
					<td>:</td>
					<td>'. app_format_money($statement['balance_due'], $statement['currency']).'</td>
				</tr>
			</tfoot>
		</table>';
}
pdf_multi_row($html_left, $html_right, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

//add services
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
		$html = '<hr/><br/>';
		$html .= '<b>'. $services_heading.'</b><br/><br/>';
		$html .= implode(', ',$services);
		$html .= '<br/>';
		$pdf->writeHTML($html, true, false, false, false, '');
	}
}


//add other data
$html = '<style>'.file_get_contents(module_dir_url('si_export_customer','assets/css/si_export_customer_pdf_style.css')).'</style>';
$html .= '<hr/><br/>';
$html .= '<table width="100%" cellspacing="5" cellpadding="5"><tr>';
if(array_key_exists("projects",$customer_tabs))
{	$counter++;
	$html .= '<td width="33%" class="right_border"><b>'. _l('projects_summary').'</b><br/><br/>';
	$html .= '<table width="100%">';
	foreach($project_statuses as $status){
		$html .= '<tr><td width="60%" style="color:'. $status['color'].'">'.$status['name'].'</td><td width="10%">:</td><td width="30%">'. $status['total'].'</td></tr>';
	}
	$html .= '</table></td>';
}
if (array_key_exists("tasks",$customer_tabs))
{ 
	$counter++;
	$html .= '<td width="33%" class="right_border"><b>'. _l('tasks_summary').'</b><br/><br/>';
	$html .= '<table width="100%">';
	foreach($tasks as $summary){
		$html .= '<tr><td width="60%" style="color:'. $summary['color'].'">'.$summary['name'].'</td><td width="10%">:</td><td width="30%">'. $summary['total_tasks'].'</td></tr>';
	}
	$html .= '</table></td>';
}
if(array_key_exists("tickets",$customer_tabs))
{
	$counter++;
	$html .= '<td width="33%" class="right_border"><b>'. _l('tickets_summary').'</b><br/><br/>';
	$html .= '<table width="100%">';
	foreach($tickets as $summary){
		$html .= '<tr><td width="60%" style="color:'. $summary['color'].'">'.$summary['name'].'</td><td width="10%">:</td><td width="30%">'. $summary['total_tickets'].'</td></tr>';
	}
	$html .= '</table></td>';
	if($counter > 0 && $counter%3 == 0){
		$html .= '</tr></table>';
		$html .= '<hr/><br/>';
		$html .= '<table width="100%" cellspacing="5" cellpadding="5"><tr>';
	}
}
if (array_key_exists("estimates",$customer_tabs))
{ 
	$counter++;
	$html .= '<td width="33%" class="right_border"><b>'. _l('estimates').'</b><br/><br/>';
	$html .= '<table width="100%">';
	foreach($estimates as $key => $data){
		$class = estimate_status_color_class($data['status']);
		$class = (isset($color_class[$class])?$color_class[$class]:$color_class['default']);
		$name = estimate_status_by_id($data['status']);
		$html .= '<tr><td width="60%" style="color:'. $class.'">'.$name.'</td><td width="10%">:</td><td width="30%">'. app_format_money($data['total'], $data['currency_name']).'</td></tr>';
	}
	$html .= '</table></td>';
	if($counter > 0 && $counter%3 == 0){
		$html .= '</tr></table>';
		$html .= '<hr/><br/>';
		$html .= '<table width="100%" cellspacing="5" cellpadding="5"><tr>';
	}
}
if (array_key_exists("invoices",$customer_tabs))
{ 
	$counter++;
	$html .= '<td width="33%" class="right_border"><b>'. _l('invoices').'</b><br/><br/>';
	$html .= '<table width="100%">';
	$html .= '<tr><td width="60%" style="color:'. $color_class['warning'].'">'._l('outstanding_invoices').'</td><td width="10%">:</td><td width="30%">'. app_format_money($invoices['due'], $invoices['currency']).'</td></tr>';
	$html .= '<tr><td width="60%" style="color:'. $color_class['danger'].'">'._l('past_due_invoices').'</td><td width="10%">:</td><td width="30%">'. app_format_money($invoices['overdue'], $invoices['currency']).'</td></tr>';
	$html .= '<tr><td width="60%" style="color:'. $color_class['success'].'">'._l('paid_invoices').'</td><td width="10%">:</td><td width="30%">'. app_format_money($invoices['paid'], $invoices['currency']).'</td></tr>';
	$html .= '</table></td>';
	if($counter > 0 && $counter%3 == 0){
		$html .= '</tr></table>';
		$html .= '<hr/><br/><br/>';
		$html .= '<table width="100%" cellspacing="5" cellpadding="5"><tr>';
	}
}
if (array_key_exists("expenses",$customer_tabs))
{ 
	$counter++;
	$html .= '<td width="33%" class="right_border"><b>'. _l('expenses').'</b><br/><br/>';
	$html .= '<table width="100%">';
	$html .= '<tr><td width="60%" style="color:'. $color_class['warning'].'">'._l('expenses_total').'</td><td width="10%">:</td><td width="30%">'. $expenses['all']['total'].'</td></tr>';
	$html .= '<tr><td width="60%" style="color:'. $color_class['success'].'">'._l('expenses_list_billable').'</td><td width="10%">:</td><td width="30%">'.$expenses['billable']['total'].'</td></tr>';
	$html .= '<tr><td width="60%" style="color:'. $color_class['warning'].'">'._l('expenses_list_non_billable').'</td><td width="10%">:</td><td width="30%">'. $expenses['non_billable']['total'].'</td></tr>';
	$html .= '<tr><td width="60%" style="color:'. $color_class['danger'].'">'._l('expenses_list_unbilled').'</td><td width="10%">:</td><td width="30%">'. $expenses['unbilled']['total'].'</td></tr>';
	$html .= '<tr><td width="60%" style="color:'. $color_class['success'].'">'._l('expense_billed').'</td><td width="10%">:</td><td width="30%">'. $expenses['billed']['total'].'</td></tr>';
	$html .= '</table></td>';
	if($counter > 0 && $counter%3 == 0){
		$html .= '</tr></table>';
		$html .= '<hr/><br/><br/>';
		$html .= '<table width="100%" cellspacing="5" cellpadding="5"><tr>';
	}
}
if($counter%3==0)
	$html .= '<td></td>';//add blank td if new table is created at last
	
$html .= '</tr></table>';

$pdf->writeHTML($html, true, false, false, false, '');


if(ob_get_length() > 0 && ENVIRONMENT == 'production'){
	ob_end_clean();
}
