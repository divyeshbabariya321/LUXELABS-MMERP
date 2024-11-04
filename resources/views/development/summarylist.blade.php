@extends('layouts.app')

@section('favicon', 'vendor.png')

@section('title', 'Quick Dev Task')

@section('styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
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
            width: 14%;
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

        .pd-rt {
            padding-right: 0px !important;
        }

        .table-bordered {
            border: 1px solid #ddd !important;
        }

        .status-selection .btn-group {
            padding: 0;
            width: 100%;
        }

        .status-selection .multiselect {
            width: 100%;
        }
        .select2-selection__rendered{overflow: inherit !important;}

        ul.select2-selection__rendered{
            display: block!important;
        }
    </style>
@endsection

@section('large_content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>

    @include('partials.flash_messages')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                Quick Dev Task (<span id="translation_count">{{ $issues->total() }}</span>)
                <div style="float: right;">
                    @if (auth()->user()->isReviwerLikeAdmin())
                        <a href="javascript:" class="btn custom-button" style="height: 35px;" id="newTaskModalBtn">Add New Dev Task </a>
                    @endif
                    @if (Auth::user()->isAdmin())
                        <button class="btn custom-button" style="color:white;" data-toggle="modal" data-target="#dscolumnvisibilityList"> Column Visiblity</button>

                        <a class="btn custom-button" href="{{ route('development.scrapper.index') }}" role="link"> Scrapper Verification Data </a>
                    @endif
                </div>
            </h2>
        </div>
    
    {{-- <p style="font-size:16px;text-align:left;margin-top: 10px;font-weight:bold;">Quick Dev Task ({{$issues->total()}})</p>
    @if (auth()->user()->isReviwerLikeAdmin())
        <a href="javascript:" class="btn custom-button mt-3" style="height: 35px;" id="newTaskModalBtn">Add New Dev Task </a>
    @endif --}}
    </div>
    <div class="row" style="margin-top:13px ;margin-bottom:11px;float: left;">
        <div class="col-lg-12 margin-tb">
            <?php $base_url = URL::to('/'); ?>

            <div class="pull-left cls_filter_box">
                <form class="form-inline" action="{{ route('development.summarylist') }}" method="GET">

                    <div class="col-md-2 pd-sm">
                        <strong>Issue Id / Subject :</strong>
                        <input type="text" style="width:100%;" name="subject" id="subject_query"
                               placeholder="Issue Id / Subject" class="form-control"
                               value="{{ !empty(app('request')->input('subject')) ? app('request')->input('subject') : '' }}">
                    </div>
                    <div class="col-md-2 pd-sm pd-rt status-selection">
                        <strong>Select a Module :</strong>
                        <select class="form-control multiselect" name="module_id[]" id="module_id" multiple="multiple">
                            <option value>Select a Module</option>
                            @foreach ($modules as $module)
                                <option {{ $request->get('module') == $module->id ? 'selected' : '' }}
                                        value="{{ $module->id }}" {{ !empty(app('request')->input('module_id')) ? app('request')->input('module_id') ==  $module->id ? 'selected' : '' : '' }}>{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if (auth()->user()->isReviwerLikeAdmin())
                        <div class="col-md-2 pd-sm custom-select2">
                            <strong>Issue Id / Subject :</strong>
                            <select class="w-100 js-example-basic-multiple js-states"
                                id="assigned_to" multiple="multiple" name="assigned_to[]" data-placeholder="Search Users By Name">
                                @foreach ($users as $id => $user)
                                    <option @if($request->get('assigned_to')){{ (in_array($id, $request->get('assigned_to'))) ? 'selected' : '' }}@endif
                                            value="{{ $id }}">{{ $user }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 pd-sm custom-select2">
                            <strong>Lead By Name :</strong>
                            <select class="form-control" name="lead[]" id="lead" multiple="multiple" data-placeholder="Search Lead By Name">
                                <option value="">Lead</option>
                                @foreach ($users as $id => $user)
                                    <option @if($request->get('lead')){{ (in_array($id, $request->get('lead'))) ? 'selected' : '' }}@endif
                                            value="{{ $id }}">{{ $user }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-2 pd-sm">
                        <strong>Order Query :</strong>
                        <select name="order" id="order_query" class="form-control">
                            <option {{$request->get('order')== "" ? 'selected' : ''}} value="">Latest Communication</option>
                            <option {{$request->get('order')== "latest_task_first" ? 'selected' : ''}} value="latest_task_first">Latest Task First</option>
                            <option {{$request->get('order')== "priority" ? 'selected' : ''}} value="priority">Sort by priority</option>
                            <option {{$request->get('order')== "oldest_first" ? 'selected' : ''}} value="oldest_first">Olderst First</option>

                        </select>
                    </div>
                    <div class="col-md-2">
                        <strong>Filter By Messages :</strong>
                        <select class="form-control" name="unread_messages" id="unread_messages">
                            <option value="">Filter By Messages</option>
                            <option {{ $request->get('unread_messages') == "unread" ? 'selected' : '' }}
                                    value="unread">Unread
                            </option>
                            

                        </select>
                    </div>
                    <div class="col-md-2 pd-sm status-selection mt-2">
                        <strong>Task Status :</strong>
                        {{ html()->multiselect('task_status[]', $statusList, request()->get('task_status', array_values($statusList)))->class('form-control multiselect') }}

                    </div>

                    <div class="pd-sm mt-4">
                        <button type="button" class="btn btn-xs btn-secondary my-3" style="color:white;" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
                    </div>


                    <button type="submit" style="padding: 5px;margin-top:-1px;margin-left: 10px;" class="btn btn-image"
                            id="show"><img src="<?php echo $base_url; ?>/images/filter.png"/></button><br>
                            <a href="{{route('development.summarylist')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>

                </form>

            </div>
        </div>

    </div>


    <div class="infinite-scroll">
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped" style="table-layout:fixed;margin-bottom:0px;" id="task_Tables">
                <thead>
                <tr>
                    @if (Auth::user()->isAdmin())
                        @if(!empty($dynamicColumnsToShowDs))
                            @if (!in_array('ID', $dynamicColumnsToShowDs))
                                <th width="4%">ID</th>
                            @endif

                            @if (!in_array('MODULE', $dynamicColumnsToShowDs))
                                <th width="6%">MODULE</th>
                            @endif

                            @if (!in_array('Assigned To', $dynamicColumnsToShowDs))
                                <th width="8%">Assigned To</th>
                            @endif

                            @if (!in_array('Lead', $dynamicColumnsToShowDs))
                                <th width="8%">Lead</th>
                            @endif

                            @if (!in_array('Communication', $dynamicColumnsToShowDs))
                                <th width="15%">Communication</th>
                            @endif

                            @if (!in_array('Send To', $dynamicColumnsToShowDs))
                                <th width="8%">Send To</th>
                            @endif

                            @if (!in_array('Status', $dynamicColumnsToShowDs))
                                <th width="8%">Status</th>
                            @endif

                            @if (!in_array('Estimated Time', $dynamicColumnsToShowDs))
                                <th width="7%">Estimated Time</th>
                            @endif

                            @if (!in_array('Estimated Start Datetime', $dynamicColumnsToShowDs))
                                <th width="10%">Estimated Datetime</th>
                            @endif

                            @if (!in_array('Shortcuts', $dynamicColumnsToShowDs))
                                <th width="25%">Shortcuts</th>
                            @endif
                            @if (!in_array('Actions', $dynamicColumnsToShowDs))
                                <th width="5%">Actions</th>
                            @endif
                        @else
                            <th width="4%">ID</th>
                            <th width="6%">MODULE</th>
                            <th width="8%">Assigned To</th>
                            <th width="8%">Lead</th>
                            <th width="15%">Communication</th>
                            <th width="8%">Send To</th>
                            <th width="5%">Status</th>
                            <th width="7%">Estimated Time</th>
                            <th width="10%">Estimated Datetime</th>
                            <th width="25%">Shortcuts</th>
                            <th width="5%">Actions</th>
                        @endif
                    @else
                        <th width="4%">ID</th>
                        <th width="6%">MODULE</th>
                        <th width="8%">Assigned To</th>
                        <th width="8%">Lead</th>
                        <th width="15%">Communication</th>
                        <th width="8%">Send To</th>
                        <th width="5%">Status</th>
                        <th width="7%">Estimated Time</th>
                        <th width="10%">Estimated Datetime</th>
                        <th width="5%">Actions</th>
                    @endif
                </tr>
                </thead>

                <tbody id="vendor-body">

                @include("development.partials.summarydatas")


                </tbody>
            </table>
        </div>
        <img class="infinite-scroll-products-loader center-block" src="{{config('app.url')}}/images/loading.gif" alt="Loading..." style="display: none"/>
    </div>
    <?php echo $issues->appends(request()->except('page'))->links(); ?>

    @include("development.partials.upload-document-modal")
    @include("partials.plain-modal")
    @include("development.partials.modal-summary-task-color")
    @include("development.partials.column-visibility-modal")
    @include("development.partials.add-scrapper")
    @include("development.partials.timer-history")

    <div id="dev_scrapper_statistics" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="p-0 m-0">Scrapper Statistics <!-- <a href="javascript:void(0)" id="scrapper-history"><i class="fa fa-list" aria-hidden="true"></i></a> --></h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body" id="dev_scrapper_statistics_content">
                </div>
            </div>
        </div>
    </div>

    <div id="dev_scrapper_statistics_history" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="p-0 m-0">Scrapper Statistics History</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body" id="dev_scrapper_statistics_history_content">
                </div>
            </div>
        </div>
    </div>

    <div id="python-action-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Action History</h4>

                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="python-action-history_div">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Old Status</th>
                                    <th>New Status</th>
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
                </div>
            </div>
        </div>
    </div>

    <div id="status_quick_history_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Status History</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="row">
                    <div class="col-md-12" id="status_history_div">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Old Status</th>
                                <th>New Status</th>
                                <th>Updated by</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div id="preview-task-create-get-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Task Remark</h4>
                    <input type="text" name="remark_pop" class="form-control remark_pop" placeholder="Please enter remark" style="width: 200px;">
                    <button type="button" class="btn btn-default sub_remark" data-task_id="">Save</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width:1%;">ID</th>
                                <th style=" width: 12%">Update By</th>
                                <th style="word-break: break-all; width:12%">Remark</th>
                                <th style="width: 11%">Created at</th>
                                <th style="width: 11%">Action</th>
                            </tr>
                            </thead>
                            <tbody class="task-create-get-list-view">
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
    @include("development.partials.add-docs-permission")
    @include("development.partials.show-scrapper-logs-modal")

@endsection

@section('scripts')
    <script
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/zoom-meetings.js') }} "></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            applyDateTimePicker(jQuery('.cls-start-due-date'));
            
            $(".add-document-permission").click(function (e) { 
                e.preventDefault();
                let user_id = $(this).data("assigned_to")
                let task_id = $(this).data("task_id")
                let task_type = $(this).data("task_type")
                $("#addGoogleDocPermission").find('input[name=user_id]').val(user_id);
                $("#addGoogleDocPermission").find('input[name=task_id]').val(task_id);
                $("#addGoogleDocPermission").find('input[name=task_type]').val(task_type);
                $.ajax({
                    type: "GET",
                    url: "{{route('google-docs.list')}}",
                    data: "data",
                    success: function (response) {
                        if(response.status == true) {
                            $("#assignDocumentList").html('').select2({
                                width: "100%", 
                                data: response.docs,
                                placeholder: "Select"
                            });
                            $("#addGoogleDocPermission").modal("show");
                            $("#assignDocumentList").val(null).trigger('change');
                        } else {
                            toastr['error']('Error while fetching the data.', 'Error');
                        }
                    },
                    error: function(error) {
                        toastr['error']('Error while fetching the data.', 'Error');
                    }
                });
            });

        });
        function applyDateTimePicker(eles) {
            if (eles.length) {
                eles.datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss',
                    sideBySide: true,
                });
            }
        }

        $(document).ready(function () {
            $(".multiselect").multiselect({
                includeSelectAllOption: true,
                nonSelectedText: 'Please Select'
            });
        });

        function funGetTaskInformationModal() {
            return jQuery('#modalTaskInformationUpdates');
        }

        function funDevTaskInformationUpdatesTime(type,id) {
            if (type == 'start_date') {
                if (confirm('Are you sure, do you want to update?')) {
                    // siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('development.update.start-date') }}",
                        type: 'POST',
                        data: {
                            id: id,
                            value: $('input[name="start_dates'+id+'"]').val(),
                            estimatedEndDateTime: $('input[name="estimate_date'+id+'"]').val(),
                        }
                    }).done(function(res) {
                        // siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        // siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            } else if (type == 'estimate_date') {
                if (confirm('Are you sure, do you want to update?')) {
                    // siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('development.update.estimate-date') }}",
                        type: 'POST',
                        data: {
                            id: id,
                            value: $('input[name="estimate_date'+id+'"]').val(),
                            remark: mdl.find('input[name="remark"]').val(),
                        }
                    }).done(function(res) {
                        // siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        // siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            } else if (type == 'cost') {
                if (confirm('Are you sure, do you want to update?')) {
                    // siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('development.update.cost') }}",
                        type: 'POST',
                        data: {
                            id: currTaskInformationTaskId,
                            value: mdl.find('input[name="cost"]').val(),
                        }
                    }).done(function(res) {
                        // siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        // siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            } else if (type == 'estimate_minutes') {
                if (confirm('Are you sure, do you want to update?')) {
                    // siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('development.update.estimate-minutes') }}",
                        type: 'POST',
                        data: {
                            issue_id: id,
                            estimate_minutes: $('input[name="estimate_minutes'+id+'"]').val(),
                            remark: mdl.find('textarea[name="remark"]').val(),
                        }
                    }).done(function(res) {
                        // siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        // siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            } else if (type == 'lead_estimate_time') {
                if (confirm('Are you sure, do you want to update?')) {
                    // siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('development.update.lead-estimate-minutes') }}",
                        type: 'POST',
                        data: {
                            issue_id: currTaskInformationTaskId,
                            lead_estimate_time: mdl.find('input[name="lead_estimate_time"]').val(),
                            remark: mdl.find('input[name="lead_remark"]').val(),
                        }
                    }).done(function(res) {
                        // siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        // siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            }
        }


        $(document).on('click', '.expand-row-msg', function () {
            var id = $(this).data('id');
            console.log(id);
            var full = '.expand-row-msg .td-full-container-' + id;
            var mini = '.expand-row-msg .td-mini-container-' + id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });
        $(document).on('change', '.assign-master-user', function () {
            let id = $(this).attr('data-id');
            let userId = $(this).val();

            if (userId == '') {
                return;
            }

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'assignMasterUser']) }}",
                data: {
                    master_user_id: userId,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("Master User assigned successfully!", "Message")
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message, "Message")

                }
            });

        });
        $(document).on("click", ".set-remark", function (e) {
            $('.remark_pop').val("");
            var task_id = $(this).data('task_id');
            $('.sub_remark').attr("data-task_id", task_id);
        });


        $(document).on("click", ".set-remark, .sub_remark", function (e) {
            var thiss = $(this);
            var task_id = $(this).data('task_id');
            var remark = $('.remark_pop').val();
            $.ajax({
                type: "POST",
                url: "{{route('task.create.get.remark')}}",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    task_id: task_id,
                    remark: remark,
                    type: "Quick-dev-task",
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function (response) {
                if (response.code == 200) {
                    $("#loading-image").hide();
                    $("#preview-task-create-get-modal").modal("show");
                    $(".task-create-get-list-view").html(response.data);
                    $('.remark_pop').val("");
                    toastr['success'](response.message);
                } else {
                    $("#loading-image").hide();
                    $("#preview-task-create-get-modal").modal("show");
                    $(".task-create-get-list-view").html("");
                    toastr['error'](response.message);
                }

            }).fail(function (response) {
                $("#loading-image").hide();
                $("#preview-task-create-get-modal").modal("show");
                $(".task-create-get-list-view").html("");
                toastr['error'](response.message);
            });
        });
        $(document).on("click", ".copy_remark", function (e) {
            var thiss = $(this);
            var remark_text = thiss.data('remark_text');
            copyToClipboard(remark_text);
            /* Alert the copied text */
            toastr['success']("Copied the text: " + remark_text);
            //alert("Copied the text: " + remark_text);
        });

        function copyToClipboard(text) {
            var sampleTextarea = document.createElement("textarea");
            document.body.appendChild(sampleTextarea);
            sampleTextarea.value = text; //save main text in it
            sampleTextarea.select(); //select textarea contenrs
            document.execCommand("copy");
            document.body.removeChild(sampleTextarea);
        }


        $(document).on('change', '.set-responsible-user', function () {
            let id = $(this).attr('data-id');
            let userId = $(this).val();

            if (userId == '') {
                return;
            }

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'assignResponsibleUser']) }}",
                data: {
                    responsible_user_id: userId,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("User assigned successfully!", "Message")
                }
            });
        });

        $(document).on('change', '.assign-user', function () {
            let id = $(this).attr('data-id');
            let userId = $(this).val();

            if (userId == '') {
                return;
            }

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'assignUser']) }}",
                data: {
                    assigned_to: userId,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("User assigned successfully!", "Message")
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message, "Message")

                }
            });

        });

        $(document).on('change', '.task-module', function () {
            let id = $(this).attr('data-id');
            let moduleID = $(this).val();

            if (moduleID == '') {
                return;
            }

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'changeModule']) }}",
                data: {
                    module_id: moduleID,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("Module assigned successfully!", "Message")
                }
            });

        });

        $(document).on('change', '#time_doctor_account', function(){
            var account_id = $(this).val();
            var url = "{{ route('select2.time_doctor_projects_ajax') }}"+"?account_id="+account_id;
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $("#time_doctor_project").html(response);
                }
            });
        });
    </script>

    <script type="text/javascript">
        function resolveIssue(obj, task_id) {
            let id = task_id;
            let status = $(obj).val();
            let self = this;

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'resolveIssue']) }}",
                data: {
                    issue_id: id,
                    is_resolved: status
                },
                success: function () {
                    toastr["success"]("Status updatedd!", "Message")
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                }
            });
        }
    </script>


    <script type="text/javascript">
        var vendorToRemind = null;
        $('#vendor-search').select2({
            tags: true,
            width: '100%',
            ajax: {
                url: BASE_URL + '/vendor-search',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    for (var i in data) {
                        data[i].id = data[i].name ? data[i].name : data[i].text;
                    }
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search by name',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {

                if (customer.name) {
                    //return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                    return "<p>" + customer.name + "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.name,

        });

        var vendorToRemind = null;
        $('#vendor-phone-number').select2({
            tags: true,
            width: '100%',
            ajax: {
                url: BASE_URL + '/vendor-search-phone',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    for (var i in data) {
                        data[i].id = data[i].phone ? data[i].phone : data[i].text;
                    }
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search by phone number',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {

                if (customer.name) {
                    return "<p style='color:#BABABA;'>" + customer.phone + "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.phone,

        });

        $(document).on('click', '.emailToAllModal', function () {
            var select_vendor = [];
            $('.select_vendor').each(function () {
                if ($(this).prop("checked")) {
                    select_vendor.push($(this).val());
                }
            });

            if (select_vendor.length === 0) {
                alert('Please Select vendors!!');
                return false;
            }

            $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val(select_vendor.join());

            $('#emailToAllModal').modal("show");

        });

        $(document).on('click', '.send-email-to-vender', function () {
            $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val($(this).data('id'));
            $('#emailToAllModal').modal("show");
        });

        $(document).on('click', '.set-reminder', function () {
            let vendorId = $(this).data('id');
            let frequency = $(this).data('frequency');
            let message = $(this).data('reminder_message');
            let reminder_from = $(this).data('reminder_from');
            let reminder_last_reply = $(this).data('reminder_last_reply');

            $('#frequency').val(frequency);
            $('#reminder_message').val(message);
            $("#reminderModal").find("#reminder_from").val(reminder_from);
            if (reminder_last_reply == 1) {
                $("#reminderModal").find("#reminder_last_reply").prop("checked", true);
            } else {
                $("#reminderModal").find("#reminder_last_reply_no").prop("checked", true);
            }
            vendorToRemind = vendorId;
        });

        $(document).on('click', '.save-reminder', function () {
            var reminderModal = $("#reminderModal");
            let frequency = $('#frequency').val();
            let message = $('#reminder_message').val();
            let reminder_from = reminderModal.find("#reminder_from").val();
            let reminder_last_reply = (reminderModal.find('#reminder_last_reply').is(":checked")) ? 1 : 0;

            $.ajax({
                url: "{{ action([\App\Http\Controllers\VendorController::class, 'updateReminder']) }}",
                type: 'POST',
                success: function () {
                    toastr['success']('Reminder updated successfully!');
                },
                data: {
                    vendor_id: vendorToRemind,
                    frequency: frequency,
                    message: message,
                    reminder_from: reminder_from,
                    reminder_last_reply: reminder_last_reply,
                    _token: "{{ csrf_token() }}"
                }
            });
        });

        $(document).on('click', '.edit-vendor', function () {
            var vendor = $(this).data('vendor');
            var url = "{{ url('vendors') }}/" + vendor.id;

            $('#vendorEditModal form').attr('action', url);
            $('#vendor_category option[value="' + vendor.category_id + '"]').attr('selected', true);
            $('#vendor_name').val(vendor.name);
            $('#vendor_address').val(vendor.address);
            $('#vendor_phone').val(vendor.phone);
            $('#vendor_email').val(vendor.email);
            $('#vendor_social_handle').val(vendor.social_handle);
            $('#vendor_website').val(vendor.website);
            $('#vendor_login').val(vendor.login);
            $('#vendor_password').val(vendor.password);
            $('#vendor_gst').val(vendor.gst);
            $('#vendor_account_name').val(vendor.account_name);
            $('#vendor_account_iban').val(vendor.account_iban);
            $('#vendor_account_swift').val(vendor.account_swift);
        });

        $(document).on('click', '.create-agent', function () {
            var id = $(this).data('id');

            $('#agent_vendor_id').val(id);
        });

        $(document).on('click', '.edit-agent-button', function () {
            var agent = $(this).data('agent');
            var url = "{{ url('agent') }}/" + agent.id;

            $('#editAgentModal form').attr('action', url);
            $('#agent_name').val(agent.name);
            $('#agent_address').val(agent.address);
            $('#agent_phone').val(agent.phone);
            $('#agent_email').val(agent.email);
        });

        $(document).on('click', '.make-remark', function (e) {
            e.preventDefault();

            var id = $(this).data('id');
            $('#add-remark input[name="id"]').val(id);

            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.gettaskremark') }}',
                data: {
                    id: id,
                    module_type: "vendor"
                },
            }).done(response => {
                var html = '';

                $.each(response, function (index, value) {
                    html += ' <p> ' + value.remark + ' <br> <small>By ' + value.user_name +
                        ' updated on ' + moment(value.created_at).format('DD-M H:mm') +
                        ' </small></p>';
                    html + "<hr>";
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });

        $('#addRemarkButton').on('click', function () {
            var id = $('#add-remark input[name="id"]').val();
            var remark = $('#add-remark').find('textarea[name="remark"]').val();

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.addRemark') }}',
                data: {
                    id: id,
                    remark: remark,
                    module_type: 'vendor'
                },
            }).done(response => {
                $('#add-remark').find('textarea[name="remark"]').val('');

                var html = ' <p> ' + remark + ' <br> <small>By You updated on ' + moment().format(
                    'DD-M H:mm') + ' </small></p>';

                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function (response) {
                console.log(response);

                alert('Could not fetch remarks');
            });
        });

        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        $(document).on('click', '.load-email-modal', function () {
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('vendors.email') }}',
                data: {
                    id: id
                },
            }).done(function (response) {
                var html = '<div class="speech-wrapper">';
                response.forEach(function (message) {
                    var content = '';
                    content += 'To : ' + message.to + '<br>';
                    content += 'From : ' + message.from + '<br>';
                    if (message.cc) {
                        content += 'CC : ' + message.cc + '<br>';
                    }
                    if (message.bcc) {
                        content += 'BCC : ' + message.bcc + '<br>';
                    }
                    content += 'Subject : ' + message.subject + '<br>';
                    content += 'Message : ' + message.message + '<br>';
                    if (message.attachment.length) {
                        content += 'Attachment : ';
                    }
                    for (var i = 0; i < message.attachment.length; i++) {
                        var imageUrl = message.attachment[i];
                        imageUrl = imageUrl.trim();

                        // Set empty imgSrc
                        var imgSrc = '';

                        // Set image type
                        var imageType = imageUrl.substr(imageUrl.length - 4).toLowerCase();

                        // Set correct icon/image
                        if (imageType == '.jpg' || imageType == 'jpeg') {
                            imgSrc = imageUrl;
                        } else if (imageType == '.png') {
                            imgSrc = imageUrl;
                        } else if (imageType == '.gif') {
                            imgSrc = imageUrl;
                        } else if (imageType == 'docx' || imageType == '.doc') {
                            imgSrc = '/images/icon-word.svg';
                        } else if (imageType == '.xlsx' || imageType == '.xls' || imageType ==
                            '.csv') {
                            imgSrc = '/images/icon-excel.svg';
                        } else if (imageType == '.pdf') {
                            imgSrc = '/images/icon-pdf.svg';
                        } else if (imageType == '.zip' || imageType == '.tgz' || imageType ==
                            'r.gz') {
                            imgSrc = '/images/icon-zip.svg';
                        } else {
                            imgSrc = '/images/icon-file-unknown.svg';
                        }

                        // Set media
                        if (imgSrc != '') {
                            content += '<div class="col-4"><a href="' + message.attachment[i] +
                                '" target="_blank"><label class="label-attached-img" for="cb1_' +
                                i + '"><img src="' + imgSrc +
                                '" style="max-width: 100%;"></label></a></div>';
                        }
                    }
                    if (message.inout == 'in') {
                        html +=
                            '<div class="bubble"><div class="txt"><p class="name"></p><p class="message">' +
                            content + '</p><br/><span class="timestamp">' + message.created_at.date
                                .substr(0, 19) + '</span></div><div class="bubble-arrow"></div></div>';
                    } else if (message.inout == 'out') {
                        html +=
                            '<div class="bubble alt"><div class="txt"><p class="name alt"></p><p class="message">' +
                            content + '</p><br/><span class="timestamp">' + message.created_at.date
                                .substr(0, 19) +
                            '</span></div> <div class="bubble-arrow alt"></div></div>';
                    }
                });

                html += '</div>';

                $("#email-list-history").find(".modal-body").html(html);
                $("#email-list-history").modal("show");
            }).fail(function (response) {
                console.log(response);

                alert('Could not load email');
            });
        });
        $(document).on("keyup", '.search_email_pop', function () {
            var value = $(this).val().toLowerCase();
            $(".speech-wrapper .bubble").filter(function () {
                $(this).toggle($(this).find('.message').text().toLowerCase().indexOf(value) > -1)
            });
        });
        $(document).on('click', '.send-message', function () {
            var thiss = $(this);
            var data = new FormData();
            var vendor_id = $(this).data('vendorid');
            var message = $(this).siblings('input').val();

            data.append("vendor_id", vendor_id);
            data.append("message", message);
            data.append("status", 1);

            if (message.length > 0) {
                if (!$(this).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL + '/whatsapp/sendMessage/vendor',
                        type: 'POST',
                        "dataType": 'json', // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(this).attr('disabled', true);
                        }
                    }).done(function (response) {
                        this.closest('tr').find('.chat_messages').html(this.siblings('input').val());
                        $(this).siblings('input').val('');

                        $(this).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(this).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
        $(document).on('click', '.send-message1', function () {
            var thiss = $(this);
            var data = new FormData();
            var vendor_id = $(this).data('vendorid');
            var message = $("#messageid_" + vendor_id).val();
            data.append("vendor_id", vendor_id);
            data.append("message", message);
            data.append("status", 1);

            if (message.length > 0) {
                if (!$(this).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL + '/whatsapp/sendMessage/vendor',
                        type: 'POST',
                        "dataType": 'json', // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function (response) {
                        //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                        $("#message-chat-txt-" + vendor_id).html(message);
                        $("#messageid_" + vendor_id).val('');

                        $(this).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(this).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
        $(document).on('change', '.update-category-user', function () {
            let catId = $(this).attr('data-categoryId');
            let userId = $(this).val();

            $.ajax({
                url: "{{ action([\App\Http\Controllers\VendorController::class, 'assignUserToCategory']) }}",
                data: {
                    user_id: userId,
                    category_id: catId
                },
                success: function (response) {
                    toastr['success']('User assigned to category completely!')
                }
            });

        });

        $(document).on('click', '.add-cc', function (e) {
            e.preventDefault();

            if ($('#cc-label').is(':hidden')) {
                $('#cc-label').fadeIn();
            }

            var el = `<div class="row cc-input">
            <div class="col-md-10">
                <input type="text" name="cc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image cc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

            $('#cc-list').append(el);
        });

        $(document).on('click', '.cc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.cc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#cc-label').fadeOut();
                }
            });
        });

        // bcc

        $(document).on('click', '.add-bcc', function (e) {
            e.preventDefault();

            if ($('#bcc-label').is(':hidden')) {
                $('#bcc-label').fadeIn();
            }

            var el = `<div class="row bcc-input">
            <div class="col-md-10">
                <input type="text" name="bcc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image bcc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

            $('#bcc-list').append(el);
        });

        $(document).on('click', '.bcc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.bcc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#bcc-label').fadeOut();
                }
            });
        });

        $(document).on('click', '.block-twilio', function () {
            var vendor_id = $(this).data('id');
            var thiss = $(this);

            $.ajax({
                type: "POST",
                url: "{{ route('vendors.block') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: vendor_id
                },
                beforeSend: function () {
                    $(thiss).text('Blocking...');
                }
            }).done(function (response) {
                if (response.is_blocked == 1) {
                    $(thiss).html('<img src="/images/blocked-twilio.png" />');
                } else {
                    $(thiss).html('<img src="/images/unblocked-twilio.png" />');
                }
            }).fail(function (response) {
                $(thiss).html('<img src="/images/unblocked-twilio.png" />');

                alert('Could not block customer!');

                console.log(response);
            });
        });

        $(document).on('click', '.call-select', function () {
            var id = $(this).data('id');
            $('#show' + id).toggle();
            console.log('#show' + id);
        });

        $(document).ready(function () {
            src = "{{ route('vendors.index') }}";
            $(".search").autocomplete({
                source: function (request, response) {
                    id = $('#id').val();
                    name = $('#name').val();
                    email = $('#email').val();
                    phone = $('#phone').val();
                    address = $('#address').val();
                    category = $('#category').val();

                    $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                            id: id,
                            name: name,
                            phone: phone,
                            email: email,
                            address: address,
                            category: category,
                        },
                        beforeSend: function () {
                            $("#loading-image").show();
                        },

                    }).done(function (data) {
                        $("#loading-image").hide();
                        console.log(data);
                        $("#vendor-table tbody").empty().html(data.tbody);
                        if (data.links.length > 10) {
                            $('ul.pagination').replaceWith(data.links);
                        } else {
                            $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                        }
                        $(".select2-quick-reply").select2({});

                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                },
                minLength: 1,

            });


            $(document).ready(function () {
                src = "{{ route('vendors.index') }}";
                $("#search_id").autocomplete({
                    source: function (request, response) {
                        $.ajax({
                            url: src,
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            beforeSend: function () {
                                $("#loading-image").show();
                            },

                        }).done(function (data) {
                            $("#loading-image").hide();
                            $("#vendor-table tbody").empty().html(data.tbody);
                            if (data.links.length > 10) {
                                $('ul.pagination').replaceWith(data.links);
                            } else {
                                $('ul.pagination').replaceWith(
                                    '<ul class="pagination"></ul>');
                            }
                            $(".select2-quick-reply").select2({});

                        }).fail(function (jqXHR, ajaxOptions, thrownError) {
                            alert('No response from server');
                        });
                    },
                    minLength: 1,

                });
            });

            $(document).on("change", ".quickComment", function (e) {

                var message = $(this).val();

                if ($.isNumeric(message) == false) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: BASE_URL + "/vendors/reply/add",
                        dataType: "json",
                        method: "POST",
                        data: {
                            reply: message
                        }
                    }).done(function (data) {

                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                }
                $(this).closest("td").find(".quick-message-field").val($(this).find("option:selected")
                    .text());

            });

            $(".select2-quick-reply").select2({
                tags: true
            });

            $(document).on("click", ".delete_quick_comment", function (e) {
                var deleteAuto = $(this).closest(".d-flex").find(".quickComment").find("option:selected")
                    .val();
                if (typeof deleteAuto != "undefined") {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: BASE_URL + "/vendors/reply/delete",
                        dataType: "json",
                        method: "GET",
                        data: {
                            id: deleteAuto
                        }
                    }).done(function (data) {
                        if (data.code == 200) {
                            $(".quickComment").empty();
                            $.each(data.data, function (k, v) {
                                $(".quickComment").append("<option value='" + k + "'>" + v +
                                    "</option>");
                            });
                            $(".quickComment").select2({
                                tags: true
                            });
                        }

                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                }
            });
        });

        function createUserFromVendor(id, email) {
            $('#vendor_id').attr('data-id', id);
            if (email) {
                $('#createUser').attr('data-email', email);
            }
            $('#createUser').modal('show');
        }

        $('#createUser').on('hidden.bs.modal', function () {
            $('#createUser').removeAttr('data-email');
        })

        $(document).on("click", "#vendor_id", function () {
            $('#createUser').modal('hide');
            id = $(this).attr('data-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: BASE_URL + "/vendors/create-user",
                dataType: "json",
                method: "POST",
                data: {
                    id: id
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                if (data.code == 200) {
                    alert(data.data);
                }

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
                alert('No response from server');
            });
        });

        function inviteGithub() {
            $('#createUser').modal('hide');
            const email = $('#createUser').attr('data-email');

            $.ajax({
                type: "POST",
                url: "/vendors/inviteGithub",
                data: {
                    _token: "{{ csrf_token() }}",
                    email
                }
            })
                .done(function (data) {
                    alert(data.message);
                })
                .fail(function (error) {
                    alert(error.responseJSON.message);
                });

            console.log(email);
        }

        function inviteHubstaff() {
            $('#createUser').modal('hide');
            const email = $('#createUser').attr('data-email');
            console.log(email);

            $.ajax({
                type: "POST",
                url: BASE_URL + "/vendors/inviteHubstaff",
                data: {
                    _token: "{{ csrf_token() }}",
                    email
                }
            })
                .done(function (data) {
                    alert(data.message);
                })
                .fail(function (error) {
                    alert(error.responseJSON.message);
                })
        }

        $('#reminder_from').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });

        $(document).on("change", ".vendor-update-status", function () {
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: BASE_URL + "vendors/change-status",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: $this.data("id"),
                    status: $this.prop('checked')
                }
            }).done(function (data) {
                if (data.code == 200) {
                    toastr["success"](data.message);
                }
            }).fail(function (error) {

            })
        });
        $(document).on("click", ".vendor-update-status-icon", function () {
            var $this = $(this);
            var vendor_id = $(this).attr("data-id");
            var hdn_vendorstatus = $("#hdn_vendorstatus_" + vendor_id).val();
            $.ajax({
                type: "POST",
                url: BASE_URL + "vendors/change-status",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: $this.data("id"),
                    status: hdn_vendorstatus
                }
            }).done(function (data) {
                if (data.code == 200) {
                    //toastr["success"](data.message);
                    if (hdn_vendorstatus == "true") {
                        var img_url = BASE_URL + 'images/do-disturb.png';
                        $("#btn_vendorstatus_" + vendor_id).html('<img src="' + img_url + '" />');
                        $("#btn_vendorstatus_" + vendor_id).attr("title", "On");
                        $("#hdn_vendorstatus_" + vendor_id).val('false');
                    } else {
                        var img_url = BASE_URL + 'images/do-not-disturb.png';
                        $("#btn_vendorstatus_" + vendor_id).html('<img src="' + img_url + '" />');
                        $("#btn_vendorstatus_" + vendor_id).attr("title", "Off");
                        $("#hdn_vendorstatus_" + vendor_id).val('true');
                    }

                }
            }).fail(function (error) {

            })
        });

        $('ul.pagination').show();
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            // debug: true,
            loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
            padding: 20,
            float: 'right',
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function () {
                $('ul.pagination').first().remove();
                $('ul.pagination').show();
            }
        });

        $(document).on('click', '.create_broadcast', function () {
            var vendors = [];
            $(".select_vendor").each(function () {
                if ($(this).prop("checked") == true) {
                    vendors.push($(this).val());
                }
            });
            if (vendors.length == 0) {
                alert('Please select vendor');
                return false;
            }
            $("#create_broadcast").modal("show");
        });

        $("#send_message").submit(function (e) {
            e.preventDefault();
            var vendors = [];
            $(".select_vendor").each(function () {
                if ($(this).prop("checked") == true) {
                    vendors.push($(this).val());
                }
            });
            if (vendors.length == 0) {
                alert('Please select vendor');
                return false;
            }

            if ($("#send_message").find("#message_to_all_field").val() == "") {
                alert('Please type message ');
                return false;
            }

            $.ajax({
                type: "POST",
                url: "{{ route('vendors/send/message') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    message: $("#send_message").find("#message_to_all_field").val(),
                    vendors: vendors
                }
            }).done(function () {
                window.location.reload();
            }).fail(function (response) {
                $(thiss).text('No');

                alert('Could not say No!');
                console.log(response);
            });
        });

        function sendImage(id) {
            $.ajax({
                url: "{{ action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue') }}",
                type: 'POST',
                data: {
                    issue_id: id,
                    type: 1,
                    message: '',
                    _token: "{{ csrf_token() }}",
                    status: 2
                },
                success: function () {
                    toastr["success"]("Message sent successfully!", "Message");

                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });

        }

        function sendUploadImage(id) {

            $('#file-input' + id).trigger('click');

            $('#file-input' + id).change(function () {
                event.preventDefault();
                let image_upload = new FormData();
                let TotalImages = $(this)[0].files.length; //Total Images
                let images = $(this)[0];

                for (let i = 0; i < TotalImages; i++) {
                    image_upload.append('images[]', images.files[i]);
                }
                image_upload.append('TotalImages', TotalImages);
                image_upload.append('status', 2);
                image_upload.append('type', 2);
                image_upload.append('issue_id', id);
                if (TotalImages != 0) {

                    $.ajax({
                        method: 'POST',
                        url: "{{ action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue') }}",
                        data: image_upload,
                        async: true,
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $("#loading-image").show();
                        },
                        success: function (images) {
                            $("#loading-image").hide();
                            alert('Images send successfully');
                        },
                        error: function () {
                            console.log(`Failed`)
                        }
                    })
                }
            })
        }

        // $('#filecount').filestyle({htmlIcon: '<span class="oi oi-random"></span>',badge: true, badgeName: "badge-danger"});

        $(document).on("click", ".upload-document-btn", function () {
            var id = $(this).data("id");
            $("#upload-document-modal").find("#hidden-identifier").val(id);
            $("#upload-document-modal").modal("show");
        });

        $(document).on("submit", "#upload-task-documents", function (e) {
            e.preventDefault();
            var form = $(this);
            var postData = new FormData(form[0]);
            $.ajax({
                method: "post",
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'uploadDocument']) }}",
                data: postData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function (response) {
                    if (response.code == 200) {
                        toastr["success"]("Status updated!", "Message")
                        $("#upload-document-modal").modal("hide");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on("click", ".list-document-btn", function () {
            var id = $(this).data("id");
            $.ajax({
                method: "GET",
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'getDocument']) }}",
                data: {
                    id: id
                },
                dataType: "json",
                success: function (response) {
                    if (response.code == 200) {
                        $("#blank-modal").find(".modal-title").html("Document List");
                        $("#blank-modal").find(".modal-body").html(response.data);
                        $("#blank-modal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });


        $(document).on('click', '.send-message-open', function (event) {
            var textBox = $(this).closest(".communication-td").find(".send-message-textbox");
            var sendToStr = $(this).closest(".communication-td").next().find(".send-message-number").val();
            let issueId = textBox.attr('data-id');
            let message = textBox.val();
            if (message == '') {
                return;
            }

            let self = textBox;

            $.ajax({
                url: "{{ action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue') }}",
                type: 'POST',
                data: {
                    "issue_id": issueId,
                    "message": message,
                    "sendTo": sendToStr,
                    "_token": "{{ csrf_token() }}",
                    "status": 2
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Message sent successfully!", "Message");
                    $('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " +
                        response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });

        $(document).on('click', '.show-status-history', function () {
            var data = $(this).data('history');
            var issueId = $(this).data('id');
            $('#status_quick_history_modal table tbody').html('');
            $.ajax({
                url: "{{ route('development/status/history') }}",
                data: {
                    id: issueId
                },
                success: function (data) {
                    if (data != 'error') {
                        $.each(data, function (i, item) {
                            if (item['is_approved'] == 1) {
                                var checked = 'checked';
                            } else {
                                var checked = '';
                            }
                            $('#status_quick_history_modal table tbody').append(
                                '<tr>\
                                    <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                    <td>' + ((item['old_value'] != null) ? item['old_value'] : '-') + '</td>\
                                    <td>' + item['new_value'] + '</td>\
                                    <td>' + item['name'] + '</td>\
                                </tr>'
                            );
                        });
                    }
                }
            });
            $('#status_quick_history_modal').modal('show');
        });

        //Popup for add new task
        $(document).on('click', '#newTaskModalBtn', function () {
            if ($("#newTaskModal").length > 0) {
                $("#newTaskModal").remove();
            }

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'openNewTaskPopup']) }}",
                type: 'GET',
                dataType: "JSON",
                success: function (resp) {
                    console.log(resp);
                    if (resp.status == 'ok') {
                        $("body").append(resp.html);
                        $('#newTaskModal').modal('show');
                        $('select.select2').select2({tags: true});
                    }
                }
            });
        });
        $(document).on('submit', '#frmaddnewtask', function (e) {
            e.preventDefault();
            var form = $(this);
            var actionUrl = form.attr('action');

            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(), // serializes the form's elements.
                success: function (data) {
                    toastr['success']('Task added successfully!!', 'success');
                    loadMoreProducts();
                    $('#newTaskModal').modal('hide');

                }
            });
        });
        var isLoadingProducts = false;

        function loadMoreProducts() {
            var $loader = $('.infinite-scroll-products-loader');
            $.ajax({
                url: '{{route("development.summarylist")}}',
                type: 'GET',
                beforeSend: function () {
                    $loader.show();

                }
            })
                .done(function (data) {
                    // console.log(data);
                    if ('' === data.trim())
                        return;

                    $loader.hide();

                    $('#vendor-body').html(data);

                    isLoadingProducts = false;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.error('something went wrong');

                    isLoadingProducts = false;
                });
        }
    $("#lead").select2();
    $("#assigned_to").select2();
    </script>
    <script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
<script type="text/javascript">
var siteHelpers = {
            
    quickCategoryAdd : function(ele) {
        var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
        var quickCategoryId = quickCategory.children("option:selected").data('id');
        var textBox = ele.closest("div").find(".quick_category");
        if (textBox.val() == "") {
            alert("Please Enter Category!!");
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                name : textBox.val(),
                quickCategoryId : quickCategoryId
            },
            url: "/add-reply-category"
        };

        if(quickCategoryId!=''){
            siteHelpers.sendAjax(params,"afterQuickSubCategoryAdd");
        } else {
            siteHelpers.sendAjax(params,"afterQuickCategoryAdd");
        }
    },
    afterQuickSubCategoryAdd : function(response) {
        $(".quick_category").val('');
        $(".quickSubCategory").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
    },
    afterQuickCategoryAdd : function(response) {
        $(".quick_category").val('');
        $(".quickCategory").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
    },
    deleteQuickCategory : function(ele) {
        var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
        if (quickCategory.val() == "") {
            alert("Please Select Category!!");
            return false;
        }
        var quickCategoryId = quickCategory.children("option:selected").data('id');
        if (!confirm("Are sure you want to delete category?")) {
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                id : quickCategoryId
            },
            url: "/destroy-reply-category"
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    deleteQuickSubCategory : function(ele) {
        var quickSubCategory = ele.closest("#shortcutsIds").find(".quickSubCategory");
        if (quickSubCategory.val() == "") {
            alert("Please Select Sub Category!!");
            return false;
        }
        var quickSubCategoryId = quickSubCategory.children("option:selected").data('id');
        if (!confirm("Are sure you want to delete sub category?")) {
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                id : quickSubCategoryId
            },
            url: "/destroy-reply-category"
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    deleteQuickComment : function(ele) {
        var quickComment = ele.closest("#shortcutsIds").find(".quickCommentEmail");
        if (quickComment.val() == "") {
            alert("Please Select Quick Comment!!");
            return false;
        }
        var quickCommentId = quickComment.children("option:selected").data('id');
        if (!confirm("Are sure you want to delete comment?")) {
            return false;
        }
        var params = {
            method : 'DELETE',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content')
            },
            url: "/reply/" + quickCommentId,
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    pageReload : function(response) {
        location.reload();
    },
    quickCommentAdd : function(ele) {
        var textBox = ele.closest("div").find(".quick_comment");
        var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
        var quickSubCategory = ele.closest("#shortcutsIds").find(".quickSubCategory");
        if (textBox.val() == "") {
            alert("Please Enter New Quick Comment!!");
            return false;
        }
        if (quickCategory.val() == "") {
            alert("Please Select Category!!");
            return false;
        }
        var quickCategoryId = quickCategory.children("option:selected").data('id');
        var quickSubCategoryId = quickSubCategory.children("option:selected").data('id');
        var formData = new FormData();
        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
        formData.append("reply", textBox.val());
        formData.append("category_id", quickCategoryId);
        formData.append("sub_category_id", quickSubCategoryId);
        formData.append("model", 'Approval Lead');
        var params = {
            method : 'post',
            data : formData,
            url: "/reply"
        };
        siteHelpers.sendFormDataAjax(params,"afterQuickCommentAdd");
    },
    afterQuickCommentAdd : function(reply) {
        $(".quick_comment").val('');
        $('.quickCommentEmail').append($('<option>', {
            value: reply,
            text: reply
        }));
    },
    changeQuickCategory : function (ele) {

        var selectedOption = ele.find('option:selected');
        var dataValue = selectedOption.data('value');

        ele.closest("#shortcutsIds").find('.quickSubCategory').empty();
        ele.closest("#shortcutsIds").find('.quickSubCategory').append($('<option>', {
            value: '',
            text: 'Select Sub Category'
        }));
        dataValue.forEach(function (category) {
            ele.closest("#shortcutsIds").find('.quickSubCategory').append($('<option>', {
                value: category.name,
                text: category.name,
                'data-id': category.id
            }));
        });

        if (ele.val() != "") {
            var replies = JSON.parse(ele.val());
            ele.closest("#shortcutsIds").find('.quickCommentEmail').empty();
            ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                value: '',
                text: 'Quick Reply'
            }));
            replies.forEach(function (reply) {
                ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                    value: reply.reply,
                    text: reply.reply,
                    'data-id': reply.id
                }));
            });
        }
    },
    changeQuickComment : function (ele) {
        $('#send_message_'+ele.attr('data-id')).val(ele.val());
        
        var userEmaillUrl = '/email/email-frame-info/'+$('#reply_email_id').val();;
        var senderName = 'Hello '+$('#sender_email_address').val().split('@')[0]+',';

        $("#reply-message").val(senderName)

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: userEmaillUrl,
            type: 'get',
        }).done( function(response) {
            $("#reply-message").val(senderName+'\n\n'+ele.val()+'\n\n'+response)
        }).fail(function(errObj) {
        })
        
    },
    changeQuickSubCategory : function (ele) {
        var selectedOption = ele.find('option:selected');
        var dataValue = selectedOption.data('id');

        var userEmaillUrl = '/email/email-replise/'+dataValue;

        $.ajax({        
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: userEmaillUrl,
            type: 'get',
        }).done( function(response) {

            if(response!=''){
                var replies = JSON.parse(response);
                ele.closest("#shortcutsIds").find('.quickCommentEmail').empty();
                ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                    value: '',
                    text: 'Quick Reply'
                }));
                replies.forEach(function (reply) {
                    ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                        value: reply.reply,
                        text: reply.reply,
                        'data-id': reply.id
                    }));
                });
            }
            
        }).fail(function(errObj) {
        })
    },
};

$.extend(siteHelpers, common);

$(document).on('click', '.quick_category_add', function () {
    siteHelpers.quickCategoryAdd($(this));
});
$(document).on('click', '.delete_category', function () {
    siteHelpers.deleteQuickCategory($(this));
});
$(document).on('click', '.delete_sub_category', function () {
    siteHelpers.deleteQuickSubCategory($(this));
});
$(document).on('click', '.delete_quick_comment', function () {
    siteHelpers.deleteQuickComment($(this));
});
$(document).on('click', '.quick_comment_add', function () {
    siteHelpers.quickCommentAdd($(this));
});
$(document).on('change', '.quickCategory', function () {
    siteHelpers.changeQuickCategory($(this));
});
$(document).on('change', '.quickCommentEmail', function () {
    siteHelpers.changeQuickComment($(this));
});
$(document).on('change', '.quickSubCategory', function () {
    siteHelpers.changeQuickSubCategory($(this));
});

$(".add-scrapper").click(function (e) { 
    e.preventDefault();
    let task_id = $(this).data("task_id")
    let task_type = $(this).data("task_type")
    $("#addScrapperModel").find('input[name=task_id]').val(task_id);
    $("#addScrapperModel").find('input[name=task_type]').val(task_type);
    $("#addScrapperModel").modal("show");
});

$(document).on("click", ".create-scrapper", function(e) {
    e.preventDefault();
    var form = $(this).closest("form");
    $.ajax({
        url: form.attr("action"),
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: form.serialize(),
        beforeSend: function() {
            $(this).text('Loading...');
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                form[0].reset();
                toastr['success'](response.message);
                $("#addScrapperModel").modal("hide");
            } else {
                toastr['error'](response.message);
            }
        }
    }).fail(function(response) {
        $('#loading-image').hide();
        toastr['error'](response.responseJSON.message);
    });
});

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

$(document).on("click", ".count-dev-scrapper", function() {

    var $this = $(this);
    var task_id = $(this).data("id");

    $('#scrapper-history').attr('data-id', task_id);

    $.ajax({
        type: 'get',
        url: '/development/countscrapper/' + task_id,
        dataType: "json",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $("#loading-image").show();
        },
        success: function(data) {

            $("#dev_scrapper_statistics").modal("show");
            var table = `<div class="table-responsive">
                <table class="table table-bordered table-striped table-scrapper" style="font-size:14px;">`;
                table = table + '<tr>';
                table = table + '<th width="10%">Column Name</th>';
                table = table + '<th width="30%">Values</th>';
                table = table + '<th width="15%">Status</th>';
                table = table + '<th width="45%">Remarks</th>';
                table = table + '</tr>';
            if(data.values!=''){

                $('#scrapper-history').attr('data-scrapperid', data.id);

                $.each(data.values, function(key, value) {
                    table = table + '<tr>';
                    table = table + '<th>'+capitalizeFirstLetter(key.replace("_", " "));
                    table = table + '</th>';

                    if(key=='properties'){

                        var iiii = 1;
                        if(data.values.properties!=''){
                            table = table + '<td><table class="table table-bordered table-striped">';
                            $.each(data.values.properties, function(key, value) {
                                if(key=='category'){
                                    if(data.values.properties.category){

                                        $.each(data.values.properties.category, function(keyC, valueC) {

                                            table = table + '<tr>';
                                                table = table + '<th>'+capitalizeFirstLetter(key.replace("_", " "))+' '+iiii;
                                                table = table + '</th>';
                                                table = table + '<td>'+valueC;
                                                table = table + '</td>';
                                            table = table + '</tr>';

                                            iiii++;
                                        });
                                    }
                                } else {
                                    table = table + '<tr>';
                                        table = table + '<th>'+capitalizeFirstLetter(key.replace("_", " "));
                                        table = table + '</th>';
                                        table = table + '<td>'+value;
                                        table = table + '</td>';
                                    table = table + '</tr>';
                                }

                            });
                            table = table + '</table></td>';

                            var approveValue = '';
                            var unapproveValue = '';
                            var StatusValue = ''
                            for (var i = 0; i < data.ScrapperValuesHistory.length; i++) {

                                if(data.ScrapperValuesHistory[i].column_name==key){

                                    StatusValue = data.ScrapperValuesHistory[i].status;

                                    if(StatusValue=='Approve'){
                                        approveValue = 'selected';
                                    }

                                    if(StatusValue=='Unapprove'){
                                        unapproveValue = 'selected';
                                    }

                                }                            
                            }

                            var remarksValue = '';
                            for (var i = 0; i < data.ScrapperValuesRemarksHistory.length; i++) {

                                if(data.ScrapperValuesRemarksHistory[i].column_name==key){

                                    remarksValue = data.ScrapperValuesRemarksHistory[i].remarks;

                                }                            
                            }

                            @if (Auth::user()->isAdmin())
                                table = table + '<td>';
                                    table = table + '<select class="add-scrapper-status form-control" id="status_values_'+data.task_id+'_'+key+'" data-value="'+key+'" data-taskid="'+data.task_id+'">';
                                    table = table + '<option>Select Status</option>';
                                    table = table + '<option '+approveValue+' value="Approve">Approve</option>';
                                    table = table + '<option '+unapproveValue+' value="Unapprove">Unapprove</option>';
                                    table = table + '</select>';
                                table = table + '</td>';


                                table = table + '<td>';
                                if(unapproveValue=='selected'){
                                    table = table + '<textarea rows="1" class="add-scrapper-textarea form-control" id="remarks_values_'+data.task_id+'_'+key+'">'+remarksValue+'</textarea>';

                                    table = table + '<button class="btn btn-sm btn-image add-scrapper-remarks"  title="Send approximate" data-taskid="'+data.task_id+'" data-value="'+key+'"><i class="fa fa-paper-plane" aria-hidden="true"></i></button></button>';
                                }

                                table = table + '</td>';
                            @else   
                                table = table + '<td>'+StatusValue+'</td>';

                                table = table + '<td>';
                                if(unapproveValue=='selected'){
                                    table = table +remarksValue;
                                }
                                table = table + '</td>';
                            @endif
                        }
                    } else if(key=='images'){
                        if(data.values.images!=''){
                            table = table + '<td><table class="table table-bordered table-striped">';
                            table = table + '<tr><td>';
                            $.each(data.values.images, function(key, value) {
                                table = table + '<img src="'+value+'" width="50px" style="cursor: default;margin-right: 10px;">';
                            });
                            table = table + '</td></tr>';
                            table = table + '</table></td>';

                            var approveValue = '';
                            var unapproveValue = '';
                            var StatusValue = ''
                            for (var i = 0; i < data.ScrapperValuesHistory.length; i++) {

                                if(data.ScrapperValuesHistory[i].column_name==key){

                                    StatusValue = data.ScrapperValuesHistory[i].status;

                                    if(StatusValue=='Approve'){
                                        approveValue = 'selected';
                                    }

                                    if(StatusValue=='Unapprove'){
                                        unapproveValue = 'selected';
                                    }

                                }                            
                            }

                            var remarksValue = '';
                            for (var i = 0; i < data.ScrapperValuesRemarksHistory.length; i++) {

                                if(data.ScrapperValuesRemarksHistory[i].column_name==key){

                                    remarksValue = data.ScrapperValuesRemarksHistory[i].remarks;

                                }                            
                            }

                            @if (Auth::user()->isAdmin())
                                table = table + '<td>';
                                    table = table + '<select class="add-scrapper-status form-control" id="status_values_'+data.task_id+'_'+key+'" data-value="'+key+'" data-taskid="'+data.task_id+'">';
                                    table = table + '<option>--Select Status--</option>';
                                    table = table + '<option '+approveValue+' value="Approve">Approve</option>';
                                    table = table + '<option '+unapproveValue+' value="Unapprove">Unapprove</option>';
                                    table = table + '</select>';
                                table = table + '</td>';

                                table = table + '<td>';
                                if(unapproveValue=='selected'){
                                    table = table + '<textarea rows="1" class="add-scrapper-textarea form-control" id="remarks_values_'+data.task_id+'_'+key+'">'+remarksValue+'</textarea>';

                                    table = table + '<button class="btn btn-image add-scrapper-remarks"  title="Send approximate" data-taskid="'+data.task_id+'" data-value="'+key+'"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>';
                                }

                                table = table + '</td>';
                            @else   
                                table = table + '<td>'+StatusValue+'</td>';

                                table = table + '<td>';
                                if(unapproveValue=='selected'){
                                    table = table +remarksValue;
                                }
                                table = table + '</td>';
                            @endif
                        }
                    } else {
                        if(key=='url'){
                            table = table + '<td><a href="'+value+'" target="_blank">'+value+'</a>';
                            table = table + '</td>';
                        } else {
                            table = table + '<td>'+value;
                            table = table + '</td>';
                        }

                        var approveValue = '';
                        var unapproveValue = '';
                        var StatusValue = ''
                        for (var i = 0; i < data.ScrapperValuesHistory.length; i++) {

                            if(data.ScrapperValuesHistory[i].column_name==key){

                                StatusValue = data.ScrapperValuesHistory[i].status;

                                if(StatusValue=='Approve'){
                                    approveValue = 'selected';
                                }

                                if(StatusValue=='Unapprove'){
                                    unapproveValue = 'selected';
                                }

                            }                            
                        }

                        var remarksValue = '';
                        for (var i = 0; i < data.ScrapperValuesRemarksHistory.length; i++) {

                            if(data.ScrapperValuesRemarksHistory[i].column_name==key){

                                remarksValue = data.ScrapperValuesRemarksHistory[i].remarks;

                            }                            
                        }

                        @if (Auth::user()->isAdmin())
                            table = table + '<td>';
                                table = table + '<select class="add-scrapper-status form-control" id="status_values_'+data.task_id+'_'+key+'" data-value="'+key+'" data-taskid="'+data.task_id+'">';
                                table = table + '<option>--Select Status--</option>';
                                table = table + '<option '+approveValue+' value="Approve">Approve</option>';
                                table = table + '<option '+unapproveValue+' value="Unapprove">Unapprove</option>';
                                table = table + '</select>';
                            table = table + '</td>';


                            table = table + '<td>';
                            if(unapproveValue=='selected'){
                                table = table + '<textarea rows="1" class="add-scrapper-textarea form-control" id="remarks_values_'+data.task_id+'_'+key+'">'+remarksValue+'</textarea> ';

                                table = table + '<button class="btn btn-image add-scrapper-remarks"  title="Send approximate" data-taskid="'+data.task_id+'" data-value="'+key+'"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>';
                            }

                            table = table + '</td>';
                        @else   
                            table = table + '<td>'+StatusValue+'</td>';

                            table = table + '<td>';
                            if(unapproveValue=='selected'){
                                table = table +remarksValue;
                            }
                            table = table + '</td>';
                        @endif
                    }
                    table = table + '</tr>';
                });
            }

            table = table + '</table></div>';
            $("#loading-image").hide();
            $(".modal").css("overflow-x", "hidden");
            $(".modal").css("overflow-y", "auto");
            $("#dev_scrapper_statistics_content").html(table);
        },
        error: function(error) {
            console.log(error);
            $("#loading-image").hide();
        }
    });
});

$(document).on("change", ".add-scrapper-status", function(e) {

    let task_id = $(this).data("taskid");
    let column_name = $(this).data("value");
    var status = $(this).val();

    $.ajax({
        url: "{{route('development.updatescrapperdata')}}",
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            'task_id' :task_id,
            'column_name' :column_name,
            'status' :status,
        },
        beforeSend: function() {
            $(this).text('Loading...');
            $("#loading-image").show();
        },
        success: function(response) {
            $('.count-dev-scrapper_'+task_id).trigger('click');
            $("#loading-image").hide();
            if (response.code == 200) {
                toastr['success'](response.message);
            } else {
                toastr['error'](response.message);
            }
        }
    }).fail(function(response) {
        $('#loading-image').hide();
        toastr['error'](response.responseJSON.message);
    });
    
});

$(document).on("click", ".add-scrapper-remarks", function() {

    let task_id = $(this).data("taskid");
    let column_name = $(this).data("value");
    var remarks = $('#remarks_values_'+task_id+'_'+column_name).val();
    
    $.ajax({
        url: "{{route('development.updatescrapperremarksdata')}}",
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            'task_id' :task_id,
            'column_name' :column_name,
            'remarks' :remarks,
        },
        beforeSend: function() {
            $(this).text('Loading...');
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                toastr['success'](response.message);
            } else {
                toastr['error'](response.message);
            }
        }
    }).fail(function(response) {
        $('#loading-image').hide();
        toastr['error'](response.responseJSON.message);
    });
});

$(document).on("click", ".approveEstimateFromshortcutButtonTaskPage", function (event) {
    var element = $(this);
    if (confirm('Are you sure, do you want to approve this task?')) {
        event.preventDefault();
        let type = $(this).data('type');
        let task_id = $(this).data('task');
        let history_id = $(this).data('id');
        $.ajax({
            url: "/development/time/history/approve",
            type: "POST",
            data: {
                _token: "{{csrf_token()}}",
                approve_time: history_id,
                developer_task_id: task_id,
                user_id: 0
            },
            success: function (response) {
                element.closest('tr').hide('slow');

                toastr["success"]("Successfully approved", "success");
            },
            error: function (error) {
                toastr["error"](error.responseJSON.message);
            },
        });
    }
});

$(document).on("click", "#scrapper-history", function() {

    var $this = $(this);
    var task_id = $(this).data("id");
    var scrapperid_id = $(this).data("scrapperid");
        
    $.ajax({
        type: 'post',
        url: "{{route('development.historyscrapper')}}",
        dataType: "json",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            'task_id' :task_id,
            'id' :scrapperid_id
        },
        beforeSend: function() {
            $("#loading-image").show();
        },
        success: function(data) {

            $("#dev_scrapper_statistics_history").modal("show");
            var table = `<div class="table-responsive infinite-scroll" style="overflow-y: auto">
                <table class="table table-bordered table-striped" style="font-size:14px;">`;
                table = table + '<tr>';
                table = table + '<th width="10%">Title</th>';
                table = table + '<th width="7%">Website</th>';
                table = table + '<th width="7%">Sku</th>';
                table = table + '<th width="5%">Url</th>';
                table = table + '<th width="4%">Images</th>';
                table = table + '<th width="5%">Description</th>';
                table = table + '<th width="5%">Properties</th>';
                table = table + '<th width="5%">Currency</th>';
                table = table + '<th width="4%">Size System</th>';
                table = table + '<th width="3%">Price</th>';
                table = table + '<th width="5%">Discounted Price</th>';
                table = table + '<th width="5%">Discounted Percentage</th>';
                table = table + '<th width="3%">B2b Price</th>';
                table = table + '<th width="3%">Brand</th>';
                table = table + '<th width="3%">Is Sale</th>';
                table = table + '<th width="7%">Date</th>';
                table = table + '</tr>';
            if(data.values!=''){
                $.each(data.values, function(key, value) {
                    table = table + '<tr>';
                    table = table + '<td>'+value.title+'</td>';
                    table = table + '<td>'+value.website+'</td>';
                    table = table + '<td>'+value.sku+'</td>';
                    table = table + '<td>'+value.url+'</td>';
                    table = table + '<td>'+value.title+'</td>';
                    table = table + '<td>'+value.description+'</td>';
                    table = table + '<td>'+value.title+'</td>';
                    table = table + '<td>'+value.currency+'</td>';
                    table = table + '<td>'+value.size_system+'</td>';
                    table = table + '<td>'+value.price+'</td>';
                    table = table + '<td>'+value.discounted_price+'</td>';
                    table = table + '<td>'+value.discounted_percentage+'</td>';
                    table = table + '<td>'+value.b2b_price+'</td>';
                    table = table + '<td>'+value.brand+'</td>';
                    table = table + '<td>'+value.is_sale+'</td>';
                    table = table + '</tr>';
                });
            }

            table = table + '</table></div>';
            $("#loading-image").hide();
            $(".modal").css("overflow-x", "hidden");
            $(".modal").css("overflow-y", "auto");
            $("#dev_scrapper_statistics_history_content").html(table);
        },
        error: function(error) {
            console.log(error);
            $("#loading-image").hide();
        }
    });
});

$(document).ready(function() {
    // Iterate through elements with class 'myClass'
    $('.m_start_date_').each(function() {
        var elementId = $(this).attr('id'); // Get ID of each element

        var task_id = $("#"+elementId).attr('data-id');
        var inputTime = $("#"+elementId).attr('data-value');

        startTime = new Date(inputTime);
        //setInterval(updateTimeCounter(startTime, task_id), 1000); 

        (function(startTime, id) {
            setInterval(function() {
                updateTimeCounter(startTime, id);
            }, 1000);
        })(startTime, task_id);
    });
});

function updateTimeCounter(startTime, id) {
    // Check if startTime is defined
    if (startTime && !isNaN(startTime.getTime())) {
        // Get the current time
        var currentTime = new Date();

        // Calculate the difference in milliseconds
        var timeDifference = currentTime - startTime;

        // Convert milliseconds to hours, minutes, and seconds
        var hours = Math.floor(timeDifference / (60 * 60 * 1000));
        var minutes = Math.floor((timeDifference % (60 * 60 * 1000)) / (60 * 1000));
        var seconds = Math.floor((timeDifference % (60 * 1000)) / 1000);

        // Display the time counter
        var counterText = pad(hours) + ":" + pad(minutes) + ":" + pad(seconds);

        $("#time-counter_"+id).text(counterText);
    }
}

// Function to pad single digits with leading zeros
function pad(number) {
    return (number < 10 ? '0' : '') + number;
}

$(document).on('click', '.show-timer-history', function() {
    var issueId = $(this).data('id');
    $('#timer_tracked_modal table tbody').html('');
    $.ajax({
        url: "{{ route('development.timer.history') }}",
        data: {
            id: issueId,
        },
        success: function(data) {
            console.log(data);
            if (data != 'error') {
                $.each(data.histories, function(i, item) {
                    $('#timer_tracked_modal table tbody').append(
                        '<tr>\
                            <td>' + moment(item['start_date']).format('DD-MM-YYYY HH:mm:ss') + '</td>\
                            <td>' + ((item['end_date'] != null) ? moment(item['end_date']).format('DD-MM-YYYY HH:mm:ss') : 'Not Stop') + '</td>\
                        </tr>'
                    );
                });
            }
        }
    });
    $('#timer_tracked_modal').modal('show');
});

$(document).on("click", ".show-scrapper-logs", function () {
    var id = $(this).data("id");
    var url = "{{ route('development.get-scrapper-logs-by-task', ['id' => ':id']) }}";
    url = url.replace(':id', id);

    $.ajax({
        method: "GET",
        url: url,
        dataType: "json",
        success: function (response) {
            if (response.code == 200) {
                $('#showScrapperLogsModal table tbody').html('');

                $.each(response.data, function(i, item) {
                    $('#showScrapperLogsModal table tbody').append(
                        `<tr>
                            <td>${ moment(item['created_at']).format('DD-MM-YYYY HH:mm:ss') }</td>
                            <td>${ item.log }</td>
                            <td>${ item.user.name }</td>
                        </tr>`
                    );
                });

                $("#showScrapperLogsModal").modal("show");
            } else {
                toastr["error"]('Something went wrong', "Message");
            }
        }
    });
});
</script>
@endsection
