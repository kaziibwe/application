        function new_contract_type(){
		"use strict";
            $('#contract_type').modal('show');
            $('.edit-title').addClass('hide');
            $('.add-title').removeClass('hide');
                $(function(){
            appValidateForm($('.payroll-table-form'),{
                payroll_month: {
                    required: true
                },
                payroll_type: {
                            required: true
                        },

                    });
                });
        }
        function edit_contract_type(invoker,id){
		"use strict";
            $('#additional_contract_type').append(hidden_input('id',id));
            $('#contract_type input[name="name_contracttype"]').val($(invoker).data('name'));
            $('#contract_type input[name="contracttype"]').val($(invoker).data('contracttype'));
            $('#contract_type input[name="duration"]').val($(invoker).data('duration'));
            $('#contract_type select[name="unit"]').val($(invoker).data('unit'));
            $('#contract_type select[name="unit"]').change();
            $('#contract_type select[name="insurance"]').val($(invoker).data('insurance'));
            $('#contract_type select[name="insurance"]').change();
            $('#contract_type').modal('show');
            $('.add-title').addClass('hide');
            $('.edit-title').removeClass('hide');
        }