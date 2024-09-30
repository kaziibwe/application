<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    'short_description',
    'created_at',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'wiki_books';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="' . admin_url('wiki/books/book/' . $aRow['id']) . '">' . $_data . '</a>';
            $_data .= '<div class="row-options">';
            $_data .= '<a href="' . admin_url('wiki/books/book/' . $aRow['id']) . '">' . _l('view') . '</a>';

            $_data .= ' | <a href="' . admin_url('wiki/books/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            $_data .= '</div>';
        }
        $row[] = $_data;
    }
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
