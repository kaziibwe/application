<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(),array('class'=>'payroll-type-form','autocomplete'=>'off')); ?>
        <div class="col-md-12">
            <div class="panel_s" >
                <?php if(isset($payslip_id)){ ?>
                <input type="hidden" name="payslip_id" value="<?php echo htmlspecialchars($payslip_id); ?>">
           <?php } else { ?>

                <input type="hidden" name="payslip_id" value="">
           <?php } ?>

                                         

                                   
                <div class="panel-body">
                                    <h4>
                                    <?php echo _l('payslip_record'); ?>
                                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                         <div class="form" id="new_payslip">
                            <div class="row">
							<br>
                                <div class="col-md-12">
                                    <h5 class="hrm-fontsize14"><?php echo _l('payslip') .' '. _l('month') .' : ' . date("m/Y", strtotime($payslip_month)); ?></h5>
                                </div>
							<br><br>
                                <div class="col-md-12">
                                   <a href="<?php echo admin_url('hrm/payroll_type/'.$payroll_type_id); ?>" class="hrm-fontsize14 hrm-colorblue" title= "<?php echo _l('detail') ?>"><?php echo _l('payroll_template_edit').': ' .$payslip_name; ?></a>
                                </div>
							<br><br>
                                    <hr class="hr-panel-heading">
                            </div>


                        </div>
                        </div>

                    <div class="modal-footer">
                       <a href="<?php echo admin_url('hrm/payroll?group=payslip'); ?>"  class="btn btn-default "><?php echo _l('close'); ?></a>
                       <?php if($latch == 0){ ?>
                        <button id="lacth_payslip" type="button" class="btn btn-info" onclick="latch_payslip(this); return false;" ><?php echo _l('latch'); ?></button>
                      <?php }?>
                    </div>
                    </div>
                </div><!-- /.modal-content -->
            </div>
                <?php echo form_close(); ?>


                    <!-- </div>
                </div>
            </div> -->
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
                    
  var column_value_CT = ['salary_insurance_',
                    'salary_allowance_tax',
                    'salary_allowance_no_taxable',
                    'penalty_timekeeping',
                    'effort_work_late',
                    'effort_work_early',
                    'effort_leave_without_reason',
                    'total_money',
                    'salary_transferred_company_account',
                    'salary_transferred_personal_account',
                    'salary_paid',
                    'unpaid_wages',
                    'total_income',
                    'income_taxes',
                    'personal_income_tax',
                    'formula',
                    'constant'
                    
                    ];
      var column_value_L =['work_number',
                      'date_entered_company',
                      'contract_code',
                      'month',
                      'year',
                      'date_total',
                      'sunday_total',
                      'saturday_total',
                      'saturday_total_odd',
                      'saturday_total_even',
                      'total_work_time',
                      'effort_ceremony',
                      'number_work_late',
                      'number_leave_company_early',
                      'number_minu_late',
                      'number_minu_early',
                      'number_effort_leave',
                      'number_effort_no_leave',
                      'effort_work',
                      'total_actual_working_hours',
                      'business_sales',
                      'actual_sales_turnover',
                      'Business_contract_number',
                      'business_order_number',
                      'salary_l',
                      'salary_alowance',
                      'individual_deduction_level',
                      'tax_exemption_level',
                      'hours_date',
                      'hours_week',
                      'work_time_by_round',
                      'total_work_time_by_round',
                      'effort_by_round',
                      'business_commission',
                      'salary_day',
                      'hours_salary',
                      'number_of_dependents',

                      ];

    <?php if(isset($data_object)){ ?>
      var dataObject = <?php echo json_encode($data_object) ; ?>;
    <?php }else{ ?>

    var  dataObject = <?php echo htmlspecialchars($payroll_tables); ?>;

  <?php } ?>

var hotElement = document.querySelector('#hot');



var hot = new Handsontable(hotElement, {
  dropdownMenu: true,
  mergeCells: true,
  contextMenu: true,
  manualRowMove: true,
  manualColumnMove: true,
  stretchH: 'all',
  autoWrapRow: true,
  rowHeights: 30,
  defaultRowHeight: 100,
  maxRows: 22,
  width: '100%',
  height:'500px',
  rowHeaders: true,
  autoColumnSize: {
    samplingRatio: 23
  },

  filters: true,
  manualRowResize: true,
  manualColumnResize: true,
  allowInsertRow: false,
  allowRemoveRow: false,
  columns: <?php echo htmlspecialchars($column); ?>,

  colHeaders: <?php echo htmlspecialchars($header); ?>,
  data: dataObject,

  cells: function (row, col, prop) {
    var cellProperties = {};
    var data = this.instance.getData();
    cellProperties.className = 'htMiddle ';
    
    if(column_value_CT.indexOf(prop) != -1){
      cellProperties.className += 'value-ct ';
      cellProperties.className += 'htRight ';

    }else if(column_value_L.indexOf(prop) != -1){
      cellProperties.className += 'value-l ';
      cellProperties.className += 'htRight ';
      
    }else{
      cellProperties.className += 'htLeft ';
    }
    
    return cellProperties;
  }

});


//export to csv

var button1 = document.getElementById('export-file');
  var exportPlugin1 = hot.getPlugin('exportFile');
  
  button1.addEventListener('click', function() {
    exportPlugin1.downloadFile('csv', {
      bom: true,
      columnDelimiter: ',',
      columnHeaders: true,
      exportHiddenColumns: true,
      exportHiddenRows: true,
      fileExtension: 'csv',
      filename: '<?php echo _l('payslip') ?>_[YYYY]-[MM]-[DD]',
      mimeType: 'text/csv',
      rowDelimiter: '\r\n',
      rowHeaders: false
    });
  });

function latch_payslip(invoker){
        var id = $('input[name = "payslip_id"]').val();
        if(id != ''){
          var formData = new FormData();
          formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
          formData.append("id", id);
            $.ajax({ 
                url: admin_url + 'hrm/latch_payslip', 
                method: 'post', 
                data: formData, 
                contentType: false, 
                processData: false
            }).done(function(response) {
            response = JSON.parse(response);
            if(response.success == true){
              $('#lacth_payslip').remove();
              alert_float('success', response.message);
            }else{
              alert_float('warning', response.message);
            }
        });
        }
    }



$('#hot-display-license-info').empty();
  
</script>
</body>
</html>
