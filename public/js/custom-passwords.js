$(document).ready(function () {
  $(".select-multiple").multiselect();
  $(".select-multiple2").select2();
});

$("#filter-date").datetimepicker({
  format: "YYYY-MM-DD",
});

// $('.date').change(function(){
//     alert('date selected');
// });

function changePassword(password_id) {
  event.preventDefault();
  $("#passwordsId").val(password_id);
  $.ajax({
    type: "POST",
    url: showPasswordEdit,
    data: { password_id: password_id },
    headers: {
      "X-CSRF-TOKEN": jQuery('meta[name="csrf-token"]').attr("content"),
    },
    dataType: "json",
    success: function (response) {
      $("#pass-website").val(response.data.website);
      $("#pass-url").val(response.data.url);
      $("#pass-username").val(response.data.username);
      $("#pass-password").val(response.pass);
      $("#pass-registered_with").val(response.data.registered_with);
      $("#passwordEditModal").modal("show");
    },
    error: function () {},
  });
}
$(".check").change(function () {
  if (this.checked) {
    $(".users").show();
  } else {
    $(".users").hide();
  }
});

function getData(password_id) {
  event.preventDefault();
  $.ajax({
    type: "POST",
    url: passHistory,
    data: { password_id: password_id },
    headers: {
      "X-CSRF-TOKEN": jQuery('meta[name="csrf-token"]').attr("content"),
    },
    dataType: "json",
    success: function (message) {
      $c = message.length;
      if ($c == 0) {
        toastr["error"]("No History Exist", "error");
      } else {
        var detials = "";
        $.each(message, function (key, value) {
          detials +=
            "<tr><th>" +
            value.website +
            "</th><th>" +
            value.username +
            "</th><th>" +
            value.password_decrypt +
            "</th><th>" +
            value.registered_with +
            "</th><tr>";
        });
        
        $("#data").html(detials);
        $("#getHistory").modal("show");
      }
    },
    error: function () {},
  });
}

$(".checkbox_ch").change(function () {
  var values = $('input[name="userIds[]"]:checked')
    .map(function () {
      return $(this).val();
    })
    .get();
  $("#userIds").val(values);
});

function sendtoWhatsapp(password_id) {
  $("#sendToWhatsapp").modal("show");
  $("#passwordId").val(password_id);
}

$(document).on("click", ".btn-copy-password", function () {
  var password = $(this).data("value");

  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val(password).select();
  document.execCommand("copy");
  $temp.remove();

  
  toastr["success"]("Copied!", "success");
});
$(document).on("click", ".btn-copy-username", function () {
  var password = $(this).data("value");

  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val(password).select();
  document.execCommand("copy");
  $temp.remove();

  toastr["success"]("Copied!", "success");
});

$(document).on("click", ".set-remark", function (e) {
  e.preventDefault();
  $(".remark_pop").val("");
  var password_id = $(this).data("password_id");
  $(".sub_remark").attr("data-password_id", password_id);
});

$(document).on("click", ".set-remark, .sub_remark", function (e) {
  e.preventDefault();
  var thiss = $(this);
  var password_id = $(this).data("password_id");
  var remark = $(".remark_pop").val();
  $.ajax({
    type: "POST",
    url: passGetRemark,
    headers: {
      "X-CSRF-TOKEN": jQuery('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      password_id: password_id,
      remark: remark,
      type: "Quick-dev-task",
    },
    beforeSend: function () {
      $("#loading-image").show();
    },
  })
    .done(function (response) {
      if (response.code == 200) {
        $("#loading-image").hide();
        if (remark == "") {
          $("#preview-task-create-get-modal").modal("show");
        }
        $(".task-create-get-list-view").html(response.data);
        $(".td-password-remark").html(response.remark_data);
        $(".remark_pop").val("");
        toastr["success"](response.message);
      } else {
        $("#loading-image").hide();
        if (remark == "") {
          $("#preview-task-create-get-modal").modal("show");
        }
        $(".task-create-get-list-view").html("");
        toastr["error"](response.message);
      }
    })
    .fail(function (response) {
      $("#loading-image").hide();
      $("#preview-task-create-get-modal").modal("show");
      $(".task-create-get-list-view").html("");
      toastr["error"](response.message);
    });
});

$(document).on("click", ".copy_remark", function (e) {
  var thiss = $(this);
  var remark_text = thiss.data("remark_text");
  copyToClipboard(remark_text);
  /* Alert the copied text */
  toastr["success"]("Copied the text: " + remark_text);
});

function copyToClipboard(text) {
  var sampleTextarea = document.createElement("textarea");
  document.body.appendChild(sampleTextarea);
  sampleTextarea.value = text; //save main text in it
  sampleTextarea.select(); //select textarea contenrs
  document.execCommand("copy");
  document.body.removeChild(sampleTextarea);
}
