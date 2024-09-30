<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Filters extends AdminController
{
    public function create()
    {
        $this->load->model('filters_model');

        $filter = $this->filters_model->create([
            'name' => $this->input->post('name'),
            'identifier' => $this->input->post('identifier'),
            'builder' => $this->unformatDateRules($this->input->post('rules')),
            'is_shared' => filter_var($this->input->post('is_shared'), FILTER_VALIDATE_BOOL),
            'is_default' => filter_var($this->input->post('is_default'), FILTER_VALIDATE_BOOL),
            'view' => $this->input->post('view'),
            'staff_id' => get_staff_user_id(),
        ]);

        $filter['builder']['rules'] = $this->mapRulesIntoInstances($filter['builder']['rules'], $filter['identifier']);

        echo json_encode($filter);
    }

    public function update($id)
    {
        $this->load->model('filters_model');
        $staffId = get_staff_user_id();
        $view = $this->input->post('view');

        $filter = $this->db->where('id', $id)->get('filters')->row_array();

        if (!is_admin() && $filter['staff_id'] != $staffId) {
            ajax_access_denied();
        }

        $filter = $this->filters_model->update($id, [
            'name' => $this->input->post('name'),
            'is_shared' => filter_var($this->input->post('is_shared'), FILTER_VALIDATE_BOOL),
            'is_default' => filter_var($this->input->post('is_default'), FILTER_VALIDATE_BOOL),
            'view' => $view,
            'builder' => $this->unformatDateRules($this->input->post('rules')),
        ], $staffId);

        $filter['builder']['rules'] = $this->mapRulesIntoInstances($filter['builder']['rules'], $filter['identifier']);

        echo json_encode($filter);
    }

    public function delete($id)
    {
        $this->load->model('filters_model');
        $filter = $this->db->where('id', $id)->get('filters')->row_array();

        if (!is_admin() && $filter['staff_id'] != get_staff_user_id()) {
            ajax_access_denied();
        }

        $this->filters_model->delete($id);
    }

    public function mark_as_default($id, $identifier, $view)
    {
        $this->load->model('filters_model');
        $this->filters_model->mark_as_default($id, $identifier, $view, get_staff_user_id());
    }

    public function unmark_as_default($identifier, $view)
    {
        $this->load->model('filters_model');
        $this->filters_model->delete_default($identifier, $view, get_staff_user_id());
    }

    public function validate_dynamic_date()
    {
        $value = $this->input->post('value');
        $parsed = strtotime($value);
        if ($parsed === false) {
            echo 'fails';
        } else {
            echo date('Y-m-d', $parsed);
        }
    }

    protected function unformatDateRules($builder)
    {
        foreach ($builder['rules'] as $key => $rule) {
            if ($rule['type'] == 'DateRule' && !$rule['has_dynamic_value']) {
                if (is_array($rule['value'])) {
                    $builder['rules'][$key]['value'] = [to_sql_date($rule['value'][0]), to_sql_date($rule['value'][1])];
                } else {
                    $builder['rules'][$key]['value'] = to_sql_date($rule['value']);
                }
            }
        }

        return $builder;
    }

    protected function mapRulesIntoInstances($rules, $identifier)
    {
        $table = App_table::find($identifier);

        foreach ($rules as $key => $rule) {
            $ruleInstance = clone $table->findRule($rule['id']);

            $rules[$key] = $ruleInstance
                ->setOperator($rule['operator'])
                ->setValue($rule['value'])
                ->dynamic($rule['has_dynamic_value']);
        }

        return $rules;
    }
}
