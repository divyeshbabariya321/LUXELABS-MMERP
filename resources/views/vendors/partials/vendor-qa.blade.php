<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }} ">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vendor Question Answers</h5>
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
                                    <select class="form-control col-md-6 mr-3" name="qa_vendor_id" id="qa_vendor_id">
                                        <option value="">Select Vendor</option>
                                            @foreach ($vendorQuestionAnswers as $vendord)
                                                <option value="{{ $vendord->id }}">{{ $vendord->name }}</option>
                                            @endforeach
                                    </select>
                                    <button type="button" class="btn btn-secondary btn-vendor-search-qa" ><i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-12 show-vendor-search-qa-list" id="">

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    var configsqa = {
        routes : {
            'vendors_qa_search':"{{route('vendors.qa.search')}}",
            'vendors_question_getgetanswer':"{{route('vendors.question.getgetanswer')}}",
            'vendors_question_saveanswer':"{{route('vendors.question.saveanswer')}}",
            'vendors_qastatus_histories':"{{route('vendors.qastatus.histories')}}",
        }
    };
    
    
function saveAnswerHeaderQa(vendor_id, question_id) {
    var answer = $("#answer_header_" + vendor_id + "_" + question_id).val();

    if(answer == ""){
        toastr["Error"]("Please enter answer.");
    }else{
        $.ajax({
            url: configsqa.routes.vendors_question_saveanswer,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },data: {
                vendor_id: vendor_id,
                question_id: question_id,
                answer: answer,
            },beforeSend: function () {
                $(this).text("Loading...");
                $("#loading-image").show();
            },success: function (response) {
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

$(document).on("click", ".btn-vendor-search-qa", function (e) {
    var qa_vendor_id = $("#qa_vendor_id").val();

    if(qa_vendor_id > 0) {
        $.ajax({
            url: configsqa.routes.vendors_qa_search,
            type: "POST",
            data: {
                vendor_id: qa_vendor_id,
            },headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },beforeSend: function () {
                $("#loading-image").show();
            },success: function (response) {
                $("#loading-image").hide();
                $(".show-vendor-search-qa-list").html(response);
            },error: function () {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    }else{
        toastr["Error"]("Please select vendor.");
    }
});
      
      
        $(document).on("click", ".answer-history-show-header-qa", function () {
          var vendor_id = $(this).attr("data-vendorid");
          var question_id = $(this).attr("data-qa_id");

          $.ajax({
            url: configsqa.routes.vendors_question_getgetanswer,
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
        
        
$(document).on("click", ".status-history-show-header-qa", function () {
    var vendor_id = $(this).attr("data-id");
    var question_id = $(this).attr("data-qa_id");

    var routeUrl    = configsqa.routes.vendors_qastatus_histories;
    var modalId     = "#qa-status-histories-list-header-qa";
    var inputData   = {
        vendor_id: vendor_id,
        question_id: question_id
    };

    postAJAXCall(routeUrl,modalId,inputData);
});

function postAJAXCall(routeUrl,modalId,data) {
    $.ajax({
        url: routeUrl,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: data,
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
        
                $(modalId).find(".qa-status-histories-list-view-header-qa").html(html);
                $(modalId).modal("show");
            }else{
                toastr["error"](response.error, "Message");
            }
        },
    });
}

</script>