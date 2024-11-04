@extends('layouts.app')
@section('title', 'User Management > User Scheduler')
@section('favicon', 'user-management.png')
@section('large_content')
@include('partials.flash_messages')
@include("partials.modals.user-schedules-modal-status-color")
<style>
.div-slot {
	min-width: 75px;
	padding: 3px !important;
}
.greenClass{background-color: {{$status[0]['color'] ?? ''}};height: 100px;}
.yellowClass{ background-color: {{$status[1]['color'] ?? ''}};height: 100px; }
.orangeClass{ background: {{$status[2]['color'] ?? ''}}; height: 100px; }
.table-bordered > tbody > tr > td{height: 100px;}
</style>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;background-color:rgba(255,255,255,0.6);"></div>
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">{{ $title }} <span id="listUserScheduleCount" class="count-text"></span></h2>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card d-normal">
                <div class="card-header">
                    <h4>
                        <a class="collapsed card-link" data-toggle="collapse" href="#collapseSearch" aria-expanded="false">
                            <i class="fa fa-arrow-up"></i>
                            <i class="fa fa-arrow-down"></i>
                            Filter Records
                        </a>

                        @if($userType==0)
                            <button style="float:right; margin-left: 10px;" type="button" class="btn custom-button count-dev-customer-tasks" title="Show task history" data-id="{{Auth::user()->id}}" data-category="{{Auth::user()->id}}"><i class="fa fa-info-circle"></i></button>
                            
                            <button style="float:right; margin-left: 10px;" title="create quick task" type="button" class="btn custom-button create-quick-task " data-id="{{Auth::user()->id}}"  data-category_title="User Schedules Page" data-title="{{'User Schedules Page - '.Auth::user()->id  }}"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        @else 
                            <button style="float:right; margin-left: 10px;" type="button" class="btn custom-button count-dev-customer-tasks-admin" title="Show task history" data-id="{{Auth::user()->id}}" data-category="{{Auth::user()->id}}"><i class="fa fa-info-circle"></i></button>
                        @endif

                        <button class="btn custom-button" style="float:right;margin-left: 10px;" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>

                        <a target="_blank" class="btn custom-button" style="float:right;" href="{{ route('user-management.user-schedules.report') }}">Report</a>
                    </h4>
                </div>
                <div id="collapseSearch" class="collapse">
                    <div class="card-body">
                        <form id="frm-search-crud" class="" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">Users List</label>
                                                <select class="form-control select2" name="srchUser">
                                                    <option value=""></option>
                                                    {!! makeDropdown($listUsers) !!}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">From Date</label>
                                                <div class="input-group date d-datetime">
                                                    <input type="text" class="form-control input-sm" name="srchDateFrom" value="{{ request('srchDateFrom', date('Y-m-d')) }}" />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">To Date</label>
                                                <div class="input-group date d-datetime">
                                                    <input type="text" class="form-control input-sm" name="srchDateTo" value="{{ request('srchDateTo', date('Y-m-d', strtotime('+3 days'))) }}" />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">Status</label>
                                                {{ html()->multiselect("task_status[]", $statusList, request('task_status'))->class("form-control select2") }}
                                                <?php //echo Form::select("task_status[]",$statusList,request()->get('task_status', []),["class" => "form-control multiselect","multiple" => true]);?>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">Task Type</label>
                                                <select class="form-control select2" name="task_type_filter">
                                                    <option value=""></option>
                                                    <option value="2">Planned</option>
                                                    <option value="1">Actual</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" onclick="siteDatatableSearch('#listUserSchedule')">Search</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="siteDatatableClearSearch('#listUserSchedule', '#frm-search-crud')">Clear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <table id="listUserSchedule" class="table table-bordered" style="width:100%">
                <thead>
					<tr>
                        <th data-data="name" data-name="name" width="10%" data-sortable="false"></th>
                        <th data-data="date" data-name="date" width="6%" data-sortable="false"></th>
                        <th width="81%" colspan="9" style="text-align: center;">Hourly Slots [T-? = Task] [DT-? = Dev Task]</th>
                    </tr>
                    <tr>
                        <th data-data="name" data-name="name" width="10%" data-sortable="false">User Name</th>
                        <th data-data="date" data-name="date" width="6%" data-sortable="false">Date</th>
						<?php for ($i = 0; $i < 9; $i++) { ?>
                            <th width="9%" data-data="slots<?php echo $i; ?>" data-name="slots<?php echo $i; ?>" width="5%"   data-sortable="false" style="text-align: center;">Slots <?php echo $i; ?></th>
						<?php } ?>
						
                    </tr>
                </thead>
                <tbody>
				
				</tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="getEstimateTime" role="dialog" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Estimate Time</h3>
                <button class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <p class="estimateTimeText"></p>
            </div>
        </div>
    </div>
</div>

<div id="create-quick-task" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo route('task.create.multiple.task.shortcutuserschedules'); ?>" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create Task</h4>
                </div>
                <div class="modal-body">

                    <input class="form-control" value="59" type="hidden" name="category_id" />
                    <input class="form-control" value="" type="hidden" name="category_title" id="category_title" />
                    <input class="form-control" type="hidden" name="site_id" id="site_id" />
                    <div class="form-group">
                        <label for="">Subject</label>
                        <input class="form-control" type="text" id="hidden-task-subject" name="task_subject" />
                    </div>
                    <div class="form-group">
                        <select class="form-control" style="width:100%;" name="task_type" tabindex="-1" aria-hidden="true">
                            <option value="0">Other Task</option>
                            <option value="4">Developer Task</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="repository_id">Repository:</label>
                        <br>
                        <select style="width:100%" class="form-control  " id="repository_id" name="repository_id">
                            <option value="">-- select repository --</option>
                            @foreach (\App\Github\GithubRepository::all() as $repository)
                            <option value="{{ $repository->id }}">{{ $repository->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Details</label>
                        <input class="form-control text-task-development" type="text" name="task_detail" />
                    </div>

                    <div class="form-group">
                        <label for="">Cost</label>
                        <input class="form-control" type="text" name="cost" />
                    </div>

                    <div class="form-group">
                        <label for="">Assign to</label>
                        <select name="task_asssigned_to" class="form-control assign-to select2">
                            @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Create Review Task?</label>
                        <div class="form-group">
                            <input type="checkbox" name="need_review_task" value="1" />
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label for="">Websites</label>
                        <div class="form-group website-list row">
                           
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default create-task">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="dev_task_statistics" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Dev Task statistics</h2>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" id="dev_task_statistics_content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Task type</th>
                                <th>Task Id</th>
                                <th>Assigned to</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="preview-task-image" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered" style="table-layout: fixed">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Sl no</th>
                                <th style=" width: 30%">Files</th>
                                <th style="word-break: break-all; width: 40%">Send to</th>
                                <th style="width: 10%">User</th>
                                <th style="width: 10%">Created at</th>
                                <th style="width: 15%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="task-image-list-view">
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
<script type="text/javascript" src="{{config('app.url')}}/js/jsrender.min.js"></script>
<script type="text/javascript" src="{{config('app.url')}}/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="{{config('app.url')}}/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{config('app.url')}}/js/common-helper.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
{{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pnp-sp-taxonomy/1.3.11/sp-taxonomy.es5.umd.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.js" integrity="sha512-wTIaZJCW/mkalkyQnuSiBodnM5SRT8tXJ3LkIUA/3vBJ01vWe5Ene7Fynicupjt4xqxZKXA97VgNBHvIf5WTvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection

@push('modals')
<div id="modalSlotAssign" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task: Add To Slots</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label>Tasks & Developer Tasks:</label>
                        <div class="form-group">
                            <select id="slotTaskId" name="slotTaskId" class="form-control select2">
                                <option value="">- Select -</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Remark</label>
                        <div class="form-group">
                            <textarea id="slotTaskRemarks" name="slotTaskRemarks" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="funSlotAssignSubmit('{!! route('task.slot.assign') !!}')">Save</button>
            </div>
        </div>
    </div>
</div>

<div id="modalSlotMoveAssign" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task: Move To Slots</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label>Slots:</label>
                        <div class="form-group">
                            <select id="slotMoveTaskId" name="slotMoveTaskId" class="form-control select2">
                                <option value="">- Select -</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="funSlotMoveSubmit('{!! route('task.slot.move') !!}')">Save</button>
            </div>
        </div>
    </div>
</div>

<div id="modalPastSlotAssign" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Note</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>You cannot edit the records which are more than one day in the past - pls. Contact admin</p>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>

<div id="estimateTimeModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
    <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Estimate Time</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body estimateTimeModelBody">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="remarksNoteModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
    <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Remarks</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body remarksNoteModelBody">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('styles')
<style>
   /* .div-slot {
        display: inline-block;
        padding: 4px;
        border: 1px solid #ddd;
        border-radius: 10px;
        margin: 2px;
    }*/
</style>
@endpush

@push("link-css")
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
@endpush

@push("jquery")
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
@endpush

@push("scripts")
<script>
    var currSlotAssignee = null;
    var dtblListUserSchedule = null;
    var urlTaskDropdown = "{!! route('task.dropdown-user-wise') !!}";

    var urlTaskMoveDropdown = "{!! route('task.dropdown-slot-wise') !!}";

    function funSlotMoveModal(ele) {
        currSlotAssignee = ele;

        siteLoader(1);
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: urlTaskMoveDropdown,
            type: 'GET',
            data: {
                userId: jQuery(currSlotAssignee).attr('data-user_id'),
            }
        }).done(function(res) {
            jQuery('#slotMoveTaskId').html(res.list ? '<option value="">- Select -</option>' + res.list : '<option value="">No records found.</option>')
            jQuery('#modalSlotMoveAssign').modal('show');
            applySelect2(jQuery('#slotMoveTaskId'));
            siteLoader(0);
        }).fail(function(err) {
            siteErrorAlert(err);
            siteLoader(0);
        });
    }

    function funPastSlotAssignModal(ele) {
        currSlotAssignee = ele;
        jQuery('#modalPastSlotAssign').modal('show');
    }

    function funSlotAssignModal(ele) {
        currSlotAssignee = ele;

        siteLoader(1);
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: urlTaskDropdown,
            type: 'GET',
            data: {
                userId: jQuery(currSlotAssignee).attr('data-user_id'),
            }
        }).done(function(res) {
            jQuery('#slotTaskId').html(res.list ? '<option value="">- Select -</option>' + res.list : '<option value="">No records found.</option>')
            jQuery('#modalSlotAssign').modal('show');
            applySelect2(jQuery('#slotTaskId'));
            siteLoader(0);
        }).fail(function(err) {
            siteErrorAlert(err);
            siteLoader(0);
        });
    }

    function funSlotMoveSubmit(url) {
        if (jQuery('#slotMoveTaskId').val()) {
            siteLoader(1);
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: {
                    taskTime: jQuery('#slotMoveTaskId').val(),
                    userId: jQuery(currSlotAssignee).attr('data-user_id'),
                    date: jQuery(currSlotAssignee).attr('data-date'),
                    slot: jQuery(currSlotAssignee).attr('data-slot'),
                    tasks: jQuery(currSlotAssignee).attr('data-tasks'),
                    dev_tasks: jQuery(currSlotAssignee).attr('data-dev_tasks'),
                }
            }).done(function(response) {
                jQuery('#modalSlotMoveAssign').modal('hide');
                siteLoader(0);
                dtblListUserSchedule.draw(false);
            }).fail(function(err) {
                siteErrorAlert(err);
                siteLoader(0);
            });
        } else {
            siteErrorAlert("Please select slot.");
        }
    }

    function funSlotAssignSubmit(url) {
        if (jQuery('#slotTaskId').val()) {
            siteLoader(1);
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: {
                    taskId: jQuery('#slotTaskId').val(),
                    slotTaskRemarks: jQuery('#slotTaskRemarks').val(),
                    userId: jQuery(currSlotAssignee).attr('data-user_id'),
                    date: jQuery(currSlotAssignee).attr('data-date'),
                    slot: jQuery(currSlotAssignee).attr('data-slot'),
                }
            }).done(function(response) {
                jQuery('#modalSlotAssign').modal('hide');
                siteLoader(0);
                dtblListUserSchedule.draw(false);
            }).fail(function(err) {
                siteErrorAlert(err);
                siteLoader(0);
            });
        } else {
            siteErrorAlert("Please select task.");
        }
    }

    jQuery(document).ready(function() {

        applySelect2(jQuery('.select2'));
        applyDatePicker(jQuery('.d-datetime'));

        // Render datatable
        dtblListUserSchedule = jQuery('#listUserSchedule').DataTable({
            lengthChange: false,
            searching: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            paging: false,
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ $urlLoadData }}",
                data: function(d) {
                    //return siteDatatableMergeSearch(d, '#frm-search-crud');
                    return $("#frm-search-crud").serialize()
                },
                dataSrc: function(json) {
                    return json.data;
                },
                error: function(xhr, error, code) {
                    siteErrorAlert(xhr);
                    jQuery('#listUserSchedule_processing').hide();
                }
            },
            initComplete: function(settings, json) {
                jQuery('#listUserScheduleCount').html('(' + dtblListUserSchedule.data().count() + ')');
            },
            drawCallback: function(settings) {
                jQuery('#listUserScheduleCount').html('(' + dtblListUserSchedule.data().count() + ')');
            },
            order: [],
        });
    });

    $(document).on("click", ".getEstimateTimeClass", function(href) {
        var estimateTime = $(this).data("id");
        
        $('.estimateTimeText').text(estimateTime);
    });

    $(document).on('click', '.estimate_minutes_class', function() {
        $('#estimateTimeModel').modal('toggle');
        $(".estimateTimeModelBody").html("");
        var time = $(this).data('time')+' Minutes';
        $(".estimateTimeModelBody").html(time);
    });

    $(document).on('click', '.slotTaskRemarks_class', function() {
        $('#remarksNoteModel').modal('toggle');
        $(".remarksNoteModelBody").html("");
        var remarks = $(this).data('remarks');
        $(".remarksNoteModelBody").html(remarks);
    });

    $(document).on("click", "#send-request-date", function (e) {
        e.preventDefault();

        if($(this).data('requested')==''){
            if (confirm("Are you sure you want to add task on this date?")) {

                var request_date = $(this).data('date');
                var user_id = $(this).data('user_id');

                $("#loading-image").show();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "/user-management/user-schedules/add-new-request",
                    data: {
                        user_id: user_id,
                        request_date: request_date
                    },
                    success: function(response) {
                        toastr["success"]("Your request has been sent to the admin!", "Message");
                        $("#loading-image").hide();
                        siteDatatableSearch('#listUserSchedule');
                    }
                });
            } else {
                toastr["error"]("Your request not sent to the admin!", "Message");
            }
        } else if($(this).data('requested')=='requested'){
            toastr["error"]("Your request has already been sent to the admin user.", "Message");
        } else if($(this).data('requested')=='accepted'){
            toastr["error"]("Your request has been accepted to the admin user. You can add task to this date.", "Message");
        } else if($(this).data('requested')=='denied'){
            toastr["error"]("Your request has been denied to the admin user.", "Message");
        }
    });

    $(document).on("click", ".send-request-date-admin", function (e) {
        e.preventDefault();

        var request_date = $(this).data('date');
        var user_id = $(this).data('user_id');
        var request_status = $(this).data('status');

        if(request_status=='accepted'){
            var messagevar = 'Are you sure you want to accept this request?'
        } else {
            var messagevar = 'Are you sure you want to denied this request?'
        }

        if (confirm(messagevar)) {

            $("#loading-image").show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "/user-management/user-schedules/update-request",
                data: {
                    user_id: user_id,
                    request_date: request_date,
                    request_status: request_status,
                },
                success: function(response) {
                    toastr["success"]("Request status updated!", "Message");
                    $("#loading-image").hide();
                    siteDatatableSearch('#listUserSchedule');
                }
            });
        } else {
            toastr["error"]("Request status not updated!", "Message");
        }
    });

    $(document).on('click', '.create-quick-task', function() {
        var $this = $(this);
        site = $(this).data("id");
        title = $(this).data("title");
        cat_title = $(this).data("category_title");
        development = $(this).data("development");
        if (!title || title == '') {
            toastr["error"]("Please add title first");
            return;
        }
        //debugger;
        /*let val = $("#change_website1").select2("val");
        $.ajax({
            url: '/task/get/websitelist',
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: val,
                cat_title:cat_title
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            //$this.siblings('input').val("");
            $('.website-list').html(response.data);
            //toastr["success"]("Remarks fetched successfully");
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
        });*/

        $("#create-quick-task").modal("show");

        var selValue = $(".save-item-select").val();
        if (selValue != "") {
            $("#create-quick-task").find(".assign-to option[value=" + selValue + "]").attr('selected',
                'selected')
            $('.assign-to.select2').select2({
                width: "100%"
            });
        }

        $("#hidden-task-subject").val(title);
        $(".text-task-development").val(development);
        $('#site_id').val(site);
    });

    $(document).on("click", ".create-task", function(e) {
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
                    $("#create-quick-task").modal("hide");
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);
        });
    });

    $(document).on("click", ".count-dev-customer-tasks", function() {

        var $this = $(this);
        // var user_id = $(this).closest("tr").find(".ucfuid").val();
        var site_id = $(this).data("id");
        var category_id = $(this).data("category");
        $("#site-development-category-id").val(category_id);
        $.ajax({
            type: 'get',
            url: 'countdevtask/' + site_id,
            dataType: "json",
            headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(data) {
                $("#dev_task_statistics").modal("show");
                var table = `<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th width="4%">Tsk Typ</th>
                            <th width="4%">Tsk Id</th>
                            <th width="7%">Asg to</th>
                            <th width="12%">Desc</th>
                            <th width="12%">Sts</th>
                            <th width="33%">Communicate</th>
                            <th width="10%">Action</th>
                        </tr>`;
                for (var i = 0; i < data.taskStatistics.length; i++) {
                    var str = data.taskStatistics[i].subject;
                    var res = str.substr(0, 100);
                    var status = data.taskStatistics[i].status;
                    if (typeof status == 'undefined' || typeof status == '' || typeof status ==
                        '0') {
                        status = 'In progress'
                    };
                    table = table + '<tr><td>' + data.taskStatistics[i].task_type + '</td><td>#' +
                        data.taskStatistics[i].id +
                        '</td><td class="expand-row-msg" data-name="asgTo" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-asgTo-' + data
                        .taskStatistics[i].id + '">' + data.taskStatistics[i].assigned_to_name
                        .replace(/(.{6})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-asgTo-' + data
                        .taskStatistics[i].id + ' hidden">' + data.taskStatistics[i]
                        .assigned_to_name +
                        '</span></td><td class="expand-row-msg" data-name="res" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-res-' + data
                        .taskStatistics[i].id + '">' + res.replace(/(.{7})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-res-' + data
                        .taskStatistics[i].id + ' hidden">' + res + '</span></td><td>' + status +
                        '</td><td><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message" title="Send message" data-taskid="' +
                        data.taskStatistics[i].id +
                        '"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id +
                        '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
                    table = table + '<a href="javascript:void(0);" data-task-type="' + data
                        .taskStatistics[i].task_type + '" data-id="' + data.taskStatistics[i].id +
                        '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
                    table = table +
                        '<button type="button" class="btn btn-xs  preview-img pd-5" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id + '" data-dismiss="modal"><i class="fa fa-list"></i></button></td>';
                    table = table + '</tr>';
                }
                table = table + '</table></div>';
                $("#loading-image").hide();
                $(".modal").css("overflow-x", "hidden");
                $(".modal").css("overflow-y", "auto");
                $("#dev_task_statistics_content").html(table);
            },
            error: function(error) {
                $("#loading-image").hide();
            }
        });
    

    });

    $(document).on("click", ".count-dev-customer-tasks-admin", function() {

        var $this = $(this);
        // var user_id = $(this).closest("tr").find(".ucfuid").val();
        var site_id = $(this).data("id");
        var category_id = $(this).data("category");
        $("#site-development-category-id").val(category_id);
        $.ajax({
            type: 'get',
            url: 'countdevtaskadmin/' + site_id,
            dataType: "json",
            headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(data) {
                $("#dev_task_statistics").modal("show");
                var table = `<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th width="4%">Tsk Typ</th>
                            <th width="4%">Tsk Id</th>
                            <th width="7%">Asg to</th>
                            <th width="12%">Desc</th>
                            <th width="12%">Sts</th>
                            <th width="33%">Communicate</th>
                            <th width="10%">Action</th>
                        </tr>`;
                for (var i = 0; i < data.taskStatistics.length; i++) {
                    var str = data.taskStatistics[i].subject;
                    var res = str.substr(0, 100);
                    var status = data.taskStatistics[i].status;
                    if (typeof status == 'undefined' || typeof status == '' || typeof status ==
                        '0') {
                        status = 'In progress'
                    };
                    table = table + '<tr><td>' + data.taskStatistics[i].task_type + '</td><td>#' +
                        data.taskStatistics[i].id +
                        '</td><td class="expand-row-msg" data-name="asgTo" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-asgTo-' + data
                        .taskStatistics[i].id + '">' + data.taskStatistics[i].assigned_to_name
                        .replace(/(.{6})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-asgTo-' + data
                        .taskStatistics[i].id + ' hidden">' + data.taskStatistics[i]
                        .assigned_to_name +
                        '</span></td><td class="expand-row-msg" data-name="res" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-res-' + data
                        .taskStatistics[i].id + '">' + res.replace(/(.{7})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-res-' + data
                        .taskStatistics[i].id + ' hidden">' + res + '</span></td><td>' + status +
                        '</td><td><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message" title="Send message" data-taskid="' +
                        data.taskStatistics[i].id +
                        '"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id +
                        '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
                    table = table + '<a href="javascript:void(0);" data-task-type="' + data
                        .taskStatistics[i].task_type + '" data-id="' + data.taskStatistics[i].id +
                        '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
                    table = table +
                        '<button type="button" class="btn btn-xs  preview-img pd-5" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id + '" data-dismiss="modal"><i class="fa fa-list"></i></button></td>';
                    table = table + '</tr>';
                }
                table = table + '</table></div>';
                $("#loading-image").hide();
                $(".modal").css("overflow-x", "hidden");
                $(".modal").css("overflow-y", "auto");
                $("#dev_task_statistics_content").html(table);
            },
            error: function(error) {
                $("#loading-image").hide();
            }
        });
    

    });

    $(document).on('click', '.send-message', function() {
        var thiss = $(this);
        var data = new FormData();
        var task_id = $(this).data('taskid');
        var message = $(this).closest('tr').find('.quick-message-field').val();
        var mesArr = $(this).closest('tr').find('.quick-message-field');
        $.each(mesArr, function(index, value) {
            if ($(value).val()) {
                message = $(value).val();
            }
        });

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
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                    $("#loading-image").hide();
                    thiss.closest('tr').find('.quick-message-field').val('');

                    toastr["success"]("Message successfully send!", "Message")
                    // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
                    //   .done(function( data ) {
                    //
                    //   }).fail(function(response) {
                    //     alert(response.responseJSON.message);
                    //   });

                    $(thiss).attr('disabled', false);
                }).fail(function(errObj) {
                    $(thiss).attr('disabled', false);

                    toastr["error"]("Could not send message", "error");
                });
            }
        } else {
            alert('Please enter a message first');
        }
    });

    $(document).on("click", ".delete-dev-task-btn", function() {
        var x = window.confirm("Are you sure you want to delete this ?");
        if (!x) {
            return;
        }
        var $this = $(this);
        var taskId = $this.data("id");
        var tasktype = $this.data("task-type");
        if (taskId > 0) {
            $.ajax({
                beforeSend: function() {
                    $("#loading-image").show();
                },
                type: 'get',
                url: "/site-development/deletedevtask",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: taskId,
                    tasktype: tasktype
                },
                dataType: "json"
            }).done(function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    $this.closest("tr").remove();
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                alert('Could not update!!');
            });
        }

    });

    $(document).on('click', '.expand-row-msg', function() {
        var name = $(this).data('name');
        var id = $(this).data('id');
        var full = '.expand-row-msg .show-short-' + name + '-' + id;
        var mini = '.expand-row-msg .show-full-' + name + '-' + id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });

    $(document).on('click', '.preview-img', function(e) {
        e.preventDefault();
        id = $(this).data('id');
        if (!id) {
            toastr["error"]("No data found", "error");
            return;
        }
        $.ajax({
            url: "/task/preview-img-task/" + id,
            type: 'GET',
            success: function(response) {
                $("#preview-task-image").modal("show");
                $(".task-image-list-view").html(response);
                initialize_select2()
            },
            error: function() {}
        });
    });

    $(document).on("click", ".send-to-sop-page", function() {
        var id = $(this).data("id");
        var task_id = $(this).data("media-id");

        $.ajax({
            url: '/task/send-sop',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                id: id,
                task_id: task_id
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.success) {
                    toastr["success"](response.message);
                } else {
                    toastr["error"](response.message);
                }

            },
            error: function(error) {
                toastr["error"];
            }

        });
    });
</script>
@endpush