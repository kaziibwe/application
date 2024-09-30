        function new_paysplit(){
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