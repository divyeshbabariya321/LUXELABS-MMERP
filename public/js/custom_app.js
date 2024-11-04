$(document).on("click", ".menu_editor_copy", function () {
  var content = $(this).data("content");

  menucopyToClipboard(content);
  /* Alert the copied text */
  toastr["success"]("Copied the text: " + content);
});

function menucopyToClipboard(text) {
  var sampleTextarea = document.createElement("textarea");
  document.body.appendChild(sampleTextarea);
  sampleTextarea.value = text; //save main text in it
  sampleTextarea.select(); //select textarea contenrs
  document.execCommand("copy");
  document.body.removeChild(sampleTextarea);
}

$("#FormModalAppLayout").submit(function (e) {
  e.preventDefault();
  let name = $("#name-app-layout").val();
  let category = $("#categorySelect-app-layout").val();
  if (category.length == 0) {
    toastr["error"]("Select Category", "Message");
    return false;
  }
  let content = CKEDITOR.instances["content-app-layout"].getData(); //$('#cke_content').html();//$("#content").val();
  if (content == "") {
    toastr["error"]("Content not", "Message");
    return false;
  }
  let _token = $("input[name=_token]").val();
  $.ajax({
    url: configs.routes.sop_store,
    type: "POST",
    data: {
      name: name,
      category: category,
      content: content,
      _token: _token,
    },
    success: function (response) {
      if (response) {
        if (response.success == false) {
          toastr["error"](response.message, "Message");
          return false;
        }
        location.reload();
      }
    },
  });
});

$(document).on("click", ".menu-sop-search", function (e) {
  e.preventDefault();
  loadSopSearchModal();
});

// Global user search from the menu - S
$(document).on("click", ".menu-user-search", function (e) {
  e.preventDefault();
  $("#modal-container").load("/menu-user-search-modal", function () {
    $("#menu-user-search-model").modal("show");
    get_user_data();
  });
});

$(document).on("click", ".create-sop-shortcut", function (e) {
  e.preventDefault();
  $("#modal-container").load("/create-sop-shortcut", function () {
    $("#create-sop-shortcut").modal("show");
  });
});
$(document).on("click", ".system-request", function (e) {
  e.preventDefault();
  $("#modal-container").load("/system-request", function () {
    $("#system-request").modal("show");
  });
});

$(document).on("click", ".user_search", function (e) {
  e.preventDefault();
  $("#modal-container").load("/quickRequestZoomModal", function () {
    $("#quickRequestZoomModal").modal("show");
  });
});

$(document).on("click", ".addnotesop", function (e) {
  e.preventDefault();
  $("#modal-container-1").load("/exampleModalAppLayout", function () {
    $("#exampleModalAppLayout").modal("show");
  });
});

$(document).on("click", ".menu-user-search-btn", function (e) {
  e.preventDefault();
  get_user_data();
});

function get_user_data() {
  let _token = $('meta[name="csrf-token"]').attr("content");
  $(".processing-txt").removeClass("d-none");
  $.ajax({
    url: configs.routes.user_search_global,
    type: "POST",
    data: {
      q: $("#menu_user_search").val().trim(),
      _token: _token,
    },
    success: function (response) {
      var trData = "";
      $(".processing-txt").addClass("d-none");
      if (response) {

        $(".user_search_global_result").html(response.html);

      }
    },
  });
}

$(document).on("click", ".copy_the_text", function (e) {
  // Get the text content of the element
  var textToCopy = $(this).prev("span.copy_me").text();

  // Create a temporary input element
  var tempInput = $("<input>");

  // Set its value to the text content
  tempInput.val(textToCopy);

  // Append it to the body
  $("body").append(tempInput);

  // Select the text in the input
  tempInput.select();

  // Copy the selected text to the clipboard
  document.execCommand("copy");

  // Remove the temporary input element
  tempInput.remove();

  // Optionally, provide feedback to the user
  toastr["success"]("Text copied!", "success");
});

// Global user search from the menu - E

$(document).on("click", ".menu-email-search", function (e) {
  e.preventDefault();
  loadEmailSearchModal();
});

$(document).ready(function () {
  $("#searchField").on("keyup", function () {
    var searchText = $(this).val().toLowerCase().replace(/\s/g, ""); // Convert to lowercase and remove spaces

    if (searchText) {
      $(".quick-icon").each(function () {
        var title = $(this).attr("title").toLowerCase().replace(/\s/g, ""); // Convert to lowercase and remove spaces
        var className = $(this).attr("class").toLowerCase().replace(/\s/g, ""); // Convert to lowercase and remove spaces

        if (
          title.indexOf(searchText) !== -1 ||
          className.indexOf(searchText) !== -1
        ) {
          $(this).closest("li").addClass("highlight"); // Add highlight class to the parent li element
          $(this).addClass("highlight");
          // $(this).closest('li').addClass('highlight'); // Add highlight class to the parent li element
        } else {
          $(this).removeClass("highlight");
          $(this).closest("li").removeClass("highlight"); // Remove highlight class from the parent li element
        }
      });
    } else {
      $(".quick-icon").removeClass("highlight");
      $(".quick-icon").closest("li").removeClass("highlight"); // Remove highlight class from all parent li elements when searchText is empty
    }
  });
});

$(document).on("click", ".send-message-open-menu", function (event) {
  var thiss = $(this);
  var $this = $(this);
  var data = new FormData();
  var sop_user_id = $(this).data("user_id");
  var id = $(this).data("id");
  var sop_user_id = $("#user_" + id).val();
  var message = $(this)
    .parents("td")
    .find("#messageid_" + id)
    .val();

  if (message.length > 0) {
    //  let self = textBox;
    $.ajax({
      url: configs.routes.wa_sendMessage,
      type: "POST",
      data: {
        sop_user_id: sop_user_id,
        message: message,
        _token: $('meta[name="csrf-token"]').attr("content"),
        status: 2,
      },
      dataType: "json",
      success: function (response) {
        $this
          .parents("td")
          .find("#messageid_" + sop_user_id)
          .val("");
        toastr["success"]("Message sent successfully!", "Message");
        $("#message_list_" + sop_user_id).append(
          "<li>" +
          response.message.created_at +
          " : " +
          response.message.message +
          "</li>"
        );
      },
      error: function (response) {
        toastr["error"]("There was an error sending the message...", "Message");
      },
    });
  } else {
    toastr["error"]("Please enter a message first", "Error");
  }
});

// $(document).on("hidden.bs.modal", "#chat-list-history", function () {
//   $("body").removeClass("openmodel");
// });
// $(document).on("shown.bs.modal", "#chat-list-history", function () {
//   $("body").addClass("openmodel");
// });

$(document).on("change", ".sop_drop_down", function () {
  var val = $(this).val();

  if ($(this).val() == "knowledge_base") {
    $(this)
      .parents(".add_sop_modal")
      .find(".knowledge_base")
      .removeAttr("hidden");
    $(".sop_solution").addClass("hidden");
  } else if ($(this).val() == "code_shortcut") {
    $(".sop_solution").removeClass("hidden");
    $(this)
      .parents(".add_sop_modal")
      .find(".knowledge_base")
      .attr("hidden", true)
      .val("");
    $(this)
      .parents(".add_sop_modal")
      .find(".knowledge_base_book")
      .attr("hidden", true)
      .val("");
  } else {
    $(this)
      .parents(".add_sop_modal")
      .find(".knowledge_base")
      .attr("hidden", true)
      .val("");
    $(this)
      .parents(".add_sop_modal")
      .find(".knowledge_base_book")
      .attr("hidden", true)
      .val("");
    $(".sop_solution").addClass("hidden");
  }

  $(".input-tag-container").each(function () {
    $(this).addClass("hidden");
  });

  switch ($(this).val()) {
    case "reply_shortcut":
      //@TODO: Load category data
      //loadreplycategories
      var loadReplyCategoriesUrl = "/livechat/loadreplycategories";
      $.ajax({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: loadReplyCategoriesUrl,
        type: "get",
      })
        .done(function (response) {
          $("#category_id_dropdown").html(response);
        })
        .fail(function (errObj) { });
      $(".reply-shortcut-input-tags-container").removeClass("hidden");
      break;
    case "devoops_modules":
      $(".add-devoops-modules-input-tags-container").removeClass("hidden");
      break;
    case "todo_shortcut":
      $(".add-todo-shortcut-input-tags-container").removeClass("hidden");
      break;
    default:
      $(".other-input-tags-container").removeClass("hidden");
      break;
  }

  var selectedOptionText = $(this).find("option:selected").text();

  $(this).parents(".add_sop_modal").find(".category").val(selectedOptionText);
});

$(document).on("click", ".expand-row-email", function () {
  var selection = window.getSelection();
  if (selection.toString().length === 0) {
    $(this).find(".td-mini-email-container").toggleClass("hidden");
    $(this).find(".td-full-email-container").toggleClass("hidden");
  }
});

$(document).ready(function () {
  $("#unreadEmail").change(function () {
    var userEmaillUrl = "/email/email-frame/" + $(this).val();

    $.ajax({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      url: userEmaillUrl,
      type: "get",
    })
      .done(function (response) { })
      .fail(function (errObj) { });
  });
});

window.openQuickMsg = function (userEmail) {
  $("#unreadEmail").prop("checked", false);

  $("#iframe").attr("src", "");
  var userEmaillUrl = "/email/email-frame/" + userEmail.id;

  $("#unreadEmail").val(userEmail.id);

  $.ajax({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    url: userEmaillUrl,
    type: "get",
  })
    .done(function (response) { })
    .fail(function (errObj) { });

  var isHTML = isHTMLContent(userEmail.message);
  if (isHTML) {
    $("#formattedContent").html(userEmail.message);
  } else {
    var formattedHTML = formatContentToHTML(userEmail.message);
    $("#formattedContent").html(formattedHTML);
  }

  $("#receiver_email").val(userEmail.to);
  $("#reply_email_id").val(userEmail.id);

  function isHTMLContent(content) {
    return /<[a-z][\s\S]*>/i.test(content);
  }

  function formatContentToHTML(rawContent) {
    var decodedContent = $("<textarea/>").html(rawContent).text();
    var formattedContent = decodedContent.replace(/\n/g, "<br>");
    formattedContent = formattedContent.replace(
      /(https?:\/\/[^\s]+)/g,
      '<a href="$1">$1</a>'
    );
    formattedContent =
      '<div class="form-control" style=" height: auto;">' +
      formattedContent +
      "</div>";

    return formattedContent;
  }
  $("#quickemailSubject").val(userEmail.subject);
  $("#quickemailDate").html(
    moment(userEmail.created_at).format("YYYY-MM-DD H:mm:ss")
  );
  $("#quickemailFrom").html(userEmail.from_full ? userEmail.from_full : "--");
  $("#quickemailTo").html(userEmail.to_full ? userEmail.to_full : "--");
  $("#quickemailCC").html(userEmail.cc ? userEmail.cc : "--");
  $("#quickemailBCC").html(userEmail.bcc ? userEmail.bcc : "--");
  $("#iframe").attr("src", userEmaillUrl);

  var senderName = "Hello " + userEmail.from.split("@")[0] + ",";
  $("#sender_email_address").val(userEmail.from);

  addTextToEditor(senderName);
};

$(document).on("click", ".updatedeclienremarks", function (e) {
  e.preventDefault();
  var appointment_requests_id = $("#appointment_requests_id").val();
  var appointment_requests_remarks = $("#appointment_requests_remarks").val();

  if (appointment_requests_id == "") {
    toastr["error"]("Something went wrong. Please try again.", "Error");
    return false;
  }

  if (appointment_requests_remarks == "") {
    $("#appointment_requests_remarks").next().text("Please enter the subject");
    return false;
  }

  $.ajax({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    url: configs.routes.appointment_request_declien_remarks,
    type: "post",
    data: {
      appointment_requests_id: appointment_requests_id,
      appointment_requests_remarks: appointment_requests_remarks,
    },
    beforeSend: function () {
      $("#loading-image").show();
    },
  })
    .done(function (response) {
      $("#loading-image").hide();

      if (data.code == 500) {
        toastr["error"]("Something went wrong. Please try again.");
      } else {
        $("#decline-remarks").modal("hide");
        toastr["success"](response.message);
        $("#menu_sop_edit_form").reset();
      }
    })
    .fail(function (errObj) {
      $("#loading-image").hide();
      toastr["error"]("Something went wrong. Please try again.");
      location.reload();
    });
});

$(document).on("click", ".submit-reply-email", function (e) {
  e.preventDefault();

  var quickemailSubject = $("#quickemailSubject").val();
  var formattedContent = $("#formattedContent").html();
  var replyMessage = $("#reply-message").val();
  var receiver_email = $("#receiver_email").val();
  var reply_email_id = $("#reply_email_id").val();

  var pass_history = $("#pass_history").prop("checked");
  if (pass_history) {
    pass_history = 1;
  } else {
    pass_history = 0;
  }

  $.ajax({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    url: configs.routes.email_submit_reply_all,
    type: "post",
    data: {
      receiver_email: receiver_email,
      subject: quickemailSubject,
      message: replyMessage,
      reply_email_id: reply_email_id,
      pass_history: pass_history,
    },
    beforeSend: function () {
      $("#loading-image").show();
    },
  })
    .done(function (response) {
      $("#loading-image").hide();
      if (response.success) {
        toastr["success"](response.message);
      } else {
        $.each(response.errors, function (key, value) {
          if (key == "message") {
            $("#view-quick-email .note-editor").after(
              '<div class="invalid-feedback">' + value[0] + "</div>"
            );
          } else {
            $("#view-quick-email input[name='" + key + "']")
              .addClass("is-invalid")
              .after('<div class="invalid-feedback">' + value[0] + "</div>");
          }
        });
      }
    })
    .fail(function (errObj) {
      $("#loading-image").hide();
      toastr["error"](response.errors[0]);
    });
  $("#view-quick-email").modal("hide");
});

$(document).on("keyup", ".app-search-table", function (e) {
  var keyword = $(this).val();
  table = document.getElementById("database-table-list1");
  tr = table.getElementsByTagName("tr");
  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.indexOf(keyword) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
});

$(document).on("click", ".btn-task-search-menu", function (e) {
  var keyword = $(".task-search-table").val();
  var task_user_id = $("#task_user_id").val();
  var selectedValues = [];

  $.ajax({
    url: configs.routes.task_module_search,
    type: "GET",
    data: {
      term: keyword,
      selected_user: task_user_id,
    },
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    // dataType: 'json',
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (response) {
      $("#loading-image").hide();
      $(".show-search-task-list").html(response);
    },
    error: function () {
      $("#loading-image").hide();
      toastr["Error"]("An error occured!");
    },
  });
});

$(document).on("click", ".btn-dev-task-search-menu", function (e) {
  var keyword = $(".dev-task-search-table").val();
  var quicktask_user_id = $("#quicktask_user_id").val();
  var selectedValues = [];

  $.ajax({
    url: configs.routes.devtask_module_search,
    type: "GET",
    data: {
      subject: keyword,
      selected_user: quicktask_user_id,
    },
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    // dataType: 'json',
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (response) {
      $("#loading-image").hide();
      $(".show-search-dev-task-list").html(response);
    },
    error: function () {
      $("#loading-image").hide();
      toastr["Error"]("An error occured!");
    },
  });
});

$(document).on("change", ".assign-user-menu", function () {
  let id = $(this).attr("data-id");
  let userId = $(this).val();

  if (userId == "") {
    return;
  }

  $.ajax({
    url: configs.routes.de_assignUser,
    data: {
      assigned_to: userId,
      issue_id: id,
    },
    success: function () {
      toastr["success"]("User assigned successfully!", "Message");
    },
    error: function (error) {
      toastr["error"](error.responseJSON.message, "Message");
    },
  });
});

$(document).on("click", ".expand-row-msg-menu", function () {
  var id = $(this).data("id");
  var full = ".expand-row-msg-menu .td-full-container-" + id;
  var mini = ".expand-row-msg-menu .td-mini-container-" + id;
  $(full).toggleClass("hidden");
  $(mini).toggleClass("hidden");
});

$(document).on("click", ".send-message-open-quick-menu", function (event) {
  var textBox = $(this)
    .closest(".communication-td")
    .find(".send-message-textbox");
  var sendToStr = $(this)
    .closest(".communication-td")
    .next()
    .find(".send-message-number")
    .val();
  let issueId = textBox.attr("data-id");
  let message = textBox.val();
  if (message == "") {
    return;
  }

  let self = textBox;

  $.ajax({
    url: configs.routes.wa_sendMessage_issue,
    type: "POST",
    data: {
      issue_id: issueId,
      message: message,
      sendTo: sendToStr,
      _token: $('meta[name="csrf-token"]').attr("content"),
      status: 2,
    },
    dataType: "json",
    success: function (response) {
      toastr["success"]("Message sent successfully!", "Message");
      $("#message_list_" + issueId).append(
        "<li>" +
        response.message.created_at +
        " : " +
        response.message.message +
        "</li>"
      );
      $(self).removeAttr("disabled");
      $(self).val("");
    },
    beforeSend: function () {
      $(self).attr("disabled", true);
    },
    error: function () {
      toastr["error"]("There was an error sending the message...", "Error");
      $(self).removeAttr("disabled", true);
    },
  });
});

$(document).on("click", ".btn-file-upload-menu", function () {
  var $this = $(this);
  var task_id = $this.data("id");
  $("#modal-container").load("/menu-file-upload-area-section", function () {
    showFileUploadAreaSectionModal()
  });
  $("#hidden-task-id").val(task_id);
  $("#loading-image").hide();
});

$(document).on("change", ".menu-task-assign-user", function () {
  let id = $(this).attr("data-id");
  let userId = $(this).val();
  if (userId == "") {
    return;
  }
  $.ajax({
    url: configs.routes.task_AssignTaskToUser,
    data: {
      user_id: userId,
      issue_id: id,
    },
    success: function () {
      toastr["success"]("User assigned successfully!", "Message");
    },
    error: function (error) {
      toastr["error"](error.responseJSON.message, "Message");
    },
  });
});

$(document).on("click", ".menu-upload-document-btn", function () {
  var id = $(this).data("id");
  $("#modal-container").load("/menu-upload-document-modal", function () {
    $("#menu-upload-document-modal").find("#hidden-identifier").val(id);
    $("#menu-upload-document-modal").modal('show');
  });
});

$(document).on("click", ".menu-show-user-history", function () {
  var issueId = $(this).data("id");
  $("#user_history_div table tbody").html("");
  $.ajax({
    url: configs.routes.task_user_history,
    data: {
      id: issueId,
    },

    success: function (data) {
      if (response.html) {
        $("#user_history_div table tbody").html(response.html);
      }
      $("#menu_user_history_modal").css("z-index", "-1");
    },
  });
  $("#modal-container").load("/menu_user_history_modal", function () {
    showUserHistoryModal();
  });
});


$(document).on("click", ".menu-send-message", function () {
  var thiss = $(this);
  var data = new FormData();
  var task_id = $(this).data("taskid");
  if ($(this).hasClass("onpriority")) {
    var message = $("#getMsgPopup" + task_id).val();
  } else {
    var message = $("#getMsg" + task_id).val();
  }
  if (message != "") {
    $("#message_confirm_text").html(message);
    $("#confirm_task_id").val(task_id);
    $("#confirm_message").val(message);
    $("#confirm_status").val(1);

    $("#modal-container").load("/menu_confirmMessageModal", function () {
      $("#menu_confirmMessageModal").modal('show');
    });
  }
});

$(document).ready(function () {
  $(".add_todo_category").on("change", function () {
    var category_id = $(".add_todo_category").find(":selected").val();
    if (category_id != "" && category_id == "-1") {
      $(".othercat").show();
    } else {
      $(".othercat").hide();
    }
  });
});

$(document).on("click", ".submit-todolist-button", function (e) {
  e.preventDefault();
  var $this = $(this);
  var formData = new FormData($this.closest("form")[0]);
  var $form = $(this).closest("form");

  var title = $(this)
    .parents("#todolist-request-model")
    .find(".add_todo_title")
    .val();
  var subject = $(this)
    .parents("#todolist-request-model")
    .find(".add_todo_subject")
    .val();
  var category = $(".add_todo_category").find(":selected").val();
  var status = $(".add_todo_status").find(":selected").val();
  var date = $(this)
    .parents("#todolist-request-model")
    .find(".add_todo_date")
    .val();
  var remark = $(this)
    .parents("#todolist-request-model")
    .find(".add_todo_remark")
    .val();
  var other = $(this)
    .parents("#todolist-request-model")
    .find(".add_todo_other")
    .val();

  $(".text-danger").html("");
  if (title == "") {
    $(".add_todo_title").next().text("Please enter the title");
    return false;
  }

  if (subject == "") {
    $(".add_todo_subject").next().text("Please enter the subject");
    return false;
  }

  if (category == "") {
    $(".add_todo_category").next().text("Please select the category");
    return false;
  } else if (category == "-1") {
    if (other == "") {
      $(".add_todo_other").next().text("Please add new category");
      return false;
    }
  }

  //-1

  if (status == "") {
    $(".add_todo_status").next().text("Please select the status");
    return false;
  }

  if (date == "") {
    $(".text-danger-date").text("Please select the date");
    return false;
  } else {
    $(".text-danger-date").text(" ");
  }

  $.ajax({
    url: configs.routes.todolist_ajax_store,
    type: "POST",
    cache: false,
    contentType: false,
    processData: false,
    data: formData,
    dataType: "json",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    beforeSend: function () {
      $("#loading-image-preview").show();
    },
  })
    .done(function (data) {
      $("#loading-image-preview").hide();
      if (data.code == 500) {
        toastr["error"](data.message);
      } else {
        $(".othercat").hide();
        $form[0].reset();
        $("#todolist-request-model").modal("hide");
        toastr["success"]("Your Todo List has been created!");
      }
    })
    .fail(function (jqXHR, ajaxOptions, thrownError) {
      toastr["error"](jqXHR.responseJSON.message);
      $("#loading-image").hide();
    });
});

$(document).on("click", ".menu-confirm-messge-button", function () {
  var thiss = $(this);
  var data = new FormData();
  var task_id = $("#confirm_task_id").val();
  var message = $("#confirm_message").val();
  var status = $("#confirm_status").val();
  data.append("task_id", task_id);
  data.append("message", message);
  data.append("status", status);
  var checkedValue = [];
  var i = 0;
  $(".send_message_recepients:checked").each(function () {
    checkedValue[i++] = $(this).val();
  });
  data.append("send_message_recepients", checkedValue);

  if (message.length > 0) {
    if (!$(thiss).is(":disabled")) {
      $.ajax({
        url: configs.routes.whatsapp_send,
        type: "POST",
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        beforeSend: function () {
          $(thiss).attr("disabled", true);
        },
      })
        .done(function (response) {
          $(thiss).siblings("input").val("");
          $("#getMsg" + task_id).val("");
          $("#menu_confirmMessageModal").modal("hide");
          toastr["success"]("Message sent successfully!", "Message");
          if (cached_suggestions) {
            suggestions = JSON.parse(cached_suggestions);
            if (suggestions.length == 10) {
              suggestions.push(message);
              suggestions.splice(0, 1);
            } else {
              suggestions.push(message);
            }
            localStorage["message_suggestions"] = JSON.stringify(suggestions);
            cached_suggestions = localStorage["message_suggestions"];
          } else {
            suggestions.push(message);
            localStorage["message_suggestions"] = JSON.stringify(suggestions);
            cached_suggestions = localStorage["message_suggestions"];
          }
          $(thiss).attr("disabled", false);
        })
        .fail(function (errObj) {
          $("#menu_confirmMessageModal").modal("hide");
          $(thiss).attr("disabled", false);
          toastr["error"]("Could not send message", "Error");
        });
    }
  } else {
    toastr["error"]("Please enter a message first", "Error");
  }
});

$(document).on("submit", "#menu-upload-task-documents", function (e) {
  e.preventDefault();
  var form = $(this);
  var postData = new FormData(form[0]);
  $.ajax({
    method: "post",
    url: configs.routes.de_uploadDocument,
    data: postData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (response.code == 200) {
        toastr["success"]("Status updated!", "Message");
        $("#menu-upload-document-modal").modal("hide");
      } else {
        toastr["error"](response.error, "Message");
      }
    },
  });
});

$(document).on("click", ".menu-list-document-btn", function () {
  var id = $(this).data("id");
  $.ajax({
    method: "GET",
    url: configs.routes.de_getDocument,
    data: {
      id: id,
    },
    dataType: "json",
    success: function (response) {
      if (response.code == 200) {
        $("#modal-container").load("/menu-blank-modal", function () {
          $("#menu-blank-modal").find(".modal-title").html("Document List");
          $("#menu-blank-modal").find(".modal-body").html(response.data);
          $("#menu-blank-modal").modal('show');
        });
      } else {
        toastr["error"](response.error, "Message");
      }
    },
  });
});

$(document).on("click", ".menu-btn-save-documents", function (e) {
  e.preventDefault();
  var $this = $(this);
  var formData = new FormData($this.closest("form")[0]);
  $.ajax({
    url: "/task/save-documents",
    type: "POST",
    enctype: "multipart/form-data",
    cache: false,
    contentType: false,
    processData: false,
    data: formData,
    dataType: "json",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    beforeSend: function () {
      $("#loading-image").show();
    },
  })
    .done(function (data) {
      $("#loading-image").hide();
      if (data.code == 500) {
        toastr["error"](data.message);
      } else {
        toastr["success"]("Document uploaded successfully");
      }
    })
    .fail(function (jqXHR, ajaxOptions, thrownError) {
      toastr["error"](jqXHR.responseJSON.message);
      $("#loading-image").hide();
    });
});

$(document).on("change", ".choose-username", function () {
  var val = $(this).val();
  var db = $(".choose-db").val();
  $(".app-database-user-id").val(val);
  $(".btn-database-add").attr("data-id", val);
  $(".btn-delete-database-access").attr("data-id", val);
  $(".btn-delete-database-access").attr("data-connection", db);
  $(".btn-assign-permission").attr("data-id", val);
  var database_user_id = val;
  var url = configs.routes.user_management_get_database;
  url = url.replace(":id", database_user_id);

  $.ajax({
    url: url,
    type: "GET",
    data: {
      id: database_user_id,
    },
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    // dataType: 'json',
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (response) {
      $("#loading-image").hide();
      if (response.code == 200) {
        $(".database_password").val(response.data.password);
        if (response.data.password) {
          $(".btn-delete-database-access").removeClass("d-none");
        } else {
          $(".btn-delete-database-access").addClass("d-none");
        }
        var aa = "";
        $(".menu_tbody").html("");
        $.each(response.data.tables, function (i, record) {
          var checkvalue = "";
          if (record.checked) {
            checkvalue = "checked";
          }

          aa +=
            '<tr role="row"><td><input type="checkbox" name="tables[]" value=' +
            record.table +
            " " +
            checkvalue +
            "></td><td>" +
            record.table +
            "</td></tr>";
        });
        $(".menu_tbody").html(aa);
      } else {
        toastr["error"](response.message, "error");
      }
    },
    error: function () {
      $("#loading-image").hide();
      toastr["Error"]("An error occured!");
    },
  });
});

$(document).on("change", ".knowledge_base", function () {
  var val = $(this).val();
  if ($(this).val() == "chapter" || $(this).val() == "page") {
    $(this)
      .parents(".add_sop_modal")
      .find(".knowledge_base_book")
      .removeAttr("hidden");
  } else {
    $(this)
      .parents(".add_sop_modal")
      .find(".knowledge_base_book")
      .attr("hidden", true)
      .val("");
  }
});

$(document).on("change", ".knowledge_base_book", function () {
  var val = $(this).val();
  if (val.length > 0) {
    $(this).parents("#createShortcutForm").find(".books_error").text("");
  }
});

$(document).on("click", ".create_shortcut_submit", function () {
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
  });
  var formdata = $("#createShortcutForm").serialize();
  var val = $(this)
    .parents("#createShortcutForm")
    .find(".knowledge_base")
    .val();
  var chatID = $(this)
    .parents("#createShortcutForm")
    .find('[name="chat_message_id"]')
    .val();
  var name = $(this).parents("#createShortcutForm").find('[name="name"]').val();
  var category = $(this)
    .parents("#createShortcutForm")
    .find('[name="category"]')
    .val();
  var content = $(this)
    .parents("#createShortcutForm")
    .find('[name="description"]')
    .text();
  var book_name = $(this)
    .parents("#createShortcutForm")
    .find(".knowledge_base_book")
    .val();

  if ($(".sop_drop_down").find(":selected").val() == "code_shortcut") {
    $.ajax({
      type: "POST",
      url: configs.routes.shortcut_code_create,
      data: formdata,
      success: function (response) {
        if (response.status) {
          toastr.success("code Shortcut Added Successfully");
          $("#create-sop-shortcut").modal("hide");
          $("#createShortcutForm")[0].reset();
        } else {
          $.each(response.message, function (key, value) {
            $("#createShortcutForm input[name='" + key + "']")
              .addClass("is-invalid")
              .after('<div class="invalid-feedback">' + value[0] + "</div>");
          });
        }
      },
    });
  }

  if ($(".sop_drop_down").find(":selected").val() == "sop") {
    if (val.length === 0) {
      $.ajax({
        type: "POST",
        url: configs.routes.shortcut_sop_create,
        data: formdata,
        success: function (response) {
          if (response.status) {
            toastr.success("Sop Added Successfully");
            $("#create-sop-shortcut").modal("hide");
            $("#createShortcutForm")[0].reset();
          } else {
            $.each(response.errors, function (key, value) {
              $("#createShortcutForm input[name='" + key + "']")
                .addClass("is-invalid")
                .after('<div class="invalid-feedback">' + value[0] + "</div>");
            });
          }
        },
      });
    }
  }

  if ($(".sop_drop_down").find(":selected").val() == "knowledge_base") {
    if (val == "book") {
      $.ajax({
        type: "POST",
        url: `/kb/books`,
        data: formdata,
        success: function (response) {
          toastr.success("Book Added Successfully");
          $("#create-sop-shortcut").modal("hide");
        },
      });
    }
    if (val == "chapter") {
      if (book_name.length == 0) {
        $(this)
          .parents("#createShortcutForm")
          .find(".books_error")
          .text("Please select Book");
        return;
      }
      $.ajax({
        type: "POST",
        url: `/kb/books/${book_name}/create-chapter`,
        data: formdata,
        success: function (response) {
          toastr.success("Chapter Added Successfully");
          $("#create-sop-shortcut").modal("hide");
        },
      });
    }
    if (val == "page") {
      if (book_name.length == 0) {
        $(this)
          .parents("#createShortcutForm")
          .find(".books_error")
          .text("Please select Book");
        return;
      }
      $.ajax({
        type: "get",
        url: `kb/books/${book_name}/create-page`,
        data: formdata,
        success: function (response) {
          toastr.success("Page Added Successfully");
          $("#create-sop-shortcut").modal("hide");
        },
      });
    }
    if (val == "shelf") {
      $.ajax({
        type: "POST",
        url: `/kb/shelves/${name}/add`,
        data: formdata,
        success: function (response) {
          toastr.success("Bookshelf Added Successfully");
          $("#create-sop-shortcut").modal("hide");
        },
      });
    }
  }

  if ($(".sop_drop_down").find(":selected").val() == "reply_shortcut") {
    if (val.length === 0) {
      $.ajax({
        type: "POST",
        url: configs.routes.reply_store,
        data: formdata,
        success: function (response) {
          if (response.status) {
            toastr.success("Quick Reply added successfully");
            $("#create-sop-shortcut").modal("hide");
            $("#createShortcutForm").trigger("reset");
          } else {
            $.each(response.errors, function (key, value) {
              $("#createShortcutForm input[name='" + key + "']")
                .addClass("is-invalid")
                .after('<div class="invalid-feedback">' + value[0] + "</div>");
            });
          }
        },
        error: function (error) {
          for (let msg of Object.values(error.responseJSON.errors)) {
            toastr.error(msg);
          }
        },
      });
    }
  }

  if ($(".sop_drop_down").find(":selected").val() == "devoops_modules") {
    if (val.length === 0) {
      $.ajax({
        url: "/devoops",
        type: "POST",
        data: {
          category_type: $("#createShortcutForm")
            .find("input[name='category_type']")
            .val(),
          category_name: $("#createShortcutForm")
            .find("input[name='category_name']")
            .val(),
        },
        success: function (response) {
          if (response.status) {
            $("#create-sop-shortcut").modal("hide");
            toastr["success"](response.message);
            $("#createShortcutForm").trigger("reset");
          } else {
            $.each(response.errors, function (key, value) {
              $("#createShortcutForm input[name='" + key + "']")
                .addClass("is-invalid")
                .after('<div class="invalid-feedback">' + value[0] + "</div>");
            });
          }
        },
      });
    }
  }

  if ($(".sop_drop_down").find(":selected").val() == "todo_shortcut") {
    if (val.length === 0) {
      let data = {
        title: $("#createShortcutForm").find("input[name='todo_title']").val(),
        subject: $("#createShortcutForm")
          .find("input[name='todo_subject']")
          .val(),
        status: $("#createShortcutForm")
          .find("select[name='todo_status']")
          .val(),
        todo_date: $("#createShortcutForm")
          .find("input[name='todo_date']")
          .val(),
        remark: $("#createShortcutForm")
          .find("input[name='todo_remark']")
          .val(),
        todo_category_id: $("#createShortcutForm")
          .find("select[name='todo_category_id']")
          .val(),
      };

      $.ajax({
        url: "todolist/store",
        type: "POST",
        data,
        success: function (response) {
          if (response.status) {
            $("#create-sop-shortcut").modal("hide");
            toastr["success"](response.message);
            $("#createShortcutForm").trigger("reset");
          } else {
            $.each(response.errors, function (key, value) {
              $("#createShortcutForm input[name='" + key + "']")
                .addClass("is-invalid")
                .after('<div class="invalid-feedback">' + value[0] + "</div>");
            });
          }
        },
      });
    }
  }
});

$(".system-request").on("click", function (e) {
  e.preventDefault();
  if ($("#system-request").data('loaded')) {
    $("#system-request").modal('show');
    loadUsersList();
  } else {
    $.ajax({
      url: configs.routes.system_request_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#system-request").data('loaded', true);
        $("#ipusers").select2({ width: "20%" });
        $("#loading-image-preview").hide();
        $("#system-request").modal('show');
        loadUsersList();
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});
$(document).on("click", ".addIp", function (e) {
  e.preventDefault();
  if ($('input[name="add-ip"]').val() != "") {
    if ($("#ipusers").val() === "") {
      toastr["error"]("Please select User OR Other from list.", "Error");
    } else if (
      $("#ipusers").val() === "other" &&
      $('input[name="other_user_name"]').val() === ""
    ) {
      toastr["error"]("Please enter other name.", "Error");
    } else {
      $.ajax({
        url: "/users/add-system-ip",
        type: "GET",
        data: {
          _token: $('meta[name="csrf-token"]').attr("content"),
          ip: $('input[name="add-ip"]').val(),
          user_id: $("#ipusers").val(),
          other_user_name: $('input[name="other_user_name"]').val(),
          comment: $('input[name="ip_comment"]').val(),
        },
        dataType: "json",
        beforeSend: function () {
          $("#loading-image").show();
        },
        success: function (result) {
          $("#loading-image").hide();
          toastr["success"]("IP added successfully");
          loadUsersList();
          $('input[name="add-ip"]').val("");
          $("#ipusers").val("");
          $('input[name="other_user_name"]').val("");
          $('input[name="ip_comment"]').val("");
        },
        error: function () {
          $("#loading-image").hide();
          toastr["Error"]("An error occured!");
        },
      });
    }
  } else {
    toastr["error"]("please enter IP", "Error");
  }
});

$(document).on("click", ".btn-database-add", function (e) {
  e.preventDefault();
  // var ele = this;
  var connection = $(".choose-db").val();
  var username = $(".choose-username").find(":selected").attr("data-name");
  username = username.replace(/ /g, "_").toLowerCase();
  var password = $(".database_password").val();
  var database_user_id = $(this).data("id");
  var url = configs.routes.user_management_create_database;
  url = url.replace(":id", database_user_id);

  $.ajax({
    url: url,
    type: "POST",
    data: {
      database_user_id: database_user_id,
      connection: connection,
      username: username,
      password: password,
    },
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    // dataType: 'json',
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (response) {
      $("#loading-image").hide();
      if (response.code == 200) {
        toastr["success"](response.message, "success");
      } else {
        toastr["error"](response.message, "error");
      }
    },
    error: function () {
      $("#loading-image").hide();
      toastr["Error"]("An error occured!");
    },
  });
});

$(document).on("click", ".btn-assign-permission", function (e) {
  e.preventDefault();
  // var ele = this;
  var connection = $(".choose-db").val();
  var assign_permission = $(".assign-permission-type").find(":selected").val();
  var search = $(".app-search-table").val();
  var tables = $(".database_password").val();
  var checked = [];
  $("input[name='tables[]']:checked").each(function () {
    checked.push($(this).val());
  });

  var database_user_id = $("#database-user-id").val();
  if (database_user_id == "") {
    toastr["error"]("Please select the user first", "error");
    return false;
  }
  var url = configs.routes.user_management_assign_database_table;
  url = url.replace(":id", database_user_id);

  $.ajax({
    url: url,
    type: "POST",
    data: {
      database_user_id: database_user_id,
      connection: connection,
      search: search,
      assign_permission: assign_permission,
      tables: checked,
    },
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    // dataType: 'json',
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (response) {
      $("#loading-image").hide();
      if (response.code == 200) {
        toastr["success"](response.message, "success");
      } else {
        toastr["error"](response.message, "error");
      }
    },
    error: function () {
      $("#loading-image").hide();
      toastr["Error"]("An error occured!");
    },
  });
});

$(document).on("click", ".btn-delete-database-access", function (e) {
  e.preventDefault();
  if (!confirm("Are you sure you want to remove access for this user?")) {
    return false;
  } else {
    var connection = $(".choose-db").val();
    var database_user_id = $("#database-user-id").val();
    if (database_user_id == "") {
      toastr["error"]("Please select the user first", "error");
      return false;
    }
    var url = configs.routes.user_management_delete_database_access;
    url = url.replace(":id", database_user_id);

    $.ajax({
      url: url,
      type: "POST",
      data: {
        connection: connection,
      },
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      // dataType: 'json',
      beforeSend: function () {
        $("#loading-image").show();
      },
      success: function (response) {
        $("#loading-image").hide();
        if (response.code == 200) {
          toastr["success"](response.message, "success");
          $("#menu-create-database-model").modal("hide");
        } else {
          toastr["error"](response.message, "error");
          $("#menu-create-database-model").modal("hide");
        }
      },
      error: function () {
        $("#loading-image").hide();
        toastr["Error"]("An error occured!");
      },
    });
  }
});

$(document).ready(function () {
  $("#ipusers").change(function () {
    var selected = $(this).val();
    if (selected == "other") {
      $("#other_user_name").show();
    } else {
      $("#other_user_name").hide();
    }
  });
});
$(document).on("click", ".deleteIp", function (e) {
  e.preventDefault();
  var btn = $(this);
  $.ajax({
    url: "/users/delete-system-ip",
    type: "GET",
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
      usersystemid: $(this).data("usersystemid"),
    },
    dataType: "json",
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (result) {
      btn.parents("tr").remove();
      $("#loading-image").hide();
      toastr["success"]("IP Deteted successfully");
    },
    error: function () {
      $("#loading-image").hide();
      toastr["Error"]("An error occured!");
    },
  });
});
$(document).on("click", ".bulkDeleteIp", function (e) {
  e.preventDefault();
  var btn = $(this);
  if (confirm("Are you sure you want to perform this Action?") == false) {
    return false;
  }
  $.ajax({
    url: "/users/bulk-delete-system-ip",
    type: "GET",
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
    },
    dataType: "json",
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (result) {
      $("#userAllIps").empty();
      $("#loading-image").hide();
      toastr["success"]("IPs Deteted successfully");
    },
    error: function () {
      $("#loading-image").hide();
      toastr["Error"]("An error occured!");
    },
  });
});
function loadUsersList() {
  var t = "";
  var ip = "";
  $.ajax({
    url: configs.routes.get_user_list,
    type: "GET",
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
    },
    dataType: "json",
    success: function (result) {
      $(".processing-txt").addClass("d-none");
      if (result.html) {
        $("#userAllIps").html(result.html);
      }
    },
    error: function () {
    },
  });
}

//STACK
$(document).ready(function () {
  var autoRefresh = $.cookie("auto_refresh");
  if (typeof autoRefresh == "undefined" || autoRefresh == 1) {
    $(".auto-refresh-run-btn").attr("title", "Stop Auto Refresh");
    $(".auto-refresh-run-btn")
      .find("i")
      .removeClass("refresh-btn-stop")
      .addClass("refresh-btn-start");
  } else {
    $(".auto-refresh-run-btn").attr("title", "Start Auto Refresh");
    $(".auto-refresh-run-btn")
      .find("i")
      .removeClass("refresh-btn-start")
      .addClass("refresh-btn-stop");
  }
  //auto-refresh-run-btn

  $(document).on("click", ".auto-refresh-run-btn", function () {
    let autoRefresh = $.cookie("auto_refresh");
    if (autoRefresh == 0) {
      toastr["success"]("Auto refresh has been enable for this page", "Success");
      $.cookie("auto_refresh", "1", {
        path: "/" + configs.routes.request_path,
      });
      $(".auto-refresh-run-btn")
        .find("i")
        .removeClass("refresh-btn-stop")
        .addClass("refresh-btn-start");
    } else {
      toastr["success"]("Auto refresh has been disable for this page", "Success");
      $.cookie("auto_refresh", "0", {
        path: "/" + configs.routes.request_path,
      });
      $(".auto-refresh-run-btn")
        .find("i")
        .removeClass("refresh-btn-start")
        .addClass("refresh-btn-stop");
    }
  });

  $("#editor-note-content").richText();
  $("#editor-instruction-content").richText();

  $("#editor-notes-content").richText(); //Purpose : Add Text content - DEVTASK-4289

  $("#notification-date").datetimepicker({
    format: "YYYY-MM-DD",
  });

  $("#notification-time").datetimepicker({
    format: "HH:mm",
  });

  $("#repeat_end").datetimepicker({
    format: "YYYY-MM-DD",
  });

  $(".selectx-vendor").select2({
    tags: true,
  });
  $(".selectx-users").select2({
    tags: true,
  });
});
window.token = $('meta[name="csrf-token"]').attr("content");

var url = window.location;
window.collectedData = [
  {
    type: "key",
    data: "",
  },
  {
    type: "mouse",
    data: [],
  },
];

$(document).keypress(function (event) {
  var x = event.charCode || event.keyCode; // Get the Unicode value
  var y = String.fromCharCode(x);
  collectedData[0].data += y;
});

// started for help button
$(".help-button").on("click", function () {
  $(".help-button-wrapper").toggleClass("expanded");
  $(".page-notes-list-rt").toggleClass("dis-none");
});

$(".instruction-button").on("click", function (e) {
  e.preventDefault();
  var currentUrl = window.location.href;
  if ($("#quick-instruction-modal").data('loaded')) {
    $("#quick-instruction-modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.quick_instruction_notes_model,
      type: 'GET',
      data: { url: currentUrl },
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#quick-instruction-modal").data('loaded', true);
        $("#editor-instruction-content").richText();
        $("#loading-image-preview").hide();
        $("#quick-instruction-modal").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

var stickyNotesUrl = configs.routes.stickyNotesCreate;
var stickyNotesPage = configs.routes.re_full_url;

var x = `<div class='sticky_notes_container pageNotesModal' style=" padding: 10px; margin: 20px;">
        <div class="icon-check">
        <div class='check-icon' title='Save'><i class='fa fa-check'></i></div>
          <div class='close-icon' title='Close'><i class='fa fa-times'></i></div>
            </div>
               Sticky Note
               <hr>
               <div class="text_box-select mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="Title">
                    Type
                  </label>
                  <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="custom-text" style=" width: 100%;">
                  <option value="notes">Notes</option>
                  <option value="todolist">To do List</option>
                  </select>
                </div>
                <div class="text_box-text mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="Title">
                    Title
                  </label>
                  <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="custom-text" type="text" placeholder="Title" style=" width: 100%;">
                </div>
                
                <div class='text_box-textarea mb-4'>
                    <label>Notes</label></br>
                    <textarea rows='5' cols='27' class='notes custom-textarea' name='notes' data-url='${stickyNotesUrl}' data-page='${stickyNotesPage}' placeholder="Notes" style=" background: #fff; width:100%"></textarea>
                </div>
            </div>`;

$(".sticky-notes").on("click", function () {
  StickyBox();
});

var marginVar = 20;

function StickyBox() {
  marginVar += 20;

  $(".sticknotes_content").draggable();
  $("#sticky_note_boxes").append(x);

  var lastStickyNote = $("#sticky_note_boxes .sticky_notes_container:last");

  lastStickyNote.css("margin", marginVar + "px");

  $(".sticky_notes_container").draggable();
  $(".close-icon").each(function () {
    $(".close-icon").click(function () {
      $(this).closest(".sticky_notes_container").remove();
    });
  });
}

$(document).on("click", ".check-icon", function (event) {
  event.preventDefault();
  var textareaValue = $(this)
    .parent()
    .siblings(".text_box-textarea")
    .find("textarea")
    .val();
  var page = $(this)
    .parent()
    .siblings(".text_box-textarea")
    .find("textarea")
    .data("page");

  var title = $(this).parent().siblings(".text_box-text").find("input").val();

  var type = $(this).parent().siblings(".text_box-select").find("select").val();

  $.ajax({
    url: configs.routes.stickyNotesCreate,
    method: "POST",
    data: {
      value: textareaValue,
      page: page,
      title: title,
      type: type,
      _token: $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (response) {
      toastr["success"](response.message, "success");
    },
    error: function (xhr, status, error) {

    },
  });
  $(this).closest(".sticky_notes_container").remove();
});

//START - Purpose : Open Modal - DEVTASK-4289
// $(".create_notes_btn").on("click", function () {
//   $("#quick_notes_modal").modal("show");
// });

$(".btn_save_notes").on("click", function (e) {
  e.preventDefault();
  var data = $("#editor-notes-content").val();

  if ($(data).text() == "") {
    toastr["error"]("Note Is Required");
    return false;
  }

  var url = window.location.href;
  $.ajax({
    type: "POST",
    url: configs.routes.notesCreate,
    data: {
      data: data,
      url: url,
      _token: $('meta[name="csrf-token"]').attr("content"),
    },
    dataType: "json",
    success: function (data) {
      if (data.code == 200) {
        toastr["success"](data.message, "success");
        $("#quick_notes_modal").modal("hide");
      }
    },
    error: function (xhr, status, error) { },
  });
});
//END - DEVTASK-4289

$(".notification-button").on("click", function () {
  if ($("#quick-user-event-notification-modal").data('loaded')) {
    $("#quick-user-event-notification-modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.calendar_event_showcreateeventmodal,
      type: "GET",
      dataType: "json",
      data: {},
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
    })
      .done(function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#quick-user-event-notification-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#quick-user-event-notification-modal").modal('show');
      })
      .fail(function (response) {
        $(".loading-image-preview").hide();
      });
  }
});

$(".ParticipantsList").on("click", function (e) {
  e.preventDefault();
  if ($("#participants-list-modal").data('loaded')) {
    $("#participants-list-modal").modal('show');
    viewParticipantsIcon();
  } else {
    $.ajax({
      url: configs.routes.view_all_participants_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#participants-list-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#participants-list-modal").modal('show');
        viewParticipantsIcon();
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function viewParticipantsIcon(pageNumber = 1) {
  var button = document.querySelector(".btn.btn-xs.ParticipantsList");

  $.ajax({
    url: configs.routes.list_all_participants,
    type: "GET",
    dataType: "json",
    data: {
      page: pageNumber,
    },
    beforeSend: function () {
      $("#loading-image-preview").show();
    },
  })
    .done(function (response) {
      $("#participants-list-modal-html").empty().html(response.html);
      $("#participants-list-modal").modal("show");
      renderdomainPagination(response.data);
      $("#loading-image-preview").hide();
    })
    .fail(function (response) {
      $(".loading-image-preview").show();

    });
}

function renderdomainPagination(response) {
  var paginationContainer = $(".pagination-container-participation");
  var currentPage = response.current_page;
  var totalPages = response.last_page;
  var html = "";
  var maxVisiblePages = 10;

  if (totalPages > 1) {
    html += "<ul class='pagination'>";
    if (currentPage > 1) {
      html +=
        "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(" +
        (currentPage - 1) +
        ")'>Previous</a></li>";
    }
    var startPage = 1;
    var endPage = totalPages;

    if (totalPages > maxVisiblePages) {
      if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
        endPage = maxVisiblePages;
      } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
        startPage = totalPages - maxVisiblePages + 1;
      } else {
        startPage = currentPage - Math.floor(maxVisiblePages / 2);
        endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
      }

      if (startPage > 1) {
        html +=
          "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(1)'>1</a></li>";
        if (startPage > 2) {
          html +=
            "<li class='page-item disabled'><span class='page-link'>...</span></li>";
        }
      }
    }

    for (var i = startPage; i <= endPage; i++) {
      html +=
        "<li class='page-item " +
        (currentPage == i ? "active" : "") +
        "'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(" +
        i +
        ")'>" +
        i +
        "</a></li>";
    }
    html += "</ul>";
  }
  paginationContainer.html(html);
}

function changeParticipantsPage(pageNumber) {
  viewParticipantsIcon(pageNumber);
}

$('select[name="repeat"]').on("change", function () {
  $(this).val() == "weekly"
    ? $("#repeat_on").removeClass("hide")
    : $("#repeat_on").addClass("hide");
});

$('select[name="ends_on"]').on("change", function () {
  $(this).val() == "on"
    ? $("#repeat_end_date").removeClass("hide")
    : $("#repeat_end_date").addClass("hide");
});

$('select[name="repeat"]').on("change", function () {
  $(this).val().length > 0
    ? $("#ends_on").removeClass("hide")
    : $("#ends_on").addClass("hide");
});

//setup before functions
var typingTimer; //timer identifier
var doneTypingInterval = 5000; //time in ms, 5 second for example
var $input = $("#editor-instruction-content");
//on keyup, start the countdown
$input.on("keyup", function () {
  clearTimeout(typingTimer);
  typingTimer = setTimeout(doneTyping, doneTypingInterval);
});

//on keydown, clear the countdown
$input.on("keydown", function () {
  clearTimeout(typingTimer);
});

//user is "finished typing," do something
function doneTyping() {
  //do something
}

// started for chat button
// open chatbox now into popup

var chatBoxOpen = false;

$("#message-chat-data-box").on("click", function (e) {
  e.preventDefault();
  if ($("#quick-chatbox-window-modal").data('loaded')) {
    $("#quick-chatbox-window-modal").modal('show');
    chatBoxOpen = true;
    openChatBox(true);
  } else {
    $.ajax({
      url: configs.routes.chat_header_model, 
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#quick-chatbox-window-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#quick-chatbox-window-modal").modal('show');
        chatBoxOpen = true;
        openChatBox(true);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$("#quick-chatbox-window-modal").on("hidden.bs.modal", function () {
  chatBoxOpen = false;
  openChatBox(false);
});

$(".chat_btn").on("click", function (e) {
  e.preventDefault();
  $("#quick-chatbox-window-modal").modal("show");
  chatBoxOpen = true;
  openChatBox(true);
});

var notesBtn = $(".save-user-notes");

notesBtn.on("click", function (e) {
  e.preventDefault();
  var $form = $(this).closest("form");
  $.ajax({
    type: "POST",
    url: $form.attr("action"),
    data: {
      _token: window.token,
      note: $form.find("#note").val(),
      category_id: $form.find("#category_id").val(),
      url: configs.routes.re_url,
    },
    dataType: "json",
    success: function (data) {
      if (data.code > 0) {
        $form.find("#note").val("");
        var listOfN = "<tr>";
        listOfN += "<td scope='row'>" + data.notes.id + "</td>";
        listOfN += "<td>" + data.notes.note + "</td>";
        listOfN += "<td>" + data.notes.category_name + "</td>";
        listOfN += "<td>" + data.notes.name + "</td>";
        listOfN += "<td>" + data.notes.created_at + "</td>";
        listOfN += "</tr>";

        $(".page-notes-list").prepend(listOfN);
      }
    },
  });
});

var getNotesList = function () { };

if ($(".help-button-wrapper").length > 0) {
  getNotesList();
}

$(document).ready(function () {
  setTimeout(function () {
    var url = window.location.href;
    var user_id = configs.auth.id;
    user_name = configs.auth.user_name;

    $.ajax({
      type: "POST",
      url: "/api/userLogs",
      data: {
        _token: $('meta[name="csrf-token"]').attr("content"),
        url: url,
        user_id: user_id,
        user_name: user_name,
      },
      dataType: "json",
      success: function (message) { },
    });
  }, 3000);
});

$("#search").on("keyup", function () {
  var input, filter, ul, li, a, i;
  var chatBoxOpen = true;
  //getting search values
  input = $("#search").val();
  //String to upper for search
  filter = input.toUpperCase();

  //Getting Values From DOM
  a = document.querySelectorAll("#navbarSupportedContent a");
  //Class to open bar
  $("#search_li").addClass("open");
  //Close when search becomes zero
  if (a.length == 0) {
    $("#search_li").removeClass("open");
  }
  //Limiting Search Count
  count = 1;
  //Empty Existing Values
  $("#search_container").empty();

  //Getting All Values
  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
    href = a[i].href;
    //If value doesnt have link
    if (href == "#" || href == "" || href.indexOf("#") > -1) {
      continue;
    }
    //Removing old search Result From DOM
    if (
      a[i].getAttribute("class") != null &&
      a[i].getAttribute("class") != ""
    ) {
      if (a[i].getAttribute("class").indexOf("old_search") > -1) {
        continue;
      }
    }
    //break when count goes above 30
    if (count > 30) {
      break;
    }
    //Pusing values to DOM Search Input
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      $("#search_container").append(
        '<li class="nav-item dropdown dropdown-submenu"><a class="dropdown-item old_search" href=' +
        href +
        ">" +
        txtValue +
        "</a></li>"
      );
      count++;
    } else {
    }
  }
  if (filter.length == 0) {
    $("#search_container").empty();
    $("#search_li").removeClass("open");
  }
});

$(document).on("change", "#autoTranslate", function (e) {
  e.preventDefault();
  var customerId = $("input[name='message-id']").val();
  var language = $(".auto-translate").val();
  let self = $(this);
  $.ajax({
    url: "/customer/language-translate/" + customerId,
    method: "PUT",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      id: customerId,
      language: language,
    },
    cache: true,
    success: function (res) {
      $('.selectedValue option[value="' + language + '"]').prop(
        "selected",
        true
      );
      toastr["success"](res.success, "Success");
    },
  });
});

$(document).ready(function () {
  $(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
      $("#back-to-top").fadeIn();
    } else {
      $("#back-to-top").fadeOut();
    }
  });
  // scroll body to 0px on click
  $("#back-to-top").click(function () {
    $("body,html").animate(
      {
        scrollTop: 0,
      },
      400
    );
    return false;
  });

  $("#sidebarCollapse").on("click", function () {
    $("#sidebar").toggleClass("active");
  });
  $(".select2-vendor").select2({});

  $("#showLatestEstimateTime").on("hide.bs.modal", function (e) {
    $("#modalTaskInformationUpdates .modal-body .row").show();
    $("#modalTaskInformationUpdates .modal-body hr").show();
  });

  $(document).on(
    "click",
    ".approveEstimateFromshortcutButton",
    function (event) {
      event.preventDefault();
      let type = $(this).data("type");
      let task_id = $(this).data("task");
      let history_id = $(this).data("id");

      if (type == "TASK") {
        $.ajax({
          url: "/task/time/history/approve",
          type: "POST",
          data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            approve_time: history_id,
            developer_task_id: task_id,
            user_id: 0,
          },
          success: function (response) {
            toastr["success"]("Successfully approved", "success");
            $("#showLatestEstimateTime").modal("hide");
          },
          error: function (error) {
            toastr["error"](error.responseJSON.message);
          },
        });
      } else {
        $.ajax({
          url: "/development/time/history/approve",
          type: "POST",
          data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            approve_time: history_id,
            developer_task_id: task_id,
            user_id: 0,
          },
          success: function (response) {
            toastr["success"]("Successfully approved", "success");
            $("#showLatestEstimateTime").modal("hide");
          },
          error: function (error) {
            toastr["error"](error.responseJSON.message);
          },
        });
      }
    }
  );
});

$(".create-zoom-meeting").on("click", function (e) {
  e.preventDefault();
  if ($("#quick-zoomModal").data('loaded')) {
    $("#quick-zoomModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.create_zoom_meeting_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#quick-zoomModal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#quick-zoomModal").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(document).on("click", ".save-meeting-zoom", function () {
  var user_id = $("#quick_user_id").val();
  var meeting_topic = $("#quick_meeting_topic").val();
  var csrf_token = $("#quick_csrfToken").val();
  var meeting_url = $("#quick_meetingUrl").val();
  $.ajax({
    url: meeting_url,
    type: "POST",
    success: function (response) {
      var status = response.success;
      if (false == status) {
        toastr["error"](response.data.msg);
      } else {
        $("#quick-zoomModal").modal("toggle");
        window.open(response.data.meeting_link);
        var html = "";
        html += response.data.msg + "<br>";
        html +=
          'Meeting URL: <a href="' +
          response.data.meeting_link +
          '" target="_blank">' +
          response.data.meeting_link +
          "</a><br><br>";
        html +=
          '<a class="btn btn-primary" target="_blank" href="' +
          response.data.start_meeting +
          '">Start Meeting</a>';
        $("#qickZoomMeetingModal").modal("toggle");
        $(".meeting_link").html(html);
        toastr["success"](response.data.msg);
      }
    },
    data: {
      user_id: user_id,
      meeting_topic: meeting_topic,
      _token: csrf_token,
      user_type: "vendor",
    },
    beforeSend: function () {
      $(this).text("Loading...");
    },
  }).fail(function (response) {
    toastr["error"](response.responseJSON.message);
  });
});

$(document).on("change", ".task_for", function (e) {
  var getTask = $(this).val();
  if (getTask == "time_doctor") {
    $(".time_doctor_project_section").show();
    $(".time_doctor_account_section").show();
  } else {
    $(".time_doctor_project_section").hide();
    $(".time_doctor_account_section").hide();
  }
});

$(document).on("click", ".save-task-window", function (e) {
  e.preventDefault();
  var form = $(this).closest("form");
  $.ajax({
    url: form.attr("action"),
    type: "POST",
    data: form.serialize(),
    beforeSend: function () {
      $(this).text("Loading...");
    },
    success: function (response) {
      if (response.code == 200) {
        form[0].reset();
        toastr["success"](response.message);
        $("#quick-create-task").modal("hide");
        $("#auto-reply-popup").modal("hide");
        $("#auto-reply-popup-form").trigger("reset");
        location.reload();
      } else {
        toastr["error"](response.message);
      }
    },
  }).fail(function (response) {
    toastr["error"](response.responseJSON.message);
  });
});

$("select.select2-discussion").select2({
  tags: true,
});

$(document).on("change", ".type-on-change", function (e) {
  e.preventDefault();
  var task_type = $(this).val();

  if (task_type == 3) {
    $.ajax({
      url: "/task/get-discussion-subjects",
      type: "GET",
      success: function (response) {
        $("select.select2-discussion").select2({
          tags: true,
        });
        var option = '<option value="" >Select</option>';
        $.each(response.discussion_subjects, function (i, item) {

          option = option + '<option value="' + i + '">' + item + "</option>";
        });
        $(".add-discussion-subjects").html(option);
      },
    }).fail(function (response) {
      toastr["error"](response.responseJSON.message);
    });
  } else {
    // $('select.select2-discussion').select2({tags: true});
    $("select.select2-discussion").empty().trigger("change");
  }
});

$(document).on("change", "#keyword_category", function () {
  if ($(this).val() != "") {
    var category_id = $(this).val();
    var store_website_id = $("#live_selected_customer_store").val();
    $.ajax({
      url:
        configs.routes.get_store_wise_replies +
        "/" +
        category_id +
        "/" +
        store_website_id,
      type: "GET",
      dataType: "json",
    }).done(function (data) {
      if (data.status == 1) {
        $("#live_quick_replies")
          .empty()
          .append('<option value="">Quick Reply</option>');
        var replies = data.data;
        replies.forEach(function (reply) {
          $("#live_quick_replies").append(
            $("<option>", {
              value: reply.reply,
              text: reply.reply,
              "data-id": reply.id,
            })
          );
        });
      }
    });
  }
});

$(".quick_comment_add_live").on("click", function () {
  var textBox = $(".quick_comment_live").val();
  var quickCategory = $("#keyword_category").val();

  if (textBox == "") {
    toastr["error"]("Please Enter New Quick Comment!!", "Error");
    return false;
  }

  if (quickCategory == "") {
    toastr["error"]("Please Select Category!!", "Error");
    return false;
  }

  $.ajax({
    type: "POST",
    url: configs.routes.save_store_wise_reply,
    dataType: "json",
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
      category_id: quickCategory,
      reply: textBox,
      store_website_id: $("#live_selected_customer_store").val(),
    },
  }).done(function (data) {
    $(".live_quick_comment").val("");
    $("#live_quick_replies").append(
      $("<option>", {
        value: data.data,
        text: data.data,
      })
    );
  });
});

$("#live_quick_replies").on("change", function () {
  $(".type_msg").text($(this).val());
});

$(document).on("click", ".show_sku_long", function () {
  $(this).hide();
  var id = $(this).attr("data-id");
  $("#sku_small_string_" + id).hide();
  $("#sku_long_string_" + id).css({
    display: "block",
  });
});

$(document).on("click", ".show_prod_long", function () {
  $(this).hide();
  var id = $(this).attr("data-id");
  $("#prod_small_string_" + id).hide();
  $("#prod_long_string_" + id).css({
    display: "block",
  });
});

$(document).on("click", ".manual-payment-btn", function (e) {
  e.preventDefault();
  var thiss = $(this);
  var type = "GET";
  $.ajax({
    url: "/voucher/manual-payment",
    type: type,
    beforeSend: function () {
      $("#loading-image").show();
    },
  })
    .done(function (response) {
      $("#loading-image").hide();
      $("#modal-container").load("/create-manual-payment", function () {
        $("#create-manual-payment").modal('show');
        $("#create-manual-payment-content").html(response);
      });

      $("#date_of_payment").datetimepicker({
        format: "YYYY-MM-DD",
      });
      $(".select-multiple").select2({
        width: "100%",
      });

      $(".currency-select2").select2({
        width: "100%",
        tags: true,
      });
      $(".payment-method-select2").select2({
        width: "100%",
        tags: true,
      });
    })
    .fail(function (errObj) {
      $("#loading-image").hide();
    });
});

$(document).on("click", ".manual-request-btn", function (e) {
  e.preventDefault();
  var thiss = $(this);
  var type = "GET";
  $.ajax({
    url: "/voucher/payment/request",
    type: type,
    beforeSend: function () {
      $("#loading-image").show();
    },
  })
    .done(function (response) {
      $("#loading-image").hide();
      $("#modal-container").load("/create-manual-payment", function () {
        $("#create-manual-payment").modal('show');
        $("#create-manual-payment-content").html(response);
      });

      $("#date_of_payment").datetimepicker({
        format: "YYYY-MM-DD",
      });
      $(".select-multiple").select2({
        width: "100%",
      });
    })
    .fail(function (errObj) {
      $("#loading-image").hide();
    });
});

$("#repo_status_list").on("click", function (e) {
  e.preventDefault();
  if ($("#pull-request-alerts-modal").data('loaded')) {
    $("#pull-request-alerts-modal").modal('show');
    getPullRequestsForShortcut(true);
  } else {
    $.ajax({
      url: configs.routes.github_pr_list_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#pull-request-alerts-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#pull-request-alerts-modal").modal('show');
        getPullRequestsForShortcut(true);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function getPullRequestsForShortcut(showModal = false) {
  $.ajax({
    type: "GET",
    url: configs.routes.github_pr_request,
    dataType: "json",
    beforeSend: function () {
      $("#loading-image-preview").show();
    },
  })
  .done(function (response) {
    $("#loading-image-preview").hide();
    $("#pull-request-alerts-modal-html").empty().html(response.tbody);
    if (showModal) {
      $("#pull-request-alerts-modal").modal("show");
    }
    if (response.count > 0) {
      $(".event-alert-badge").removeClass("hide");
    }
  })
  .fail(function (response) {
    $("#loading-image-preview").hide();
  });
}

function confirmMergeToMaster(branchName, url) {
  let result = confirm(
    "Are you sure you want to merge " + branchName + " to master?"
  );
  if (result) {
    window.location.href = url;
  }
}

$("#website_Off_status").on("click", function (e) {
  e.preventDefault();
  if ($("#create-status-modal").data('loaded')) {
    $("#create-status-modal").modal('show');
    getWebsiteMonitorStatus(1);
  } else {
    $.ajax({
      url: configs.routes.monitor_status_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#create-status-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#create-status-modal").modal('show');
        getWebsiteMonitorStatus(1);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function getWebsiteMonitorStatus(page) {
  var url = "/monitor-server/list?page=" + page;

  $.ajax({
    type: "GET",
    url: url,
    dataType: "json",
    beforeSend: function (data) {
      $("#loading-image-preview").show();
    },
  })
    .done(function (response) {
      $("#loading-image-preview").hide();
      var tableBody = $("#website-monitor-status-modal-html");
      tableBody.empty(); // Clear the table body
      // Loop through the data and populate the table rows
      $.each(response.data, function (index, item) {
        var row = $("<tr>");
        row.append($("<td>").text(item.server_id));
        row.append($("<td>").text(item.ip));
        tableBody.append(row);
      });
      var paginationLinks = $(
        "#website-monitor-status-modal-table-paginationLinks"
      );
      paginationLinks.empty(); // Clear the pagination links
      // Generate the pagination links manually
      var links = response.links;
      var currentPage = response.current_page;
      var lastPage = response.last_page;
      var pagination = $('<ul class="pagination"></ul>');
      // Previous page link
      if (currentPage > 1) {
        pagination.append(
          '<li class="page-item"><a href="#" class="page-link" data-page="' +
          (currentPage - 1) +
          '">Previous</a></li>'
        );
      }
      // Individual page links
      for (var i = 1; i <= lastPage; i++) {
        var activeClass = i === currentPage ? "active" : "";
        pagination.append(
          '<li class="page-item ' +
          activeClass +
          '"><a href="#" class="page-link" data-page="' +
          i +
          '">' +
          i +
          "</a></li>"
        );
      }
      // Next page link
      if (currentPage < lastPage) {
        pagination.append(
          '<li class="page-item"><a href="#" class="page-link" data-page="' +
          (currentPage + 1) +
          '">Next</a></li>'
        );
      }
      paginationLinks.append(pagination);
      // Handle pagination link clicks
      paginationLinks.find("a").on("click", function (event) {
        event.preventDefault();
        var page = $(this).data("page");
        getWebsiteMonitorStatus(page);
      });
    })
    .fail(function (response) {

      $("#loading-image-preview").hide();

    });
}

$("#live-laravel-logs").on("click", function (e) {
  e.preventDefault();
  if ($("#live-laravel-logs-summary-modal").data('loaded')) {
    $("#live-laravel-logs-summary-modal").modal('show');
    liveLaravelLog();
  } else {
    $.ajax({
      url: configs.routes.live_laravel_logs_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#live-laravel-logs-summary-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#live-laravel-logs-summary-modal").modal('show');
        liveLaravelLog();
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function liveLaravelLog() {
  $.ajax({
    type: "GET",
    url: configs.routes.logging_live_logs_summary,
    dataType: "json",
    beforeSend: function (data) {
      $("#loading-image-preview").show();
    },
  })

    .done(function (response) {
      $("#loading-image-preview").hide();
      $("#live-laravel-logs-summary-modal-html").empty().html(response.html);
      $("#live-laravel-logs-summary-modal").modal("show");
    })
    .fail(function (response) {
      $("#loading-image-preview").hide();
    });
}


function getZabbixIssues(page) {
  var url = "/zabbix-webhook-data/issues-summary?page=" + page;

  $.ajax({
    type: "GET",
    url: url,
    dataType: "json",
    beforeSend: function (data) {
      $("#loading-image-preview").show();
    },
  })
    .done(function (response) {
      $("#loading-image-preview").hide();
      var tableBody = $("#zabbix-issues-summary-modal-html");
      tableBody.empty(); // Clear the table body
      // Loop through the data and populate the table rows
      $.each(response.data, function (index, item) {
        var row = $("<tr>");
        row.append($("<td>").text(item.subject));
        row.append($("<td>").text(item.short_message));
        row.append($("<td>").text(item.event_start));
        row.append($("<td>").text(item.event_name));
        row.append($("<td>").text(item.host));
        row.append($("<td>").text(item.severity));
        row.append($("<td>").text(item.short_operational_data));
        row.append($("<td>").text(item.event_id));
        // Add more table data cells as needed
        tableBody.append(row);
      });
      var paginationLinks = $(
        "#zabbix-issues-summary-modal-table-paginationLinks"
      );
      paginationLinks.empty(); // Clear the pagination links
      // Generate the pagination links manually
      var links = response.links;
      var currentPage = response.current_page;
      var lastPage = response.last_page;
      var pagination = $('<ul class="pagination"></ul>');
      // Previous page link
      if (currentPage > 1) {
        pagination.append(
          '<li class="page-item"><a href="#" class="page-link" data-page="' +
          (currentPage - 1) +
          '">Previous</a></li>'
        );
      }
      // Individual page links
      for (var i = 1; i <= lastPage; i++) {
        var activeClass = i === currentPage ? "active" : "";
        pagination.append(
          '<li class="page-item ' +
          activeClass +
          '"><a href="#" class="page-link" data-page="' +
          i +
          '">' +
          i +
          "</a></li>"
        );
      }
      // Next page link
      if (currentPage < lastPage) {
        pagination.append(
          '<li class="page-item"><a href="#" class="page-link" data-page="' +
          (currentPage + 1) +
          '">Next</a></li>'
        );
      }
      paginationLinks.append(pagination);
      // Handle pagination link clicks
      paginationLinks.find("a").on("click", function (event) {
        event.preventDefault();
        var page = $(this).data("page");
        getZabbixIssues(page);
      });
    })
    .fail(function (response) {

      $(".ajax-loader").hide();

      $("#loading-image-preview").hide();

    });
}

$("#zabbix-issues").on("click", function (e) {
  e.preventDefault();
  if ($("#zabbix-issues-summary-modal").data('loaded')) {
    $("#zabbix-issues-summary-modal").modal('show');
    getZabbixIssues(1);
  } else {
    $.ajax({
      url: configs.routes.zabbix_issue_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#zabbix-issues-summary-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#zabbix-issues-summary-modal").modal('show');
        getZabbixIssues(1);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$("#create_event").on("click", function (e) {
  e.preventDefault();

  if ($("#create-event-modal").data('loaded')) {
    $("#create-event-modal").modal('show');
  } else {
    // $("#loading-image-preview").show();
    $.ajax({
      url: configs.routes.create_event_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {

        $("#dynamic-model-section").append(response.html);
        $("#create-event-modal").data('loaded', true);

        $(".select2").select2();
        $(".clockpicker").clockpicker({
          autoclose: true,
          doneText: "Done",
          placement: "top"
        });
        $("#loading-image-preview").hide();
        $("#create-event-modal").modal('show');

        // Set up event handlers if necessary
        setUpEventHandlers();
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});


function setUpEventHandlers() {

  var days = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];

  function updateDayRows(startDate, endDate) {
    $('.day-row').hide();
    var currentDate = new Date(startDate);
    while (currentDate <= endDate) {
      var currentDay = currentDate.getDay();
      $('.' + days[currentDay]).show();
      currentDate.setDate(currentDate.getDate() + 1);
    }
  }

  function updateDayRowsWithEndDate(startDate, endDate) {
    $('.day-row').hide();
    var currentDay = startDate.getDay();
    var lastDay = endDate.getDay();
    for (var i = currentDay; i <= lastDay; i++) {
      $('.' + days[i]).show();
    }
  }

  function showAdditionalElements() {
    $('.day-row, .clockpicker').show();
  }

  $('#event-start-date, #event-end-date').on('change', function () {
    var startDate = new Date($('#event-start-date').val());
    var endDate = new Date($('#event-end-date').val());
    updateDayRows(startDate, endDate);
  });

  $('select[name="date_range_type"]').on('change', function () {
    if ($(this).val() == 'within') {
      $('#end-date-div').removeClass('hide');
      var startDate = new Date($('#event-start-date').val());
      var endDate = new Date($('#event-end-date').val());
      updateDayRowsWithEndDate(startDate, endDate);
    } else {
      $('#end-date-div').addClass('hide');
      var startDate = new Date($('#event-start-date').val());
      updateDayRows(startDate, startDate);
      showAdditionalElements(); // Hide additional elements
    }
  });

  // Initialize day rows based on current day
  var currentDay = new Date().getDay();
  $('.day-row').hide();
  $('.' + days[currentDay]).show();

  $('.clockpicker[name$="[start_at]"], #event-duration').on('change', function () {
    var startInput = $(this).closest('tr').find('.clockpicker[name$="[start_at]"]');
    var endInput = $(this).closest('tr').find('.clockpicker[name$="[end_at]"]');
    var selectedDuration = parseInt($('#event-duration').val()); // Get selected duration in minutes

    if (startInput.val() && !isNaN(selectedDuration)) {
      var start = moment(startInput.val(), 'HH:mm');
      var duration = moment.duration(selectedDuration, 'minutes');
      var end = start.clone().add(duration);
      endInput.val(end.format('HH:mm'));
    }
  });

  $('.add-vendor').click(function () {
    $('#vendor-inputs').toggle();
    $("#vendor_id").css({
      display: 'none'
    });
  });
}


$(document).on("submit", "#create-event-submit-form", function (e) {
  e.preventDefault();
  var $form = $(this).closest("form");
  $.ajax({
    type: "POST",
    url: $form.attr("action"),
    data: $form.serialize(),
    dataType: "json",
    beforeSend: function () {
      $("#loading-image-preview").show();
    },
    success: function (data) {
      if (data.code == 200) {
        $("#loading-image-preview").hide();
        $form[0].reset();
        $("#dynamic-model-section").modal("hide");
        toastr["success"](data.message, "Message");
      } else {
        toastr["error"](data.message, "Message");
        $("#loading-image-preview").hide();
      }
    },
    error: function (xhr, status, error) {
      var errors = xhr.responseJSON;
      $.each(errors, function (key, val) {
        $("#create-event-submit-form " + "#" + key + "_error").text(val[0]);
      });
      $("#loading-image-preview").hide();
    },
  });
});

$("#database-backup-monitoring").on("click", function (e) {
  e.preventDefault();
  if ($("#db-errors-list-modal").data('loaded')) {
    $("#db-errors-list-modal").modal('show');
    getdbbackupList(1);
  } else {
    $.ajax({
      url: configs.routes.database_backup_monitoring_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#db-errors-list-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#db-errors-list-modal").modal('show');
        getdbbackupList(1);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function getdbbackupList(pageNumber = 1) {
  $.ajax({
    url: configs.routes.get_backup_monitor_lists,
    type: "GET",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      page: pageNumber,
    },
    dataType: "json",
    beforeSend: function () {
      $("#loading-image").show();
    },
  })
    .done(function (response) {
      $("#loading-image").hide();
      if (response.html) {
        $(".db-list").html(response.html);
        $("#db-errors-list-modal").modal("show");
        if (response.count > 0) {
          $(".database-alert-badge").removeClass("hide");
        }
        renderPagination(response.data); // Ensure you handle pagination correctly
      }
    })
    .fail(function (response, ajaxOptions, thrownError) {
      toastr["error"](response.message);
      $("#loading-image").hide();
    });
}

$(document).on("click", ".expand-row-dblist", function () {
  var selection = window.getSelection();
  if (selection.toString().length === 0) {
    $(this).find(".td-mini-container").toggleClass("hidden");
    $(this).find(".td-full-container").toggleClass("hidden");
  }
});

function renderPagination(data) {
  var paginationContainer = $(".pagination-container");
  var currentPage = data.current_page;
  var totalPages = data.last_page;
  var html = "";
  if (totalPages > 1) {
    html += "<ul class='pagination'>";
    if (currentPage > 1) {
      html +=
        "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changePage(" +
        (currentPage - 1) +
        ")'>Previous</a></li>";
    }
    for (var i = 1; i <= totalPages; i++) {
      html +=
        "<li class='page-item " +
        (currentPage == i ? "active" : "") +
        "'><a class='page-link' href='javascript:void(0);' onclick='changePage(" +
        i +
        ")'>" +
        i +
        "</a></li>";
    }
    if (currentPage < totalPages) {
      html +=
        "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changePage(" +
        (currentPage + 1) +
        ")'>Next</a></li>";
    }
    html += "</ul>";
  }
  paginationContainer.html(html);
}
function changePage(pageNumber) {
  getdbbackupList(pageNumber);
}

function updateIsResolved(checkbox) {
  var dbListId = checkbox.getAttribute("data-id");
  $.ajax({
    url: configs.routes.db_update_isResolved,
    method: "GET",
    data: {
      id: dbListId,
    },
    success: function (response) {
      toastr["success"](response.message, "Message");
    },
    error: function (xhr, status, error) {
      toastr["error"]("Error occured.please try again", "Error");
    },
  });
}

function updateReadEmail(checkbox) {
  var emailId = checkbox.getAttribute("data-id");
  $.ajax({
    url: configs.routes.website_email_update,
    type: "GET",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      id: emailId,
    },
    success: function (response) {
      toastr["success"](response.message, "Message");
    },
    error: function (xhr, status, error) {
      toastr["error"]("Error occured.please try again", "Error");
    },
  });
}

$("#jenkins-build-status").on("click", function (e) {
  e.preventDefault();
  if ($("#create-jenkins-status-modal").data('loaded')) {
    $("#create-jenkins-status-modal").modal('show');
    getJenkinsStatus(1);
  } else {
    $.ajax({
      url: configs.routes.jenkins_build_status_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#create-jenkins-status-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#create-jenkins-status-modal").modal('show');
        getJenkinsStatus(1);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function getJenkinsStatus(page) {
  var url = "/monitor-jenkins-build/list?page=" + page;

  $.ajax({
    type: "GET",
    url: url,
    dataType: "json",
    beforeSend: function (data) {
      $("#loading-image-preview").show();
    },
  })
    .done(function (response) {
      $("#loading-image-preview").hide();
      var tableBody = $("#jenkins-status-modal-html");
      tableBody.empty(); // Clear the table body
      // Loop through the data and populate the table rows
      $.each(response.data, function (index, item) {
        var row = $("<tr>");
        row.append($("<td>").text(item.id));
        row.append($("<td>").text(item.project));
        row.append($("<td>").text(item.failuare_status_list));
        tableBody.append(row);
      });
      var paginationLinks = $("#jenkins-status-modal-table-paginationLinks");
      paginationLinks.empty(); // Clear the pagination links
      // Generate the pagination links manually
      var links = response.links;
      var currentPage = response.current_page;
      var lastPage = response.last_page;
      var pagination = $('<ul class="pagination"></ul>');
      // Previous page link
      if (currentPage > 1) {
        pagination.append(
          '<li class="page-item"><a href="#" class="page-link" data-page="' +
          (currentPage - 1) +
          '">Previous</a></li>'
        );
      }
      // Individual page links
      for (var i = 1; i <= lastPage; i++) {
        var activeClass = i === currentPage ? "active" : "";
        pagination.append(
          '<li class="page-item ' +
          activeClass +
          '"><a href="#" class="page-link" data-page="' +
          i +
          '">' +
          i +
          "</a></li>"
        );
      }
      // Next page link
      if (currentPage < lastPage) {
        pagination.append(
          '<li class="page-item"><a href="#" class="page-link" data-page="' +
          (currentPage + 1) +
          '">Next</a></li>'
        );
      }
      paginationLinks.append(pagination);
      // Handle pagination link clicks
      paginationLinks.find("a").on("click", function (event) {
        event.preventDefault();
        var page = $(this).data("page");
        getJenkinsStatus(page);
      });
    })
    .fail(function (response) {


      $("#loading-image-preview").hide();

    });
}

$("#magento-cron-error-status-alerts").on("click", function (e) {
  e.preventDefault();
  if ($("#magento-cron-error-status-modal").data('loaded')) {
    $("#magento-cron-error-status-modal").modal('show');
    listmagnetoerros();
  } else {
    $.ajax({
      url: configs.routes.magento_cron_error_status_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#magento-cron-error-status-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#magento-cron-error-status-modal").modal('show');
        listmagnetoerros();
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function listmagnetoerros(pageNumber = 1) {
  $.ajax({
    url: configs.routes.magento_cron_error_list,
    type: "GET",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      page: pageNumber,
    },
    dataType: "json",
    beforeSend: function () {
      $("#loading-image").show();
    },
  })
    .done(function (response) {
      $("#loading-image").hide();
      if (response.html) {
        $(".magneto-error-list").html(response.html);
        $("#magento-cron-error-status-modal").modal("show");
        renderMangetoErrorPagination(response.data); // Ensure you handle pagination correctly
      }
    })
    .fail(function (response, ajaxOptions, thrownError) {
      toastr["error"](response.message);
      $("#loading-image").hide();
    });
}

function renderMangetoErrorPagination(data) {
  var paginationContainer = $(".pagination-container");
  var currentPage = data.current_page;
  var totalPages = data.last_page;
  var html = "";
  var maxVisiblePages = 10;

  if (totalPages > 1) {
    html += "<ul class='pagination'>";
    if (currentPage > 1) {
      html +=
        "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeMagnetoErrorPage(" +
        (currentPage - 1) +
        ")'>Previous</a></li>";
    }

    var startPage = 1;
    var endPage = totalPages;

    if (totalPages > maxVisiblePages) {
      if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
        endPage = maxVisiblePages;
      } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
        startPage = totalPages - maxVisiblePages + 1;
      } else {
        startPage = currentPage - Math.floor(maxVisiblePages / 2);
        endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
      }

      if (startPage > 1) {
        html +=
          "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeMagnetoErrorPage(1)'>1</a></li>";
        if (startPage > 2) {
          html +=
            "<li class='page-item disabled'><span class='page-link'>...</span></li>";
        }
      }
    }

    for (var i = startPage; i <= endPage; i++) {
      html +=
        "<li class='page-item " +
        (currentPage == i ? "active" : "") +
        "'><a class='page-link' href='javascript:void(0);' onclick='changeMagnetoErrorPage(" +
        i +
        ")'>" +
        i +
        "</a></li>";
    }
    html += "</ul>";
  }
  paginationContainer.html(html);
}

function changeMagnetoErrorPage(pageNumber) {
  listmagnetoerros(pageNumber);
}

$("#code-shortcuts").on("click", function (e) {
  e.preventDefault();
  if ($("#short-cut-notes-alerts-modal").data('loaded')) {
    $("#short-cut-notes-alerts-modal").modal('show');
    getShortcutNotes(true);
  } else {
    $.ajax({
      url: configs.routes.code_shortcut_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#short-cut-notes-alerts-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#short-cut-notes-alerts-modal").modal('show');
        getShortcutNotes(true);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function getShortcutNotes(pageNumber = 1) {
  $.ajax({
    url: configs.routes.code_get_Shortcut_notes,
    type: "GET",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      page: pageNumber,
    },
    dataType: "json",
    beforeSend: function () {
      $("#loading-image").show();
    },
  })
    .done(function (response) {
      $("#loading-image").hide();
      if (response.html) {
        $(".short-cut-notes-alerts-list").html(response.html);
        $("#short-cut-notes-alerts-modal").modal("show");
        renderShortcutNotesPagination(response.data); // Ensure you handle pagination correctly
      }
    })
    .fail(function (data, ajaxOptions, thrownError) {
      toastr["error"](data.message);
      $("#loading-image").hide();
    });
}

function renderShortcutNotesPagination(data) {
  var codePagination = $(".pagination-container-short-cut-notes-alerts");
  var currentPage = data.current_page;
  var totalPages = data.last_page;
  var html = "";
  if (totalPages > 1) {
    html += "<ul class='pagination'>";
    if (currentPage > 1) {
      html +=
        "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changePageForShortCut(" +
        (currentPage - 1) +
        ")'>Previous</a></li>";
    }
    for (var i = 1; i <= totalPages; i++) {
      html +=
        "<li class='page-item " +
        (currentPage == i ? "active" : "") +
        "'><a class='page-link' href='javascript:void(0);' onclick='changePageForShortCut(" +
        i +
        ")'>" +
        i +
        "</a></li>";
    }
    if (currentPage < totalPages) {
      html +=
        "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changePageForShortCut(" +
        (currentPage + 1) +
        ")'>Next</a></li>";
    }
    html += "</ul>";
  }
  codePagination.html(html);
}
function changePageForShortCut(pageNumber) {
  getShortcutNotes(pageNumber);
}

$("#create-documents").on("click", function (e) {
  e.preventDefault();
  if ($("#short-cut-documentation-modal").data('loaded')) {
    $("#short-cut-documentation-modal").modal('show');
    getDocumentations(true);
  } else {
    $.ajax({
      url: configs.routes.list_documentation_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#short-cut-documentation-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#short-cut-documentation-modal").modal('show');
        getDocumentations(true);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function getDocumentations(showModal = false) {
  $.ajax({
    type: "GET",
    url: configs.routes.documentShorcut_list,
    dataType: "json",
    beforeSend: function (data) {
      $("#loading-image-preview").show();
    },
  })
    .done(function (response) {
      $("#loading-image-preview").hide();
      $("#list-documentation-shortcut-modal-html").empty().html(response.tbody);
      if (showModal) {
        $("#short-cut-documentation-modal").modal("show");
      }
    })
    .fail(function (response) {

      $("#loading-image-preview").hide();

    });
}

function showdocumentCreateModal() {
  $("#short-cut-documentation-modal").modal("hide");
  if ($("#documentaddModal").data('loaded')) {
    $("#documentaddModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.create_documentation_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#documentaddModal").data('loaded', true);
        $('.globalSelect2').select2({
          ajax: {
            url: function (params) {
              return $(this).data('ajax');
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                q: params.term // search term
              };
            },
            processResults: function (data) {
              return {
                results: data.items
              };
            },
            cache: true
          },
          placeholder: $(this).data('placeholder')
        });
        $("#loading-image-preview").hide();
        $("#documentaddModal").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
}

$("#event-alerts").on("click", function (e) {
  e.preventDefault();
  if ($("#event-alerts-modal").data('loaded')) {
    $("#event-alerts-modal").modal('show');
    getEventAlerts(true);
  } else {
    $.ajax({
      url: configs.routes.event_alert_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#event-alerts-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#event-alerts-modal").modal('show');
        getEventAlerts(true);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});
$(document).ready(function () {
  var Role = configs.auth.has_admin;
  if (Role) {
    // getEventAlerts();
    // getTimeEstimationAlerts();
  }
});

function getEventAlerts(showModal = false) {
  $.ajax({
    type: "GET",
    url: configs.routes.event_getEventAlerts,
    dataType: "json",
    beforeSend: function (data) {
      $("#loading-image-preview").show();
    },
  })
    .done(function (response) {
      $("#loading-image-preview").hide();
      $("#event-alerts-modal-html").empty().html(response.html);
      if (showModal) {
        $("#event-alerts-modal").modal("show");
      }
      if (response.count > 0) {
        $(".event-alert-badge").removeClass("hide");
      }
    })
    .fail(function (response) {

      $("#loading-image-preview").hide();

    });
}

$(".create-event-shortcut").on("click", function (e) {
  e.preventDefault();
  if ($("#shortcut-user-event-model").data('loaded')) {
    $("#shortcut-user-event-model").modal('show');
  } else {
    $.ajax({
      url: configs.routes.create_event_shortcut_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#shortcut-user-event-model").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#shortcut-user-event-model").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".user-availability").on("click", function (e) {
  e.preventDefault();
  if ($("#searchUserSchedule").data('loaded')) {
    $("#searchUserSchedule").modal('show');
  } else {
    $.ajax({
      url: configs.routes.user_availability_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#searchUserSchedule").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#searchUserSchedule").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".create-google-doc").on("click", function (e) {
  e.preventDefault();
  if ($("#createGoogleDocModal").data('loaded')) {
    $("#createGoogleDocModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.create_google_doc_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#createGoogleDocModal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#createGoogleDocModal").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".search-google-doc").on("click", function (e) {
  e.preventDefault();
  if ($("#SearchGoogleDocModal").data('loaded')) {
    $("#SearchGoogleDocModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.search_google_doc_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#SearchGoogleDocModal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#SearchGoogleDocModal").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$("#search-command").on("click", function (e) {
  e.preventDefault();
  if ($("#magento-commands-modal").data('loaded')) {
    $("#magento-commands-modal").modal('show');
    getMagentoCommand(true);
  } else {
    $.ajax({
      url: configs.routes.search_command_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#magento-commands-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#magento-commands-modal").modal('show');
        getMagentoCommand(true);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function getMagentoCommand(showModal = false) {
  $.ajax({
    type: "GET",
    url: configs.routes.magento_getMagentoCommand,
    dataType: "json",
    beforeSend: function (data) {
      $("#loading-image-preview").show();
    },
  })
    .done(function (response) {
      $("#loading-image-preview").hide();
      $("#magento-commands-modal-html").empty().html(response.html);
      //if (showModal) {
      $("#magento-commands-modal").modal("show");
      //}
    })
    .fail(function (response) {

      $("#loading-image-preview").hide();

    });
}

$("#timer-alerts").on("click", function (e) {
  e.preventDefault();
  if ($("#timer-alerts-modal").data('loaded')) {
    $("#timer-alerts-modal").modal('show');
    getTimerAlerts(true);
  } else {
    $.ajax({
      url: configs.routes.time_doctor_logs_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#timer-alerts-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#timer-alerts-modal").modal('show');
        getTimerAlerts(true);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function getTimerAlerts(showModal = false) {
  $.ajax({
    type: "GET",
    url: configs.routes.get_timer_alerts,
    dataType: "json",
    beforeSend: function (data) {
      $("#loading-image-preview").show();
    },
  })
    .done(function (response) {
      $("#loading-image-preview").hide();
      $("#timer-alerts-modal-html").empty().html(response.tbody);
      if (showModal) {
        $("#timer-alerts-modal").modal("show");
      }
      if (response.count > 0) {
        $(".timer-alert-badge").removeClass("hide");
      }
    })
    .fail(function (response) {
      $("#loading-image-preview").hide();
    });
}

function getTimeEstimationAlerts() {
  $.ajax({
    type: "GET",
    url: configs.routes.task_estimate_alert,
    dataType: "json",
    beforeSend: function (data) {
      $(".ajax-loader").show();
    },
  })
    .done(function (response) {
      $(".ajax-loader").hide();
      if (response.count > 0) {
        $(".time-estimation-badge").removeClass("hide");
      }
    })
    .fail(function (response) {
      $(".ajax-loader").hide();
    });
}

$(document).on("submit", "#event-alert-date-form", function (event) {
  event.preventDefault();
  var dateValue = $('input[name="event_alert_date"]').val();
  $.ajax({
    type: "GET",
    url: configs.routes.event_getEventAlerts,
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
      date: dateValue,
    },
  })
    .done(function (response) {
      $(".ajax-loader").hide();
      $("#event-alerts-modal-html").empty().html(response.html);
      if (showModal) {
        $("#event-alerts-modal").modal("show");
      }
      if (response.count > 0) {
        $(".event-alert-badge").removeClass("hide");
      }
    })
    .fail(function (response) {
      $(".ajax-loader").hide();
    });
});

$(document).on("click", ".event-alert-log-modal", function (e) {
  var event_type = $(this).data("event_type");
  var event_id = $(this).data("event_id");
  var event_schedule_id = $(this).data("event_schedule_id");
  var assets_manager_id = $(this).data("assets_manager_id");
  var event_alert_date = $(this).data("event_alert_date");
  var is_read = $(this).prop("checked");

  $.ajax({
    type: "POST",
    url: configs.routes.event_saveAlertLog,
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
      event_type,
      event_id,
      event_schedule_id,
      assets_manager_id,
      event_alert_date,
      is_read,
    },
    dataType: "json",
    beforeSend: function (data) {
      $(".ajax-loader").show();
    },
  })
    .done(function (response) {
      toastr["success"](response.message, "Message");
      $(".ajax-loader").hide();
    })
    .fail(function (response) {
      $(".ajax-loader").hide();
    });
});

$("#script-document-logs").on("click", function (e) {
  e.preventDefault();
  if ($("#script-document-error-logs-alerts-modal").data('loaded')) {
    $("#script-document-error-logs-alerts-modal").modal('show');
    getScriptDocumentErrorLogs(true);
  } else {
    $.ajax({
      url: configs.routes.script_document_error_log_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#script-document-error-logs-alerts-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#script-document-error-logs-alerts-modal").modal('show');
        getScriptDocumentErrorLogs(true);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(document).on("click", "#assets-manager-listing", function (e) {
  e.preventDefault();
  getAssetsManager();
});

function getScriptDocumentErrorLogs(showModal = false) {
  $.ajax({
    type: "GET",
    url: configs.routes.script_documents_getScriptDocumentErrorLogsList,
    dataType: "json",
    beforeSend: function (data) {
      $("#loading-image-preview").show();
    },
  })
    .done(function (response) {
      $("#loading-image-preview").hide();
      $("#script-document-error-logs-alerts-modal-html")
        .empty()
        .html(response.tbody);

      if (response.count === 0) {
        $(".script-document-error-badge").addClass("hide");
      }

      if (showModal) {
        $("#script-document-error-logs-alerts-modal").modal("show");
      }
    })
    .fail(function (response) {

      $("#loading-image-preview").hide();

    });
}

function getAssetsManager() {
  $.ajax({
    type: "GET",
    url: configs.routes.assetsManager_loadTable,
    dataType: "json",
    beforeSend: function (data) {
      $(".ajax-loader").show();
    },
  })
    .done(function (response) {
      $(".ajax-loader").hide();
      $("#ajax-assets-manager-listing-modal").empty().html(response.tpl);
      $("#assetsEditModal").modal("show");
    })
    .fail(function (response) {
      $(".ajax-loader").hide();
      $("#ajax-assets-manager-listing-modal").empty().html(response.tpl);
      $("#assetsEditModal").modal("show");
    });
}

$(document).on(
  "click",
  ".script-document-last_output-header-view",
  function () {
    id = $(this).data("id");
    $.ajax({
      method: "GET",
      url: configs.routes.comment + "/" + id,
      dataType: "json",
      success: function (response) {
        $("#script-document-last-output-list-header")
          .find(".script-document-last-output-header-view")
          .html(response.last_output);
        $("#script-document-last-output-list-header").modal("show");
      },
    });
  }
);

$("#google-drive-screen-cast").on("click", function (e) {
  e.preventDefault();
  if ($("#google-drive-screen-cast-alerts-modal").data('loaded')) {
    $("#google-drive-screen-cast-alerts-modal").modal('show');
    getgooglescreencast(true);
  } else {
    $.ajax({
      url: configs.routes.google_drive_screencast_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#google-drive-screen-cast-alerts-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#google-drive-screen-cast-alerts-modal").modal('show');
        getgooglescreencast(true);
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".upload-screencast-model").on("click", function (e) {
  e.preventDefault();
  if ($("#uploadeScreencastModal").data('loaded')) {
    $("#uploadeScreencastModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.upload_screencast_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#uploadeScreencastModal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#uploadeScreencastModal").modal('show');
        showCreateScreencastModal();
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

function showPasswordCreateModal(is_close_modal = 0) {
  if ($("#passwordCreateModal").data('loaded')) {
    $("#passwordCreateModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.password_create_modal,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#passwordCreateModal").data('loaded', true);
        $("#passwordCreateModal").modal('show');
      }
    });
    if (is_close_modal === 1) {
      $("#searchPassswordModal").modal("hide");
    }
  }
}

function showCodeShortcutPlatformModal() {
  if ($("#code-shortcut-platform").data('loaded')) {
    $("#code-shortcut-platform").modal('show');
  } else {
    $.ajax({
      url: configs.routes.short_cut_notes_create,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#code-shortcut-platform").data('loaded', true);
        $("#create_code_shortcut").data('loaded', true);
        $("#show_full_log_modal").data('loaded', true);
        $("#code-shortcut-platform").modal('show');
      }
    });
  }
}
function showCreateCodeShortcutModal() {
  if ($("#create_code_shortcut").data('loaded')) {
    $("#create_code_shortcut").modal('show');
  } else {
    $.ajax({
      url: configs.routes.short_cut_notes_create,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#create_code_shortcut").data('loaded', true);
        $("#code-shortcut-platform").data('loaded', true);
        $("#show_full_log_modal").data('loaded', true);
        $("#create_code_shortcut").modal('show');
      }
    });
  }
}
function showFullLogModal() {
  if ($("#show_full_log_modal").data('loaded')) {
    $("#show_full_log_modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.short_cut_notes_create,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#show_full_log_modal").data('loaded', true);
        $("#code-shortcut-platform").data('loaded', true);
        $("#create_code_shortcut").data('loaded', true);
        $("#show_full_log_modal").modal('show');
      }
    });
  }
}

function uploadeScreencastModal() {
  if ($("#uploadeScreencastModal").data('loaded')) {
    $("#uploadeScreencastModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.upload_screencast_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#uploadeScreencastModal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#uploadeScreencastModal").modal('show');
        showCreateScreencastModal();
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
}
function showCreateScreencastModal() {
  $("#google-drive-screen-cast-alerts-modal").modal("hide");
  $.ajax({
    url: configs.routes.getDropdownDatas,
    type: "GET",
    dataType: "json",
    success: function (response) {
      var tasks = response.tasks;
      var users = response.users;
      var generalTask = response.generalTask;

      var $taskSelect = $("#id_label_task");
      var $userReadSelect = $("#id_label_multiple_user_read");
      var $userWriteSelect = $("#id_label_multiple_user_write");

      $taskSelect.empty();
      $taskSelect.append(
        '<option value="" class="form-control">Select Task</option>'
      );

      tasks.forEach(function (task) {
        $taskSelect.append(
          '<option value="' +
          task.id +
          '">' +
          task.id +
          "-" +
          task.subject +
          "</option>"
        );
      });
      generalTask.forEach(function (generalTask) {
        $taskSelect.append(
          '<option value="' +
          generalTask.id +
          '">' +
          generalTask.id +
          "-" +
          generalTask.subject +
          "</option>"
        );
      });

      $userReadSelect.empty();
      $userWriteSelect.empty();
      $userReadSelect.append(
        '<option value="" class="form-control">Select User</option>'
      );
      $userWriteSelect.append(
        '<option value="" class="form-control">Select User</option>'
      );

      users.forEach(function (user) {
        var optionText = user.name;
        $userReadSelect.append(
          '<option value="' + user.gmail + '">' + optionText + "</option>"
        );
        $userWriteSelect.append(
          '<option value="' + user.gmail + '">' + optionText + "</option>"
        );
      });
    },
    error: function (xhr, status, error) {
      console.error(error);
    },
  });
}

function getgooglescreencast(showModal = false) {
  $.ajax({
    type: "GET",
    url: configs.routes.google_drive_screencast_getGooglesScreencast,
    dataType: "json",
    beforeSend: function (data) {
      $("#loading-image-preview").show();
    },
  })
    .done(function (response) {
      $("#loading-image-preview").hide();
      $("#google-drive-screen-cast-modal-html").empty().html(response.tbody);
      if (showModal) {
        $("#google-drive-screen-cast-modal").modal("show");
      }
    })
    .fail(function (response) {

      $("#loading-image-preview").hide();

    });
}

$(".permission-request").on("click", function (e) {
  e.preventDefault();

  setPermissionRequests();
  $("#modal-container").load("/permission-request-model", function () {
    $('#permission-request-model').modal('show');
  });
  if ($("#permission-request-model").data('loaded')) {
    $("#permission-request-model").modal('show');
    setPermissionRequests();
  } else {
    $.ajax({
      url: configs.routes.permission_request_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#permission-request-model").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#modal-container").load("/permission-request-model", function () {
          $('#permission-request-model').modal('show');
        });
        setPermissionRequests();
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(document).on("click", ".permission-request-search", function (e) {
  e.preventDefault();
  setPermissionRequests();
});

function setPermissionRequests() {
  let user_id = $(".permission-request-search-user-id").val();

  let url =
    "/user-management/request-list/" + (user_id ? "?user_id=" + user_id : "");

  $.ajax({
    url,
    type: "GET",
    dataType: "json",
    beforeSend: function () {
      $("#loading-image-preview").show();
    },
    success: function (result) {
      $("#loading-image-preview").hide();
      if (result.code == 200) {
        var t = "";
        $.each(result.data, function (k, v) {
          t += `<tr><td>` + v.name + `</td>`;
          t += `<td>` + v.permission_name + `</td>`;
          t += `<td>` + v.request_date + `</td>`;
          t +=
            `<td><button class="btn btn-secondary btn-xs permission-grant" data-type="accept" data-id="` +
            v.permission_id +
            `" data-user="` +
            v.user_id +
            `">Accept</button>
                             <button class="btn btn-secondary btn-xs permission-grant" data-type="reject" data-id="` +
            v.permission_id +
            `" data-user="` +
            v.user_id +
            `">Reject</button>
                          </td></tr>`;
        });
        if (t == "") {
          t = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
        }
      }
      $("#permission-request-model").find(".show-list-records").html(t);
    },
    error: function () {
      $("#loading-image-preview").hide();
    },
  });
}

$(".add_todo_title").change(function () {
  if ($(".add_todo_subject").val() == "") {
    $(".add_todo_subject").val("");
    $(".add_todo_subject").val($(".add_todo_title").val());
  }
});

$("#todo-date").datetimepicker({
  format: "YYYY-MM-DD",
});

$(".todo-date").datetimepicker({
  format: "YYYY-MM-DD",
});

$(document).on("click", ".todolist-request", function (e) {
  e.preventDefault();
  $("#modal-container").load("/todolist-request-model", function () {
    getTodoCategoryAndStatus();
    $("#todolist-request-model").modal("show");
  });
});

function getTodoCategoryAndStatus() {
  $.ajax({
    url: configs.routes.todolist_category_status,
    type: "GET",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {},
    beforeSend: function () {
      $(this).text("Loading...");
      $("#loading-image").show();
    },
    success: function (response) {
      // Inject todocategories and statuses
      // the response is a JSON array of categories
      var todoCategories = response.todoCategories;
      var $dropdownCategory = $(".add_todo_category");
      oldCategory = "";
      // Clear existing options
      $dropdownCategory.empty();
      $dropdownCategory.append('<option value="">Select Category</option>');
      $dropdownCategory.append('<option value="-1">Add New Category</option>');
      // Append new options
      $.each(todoCategories, function (index, todoCategory) {
        var selected = oldCategory == todoCategory.id ? "selected" : ""; // Check if the category should be selected
        $dropdownCategory.append(
          '<option value="' +
          todoCategory.id +
          '" ' +
          selected +
          ">" +
          todoCategory.name +
          "</option>"
        );
      });
      // the response is a JSON array of statuses
      var statuses = response.statuses;
      var $dropdownStatus = $(".add_todo_status");
      oldStatus = "";
      // Clear existing options
      $dropdownStatus.empty();

      // Append new options
      $.each(statuses, function (index, status) {
        var selected = oldStatus == status.id ? "selected" : ""; // Check if the status should be selected
        $dropdownStatus.append(
          '<option value="' +
          status.id +
          '" ' +
          selected +
          ">" +
          status.name +
          "</option>"
        );
      });
      $("#loading-image").hide();
    },
  }).fail(function (response) {
    $("#loading-image").hide();
    toastr["error"](response.responseJSON.message);
  });
}

$(document).on("click", ".todolist-get", function (e) {
  e.preventDefault();
  $("#todolist-get-model").modal("show");
});

$(".menu-create-database").on("click", function (e) {
  e.preventDefault();

  $("#modal-container").load("/menu-create-database-model", function () {
    $('#menu-create-database-model').modal('show');
  });
  if ($("#menu-create-database-model").data('loaded')) {
    $("#menu-create-database-model").modal('show');
  } else {
    $.ajax({
      url: configs.routes.create_database_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#menu-create-database-model").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#modal-container").load("/menu-create-database-model", function () {
          $('#menu-create-database-model').modal('show');
        });
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".menu-show-task").on("click", function (e) {
  e.preventDefault();
  $("#modal-container").load("/menu-show-task-model", function () {
    $("#menu-show-task-model").modal('show');
  });
  if ($("#menu-show-task-model").data('loaded')) {
    $("#menu-show-task-model").modal('show');
  }

});

$(".vendor-flowchart-header").on("click", function (e) {
  e.preventDefault();
  if ($("#vendor-flowchart-header-model").data('loaded')) {
    $("#vendor-flowchart-header-model").modal('show');
  } else {
    $.ajax({
      url: configs.routes.vendor_flowchart_header_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#vendor-flowchart-header-model").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#vendor-flowchart-header-model").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".vendor-qa-header").on("click", function (e) {
  e.preventDefault();
  if ($("#vendor-qa-header-model").data('loaded')) {
    $("#vendor-qa-header-model").modal('show');
  } else {
    $.ajax({
      url: configs.routes.vendor_qa_header_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#vendor-qa-header-model").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#vendor-qa-header-model").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".vendor-rqa-header").on("click", function (e) {
  e.preventDefault();
  if ($("#vendor-rqa-header-model").data('loaded')) {
    $("#vendor-rqa-header-model").modal('show');
  } else {
    $.ajax({
      url: configs.routes.vendor_rating_qa_header_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#vendor-rqa-header-model").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#vendor-rqa-header-model").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".create-resource").on("click", function (e) {
  e.preventDefault();

  $("#modal-container").load("/menu-show-dev-task-model", function () {
    $("#menu-show-dev-task-model").modal('show');
  });
  if ($("#shortcut_addresource").data('loaded')) {
    $("#shortcut_addresource").modal('show');
  } else {
    $.ajax({
      url: configs.routes.create_resource_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#shortcut_addresource").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#shortcut_addresource").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".create-vendor").on("click", function (e) {
  e.preventDefault();

  $("#modal-container-1").load("/menu-todolist-get-model", function () {
    getTodoListHeader();
    $("#menu-todolist-get-model").modal('show');
  });

  if ($("#vendorShortcutCreateModal").data('loaded')) {
    $("#vendorShortcutCreateModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.create_vendor_shortcut_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#vendorShortcutCreateModal").data('loaded', true);
        $(".select-multiple-s").select2({
          tags: true,
        });
        $(function () {
          $('.selectpicker').selectpicker();
        });
        $("#loading-image-preview").hide();
        $("#vendorShortcutCreateModal").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".keyword-quick-reply").on("click", function (e) {
  e.preventDefault();
  if ($("#shortcut-header-modal").data('loaded')) {
    $("#shortcut-header-modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.keyword_quick_reply_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#shortcut-header-modal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#shortcut-header-modal").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".create-easy-task").on("click", function (e) {
  e.preventDefault();
  if ($("#quick-create-task").data('loaded')) {
    $("#quick-create-task").modal('show');
  } else {
    $.ajax({
      url: configs.routes.create_dev_task_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#quick-create-task").data('loaded', true);
        $('.globalSelect2').select2({
          ajax: {
            url: function (params) {
              return $(this).data('ajax');
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                q: params.term // search term
              };
            },
            processResults: function (data) {
              return {
                results: data.items
              };
            },
            cache: true
          },
          placeholder: $(this).data('placeholder')
        });
        $("#loading-image-preview").hide();
        $("#quick-create-task").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(".menu-show-dev-task").on("click", function (e) {
  e.preventDefault();
  if ($("#menu-show-dev-task-model").data('loaded')) {
    $("#menu-show-dev-task-model").modal('show');
  } else {
    $.ajax({
      url: configs.routes.quick_dev_task_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#menu-show-dev-task-model").data('loaded', true);
        $("#quicktask_user_id").select2({ width: "20%" });
        $("#loading-image-preview").hide();
        $("#menu-show-dev-task-model").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(document).on("click", ".menu-todolist-get", function (e) {
  e.preventDefault();

  $.ajax({
    url: configs.routes.vendors_flowchart_getremarks,
    type: "POST",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      vendor_id: vendor_id,
      flow_chart_id: flow_chart_id,
    },
    success: function (response) {
      if (response.status) {
        $("#vfc-remarks-histories-list-header-fc")
          .find(".vfc-remarks-histories-list-view-header-fc")
          .html(response.html);
        $("#vfc-remarks-histories-list-header-fc").modal("show");
      } else {
        toastr["error"](response.error, "Message");
      }
    },
  });
  getTodoListHeader();
  $("#menu-todolist-get-model").modal("show");
});

$(document).on("change", ".status-dropdown-header-fc", function (e) {
  e.preventDefault();
  var vendor_id = $(this).data("id");
  var flow_chart_id = $(this).data("flow_chart_id");
  var selectedStatus = $(this).val();

  // Make an AJAX request to update the status
  $.ajax({
    url: "/vendor/update-flowchartstatus",
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      vendor_id: vendor_id,
      flow_chart_id: flow_chart_id,
      selectedStatus: selectedStatus,
    },
    success: function (response) {
      toastr["success"]("Status  Created successfully!!!", "success");
    },
    error: function (xhr, status, error) {
      // Handle the error here
      console.error(error);
    },
  });
});

$(document).on("click", ".status-history-show-header-fc", function () {
  var vendor_id = $(this).attr("data-id");
  var flow_chart_id = $(this).attr("data-flow_chart_id");

  $.ajax({
    url: configs.routes.vendors_flowchartstatus_histories,
    type: "POST",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      vendor_id: vendor_id,
      flow_chart_id: flow_chart_id,
    },
    success: function (response) {
      if (response.status) {
        $("#fl-status-histories-list-header-fc")
          .find(".fl-status-histories-list-view-header-fc")
          .html(response.html); // Insert the server-rendered HTML

        $("#fl-status-histories-list-header-fc").modal("show");
      } else {
        toastr["error"](response.error, "Message");
      }
    },
  });
});

function saveAnswerHeaderQa(vendor_id, question_id) {
  var answer = $("#answer_header_" + vendor_id + "_" + question_id).val();

  if (answer == "") {
    
    toastr["error"]("Please enter answer.", "error");
  } else {
    $.ajax({
      url: configs.routes.vendors_question_saveanswer,
      type: "POST",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      data: {
        vendor_id: vendor_id,
        question_id: question_id,
        answer: answer,
      },
      beforeSend: function () {
        $(this).text("Loading...");
        $("#loading-image").show();
      },
      success: function (response) {
        $("#answer_header_" + vendor_id + "_" + question_id).val("");
        $("#loading-image").hide();
        toastr["success"]("Answer Added successfully!!!", "success");
      },
    }).fail(function (response) {
      $("#loading-image").hide();
      toastr["error"](response.responseJSON.message);
    });
  }
}

$(document).on("click", ".answer-history-show-header-qa", function () {
  var vendor_id = $(this).attr("data-vendorid");
  var question_id = $(this).attr("data-qa_id");

  $.ajax({
    url: configs.routes.vendors_question_getgetanswer,
    type: "POST",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      vendor_id: vendor_id,
      question_id: question_id,
    },
    success: function (response) {
      if (response.status) {
        var html = "";
        $.each(response.data, function (k, v) {
          html += `<tr>
                                <td> ${k + 1} </td>
                                <td> ${v.answer} </td>
                                <td> ${v.created_at} </td>
                            </tr>`;
        });
        $("#vqa-answer-histories-list-header-qa")
          .find(".vqa-answer-histories-list-view-header-qa")
          .html(html);
        $("#vqa-answer-histories-list-header-qa").modal("show");
      } else {
        toastr["error"](response.error, "Message");
      }
    },
  });
});
$(document).on("change", ".status-dropdown-header-qa", function (e) {
  e.preventDefault();
  var vendor_id = $(this).data("id");
  var question_id = $(this).data("qa_id");
  var selectedStatus = $(this).val();

  // Make an AJAX request to update the status
  $.ajax({
    url: "/vendor/update-qastatus",
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      vendor_id: vendor_id,
      question_id: question_id,
      selectedStatus: selectedStatus,
    },
    success: function (response) {
      toastr["success"]("Status  Created successfully!!!", "success");
    },
    error: function (xhr, status, error) {
      // Handle the error here
      console.error(error);
    },
  });
});
// $(document).on("click", ".status-history-show-header-qa", function () {
//   var vendor_id = $(this).attr("data-id");
//   var question_id = $(this).attr("data-qa_id");

//   $.ajax({
//     url: configs.routes.vendors_qastatus_histories,
//     type: "POST",
//     headers: {
//       "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//     },
//     data: {
//       vendor_id: vendor_id,
//       question_id: question_id,
//     },
//     success: function (response) {
//       if (response.status) {
//         var html = "";
//         $.each(response.data, function (k, v) {
//           html += `<tr>
//                                 <td> ${k + 1} </td>
//                                 <td> ${v.old_value != null
//               ? v.old_value.status_name
//               : " - "
//             } </td>
//                                 <td> ${v.new_value != null
//               ? v.new_value.status_name
//               : " - "
//             } </td>
//                                 <td> ${v.user !== undefined ? v.user.name : " - "
//             } </td>
//                                 <td> ${v.created_at} </td>
//                             </tr>`;
//         });
//         $("#qa-status-histories-list-header-qa")
//           .find(".qa-status-histories-list-view-header-qa")
//           .html(html);
//         $("#qa-status-histories-list-header-qa").modal("show");
//       } else {
//         toastr["error"](response.error, "Message");
//       }
//     },
//   });
// });

// function saveAnswerHeaderRQa(vendor_id, question_id) {
//   var answer = $("#answerr_header_" + vendor_id + "_" + question_id)
//     .find("option:selected")
//     .val();

//   if (answer == "") {
//   } else {
//     $.ajax({
//       url: configs.routes.vendors_question_saveranswer,
//       type: "POST",
//       headers: {
//         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//       },
//       data: {
//         vendor_id: vendor_id,
//         question_id: question_id,
//         answer: answer,
//       },
//       beforeSend: function () {
//         $(this).text("Loading...");
//         $("#loading-image").show();
//       },
//       success: function (response) {
//         $(
//           "#answerr_header_" + vendor_id + "_" + question_id + " option:first"
//         ).prop("selected", true);
//         $("#loading-image").hide();
//         toastr["success"]("Answer Added successfully!!!", "success");
//       },
//     }).fail(function (response) {
//       $("#loading-image").hide();
//       toastr["error"](response.responseJSON.message);
//     });
//   }
// }

// $(document).on("click", ".ranswer-history-show-header-rqa", function () {
//   var vendor_id = $(this).attr("data-vendorid");
//   var question_id = $(this).attr("data-rqa_id");

//   $.ajax({
//     url: configs.routes.vendors_rquestion_getgetanswer,
//     type: "POST",
//     headers: {
//       "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//     },
//     data: {
//       vendor_id: vendor_id,
//       question_id: question_id,
//     },
//     success: function (response) {
//       if (response.status) {
//         var html = "";
//         $.each(response.data, function (k, v) {
//           html += `<tr>
//                                 <td> ${k + 1} </td>
//                                 <td> ${v.answer} </td>
//                                 <td> ${v.created_at} </td>
//                             </tr>`;
//         });
//         $("#vqar-answer-histories-list-header-rqa")
//           .find(".vqar-answer-histories-list-view-header-rqa")
//           .html(html);
//         $("#vqar-answer-histories-list-header-rqa").modal("show");
//       } else {
//         toastr["error"](response.error, "Message");
//       }
//     },
//   });
// });


$(document).on("click", ".status-history-show-header-qa", function () {
  var vendor_id = $(this).attr("data-id");
  var question_id = $(this).attr("data-qa_id");

  $.ajax({
    url: configs.routes.vendors_qastatus_histories,
    type: "POST",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      vendor_id: vendor_id,
      question_id: question_id,
    },
    success: function (response) {
      if (response.status) {
        $("#qa-status-histories-list-header-qa")
          .find(".qa-status-histories-list-view-header-qa")
          .html(response.html); // Insert the server-rendered HTML
        $("#qa-status-histories-list-header-qa").modal("show");
      } else {
        toastr["error"](response.error, "Message");
      }
    },
  });
});

function saveAnswerHeaderRQa(vendor_id, question_id) {
  var answer = $("#answerr_header_" + vendor_id + "_" + question_id)
    .find("option:selected")
    .val();

  if (answer == "") {
    
    toastr["error"]("Please select answer.", "error");
  } else {
    $.ajax({
      url: configs.routes.vendors_question_saveranswer,
      type: "POST",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      data: {
        vendor_id: vendor_id,
        question_id: question_id,
        answer: answer,
      },
      beforeSend: function () {
        $(this).text("Loading...");
        $("#loading-image").show();
      },
      success: function (response) {
        $(
          "#answerr_header_" + vendor_id + "_" + question_id + " option:first"
        ).prop("selected", true);
        $("#loading-image").hide();
        toastr["success"]("Answer Added successfully!!!", "success");
      },
    }).fail(function (response) {
      $("#loading-image").hide();
      toastr["error"](response.responseJSON.message);
    });
  }
}

$(document).on("click", ".ranswer-history-show-header-rqa", function () {
  var vendor_id = $(this).attr("data-vendorid");
  var question_id = $(this).attr("data-rqa_id");

  $.ajax({
    url: configs.routes.vendors_rquestion_getgetanswer,
    type: "POST",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      vendor_id: vendor_id,
      question_id: question_id,
    },
    success: function (response) {
      if (response.status) {
        var html = "";
        $.each(response.data, function (k, v) {
          html += `<tr>
                                <td> ${k + 1} </td>
                                <td> ${v.answer} </td>
                                <td> ${v.created_at} </td>
                            </tr>`;
        });
        $("#vqar-answer-histories-list-header-rqa")
          .find(".vqar-answer-histories-list-view-header-rqa")
          .html(html);
        $("#vqar-answer-histories-list-header-rqa").modal("show");
      } else {
        toastr["error"](response.error, "Message");
      }
    },
  });
});

$(document).on("change", ".status-dropdown-header-rqa", function (e) {
  e.preventDefault();
  var vendor_id = $(this).data("id");
  var question_id = $(this).data("rqa_id");
  var selectedStatus = $(this).val();

  // Make an AJAX request to update the status
  $.ajax({
    url: "/vendor/update-rqastatus",
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      vendor_id: vendor_id,
      question_id: question_id,
      selectedStatus: selectedStatus,
    },
    success: function (response) {
      toastr["success"]("Status  Created successfully!!!", "success");
    },
    error: function (xhr, status, error) {
      // Handle the error here
      console.error(error);
    },
  });
});

$(document).on("click", ".status-history-show-header-rqa", function () {
  var vendor_id = $(this).attr("data-id");
  var question_id = $(this).attr("data-rqa_id");

  $.ajax({
    url: configs.routes.vendors_rqastatus_histories,
    type: "POST",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      vendor_id: vendor_id,
      question_id: question_id,
    },
    success: function (response) {
      if (response.status) {
        $("#rqa-status-histories-list-header-rqa")
          .find(".rqa-status-histories-list-view-header-rqa")
          .html(response.html); // Insert the server-rendered HTML

        $("#rqa-status-histories-list-header-rqa").modal("show");
      } else {
        toastr["error"](response.error, "Message");
      }
    },
  });
});

function getTodoListHeader() {
  var keyword = $(".dev-todolist-table").val();
  var todolist_start_date = $("#todolist_start_date").val();
  var todolist_end_date = $("#todolist_end_date").val();

  $.ajax({
    url: configs.routes.todolist_module_search,
    type: "GET",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      keyword: keyword,
      todolist_start_date: todolist_start_date,
      todolist_end_date: todolist_end_date,
    },
    // dataType: 'json',
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (response) {
      $("#loading-image").hide();
      $(".show-search-todolist-list").html(response);
    },
    error: function () {
      $("#loading-image").hide();
      toastr["Error"]("An error occured!");
    },
  });
}

$(document).on("click", ".btn-todolist-search-menu", function (e) {
  getTodoListHeader();
});

$(document).on("click", ".menu-preview-img-btn", function (e) {
  e.preventDefault();
  id = $(this).data("id");
  if (!id) {
    toastr["error"]("No data found", "Error");
    return;
  }
  $.ajax({
    url: "/task/preview-img/" + id,
    type: "GET",
    success: function (response) {
      $("#modal-container").load("/menu-preview-task-image", function () {
        $("#menu-preview-task-image").modal('show');
      });
      $(".menu-task-image-list-view").html(response);
      initialize_select2();
    },
    error: function () { },
  });
});

$("#add-vochuer").on("click", function (e) {
  e.preventDefault();
  if ($("#addvoucherModel").data('loaded')) {
    $("#addvoucherModel").modal('show');
  } else {
    $.ajax({
      url: configs.routes.add_voucher_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        $("#dynamic-model-section").append(response.html);
        $("#addvoucherModel").data('loaded', true);
        $('.globalSelect2').select2({
          ajax: {
            url: function (params) {
              return $(this).data('ajax');
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                q: params.term // search term
              };
            },
            processResults: function (data) {
              return {
                results: data.items
              };
            },
            cache: true
          },
          placeholder: $(this).data('placeholder')
        });
        $("#loading-image-preview").hide();
        $("#addvoucherModel").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(document).on("click", ".permission-grant", function (e) {
  e.preventDefault();
  var permission = $(this).data("id");
  var user = $(this).data("user");
  var type = $(this).data("type");

  $.ajax({
    url: "/user-management/modifiy-permission",
    type: "POST",
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
      permission: permission,
      user: user,
      type: type,
    },
    dataType: "json",
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (result) {
      $("#loading-image").hide();
      if (result.code == 200) {
        toastr["success"](result.data, "");
        setPermissionRequests();
      } else {
        toastr["error"](result.data, "");
      }
    },
    error: function () {
      $("#loading-image").hide();
    },
  });
});
$(".search-password").on("click", function (e) {
  e.preventDefault();
  if ($("#searchPassswordModal").data('loaded')) {
    $("#searchPassswordModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.search_password_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#searchPassswordModal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#searchPassswordModal").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});


$(document).on("click", ".permission-delete-grant", function (e) {
  e.preventDefault();
  $.ajax({
    url: "/user-management/request-delete",
    type: "POST",
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
    },
    dataType: "json",
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (result) {
      $("#loading-image").hide();
      if (result.code == 200) {
        $("#permission-request").find(".show-list-records").html("");
        toastr["success"](result.data, "");
      } else {
        toastr["error"](result.data, "");
      }
    },
    error: function () {
      $("#loading-image").hide();
    },
  });
});
$("#id_label_task").select2({
  minimumInputLength: 3, // only start searching when the user has input 3 or more characters
});
$("#search_task").select2({
  minimumInputLength: 3, // only start searching when the user has input 3 or more characters
});
$("#id_label_multiple_user_read").select2();
$("#id_label_multiple_user_write").select2();
$("#search_user").select2();

$(document).on("click", ".filepermissionupdate", function (e) {
  $("#updateGoogleFilePermissionModal #id_label_file_permission_read")
    .val("")
    .trigger("change");
  $("#updateGoogleFilePermissionModal #id_label_file_permission_write")
    .val("")
    .trigger("change");

  let data_read = $(this).data("readpermission");
  let data_write = $(this).data("writepermission");
  var file_id = $(this).data("fileid");
  var id = $(this).data("id");
  var permission_read = data_read.split(",");
  var permission_write = data_write.split(",");
  if (permission_read) {
    $("#updateGoogleFilePermissionModal #id_label_file_permission_read")
      .val(permission_read)
      .trigger("change");
  }
  if (permission_write) {
    $("#updateGoogleFilePermissionModal #id_label_file_permission_write")
      .val(permission_write)
      .trigger("change");
  }

  $("#file_id").val(file_id);
  $("#id").val(id);
});

$(document).on("click", ".showFullMessage", function () {
  let title = $(this).data("title");
  let message = $(this).data("message");

  $("#showFullMessageModel .modal-body").html(message);
  $("#showFullMessageModel .modal-title").html(title);
  $("#showFullMessageModel").modal("show");
});

$(document).on("click", ".filedetailupdate", function (e) {
  e.preventDefault();
  let id = $(this).data("id");
  let fileid = $(this).data("fileid");
  let fileremark = $(this).data("file_remark");
  let filename = $(this).data("file_name");

  $("#updateUploadedFileDetailModal .id").val(id);
  $("#updateUploadedFileDetailModal .file_id").val(fileid);
  $("#updateUploadedFileDetailModal .file_remark").val(fileremark);
  $("#updateUploadedFileDetailModal .file_name").val(filename);
});

function todoHomeStatusChange(id, xvla) {
  $.ajax({
    type: "POST",
    url: configs.routes.todolist_status_update,
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
      id: id,
      status: xvla,
    },
    dataType: "json",
    success: function (message) {
      $c = message.length;
      if ($c == 0) {
        toastr["error"]("No History Exist", "Error");
      } else {
        toastr["success"](message.message, "success");
      }
    },
    error: function (error) {
      toastr["error"](error, "error");
    },
  });
}

// function estimateFunTaskDetailHandler(elm) {
//   let tasktype = $(elm).data("task");
//   let taskid = $(elm).data("id");
//   if (tasktype == "DEVTASK") {
//     estimatefunTaskInformationModal(elm, taskid, tasktype);
//   } else {
//     estimatefunTaskInformationModal(elm, taskid, tasktype);
//   }
// }
$(document).on('click', '.estimate-history', function () {
  let tasktype = $(this).data("task");
  let taskid = $(this).data("id");

  if (tasktype == "DEVTASK") {
    estimatefunTaskInformationModal(this, taskid, tasktype);
  } else {
    estimatefunTaskInformationModal(this, taskid, tasktype);
  }
});

$(document).on("submit", "#magento-command-date-form", function (event) {
  event.preventDefault();
  var $form = $(this).closest("form");
  $.ajax({
    type: "GET",
    url: configs.routes.magento_getMagentoCommand,
    data: $form.serialize(),
  })
    .done(function (response) {
      $(".ajax-loader").hide();
      $("#magento-commands-modal-html").empty().html(response.html);

      $("#magento-commands-modal").modal("show");
    })
    .fail(function (response) {
      $(".ajax-loader").hide();
    });
});

$(document).on("click", ".list-code-shortcut-title-view", function () {
  id = $(this).data("id");
  type = $(this).data("type");
  $.ajax({
    method: "GET",
    url: configs.routes.code_get_Shortcut_data + "/" + id,
    dataType: "json",
    success: function (response) {
      if (type == "title") {
        showListCodeShortcutTitleModal("Title",response.title);
      } else if (type == "code") {
        showListCodeShortcutTitleModal("Code",response.code);
      } else if (type == "description") {
        showListCodeShortcutTitleModal("Description",response.description);
      } else if (type == "solution") {
        showListCodeShortcutTitleModal("Solution",response.solution);
      }
    },
  });
});

function showListCodeShortcutTitleModal(type,data_html) {
  e.preventDefault();
  if ($("#list-code-shortcode-title-list-header").data('loaded')) {
    $("#list-code-shortcode-title-list-header").find(".modal-title").html(type);
    $("#list-code-shortcode-title-list-header").find(".list-code-shortcode-title-header-view").html(data_html);
    $("#list-code-shortcode-title-list-header").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_list_code_shortcut_title_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#list-code-shortcode-title-list-header").data('loaded', true);

        $("#list-code-shortcode-title-list-header").find(".modal-title").html(type);
        $("#list-code-shortcode-title-list-header").find(".list-code-shortcode-title-header-view").html(data_html);
        $("#list-code-shortcode-title-list-header").modal('show');
      }
    });
  }
}

$(document).ready(function () {
  $("#availabilityToggle").change(function () {
    $("#availabilityText").removeClass("textLeft");
    $("#availabilityText").removeClass("textRight");

    var isChecked = $(this).prop("checked");
    var availabilityText = isChecked ? "Online" : "Offline";
    var alignmentText = isChecked ? "textLeft" : "textRight";

    // Update the text content within the toggle switch
    $("#availabilityText").text(availabilityText);
    var availabilityTextNew = isChecked ? "On" : "Off";
    $("#availabilityText").addClass(availabilityTextNew);

    $.ajax({
      type: "POST",
      url: configs.routes.useronlinestatus_status_update,
      beforeSend: function () {
        $("#loading-image-modal").show();
      },
      data: {
        _token: $('meta[name="csrf-token"]').attr("content"),
        is_online_flag: availabilityText,
      },
      dataType: "json",
    })
      .done(function (response) {
        $("#loading-image-modal").hide();
        toastr["success"](response.message, "success");
      })
      .fail(function (response) {
        $("#loading-image-modal").hide();
      });
  });
});

$(".quick-appointment-request").on("click", function (e) {
  e.preventDefault();
  if ($("#quickRequestZoomModal").data('loaded')) {
    $("#quickRequestZoomModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.quick_appointment_request_model,
      type: 'GET',
      beforeSend: function () {
        $("#loading-image-preview").show();
      },
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
          $("#loading-image-preview").hide();
        }
        $("#dynamic-model-section").append(response.html);
        $("#quickRequestZoomModal").data('loaded', true);
        $("#loading-image-preview").hide();
        $("#quickRequestZoomModal").modal('show');
      },
      error: function () {
        $("#loading-image-preview").hide();
      }
    });
  }
});

$(document).on("click", ".send-ap-quick-request", function (event) {
  if ($("#requested_ap_user_id").val() == "") {
    toastr["error"]("Please select user", "Error");
    return false;
  }

  if ($("#requested_ap_remarks").val() == "") {
    toastr["error"]("Please enter remarks", "Error");
    return false;
  }

  var currentDate = moment(); // Current date and time
  var dateAfterOneHour = moment(currentDate).add(1, "hours");

  $.ajax({
    type: "POST",
    url: configs.routes.event_sendAppointmentRequest,
    beforeSend: function () {
      $("#loading-image-modal").show();
    },
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
      requested_user_id: $("#requested_ap_user_id").val(),
      requested_time: moment(currentDate).format("YYYY-MM-DD HH:mm:ss"),
      requested_time_end: moment(dateAfterOneHour).format(
        "YYYY-MM-DD HH:mm:ss"
      ),
      requested_remarks: $("#requested_ap_remarks").val(),
    },
    dataType: "json",
  })
    .done(function (response) {
      $("#loading-image-modal").hide();
      if (response.code == 200) {
        toastr["success"](response.message, "success");
      }

      setTimeout(function () {
        location.reload();
      }, 60000);
    })
    .fail(function (response) {
      $("#loading-image-modal").hide();
      toastr["error"](response.message, "error");
    });
});

$(document).ready(function () {
  $("#translationToggle").change(function () {
    $("#translationText").removeClass("textLeft");
    $("#translationText").removeClass("textRight");

    var isChecked = $(this).prop("checked");
    var translationText = isChecked ? "Free" : "Paid";
    var alignmentText = isChecked ? "textLeft" : "textRight";

    // Update the text content within the toggle switch
    $("#translationText").text(translationText);
    $("#translationText").addClass(alignmentText);

    $.ajax({
      type: "POST",
      url: configs.routes.google_translate_plan,
      data: {
        _token: $('meta[name="csrf-token"]').attr("content"),
        is_free: translationText,
      },
      dataType: "json",
    })
      .done(function (response) {
        toastr["success"](response.message, "success");
      })
      .fail(function (response) {
        toastr["success"](response.message, "error");
      });
  });
});

$(".select-multiple-s").select2({
  tags: true,
});

function loadEmailSearchModal() {
  $.ajax({
    url: configs.routes.menu_email_searchmodal,
    type: "GET",
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
    },
    dataType: "json",
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (result) {
      $("#loading-image").hide();
      $("#modal-container").load("/menu-email-search", function () {
        $("#menu-email-search").empty().html(result.html);
        $("#menu-email-search").modal("show");
      });
    },
    error: function () {
      $("#loading-image").hide();
      toastr["Error"]("An error occured!");
    },
  });
}

function loadSopSearchModal() {
  $.ajax({
    url: configs.routes.menu_sop_searchmodal,
    type: "GET",
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
    },
    dataType: "json",
    beforeSend: function () {
      $("#loading-image").show();
    },
    success: function (result) {
      $("#loading-image").hide();
      $("#modal-container").load("/menu-sop-search-model", function () {
        $("#menu-sop-search-model").empty().html(result.html);
        $("#menu-sop-search-model").modal("show");
      });

    },
    error: function () {
      $("#loading-image").hide();
      toastr["Error"]("An error occured!");
    },
  });
}

function loadSopCategoryList() {
  $.ajax({
    url: configs.routes.sop_categorylistajax,
    type: "GET",
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (result) {
      $("#loading-image").hide();
      $("#sop_edit_category").empty().html(result.data);
    },
    error: function () {
      toastr["Error"]("An error occured!");
    },
  });
}

function showContactModal() {
  if ($("#createQuickContactModal").data('loaded')) {
    $("#createQuickContactModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_contact_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#createQuickContactModal").data('loaded', true);
        $("#createQuickContactModal").modal('show');
      }
    });
  }
}
function showTaskCategorytModal() {
  if ($("#createTaskCategorytModal").data('loaded')) {
    $("#createTaskCategorytModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_task_category_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#createTaskCategorytModal").data('loaded', true);
        $("#createTaskCategorytModal").modal('show');
      }
    });
  }
}
function showTaskViewModal() {
  if ($("#taskViewModal").data('loaded')) {
    $("#taskViewModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_task_view_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#taskViewModal").data('loaded', true);
        $("#taskViewModal").modal('show');
      }
    });
  }
}
function showWhatsappMessageModal() {
  if ($("#whatsAppMessageModal").data('loaded')) {
    $("#whatsAppMessageModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_whatsapp_group_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#whatsAppMessageModal").data('loaded', true);
        $("#whatsAppMessageModal").modal('show');
      }
    });
  }
}
function showTaskReminderModal() {
  if ($("#taskReminderModal").data('loaded')) {
    $("#taskReminderModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_task_reminder_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#taskReminderModal").data('loaded', true);
        $("#taskReminderModal").modal('show');
      }
    });
  }
}
function showConfirmMessageModal() {
  if ($("#confirmMessageModal").data('loaded')) {
    $("#confirmMessageModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_confirm_message_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#confirmMessageModal").data('loaded', true);
        $("#confirmMessageModal").modal('show');
      }
    });
  }
}
function showCsvExportModal() {
  if ($("#confirmMessageModal").data('loaded')) {
    $("#confirmMessageModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_csv_export_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#confirmMessageModal").data('loaded', true);
        $("#confirmMessageModal").modal('show');
      }
    });
  }
}
function showReminderMessageModal() {
  if ($("#reminderMessageModal").data('loaded')) {
    $("#reminderMessageModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_reminder_message_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#reminderMessageModal").data('loaded', true);
        $("#reminderMessageModal").modal('show');
      }
    });
  }
}
function showTaskStatusModal() {
  if ($("#taskStatusModal").data('loaded')) {
    $("#taskStatusModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_task_status_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#taskStatusModal").data('loaded', true);
        $("#taskStatusModal").modal('show');
      }
    });
  }
}
function showTimeTrackedModal() {
  if ($("#time_tracked_modal").data('loaded')) {
    $("#time_tracked_modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_tracked_time_history_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#time_tracked_modal").data('loaded', true);
        $("#time_tracked_modal").modal('show');
      }
    });
  }
}
function showTimerHistoryModal() {
  if ($("#timer_tracked_modal").data('loaded')) {
    $("#timer_tracked_modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_timer_history_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#timer_tracked_modal").data('loaded', true);
        $("#timer_tracked_modal").modal('show');
      }
    });
  }
}

function showUserHistoryModal() {
  if ($("#user_history_modal").data('loaded')) {
    $("#user_history_modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_user_history_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#user_history_modal").data('loaded', true);
        $("#user_history_modal").modal('show');
      }
    });
  }
}
function showColumnVisibilityModal() {
  if ($("#taskcolumnvisibilityList").data('loaded')) {
    $("#taskcolumnvisibilityList").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_column_visibility_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#taskcolumnvisibilityList").data('loaded', true);
        $("#taskcolumnvisibilityList").modal('show');
      }
    });
  }
}
function showStatusColourModal() {
  if ($("#newStatusColor").data('loaded')) {
    $("#newStatusColor").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_status_colour_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#newStatusColor").data('loaded', true);
        $("#newStatusColor").modal('show');
      }
    });
  }
}
function showPriorityModal() {
  if ($("#priority_model").data('loaded')) {
    $("#priority_model").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_priority_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#priority_model").data('loaded', true);
        $("#priority_model").modal('show');
      }
    });
  }
}
function showAllTaskCategoryModal() {
  if ($("#allTaskCategoryModal").data('loaded')) {
    $("#allTaskCategoryModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_all_task_category_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#allTaskCategoryModal").data('loaded', true);
        $("#allTaskCategoryModal").modal('show');
      }
    });
  }
}
function showChatListHistoryModal() {
  if ($("#chat-list-history").data('loaded')) {
    $("#chat-list-history").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_chat_list_history_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#chat-list-history").data('loaded', true);
        $("#chat-list-history").modal('show');
      }
    });
  }
}
function showCreateTaskModal() {
  if ($("#create-task-modal").data('loaded')) {
    $("#create-task-modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_create_task_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#create-task-modal").data('loaded', true);
        $("#create-task-modal").modal('show');
      }
    });
  }
}
function showChatListHistoryModal() {
  if ($("#chat-list-history").data('loaded')) {
    $("#chat-list-history").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_chat_list_history_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#chat-list-history").data('loaded', true);
        $("#chat-list-history").modal('show');
      }
    });
  }
}
function showPreviewTaskImageModal() {
  if ($("#preview-task-image").data('loaded')) {
    $("#preview-task-image").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_preview_task_image_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#preview-task-image").data('loaded', true);
        $("#preview-task-image").modal('show');
      }
    });
  }
}
function showPreviewTaskCreateModal() {
  if ($("#preview-task-create-get-modal").data('loaded')) {
    $("#preview-task-create-get-modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_preview_task_create_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#preview-task-create-get-modal").data('loaded', true);
        $("#preview-task-create-get-modal").modal('show');
      }
    });
  }
}
function showFileUploadAreaSectionModal() {
  if ($("#file-upload-area-section").data('loaded')) {
    $("#file-upload-area-section").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_file_upload_area_section_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#file-upload-area-section").data('loaded', true);
        $("#file-upload-area-section").modal('show');
      }
    });
  }
}
function showSendMessageTextBoxModal() {
  if ($("#send-message-text-box").data('loaded')) {
    $("#send-message-text-box").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_send_message_text_box_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#send-message-text-box").data('loaded', true);
        $("#send-message-text-box").modal('show');
      }
    });
  }
}
function showPreviewDocumentModal() {
  if ($("#previewDoc").data('loaded')) {
    $("#previewDoc").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_preview_document_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#previewDoc").data('loaded', true);
        $("#previewDoc").modal('show');
      }
    });
  }
}
function showRecurringHistoryModal() {
  if ($("#recurring-history-modal").data('loaded')) {
    $("#recurring-history-modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_recurring_history_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#recurring-history-modal").data('loaded', true);
        $("#recurring-history-modal").modal('show');
      }
    });
  }
}
function showTaskCreateLogListingModal() {
  if ($("#task-create-log-listing").data('loaded')) {
    $("#task-create-log-listing").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_task_create_log_listing_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#task-create-log-listing").data('loaded', true);
        $("#task-create-log-listing").modal('show');
      }
    });
  }
}
function showCreatedTaskModal() {
  if ($("#create-d-task-modal").data('loaded')) {
    $("#create-d-task-modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_created_task_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#create-d-task-modal").data('loaded', true);
        $("#create-d-task-modal").modal('show');
      }
    });
  }
}
function showTaskGoogleDocModal() {
  if ($("#taskGoogleDocModal").data('loaded')) {
    $("#taskGoogleDocModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_task_google_doc_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#taskGoogleDocModal").data('loaded', true);
        $("#taskGoogleDocModal").modal('show');
      }
    });
  }
}
function showTaskGoogleDocListModal() {
  if ($("#taskGoogleDocListModal").data('loaded')) {
    $("#taskGoogleDocListModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_task_google_doc_list_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#taskGoogleDocListModal").data('loaded', true);
        $("#taskGoogleDocListModal").modal('show');
      }
    });
  }
}
function showUploadeTaskFileModal() {
  if ($("#uploadeTaskFileModal").data('loaded')) {
    $("#uploadeTaskFileModal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_uploade_task_file_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#uploadeTaskFileModal").data('loaded', true);
        $("#uploadeTaskFileModal").modal('show');
      }
    });
  }
}
function showDisplayTaskFileUploadModal() {
  if ($("#displayTaskFileUpload").data('loaded')) {
    $("#displayTaskFileUpload").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_display_task_file_upload_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#displayTaskFileUpload").data('loaded', true);
        $("#displayTaskFileUpload").modal('show');
      }
    });
  }
}
function showRecordVoiceNotesModal() {
  if ($("#record-voice-notes").data('loaded')) {
    $("#record-voice-notes").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_record_voice_notes_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#record-voice-notes").data('loaded', true);
        $("#record-voice-notes").modal('show');
      }
    });
  }
}
function showStatusQuickHistoryModal() {
  if ($("#status_quick_history_modal").data('loaded')) {
    $("#status_quick_history_modal").modal('show');
  } else {
    $.ajax({
      url: configs.routes.load_status_quick_history_model,
      type: 'GET',
      success: function (response) {
        if (response.code === 400) {
          toastr["error"]("Opps! Something went wrong, Pease try again.", "Error");
        }
        $("#dynamic-model-section").append(response.html);
        $("#status_quick_history_modal").data('loaded', true);
        $("#status_quick_history_modal").modal('show');
      }
    });
  }
}

$(document).ready(function () {
  $("#sop_edit_category").select2({
    width: "100%",
    multiple: true,
    placeholder: "Select sop category",
    tags: true,
    ajax: loadSopCategoryList(),
  });
});

function loadVendorRatingQandAModal(vendorModalType) {
  if (vendorModalType == "rqa") {
    $.ajax({
      url: configs.routes.vendors_rqa_modal,
      type: "GET",
      data: {
        _token: $('meta[name="csrf-token"]').attr("content"),
        vtype: vendorModalType,
      }, beforeSend: function () {
        $("#loading-image").show();
      }, success: function (result) {
        $("#loading-image").hide();
        $("#vendor-rqa-header-model").empty().html(result.html);
        $("#vendor-rqa-header-model").modal("show");
      }, error: function () {
        toastr["Error"]("An error occured!");
      },
    });
  } else if (vendorModalType == "qa") {
    $.ajax({
      url: configs.routes.vendors_rqa_modal,
      type: "GET",
      data: {
        _token: $('meta[name="csrf-token"]').attr("content"),
        vtype: vendorModalType,
      }, beforeSend: function () {
        $("#loading-image").show();
      }, success: function (result) {
        $("#loading-image").hide();
        $("#vendor-qa-header-model").empty().html(result.html);
        $("#vendor-qa-header-model").modal("show");
      }, error: function () {
        toastr["Error"]("An error occured!");
      },
    });
  } else if (vendorModalType == "fw") {
    $.ajax({
      url: configs.routes.vendors_rqa_modal,
      type: "GET",
      data: {
        _token: $('meta[name="csrf-token"]').attr("content"),
        vtype: vendorModalType,
      }, beforeSend: function () {
        $("#loading-image").show();
      }, success: function (result) {
        $("#loading-image").hide();
        $("#vendor-flowchart-header-model").empty().html(result.html);
        $("#vendor-flowchart-header-model").modal("show");
      }, error: function () {
        toastr["Error"]("An error occured!");
      },
    });
  }
}
