"use strict";

var	calendars_selector = $('#calendars');

if (calendars_selector.length > 0) {
    validate_calendars_form();

    var calendars_settings = {
        themeSystem: 'bootstrap3',
        customButtons: {},
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,viewFullCalendar,calendarFilter'
        },
        editable: false,
        eventLimit: parseInt(app.options.calendar_events_limit) + 1,
        views: {
            day: {
                eventLimit: false
            }
        },
        defaultView: app.options.default_view_calendar,
        isRTL: (isRTL == 'true' ? true : false),
        eventStartEditable: false,
        timezone: app.options.timezone,
        firstDay: parseInt(app.options.calendar_first_day),
        year: moment.tz(app.options.timezone).format("YYYY"),
        month: moment.tz(app.options.timezone).format("M"),
        date: moment.tz(app.options.timezone).format("DD"),
        loading: function(isLoading, view) {
            isLoading && $('#calendars .fc-header-toolbar .btn-default').addClass('btn-info').removeClass('btn-default').css('display', 'block');
            !isLoading ? $('.dt-loader').addClass('hide') : $('.dt-loader').removeClass('hide');
        },

        eventSources: [{
            url: admin_url + 'resourcebooking/get_calendar_data',
            data: function() {
                var params = {};
                $('#calendar_filters').find('input:checkbox:checked').map(function() {
                    params[$(this).attr('name')] = true;
                }).get();

                if (!jQuery.isEmptyObject(params)) {
                    params['calendar_filters'] = true;
                }

                return params;
            },
            type: 'POST',
            error: function() {
                console.error('There was error fetching calendar data');
            },
        }, ],

        eventLimitClick: function(cellInfo, jsEvent) {
            //$('#calendars').fullCalendar('gotoDate', cellInfo.date);
            //$('#calendars').fullCalendar('changeView', 'basicDay');
        },

        eventRender: function(event, element) {
            element.attr('title', event._tooltip);
            element.attr('onclick', event.onclick);
            element.attr('data-toggle', 'tooltip');
            if (!event.url) {
                element.click(function() { view_event(event.eventid); });
            }
        },

        dayClick: function(date, jsEvent, view) {
            var d = date.format();
            if (!$.fullCalendar.moment(d).hasTime()) {
                d += ' 00:00';
            }

            var vformat = (app.options.time_format == 24 ? app.options.date_format + ' H:i' : app.options.date_format + ' g:i A');
            var fmt = new DateFormatter();
            var d1 = fmt.formatDate(new Date(d), vformat);
            $("input[name='start_time'].datetimepicker").val(d1);
            $('#newEventModal').modal('show');
            return false;
        }
    };

    if (app.user_is_staff_member == 1) {
        if (app.options.google_api !== '') {
            calendars_settings.googleCalendarApiKey = app.options.google_api;
        }

        if (app.calendarIDs !== '') {
            app.calendarIDs = JSON.parse(app.calendarIDs);
            if (app.calendarIDs.length != 0) {
                if (app.options.google_api !== '') {
                    for (var i = 0; i < app.calendarIDs.length; i++) {
                        var _gcal = {};
                        _gcal.googleCalendarId = app.calendarIDs[i];
                        calendars_settings.eventSources.push(_gcal);
                    }
                } else {
                    console.error('You have setup Google Calendar IDs but you dont have specified Google API key. To setup Google API key navigate to Setup->Settings->Google');
                }
            }
        }
    }

    // Init calendar
    var calendars = new FullCalendar.Calendar(
        calendars_selector[0],
        calendars_settings
    );
    calendars.render();
    // calendars_selector.fullCalendar(calendars_settings);

    var new_event = get_url_param('new_event');

    if (new_event) {
        $("input[name='start'].datetimepicker").val(get_url_param('date'));
        $('#newEventModal').modal('show');
    }
}

// Delete calendar event form

// Validate calendar event form

function validate_calendars_form() {
    appValidateForm($("body").find('._event form'), {
        title: 'required',
        start: 'required',
        reminder_before: 'required'
    }, calendars_form_handler);

    appValidateForm($("body").find('#viewEvent form'), {
        title: 'required',
        start: 'required',
        reminder_before: 'required'
    }, calendars_form_handler);
}

// Handles calendar event saving
function calendars_form_handler(form) {
    $.post(form.action, $(form).serialize()).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
            setTimeout(function() {
                var location = window.location.href;
                location = location.split('?');
                window.location.href = location[0];
            }, 500);
        }
    });

    return false;
}