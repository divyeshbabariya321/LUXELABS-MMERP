var page = {
  init: function (settings) {
    page.config = {
      bodyView: settings.bodyView,
    };

    settings.baseUrl += "/chat-messages";

    $.extend(page.config, settings);

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

    $(".common-modal").on("click", ".submit-store-site", function () {
      page.submitFormSite($(this));
    });

    page.config.bodyView.on("click", ".btn-push", function (e) {
      page.push($(this));
    });

    $(document).on("click", ".create-default-stores", function (e) {
      page.createDefaultStores($(this));
    });

    $(document).on("click", ".move-stores", function (e) {
      page.moveStores($(this));
    });

    page.config.bodyView.on("click", ".btn-copy-template", function (e) {
      $("#copy-website-modal")
        .find("#copy-website-field")
        .val($(this).data("id"));
      $("#copy-website-modal").modal("show");
    });

    $(document).on("click", ".copy-stores", function (e) {
      page.copyStores($(this));
    });

    $(document).on("click", ".change-status", function (e) {
      page.changeStatus($(this));
    });

    $(".select2").select2({ tags: true });

    $(".time-range").daterangepicker({
      autoUpdateInput: false,
      timePicker: true,
      locale: {
        format: "YYYY-MM-DD hh:mm A",
      },
    });

    $(".time-range").on("apply.daterangepicker", function (ev, picker) {
      $(this).val(
        picker.startDate.format("YYYY-MM-DD hh:mm A") +
          " - " +
          picker.endDate.format("YYYY-MM-DD hh:mm A")
      );
    });

    $(".time-range").on("cancel.daterangepicker", function (ev, picker) {
      $(this).val("");
    });

    $(document).on("click", ".choose-all", function (e) {
      $(".select-customer").trigger("click");
    });

    $(document).on("click", ".btn-move-to-dnd", function (e) {
      page.moveDnd($(this));
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
      url: this.config.baseUrl + "/dnd-list/records",
      method: "get",
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "showResults");
  },
  getResults: function (href) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/dnd-list/records",
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

    page.config.bodyView.find("#page-view-result").html(tplHtml);
  },
  deleteRecord: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/dnd-list/" + ele.data("id") + "/delete",
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
      toastr["success"]("Request deleted successfully", "success");
      location.reload();
    } else {
      toastr["error"]("Oops.something went wrong", "error");
    }
  },
  createRecord: function (response) {
    var createWebTemplate = $.templates("#template-create-website");
    var tplHtml = createWebTemplate.render({ data: {} });

    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },

  editRecord: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/dnd-list/" + ele.data("id") + "/edit",
      method: "get",
    };
    this.sendAjax(_z, "editResult");
  },

  editResult: function (response) {
    var createWebTemplate = $.templates("#template-create-website");
    var tplHtml = createWebTemplate.render(response);
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");

    common.find(".select-2").select2({ tags: true });
  },

  submitFormSite: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/dnd-list/save",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveSite");
  },

  assignSelect2: function () {
    var selectList = $("select.select-searchable");
    if (selectList.length > 0) {
      $.each(selectList, function (k, v) {
        var element = $(v);
        if (!element.hasClass("select2-hidden-accessible")) {
          element.select2({ tags: true, width: "100%" });
        }
      });
    }
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
  push: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/dnd-list/" + ele.data("id") + "/push",
      method: "get",
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "afterPush");
  },
  afterPush: function (response) {
    $("#loading-image").hide();
    if (response.code == 200) {
      toastr["success"](response.message, "");
      location.reload();
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  createDefaultStores: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/dnd-list/create-default-stores",
      method: "post",
      data: {
        store_website_id: $(".default-store-website-select").val(),
      },
    };
    this.sendAjax(_z, "afterCreateDefaultStores");
  },
  afterCreateDefaultStores: function (response) {
    if (response.code == 200) {
      toastr["success"](response.message, "");
      location.reload();
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  moveStores: function (ele) {
    var groups = [];
    var checkedGroups = $(".groups:checked");

    $.each(checkedGroups, function (k, v) {
      groups.push($(v).val());
    });

    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/dnd-list/move-stores",
      method: "post",
      data: {
        store_website_id: $(".move-store-website-select").val(),
        ids: groups,
        group_name: $(".move-store-group-change").val(),
      },
    };

    this.sendAjax(_z, "afterMoveStores");
  },
  afterMoveStores: function (response) {
    if (response.code == 200) {
      toastr["success"](response.message, "");
      location.reload();
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  moveDnd: function (ele) {
    var ids = [];
    var selections = $(".select-customer:checked");
    $.each(selections, function (k, v) {
      ids.push($(v).val());
    });

    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/dnd-list/move-dnd",
      method: "post",
      data: {
        customer_id: ids,
      },
    };

    this.sendAjax(_z, "afterMoveDnd");
  },
  afterMoveDnd: function (response) {
    if (response.code == 200) {
      toastr["success"](response.message, "");
      location.reload();
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
};

$.extend(page, common);
