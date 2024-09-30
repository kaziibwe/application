<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 * Check whether column exists in a table
 * Custom function because Codeigniter is caching the tables and this is causing issues in migrations
 * @param  string $column column name to check
 * @param  string $table table name to check
 * @return boolean
 */


function get_hrm_option($name)
{
	$CI = & get_instance();
	$options = [];
    $val  = '';
    $name = trim($name);
    

    if (!isset($options[$name])) {
        // is not auto loaded
        $CI->db->select('option_val');
        $CI->db->where('option_name', $name);
        $row = $CI->db->get(db_prefix() . 'hrm_option')->row();
        if ($row) {
            $val = $row->option_val;
        }
    } else {
        $val = $options[$name];
    }

    return hooks()->apply_filters('get_hrm_option', $val, $name);
}
function row_options_exist($name){
    $CI = & get_instance();
    $i = count($CI->db->query('Select * from '.db_prefix().'hrm_option where option_name = '.$name)->result_array());
    if($i == 0){
        return 0;
    }
    if($i > 0){
        return 1;
    }
}

function handle_hrm_attachments_array($staffid, $index_name = 'attachments')
{
    $uploaded_files = [];
    $path           = HRM_MODULE_UPLOAD_FOLDER.'/'.$staffid .'/';
    $CI             = &get_instance();
    if (isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (_perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                $newFilePath = $path . $filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, [
                    'file_name' => $filename,
                    'filetype'  => $_FILES[$index_name]['type'][$i],
                    ]);
                    if (is_image($newFilePath)) {
                        create_img_thumb($path, $filename);
                    }
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}
function render_hrm_yes_no_option($option_value, $label, $tooltip = '', $replace_yes_text = '', $replace_no_text = '', $replace_1 = '', $replace_0 = '')
{
    ob_start(); ?>
    <div class="form-group">
        <label for="<?php echo htmlspecialchars($option_value); ?>" class="control-label clearfix">
            <?php echo($tooltip != '' ? '<i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l($tooltip, '', false) . '"></i> ': '') . _l($label, '', false); ?>
        </label>
        <div class="radio radio-primary radio-inline">
            <input type="radio" id="y_opt_1_<?php echo htmlspecialchars($label); ?>" name="hrm_setting[<?php echo htmlspecialchars($option_value); ?>]" value="<?php echo htmlspecialchars($replace_1) == '' ? 1 : $replace_1; ?>" <?php if (get_hrm_option($option_value) == ($replace_1 == '' ? '1' : $replace_1)) {
        echo 'checked';
    } ?>>
            <label for="y_opt_1_<?php echo htmlspecialchars($label); ?>">
                <?php echo htmlspecialchars($replace_yes_text) == '' ? _l('settings_yes') : $replace_yes_text; ?>
            </label>
        </div>
        <div class="radio radio-primary radio-inline">
                <input type="radio" id="y_opt_2_<?php echo htmlspecialchars($label); ?>" name="hrm_setting[<?php echo htmlspecialchars($option_value); ?>]" value="<?php echo htmlspecialchars($replace_0) == '' ? 0 : $replace_0; ?>" <?php if (get_hrm_option($option_value) == ($replace_0 == '' ? '0' : $replace_0)) {
        echo 'checked';
    } ?>>
                <label for="y_opt_2_<?php echo htmlspecialchars($label); ?>">
                    <?php echo htmlspecialchars($replace_no_text) == '' ? _l('settings_no') : $replace_no_text; ?>
                </label>
        </div>
    </div>
    <?php
    $settings = ob_get_contents();
    ob_end_clean();
    echo htmlspecialchars($settings);
}

function get_dpm_in_dayoff($id){
    $CI             = &get_instance();
    if($id != 0){
        $dpm = $CI->db->query('select name from '.db_prefix().'departments where departmentid = '.$id)->row();
        return $dpm->name;
    }else{
        return _l('all');
    }
}

function get_position_in_dayoff($id){
    $CI             = &get_instance();
    if($id != 0){
        $pss = $CI->db->query('select position_name from '.db_prefix().'job_position where position_id = '.$id)->row();
        return $pss->position_name;
    }else{
        return _l('all');
    }
}

function get_status_modules($module_name){
    $CI             = &get_instance();
    $CI->db->where('module_name',$module_name);
    $module = $CI->db->get(db_prefix().'modules')->row();
    
}

function get_time_rp($id,$type){
    $CI             = &get_instance();
    $CI->db->where('request_id',$id);
    $CI->db->where('name',$type);
    $time = $CI->db->get(db_prefix().'request_form')->row();
    if(isset($time->value))
    {
        return $time->value;
    }else{
        return '';
    }
    
}

function sort_array_by_char($arr_data, $char, $order = 'desc'){
    $arr_cal = array();
    for($i=0;$i<count($arr_data);$i++){
        $c = substr_count($arr_data[$i],$char);
        if(!isset($arr_cal[$c])){
        $arr_cal[$c]=array();
        }
        $arr_cal[$c][] = $arr_data[$i];
    }
    if($order == 'desc'){
        asort($arr_cal);
    } else {
        ksort($arr_cal);
    }
    $res = array();
    foreach($arr_cal as $key => $value){
    $res = array_merge($res, $value);
    }
    
    return $res;
}

function get_hrm_department_name_by_staffid($staff_id = ''){
    if($staff_id == ''){
        $staff_id = get_staff_user_id();        
    }
    $CI = & get_instance();
    $arr_dept = $CI->db->query('select d.name from tblstaff s 
        left join tblstaff_departments sd on s.staffid = sd.staffid
        left join tbldepartments d on sd.departmentid = d.departmentid
        where s.staffid = '.$staff_id)->result_array();
    if(count($arr_dept) > 0){
        return $arr_dept[0]['name'];
    }
    return '';
}