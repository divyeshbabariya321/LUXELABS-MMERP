function siteLoader(status) {
  if (jQuery("#loading-image").length) {
    if (status) {
      jQuery("#loading-image").show();
    } else {
      jQuery("#loading-image").hide();
    }
  }
}
function siteAlert(status, message) {
  if (status && message) {
    toastr["success"](message);
  } else if (message) {
    toastr["error"](message);
  }
}
function siteSuccessAlert(res) {
  if (res.message != undefined) {
    siteAlert(1, res.message);
  } else if (message) {
    siteAlert(1, message);
  }
}
function siteErrorAlert(err) {
  if (err.responseJSON != undefined) {
    siteAlert(0, err.responseJSON.message);
  } else if (err.message != undefined) {
    siteAlert(0, err.message);
  } else if (err.msg != undefined) {
    siteAlert(0, err.msg);
  } else if (err) {
    siteAlert(0, err);
  } else {
    siteAlert(0, "Unknown error occured.");
  }
}

function applySelect2(eles) {
  if (eles.length) {
    eles.each(function () {
      if (jQuery(this).hasClass("select2-hidden-accessible")) {
        jQuery(this).select2("destroy");
      }

      jQuery(this)
        .select2({
          width: "100%",
          placeholder: "Please Select",
          allowClear: true,
          // dropdownAutoWidth: true,
          // dropdownParent: jQuery(this).parent()
        })
        .change(function () {
          // jQuery(this).valid();
        });
    });
  }
}
function applyDatePicker(eles) {
  if (eles.length) {
    eles.datetimepicker({
      format: "YYYY-MM-DD",
    });
  }
}
function applyDateTimePicker(eles) {
  if (eles.length) {
    eles.datetimepicker({
      format: "YYYY-MM-DD HH:mm:ss",
      sideBySide: true,
    });
  }
}
function applyTimePicker(eles) {
  if (eles.length) {
    eles.datetimepicker({
      format: "HH:mm:ss",
    });
  }
}

// DATATABLE
function siteDatatableRefresh(tableId) {
  jQuery(tableId).DataTable().draw(false);
}
function siteDatatableMergeSearch(d, formId) {
  var extra = {};
  if (jQuery(formId).length) {
    var temp = jQuery(formId).serializeArray();
    for (var i in temp) extra[temp[i].name] = temp[i].value;
  }
  return Object.assign(d, extra);
}
function siteDatatableSearch(obj) {
  siteDatatableRefresh(obj);
}
function siteDatatableClearSearch(tableId, formId) {
  jQuery(formId + " input:text").val("");
  jQuery(formId + " select").val("");
  applySelect2(jQuery(formId + " select"));
  siteDatatableRefresh(tableId);
}
