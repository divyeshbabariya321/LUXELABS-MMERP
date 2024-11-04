var page = {
  init: function (settings) {
    page.config = {
      bodyView: settings.bodyView,
    };

    

    settings.baseUrl += "/test-suites";

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

    // page.config.bodyView.on("click",".btn-add-action",function(e) {
    //     e.preventDefault();
    //     page.createRecord();
    // });
    page.config.bodyView.on("click", ".btn-add-environment", function (e) {
      e.preventDefault();
      page.createEnvironment();
    });
    page.config.bodyView.on("click", ".btn-add-severity", function (e) {
      e.preventDefault();
      page.createSeverity();
    });
    page.config.bodyView.on("click", ".btn-add-type", function (e) {
      e.preventDefault();
      page.createType();
    });
    page.config.bodyView.on("click", ".btn-add-status", function (e) {
      e.preventDefault();
      page.createStatus();
    });

    page.config.bodyView.on("click", ".send-message", function (e) {
      e.preventDefault();
      var id = $(this).data("id");
      var message = $("#getMsg" + id).val();
      if (message != null && message != "") {
        page.sendMessage(id, message);
      }
    });
    page.config.bodyView.on("change", ".assign_to", function (e) {
      e.preventDefault();
      page.sendAssign($(this));
    });
    page.config.bodyView.on("change", ".bug_severity_id", function (e) {
      e.preventDefault();
      page.sendSeverity($(this));
    });
    page.config.bodyView.on("change", ".bug_status_id", function (e) {
      e.preventDefault();
      page.sendStatus($(this));
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

    $(".common-modal").on("click", ".submit-environment", function () {
      page.submitEnvironment($(this));
    });
    $(".common-modal").on("click", ".submit-severity", function () {
      page.submitSeverity($(this));
    });
    $(".common-modal").on("click", ".submit-type", function () {
      page.submitType($(this));
    });
    $(".common-modal").on("click", ".submit-status", function () {
      page.submitStatus($(this));
    });
    page.config.bodyView.on("click", ".btn-push", function (e) {
      page.push($(this));
    });

    page.config.bodyView.on("click", ".btn-update", function (e) {
      page.updateData($(this));
    });
    page.config.bodyView.on(
      "click",
      ".btn-load-communication-modal",
      function (e) {
        page.communicationModel($(this));
      }
    );
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
      url: this.config.baseUrl + "/records",
      method: "get",
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "showResults");
  },
  getResults: function (href) {
    var _z = {
      url: typeof href != "undefined" ? href : this.config.baseUrl + "/records",
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
      url: this.config.baseUrl + "/" + ele.data("id") + "/delete",
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
      toastr["success"]("Test suites deleted successfully", "success");
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
  createEnvironment: function (response) {
    var createWebTemplate = $.templates("#template-bug-environment");
    var tplHtml = createWebTemplate.render({ data: {} });

    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },
  createSeverity: function (response) {
    var createWebTemplate = $.templates("#template-bug-severity");
    var tplHtml = createWebTemplate.render({ data: {} });

    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },
  createType: function (response) {
    var createWebTemplate = $.templates("#template-bug-type");
    var tplHtml = createWebTemplate.render({ data: {} });

    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },
  createStatus: function (response) {
    var createWebTemplate = $.templates("#template-bug-status");
    var tplHtml = createWebTemplate.render({ data: {} });

    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },

  editRecord: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/edit/" + ele.data("id"),
      method: "get",
    };
    this.sendAjax(_z, "editResult");
  },

  editResult: function (response) {
    $("#bugtrackingEditModal").modal("show");
    $(".id").val("");
    $(".name").val("");
    $(".test_cases").val("");
    $(".step_to_reproduce").val("");
    $(".url").val("");
    $(".bug_environment_id").val("");
    $(".assign_to").val("");
    $(".bug_status_id").val("");
    $(".module_id").val("");
    $(".remark").val("");
    $(".website").val("");

    $(".id").val(response.data.id);
    $(".name").val(response.data.name.replaceAll("<br>", "\n"));
    $(".name").val(response.data.name.replaceAll("<br/>", "\n"));
    if (
      response.data.test_cases != null &&
      response.data.test_cases != "null" &&
      response.data.test_cases != ""
    ) {
      var test_cases = response.data.test_cases.replaceAll("<br>", "\n");
      $(".test_cases").val(test_cases.replaceAll("<br/>", "\n"));
    } else {
      $(".test_cases").val("");
    }

    if (
      response.data.step_to_reproduce != null &&
      response.data.step_to_reproduce != "null" &&
      response.data.step_to_reproduce != ""
    ) {
      var step_to_reproduce = response.data.step_to_reproduce.replaceAll(
        "<br>",
        "\n"
      );
      $(".step_to_reproduce").val(step_to_reproduce.replaceAll("<br/>", "\n"));
    } else {
      $(".step_to_reproduce").val("");
    }

    $(".url").val(response.data.url);
    $(".bug_environment_id").val(response.data.bug_environment_id);
    $(".bug_environment_ver").val(response.data.bug_environment_ver);
    $(".assign_to").val(response.data.assign_to);
    $(".bug_status_id").val(response.data.bug_status_id);
    $(".module_id").val(response.data.module_id);
    $(".remark").val(response.data.remark);
    $(".website").val(response.data.website);
  },

  submitFormSite: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/store",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveSite");
  },

  updateData: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/update",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveSite");
  },
  sendMessage: function (id, message) {
    var _z = {
      url: this.config.baseUrl + "/sendmessage",
      method: "post",
      data: { id: id, message: message },
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveMessage");
  },
  sendAssign: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/assign_user",
      method: "POST",
      data: {
        id: ele.data("id"),
        user_id: ele.val(),
        _token: ele.data("token"),
      },
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveAssign");
  },
  sendSeverity: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/severity_user",
      method: "POST",
      data: {
        id: ele.data("id"),
        severity_id: ele.val(),
        _token: ele.data("token"),
      },
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveSeverity");
  },
  sendStatus: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/status_user",
      method: "POST",
      data: {
        id: ele.data("id"),
        status_id: ele.val(),
        _token: ele.data("token"),
      },
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveStatus");
  },

  submitEnvironment: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/environment",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveEnvironment");
  },
  submitSeverity: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/severity",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveSeverity");
  },
  submitType: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/type",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveType");
  },
  submitStatus: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/status",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveStatus");
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
      toastr["success"](response.message, "Test Suites Saved Successfully");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveMessage: function (response) {
    if (response.code == 200) {
      $("#loading-image").hide();

      page.loadFirst();
      // $(".common-modal").modal("hide");
      toastr["success"](response.message, "Test Suites Saved Successfully");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveAssign: function (response) {
    if (response.code == 200) {
      // $("#loading-image").hide();
      location.reload();
      // page.loadFirst();
      // $(".common-modal").modal("hide");
      toastr["success"](response.message, "Test Suites Changed Successfully");
    } else {
      // $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveSeverity: function (response) {
    if (response.code == 200) {
      // $("#loading-image").hide();
      location.reload();
      // page.loadFirst();
      // $(".common-modal").modal("hide");
      toastr["success"](response.message, "Test Suites Changed Successfully");
    } else {
      // $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveStatus: function (response) {
    if (response.code == 200) {
      // $("#loading-image").hide();

      location.reload();
      // $(".common-modal").modal("hide");
      toastr["success"](response.message, "Test Suites Changed Successfully");
    } else {
      // $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveEnvironment: function (response) {
    if (response.code == 200) {
      page.loadFirst();
      $(".common-modal").modal("hide");
      toastr["success"](response.message, "Environment Saved Successfully");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveSeverity: function (response) {
    if (response.code == 200) {
      page.loadFirst();
      $(".common-modal").modal("hide");
      toastr["success"](response.message, "Severity Saved Successfully");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveType: function (response) {
    if (response.code == 200) {
      page.loadFirst();
      $(".common-modal").modal("hide");
      toastr["success"](response.message, "Type Saved Successfully");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveStatus: function (response) {
    if (response.code == 200) {
      page.loadFirst();
      $(".common-modal").modal("hide");
      toastr["success"](response.message, "Status Saved Successfully");
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
          : this.config.baseUrl + "/bug-history/" + ele.data("id"),
      method: "get",
    };
    this.sendAjax(_z, "afterPush");
  },

  communicationModel: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/communicationData/" + ele.data("id"),
      method: "get",
    };
    this.sendAjax(_z, "afterCommunication");
  },
  afterPush: function (response) {
    if (response.code == 200) {
      
      $("#newHistoryModal").modal("show");

      $(".tbh").html("");
      if (response.data.length > 0) {
        var html = "";

        $.each(response.data, function (i, item) {
          
          html += "<tr>";
          html += " <th>" + item.name + "</th>";
          html += " <th>" + item.bug_environment_id + "</th>";
          html += " <th>" + item.bug_status_id + "</th>";
          html += " <th>" + item.module_id + "</th>";
          html += " <th>" + item.updated_by + "</th>";
          html += "</tr>";
        });

        $(".tbh").html(html);
      }
      toastr["success"](
        response.message,
        "Test Suites History Listed Successfully"
      );
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "Something went wrong");
    }
  },

  afterCommunication: function (response) {
    if (response.code == 200) {
      
      $("#newCommunictionModal").modal("show");

      $(".tbh").html("");
      if (response.data.length > 0) {
        var html = "";

        $.each(response.data, function (i, item) {
          
          html += "<tr class='in-background filter-message reviewed_msg'>";
          html += " <th>" + item.message + "</th>";

          html += " <th>" + item.user_name + "</th>";
          html += "</tr>";
        });

        $(".tbhc").html(html);
      }
      // toastr["success"](response.message,"Bug Tracking History Listed Successfully");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "Something went wrong");
    }
  },
};

$.extend(page, common);

$(document).on("click", ".btn-save-bug", function () {
  $(".text-danger").html("");
  if ($("#name_bug").val() == "") {
    $("#name_bug").next().text("Please enter the name");
    return false;
  }
  if ($("#test_cases_bug").val() == "") {
    $("#test_cases_bug").next().text("Please enter the test cases");
    return false;
  }
  if ($("#step_to_reproduce_bug").val() == "") {
    $("#step_to_reproduce_bug").next().text("Please enter the steps");
    return false;
  }

  if ($("#url_bug").val() == "") {
    $("#url_bug").next().text("Please enter the url");

    return false;
  }

  if (
    $("#bug_environment_id_bug").val() == "" ||
    $("#bug_environment_id_bug").val() == null ||
    $("#bug_environment_id_bug").val() == "null"
  ) {
    $("#bug_environment_id_bug").next().text("Please enter the environment");
    return false;
  }

  if (
    $("#assign_to_bug").val() == "" ||
    $("#assign_to_bug").val() == null ||
    $("#assign_to_bug").val() == "null"
  ) {
    $("#assign_to_bug").next().text("Please enter the assign to");
    return false;
  }

  if (
    $("#bug_status_id_bug").val() == "" ||
    $("#bug_status_id_bug").val() == null ||
    $("#bug_status_id_bug").val() == "null"
  ) {
    $("#bug_status_id_bug").next().text("Please enter the status");
    return false;
  }
  if (
    $("#module_id_bug").val() == "" ||
    $("#module_id_bug").val() == null ||
    $("#module_id_bug").val() == "null"
  ) {
    $("#module_id_bug").next().text("Please enter the module");
    return false;
  }
  if ($("#remark_bug").val() == "") {
    $("#remark_bug").next().text("Please enter the remark");
    return false;
  }
  if (
    $("#website_bug").val() == "" ||
    $("#website_bug").val() == null ||
    $("#website_bug").val() == "null"
  ) {
    $("#website_bug").next().text("Please enter the website");
    return false;
  }
  return true;
});

$(document).on("click", ".btn-update-bug", function () {
  $(".text-danger").html("");

  if ($("#name_update").val() == "") {
    $("#name_update").next().text("Please enter the name");
    return false;
  }
  if ($("#test_cases_update").val() == "") {
    $("#test_cases_update").next().text("Please enter the test cases");
    return false;
  }
  if ($("#step_to_reproduce_update").val() == "") {
    $("#step_to_reproduce_update").next().text("Please enter the steps");
    return false;
  }

  if ($("#url_update").val() == "") {
    $("#url_update").next().text("Please enter the url");

    return false;
  }

  if (
    $("#bug_environment_id_update").val() == "" ||
    $("#bug_environment_id_update").val() == null ||
    $("#bug_environment_id_update").val() == "null"
  ) {
    $("#bug_environment_id_update").next().text("Please enter the environment");
    return false;
  }

  if (
    $("#assign_to_update").val() == "" ||
    $("#assign_to_update").val() == null ||
    $("#assign_to_update").val() == "null"
  ) {
    $("#assign_to_update").next().text("Please enter the assign to");
    return false;
  }

  if (
    $("#bug_status_id_update").val() == "" ||
    $("#bug_status_id_update").val() == null ||
    $("#bug_status_id_update").val() == "null"
  ) {
    $("#bug_status_id_update").next().text("Please enter the status");
    return false;
  }

  if (
    $("#module_id_update").val() == "" ||
    $("#bug_status_id_update").val() == null ||
    $("#bug_status_id_update").val() == "null"
  ) {
    $("#module_id_update").next().text("Please enter the module");
    return false;
  }
  if ($("#remark_update").val() == "") {
    $("#remark_update").next().text("Please enter the remark");
    return false;
  }
  if (
    $("#website_update").val() == "" ||
    $("#website_update").val() == null ||
    $("#website_update").val() == "null"
  ) {
    $("#website_update").next().text("Please enter the website");
    return false;
  }
  return true;
});
