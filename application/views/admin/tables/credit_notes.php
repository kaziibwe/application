<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('credit_notes_model');
$remainingAmountSelect = '(SELECT ' . db_prefix() . 'creditnotes.total - (
    (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.credit_id=' . db_prefix() . 'creditnotes.id)
    +
    (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'creditnote_refunds WHERE ' . db_prefix() . 'creditnote_refunds.credit_note_id=' . db_prefix() . 'creditnotes.id)
    )
  )';

return App_table::find('credit_notes')
    ->outputUsing(function ($params) use($remainingAmountSelect) {
        extract($params);

        $aColumns = [
            'number',
            'date',
            get_sql_select_client_company(),
            db_prefix() . 'creditnotes.status as status',
            db_prefix() . 'projects.name as project_name',
            'reference_no',
            'total',
            $remainingAmountSelect.' as remaining_amount',
        ];

        $join = [
            'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'creditnotes.clientid',
            'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'creditnotes.currency',
            'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'creditnotes.project_id',
        ];

        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'creditnotes';

        $custom_fields = get_table_custom_fields('credit_note');

        foreach ($custom_fields as $key => $field) {
            $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
            array_push($customFieldsColumns, $selectAs);
            array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
            array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'creditnotes.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
        }

        $where  = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        if ($clientid != '') {
            array_push($where, 'AND ' . db_prefix() . 'creditnotes.clientid=' . $this->ci->db->escape_str($clientid));
        }

        if (staff_cant('view', 'credit_notes')) {
            array_push($where, 'AND ' . db_prefix() . 'creditnotes.addedfrom=' . get_staff_user_id());
        }

        if ($project_id = $this->ci->input->get('project_id')) {
            array_push($where, 'AND project_id=' . $this->ci->db->escape_str($project_id));
        }

        // Fix for big queries. Some hosting have max_join_limit
        if (count($custom_fields) > 4) {
            @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            db_prefix() . 'creditnotes.id',
            db_prefix() . 'creditnotes.clientid',
            db_prefix() . 'currencies.name as currency_name',
            'project_id',
            'deleted_customer_name',
        ]);

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];

            $numberOutput = '';
            // If is from client area table
            if (is_numeric($clientid) || $project_id) {
                $numberOutput = '<a href="' . admin_url('credit_notes/list_credit_notes/' . $aRow['id']) . '" target="_blank">' . e(format_credit_note_number($aRow['id'])) . '</a>';
            } else {
                $numberOutput = '<a href="' . admin_url('credit_notes/list_credit_notes/' . $aRow['id']) . '" onclick="init_credit_note(' . $aRow['id'] . '); return false;">' . e(format_credit_note_number($aRow['id'])) . '</a>';
            }

            $numberOutput .= '<div class="row-options">';

            if (staff_can('edit',  'credit_notes')) {
                $numberOutput .= '<a href="' . admin_url('credit_notes/credit_note/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            $numberOutput .= '</div>';

            $row[] = $numberOutput;

            $row[] = e(_d($aRow['date']));

            if (empty($aRow['deleted_customer_name'])) {
                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . e($aRow['company']) . '</a>';
            } else {
                $row[] = e($aRow['deleted_customer_name']);
            }

            $row[] = format_credit_note_status($aRow['status']);

            $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . e($aRow['project_name']) . '</a>';

            $row[] = e($aRow['reference_no']);

            $row[] = e(app_format_money($aRow['total'], $aRow['currency_name']));

            $row[] = e(app_format_money($aRow['remaining_amount'], $aRow['currency_name']));

            // Custom fields add values
            foreach ($customFieldsColumns as $customFieldColumn) {
                $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
            }

            $output['aaData'][] = $row;
        }

        return $output;
    })->setRules([
        App_table_filter::new('number', 'NumberRule')->label(_l('credit_note_number')),

        App_table_filter::new('reference_no', 'TextRule')->label(_l('reference_no')),

        App_table_filter::new('date', 'DateRule')->label(_l('credit_note_date')),

        App_table_filter::new('total', 'NumberRule')->label(_l('credit_note_amount')),

        App_table_filter::new('remaining_amount', 'NumberRule')->label(_l('credit_note_remaining_credits'))->column($remainingAmountSelect),

        App_table_filter::new('status', 'MultiSelectRule')
            ->label(_l('credit_note_status'))
            ->options(function ($ci) {
                return collect($ci->credit_notes_model->get_statuses())->map(fn ($status) => [
                    'value' => $status['id'],
                    'label' => $status['name'],
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
                return collect($ci->credit_notes_model->get_credits_years())->map(fn ($data) => [
                    'value' => $data['year'],
                    'label' => $data['year'],
                ])->all();
            }),
    ]);
