<?php

defined('BASEPATH') or exit('No direct script access allowed');

$sTable = db_prefix() . 'affiliate_m_payouts';
$affiliatesTable = db_prefix() .  'affiliate_m_affiliates';

$sIndexColumn = 'payout_id';
$is_single_affiliate = !empty($table_affiliate_id);


$aColumns = [
    'payout_id',
    'note_for_admin',
    'balance',
    'amount',
    'payout_method',
    $sTable . '.status as status',
    "$sTable.created_at as created_at",
];

$contactTable = db_prefix() . 'contacts';

$join = [
    'LEFT JOIN ' . $affiliatesTable . ' ON ' . $affiliatesTable . '.affiliate_id = ' . $sTable . '.affiliate_id'
];

$affiliateJoin = ' LEFT JOIN ' . $contactTable . ' ON ' . $contactTable . '.id = ' . $affiliatesTable . '.contact_id';
$affiliate = 'CONCAT(`firstname`, \' \', `lastname`) as affiliate';
$join[] = $affiliateJoin;

$where = [];
if ($is_single_affiliate) {
    $table_affiliate_id = (int)$table_affiliate_id;
    $where = ["AND $sTable.affiliate_id = '$table_affiliate_id'"];
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,  [$sTable . '.affiliate_id', $affiliate, 'note_for_affiliate']);

$output  = $result['output'];
$rResult = $result['rResult'];

$CI = &get_instance();
$currency = get_base_currency();
$isClient = is_client_logged_in();
$hasEditPermission = has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'edit') && !$isClient;
foreach ($rResult as $aRow) {

    $pid = $aRow['payout_id'];
    $status = $aRow['status'];
    $affiliate = '<a href="' . admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/view_affiliate/' . $aRow['affiliate_id']) . '" target="_blank">' . $aRow['affiliate'] . '</a>';

    $note = empty($aRow['note_for_affiliate']) ? '' : "<span class='tw-ml-4' data-title='{$aRow['note_for_affiliate']}' data-toggle='tooltip'><i class='fa fa-bell text-danger'></i></span>";

    $options = '';

    if ($hasEditPermission) {
        $options = '<div class="row-options tw-ml-9 payout-option">';

        if ($status === AffiliateManagementHelper::STATUS_PENDING)
            $options .= '<a  data-title="' . _l('affiliate_management_processing_hint') . '" data-toggle="tooltip" class="text-warning tw-ml-2" href="' . admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/update_payouts/' . $pid . '/' . AffiliateManagementHelper::STATUS_PROCESSING) . '">' . _l('affiliate_management_processing') . '</a>';
        if ($status === AffiliateManagementHelper::STATUS_PROCESSING)
            $options .= '<a class="text-success" href="javascript:;" onclick="affiliatePayoutOptionClick($(this));" data-payoutinfo="' . app_format_money($aRow['amount'], $currency) . ' - ' . html_escape($affiliate) . '" data-href="' . admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/update_payouts/' . $pid . '/' . AffiliateManagementHelper::STATUS_APPROVED) . '">' . _l('affiliate_management_approve') . '</a>';
        $options .= '<a class="text-danger tw-ml-2" href="javascript:;" onclick="affiliatePayoutOptionClick($(this));" data-payoutinfo="' . app_format_money($aRow['amount'], $currency) . ' - ' . html_escape($affiliate) . '" data-href="' . admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/update_payouts/' . $pid . '/' . AffiliateManagementHelper::STATUS_REJECTED) . '">' . _l('affiliate_management_reject') . '</a>';
        $options .= '</div>';
    }

    if ($isClient && $status === AffiliateManagementHelper::STATUS_PENDING)
        $options = '<a class="text-danger tw-ml-2" href="' . base_url('clients/' . AFFILIATE_MANAGEMENT_MODULE_NAME . '/cancel_payout/' . $pid) . '">' . _l('affiliate_management_cancel') . '</a>';

    $options = in_array($status, [AffiliateManagementHelper::STATUS_PENDING, AffiliateManagementHelper::STATUS_PROCESSING]) ? $options : '';

    $statusClassname = $status === AffiliateManagementHelper::STATUS_APPROVED ? 'bg-success' : ($status === AffiliateManagementHelper::STATUS_PENDING ? 'bg-primary' : ($status === AffiliateManagementHelper::STATUS_PROCESSING ? 'bg-warning' : 'bg-danger'));
    $status = "<span class='badge $statusClassname'>" . _l('affiliate_management_' . $status) . "</span>";

    $row = [
        $aRow['payout_id'],
        $affiliate,
        app_format_money($aRow['balance'], $currency),
        app_format_money($aRow['amount'], $currency) . $options,
        $aRow['payout_method'],
        $aRow['note_for_admin'],
        $status . $note,
        $aRow['created_at']
    ];

    if ($is_single_affiliate) {
        unset($row[1]);
    }


    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = array_values($row);
}
