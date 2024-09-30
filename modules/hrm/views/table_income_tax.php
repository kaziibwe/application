<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
    'staff_identifi',
    db_prefix().'staff.staffid',
    ];
$sIndexColumn = 'staffid';
$sTable       = db_prefix().'staff';

$where = [];

$department_id = $this->ci->input->post('hrm_deparment');

if(isset($department_id) && strlen($department_id) > 0){
    $join = [

        'LEFT JOIN '.db_prefix().'staff_departments ON '.db_prefix().'staff_departments.staffid = '.db_prefix().'staff.staffid',
        'LEFT JOIN '.db_prefix().'roles ON '.db_prefix().'roles.roleid = '.db_prefix().'staff.role' ,
       
        ];
}else {
    $join = [

        'LEFT JOIN '.db_prefix().'roles ON '.db_prefix().'roles.roleid = '.db_prefix().'staff.role' ,
       
        ];
}

if(isset($department_id) && strlen($department_id) > 0){

    $where[] = ' AND departmentid IN (select 
        departmentid 
        from    (select * from '.db_prefix().'departments
        order by '.db_prefix().'departments.parent_id, '.db_prefix().'departments.departmentid) departments_sorted,
        (select @pv := '.$department_id.') initialisation
        where   find_in_set(parent_id, @pv)
        and     length(@pv := concat(@pv, ",", departmentid)) OR departmentid = '.$department_id.')';
}

if($this->ci->input->post('staff_role')){
    $where_role = '';
    $staff_role      = $this->ci->input->post('staff_role');
        foreach ($staff_role as $staff_id) {
            if($staff_id != '')
            {
                if($where_role == ''){
                    $where_role .= '( '.db_prefix().'staff.role = '.$staff_id;
                }else{
                    $where_role .= ' or '.db_prefix().'staff.role = '.$staff_id;
                }
            }
        }

         if($where_role != '')
        {
            $where_role .= ' )';
            if($where_role != ''){
                array_push($where, 'AND '. $where_role);
            }else{
                array_push($where, $where_role);
            }

        }
            
}

$staff_id = $this->ci->input->post('hrm_staff');
if(isset($staff_id) && $staff_id != ''){
    $where_staff = ' AND '.db_prefix().'staff.staffid in ('.$staff_id.')';
        
    array_push($where, $where_staff);
        
}

$from_month = $this->ci->input->post('hrm_from_month');
$from_year = $this->ci->input->post('hrm_from_year');

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'staff.staffid','staff_identifi','firstname','lastname']);

$output  = $result['output'];
$rResult = $result['rResult'];
function get_payment_company($premium, $social_company, $labor_accident_company, $health_company, $unemployment_company ){
    return (($premium * $social_company)+ ($premium * $labor_accident_company)+($premium * $health_company)+($premium * $unemployment_company))/100;
}

function get_payment_worker($premium, $social_staff, $labor_accident_staff, $health_staff, $unemployment_staff ){
    return (($premium * $social_staff)+ ($premium * $labor_accident_staff)+($premium * $health_staff)+($premium * $unemployment_staff))/100;
}

if($staff_id == ''){

    foreach ($rResult as $aRow) {

        $current_year = $from_year;
        $current_month = '';
        $payslip = $this->ci->hrm_model->get_paysplip_all_month($aRow[db_prefix().'staff.staffid'], $current_year, $current_month, $aRow['staff_identifi']);
        $per_deduciton = get_hrm_option('per_deduciton')*12;

        $dependent_person = $this->ci->hrm_model->count_dependent_person($aRow[db_prefix().'staff.staffid'], $current_year, $current_month, $aRow['staff_identifi']);
        $exemption_person = $dependent_person;

        //total salary alowance

        if($payslip['salary_alowance'] == 1){
            $total_salary_alowance = $this->ci->hrm_model->salary_alowance_by_month($aRow[db_prefix().'staff.staffid'], $current_year, $current_month);
        }else{
            $total_salary_alowance = 0;
        }


        $insurance_deduction = 0;


        $taxable_income  = (float)$payslip['total_money'] - (float)$total_salary_alowance;

        $total_taxable_income = $taxable_income - ((float)$per_deduciton*12 + (float)$exemption_person + (float)$insurance_deduction);

        $tax_rate_option = json_decode(get_hrm_option('tax_rate'), true);
        $flag_tax_rate = 0;
        foreach ($tax_rate_option as $tax_rate_value) {
            if($tax_rate_value['tax_rate_to'] != 0){
                if($tax_rate_value['tax_rate_from'] <= $total_taxable_income && $total_taxable_income < $tax_rate_value['tax_rate_to']){

                    $personal_income_tax = ($total_taxable_income*$tax_rate_value['tax_value'])/100-(float)$tax_rate_value['tax_rate_from'];
                    $flag_tax_rate = 1;
                }

            }else{
                if($tax_rate_value['tax_rate_from'] <= $total_taxable_income ){

                    $personal_income_tax = ($total_taxable_income*$tax_rate_value['tax_value'])/100-(float)$tax_rate_value['tax_rate_from'];
                    $flag_tax_rate = 1;
                }
            }

        }

        if($flag_tax_rate == 0){
            $personal_income_tax = 0;
        }


        if($payslip['personal_income_tax'] != $personal_income_tax){
            $company_pay =  0;   
            $not_company_pay =  $personal_income_tax; 
        }else{
            $company_pay =  $personal_income_tax;   
            $not_company_pay =  0; 
        }



        $row = [];

         $row[] = $aRow['staff_identifi'] ;
         $row[] = $aRow['firstname'].' '.$aRow['lastname']; 
         $row[] = (float)$payslip['total_money'];
         $row[] = (float)$total_salary_alowance ;
         $row[] = app_format_money((float)$taxable_income,''); 
         $row[] = app_format_money($per_deduciton,'');
         $row[] = app_format_money((float)$exemption_person,'');
         $row[] = app_format_money((float)$insurance_deduction,'') ;
         $row[] = app_format_money((float)$total_taxable_income,'') ;
         $row[] = app_format_money((float)$personal_income_tax,'') ;
         $row[] = app_format_money($company_pay,'') ;
         $row[] = app_format_money($not_company_pay,'') ;
        $output['aaData'][] = $row;
    }

}else if($staff_id != '' && ( $from_month == '' || $from_month == null)){
    for($m = 1 ; $m <= 12; $m++){
        $_month = date('m',mktime(0, 0, 0, $m, 04, 2016));
        foreach ($rResult as $aRow) {

            $current_year = $from_year;
            $current_month = $_month;
            $payslip = $this->ci->hrm_model->get_paysplip_all_month($aRow[db_prefix().'staff.staffid'], $current_year, $current_month, $aRow['staff_identifi']);

            $per_deduciton = get_hrm_option('per_deduciton');


            $dependent_person = $this->ci->hrm_model->count_dependent_person($aRow[db_prefix().'staff.staffid'], $current_year, $current_month, $aRow['staff_identifi']);
            $exemption_person = $dependent_person;

            //total salary alowance

            if($payslip['salary_alowance'] == 1){
                $total_salary_alowance = $this->ci->hrm_model->salary_alowance_by_month($aRow[db_prefix().'staff.staffid'], $current_year, $current_month);
            }else{
                $total_salary_alowance = 0;
            }


            $insurance_deduction = 0;

            $taxable_income  = (float)$payslip['total_money'] - (float)$total_salary_alowance;
            $total_taxable_income = $taxable_income - ((float)$per_deduciton + (float)$exemption_person + (float)$insurance_deduction);

            $tax_rate_option = json_decode(get_hrm_option('tax_rate'), true);
            $flag_tax_rate = 0;
            foreach ($tax_rate_option as $tax_rate_value) {
                if($tax_rate_value['tax_rate_to'] != 0){
                    if($tax_rate_value['tax_rate_from'] <= $total_taxable_income && $total_taxable_income < $tax_rate_value['tax_rate_to']){

                        $personal_income_tax = ($total_taxable_income*$tax_rate_value['tax_value'])/100-(float)$tax_rate_value['tax_rate_from'];
                        $flag_tax_rate = 1;
                    }

                }else{
                    if($tax_rate_value['tax_rate_from'] <= $total_taxable_income ){

                        $personal_income_tax = ($total_taxable_income*$tax_rate_value['tax_value'])/100-(float)$tax_rate_value['tax_rate_from'];
                        $flag_tax_rate = 1;
                    }
                }

            }

            if($flag_tax_rate == 0){
                $personal_income_tax = 0;
            }



            if($payslip['personal_income_tax'] != $personal_income_tax){
                $company_pay =  0;   
                $not_company_pay =  $personal_income_tax; 
            }else{
                $company_pay =  $personal_income_tax;   
                $not_company_pay =  0; 
            }



            $row = [];

             $row[] = $current_month.'/'.$current_year ;
             $row[] = $aRow['firstname'].' '.$aRow['lastname']; 
             $row[] = (float)$payslip['total_money'];
             $row[] = (float)$total_salary_alowance ;
             $row[] = app_format_money((float)$taxable_income,''); 
             $row[] = app_format_money($per_deduciton,'');
             $row[] = app_format_money((float)$exemption_person,'');
             $row[] = app_format_money((float)$insurance_deduction,'') ;
             $row[] = app_format_money((float)$total_taxable_income,'') ;
             $row[] = app_format_money((float)$personal_income_tax,'') ;
             $row[] = app_format_money($company_pay,'') ;
             $row[] = app_format_money($not_company_pay,'') ;

            $output['aaData'][] = $row;
        }

    }
}else{

    foreach ($rResult as $aRow) {


            $current_year = $from_year;
            $current_month = $from_month;
            $payslip = $this->ci->hrm_model->get_paysplip_all_month($aRow[db_prefix().'staff.staffid'], $current_year, $current_month, $aRow['staff_identifi']);

            $per_deduciton = get_hrm_option('per_deduciton');


            $dependent_person = $this->ci->hrm_model->count_dependent_person($aRow[db_prefix().'staff.staffid'], $current_year, $current_month, $aRow['staff_identifi']);
            $exemption_person = $dependent_person;


            if($payslip['salary_alowance'] == 1){
                $total_salary_alowance = $this->ci->hrm_model->salary_alowance_by_month($aRow[db_prefix().'staff.staffid'], $current_year, $current_month);
            }else{
                $total_salary_alowance = 0;
            }

            $insurance_deduction = 0;

            $taxable_income  = (float)$payslip['total_money'] - (float)$total_salary_alowance;
            $total_taxable_income = $taxable_income - ((float)$per_deduciton + (float)$exemption_person + (float)$insurance_deduction);

            $tax_rate_option = json_decode(get_hrm_option('tax_rate'), true);
            $flag_tax_rate = 0;
            foreach ($tax_rate_option as $tax_rate_value) {
                if($tax_rate_value['tax_rate_to'] != 0){
                    if($tax_rate_value['tax_rate_from'] <= $total_taxable_income && $total_taxable_income < $tax_rate_value['tax_rate_to']){

                        $personal_income_tax = ($total_taxable_income*$tax_rate_value['tax_value'])/100-(float)$tax_rate_value['tax_rate_from'];
                        $flag_tax_rate = 1;
                    }

                }else{
                    if($tax_rate_value['tax_rate_from'] <= $total_taxable_income ){

                        $personal_income_tax = ($total_taxable_income*$tax_rate_value['tax_value'])/100-(float)$tax_rate_value['tax_rate_from'];
                        $flag_tax_rate = 1;
                    }
                }

            }

            if($flag_tax_rate == 0){
                $personal_income_tax = 0;
            }

      
            if($payslip['personal_income_tax'] != $personal_income_tax){
                $company_pay =  0;   
                $not_company_pay =  $personal_income_tax; 
            }else{
                $company_pay =  $personal_income_tax;  
                $not_company_pay =  0;
            }



            $row = [];

             $row[] = $aRow['staff_identifi'] ;
             $row[] = $aRow['firstname'].' '.$aRow['lastname']; 
             $row[] = (float)$payslip['total_money'];
             $row[] = (float)$total_salary_alowance ;
             $row[] = app_format_money((float)$taxable_income,''); 
             $row[] = app_format_money($per_deduciton,'');
             $row[] = app_format_money((float)$exemption_person,'');
             $row[] = app_format_money((float)$insurance_deduction,'') ;
             $row[] = app_format_money((float)$total_taxable_income,'') ;
             $row[] = app_format_money((float)$personal_income_tax,'') ;
             $row[] = app_format_money($company_pay,'') ;
             $row[] = app_format_money($not_company_pay,'') ;
            $output['aaData'][] = $row;
        }
}