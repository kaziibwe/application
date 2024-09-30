        function new_workplace(){
		"use strict";
            $('#workplace').modal('show');
            $('.edit-title').addClass('hide');
            $('.add-title').removeClass('hide');
        }
        function edit_workplace(invoker,id){
		"use strict";
            $('#additional_workplace').append(hidden_input('id',id));
            $('#workplace input[name="workplace_name"]').val($(invoker).data('name'));
            $('#workplace').modal('show');
            $('.add-title').addClass('hide');
            $('.edit-title').removeClass('hide');
        }