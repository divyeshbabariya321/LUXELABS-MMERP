var page = {
  init: function (settings) {
    page.config = {
      bodyView: settings.bodyView,
    };

    $.extend(page.config, settings);

    page.config.mainUrl =
      page.config.baseUrl + "/time-doctor-activities/notification";

    this.getResults();

    //initialize pagination
    page.config.bodyView.on("click", ".page-link", function (e) {
      e.preventDefault();
      page.getResults($(this).attr("href"));
    });

    page.config.bodyView.on("click", ".btn-search-action", function (e) {
      e.preventDefault();
      page.getResults();
    });

    page.config.bodyView.on("click", ".btn-add-action", function (e) {
      e.preventDefault();
      page.createRecord();
    });

    page.config.bodyView.on("click", ".btn-edit-reason", function (e) {
      e.preventDefault();
      var id = $(this).data("id");
      page.showEditReason(id);
    });

    page.config.bodyView.on("click", ".btn-change-status", function (e) {
      e.preventDefault();
      var id = $(this).data("id");
      page.showChangeStatus(id);
    });

    $(".common-modal").on("click", ".store-reason-btn", function (e) {
      e.preventDefault();
      page.submitReasonForm($(this));
    });

    $(".common-modal").on("click", ".submit-change-status", function (e) {
      e.preventDefault();
      page.submitChangeStatus($(this));
    });

    // delete product templates
    page.config.bodyView.on("click", ".btn-delete-template", function (e) {
      if (!confirm("Are you sure you want to delete record?")) {
        return false;
      } else {
        page.deleteRecord($(this));
      }
    });

    page.config.bodyView.on("click", ".btn-edit-template", function (e) {
      page.editRecord($(this));
    });

    let r_s = jQuery('input[name="start_date"]').val();
    let r_e = jQuery('input[name="end_date"]').val();

    if (r_s == "0000-00-00 00:00:00") {
      r_s = undefined;
    }

    if (r_e == "0000-00-00 00:00:00") {
      r_e = undefined;
    }

    let start = r_s ? moment(r_s, "YYYY-MM-DD") : moment().subtract(6, "days");
    let end = r_e ? moment(r_e, "YYYY-MM-DD") : moment();

    function cb(start, end) {
      $("#reportrange span").html(
        start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
      );
    }

    $("#reportrange").daterangepicker(
      {
        startDate: start,
        maxYear: 1,
        endDate: end,
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
      },
      cb
    );

    cb(start, end);

    $("#reportrange").on("apply.daterangepicker", function (ev, picker) {
      jQuery('input[name="start_date"]').val(
        picker.startDate.format("YYYY-MM-DD")
      );
      jQuery('input[name="end_date"]').val(picker.endDate.format("YYYY-MM-DD"));
    });
  },
  validationRule: function (response) {
    $(document)
      .find("#product-template-from")
      .validate({
        rules: {
          name: "required",
        },
        messages: {
          name: "Template name is required",
        },
      });
  },
  loadFirst: function () {
    var _z = {
      url: this.config.mainUrl + "/records",
      method: "get",
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "showResults");
  },
  getResults: function (href) {
    var _z = {
      url: typeof href != "undefined" ? href : this.config.mainUrl + "/records",
      method: "get",
      data: $(".message-search-handler").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "showResults");
  },
  showResults: function (response) {
    $("#loading-image").hide();
    var addProductTpl = $.templates("#template-result-block");
    var tplHtml = addProductTpl.render(response);

    $(".count-text").html("(" + response.total + ")");
    $(".total_working_hr").html("(" + response.total + ")");

    page.config.bodyView.find("#page-view-result").html(tplHtml);
  },
  deleteRecord: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.mainUrl + "/" + ele.data("id") + "/delete",
      method: "get",
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "deleteResults");
  },
  deleteResults: function (response) {
    if (response.code == 200) {
      this.getResults();
      toastr["success"]("Message deleted successfully", "success");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "error");
    }
  },
  createRecord: function (response) {
    var createWebTemplate = $.templates("#template-create-form");
    var tplHtml = createWebTemplate.render({ data: {} });

    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    $("#billing_start,#billing_end,#scheduled_on").datetimepicker({
      format: "YYYY-MM-DD HH:mm:00",
    });
    common.modal("show");
  },

  showEditReason: function (id) {
    var createWebTemplate = $.templates("#template-edit-reason");
    var tplHtml = createWebTemplate.render({ id: id });
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },

  showChangeStatus: function (id) {
    var createWebTemplate = $.templates("#template-change-status");
    var tplHtml = createWebTemplate.render({ id: id });
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },

  submitReasonForm: function (ele) {
    var _z = {
      url: typeof href != "undefined" ? href : this.config.mainUrl + "/save",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveSite");
  },
  saveSite: function (response) {
    if (response.code == 200) {
      page.loadFirst();
      $(".common-modal").modal("hide");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  submitChangeStatus: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.mainUrl + "/change-status",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveStatus");
  },
  saveStatus: function (response) {
    if (response.code == 200) {
      page.loadFirst();
      $(".common-modal").modal("hide");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
};

$.extend(page, common);
