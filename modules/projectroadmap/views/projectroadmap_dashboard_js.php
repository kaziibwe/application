<script>
  $(function(){
     var circle = $('.project-progress').circleProgress({fill: {
      gradient: ['#84c529', '#84c529']
    }}).on('circle-animation-progress', function(event, progress, stepValue) {
      $(this).find('strong.project-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
    });
    var arrayValues = $('.list_project_id').map(function(){
                      return $(this).val()
                  }).get();
      $.each(arrayValues, function() {
        var pr_id = $(this)[0];
        projectroadmap_tasks(pr_id);

        $('select[name="milestones_'+pr_id+'"]').on('change', function() {
            projectroadmap_tasks(pr_id);
        });
        $('select[name="members_'+pr_id+'"]').on('change', function() {
            projectroadmap_tasks(pr_id);
        });
        $('select[name="task_status_'+pr_id+'"]').on('change', function() {
            projectroadmap_tasks(pr_id);
        });
    });
  });
  $('select[name="milestones"],select[name="members"],select[name="task_status"]').on('change', function() {
       projectroadmap_tasks();
     });
  function projectroadmap_tasks(project_id) {
     if ($.fn.DataTable.isDataTable('.table-projectroadmap_tasks_' + project_id)) {
       $('.table-projectroadmap_tasks_' + project_id).DataTable().destroy();
     }
     var fnServerParams = {
       "milestones": 'select[name="milestones_'+project_id+'"]',
       "members": 'select[name="members_'+project_id+'"]',
       "task_status": 'select[name="task_status_'+project_id+'"]',
     }
     initDataTable('.table-projectroadmap_tasks_' + project_id, admin_url + 'projectroadmap/projectroadmap_tasks/'+project_id, false, false, fnServerParams);
   }
   function projectroadmap_tasks_by_member(project_id,id) {
     if ($.fn.DataTable.isDataTable('.table-projectroadmap_tasks_' + project_id)) {
       $('.table-projectroadmap_tasks_' + project_id).DataTable().destroy();
     }
     $('input[name="member_id_'+project_id+'"]').val(id);
     var Params = {
      "members": 'input[name="member_id_'+project_id+'"]'};
     initDataTable('.table-projectroadmap_tasks_' + project_id, admin_url + 'projectroadmap/projectroadmap_tasks/'+project_id, false, false, Params);
   }
   function projectroadmap_tasks_by_milestone(project_id,id) {
     if ($.fn.DataTable.isDataTable('.table-projectroadmap_tasks_' + project_id)) {
       $('.table-projectroadmap_tasks_' + project_id).DataTable().destroy();
     }
     $('input[name="milestone_id_'+project_id+'"]').val(id);
     var Params = {
      "milestones": 'input[name="milestone_id_'+project_id+'"]'};
     initDataTable('.table-projectroadmap_tasks_' + project_id, admin_url + 'projectroadmap/projectroadmap_tasks/'+project_id, false, false, Params);
   }
 function remove_projectroadmap_dashboard(id){
    $.post(admin_url + 'projectroadmap/remove_projectroadmap_filter_widget/'+id).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
            window.location.reload();
        }
     });
 }
  var rows = $('.table-milestones').find('tr');
    $.each(rows, function() {
        var td = $(this).find('td').eq(1);
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

</script>