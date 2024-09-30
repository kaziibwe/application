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
			locale: app.locale,
            headerToolbar: {
                left: 'prevYear,prev,today,next,nextYear',
                center: 'title',
               right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            dayMaxEventRows: parseInt(app.options.calendar_events_limit) + 1,
			views: {
                day: {
					dayMaxEventRows: false
                }
            },
           // defaultView: app.options.default_view_calendar,
			initialView: app.options.default_view_calendar,
            direction: (isRTL == 'true' ? true : false),
            timeZone: app.options.timezone,
            firstDay: parseInt(app.options.calendar_first_day),
            //year: moment.tz(app.options.timezone).format("YYYY"),
            //month: moment.tz(app.options.timezone).format("M"),
            //date: moment.tz(app.options.timezone).format("DD"),
            loading: function(isLoading, view) {
                isLoading && $('#si_calendar .fc-header-toolbar .btn-default').addClass('btn-info').removeClass('btn-default').css('display', 'block');
                !isLoading ? $('.dt-loader').addClass('hide') : $('.dt-loader').removeClass('hide');
            },
            
			eventSources: [function(info, successCallback, failureCallback){
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

                return $.getJSON(admin_url + 'si_timesheet/get_calendar_data', $.extend({}, params, {
                    start: info.startStr,
                    end: info.endStr,
                })).then(function(data){
                    successCallback(data.map(function(e){
                        return $.extend( {}, e, {
                            start: e.start || e.date,
                            end: e.end || e.date
                        });
                    }));
                });
            }],
			moreLinkClick: function (info) {
                sicalendar.gotoDate( info.date );
            },
			eventDidMount: function (data) {
                var $el = $(data.el);
				$el.attr('title', data.event.title);
                $el.attr('onclick', data.event.extendedProps.onclick);
                $el.attr('data-toggle', 'tooltip');
				$el.find('.fc-event-title').html(data.event.extendedProps._tooltip);
				$el.css("background-color", data.backgroundColor);
				$el.css("color", '#fff');
                if (!data.event.extendedProps.url) {
                    $el.on('click', function(){
                        view_event(data.event.extendedProps.eventid);
                    });
                }
            },
			eventDrop: si_edit_timeline,
			eventResize: si_edit_timeline,
          
		 	dateClick: function (info) {
                if (info.dateStr.length <= 10) { // has not time
                    info.dateStr += ' 00:00';
                }

                var fmt = new DateFormatter();
				var vformat =app.options.time_format == 24 ?
                        app.options.date_format + ' H:i' :
                        app.options.date_format + ' g:i A';
                var d1 = fmt.formatDate(new Date(info.dateStr), vformat);

                $("input[name='start'].datetimepicker").val(d1);
                $('#newTimesheetModal').modal('show');

                return false;
            },
        };
        if ($("body").hasClass('dashboard')) {
            si_calendar_settings.customButtons.viewFullCalendar = {
                text: app.lang.calendar_expand,
                click: function() {
                    window.location.href = admin_url + 'si_timesheet';
                }
            };
			si_calendar_settings.headerToolbar.right += ',viewFullCalendar';
        }
		if (!$("body").hasClass('dashboard')) {
			si_calendar_settings.customButtons.calendarFilter = {
				text: app.lang.filter_by.toLowerCase(),
				click: function() {
					slideToggle('#si_ts_calendar_filters');
				}
			};
			si_calendar_settings.headerToolbar.right += ',calendarFilter';
		}
        // Init calendar
        var sicalendar = new FullCalendar.Calendar(si_calendar_selector[0], si_calendar_settings);
       	sicalendar.render();
		
        var new_event = get_url_param('new_event');
        if (new_event) {
            $("input[name='start'].datetimepicker").val(get_url_param('date'));
            $('#newTimesheetModal').modal('show');
        }
    }
	function si_edit_timeline(info)
	{
			
		if (!confirm(si_ts_confirm_edit)) {
		  info.revert();
		  return;
		}
		var fmt = new DateFormatter();
		var start =fmt.formatDate(info.event.start,"Y-m-d H:i:s");
		var end = fmt.formatDate(info.event.end,"Y-m-d H:i:s");
		var data = 'start=' + start + '&end=' + end + '&id=' + info.event.id;
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
					info.revert();
				}
			},
			fail:function(data){
				var error = JSON.parse(data.responseText);
                alert_float('danger',error.message);
				info.revert();
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