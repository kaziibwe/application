(function($) { 
  "use strict"; 
  // setup period dropdown
  const fnSetupPeriod = async function () {
    var report_from = $(
      '.perfexdashboard-period-select input[name="period_from"]'
    );
    var report_to = $('.perfexdashboard-period-select input[name="period_to"]');
    var date_range = $(".perfexdashboard-date-range");

    $(".perfexdashboard-period-dropdown").on("change", function () {
      var val = $(this).val();
      var val_to = "";
      var val_from = "";

      if (val !== "") {
        var vals = val.split(";");
        if (vals.length === 2) {
          val_from = vals[0];
          val_to = vals[1];
        }
      }

      if (val != "custom") {
        report_from.val(val_from);
        report_to.val(val_to);
      }

      if (val == "custom") {
        date_range.addClass("fadeIn").removeClass("hide");
        return;
      } else {
        if (!date_range.hasClass("hide")) {
          date_range.removeClass("fadeIn").addClass("hide");
        }
      }
    });
    $(".perfexdashboard-period-dropdown").trigger("change");
  };

  if (typeof APP_PATH === "undefined") {
    return;
  }

  // get lang
  const fnGetLang = async function (langs) {
    return new Promise((resolve, reject) => {
      var reqData = {
        langs: langs,
      };
      $.ajax({
        url: site_url + "admin/perfex_dashboard/api/lang",
        type: "GET",
        data: reqData,
        dataType: "json",
        success: function (rs) {
          resolve(rs);
        },
        error: function () {
          reject();
        },
      });
    });
  };

  // dashboard js
  const fnDashboardJs = async function () {
    if (
      typeof window.perfex_dashboard_enable_sortable !== "undefined" ||
      window.perfex_dashboard_enable_sortable
    ) {
      var langs = await fnGetLang(["hide_widget_area", "widget_area"]);

      window.perfex_dashboard_update_widgets_order = function () {
        var order = {};
        $("[data-container]").each(function () {
          var cId = $(this).attr("data-container");
          var widgetIds = [];
          $(this)
            .find(".widget")
            .each(function () {
              var widgetId = $(this).data("widget-id");
              if (widgetId != undefined && widgetId != null && widgetId != "") {
                widgetIds.push(widgetId);
              }
            });
          if (widgetIds.length > 0) {
            order[cId] = widgetIds;
          }
        });

        var reqData = {
          dashboard_id: DASHBOARD_ID,
          order: order,
        };
        reqData[csrfData.token_name] = csrfData.hash;
        $.ajax({
          url:
            site_url +
            "admin/perfex_dashboard/dashboards/api_update_widgets_order",
          type: "POST",
          data: reqData,
          dataType: "json",
          success: function (rs) {
            // do nothing
          },
          error: function () {
            // do nothing
          },
        });
      };

      $(function () {
        // sortable
        $("[data-container]").sortable({
          connectWith: "[data-container]",
          helper: "clone",
          handle: ".widget-dragger",
          tolerance: "pointer",
          forcePlaceholderSize: true,
          placeholder: "placeholder-dashboard-widgets",
          start: function (event, ui) {
            $("body,#wrapper").addClass("noscroll");
            $("body").find("[data-container]").css("min-height", "20px");
          },
          stop: function (event, ui) {
            $("body,#wrapper").removeClass("noscroll");
            $("body").find("[data-container]").removeAttr("style");
          },
          update: function (event, ui) {
            if (this === ui.item.parent()[0]) {
              window.perfex_dashboard_update_widgets_order();
            }
          },
        });

        $("body").on("click", "#viewWidgetableArea", function (e) {
          e.preventDefault();

          if (!$(this).hasClass("preview")) {
            $(this).html(langs.hide_widgetable_area);
            $("[data-container]").append(
              '<div class="placeholder-dashboard-widgets pl-preview"></div>'
            );
          } else {
            $(this).html(langs.view_widgetable_area);
            $("[data-container]").find(".pl-preview").remove();
          }

          $("[data-container]").toggleClass("preview-widgets");
          $(this).toggleClass("preview");
        });
      });
    }
  };

  // path "/categories"
  const fnPageCategories = async function () {
    var langs = await fnGetLang(["perfex_dashboard_confirm_delete"]);

    // create category
    $(".btn-create-category").on("click", function () {
      var modalCreate = $("#modalCreateCategory");
      if (modalCreate.length > 0) {
        modalCreate.modal();
      }
    });

    //edit category
    var perfex_dashboard_lock_edit = false;
    $(".btn-edit-category").on("click", function () {
      if (perfex_dashboard_lock_edit) {
        return;
      }

      var id = $(this).data("id");
      if (id == undefined && id == null && id == "") {
        return;
      }

      perfex_dashboard_lock_edit = true;
      $.ajax({
        url:
          site_url +
          "admin/perfex_dashboard/categories/api_get_category_data?category_id=" +
          id,
        type: "GET",
        data: {},
        dataType: "json",
        success: function (rs) {
          if (rs) {
            $('#modalEditCategory [name="id"]').val(rs.id);
            $('#modalEditCategory [name="name"]').val(rs.name);
            $('#modalEditCategory [name="note"]').val(rs.note);
          }

          $("#modalEditCategory").modal();

          perfex_dashboard_lock_edit = false;
        },
        error: function () {
          perfex_dashboard_lock_edit = false;
        },
      });
    });

    // delete category
    $(".btn-delete").on("click", function () {
      var id = $(this).data("id");
      if (id != undefined && id != null && id != "") {
        if (!confirm(langs.perfex_dashboard_confirm_delete)) {
          return;
        }

        $('#formDelete [name="id"]').val(id);
        $("#formDelete").submit();
      }
    });
  };

  // path "/dashboards/index"
  const fnPageDashboards = async function () {
    var langs = await fnGetLang(["perfex_dashboard_confirm_delete"]);

    // create dashboard
    $(".btn-create-dashboard").on("click", function () {
      var modalCreate = $("#modalCreateDashboard");
      if (modalCreate.length > 0) {
        modalCreate.modal();
      }
    });

    // delete dashboard
    $(".btn-delete").on("click", function () {
      var id = $(this).data("id");
      if (id != undefined && id != null && id != "") {
        if (!confirm(langs.perfex_dashboard_confirm_delete)) {
          return;
        }

        $('#formDelete [name="dashboard_id"]').val(id);
        $("#formDelete").submit();
      }
    });

    // clone dashboard
    $(".btn-clone").on("click", function () {
      var id = $(this).data("id");
      if (id != undefined && id != null && id != "") {
        var name = $('.data-name[data-dashboard-id="' + id + '"]').text();
        var note = $('.data-note[data-dashboard-id="' + id + '"]').text();

        $('#modalCloneDashboard [name="clone_id"]').val(id);
        $('#modalCloneDashboard [name="name"]').val(name + " -  Copy");
        $('#modalCloneDashboard [name="note"]').val(note);

        $("#modalCloneDashboard").modal();
        // $('#formDelete [name="dashboard_id"]').val(id);
        // $('#formDelete').submit();
      }
    });
  };

  // path "/my_dashboard"
  const fnPageMyDashboard = async function () {
    $('[name="dashboard_id"]').on("change", function () {
      var dashboardId = $(this).val();
      if (
        dashboardId != undefined &&
        dashboardId != null &&
        dashboardId != ""
      ) {
        $(window).off('beforeunload');
        $(this).parents("form").submit();
      }
    });
  };

  // path "/widgets"
  const fnPageWidgets = async function () {
    var langs = await fnGetLang(["perfex_dashboard_confirm_delete"]);

    // add custom buttons
    if (widget_edit_permission) {
      $(".widget").prepend(
        `<div class="perfexdashboard-widget-removable"></div>`
      );
    }
    if (widget_delete_permission) {
      $(".widget").prepend(
        `<div class="perfexdashboard-widget-changeable"></div>`
      );
    }

    // on remove widget
    $("body").on("click", ".perfexdashboard-widget-removable", function () {
      if (!confirm(langs.perfex_dashboard_confirm_delete)) {
        return;
      }

      var id = $(this).parents(".widget").data("widget-id");
      if (id == undefined && id == null && id == "") {
        return;
      }

      $('#formDeleteWidget [name="id"]').val(id);

      $("#formDeleteWidget").submit();
    });

    // show modal edit widget
    var perfex_dashboard_lock_changeable = false;
    $("body").on("click", ".perfexdashboard-widget-changeable", function () {
      if (perfex_dashboard_lock_changeable) {
        return;
      }

      var id = $(this).parents(".widget").data("widget-id");
      if (id == undefined && id == null && id == "") {
        return;
      }

      perfex_dashboard_lock_changeable = true;
      $.ajax({
        url:
          site_url +
          "admin/perfex_dashboard/widgets/api_get_widget_data?widget_id=" +
          id,
        type: "GET",
        data: {},
        dataType: "json",
        success: function (rs) {
          if (rs) {
            $('#modalEditWidget [name="id"]').val(rs.id);
            $('#modalEditWidget [name="name"]').val(rs.name);
            $('#modalEditWidget [name="note"]').val(rs.note);
            $('#modalEditWidget [name="category"]').val(rs.category);
            $('#modalEditWidget [name="widget_name"]').val(
              rs.widget_name.substr(7)
            );
          }

          $("#modalEditWidget").modal();

          perfex_dashboard_lock_changeable = false;
        },
        error: function () {
          perfex_dashboard_lock_changeable = false;
        },
      });
    });
  };

  // path "/dashboards/edit_dashboard"
  const fnPageEditDashboard = async function () {
    var langs = await fnGetLang([
      "add",
      "preview",
      "perfex_dashboard_confirm_delete",
      "not_found",
      "perfex_dashboard_message_success_delete_widget_from_dashboard",
    ]);

    window.perfex_dashboard_enable_sortable = true;
    fnDashboardJs();

    var widget_preview_url =
      site_url + "admin/perfex_dashboard/widgets?category=&search=";
    // add widget
    (function () {
      $("#formAddWidget").on(
        "click",
        ".perfexdashboard-add-widget",
        function () {
          var widgetId = $(this).data("widget-id");
          var widgetContainer = $(this).data("widget-container");
          if (
            widgetId != undefined &&
            widgetId != null &&
            widgetId != "" &&
            widgetContainer != undefined &&
            widgetContainer != null &&
            widgetContainer != ""
          ) {
            $('#formAddWidget [name="widget_id"]').val(widgetId);
            $('#formAddWidget [name="widget_container"]').val(widgetContainer);
            $("#formAddWidget").submit();
          }
        }
      );
    })();

    // render available widgets for adding
    (function () {
      var data = [];
      var fnRender = function () {
        var selectedCategory = $("#changeCategory").val();
        if (
          selectedCategory == undefined ||
          selectedCategory == null ||
          selectedCategory == ""
        ) {
          return;
        }

        var wrapper = $("#modalAddWidget tbody");
        wrapper.html("");

        var availables = data.filter((ele) => {
          return selectedCategory == ele.category;
        });

        if (availables.length > 0) {
          availables.forEach((available) => {
            wrapper.append(`
              <tr>
                <td>${available.name}<br/><small>${available.note}</small></td>
                <td><a class="perfexdashboard-add-widget" href="javascript:void(0);" data-widget-id="${
                  available.id
                }" data-widget-container="${available.default_container}">${
              langs["add"]
            }</a></td>
                <td><a class="perfexdashboard-add-widget" href="${
                  widget_preview_url + available.name
                }" target="_blank">${langs.preview}</a></td>
              </tr>
            `);
          });
        } else {
          wrapper.append(`
            <tr>
              <td colspan="2">${langs.not_found}</td>
            </tr>
          `);
        }
      };
      $("#changeCategory").on("change", function () {
        fnRender();
      });

      window.perfex_dashboard_fetch_available_widgets = function () {
        var reqData = {
          dashboard_id: DASHBOARD_ID,
        };
        reqData[csrfData.token_name] = csrfData.hash;
        $.ajax({
          url:
            site_url +
            "admin/perfex_dashboard/dashboards/api_available_widgets",
          type: "POST",
          data: reqData,
          dataType: "json",
          success: function (rs) {
            data = rs;
            fnRender();
          },
          error: function () {
            fnRender();
          },
        });
      };

      window.perfex_dashboard_fetch_available_widgets();
    })();

    // removable widget button
    (function () {
      $(".widget").prepend(
        `<div class="perfexdashboard-widget-removable"></div>`
      );

      $("[data-container]").on(
        "click",
        ".perfexdashboard-widget-removable",
        function () {
          if (!confirm(langs.perfex_dashboard_confirm_delete)) {
            return;
          }

          $(this).parents(".widget").remove();
          window.perfex_dashboard_update_widgets_order();
          window.perfex_dashboard_fetch_available_widgets();
          alert_float(
            "warning",
            langs.perfex_dashboard_message_success_delete_widget_from_dashboard
          );
        }
      );
    })();

    // delete dashboard
    (function () {
      $(".btn-delete").on("click", function () {
        if (!confirm(langs.perfex_dashboard_confirm_delete)) {
          return;
        }

        $("#formDelete").submit();
      });
    })();
  };

  fnSetupPeriod();

  // switch path
  switch (APP_PATH) {
    case "/categories":
      fnPageCategories();
      break;
    case "/dashboards":
      fnPageDashboards();
      break;
    case "/my_dashboard":
      fnPageMyDashboard();
      break;
    case "/widgets":
      fnPageWidgets();
      break;
    case "/dashboards/edit_dashboard":
      fnPageEditDashboard();
      break;
  }
})(jQuery);
