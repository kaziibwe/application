<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
    'staffid',
    'staff_name',
    'tax_code',
    'total_money',
    'total_deduciton_alowance',
    'total_taxable_Incomes',
    'total_deduciton_person',
    'total_deduciton_depen',
    'total_abatement_Sihiui',
    'total_assessable_income',
    'personal_income_tax_finalisation',

    ];
$sIndexColumn = 'year';
$sTable       = db_prefix().'tax_finalisation';
$join = [];
$where = [];

$from_year = $this->ci->input->post('hrm_from_year');

if(isset($from_year) && $from_year != ''){
    $where_from_year = ' AND date_format(year, "%Y") = '.$from_year;
        
    array_push($where, $where_from_year);
        
}

$hrm_staff = $this->ci->input->post('hrm_staff');
if(isset($hrm_staff) && $hrm_staff != ''){
    $where_hrm_staff = ' AND staffid = '.$hrm_staff;
        
    array_push($where, $where_hrm_staff);
        
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    
    $row = [];
     $row[] = $aRow['staffid'] ;
     $row[] = $aRow['staff_name']; 
     $row[] = $aRow['tax_code']; 
     $row[] = app_format_money((float)$aRow['total_money'],''); 
     $row[] = app_format_money((float)$aRow['total_deduciton_alowance'],''); 
     $row[] = app_format_money((float)$aRow['total_taxable_Incomes'],''); 
     $row[] = app_format_money((float)$aRow['total_deduciton_person'],'');  
     $row[] = app_format_money((float)$aRow['total_deduciton_depen'],'');  
     $row[] = app_format_money((float)$aRow['total_abatement_Sihiui'],'');  
     $row[] = app_format_money((float)$aRow['total_assessable_income'],'');  
     $row[] = app_format_money((float)$aRow['personal_income_tax_finalisation'],'');  
     
    $output['aaData'][] = $row;
}
