var productTemplate = {
  init: function (settings) {
    productTemplate.config = {
      bodyView: settings.bodyView,
    };

    $.extend(productTemplate.config, settings);

    this.getResults();

    //initialize pagination
    productTemplate.config.bodyView.on("click", ".page-link", function (e) {
      e.preventDefault();
      productTemplate.getResults($(this).attr("href"));
    });

    //create producte template
    productTemplate.config.bodyView.on(
      "click",
      ".create-product-template-btn",
      function (e) {
        productTemplate.openForm();
      }
    );

    $(document).on("click", ".create-product-template", function (e) {
      if ($("#product-template-from").valid()) {
        productTemplate.submitForm($(this));
      }
    });
    if (productTemplate.config.isOpenCreateFrom == "true") {
      productTemplate.openForm();
    }
  },
  validationRule: function () {
    $(document)
      .find("#product-template-from")
      .validate({
        rules: {
          no_of_virtual_user: "required",
          domain_name   : "required",
          path        : "required",
          ramp_time: "required",
        },
        messages: {
          no_of_virtual_user: "Please Enter No Of Virtual User",
          domain_name   : "Please Enter Domain Name",
          path        : "Please Enter Path",
          ramp_time: "Please Enter Ramp Time",
        },
      });
  },
  getResults: function (href) {
    var search = $(".keyword-text").val();
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl +
            "/load-testing/response?keyword=" +
            search,
      method: "get",
    };
    this.sendAjax(_z, "showResults");
  },
  showResults: function (response) {
    var addProductTpl = $.templates("#product-templates-result-block");
    var tplHtml = addProductTpl.render(response);
    productTemplate.config.bodyView.find("#page-view-result").html(tplHtml);
  },
  openForm: function () {
    var addProductTpl = $.templates("#product-templates-create-block");
    
    var tplHtml = addProductTpl.render({});
    $("#display-area").html(tplHtml);
    $("#product-template-create-modal").modal("show");
    productTemplate.validationRule();
    $(".select-2-brand").select2({
      width: "100%",
    });
    $(".select2").select2({
      width: "100%",
      tags: true,
    });
    productTemplate.productSearch();

    productTemplate.changeTemplateNo();
    $(".template_no").trigger("change");
  },
  selectProductId: function (response) {
    $(".show-product-image").html(response.data);
  },
  changeTemplateNo: function (response) {
    $(document).on("change", ".template_no", function (e) {
      var id = $(this).val();
      var html = "";
      var changeTemplateImage = $(this).find("option:selected").data("image");
      if (changeTemplateImage) {
        html = '<img src="' + changeTemplateImage + '" width="100%">';
      }
      $(".image_template_no").html(html);
    });
  },
  submitForm: function (ele) {
    var form = ele.closest("#product-template-create-modal").find("form");
    var formData = new FormData(form[0]);
    var _z = {
      url:
        typeof href != "undefined"
          ? href
          : this.config.baseUrl + "/load-testing/create",
      method: "post",
      data: formData,
    };
    this.sendFormDataAjax(_z, "closeForm");
  },
  closeForm: function (response) {
    
    if (response.code == 1) {
      location.href = "/load-testing";
    }
    if (response.code == 0) {
      
      toastr["error"](response.message, "Error");
   }
  },
};

$.extend(productTemplate, common);
