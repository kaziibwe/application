 var ResourceServerParams = {  
        "group": "[name='group[]']",
    };
    table_booking = $('table.table-table_booking');
    _table_api = initDataTable(table_booking, admin_url+'resourcebooking/booking_table', '', '', ResourceServerParams);
    $.each(ResourceServerParams, function(i, obj) {
		"use strict";
        $('select' + obj).on('change', function() {  
            table_booking.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });
