<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'departmentid',
    'name',
    'email',
    'calendar_id',
    ];
$sIndexColumn = 'departmentid';
$sTable       = db_prefix() . 'departments';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['email', 'hidefromclient', 'host', 'encryption', 'password', 'delete_after_import', 'imap_username', 'folder']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        $ps    = '';
        if (!empty($aRow['password'])) {
            $ps = $this->ci->encryption->decrypt($aRow['password']);
        }
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="#" onclick="edit_department(this,' . e($aRow['departmentid']) . '); return false" data-name="' . e($aRow['name']) . '" data-calendar-id="' . e($aRow['calendar_id']) . '" data-email="' . e($aRow['email']) . '" data-hide-from-client="' . e($aRow['hidefromclient']) . '" data-host="' . e($aRow['host']) . '" data-password="' . $ps . '" data-folder="' . e($aRow['folder']) . '" data-imap_username="' . e($aRow['imap_username']) . '" data-encryption="' . e($aRow['encryption']) . '" data-delete-after-import="' . e($aRow['delete_after_import']) . '">' . e($_data) . '</a>';
        }
        $row[] = $_data;
    }

    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';
    $options .= '<a href="' . admin_url('departments/department/' . $aRow['departmentid']) . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700" ' . _attributes_to_string([
        'onclick' => 'edit_department(this,' . e($aRow['departmentid']) . '); return false', 'data-name' => e($aRow['name']), 'data-calendar-id' => e($aRow['calendar_id']), 'data-email' => e($aRow['email']), 'data-hide-from-client' => e($aRow['hidefromclient']), 'data-host' => e($aRow['host']), 'data-password' => $ps, 'data-encryption' => e($aRow['encryption']), 'data-folder' => e($aRow['folder']), 'data-imap_username' => e($aRow['imap_username']), 'data-delete-after-import' => e($aRow['delete_after_import']),
        ]) . '>
        <i class="fa-regular fa-pen-to-square fa-lg"></i>
    </a>';

    $options .= '<a href="' . admin_url('departments/delete/' . $aRow['departmentid']) . '"
    class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
        <i class="fa-regular fa-trash-can fa-lg"></i>
    </a>';

    $options .= '</div>';

    $row[] = $options;

    $output['aaData'][] = $row;
}