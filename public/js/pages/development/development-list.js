jQuery(document).ready(function () {
  if (jQuery("#start_date").length) {
    jQuery("#start_date").datetimepicker({
      format: "YYYY-MM-DD HH:mm:ss",
    });
  }
});
