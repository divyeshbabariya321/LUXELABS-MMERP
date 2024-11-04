var page = {
  init: function (settings) {
    page.config = {
      bodyView: settings.bodyView,
    };

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
    page.config.bodyView.on("click", ".btn-add-action-password", function (e) {
      e.preventDefault();
      page.createRecordPassword();
    });
    page.config.bodyView.on("click", ".load-duplicate-modal", function (e) {
      e.preventDefault();
      page.createDuplicate($(this).data("id"));
    });

    page.config.bodyView.on("click", ".load-tag-modal", function (e) {
      e.preventDefault();
      page.showtags($(this));
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

    page.config.bodyView.on("click", ".btn-edit-template-password", function (e) {
      page.editRecordPassword($(this));
    });

    $(".common-modal").on("click", ".test-store-site", function (e) {
      e.preventDefault();
      var token = $("#api_token").val();
      var magentoURL = $("#magento_url").val();
      if (!token) {
        toastr["error"]("Please enter token for website", "error");
        return;
      }
      if (!magentoURL) {
        toastr["error"]("Please enter magento URL for website", "error");
        return;
      }
      page.checkToken(token, magentoURL);
    });

    page.config.bodyView.on(
      "click",
      ".btn-edit-cancellation-template",
      function (e) {
        page.editCancellationRecord($(this));
      }
    );

    page.config.bodyView.on("click", ".btn-attach-category", function (e) {
      page.attachCategory($(this).data("id"));
    });

    $(".common-modal").on("click", ".submit-store-site", function () {
      page.submitFormSite($(this));
    });

    $(".common-modal").on("click", ".submit-duplicate", function () {
      page.submitDuplicate($(this));
    });

    $(".common-modal").on(
      "click",
      ".submit-store-site-cancellation",
      function () {
        page.submitFormSiteCancellation($(this));
      }
    );

    $(".common-modal").on("click", ".btn-edit-magento-user", function () {
      page.submitMagentoUserForm($(this));
    });

    $(".common-modal").on("click", ".btn-add-magento-user", function () {
      page.AddMagentoUserForm($(this));
    });

    $(".common-modal").on("click", ".btn-delete-magento-user", function () {
      page.deleteMagentoUserForm($(this));
    });

    $(".common-modal").on("click", ".add-attached-category", function (e) {
      e.preventDefault();
      page.submitCategory($(this));
    });

    $(".common-modal").on(
      "click",
      ".btn-delete-store-website-category",
      function (e) {
        e.preventDefault();
        page.deleteCategory($(this));
      }
    );

    page.config.bodyView.on("click", ".btn-attach-brands", function (e) {
      e.preventDefault();
      page.attachBrands($(this).data("id"));
    });

    page.config.bodyView.on("click", ".show-facebook-remarks", function (e) {
      e.preventDefault();
      page.showRemarks(
        "facebook_remarks",
        $(this).data("id"),
        $(this).data("value")
      );
    });

    $(".common-modal").on("click", ".update-remark-btn", function (e) {
      e.preventDefault();
      page.submitRemarks($(this));
    });

    page.config.bodyView.on("click", ".show-instagram-remarks", function (e) {
      e.preventDefault();
      page.showRemarks(
        "instagram_remarks",
        $(this).data("id"),
        $(this).data("value")
      );
    });

    page.config.bodyView.on("click", ".btn-seo-format", function (e) {
      e.preventDefault();
      page.editSeoRecord($(this));
    });

    page.config.bodyView.on(
      "click",
      ".open-build-process-template",
      function (e) {
        e.preventDefault();
        page.editBuildProcess($(this));
      }
    );

    page.config.bodyView.on(
      "click",
      ".add-website-company-address-template",
      function (e) {
        e.preventDefault();
        page.addCompanyWebAddress($(this));
      }
    );
    $(".common-modal").on(
      "click",
      ".update-company-wesite-address",
      function (e) {
        e.preventDefault();
        page.updateCompanyAddress($(this));
      }
    );

    $(".common-modal").on("click", ".add-attached-brands", function (e) {
      e.preventDefault();
      page.submitAttachedBrands($(this));
    });

    $(".common-modal").on(
      "click",
      ".btn-delete-store-website-brand",
      function (e) {
        e.preventDefault();
        $cof = confirm("Are you sure you want to delete ?");
        if ($cof == true) {
          page.deleteAttachedBrands($(this));
        }
      }
    );

    $(".common-modal").on("click", ".update-seo-format", function (e) {
      e.preventDefault();
      page.updateSeoFormat($(this));
    });

    $(".common-modal").on("click", ".update-build-process", function (e) {
      e.preventDefault();
      page.updateBuildProcess($(this));
    });

    $(document).on("change", "select.select-searchable", function () {
      // now need to call for getting child
      var id = $(this).val();
      page.getChildCategories(id);
    });

    $(document).on("click", ".btn-show-password", function () {
      var block = $(this).closest(".subMagentoUser");
      var password = block.find(".user-password");
      const type = password.attr("type") === "password" ? "text" : "password";
      password.attr("type", type);
    });

    $(document).on("click", ".btn-copy-password", function () {
      var block = $(this).closest(".subMagentoUser");
      var username = block.find(".userName");
      var password = block.find(".user-password");

      var combinedValue = 'Username : '+username.val()+ "\n" +'Password : '+password.val();


      var $temp = $("<textarea>");
      $("body").append($temp);
      $temp.val(combinedValue).select();
      document.execCommand("copy");
      $temp.remove();
    });

    $(document).on("click", ".btn-copy-admin-url", function () {

        var combinedValue = 'Admin URL : '+$("#generated-admin-url").val();

        if($("#generated-admin-url").val()!=''){

            var $temp = $("<textarea>");
            $("body").append($temp);
            $temp.val(combinedValue).select();
            document.execCommand("copy");
            $temp.remove();
            toastr["success"]("Copied!", "success");
        } else {
          toastr["error"]("You don't have admin url!", "error");
        }
    });

    $(document).on("click", ".generate-pem-file", function () {
      page.openGenerateFile();
    });

    $(document).on("click", ".open-store-magento-user-lising", function (href) {
      page.openUserListing();
    });

    $(document).on("click", ".magento-setting-update", function (href) {
      $("#loading-image").show();
      page.magentosettingUpdate();
    });

    $(document).on("click", ".open-store-user-histoty", function (href) {
      page.openUserStoreHistorListing($(this));
    });

    $(document).on("click", ".open-store-user-histoty", function (href) {
      page.openUserStoreHistorListing($(this));
    });
  },
  showtags: function (ele) {
    $("#store-attach-tag #store_id").val(ele.data("id"));
  },
  openStoreReindexHistory: function (ele) {
    var _z = {
      url:
        this.config.baseUrl +
        "/store-website/" +
        ele.data("id") +
        "/store-reindex-history",
      method: "get",
    };
    this.sendAjax(_z, "showStoreReindexHistory");
  },
  showStoreReindexHistory: function (response) {
    if (response.code == 200) {
      var createWebTemplate = $.templates("#template-store-reindex-history");
      var tplHtml = createWebTemplate.render(response);
      var common = $(".common-modal");
      common.find(".modal-dialog").html(tplHtml);
      common.modal("show");
    }
  },
  openUserStoreHistorListing: function (ele) {
    var _z = {
      url:
        this.config.baseUrl +
        "/store-website/" +
        ele.data("id") +
        "/userhistory",
      method: "get",
    };
    this.sendAjax(_z, "showStoreUserHistoryLiting");
  },
  showStoreUserHistoryLiting: function (response) {
    if (response.code == 200) {
      var createWebTemplate = $.templates(
        "#template-history-store-magento-user"
      );
      var tplHtml = createWebTemplate.render(response);
      var common = $(".common-modal");
      common.find(".modal-dialog").html(tplHtml);
      common.modal("show");
    }
  },
  openUserListing: function () {
    var _z = {
      url: this.config.baseUrl + "/store-website/magento-user-lising",
      method: "get",
    };
    this.sendAjax(_z, "showUserLiting");
  },
  showUserLiting: function (response) {
    if (response.code == 200) {
      var createWebTemplate = $.templates("#template-magento-user-lising");
      var tplHtml = createWebTemplate.render(response);
      var common = $(".common-modal");
      common.find(".modal-dialog").html(tplHtml);
      common.modal("show");
    }
  },
  magentosettingUpdate: function () {
    var _z = {
      url: this.config.baseUrl + "/magento-setting-updates",
      method: "post",
    };
    this.sendAjax(_z, "showMagentoSetting");
  },
  showMagentoSetting: function (response) {
    if (response.code == 200) {
      $("#loading-image").hide();
      this.getResults();
      toastr["success"](response.message, "success");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.message, "error");
    }
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
      url: this.config.baseUrl + "/store-website/records",
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
          : this.config.baseUrl + "/store-website/records",
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
    this.config.bodyView.find("#page-view-result").html(tplHtml);
  },
  deleteRecord: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl +
            "/store-website/" +
            ele.data("id") +
            "/delete",
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
  createRecordPassword: function (response) {
    var createWebTemplate = $.templates("#template-create-website-password");
    var tplHtml = createWebTemplate.render({ data: {} });

    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },
  createDuplicate: function (response) {
    var createWebTemplate = $.templates("#template-new-duplicate");
    var tplHtml = createWebTemplate.render({ data: {} });

    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
    $(".store_website_id").val(0);
    $(".store_website_id").val(response);
  },

  editRecord: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/store-website/" + ele.data("id") + "/edit",
      method: "get",
    };
    this.sendAjax(_z, "editResult");
  },

  editRecordPassword: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/store-website/" + ele.data("id") + "/edit",
      method: "get",
    };
    this.sendAjax(_z, "editResultPassword");
  },

  editResult: function (response) {
    var createWebTemplate = $.templates("#template-create-website");
    var tplHtml = createWebTemplate.render(response);
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },

  editResultPassword: function (response) {
    var createWebTemplate = $.templates("#template-create-website-password");
    var tplHtml = createWebTemplate.render(response);
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },

  checkToken: function (token, url) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/store-website/" + token + "/token-check",
      method: "get",
      data: { url },
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "afterCheckProcess");
  },
  afterCheckProcess: function (response) {
    if (response.code == 200) {
      $("#loading-image").hide();
      toastr["success"]("Token is valid", "");
    } else {
      $("#loading-image").hide();
      toastr["error"]("Token is invalid", "");
    }
  },

  editSeoRecord: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl +
            "/store-website/" +
            ele.data("id") +
            "/seo-format",
      method: "get",
    };
    this.sendAjax(_z, "afterSeoFormat");
  },

  afterSeoFormat: function (response) {
    var createWebTemplate = $.templates("#template-edit-seo");
    var tplHtml = createWebTemplate.render(response);
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },

  updateSeoFormat: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl +
            "/store-website/" +
            ele.data("id") +
            "/seo-format/save",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "afterUpdateSeo");
  },

  afterUpdateSeo: function (response) {
    if (response.code == 200) {
      $("#loading-image").hide();
      $(".common-modal").modal("hide");
      toastr["success"]("Added successfully", "");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },

  editBuildProcess: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl +
            "/store-website/" +
            ele.data("id") +
            "/build-process",
      method: "get",
    };
    this.sendAjax(_z, "afterBuildProcess");
  },

  addCompanyWebAddress: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl +
            "/store-website/" +
            ele.data("id") +
            "/add-company-website-address",
      method: "get",
    };
    this.sendAjax(_z, "afterCopmanyWebsiteAdd");
  },
  afterCopmanyWebsiteAdd: function (response) {
    var createWebTemplate = $.templates("#add-website-company-address");
    var tplHtml = createWebTemplate.render(response);
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },
  updateCompanyAddress: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl +
            "/store-website/update-company-website-address",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "afterUpdateCompanyAddress");
  },
  afterUpdateCompanyAddress: function (response) {
    if (response.code == 200) {
      $("#loading-image").hide();
      $(".common-modal").modal("hide");
      toastr["success"]("Address Updated successfully", "");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },

  afterBuildProcess: function (response) {
    var createWebTemplate = $.templates("#template-build-process");
    var tplHtml = createWebTemplate.render(response);
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },
  updateBuildProcess: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl +
            "/store-website/" +
            ele.data("id") +
            "/build-process/save",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "afterUpdateBuildProcess");
  },
  afterUpdateBuildProcess: function (response) {
    if (response.code == 200) {
      $("#loading-image").hide();
      $(".common-modal").modal("hide");
      toastr["success"]("Build Process successfully", "");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },

  editCancellationRecord: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl +
            "/store-website/" +
            ele.data("id") +
            "/edit-cancellation",
      method: "get",
    };
    this.sendAjax(_z, "editCancellationResult");
  },
  editCancellationResult: function (response) {
    var createWebTemplate = $.templates(
      "#template-create-website-cancellation"
    );
    var tplHtml = createWebTemplate.render(response);
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },
  submitFormSite: function (ele) {
    /*var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/save",
            method: "post",
            data : ele.closest("form").serialize(),
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "saveSite");*/
    var id = $("#form-create-website #store_website_id").val();
    var title = $("#form-create-website #swTitle").val();
    var website = $("#form-create-website #website").val();
    var semrush_project_id = $(
      "#form-create-website #semrush_project_id"
    ).val();
    var mailing_service_id = $(
      "#form-create-website #mailing_service_id"
    ).val();
    var sale_old_products = $("#form-create-website #sale_old_products").val();
    var is_debug_true = $("#form-create-website #is_debug_true").val();
    var is_dev_website = $("#form-create-website #is_dev_website").val();
    var description = $("#form-create-website #description").val();
    var send_in_blue_account = $(
      "#form-create-website #send_in_blue_account"
    ).val();
    var send_in_blue_api = $("#form-create-website #send_in_blue_api").val();
    var send_in_blue_smtp_email_api = $(
      "#form-create-website #send_in_blue_smtp_email_api"
    ).val();
    var remote_software = $("#form-create-website #remote_software").val();
    var magento_url = $("#form-create-website #magento_url").val();
    var dev_magento_url = $("#form-create-website #dev_magento_url").val();
    var stage_magento_url = $("#form-create-website #stage_magento_url").val();
    var magento_username = $("#form-create-website #magento_username").val();
    var magento_password = $("#form-create-website #magento_password").val();
    var api_token = $("#form-create-website #api_token").val();
    var dev_api_token = $("#form-create-website #dev_api_token").val();
    var stage_api_token = $("#form-create-website #stage_api_token").val();
    var facebook = $("#form-create-website #facebook").val();
    var facebook_remarks = $("#form-create-website #facebook_remarks").val();
    var product_markup = $("#form-create-website #product_markup").val();
    var instagram = $("#form-create-website #instagram").val();
    var instagram_remarks = $("#form-create-website #instagram_remarks").val();
    var cropper_color = $("#form-create-website #cropper_color").val();
    var cropping_size = $("#form-create-website #cropping_size").val();
    var logo_color = $("#form-create-website #logo_color").val();
    var logo_border_color = $("#form-create-website #logo_border_color").val();
    var text_color = $("#form-create-website #text_color").val();
    var border_color = $("#form-create-website #border_color").val();
    var border_thickness = $("#form-create-website #border_thickness").val();
    var country_duty = $("#form-create-website #country_duty").val(); //selected
    var is_published = $("#form-create-website #is_published").val(); // selected
    var disable_push = $("#form-create-website #disable_push").val(); //selected
    var website_source = $("#form-create-website #website_source").val(); //selected
    var server_ip = $("#form-create-website #server_ip").val();
    var repository_id = $("#form-create-website #repository_id").val(); //selected
    var staging_username = $("#form-create-website #staging_username").val();
    var staging_password = $("#form-create-website #staging_password").val();
    var mysql_username = $("#form-create-website #mysql_username").val();
    var mysql_password = $("#form-create-website #mysql_password").val();
    var mysql_staging_username = $(
      "#form-create-website #mysql_staging_username"
    ).val();
    var mysql_staging_password = $(
      "#form-create-website #mysql_staging_password"
    ).val();
    var push_web_key = $("#form-create-website #push_web_key").val();
    var push_web_id = $("#form-create-website #push_web_id").val();
    var icon = $("#form-create-website #icon").val();
    var is_price_override = $("#form-create-website #is_price_override").val(); // selected
    var files = $("#key_file_path1")[0].files[0];
    var project_id = $("#form-create-website #project_id").val();
    var site_folder = $("#form-create-website #site_folder").val();
    var store_code_id = $("#form-create-website #store_code_id").val();
    var working_directory = $("#form-create-website #working_directory").val();
    var assets_manager_id = $("#form-create-website #assets_manager_id").val();
    var database_name = $("#form-create-website #database_name").val();
    var instance_number = $("#form-create-website #instance_number").val();
    var website_store_project_id = $("#form-create-website #website_store_project_id").val();
    var aws_region = $("#form-create-website #aws_region").val();
    var aws_cluster = $("#form-create-website #aws_cluster").val();
    var aws_ecs_service_id = $("#form-create-website #aws_ecs_service_id").val();
    var aws_api_key = $("#form-create-website #aws_api_key").val();
    var aws_api_secret = $("#form-create-website #aws_api_secret").val();
    var aws_document_name = $("#form-create-website #aws_document_name").val();
    var cluster_file = $("#form-create-website #cluster_file").val();
    var pod_name = $("#form-create-website #pod_name").val();
    var cloud_platform = $("#form-create-website #cloud_platform").val();
    var ai_assistant = $("#form-create-website #ai_assistant").val();

    var formData = new FormData();
    formData.append("id", id);
    formData.append("title", title);
    formData.append("website", website);
    formData.append("semrush_project_id", semrush_project_id);
    formData.append("mailing_service_id", mailing_service_id);
    formData.append("sale_old_products", sale_old_products);
    formData.append("is_debug_true", is_debug_true);
    formData.append("is_dev_website", is_dev_website);
    formData.append("description", description);
    formData.append("send_in_blue_account", send_in_blue_account);
    formData.append("send_in_blue_api", send_in_blue_api);
    formData.append("send_in_blue_smtp_email_api", send_in_blue_smtp_email_api);
    formData.append("remote_software", remote_software);
    formData.append("magento_url", magento_url);
    formData.append("dev_magento_url", dev_magento_url);
    formData.append("stage_magento_url", stage_magento_url);
    formData.append("magento_username", magento_username);
    formData.append("magento_password", magento_password);
    formData.append("api_token", api_token);
    formData.append("dev_api_token", dev_api_token);
    formData.append("stage_api_token", stage_api_token);
    formData.append("facebook", facebook);
    formData.append("facebook_remarks", facebook_remarks);
    formData.append("product_markup", product_markup);
    formData.append("instagram", instagram);
    formData.append("instagram_remarks", instagram_remarks);
    formData.append("cropper_color", cropper_color);
    formData.append("cropping_size", cropping_size);
    formData.append("logo_color", logo_color);
    formData.append("logo_border_color", logo_border_color);
    formData.append("text_color", text_color);
    formData.append("border_color", border_color);
    formData.append("border_thickness", border_thickness);
    formData.append("country_duty", country_duty);
    formData.append("is_published", is_published);
    formData.append("disable_push", disable_push);
    formData.append("website_source", website_source);
    formData.append("server_ip", server_ip);
    formData.append("repository_id", repository_id);
    formData.append("staging_username", staging_username);
    formData.append("staging_password", staging_password);
    formData.append("mysql_username", mysql_username);
    formData.append("mysql_password", mysql_password);
    formData.append("mysql_staging_username", mysql_staging_username);
    formData.append("mysql_staging_password", mysql_staging_password);
    formData.append("push_web_key", push_web_key);
    formData.append("push_web_id", push_web_id);
    formData.append("icon", icon);
    formData.append("is_price_override", is_price_override);
    formData.append("project_id", project_id);
    formData.append("key_file_path1", files);
    formData.append("site_folder", site_folder);
    formData.append("store_code_id", store_code_id);
    formData.append("working_directory", working_directory);
    formData.append("assets_manager_id", assets_manager_id);
    formData.append("database_name", database_name);
    formData.append("instance_number", instance_number);
    formData.append("website_store_project_id", website_store_project_id);
    formData.append("aws_region", aws_region);
    formData.append("aws_cluster", aws_cluster);
    formData.append("aws_ecs_service_id", aws_ecs_service_id);
    formData.append("aws_api_key", aws_api_key);
    formData.append("aws_api_secret", aws_api_secret);
    formData.append("aws_document_name", aws_document_name);
    formData.append("cluster_file", cluster_file);
    formData.append("pod_name", pod_name);
    formData.append("cloud_platform", cloud_platform);
    formData.append("ai_assistant", ai_assistant);

    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
    });

    $.ajax({
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/store-website/save",
      type: "POST",
      contentType: false,
      processData: false,
      data: formData,
      success: function (response) {
        if (response.code == 200) {
          toastr["success"](response.message, "success");
        } else {
          toastr["error"](response.error, "error");
        }
      },
      error: function (response) {
        toastr["error"](response.error, "error");
      },
    });
  },
  submitFormSiteCancellation: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/store-website/save-cancellation",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveSiteCancellation");
  },

  submitDuplicate: function (ele) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/store-website/save-duplicate",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveSiteDuplicate");
  },

  submitMagentoUserForm: function (ele) {
    var username = ele.parents(".subMagentoUser").find(".userName").val();
    var userEmail = ele.parents(".subMagentoUser").find(".userEmail").val();
    var firstName = ele.parents(".subMagentoUser").find(".firstName").val();
    var lastName = ele.parents(".subMagentoUser").find(".lastName").val();

    var password = ele.parents(".subMagentoUser").find(".user-password").val();
    var websitemode = ele.parents(".subMagentoUser").find(".websiteMode").val();
    var store_id = $("#store_website_id").val();

    var adminpassword = $("#adminpassword").val();

    //use in user-lising-popup
    if (!store_id) {
      store_id = ele.parents(".subMagentoUser").find(".store_website_id").val();
    }

    var store_website_userid = ele.attr("data-id");
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/store-website/save-user-in-magento-admin-password",
      method: "post",
      data: {
        _token: $('meta[name="csrf-token"]').attr("content"),
        websitemode: websitemode,
        username: username,
        userEmail: userEmail,
        firstName: firstName,
        lastName: lastName,
        password: password,
        store_id: store_id,
        store_website_userid: store_website_userid,
      },
      beforeSend: function () {
        $("#loading-image").show();
      },
    };

    if(adminpassword==1){
      this.sendAjax(_z, "saveSiteAdminPassword");
    } else {
      this.sendAjax(_z, "saveSite");
    }
  },
  AddMagentoUserForm: function (ele) {
    var html =
      '<div class="subMagentoUser" style="border:1px solid #ccc;padding: 15px;margin-bottom:5px">' +
      '<div class="form-group">' +
      '<div class="row">' +
      '<div class="col-sm-6">' +
      '<label for="username">Username</label>' +
      '<input type="text" name="username" value="" class="form-control userName" id="username" placeholder="Enter Username">' +
      "</div>" +
      '<div class="col-sm-6">' +
      '<label for="userEmail">Email</label>' +
      '<input type="email" name="userEmail" value="" class="form-control userEmail" id="userEmail" placeholder="Enter Email">' +
      "</div>" +
      "</div>" +
      "</div>" +
      '<div class="form-group">' +
      '<div class="row">' +
      '<div class="col-sm-6">' +
      '<label for="firstName">First Name</label>' +
      '<input type="text" name="firstName" value="" class="form-control firstName" id="firstName" placeholder="Enter First Name">' +
      "</div>" +
      '<div class="col-sm-6">' +
      '<label for="lastName">Last Name</label>' +
      '<input type="text" name="lastName" value="" class="form-control lastName" id="lastName" placeholder="Enter Last Name">' +
      "</div>" +
      "</div>" +
      "</div>" +
      '<div class="form-group">' +
      '<div class="row">' +
      '<div class="col-sm-6">' +
      '<labelfor="password">Password</label>' +
      '<input type="password" name="password" value="" class="form-control user-password" id="password" placeholder="Enter Password">' +
      "</div>" +
      '<div class="col-sm-6">' +
      '<label for="website_mode">Website Mode</label>' +
      '<select name="website_mode" id="website_mode" class="form-control websiteMode"><option value="production">Production</option><option value="staging">Staging</option></select>' +
      "</div>" +
      "</div>" +
      "</div>" +
      '<div class="form-group">' +
      '<div class="row">' +
      '<div class="col-sm-5">' +
      '<button type="button" data-id="" class="btn btn-show-password btn-sm" style="border:1px solid">' +
      '<i class="fa fa-eye" aria-hidden="true"></i>' +
      "</button>" +
      '<button type="button" data-id="" class="btn btn-copy-password btn-sm" style="border:1px solid">' +
      '<i class="fa fa-clone" aria-hidden="true"></i>' +
      "</button>" +
      '<button type="button" data-id="" class="btn btn-edit-magento-user btn-sm" style="border:1px solid">' +
      '<i class="fa fa-check" aria-hidden="true"></i>' +
      "</button>" +
      '<button type="button" data-id="" class="btn btn-delete-magento-user btn-sm" style="border:1px solid">' +
      '<i class="fa fa-trash" aria-hidden="true"></i>' +
      "</button>" +
      "</div>" +
      "</div>" +
      "</div>" +
      "</div>";
    $(".MainMagentoUser").append(html);
  },

  deleteMagentoUserForm: function (ele) {
    var store_website_userid = ele.attr("data-id");
    if (store_website_userid != "") {
      var _z = {
        url:
          typeof href != "undefined"
            ? href
            : this.config.baseUrl + "/store-website/delete-user-in-magento",
        method: "post",
        data: {
          _token: $('meta[name="csrf-token"]').attr("content"),
          store_website_userid: store_website_userid,
        },
        beforeSend: function () {
          $("#loading-image").show();
        },
      };
    }
    ele.parents(".subMagentoUser").remove();
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
  saveSiteAdminPassword: function (response) {
    if (response.code == 200) {
        toastr["success"]("User details saved Successfully!", "Success");
      page.loadFirst();
      $(".common-modal").modal("hide");
      window.location = window.location;
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveSite: function (response) {
    if (response.code == 200) {
        toastr["success"]("User details saved Successfully!", "Success");
      page.loadFirst();
      $(".common-modal").modal("hide");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveSiteCancellation: function (response) {
    if (response.code == 200) {
      $("#loading-image").hide();
      $(".common-modal").modal("hide");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveSiteDuplicate: function (response) {
    if (response.code == 200) {
      page.loadFirst();
      $("#loading-image").hide();
      $(".common-modal").modal("hide");
      toastr["success"]("Store website(s) created successfully!", "Success");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  attachCategory: function (id) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/store-website/" + id + "/attached-category",
      method: "get",
    };
    this.sendAjax(_z, "showAttachedCategory");
  },
  showAttachedCategory: function (response) {
    $("#loading-image").hide();
    if (response.code == 200) {
      var createWebTemplate = $.templates("#template-attached-category");
      var tplHtml = createWebTemplate.render(response);
      var common = $(".common-modal");
      common.find(".modal-dialog").html(tplHtml);
      page.assignSelect2();
      common.modal("show");
    }
  },
  submitCategory: function (ele) {
    var website_id = ele
      .closest("form")
      .find('input[name="store_website_id"]')
      .val();
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl +
            "/store-website/" +
            website_id +
            "/attached-category",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "afterSubmitCategory");
  },
  afterSubmitCategory: function (response) {
    if (response.code == 200) {
      page.attachCategory(response.data.store_website_id);
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  deleteCategory: function (ele) {
    var storeWebsiteId = ele.data("store-website-id");
    var id = ele.data("id");

    var _z = {
      url:
        this.config.baseUrl +
        "/store-website/" +
        storeWebsiteId +
        "/attached-category/" +
        id +
        "/delete",
      method: "get",
    };

    this.sendAjax(_z, "deleteCategoryResponse", ele);
  },
  deleteCategoryResponse: function (response, ele) {
    if (response.code == 200) {
      ele.closest("tr").remove();
    }
  },
  attachBrands: function (id) {
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/store-website/" + id + "/attached-brand",
      method: "get",
    };
    this.sendAjax(_z, "showAttachedBrands");
  },
  showAttachedBrands: function (response) {
    $("#loading-image").hide();
    if (response.code == 200) {
      var createWebTemplate = $.templates("#template-attached-brands");
      var tplHtml = createWebTemplate.render(response);
      var common = $(".common-modal");
      common.find(".modal-dialog").html(tplHtml);
      page.assignSelect2();
      common.modal("show");
    }
  },
  submitAttachedBrands: function (ele) {
    var website_id = ele
      .closest("form")
      .find('input[name="store_website_id"]')
      .val();
    var _z = {
      url:
        this.config.baseUrl +
        "/store-website/" +
        website_id +
        "/attached-brand",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "afterSubmitAttachedBrands");
  },
  afterSubmitAttachedBrands: function (response) {
    if (response.code == 200) {
      page.attachBrands(response.data.store_website_id);
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  deleteAttachedBrands: function (ele) {
    var storeWebsiteId = ele.data("store-website-id");
    var id = ele.data("id");
    var _z = {
      url:
        this.config.baseUrl +
        "/store-website/" +
        storeWebsiteId +
        "/attached-brand/" +
        id +
        "/delete",
      method: "get",
    };
    this.sendAjax(_z, "deleteBrandResponse", ele);
  },
  deleteBrandResponse: function (response, ele) {
    if (response.code == 200) {
      ele.closest("tr").remove();
    }
  },
  getChildCategories: function (id) {
    var _z = {
      url: this.config.baseUrl + "/store-website/" + id + "/child-categories",
      method: "get",
    };
    this.sendAjax(_z, "showChildCategoriesFrom");
  },
  showChildCategoriesFrom: function (response) {
    if (response.code == 200) {
      var template = $.templates("#template-category-list");
      var tplHtml = template.render(response);
      $(".preview-category").html(tplHtml);

      $(".preview-category").on(
        "click",
        ".btn-delete-preview-category",
        function () {
          $(this).closest("tr").remove();
        }
      );

      $(".preview-category").on(
        "click",
        ".select-all-preview-category",
        function () {
          var table = $(this).closest("table");
          checkBoxes = table.find(".preview-checkbox");
          checkBoxes.prop("checked", !checkBoxes.prop("checked"));
        }
      );

      $(".preview-category").on(
        "click",
        ".save-preview-categories",
        function () {
          page.storeMultipleCategories($(this));
        }
      );
    }
  },
  storeMultipleCategories: function (ele) {
    var website_id = ele
      .closest(".modal-body")
      .find('input[name="store_website_id"]')
      .val();
    var categories = [];
    var selectedcategories = ele
      .closest(".modal-body")
      .find(".preview-checkbox:checked");
    if (selectedcategories.length > 0) {
      $.each(selectedcategories, function (k, v) {
        categories.push($(v).val());
      });
    }
    var _z = {
      url:
        this.config.baseUrl +
        "/store-website/" +
        website_id +
        "/attached-categories",
      method: "post",
      data: {
        _token: $('meta[name="csrf-token"]').attr("content"),
        website_id: website_id,
        categories: categories,
      },
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "afterMultipleCategories");
  },
  afterMultipleCategories: function (response) {
    if (response.code == 200) {
      page.attachCategory(response.data.store_website_id);
    }
  },
  showRemarks: function (field, id, remarks) {
    $("#loading-image").hide();
    var createWebTemplate = $.templates("#template-update-remarks");
    var tplHtml = createWebTemplate.render({
      field: field,
      id: id,
      remarks: remarks,
    });
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },
  submitRemarks: function (ele) {
    var website_id = ele.closest("form").find(".frm_store_website_id").val();
    var _z = {
      url:
        this.config.baseUrl +
        "/store-website/" +
        website_id +
        "/submit-social-remarks",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "afterSubmitRemarks");
  },
  afterSubmitRemarks: function (response) {
    $("#loading-image").hide();
    if (response.code == 200) {
      $(".common-modal").modal("hide");
      toastr["success"](response.message, "success");
      page.loadFirst();
    } else {
      toastr["error"](response.message, "error");
    }
  },
  openGenerateFile: function () {
    var createWebTemplate = $.templates("#template-generate-file");
    var tplHtml = createWebTemplate.render({});
    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },
};

$.extend(page, common);
