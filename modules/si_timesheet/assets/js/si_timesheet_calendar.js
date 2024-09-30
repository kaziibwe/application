$(function(){
"use strict";
	var si_calendar_selector = $('#si_calendar');
	//init_ajax_search('tasks', '#si_ts_task_id', undefined, admin_url + 'si_timesheet/ajax_search_assign_task_to_timer');
	//validate attr   
	appValidateForm($('#si-add-tasksheet-form'),{task_id:'required',start:'required',end:'required'});
	// Check if calendar exists in the DOM and init.
	if (si_calendar_selector.length > 0) {
        si_ts_validate_calendar_form();
        var si_calendar_settings = {
            themeSystem: 'bootstrap3',
            customButtons: {},
            header: {
                left: 'prevYear,prev,today,next,nextYear',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,viewFullCalendar,calendarFilter'
            },
            eventLimit: parseInt(app.options.calendar_events_limit) + 1,

            views: {
                day: {
                    eventLimit: false
                }
            },
            defaultView: app.options.default_view_calendar,
            isRTL: (isRTL == 'true' ? true : false),
            timezone: app.options.timezone,
            firstDay: parseInt(app.options.calendar_first_day),
            year: moment.tz(app.options.timezone).format("YYYY"),
            month: moment.tz(app.options.timezone).format("M"),
            date: moment.tz(app.options.timezone).format("DD"),
            loading: function(isLoading, view) {
                isLoading && $('#si_calendar .fc-header-toolbar .btn-default').addClass('btn-info').removeClass('btn-default').css('display', 'block');
                !isLoading ? $('.dt-loader').addClass('hide') : $('.dt-loader').removeClass('hide');
            },
            eventSources: [{
                url: admin_url + 'si_timesheet/get_calendar_data',
                data: function() {
						var params = {};
						$('#si_ts_calendar_filters_form').find('select').map(function() {
							params[$(this).attr('name')] = $(this).val();
						}).get();
						if (!jQuery.isEmptyObject(params)) {
							params['calendar_filters'] = true;
						}
						if ($("body").hasClass('dashboard')) {
							params['dashboard_calendar_filters'] = true;
						}
						return params;
					},
                type: 'POST',
                error: function() {
                    console.error('There was error fetching calendar data');
                },
            }, ],
            eventLimitClick: function(cellInfo, jsEvent) {
                $('#si_calendar').fullCalendar('gotoDate', cellInfo.date);
                $('#si_calendar').fullCalendar('changeView', 'basicDay');
            },
            eventRender: function(event, element) {
			
                element.attr('title', event.title);
                element.attr('onclick', event.onclick);
                element.attr('data-toggle', 'tooltip');
				element.find('.fc-title').html(event._tooltip);
                if (!event.url) {
                    element.click(function() { view_event(event.eventid); });
                }
				
            },
			eventDrop: si_edit_timeline,
			eventResize: si_edit_timeline,
            dayClick: function(date, jsEvent, view) {
                var d = date.format();
                if (!$.fullCalendar.moment(d).hasTime()) {
                    d += ' 00:00';
                }
                var vformat = (app.options.time_format == 24 ? app.options.date_format + ' H:i' : app.options.date_format + ' g:i A');
                var fmt = new DateFormatter();
                var d1 = fmt.formatDate(new Date(d), vformat);
                $("input[name='start'].datetimepicker").val(d1);
                $('#newTimesheetModal').modal('show');
                return false;
            }
        };
        if ($("body").hasClass('dashboard')) {
            si_calendar_settings.customButtons.viewFullCalendar = {
                text: app.lang.calendar_expand,
                click: function() {
                    window.location.href = admin_url + 'si_timesheet';
                }
            };
        }
		if (!$("body").hasClass('dashboard')) {
			si_calendar_settings.customButtons.calendarFilter = {
				text: app.lang.filter_by.toLowerCase(),
				click: function() {
					slideToggle('#si_ts_calendar_filters');
				}
			};
		}
        // Init calendar
        si_calendar_selector.fullCalendar(si_calendar_settings);
        var new_event = get_url_param('new_event');
        if (new_event) {
            $("input[name='start'].datetimepicker").val(get_url_param('date'));
            $('#newTimesheetModal').modal('show');
        }
    }
	function si_edit_timeline(event, delta, revertFunc, jsEvent, ui, view)
	{
			
		if (!confirm(si_ts_confirm_edit)) {
		  revertFunc();
		  return;
		}
		
		var start =event.start.format("Y-MM-DD HH:mm:ss");
		var end = event.end.format("Y-MM-DD HH:mm:ss");
		var data = 'start=' + start + '&end=' + end + '&id=' + event.id;
		if (typeof(csrfData) !== 'undefined') {
            data += '&' + csrfData['token_name'] + '=' + csrfData['hash'];
        }
		 $.ajax({
			url: admin_url + "si_timesheet/edit_timesheet",
			data: data,
			type: "POST",
			success: function (response) {
				response = JSON.parse(response);
				if (response.error) {
					alert_float('danger', response.message);
					revertFunc();
				}
			},
			fail:function(data){
				var error = JSON.parse(data.responseText);
                alert_float('danger',error.message);
				revertFunc();
			}	
		});
	}
	//validate dates for add timesheet
	$("#newTimesheetModal input[name='start'],#newTimesheetModal input[name='end']").on('change',function() {
		var startDate = $("#newTimesheetModal input[name='start']").val();
		var endDate = $("#newTimesheetModal input[name='end']").val();
		if(endDate!='')
			si_calculate_hours(startDate,endDate,$('#newTimesheetModal #si_total_hours'));
		if ((Date.parse(endDate) <= Date.parse(startDate))) {
		  alert(si_ts_date_alert);
		  $("#newTimesheetModal input[name='end']").val("");
		}
	});
	//validate dates for view timesheet
	$(document).on('change',"#viewTimesheet input[name='start'],#viewTimesheet input[name='end']",function() {																							   
		var startDate = $("#viewTimesheet input[name='start']").val();
		var endDate = $("#viewTimesheet input[name='end']").val();
		if(endDate!='')
			si_calculate_hours(startDate,endDate,$('#viewTimesheet #si_total_hours'));
		if ((Date.parse(endDate) <= Date.parse(startDate))) {
		  alert(si_ts_date_alert);
		  $("#viewTimesheet input[name='end']").val("");
		}
	}); 
    function si_calculate_hours(startDate,endDate,displayObj)
	{
		var m1 = moment(startDate, 'DD-MM-YYYY HH:mm A'); 
		var m2 = moment(endDate, 'DD-MM-YYYY HH:mm A'); 
		var m3 = m2.diff(m1,'minutes'); 
		var m4 = m2.diff(m1,'h');
		var __hours = (m4<=9) ? '0'+m4 : m4;
		var __minutes = m3-(m4*60);
		__minutes = (__minutes<=9) ? '0'+__minutes : __minutes;
		displayObj.text(__hours+':'+__minutes);
	}	
});
	
// Validate calendar event form
function si_ts_validate_calendar_form() {
	appValidateForm($("body").find('._event form'), {
		title: 'required',
		start: 'required',
		end: 'required',
		reminder_before: 'required'
	}, calendar_form_handler);

	appValidateForm($("body").find('#viewTimesheet form'), {
		title: 'required',
		start: 'required',
		end: 'required',
		reminder_before: 'required'
	}, calendar_form_handler);
}
// View calendar custom single event
function view_timesheet(id) {
	if (typeof(id) == 'undefined') { return; }
	$.post(admin_url + 'si_timesheet/view_timesheet/' + id).done(function(response) {
		$('#event').html(response);
		$('#viewTimesheet').modal('show');
		init_datepicker();
		init_selectpicker();
		si_ts_validate_calendar_form();
	});
}

// Delete calendar timesheet event form
function delete_timesheet(id) {
	if (confirm_delete()) {
		requestGetJSON('si_timesheet/delete_timesheet/' + id).done(function(response) {
			if (response.success === true || response.success == 'true') { window.location.reload(); }
		});
	}
}