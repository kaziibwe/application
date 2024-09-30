<script>
var fnServerParams = {
   "project_id": 'input[name="project_id"]',
   "milestones": 'select[name="milestones"]',
   "members": 'select[name="members"]',
   "task_status": 'select[name="task_status"]',
 }
  $(function(){
     var project_progress_color = '<?php echo do_action('admin_project_progress_color','#84c529'); ?>';
     var circle = $('.project-progress').circleProgress({fill: {
      gradient: [project_progress_color, project_progress_color]
    }}).on('circle-animation-progress', function(event, progress, stepValue) {
      $(this).find('strong.project-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
    });
    projectroadmap_tasks();
  });
  $('select[name="milestones"],select[name="members"],select[name="task_status"]').on('change', function() {
       projectroadmap_tasks();
     });
  function projectroadmap_tasks() {
     if ($.fn.DataTable.isDataTable('.table-projectroadmap_tasks')) {
       $('.table-projectroadmap_tasks').DataTable().destroy();
     }
     initDataTable('.table-projectroadmap_tasks', admin_url + 'reports/projectroadmap_tasks', false, false, fnServerParams);
   }
   function projectroadmap_tasks_by_member(id) {
     if ($.fn.DataTable.isDataTable('.table-projectroadmap_tasks')) {
       $('.table-projectroadmap_tasks').DataTable().destroy();
     }
     $('input[name="member_id"]').val(id);
     var Params = { "project_id": 'input[name="project_id"]',
      "members": 'input[name="member_id"]'};
     initDataTable('.table-projectroadmap_tasks', admin_url + 'reports/projectroadmap_tasks', false, false, Params);
   }
   function projectroadmap_tasks_by_milestone(id) {
     if ($.fn.DataTable.isDataTable('.table-projectroadmap_tasks')) {
       $('.table-projectroadmap_tasks').DataTable().destroy();
     }
     $('input[name="milestone_id"]').val(id);
     var Params = { "project_id": 'input[name="project_id"]',
      "milestones": 'input[name="milestone_id"]'};
     initDataTable('.table-projectroadmap_tasks', admin_url + 'reports/projectroadmap_tasks', false, false, Params);
   }

   function add_dashboard(id){
    $.post(admin_url + 'reports/add_projectroadmap_filter_widget/'+id).done(function(response) {
        alert_float('success', response.message);
        window.location.reload();
     });
 }
 function remove_dashboard(id){
    $.post(admin_url + 'reports/remove_projectroadmap_filter_widget/'+id).done(function(response) {
        alert_float('success', response.message);
        window.location.reload();
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

    
$(function(){
    var list = [];
    <?php foreach ($task_bookmarks_list as $task_bookmarks) { ?>
        list.push(<?php echo '' . $task_bookmarks['rel_id']; ?>);
    <?php }?>
    $.each(list,function(key,value){
        var list_tasks = {
            "list_tasks": 'input[name="list_tasks_'+value+'"]',
        }
        if ($.fn.DataTable.isDataTable('.table-task_bookmarks_list_task_add_'+value)) {
            $('.table-task_bookmarks_list_task_add_'+value).DataTable().destroy();
        }
        initDataTable('.table-task_bookmarks_list_task_add_'+value, admin_url + 'reports/task_bookmarks_list_task_add', false, false, list_tasks);
    });
  });

</script>