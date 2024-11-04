var page = {
  init: function (settings) {
    page.config = {
      bodyView: settings.bodyView,
    };
    
    settings.baseUrl += "/test-case-automation";
    $.extend(page.config, settings);
    page.config.bodyView.on("click", ".btn-add-action", function (e) {
      e.preventDefault();
      page.createRecord();
    });
    page.config.bodyView.on("click", ".btn-add-status", function (e) {
      e.preventDefault();
      page.createStatus();
    });
    page.config.bodyView.on("click", ".btn-add-test-case-modal", function (e) {
      e.preventDefault();
      
      page.createTestCase();
    });

    page.config.bodyView.on("click", ".btn-push", function (e) {
      page.push($(this));
    });

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

    $(".common-modal").on("click", ".submit-status", function () {
      page.submitStatus($(this));
    });
    $(".common-modal").on("click", ".submit-test-cases", function () {
      page.submitTestCases($(this));
    });

    page.config.bodyView.on("click", ".btn-update", function (e) {
      page.updateData($(this));
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
  },
  loadFirst: function () {
    window.location.reload();
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
    var createWebTemplate = $.templates("#template-test-status");
    var tplHtml = createWebTemplate.render({ data: {} });

    var common = $(".common-modal");
    common.find(".modal-dialog").html(tplHtml);
    common.modal("show");
  },

  editResult: function (response) {
    $("#testcaseEditModal").modal("show");
    $(".id").val("");
    $(".name").val("");
    $(".step_to_reproduce").val("");
    $(".suite").val("");
    $(".module_id").val("");
    $(".assign_to_edit").val("");
    $(".precondition").val("");
    $(".expected_result").val("");
    $(".test_status_id").val("");
    $(".website").val("");

    $(".id").val(response.data.id);
    $(".name").val(response.data.name);
    $(".step_to_reproduce").val(response.data.step_to_reproduce);
    $(".suite").val(response.data.suite);
    $(".module_id").val(response.data.module_id);
    $(".assign_to_edit").val(response.data.assign_to);
    $(".precondition").val(response.data.precondition);
    $(".expected_result").val(response.data.expected_result);
    $(".test_status_id").val(response.data.test_status_id);
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
    this.sendAjax(_z, "saveSite", null, true);
  },
  usertestHistory: function (ele) {
    // alert('v');
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/usertest-history/" + ele.data("id"),
      method: "get",
    };
    this.sendAjax(_z, "aftertestUser", null, true);
  },

  userteststatusHistory: function (ele) {
    // alert('v');
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/user-teststatus-history/" + ele.data("id"),
      method: "get",
    };
    this.sendAjax(_z, "afterteststatusUser", null, true);
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
    this.sendAjax(_z, "saveSite", null, true);
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
    this.sendAjax(_z, "saveMessage", null, true);
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
    this.sendAjax(_z, "saveAssign", null, true);
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
    this.sendAjax(_z, "saveSeverity", null, true);
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
    this.sendAjax(_z, "saveStatus", null, true);
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
    this.sendAjax(_z, "saveEnvironment", null, true);
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
    this.sendAjax(_z, "saveSeverity", null, true);
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
    this.sendAjax(_z, "saveType", null, true);
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
    this.sendAjax(_z, "saveStatus", null, true);
  },
  submitTestCases: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/add-test-cases",
      method: "post",
      data: ele.closest("form").serialize(),
      beforeSend: function () {
        $("#loading-image").show();
      },
    };
    this.sendAjax(_z, "saveBugTestCases", null, true);
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
      toastr["success"](response.message, "Bug Tracking Saved Successfully");
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
      toastr["success"](response.message, "Test Cases Saved Successfully");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveAssign: function (response) {
    if (response.code == 200) {
      $("#loading-image").hide();
      // location.reload()
      // page.loadFirst();
      // $(".common-modal").modal("hide");
      toastr["success"](
        response.message,
        "Test case User Changed Successfully"
      );
    } else {
      // $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveSeverity: function (response) {
    if (response.code == 200) {
      $("#loading-image").hide();
      // location.reload()
      // page.loadFirst();1
      // $(".common-modal").modal("hide");
      toastr["success"](response.message, "Bug Tracking Changed Successfully");
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
      toastr["success"](response.message, "Bug Tracking Changed Successfully");
    } else {
      // $("#loading-image").hide();
      toastr["error"](response.error, "");
    }
  },
  saveBugTestCases: function (response) {
    if (response.code == 200) {
      $("#loading-image").hide();

      // location.reload()
      $(".common-modal").modal("hide");
      toastr["success"](response.message, "Test Cases Added Successfully");
    } else {
      $("#loading-image").hide();
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
          : this.config.baseUrl + "/test-case-history/" + ele.data("id"),
      method: "get",
    };
    this.sendAjax(_z, "afterPush", null, true);
  },

  communicationModel: function (ele) {
    var _z = {
      url: this.config.baseUrl + "/communicationData/" + ele.data("id"),
      method: "get",
    };
    this.sendAjax(_z, "afterCommunication", null, true);
  },
  aftertestUser: function (response) {
    if (response.code == 200) {
      
      $("#newtestHistoryModal").modal("show");

      $(".tbhusertest").html("");
      if (response.data.length > 0) {
        var html = "";

        $.each(response.data, function (i, item) {
          
          var created_date = $.datepicker.formatDate(
            "dd-M-yy",
            new Date(item.created_at)
          );
          html += "<tr>";
          html += " <th>" + created_date + "</th>";
          html += " <th>" + item.new_user + "</th>";
          html += " <th>" + item.old_user + "</th>";
          html += " <th>" + item.updated_by + "</th>";

          html += "</tr>";
        });

        $(".tbhusertest").html(html);
      }
      toastr["success"](response.message, "Test Cases Listed Successfully");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "Something went wrong");
    }
  },

  afterteststatusUser: function (response) {
    if (response.code == 200) {
      
      $(".newstatusHistory").modal("show");

      $(".tbhuserteststatus").html("");
      if (response.data.length > 0) {
        var html = "";

        $.each(response.data, function (i, item) {
          
          var created_date = $.datepicker.formatDate(
            "dd-M-yy",
            new Date(item.created_at)
          );
          html += "<tr>";
          html += " <th>" + created_date + "</th>";
          html += " <th>" + item.new_status + "</th>";
          html += " <th>" + item.old_status + "</th>";
          html += " <th>" + item.updated_by + "</th>";

          html += "</tr>";
        });

        $(".tbhuserteststatus").html(html);
      }
      toastr["success"](response.message, "Test Cases Listed Successfully");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "Something went wrong");
    }
  },
  afterPush: function (response) {
    if (response.code == 200) {
      
      $("#newHistoryModal").modal("show");

      $(".tbh").html("");
      if (response.data.length > 0) {
        var html = "";

        $.each(response.data, function (i, item) {
          
          var created_date = $.datepicker.formatDate(
            "dd-M-yy",
            new Date(item.created_at)
          );
          html += "<tr>";
          html += " <th>" + created_date + "</th>";
          html += " <th>" + item.name + "</th>";
          html += " <th>" + item.test_status_id + "</th>";
          html += " <th>" + item.suite + "</th>";
          html += " <th>" + item.expected_result + "</th>";
          html += " <th>" + item.assign_to + "</th>";
          html += " <th>" + item.module_id + "</th>";
          html += " <th>" + item.updated_by + "</th>";
          html += "</tr>";
        });

        $(".tbh").html(html);
      }
      toastr["success"](
        response.message,
        "Test Cases History Listed Successfully"
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
      // toastr["success"](response.message,"Test Cases History Listed Successfully");
    } else {
      $("#loading-image").hide();
      toastr["error"](response.error, "Something went wrong");
    }
  },
};

$.extend(page, common);
