<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
<div class="_buttons">
    <?php if(is_admin()){ ?>
        <a href="<?php echo admin_url('hrm/payroll_type'); ?>" class="btn btn-info mright5 pull-left display-block"><?php echo _l('payroll_type_add'); ?></a>
    <?php } ?>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>

    <th><?php echo _l('id'); ?></th>
    <th><?php echo _l('payroll_table'); ?></th>
    <th><?php echo _l('role'); ?></th>
    <th><?php echo _l('bl'); ?></th>
    <th><?php echo _l('manager'); ?></th>
    <th><?php echo _l('follower'); ?></th>
    <th><?php echo _l('options'); ?></th>

 </thead>
 <tbody>
    <?php foreach($payroll_type as $pr){ ?>
    <tr> 
        <?php 


         // get role name from array role id
          $rolenames ='';
           $role_id = json_decode($pr['role_id']);
           $total_role_name = $this->roles_model->get();

          if(isset($role_id) && count($role_id) != 0){
              if( count($role_id) == count($total_role_name) ){
                $rolenames = '<span class="label label-tag tag-id-1"><span class="tag">'._l('all').'</span><span class="hide">, </span></span>&nbsp';
              }else{
                $str_rolename = '';
                $role_index = 0;
           foreach ($role_id as $roleid) {
                $rolevalue = $this->roles_model->get($roleid);
                $role_name = isset($rolevalue->name) ? $rolevalue->name : '' ;
                $role_index++;
                    $str_rolename .= '<span class="label label-tag tag-id-1"><span class="tag">'.$role_name.'</span><span class="hide">, </span></span>&nbsp';
                    if($role_index%2 == 0){
                         $str_rolename .= '<br><br/>';
                    }
                }
                $rolenames = $str_rolename;
              }

         }else{
            $rolenames='';
         }

          
          // get position name from array position id
          $position_names ='';
          $position_id = json_decode($pr['position_id']);
          $total_position_name = $this->hrm_model->get_job_position();

          if(isset($position_id) && count($position_id) != 0){
              if( count($position_id) == count($total_position_name) ){
                $position_names = '<span class="label label-tag tag-id-1"><span class="tag">'._l('all').'</span><span class="hide">, </span></span>&nbsp';
              }else{
                $str_positionname = '';
                $position_index = 0;
           foreach ($position_id as $positionid) {
                $rolevalue = $this->roles_model->get($positionid);
                $role_name = isset($rolevalue->name) ? $rolevalue->name : '' ;
                $position_index++;
                    $str_positionname .= '<span class="label label-tag tag-id-1"><span class="tag">'.$role_name.'</span><span class="hide">, </span></span>&nbsp';
                    if($position_index%2 == 0){
                         $str_positionname .= '<br><br/>';
                    }
                }
                $position_names = $str_positionname;
              }

         }else{
            $position_names='';
         }


           if(isset($pr['salary_form_id']) ){
             if( $pr['salary_form_id'] == 1){
              $salary_name = _l('primary');
             }else{
              $salary_name = _l('alowance');
             }
           }else{
              $salary_name = '';
           }
        ?>
       <td><?php echo htmlspecialchars($pr['id']); ?></td>
       <td><?php echo htmlspecialchars($pr['payroll_type_name']); ?></td>
       <td><?php echo ''.$rolenames; ?></td>
       <td><?php echo htmlspecialchars($salary_name); ?></td>
       <td><?php echo get_staff_full_name($pr['manager_id']); ?></td>
       <td><?php echo get_staff_full_name($pr['follower_id']); ?></td>
       <td>
         <a href="<?php echo admin_url('hrm/payroll_type/'.$pr['id']); ?>" class="btn btn-default btn-icon" title= "<?php echo _l('update') ?>"><i class="fa fa-pencil-square"></i></a>
         <a href="<?php echo admin_url('hrm/delete_payroll_type/'.$pr['id']); ?>" class="btn btn-danger btn-icon _delete" title= "<?php echo _l('delete') ?>"><i class="fa fa-remove"></i></a>
       </td>
    </tr>
    <?php } ?>
 </tbody>
</table>       
</div>
</body>
</html>
