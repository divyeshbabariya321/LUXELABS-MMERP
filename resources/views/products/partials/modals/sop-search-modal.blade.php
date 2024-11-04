<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }} ">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Sop Search</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex" id="search-bar">
                        <input type="text" value="" name="search" id="menu_sop_search" class="form-control"
                            placeholder="Search Here.." style="width: 30%;">
                        <a title="Sop Search" type="button" class="sop_search_menu btn btn-sm btn-image "
                            style="padding: 10px"><span>
                                <img src="{{ asset('images/search.png') }}" alt="Search"></span></a>
                        <button type="button" class="btn btn-secondary1 mr-2 addnotesop" data-toggle="modal"
                            data-target="#exampleModalAppLayout">Add Notes</button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered page-notes"
                            style="font-size:13.8px;border:0px !important; table-layout:fixed"
                            id="NameTable-app-layout">
                            <thead>
                                <tr>
                                    <th width="2%">ID</th>
                                    <th width="10%">Name</th>
                                    <th width="14%">Content</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody class="sop_search_result">
                                @include('products.sop-search-modal-content', ['usersop' => $usersop])
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    var configssop = {
        routes : {
            'editName' : "{{ route('editName') }}",
            'updateName' : "{{ route('updateName') }}",
            'menu_sop_search' : "{{ route('menu.sop.search') }}",
            'sop_store' : "{{ route('sop.store') }}",

            'sop_categorylistajax': "{{ route('sop.categorylistajax') }}",
        }
    };

    $(document).on("click", ".sop_search_menu", function(e) {
        let $this = $("#menu_sop_search").val();
        var q = $this;
        $.ajax({
            url: configssop.routes.menu_sop_search,
            type: "GET",
            data: {
                search: q,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            // dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {

                $("#loading-image").hide();
                $(".sop_search_result").empty();
                $(".sop_search_result").append(response);
                toastr["success"]("Data found successfully", "success");
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            },
        });
    });

    $(document).on("click", ".menu_editor_edit", function() {
        var $this = $(this);

        $.ajax({
            type: "GET",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id: $this.data("id"),
            },

            url: configssop.routes.editName,
        }).done(function (data) {

            $("#sop_edit_id").val(data.sopedit.id);
            $("#sop_edit_name").val(data.sopedit.name);
            $("#sop_edit_category").val(data.sopedit.sopCategory);
            $("#sop_edit_category").trigger("change");
            $("#sop_old_name").val(data.sopedit.name);
            $("#sop_old_category").val(data.sopedit.category);
            $("#sop_edit_content").val(data.sopedit.content);

            $("#modal-container-1").load("/menu-sopupdate", function () {
        $("#menu-sopupdate").modal("show");
      });
            CKEDITOR.instances["sop_edit_content"].setData(data.sopedit.content);

            $("#menu-sopupdate #menu_sop_edit_form").attr(
                "data-id",
                $($this).attr("data-id")
            );
        }).fail(function(data) {});

    });

    $(document).on("submit", "#menu_sop_edit_form", function(e) {
        e.preventDefault();
        const $this = $(this);

        $.ajax({

            type: "POST",
            data: $(this).serialize(),
            url: configssop.routes.updateName,
            datatype: "json",
        })
        .done(function (data) {
            if (data.success == false) {
                toastr["error"](data.message, "Message");
                return false;
            }
            if (data.type == "edit") {
                var content = data.sopedit.content.replace(/(<([^>]+)>)/gi, "");
                let id = $($this).attr("data-id");
                $("#sid" + id + " td:nth-child(3)").html(
                `
                                <span class="show-short-content-` +
                    data.sopedit.id +
                    `">` +
                    content.replace(/(.{50})..+/, "$1..") +
                    `</span>
                                <span style="word-break:break-all;" class="show-full-content-` +
                    data.sopedit.id +
                    ` hidden">` +
                    content +
                    `</span>`
                );
                $("#menu_sopupdate").modal("hide");
                toastr["success"]("Data Updated Successfully!", "Message");
            }
        })
        .fail(function (data) {
            console.log(data);
        });

    });

    // $(document).on("submit", "#menu_sop_edit_form", function (e) {
    //     e.preventDefault();
    //     const $this = $(this);
    //     $(this).attr("data-id");

    //     $.ajax({
    //         type: "POST",
    //         data: $(this).serialize(),
    //         url: configs.routes.updateName,
    //         datatype: "json",
    //     })
    //     .done(function (data) {
    //         if (data.success == false) {
    //             toastr["error"](data.message, "Message");
    //             return false;
    //         }
    //         if (data.type == "edit") {
    //             var content = data.sopedit.content.replace(/(<([^>]+)>)/gi, "");
    //             let id = $($this).attr("data-id");
    //             $("#sid" + id + " td:nth-child(3)").html(
    //             `
    //                             <span class="show-short-content-` +
    //                 data.sopedit.id +
    //                 `">` +
    //                 content.replace(/(.{50})..+/, "$1..") +
    //                 `</span>
    //                             <span style="word-break:break-all;" class="show-full-content-` +
    //                 data.sopedit.id +
    //                 ` hidden">` +
    //                 content +
    //                 `</span>`
    //             );
    //             $("#menu_sopupdate").modal("hide");
    //             toastr["success"]("Data Updated Successfully!", "Message");
    //         }
    //     })
    //     .fail(function (data) {
    //     });
    // });
</script>
