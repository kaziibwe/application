<?php
defined('BASEPATH') or exit('No direct script access allowed');
if(!$CI->db->table_exists(db_prefix() . 'si_export_customer_kyc_files')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_export_customer_kyc_files` (
	`id` int(11) NOT NULL,
	`client_id` int(11) NOT NULL,
	`files_id` text NOT NULL,
	`profile_file_id` int(11) NOT NULL DEFAULT '0'
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
	$CI->db->query('ALTER TABLE `' . db_prefix() . 'si_export_customer_kyc_files`
	ADD PRIMARY KEY (`id`),
	ADD KEY `client_id` (`client_id`);');
	$CI->db->query('ALTER TABLE `' . db_prefix() . 'si_export_customer_kyc_files`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}
if(!$CI->db->table_exists(db_prefix() . 'si_export_customer_services')) {	
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_export_customer_services` (
	`client_id` int(11) NOT NULL DEFAULT '0',
	`item_id` int(11) NOT NULL DEFAULT '0'
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
	$CI->db->query('ALTER TABLE `' . db_prefix() . 'si_export_customer_services`
	ADD PRIMARY KEY (`client_id`,`item_id`);');
}
//add in settings
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_logo_size',135);
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_specimen_sign',2);
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_shipping_address',0);
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_billing_address',0);
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_files_separate_page',1);
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_heading_color','#00A1CB');
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_primary_contact',1);
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_groups',1);
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_custom_fields',1);
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_heading_text','Client Profile');
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_custom_fields_text','Other Information');
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_attachment_text','Documents Attached');
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_specimen_sign_text','Specimen Signature');
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_services_text','Services');
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_orientation','P');
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_services','1');
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_heading_text','Overview');
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_heading_color','#00A1CB');
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_orientation','P');
add_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_services','1');
