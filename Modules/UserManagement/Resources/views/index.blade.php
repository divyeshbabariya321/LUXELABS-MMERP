<!--jQuery-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
{{-- <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet"> --}}


<!--prettyTag JS-->
{{-- <script src="js/jquery.prettytag.js"></script> --}}

<!--prettyTag CSS-->
{{-- <link rel="stylesheet" href="css/prertytag.css" /> --}}
{{-- <link rel="stylesheet" href="../fancymetags.css"> --}}
@extends('layouts.app')
@section('favicon', 'user-management.png')

@section('large_content')
<style type="text/css">
    .preview-category input.form-control {
        width: auto;
    }
</style>

<style>
    #chat-list-history {
        z-index: 9999;
    }

    #payment-table_filter {
        text-align: right;
    }

    .activity-container {
        margin-top: 3px;
    }

    .elastic {
        transition: height 0.5s;
    }

    .activity-table-wrapper {
        position: absolute;
        width: calc(100% - 50px);
        max-height: 500px;
        overflow-y: auto;
    }

    .dropdown-wrapper {
        position: relative;
    }

    .dropdown-wrapper.hidden {
        display: none;
    }

    .dropdown-wrapper>ul {
        margin: 0px;
        padding: 5px;
        list-style: none;
        position: absolute;
        width: 100%;
        box-shadow: 3px 3px 10px 0px;
        background: white;
    }

    .dropdown input {
        width: calc(100% - 120px);
        line-height: 2;
        outline: none;
        border: none;
    }

    .payment-method-option:hover {
        background: #d4d4d4;
    }

    .payment-method-option.selected {
        font-weight: bold;
    }

    .payment-dropdown-header {
        padding: 2px;
        border: 1px solid #e0e0e0;
        border-radius: 3px;
    }

    .payment-overlay {
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0px;
    }

    .error {
        color: red;
        font-size: 10pt;
    }

    .pd-5 {
        padding: 5px;
    }

    .feedback_model .modal-dialog {
        max-width: 1024px;
        width: 100%;
    }

    .quick_feedback,
    #feedback-status {
        border: 1px solid #ddd;
        border-radius: 4px;
        height: 35px;
        outline: none;
    }

    .quick_feedback:focus,
    #feedback-status:focus {
        outline: none;
    }

    .communication-td input {
        width: calc(100% - 25px) !important;
    }

    .communication-td button {
        width: 20px;
    }
</style>
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" /> --}}
@include('partials.flash_messages')
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">{{ $title }} <span class="count-text"></span></h2>
    </div>
</div>
<div class="row" id="common-page-layout">
    <input type="hidden" name="page_no" class="page_no" />
    <div class="col-lg-12 margin-tb">
        <div class="row">
            <div class="col">
                <div class="" style="margin-bottom:10px;">
                    <div class="row">
                        <form class="form-inline message-search-handler" method="post">
                            <div class="col">
                                <div class="form-group">
                                    <a class="btn btn-secondary addToWhitelist" href="javascript:;" onclick="whitelistValueBulkUpdate(1)">Add to Whitelist</a>
                                </div>
                                <div class="form-group">
                                    <a class="btn btn-secondary removeFromWhitelist" href="javascript:;" onclick="whitelistValueBulkUpdate(0)">Remove selected From Whitelist</a>
                                </div>
                                <div class="form-group">
                                    <a class="btn btn-secondary removeFromWhitelist" href="javascript:;" onclick="whitelistValueBulkUpdate(2)">Remove All From Whitelist</a>
                                </div>
                                <div class="form-group">
                                {{ html()->text('keyword', request('keyword'))->class('form-control data-keyword')->placeholder('Enter keyword')->attribute('list', 'name-lists') }}
                                    <datalist id="name-lists">
                                        @foreach ($userLists as $key => $val )
                                            <option value="{{$val}}">
                                        @endforeach
                                    </datalist>
                                </div>
                                <div class="form-group">
                                    <select name="is_active" class="form-control" placholder="Active:">
                                        <option value="0" {{ request('is_active') == 0 ? 'selected' : '' }}>All</option>
                                        <option value="1" {{ request('is_active') == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="2" {{ request('is_active') == 2 ? 'selected' : '' }}>In active
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="is_whitelisted" class="form-control" placholder="Whitelist:">
                                        <option value="0" {{ request('is_whitelisted') == 0 ? 'selected' : '' }}>All</option>
                                        <option value="1" {{ request('is_whitelisted') == 1 ? 'selected' : '' }}>Whitelisted
                                        </option>
                                        <option value="2" {{ request('is_whitelisted') == 2 ? 'selected' : '' }}>Not Whitelisted
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group pl-3">
                                    <label for="button">&nbsp;</label>
                                    <button style="display: inline-block;width: 10%;margin-top: -16px;" class="btn btn-sm btn-image btn-search-action" title="Search">
                                        <img src="/images/search.png" style="cursor: default;">
                                    </button>
                                </div>
                            </div>
                        </form>
                        @if (auth()->user()->isAdmin())

                        <img src="http://cdn.onlinewebfonts.com/svg/img_108143.png" class="mt-3 permission-request" style="width:20px; height:20px;" alt="Permission request" title="Permission request" >

                        <img src="https://e7.pngegg.com/pngimages/287/966/png-clipart-computer-icons-erp-icon-computer-network-business.png" class="mt-2 ml-4 erp-request" style="width:35px; height:25px;" alt="ERP IPs" title="ERP IPs">
                        <img src="https://p1.hiclipart.com/preview/160/386/395/cloud-symbol-cloud-computing-business-telephone-system-itc-technology-workflow-ip-pbx-vmware-png-clipart.jpg" class="mt-2 ml-4 system-request" style="width:35px; height:25px;" data-toggle="modal" data-target="#system-request" title="System Request" >
                        <img src="https://www.kindpng.com/picc/m/391-3916045_task-management-task-management-icon-hd-png-download.png" class="mt-2 ml-4 today-history" style="width:31px; height:25px;" alt="All user task" title="All user task" >

                        <button type="button" class="btn btn-lg" title="View Planned Users With Availabilities" onclick="funPlannedUserAndAvailabilityList()" style="padding-top: 0px;" >
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>

                        <a href="{{ route('user-management.database-logs') }}" class="btn btn-lg" title="logs History" style=" padding-left: 0px;"><i class="fa fa-list" aria-hidden="true"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 margin-tb" id="page-view-result">

        </div>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
              50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document" id="modalDialog" style="width:800px !important">
    </div>
</div>

{{-- //feeback model --}}

<div id="exampleModal123" class="modal fade feedback_model" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content" id="success">
            <div class="modal-header">
                <h5 class="modal-title">User Feedback</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 p-0" id="permission-request">
                    <table class="table table-bordered" id="feedback_tbl">
                        <thead>
                            <tr>
                                <th width="20%">Category</th>
                                <th width="25%">Admin Response</th>
                                <th width="25%">User Response</th>
                                <th width="20%">Status</th>
                                <th width="10%">History</th>
                            </tr>
                            @if (Auth::user()->isAdmin())
                            <tr>
                                <td>
                                    <input type="text" style="width:calc(100% - 25px)" class="quick_feedback" id="addcategory" name="category">
                                    <button style="width: 20px" type="button" class="btn btn-image add-feedback" id="btn-save"><img src="/images/add.png" style="cursor: nwse-resize; width: 0px;"></button>
                                </td>
                                <td></td>
                                <td></td>
                                <td><input type="textbox" style="width:calc(100% - 25px)" id="feedback-status">
                                    <button style="width: 20px" type="button" class="btn btn-image user-feedback-status"><img src="/images/add.png" style="cursor: nwse-resize; width: 0px;"></button>
                                </td>
                                <td></td>
                            </tr>
                            @endif
                        </thead>

                        <tbody class="show-list-records user-feedback-data">

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

<div id="status-history" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status History</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12" id="permission-request">
                    <table class="table fixed_header">
                        <thead>
                            <tr>
                                <th>Index</th>
                                <th>Actioned By</th>
                                <th>Change Status To</th>
                            </tr>
                        </thead>
                        <tbody class="show-list-records">
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

<div id="erp-request" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Login IPs</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12" id="permission-request">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User Email</th>
                                <th>IP</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="show-list-records">
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



<div id="user-task-activity" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User task activity ( Last week )</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12" id="user-task-activity">
                    <table class="table fixed_header">
                        <thead>
                            <tr>
                                <th>User name</th>
                                <th>Task</th>
                                <th>Tracked time</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="show-list-records">
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

<div id="time_history_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Estimated Time History</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" id="approve-time-btn" method="POST">
                @csrf
                <input type="hidden" name="hidden_task_type" id="hidden_task_type">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="developer_task_id" id="developer_task_id">

                        <div class="col-md-12" id="time_history_div">
                            <table class="table table table-bordered" style="font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Old Value</th>
                                        <th>New Value</th>
                                        <th>Updated by</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    @if (auth()->user()->isReviwerLikeAdmin())
                    <button type="submit" class="btn btn-secondary">Confirm</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<div id="chat-list-history" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Communication</h4>
                <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
            </div>
            <div class="modal-body" style="background-color: #999999;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Feedback Modal -->

<div id="today-history" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Today task history</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="time_history_div">
                        <table class="table table table-bordered" style="font-size: 14px;">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Task id</th>
                                    <th>Description</th>
                                    <th>From time</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody class="show-list-records">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="logMessageModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task description</h4>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<div id="logMessageModelTask" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task Heading</h4>
            </div>
            <div class="modal-body">
                <p class="task-head" style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

@include('common.commonEmailModal')
@include("usermanagement::templates.list-template", array('servers' => $servers))
@include("usermanagement::templates.create-solution-template")
@include("usermanagement::templates.load-communication-history")
@include("usermanagement::templates.add-role")
@include("usermanagement::templates.add-permission")
@include("usermanagement::templates.load-task-history")
@include("usermanagement::templates.add-team")
@include("usermanagement::templates.edit-team")
@include("usermanagement::templates.add-time")
@include("usermanagement::templates.user-avaibility")
@include("usermanagement::templates.show-task-hours")
@include("usermanagement::templates.show-user-details")

@push('modals')
@include("user-availability.modal-list")

<div id="modalPlannedUserAndAvailabilityList" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Planned users and their availabilities</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endpush

<script>
    var urlUserManagementUpdateFlagForTaskPlan = "{!! route('user-management.update.flag-for-task-plan') !!}";
</script>
<script type="text/javascript" src="{{asset('js/jsrender.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/jquery.validate.min.js')}}"></script>
<script src="{{asset('/js/jquery-ui.js')}}"></script>
<script type="text/javascript" src="{{asset('js/common-helper.js')}}"></script>
<script type="text/javascript" src="{{asset('js/user-management-list.js')}}?v=1"></script>


<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
{{-- <script src="{{ asset('js/common-email-send.js') }}"> --}}
{{-- </script> --}}
{{-- <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pnp-sp-taxonomy/1.3.11/sp-taxonomy.es5.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.js" integrity="sha512-wTIaZJCW/mkalkyQnuSiBodnM5SRT8tXJ3LkIUA/3vBJ01vWe5Ene7Fynicupjt4xqxZKXA97VgNBHvIf5WTvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<script>
    // for feedback model
    $(document).on("click", ".feedback_btn", function() {
        $('#exampleModal123').modal('show');

    });

    // for user admin chat hisrty
    $(document).on("click", "#histry", function() {
        alert('helloooo');
    });

   
    $('.select-multiple').select2({
        width: '100%'
    });
    $(document).on("change", ".number .whatsapp_number", function(e) {        
        e.preventDefault();
        $("#loading-image").show();
        $.ajax({
            type: "POST",
            url: "{{ route('user.changewhatsapp') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                user_id: $(this).attr('data-user-id'),
                whatsapp_number: $(this).val()
            },
            success: function(response) {
                $("#loading-image").hide();
            }
        });
    });

    $('#due-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
    });

    $(document).on("click", ".today-history", function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/user-management/today-task-history',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#loading-image").hide();
                if (result.code == 200) {
                    var t = '';
                    $.each(result.data[0], function(k, v) {
                        t += `<tr><td>` + v.user_name + `</td>`;
                        t += `<td>` + v.devtaskId + `</td>`;
                        t += `<td>` + v.task + `</td>`;
                        t += `<td>` + v.date + `</td>`;
                        t += `<td>` + v.tracked + `</td></tr>`;
                    });
                    if (t == '') {
                        t = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
                    }
                    $("#today-history").find(".show-list-records").html(t);
                    $("#today-history").modal("show");
                } else {

                    toastr["error"]('No record found');
                }
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });


    $(document).on("click", ".task-activity", function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/user-management/task-activity',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#loading-image").hide();
                if (result.code == 200) {
                    var t = '';
                    $.each(result.data, function(k, v) {
                        t += `<tr><td>` + v.user_name + `</td>`;
                        t += `<td>` + v.task + `</td>`;
                        t += `<td>` + v.tracked + `</td>`;
                        t += `<td>` + v.date + `</td></tr>`;
                    });
                    if (t == '') {
                        t = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
                    }
                    $("#user-task-activity").find(".show-list-records").html(t);
                    $("#user-task-activity").modal("show");
                } else {
                    toastr["error"]('No record found');
                }
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });

    $(document).on("click", ".erp-request", function(e) {
        e.preventDefault();
        $.ajax({
            url: '/users/loginips',
            type: 'GET',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#loading-image").hide();
                if (result.code == 200) {
                    var t = '';
                    $.each(result.data, function(k, v) {
                        button = status = '';
                        if (v.is_active) {
                            status = 'Active';
                            button =
                                '<button type="button" class="btn btn-warning ml-3 statusChange" data-status="Inactive" data-id="' +
                                v.id + '">Inactive</button>';
                        } else {
                            status = 'Inactive';
                            button =
                                '<button type="button" class="btn btn-success ml-3 statusChange" data-status="Active" data-id="' +
                                v.id + '">Active</button>';
                        }
                        t += `<tr><td>` + v.created_at + `</td>`;
                        t += `<td>` + v.email + `</td>`;
                        t += `<td>` + v.ip + `</td>`;
                        t += `<td>` + status + `</td>`;
                        t += `<td>` + button + `</td>`;
                    });
                    if (t == '') {
                        t = '<tr><td colspan="5" class="text-center">No data found</td></tr>';
                    }
                }
                $("#erp-request").find(".show-list-records").html(t);
                $("#erp-request").modal("show");
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });



    $('.due-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
    });
    page.init({
        bodyView: $("#common-page-layout"),
        baseUrl: "<?php echo url('/'); ?>"
    });

    function editUser(id) {
        $.ajax({
            url: "/user-management/edit/" + id,
            type: "get"
        }).done(function(response) {
            $('.common-modal').modal('show');
            $(".modal-dialog").html(response);
        }).fail(function(errObj) {
            $('.common-modal').modal('hide');
        });
    }

    function payuser(id) {
        $.ajax({
            url: "/user-management/paymentInfo/" + id,
            type: "get"
        }).done(function(response) {
            if (response.code == 500) {
                toastr['error'](response.message, 'error');
            } else {
                $('.common-modal').modal('show');
                $(".modal-dialog").html(response);
            }
        }).fail(function(errObj) {
            $('.common-modal').modal('hide');
        });
    }

    $(".common-modal").on("click", ".open-payment-method", function() {
        if ($('.common-modal #permission-from').hasClass('hidden')) {
            $('.common-modal #permission-from').removeClass('hidden');
        } else {
            $('.common-modal #permission-from').addClass('hidden');
        }
    });

    $(".common-modal").on("click", ".add-payment-method", function() {
        var name = $('.common-modal #payment-method-input').val();
        if (!name) {
            return;
        }

        $.ajax({
            url: "/user-management/add-new-method",
            type: "post",
            data: {
                name: name,
                "_token": "{{ csrf_token() }}"
            }
        }).done(function(response) {
            $(".common-modal #payment_method").html(response);
            $('.common-modal #permission-from').addClass('hidden');
            $('.common-modal #payment-method-input').val('');
        }).fail(function(errObj) {});
    });

    let paymentMethods;

    function makePayment(userId, defaultMethod = null) {
        $('input[name="user_id"]').val(userId);

        if (defaultMethod) {
            $('#payment_method').val(defaultMethod);
        }
        filterMethods('');
        $('.dropdown input').val('');

        $("#paymentModal").modal();
    }

    function setPaymentMethods() {
        paymentMethods = $('.payment-method-option');
    }

    $(document).ready(function() {

        adjustHeight();

        $('#payment-table').DataTable({
            "ordering": true,
            "info": false
        });

        setPaymentMethods();

        $('#payment-dropdown-wrapper').click(function() {
            event.stopPropagation();
        })

        $("#paymentModal").click(function() {
            closeDropdown();
        })
    });

    function adjustHeight() {
        $('.activity-container').each(function(index, element) {
            const childElement = $($(element).children()[0]);
            $(element).attr('data-expanded-height', childElement.height());
            $(element).height(0);
            childElement.height(0);

            setTimeout(
                function() {
                    $(element).addClass('elastic');
                    childElement.addClass('elastic');
                    $('#payment-table').css('visibility', 'visible');
                },
                1
            )
        })
    }

    function toggle(id) {
        const expandableElement = $('#elastic-' + id);

        const isExpanded = expandableElement.attr('data-expanded') === 'true';


        if (isExpanded) {
            expandableElement.height(0);
            $($(expandableElement).children()[0]).height(0);
            expandableElement.attr('data-expanded', 'false');
        } else {
            const expandedHeight = expandableElement.attr('data-expanded-height');
            expandableElement.height(expandedHeight);
            $($(expandableElement).children()[0]).height(expandedHeight);
            expandableElement.attr('data-expanded', 'true');
        }



    }

    function filterMethods(needle) {
        $('#payment-method-dropdown .payment-method-option').remove();

        let filteredElements = paymentMethods.filter(
            function(index, element) {
                const optionValue = $(element).text();
                return optionValue.toLowerCase().includes(needle.toLowerCase());
            }
        )

        filteredElements.each(function(index, element) {
            const value = $(element).text();
            if (value == $('#payment_method').val()) {
                $(element).addClass('selected');
            } else {
                $(element).removeClass('selected');
            }
        });

        $('#payment-method-dropdown').append(filteredElements);
    }

    function selectOption(element) {
        selectOptionWithText($(element).text());
    }

    function selectOptionWithText(text) {
        $('#payment_method').val(text);
        closeDropdown();
    }

    function toggleDropdown() {
        if ($('#payment-dropdown-wrapper').hasClass('hidden')) {
            filterMethods('');
            $('.dropdown input').val('');
            $('#payment-dropdown-wrapper').css('display', 'block !important');
            $('#payment-dropdown-wrapper').removeClass('hidden');
        } else {
            $('#payment-dropdown-wrapper').addClass('hidden');
        }
        event.stopPropagation();
    }

    function closeDropdown() {
        $('#payment-dropdown-wrapper').addClass('hidden');
    }

    function addPaymentMethod() {

        const newPaymentMethod = $('#payment-method-input').val();

        let paymentExists = false;
        $('#payment-method-dropdown .payment-method-option')
            .each(function(index, element) {
                if ($(element).text() == newPaymentMethod) {
                    paymentExists = true;
                }
            });

        if (paymentExists) {
            alert('Payment method exits');
            return;
        } else if (!newPaymentMethod || newPaymentMethod.trim() == '') {
            alert('Payment method required');
            return;
        }

        filterMethods('');

        $('#payment-method-dropdown').append(
            '<li onclick="selectOption(this)" class="payment-method-option">' + newPaymentMethod + '</li>'
        );

        $('#payment_method').append(
            '<option value="' + newPaymentMethod + '">' + newPaymentMethod + '</option>'
        );

        setPaymentMethods();



        selectOptionWithText(newPaymentMethod);
        event.stopPropagation();
        event.preventDefault();

        return true;
    }

    $(document).on("change", ".quickComment", function(e) {

        var message = $(this).val();

        if ($.isNumeric(message) == false) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "/user-management/reply/add",
                dataType: "json",
                method: "POST",
                data: {
                    reply: message
                }
            }).done(function(data) {

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        }
        $(this).closest("td").find(".quick-message-field").val($(this).find("option:selected").text());

    });

    $(".select2-quick-reply").select2({
        tags: true
    });

    $(document).on("click", ".delete_quick_comment", function(e) {
        var deleteAuto = $(this).closest(".d-flex").find(".quickComment").find("option:selected").val();
        if (typeof deleteAuto != "undefined") {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "/user-management/reply/delete",
                dataType: "json",
                method: "GET",
                data: {
                    id: deleteAuto
                }
            }).done(function(data) {
                if (data.code == 200) {
                    $(".quickComment").empty();
                    $.each(data.data, function(k, v) {
                        $(".quickComment").append("<option value='" + k + "'>" + v +
                            "</option>");
                    });
                    $(".quickComment").select2({
                        tags: true
                    });
                }

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        }
    });

    $(document).on('click', '.statusChange', function(event) {
        event.preventDefault();
        $.ajax({
            type: "post",
            url: "{{ action([\App\Http\Controllers\UserController::class, 'statusChange']) }}",
            data: {
                _token: "{{ csrf_token() }}",
                status: $(this).attr('data-status'),
                id: $(this).attr('data-id')
            },
            beforeSend: function() {
                $(this).attr('disabled', true);
                // $(element).text('Approving...');
            }
        }).done(function(data) {
            toastr["success"]("Status updated!", "Message")
            window.location.reload();
        }).fail(function(response) {
            alert(response.responseJSON.message);
            toastr["error"](error.responseJSON.message);
        });
    });

    $(document).on('click', '.send-message', function() {
        var thiss = $(this);
        var data = new FormData();
        var user_id = $(this).data('userid');
        var message = $(this).siblings('input').val();

        data.append("user_id", user_id);
        data.append("message", message);
        data.append("status", 1);

        if (message.length > 0) {
            if (!$(thiss).is(':disabled')) {
                $.ajax({
                    url: '/whatsapp/sendMessage/user',
                    type: 'POST',
                    "dataType": 'json', // what to expect back from the PHP script, if anything
                    "cache": false,
                    "contentType": false,
                    "processData": false,
                    "data": data,
                    beforeSend: function() {
                        $(thiss).attr('disabled', true);
                    }
                }).done(function(response) {
                    // thiss.closest('tr').find('.chat_messages').html(thiss.siblings('input').val());
                    $(thiss).siblings('input').val('');

                    $(thiss).attr('disabled', false);
                }).fail(function(errObj) {
                    $(thiss).attr('disabled', false);

                    toastr["error"]("Could not send message", "error");
                });
            }
        } else {
            toastr["error"]("Please enter a message first", "error");
        }
    });
    $(document).on("click", ".status-history", function(e) {
        $.ajax({
            url: '/user-management/' + $(this).data('id') + '/status-history',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#loading-image").hide();
                if (result.code == 200) {
                    var t = '';
                    var count = 0;
                    $.each(result.data, function(k, v) {
                        t += `<tr><td>` + parseInt(count + 1) + `</td>`;
                        t += `<td>` + v.status + `</td>`;
                        t += `<td>` + v.name + `</td>`;
                        t += `</tr>`;
                    });
                    if (t == '') {
                        t = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
                    }
                    $("#status-history").find(".show-list-records").html(t);
                    $("#status-history").modal("show");
                    //toastr["success"]('No record found');
                    //show-list-records
                    //
                } else {
                    toastr["error"]('No record found');
                }
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });
    $(document).on('click', '.flag-task', function() {
        var task_id = $(this).data('id');
        var task_type = $(this).data('task_type');
        var thiss = $(this);

        $.ajax({
            type: "POST",
            url: "{{ route('task.flag') }}",
            data: {
                _token: "{{ csrf_token() }}",
                task_id: task_id,
                task_type: task_type
            },
            beforeSend: function() {
                $(thiss).text('Flagging...');
            }
        }).done(function(response) {
            if (response.is_flagged == 1) {
                // var badge = $('<span class="badge badge-secondary">Flagged</span>');
                //
                // $(thiss).parent().append(badge);
                $(thiss).html('<img src="/images/flagged.png" />');
            } else {
                $(thiss).html('<img src="/images/unflagged.png" />');
                // $(thiss).parent().find('.badge').remove();
            }

            // $(thiss).remove();
        }).fail(function(response) {
            $(thiss).html('<img src="/images/unflagged.png" />');

            alert('Could not flag task!');
        });
    });
    $(document).on('click', '.task-send-message-btn', function() {
        var thiss = $(this);
        var data = new FormData();
        var task_id = $(this).data('id');
        // var message = $(this).siblings('input').val();
        var message = $('#getMsg' + task_id).val();
        data.append("task_id", task_id);
        data.append("message", message);
        data.append("status", 1);

        if (message.length > 0) {
            if (!$(thiss).is(':disabled')) {
                $.ajax({
                    url: '/whatsapp/sendMessage/task',
                    type: 'POST',
                    "dataType": 'json', // what to expect back from the PHP script, if anything
                    "cache": false,
                    "contentType": false,
                    "processData": false,
                    "data": data,
                    beforeSend: function() {
                        $(thiss).attr('disabled', true);
                    }
                }).done(function(response) {
                    $(thiss).siblings('input').val('');
                    $('#getMsg' + task_id).val('');

                    // if (cached_suggestions) {
                    //     suggestions = JSON.parse(cached_suggestions);

                    //     if (suggestions.length == 10) {
                    //         suggestions.push(message);
                    //         suggestions.splice(0, 1);
                    //     } else {
                    //         suggestions.push(message);
                    //     }
                    //     localStorage['message_suggestions'] = JSON.stringify(suggestions);
                    //     cached_suggestions = localStorage['message_suggestions'];

                    // } else {
                    //     suggestions.push(message);
                    //     localStorage['message_suggestions'] = JSON.stringify(suggestions);
                    //     cached_suggestions = localStorage['message_suggestions'];
                    // }

                    // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
                    //   .done(function( data ) {
                    //
                    //   }).fail(function(response) {
                    //     alert(response.responseJSON.message);
                    //   });

                    $(thiss).attr('disabled', false);
                    toastr["success"]('Message sent successfully.');
                }).fail(function(errObj) {
                    $(thiss).attr('disabled', false);
                    toastr["error"]('An erro occured! please try again later.');
                    toastr["error"]("Could not send message", "error");
                });
            }
        } else {
            toastr["error"]("Please enter a message first", "error");
        }
    });
    //
    $(document).on('click', '.load-task-modal', function() {
        setTimeout(function() {
            $('.task-modal-userid').attr('data-id', $(this).data('id'));
            $.each($('.data-status'), function(i, item) {
                var value = $(this).data('status');
                $.each($(this).children('option'), function(is, items) {
                    if ($(this).attr('value') == value || $(this).data('id') == value) {
                        $(this).attr('selected', 'selected');
                    }
                })
            });
        }, 3000);

    });

    $(document).on('click', '.statusChange', function(event) {
        event.preventDefault();
        $.ajax({
            type: "post",
            url: "{{ action([\App\Http\Controllers\UserController::class, 'statusChange']) }}",
            data: {
                _token: "{{ csrf_token() }}",
                status: $(this).attr('data-status'),
                id: $(this).attr('data-id')
            },
            beforeSend: function() {
                $(this).attr('disabled', true);
                // $(element).text('Approving...');
            }
        }).done(function(data) {
            toastr["success"]("Status updated!", "Message")
            window.location.reload();
        }).fail(function(response) {
            alert(response.responseJSON.message);
            toastr["error"](error.responseJSON.message);
        });
    });

    $(document).on('click', '.user-feedback-status', function() {
        var status = $('#feedback-status').val();
        $('.user_feedback_status').text('');

        $.ajax({
            type: "get",
            url: '{{ route("user.feedback-status") }}',
            data: {
                'status': status
            },
            success: function(response) {
                if (response.status == true) {
                    $('#feedback-status').val('');
                    var all_status = response.feedback_status;
                    var Select = '<option value="">Select</option>'
                    $('.user_feedback_status').append(Select);

                    for (let i = 0; i < all_status.length; i++) {
                        var html = '<option value="' + all_status[i].id + '">' + all_status[i].status + '</option>';
                        $('.user_feedback_status').append(html);
                    }
                }
            }
        });
    })

   

    $(document).on('change', '.user_feedback_status', function() {
        var status_id = $(this).val();
        var user_id = $(this).closest('tr').data('user_id');
        var cat_id = $(this).closest('tr').data('cat_id');
        $.ajax({
            type: "get",
            url: '{{ route("user.feedback-status") }}',

            data: {
                status_id: status_id,
                user_id: user_id,
                cat_id: cat_id
            },
            success: function(response) {
                toastr.success(response.message);
            }
        });
    })

    $(document).on('click', '.user-feedback-modal', function() {
        var user_id = $(this).data('user_id');
        $('.user-feedback-data').data('user_id', user_id);
        $('#btn-save').data('user_id', user_id);

        $('.user-feedback-data').text('');

        $.ajax({
            type: "get",
            url: '{{ route("user.feedback-table-data") }}',
            data: {
                'user_id': user_id
            },
            success: function(response) {
                $('.user-feedback-data').append(response);

            }
        });

    })

    $(document).on("click", "#btn-save", function(e) {
        // $('#btn-save').attr("disabled", "disabled");
        e.preventDefault();
        var category = $('#addcategory').val();
        var user_id = $(this).data('user_id');
        if (category != "") {
            $.ajax({
                url: "{{ route('user.feedback-category') }}",
                type: "get",
                data: {
                    category: category,
                    user_id: user_id,
                },
                cashe: false,
                success: function(response) {
                    if (response.message) {
                        toastr.error(response.message);
                    } else {
                        $('#addcategory').val('');
                        $(document).find('.user-feedback-data').append(response);
                    }
                }
            });
        } else {
            toastr["error"]("error", "error");
        }
    });

    $(document).on('click', '.send-message-open', function(event) {
        var feedback_status_id = $(this).parents('tr').find('.user_feedback_status').val();
        var textBox = $(this).closest(".communication-td").find(".send-message-textbox");
        let user_id = textBox.attr('data-id');
        let message = textBox.val();
        var feedback_cat_id = $(this).data('feedback_cat_id');
        var $this = $(this);
        if (message == '') {
            return;
        }

        let self = textBox;

        $.ajax({
            url: "{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'user-feedback')}}",
            type: 'POST',
            data: {
                "feedback_status_id": feedback_status_id,
                "feedback_cat_id": feedback_cat_id,
                "user_id": user_id,
                "message": message,
                "_token": "{{csrf_token()}}",
                "status": 2
            },
            dataType: "json",
            success: function(response) {
                toastr["success"]("Message sent successfully!", "Message");
                $('#message_list_' + user_id).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                $(self).removeAttr('disabled');
                $(self).val('');
                var msg = response.message.message;
                if (msg.length > 20) {
                    var msg = msg.substring(1, 20) + '...';
                    $this.siblings('.latest_message').text(msg);
                } else {
                    $this.siblings('.latest_message').text(msg);
                }
            },
            beforeSend: function() {
                $(self).attr('disabled', true);
            },
            error: function() {
                alert('There was an error sending the message...');
                
                $(self).removeAttr('disabled', true);
            }
        });
    });

    function funPlannedUserAndAvailabilityList() {
        siteLoader(1);
        let mdl = jQuery('#modalPlannedUserAndAvailabilityList');
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('user-management.planned-user-and-availability') }}",
            type: 'GET',
            data: {}
        }).done(function(response) {
            siteLoader(0);
            mdl.find('.modal-body').html(response.data);
            mdl.modal('show');
        }).fail(function(err) {
            siteLoader(0);
            siteErrorAlert(err);
        });
    }
    //Select all permission
    $(document).on('click', '#selectAll', function(event) {
        $("input[name='permissions[]']").prop('checked', $(this).prop('checked'));
    });
    function whitelistValueBulkUpdate(action)
    {
        event.preventDefault();
        var users = [];
        var action = action;
        var actionMessage = action==1?'Added To':'Removed From';
        if(action != 2)
        {
            $(".bulk_user_action").each(function () {
            if ($(this).prop("checked") == true) {
                users.push($(this).val());
            }
            });
            if (users.length == 0) {
                alert('Please select User');
                return false;
            }
        }
        else{
            if(confirm('Are you sure you want to perform this action?')==false)
            {
                return false;
            }
        }
        $.ajax({
            type: "post",
            url: "{{ route('user-management.whitelist-bulk-update') }}",
            data: {
                _token: "{{ csrf_token() }}",
                users: users,
                action: action,
            },
            beforeSend: function() {
                $(this).attr('disabled', true);
            }
        }).done(function(data) {
            toastr["success"](actionMessage+" Whitelist!", "Message")
            window.location.reload();
        }).fail(function(response) {
            alert(response.responseJSON.message);
            toastr["error"](error.responseJSON.message);
        });
    }
    
    //});
    $( document ).ready(function() {
        $(document).on('click', '.expand-row,.expand-row-msg', function () {
		    $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
		});
    });
	
</script>

@endsection