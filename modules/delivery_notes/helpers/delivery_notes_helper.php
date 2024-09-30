<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get delivery_note short_url
 * @since  Version 2.7.3
 * @param  object $delivery_note
 * @return string Url
 */
function get_delivery_note_shortlink($delivery_note)
{
    $long_url = site_url("delivery_notes/client/dn/{$delivery_note->id}/{$delivery_note->hash}");
    if (!get_option('bitly_access_token')) {
        return $long_url;
    }

    // Check if delivery_note has short link, if yes return short link
    if (!empty($delivery_note->short_link)) {
        return $delivery_note->short_link;
    }

    // Create short link and return the newly created short link
    $short_link = app_generate_short_link([
        'long_url' => $long_url,
        'title'    => format_delivery_note_number($delivery_note->id),
    ]);

    if ($short_link) {
        $CI = &get_instance();
        $CI->db->where('id', $delivery_note->id);
        $CI->db->update(db_prefix() . 'delivery_notes', [
            'short_link' => $short_link,
        ]);

        return $short_link;
    }

    return $long_url;
}

/**
 * Check delivery_note restrictions - hash, clientid
 * @param  mixed $id   delivery_note id
 * @param  string $hash delivery_note hash
 */
function check_delivery_note_restrictions($id, $hash)
{
    $CI = &get_instance();
    $CI->load->model('delivery_notes_model');
    if (!$hash || !$id) {
        show_404();
    }
    if (!is_client_logged_in() && !is_staff_logged_in()) {
        if (get_option('view_delivery_note_only_logged_in') == 1) {
            redirect_after_login_to_current_url();
            redirect(site_url('authentication/login'));
        }
    }
    $delivery_note = $CI->delivery_notes_model->get($id);
    if (!$delivery_note || ($delivery_note->hash != $hash)) {
        show_404();
    }
    // Do one more check
    if (!is_staff_logged_in()) {
        if (get_option('view_delivery_note_only_logged_in') == 1) {
            if ($delivery_note->clientid != get_client_user_id()) {
                show_404();
            }
        }
    }
}

/**
 * Check if delivery_note email template for expiry reminders is enabled
 * @return boolean
 */
function is_delivery_notes_email_expiry_reminder_enabled()
{
    return total_rows(db_prefix() . 'emailtemplates', ['slug' => 'delivery_note-expiry-reminder', 'active' => 1]) > 0;
}

/**
 * Check if there are sources for sending delivery_note expiry reminders
 * Will be either email or SMS
 * @return boolean
 */
function is_delivery_notes_expiry_reminders_enabled()
{
    return is_delivery_notes_email_expiry_reminder_enabled();
}

/**
 * Return RGBa delivery_note status color for PDF documents
 * @param  mixed $status_id current delivery_note status
 * @return string
 */
function delivery_note_status_color_pdf($status_id)
{
    if ($status_id == 1) {
        $statusColor = '119, 119, 119';
    } elseif ($status_id == 2) {
        // Sent
        $statusColor = '3, 169, 244';
    } elseif ($status_id == 3) {
        //Declines
        $statusColor = '252, 45, 66';
    } elseif ($status_id == 4) {
        //Delivered
        $statusColor = '0, 191, 54';
    } elseif ($status_id == 5) {
        // Partially delivered
        $statusColor = '202, 138, 3';
    } else {
        // Expired
        $statusColor = '255, 111, 0';
    }

    return hooks()->apply_filters('delivery_note_status_pdf_color', $statusColor, $status_id);
}

/**
 * Format delivery_note status
 * @param  integer  $status
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_delivery_note_status($status, $classes = '', $label = true)
{
    $id          = $status;
    $label_class = delivery_note_status_color_class($status);
    $status      = delivery_note_status_by_id($status);
    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status delivery_note-status-' . $id . ' delivery_note-status-' . $label_class . '">' . $status . '</span>';
    }

    return $status;
}

/**
 * Return delivery_note status translated by passed status id
 * @param  mixed $id delivery_note status id
 * @return string
 */
function delivery_note_status_by_id($id)
{
    $status = '';
    if ($id == 1) {
        $status = _l('delivery_note_status_waiting');
    } elseif ($id == 2) {
        $status = _l('delivery_note_status_sent');
    } elseif ($id == 4) {
        $status = _l('delivery_note_status_delivered');
    } elseif ($id == 5) {
        $status = _l('delivery_note_status_partially_delivered');
    } elseif ($id == 3) {
        $status = _l('delivery_note_status_cancelled');
    } else {
        if (!is_numeric($id)) {
            if ($id == 'not_sent') {
                $status = _l('not_sent_indicator');
            }
        }
    }

    return hooks()->apply_filters('delivery_note_status_label', $status, $id);
}

/**
 * Return delivery_note status color class based on twitter bootstrap
 * @param  mixed  $id
 * @param  boolean $replace_default_by_muted
 * @return string
 */
function delivery_note_status_color_class($id, $replace_default_by_muted = false)
{
    $class = '';
    if ($id == 1) {
        $class = 'default';
        if ($replace_default_by_muted == true) {
            $class = 'muted';
        }
    } elseif ($id == 2) {
        $class = 'info';
    } elseif ($id == 3) {
        $class = 'danger';
    } elseif ($id == 4) {
        $class = 'success';
    } elseif ($id == 5) {
        $class = 'warning';
    } else {
        if (!is_numeric($id)) {
            if ($id == 'not_sent') {
                $class = 'default';
                if ($replace_default_by_muted == true) {
                    $class = 'muted';
                }
            }
        }
    }

    return hooks()->apply_filters('delivery_note_status_color_class', $class, $id);
}

/**
 * Check if the delivery_note id is last invoice
 * @param  mixed  $id delivery_noteid
 * @return boolean
 */
function is_last_delivery_note($id)
{
    $CI = &get_instance();
    $CI->db->select('id')->from(db_prefix() . 'delivery_notes')->order_by('id', 'desc')->limit(1);
    $query            = $CI->db->get();
    $last_delivery_noteid = $query->row()->id;
    if ($last_delivery_noteid == $id) {
        return true;
    }

    return false;
}

/**
 * Format delivery_note number based on description
 * @param  mixed $id
 * @return string
 */
function format_delivery_note_number($id)
{
    $CI = &get_instance();

    if (!is_object($id)) {
        $CI->db->select('date,number,prefix,number_format')->from(db_prefix() . 'delivery_notes')->where('id', $id);
        $delivery_note = $CI->db->get()->row();
    } else {
        $delivery_note = $id;
        $id       = $delivery_note->id;
    }

    if (!$delivery_note) {
        return '';
    }

    $number = sales_number_format($delivery_note->number, $delivery_note->number_format, $delivery_note->prefix, $delivery_note->date);

    return hooks()->apply_filters('format_delivery_note_number', $number, [
        'id'       => $id,
        'delivery_note' => $delivery_note,
    ]);
}


/**
 * Function that return delivery_note item taxes based on passed item id
 * @param  mixed $itemid
 * @return array
 */
function get_delivery_note_item_taxes($itemid)
{
    $CI = &get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'delivery_note');
    $taxes = $CI->db->get(db_prefix() . 'item_tax')->result_array();
    $i     = 0;
    foreach ($taxes as $tax) {
        $taxes[$i]['taxname'] = $tax['taxname'] . '|' . $tax['taxrate'];
        $i++;
    }

    return $taxes;
}

/**
 * Calculate delivery_notes percent by status
 * @param  mixed $status          delivery_note status
 * @return array
 */
function get_delivery_notes_percent_by_status($status, $project_id = null)
{
    $has_permission_view = staff_can('view',  'delivery_notes');
    $where               = '';

    if (isset($project_id)) {
        $where .= 'project_id=' . get_instance()->db->escape_str($project_id) . ' AND ';
    }
    if (!$has_permission_view) {
        $where .= get_delivery_notes_where_sql_for_staff(get_staff_user_id());
    }

    $where = trim($where);

    if (endsWith($where, ' AND')) {
        $where = substr_replace($where, '', -3);
    }

    $total_delivery_notes = total_rows(db_prefix() . 'delivery_notes', $where);

    $data            = [];
    $total_by_status = 0;

    if (!is_numeric($status)) {
        if ($status == 'not_sent') {
            $total_by_status = total_rows(db_prefix() . 'delivery_notes', 'sent=0 AND status NOT IN(2,3,4)' . ($where != '' ? ' AND (' . $where . ')' : ''));
        }
    } else {
        $whereByStatus = 'status=' . $status;
        if ($where != '') {
            $whereByStatus .= ' AND (' . $where . ')';
        }
        $total_by_status = total_rows(db_prefix() . 'delivery_notes', $whereByStatus);
    }

    $percent                 = ($total_delivery_notes > 0 ? number_format(($total_by_status * 100) / $total_delivery_notes, 2) : 0);
    $data['total_by_status'] = $total_by_status;
    $data['percent']         = $percent;
    $data['total']           = $total_delivery_notes;

    return $data;
}

function get_delivery_notes_where_sql_for_staff($staff_id)
{
    $CI                                  = &get_instance();
    $has_permission_view_own             = staff_can('view_own',  'delivery_notes');
    $allow_staff_view_delivery_notes_assigned = get_option('allow_staff_view_delivery_notes_assigned');
    $whereUser                           = '';
    if ($has_permission_view_own) {
        $whereUser = '((' . db_prefix() . 'delivery_notes.addedfrom=' . $CI->db->escape_str($staff_id) . ' AND ' . db_prefix() . 'delivery_notes.addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature = "delivery_notes" AND capability="view_own"))';
        if ($allow_staff_view_delivery_notes_assigned == 1) {
            $whereUser .= ' OR sale_agent=' . $CI->db->escape_str($staff_id);
        }
        $whereUser .= ')';
    } else {
        $whereUser .= 'sale_agent=' . $CI->db->escape_str($staff_id);
    }

    return $whereUser;
}
/**
 * Check if staff member have assigned delivery_notes / added as sale agent
 * @param  mixed $staff_id staff id to check
 * @return boolean
 */
function staff_has_assigned_delivery_notes($staff_id = '')
{
    $CI       = &get_instance();
    $staff_id = is_numeric($staff_id) ? $staff_id : get_staff_user_id();
    $cache    = $CI->app_object_cache->get('staff-total-assigned-delivery_notes-' . $staff_id);

    if (is_numeric($cache)) {
        $result = $cache;
    } else {
        $result = total_rows(db_prefix() . 'delivery_notes', ['sale_agent' => $staff_id]);
        $CI->app_object_cache->add('staff-total-assigned-delivery_notes-' . $staff_id, $result);
    }

    return $result > 0 ? true : false;
}
/**
 * Check if staff member can view delivery_note
 * @param  mixed $id delivery_note id
 * @param  mixed $staff_id
 * @return boolean
 */
function user_can_view_delivery_note($id, $staff_id = false)
{
    $CI = &get_instance();

    $staff_id = $staff_id ? $staff_id : get_staff_user_id();

    if (has_permission('delivery_notes', $staff_id, 'view')) {
        return true;
    }

    $CI->db->select('id, addedfrom, sale_agent');
    $CI->db->from(db_prefix() . 'delivery_notes');
    $CI->db->where('id', $id);
    $delivery_note = $CI->db->get()->row();

    if ((has_permission('delivery_notes', $staff_id, 'view_own') && $delivery_note->addedfrom == $staff_id)
        || ($delivery_note->sale_agent == $staff_id && get_option('allow_staff_view_delivery_notes_assigned') == '1')
    ) {
        return true;
    }

    return false;
}

/**
 * Prepares email template preview $data for the view
 * @param  string $template    template class name
 * @param  mixed $customer_id_or_email customer ID to fetch the primary contact email or email
 * @return array
 */
if (!function_exists('my_prepare_mail_preview_data')) {
    function my_prepare_mail_preview_data($template, $customer_id_or_email, $mailClassParams = [])
    {
        $CI = &get_instance();

        if (is_numeric($customer_id_or_email)) {
            $contact = $CI->clients_model->get_contact(get_primary_contact_user_id($customer_id_or_email));
            $email   = $contact ? $contact->email : '';
        } else {
            $email = $customer_id_or_email;
        }

        $CI->load->model('emails_model');

        $data['template'] = $CI->app_mail_template->prepare($email, $template, $mailClassParams);
        $slug             = $CI->app_mail_template->get_default_property_value('slug', $template, $mailClassParams);

        $data['template_name'] = $slug;

        $template_result = $CI->emails_model->get(['slug' => $slug, 'language' => 'english'], 'row');

        $data['template_system_name'] = $template_result->name;
        $data['template_id']          = $template_result->emailtemplateid;

        $data['template_disabled'] = $template_result->active == 0;

        return $data;
    }
}

/**
 * Prepare general delivery note pdf
 * @since  Version 1.0.2
 * @param  object $delivery_note delivery note as object with all necessary fields
 * @param  string $tag tag for bulk pdf exporter
 * @return mixed object
 */
function delivery_note_pdf($delivery_note, $tag = '')
{
    return app_pdf('delivery_note', module_dir_path(DELIVERY_NOTE_MODULE_NAME, '/libraries/pdf/Delivery_note_pdf'), $delivery_note, $tag);
}


/**
 * HOOKS CALLBACKS
 */
/**
 * Global search result query for Delivery Note.
 *
 * @param array  $result Search result.
 * @param string $q      Search query.
 * @param int    $limit  Limit of results.
 *
 * @return array
 */
function delivery_note_global_search_result_query($result, $q, $limit)
{
    // Delivery Note Items Search
    $has_permission_view_delivery_notes = staff_can('view', 'delivery_notes');
    $has_permission_view_delivery_notes_own = staff_can('view_own', 'delivery_notes');
    $allow_staff_view_delivery_notes_assigned = get_option('allow_staff_view_delivery_notes_assigned');

    if ($has_permission_view_delivery_notes || $has_permission_view_delivery_notes_own || $allow_staff_view_delivery_notes_assigned) {
        $noPermissionQuery = get_delivery_notes_where_sql_for_staff(get_staff_user_id());

        $db = get_instance()->delivery_notes_model->db;
        $db->select()->from(db_prefix() . 'itemable');
        $db->where('rel_type', 'delivery_note');

        if (!$has_permission_view_delivery_notes) {
            $db->where('rel_id IN (select id from ' . db_prefix() . 'delivery_notes where ' . $noPermissionQuery . ')');
        }

        $db->where('(description LIKE "%' . $db->escape_like_str($q) . '%" ESCAPE \'!\' OR long_description LIKE "%' . $db->escape_like_str($q) . '%" ESCAPE \'!\')');
        $db->order_by('description', 'ASC');

        $result[] = [
            'result'         => $db->get()->result_array(),
            'type'           => 'delivery_note_items',
            'search_heading' => _l('delivery_note_items'),
        ];
    }

    return $result;
}

/**
 * Task modal relation type select for Delivery Note.
 *
 * @param array $value Selected value.
 *
 * @return void
 */
function delivery_note_task_modal_rel_type_select($value)
{
    $selected = '';

    if (isset($value) && isset($value['rel_type']) && $value['rel_type'] == 'delivery_note') {
        $selected = 'selected';
    }

    echo "<option value='delivery_note' " . $selected . ">" .
        _l('delivery_note') . "
           </option>";
}

/**
 * Relation values for Delivery Note.
 *
 * @param array $values Relation values.
 *
 * @return array
 */
function delivery_note_relation_values($values)
{
    $relation = $values['relation'] ?? [];

    if ($values['type'] == 'delivery_note') {
        if (empty($relation))
            $relation = ['id' => get_instance()->input->get('rel_id', true)];

        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = format_delivery_note_number($relation['id']);
        } else {
            $values['id'] = $relation->id;
            $values['name'] = format_delivery_note_number($relation->id);
        }

        $values['link'] = admin_url('delivery_notes/index/' . $values['id']);
    }

    return $values;
}

/**
 * Get relation data for Delivery Note.
 *
 * @param array $data Data for the relation.
 * @param array $obj  Object containing type and rel_id.
 *
 * @return array
 */
function delivery_note_get_relation_data($data, $obj)
{
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];

    if ($type == 'delivery_note') {
        $CI = &get_instance();

        if ($rel_id != '') {
            $data = $CI->delivery_notes_model->get($rel_id);
        } else {
            $data = [];

            if ($CI->input->post('q')) {
                $table = db_prefix() . 'delivery_notes';
                $q = $CI->input->post('q');
                $q = trim($q);
                $CI->delivery_notes_model->db->or_where("$table.number LIKE '%$q%'");
                $CI->delivery_notes_model->db->or_where("$table.id LIKE '%$q%'");
                $data = $CI->delivery_notes_model->get();
            }
        }
    }

    return $data;
}

/**
 * Tasks table row data for Delivery Note.
 *
 * @param array $row  Current row data.
 * @param array $aRow All row data.
 *
 * @return array
 */
function delivery_note_tasks_table_row_data($row, $aRow)
{
    if ($aRow['rel_type'] == 'delivery_note') {

        $CI = &get_instance();
        $CI->load->model('delivery_notes/delivery_notes_model');
        $dn = $CI->delivery_notes_model->get($aRow['rel_id']);

        if ($dn) {
            $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('delivery_notes/delivery_note/' . $dn->id) . '">' . format_delivery_note_number($dn->id) . '</a><br />';

            $row[2] = $row[2] . $str;
        }
    }

    return $row;
}

/**
 * Get staff and contact fields and insert into email templates.
 *
 * @param [array] $fields
 *
 * @return array
 */
function delivery_note_allow_staff_client_merge_fields($fields)
{
    $done = 0;
    foreach ($fields as $index => $group) {
        foreach ($group as $key => $groupFields) {
            if ($key == 'client') {
                foreach ($groupFields as $groupIndex => $groupField) {
                    if (isset($groupField['templates']))
                        $fields[$index][$key][$groupIndex]['templates'] = array_merge($groupField['templates'], ['delivery-note-send-to-client']);
                }
                $done++;
                break;
            }
            if ($key == 'staff') {
                foreach ($groupFields as $groupIndex => $groupField) {
                    if (isset($groupField['templates']) && in_array($groupField['key'], ['{staff_firstname}', '{staff_lastname}', '{staff_email}']))

                        $fields[$index][$key][$groupIndex]['templates'] = array_merge($groupField['templates'], ['delivery-note-confirmed-to-staff', 'delivery-note-cancelled-to-staff']);
                }
                $done++;
                break;
            }
            if ($done > 1) break;
        }
    }

    return $fields;
}

/**
 * Get items table for preview
 * @param  object  $transaction   e.q. invoice, estimate from database result row
 * @param  string  $type          type, e.q. invoice, estimate, proposal
 * @param  string  $for           where the items will be shown, html or pdf
 * @param  boolean $admin_preview is the preview for admin area
 * @return object
 */
function delivery_notes_get_items_table_data($transaction, $type, $for = 'html', $admin_preview = false)
{
    include_once(module_dir_path(DELIVERY_NOTE_MODULE_NAME, 'libraries/Delivery_notes_items_table.php'));
    $class = new Delivery_notes_items_table($transaction, $type, $for, $admin_preview);

    $class = hooks()->apply_filters('items_table_class', $class, $transaction, $type, $for, $admin_preview);

    if (!$class instanceof App_items_table_template) {
        show_error(get_class($class) . ' must be instance of "App_items_template"');
    }

    return $class;
}


/****************************START POLYFILL FOR LATEST PERFEX VESION*****************************/

if (!function_exists('staff_can')) {
    /**
     * Check if the logged in staff have the ability to perform given action on the resources
     * 
     * Polyfil method for those using older version of Perfex. 
     * @todo  Should be removed in future version
     *
     * @param string $action
     * @param string $resources
     * @return bool
     */
    function staff_can($action, $resources)
    {
        return has_permission($resources, '', $action);
    }
}

if (!function_exists('staff_cant')) {
    /**
     * Check if the logged in staff have the ability to perform given action on the resources
     * 
     * Polyfil method for those using older version of Perfex. 
     * @todo  Should be removed in future version
     *
     * @param string $action
     * @param string $resources
     * @return bool
     */
    function staff_cant($action, $resources)
    {
        return !staff_can($action, $resources);
    }
}


if (!function_exists('e')) {
    /**
     * Encode HTML special characters in a string.
     *
     * @param  bool  $doubleEncode
     * @return string
     */
    function e($value, $doubleEncode = true)
    {
        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        }

        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}

/****************************END POLYFILL FOR LATEST PERFEX VESION*****************************/

/**
 * List of hidden items fields
 *
 * @return array
 */
function delivery_note_items_hidden_fields()
{
    $key = 'delivery_note_items_hidden_fields';
    $value = get_option($key);
    $value = empty($value) ? [] : (array)json_decode($value);
    $value = array_filter($value, function ($n) {
        return !empty($n);
    });
    return $value;
}

/**
 * Check if a field is hidden
 *
 * @param string $field
 * @return bool
 */
function delivery_note_item_field_hidden($field)
{
    return in_array($field, delivery_note_items_hidden_fields());
}

/**
 * Add delivery note to custom field relation select
 *
 * @param object|null $selected
 * @return void
 */
function delivery_note_custom_filed_select_option($selected)
{
    $data_ppt = '';
    if (is_object($selected)) {

        foreach ($selected as $key => $value) {

            if (in_array($key, ['show_on_table', 'show_on_pdf', 'show_on_client_portal']))
                $data_ppt .= "data-$key='$value' ";
        }
    }

    $selected = is_object($selected) ? $selected->fieldto : $selected;
    get_instance()->app_scripts->add('delivery-note-custom-field', module_dir_url(DELIVERY_NOTE_MODULE_NAME, 'assets/js/custom-field.js'));

    echo "<option $data_ppt value='delivery_note' " . ($selected == 'delivery_note' ? 'selected' : '') . ">" . _l(DELIVERY_NOTE_MODULE_NAME) . "</option>";
}
