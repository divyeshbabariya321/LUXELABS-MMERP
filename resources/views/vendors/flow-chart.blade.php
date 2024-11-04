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
            Vendor Flow Chart - {{$flowchart_master->first()->title}} ({{ $totalVendor }})
            <div style="float: right;">
                <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#vfcdatatablecolumnvisibilityList">Column Visiblity</button>

                <a class="btn btn-secondary btn-xs" style="color:white;" data-toggle="modal" data-target="#newFlowChartModal">Create Flow Chart</a>

                <button class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#setFlowChartSorting"> Set Flow Chart Sorting</button>

                <button type="button" class="btn btn-secondary btn-xs" style="color:white;" data-toggle="modal" data-target="#status-create">Add Status</button>

                <button class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
            </div>
        </h2>
    </div>

    <div class="col-12">
        <form class="form-inline" action="{{ route('vendors.flow-chart',$master_id) }}" method="GET">

            <div class="form-group mr-3">
                <strong>Select Vendor :</strong></br>
                <input type="text" name="term" id="searchInput" value="{{ request('term') }}" class="form-control" placeholder="Enter Vendor Name">
                <input type="hidden" id="selectedId" name="selectedId" value="{{ request('selectedId') }}">
            </div>

            <div class="form-group mr-3">
                <strong>Select Vendor Category :</strong></br>
                @php $category_post = request('category'); @endphp
                <select class="form-control" name="category" id="category">
                    <option value="">Category</option>
                    @foreach($vendor_categories as $row_cate)
                        <option value="{{$row_cate->id}}" {{ ($category_post == $row_cate->id)?'selected':''}}>
                            {{$row_cate->title}}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="form-group col-md-2 pr-0 pt-20" style=" padding-top: 20px;">
                <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                <a href="{{route('vendors.flow-chart',$master_id)}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </form>
    </div>
</div>

@include('partials.flash_messages')
@include("vendors.partials.column-visibility-modal-fc")
@include('vendors.partials.add-flow-chart')

<div class="infinite-scroll mt-5" style="overflow-y: auto">
    <table class="table table-bordered table-striped" id="vendor-table">
        <thead>
            <tr>
                @if(!empty($dynamicColumnsToShowVendorsfc))
                    @if (!in_array('Vendor', $dynamicColumnsToShowVendorsfc))
                        <th width="20%">Vendor</th>
                    @endif
                    @if (!in_array('Categgory', $dynamicColumnsToShowVendorsfc))
                        <th width="20%">Categgory</th>
                    @endif
                    @if($vendor_flow_charts)
                        @foreach($vendor_flow_charts as $flow_chart)
                            @if (!in_array($flow_chart->id, $dynamicColumnsToShowVendorsfc))
                                <th width="20%">

                                    {{$flow_chart->name}}

                                    @if (auth()->user()->isAdmin())
                                        <button style="padding-left: 10px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline delete-category" title="Delete Category" data-id="{{$flow_chart->id}}" ><i class="fa fa-trash"></i></button>
                                    @endif

                                </th>
                            @endif
                        @endforeach
                    @endif
                @else
                    <th width="20%">Vendor</th>
                    <th width="20%">Categgory</th>
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
                @endif
            </tr>
        </thead>

        <tbody>
            @include('vendors.partials.data-fc')
        </tbody>
    </table>

    {!! $VendorFlowchart->appends(Request::except('page'))->links() !!}
</div>

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

<div id="status-create" class="modal fade in" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Add Stauts</h4>
      <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <form  method="POST" id="status-create-form">
        @csrf
        @method('POST')
          <div class="modal-body">
            <div class="form-group">
              {{ html()->label('Name', 'status_name')->class('form-control-label') }}
              {{ html()->text('status_name')->class('form-control')->required()->attribute('rows', 3) }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary status-save-btn">Save</button>
          </div>
        </div>
      </form>
    </div>

  </div>
</div>
<div id="newStatusColor" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Color</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('vendors.flowchartstatuscolor') }}" method="POST">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                            <td class="text-center"><b>Action</b></td>
                        </tr>
                        <?php
                        foreach ($status as $status_data) { ?>
                        <tr>
                            <td>
                                <input type="text" name="colorname[<?php echo $status_data->id; ?>]" class="form-control" value="<?php echo $status_data->status_name; ?>">
                            </td>
                            <td style="text-align:center;"><?php echo $status_data->status_color; ?></td>
                            <td style="text-align:center;"><input type="color" name="color_name[<?php echo $status_data->id; ?>]" class="form-control" data-id="<?php echo $status_data->id; ?>" id="color_name_<?php echo $status_data->id; ?>" value="<?php echo $status_data->status_color; ?>" style="height:30px;padding:0px;"></td>
                            <td>
                                <button style="padding-left: 10px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline delete-status-fc" title="Delete Status" data-id="{{$status_data->id}}" ><i class="fa fa-trash"></i></button>
                            </td>                           
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit-status-color">Save changes</button>
                </div>
            </form>
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

<div id="fchartnotes-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Flow Chart Notes</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('vendor.flowchart.notes.store') }}" method="POST">
                    @csrf
                    <div class="form-group col-md-10 p-0">
                        <input type="hidden" id="notes_vendor_id" name="vendor_id">
                        <input type="hidden" id="notes_flow_chart_id" name="flow_chart_id">
                        <textarea class="form-control" name="notes" placeholder="Enter Notes" value="{{ old('notes') }}" required></textarea>

                        @if ($errors->has('notes'))
                            <div class="alert alert-danger">{{$errors->first('notes')}}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-1">
                        <button type="submit" class="btn btn-secondary">Add Notes</button>
                    </div>
                </form>

                <div class="col-md-12 p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th>Note</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fchartnotes-histories-list-view">
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
<div id="vendor-flowchart-history-model" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vendor Flow charts <span><b id="vendornameTitle"></b></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="show-vendor-history-flowchart-list" id="">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include("vendors.partials.modal-flow-chart-sorting")
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

    $("#filter_vendor").select2();

    $(document).ready(function($) {
        $("#searchInput").autocomplete({
            source: function(request, response) {
                // Send an AJAX request to the server-side script
                $.ajax({
                    url: '{{ route('vendors.autocomplete') }}',
                    dataType: 'json',
                    data: {
                        term: request.term // Pass user input as 'term' parameter
                    },
                    success: function (data) {
                        var transformedData = Object.keys(data).map(function(key) {
                            return {
                                label: data[key],
                                value: data[key],
                                id: key
                            };
                        });
                        response(transformedData); // Populate autocomplete suggestions with label, value, and id
                    }
                });
            },
            minLength: 2, // Minimum characters before showing suggestions
            select: function(event, ui) {
                $('#selectedId').val(ui.item.id);
            }
        });
    })

    $(document).on("click", ".status-save-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
          url: "{{route('vendors.flowchartstatus.create')}}",
          type: "post",
          data: $('#status-create-form').serialize()
        }).done(function(response) {
          if (response.code = '200') {
            $('#loading-image').hide();
            $('#addPostman').modal('hide');
            toastr['success']('Status  Created successfully!!!', 'success');
            location.reload();
          } else {
            toastr['error'](response.message, 'error');
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
          toastr['error'](errObj.message, 'error');
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

    $(document).on("click", ".delete-status-fc",function(e){
        // $('#btn-save').attr("disabled", "disabled");
        e.preventDefault();
        let _token = $("input[name=_token]").val();
        let status_id =  $(this).data('id');
        if(status_id!=""){
            if(confirm("Are you sure you want to delete record?")) {
                $.ajax({
                    url:"{{ route('delete.flowchart-status') }}",
                    type:"post",
                    data:{
                        id:status_id,
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

    $(document).on('click', '.add-note-flowchart', function() {
        var vendor_id = $(this).attr('data-id');
        var flow_chart_id = $(this).attr('data-flow_chart_id');

        $("#notes_vendor_id").val(vendor_id);
        $("#notes_flow_chart_id").val(flow_chart_id);

        $.ajax({
            url: "{{route('vendors.getflowchartnotes')}}",
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
                                    <td> <input type="text" value="`+v.notes+`" style="width:100%" id="note_`+v.id+`"> </td>
                                    <td> 
                                        <button type="button"  class="btn btn-edit-notes btn-sm p-0" data-id="`+v.id+`">
                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                        </button>
                                        <button type="button"  class="btn btn-copy-notes btn-sm p-0" data-id="`+v.notes+`">
                                            <i class="fa fa-clone" aria-hidden="true"></i>
                                        </button>
                                        <button type="button"  class="btn btn-delete-notes btn-sm p-0" data-id="`+v.id+`">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>`;
                    });
                    $("#fchartnotes-histories-list").find(".fchartnotes-histories-list-view").html(html);
                    $("#fchartnotes-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on('click', '.btn-edit-notes', function() {
        var note_id = $(this).attr('data-id');

        var notes = $("#note_"+note_id).val();

        if(notes==''){
            alert('Please add notes.')
            return false;
        }

        if(note_id>0){

            $.ajax({
                url: '{{route('vendors.getflowchartupdatenotes')}}',
                type: 'POST',
                data: {
                    note_id: note_id,
                    notes: notes,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // dataType: 'json',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    toastr["success"]('Note successfully updated.');
                },
                error: function () {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                }
            });
        } else {
            alert('Something went wrong. please try again.')
        }
    });

    $(document).on("click",".btn-copy-notes",function() {
      var password = $(this).data('id');
      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val(password).select();
      document.execCommand("copy");
      $temp.remove();
      alert("Copied!");
    });

    $(document).on('click', '.flowchart-history-show', function() {
        var vendor_id = $(this).attr('data-vendorid');
        var vendor_name = $(this).attr('data-vendorname');

        $('#vendornameTitle').text('');
        if(vendor_id>0){

            $('#vendornameTitle').text(' - '+vendor_name);

            $.ajax({
                url: '{{route('vendors.flowchartssearch')}}',
                type: 'POST',
                data: {
                    vendor_id: vendor_id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // dataType: 'json',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    $("#vendor-flowchart-history-model").find(".show-vendor-history-flowchart-list").html(response);
                    $("#vendor-flowchart-history-model").modal("show");
                },
                error: function () {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                }
            });
        } else {
            alert('Please select vendor.')
        }
    });

    $(document).on("click", ".btn-delete-notes",function(e){        
        e.preventDefault();
        let _token = $("input[name=_token]").val();
        let note_id =  $(this).data('id');
        if(note_id!=""){
            if(confirm("Are you sure you want to delete record?")) {
                $.ajax({
                    url:"{{ route('delete.flowchart-notes') }}",
                    type:"post",
                    data:{
                        id:note_id,
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
</script>
@endsection