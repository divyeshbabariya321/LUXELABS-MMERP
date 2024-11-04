var spinner_html = `<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...`;

// String Limit set
function setStringLength(string_value, length = 15) {
  return string_value.length > length
    ? string_value.substring(0, length) + "..."
    : string_value;
}



// get Date in formatting
function getDateByFormat(date) {
  const d = new Date(date);
  const ye = new Intl.DateTimeFormat("en", {
    year: "2-digit",
  }).format(d);
  const mo = new Intl.DateTimeFormat("en", {
    // month: 'short'
    month: "2-digit",
  }).format(d);
  const da = new Intl.DateTimeFormat("en", {
    day: "2-digit",
  }).format(d);
  return `${da}/${mo}/${ye}`;
}

// get DateTime in formatting
function getDateTimeByFormat(date) {
  const d = new Date(date);
  const ye = new Intl.DateTimeFormat("en", {
    year: "numeric",
  }).format(d);
  const mo = new Intl.DateTimeFormat("en", {
    month: "short",
  }).format(d);
  const da = new Intl.DateTimeFormat("en", {
    day: "2-digit",
  }).format(d);
  // const h = new Intl.DateTimeFormat('en', {
  //     hour: '2-digit'
  // }).format(d);
  // const i = new Intl.DateTimeFormat('en', {
  //     minute: '2-digit'
  // }).format(d);
  // return `${da} ${mo} ${ye} ${h}:${i}`;
  return `${da} ${mo} ${ye}`;
}

// Show details page button
function actionShowButton(url) {
  return `<a href="${url}" title="Details Page" class="btn btn-image padding-10-3"><img src="/images/view.png" /></a>`;
}

// Show details page button
function actionShowButtonWithTitle(url, title) {
  return `<a href="${url}" title="${title}" class="btn btn-image padding-10-3"><i class="fa fa-file-text" aria-hidden="true"></i></a>`;
}

// Show details page button
function actionShowButtonWithClass(cls, id) {
  return `<button type="button" title="Details Page"  class="btn btn-image padding-10-3 ${cls}" data-id="${id}"><img src="/images/view.png" /></button>`;
}

// Edit Button
function actionEditButtonWithClass(cls, data) {
  return `<button type='button' title='Edit'  class='btn btn-image padding-10-3 ${cls}' data-row='${data}'><img src='/images/edit.png' /></button>`;
}

// Title or String persent to better way
function actionShowTitle(url, stringTitle) {
  return (
    `<a class="btn btn-sm btn-clean" href="` +
    url +
    `" title="` +
    stringTitle +
    `">` +
    stringTitle +
    `</a>`
  );
}

// delete button
function actionDeleteButton(id, deleteclass = "clsdelete") {
  return `<a  class="btn btn-image padding-10-3 ${deleteclass}" data-id="${id}"><img src="/images/delete.png" /></a>`;
}

// status Button
function actionActiveButton(data, attr, statusclass = "clsstatus") {
  // return parseInt(data) ? "<span class=\"badge badge-success cursor-pointer "+statusclass+" \" "+ attr +" >"+"{{ trans_choice('content.active_title', 1) }}"+"</span>" : "<span class=\"badge badge-danger cursor-pointer  "+statusclass+" \" "+ attr +">"+"Deactivate"+"</span>";
  if (data == 1) {
    return `<div class="badge badge-light-success fw-bolder ${statusclass}" ${attr}>{{ trans_choice('content.active_title', 1) }}</div>`;
  } else {
    return `<div class="badge badge-light-danger fw-bolder ${statusclass}" ${attr}>{{ trans_choice('content.inactive_title', 1) }}</div>`;
  }
}

// Ajax For delete row
function tableDeleteRow(url, oTable) {
  Swal.fire({
    title: "Are you sure?",
    text: "You want be able to delete this!",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
    showLoaderOnConfirm: true,
    preConfirm: function () {
      return new Promise(function (resolve) {
        $.ajax({
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
          url: url,
          type: "DELETE",
          dataType: "json",
        })
          .done(function (response) {
            oTable.draw();
            Swal.fire("Deleted!", response.message, "success");
          })
          .fail(function (response) {
            
            
            Swal.fire("Oops...", "Something went wrong with ajax !", "error");
          });
      });
    },
    allowOutsideClick: false,
  });
}

// Ajax update status
function tableChnageStatus(
  url,
  oTable,
  message = "You will be able to revert this"
) {
  Swal.fire({
    title: "Are you sure?",
    text: message,
    type: "info",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
    showLoaderOnConfirm: true,
    preConfirm: function () {
      return new Promise(function (resolve) {
        $.ajax({
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
          url: url,
          type: "GET",
          dataType: "json",
        })
          .done(function (response) {
            if (response.status == 1) {
              oTable.draw();
              Swal.fire("Updated!", response.message, "success");
            } else {
              Swal.fire("Info!", response.message, "info");
            }
          })
          .fail(function () {
            Swal.fire("Oops...", "Something went wrong with ajax !", "error");
          });
      });
    },
    allowOutsideClick: false,
  });
}

//Errors print
function customFnErrors(form, element_errors) {
  form.find(".invalid-feedback").remove();
  $.each(element_errors, function (key, element_error) {
    var html =
      `<div id="` + key + `-error" class="error invalid-feedback d-block">`;
    $.each(element_error, function (index, error) {
      html = html + error + `<br>`;
    });
    html = html + `</div>`;
    
    

    if (key.indexOf(".") != -1) {
      var arr = key.split(".");
      var selector = "[name='" + arr[0];
      for (var i = 1; i < arr.length; i++) {
        selector = selector + "[" + arr[i] + "]";
      }
      selector = selector + "']";
      
      form.find(selector).closest(".form-group").append(html);
    } else {
      if (form.find('[name="' + key + '"]').length) {
        form
          .find('[name="' + key + '"]')
          .closest(".form-group")
          .append(html);
      } else if (form.find('[name="' + key + '[]"]').length) {
        form
          .find('[name="' + key + '[]"]')
          .closest(".form-group")
          .append(html);
      }
    }
  });
}

$(document).ready(function () {
  setTimeout(function () {
    if ($("#ns").length > 0) {
      $("#ns").remove();
    }
  }, 5000);
});


$(document).ready(function () {
    $('#category_id_dropdown').on('change', function () {
        var categoryId = $(this).val();
        if (categoryId) {
            $.ajax({
                url: '/get-subcategories',
                type: 'GET',
                data: { category_id: categoryId },
                success: function (data) {
                    $('#subcategory').empty();
                    $.each(data, function (key, value) {
                        $('#subcategory').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('#subcategory').empty();
        }
    });
});

// EXPOSE ONLY GLOBALLY ACCESS METHODS
window.setStringLength = setStringLength
window.customFnErrors = customFnErrors
window.tableChnageStatus = tableChnageStatus
window.tableDeleteRow = tableDeleteRow
window.actionActiveButton = actionActiveButton
window.actionDeleteButton = actionDeleteButton
window.actionShowTitle = actionShowTitle
window.actionEditButtonWithClass = actionEditButtonWithClass
window.actionShowButtonWithClass = actionShowButtonWithClass
window.actionShowButtonWithTitle = actionShowButtonWithTitle
window.actionShowButton = actionShowButton
window.getDateTimeByFormat = getDateTimeByFormat
window.getDateByFormat = getDateByFormat