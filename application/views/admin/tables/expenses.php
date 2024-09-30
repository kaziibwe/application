<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('expenses_model');

return App_table::find('expenses')
    ->outputUsing(function ($params) {
        extract($params);

        $aColumns = [
            '1', // bulk actions
            db_prefix() . 'expenses.id as id',
            db_prefix() . 'expenses_categories.name as category_name',
            'amount',
            'expense_name',
            'file_name',
            'date',
            db_prefix() . 'projects.name as project_name',
            get_sql_select_client_company(),
            'invoiceid',
            'reference_no',
            'paymentmode',
        ];

        $join = [
            'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'expenses.clientid',
            'JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
            'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'expenses.project_id',
            'LEFT JOIN ' . db_prefix() . 'files ON ' . db_prefix() . 'files.rel_id = ' . db_prefix() . 'expenses.id AND rel_type="expense"',
            'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'expenses.currency',
        ];

        $custom_fields = get_table_custom_fields('expenses');

        foreach ($custom_fields as $key => $field) {
            $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
            array_push($customFieldsColumns, $selectAs);
            array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
            array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'expenses.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
        }

        $where  = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        if ($clientid != '') {
            array_push($where, 'AND ' . db_prefix() . 'expenses.clientid=' . $this->ci->db->escape_str($clientid));
        }

        if (staff_cant('view', 'expenses')) {
            array_push($where, 'AND ' . db_prefix() . 'expenses.addedfrom=' . get_staff_user_id());
        }

        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'expenses';

        $aColumns = hooks()->apply_filters('expenses_table_sql_columns', $aColumns);

        // Fix for big queries. Some hosting have max_join_limit
        if (count($custom_fields) > 4) {
            @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'billable',
            db_prefix() . 'currencies.name as currency_name',
            db_prefix() . 'expenses.clientid',
            'tax',
            'tax2',
            'project_id',
            'recurring',
        ]);
        $output  = $result['output'];
        $rResult = $result['rResult'];

        $this->ci->load->model('payment_modes_model');

        foreach ($rResult as $aRow) {
            $row = [];

            $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

            $row[] = $aRow['id'];

            $categoryOutput = '';

            if (is_numeric($clientid)) {
                $categoryOutput = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '">' . e($aRow['category_name']) . '</a>';
            } else {
                $categoryOutput = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" onclick="init_expense(' . $aRow['id'] . ');return false;">' . e($aRow['category_name']) . '</a>';
            }

            if ($aRow['billable'] == 1) {
                if ($aRow['invoiceid'] == null) {
                    $categoryOutput .= ' <p class="text-danger tw-text-sm tw-mb-1">' . _l('expense_list_unbilled') . '</p>';
                } else {
                    if (total_rows(db_prefix() . 'invoices', [
                        'id' => $aRow['invoiceid'],
                        'status' => 2,
                    ]) > 0) {
                        $categoryOutput .= ' <p class="text-success tw-text-sm tw-mb-1">' . _l('expense_list_billed') . '</p>';
                    } else {
                        $categoryOutput .= ' <p class="text-success tw-text-sm tw-mb-1">' . _l('expense_list_invoice') . '</p>';
                    }
                }
            }

            if ($aRow['recurring'] == 1) {
                $categoryOutput .= '<span class="label label-primary"> ' . _l('expense_recurring_indicator') . '</span>';
            }

            $categoryOutput .= '<div class="row-options">';

            $categoryOutput .= '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" onclick="init_expense(' . $aRow['id'] . ');return false;">' . _l('view') . '</a>';

            if (staff_can('edit',  'expenses')) {
                $categoryOutput .= ' | <a href="' . admin_url('expenses/expense/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }

            if (staff_can('delete',  'expenses')) {
                $categoryOutput .= ' | <a href="' . admin_url('expenses/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }

            $categoryOutput .= '</div>';
            $row[] = $categoryOutput;

            $total    = $aRow['amount'];
            $tmpTotal = $total;

            if ($aRow['tax'] != 0) {
                $tax = get_tax_by_id($aRow['tax']);
                $total += ($total / 100 * $tax->taxrate);
            }
            if ($aRow['tax2'] != 0) {
                $tax = get_tax_by_id($aRow['tax2']);
                $total += ($tmpTotal / 100 * $tax->taxrate);
            }

            $row[] = e(app_format_money($total, $aRow['currency_name']));

            $row[] = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" onclick="init_expense(' . $aRow['id'] . ');return false;">' . e($aRow['expense_name']) . '</a>';

            $outputReceipt = '';

            if (!empty($aRow['file_name'])) {
                $outputReceipt = '<a href="' . site_url('download/file/expense/' . $aRow['id']) . '">' . e($aRow['file_name']) . '</a>';
            }

            $row[] = $outputReceipt;

            $row[] = e(_d($aRow['date']));

            $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . e($aRow['project_name']) . '</a>';

            $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . e($aRow['company']) . '</a>';

            if ($aRow['invoiceid']) {
                $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['invoiceid']) . '">' . e(format_invoice_number($aRow['invoiceid'])) . '</a>';
            } else {
                $row[] = '';
            }

            $row[] = e($aRow['reference_no']);

            $paymentModeOutput = '';

            if ($aRow['paymentmode'] != '0' && !empty($aRow['paymentmode'])) {
                $payment_mode = $this->ci->payment_modes_model->get($aRow['paymentmode'], [], false, true);
                if ($payment_mode) {
                    $paymentModeOutput = e($payment_mode->name);
                }
            }

            $row[] = $paymentModeOutput;

            // Custom fields add values
            foreach ($customFieldsColumns as $customFieldColumn) {
                $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
            }

            $row['DT_RowClass'] = 'has-row-options';

            $row = hooks()->apply_filters('expenses_table_row_data', $row, $aRow);

            $output['aaData'][] = $row;
        }
        return $output;
    })->setRules([
        App_table_filter::new('expense_name', 'TextRule')->label(_l('expense_name')),
        App_table_filter::new('date', 'DateRule')->label(str_replace(':', '', _l('expense_date'))),
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
                return collect($ci->expenses_model->get_expenses_years())->map(fn ($data) => [
                    'value' => $data['year'],
                    'label' => $data['year'],
                ])->all();
            }),
        App_table_filter::new('amount', 'NumberRule')->label(str_replace(':', '', _l('expense_amount'))),
        App_table_filter::new('category', 'MultiSelectRule')
            ->label(_l('expense_report_category'))
            ->options(function ($ci) {
                return collect($ci->expenses_model->get_category())->map(fn ($category) => [
                    'value' => $category['id'],
                    'label' => $category['name'],
                ])->all();
            }),
        App_table_filter::new('billable', 'BooleanRule')->label(_l('expenses_list_billable')),
        App_table_filter::new('unbilled', 'BooleanRule')->label(_l('expenses_list_unbilled'))->raw(function ($value) {
            return $value == "1" ? 'invoiceid IS NULL' : 'invoiceid IS NOT NULL';
        }),
        App_table_filter::new('recurring', 'BooleanRule')->label(_l('expenses_list_recurring')),
        App_table_filter::new('paymentmode', 'SelectRule')->label(_l('payment_mode'))->options(function ($ci) {
            return collect($ci->payment_modes_model->get('', [
                'invoices_only !=' => 1,
            ], true))->map(fn ($mode) => [
                'value' => $mode['id'],
                'label' => $mode['name'],
            ])->all();
        }),
    ]);
