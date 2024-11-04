<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }} ">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Vendor Rating Question Answers</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12">
                    <form id="database-form">
                        @csrf
                        <div class="row">
                            <div class="col-12 pb-3">
                                <select class="form-control col-md-6 mr-3" name="rqa_vendor_id" id="rqa_vendor_id">
                                    <option value="">Select Vendor</option>
                                        @foreach ($vendorRatingQuestionAnswers as $vendord)
                                            <option value="{{ $vendord->id }}">{{ $vendord->name }}</option>
                                        @endforeach
                                </select>
                                <button type="button" class="btn btn-secondary btn-vendor-search-rqa" ><i class="fa fa-search"></i></button>
                            </div>
                            <div class="col-12 show-vendor-search-rqa-list" id="">

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var configsrtg = {
        routes : {
            'vendors_rqa_search':"{{route('vendors.rqa.search')}}",
            'vendors_rquestion_getgetanswer':"{{route('vendors.rquestion.getgetanswer')}}",
            'vendors_question_saveranswer':"{{route('vendors.question.saveranswer')}}",
            'vendors_rqastatus_histories':"{{route('vendors.rqastatus.histories')}}",
        }
    };
    $(document).on("click", ".btn-vendor-search-rqa", function (e) {
        var rqa_vendor_id = $("#rqa_vendor_id").val();

        if (rqa_vendor_id > 0) {
            $.ajax({
                url: configsrtg.routes.vendors_rqa_search,
                type: "POST",
                data: {
                    vendor_id: rqa_vendor_id,
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
                    $(".show-vendor-search-rqa-list").html(response);
                },
                error: function () {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                },
            });
        } else {
            alert("Please select vendor.");
        }
    });

    $(document).on("click", ".ranswer-history-show-header-rqa", function () {
        var vendor_id = $(this).attr("data-vendorid");
        var question_id = $(this).attr("data-rqa_id");

        $.ajax({
            url: configsrtg.routes.vendors_rquestion_getgetanswer,
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

    function saveAnswerHeaderRQa(vendor_id, question_id) {
        var answer = $("#answerr_header_" + vendor_id + "_" + question_id)
            .find("option:selected")
            .val();

        if (answer == "") {
            alert("Please select answer.");
        } else {
            $.ajax({
            url: configsrtg.routes.vendors_question_saveranswer,
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

    $(document).on("click", ".status-history-show-header-rqa", function () {
        var vendor_id = $(this).attr("data-id");
        var question_id = $(this).attr("data-rqa_id");

        $.ajax({
            url: configsrtg.routes.vendors_rqastatus_histories,
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
                            <td> ${
                                v.old_value != null
                                ? v.old_value.status_name
                                : " - "
                                } </td>
                            <td> ${
                                v.new_value != null
                                ? v.new_value.status_name
                                : " - "
                                } </td>
                            <td> ${
                                v.user !== undefined ? v.user.name : " - "
                                } </td>
                            <td> ${v.created_at} </td>
                        </tr>`;
                    });
                    $("#rqa-status-histories-list-header-rqa")
                    .find(".rqa-status-histories-list-view-header-rqa")
                    .html(html);
                    $("#rqa-status-histories-list-header-rqa").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            },
        });
    });
</script>