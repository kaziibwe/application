    var fnServerParams = {
      "from_date": 'input[name="from_date"]',
      "to_date": 'input[name="to_date"]',
      "status": 'select[name="status"]',
    }

     $('input[name="from_date"],input[name="to_date"],select[name="status"]').on('change', function() {
        if ($.fn.DataTable.isDataTable('.table-projectroadmap')) {
             $('.table-projectroadmap').DataTable().destroy();
           }
        initDataTable('.table-projectroadmap', window.location.href, false, false, fnServerParams);
     });

  $(function(){
  "use strict";
    initDataTable('.table-projectroadmap', window.location.href, false, false, fnServerParams);
    $('.table-projectroadmap').DataTable().on('draw', function() {
        var rows = $('.table-projectroadmap').find('tr');
        $.each(rows, function() {
            var td = $(this).find('td').eq(5);
            var percent = $(td).find('input[name="percent"]').val();
            $(td).find('.goal-progress').circleProgress({
                value: percent,
                size: 45,
                animation: false,
                fill: {
                    gradient: ["#28b8da", "#059DC1"]
                }
            })
        })
    })
  });


 function add_dashboard(id){
 "use strict";
    $.post(admin_url + 'projectroadmap/add_projectroadmap_filter_widget/'+id).done(function(response) {
        response = JSON.parse(response);
            alert_float('success', response.message);
            window.location.reload();
     });
 }
 function remove_dashboard(id){
 "use strict";
    $.post(admin_url + 'projectroadmap/remove_projectroadmap_filter_widget/'+id).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
            window.location.reload();
        }
     });
 }