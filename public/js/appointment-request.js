var page = {
    init: function (settings) {
        page.config = {
          bodyView: settings.bodyView,
        };

        

        settings.baseUrl += "/appointment-request";

        $.extend(page.config, settings);

        this.getResults();

        //initialize pagination
        page.config.bodyView.on("click", ".page-link", function (e) {
            e.preventDefault();
            page.getResults($(this).attr("href"));
        });

        page.config.bodyView.on("click", ".btn-search-action", function (e) {
            e.preventDefault();
            page_script_document = 0;
            page.getResults();
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
            toastr["success"]("Script Document deleted successfully", "success");
        } else {
            toastr["error"]("Oops.something went wrong", "error");
        }
    },
    editRecord: function (ele) {
        var _z = {
            url: this.config.baseUrl + "/edit/" + ele.data("id"),
            method: "get",
        };
        this.sendAjax(_z, "editResult");
    },
    editResult: function (response) {
        $("#scriptdocumentEditModal").modal("show");
        $(".id").val("");
        $("#script_type_update").val("");
        $("#file_update").val("");
        $("#category_update").val("");
        $("#usage_parameter_update").val("");
        $("#comments_update").val("");
        $("#author_update").val("");
        $("#description_update").val("");
        $("#location_update").val("");
        $("#last_run_update").val("");
        $("#status_update").val("");

        $(".id").val(response.data.id);
        $("#script_type_update").val(response.data.script_type);
        $("#file_update").val(response.data.file);
        $("#category_update").val(response.data.category);
        $("#usage_parameter_update").val(response.data.usage_parameter);
        $("#comments_update").val(response.data.comments);
        $("#author_update").val(response.data.author);
        $("#description_update").val(response.data.description);
        $("#location_update").val(response.data.location);
        $("#last_run_update").val(response.data.last_run);
        $("#status_update").val(response.data.status);
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
};
$.extend(page, common);