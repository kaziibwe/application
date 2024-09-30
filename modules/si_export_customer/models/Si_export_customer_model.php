<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_export_customer_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	*Get KYC files of client
	*/
	public function get_customer_files($id, $file_ids = [])
	{
		if(!empty($file_ids))
			$this->db->where_in('id',$file_ids);
		$this->db->where('rel_id', $id);
		$this->db->where('rel_type', 'customer');

		return $this->db->get(db_prefix() . 'files')->result_array();
	}
	/**
	* Update kyc files of client
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function update_client_kyc($data,$client_id)
	{
		if (is_numeric($client_id)) {
			$this->db->where('client_id',$client_id);
			$result = $this->db->get(db_prefix() . 'si_export_customer_kyc_files');
			if($result->num_rows()>0)
			{
				$this->db->where('client_id',$client_id);
				$update = $this->db->update(db_prefix() . 'si_export_customer_kyc_files', $data);
				if ($update) {
					return true;
				}
				return false;
			}
			else
			{
				$data['client_id']=$client_id;
				$insert = $this->db->insert(db_prefix() . 'si_export_customer_kyc_files', $data);
				if ($insert) {
					return true;
				}
				return false;
			}
		}
	}
	/**
	* Update items/services of client
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function update_client_services($data,$client_id)
	{
		if (is_numeric($client_id)) {
			$this->db->where('client_id',$client_id);
			$this->db->delete(db_prefix() . 'si_export_customer_services');
			$insert_data = array();
			if(!empty($data))
			{
				foreach($data as $item_id)
					$insert_data[] = array('client_id'=>$client_id,'item_id'=>$item_id);
				$this->db->insert_batch(db_prefix() . 'si_export_customer_services', $insert_data);
			}
		}
	}
	
	/**
	* Get invoice total from all statuses
	* @since  Version 1.0.2
	* @param  mixed $data $_POST data
	* @return array
	*/
	public function get_invoices_total($data)
	{
		$this->load->model('currencies_model');
		if (isset($data['currency'])) {
			$currencyid = $data['currency'];
		} elseif(isset($data['customer_id']) && $data['customer_id'] != ''){
			$currencyid = $this->clients_model->get_customer_default_currency($data['customer_id']);
			if ($currencyid == 0) {
				$currencyid = $this->currencies_model->get_base_currency()->id;
			}
		}elseif(isset($data['project_id']) && $data['project_id'] != ''){
			$this->load->model('projects_model');
			$currencyid = $this->projects_model->get_currency($data['project_id'])->id;
		}else{
			$currencyid = $this->currencies_model->get_base_currency()->id;
		}

		$result				= [];
		$result['due']		= [];
		$result['paid']		= [];
		$result['overdue']	= [];

		$has_permission_view                = has_permission('invoices', '', 'view');
		$has_permission_view_own            = has_permission('invoices', '', 'view_own');
		$allow_staff_view_invoices_assigned = get_option('allow_staff_view_invoices_assigned');
		$noPermissionsQuery                 = get_invoices_where_sql_for_staff(get_staff_user_id());

		for ($i = 1; $i <= 3; $i++) {
			$select = 'id,total';
			if ($i == 1) {
				$select .= ', (SELECT total - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid = ' . db_prefix() . 'invoices.id) - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id)) as outstanding';
			} elseif ($i == 2) {
				$select .= ',(SELECT SUM(amount) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid=' . db_prefix() . 'invoices.id) as total_paid';
			}
			$this->db->select($select);
			$this->db->from(db_prefix() . 'invoices');
			$this->db->where('currency', $currencyid);
			// Exclude cancelled invoices
			$this->db->where('status !=', Invoices_model::STATUS_CANCELLED);
			// Exclude draft
			$this->db->where('status !=', Invoices_model::STATUS_DRAFT);

			if (isset($data['project_id']) && $data['project_id'] != '') {
				$this->db->where('project_id', $data['project_id']);
			}elseif(isset($data['customer_id']) && $data['customer_id'] != '')	{
				$this->db->where('clientid', $data['customer_id']);
			}

			if ($i == 3) {
				$this->db->where('status', Invoices_model::STATUS_OVERDUE);
			}elseif ($i == 1)	{
				$this->db->where('status !=', Invoices_model::STATUS_PAID);
            }

			if (isset($data['from']) && isset($data['to']))	{
				$this->db->where('date BETWEEN "'. $data['from']. '" and "'. $data['to'].'"');
			}else {
				$this->db->where('YEAR(date)', date('Y'));
			}

			if (!$has_permission_view)	{
				$whereUser = $noPermissionsQuery;
				$this->db->where('(' . $whereUser . ')');
			}

			$invoices = $this->db->get()->result_array();

			foreach ($invoices as $invoice)	{
				if($i == 1)	{
					$result['due'][] = $invoice['outstanding'];
				}elseif($i == 2)	{
					$result['paid'][] = $invoice['total_paid'];
				}elseif($i == 3)	{
					$result['overdue'][] = $invoice['total'];
				}
			}
		}
		$currency             = get_currency($currencyid);
		$result['due']        = array_sum($result['due']);
		$result['paid']       = array_sum($result['paid']);
		$result['overdue']    = array_sum($result['overdue']);
		$result['currency']   = $currency;
		$result['currencyid'] = $currencyid;

		return $result;
	}
	
	/**
	* Performs estimates totals status
	* @param  array $data
	* @return array
	*/
	public function get_estimates_total($data)
	{
		$this->load->model('estimates_model');
		$statuses            = $this->estimates_model->get_statuses();
		$has_permission_view = has_permission('estimates', '', 'view');
		$this->load->model('currencies_model');
		if(isset($data['currency']))	{
			$currencyid = $data['currency'];
		}elseif(isset($data['customer_id']) && $data['customer_id'] != '')	{
			$currencyid = $this->clients_model->get_customer_default_currency($data['customer_id']);
			if ($currencyid == 0) {
				$currencyid = $this->currencies_model->get_base_currency()->id;
			}
		} elseif (isset($data['project_id']) && $data['project_id'] != '') {
			$this->load->model('projects_model');
			$currencyid = $this->projects_model->get_currency($data['project_id'])->id;
		} else {
			$currencyid = $this->currencies_model->get_base_currency()->id;
		}

		$currency = get_currency($currencyid);
		$where  = '';
		if(isset($data['customer_id']) && $data['customer_id'] != '')	{
			$where = ' AND clientid=' . $data['customer_id'];
		}

		if(isset($data['project_id']) && $data['project_id'] != '')	{
			$where .= ' AND project_id=' . $data['project_id'];
		}

		if(!$has_permission_view)	{
			$where .= ' AND ' . get_estimates_where_sql_for_staff(get_staff_user_id());
		}

		$sql = 'SELECT';
		foreach ($statuses as $estimate_status) {
			$sql .= '(SELECT SUM(total) FROM ' . db_prefix() . 'estimates WHERE status=' . $estimate_status;
			$sql .= ' AND currency =' . $currencyid;
			
			if (isset($data['from']) && isset($data['to'])) {
				$sql .= ' AND date BETWEEN "'. $data['from']. '" and "'. $data['to'].'"';
			} else {
				$sql .= ' AND YEAR(date) = ' . date('Y');
			}
			$sql .= $where;
			$sql .= ') as "' . $estimate_status . '",';
		}

		$sql     = substr($sql, 0, -1);
		$result  = $this->db->query($sql)->result_array();
		$_result = [];
		$i       = 1;
		foreach ($result as $key => $val) {
			foreach ($val as $status => $total) {
				$_result[$i]['total']  = $total;
				$_result[$i]['symbol'] = $currency->symbol;
				$_result[$i]['currency_name'] = $currency->name;
				$_result[$i]['status'] = $status;
				$i++;
			}
		}
		$_result['currencyid'] = $currencyid;

		return $_result;
	}
	
	/** get expenses total
	*/
	public function get_expenses_total($data)
	{
		$this->load->model('currencies_model');
		$base_currency     = $this->currencies_model->get_base_currency()->id;
		$base              = true;
		$currency_switcher = false;
		if (isset($data['currency'])) {
			$currencyid        = $data['currency'];
			$currency_switcher = true;
		} elseif (isset($data['customer_id']) && $data['customer_id'] != '') {
			$currencyid = $this->clients_model->get_customer_default_currency($data['customer_id']);
			if ($currencyid == 0) {
				$currencyid = $base_currency;
			} else {
				if (total_rows(db_prefix() . 'expenses', [
					'currency' => $base_currency,
					'clientid' => $data['customer_id'],
				])) {
					$currency_switcher = true;
				}
			}
		} elseif (isset($data['project_id']) && $data['project_id'] != '') {
			$this->load->model('projects_model');
			$currencyid = $this->projects_model->get_currency($data['project_id'])->id;
		} else {
			$currencyid = $base_currency;
			if (total_rows(db_prefix() . 'expenses', [
				'currency !=' => $base_currency,
			])) {
				$currency_switcher = true;
			}
		}

		$currency = get_currency($currencyid);

		$has_permission_view	=	has_permission('expenses', '', 'view');
		$_result				=	[];

		for ($i = 1; $i <= 5; $i++) {
			$this->db->select('amount,tax,tax2,invoiceid');
			$this->db->where('currency', $currencyid);

			if (isset($data['from']) && isset($data['to'])) {
				$this->db->where('date BETWEEN "'. $data['from']. '" and "'. $data['to'].'"');
			} else {
				$this->db->where('YEAR(date) = ', date('Y'));
			}
			if (isset($data['customer_id']) && $data['customer_id'] != '') {
				$this->db->where('clientid', $data['customer_id']);
			}
			if (isset($data['project_id']) && $data['project_id'] != '') {
				$this->db->where('project_id', $data['project_id']);
			}

			if (!$has_permission_view) {
				$this->db->where('addedfrom', get_staff_user_id());
			}
			switch ($i) {
				case 1:
					$key = 'all';

					break;
				case 2:
					$key = 'billable';
					$this->db->where('billable', 1);

					break;
				case 3:
					$key = 'non_billable';
					$this->db->where('billable', 0);

					break;
				case 4:
					$key = 'billed';
					$this->db->where('billable', 1);
					$this->db->where('invoiceid IS NOT NULL');
					$this->db->where('invoiceid IN (SELECT invoiceid FROM ' . db_prefix() . 'invoices WHERE status=2 AND id=' . db_prefix() . 'expenses.invoiceid)');

					break;
				case 5:
					$key = 'unbilled';
					$this->db->where('billable', 1);
					$this->db->where('invoiceid IS NULL');

					break;
			}
			$all_expenses = $this->db->get(db_prefix() . 'expenses')->result_array();
			$_total_all   = [];
			$cached_taxes = [];
			foreach ($all_expenses as $expense) {
				$_total = $expense['amount'];
				if ($expense['tax'] != 0) {
					if (!isset($cached_taxes[$expense['tax']])) {
						$tax							=	get_tax_by_id($expense['tax']);
						$cached_taxes[$expense['tax']]	=	$tax;
					} else {
						$tax = $cached_taxes[$expense['tax']];
					}
					$_total += ($_total / 100 * $tax->taxrate);
				}
				if ($expense['tax2'] != 0) {
					if (!isset($cached_taxes[$expense['tax2']])) {
						$tax							= get_tax_by_id($expense['tax2']);
						$cached_taxes[$expense['tax2']]	= $tax;
					} else {
						$tax = $cached_taxes[$expense['tax2']];
					}
					$_total += ($expense['amount'] / 100 * $tax->taxrate);
				}
				array_push($_total_all, $_total);
			}
			$_result[$key]['total'] = app_format_money(array_sum($_total_all), $currency);
		}
		$_result['currency_switcher'] = $currency_switcher;
		$_result['currencyid']        = $currencyid;
		
		return $_result;
	}
	
	/**
	 * Return tasks summary formated data
	 * @param  string $where additional where to perform
	 * @return array
	 */
	function get_tasks_total($data)
	{
		$tasks_summary = [];
		$this->load->model('tasks_model');
		$statuses      = $this->tasks_model->get_statuses();
		foreach ($statuses as $status) {
			$tasks_where = 'status = ' . $status['id'];
			if (!has_permission('tasks', '', 'view')) {
				$tasks_where .= ' ' . get_tasks_where_string();
			}
		
			$tasks_where .= ' AND startdate BETWEEN "'. $data['from']. '" and "'. $data['to'].'"';
			if ($data['customer_id']) {
				$tasks_where .= ' AND  ((rel_id=' . $data['customer_id']. ' AND rel_type="customer")';
				$tasks_where .= ' OR (rel_id IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE clientid=' . $data['customer_id'] . ') AND rel_type="invoice")';
				$tasks_where .= ' OR (rel_id IN (SELECT id FROM ' . db_prefix() . 'estimates WHERE clientid=' . $data['customer_id'] . ') AND rel_type="estimate")';
				$tasks_where .= ' OR (rel_id IN (SELECT id FROM ' . db_prefix() . 'contracts WHERE client=' . $data['customer_id'] . ') AND rel_type="contract")';
				$tasks_where .= ' OR (rel_id IN (SELECT ticketid FROM ' . db_prefix() . 'tickets WHERE userid=' . $data['customer_id'] . ') AND rel_type="ticket")';
				$tasks_where .= ' OR (rel_id IN (SELECT id FROM ' . db_prefix() . 'expenses WHERE clientid=' . $data['customer_id'] . ') AND rel_type="expense")';
				$tasks_where .= ' OR (rel_id IN (SELECT id FROM ' . db_prefix() . 'proposals WHERE rel_id=' . $data['customer_id'] . ' AND rel_type="customer") AND rel_type="proposal")';
				$tasks_where .= ' OR (rel_id IN (SELECT id FROM ' . db_prefix() . 'projects WHERE clientid=' . $data['customer_id'] . ') AND rel_type="project"))';
				
			} 
	
			$summary                   = [];
			$summary['total_tasks']    = total_rows(db_prefix() . 'tasks', $tasks_where);
			$summary['color']          = $status['color'];
			$summary['name']           = $status['name'];
			$summary['status_id']      = $status['id'];
			$tasks_summary[]           = $summary;
		}
	
		return $tasks_summary;
	}
	
	/**
	 * Return tickets summary formated data of customer
	 * @param  string $where additional where to perform
	 * @return array
	 */
	function get_tickets_total($data)
	{
		$tickets_summary = [];
		$this->load->model('tickets_model');
		$statuses	=	$this->tickets_model->get_ticket_status();
		foreach ($statuses as $status) {
			$_where = 'status = ' . $status['ticketstatusid'];
			$_where .= ' AND date BETWEEN "'. $data['from']. '" and "'. $data['to'].'"';
			$_where .= ' AND userid='.$data['customer_id'];
			$summary					=	[];
			$summary['total_tickets']	=	total_rows(db_prefix() . 'tickets', $_where);
			$summary['color']			=	$status['statuscolor'];
			$summary['name']			=	$status['name'];
			$summary['status_id']		=	$status['ticketstatusid'];
			$tickets_summary[]			=	$summary;
		}
		return $tickets_summary;	
	}
	
	function get_customer_services_list($client_id,$group_id,$staff_id)	
	{
		if (is_numeric($client_id)) {
			$this->db->select(db_prefix() . 'items.*,'.db_prefix() .'items_groups.name as item_group_name,'.db_prefix() .'clients.company,'.db_prefix() . 'customer_groups.groupid as customer_group_id, '.db_prefix() . 'clients.userid as client_id,'.$this->get_sql_select_customer_admins_ids().' as staff_ids,'.$this->get_sql_select_customer_admins_full_names().' as staff_names,'.$this->get_sql_select_customer_group_names().'as customer_groups',false);
				
			$this->db->join(db_prefix() . 'items',db_prefix() . 'items.id='.db_prefix() . 'si_export_customer_services.item_id','left');
			$this->db->join(db_prefix() . 'items_groups',db_prefix() . 'items_groups.id='.db_prefix() . 'items.group_id','left');
			$this->db->join(db_prefix() . 'clients',db_prefix() . 'clients.userid='.db_prefix() . 'si_export_customer_services.client_id','left');
			$this->db->join(db_prefix() . 'customer_groups',db_prefix() . 'customer_groups.customer_id='.db_prefix() . 'si_export_customer_services.client_id','left');
			
			if($client_id > 0)
				$this->db->where(db_prefix() . 'si_export_customer_services.client_id',$client_id);
			if($group_id > 0)
			{
				$this->db->where(db_prefix() . 'customer_groups.groupid',$group_id);	
			}
			if($staff_id > 0)
			{
				$this->db->join(db_prefix() . 'customer_admins',db_prefix() . 'customer_admins.customer_id='.db_prefix() . 'si_export_customer_services.client_id','left');
				$this->db->where(db_prefix() . 'customer_admins.staff_id',$staff_id);	
			}
			$this->db->where(db_prefix() . 'clients.active',1);
			$this->db->group_by(db_prefix() . 'si_export_customer_services.client_id,'.db_prefix() . 'items.id');
					
			$result = $this->db->get(db_prefix() . 'si_export_customer_services');
			if($result->num_rows()>0)
			{
				return $result->result_array();
			}
		}
		return array();
	}
	
	function get_service_customers_list($item_id,$group_id,$staff_id)
	{
		if (is_numeric($item_id)) {
			$this->db->select(db_prefix() . 'items.*,'.db_prefix() .'items_groups.name as item_group_name,'.db_prefix() .'clients.company,'.db_prefix() . 'customer_groups.groupid as customer_group_id, '.db_prefix() . 'clients.userid as client_id,'.$this->get_sql_select_customer_admins_ids().' as staff_ids,'.$this->get_sql_select_customer_admins_full_names().' as staff_names,'.$this->get_sql_select_customer_group_names().'as customer_groups',false);
				
			$this->db->join(db_prefix() . 'items',db_prefix() . 'items.id='.db_prefix() . 'si_export_customer_services.item_id','left');
			$this->db->join(db_prefix() . 'items_groups',db_prefix() . 'items_groups.id='.db_prefix() . 'items.group_id','left');
			$this->db->join(db_prefix() . 'clients',db_prefix() . 'clients.userid='.db_prefix() . 'si_export_customer_services.client_id','left');
			$this->db->join(db_prefix() . 'customer_groups',db_prefix() . 'customer_groups.customer_id='.db_prefix() . 'si_export_customer_services.client_id','left');
			
			if($item_id > 0)
				$this->db->where(db_prefix() . 'items.id',$item_id);
			if($group_id > 0)
			{
				$this->db->where(db_prefix() . 'items_groups.id',$group_id);	
			}
			if($staff_id > 0)
			{
				$this->db->join(db_prefix() . 'customer_admins',db_prefix() . 'customer_admins.customer_id='.db_prefix() . 'si_export_customer_services.client_id','left');
				$this->db->where(db_prefix() . 'customer_admins.staff_id',$staff_id);	
			}
			$this->db->where(db_prefix() . 'clients.active',1);
			$this->db->group_by(db_prefix() . 'si_export_customer_services.client_id,'.db_prefix() . 'items.id');
					
			$result = $this->db->get(db_prefix() . 'si_export_customer_services');
			if($result->num_rows()>0)
			{
				return $result->result_array();
			}	
		}
		return array();
	}
	
	function get_sql_select_customer_admins_ids()
	{
		return '(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'customer_admins WHERE ' . db_prefix() . 'customer_admins.customer_id=' . db_prefix() . 'clients.userid ORDER BY ' . db_prefix() . 'customer_admins.staff_id)';
	}
	
	function get_sql_select_customer_admins_full_names()
	{
		return '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM '.db_prefix().'customer_admins JOIN '.db_prefix().'staff ON '.db_prefix().'staff.staffid = '.db_prefix().'customer_admins.staff_id WHERE ' . db_prefix() . 'customer_admins.customer_id='.db_prefix().'clients.userid ORDER BY '.db_prefix().'customer_admins.staff_id)';
	}
	
	function get_sql_select_customer_group_names()
	{
		return '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'customer_groups JOIN '.db_prefix().'customers_groups ON '.db_prefix().'customers_groups.id = '.db_prefix().'customer_groups.groupid WHERE ' . db_prefix() . 'customer_groups.customer_id='.db_prefix().'clients.userid ORDER BY '.db_prefix().'customer_groups.groupid)';
	}
}
