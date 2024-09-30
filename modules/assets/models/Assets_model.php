<?php

defined('BASEPATH') or exit('No direct script access allowed');

class assets_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        if ('' == $id) {
            return  $this->db->get(db_prefix().'assets')->result_array();
        }
        $this->db->where('id', $id);

        return $this->db->get(db_prefix().'assets')->row();
    }

    public function get_clients_assign_assets($table, $where = [])
    {
        $this->db->select(db_prefix().'assets.*,'.db_prefix().'asset_unit.*,'.db_prefix().'assets_group.*,'.db_prefix().'departments.*, '.db_prefix().'departments.name as dpm_name');

        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->where('visible_to_client', 1);

        $this->db->join(db_prefix().'asset_unit', db_prefix().'asset_unit.unit_id = '.db_prefix().'assets.unit', 'LEFT');
        $this->db->join(db_prefix().'assets_group', db_prefix().'assets_group.group_id = '.db_prefix().'assets.asset_group', 'LEFT');
        $this->db->join(db_prefix().'departments', db_prefix().'departments.departmentid = '.db_prefix().'assets.department', 'LEFT');

        return $this->db->get(db_prefix().$table)->result_array();
    }

    public function get_asset_group($id = '')
    {
        if ('' == $id) {
            return  $this->db->get(db_prefix().'assets_group')->result_array();
        }
        $this->db->where('group_id', $id);

        return $this->db->get(db_prefix().'assets_group')->row();
    }

    public function get_asset_unit($id = '')
    {
        if ('' == $id) {
            return  $this->db->get(db_prefix().'asset_unit')->result_array();
        }
        $this->db->where('unit_id', $id);

        return $this->db->get(db_prefix().'asset_unit')->row();
    }

    public function get_asset_location($id = '')
    {
        if ('' == $id) {
            return  $this->db->get(db_prefix().'asset_location')->result_array();
        }
        $this->db->where('location_id', $id);

        return $this->db->get(db_prefix().'asset_location')->row();
    }

    public function add_asset_group($data)
    {
        $this->db->insert(db_prefix().'assets_group', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function update_asset_group($data, $id)
    {
        $this->db->where('group_id', $id);
        $this->db->update(db_prefix().'assets_group', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function delete_asset_group($id)
    {
        $this->db->where('group_id', $id);
        $this->db->delete(db_prefix().'assets_group');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function add_asset_unit($data)
    {
        $this->db->insert(db_prefix().'asset_unit', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function update_asset_unit($data, $id)
    {
        $this->db->where('unit_id', $id);
        $this->db->update(db_prefix().'asset_unit', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function delete_asset_unit($id)
    {
        $this->db->where('unit_id', $id);
        $this->db->delete(db_prefix().'asset_unit');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function add_asset_location($data)
    {
        $this->db->insert(db_prefix().'asset_location', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function update_asset_location($data, $id)
    {
        $this->db->where('location_id', $id);
        $this->db->update(db_prefix().'asset_location', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function delete_asset_location($id)
    {
        $this->db->where('location_id', $id);
        $this->db->delete(db_prefix().'asset_location');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function add_asset($data)
    {
        $data['unit_price'] = reformat_currency_asset($data['unit_price']);
        $data['date_buy']   = to_sql_date($data['date_buy']);
        if (isset($data['file_asset'])) {
            unset($data['file_asset']);
        }
        if (!empty($data['clientid'])) {
            $data['belongs_to'] = implode(',', $data['clientid']);
            unset($data['clientid']);
        }
        if (isset($data['visible_to_client'])) {
            $data['visible_to_client'] = 1;
        }
        $this->db->insert(db_prefix().'assets', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->db->insert(db_prefix().'inventory_history', [
                'assets'          => $insert_id,
                'date_time'       => $data['date_buy'],
                'acction'         => 'add_new',
                'inventory_begin' => 0,
                'inventory_end'   => $data['amount'],
                'cost'            => $data['unit_price'] * $data['amount'],
            ]);

            return $insert_id;
        }
    }

    public function update_asset($data, $id)
    {
        if (!empty($data['clientid'])) {
            $data['belongs_to'] = implode(',', $data['clientid']);
            unset($data['clientid']);
        }
        if (isset($data['visible_to_client'])) {
            $data['visible_to_client'] = 1;
        } else {
            $data['visible_to_client'] = 0;
        }
        if (!empty($data['unit_price'])) {
            $data['unit_price'] = reformat_currency_asset($data['unit_price']);
        }
        if (!empty($data['date_buy'])) {
            $data['date_buy']   = to_sql_date($data['date_buy']);
        }
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'assets', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function delete_assets($id)
    {
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'assets');
        $attachments = $this->db->get('tblfiles')->result_array();
        foreach ($attachments as $attachment) {
            $this->delete_assets_attachment($attachment['id']);
        }
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'assets');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function get_assets_attachments($assets, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $assets);
        }
        $this->db->where('rel_type', 'assets');
        $result = $this->db->get('tblfiles');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    public function delete_assets_attachment($id)
    {
        $attachment = $this->get_assets_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(ASSETS_UPLOAD_FOLDER.'/'.$attachment->rel_id.'/'.$attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }

            if (is_dir(ASSETS_UPLOAD_FOLDER.'/'.$attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(ASSETS_UPLOAD_FOLDER.'/'.$attachment->rel_id);
                if (0 == count($other_attachments)) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(ASSETS_UPLOAD_FOLDER.'/'.$attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    public function get_asset_file($asset)
    {
        $this->db->where('rel_id', $asset);
        $this->db->where('rel_type', 'assets');

        return $this->db->get('tblfiles')->result_array();
    }

    public function get_file($id, $rel_id = false)
    {
        $this->db->where('id', $id);
        $file = $this->db->get('tblfiles')->row();

        if ($file && $rel_id) {
            if ($file->rel_id != $rel_id) {
                return false;
            }
        }

        return $file;
    }

    public function allocation_asset($data)
    {
        $assets               = $this->get($data['assets']);
        $data['time_acction'] = to_sql_date($data['time_acction'], true);
        $insert_id            = $this->db->insert('tblassets_acction_1', $data);
        if ($insert_id) {
            $this->db->insert(db_prefix().'inventory_history', [
                'assets'          => $data['assets'],
                'date_time'       => $data['time_acction'],
                'acction'         => $data['type'],
                'inventory_begin' => $assets->amount - $assets->total_allocation,
                'inventory_end'   => $assets->amount - $assets->total_allocation - $data['amount'],
            ]);

            $this->db->where('id', $data['assets']);
            $this->db->update(db_prefix().'assets', ['total_allocation' => $assets->total_allocation + $data['amount']]);

            return $insert_id;
        }
    }

    public function get_asset_allocation_by_staff($staff, $asset)
    {
        $this->db->where('acction_to', $staff);
        $this->db->where('assets', $asset);
        $this->db->where('type', 'allocation');

        return $this->db->get(db_prefix().'assets_acction_1')->result_array();
    }

    public function get_asset_revoke_by_staff($staff, $asset)
    {
        $this->db->where('acction_to', $staff);
        $this->db->where('assets', $asset);
        $this->db->where('type', 'revoke');

        return $this->db->get(db_prefix().'assets_acction_1')->result_array();
    }

    public function get_amount_asset_broken($asset)
    {
        $this->db->where('assets', $asset);
        $this->db->where('type', 'broken');

        return $this->db->get(db_prefix().'assets_acction_2')->result_array();
    }

    public function get_amount_asset_warranty($asset)
    {
        $this->db->where('assets', $asset);
        $this->db->where('type', 'warranty');

        return $this->db->get(db_prefix().'assets_acction_2')->result_array();
    }

    public function revoke_asset($data)
    {
        $assets               = $this->get($data['assets']);
        $data['time_acction'] = to_sql_date($data['time_acction'], true);
        $insert_id            = $this->db->insert('tblassets_acction_1', $data);
        if ($insert_id) {
            $this->db->insert(db_prefix().'inventory_history', [
                'assets'          => $data['assets'],
                'date_time'       => $data['time_acction'],
                'acction'         => $data['type'],
                'inventory_begin' => $assets->amount - $assets->total_allocation,
                'inventory_end'   => $assets->amount - $assets->total_allocation + $data['amount'],
            ]);

            $this->db->where('id', $data['assets']);
            $this->db->update(db_prefix().'assets', [
                'total_allocation' => $assets->total_allocation - $data['amount'],
            ]);

            return $insert_id;
        }
    }

    public function additional_asset($data)
    {
        $assets               = $this->get($data['assets']);
        $data['acction_from'] = get_staff_user_id();
        $data['time_acction'] = to_sql_date($data['time_acction'], true);
        $data['cost']         = $assets->unit_price * $data['amount'];
        $insert_id            = $this->db->insert('tblassets_acction_2', $data);
        if ($insert_id) {
            $this->db->insert(db_prefix().'inventory_history', [
                'assets'          => $data['assets'],
                'date_time'       => $data['time_acction'],
                'acction'         => $data['type'],
                'inventory_begin' => $assets->amount - $assets->total_allocation,
                'inventory_end'   => $assets->amount - $assets->total_allocation + $data['amount'],
                'cost'            => $data['cost'],
            ]);

            $this->db->where('id', $data['assets']);
            $this->db->update(db_prefix().'assets', [
                'amount' => $assets->amount + $data['amount'],
            ]);

            return $insert_id;
        }
    }

    public function lost_asset($data)
    {
        $assets               = $this->get($data['assets']);
        $data['acction_from'] = get_staff_user_id();
        $data['time_acction'] = to_sql_date($data['time_acction'], true);
        $insert_id            = $this->db->insert('tblassets_acction_2', $data);
        $asset_id             = $data['assets'];
        
        if ($insert_id) {
            
            $this->db->insert(db_prefix().'inventory_history', [
                'assets'          => $data['assets'],
                'date_time'       => $data['time_acction'],
                'acction'         => $data['type'],
                'inventory_begin' => $assets->amount - $assets->total_allocation,
                'inventory_end'   => $assets->amount - $assets->total_allocation - $data['amount'],
            ]);

            $this->db->where('id', $asset_id);
            $this->db->update(db_prefix().'assets', [
                'amount'     => $assets->amount - $data['amount'],
                'total_lost' => $assets->total_lost + $data['amount'],
            ]);

            return $insert_id;
        }
    }

    public function broken_asset ($data) {

        $assets               = $this->get($data['assets']);
        $data['acction_from'] = get_staff_user_id();
        $data['time_acction'] = to_sql_date($data['time_acction'], true);
        $insert_id            = $this->db->insert('tblassets_acction_2', $data);
        $asset_id             = $data['assets'];

        if ($insert_id) {

            $this->db->insert(db_prefix().'inventory_history', [
                'assets'          => $data['assets'],
                'date_time'       => $data['time_acction'],
                'acction'         => $data['type'],
                'inventory_begin' => $assets->amount - $assets->total_allocation,
                'inventory_end'   => $assets->amount - $assets->total_allocation,
            ]);

            $this->db->where('id', $asset_id);
            $this->db->update(db_prefix().'assets', [
                'total_damages' => $assets->total_damages + $data['amount'],
            ]);

            return $insert_id;

        }

    }

    public function liquidation_asset($data)
    {
        $assets               = $this->get($data['assets']);
        $data['cost']         = reformat_currency_asset($data['cost']);
        $data['acction_from'] = get_staff_user_id();
        $data['time_acction'] = to_sql_date($data['time_acction'], true);
        $insert_id            = $this->db->insert('tblassets_acction_2', $data);
        $asset_id             = $data['assets'];
        
        if ($insert_id) {

            $this->db->insert(db_prefix().'inventory_history', [
                'assets'          => $data['assets'],
                'date_time'       => $data['time_acction'],
                'acction'         => $data['type'],
                'inventory_begin' => $assets->amount - $assets->total_allocation,
                'inventory_end'   => $assets->amount - $assets->total_allocation - $data['amount'],
                'cost'            => $data['cost'],
            ]);

            $this->db->where('id', $asset_id);
            $this->db->update(db_prefix().'assets', [
                'amount'            => $assets->amount - $data['amount'],
                'total_liquidation' => $assets->total_liquidation + $data['amount'],
            ]);

            return $insert_id;
        }
    }

    public function warranty_asset($data)
    {
        $assets               = $this->get($data['assets']);
        $data['cost']         = reformat_currency_asset($data['cost']);
        $data['acction_from'] = get_staff_user_id();
        $data['time_acction'] = to_sql_date($data['time_acction'], true);
        $insert_id            = $this->db->insert('tblassets_acction_2', $data);
        $asset_id             = $data['assets'];

        if ($insert_id) {

            $this->db->insert(db_prefix().'inventory_history', [
                'assets'          => $data['assets'],
                'date_time'       => $data['time_acction'],
                'acction'         => $data['type'],
                'inventory_begin' => $assets->amount - $assets->total_allocation,
                'inventory_end'   => $assets->amount - $assets->total_allocation,
                'cost'            => $data['cost'],
            ]);

            $this->db->where('id', $asset_id);
            $this->db->update(db_prefix().'assets', [
                'total_warranty' => $assets->total_liquidation + $data['amount'],
                'total_damages'  => $assets->total_damages - $data['amount'],
            ]);

            return $insert_id;
        }
    }

    public function get_assets($id = '')
    {
        if ('' != $id) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix().'assets')->row();
        }

        return $this->db->get(db_prefix().'assets')->result_array();
    }
}
