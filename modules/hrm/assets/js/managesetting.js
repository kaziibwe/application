  appValidateForm($('form'),{position_name:'required',workplace_name:'required',name_contracttype:'required',type_name:'required',allowance_val:'required',taxable:'required', contracttype:'required', insurance:'required',from_month:'required'});
$(function() {
    $('.date-picker').datepicker( {
	"use strict";
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'mm/yy',
        onClose: function(dateText, inst) { 
            $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
        }
    });
});