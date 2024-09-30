<?php init_head(); ?>
<script src="<?php echo module_dir_url('resourcebooking','assets/js/highcharts.js'); ?>"></script>
<script src="<?php echo module_dir_url('resourcebooking','assets/js/modules/exporting.js'); ?>"></script>
<script src="<?php echo module_dir_url('resourcebooking','assets/js/modules/export-data.js'); ?>"></script>
<div id="wrapper">
   <div class="content">
   	<div class="row">
	 <div class="col-md-12">
	    <div class="panel_s">
	       <div class="panel-body">
	          <div class="row">
	             <div class="col-md-3">
	              <br><h4 class="no-margin font-bold"><i class="fa fa-bar-chart" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
	             <hr />
	            </div>
                <div class="col-md-3">
                    <label for="resource_group"><?php echo _l('resource_group'); ?></label>
                  <select name="resource_group" id="resource_group" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                    <option value=""></option>
                    <?php foreach($resource_group as $rg){ ?>
                    <option value="<?php echo htmlspecialchars($rg['id']); ?>" <?php if(isset($booking) && $booking->resource_group =$rg['id'] ){echo 'selected';} ?>><?php echo htmlspecialchars($rg['group_name']); ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-3">
                   <label for="resource"><?php echo _l('resource'); ?></label>
                  <select name="resource" id="resource" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                    <option value=""></option>
                    <?php foreach($resources as $rs){ ?>
                    <option value="<?php echo htmlspecialchars($rs['id']); ?>"<?php if(isset($booking) && $booking->resource = $rs['id'] ){echo 'selected';} ?>><?php echo htmlspecialchars($rs['resource_name']); ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-3">
                   <label for="month" class="control-label">
                      <?php echo _l('month'); ?>
                  </label>
                  <select name="month[]" id="month" class="form-control selectpicker" multiple="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-width="100%">
                     
                      <?php for($i=1; $i<13; $i++) {?>
                        <option value="<?php echo htmlspecialchars($i); ?>"><?php echo _l('month_'.$i); ?></option>
                      <?php } ?>
                  </select> 
                </div>

	          </div>
              <hr />
	          <div class="row">
				<div class="col-md-12" id="container3" ></div>
	          </div> 
	          <hr>
	          <div class="row">
	          	<div class="col-md-6" id="container2" ></div>
				<div class="col-md-6" id="container1" ></div>
	          </div>        
	       </div>
	    </div>
	 </div>
</div>	
   	</div>
</div>
<?php 
$month = [];
for($i=1; $i<13; $i++) {
    array_push($month, _l('month_'.$i));
} ?>
<?php init_tail(); ?>
</body>
</html>
<script>
var chart1 = new Highcharts.chart('container1', {
chart: {
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie'
},
credits: {
        enabled: false
    },
title: {
    text: <?php echo json_encode(_l('group_overview')); ?>
},
tooltip: {
    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
},
plotOptions: {
    pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
            enabled: false
        },
        showInLegend: true
    }
},
series: [{
    name: <?php echo json_encode(_l('booking_rate')) ?>,
    colorByPoint: true,
    data: <?php echo $pie; ?>
}]
});
var chart2 = new Highcharts.chart('container2', {
    chart: {
        zoomType: 'xy'
    },
    title: {
        text: <?php echo json_encode(_l('time_use_hour(group)')); ?>
    },
    credits: {
        enabled: false
    },
    xAxis: [{
        categories: <?php echo $col_line_name; ?>,
        crosshair: true
    }],
    yAxis: [{ // Primary yAxis
        labels: {
            format: '{value}',
            style: {
                color: Highcharts.getOptions().colors[4]
            }
        },
        title: {
            text: <?php echo json_encode(_l('hour_use')); ?>,
            style: {
                color: Highcharts.getOptions().colors[4]
            }
        }
    }, { // Secondary yAxis
        title: {
            text: <?php echo json_encode(_l('time_use')); ?>,
            style: {
                color: Highcharts.getOptions().colors[5]
            }
        },
        labels: {
            format: '{value}',
            style: {
                color: Highcharts.getOptions().colors[5]
            }
        },
        opposite: true
    }],
    tooltip: {
        shared: true
    },
    
    series: [{
        name: <?php echo json_encode(_l('time_use')); ?>,
        type: 'column',
        yAxis: 1,
        data: <?php echo $col_line_col; ?>,
        color: Highcharts.getOptions().colors[5]
    }, {
        name: <?php echo json_encode(_l('hour_use')); ?>,
        type: 'spline',
        data: <?php echo $col_line_line; ?>,
        color: Highcharts.getOptions().colors[4]
        
    }]
});
var chart3 = new Highcharts.chart('container3', {
    chart: {
        zoomType: 'xy'
    },
    credits: {
        enabled: false
    },
    title: {
        text: <?php echo json_encode(_l('time_use_hour(month)')); ?>
    },
    xAxis: [{
        categories: <?php echo json_encode($month); ?>,
        crosshair: true
    }],
    yAxis: [{ // Primary yAxis
        labels: {
            format: '{value}',
            style: {
                color: Highcharts.getOptions().colors[4]
            }
        },
        title: {
            text: <?php echo json_encode(_l('hour_use')); ?>,
            style: {
                color: Highcharts.getOptions().colors[4]
            }
        }
    }, { // Secondary yAxis
        title: {
            text:  <?php echo json_encode(_l('time_use')); ?>,
            style: {
               color: Highcharts.getOptions().colors[5]
            }
        },
        labels: {
            format: '{value}',
            style: {
                color: Highcharts.getOptions().colors[5]
            }
        },
        opposite: true
    }],
    tooltip: {
        shared: true
    },
    series: [{
        name:  <?php echo json_encode(_l('time_use')); ?>,
        type: 'column',
        yAxis: 1,
        data: <?php echo $month_col; ?>,
        color: Highcharts.getOptions().colors[5],
       
    }, {
        name: <?php echo json_encode(_l('hour_use')); ?>,
        type: 'spline',
        data: <?php echo $month_line; ?>,
        color: Highcharts.getOptions().colors[4]
        
    }]
});


$('#resource_group').on('change', function(){
  if(this.value != 0 && this.value != '') {
      $.post(admin_url+'resourcebooking/get_resource_by_group/'+this.value).done(function(response){
         response = JSON.parse(response);
         $("#resource").html('');
         $html = '<option value=""></option>';
         $.each(response.cont,function(){
            $html += '<option value="'+ this.id +'">'+ this.resource_name +'</option>';
         });
         $("#resource").html($html);
         $("#resource").selectpicker('refresh');
      });
    }else{
        $.post(admin_url+'resourcebooking/get_resource').done(function(response){
            response = JSON.parse(response);
            $html = '<option value=""></option>';
            $.each(response.cont,function(){
                $html += '<option value="'+ this.id +'">'+ this.resource_name +'</option>';
             });
             $("#resource").html($html);
             $("#resource").selectpicker('refresh');
        });
    }
  if(this.value != 0 && this.value != ''){
    $.post(admin_url+'resourcebooking/get_resource_by_group_filter_chart/'+this.value).done(function(response){
        response = JSON.parse(response);
        chart1.title.update({
          text: response.title1
        }, false);
        chart1.series[0].update({
          data: response.pie_chart
        }, false);
        chart2.title.update({
          text: <?php echo json_encode(_l('time_use_hour(rs)')); ?>
        }, false);
        chart2.xAxis[0].update({
          categories: response.name
        }, false);
        chart2.series[0].update({
          data: response.col
        }, false);
        chart2.series[1].update({
          data: response.line
        }, false);
        chart1.redraw();
        chart2.redraw();
        $('#container3').addClass('hide');
        $('#container4').addClass('hide');
        $('#container1').removeClass('hide');
        $('#container2').removeClass('hide');
    });  
  }else if(this.value == ''){
    chart1.title.update({
          text: <?php echo json_encode(_l('group_overview')); ?>
        }, false);
    chart1.series[0].update({
          data: <?php echo $pie; ?>
        }, false);
    chart2.title.update({
      text: <?php echo json_encode(_l('time_use_hour(rs)')); ?>
    }, false);
    chart2.xAxis[0].update({
      categories: <?php echo $col_line_name; ?>
    }, false);
    chart2.series[0].update({
      data: <?php echo $col_line_col; ?>
    }, false);
    chart2.series[1].update({
      data: <?php echo $col_line_line; ?>
    }, false);
    chart1.redraw();
    chart2.redraw();
    $('#container3').removeClass('hide');
    $('#container4').removeClass('hide');
    $('#container1').removeClass('hide');
    $('#container2').removeClass('hide');
  }
});
$('#resource').on('change', function(){
    if(this.value != 0 && this.value != ''){
        $.post(admin_url+'resourcebooking/get_resource_filter_chart/'+this.value).done(function(response){
            response = JSON.parse(response);
            chart3.title.update({
              text: <?php echo json_encode(_l('time_use_hour(rs)')); ?>
            }, false);
            chart3.xAxis[0].update({
              categories: <?php echo json_encode($month); ?> 
            }, false);
            chart3.series[0].update({
              data: response.col
            }, false);
            chart3.series[1].update({
              data: response.line
            }, false);

            chart3.redraw();
            $('#container1').addClass('hide');
            $('#container2').addClass('hide');
            $('#container4').addClass('hide');
            $('#container3').removeClass('hide');
        });
    }else{
        chart3.title.update({
          text: <?php echo json_encode(_l('time_use_hour(month)')); ?>
        }, false);
        chart3.xAxis[0].update({
          categories: <?php echo json_encode($month); ?> 
        }, false);
        chart3.series[0].update({
          data: <?php echo $month_col; ?>,
        }, false);
        chart3.series[1].update({
          data: <?php echo $month_line; ?>,
        }, false);
        chart3.redraw();
        $('#container3').removeClass('hide');
        $('#container4').removeClass('hide');
        $('#container1').removeClass('hide');
        $('#container2').removeClass('hide');
    }
});
$('#month').on('change', function(){
    var data = {};
    data.month = $(this).val();
    if($(this).val() != ''){
        $.post(admin_url+'resourcebooking/get_month_filter_chart',data).done(function(response){
            response = JSON.parse(response);
            chart3.xAxis[0].update({
              categories: response.name
            }, false);
            chart3.series[0].update({
              data: response.col
            }, false);
            chart3.series[1].update({
              data: response.line
            }, false);
            chart3.redraw();
            $('#container1').addClass('hide');
            $('#container2').addClass('hide');
            $('#container4').addClass('hide');
            $('#container3').removeClass('hide'); 
        });
    }else{
        chart3.title.update({
          text: <?php echo json_encode(_l('time_use_hour(month)')); ?>
        }, false);
        chart3.xAxis[0].update({
          categories: <?php echo json_encode($month); ?> 
        }, false);
        chart3.series[0].update({
          data: <?php echo $month_col; ?>,
        }, false);
        chart3.series[1].update({
          data: <?php echo $month_line; ?>,
        }, false);
        chart3.redraw();
        $('#container3').removeClass('hide');
        $('#container4').removeClass('hide');
        $('#container1').removeClass('hide');
        $('#container2').removeClass('hide');
    }
    
});
</script>