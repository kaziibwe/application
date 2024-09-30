<?php

use app\services\utilities\Js;
use app\services\utilities\Str;

defined("BASEPATH") or exit("No direct script access allowed");

class App_table
{
    public $ci;

    protected $viewPath;

    protected $view;

    protected $rules = [];

    protected $id;

    protected $tableName = null;

    protected $outputUsing;

    protected $relatedTo;

    protected $primaryKeyName = 'id';

    protected $customfieldable;

    protected $excludedCustomFields = ['colorpicker', 'link'];

    protected $customFieldsToRuleMap = [
        'input' => 'TextRule',
        'number' => 'NumberRule',
        'select' => 'SelectRule',
        'multiselect' => 'MultiSelectRule',
        'checkbox' => 'CheckboxRule',
        'date_picker' => 'DateRule',
        'date_picker_time' => 'DateRule',
    ];

    protected static $registered = [];

    protected static $customRules = [];

    protected static $compatibility = null;

    protected static $customFields = null;

    /**
     * Available query operators.
     */
    protected array $operators = [
        "dynamic" => ['apply_to' => "DateRule"],
        "equal" => ["apply_to" => ["TextRule", "NumberRule", "DateRule", "RadioRule", "SelectRule"]],
        "not_equal" => ["apply_to" => ["TextRule", "NumberRule", "DateRule", "SelectRule"]],
        "in" => ["apply_to" => ["MultiSelectRule", "CheckboxRule"]],
        "not_in" => ["apply_to" => ["MultiSelectRule", "CheckboxRule"]],
        "less" => ["apply_to" => ["NumberRule", "DateRule"]],
        "less_or_equal" => ["apply_to" => ["NumberRule", "DateRule"]],
        "greater" => ["apply_to" => ["NumberRule", "DateRule"]],
        "greater_or_equal" => ["apply_to" => ["NumberRule", "DateRule"]],
        'between' => ['apply_to' => ['NumberRule', 'DateRule']],
        'not_between' => ['apply_to' => ['NumberRule', 'DateRule']],
        "begins_with" => ["apply_to" => ["TextRule"]],
        "not_begins_with" => ["apply_to" => ["TextRule"]],
        "contains" => ["apply_to" => ["TextRule"]],
        "not_contains" => ["apply_to" => ["TextRule"]],
        "ends_with" => ["apply_to" => ["TextRule"]],
        "not_ends_with" => ["apply_to" => ["TextRule"]],
        'is_empty' => ['apply_to' => []],
        'is_not_empty' => ['apply_to' => []],
    ];

    /**
     * SQL Operators.
     */
    protected array $operator_sql = [
        "equal" => ["operator" => "="],
        "not_equal" => ["operator" => "!="],
        "in" => ["operator" => "IN"],
        "not_in" => ["operator" => "NOT IN"],
        "less" => ["operator" => "<"],
        "less_or_equal" => ["operator" => "<="],
        "greater" => ["operator" => ">"],
        "greater_or_equal" => ["operator" => ">="],
        'between' => ['operator' => 'BETWEEN'],
        'not_between' => ['operator' => 'NOT BETWEEN'],
        "begins_with" => ["operator" => "LIKE", "append" => "%"],
        "not_begins_with" => ["operator" => "NOT LIKE", "append" => "%"],
        "contains" => ["operator" => "LIKE", "prepend" => "%", "append" => "%"],
        "not_contains" => ["operator" => "NOT LIKE", "prepend" => "%", "append" => "%"],
        "ends_with" => ["operator" => "LIKE", "prepend" => "%"],
        "not_ends_with" => ["operator" => "NOT LIKE", "prepend" => "%"],
        'is_empty' => ['operator' => '='],
        'is_not_empty' => ['operator' => '!='],
    ];

    /**
     * The operator that needs array.
     */
    protected array $needs_array = ["IN", "NOT IN", "BETWEEN", "NOT BETWEEN"];

    public function __construct($id = null, $viewPath = null)
    {
        $this->ci = &get_instance();
        $this->id = $id;
        $this->viewPath = $viewPath ?? $id;

        if (is_null(static::$compatibility)) {
            static::$compatibility = json_decode(get_option('v310_incompatible_tables') ?: '{}', true);
        }
    }

    /**
     * @param \App_table  $table
     * @return void
     */
    public static function register($table)
    {
        if (!$table->id) {
            throw new Exception('A table must have an ID.');
        }

        if (array_key_exists($table->id, static::$registered)) {
            throw new Exception('A table with ID: "' . $table->id . '" is already registered.');
        }

        static::$registered[$table->id] = $table;

        $table->init();
    }

    protected function init()
    {
        $incompatible = false;
        $comp_check_key = 'my_' . $this->viewPath . '.php';
        $comp_check_view_path = VIEWPATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . $comp_check_key;

        if (isset(static::$compatibility[$comp_check_key]) && is_file($comp_check_key)) {
            $incompatible = static::$compatibility[$comp_check_key] == filemtime(
                $comp_check_view_path
            );
        }

        include_once($this->ci->app->get_table_path($this->viewPath, $incompatible === false));
    }

    public function relatedTo($tableId)
    {
        $this->relatedTo = $tableId;

        return $this;
    }
    /**
     * @param string $id 
     * @return \App_table 
     */
    public static function find($id)
    {
        return static::$registered[$id] ?? null;
    }

    public static function new($id, $viewPath = null)
    {
        return new static($id, $viewPath);
    }

    public static function rule($tableId, $rule)
    {
        if (!array_key_exists($tableId, static::$customRules)) {
            static::$customRules[$tableId] = [];
        }

        static::$customRules[$tableId][] = $rule;
    }

    public function getDbTableName()
    {
        return $this->tableName ?? ($this->relatedTo ? static::find($this->relatedTo)->getDbTableName() : $this->id());
    }

    public function setDbTableName($table)
    {
        $this->tableName = $table;

        return $this;
    }

    protected function operatorRequiresArray($operator): bool
    {
        return in_array($operator, $this->needs_array);
    }

    protected function rememberCustomFields()
    {
        if (!is_null(static::$customFields)) {
            return static::$customFields;
        }

        $this->ci->db->where('active', 1);
        $this->ci->db->order_by('field_order', 'asc');

        $result = $this->ci->db->get('customfields')->result_array();

        foreach ($result as $key => $cf) {
            $result[$key]['name'] = _maybe_translate_custom_field_name($cf['name'], $cf['slug']);
        }

        return static::$customFields = collect($result);
    }

    protected function getCustomFields()
    {
        return $this->rememberCustomFields()->filter(function ($field) {
            return $field['fieldto'] == $this->customfieldIdentifier();
        });
    }

    protected function createRulesFromCustomFields()
    {
        $rulesMap = hooks()->apply_filters('table_custom_field_rules_map', $this->customFieldsToRuleMap);
        $isAdmin = is_admin();

        if (!$this->customfieldable) {
            return [];
        }

        return $this->getCustomFields()->reject(function ($field) use ($rulesMap) {
            return in_array($field['type'], $this->excludedCustomFields) ||
                !array_key_exists($field['type'], $rulesMap);
        })->map(function ($field) use ($rulesMap, $isAdmin) {
            $options = collect(explode(',', $field['options']))
                ->map(fn ($option) => trim($option))
                ->filter()
                ->map(fn ($option) => ['value' => $option, 'label' => $option]);

            $rule = App_table_filter::new(
                $field['slug'],
                $rulesMap[$field['type']]
            )->label($field['name']);

            if (!$isAdmin && $field['only_admin'] == '1') {
                $rule->isVisible(fn () => false);
            }

            if (!empty($options->all())) {
                $rule->options($options);
            }

            $rule->raw(function ($value, $operator, $operatorSql, $ruleInstance, $table) use ($field, $options) {
                $prefix = 'exists';

                if (!empty($options->all())) {
                    if ($ruleInstance->type === 'MultiSelectRule' || $ruleInstance->type === 'CheckboxRule') {
                        $whereValueSql = '(';
                        foreach ($value as $option) {
                            $whereValueSql .= ' value ' . 'LIKE' . ' "%' . $option . '%" OR';
                        }

                        $whereValueSql = trim(substr_replace($whereValueSql, '', -3));
                        $whereValueSql .= ')';
                    } else {
                        $whereValueSql = ' value = ' . $this->wrapValueInQuotes($value);
                    }

                    if ($operator === 'not_in' || $operator === 'not_equal') {
                        $prefix = 'not exists';
                    }
                } else {
                    $valueSqlColumn = 'value';

                    if ($ruleInstance->type === 'NumberRule') {
                        $valueSqlColumn = 'CAST(value as SIGNED)';
                    } else if ($ruleInstance->type === 'DateRule') {
                        $valueSqlColumn = 'CAST(value as DATE)';
                    }

                    $whereValueSql = $this->toSql($value, $operator, $operatorSql, $valueSqlColumn, $ruleInstance);
                }   

                return $prefix . $this->customFieldWhereSql($whereValueSql, $table, $field['fieldto'], $field['id']);
            });

            $rule = hooks()->apply_filters('table_' . $this->id() . '_custom_field_rule', $rule, $field);

            return $rule;
        })->all();
    }

    protected function customFieldWhereSql($valueSql, $table, $fieldTo, $fieldId)
    {
        return ' (SELECT value FROM ' . db_prefix() . 'customfieldsvalues WHERE fieldto="' . $fieldTo . '" AND fieldid=' . $fieldId . ' and ' . $table . '.' . $this->getPrimaryKeyName() . ' = relid AND ' . $valueSql . ')';
    }

    public function getPrimaryKeyName()
    {
        return $this->primaryKeyName;
    }

    public function setPrimaryKeyName($name)
    {
        $this->primaryKeyName = $name;

        return $this;
    }

    public function findRule($ruleId)
    {
        foreach ($this->rules() as $rule) {
            if ($rule->id == $ruleId) {
                return $rule;
            }
        }
    }

    public function customfieldable($identifier)
    {
        $this->customfieldable = $identifier;

        return $this;
    }

    public function customfieldIdentifier()
    {
        return $this->customfieldable ?? $this->id();
    }

    public function id()
    {
        return $this->id;
    }

    public function setRules($rules)
    {
        $this->rules = $rules;

        return $this;
    }

    public function rules()
    {
        return hooks()->apply_filters(
            "table_" . $this->id() . "_rules",
            array_merge($this->rules, static::$customRules[$this->id()] ?? [], $this->createRulesFromCustomFields())
        );
    }

    public function viewName()
    {
        return $this->view ?? $this->id();
    }

    public function setViewName($view)
    {
        return $this->view = $view;
        return $this;
    }

    protected function allRelatedTables()
    {
        $related = [];

        foreach (static::$registered as $table) {
            if ($table->relatedTo === $this->id() || $this->relatedTo === $table->id()) {
                $related[] = $table;
            }
        }

        return $related;
    }

    public function filters($view = null, $related = true)
    {
        $this->ci->load->model('filters_model');

        $filters = $this->castRules($this->ci->filters_model->get_for_staff(
            $this->id(),
            $view ?? $this->viewName(),
            get_staff_user_id()
        ), $this);

        if ($related) {
            foreach ($this->allRelatedTables() as $table) {
                $filters = array_merge($filters, $this->castRules($table->filters($this->viewName(), false), $table));
            }
        }

        return $filters;
    }

    protected function castRules($filters, $table)
    {
        foreach ($filters as $key => $filter) {
            if(!isset($filter['builder']['rules'])) {
                $filters[$key]['builder']['rules'] = [];
                $filter['builder']['rules'] = [];
            }

            foreach ($filter['builder']['rules'] as $rk => $rule) {
                if ($rule instanceof App_table_filter) {
                    continue;
                }

                if ($ruleInstance = $table->findRule($rule['id'])) {
                    $ruleInstance = clone $ruleInstance;

                    $filters[$key]['builder']['rules'][$rk] = $ruleInstance
                        ->setValue($rule['value'])
                        ->dynamic($rule['has_dynamic_value'])
                        ->setOperator($rule['operator'] ?? null);
                } else {
                    unset($filters[$key]['builder']['rules'][$rk]);
                }
            }
        }
        return $filters;
    }

    protected function getRuleValueForSql($rule)
    {
        $value = $rule['value'];

        if (is_array($value)) {
            $value = array_map(fn ($v) => $this->ci->db->escape_str($v), $value);
        } else {
            $value = $this->ci->db->escape_str($value);
        }

        if ($rule['type'] === 'DateRule') {
            if (filter_var($rule['has_dynamic_value'], FILTER_VALIDATE_BOOL)) {
                if (is_array($value)) {
                    $value = [date('Y-m-d', strtotime($value[0])), date('Y-m-d', strtotime($value[1]))];
                } else {
                    $value = date('Y-m-d', strtotime($value));
                }
            } else {
                if (is_array($value)) {
                    $value = [to_sql_date($value[0]), to_sql_date($value[1])];
                } else {
                    $value = to_sql_date($value);
                }
            }
        }

        return $value;
    }

    public function outputUsing($closure)
    {
        $this->outputUsing = $closure;

        return $this;
    }

    public function filtersJs()
    {
        return Js::from($this->filters());
    }

    public function rulesJs()
    {
        return Js::from($this->rules());
    }


    public function getWhereFromRules()
    {
        $whereSqls = [];
        $matchType = $this->ci->input->post("filters")["match_type"] ?? "and";
        $appliedRules = $this->ci->input->post("filters")["rules"] ?? [];

        $fullTableName = db_prefix() . $this->getDbTableName();

        foreach ($appliedRules as $rule) {
            $ruleInstance = $this->findRule($rule["id"])->dynamic($rule['has_dynamic_value']);

            if (!$ruleInstance) {
                continue;
            }

            $sqlColumn = $ruleInstance->column ?? $ruleInstance->id;

            if (!$ruleInstance->column && !Str::startsWith($fullTableName . ".", $sqlColumn)) {
                $sqlColumn = $fullTableName . "." . $sqlColumn;
            }

            $operator = ($rule["operator"] ?? '') ?: "equal";
            $operatorSql = $this->operator_sql[$operator] ?? null;

            if ($ruleInstance->hasDynamicValue && !$operatorSql && $operator === 'dynamic') {
                $operatorSql = ['operator' => '='];
            }

            $value = $this->getRuleValueForSql($rule);

            if (is_callable($ruleInstance->callback)) {
                $whereSqls[] = call_user_func_array($ruleInstance->callback, [
                    $value,
                    $operator,
                    $operatorSql,
                    $ruleInstance,
                    $fullTableName
                ]);
                continue;
            }


            $whereSqls[] = $this->toSql($value, $operator, $operatorSql, $sqlColumn, $ruleInstance);
        }

        $whereSqls = array_filter($whereSqls);

        foreach ($whereSqls as $key => $sql) {
            if ($key === 0) {
                continue;
            }

            $whereSqls[$key] = $matchType . " " . $sql;
        }

        if (count($whereSqls) > 0) {
            return "AND (" . implode(" ", $whereSqls) . ")";
        }

        return null;
    }

    protected function toSql($value, $operator, $operatorSql, $sqlColumn, $rule)
    {

        if (
            $this->operatorRequiresArray(
                strtoupper($operatorSql["operator"])
            )
        ) {
            if (
                $operatorSql["operator"] === "IN" ||
                $operatorSql["operator"] === "NOT IN"
            ) {
                $value = "'" . implode("','", $value) . "'";
                return
                    $sqlColumn .
                    " " .
                    $operatorSql["operator"] .
                    " (" .
                    $value .
                    ")";
            } else if (
                $operatorSql["operator"] === "BETWEEN" ||
                $operatorSql["operator"] === "NOT BETWEEN"
            ) {
                if ($value[0] == $value[1]) {
                    return $sqlColumn . " = " . $this->wrapValueInQuotes($value[0]);
                } else {
                    return
                        $sqlColumn .
                        " " .
                        $operatorSql["operator"] . " " .
                        $this->wrapValueInQuotes($value[0]) .
                        " AND " .
                        $this->wrapValueInQuotes($value[1]);
                }
            }
        }

        $appendAfter = "";

        if (
            $operatorSql["operator"] === "LIKE" ||
            $operatorSql["operator"] === "NOT LIKE"
        ) {
            $value = $this->ci->db->escape_like_str($value);
            $appendAfter = " ESCAPE '!'";
        }

        if (in_array($operator, ['is_empty', 'is_not_empty'])) {
            if ($rule->emptyOperatorValue === null) {
                $operatorSql['operator'] = $operator === 'is_empty' ? 'IS NULL' : 'IS NOT NULL';
                $value = null;
            } else {
                $value = $rule->emptyOperatorValue;
            }
        }

        $sql =
            ($sqlColumn .
                " " .
                $operatorSql["operator"]) .
            ($value !== null ? $this->wrapValueInQuotes((($operatorSql["prepend"] ?? "") .
                $value .
                ($operatorSql["append"] ?? ""))) : '');

        $sql .= $appendAfter;

        return $sql;
    }

    protected function wrapValueInQuotes($value)
    {
        // If not wrapped in mysql function matches uppercase functions and ( e.q. CAST(
        if (preg_match('/[A-Z]+\(/', $value)) {
            return $value;
        }

        return '"' . $value . '"';
    }

    public function output($params = [])
    {
        $params = apply_filters_deprecated('table_params', [$params, $this->viewPath], '3.0.7', 'table_[TABLE_ID]_output_params');
        $params = hooks()->apply_filters('table_' . $this->id() . '_output_params', $params);

        $closure = $this->outputUsing;

        $closure = $closure->bindTo($this);

        $params = array_merge(["customFieldsColumns" => []], $params);

        echo json_encode($closure($params, $this->rules()));
        die();
    }
}
