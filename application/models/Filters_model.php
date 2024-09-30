<?php

use app\services\utilities\Arr;

defined("BASEPATH") or exit("No direct script access allowed");

class Filters_model extends App_Model
{
    public function create($data)
    {
        $isDefault = Arr::pull($data, 'is_default');
        $view = Arr::pull($data, 'view');

        $data["builder"] = json_encode($data["builder"]);
        $this->db->insert("filters", $data);

        $filterId = $this->db->insert_id();

        if ($isDefault === true) {
            $this->delete_default($data['identifier'], $view, $data['staff_id']);
            $this->mark_as_default($filterId, $data['identifier'], $view, $data['staff_id']);
        }

        return $this->find($filterId, $view, $data['staff_id']);
    }

    public function find($id, $view, $staffId)
    {
        $filter = $this->db->where("id", $id)->get("filters")->row_array();

        $filter["builder"] = json_decode($filter["builder"], true);

        return $this->merge_defaults([$filter], $view, $staffId)[0];
    }

    public function update($id, $data, $staffId)
    {
        $filter = $this->db->select(['identifier', 'staff_id'])->where('id', $id)->get('filters')->row_array();
        $isDefault = Arr::pull($data, 'is_default');
        $view = Arr::pull($data, 'view');
        
        if ($isDefault === true) {
            $this->delete_default($filter['identifier'], $view, $filter['staff_id']);
            $this->mark_as_default($id, $filter['identifier'], $view, $filter['staff_id']);
        } else if ($isDefault === false && $this->is_default($id, $filter['identifier'], $view, $filter['staff_id'])) {
            $this->delete_default($filter['identifier'], $view, $filter['staff_id']);
        }

        $data["builder"] = json_encode($data["builder"]);

        $this->db->where("id", $id)->update("filters", $data);

        return $this->find($id, $view, $staffId);
    }

    public function delete($id)
    {
        $this->db->where('id', $id)->delete('filters');

        return true;
    }

    public function get_for_staff($identifier, $view, $staffId)
    {
        $this->db->where("identifier", $identifier);

        $this->db->group_start();
        $this->db->where('staff_id', $staffId);
        $this->db->or_where('is_shared', 1);
        $this->db->group_end();

        $filters = $this->db->get("filters")->result_array();

        foreach ($filters as $key => $filter) {
            $filters[$key]["builder"] = json_decode($filter["builder"], true);
        }

        return $this->merge_defaults($filters, $view, $staffId);
    }

    public function is_default($filterId, $identifier, $view, $staffId)
    {
        return $this->db->where('staff_id', $staffId)
            ->where('identifier', $identifier)
            ->where('filter_id', $filterId)
            ->where('view', $view)
            ->count_all('filter_defaults') > 0;
    }

    public function mark_as_default($filterId, $identifier, $view, $staffId)
    {
        $this->db->insert('filter_defaults', [
            'staff_id' => $staffId,
            'filter_id' => $filterId,
            'view' => $view,
            'identifier' => $identifier,
        ]);
    }

    public function delete_default($identifier, $view, $staffId)
    {
        $this->db->where('staff_id', $staffId)
            ->where('identifier', $identifier)
            ->where('view', $view)
            ->delete('filter_defaults');
    }

    protected function merge_defaults($filters, $view, $staffId)
    {
        $filterIds = Arr::pluck($filters, 'id');

        if (count($filterIds) === 0) {
            return $filters;
        }

        $defaults = $this->db->where_in('filter_id', $filterIds)
            ->where('view', $view)
            ->get('filter_defaults')
            ->result_array();

        foreach ($filters as $key => $filter) {
            $filters[$key]['is_default'] = "0";

            foreach ($defaults as $default) {
                if ($default['staff_id'] == $staffId && $default['filter_id'] == $filter['id']) {
                    $filters[$key]['is_default'] = "1";
                }
            }
        }

        return $filters;
    }
}
