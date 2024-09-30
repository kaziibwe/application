<?php

defined("BASEPATH") or exit("No direct script access allowed");

class App_table_filter implements JsonSerializable
{
    public $id;
    public $type;
    public $label;
    public $callback;
    public $operators;
    public $operator;
    public $value;
    public $column;
    public $hasDynamicValue;
    public $emptyOperatorValue = null; // or '' // or any
    public $withEmptyOperators = false;
    public $options = [];
    protected $visibleToAll = true;

    protected $commonOperators = [
        "CheckboxRule" => ["in", "not_in"],
        "MultiSelectRule" => ["in", "not_in"],
        "SelectRule" => ["equal", "not_equal"],
        "TextRule" => ["equal", "not_equal", "begins_with", "not_begins_with", "contains", "not_contains", "ends_with", "not_ends_with"],
        "DateRule" => ["equal", "not_equal", 'between', 'not_between', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'dynamic'],
        "NumberRule" => ["equal", "not_equal", 'between', 'not_between', 'less', 'less_or_equal', 'greater', 'greater_or_equal'],
    ];

    public function __construct($id = null, $type = null)
    {
        $this->id = $id;
        $this->type = $type;
    }

    public function column($column)
    {
        $this->column = $column;

        return $this;
    }

    public function raw($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    public function emptyOperatorValue($value)
    {
        $this->emptyOperatorValue = $value;

        return $this;
    }

    public function withEmptyOperators()
    {
        $this->withEmptyOperators = true;

        return $this;
    }

    public function isVisible(Closure $value)
    {
        $this->visibleToAll = $value;

        return $this;
    }

    /**
     * Set the table input options.
     *
     * Only for select fields.
     *
     * @param callable|array $options
     */
    public function options($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get the table input options.
     *
     * Only for select fields.
     *
     * @return array
     */
    public function getOptions()
    {
        if (!$this->checkVisibility()) {
            return [];
        }

        if ($this->options instanceof Closure) {
            return call_user_func_array($this->options, [get_instance()]);
        }

        return $this->options;
    }

    public function dynamic($value)
    {
        $this->hasDynamicValue = filter_var($value, FILTER_VALIDATE_BOOL);

        return $this;
    }

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public function operators($operators)
    {
        $this->operators = $operators;

        return $this;
    }

    public function formattedValue()
    {
        if (!$this->value) {
            return null;
        }

        if ($this->type === 'DateRule' && !$this->hasDynamicValue) {
            if (is_array($this->value)) {
                return [_d($this->value[0]), _d($this->value[1])];
            }

            return _d($this->value);
        }

        return $this->value;
    }

    public static function new($id, $type)
    {
        return new static($id, $type);
    }

    protected function determineOperators()
    {
        $operators = $this->operators ?? ($this->commonOperators[$this->type] ?? []);

        if ($this->withEmptyOperators) {
            $operators[] = 'is_empty';
            $operators[] = 'is_not_empty';
        }

        return $operators;
    }

    protected function checkVisibility()
    {
        if ($this->visibleToAll === true) {
            return true;
        }

        return call_user_func($this->visibleToAll);
    }

    public function jsonSerialize() : array
    {
        return [
            "id" => $this->id,
            "type" => $this->type,
            "label" => $this->label,
            "value" => $this->value,
            "has_dynamic_value" => $this->hasDynamicValue,
            "visible_to_all" => $this->checkVisibility(),
            "has_authorizations" => $this->visibleToAll instanceof Closure,
            "formatted_value" => $this->formattedValue(),
            "operator" => $this->operator ?? $this->commonOperators[$this->type][0] ?? null,
            "operators" => $this->determineOperators(),
            "options" => $this->getOptions(),
        ];
    }
}
