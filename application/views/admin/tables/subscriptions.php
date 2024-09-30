<?php

defined('BASEPATH') or exit('No direct script access allowed');

return App_table::find('subscriptions')
    ->outputUsing(function () {
        $aColumns = [
            db_prefix() . 'subscriptions.id as id',
            db_prefix() . 'subscriptions.name as name',
            get_sql_select_client_company(),
            db_prefix() . 'projects.name as project_name',
            db_prefix() . 'subscriptions.status as status',
            'next_billing_cycle',
            'date_subscribed',
            'last_sent_at',
        ];

        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'subscriptions';

        $where  = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        if ($this->ci->input->get('project_id')) {
            array_push($where, 'AND project_id=' . $this->ci->db->escape_str($this->ci->input->get('project_id')));
        }

        if ($this->ci->input->get('client_id')) {
            array_push($where, 'AND ' . db_prefix() . 'subscriptions.clientid=' . $this->ci->db->escape_str($this->ci->input->get('client_id')));
        }

        if (staff_cant('view', 'subscriptions')) {
            array_push($where, 'AND ' . db_prefix() . 'subscriptions.created_from=' . get_staff_user_id());
        }

        $join = [
            'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'subscriptions.clientid',
            'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'subscriptions.project_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            db_prefix() . 'subscriptions.id',
            db_prefix() . 'subscriptions.clientid as clientid',
            'in_test_environment',
            'stripe_subscription_id',
            'project_id',
            'hash',
        ]);

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];

            $row[] = $aRow['id'];

            $link       = admin_url('subscriptions/edit/' . $aRow['id']);
            $outputName = '<a href="' . $link . '">' . e($aRow['name']) . '</a>';

            $outputName .= '<div class="row-options">';

            $outputName .= '<a href="' . site_url('subscription/' . $aRow['hash']) . '" target="_blank">' . _l('view_subscription') . '</a>';

            if (staff_can('edit',  'subscriptions')) {
                $outputName .= ' | <a href="' . admin_url('subscriptions/edit/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if ((empty($aRow['stripe_subscription_id'])
                    || (!is_null($aRow['in_test_environment'])
                        && $aRow['in_test_environment'] == 1))
                && staff_can('delete',  'subscriptions')
            ) {
                $outputName .= ' | <a href="' . admin_url('subscriptions/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $outputName .= '</div>';

            $row[] = $outputName;

            $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . e($aRow['company']) . '</a>';

            $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . e($aRow['project_name']) . '</a>';

            if (empty($aRow['status'])) {
                $row[] = _l('subscription_not_subscribed');
            } else {
                $row[] = e(_l('subscription_' . $aRow['status'], '', false));
            }

            if ($aRow['next_billing_cycle']) {
                $row[] = e(_d(date('Y-m-d', $aRow['next_billing_cycle'])));
            } else {
                $row[] = '-';
            }

            if ($aRow['date_subscribed']) {
                $row[] = e(_dt($aRow['date_subscribed']));
            } else {
                $row[] = '-';
            }

            if ($aRow['last_sent_at']) {
                $row[] = e(_dt($aRow['last_sent_at']));
            } else {
                $row[] = '-';
            }


            $row['DT_RowClass'] = 'has-row-options';
            $output['aaData'][] = $row;
        }
        return $output;
    })->setRules([
        // ( stripe_subscription_id IS NULL OR stripe_subscription_id = "" ) - not subscribed
        App_table_filter::new('name','TextRule')->label(_l('subscription_name')),
        App_table_filter::new('date_subscribed','DateRule')->label(_l('date_subscribed')),
        App_table_filter::new('status', 'MultiSelectRule')
            ->label(_l('subscription_status'))
            ->options(function () {
                return collect(get_subscriptions_statuses())->map(fn($status)=> [
                    'value'=>$status['id'],
                    'label'=>_l('subscription_' . $status['id'])
                ]);
            }),
    ]);
