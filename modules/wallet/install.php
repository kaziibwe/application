<?php

defined('BASEPATH') or exit('No direct script access allowed');

add_option('wallet_min_funding_amount', '5');
add_option('wallet_max_funding_amount', '1000');
add_option('wallet_allowed_payment_modes', '[]');
add_option('wallet_taxname', '[]');
add_option('wallet_enable_overdue_invoice_auto_payment', '1');
add_option('wallet_enabled_reversal', '1');
add_option('wallet_allow_withdraw', '0');
add_option('wallet_withdrawal_methods', 'Paypal, Bank');
add_option('wallet_allow_funding', '1');
add_option('wallet_initial_credit_amount', '');

// Create tables
$db_prefix = db_prefix();
$contact_table = $db_prefix . 'contacts';

$table = $db_prefix . 'wallet';
if (!$CI->db->table_exists($table)) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . $table . "` (
            `id` int NOT NULL AUTO_INCREMENT,
            `contact_id` int NOT NULL,
            `balance` text,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_" . $db_prefix . "_contact_id` (`contact_id`),
            CONSTRAINT `fk_" . $db_prefix . "_wallet_contact_id` FOREIGN KEY (`contact_id`) REFERENCES `$contact_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

$table = $db_prefix . 'wallet_transaction';
if (!$CI->db->table_exists($table)) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . $table . "` (
            `id` int NOT NULL AUTO_INCREMENT,
            `contact_id` int NOT NULL,
            `amount` DECIMAL(15, 2) NOT NULL,
            `mode` enum('debit','credit','log') NOT NULL,
            `tag` varchar(255) NOT NULL,
            `description` TEXT,
            `ref_id` varchar(255) DEFAULT NULL,
            `invoice_id` varchar(255) DEFAULT NULL,
            `metadata` TEXT,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_" . $db_prefix . "_ref_id` (`ref_id`),
            CONSTRAINT `fk_" . $db_prefix . "_wallet_transactions_contact_id` FOREIGN KEY (`contact_id`) REFERENCES `$contact_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

// New withdraw request notifications
$withdraw_request_admin = [
    'type' => 'staff',
    'slug' => 'wallet_withdraw_request_for_admin',
    'name' => 'Wallet withdraw Request',
    'subject' => 'You have new wallet withdraw request',
    'message' => 'Dear Admin,<br/><br/>
    I wanted to inform you about a new withdraw request, identified by reference number <b>#{log_ref_id}</b>. 
    <br/><br/>
    Contact Name: {contact_firstname}<br/>
    Amount: {log_amount}<br/>
    Note: {log_metadata_withdraw_method}<br/>
    Created: {log_created_at}<br/>
    <br/><br/>    
    Best regards,<br/>
    {email_signature}'
];

// withdraw request update
$withdraw_request_updated = [
    'type' => 'client',
    'slug' => 'wallet_withdraw_request_updated',
    'name' => 'Wallet Withdraw Request Update',
    'subject' => 'An update regarding your withdraw request',
    'message' => 'Dear {contact_firstname},<br/><br/>
    We hope this message finds you well.
    <br/>
    We wanted to inform you about the latest status of your withdraw request, identified by reference number <b>#{log_ref_id}</b>. 
    <br/><br/>
    Status: <b>{log_metadata_withdraw_status}</b><br/>
    Admin Note: <b>{log_metadata_withdraw_admin_note}</b><br/>
    Amount: {log_amount}<br/>
    Method: {log_metadata_withdraw_method}<br/>
    Details: {log_metadata_withdraw_note}<br/>
    Created: {log_created_at}<br/>
    <br/><br/>
    If you have any questions or need further clarification, please do not hesitate to reach out. We are here to assist you in any way possible.<br/><br/>
    
    Best regards,<br/>
    {email_signature}'
];

$CI->load->model('emails_model');
$templates = [$withdraw_request_updated, $withdraw_request_admin];
$fromname = '{companyname} | CRM';
foreach ($templates as $t) {
    //this helper check buy slug and create if not exist by slug
    create_email_template($t['subject'], $t['message'], $t['type'], $t['name'], $t['slug']);
}
