        function new_salary_form(){
		"use strict";
            $('#salary_form').modal('show');
            $('.edit-title').addClass('hide');
            $('.add-title').removeClass('hide');
        }
        function edit_salary_form(invoker,id){
		"use strict";
            $('#additional_salary_form').append(hidden_input('id',id));
            $('#salary_form input[name="form_name"]').val($(invoker).data('name'));
            $('#salary_form input[name="salary_val"]').val($(invoker).data('amount'));
            $('#salary_form select[name="tax"]').val($(invoker).data('taxable'));
            $('#salary_form select[name="tax"]').change();
            $('#salary_form').modal('show');
            $('.add-title').addClass('hide');
            $('.edit-title').removeClass('hide');
        }