<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                 
                     <div class="border-right">
                      <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
                      <hr />
                    </div>
                   <div class="row">
                  <div class="col-md-3">
                    <?php echo render_select('status',$status,array('id','name'),'project_status','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
                  </div>
                  <div class="col-md-3">
                          <?php echo render_date_input('from_date','from_date',''); ?>
                  </div>
                  <div class="col-md-3">
                          <?php echo render_date_input('to_date','to_date',''); ?>
                  </div>
                </div>
                   <br>
                  <?php render_datatable(array(
                         _l('the_number_sign'),
                         _l('project_name'),
                         _l('project_start_date'),
                         _l('project_deadline'),
                         _l('project_member'),
                         _l('project_status'),
                         _l('options')
                        ),'projectroadmap'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
</body>
</html>