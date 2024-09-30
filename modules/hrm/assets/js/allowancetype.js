        function new_allowance_type(){
		"use strict";
            $('#allowance_type').modal('show');
            $('.edit-title').addClass('hide');
            $('.add-title').removeClass('hide');
        }
        function edit_allowance_type(invoker,id){
		"use strict";
            $('#additional_allowance_type').append(hidden_input('id',id));
            $('#allowance_type input[name="type_name"]').val($(invoker).data('name'));
            $('#allowance_type input[name="allowance_val"]').val($(invoker).data('amount'));
            $('#allowance_type select[name="taxable"]').val($(invoker).data('taxable'));
            $('#allowance_type select[name="taxable"]').change();
            $('#allowance_type').modal('show');
            $('.add-title').addClass('hide');
            $('.edit-title').removeClass('hide');
        }