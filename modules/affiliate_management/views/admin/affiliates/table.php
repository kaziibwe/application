<?php

defined('BASEPATH') or exit('No direct script access allowed');

$sTable = db_prefix() . 'affiliate_m_affiliates';
$payoutsTable = db_prefix() .  'affiliate_m_payouts';
$referralsTable = db_prefix() .  'affiliate_m_referrals';
$affiliateGroups = AffiliateManagementHelper::get_affiliate_groups();

$sIndexColumn = 'affiliate_id';


$aColumns = [
    'affiliate_slug',
    'group_id',
    'email',
    'status',
    'total_earnings',
    'balance',
    'created_at',
];

$contactTable = db_prefix() . 'contacts';
$join = ['LEFT JOIN ' . $contactTable . ' ON ' . $contactTable . '.id = ' . $sTable . '.contact_id'];
$payouts = '(SELECT SUM(amount) FROM ' . $payoutsTable . ' WHERE affiliate_id = ' . $sTable . '.affiliate_id AND `status`=\'' . AffiliateManagementHelper::STATUS_APPROVED . '\') as total_payouts';
$referrals = '(SELECT COUNT(referral_id) FROM ' . $referralsTable . ' WHERE affiliate_id = ' . $sTable . '.affiliate_id) as total_referrals';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], ['affiliate_id', 'userid', 'CONCAT(`firstname`, \' \', `lastname`) as name', $payouts, $referrals]);

$output  = $result['output'];
$rResult = $result['rResult'];

$currency = get_base_currency();

foreach ($rResult as $aRow) {
    $row = [];

    $id = $aRow['affiliate_id'];
    $viewLink = admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . "/view_affiliate/$id");
    $clientViewLink = admin_url("clients/client/{$aRow['userid']}");

    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if (in_array($aColumns[$i], ['total_payouts', 'total_earnings', 'balance'])) {
            $_data = app_format_money($_data, $currency);
        }

        if ($aColumns[$i] === 'affiliate_slug') {
            $_data = "<a href='$viewLink'>$_data</a>";
        }

        if ($aColumns[$i] === 'email') {
            $row[] = "<a href='$clientViewLink'>{$aRow['name']}</a>";
        }

        if ($aColumns[$i] === 'total_earnings') {
            $row[] = $aRow['total_referrals'];
        }

        if ($aColumns[$i] == 'status') {
            $className = $_data == AffiliateManagementHelper::STATUS_ACTIVE ? 'success' : ($_data === AffiliateManagementHelper::STATUS_PENDING ? 'info' : 'danger');
            $_data = '<span class="badge tw-bg-' . $className . '-200">' . $_data . '</span>';
        }

        if ($aColumns[$i] == 'group_id') {
            $_data = isset($affiliateGroups[$_data]) ? $_data : AffiliateManagementHelper::DEFAULT_GROUP_ID;
            $editLink = admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/groups/edit/' . $_data);
            $_data = $affiliateGroups[$_data]['name'];
            $_data = "<a href='$editLink' target='_blank'>$_data</a>";
        }

        $row[] = $_data;

        if ($aColumns[$i] === 'total_earnings') {
            $row[] = app_format_money($aRow['total_payouts'], $currency);
        }
    }

    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';

    $options .= '<a data-toggle="tooltip" data-original-title="' . _l('view') . '" href="' . $viewLink . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
    <i class="fa fa-eye fa-lg"></i></a>';

    if (has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'edit')) {
        $status = $aRow['status'];
        $statusText = '';
        $statusLink = '';
        $className = 'tw-text-success hover:tw-text-neutral-700 focus:tw-text-neutral-700';

        if ($status === AffiliateManagementHelper::STATUS_ACTIVE) {
            $statusText = _l('affiliate_management_deactivate');
            $statusLink = admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . "/update_affiliate_status/" . AffiliateManagementHelper::STATUS_INACTIVE . "/" . $id);
            $className = 'text-danger';
        } elseif ($status === AffiliateManagementHelper::STATUS_INACTIVE || $status === AffiliateManagementHelper::STATUS_PENDING) {
            $statusText = _l('affiliate_management_activate');
            $statusLink = admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . "/update_affiliate_status/" . AffiliateManagementHelper::STATUS_ACTIVE . "/" . $id);
        }

        if (!empty($statusLink))
            $options .= '<span>|</span><a href="' . $statusLink . '" class="' . $className . '">' . $statusText . '</a>';
    }

    $options .= '</div>';


    $row[] = $options;
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}