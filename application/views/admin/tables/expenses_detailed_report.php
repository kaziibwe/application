<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('expenses_model');
$this->ci->load->model('payment_modes_model');

return App_table::find('expenses_detailed_report')
    ->setDbTableName('expenses')
    ->outputUsing(function ($params) {
        extract($params);

        $aColumns = [
            db_prefix() . 'expenses.category',
            'amount',
            'expense_name',
            'tax',
            'tax2',
            '(SELECT taxrate FROM ' . db_prefix() . 'taxes WHERE id=' . db_prefix() . 'expenses.tax)',
            'amount as amount_with_tax',
            'billable',
            'date',
            get_sql_select_client_company(),
            'invoiceid',
            'reference_no',
            'paymentmode',
        ];

        $join = [
            'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'expenses.clientid',
            'LEFT JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
            'LEFT JOIN ' . db_prefix() . 'taxes ON ' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax',
            'LEFT JOIN ' . db_prefix() . 'taxes as taxes_2 ON taxes_2.id = ' . db_prefix() . 'expenses.tax2',
        ];

        $where  = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        $by_currency = $this->ci->input->post('currency');

        if ($by_currency) {
            $currency = $this->ci->currencies_model->get($by_currency);
            array_push($where, 'AND currency=' . $this->ci->db->escape_str($by_currency));
        } else {
            $currency = $base_currency;
        }

        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'expenses';
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            db_prefix() . 'expenses_categories.name as category_name',
            db_prefix() . 'expenses.id',
            db_prefix() . 'expenses.clientid',
            'currency',
            db_prefix() . 'taxes.name as tax1_name',
            db_prefix() . 'taxes.taxrate as tax1_taxrate',
            'taxes_2.name as tax2_name',
            'taxes_2.taxrate as tax2_taxrate',
        ]);
        $output  = $result['output'];
        $rResult = $result['rResult'];

        $footer_data = [
            'tax_1'           => 0,
            'tax_2'           => 0,
            'amount'          => 0,
            'total_tax'       => 0,
            'amount_with_tax' => 0,
        ];

        foreach ($rResult as $aRow) {
            $row = [];
            for ($i = 0; $i < count($aColumns); $i++) {
                if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                    $_data = $aRow[strafter($aColumns[$i], 'as ')];
                } else {
                    $_data = $aRow[$aColumns[$i]];
                }

                if ($aColumns[$i] == db_prefix() . 'expenses.category') {
                    $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . e($aRow['category_name']) . '</a>';
                } elseif ($aColumns[$i] == 'expense_name') {
                    $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . e($aRow['expense_name']) . '</a>';
                } elseif ($aColumns[$i] == 'amount' || $i == 6) {
                    $total = $_data;
                    if ($i != 6) {
                        $footer_data['amount'] += $total;
                    } else {
                        if ($aRow['tax'] != 0 && $i == 6) {
                            $total += ($total / 100 * $aRow['tax1_taxrate']);
                        }
                        if ($aRow['tax2'] != 0 && $i == 6) {
                            $total += ($aRow['amount'] / 100 * $aRow['tax2_taxrate']);
                        }
                        $footer_data['amount_with_tax'] += $total;
                    }

                    $_data = e(app_format_money($total, $currency->name));
                } elseif ($i == 9) {
                    $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . e($aRow['company']) . '</a>';
                } elseif ($aColumns[$i] == 'paymentmode') {
                    $_data = '';
                    if ($aRow['paymentmode'] != '0' && !empty($aRow['paymentmode'])) {
                        $payment_mode = $this->ci->payment_modes_model->get($aRow['paymentmode'], [], false, true);
                        if ($payment_mode) {
                            $_data = e($payment_mode->name);
                        }
                    }
                } elseif ($aColumns[$i] == 'date') {
                    $_data = e(_d($_data));
                } elseif ($aColumns[$i] == 'tax') {
                    if ($aRow['tax'] != 0) {
                        $_data = e($aRow['tax1_name'] . ' - ' . app_format_number($aRow['tax1_taxrate']) . '%');
                    } else {
                        $_data = '';
                    }
                } elseif ($aColumns[$i] == 'tax2') {
                    if ($aRow['tax2'] != 0) {
                        $_data = e($aRow['tax2_name'] . ' - ' . app_format_number($aRow['tax2_taxrate']) . '%');
                    } else {
                        $_data = '';
                    }
                } elseif ($i == 5) {
                    if ($aRow['tax'] != 0 || $aRow['tax2'] != 0) {
                        if ($aRow['tax'] != 0) {
                            $total = ($total / 100 * $aRow['tax1_taxrate']);
                            $footer_data['tax_1'] += $total;
                        }
                        if ($aRow['tax2'] != 0) {
                            $totalTax2 = ($aRow['amount'] / 100 * $aRow['tax2_taxrate']);
                            $total += $totalTax2;
                            $footer_data['tax_2'] += $totalTax2;
                        }
                        $_data = e(app_format_money($total, $currency->name));
                        $footer_data['total_tax'] += $total;
                    } else {
                        $_data = app_format_number(0);
                    }
                } elseif ($aColumns[$i] == 'billable') {
                    if ($aRow['billable'] == 1) {
                        $_data = _l('expenses_list_billable');
                    } else {
                        $_data = _l('expense_not_billable');
                    }
                } elseif ($aColumns[$i] == 'invoiceid') {
                    if ($_data) {
                        $_data = '<a href="' . admin_url('invoices/list_invoices/' . $_data) . '">' . e(format_invoice_number($_data)) . '</a>';
                    } else {
                        $_data = '';
                    }
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }

        foreach ($footer_data as $key => $total) {
            $footer_data[$key] = e(app_format_money($total, $currency->name));
        }

        $output['sums'] = $footer_data;

        return $output;
    })->setRules(App_table::find('expenses')->rules());
