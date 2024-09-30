<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * General function for all datatables, performs search,additional select,join,where,orders
 * @param  array $aColumns           table columns
 * @param  mixed $sIndexColumn       main column in table for bettter performing
 * @param  string $sTable            table name
 * @param  array  $join              join other tables
 * @param  array  $where             perform where in query
 * @param  array  $additionalSelect  select additional fields
 * @param  string $sGroupBy group results
 * @return array
 */
function data_tables_init($aColumns, $sIndexColumn, $sTable, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [])
{
    $CI          = &get_instance();
    $data      = $CI->input->post();

    /*
     * Paging
     */
    $sLimit = '';
    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = 'LIMIT ' . intval($CI->input->post('start')) . ', ' . intval($CI->input->post('length'));
    }

    $allColumns = [];

    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (startsWith($_column[0], db_prefix())) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);
                    array_push($allColumns, $_prefix);
                } else {
                    array_push($allColumns, $column);
                }
            } else {
                array_push($allColumns, $_column[0]);
            }
        } else {
            array_push($allColumns, $column);
        }
    }

    /*
     * Ordering
     */
    $nullColumnsAsLast = get_null_columns_that_should_be_sorted_as_last();

    $sOrder = '';
    if ($CI->input->post('order')) {
        $sOrder = 'ORDER BY ';
        foreach ($CI->input->post('order') as $key => $val) {
            $columnName = $aColumns[intval($data['order'][$key]['column'])];
            $dir        = strtoupper($data['order'][$key]['dir']);
            $type       = $data['order'][$key]['type'] ?? null;

            // Security
            if (!in_array($dir, ['ASC', 'DESC'])) {
                $dir = 'ASC';
            }

            if (strpos($columnName, ' as ') !== false) {
                $columnName = strbefore($columnName, ' as');
            }

            // first checking is for eq tablename.column name
            // second checking there is already prefixed table name in the column name
            // this will work on the first table sorting - checked by the draw parameters
            // in future sorting user must sort like he want and the duedates won't be always last
            if ((in_array($sTable . '.' . $columnName, $nullColumnsAsLast)
                || in_array($columnName, $nullColumnsAsLast))) {
                $sOrder .= $columnName . ' IS NULL ' . $dir . ', ' . $columnName;
            } else {
                // Custom fields sorting support for number type custom fields
                if ($type === 'number') {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', 'CAST(' . $columnName . ' as SIGNED)', $sTable);
                } elseif ($type === 'date_picker') {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', 'CAST(' . $columnName . ' as DATE)', $sTable);
                } elseif ($type === 'date_picker_time') {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', 'CAST(' . $columnName . ' as DATETIME)', $sTable);
                } else {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', $columnName, $sTable);
                }
            }

            $sOrder .= ' ' . $dir . ', ';
        }

        if (trim($sOrder) == 'ORDER BY') {
            $sOrder = '';
        }

        $sOrder = rtrim($sOrder, ', ');

        if (
            get_option('save_last_order_for_tables') == '1'
            && $CI->input->post('last_order_identifier')
            && $CI->input->post('order')
        ) {
            // https://stackoverflow.com/questions/11195692/json-encode-sparse-php-array-as-json-array-not-json-object

            $indexedOnly = [];
            foreach ($CI->input->post('order') as $row) {
                $indexedOnly[] = array_values($row);
            }

            $meta_name = $CI->input->post('last_order_identifier') . '-table-last-order';

            update_staff_meta(get_staff_user_id(), $meta_name, json_encode($indexedOnly, JSON_NUMERIC_CHECK));
        }
    }
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = '';
    if ((isset($data['search'])) && $data['search']['value'] != '') {
        $search_value = $data['search']['value'];
        $search_value = trim($search_value);

        $sWhere             = 'WHERE (';
        $sMatchCustomFields = [];

        // Not working, do not use it
        $useMatchForCustomFieldsTableSearch = hooks()->apply_filters('use_match_for_custom_fields_table_search', 'false');

        for ($i = 0; $i < count($aColumns); $i++) {
            $columnName = $aColumns[$i];
            if (strpos($columnName, ' as ') !== false) {
                $columnName = strbefore($columnName, ' as');
            }

            if (stripos($columnName, 'AVG(') === false && stripos($columnName, 'SUM(') === false) {
                if (($data['columns'][$i]) && $data['columns'][$i]['searchable'] == 'true') {
                    if (isset($searchAs[$i])) {
                        $columnName = $searchAs[$i];
                    }

                    // Custom fields values are FULLTEXT and should be searched with MATCH
                    // Not working ATM
                    if ($useMatchForCustomFieldsTableSearch === 'true' && startsWith($columnName, 'ctable_')) {
                        $sMatchCustomFields[] = $columnName;
                    } else {
                        $sWhere .= 'convert(' . $columnName . ' USING utf8)' . " LIKE '%" . $CI->db->escape_like_str($search_value) . "%' ESCAPE '!' OR ";
                    }
                }
            }
        }

        if (count($sMatchCustomFields) > 0) {
            $s = $CI->db->escape_str($search_value);
            foreach ($sMatchCustomFields as $matchCustomField) {
                $sWhere .= "MATCH ({$matchCustomField}) AGAINST (CONVERT(BINARY('{$s}') USING utf8)) OR ";
            }
        }

        if (count($additionalSelect) > 0) {
            foreach ($additionalSelect as $searchAdditionalField) {
                if (strpos($searchAdditionalField, ' as ') !== false) {
                    $searchAdditionalField = strbefore($searchAdditionalField, ' as');
                }

                if (stripos($columnName, 'AVG(') === false && stripos($columnName, 'SUM(') === false) {
                    // Use index
                    $sWhere .= 'convert(' . $searchAdditionalField . ' USING utf8)' . " LIKE '%" . $CI->db->escape_like_str($search_value) . "%'ESCAPE '!' OR ";
                }
            }
        }

        $sWhere = substr_replace($sWhere, '', -3);
        $sWhere .= ')';
    } else {
        // Check for custom filtering
        $searchFound = 0;
        $sWhere      = 'WHERE (';

        foreach ($aColumns as $i => $column) {
            if (isset($data['columns'][$i]) && $data['columns'][$i]['searchable'] == 'true') {
                $search_value = $data['columns'][$i]['search']['value'];
                $columnName = $column;

                if (strpos($columnName, ' as ') !== false) {
                    $columnName = strbefore($columnName, ' as');
                }

                if ($search_value != '') {
                    // Add condition for current column
                    $likeClause = $CI->db->escape_like_str($search_value);
                    $sWhere .= "convert($columnName USING utf8) LIKE '%$likeClause%' ESCAPE '!' OR ";

                    // Process additional select fields if any
                    if (count($additionalSelect) > 0) {
                        foreach ($additionalSelect as $searchAdditionalField) {
                            $sWhere .= "convert($searchAdditionalField USING utf8) LIKE '%$likeClause%' ESCAPE '!' OR ";
                        }
                    }

                    $searchFound++;
                }
            }
        }

        if ($searchFound > 0) {
            $sWhere = substr_replace($sWhere, '', -3);
            $sWhere .= ')';
        } else {
            $sWhere = '';
        }
    }

    /*
     * SQL queries
     * Get data to display
     */
    $additionalColumns = '';
    if (count($additionalSelect) > 0) {
        $additionalColumns = ',' . implode(',', $additionalSelect);
    }

    $where = implode(' ', $where);

    if ($sWhere == '') {
        $where = trim($where);
        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
            if (startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
            $where = 'WHERE ' . $where;
        }
    }

    $join = implode(' ', $join);

    $resultQuery = '
    SELECT ' . str_replace(' , ', ' ', implode(', ', $allColumns)) . ' ' . $additionalColumns . "
    FROM $sTable
    " . $join . "
    $sWhere
    " . $where . "
    $sGroupBy
    $sOrder
    $sLimit
    ";

    $rResult = hooks()->apply_filters(
        'datatables_sql_query_results',
        $CI->db->query($resultQuery)->result_array(),
        [
            'table' => $sTable,
            'limit' => $sLimit,
            'order' => $sOrder,
        ]
    );

    /* Data set length after filtering */
    $iFilteredTotal = $CI->db->query("
        SELECT COUNT(*) as iFilteredTotal
        FROM $sTable
        " . $join . "
        $sWhere
        " . $where . "
        $sGroupBy
    ")->row()->iFilteredTotal;

    if (startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }

    /* Total data set length */
    $iTotal = $CI->db->query("SELECT COUNT(*) as iTotal from $sTable $join $where")->row()->iTotal;

    return [
        'rResult' => $rResult,
        'output'  => [
            'draw'                 => $data['draw'] ? intval($data['draw']) : 0,
            'iTotalRecords'        => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData'               => [],
        ],
    ];
}

/**
 * Used in data_tables_init function to fix sorting problems when duedate is null
 * Null should be always last
 * @return array
 */
function get_null_columns_that_should_be_sorted_as_last()
{
    $columns = [
        db_prefix() . 'projects.deadline',
        db_prefix() . 'tasks.duedate',
        db_prefix() . 'contracts.dateend',
        db_prefix() . 'subscriptions.date_subscribed',
    ];

    return hooks()->apply_filters('null_columns_sort_as_last', $columns);
}
/**
 * Render table used for datatables
 * @param  array  $headings           [description]
 * @param  string $class              table class / added prefix table-$class
 * @param  array  $additional_classes
 * @return string                     formatted table
 */
/**
 * Render table used for datatables
 * @param  array   $headings
 * @param  string  $class              table class / add prefix eq.table-$class
 * @param  array   $additional_classes additional table classes
 * @param  array   $table_attributes   table attributes
 * @param  boolean $tfoot              includes blank tfoot
 * @return string
 */
function render_datatable($headings = [], $class = '', $additional_classes = [''], $table_attributes = [])
{
    $_additional_classes = '';
    $_table_attributes   = ' ';
    if (count($additional_classes) > 0) {
        $_additional_classes = ' ' . implode(' ', $additional_classes);
    }
    $CI      = &get_instance();
    $browser = $CI->agent->browser();
    $IEfix   = '';
    if ($browser == 'Internet Explorer') {
        $IEfix = 'ie-dt-fix';
    }

    foreach ($table_attributes as $key => $val) {
        $_table_attributes .= $key . '=' . '"' . $val . '" ';
    }

    $table = '<div class="' . $IEfix . '"><table' . $_table_attributes . 'class="dt-table-loading table table-' . $class . '' . $_additional_classes . '">';
    $table .= '<thead>';
    $table .= '<tr>';
    foreach ($headings as $heading) {
        if (!is_array($heading)) {
            $table .= '<th>' . $heading . '</th>';
        } else {
            $th_attrs = '';
            if (isset($heading['th_attrs'])) {
                foreach ($heading['th_attrs'] as $key => $val) {
                    $th_attrs .= $key . '=' . '"' . $val . '" ';
                }
            }
            $th_attrs = ($th_attrs != '' ? ' ' . $th_attrs : $th_attrs);
            $table .= '<th' . $th_attrs . '>' . $heading['name'] . '</th>';
        }
    }
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody></tbody>';
    $table .= '</table></div>';
    echo $table;
}

/**
 * Translated datatables language based on app languages
 * This feature is used on both admin and customer area
 * @return array
 */
function get_datatables_language_array()
{
    $lang = [
        'emptyTable'        => preg_replace("/{(\d+)}/", _l('dt_entries'), _l('dt_empty_table')),
        'info'              => preg_replace("/{(\d+)}/", _l('dt_entries'), _l('dt_info')),
        'infoEmpty'         => preg_replace("/{(\d+)}/", _l('dt_entries'), _l('dt_info_empty')),
        'infoFiltered'      => preg_replace("/{(\d+)}/", _l('dt_entries'), _l('dt_info_filtered')),
        'lengthMenu'        => '_MENU_',
        'loadingRecords'    => _l('dt_loading_records'),
        'processing'        => '<div class="dt-loader"></div>',
        'search'            => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-search"></span></span>',
        'searchPlaceholder' => _l('dt_search'),
        'zeroRecords'       => _l('dt_zero_records'),
        'paginate'          => [
            'first'    => _l('dt_paginate_first'),
            'last'     => _l('dt_paginate_last'),
            'next'     => _l('dt_paginate_next'),
            'previous' => _l('dt_paginate_previous'),
        ],
        'aria' => [
            'sortAscending'  => _l('dt_sort_ascending'),
            'sortDescending' => _l('dt_sort_descending'),
        ],
    ];

    return hooks()->apply_filters('datatables_language_array', $lang);
}

/**
 * Function that will parse filters for datatables and will return based on a couple conditions.
 * The returned result will be pushed inside the $where variable in the table SQL
 * @param  array $filter
 * @return string
 */
function prepare_dt_filter($filter)
{
    $filter = implode(' ', $filter);
    if (startsWith($filter, 'AND')) {
        $filter = substr($filter, 3);
    } elseif (startsWith($filter, 'OR')) {
        $filter = substr($filter, 2);
    }

    return $filter;
}
/**
 * Get table last order
 * @param  string $tableID table unique identifier id
 * @return string
 */
function get_table_last_order($tableID)
{
    return htmlentities(get_staff_meta(get_staff_user_id(), $tableID . '-table-last-order'));
}
