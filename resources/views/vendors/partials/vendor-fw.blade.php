<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }} ">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vendor Flow charts</h5>
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
                                    <select class="form-control col-md-6 mr-3" name="fc_vendor_id" id="fc_vendor_id">
                                        <option value="">Select Vendor</option>
                                            @foreach ($vendorFlowcharts as $vendord)
                                                <option value="{{ $vendord->id }}">{{ $vendord->name }}</option>
                                            @endforeach
                                    </select>
                                    <button type="button" class="btn btn-secondary btn-vendor-search-flowchart" ><i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-12 show-vendor-search-flowchart-list" id=""></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    var configsfw = {
        routes : {
            'vendors_flowcharts_search':"{{route('vendors.flowcharts.search')}}",
            'vendors_flowchart_saveremarks':"{{route('vendors.flowchart.saveremarks')}}",
            'vendors_flowchart_getremarks':"{{route('vendors.flowchart.getremarks')}}",
            'vendors_flowchartstatus_histories':"{{route('vendors.flowchartstatus.histories')}}",
        }
    };
    
    
function saveRemarksHeaderFc(vendor_id, flow_chart_id) {
    var remarks = $("#remark_header_" + vendor_id + "_" + flow_chart_id).val();

    if(remarks == "") {
        toastr["error"]("Please enter remarks.");
    }else{
        $.ajax({
            url: configsfw.routes.vendors_flowchart_saveremarks,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },data: {
                vendor_id: vendor_id,
                flow_chart_id: flow_chart_id,
                remarks: remarks,
            },beforeSend: function () {
                $(this).text("Loading...");
                $("#loading-image").show();
            },success: function (response) {
                $("#remark_header_" + vendor_id + "_" + flow_chart_id).val("");
                $("#loading-image").hide();
                toastr["success"]("Remarks Added successfully!!!", "success");
            }
        }).fail(function (response) {
            $("#loading-image").hide();
            toastr["error"](response.responseJSON.message);
        });
    }
}

$(document).on("click", ".btn-vendor-search-flowchart", function (e) {
  var fc_vendor_id = $("#fc_vendor_id").val();

    if (fc_vendor_id > 0) {
        $.ajax({
            url: configsfw.routes.vendors_flowcharts_search,
            type: "POST",
            data: {
                vendor_id: fc_vendor_id,
            },headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },beforeSend: function () {
                $("#loading-image").show();
            },success: function (response) {
                $("#loading-image").hide();
                $(".show-vendor-search-flowchart-list").html(response);
            },error: function () {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            },
        });
    }else{
        toastr["error"]("Please select vendor.");
    }
});


$(document).on("click", ".remarks-history-show-header-fc", function () {
    var vendor_id = $(this).attr("data-vendorid");
    var flow_chart_id = $(this).attr("data-flow_chart_id");

    $.ajax({
        url: configsfw.routes.vendors_flowchart_getremarks,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },data: {
            vendor_id: vendor_id,
            flow_chart_id: flow_chart_id,
        },success: function (response) {
            if(response.status){
                var html = response.results;
                $("#vfc-remarks-histories-list-header-fc").find(".vfc-remarks-histories-list-view-header-fc").html(html);
                $("#vfc-remarks-histories-list-header-fc").modal("show");
            }else{
                toastr["error"](response.error, "Message");
            }
        },
    });
});

$(document).on("click", ".status-history-show-header-fc", function () {
    var vendor_id = $(this).attr("data-id");
    var flow_chart_id = $(this).attr("data-flow_chart_id");

    $.ajax({
        url: configsfw.routes.vendors_flowchartstatus_histories,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },data: {
            vendor_id: vendor_id,
            flow_chart_id: flow_chart_id,
        },success: function (response) {
            if(response.status){
                var html = "";
                html = response.results
                
                $("#fl-status-histories-list-header-fc").find(".fl-status-histories-list-view-header-fc").html(html);
                $("#fl-status-histories-list-header-fc").modal("show");
            } else {
                toastr["error"](response.error, "Message");
            }
        }
    });
});
</script>