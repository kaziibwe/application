<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="clearfix"></div>
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php $this->load->view('view_projectroadmap_dashboard'); ?>
            </div>
          </div>
        </div>
      </div>
</div>
</div>

<?php init_tail(); ?>
<?php $this->load->view('projectroadmap_js'); ?>
 
</body>
</html>
<script>

  Highcharts.chart('container_task', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45
        }
    },
    title: {
        text: '<?php echo _l('statistics_by_task_status'); ?>'
    },
    plotOptions: {
        pie: {
            innerSize: 100,
            depth: 45
        }
    },
    series: [{
        innerSize: '20%',
        name: '<?php echo _l('total_task'); ?>',
        data: <?php echo '' . $tasks_status_stats; ?>    
      }]
});

Highcharts.chart('container_time', {
    chart: {
        type: 'bar'
    },
    title: {
        text: '<?php echo _l('statistics_by_estimate_hour'); ?>'
    },
    xAxis: {
        categories: [''],
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: '<?php echo _l('hours'); ?>',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    tooltip: {
        valueSuffix: ' <?php echo _l('hours'); ?>'
    },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            }
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'top',
        x: -40,
        y: 80,
        floating: true,
        borderWidth: 1,
        backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
        shadow: true
    },
    credits: {
        enabled: false
    },
    series: <?php echo '' . $project_hour_stats; ?>
});

Highcharts.chart('container_priority', {
    chart: {
        type: 'column'
    },
    title: {
        text:'<?php echo _l('statistics_by_task_priority'); ?>'
    },
    xAxis: {
        categories: [''],
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: '<?php echo _l('task'); ?>',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    tooltip: {
        valueSuffix: ' <?php echo _l('task'); ?>'
    },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            }
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'top',
        x: -40,
        y: 80,
        floating: true,
        borderWidth: 1,
        backgroundColor:
          Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
        shadow: true
    },
    credits: {
        enabled: false
    },
    series: <?php echo '' . $tasks_priority_stats; ?>
});

  Highcharts.chart('container', {
    chart: {
        type: 'variablepie'
    },
    accessibility: {
        description: ''
    },
    title: {
        text: '<?php echo _l('statistics_by_milestone_status'); ?>'
    },
    tooltip: {
        headerFormat: '',
        pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
            '<?php echo _l('total_milestones'); ?>: <b>{point.y}</b><br/>' 
    },
    series: [{
        minPointSize: 1,
        innerSize: '20%',
        zMin: 0,
        name: 'countries',
        data: <?php echo '' . $milestones_status_stats; ?>
    }]
});

</script>