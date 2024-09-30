<?php

defined('BASEPATH') or exit('No direct script access allowed');

$sTable = db_prefix() . 'affiliate_m_commissions';
$referralTable = db_prefix() .  'affiliate_m_referrals';
$affiliatesTable = db_prefix() .  'affiliate_m_affiliates';

$sIndexColumn = 'commission_id';
$is_single_affiliate = !empty($table_affiliate_id);


$aColumns = [
    $sTable . '.referral_id as referral_id',
    get_sql_select_client_company(),
    'amount',
    'commission_id',
    "$sTable.created_at as created_at",
];

$contactTable = db_prefix() . 'contacts';
$clientTable = db_prefix() . 'clients';

$join = [
    'LEFT JOIN ' . $clientTable . ' ON ' . $clientTable . '.userid = ' . $sTable . '.client_id',
    'LEFT JOIN ' . $affiliatesTable . ' ON ' . $affiliatesTable . '.affiliate_id = ' . $sTable . '.affiliate_id',
    'LEFT JOIN ' . $referralTable . ' ON ' . $referralTable . '.affiliate_id = ' . $sTable . '.affiliate_id'
];


$affiliateJoin = ' LEFT JOIN ' . $contactTable . ' ON ' . $contactTable . '.id = ' . $affiliatesTable . '.contact_id';
$affiliate = 'CONCAT(`firstname`, \' \', `lastname`) as affiliate';
$join[] = $affiliateJoin;

$where = ["AND commission_id IS NOT NULL"];
if ($is_single_affiliate) {
    $table_affiliate_id = (int)$table_affiliate_id;
    $where = ["AND $sTable.affiliate_id ='$table_affiliate_id'"];
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [$clientTable . '.userid', $sTable . '.affiliate_id as affiliate_id', 'invoice_id', $sTable . '.status as status', 'amount', 'rule_info', 'payment_id', $affiliate], ' GROUP BY commission_id');

$output  = $result['output'];
$rResult = $result['rResult'];

$CI = &get_instance();
$currency = get_base_currency();
$allowedReferralClientInfo = AffiliateManagementHelper::get_option('affiliate_management_save_referral_client_info') == '1';

$canViewCustomer = has_permission('customers', '', 'view') && !is_client_logged_in();
$canViewInvoice = has_permission('invoices', '', 'view') && !is_client_logged_in();

$dateFormat = AffiliateManagementHelper::get_option('affiliate_management_commission_date_format');

foreach ($rResult as $aRow) {
    $paymentId = $aRow['payment_id'];
    $rule = $aRow['rule_info'];
    $ruleInfo = empty($rule) ? AffiliateManagementHelper::COMMISION_TYPE_FIXED : ' <span class="tw-truncate">' . $rule . '</span>';
    $paymentInfo = '';
    if (!empty($paymentId)) {
        $invoiceUrl = admin_url('payments/payment/' . $paymentId);
        $invoiceNumber = !empty($paymentId) ? format_invoice_number($aRow['invoice_id']) : '';
        if ($aRow['status'] === AffiliateManagementHelper::STATUS_REVERSED)
            $paymentInfo = "<span class='tw-ml-1 text-danger' data-toggle='tooltip' title='" . _l('affiliate_management_reversed_commission') . "'> - $invoiceNumber <i class='fa fa-circle-exclamation'></i></span>";
        else if (!empty($invoiceNumber))
            $paymentInfo = (!$canViewInvoice ? '- ' . $invoiceNumber : '<a class="tw-truncate tw-ml-1" href="' . $invoiceUrl . '" target="_blank">- ' . $invoiceNumber . '</a>');
    }

    $commissions = '<div class="tw-flex">' . $ruleInfo . $paymentInfo . "</div>";


    $row = [
        '<a href="' . admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/view_affiliate/' . $aRow['affiliate_id']) . '">' . $aRow['affiliate'] . '</a>',
        $canViewCustomer ? '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>' : $aRow['company'],
        app_format_money($aRow['amount'], $currency),
        "<div class='tw-flex tw-flex-col'>$commissions</div>"
    ];

    if ($is_single_affiliate)
        unset($row[0]);

    $row[] = $aRow['created_at'];

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = array_values($row);
}