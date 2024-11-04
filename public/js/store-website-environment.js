var page = {
  init: function (settings) {
    page.config = {
      bodyView: settings.bodyView,
    };
    settings.baseUrl += "/store-website";

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

    page.config.bodyView.on("click", ".btn-update-value", function (e) {
      e.preventDefault();
      page.updateEvnValue($(this));
    });

    page.config.bodyView.on("click", ".btn-edit-template", function (e) {
      page.editRecord($(this));
    });

    $(".common-modal").on("click", ".submit-store-site", function () {
      page.submitFormSite($(this));
    });
    $(".common-modal").on("click", ".submit-update-value", function () {
      page.submitFormUpdateValue($(this));
    });
    $(document).on("click", ".btn-history", function (e) {
      e.preventDefault();
      page.loadHistory($(this));
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
    if (!$("#page-view-result").length) {
      location.reload();
    }
    var _z = {
      url: this.config.baseUrl + "/environment/records",
      method: "get",
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "showResults");
  },
  getResults: function (href) {
    if (!$("#page-view-result").length) {
      return "";
    }
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/environment/records",
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
  createRecord: function (response) {
    var createWebTemplate = $.templates("#template-create-website");
    var tplHtml = createWebTemplate.render({ data: {} });

    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },
  updateEvnValue: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/environment/" + ele.data("id") + "/edit",
      method: "get",
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "updateEvnValueResult");
  },
  updateEvnValueResult: function (response) {
    $("#loading-image").hide();
    var createWebTemplate = $.templates("#template-change-value");
    var tplHtml = createWebTemplate.render(response);
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },
  editRecord: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/environment/" + ele.data("id") + "/edit",
      method: "get",
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "editResult");
  },
  editResult: function (response) {
    $("#loading-image").hide();
    var createWebTemplate = $.templates("#template-create-website");
    var tplHtml = createWebTemplate.render(response);
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
    $("#store_website_id").val(response.data.store_website_id);
  },
  submitFormSite: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/environment/save",
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
  submitFormUpdateValue: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/environment/updateValue",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "submitFormUpdateValueResponse");
  },
  submitFormUpdateValueResponse: function (response) {
    if (response.code == 200) {
      page.loadFirst();
      $(".common-modal").modal("hide");
      if (response.message != "") {
        toastr["success"](response.message, "");
      }
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  loadHistory: function (ele) {
    let page = ele.data("id");

    var _z = {
      url: this.config.baseUrl + "/environment/" + page + "/history",
      method: "get",
      beforeSend: function () {
        $("#loading-image").show();
      },
    };

    this.sendAjax(_z, "afterLoadHistory");
  },
  afterLoadHistory: function (response) {
    var html = ``;
    if (response.code == 200) {
      $.each(response.data, function (k, v) {
        var user = v.user ? v.user.name : "";
        html +=
          `<tr>
                    <td>` +
          v.id +
          `</td>
                    <td>` +
          v.key +
          `</td>
                    <td>` +
          v.new_value +
          `</td>
                    <td>` +
          v.command +
          `</td>
                    <td>` +
          v.job_id +
          `</td>
                    <td>` +
          v.status +
          `</td>
                    <td>` +
          v.response +
          `</td>
                    <td>` +
          v.userName +
          `</td>
                    <td>` +
          v.created_at +
          `</td>
                </tr>`;
      });
      $("#preview-history-tbody").html(html);
      $("#loading-image").hide();
      $(".preview-history-modal").modal("show");
    }
  },
};

$.extend(page, common);
