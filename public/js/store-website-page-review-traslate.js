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

    page.config.bodyView.on("click", ".btn-edit-template", function (e) {
      page.editRecord($(this));
    });

    $(".common-modal").on("click", ".submit-store-site", function () {
      page.submitFormSite($(this));
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
      url: this.config.baseUrl + "/page/getReviewTranslateRecords",
      method: "get",
      data: $(".message-search-handler").serialize(),
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
          : this.config.baseUrl + "/page/getReviewTranslateRecords",
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
  editRecord: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/page/" + ele.data("id") + "/edit",
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
    $('textarea[name="meta_keywords"]').trigger("change");
    $('textarea[name="meta_description"]').trigger("change");
    $("#google_translate_element").summernote();
    $("#keyword-search-btn").trigger("click");
    $('textarea[name="meta_keyword"]').trigger("input");
    $('textarea[name="meta_keyword_avg_monthly"]').trigger("input");

    //new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
  },
  submitFormSite: function (ele) {
    var _z = {
      url:
        typeof href != "undefined" ? href : this.config.baseUrl + "/page/save",
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
};

$.extend(page, common);

function auto_grow(element) {
  element.style.height = "5px";
  element.style.height = element.scrollHeight + 1 + "px";
}
