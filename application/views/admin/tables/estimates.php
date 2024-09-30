<?php

defined('BASEPATH') or exit('No direct script access allowed');
$this->ci->load->model('estimates_model');

return App_table::find('estimates')
    ->outputUsing(function ($params) {
        $clientid = $params['clientid'];
        $customFieldsColumns = $params['customFieldsColumns'];

        $project_id = $this->ci->input->post('project_id');

        $aColumns = [
            'number',
            'total',
            'total_tax',
            'YEAR(date) as year',
            get_sql_select_client_company(),
            db_prefix() . 'projects.name as project_name',
            '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'estimates.id and rel_type="estimate" ORDER by tag_order ASC) as tags',
            'date',
            'expirydate',
            'reference_no',
            db_prefix() . 'estimates.status',
        ];

        $join = [
            'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'estimates.clientid',
            'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'estimates.currency',
            'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'estimates.project_id',
        ];

        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'estimates';

        $custom_fields = get_table_custom_fields('estimate');

        foreach ($custom_fields as $key => $field) {
            $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
            array_push($customFieldsColumns, $selectAs);
            array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
            array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'estimates.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
        }

        $where  = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        if ($clientid != '') {
            array_push($where, 'AND ' . db_prefix() . 'estimates.clientid=' . $this->ci->db->escape_str($clientid));
        }

        if ($project_id) {
            array_push($where, 'AND project_id=' . $this->ci->db->escape_str($project_id));
        }

        if (staff_cant('view', 'estimates')) {
            $userWhere = 'AND ' . get_estimates_where_sql_for_staff(get_staff_user_id());
            array_push($where, $userWhere);
        }

        $aColumns = hooks()->apply_filters('estimates_table_sql_columns', $aColumns);

        // Fix for big queries. Some hosting have max_join_limit
        if (count($custom_fields) > 4) {
            @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            db_prefix() . 'estimates.id',
            db_prefix() . 'estimates.clientid',
            db_prefix() . 'estimates.invoiceid',
            db_prefix() . 'currencies.name as currency_name',
            'project_id',
            'deleted_customer_name',
            'hash',
        ]);

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];

            $numberOutput = '';
            // If is from client area table or projects area request
            if (is_numeric($clientid) || $project_id) {
                $numberOutput = '<a href="' . admin_url('estimates/list_estimates/' . $aRow['id']) . '" target="_blank">' . e(format_estimate_number($aRow['id'])) . '</a>';
            } else {
                $numberOutput = '<a href="' . admin_url('estimates/list_estimates/' . $aRow['id']) . '" onclick="init_estimate(' . $aRow['id'] . '); return false;">' . e(format_estimate_number($aRow['id'])) . '</a>';
            }

            $numberOutput .= '<div class="row-options">';

            $numberOutput .= '<a href="' . site_url('estimate/' . $aRow['id'] . '/' . $aRow['hash']) . '" target="_blank">' . _l('view') . '</a>';
            if (staff_can('edit',  'estimates')) {
                $numberOutput .= ' | <a href="' . admin_url('estimates/estimate/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            $numberOutput .= '</div>';

            $row[] = $numberOutput;

            $amount = e(app_format_money($aRow['total'], $aRow['currency_name']));

            if ($aRow['invoiceid']) {
                $amount .= '<br /><span class="hide"> - </span><span class="text-success tw-text-sm">' . _l('estimate_invoiced') . '</span>';
            }

            $row[] = $amount;

            $row[] = e(app_format_money($aRow['total_tax'], $aRow['currency_name']));

            $row[] = $aRow['year'];

            if (empty($aRow['deleted_customer_name'])) {
                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . e($aRow['company']) . '</a>';
            } else {
                $row[] = e($aRow['deleted_customer_name']);
            }

            $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . e($aRow['project_name']) . '</a>';

            $row[] = render_tags($aRow['tags']);

            $row[] = e(_d($aRow['date']));

            $row[] = e(_d($aRow['expirydate']));

            $row[] = e($aRow['reference_no']);

            $row[] = format_estimate_status($aRow[db_prefix() . 'estimates.status']);

            // Custom fields add values
            foreach ($customFieldsColumns as $customFieldColumn) {
                $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
            }

            $row['DT_RowClass'] = 'has-row-options';

            $row = hooks()->apply_filters('estimates_table_row_data', $row, $aRow);

            $output['aaData'][] = $row;
        }
        return $output;
    })->setRules([
        App_table_filter::new('number', 'NumberRule')->label(_l('estimate_add_edit_number')),
        App_table_filter::new('reference_no', 'TextRule')->label(_l('reference_no')),
        App_table_filter::new('total', 'NumberRule')->label(_l('estimate_total')),
        App_table_filter::new('subtotal', 'NumberRule')->label(_l('estimate_subtotal')),
        App_table_filter::new('date', 'DateRule')->label(_l('estimate_data_date')),
        App_table_filter::new('expirydate', 'DateRule')
            ->label(_l('estimate_dt_table_heading_expirydate'))
            ->withEmptyOperators(),
        App_table_filter::new('sent', 'BooleanRule')->label(_l('estimate_status_sent'))->raw(function($value) {
            if($value == '1') {
                return 'sent = 1';
            } else {
                return 'sent = 0 and '.db_prefix().'estimates.status NOT IN (2,3,4)';
            }
        }),
        App_table_filter::new('invoiced', 'BooleanRule')->label(_l('estimate_invoiced'))->raw(function($value) {
            return $value == '1' ? 'invoiceid IS NOT NULL' : 'invoiceid IS NULL';
        }),
        App_table_filter::new('signed', 'BooleanRule')->label(_l('contracts_view_signed'))
            ->raw(function ($value) {
                return $value == '1' ? 'signature IS NOT NULL' : 'signature IS NULL';
            }),
        App_table_filter::new('sale_agent', 'SelectRule')->label(_l('sale_agent_string'))
            ->withEmptyOperators()
            ->emptyOperatorValue(0)
            ->isVisible(fn () => staff_can('view', 'estimates'))
            ->options(function ($ci) {
                return collect($ci->estimates_model->get_sale_agents())->map(function ($data) {
                    return [
                        'value' => $data['sale_agent'],
                        'label' => get_staff_full_name($data['sale_agent'])
                    ];
                })->all();
            }),

        App_table_filter::new('status', 'MultiSelectRule')
            ->label(_l('estimate_status'))
            ->options(function ($ci) {
                return collect($ci->estimates_model->get_statuses())->map(fn ($status) => [
                    'value' => (string) $status,
                    'label' => format_estimate_status($status, '', false),
                ])->all();
            }),

        App_table_filter::new('year', 'MultiSelectRule')
            ->label(_l('year'))
            ->raw(function ($value, $operator) {
                if ($operator == 'in') {
                    return "YEAR(date) IN (" . implode(',', $value) . ")";
                } else {
                    return "YEAR(date) NOT IN (" . implode(',', $value) . ")";
                }
            })
            ->options(function ($ci) {
                return collect($ci->estimates_model->get_estimates_years())->map(fn ($data) => [
                    'value' => $data['year'],
                    'label' => $data['year'],
                ])->all();
            }),
    ]);
