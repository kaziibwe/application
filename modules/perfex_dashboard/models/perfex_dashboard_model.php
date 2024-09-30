<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Perfex_dashboard_model extends App_Model
{
    protected static $PER_PAGE = 5;

    public function __construct()
    {
        parent::__construct();
    }

    public function create_dashboard($dashboard_name, $note)
    {
        $data = [
            'name' => $dashboard_name,
            'dashboard_widgets' => serialize([]),
            'note' => $note,
        ];
        $this->db->insert(db_prefix() . 'perfex_dashboard_list', $data);
        $dashboard_id = $this->db->insert_id();

        return $dashboard_id;
    }

    public function clone_dashboard($clone_id, $dashboard_name, $note)
    {
        $dashboard_rows = $this->perfex_dashboard_model->find_dashboard($clone_id);
        if (count($dashboard_rows) == 0) {
            show_404();
            die();
        }

        $data = [
            'name' => $dashboard_name,
            'note' => $note,
            'dashboard_widgets' => $dashboard_rows[0]['dashboard_widgets'],
        ];
        $this->db->insert(db_prefix() . 'perfex_dashboard_list', $data);
        $dashboard_id = $this->db->insert_id();

        return $dashboard_id;
    }

    public function get_dashboards()
    {
        $sqlSelect = "
            SELECT TBLList.*
            FROM " .  db_prefix() . "perfex_dashboard_list TBLList
        ";
        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return $dataSelect;
    }

    public function find_dashboard($dashboard_id)
    {
        $sqlSelect = "
            SELECT TBLList.*
            FROM " . db_prefix() . "perfex_dashboard_list TBLList
            WHERE TBLList.id = " . $dashboard_id . "
            LIMIT 1 
        ";
        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return $dataSelect;
    }

    public function delete_dashboard($dashboard_id)
    {
        $sqlDeleteDashboard = "
            DELETE FROM " . db_prefix() . "perfex_dashboard_list WHERE id = " . $dashboard_id . "
        ";
        $sqlDeleteUsers = "
            DELETE FROM " . db_prefix() . "perfex_dashboard_users WHERE dashboard_id = " . $dashboard_id . "
        ";

        $this->db->query($sqlDeleteDashboard);
        $this->db->query($sqlDeleteUsers);
    }

    public function paginate_widgets($page)
    {
        $sqlCount = "
            SELECT 
                COUNT(*) AS TotalRows
            FROM " . db_prefix() . "perfex_dashboard_widgets TBLWidgets 
        ";
        $rsCount = $this->db->query($sqlCount);
        $dataCount = $rsCount->result_array();

        $total_items = intval($dataCount[0]['TotalRows']);
        $total_pages = ceil($total_items / static::$PER_PAGE);

        $start_index = ($page - 1) * static::$PER_PAGE;

        $sqlSelect = "
            SELECT 
                TBLWidgets.*
            FROM " . db_prefix() . "perfex_dashboard_widgets TBLWidgets 
            LIMIT " . $start_index . ", " . static::$PER_PAGE . " 
        ";
        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return [
            'current_page' => $page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'data' => $dataSelect,
        ];
    }

    public function get_widgets_by_category($category)
    {
        $sqlSelect = "
            SELECT 
                TBLWidgets.*
            FROM " . db_prefix() . "perfex_dashboard_widgets TBLWidgets 
            WHERE TBLWidgets.category = '" . $category . "'
        ";
        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return $dataSelect;
    }

    public function get_widgets($filters)
    {
        $sqlSelect = "
            SELECT 
                TBLWidgets.*,
                TBLCategories.name AS category_name
            FROM " . db_prefix() . "perfex_dashboard_widgets TBLWidgets 
                LEFT JOIN " . db_prefix() . "perfex_dashboard_widget_categories TBLCategories
                    ON TBLWidgets.category = TBLCategories.id
        ";
        if (count($filters) > 0) {
            $sqlSelect .= ' WHERE ';
            $countWhere = 0;
            foreach ($filters as $filter_key => $filter_value) {
                $countWhere++;
                if ($countWhere != 1) {
                    $sqlSelect .= " AND ";
                }
                switch ($filter_key) {
                    case 'category':
                        if ($filter_value != '') {
                            $sqlSelect .= " TBLWidgets.category = '" . $filter_value . "' ";
                        } else {
                            $sqlSelect .= " 1 = 1 ";
                        }
                        break;
                    case 'search':
                        $sqlSelect .= " ( TBLWidgets.name LIKE '%" . $filter_value . "%' OR TBLWidgets.note LIKE '%" . $filter_value . "%' ) ";
                        break;
                }
            }
        }
        $sqlSelect .= " ORDER BY TBLWidgets.created_at DESC ";

        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return $dataSelect;
    }

    public function select_widgets_by_ids($ids = [])
    {
        if (count($ids) == 0) {
            return [];
        }

        $concated_ids = $this->concat_array_to_string($ids);

        $sqlSelect = "
            SELECT 
                TBLWidgets.*
            FROM " . db_prefix() . "perfex_dashboard_widgets TBLWidgets 
            WHERE TBLWidgets.id IN ( 
        ";
        $sqlSelect .= $concated_ids;
        $sqlSelect .= ") ";
        $sqlSelect .= " ORDER BY FIELD(id, " . $concated_ids . " ) ASC ";
        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return $dataSelect;
    }

    public function select_widgets_except_names($names = [])
    {
        $sqlSelect = "
            SELECT 
                TBLWidgets.*
            FROM " . db_prefix() . "perfex_dashboard_widgets TBLWidgets 
        ";
        if (count($names) > 0) {
            $sqlSelect .= " WHERE TBLWidgets.widget_name NOT IN ( ";
            $sqlSelect .= $this->concat_array_to_string($names);
            $sqlSelect .= ")";
        }

        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return $dataSelect;
    }

    public function select_widgets_except_ids($names = [])
    {
        $sqlSelect = "
            SELECT 
                TBLWidgets.*
            FROM " . db_prefix() . "perfex_dashboard_widgets TBLWidgets 
        ";
        if (count($names) > 0) {
            $sqlSelect .= " WHERE TBLWidgets.id NOT IN ( ";
            $sqlSelect .= $this->concat_array_to_string($names);
            $sqlSelect .= ")";
        }

        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return $dataSelect;
    }

    public function update_dashboard_info($dashboard_id, $dashboard_name, $note)
    {
        $data = [
            'name' => $dashboard_name,
            'note' => $note,
        ];

        $this->db->where('id', $dashboard_id);
        $this->db->update(db_prefix() . 'perfex_dashboard_list', $data);
    }

    public function update_dashboard_widgets($dashboard_id, $dashboard_widgets)
    {
        $data = [
            'dashboard_widgets' => serialize($dashboard_widgets)
        ];

        $this->db->where('id', $dashboard_id);
        $this->db->update(db_prefix() . 'perfex_dashboard_list', $data);
    }

    public function count_dashboard_with_widget_name($widget_name)
    {
        $sqlCount = "
            SELECT 
                COUNT(*) AS TotalRows
            FROM " . db_prefix() . "perfex_dashboard_list TBLList 
            WHERE TBLList.dashboard_widgets LIKE '%\"" . $widget_name . "\"%'
        ";
        $rsCount = $this->db->query($sqlCount);
        $dataCount = $rsCount->result_array();

        $total_items = intval($dataCount[0]['TotalRows']);

        return $total_items;
    }

    public function remove_widget($widget_name)
    {
        $count_dashboard_with_widget_name = $this->count_dashboard_with_widget_name($widget_name);
        if ($count_dashboard_with_widget_name > 0) {
            return false;
        }

        $sqlDeleteWidgets = "
            DELETE FROM " . db_prefix() . "perfex_dashboard_widgets WHERE widget_name = '" . $widget_name . "'
        ";

        $this->db->query($sqlDeleteWidgets);

        return true;
    }

    public function update_widget($old_widget_name, $new_name, $new_category, $new_widget_name, $new_note)
    {
        $data = [
            'name' => $new_name,
            'category' => $new_category,
            'widget_name' => $new_widget_name,
            'note' => $new_note,
        ];

        $this->db->where('widget_name', $old_widget_name);
        $this->db->update(db_prefix() . 'perfex_dashboard_widgets', $data);
    }

    public function add_widget($new_name, $new_category, $new_widget_name, $new_note)
    {
        $data = [
            'name' => $new_name,
            'category' => $new_category,
            'widget_name' => $new_widget_name,
            'note' => $new_note,
            'default_container' => 'top-12',
        ];

        $this->db->insert(db_prefix() . 'perfex_dashboard_widgets', $data);
    }

    public function create_widget($name, $note, $category, $widget_name)
    {
        $data = [
            'name' => $name,
            'note' => $note,
            'category' => $category,
            'widget_name' => $widget_name,
            'default_container' => 'top-12',
        ];

        $this->db->insert(db_prefix() . 'perfex_dashboard_widgets', $data);
        $widget_id = $this->db->insert_id();

        return $widget_id;
    }

    public function update_widget_by_id($id, $name, $note, $category, $widget_name)
    {
        $data = [
            'name' => $name,
            'note' => $note,
            'category' => $category,
            'widget_name' => $widget_name,
        ];

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'perfex_dashboard_widgets', $data);
    }

    public function remove_widget_by_id($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'perfex_dashboard_widgets');
    }

    public function get_categories()
    {
        $sqlSelect = "
            SELECT 
                TBLCategories.*
            FROM " . db_prefix() . "perfex_dashboard_widget_categories TBLCategories ORDER BY TBLCategories.created_at DESC 
        ";

        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return $dataSelect;
    }

    public function store_category($name, $note)
    {
        $data = [
            'name' => $name,
            'note' => $note,
        ];

        $this->db->insert(db_prefix() . 'perfex_dashboard_widget_categories', $data);
        $category_id = $this->db->insert_id();

        return $category_id;
    }

    public function select_categories_by_ids($ids = [])
    {
        if (count($ids) == 0) {
            return [];
        }

        $concated_ids = $this->concat_array_to_string($ids);

        $sqlSelect = "
            SELECT 
                TBLCategories.*
            FROM " . db_prefix() . "perfex_dashboard_widget_categories TBLCategories 
            WHERE TBLCategories.id IN ( 
        ";
        $sqlSelect .= $concated_ids;
        $sqlSelect .= ") ";
        $sqlSelect .= " ORDER BY FIELD(id, " . $concated_ids . " ) ASC ";
        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return $dataSelect;
    }

    public function update_category_by_id($id, $name, $note)
    {
        $data = [
            'name' => $name,
            'note' => $note,
        ];

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'perfex_dashboard_widget_categories', $data);
    }

    public function destroy_category_by_id($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'perfex_dashboard_widget_categories');
    }

    public function get_dashboard_staff($dashboard_id)
    {
        $sqlSelect = "
            SELECT 
                TBLUsers.* 
            FROM " . db_prefix() . "perfex_dashboard_users TBLUsers 
            WHERE TBLUsers.dashboard_id = " . $dashboard_id . "
        ";

        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return $dataSelect;
    }

    public function update_dashboard_staff($dashboard_id, $dashboard_staff)
    {
        $sqlDelete = "
            DELETE FROM " . db_prefix() . "perfex_dashboard_users WHERE dashboard_id = " . $dashboard_id . "
        ";
        $this->db->query($sqlDelete);

        if (count($dashboard_staff) > 0) {
            $sqlInsert = "
                INSERT INTO " . db_prefix() . "perfex_dashboard_users (user_id, dashboard_id) VALUES 
            ";
            $countInsert = 0;
            foreach ($dashboard_staff as $member) {
                $countInsert++;
                if ($countInsert != 1) {
                    $sqlInsert .= " , ";
                }
                $sqlInsert .= "
                    ( " . $member . ", " . $dashboard_id . " )
                ";
            }
            $this->db->query($sqlInsert);
        }
    }

    public function get_dashboards_by_user($user_id)
    {
        $sqlSelect = "
            SELECT 
                TBLList.* 
            FROM " . db_prefix() . "perfex_dashboard_users TBLUsers 
                INNER JOIN " . db_prefix() . "perfex_dashboard_list TBLList 
                    ON TBLUsers.dashboard_id = TBLList.id
            WHERE TBLUsers.user_id = " . $user_id . " 
        ";

        $rsSelect = $this->db->query($sqlSelect);
        $dataSelect = $rsSelect->result_array();

        return $dataSelect;
    }

    public function get_calendar_data($start, $end, $client_id, $contact_id, $filters)
    {
        $start      = $this->db->escape_str($start);
        $end        = $this->db->escape_str($end);
        $client_id  = $this->db->escape_str($client_id);
        $contact_id = $this->db->escape_str($contact_id);

        $is_admin                     = is_admin();
        $has_permission_tasks_view    = has_permission('tasks', '', 'view');
        $has_permission_projects_view = has_permission('projects', '', 'view');
        $has_permission_invoices      = has_permission('invoices', '', 'view');
        $has_permission_invoices_own  = has_permission('invoices', '', 'view_own');
        $has_permission_estimates     = has_permission('estimates', '', 'view');
        $has_permission_estimates_own = has_permission('estimates', '', 'view_own');
        $has_permission_contracts     = has_permission('contracts', '', 'view');
        $has_permission_contracts_own = has_permission('contracts', '', 'view_own');
        $has_permission_proposals     = has_permission('proposals', '', 'view');
        $has_permission_proposals_own = has_permission('proposals', '', 'view_own');
        $data                         = [];

        $client_data = false;
        if (is_numeric($client_id) && is_numeric($contact_id)) {
            $client_data                      = true;
            $has_contact_permission_invoices  = has_contact_permission('invoices', $contact_id);
            $has_contact_permission_estimates = has_contact_permission('estimates', $contact_id);
            $has_contact_permission_proposals = has_contact_permission('proposals', $contact_id);
            $has_contact_permission_contracts = has_contact_permission('contracts', $contact_id);
            $has_contact_permission_projects  = has_contact_permission('projects', $contact_id);
        }

        $hook = [
            'client_data' => $client_data,
        ];
        if ($client_data == true) {
            $hook['client_id']  = $client_id;
            $hook['contact_id'] = $contact_id;
        }

        $data = hooks()->apply_filters('before_fetch_events', $data, $hook);

        $ff = false;
        if ($filters) {
            // excluded calendar_filters from post
            $ff = (count($filters) > 1 && isset($filters['calendar_filters']) ? true : false);
        }

        if (get_option('show_invoices_on_calendar') == 1 && !$ff || $ff && array_key_exists('invoices', $filters)) {
            $noPermissionsQuery = get_invoices_where_sql_for_staff(get_staff_user_id());

            $this->db->select('duedate as date,number,id,clientid,hash,' . get_sql_select_client_company());
            $this->db->from(db_prefix() . 'invoices');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'invoices.clientid', 'left');
            $this->db->where_not_in('status', [
                2,
                5,
            ]);

            $this->db->where('(duedate BETWEEN "' . $start . '" AND "' . $end . '")');

            if ($client_data) {
                $this->db->where('clientid', $client_id);

                if (get_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                    $this->db->where('status !=', 6);
                }
            } else {
                if (!$has_permission_invoices) {
                    $this->db->where($noPermissionsQuery);
                }
            }
            $invoices = $this->db->get()->result_array();
            foreach ($invoices as $invoice) {
                if (($client_data && !$has_contact_permission_invoices) || (!$client_data && !user_can_view_invoice($invoice['id']))) {
                    continue;
                }

                $rel_showcase = '';

                /**
                 * Show company name on calendar tooltip for admins
                 */
                if (!$client_data) {
                    $rel_showcase = ' (' . $invoice['company'] . ')';
                }

                $number = format_invoice_number($invoice['id']);

                $invoice['_tooltip'] = _l('calendar_invoice') . ' - ' . $number . $rel_showcase;
                $invoice['title']    = $number;
                $invoice['color']    = get_option('calendar_invoice_color');

                if (!$client_data) {
                    $invoice['url'] = admin_url('invoices/list_invoices/' . $invoice['id']);
                } else {
                    $invoice['url'] = site_url('invoice/' . $invoice['id'] . '/' . $invoice['hash']);
                }

                array_push($data, $invoice);
            }
        }
        if (get_option('show_estimates_on_calendar') == 1 && !$ff || $ff && array_key_exists('estimates', $filters)) {
            $noPermissionsQuery = get_estimates_where_sql_for_staff(get_staff_user_id());

            $this->db->select('number,id,clientid,hash,CASE WHEN expirydate IS NULL THEN date ELSE expirydate END as date,' . get_sql_select_client_company(), false);
            $this->db->from(db_prefix() . 'estimates');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'estimates.clientid', 'left');
            $this->db->where('status !=', 3, false);
            $this->db->where('status !=', 4, false);
            // $this->db->where('expirydate IS NOT NULL');

            $this->db->where("CASE WHEN expirydate IS NULL THEN (date BETWEEN '$start' AND '$end') ELSE (expirydate BETWEEN '$start' AND '$end') END", null, false);

            if ($client_data) {
                $this->db->where('clientid', $client_id, false);

                if (get_option('exclude_estimate_from_client_area_with_draft_status') == 1) {
                    $this->db->where('status !=', 1, false);
                }
            } else {
                if (!$has_permission_estimates) {
                    $this->db->where($noPermissionsQuery);
                }
            }

            $estimates = $this->db->get()->result_array();

            foreach ($estimates as $estimate) {
                if (($client_data && !$has_contact_permission_estimates) || (!$client_data && !user_can_view_estimate($estimate['id']))) {
                    continue;
                }

                $rel_showcase = '';
                if (!$client_data) {
                    $rel_showcase = ' (' . $estimate['company'] . ')';
                }

                $number               = format_estimate_number($estimate['id']);
                $estimate['_tooltip'] = _l('calendar_estimate') . ' - ' . $number . $rel_showcase;
                $estimate['title']    = $number;
                $estimate['color']    = get_option('calendar_estimate_color');
                if (!$client_data) {
                    $estimate['url'] = admin_url('estimates/list_estimates/' . $estimate['id']);
                } else {
                    $estimate['url'] = site_url('estimate/' . $estimate['id'] . '/' . $estimate['hash']);
                }
                array_push($data, $estimate);
            }
        }
        if (get_option('show_proposals_on_calendar') == 1 && !$ff || $ff && array_key_exists('proposals', $filters)) {
            $noPermissionsQuery = get_proposals_sql_where_staff(get_staff_user_id());

            $this->db->select('subject,id,hash,CASE WHEN open_till IS NULL THEN date ELSE open_till END as date', false);
            $this->db->from(db_prefix() . 'proposals');
            $this->db->where('status !=', 2, false);
            $this->db->where('status !=', 3, false);


            $this->db->where("CASE WHEN open_till IS NULL THEN (date BETWEEN '$start' AND '$end') ELSE (open_till BETWEEN '$start' AND '$end') END", null, false);

            if ($client_data) {
                $this->db->where('rel_type', 'customer');
                $this->db->where('rel_id', $client_id, false);

                if (get_option('exclude_proposal_from_client_area_with_draft_status')) {
                    $this->db->where('status !=', 6, false);
                }
            } else {
                if (!$has_permission_proposals) {
                    $this->db->where($noPermissionsQuery);
                }
            }

            $proposals = $this->db->get()->result_array();
            foreach ($proposals as $proposal) {
                if (($client_data && !$has_contact_permission_proposals) || (!$client_data && !user_can_view_proposal($proposal['id']))) {
                    continue;
                }

                $proposal['_tooltip'] = _l('proposal');
                $proposal['title']    = $proposal['subject'];
                $proposal['color']    = get_option('calendar_proposal_color');
                if (!$client_data) {
                    $proposal['url'] = admin_url('proposals/list_proposals/' . $proposal['id']);
                } else {
                    $proposal['url'] = site_url('proposal/' . $proposal['id'] . '/' . $proposal['hash']);
                }
                array_push($data, $proposal);
            }
        }

        if (get_option('show_tasks_on_calendar') == 1 && !$ff || $ff && array_key_exists('tasks', $filters)) {
            if ($client_data && !$has_contact_permission_projects) { } else {
                $this->db->select(db_prefix() . 'tasks.name as title,id,' . tasks_rel_name_select_query() . ' as rel_name,rel_id,status,milestone,CASE WHEN duedate IS NULL THEN startdate ELSE duedate END as date', false);
                $this->db->from(db_prefix() . 'tasks');
                $this->db->where('status !=', 5);

                $this->db->where("CASE WHEN duedate IS NULL THEN (startdate BETWEEN '$start' AND '$end') ELSE (duedate BETWEEN '$start' AND '$end') END", null, false);

                if ($client_data) {
                    $this->db->where('rel_type', 'project');
                    $this->db->where('rel_id IN (SELECT id FROM ' . db_prefix() . 'projects WHERE clientid=' . $client_id . ')');
                    $this->db->where('rel_id IN (SELECT project_id FROM ' . db_prefix() . 'project_settings WHERE name="view_tasks" AND value=1)');
                    $this->db->where('visible_to_client', 1);
                }

                if ((!$has_permission_tasks_view || get_option('calendar_only_assigned_tasks') == '1') && !$client_data) {
                    $this->db->where('(id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . '))');
                }

                $tasks = $this->db->get()->result_array();

                foreach ($tasks as $task) {
                    $rel_showcase = '';

                    if (!empty($task['rel_id']) && !$client_data) {
                        $rel_showcase = ' (' . $task['rel_name'] . ')';
                    }

                    $task['date'] = $task['date'];

                    $name             = mb_substr($task['title'], 0, 60) . '...';
                    $task['_tooltip'] = _l('calendar_task') . ' - ' . $name . $rel_showcase;
                    $task['title']    = $name;
                    $status           = get_task_status_by_id($task['status']);
                    $task['color']    = $status['color'];

                    if (!$client_data) {
                        $task['onclick'] = 'init_task_modal(' . $task['id'] . '); return false';
                        $task['url']     = '#';
                    } else {
                        $task['url'] = site_url('clients/project/' . $task['rel_id'] . '?group=project_tasks&taskid=' . $task['id']);
                    }

                    $task['className'] = $task['milestone'] ? ['milestone-' . $task['milestone']] : '';

                    array_push($data, $task);
                }
            }
        }

        if (!$client_data) {
            $available_reminders   = $this->app->get_available_reminders_keys();
            $hideNotifiedReminders = get_option('hide_notified_reminders_from_calendar');
            foreach ($available_reminders as $key) {
                if (get_option('show_' . $key . '_reminders_on_calendar') == 1 && !$ff || $ff && array_key_exists($key . '_reminders', $filters)) {
                    $this->db->select('date,description,firstname,lastname,creator,staff,rel_id')
                        ->from(db_prefix() . 'reminders')
                        ->where('(date BETWEEN "' . $start . '" AND "' . $end . '")')
                        ->where('rel_type', $key)
                        ->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'reminders.staff');
                    if ($hideNotifiedReminders == '1') {
                        $this->db->where('isnotified', 0);
                    }
                    $reminders = $this->db->get()->result_array();
                    foreach ($reminders as $reminder) {
                        if ((get_staff_user_id() == $reminder['creator'] || get_staff_user_id() == $reminder['staff']) || $is_admin) {
                            $_reminder['title'] = '';

                            if (get_staff_user_id() != $reminder['staff']) {
                                $_reminder['title'] .= '(' . $reminder['firstname'] . ' ' . $reminder['lastname'] . ') ';
                            }

                            $name = mb_substr($reminder['description'], 0, 60) . '...';

                            $_reminder['_tooltip'] = _l('calendar_' . $key . '_reminder') . ' - ' . $name;
                            $_reminder['title'] .= $name;
                            $_reminder['date']  = $reminder['date'];
                            $_reminder['color'] = get_option('calendar_reminder_color');

                            if ($key == 'customer') {
                                $url = admin_url('clients/client/' . $reminder['rel_id']);
                            } elseif ($key == 'invoice') {
                                $url = admin_url('invoices/list_invoices/' . $reminder['rel_id']);
                            } elseif ($key == 'estimate') {
                                $url = admin_url('estimates/list_estimates/' . $reminder['rel_id']);
                            } elseif ($key == 'lead') {
                                $url                  = '#';
                                $_reminder['onclick'] = 'init_lead(' . $reminder['rel_id'] . '); return false;';
                            } elseif ($key == 'proposal') {
                                $url = admin_url('proposals/list_proposals/' . $reminder['rel_id']);
                            } elseif ($key == 'expense') {
                                $url = admin_url('expenses/list_expenses/' . $reminder['rel_id']);
                            } elseif ($key == 'credit_note') {
                                $url = admin_url('credit_notes/list_credit_notes/' . $reminder['rel_id']);
                            } elseif ($key == 'ticket') {
                                $url = admin_url('tickets/ticket/' . $reminder['rel_id']);
                            } elseif ($key == 'task') {
                                $url                  = '#';
                                $_reminder['onclick'] = 'init_task_modal(' . $reminder['rel_id'] . '); return false;';
                            }

                            $_reminder['url'] = $url;
                            array_push($data, $_reminder);
                        }
                    }
                }
            }
        }

        if (get_option('show_contracts_on_calendar') == 1 && !$ff || $ff && array_key_exists('contracts', $filters)) {
            $this->db->select('hash, subject as title, dateend, datestart, id, client, content, ' . get_sql_select_client_company());
            $this->db->from(db_prefix() . 'contracts');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'contracts.client');
            $this->db->where('trash', 0);

            if ($client_data) {
                $this->db->where('client', $client_id);
                $this->db->where('not_visible_to_client', 0);
            } else {
                if (!$has_permission_contracts) {
                    $this->db->where(db_prefix() . 'contracts.addedfrom', get_staff_user_id());
                }
            }

            $this->db->where('(dateend > "' . date('Y-m-d') . '" AND dateend IS NOT NULL AND dateend BETWEEN "' . $start . '" AND "' . $end . '" OR datestart >"' . date('Y-m-d') . '")');

            $contracts = $this->db->get()->result_array();

            foreach ($contracts as $contract) {
                if (!$has_permission_contracts && !$has_permission_contracts_own && !$client_data) {
                    continue;
                } elseif ($client_data && !$has_contact_permission_contracts) {
                    continue;
                }

                $rel_showcase = '';
                if (!$client_data) {
                    $rel_showcase = ' (' . $contract['company'] . ')';
                }

                $name                  = $contract['title'];
                $_contract['title']    = $name;
                $_contract['color']    = get_option('calendar_contract_color');
                $_contract['_tooltip'] = _l('calendar_contract') . ' - ' . $name . $rel_showcase;
                if (!$client_data) {
                    $_contract['url'] = admin_url('contracts/contract/' . $contract['id']);
                } else {
                    $_contract['url'] = site_url('contract/' . $contract['id'] . '/' . $contract['hash']);
                }
                if (!empty($contract['dateend'])) {
                    $_contract['date'] = $contract['dateend'];
                } else {
                    $_contract['date'] = $contract['datestart'];
                }
                array_push($data, $_contract);
            }
        }
        //calendar_project
        if (get_option('show_projects_on_calendar') == 1 && !$ff || $ff && array_key_exists('projects', $filters)) {
            $this->load->model('projects_model');
            $this->db->select('name as title,id,clientid, CASE WHEN deadline IS NULL THEN start_date ELSE deadline END as date,' . get_sql_select_client_company(), false);

            $this->db->from(db_prefix() . 'projects');

            // Exclude cancelled and finished
            $this->db->where('status !=', 4);
            $this->db->where('status !=', 5);
            $this->db->where("CASE WHEN deadline IS NULL THEN (start_date BETWEEN '$start' AND '$end') ELSE (deadline BETWEEN '$start' AND '$end') END", null, false);

            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'projects.clientid');

            if (!$client_data && !$has_permission_projects_view) {
                $this->db->where('id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
            } elseif ($client_data) {
                $this->db->where('clientid', $client_id);
            }

            $projects = $this->db->get()->result_array();
            foreach ($projects as $project) {
                $rel_showcase = '';

                if (!$client_data) {
                    $rel_showcase = ' (' . $project['company'] . ')';
                } else {
                    if (!$has_contact_permission_projects) {
                        continue;
                    }
                }

                $name                 = $project['title'];
                $_project['title']    = $name;
                $_project['color']    = get_option('calendar_project_color');
                $_project['_tooltip'] = _l('calendar_project') . ' - ' . $name . $rel_showcase;
                if (!$client_data) {
                    $_project['url'] = admin_url('projects/view/' . $project['id']);
                } else {
                    $_project['url'] = site_url('clients/project/' . $project['id']);
                }

                $_project['date'] = $project['date'];

                array_push($data, $_project);
            }
        }
        if (!$client_data && !$ff || (!$client_data && $ff && array_key_exists('events', $filters))) {
            $events = $this->get_all_events($start, $end);
            foreach ($events as $event) {
                if ($event['userid'] != get_staff_user_id() && !$is_admin) {
                    $event['is_not_creator'] = true;
                    $event['onclick']        = true;
                }
                $event['_tooltip'] = _l('calendar_event') . ' - ' . $event['title'];
                $event['color']    = $event['color'];
                array_push($data, $event);
            }
        }

        return hooks()->apply_filters('calendar_data', $data, [
            'start'      => $start,
            'end'        => $end,
            'client_id'  => $client_id,
            'contact_id' => $contact_id,
        ]);
    }

    public function get_all_events($start, $end)
    {
        $is_staff_member = is_staff_member();
        $this->db->select('title,start,end,eventid,userid,color,public');
        // Check if is passed start and end date
        $this->db->where('(start BETWEEN "' . $start . '" AND "' . $end . '")');
        $this->db->where('userid', get_staff_user_id());
        if ($is_staff_member) {
            $this->db->or_where('public', 1);
        }

        return $this->db->get(db_prefix() . 'events')->result_array();
    }

    protected function concat_array_to_string($items)
    {
        $concated = '';

        foreach ($items as $position => $item) {
            if ($position != 0) {
                $concated .= " , ";
            }

            $concated .= " '" . $item . "' ";
        }

        return $concated;
    }
}
