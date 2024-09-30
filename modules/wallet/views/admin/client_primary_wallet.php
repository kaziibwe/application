<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (!isset($client)) return; ?>
<?php
$data = array();
$contact_id = get_primary_contact_user_id($client->userid);
$data['currency'] = get_base_currency();
$data['currency_code'] = $data['currency']->name;
$data['min_funding_amount'] = (int)get_option('wallet_min_funding_amount');
$data['max_funding_amount'] = (int)get_option('wallet_max_funding_amount');
$data['contact_id'] = $contact_id;
$data['contact'] = $this->clients_model->get_contact($contact_id);
$data['transactions'] = $this->wallet->ledger($contact_id);
$data['small_variant'] = true;
$this->load->view(WALLET_MODULE_NAME . '/wallet', $data);
?>