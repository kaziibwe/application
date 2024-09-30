<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_shift_setting(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('new_shift'); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<div id="table_view">
<table class="table dt-table">
 <thead>
 	<th><?php echo _l('shift_code'); ?></th>
    <th><?php echo _l('shift_name'); ?></th>
    <th><?php echo _l('shift_type'); ?></th>  
    <th><?php echo _l('department'); ?></th>
    <th><?php echo _l('position'); ?></th>
    <th><?php echo _l('add_from'); ?></th>
    <th><?php echo _l('date_create'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
    <?php foreach($shifts as $s){ ?>
    <tr>
       <td><a href="#" onclick="edit_shift_setting(this,<?php echo htmlspecialchars($s['id']); ?>); return false" data-shift_name="<?php echo htmlspecialchars($s['shift_name']); ?>" data-shift_code="<?php echo htmlspecialchars($s['shift_code']); ?>" data-shift_type="<?php echo htmlspecialchars($s['shift_type']); ?>" data-department="<?php echo htmlspecialchars($s['department']); ?>" data-position="<?php echo htmlspecialchars($s['position']); ?>" data-from_date="<?php echo _d($s['from_date']); ?>" data-to_date="<?php echo _d($s['to_date']); ?>"><?php echo htmlspecialchars($s['shift_code']); ?></a></td>

       <td><a href="#" onclick="edit_shift_setting(this,<?php echo htmlspecialchars($s['id']); ?>); return false" data-shift_name="<?php echo htmlspecialchars($s['shift_name']); ?>" data-shift_code="<?php echo htmlspecialchars($s['shift_code']); ?>" data-shift_type="<?php echo htmlspecialchars($s['shift_type']); ?>" data-department="<?php echo htmlspecialchars($s['department']); ?>" data-position="<?php echo htmlspecialchars($s['position']); ?>" data-from_date="<?php echo _d($s['from_date']); ?>" data-to_date="<?php echo _d($s['to_date']); ?>"><?php echo htmlspecialchars($s['shift_name']); ?></a></td>

       <td><?php echo _l($s['shift_type']); ?></td>
       <td><?php echo get_dpm_in_dayoff($s['department']); ?></td>
       <td><?php echo get_position_in_dayoff($s['position']); ?></td>
       <td><a href="<?php echo admin_url('hrm/member/'.$s["add_from"]); ?>">
                    <?php echo staff_profile_image($s['add_from'],[
                'staff-profile-image-small mright5',
                ], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => get_staff_full_name($s['add_from']),
                ]); ?>
                 </a>
       </td>
       <td><?php echo _d($s['date_create']); ?></td>
       <td>
         <a href="#" onclick="edit_shift_setting(this,<?php echo htmlspecialchars($s['id']); ?>); return false" data-shift_name="<?php echo htmlspecialchars($s['shift_name']); ?>" data-shift_code="<?php echo htmlspecialchars($s['shift_code']); ?>" data-shift_type="<?php echo htmlspecialchars($s['shift_type']); ?>" data-department="<?php echo htmlspecialchars($s['department']); ?>" data-position="<?php echo htmlspecialchars($s['position']); ?>" data-from_date="<?php echo _d($s['from_date']); ?>" data-to_date="<?php echo _d($s['to_date']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>
          <a href="<?php echo admin_url('hrm/delete_shift/'.$s['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
       </td>
    </tr>
    <?php } ?> 
 </tbody>
</table>
</div>       
<div class="hide" id="shift_setting">
        <?php echo form_open(admin_url('hrm/shifts'),array('id'=>'shift_f-form')); ?>
        <div class="content hrm-fullwidth">
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_shift'); ?></span>
                    <span class="add-title"><?php echo _l('new_shift'); ?></span>
                </h4>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional_shift"></div>   
                       <h4><?php echo _l('general_infor') ?></h4> 
                       <hr/>
                    </div>
                    <div class="col-md-4">
                        <?php echo render_input('shift_code','shift_code','') ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo render_input('shift_name','shift_name','') ?>
                    </div>
	 
                    <div class="col-md-6">
                    	<label for="shift_type" class="control-label"><?php echo _l('shift_type'); ?></label>
		                <select name="shift_type" class="selectpicker" id="shift_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
		                  <option value=""></option>                  
		                  <option value="part_office_time"><?php echo _l('part_office_time'); ?></option>
		                  <option value="overtime_shifts"><?php echo _l('overtime_shifts'); ?></option>
		                </select>
                    </div>
                    <div class="col-md-6 mbot15">
                    	<label for="department"><?php echo _l('department'); ?></label>
                        <select name="department" id="department" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('all'); ?>" data-hide-disabled="true">  
                         <option value=""></option> 
                          <?php foreach($departments as $dpm){ ?>
                            <option value="<?php echo htmlspecialchars($dpm['departmentid']); ?>"><?php echo htmlspecialchars($dpm['name']); ?></option>
                          <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-12 mbot15">
					
                    	<label for="position"><?php echo _l('position'); ?></label>
                        <select name="position" id="position" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('all'); ?>" data-hide-disabled="true">
                         <option value=""></option> 
                          <?php foreach($positions as $dpm){ ?>
                            <option value="<?php echo htmlspecialchars($dpm['position_id']); ?>"><?php echo htmlspecialchars($dpm['position_name']); ?></option>
                          <?php } ?>   
                        </select>
                    </div>

                    <div class="col-md-6">
                    	<?php echo render_date_input('from_date','apply_from_date',''); ?>
                    </div>
                    <div class="col-md-6">
                    	<?php echo render_date_input('to_date','to_date',''); ?>
                    </div>
                    <div class="col-md-12">
                    	<h4><?php echo _l('shifts_detail'); ?></h4>
                    	<hr/>
                    </div>
                    <div class="col-md-12 mleft10" id="example">
                    	
                    </div>
                    <?php echo form_hidden('shifts_detail'); ?>
                </div>
                    <button class="btn btn-info pull-right save_detail_shift"><?php echo _l('submit'); ?></button>
                
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        
    </div><!-- /.modal -->
</div>
<script>       
    function new_shift_setting(){
    	$('#additional_shift').html('');
    	$('#shift_setting input[name="shift_name"]').val('');
    	$('#shift_setting input[name="shift_code"]').val('');
    	$('#shift_setting select[name="shift_type"]').val('');
    	$('#shift_setting select[name="shift_type"]').change();
    	$('#shift_setting select[name="department"]').val('');
    	$('#shift_setting select[name="department"]').change();
    	$('#shift_setting select[name="position"]').val('');
    	$('#shift_setting select[name="position"]').change();
    	$('#shift_setting input[name="from_date"]').val('');
    	$('#shift_setting input[name="to_date"]').val('');
        $('#shift_setting').toggleClass('hide');
        $('#table_view').toggleClass('hide');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
        var dataObject = [
		  {
		    '<?php echo _l('detail'); ?>': '<?php echo _l('time_start_work'); ?>'
		   
		  },
		  {
		    '<?php echo _l('detail'); ?>': '<?php echo _l('time_end_work'); ?>'
		 
		  },
		  {
		    '<?php echo _l('detail'); ?>': '<?php echo _l('start_lunch_break_time'); ?>'
		  
		  },
		  {
		    '<?php echo _l('detail'); ?>': '<?php echo _l('end_lunch_break_time'); ?>'

		  }
		];

		var hotElement = document.querySelector('#example');
		var hotElementContainer = hotElement.parentNode;
		var hotSettings = {
		  data: dataObject,
		  columns: [
		  	{
		      data: '<?php echo _l('detail'); ?>',
		      type: 'text'
		    },
		    {
		      data: '<?php echo _l('monday'); ?>',
		      type: 'time',
		      timeFormat: 'H:mm',
		      correctFormat: true
		    },
		    {
		      data: '<?php echo _l('tuesday'); ?>',
		      type: 'time',
		      timeFormat: 'H:mm',
		      correctFormat: true
		    },
		    {
		      data: '<?php echo _l('wednesday'); ?>',
		      type: 'time',
		      timeFormat: 'H:mm',
		      correctFormat: true
		    },
		    {
		      data: '<?php echo _l('thursday'); ?>',
		      type: 'time',
		      timeFormat: 'H:mm',
		      correctFormat: true
		    },
		    {
		      data: '<?php echo _l('friday'); ?>',
		      type: 'time',
		      timeFormat: 'H:mm',
		      correctFormat: true
		    },
		    {
		      data: '<?php echo _l('saturday_even'); ?>',
		      type: 'time',
		      timeFormat: 'H:mm',
		      correctFormat: true
		    },
		    {
		      data: '<?php echo _l('saturday_odd'); ?>',
		      type: 'time',
		      timeFormat: 'H:mm',
		      correctFormat: true

		    },{
		      data: '<?php echo _l('sunday'); ?>',
		      type: 'time',
		      timeFormat: 'H:mm',
		      correctFormat: true
		    },
		  ],
		  licenseKey: 'non-commercial-and-evaluation',
		  stretchH: 'all',
		  width: '100%',
		  autoWrapRow: true,
		  rowHeights: 40,
		  height: 200,
		  maxRows: 22,
		  rowHeaders: [
		  	
		  ],
		  colHeaders: [
		  	'<?php echo _l('detail'); ?>',
		    '<?php echo _l('monday'); ?>',
		    '<?php echo _l('tuesday'); ?>',
		    '<?php echo _l('wednesday'); ?>',
		    '<?php echo _l('thursday'); ?>',
		    '<?php echo _l('friday'); ?>',
		    '<?php echo _l('saturday_even'); ?>',
		    '<?php echo _l('saturday_odd'); ?>',
		    '<?php echo _l('sunday'); ?>'
		  ],
		   columnSorting: {
		    indicator: true
		  },
		  autoColumnSize: {
		    samplingRatio: 23
		  },
		  dropdownMenu: true,
		  mergeCells: true,
		  contextMenu: true,
		  manualRowMove: true,
		  manualColumnMove: true,
		  multiColumnSorting: {
		    indicator: true
		  },
		  filters: true,
		  manualRowResize: true,
		  manualColumnResize: true
		};
        var hot = new Handsontable(hotElement, hotSettings);
        $('.save_detail_shift').on('click', function() {
		    $('input[name="shifts_detail"]').val(hot.getData());   
		});
		appValidateForm($('#shift_f-form'),{shift_name:'required',shift_type:'required'});
    }
    function edit_shift_setting(invoker,id){
    	$('#additional_shift').html('');
        $('#additional_shift').append(hidden_input('id',id));
        $('#shift_setting input[name="shift_name"]').val($(invoker).data('shift_name'));
        $('#shift_setting input[name="shift_code"]').val($(invoker).data('shift_code'));
        $('#shift_setting select[name="shift_type"]').val($(invoker).data('shift_type'));
    	$('#shift_setting select[name="shift_type"]').change();
    	$('#shift_setting select[name="department"]').val($(invoker).data('department'));
    	$('#shift_setting select[name="department"]').change();
    	$('#shift_setting select[name="position"]').val($(invoker).data('position'));
    	$('#shift_setting select[name="position"]').change();
    	$('#shift_setting input[name="from_date"]').val($(invoker).data('from_date'));
    	$('#shift_setting input[name="to_date"]').val($(invoker).data('to_date'));

        $('#example').html('');

        $.post(admin_url+'hrm/get_data_edit_shift/'+id).done(function(response){
            response = JSON.parse(response);
	        var dataObject = response.handson;

			var hotElement = document.querySelector('#example');
			var hotElementContainer = hotElement.parentNode;
			var hotSettings = {
			  data: dataObject,
			  columns:[
			  	{
			      data: '<?php echo _l('detail'); ?>',
			      type: 'text'
			    },
			    {
			      data: '<?php echo _l('monday'); ?>',
			      type: 'time',
			      timeFormat: 'H:mm',
			      correctFormat: true
			    },
			    {
			      data: '<?php echo _l('tuesday'); ?>',
			      type: 'time',
			      timeFormat: 'H:mm',
			      correctFormat: true
			    },
			    {
			      data: '<?php echo _l('wednesday'); ?>',
			      type: 'time',
			      timeFormat: 'H:mm',
			      correctFormat: true
			    },
			    {
			      data: '<?php echo _l('thursday'); ?>',
			      type: 'time',
			      timeFormat: 'H:mm',
			      correctFormat: true
			    },
			    {
			      data: '<?php echo _l('friday'); ?>',
			      type: 'time',
			      timeFormat: 'H:mm',
			      correctFormat: true
			    },
			    {
			      data: '<?php echo _l('saturday_even'); ?>',
			      type: 'time',
			      timeFormat: 'H:mm',
			      correctFormat: true
			    },
			    {
			      data: '<?php echo _l('saturday_odd'); ?>',
			      type: 'time',
			      timeFormat: 'H:mm',
			      correctFormat: true

			    },{
			      data: '<?php echo _l('sunday'); ?>',
			      type: 'time',
			      timeFormat: 'H:mm',
			      correctFormat: true
			    },
			  ],
			  licenseKey: 'non-commercial-and-evaluation',
			  stretchH: 'all',
			  width: '100%',
			  autoWrapRow: true,
			  rowHeights: 40,
			  height: 200,
			  maxRows: 22,
			  rowHeaders: [
			  	
			  ],
			  colHeaders: [
			  	'<?php echo _l('detail'); ?>',
			    '<?php echo _l('monday'); ?>',
			    '<?php echo _l('tuesday'); ?>',
			    '<?php echo _l('wednesday'); ?>',
			    '<?php echo _l('thursday'); ?>',
			    '<?php echo _l('friday'); ?>',
			    '<?php echo _l('saturday_even'); ?>',
			    '<?php echo _l('saturday_odd'); ?>',
			    '<?php echo _l('sunday'); ?>'
			  ],
			   columnSorting: {
			    indicator: true
			  },
			  autoColumnSize: {
			    samplingRatio: 23
			  },
			  dropdownMenu: true,
			  mergeCells: true,
			  contextMenu: true,
			  manualRowMove: true,
			  manualColumnMove: true,
			  multiColumnSorting: {
			    indicator: true
			  },
			  filters: true,
			  manualRowResize: true,
			  manualColumnResize: true
			};

	        var hot = new Handsontable(hotElement, hotSettings);
	         $('.save_detail_shift').on('click', function() {
			    $('input[name="shifts_detail"]').val(hot.getData());   
			});
        });
        $('#shift_setting').toggleClass('hide');
        $('#table_view').toggleClass('hide');
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
    }
</script>
</body>
</html>
