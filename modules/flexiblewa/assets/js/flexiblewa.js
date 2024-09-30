$(function(){
   $('#flexiblewa_action_sequence #section_id').on('change', function(){
       //get the value
      const id = $(this).val();
      const container = $('.flexiblewa_action_sequence_container');
      const url = $('#flexiblewa_ajax_url').val();
      const data = {
         action: 'get_list_of_actions_for_section',
         id: id,
      }
      //make get request
      $.get(url, data, function(response) {
         if(response.success){
            $(container).html(response.html);
            flexiblewa_initialize_sortable_action();
         }
      });
   });

   //initialiaze the sortable list
   function flexiblewa_initialize_sortable_action(){
       const container = $('#flexiblewa-action-list-item');
       if($(container).length) {
           $(container).sortable({
               placeholder: "ui-state-highlight-flexiblewa",
               update: function (event, ui) {
                   // Update actions order
                   flexiblewa_update_actions_order();
               }
           });
       }
   }

    //update actions order
    function flexiblewa_update_actions_order() {
        const actions = [];
        $('#flexiblewa-action-list-item .flexiblewa-action-item').each(function() {
            actions.push($(this).data('id'));
        });
        const url = $('#flexiblewa_ajax_url').val();
        const data = {
           action: 'update_actions_order',
           actions: actions,
        };
        $.post(url, data);
        //show success
        alert_float('success', $('#flexiblewa-action-list-item').data('success'));
    }
});