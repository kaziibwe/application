<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'subject',
    'last_activity',
    '(SELECT COUNT(*) FROM ' . db_prefix() . 'projectdiscussioncomments WHERE discussion_id = ' . db_prefix() . 'projectdiscussions.id AND discussion_type="regular") as totalComments',
    'show_to_customer',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'projectdiscussions';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], ['AND project_id=' . $this->ci->db->escape_str($project_id)], [
    'id',
    'description',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $subject = '<a href="' . admin_url('projects/view/' . $project_id . '?group=project_discussions&discussion_id=' . $aRow['id']) . '">' . e($aRow['subject']) . '</a>';
    if (staff_can('edit',  'projects') || staff_can('delete',  'projects')) {
        $subject .= '<div class="row-options">';
        if (staff_can('edit',  'projects')) {
            $subject .= '<a href="#" onclick="edit_discussion(this,' . $aRow['id'] . '); return false;" data-subject="' . e($aRow['subject']) . '" data-description="' . e(clear_textarea_breaks($aRow['description'])) . '" data-show-to-customer="' . e($aRow['show_to_customer']) . '">' . _l('edit') . '</a>';
        }
        if (staff_can('delete',  'projects')) {
            $subject .= (staff_can('edit',  'projects') ? ' | ' : '') . '<a href="#" onclick="delete_project_discussion(' . $aRow['id'] . '); return false;" class="text-danger">' . _l('delete') . '</a>';
        }
        $subject .= '</div>';
    }

    $row[] = $subject;

    if (!is_null($aRow['last_activity'])) {
        $row[] = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . e(_dt($aRow['last_activity'])) . '">' . e(time_ago($aRow['last_activity'])) . '</span>';
    } else {
        $row[] = _l('project_discussion_no_activity');
    }

    $row[] = e($aRow['totalComments']);

    if ($aRow['show_to_customer'] == 1) {
        $row[] = _l('project_discussion_visible_to_customer_yes');
    } else {
        $row[] = _l('project_discussion_visible_to_customer_no');
    }

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
