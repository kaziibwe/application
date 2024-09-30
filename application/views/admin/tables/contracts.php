<?php

defined('BASEPATH') or exit('No direct script access allowed');

return App_table::find('contracts')
    ->outputUsing(function ($params) {
        extract($params);

        $base_currency = get_base_currency();

        $aColumns = [
            db_prefix() . 'contracts.id as id',
            'subject',
            get_sql_select_client_company(),
            db_prefix() . 'contracts_types.name as type_name',
            'contract_value',
            'datestart',
            'dateend',
            db_prefix() . 'projects.name as project_name',
            'signature',
        ];

        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'contracts';

        $join = [
            'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'contracts.client',
            'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'contracts.project_id',
            'LEFT JOIN ' . db_prefix() . 'contracts_types ON ' . db_prefix() . 'contracts_types.id = ' . db_prefix() . 'contracts.contract_type',
        ];

        $custom_fields = get_table_custom_fields('contracts');

        foreach ($custom_fields as $key => $field) {
            $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
            array_push($customFieldsColumns, $selectAs);
            array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);

            array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'contracts.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
        }

        $where  = [];
        $filter = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        $projectId = $this->ci->input->get('project_id');

        if ($projectId) {
            array_push($where, 'AND project_id=' . $this->ci->db->escape_str($projectId));
        }

        if (count($filter) > 0) {
            array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
        }

        if ($clientid != '') {
            array_push($where, 'AND client=' . $this->ci->db->escape_str($clientid));
        }

        if (staff_cant('view', 'contracts')) {
            array_push($where, 'AND ' . db_prefix() . 'contracts.addedfrom=' . get_staff_user_id());
        }

        $aColumns = hooks()->apply_filters('contracts_table_sql_columns', $aColumns);

        // Fix for big queries. Some hosting have max_join_limit
        if (count($custom_fields) > 4) {
            @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'contracts.id', 'trash', 'client', 'hash', 'marked_as_signed', 'project_id']);

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];

            $row[] = $aRow['id'];

            $subjectOutput = '<a href="' . admin_url('contracts/contract/' . $aRow['id']) . '"' . ($projectId ? ' target="_blank"' : '') . ' class="tw-truncate tw-max-w-sm tw-block tw-w-full">' . e($aRow['subject']) . '</a>';
            if ($aRow['trash'] == 1) {
                $subjectOutput .= '<span class="label label-danger pull-right">' . _l('contract_trash') . '</span>';
            }

            $subjectOutput .= '<div class="row-options">';

            $subjectOutput .= '<a href="' . site_url('contract/' . $aRow['id'] . '/' . $aRow['hash']) . '" target="_blank">' . _l('view') . '</a>';

            if (staff_can('edit',  'contracts')) {
                $subjectOutput .= ' | <a href="' . admin_url('contracts/contract/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }

            if (staff_can('delete',  'contracts')) {
                $subjectOutput .= ' | <a href="' . admin_url('contracts/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }

            $subjectOutput .= '</div>';
            $row[] = $subjectOutput;

            $row[] = '<a href="' . admin_url('clients/client/' . $aRow['client']) . '">' . e($aRow['company']) . '</a>';

            $row[] = e($aRow['type_name']);

            $row[] = e(app_format_money($aRow['contract_value'], $base_currency));

            $row[] = e(_d($aRow['datestart']));

            $row[] = e(_d($aRow['dateend']));

            $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . e($aRow['project_name']) . '</a>';

            if ($aRow['marked_as_signed'] == 1) {
                $row[] = '<span class="text-success">' . _l('marked_as_signed') . '</span>';
            } elseif (!empty($aRow['signature'])) {
                $row[] = '<span class="text-success">' . _l('is_signed') . '</span>';
            } else {
                $row[] = '<span class="text-muted">' . _l('is_not_signed') . '</span>';
            }

            // Custom fields add values
            foreach ($customFieldsColumns as $customFieldColumn) {
                $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
            }

            if (!empty($aRow['dateend']) && $aRow['marked_as_signed'] == 0 && empty($aRow['signature'])) {
                $_date_end = date('Y-m-d', strtotime($aRow['dateend']));
                if ($_date_end < date('Y-m-d')) {
                    $row['DT_RowClass'] = 'danger';
                }
            }

            if (isset($row['DT_RowClass'])) {
                $row['DT_RowClass'] .= ' has-row-options';
            } else {
                $row['DT_RowClass'] = 'has-row-options';
            }

            $row = hooks()->apply_filters('contracts_table_row_data', $row, $aRow);

            $output['aaData'][] = $row;
        }
        return $output;
    })->setRules([
        App_table_filter::new('subject', 'TextRule')->label(_l('contract_subject')),
        App_table_filter::new('datestart', 'DateRule')->label(_l('contract_start_date')),
        App_table_filter::new('dateend', 'DateRule')->label(_l('contract_end_date'))->withEmptyOperators(),
        App_table_filter::new('contract_value', 'NumberRule')->label(_l('contract_value')),
        App_table_filter::new('trash', 'BooleanRule')->label(_l('contract_trash')),
        App_table_filter::new('signed', 'BooleanRule')->label(_l('contracts_view_signed')),
        App_table_filter::new('marked_as_signed', 'BooleanRule')->label(_l('marked_as_signed')),
        App_table_filter::new('expired', 'BooleanRule')->label(_l('contracts_view_expired'))->raw(function ($value) {
            if ($value == '1') {
                return 'dateend IS NOT NULL AND dateend < "' . date('Y-m-d') . '" and trash = 0';
            } else {
                return 'dateend IS NOT NULL AND dateend > "' . date('Y-m-d') . '" and trash = 0';
            }
        }),
        App_table_filter::new('contract_type', 'MultiSelectRule')
            ->label(_l('contract_type'))
            ->options(function ($ci) {
                return collect($ci->contracts_model->get_contract_types())->map(fn ($category) => [
                    'value' => $category['id'],
                    'label' => $category['name'],
                ])->all();
            }),
        App_table_filter::new('year', 'MultiSelectRule')
            ->label(_l('year'))
            ->raw(function ($value, $operator) {
                if ($operator == 'in') {
                    return "YEAR(datestart) IN (" . implode(',', $value) . ")";
                } else {
                    return "YEAR(datestart) NOT IN (" . implode(',', $value) . ")";
                }
            })
            ->options(function ($ci) {
                return collect($ci->contracts_model->get_contracts_years())->map(fn ($data) => [
                    'value' => $data['year'],
                    'label' => $data['year'],
                ])->all();
            }),
    ]);
