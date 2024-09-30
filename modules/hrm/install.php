<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'request')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'request` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `request_type_id` VARCHAR(45) NULL,
  `date_create` DATETIME NOT NULL,
  `approval_deadline` DATETIME NULL,
  `addedfrom` INT(11),
  `status` VARCHAR(45),
  `code` VARCHAR(255) NULL DEFAULT "",
  `description` MEDIUMTEXT NULL,
  PRIMARY KEY (`id`));');
}



if (!$CI->db->table_exists(db_prefix() . 'request_follow')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'request_follow` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `request_id` VARCHAR(45) NULL,
  `staffid` int(11) NOT NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'request_log')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'request_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `request_id` VARCHAR(45) NULL,
  `staffid` int(11) NOT NULL,
  `date` DATETIME NULL DEFAULT NULL,
  `note` TEXT NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'request_form')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'request_form` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `request_id` INT(11) NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `value` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`));');
}
if (!$CI->db->field_exists('slug', 'request_form')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'request_form` 
ADD COLUMN `slug` VARCHAR(45) NOT NULL AFTER `value`;');            
}



if (!$CI->db->table_exists(db_prefix() . 'request_related')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'request_related` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `request_id` INT(11) NOT NULL,
  `rel_type` VARCHAR(45) NOT NULL,
  `rel_id` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'request_files')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'request_files` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `request_id` INT(11) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `filetype` VARCHAR(255) NOT NULL,
  `dateadded` DATETIME NOT NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'request_approval_details')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'request_approval_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `request_id` INT(11) NOT NULL,
  `staffid` VARCHAR(255) NOT NULL,
  `approve` VARCHAR(45) NOT NULL,
  `note` TEXT NULL,
  `date` DATETIME NULL DEFAULT NULL,
  `approve_action` VARCHAR(255) NULL,
  `reject_action` VARCHAR(255) NULL,
  `approve_value` VARCHAR(255) NULL,
  `reject_value` VARCHAR(255) NULL,
  `staff_approve` INT(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`));


');
}
if (!$CI->db->field_exists('action', 'request_approval_details')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'request_approval_details` 
ADD COLUMN `action` VARCHAR(255) NULL DEFAULT NULL AFTER `staff_approve`;');            
}

if (!$CI->db->table_exists(db_prefix() . 'request_type')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'request_type` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `maximum_number_day` VARCHAR(45) NULL,
  `description` MEDIUMTEXT NULL,
  `data_chart` LONGTEXT NOT NULL,
  `active` VARCHAR(45) NOT NULL DEFAULT "1",
  PRIMARY KEY (`id`));');
}

if (!$CI->db->field_exists('content', 'request_type')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'request_type` 
ADD COLUMN `content` LONGTEXT NULL AFTER `description`;');            
}

if (!$CI->db->field_exists('related_to', 'request_type')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'request_type` 
ADD COLUMN `related_to` VARCHAR(255) NULL AFTER `active`;');            
}

if (!$CI->db->table_exists(db_prefix() . 'request_type_workflow')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'request_type_workflow` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `request_type_id` VARCHAR(45) NOT NULL,
  `staffid` VARCHAR(255) NOT NULL,
  `approve_action` VARCHAR(255) NULL,
  `reject_action` VARCHAR(255) NULL,
  `approve_value` VARCHAR(255) NULL,
  `reject_value` VARCHAR(255) NULL,
  PRIMARY KEY (`id`));');
}


if (!$CI->db->table_exists(db_prefix() . 'request_type_form')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'request_type_form` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `request_type_id` INT(11) NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`));');
}
$row_exists = $CI->db->query('SELECT * FROM '.db_prefix() . 'emailtemplates where type = "approve" and slug = "send-request-approve" and language = "english";')->row();
if(!$row_exists){
  $CI->db->query("INSERT INTO `".db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `plaintext`, `active`, `order`) VALUES ('approve', 'send-request-approve', 'english', 'Send Approval Request', 'Require Approval', '<p>Hi {staff_firstname}<br /><br />You have received a request to approve the {object_type}.<br /><br />You can view the {object_type} on the following link <a href=\"{object_link}\">{object_name}</a><br /><br />{email_signature}</p>', '{companyname} | CRM', '0', '1', '0');");
}

$row_exists = $CI->db->query('SELECT * FROM '.db_prefix() . 'emailtemplates where type = "approve" and slug = "send-request-approve" and language = "vietnamese";')->row();
if(!$row_exists){
  $CI->db->query("INSERT INTO `".db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `plaintext`, `active`, `order`) VALUES ('approve', 'send-request-approve', 'vietnamese', 'Gửi yêu cầu phê duyệt', 'Yêu cầu phê duyệt', 'Xin ch&agrave;o {staff_firstname} {staff_lastname}<br /><br />Bạn đã nhận được yêu cầu phê duyệt {object_type} mới.<br /><br />Bạn c&oacute; thể xem hóa đơn tại đ&acirc;y&nbsp;<a href=\"{object_link}\">{object_name}</a><br /><br />{email_signature}', '{companyname} | CRM', '0', '1', '0');");
}


$row_exists = $CI->db->query('SELECT * FROM '.db_prefix() . 'emailtemplates where type = "approve" and slug = "send_rejected" and language = "english";')->row();
if(!$row_exists){
  $CI->db->query("INSERT INTO `".db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `plaintext`, `active`, `order`) VALUES ('approve', 'send_rejected', 'english', 'Send Rejected', 'The {object_type} has been rejected', '<p>Hi {staff_firstname}<br /><br />Your {object_type} has been rejected.<br /><br />You can view the {object_type} on the following link <a href=\"{object_link}\">{object_name}</a><br /><br />{email_signature}</p>', '{companyname} | CRM', '0', '1', '0');");
}


$row_exists = $CI->db->query('SELECT * FROM '.db_prefix() . 'emailtemplates where type = "approve" and slug = "send_rejected" and language = "vietnamese";')->row();
if(!$row_exists){
  $CI->db->query("INSERT INTO `".db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `plaintext`, `active`, `order`) VALUES ('approve', 'send_rejected', 'vietnamese', 'Gửi từ chối', '{object_type} đã bị từ chối', 'Xin ch&agrave;o {staff_firstname} {staff_lastname}<br /><br />{object_type} của bạn đã bị từ chối.<br /><br />Bạn c&oacute; thể xem {object_type} tại đ&acirc;y&nbsp;<a href=\"{object_link}\">{object_name}</a><br /><br />{email_signature}', '{companyname} | CRM', '0', '1', '0');");
}


$row_exists = $CI->db->query('SELECT * FROM '.db_prefix() . 'emailtemplates where type = "approve" and slug = "send_approve" and language = "english";')->row();
if(!$row_exists){
  $CI->db->query("INSERT INTO `".db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `plaintext`, `active`, `order`) VALUES ('approve', 'send_approve', 'english', 'Send Approve', 'The {object_type} has been approved', '<p>Hi {staff_firstname}<br /><br />Your {object_type} has been approved.<br /><br />You can view the {object_type} on the following link <a href=\"{object_link}\">{object_name}</a><br /><br />{email_signature}</p>', '{companyname} | CRM', '0', '1', '0');");
}


$row_exists = $CI->db->query('SELECT * FROM '.db_prefix() . 'emailtemplates where type = "approve" and slug = "send_approve" and language = "vietnamese";')->row();
if(!$row_exists){
  $CI->db->query("INSERT INTO `".db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `plaintext`, `active`, `order`) VALUES ('approve', 'send_approve', 'vietnamese', 'Gửi phê duyệt', '{object_type} đã được phê duyệt', 'Xin ch&agrave;o {staff_firstname} {staff_lastname}<br /><br />{object_type} của bạn đã được phê duyệt.<br /><br />Bạn c&oacute; thể xem {object_type} tại đ&acirc;y&nbsp;<a href=\"{object_link}\">{object_name}</a><br /><br />{email_signature}', '{companyname} | CRM', '0', '1', '0');");
}

if (!$CI->db->field_exists('team_manage', 'staff')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'staff` 
ADD COLUMN `team_manage` INT(11) NULL DEFAULT 0;');            
}

if (!$CI->db->field_exists('manager_id', 'departments')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'departments` 
ADD COLUMN `manager_id` INT(11) NULL DEFAULT 0,
ADD COLUMN `parent_id` INT(11) NULL DEFAULT 0 ;');            
}


if (!$CI->db->field_exists('staff_identifi' ,db_prefix() . 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff`
  ADD COLUMN `staff_identifi` VARCHAR(25) NULL AFTER `team_manage`');
}


if (!$CI->db->field_exists('birthday' ,db_prefix() . 'staff')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "staff`
ADD COLUMN `birthday` date NULL AFTER `email_signature`,
ADD COLUMN `birthplace` VARCHAR(200) NULL AFTER `birthday`,
ADD COLUMN `sex` varchar(15) NULL AFTER `birthplace`,
ADD COLUMN `marital_status` varchar(25) NULL AFTER `sex`,
ADD COLUMN `nation` varchar(25) NULL AFTER `marital_status`,
ADD COLUMN `religion` varchar(50) NULL AFTER `nation`,
ADD COLUMN `identification` varchar(100) NULL AFTER `religion`,
ADD COLUMN `days_for_identity` date NULL AFTER `identification`,
ADD COLUMN `home_town` varchar(200) NULL AFTER `days_for_identity`,
ADD COLUMN `resident` varchar(200) NULL AFTER `home_town`,
ADD COLUMN `current_address` varchar(200) NULL AFTER `resident`,
ADD COLUMN `literacy` varchar(50) NULL AFTER `current_address`,
ADD COLUMN `orther_infor` text NULL AFTER `literacy`

;");
}


if (!$CI->db->field_exists('place_of_issue' ,db_prefix() . 'staff')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "staff`

    ADD COLUMN `place_of_issue` varchar(50) NULL AFTER `orther_infor`,
    ADD COLUMN `account_number` varchar(50) NULL AFTER `place_of_issue`,
    ADD COLUMN `name_account` varchar(50) NULL AFTER `account_number`,
    ADD COLUMN `issue_bank` varchar(200) NULL AFTER `name_account`,
    ADD COLUMN `Personal_tax_code` varchar(50) NULL AFTER `issue_bank`

;");
}

if (!$CI->db->field_exists('records_received' ,db_prefix() . 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff`
  ADD COLUMN `records_received` LONGTEXT NULL AFTER `issue_bank`');
}

if (!$CI->db->field_exists('status_work' ,db_prefix() . 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff`
  ADD COLUMN `status_work` VARCHAR(100) NULL');
}

if (!$CI->db->field_exists('date_update' ,db_prefix() . 'staff')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "staff`
    ADD COLUMN `date_update` DATE NULL AFTER `status_work`
  ;");
}

if (!$CI->db->field_exists('job_position' ,db_prefix() . 'staff')) {
   $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff`
ADD COLUMN `job_position` int(11) NULL AFTER `orther_infor`,
ADD COLUMN `workplace` int(11) NULL AFTER `job_position`');
}

if (!$CI->db->table_exists(db_prefix() . 'job_position')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "job_position` (
      `position_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `position_name` varchar(200) NOT NULL,
      PRIMARY KEY (`position_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'workplace')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "workplace` (
      `workplace_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `workplace_name` varchar(200) NOT NULL,
      PRIMARY KEY (`workplace_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'staff_contracttype')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "staff_contracttype` (
      `id_contracttype` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name_contracttype` varchar(200) NOT NULL,
      `contracttype` varchar(200) NOT NULL,
      `duration` int(11) NULL,
      `unit` varchar(20) NULL,
      `insurance` boolean NOT NULL,
      PRIMARY KEY (`id_contracttype`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'allowance_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "allowance_type` (
      `type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `type_name` varchar(200) NOT NULL,
      `allowance_val` decimal(15,2) NOT NULL,
      `taxable` boolean NOT NULL,
      PRIMARY KEY (`type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hrm_option')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hrm_option` (
      `option_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `option_name` varchar(200) NOT NULL,
      `option_val` longtext NULL,
      `auto` tinyint(1) NULL,
      PRIMARY KEY (`option_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'staff_contract')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "staff_contract` (
      `id_contract` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `contract_code` varchar(15) NOT NULL,
      `name_contract` int(11) NOT NULL,
      `staff` int(11) NOT NULL,
      `contract_form` varchar(191) NULL,
      `start_valid` date NULL,
      `end_valid` date NULL,
      `contract_status` varchar(100) NULL,
      `salary_form` int(11) NULL,
      `allowance_type` varchar(11) NULL,
      `sign_day` date NULL,
      PRIMARY KEY (`id_contract`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('staff_delegate' ,db_prefix() . 'staff_contract')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff_contract`
  ADD COLUMN `staff_delegate` int(11) NULL AFTER `sign_day`');
}

if (!$CI->db->field_exists('staff_role' ,db_prefix() . 'staff_contract')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff_contract`
  ADD COLUMN `staff_role` int(11) NULL AFTER `staff_delegate`');
}


if (!$CI->db->table_exists(db_prefix() . 'salary_form')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "salary_form` (
      `form_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `form_name` varchar(200) NOT NULL,
      `salary_val` decimal(15,2) NOT NULL,
      `tax` boolean NOT NULL,
      PRIMARY KEY (`form_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'manage_leave')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "manage_leave` (
      `leave_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `id_staff` int(11) NOT NULL,
      `leave_date` int(11) NULL,
      `leave_year` int(11) NULL,
      `accumulated_leave` int(11) NULL,
      `seniority_leave` int(11) NULL,
      `borrow_leave` int(11) NULL,
      `actual_leave` int(11) NULL,
      `expected_leave` int(11) NULL,
      PRIMARY KEY (`leave_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'staff_contract_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "staff_contract_detail` (
      `contract_detail_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `staff_contract_id` int(11) UNSIGNED NOT NULL,
      `since_date` date NULL,
      `contract_note` varchar(100) NULL,
      `contract_salary_expense` longtext NULL,
      `contract_allowance_expense` longtext NULL,
      PRIMARY KEY (`contract_detail_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'staff_insurance')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "staff_insurance` (
      `insurance_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `staff_id` int(11) UNSIGNED NOT NULL,
      `insurance_book_num` varchar(100) NULL,
      `health_insurance_num` varchar(100) NULL,
      `city_code` varchar(100) NULL,
      `registration_medical` varchar(100) NULL,
      PRIMARY KEY (`insurance_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'staff_insurance_history')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "staff_insurance_history` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `insurance_id` int(11) UNSIGNED NOT NULL,
      `staff_id` int(11) UNSIGNED  NULL,
      `from_month` date NULL,
      `formality` varchar(50) NULL,
      `reason` varchar(50) NULL,
      `premium_rates` varchar(100) NULL,
      `payment_company` varchar(100) NULL,
      `payment_worker` varchar(100) NULL,
      PRIMARY KEY (`id`,`insurance_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'payroll_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "payroll_type` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `payroll_type_name` varchar(100) NOT NULL,
      `department_id`  longtext  NULL,
      `role_id`  longtext  NULL,
      `position_id`  longtext  NULL,
      `salary_form_id` int(11) UNSIGNED  NULL COMMENT '1:Chính 2:Phụ cấp',
      `manager_id` int(11) UNSIGNED  NULL,
      `follower_id` int(11) UNSIGNED  NULL,
      `template` longtext  NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'payroll_table')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "payroll_table` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `payroll_month` date NOT NULL,
      `payroll_type` int(11) UNSIGNED  NULL,
      `template_data` longtext  NULL,
      `status` int(11) UNSIGNED  NULL DEFAULT 0 COMMENT '1:đã chốt 0:chưa chốt',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (row_options_exist('"hrm_contract_form"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `option_val`, `auto`) VALUES ("hrm_contract_form", "[]", "1");
');
}

if (row_options_exist('"hrm_leave_position"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `option_val`, `auto`) VALUES ("hrm_leave_position", "[]", "1");
');
}

if (row_options_exist('"hrm_leave_contract_type"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `option_val`, `auto`) VALUES ("hrm_leave_contract_type", "[]", "1");
');
}

if (row_options_exist('"hrm_leave_start_date"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_leave_start_date", "1");
');
}

if (row_options_exist('"hrm_max_leave_in_year"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_max_leave_in_year", "1");
');
}

if (row_options_exist('"hrm_start_leave_from_month"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_start_leave_from_month", "1");
');
}

if (row_options_exist('"hrm_start_leave_to_month"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_start_leave_to_month", "1");
');
}

if (row_options_exist('"hrm_add_new_leave_month_from_date"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_add_new_leave_month_from_date", "1");
');
}

if (row_options_exist('"hrm_accumulated_leave_to_month"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_accumulated_leave_to_month", "1");
');
}

if (row_options_exist('"hrm_leave_contract_sign_day"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_leave_contract_sign_day", "1");
');
}

if (row_options_exist('"hrm_start_date_seniority"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_start_date_seniority", "1");
');
}

if (row_options_exist('"hrm_seniority_year"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_seniority_year", "1");
');
}

if (row_options_exist('"hrm_seniority_year_leave"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_seniority_year_leave", "1");
');
}

if (row_options_exist('"hrm_next_year"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_next_year", "1");
');
}

if (row_options_exist('"hrm_next_year_leave"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("hrm_next_year_leave", "1");
');
}

if (row_options_exist('"alow_borrow_leave"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("alow_borrow_leave", "1");
');
}

if (row_options_exist('"contract_type_borrow"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `option_val`, `auto`) VALUES ("contract_type_borrow", "[]", "1");
');
}

if (row_options_exist('"max_leave_borrow"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`, `auto`) VALUES ("max_leave_borrow", "1");
');
}

if (row_options_exist('"day_increases_monthly"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`,`option_val`, `auto`) VALUES ("day_increases_monthly", "15", "1");
');
}

if (row_options_exist('"sign_a_labor_contract"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`,`option_val`, `auto`) VALUES ("sign_a_labor_contract", "1", "1");
');
}

if (row_options_exist('"maternity_leave_to_return_to_work"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`,`option_val`, `auto`) VALUES ("maternity_leave_to_return_to_work", "1", "1");
');
}

if (row_options_exist('"unpaid_leave_to_return_to_work"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`,`option_val`, `auto`) VALUES ("unpaid_leave_to_return_to_work", "1", "1");
');
}

if (row_options_exist('"increase_the_premium"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`,`option_val`, `auto`) VALUES ("increase_the_premium", "1", "1");
');
}

if (row_options_exist('"day_decreases_monthly"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`,`option_val`, `auto`) VALUES ("day_decreases_monthly", "5", "1");
');
}

if (row_options_exist('"contract_paid_for_unemployment"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`,`option_val`, `auto`) VALUES ("contract_paid_for_unemployment", "1", "1");
');
}

if (row_options_exist('"maternity_leave_regime"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`,`option_val`, `auto`) VALUES ("maternity_leave_regime", "1", "1");
');
}

if (row_options_exist('"unpaid_leave_of_more_than"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`,`option_val`, `auto`) VALUES ("unpaid_leave_of_more_than", "10", "1");
');
}

if (row_options_exist('"reduced_premiums"') == 0){
  $CI->db->query('INSERT INTO `tblhrm_option` (`option_name`,`option_val`, `auto`) VALUES ("reduced_premiums", "1", "1");
');
}

if (!$CI->db->table_exists(db_prefix() . 'insurance_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "insurance_type` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `from_month` date NOT NULL,
      `social_company` VARCHAR(15) NULL,
      `social_staff` VARCHAR(15) NULL,
      `labor_accident_company` VARCHAR(15) NULL,
      `labor_accident_staff` VARCHAR(15) NULL,
      `health_company` VARCHAR(15) NULL,
      `health_staff` VARCHAR(15) NULL,
      `unemployment_company` VARCHAR(15) NULL,
      `unemployment_staff` VARCHAR(15) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'province_city')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "province_city` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `province_code` varchar(45) NOT NULL,
      `province_name` VARCHAR(200) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'day_off')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "day_off` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `off_reason` varchar(255) NOT NULL,
      `off_type` varchar(100) NOT NULL,
      `break_date` date NOT NULL,
      `timekeeping` varchar(45) NULL,
      `department` int(11) NULL DEFAULT '0',
      `position` int(11) NULL DEFAULT '0',
      `add_from` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'work_shift')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "work_shift` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `shift_code` varchar(45) NOT NULL,
      `shift_name` varchar(200) NOT NULL,
      `shift_type` varchar(200) NOT NULL,
      `department` int(11) NULL DEFAULT '0',
      `position` int(11) NULL DEFAULT '0',
      `add_from` int(11) NOT NULL,
      `date_create` date NULL,
      `from_date` date NULL,
      `to_date` date NULL,
      `shifts_detail` TEXT NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hrm_timesheet')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hrm_timesheet` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `staff_id` int(11) NOT NULL,
      `date_work` date NOT NULL,
      `value` text NULL,
      `type` varchar(45) NULL,
      `add_from` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}