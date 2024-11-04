@extends('layouts.app')

@section('favicon' , 'vendor.png')

@section('title', 'Vendor Info')

@section('styles')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<style type="text/css">
    .numberSend {
        width: 160px;
        background-color: transparent;
        color: transparent;
        text-align: center;
        border-radius: 6px;
        position: absolute;
        z-index: 1;
        left: 19%;
        margin-left: -80px;
        display: none;
    }

    .input-sm {
        width: 60px;
    }

    #loading-image {
        position: fixed;
        top: 50%;
        left: 50%;
        margin: -50px 0px 0px -50px;
        z-index: 60;
    }

    .cls_filter_inputbox {
        width: 12%;
        text-align: center;
    }

    .message-chat-txt {
        color: #333 !important;
    }

    .cls_remove_leftpadding {
        padding-left: 0px !important;
    }

    .cls_remove_rightpadding {
        padding-right: 0px !important;
    }

    .cls_action_btn .btn {
        padding: 6px 12px;
    }

    .cls_remove_allpadding {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }

    .cls_quick_message {
        width: 100% !important;
        height: 35px !important;
    }

    .cls_filter_box {
        width: 100%;
    }

    .select2-selection.select2-selection--single {
        height: 35px;
    }

    .cls_action_btn .btn-image img {
        width: 13px !important;
    }

    .cls_action_btn .btn {
        padding: 6px 2px;
    }

    .cls_textarea_subbox {
        width: 100%;
    }

    .btn.btn-image.delete_quick_comment {
        padding: 4px;
    }

    .vendor-update-status-icon {
        padding: 0px;
    }

    .cls_commu_his {
        width: 100% !important;
    }

    .vendor-update-status-icon {
        margin-top: -7px;
    }

    .clsphonebox .btn.btn-image {
        padding: 5px;
    }

    .clsphonebox {
        margin-top: -8px;
    }

    .send-message1 {
        padding: 0px 10px;
    }

    .load-communication-modal {
        margin-top: -6px;
        margin-left: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 35px;
    }

    .select2-selection__arrow {
        display: none;
    }

    .cls_mesg_box {
        margin-top: -7px;
        font-size: 12px;
    }

    .td-full-container {
        color: #333;
    }

    .select-width {
        width: 80% !important;
    }

    .i-vendor-status-history {
        position: absolute;
        top: 17px;
        right: 10px;
    }
    #vendorCreateModal .select2-container, #vendorEditModal .select2-container {width: 100% !important;}

    table select.form-control, table input.form-control {
        min-width: 140px;
    }
</style>
@endsection

@section('large_content')
<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">
            Vendor All Section
        </h2>
    </div>

    <div class="col-12">
        <form class="form-inline" action="{{ route('vendors.all-section') }}" method="GET">

            <div class="form-group mr-3 col-md-6 p-0">
                <strong>Select Vendor :</strong></br>

                {{ html()->multiselect("vendors[]", $vendors, request('vendors'))->class("form-control selectx-vendor") }}
            </div>

            <div class="form-group col-md-2 pr-0 pt-20" style=" padding-top: 20px;">
                <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                <a href="{{route('vendors.all-section')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </form>
    </div>
</div>

@include('partials.flash_messages')

@if(count($VendorFlowchart)>0)
<div class="infinite-scroll mt-2" style="overflow-y: auto">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">
            Vendor Flow Chart
        </h2>
    </div>
    <table class="table table-bordered table-striped" id="vendor-table">
        <thead>
            <tr>
                <th width="10%">Vendor</th>
                <th width="10%">Categgory</th>
                @if($vendor_flow_charts)
                    @foreach($vendor_flow_charts as $flow_chart)
                        <th width="20%">
                            {{$flow_chart->name}}

                            @if (auth()->user()->isAdmin())
                                <button style="padding-left: 10px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline delete-category" title="Delete Category" data-id="{{$flow_chart->id}}" ><i class="fa fa-trash"></i></button>
                            @endif
                        </th>
                    @endforeach
                @endif
            </tr>
        </thead>

        <tbody>
            @foreach ($VendorFlowchart as $vendor)
                <tr>
                    <td>{{ $vendor->name }}</td>
                    <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
                    @if($vendor_flow_charts)
                        @foreach($vendor_flow_charts as $flow_chart)
                            @php
                                $status_color = new stdClass();
                                $status_hcolor = \App\Helpers\VendorHelper::getVendorFlowChartStatusHistoryByFlowChartAndVendorId($flow_chart->id, $vendor->id);
                                if (!empty($status_hcolor->new_value)) {
                                    $status_color = \App\Helpers\VendorHelper::getVendorFlowChartStatusById($status_hcolor->new_value);
                                }
                            @endphp
                            <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                                <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                    <input style="margin-top: 0px;width:40% !important;" type="text" class="form-control " name="message" placeholder="Remarks" id="remark_{{ $vendor->id }}_{{ $flow_chart->id }}" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}">
                                    <div style="margin-top: 0px;" class="d-flex p-0">
                                        <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{ $vendor->id }}, {{ $flow_chart->id }})"><img src="/images/filled-sent.png"></button>
                                        <button type="button" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}" class="btn btn-image remarks-history-show p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                                    </div>

                                    <select style="margin-top: 0px;width:40% !important;" class="form-control status-dropdown" name="status" class="status-dropdown" data-id="{{$vendor->id}}" data-flow_chart_id="{{$flow_chart->id}}">
                                        <option value="">Select Status</option>
                                        @foreach ($status as $stat)
                                            <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" data-id="{{ $vendor->id  }}" data-flow_chart_id="{{$flow_chart->id}}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                                </div>
                            </td>
                        @endforeach
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if(count($VendorQuestionAnswer)>0)
<div class="infinite-scroll mt-5" style="overflow-y: auto">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">
            Vendor Question Answer
        </h2>
    </div>
    <table class="table table-bordered" id="vendor-table">
        <thead>
            <tr>
                <th width="10%">Vendor</th>
                <th width="10%">Category</th>
                @if($vendor_questions)
                    @foreach($vendor_questions as $question_data)
                        <th width="20%">
                            {{$question_data->question}}

                            @if (auth()->user()->isAdmin())
                                <button style="padding-left: 10px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline delete-qa-category" title="Delete Category" data-id="{{$question_data->id}}" ><i class="fa fa-trash"></i></button>
                            @endif
                        </th>
                    @endforeach
                @endif
            </tr>
        </thead>

        <tbody id="vendor-body">
            @foreach ($VendorQuestionAnswer as $vendor)
                <tr>
                    <td>{{ $vendor->name }}</td>
                    <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
                    @if($vendor_questions)
                        @foreach($vendor_questions as $question_data)
                            @php
                                $status_color = new stdClass();
                                $status_hcolor = \App\Helpers\VendorHelper::getVendorQuestionStatusHistoryByQidAndVendorId($question_data->id, $vendor->id);
                                if (!empty($status_hcolor->new_value)) {
                                    $status_color = \App\Helpers\VendorHelper::getVendorQuestionStatusById($status_hcolor->new_value);
                                }
                            @endphp

                            <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                                <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                    <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="answer" placeholder="Answer" id="answer_{{ $vendor->id }}_{{ $question_data->id }}" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}">
                                    <div style="margin-top: 0px;" class="d-flex p-0">
                                        <button class="btn pr-0 btn-xs btn-image " onclick="saveAnswer({{ $vendor->id }}, {{ $question_data->id }})"><img src="/images/filled-sent.png"></button>
                                        <button type="button" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}" class="btn btn-image answer-history-show p-0 ml-2" title="Answer Histories"><i class="fa fa-info-circle"></i></button>
                                    </div>

                                    <select style="margin-top: 0px;width:10% !important;" class="form-control status-dropdown-q" name="status" data-id="{{$vendor->id}}" data-question_id="{{$question_data->id}}">
                                        <option value="">Select Status</option>
                                        @foreach ($status_q as $stat)
                                            <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" data-id="{{ $vendor->id  }}" data-question_id="{{$question_data->id}}" class="btn btn-image status-history-show-q p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                                </div>
                            </td>
                        @endforeach
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if(count($VendorQuestionRAnswer)>0)
<div class="infinite-scroll mt-5" style="overflow-y: auto">
    <h2 class="page-heading">
            Vendor Rating Question Answer
        </h2>
    <table class="table table-bordered" id="vendor-table">
        <thead>
            <tr>                
                <th width="10%">Vendor</th>
                <th width="10%">Category</th>
                @if($vendor_r_questions)
                    @foreach($vendor_r_questions as $question_data)
                        <th width="20%">
                            {{$question_data->question}}

                            @if (auth()->user()->isAdmin())
                                <button style="padding-left: 10px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline delete-rqa-category" title="Delete Category" data-id="{{$question_data->id}}" ><i class="fa fa-trash"></i></button>
                            @endif
                        </th>
                    @endforeach
                @endif
            </tr>
        </thead>

        <tbody id="vendor-body">
            @foreach ($VendorQuestionRAnswer as $vendor)
                <tr>
                    <td>{{ $vendor->name }}</td>
                    <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
                    @if($vendor_r_questions)
                        @foreach($vendor_r_questions as $question_data)
                            @php
                                $status_color = new stdClass();
                                $status_hcolor = \App\Helpers\VendorHelper::getVendorRatingQAStatusHistoryByQidAndVendorId($question_data->id, $vendor->id);
                                if (!empty($status_hcolor->new_value)) {
                                    $status_color = \App\Helpers\VendorHelper::getVendorRatingQAStatusById($status_hcolor->new_value);
                                }
                            @endphp
                            <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                                <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                    <select style="margin-top: 0px;width:10% !important;" class="form-control " name="answer" id="answerr_{{ $vendor->id }}_{{ $question_data->id }}" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}">
                                        <option>-Select rating-</option>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                    </select>
                                    <div style="margin-top: 0px; margin-right: 10px;" class="d-flex p-0">
                                        <button class="btn pr-0 btn-xs btn-image " onclick="saverAnswerr({{ $vendor->id }}, {{ $question_data->id }})"><img src="/images/filled-sent.png"></button>
                                        <button type="button" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}" class="btn btn-image ranswer-history-show p-0 ml-2" title="Answer Histories"><i class="fa fa-info-circle"></i></button>
                                    </div>
                                
                                    <select style="margin-top: 0px;width:10% !important;" class="form-control status-dropdown-r" name="status"data-id="{{$vendor->id}}" data-question_id="{{$question_data->id}}">
                                        <option value="">Select Status</option>
                                        @foreach ($status_r as $stat)
                                            <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" data-id="{{ $vendor->id  }}" data-question_id="{{$question_data->id}}" class="btn btn-image status-history-show-r p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                                </div>
                            </td>
                        @endforeach
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div id="vfc-remarks-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Remarks Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Remarks</th>
                                <th width="20%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="vfc-remarks-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="fl-status-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Old Status</th>
                                <th width="30%">New Status</th>
                                <th width="20%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="fl-status-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="vqa-answer-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Answer Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Answer</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="vqa-answer-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="qa-status-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Old Status</th>
                                <th width="30%">New Status</th>
                                <th width="20%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="qa-status-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="vqar-answer-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rating Answer Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Rating</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="vqar-answer-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="rqa-status-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Old Status</th>
                                <th width="30%">New Status</th>
                                <th width="20%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="rqa-status-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>

<script type="text/javascript">

    function saveRemarks(vendor_id, flow_chart_id){

        var remarks = $("#remark_"+vendor_id+"_"+flow_chart_id).val();

        if(remarks==''){
            alert('Please enter remarks.');
        } else {

            $.ajax({
                url: "{{route('vendors.flowchart.saveremarks')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'vendor_id' :vendor_id,
                    'flow_chart_id' :flow_chart_id,
                    'remarks' :remarks,
                },
                beforeSend: function() {
                    $(this).text('Loading...');
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#remark_"+vendor_id+"_"+flow_chart_id).val('');
                    $("#loading-image").hide();
                    toastr['success']('Remarks Added successfully!!!', 'success');
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                toastr['error'](response.responseJSON.message);
            });
        }
    }

    $(document).on('click', '.remarks-history-show', function() {
        var vendor_id = $(this).attr('data-vendorid');
        var flow_chart_id = $(this).attr('data-flow_chart_id');

        $.ajax({
            url: "{{route('vendors.flowchart.getremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'flow_chart_id' :flow_chart_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.remarks != null) ? v.remarks : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#vfc-remarks-histories-list").find(".vfc-remarks-histories-list-view").html(html);
                    $("#vfc-remarks-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $('.status-dropdown').change(function(e) {
      e.preventDefault();
      var vendor_id = $(this).data('id');
      var flow_chart_id = $(this).data('flow_chart_id');
      var selectedStatus = $(this).val();

      // Make an AJAX request to update the status
      $.ajax({
        url: '/vendor/update-flowchartstatus',
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          vendor_id: vendor_id,
          flow_chart_id: flow_chart_id,
          selectedStatus: selectedStatus
        },
        success: function(response) {
          toastr['success']('Status  Created successfully!!!', 'success');
          console.log(response);
        },
        error: function(xhr, status, error) {
          // Handle the error here
          console.error(error);
        }
      });
    });

    $(document).on('click', '.status-history-show', function() {
        var vendor_id = $(this).attr('data-id');
        var flow_chart_id = $(this).attr('data-flow_chart_id');

        $.ajax({
            url: "{{route('vendors.flowchartstatus.histories')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'flow_chart_id' :flow_chart_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                    <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#fl-status-histories-list").find(".fl-status-histories-list-view").html(html);
                    $("#fl-status-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });


    function saveAnswer(vendor_id, question_id){

        var answer = $("#answer_"+vendor_id+"_"+question_id).val();

        if(answer==''){
            alert('Please enter answer.');
        } else {

            $.ajax({
                url: "{{route('vendors.question.saveanswer')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'vendor_id' :vendor_id,
                    'question_id' :question_id,
                    'answer' :answer,
                },
                beforeSend: function() {
                    $(this).text('Loading...');
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#answer_"+vendor_id+"_"+question_id).val('');
                    $("#loading-image").hide();
                    toastr['success']('Answer Added successfully!!!', 'success');
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                toastr['error'](response.responseJSON.message);
            });
        }
    }

    $(document).on('click', '.answer-history-show', function() {
        var vendor_id = $(this).attr('data-vendorid');
        var question_id = $(this).attr('data-question_id');

        $.ajax({
            url: "{{route('vendors.question.getgetanswer')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'question_id' :question_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${v.answer} </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#vqa-answer-histories-list").find(".vqa-answer-histories-list-view").html(html);
                    $("#vqa-answer-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $('.status-dropdown-q').change(function(e) {
      e.preventDefault();
      var vendor_id = $(this).data('id');
      var question_id = $(this).data('question_id');
      var selectedStatus = $(this).val();

      // Make an AJAX request to update the status
      $.ajax({
        url: '/vendor/update-qastatus',
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          vendor_id: vendor_id,
          question_id: question_id,
          selectedStatus: selectedStatus
        },
        success: function(response) {
          toastr['success']('Status  Created successfully!!!', 'success');
          console.log(response);
        },
        error: function(xhr, status, error) {
          // Handle the error here
          console.error(error);
        }
      });
    });

    $(document).on('click', '.status-history-show-q', function() {
        var vendor_id = $(this).attr('data-id');
        var question_id = $(this).attr('data-question_id');

        $.ajax({
            url: "{{route('vendors.qastatus.histories')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'question_id' :question_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                    <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#qa-status-histories-list").find(".qa-status-histories-list-view").html(html);
                    $("#qa-status-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    function saverAnswerr(vendor_id, question_id){

        var answer = $("#answerr_"+vendor_id+"_"+question_id).find("option:selected").val();

        if(answer==''){
            alert('Please select answer.');
        } else {

            $.ajax({
                url: "{{route('vendors.question.saveranswer')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'vendor_id' :vendor_id,
                    'question_id' :question_id,
                    'answer' :answer,
                },
                beforeSend: function() {
                    $(this).text('Loading...');
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#answer_"+vendor_id+"_"+question_id).val('');
                    $("#loading-image").hide();
                    toastr['success']('Answer Added successfully!!!', 'success');
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                toastr['error'](response.responseJSON.message);
            });
        }
    }

    $(document).on('click', '.ranswer-history-show', function() {
        var vendor_id = $(this).attr('data-vendorid');
        var question_id = $(this).attr('data-question_id');

        $.ajax({
            url: "{{route('vendors.rquestion.getgetanswer')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'question_id' :question_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${v.answer} </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#vqar-answer-histories-list").find(".vqar-answer-histories-list-view").html(html);
                    $("#vqar-answer-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $('.status-dropdown-r').change(function(e) {
      e.preventDefault();
      var vendor_id = $(this).data('id');
      var question_id = $(this).data('question_id');
      var selectedStatus = $(this).val();

      // Make an AJAX request to update the status
      $.ajax({
        url: '/vendor/update-rqastatus',
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          vendor_id: vendor_id,
          question_id: question_id,
          selectedStatus: selectedStatus
        },
        success: function(response) {
          toastr['success']('Status  Created successfully!!!', 'success');
          console.log(response);
        },
        error: function(xhr, status, error) {
          // Handle the error here
          console.error(error);
        }
      });
    });

    $(document).on('click', '.status-history-show-r', function() {
        var vendor_id = $(this).attr('data-id');
        var question_id = $(this).attr('data-question_id');

        $.ajax({
            url: "{{route('vendors.rqastatus.histories')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'question_id' :question_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                    <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#rqa-status-histories-list").find(".rqa-status-histories-list-view").html(html);
                    $("#rqa-status-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on("click", ".delete-category",function(e){
        // $('#btn-save').attr("disabled", "disabled");
        e.preventDefault();
        let _token = $("input[name=_token]").val();
        let category_id =  $(this).data('id');
        if(category_id!=""){
            if(confirm("Are you sure you want to delete record?")) {
                
                $.ajax({
                    url:"{{ route('delete.flowchart-category') }}",
                    type:"post",
                    data:{
                        id:category_id,
                        _token: _token
                    },
                    cashe:false,
                    success:function(response){
                        if (response.message) {
                            toastr["success"](response.message, "Message");
                            location.reload();
                        }else{
                            toastr.error(response.message);
                        }
                    }
                });
            } else {

            }
        }else{
            toastr.error("Please realod and try again");
        }
    });

    $(document).on("click", ".delete-qa-category",function(e){
        // $('#btn-save').attr("disabled", "disabled");
        e.preventDefault();
        let _token = $("input[name=_token]").val();
        let category_id =  $(this).data('id');
        if(category_id!=""){
            if(confirm("Are you sure you want to delete record?")) {
                
                $.ajax({
                    url:"{{ route('delete.qa-category') }}",
                    type:"post",
                    data:{
                        id:category_id,
                        _token: _token
                    },
                    cashe:false,
                    success:function(response){
                        if (response.message) {
                            toastr["success"](response.message, "Message");
                            location.reload();
                        }else{
                            toastr.error(response.message);
                        }
                    }
                });
            } else {

            }
        }else{
            toastr.error("Please realod and try again");
        }
    });

    $(document).on("click", ".delete-rqa-category",function(e){
        // $('#btn-save').attr("disabled", "disabled");
        e.preventDefault();
        let _token = $("input[name=_token]").val();
        let category_id =  $(this).data('id');
        if(category_id!=""){
            if(confirm("Are you sure you want to delete record?")) {
                
                $.ajax({
                    url:"{{ route('delete.rqa-category') }}",
                    type:"post",
                    data:{
                        id:category_id,
                        _token: _token
                    },
                    cashe:false,
                    success:function(response){
                        if (response.message) {
                            toastr["success"](response.message, "Message");
                            location.reload();
                        }else{
                            toastr.error(response.message);
                        }
                    }
                });
            } else {

            }
        }else{
            toastr.error("Please realod and try again");
        }
    });

    $(".selectx-vendor").select2({
            tags: true
        });
</script>
@endsection