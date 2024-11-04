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
            Vendor Rating Question Answer ({{ $totalVendor }})
            <div style="float: right;">
                <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#rqadatatablecolumnvisibilityList">Column Visiblity</button>

                <a class="btn btn-secondary btn-xs" style="color:white;" data-toggle="modal" data-target="#newRQuestionModal">Create Question</a>

                <button type="button" class="btn btn-secondary btn-xs" style="color:white;" data-toggle="modal" data-target="#status-create">Add Status</button>

                <button class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>

                <button class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#setRQuestionSorting"> Set Question Sorting</button>
            </div>
        </h2>
    </div>

    <div class="col-12">
        <form class="form-inline" action="{{ route('vendors.rating.question-answer') }}" method="GET">

            <div class="form-group mr-3">
                <strong>Select Vendor :</strong></br>
                <input type="text" name="term" id="searchInput" value="{{ request('term') }}" class="form-control" placeholder="Enter Vendor Name">
                <input type="hidden" id="selectedId" name="selectedId" value="{{ request('selectedId') }}">
            </div>

            <div class="form-group mr-3">
                <strong>Select Vendor Category :</strong></br>
                <?php
                $category_post = request('category');
                ?>
                <select class="form-control" name="category" id="category">
                    <option value="">Category</option>
                    <?php
                    foreach ($vendor_categories as $row_cate) { ?>
                        <option value="<?php echo $row_cate->id; ?>" <?php if ($category_post == $row_cate->id) {
                            echo 'selected';
                        } ?>><?php echo $row_cate->title; ?></option>
                    <?php }
                    ?>
                </select>
            </div>

            <div class="form-group col-md-2 pr-0 pt-20" style=" padding-top: 20px;">
                <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                <a href="{{route('vendors.rating.question-answer')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </form>
    </div>
</div>

<div id="setRQuestionSorting" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Set Flow Chart Sorting</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('vendors.rqa-sort-order') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Question</b></td>
                            <td class="text-center"><b>Sorting</b></td>
                        </tr>
                        <?php
                        foreach ($rating_questions as $vendorquestion) { ?>
                        <tr>
                            <td><?php echo $vendorquestion->question; ?></td>
                            
                            <td style="text-align:center;">
                                <input type="number" name="sorting[<?php echo $vendorquestion->id; ?>]" class="form-control" value="<?php echo $vendorquestion->sorting; ?>">
                            </td>                              
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>

    </div>
</div>

@include('partials.flash_messages')
@include("vendors.partials.column-visibility-modal-rqa")
@include('vendors.partials.add-question')

<div class="infinite-scroll mt-5" style="overflow-y: auto">
    <table class="table table-bordered" id="vendor-table">
        <thead>
            <tr>
                @if(!empty($dynamicColumnsToShowVendorsrqa))
                    @if (!in_array('Vendor', $dynamicColumnsToShowVendorsrqa))
                        <th width="10%">Vendor</th>
                    @endif
                    @if (!in_array('Category', $dynamicColumnsToShowVendorsrqa))
                        <th width="10%">Category</th>
                    @endif
                    @if($rating_questions)
                        @foreach($rating_questions as $question_data)
                            @if (!in_array($question_data->id, $dynamicColumnsToShowVendorsrqa))
                                <th width="20%">
                                    {{$question_data->question}}

                                    @if (auth()->user()->isAdmin())
                                        <button style="padding-left: 10px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline delete-category" title="Delete Category" data-id="{{$question_data->id}}" ><i class="fa fa-trash"></i></button>
                                    @endif
                                </th>
                            @endif
                        @endforeach
                    @endif
                @else
                    <th width="10%">Vendor</th>
                    <th width="10%">Category</th>
                    @if($rating_questions)
                        @foreach($rating_questions as $question_data)
                            <th width="20%">
                                {{$question_data->question}}

                                @if (auth()->user()->isAdmin())
                                    <button style="padding-left: 10px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline delete-category" title="Delete Category" data-id="{{$question_data->id}}" ><i class="fa fa-trash"></i></button>
                                @endif
                            </th>
                        @endforeach
                    @endif
                @endif
            </tr>
        </thead>

        <tbody id="vendor-body">
            @include('vendors.partials.data-rqa')
        </tbody>
    </table>

    {!! $VendorQuestionAnswer->appends(Request::except('page'))->links() !!}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/zoom-meetings.js') }} "></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="{{asset('js/common-email-send.js')}}">
    //js for common mail
</script>

<script type="text/javascript">

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

    function saverAnswer(vendor_id, question_id){

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

    $(document).on("click", ".status-save-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
          url: "{{route('vendors.rqastatus.create')}}",
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

    $(document).on('click', '.status-history-show', function() {
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

    $(document).on('click', '.add-note-answer', function() {
        var vendor_id = $(this).attr('data-id');
        var question_id = $(this).attr('data-question_id');

        $("#notes_vendor_id").val(vendor_id);
        $("#notes_question_id").val(question_id);

        $.ajax({
            url: "{{route('vendors.getrquestionnotes')}}",
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
                    $("#vqarnotes-histories-list").find(".vqarnotes-histories-list-view").html(html);
                    $("#vqarnotes-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
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

    $(document).on("click", ".delete-category",function(e){
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

    $(document).on("click", ".delete-status-rq",function(e){
        // $('#btn-save').attr("disabled", "disabled");
        e.preventDefault();
        let _token = $("input[name=_token]").val();
        let status_id =  $(this).data('id');
        if(status_id!=""){
            if(confirm("Are you sure you want to delete status?")) {
                $.ajax({
                    url:"{{ route('delete.rqa-status') }}",
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

    $(document).on('click', '.btn-edit-notes', function() {
        var note_id = $(this).attr('data-id');

        var notes = $("#note_"+note_id).val();

        if(notes==''){
            alert('Please add notes.')
            return false;
        }

        if(note_id>0){

            $.ajax({
                url: '{{route('vendors.getrqaupdatenotes')}}',
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

    $(document).on("click", ".btn-delete-notes",function(e){        
        e.preventDefault();
        let _token = $("input[name=_token]").val();
        let note_id =  $(this).data('id');
        if(note_id!=""){
            if(confirm("Are you sure you want to delete record?")) {
                $.ajax({
                    url:"{{ route('delete.rqa-notes') }}",
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