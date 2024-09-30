<?php

defined('BASEPATH') or exit('No direct script access allowed');

$sTable = db_prefix() . 'affiliate_m_referrals';
$commissionTable = db_prefix() .  'affiliate_m_commissions';
$affiliatesTable = db_prefix() .  'affiliate_m_affiliates';
$contactTable = db_prefix() . 'contacts';
$clientTable = db_prefix() . 'clients';

$sIndexColumn = 'referral_id';
$is_single_affiliate = !empty($table_affiliate_id);
$commission = '(SELECT SUM(amount) FROM ' . $commissionTable . ' WHERE referral_id = ' . $sTable . '.referral_id) as total_commissions';

$affiliate_col = $sTable . '.affiliate_id as affiliate_id';

$aColumns = [
    'referral_id',
    $affiliate_col,
    get_sql_select_client_company(),
    $commission,
    'ip',
    'ua',
    "$sTable.created_at as created_at"
];

$join = [
    'LEFT JOIN ' . $clientTable . ' ON ' . $clientTable . '.userid = ' . $sTable . '.client_id',
    'LEFT JOIN ' . $affiliatesTable . ' ON ' . $affiliatesTable . '.affiliate_id = ' . $sTable . '.affiliate_id'
];

$affiliateJoin = ' LEFT JOIN ' . $contactTable . ' ON ' . $contactTable . '.id = ' . $affiliatesTable . '.contact_id';
$affiliate = 'CONCAT(`firstname`, \' \', `lastname`) as affiliate';
$join[] = $affiliateJoin;

$where = [];
if ($is_single_affiliate) {
    $table_affiliate_id = (int)$table_affiliate_id;
    $where = ["AND $sTable.affiliate_id ='$table_affiliate_id'"];
}

$extraCols = [$clientTable . '.userid', $affiliate];
if ($is_single_affiliate) {
    $extraCols[] = $affiliate_col;
    unset($aColumns[1]);
    $aColumns = array_values($aColumns);
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,  $extraCols);

$output  = $result['output'];
$rResult = $result['rResult'];

$CI = &get_instance();
$currency = get_base_currency();
$allowedReferralClientInfo = AffiliateManagementHelper::get_option('affiliate_management_save_referral_client_info') == '1';

$isClient = is_client_logged_in();
$canViewCustomer = has_permission('customers', '', 'view') && !$isClient;
$canViewInvoice = has_permission('invoices', '', 'view') && !$isClient;

$dateFormat = AffiliateManagementHelper::get_option('affiliate_management_commission_date_format');
$showCommissions = (int)AffiliateManagementHelper::get_option('affiliate_management_show_commission_info_on_referral_table');
$hasEditPermission = has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'edit') && !$isClient;
$referral_removal_enabled = AffiliateManagementHelper::get_option('affiliate_management_enable_referral_removal') == '1';

foreach ($rResult as $aRow) {

    $options = '';
    if ($hasEditPermission && $referral_removal_enabled) {
        $options = '<div class="row-options">';
        $options .= '<a class="text-danger _delete" href="' . admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/remove_referral/' . $aRow['referral_id']) . '">' . _l('affiliate_management_remove_referral') . '</a>';
        $options .= '</div>';
    }

    $row = [
        $aRow['referral_id'],
        '<a href="' . admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/view_affiliate/' . $aRow['affiliate_id']) . '">' . $aRow['affiliate'] . '</a>',
        $canViewCustomer ? '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>' . $options : $aRow['company']
    ];

    if ($showCommissions) {
        $commissions = '';
        $commissionData = $CI->affiliate_management_model->get_all_commissions(['referral_id' => $aRow['referral_id']]);
        foreach ($commissionData as $commission) {
            $ruleInfo = empty($commission->rule_info) ? '' : ' <span class="tw-truncate">(' . $commission->rule_info . ')</span>';
            $paymentInfo = '';
            if (!empty($commission->payment_id)) {
                $invoiceUrl = admin_url('payments/payment/' . $commission->payment_id);
                $invoiceNumber = !empty($commission->payment_id) ? format_invoice_number($commission->invoice_id) : '';
                if ($commission->status === AffiliateManagementHelper::STATUS_REVERSED)
                    $paymentInfo = "<span class='tw-ml-1 text-danger' data-toggle='tooltip' title='" . _l('affiliate_management_reversed_commission') . "'> - $invoiceNumber <i class='fa fa-circle-exclamation'></i></span>";
                else if (!empty($invoiceNumber))
                    $paymentInfo = (!$canViewInvoice ? '- ' . $invoiceNumber : '<a class="tw-truncate tw-ml-1" href="' . $invoiceUrl . '" target="_blank">- ' . $invoiceNumber . '</a>');
            }

            $date = $dateFormat == 'datetime' ? _dt($commission->created_at) : ($dateFormat == 'date' ? _d($commission->created_at) : time_ago($commission->created_at));
            $commissions .= '<div class="tw-flex"><span class="tw-truncate tw-mr-1">' . app_format_money($commission->amount, $currency) . '</span>' . $ruleInfo . $paymentInfo . '<span class="tw-truncate tw-ml-1"> - ' . $date . "</span></div>\n";
        }
        $row[] = "<div class='tw-flex tw-flex-col'>$commissions</div>";
    }

    if ($is_single_affiliate)
        unset($row[1]);

    if ($allowedReferralClientInfo && !is_client_logged_in()) {
        $row[] = $aRow['ip'];
        $row[] = $aRow['ua'];
    }
    $row[] = $aRow['created_at'];

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = array_values($row);
}
