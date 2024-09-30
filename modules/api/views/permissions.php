<?php $api_permissions_count = count(get_available_api_permissions()); ?>

<div class="row">
   <div class="col-md-12">      
      <?php echo _l('permissions'); ?>
   </div>
</div>

<div class="row">
   <div class="col-md-6">
      <div class="panel_s">
         <div class="panel-body">
            <div class="table-responsive">
               <table class="table table-bordered roles no-margin">
                  <thead>
                     <tr>
                        <th>Feature</th>
                        <th>Capabilities</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                        $api_permission_index = 0;
                        foreach (get_available_api_permissions() as $feature => $permission) {
                           $api_permission_index += 1;
                           if ($api_permission_index >= floor($api_permissions_count / 2)) continue;
                           ?>
                           <tr data-name="<?php echo $feature; ?>">
                              <td>
                                 <b><?php echo $permission['name']; ?></b>
                              </td>
                              <td>
                                 <?php
                                    foreach ($permission['capabilities'] as $capability => $name) {
                                       $checked  = '';
                                       if (isset($user_api) && api_can($user_api['id'] ?? '', $feature, $capability)) {
                                          $checked = ' checked ';
                                       }
                                       ?>
                                       <div class="checkbox" style="padding-left: 20px">
                                          <input type="checkbox" <?php echo $checked; ?> class="capability" id="<?php echo $feature . '_' . $capability; ?>" name="permissions[<?php echo $feature; ?>][]" value="<?php echo $capability; ?>">
                                          <label for="<?php echo $feature . '_' . $capability; ?>"> <?php echo $name; ?></label>
                                       </div>
                                       <?php
                                    }
                                 ?>
                              </td>
                           </tr>
                        <?php }
                     ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-6">
      <div class="panel_s">
         <div class="panel-body">
            <div class="table-responsive">
               <table class="table table-bordered roles no-margin">
                  <thead>
                     <tr>
                        <th>Feature</th>
                        <th>Capabilities</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                        $api_permission_index = 0;
                        foreach (get_available_api_permissions() as $feature => $permission) {
                           $api_permission_index += 1;
                           if ($api_permission_index < floor($api_permissions_count / 2)) continue;
                           ?>
                           <tr data-name="<?php echo $feature; ?>">
                              <td>
                                 <b><?php echo $permission['name']; ?></b>
                              </td>
                              <td>
                                 <?php
                                    foreach ($permission['capabilities'] as $capability => $name) {
                                       $checked  = '';
                                       if (isset($user_api) && api_can($user_api['id'] ?? '', $feature, $capability)) {
                                          $checked = ' checked ';
                                       }
                                       ?>
                                       <div class="checkbox" style="padding-left: 20px">
                                          <input type="checkbox" <?php echo $checked; ?> class="capability" id="<?php echo $feature . '_' . $capability; ?>" name="permissions[<?php echo $feature; ?>][]" value="<?php echo $capability; ?>">
                                          <label for="<?php echo $feature . '_' . $capability; ?>"> <?php echo $name; ?></label>
                                       </div>
                                       <?php
                                    }
                                 ?>
                              </td>
                           </tr>
                        <?php }
                     ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>