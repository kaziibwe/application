<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = array(
    _l('delivery_note_dt_table_heading_number'),
    array(
        'name' => _l('invoice_delivery_note_year'),
        'th_attrs' => array('class' => 'not_visible')
    ),
    array(
        'name' => _l('delivery_note_dt_table_heading_client'),
        'th_attrs' => array('class' => (isset($client) ? 'not_visible' : ''))
    ),
    _l('project'),
    _l('tags'),
    _l('delivery_note_dt_table_heading_date'),
    _l('reference_no'),
    _l('delivery_note_dt_table_heading_status'),
    _l('delivery_note_dt_table_heading_created_by')
);

$custom_fields = get_custom_fields('delivery_note', array('show_on_table' => 1));

foreach ($custom_fields as $field) {
    array_push($table_data, [
        'name' => $field['name'],
        'th_attrs' => array('data-type' => $field['type'], 'data-custom-field' => 1),
    ]);
}

$table_data = hooks()->apply_filters('delivery_notes_table_columns', $table_data);

render_datatable(
    $table_data,
    isset($class) ? $class : 'delivery_notes',
    [],
    [
        'id' => $table_id ?? 'delivery_notes',
        'data-default-order' => get_table_last_order('delivery_notes'),
        'data-last-order-identifier' => 'delivery_notes',
    ]
);
