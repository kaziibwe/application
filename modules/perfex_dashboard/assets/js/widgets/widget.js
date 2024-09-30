(function($) { 
  "use strict";
    // Format money base sympol currency function
  function format_money_base_sympol(total) {
    let symbol = symbol_base_currency;
    return accounting.formatMoney(total, {
      symbol: symbol
    });
  }
  // widget-calendar
  (function () {
    $(".widget-calendar").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var selectorId = "#calendar-" + widgetId;
      var calendar_selector = $(selectorId);

      $(function () {
        // Check if calendar exists in the DOM and init.
        if (calendar_selector.length > 0) {
          validate_calendar_form();
          var calendar_settings = {
            customButtons: {},
            locale: app.locale,
            headerToolbar: {
              left: "prev,next today",
              center: "title",
              right: "dayGridMonth,timeGridWeek,timeGridDay",
            },
            editable: false,
            dayMaxEventRows: parseInt(app.options.calendar_events_limit) + 1,
            views: {
              day: {
                dayMaxEventRows: false,
              },
            },
            direction: isRTL == "true" ? "rtl" : "ltr",
            eventStartEditable: false,
            firstDay: parseInt(app.options.calendar_first_day),
            initialView: app.options.default_view_calendar,
            timeZone: app.options.timezone,
            loading: function (isLoading, view) {
              !isLoading
                ? $(".widget-" + widgetId + " .dt-loader").addClass("hide")
                : $(".widget-" + widgetId + " .dt-loader").removeClass("hide");
            },
            eventSources: [
              function (info, successCallback, failureCallback) {
                var params = {};
                $("#calendar_filters-" + widgetId)
                  .find("input:checkbox:checked")
                  .map(function () {
                    params[$(this).attr("name")] = true;
                  })
                  .get();
                if (!jQuery.isEmptyObject(params)) {
                  params["calendar_filters"] = true;
                }
                return $.getJSON(
                  admin_url + "perfex_dashboard/widgets/api_get_calendar_data",
                  $.extend({}, params, {
                    start: info.startStr,
                    end: info.endStr,
                  })
                ).then(function (data) {
                  successCallback(
                    data.map(function (e) {
                      return $.extend({}, e, {
                        start: e.start || e.date,
                        end: e.end || e.date,
                      });
                    })
                  );
                });
              },
            ],
            moreLinkClick: function (info) {
              calendar.gotoDate(info.date);
              calendar.changeView("dayGridDay");
              setTimeout(function () {
                $(".widget-" + widgetId + " .fc-popover-close").click();
              }, 250);
            },
            eventDidMount: function (data) {
              var $el = $(data.el);
              $el.attr("title", data.event.extendedProps._tooltip);
              $el.attr("onclick", data.event.extendedProps.onclick);
              $el.attr("data-toggle", "tooltip");
              if (!data.event.extendedProps.url) {
                $el.on("click", function () {
                  view_event(data.event.extendedProps.eventid);
                });
              }
            },
            dateClick: function (info) {
              if (info.dateStr.length <= 10) {
                // has not time
                info.dateStr += " 00:00";
              }
              var fmt = new DateFormatter();
              var d1 = fmt.formatDate(
                new Date(info.dateStr),
                (vformat =
                  app.options.time_format == 24
                    ? app.options.date_format + " H:i"
                    : app.options.date_format + " g:i A")
              );
              $(
                ".widget-" + widgetId + " input[name='start'].datetimepicker"
              ).val(d1);
              $("#perfex_dashboard_new_event_modal_" + widgetId).modal("show");
              return false;
            },
          };
          if ($("body").hasClass("dashboard")) {
            calendar_settings.customButtons.viewFullCalendar = {
              text: app.lang.calendar_expand,
              click: function () {
                window.location.href = admin_url + "utilities/calendar";
              },
            };
            calendar_settings.headerToolbar.left += ",viewFullCalendar";
          }
          calendar_settings.customButtons.calendarFilter = {
            text: app.lang.filter_by.toLowerCase(),
            click: function () {
              slideToggle("#calendar_filters-" + widgetId);
            },
          };
          calendar_settings.headerToolbar.right += ",calendarFilter";
          if (app.user_is_staff_member == 1) {
            if (app.options.google_api !== "") {
              calendar_settings.googleCalendarApiKey = app.options.google_api;
            }
          }
          var calendar = new FullCalendar.Calendar(
            calendar_selector[0],
            calendar_settings
          );
          calendar.render();
          var new_event = get_url_param("new_event");
          if (new_event) {
            $(
              ".widget-" + widgetId + " input[name='start'].datetimepicker"
            ).val(get_url_param("date"));
            $("#perfex_dashboard_new_event_modal_" + widgetId).modal("show");
          }
        }
      });
    });
  })();

  // widget-leads_chart
  (function () {
    $(".widget-leads-chart").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var leads_chart = $("#leads_status_stats-" + widgetId);
      if (leads_chart.length > 0) {
        new Chart(leads_chart, {
          type: "doughnut",
          data: window[
            "perfex_dashboard_widget_leads_chart_" +
              widgetId +
              "_leads_status_stats"
          ],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-projects_chart
  (function () {
    $(".widget-projects-chart").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var projects_chart = $("#projects_status_stats-" + widgetId);
      if (projects_chart.length > 0) {
        new Chart(projects_chart, {
          type: "doughnut",
          data: window[
            "perfex_dashboard_widget_projects_chart_" +
              widgetId +
              "_projects_status_stats"
          ],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-tickets_chart
  (function () {
    $(".widget-tickets-chart").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var tickets_chart_departments = $(
        "#tickets-awaiting-reply-by-department-" + widgetId
      );
      var tickets_chart_status = $(
        "#tickets-awaiting-reply-by-status-" + widgetId
      );

      if (tickets_chart_departments.length > 0) {
        var tickets_dep_chart = new Chart(tickets_chart_departments, {
          type: "doughnut",
          data: window[
            "perfex_dashboard_widget_tickets_chart_tickets_awaiting_reply_by_department_" +
              widgetId +
              "_tickets_awaiting_reply_by_department"
          ],
        });
      }
      if (tickets_chart_status.length > 0) {
        new Chart(tickets_chart_status, {
          type: "doughnut",
          data: window[
            "perfex_dashboard_widget_tickets_chart_tickets_reply_by_status_" +
              widgetId +
              "_tickets_reply_by_status"
          ],
          options: {
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-todos
  (function () {
    $(".widget-todos").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      $(".widget-" + widgetId + " .read-more").readmore({
        collapsedHeight: 150,
        moreLink:
          '<a href="#">' +
          window[
            "perfex_dashboard_widget_todos_" + widgetId + "lang_read_more"
          ] +
          "</a>",
        lessLink:
          '<a href="#">' +
          window[
            "perfex_dashboard_widget_todos_" + widgetId + "lang_show_less"
          ] +
          "</a>",
      });
    });
  })();

  // widget-user_data
  (function () {
    $(".widget-user-data").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var fix_user_data_widget_tabs = function () {
        if (
          (app.browser != "firefox" && isRTL == "false" && is_mobile()) ||
          (app.browser == "firefox" && isRTL == "false" && is_mobile())
        ) {
          $(
            ".widget-" +
              widgetId +
              " .horizontal-scrollable-tabs ul.nav-tabs-horizontal"
          ).css("margin-bottom", "26px");
        }
      };

      fix_user_data_widget_tabs();

      $(window).on("resize", function () {
        $(
          ".widget-" +
            widgetId +
            " .horizontal-scrollable-tabs ul.nav-tabs-horizontal"
        ).removeAttr("style");
        fix_user_data_widget_tabs();
      });
    });
  })();

  // widget-weekly_payments_chart
  (function () {
    $(".widget-weekly-payments-chart").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var weekly_payments_statistics;

      if ($(window).width() < 500) {
        $("#weekly-payment-statistics-" + widgetId).attr("height", "250");
      }

      var init_weekly_payment_statistics = function (data) {
        if ($("#weekly-payment-statistics-" + widgetId).length > 0) {
          if (typeof weekly_payments_statistics !== "undefined") {
            weekly_payments_statistics.destroy();
          }
          if (typeof data == "undefined") {
            var currency = $(
              ".widget-" + widgetId + ' select[name="currency"]'
            ).val();
            $.get(
              admin_url + "home/weekly_payments_statistics/" + currency,
              function (response) {
                weekly_payments_statistics = new Chart(
                  $("#weekly-payment-statistics-" + widgetId),
                  {
                    type: "bar",
                    data: response,
                    options: {
                      responsive: true,
                      scales: {
                        yAxes: [
                          {
                            ticks: {
                              beginAtZero: true,
                            },
                          },
                        ],
                      },
                    },
                  }
                );
              },
              "json"
            );
          } else {
            weekly_payments_statistics = new Chart(
              $("#weekly-payment-statistics-" + widgetId),
              {
                type: "bar",
                data: data,
                options: {
                  responsive: true,
                  scales: {
                    yAxes: [
                      {
                        ticks: {
                          beginAtZero: true,
                        },
                      },
                    ],
                  },
                },
              }
            );
          }
        }
      };

      init_weekly_payment_statistics(
        window[
          "perfex_dashboard_widget_weekly_payments_chart_" +
            widgetId +
            "_weekly_payment_stats"
        ]
      );

      $(".widget-" + widgetId + ' select[name="currency"]').on(
        "change",
        function () {
          init_weekly_payment_statistics();
        }
      );
    });
  })();

  // widget-income_and_expenses
  (function () {
    $(".widget-income-and-expenses").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      $(".widget-" + widgetId + " .format-money").each(function () {
        var text = $(this).text();
        if (text !== undefined && text !== null && text !== "") {
          $(this).text(format_money_base_sympol(text));
        }
      });
    });
  })();

  // widget-monthly_income
  (function () {
    $(".widget-monthly-income").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      new Chart($("#perfex_dashboard_widget_" + widgetId + "_chart_income"), {
        type: "bar",
        data: window[
          "perfex_dashboard_widget_monthly_income_" + widgetId + "_data"
        ],
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false,
          },
          scales: {
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                },
              },
            ],
          },
        },
      });

      var total_income_selector = $(
        ".widget-" + widgetId + " .total-income-amount"
      );
      if (total_income_selector.length > 0) {
        total_income_selector.text(format_money_base_sympol(total_income_selector.text()));
      }
    });
  })();

  // widget-top_10_selling_products
  (function () {
    $(".widget-top-10-selling-products").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var selector_priceables = $(".widget-" + widgetId + " .priceable");
      if (selector_priceables.length > 0) {
        selector_priceables.each(function () {
          $(this).text(format_money_base_sympol($(this).text()));
        });
      }

      var selector_quantities = $(".widget-" + widgetId + " .quantity-num");
      if (selector_quantities.length > 0) {
        selector_quantities.each(function () {
          $(this).text(parseInt($(this).text()));
        });
      }
    });
  })();

  // widget-total_revenue
  (function () {
    $(".widget-total-revenue").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var selector_priceables = $(".widget-" + widgetId + " .priceable");
      if (selector_priceables.length > 0) {
        selector_priceables.each(function () {
          $(this).text(format_money_base_sympol($(this).text()));
        });
      }
    });
  })();

  // widget-revenue_top_10_with_country
  (function () {
    $(".widget-revenue-top-10-with-country").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var selector_priceables = $(".widget-" + widgetId + " .priceable");
      if (selector_priceables.length > 0) {
        selector_priceables.each(function () {
          $(this).text(format_money_base_sympol($(this).text()));
        });
      }

      var selector_quantities = $(".widget-" + widgetId + " .order-num");
      if (selector_quantities.length > 0) {
        selector_quantities.each(function () {
          $(this).text(parseInt($(this).text()));
        });
      }
    });
  })();

  // widget-revenue_top_10_with_city
  (function () {
    $(".widget-revenue-top-10-with-city").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var selector_priceables = $(".widget-" + widgetId + " .priceable");
      if (selector_priceables.length > 0) {
        selector_priceables.each(function () {
          $(this).text(format_money_base_sympol($(this).text()));
        });
      }

      var selector_quantities = $(".widget-" + widgetId + " .order-num");
      if (selector_quantities.length > 0) {
        selector_quantities.each(function () {
          $(this).text(parseInt($(this).text()));
        });
      }
    });
  })();

  // widget-card_contacts
  (function () {
    $(".widget-card-contacts").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      // js code
    });
  })();

  // widget-card_orders
  (function () {
    $(".widget-card-orders").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      // js code
    });
  })();

  // widget-card_orders_revenue
  (function () {
    $(".widget-card-orders-revenue").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var selector_priceables = $(".widget-" + widgetId + " .priceable");
      if (selector_priceables.length > 0) {
        selector_priceables.each(function () {
          $(this).text(format_money_base_sympol($(this).text()));
        });
      }
    });
  })();

  // widget-card_subscriptions
  (function () {
    $(".widget-card-subscriptions").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      // js code
    });
  })();

  // widget-card_subscription_revenue
  (function () {
    $(".widget-card-subscription-revenue").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var selector_priceables = $(".widget-" + widgetId + " .priceable");
      if (selector_priceables.length > 0) {
        selector_priceables.each(function () {
          $(this).text(format_money_base_sympol($(this).text()));
        });
      }
    });
  })();

  // widget-new_customers
  (function () {
    $(".widget-new-customers").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      // js code
    });
  })();

  // widget-top_10_customer_payment
  (function () {
    $(".widget-top-10-customer-payment").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var selector_priceables = $(".widget-" + widgetId + " .priceable");
      if (selector_priceables.length > 0) {
        selector_priceables.each(function () {
          $(this).text(format_money_base_sympol($(this).text()));
        });
      }
    });
  })();

  // widget-top_20_staff_income
  (function () {
    $(".widget-top-20-staff-income").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var selector_priceables = $(".widget-" + widgetId + " .priceable");
      if (selector_priceables.length > 0) {
        selector_priceables.each(function () {
          $(this).text(format_money_base_sympol($(this).text()));
        });
      }
    });
  })();

  // widget-total_staff_of_roles
  (function () {
    $(".widget-total-staff-of-roles").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var chart = $("#chart-" + widgetId);
      if (chart.length > 0) {
        new Chart(chart, {
          type: "doughnut",
          data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-staff_increment_by_years
  (function () {
    $(".widget-staff-increment-by-years").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      new Chart($("#perfex_dashboard_widget_" + widgetId + "_chart"), {
        type: "bar",
        data: window["perfex_dashboard_widget_" + widgetId + "_data"],
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false,
          },
          scales: {
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                },
              },
            ],
          },
        },
      });

      var total_income_selector = $(
        ".widget-" + widgetId + " .total-income-amount"
      );
      if (total_income_selector.length > 0) {
        total_income_selector.text(format_money_base_sympol(total_income_selector.text()));
      }
    });
  })();

  // widget-monthly_deadline_projects_total
  (function () {
    $(".widget-monthly-deadline-projects-total").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      // js code
    });
  })();

  // widget-monthly_deadline_tasks_total
  (function () {
    $(".widget-monthly-deadline-tasks-total").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      // js code
    });
  })();

  // widget-total_customer_of_groups
  (function () {
    $(".widget-total-customer-of-groups").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var chart = $("#chart-" + widgetId);
      if (chart.length > 0) {
        new Chart(chart, {
          type: "doughnut",
          data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-weekly_deadline_tasks
  (function () {
    $(".widget-weekly-deadline-tasks").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      // js code
    });
  })();

  // widget-monthly_deadline_projects
  (function () {
    $(".widget-monthly-deadline-projects").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      // js code
    });
  })();

  // widget-logged_hours_by_projects
  (function () {
    $(".widget-logged-hours-by-projects").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var chart = $("#chart-" + widgetId);
      if (chart.length > 0) {
        new Chart(chart, {
          type: "doughnut",
          data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-logged_hours_by_staff
  (function () {
    $(".widget-logged-hours-by-staff").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var chart = $("#chart-" + widgetId);
      if (chart.length > 0) {
        new Chart(chart, {
          type: "doughnut",
          data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-techfago_leads_and_converted_leads_by_sources
  (function () {
    $(".widget-leads-and-converted-leads-by-sources").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      new Chart($("#perfex_dashboard_widget_" + widgetId + "_chart_data"), {
        type: "bar",
        data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false,
          },
          scales: {
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                },
              },
            ],
          },
        },
      });
    });
  })();

  // widget-techfago_leads_and_converted_leads_by_staff
  (function () {
    $(".widget-leads-and-converted-leads-by-staff").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      new Chart($("#perfex_dashboard_widget_" + widgetId + "_chart_data"), {
        type: "bar",
        data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false,
          },
          scales: {
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                },
              },
            ],
          },
        },
      });
    });
  })();

  // widget-techfago_leads_lead_by_countries
  (function () {
    $(".widget-lead-by-countries").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      new Chart($("#perfex_dashboard_widget_" + widgetId + "_chart_data"), {
        type: "bar",
        data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false,
          },
          scales: {
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                },
              },
            ],
          },
        },
      });
    });
  })();

  // widget-techfago_leads_lead_by_sources
  (function () {
    $(".widget-leads-lead-by-sources").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var chart = $("#chart-" + widgetId);
      if (chart.length > 0) {
        new Chart(chart, {
          type: "doughnut",
          data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-techfago_leads_lead_by_tags
  (function () {
    $(".widget-leads-lead-by-tags").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var chart = $("#chart-" + widgetId);
      if (chart.length > 0) {
        new Chart(chart, {
          type: "doughnut",
          data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-techfago_projects_logged_hours_by_projects
  (function () {
    $(".widget-projects-logged-hours-by-projects").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var chart = $("#chart-" + widgetId);
      if (chart.length > 0) {
        new Chart(chart, {
          type: "pie",
          data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-techfago_projects_logged_hours_by_staff
  (function () {
    $(".widget-projects-logged-hours-by-staff").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var chart = $("#chart-" + widgetId);
      if (chart.length > 0) {
        new Chart(chart, {
          type: "pie",
          data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-techfago_staff_staff_with_roles
  (function () {
    $(".widget-staff-staff-with-roles").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var chart = $("#chart-" + widgetId);
      if (chart.length > 0) {
        new Chart(chart, {
          type: "pie",
          data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-techfago_staff_staff_with_departments
  (function () {
    $(".widget-staff-staff-with-departments").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var chart = $("#chart-" + widgetId);
      if (chart.length > 0) {
        new Chart(chart, {
          type: "pie",
          data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-techfago_finance_total_income
  (function () {
    $(".widget-finance-total-income").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var selector_priceables = $(this).find(".priceable");
      if (selector_priceables.length > 0) {
        selector_priceables.each(function () {
          $(this).text(format_money_base_sympol($(this).text()));
        });
      }
    });
  })();

  // widget-techfago_finance_total_expense
  (function () {
    $(".widget-finance-total-expense").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var selector_priceables = $(this).find(".priceable");
      if (selector_priceables.length > 0) {
        selector_priceables.each(function () {
          $(this).text(format_money_base_sympol($(this).text()));
        });
      }
    });
  })();

  // widget-techfago_finance_total_profit
  (function () {
    $(".widget-finance-total-profit").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }
      var selector_priceables = $(this).find(".priceable");
      if (selector_priceables.length > 0) {
        selector_priceables.each(function () {
          $(this).text(format_money_base_sympol($(this).text()));
        });
      }
    });
  })();

  // widget-techfago_finance_expense_with_categories
  (function () {
    $(".widget-finance-expense-with-categories").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      var chart = $("#chart-" + widgetId);
      if (chart.length > 0) {
        new Chart(chart, {
          type: "pie",
          data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
          options: {
            maintainAspectRatio: false,
            onClick: function (evt) {
              onChartClickRedirect(evt, this);
            },
          },
        });
      }
    });
  })();

  // widget-techfago_finance_monthly_income_and_expense
  (function () {
    $(".widget-finance-monthly-income-and-expense").each(function () {
      var widgetId = $(this).data("widget-id");
      if (widgetId === undefined || widgetId === null || widgetId === "") {
        return;
      }

      new Chart($("#perfex_dashboard_widget_" + widgetId + "_chart_data"), {
        type: "bar",
        data: window["perfex_dashboard_widget_" + widgetId + "_chart_data"],
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false,
          },
          scales: {
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                },
              },
            ],
          },
        },
      });
    });
  })();
})(jQuery);
