<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI->load->helper(AFFILIATE_MANAGEMENT_MODULE_NAME . '/' . AFFILIATE_MANAGEMENT_MODULE_NAME);

add_option('affiliate_management_auto_approve_signup', '1');
add_option('affiliate_management_save_referral_client_info', '1');
add_option('affiliate_management_join_page_content', AffiliateManagementHelper::default_affiliate_page()['content']);
add_option('affiliate_management_affiliate_model', AffiliateManagementHelper::AFFILIATE_MODEL_FIRST_CLICK);
add_option('affiliate_management_groups', json_encode(AffiliateManagementHelper::get_affiliate_groups()));
add_option('affiliate_management_enable_referral_removal', '0');

$db_prefix = db_prefix();

//create tables
$table = $db_prefix . 'affiliate_m_affiliates';
$affiliate_table = $table;
if (!$CI->db->table_exists($table)) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . $table . "` (
            `affiliate_id` int NOT NULL AUTO_INCREMENT,
            `affiliate_slug` varchar(255) NOT NULL,
            `contact_id` int NOT NULL,
            `total_earnings` DECIMAL(10, 2) DEFAULT 0.00,
            `balance` DECIMAL(10, 2) DEFAULT 0.00,
            `status` varchar(255) DEFAULT 'pending',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`affiliate_id`),
            UNIQUE KEY `unique_" . $db_prefix . "_contact_id` (`contact_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

if (!$CI->db->field_exists('group_id', $table)) {
    $CI->db->query("ALTER TABLE `$table` ADD `group_id` VARCHAR(255) NULL AFTER `affiliate_slug`;");
}

$table = $db_prefix . 'affiliate_m_referrals';
$referral_table = $table;
if (!$CI->db->table_exists($table)) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . $table . "` (
            `referral_id` int NOT NULL AUTO_INCREMENT,
            `affiliate_id` int NOT NULL,
            `client_id` int NOT NULL,
            `ua` text,
            `ip` varchar(255) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`referral_id`),
            UNIQUE KEY `unique_" . $db_prefix . "_client_id` (`client_id`),
            CONSTRAINT `fk_" . $db_prefix . "_referral_affiliate_id` FOREIGN KEY (`affiliate_id`) REFERENCES `$affiliate_table` (`affiliate_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

$table = $db_prefix . 'affiliate_m_commissions';
if (!$CI->db->table_exists($table)) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . $table . "` (
            `commission_id` INT NOT NULL AUTO_INCREMENT,
            `referral_id` INT NOT NULL,
            `affiliate_id` INT NOT NULL,
            `client_id` INT NOT NULL,
            `payment_id` INT DEFAULT NULL,
            `invoice_id` INT DEFAULT NULL,
            `amount` DECIMAL(10, 2) NOT NULL,
            `rule_info` varchar(255) DEFAULT NULL,
            `status` varchar(100) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`commission_id`),
            CONSTRAINT `fk_" . $db_prefix . "_commission_referral_id` FOREIGN KEY (`referral_id`) REFERENCES `$referral_table` (`referral_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk_" . $db_prefix . "_commission_affiliate_id` FOREIGN KEY (`affiliate_id`) REFERENCES `$affiliate_table` (`affiliate_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}
if (!$CI->db->field_exists('status', $table)) {
    $CI->db->query("ALTER TABLE `$table` ADD `status` VARCHAR(100) NULL AFTER `rule_info`;");
}

$table = $db_prefix . 'affiliate_m_payouts';
if (!$CI->db->table_exists($table)) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . $table . "` (
            `payout_id` INT NOT NULL AUTO_INCREMENT,
            `affiliate_id` INT NOT NULL,
            `amount` DECIMAL(10, 2) NOT NULL,
            `note_for_affiliate` TEXT,
            `note_for_admin` TEXT,
            `payout_method` varchar(255) NOT NULL,
            `status` varchar(255) DEFAULT 'pending',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`payout_id`),
            CONSTRAINT `fk_" . $db_prefix . "_afm_payout_affiliate_id` FOREIGN KEY (`affiliate_id`) REFERENCES `$affiliate_table` (`affiliate_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

$table = $db_prefix . 'affiliate_m_tracking';
if (!$CI->db->table_exists($table)) {
    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `" . $table . "` (
            `tracking_id` INT NOT NULL AUTO_INCREMENT,
            `affiliate_slug` varchar(255) NOT NULL,
            `rel_type` varchar(255) NOT NULL,
            `rel_id` INT NOT NULL,
            `email` varchar(255) DEFAULT NULL,
            `phonenumber` varchar(255) DEFAULT NULL,
            `metadata` TEXT,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`tracking_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";"
    );
}

/*** EMAIL TEMPLATES */

// New affiliate signup
$new_affiliate_signup_admin = [
    'type' => 'staff',
    'slug' => AffiliateManagementHelper::EMAIL_TEMPLATE_NEW_AFFILIATE_SIGNUP_FOR_ADMIN,
    'name' => 'New Affiliate Signup',
    'subject' => 'A new affiliate signup',
    'message' => 'Dear Admin,<br/><br/>
    I wanted to inform you about a new affiliate signup, identified by reference number <b>#{affiliate_slug}</b>. 
    <br/><br/>
    Affiliate: {affiliate_name}<br/>
    Status: {affiliate_status}<br/>
    Created: {affiliate_created_at}<br/>
    <br/><br/>    
    Best regards,<br/>
    {email_signature}'
];

$signup_through_affiliate_template = [
    'type' => 'client',
    'slug' => AffiliateManagementHelper::EMAIL_TEMPLATE_SIGNUP_THROUGH_AFFILIATE,
    'name' => 'Successful Signup through Affiliate Link',
    'subject' => 'Congratulations on a successful signup!',
    'message' => 'Dear {contact_firstname},<br/><br/>
    We are delighted to inform you that someone has successfully signed up using your affiliate link.
    <br/><br/>
    Company/Contact: {referral_name}<br/>
    Date: {referral_created_at}<br/>
    <br/><br/>
    While this signup may not have resulted in an immediate transaction, your efforts are highly valued. We appreciate your contribution to our community and anticipate further success in the future.
    <br/><br/>
    If you have any questions or need additional information, please feel free to reach out.
    <br/><br/>
    Best regards,<br/>
    {email_signature}'
];

$successful_referral_commission_template = [
    'type' => 'client',
    'slug' => AffiliateManagementHelper::EMAIL_TEMPLATE_SUCCESSFUL_REFERRAL_COMMISSION,
    'name' => 'Successful Referral Commission Notification',
    'subject' => 'A referral commission received!',
    'message' => 'Dear {contact_firstname},<br/><br/>
    We are thrilled to inform you that your referral has resulted in a successful transaction, earning you a commission.
    <br/><br/>
    Company/Contact: {referral_name}<br/>
    Transaction Amount: {payment_amount}<br/>
    Commission Earned: {commission_amount}<br/>
    New Balance: {affiliate_balance}<br/>
    Date: {commission_created_at}<br/>
    <br/><br/>
    We appreciate your efforts and look forward to more successful collaborations. Should you have any questions or require further information, feel free to reach out.
    <br/><br/>
    Best regards,<br/>
    {email_signature}'
];

$referral_commission_reversal_template = [
    'type' => 'client',
    'slug' => AffiliateManagementHelper::EMAIL_TEMPLATE_REFERRAL_COMMISSION_REVERSAL,
    'name' => 'Referral Commission Reversal Notification',
    'subject' => 'A referral commission was reversed!',
    'message' => 'Dear {contact_firstname},<br/><br/>
    We regret to inform you that the commission previously awarded for your referral has been reversed due to reversal or removal of the rewarded payment.
    <br/><br/>
    Company/Contact: {referral_name}<br/>
    Comission ID: {commission_id}<br/>
    Amount Reversed: {commission_amount}<br/>
    New Balance: {affiliate_balance}<br/>
    Date of commission: {commission_created_at}<br/>
    <br/><br/>
    We appreciate your efforts and look forward to more successful collaborations. Should you have any questions or require further information, feel free to reach out.
    <br/><br/>
    Best regards,<br/>
    {email_signature}'
];

// Payout request update
$payout_updated_template = [
    'type' => 'client',
    'slug' => AffiliateManagementHelper::EMAIL_TEMPLATE_PAYOUT_UPDATED,
    'name' => 'Affiliate Payout Request Update',
    'subject' => 'An update regarding your payout request',
    'message' => 'Dear {contact_firstname},<br/><br/>
    We hope this message finds you well.
    <br/>
    We wanted to inform you about the latest status of your payout request, identified by reference number <b>#{payout_id}</b>. 
    <br/><br/>
    Amount: {payout_amount}<br/>
    Status: {payout_status}<br/>
    Note: {payout_note_for_affiliate}<br/>
    Created: {payout_created_at}<br/>
    <br/><br/>
    If you have any questions or need further clarification, please do not hesitate to reach out. We are here to assist you in any way possible.<br/><br/>
    
    Best regards,<br/>
    {email_signature}'
];

// New payout request notifications
$payout_request_admin = [
    'type' => 'staff',
    'slug' => AffiliateManagementHelper::EMAIL_TEMPLATE_NEW_PAYOUT_REQUEST_FOR_ADMIN,
    'name' => 'Affiliate Payout Request',
    'subject' => 'You have new affiliate payout request',
    'message' => 'Dear Admin,<br/><br/>
    I wanted to inform you about a new payout request, identified by reference number <b>#{payout_id}</b>. 
    <br/><br/>
    Affiliate: {affiliate_name}<br/>
    Amount: {payout_amount}<br/>
    Status: {payout_status}<br/>
    Note: {payout_note_for_admin}<br/>
    Created: {payout_created_at}<br/>
    <br/><br/>    
    Best regards,<br/>
    {email_signature}'
];

// Payout updated admin
$payout_updated_template_admin = [
    'type' => 'staff',
    'slug' => AffiliateManagementHelper::EMAIL_TEMPLATE_PAYOUT_UPDATED_FOR_ADMIN,
    'name' => 'Affiliate Payout Request Updated',
    'subject' => 'You marked payout: #{payout_id} as {payout_status}',
    'message' => 'Dear Admin,<br/><br/>
    I wanted to update you about the recent update to a payout request, identified by reference number <b>#{payout_id}</b>. 
    <br/><br/>
    Affiliate: {affiliate_name}<br/>
    Amount: {payout_amount}<br/>
    Status: {payout_status}<br/><br/>
    Admin Note: {payout_note_for_admin}<br/>
    Note: {payout_note_for_affiliate}<br/>
    Created: {payout_created_at}<br/>
    <br/><br/>    
    Best regards,<br/>
    {email_signature}'
];

$CI->load->model('emails_model');
$templates = [
    $payout_updated_template,
    $signup_through_affiliate_template,
    $successful_referral_commission_template,
    $payout_request_admin,
    $payout_updated_template_admin,
    $new_affiliate_signup_admin,
    $referral_commission_reversal_template
];
$fromname = '{companyname} | CRM';
foreach ($templates as $t) {
    //this helper check buy slug and create if not exist by slug
    create_email_template($t['subject'], $t['message'], $t['type'], $t['name'], $t['slug']);
}
