@extends('layouts.app')

@section('favicon' , 'task.png')

@section('title', 'Tasks')

@section('styles')

    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}

    <link rel="stylesheet" href="{{ mix('webpack-dist/css/bootstrap-datetimepicker.min.css') }} ">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">

    <style>
        .communication_th{width:10%!important;min-width:250px!important}
        #message-wrapper{height:450px;overflow-y:scroll}
        .dis-none{display:none}
        .pd-5{padding:3px}
        .cls_task_detailstextarea{height:30px!important}
        .cls_remove_allpadding{padding-right:0!important;padding-left:0!important}
        .cls_right_allpadding{padding-right:0!important}
        .cls_left_allpadding{padding-left:0!important}
        #addNoteButton{margin-top:2px}
        #saveNewNotes{margin-top:2px}
        .col-xs-12.col-md-2{padding-left:5px!important;padding-right:5px!important;height:38px}
        .cls_task_subject{padding-left:9px}
        #recurring-task .col-xs-12.col-md-6{padding-left:5px!important;padding-right:5px!important}
        #appointment-container .col-xs-12.col-md-6{padding-left:5px!important;padding-right:5px!important}
        #taskCreateForm .form-group{margin-bottom:0}
        .cls_action_box .btn-image img{width:12px!important}
        .cls_action_box .btn.btn-image{padding:2px}
        .btn.btn-image{padding:5px 3px}
        .td-mini-container{margin-top:9px}
        .td-full-container{margin-top:9px}
        .cls_textbox_notes{width:100%!important}
        .cls_multi_contact .btn-image img{width:12px!important}
        .cls_multi_contact{width:100%}
        .cls_multi_contact_first{width:80%;display:inline-block}
        .cls_multi_contact_second{width:7%;display:inline-block}
        .cls_categoryfilter_box .btn-image img{width:12px!important}
        .cls_categoryfilter_box{width:100%}
        .cls_categoryfilter_first{width:80%;display:inline-block}
        .cls_categoryfilter_second{width:7%;display:inline-block}
        .cls_comm_btn{margin-left:3px;padding:4px 8px}
        .btn.btn-image.btn-call-data{margin-top:-15px}
        .dis-none{display:none}
        .no-due-date{background-color:#f1f1f1!important}
        .over-due-date{background-color:#777!important;color:#fff}
        .over-due-date .btn{background-color:#777!important}
        .over-due-date .btn .fa{color:#000!important}
        .pd-2{padding:2px}
        .zoom-img:hover{-ms-transform:scale(1.5);-webkit-transform:scale(1.5);transform:scale(1.5)}
        .status-selection .btn-group{padding:0;width:100%}
        .status-selection .multiselect{width:100%}
        .green-notification{color:green}
        .red-notification{color:grey}
        select.globalSelect2+span.select2{width:calc(100% - 26px)!important}
        .cmn-toggle{position:absolute;margin-left:-9999px;visibility:hidden}
        .cmn-toggle+label{display:block;position:relative;cursor:pointer;outline:none;user-select:none}
        input.cmn-toggle-round+label{padding:2px;width:40px;height:20px;background-color:#ddd;border-radius:60px}
        input.cmn-toggle-round+label:before,input.cmn-toggle-round+label:after{display:block;position:absolute;top:1px;left:1px;bottom:1px;content:""}
        input.cmn-toggle-round+label:before{right:1px;background-color:#f1f1f1;border-radius:60px;transition:background .4s}
        input.cmn-toggle-round+label:after{width:18px;background-color:#fff;border-radius:100%;box-shadow:0 2px 5px rgba(0,0,0,0.3);transition:margin .4s}
        input.cmn-toggle-round:checked+label:before{background-color:#333}
        input.cmn-toggle-round:checked+label:after{margin-left:20px}
        .btn.btn-image{margin-top:0!important}
        .tablesorter-header-inner{white-space:nowrap}
        .show-finished-task{height:auto}
        .time_doctor_project_section_modal,.time_doctor_account_section_modal{display:none}
    </style>
@endsection

@section('large_content')

    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="page-heading">{{$title}}</h2>
        </div>
    </div>
    <!--- Pre Loader -->
    <img src="/images/pre-loader.gif" id="Preloader" style="display:none;" />
    @include('task-module.partials.modal-whatsapp-group')
    @include('task-module.partials.modal-task-bell')
    @include('task-module.partials.modal-chat')
    @include('partials.flash_messages')

    <div class="row">
        <div class="col-xs-12">
            <form class="form-search-data">
                <input type="hidden" name="daily_activity_date" value="{{ $data['daily_activity_date'] }}">
                <input type="hidden" name="type" id="tasktype" value="pending">
                <div class="row">
                    <div class="col-xs-12 col-md-1 pd-2">
                        <div class="form-group cls_task_subject">
                            <input type="text" name="term" placeholder="Search Term" id="task_search" class="form-control input-sm" value="{{ isset($term) ? $term : "" }}">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-1 pd-2">
                        <div class="form-group">
                            {!! $task_categories_dropdown !!}
                        </div>
                    </div>

                    @if(auth()->user()->checkPermission('activity-list'))
                        <div class="col-xs-12 col-md-2 pd-2">
                            <div class="form-group ml-3">
                                <select id="search_by_user" class="form-control select2" name="selected_user[]" multiple>
                                    <option value="">Select a User</option>
                                    @foreach ($users as $id => $user)
                                        <option value="{{ $id }}" {{ $id == $selected_user ? 'selected' : '' }}>{{ $user }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12  col-md-1 pd-2">
                            <div class="form-group ml-3">
                                <select id="master_user_id" class="form-control select2" name="search_master_user_id" id="search_master_user_id">
                                    <option value="">Lead 1 Select</option>
                                    @foreach($users as $leadId => $leadName)
                                        <option value="{{ $leadId }}">{{ $leadName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12  col-md-1 pd-2">
                            <div class="form-group ml-3">
                                <select id="master_user_id" class="form-control select2" name="search_second_master_user_id" id="search_second_master_user_id">
                                    <option value="">Lead 2 Select
                                    <option>
                                    @foreach($users as $leadId => $leadName)
                                        <option value="{{ $leadId }}">{{ $leadName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="col-xs-12 col-md-1 pd-2">
                        <div class="form-group">
                            <select name="is_statutory_query" id="is_statutory_query" class="form-control input-sm">
                                <option @if(request('is_statutory_query')==0) selected @endif value="0">Other Task</option>
                                <option @if(request('is_statutory_query')==1) selected @endif value="1">Statutory Task</option>
                                <option @if(request('is_statutory_query')==2) selected @endif value="2">Calendar Task</option>
                            <!-- <option @if(request('is_statutory_query') == 3) selected @endif value="3">Discussion Task</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-1 pd-2">
                        <div class="form-group">
                            <select name="sort_by" id="sort_by" class="form-control input-sm">
                                <option value="">Sort by</option>
                                <option @if(request('sort_by')==1) selected @endif value="1">Date desc</option>
                                <option @if(request('sort_by')==2) selected @endif value="2">Date Asc</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-1 pd-2">
                        <div class="form-group">
                            <select name="filter_by" id="filter_by" class="form-control input-sm">
                                <option value="">Filter by</option>
                                <option @if(request('filter_by')==1) selected @endif value="1">Pending tasks</option>
                                <option @if(request('filter_by')==2) selected @endif value="2">Completed by user</option>
                            </select>
                        </div>
                    </div>

                    

                    <div class="col-xs-12 col-md-1 pd-2 status-selection">
                        {{ html()->multiselect("filter_status[]", $statuseslist, request()->get('filter_status', $selectStatusList))->class("form-control multiselect") }}
                    </div>


                    <div class="col-xs-12 col-md-1 pd-2">
                        <input type="checkbox" checked="checked" name="flag_filter"> Flagged
                    </div>
                    <button type="button" class="btn btn-image btn-call-data"><img src="{{asset('images/filter.png')}}" /></button>
                </div>

            </form>
        </div>
    </div>

    <?php
    if (\App\Helpers::getadminorsupervisor() && ! empty($selected_user)) {
        $isAdmin = true;
    } else {
        $isAdmin = false;
    }
    ?>

    @include('task-module.partials.modal-reminder')

    <div id="exTab2" style="overflow: auto">
        <div class="tab-content ">
            <!-- Pending task div start -->
            <div class="tab-pane active" id="1">
                <div class="row" style="margin:0px;">
                    <!-- <h4>List Of Pending Tasks</h4> -->
                    <div class="col-12">
                        <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />
                    </div>
                    <div class="col-12">
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th width="4%">ID</th>
                                <th width="7%">Date</th>
                                <th width="4%" class="category">Category</th>
                                <th width="4%">Task Subject</th>
                                <th width="10%">Assign To</th>
                                <th width="8%">Status</th>
                                <th width="5%">Tracked time</th>
                                <th class="communication_th">Communication</th>
                                <th width="6%">Estimated Time</th>
                                <th width="6%">Estimated Start Datetime</th>
                                <th width="6%">Estimated End Datetime</th>
                                <th width="6%">
                                    ICON &nbsp;
                                    <label><input type="checkbox" class="show-finished-task" name="show_finished" value="on"> Finished</label>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="pending-row-render-view">
                                @if(count($data['task']['pending']) >0)
                                @foreach($data['task']['pending'] as $task)
                                    @php
                                        $taskDueDate = $task->due_date;
                                        $task->due_date='';
                                        $status_color = \App\Helpers::getFirstTaskStatusByID($task->status);
                                        if ($status_color == null) {
                                            $status_color = new stdClass();
                                        }
                                    @endphp
                                    <tr style="background-color: {{$status_color->task_color ?? ""}}!important;" class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} {{ !$task->due_date ? 'no-due-date' : '' }} {{ $task->due_date && (date('Y-m-d H:i') > $task->due_date && !$task->is_completed) ? 'over-due-date' : '' }} {{ $task->is_statutory == 3 ? 'row-highlight' : '' }}" id="task_{{ $task->id }}">
                                        <td class="p-2">
                                            @if(auth()->user()->isAdmin())
                                                <input type="checkbox" name="selected_issue[]" value="{{$task->id}}" title="Task is in priority" {{in_array($task->id, $priority) ? 'checked' : ''}}>
                                            @endif
                                            <input type="checkbox" title="Select task" class="select_task_checkbox" name="task" data-id="{{ $task->id }}" value="">
                                            {{ $task->id }}
                                        </td>
                                        <td class="p-2">{{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}
                                            <br>
                                            @if($task->customer_id)
                                                Cus-{{$task->customer_id}}
                                                <br>
                                                @if(Auth::user()->isAdmin())
                                                    <span>
                                                        {{ isset($task->customer_name) ? $task->customer_name : '' }}
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="expand-row table-hover-cell p-2">
                                            @if (isset($categories[$task->category]))
                                                <span class="td-mini-container">
                                        {{ strlen($categories[$task->category]) > 10 ? substr($categories[$task->category], 0, 10) : $categories[$task->category] }}
                                    </span>

                                                <span class="td-full-container hidden">
                                        {{ $categories[$task->category] }}
                                    </span>
                                            @endif
                                        </td>
                                        <td class="expand-row" data-subject="{{$task->task_subject ? $task->task_subject : 'Task Details'}}" data-details="{{$task->task_details}}" data-switch="0" style="word-break: break-all;">
                                    <span class="td-mini-container">
                                        {{ $task->task_subject ? substr($task->task_subject, 0, 15) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
                                    </span>
                                            <span class="td-full-container hidden">
                                        <strong>{{ $task->task_subject ? $task->task_subject : 'Task Details' }}</strong>
                                        {{ $task->task_details }}
                                    </span>
                                        </td>
                                    
                                        <td class="table-hover-cell p-2">
                                        @php
                                            $special_task = $task; 
                                            $users_list = \App\Helpers::getTaskUserList($task, $users);
                                        @endphp
                                        {{ strlen($users_list) > 15 ? substr($users_list, 0, 15) : $users_list }}

                                        @if(auth()->user()->isAdmin() || $isTeamLeader)
                                            @php 
                                                $selectBoxId = 'assign_to';  
                                                $selectClass = "assign-user";
                                                $type="assign-user";
                                            @endphp
                                            @include('task-module.partials.select-user',compact('task', 'users', 'selectBoxId', 'selectClass', 'type'))
                                        @else
                                            @if($task->assign_to)
                                                @if(isset($users[$task->assign_to]))
                                                    <p>{{$users[$task->assign_to]}}</p>
                                                @else
                                                    <p>-</p>
                                                @endif
                                            @endif
                                        @endif

                                        <span class="td-full-container hidden">
                                            {{ $users_list }}
                                        </span>
                                        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history" title="Show History" data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
                                            <div class="col-md-12 expand-col-lead{{$task->id}} dis-none" style="padding:0px;">
                                                <br>
                                                @if(auth()->user()->isAdmin()  || $isTeamLeader)
                                                    <label for="" style="font-size: 12px;margin-top:10px;">Lead :</label>
                                                    @php 
                                                        $selectBoxId = 'master_user_id';  
                                                        $selectClass = "assign-master-user";
                                                        $type="master-user";
                                                    @endphp
                                                    @include('task-module.partials.select-user',compact('task', 'users', 'selectBoxId', 'selectClass', 'type'))

                                                @else
                                                    @if($task->master_user_id)
                                                        @if(isset($users[$task->master_user_id]))
                                                            <p>{{$users[$task->master_user_id]}}</p>
                                                        @else
                                                            <p>-</p>
                                                        @endif
                                                    @endif
                                                @endif

                                                <br>

                                                @if(auth()->user()->isAdmin()  || $isTeamLeader)
                                                    <label for="" style="font-size: 12px;margin-top:10px;">Lead 2 :</label>
                                                    @php 
                                                        $selectBoxId = 'master_user_id';  
                                                        $selectClass = "assign-master-user";
                                                        $type="second-master-user";
                                                    @endphp
                                                    @include('task-module.partials.select-user',compact('task', 'users', 'selectBoxId', 'selectClass', 'type'))
                                                @else
                                                    @if($task->second_master_user_id)
                                                        @if(isset($users[$task->second_master_user_id]))
                                                            <p>{{$users[$task->second_master_user_id]}}</p>
                                                        @else
                                                            <p>-</p>
                                                        @endif
                                                    @endif
                                                @endif

                                                @if($task->is_milestone)
                                                    <p style="margin-bottom:0px;">Total : {{$task->no_of_milestone}}</p>
                                                    @if($task->no_of_milestone == $task->milestone_completed)
                                                        <p style="margin-bottom:0px;">Done : {{$task->milestone_completed}}</p>
                                                    @else
                                                        <input type="number" name="milestone_completed" id="milestone_completed_{{$task->id}}" placeholder="Completed..." class="form-control save-milestone" value="{{$task->milestone_completed}}" data-id="{{$task->id}}">
                                                    @endif
                                                @else
                                                    <p>No milestone</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <select id="master_user_id" class="form-control change-task-status select2" data-id="{{$task->id}}" name="master_user_id" id="user_{{$task->id}}">
                                                <option value="">Select...</option>
                                                <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                                                @foreach($task_statuses as $index => $status)
                                                    @if(!auth()->user()->isAdmin() AND $status->name == 'Done')
                                                        @continue
                                                    @endif
                                                    @if( $status->id == $task->status )
                                                        <option value="{{$status->id}}" selected>{{ $status->name }}</option>
                                                    @else
                                                        <option value="{{$status->id}}">{{ $status->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            @if (isset($special_task->timeSpent) && $special_task->timeSpent->task_id > 0)
                                                {{ formatDuration($special_task->timeSpent->tracked) }}
                                                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history" title="Show tracked time History" data-id="{{$task->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button>
                                            @endif

                                            <div class="col-md-12 expand-col" style="padding:0px;">
                                                @if(!$task->hubstaff_task_id && (auth()->user()->isAdmin() || auth()->user()->id == $task->assign_to))
                                                    <button type="button" class="btn btn-xs create-hubstaff-task" title="Create Hubstaff task for User" data-id="{{$task->id}}" data-type="developer">Create D Task</button>
                                                @endif
                                                @if(!$task->lead_hubstaff_task_id && $task->master_user_id && (auth()->user()->isAdmin() || auth()->user()->id == $task->master_user_id))
                                                    <button style="margin-top:10px;color:black;" type="button" class="btn btn-secondary btn-xs create-hubstaff-task" title="Create Hubstaff task for Master user" data-id="{{$task->id}}" data-type="lead">Create L Task</button>
                                                @endif
                                                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-hubtask-log-history" title="Show create hubtask Logs" data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
                                            </div>
                                        </td>
                                        <td class="table-hover-cell p-2 {{ ($task->message && $task->message_status == 0) || $task->message_is_reminder == 1 || ($task->message_user_id == $task->assign_from && $task->assign_from != Auth::id()) ? 'text-danger' : '' }}">
                                            @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                                <div style="margin-bottom:10px;width: 100%;">
                                                    <?php $text_box = '100'; ?>
                                                    <textarea rows="2" class="form-control quick-message-field input-sm" id="getMsg{{$task->id}}" name="message" placeholder="Message"></textarea>
                                                    <div class="d-flex">
                                                        <div style="">
                                                            <button id="send-message_{{ $task->id }}" class="btn btn-sm btn-image send-message" title="Send message" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button>

                                                            <input type="hidden" name="is_audio" id="is_audio_{{$task->id}}" value="0" >
                                                            <!-- <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image btn-trigger-rvn-modal" data-id="{{$task->id}}" data-tid="{{$task->id}}" data-load-type="text" data-all="1" title="Record & Send Voice Message"><img src="{{asset('images/record-voice-message.png')}}" alt=""></button> -->
                                                        </div>
                                                        @if (isset($task->message))
                                                            <div style="max-width: 30px;">
                                                                <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='task' data-id="{{ $task->id }}" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                                            </div>
                                                        @endif
                                                        <button class="btn btn-image upload-task-files-button ml-2" type="button" title="Uploaded Files" data-task_id="{{$task->id}}">
                                                            <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                                        </button>
                                                        <button class="btn btn-image view-task-files-button ml-2" type="button" title="View Uploaded Files" data-task_id="{{$task->id}}">
                                                            <img src="/images/google-drive.png" style="cursor: nwse-resize; width: 10px;">
                                                        </button>
                                                    </div>
                                                    @if (isset($task->message))
                                                        <div style="margin-bottom:10px;width: 100%;">
                                                            @if (isset($task->is_audio) && $task->is_audio)
                                                                <audio controls="" src="{{ \App\Helpers::getAudioUrl($task->message) }}"></audio>
                                                            @else
                                                            <div class="d-flex justify-content-between expand-row-msg" data-id="{{$task->id}}">
                                                                <span class="td-mini-container-{{$task->id}}" style="margin:0px;">
                                                                <?php
                                                                    if (! empty($task->message) && ! empty($task->task_subject)) {
                                                                        $pos = strpos($task->message, $task->task_subject);
                                                                        $length = strlen($task->task_subject);
                                                                        if ($pos) {
                                                                            $start = $pos + $length + 1;
                                                                        } else {
                                                                            $start = 0;
                                                                        }
                                                                    } else {
                                                                        $start = 0;
                                                                    }
    ?>
                                                                {{substr($task->message, $start,28)}}
                                                                </span>
                                                            </div>
                                                            <div class="expand-row-msg" data-id="{{$task->id}}">
                                                                <span class="td-full-container-{{$task->id}} hidden">
                                                                {{ $task->message }}
                                                                </span>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                @if(auth()->user()->isAdmin())
                                                    <div style="margin-bottom:10px;width: 100%;">
                                                        <div class="expand-col dis-none">
                                                            <label for="">Lead:</label>
                                                            <div class="d-flex">
                                                                <input type="text" style="width: 100%;" class="form-control quick-message-field input-sm" id="getMsg{{$task->id}}" name="message" placeholder="Message" value="">
                                                                <div style="max-width: 30px;"><button class="btn btn-sm btn-image send-message-lead" title="Send message" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                Private
                                            @endif
                                        </td>
                                        <td class="p-2">
                                            <div style="margin-bottom:10px;width: 100%;">
                                                <div class="d-flex">
                                                    <input type="number" class="form-control" name="approximates{{$task->id}}" value="{{$task->approximate}}" min="1" autocomplete="off">
                                                    <div style="max-width: 30px;"><button class="btn btn-sm btn-image send-approximate-lead" title="Send approximate" onclick="funTaskInformationUpdatesTime('approximate',{{$task->id}})" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button></div>
                                                </div>
                                            </div>
                                        </td>
                                        @php
                                            $single = \App\Helpers\DevelopmentHelper::getTaskById($task->id);
                                        @endphp
                                        <td class="p-2">
                                            <div class="form-group d-flex">
                                                <div class='input-group date cls-start-due-date'>
                                                    <input type="text" class="form-control" name="start_dates{{$task->id}}" value="{{$single->task_start_date}}" autocomplete="off" />
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                                    <div style="max-width: 30px;"><button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate" onclick="funTaskInformationUpdatesTime('start_date',{{$task->id}})" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button></div>
                                            </div>
                                        </td>
                                        <td class="p-2">
                                            <div class="form-group d-flex">
                                                <div class='input-group date cls-start-due-date'>
                                                    <input type="text" class="form-control" name="due_dates{{$task->id}}" value="{{$single->task_new_due_date}}" autocomplete="off" />
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                                <div style="max-width: 30px;"><button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate" onclick="funTaskInformationUpdatesTime('due_date',{{$task->id}})" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button></div>
                                            </div>
                                        </td>
                                        <td class="p-2">
                                            <div class="dropdown dropleft">
                                                <a class="btn btn-secondary btn-sm dropdown-toggle" href="javascript:void(0);" role="button" id="dropdownMenuLink{{$task->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Actions
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink{{$task->id}}">
                                                    <a class="dropdown-item" href="javascript:void(0);" onclick="funTaskInformationModal(this, '{{$task->id}}')">Task Information: Update</a>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$task->id}}')"><i class="fa fa-arrow-down"></i></button>
                                        </td>
                                    </tr>
                                    <tr class="action-btn-tr-{{$task->id}} d-none">
                                        <td class="font-weight-bold">Action</td>
                                        <td colspan="11">
                                            <div>
                                                <div class="row cls_action_box" style="margin:0px;">
                                                    <button class="btn btn-image mt-2 create-task-document" title="Create document" data-id="{{$task->id}}">
                                                        <i class="fa fa-file-text" aria-hidden="true"></i>
                                                    </button>
                                                    <button class="btn btn-image mt-2 show-created-task-document" title="Show created document" data-id="{{$task->id}}">
                                                        <i class="fa fa-list" aria-hidden="true"></i>
                                                    </button>

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Pending task div end -->
        </div>
    </div>
    </div>


    <div id="allTaskCategoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" id="category-list-area">

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

    <div id="create-task-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create task</h4>
                </div>
                <div class="modal-body" id="create-task-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
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
    <div id="send-message-text-box" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('task.send-brodcast') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="task_id" id="hidden-task-id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Send Brodcast Message</h4>
                    </div>
                    <div class="modal-body" style="background-color: #999999;">
                        @csrf
                        <div class="form-group">
                            <label for="document">Message</label>
                            <textarea class="form-control message-for-brodcast" name="message" placeholder="Enter your message"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-send-brodcast-message">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route("task.save-documents") }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="task_id" id="hidden-task-id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload File(s)</h4>
                    </div>
                    <div class="modal-body" style="background-color: #999999;">
                        @csrf
                        <div class="form-group">
                            <label for="document">Documents</label>
                            <div class="needsclick dropzone" id="document-dropzone">

                            </div>
                        </div>
                        <div class="form-group add-task-list">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-save-documents">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="previewDoc" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-md-12">
                        <iframe src="" id="previewDocSource" width='700' height='550' allowfullscreen webkitallowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;"></div>

    @include("task-module.partials.tracked-time-history")
    @include("development.partials.user_history_modal")

    <div id="recurring-history-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Log case id</th>
                                <th>Message</th>
                                <th>Log msg </th>
                                <th>Date/Time</th>
                            </tr>
                            </thead>
                            <tbody class="recurring-history-list-view">
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

    @include("task-module.task-update-modal")
    @include("task-module.partials.time-history-modal")
    @include("task-module.partials.modal-status-color")

    
    <div id="task-create-log-listing" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
    
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Task Subject</th>
                                    <th width="30%">Message</th>
                                    <th width="30%">Updated by</th>
                                    <th width="20%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="task-log-listing-view">
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

<div id="create-d-task-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Task</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo route('task.create.hubstaff_task'); ?>" method="post" id="assign_task_form">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <input type="hidden" name="id" id="issueId"/>
                        <input type="hidden" name="type" id="type"/>
                        <label for="task_for_modal">Task For</label>
                        <select name="task_for_modal" class="form-control task_for_modal" style="width:100%;">
                            <option value="">Select</option>
                            <option value="hubstaff">Hubstaff</option>
                            <option value="time_doctor">Time Doctor</option>
                        </select>
                    </div>
                    <div class="form-group time_doctor_account_section_modal">
                        <label for="time_doctor_account">Task Account</label>
                        {{ html()->select("time_doctor_account", ['' => ''])->class("form-control time_doctor_account_modal globalSelect2")->style("width:100%;")->data('ajax', route('select2.time_doctor_accounts_for_task'))->data('placeholder', 'Account') }}
                    </div>
                    <div class="form-group time_doctor_project_section_modal">
                        <label for="time_doctor_project">Time Doctor Project</label>
                        {{ html()->select("time_doctor_project", ['' => ''])->class("form-control time_doctor_project globalSelect2")->style("width:100%;")->data('ajax', route('select2.time_doctor_projects'))->data('placeholder', 'Project') }}
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-default" data-task_id="">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div id="taskGoogleDocModal" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Google Doc</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <form action="{{route('google-docs.task')}}" method="POST">
                @csrf
                <input type="hidden" id="task_id">
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Document type:</strong>

                        <select class="form-control" name="type" required id="doc-type">
                            <option value="spreadsheet">Spreadsheet</option>
                            <option value="doc">Doc</option>
                            <option value="ppt">Ppt</option>
                            <option value="xps">Xps</option>
                            <option value="txt">Txt</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="doc_name" value="" class="form-control input-sm" placeholder="Document Name" required id="doc-name">
                    </div>

                    
                   
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-secondary" id="btnCreateTaskDocument">Create</button>
                </div>
            </form>
        </div>

    </div>
</div>
<div id="taskGoogleDocListModal" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Google Documents list</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="5%">File Name</th>
                        <th width="5%">Created Date</th>
                        <th width="10%">URL</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<div id="uploadeTaskFileModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload Screencast/File to Google Drive</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<form action="{{ route('task.upload-file') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="task_id" id="upload_task_id">
				<div class="modal-body">						
					<div class="form-group">
						<strong>Upload File</strong>
						<input type="file" name="file[]" id="fileInput" class="form-control input-sm" placeholder="Upload File" style="height: fit-content;" multiple required>
						@if ($errors->has('file'))
							<div class="alert alert-danger">{{$errors->first('file')}}</div>
						@endif
					</div>
					<div class="form-group">
						<strong>File Creation Date:</strong>
						<input type="date" name="file_creation_date" value="{{ old('file_creation_date') }}" class="form-control input-sm" placeholder="Drive Date" required>
					</div>
					<div class="form-group">
							<label>Remarks:</label>
							<textarea id="remarks" name="remarks" rows="4" cols="64" value="{{ old('remarks') }}" placeholder="Remarks" required class="form-control"></textarea>

							@if ($errors->has('remarks'))
								<div class="alert alert-danger">{{$errors->first('remarks')}}</div>
							@endif
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-default">Upload</button>
				</div>
			</form>
		</div>

	</div>
</div>
<div id="displayTaskFileUpload" class="modal fade" role="dialog">
	<div class="modal-dialog modal-xl">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Google Drive Uploaded files</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Filename</th>
								<th>File Creation Date</th>
								<th>URL</th>
								<th>Remarks</th>
                                <th>Created by</th>
							</tr>
						</thead>
						<tbody id="taskFileUploadedData">
							
						</tbody>
					</table>
				</div>
			 </div>


		</div>

	</div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>

    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/recorder.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/record-voice-notes.js') }} "></script>
    <script>
        function Showactionbtn(id){
            $(".action-btn-tr-"+id).toggleClass('d-none')
        }
        $(document).on('click', '.previewDoc', function() {
            $('#previewDocSource').attr('src', '');
            var docUrl = $(this).data('docurl');
            var type = $(this).data('type');
            var type = jQuery.trim(type);
            if (type == "image") {
                $('#previewDocSource').attr('src', docUrl);
            } else {
                $('#previewDocSource').attr('src', "https://docs.google.com/gview?url=" + docUrl + "&embedded=true");
            }
            $('#previewDoc').modal('show');
        });
        $("#previewDoc").on("hidden", function() {
            $('#previewDocSource').attr('src', '');
        });
        var taskSuggestions = @json($search_suggestions, true);
        var searchSuggestions = @json($search_term_suggestions, true);
        var cached_suggestions = localStorage['message_suggestions'];
        var suggestions = [];
        $(document).on('click', '.expand-row-msg', function() {
            var id = $(this).data('id');
            var full = '.expand-row-msg .td-full-container-' + id;
            var mini = '.expand-row-msg .td-mini-container-' + id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });
        $(document).on('click', '.expand-row', function() {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });
        $('#completion-datetime, #reminder-datetime, #sending-datetime #due-datetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        $('.due-datetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        $('#daily_activity_date').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        let users = @json($users);
        let isAdmin = <?php echo $isAdmin ? 1 : 0; ?>;
        $("#add-row").click(function() {
            table.addRow({});
        });
        $(".add-task").click(function() {
            var taskId = $(this).attr('data-id');
            $("#add-new-remark").find('input[name="id"]').val(taskId);
        });
        $(".view-remark").click(function() {
            var taskId = $(this).attr('data-id');
            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('task.gettaskremark') }}",
                data: {
                    id: taskId,
                    module_type: "task"
                },
            }).done(response => {
                console.log(response);
                var html = '';
                $.each(response, function(index, value) {
                    html += ' <p> ' + value.remark + ' <br> <small>By ' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></p>';
                    html + "<hr>";
                });
                $("#view-remark-list").find('#remark-list').html(html);
            });
        });
        $(document).ready(function() {
            $(".multiselect").multiselect({
                nonSelectedText: 'Status Filter',
                allSelectedText: 'All',
                includeSelectAllOption: true
            });
            $('.js-example-basic-multiple').select2();
            $('#master_user_id').select2({
                width: "100%"
            });
            $('#search_by_user').select2({
                width: "100%"
            });

            $('#search_by_user').change(function() {
                $("#priority_user_id").select2({
                    tags: true,
                    width: '100%'
                }).val($(this).val()).trigger('change');
            });

            $('#priority_user_id').select2({
                tags: true,
                width: '100%'
            });
            var isLoading = false;
            var page = 1;
            $('#task_reminder_from').datetimepicker({
                format: 'YYYY-MM-DD HH:mm'
            });
            var TaskToRemind = null
            $(document).on('click', '.task-set-reminder', function() {
                let taskId = $(this).data('id');
                let frequency = $(this).data('frequency');
                let message = $(this).data('reminder_message');
                let reminder_from = $(this).data('reminder_from');
                let reminder_last_reply = $(this).data('reminder_last_reply');
                $('#frequency').val(frequency);
                $('#reminder_message').val(message);
                $("#taskReminderModal").find("#task_reminder_from").val(reminder_from);
                if (reminder_last_reply == 1) {
                    $("#taskReminderModal").find("#reminder_last_reply").prop("checked", true);
                } else {
                    $("#taskReminderModal").find("#reminder_last_reply_no").prop("checked", true);
                }
                TaskToRemind = taskId;
            });
            $(document).on('click', '.task-submit-reminder', function() {
                var taskReminderModal = $("#taskReminderModal");
                let frequency = $('#frequency').val();
                let message = $('#reminder_message').val();
                let task_reminder_from = taskReminderModal.find("#task_reminder_from").val();
                let reminder_last_reply = (taskReminderModal.find('#reminder_last_reply').is(":checked")) ? 1 : 0;
                $.ajax({
                    url: "{{ route('task.reminder.update') }}",
                    type: 'POST',
                    success: function() {
                        toastr['success']('Reminder updated successfully!');
                        $(".set-reminder img").css("background-color", "");
                        if (frequency > 0) {
                            $(".task-set-reminder img").css("background-color", "red");
                        }
                    },
                    data: {
                        task_id: TaskToRemind,
                        frequency: frequency,
                        message: message,
                        reminder_from: task_reminder_from,
                        reminder_last_reply: reminder_last_reply,
                        _token: "{{ csrf_token() }}"
                    }
                });
            });
            $(document).on('click', '.btn-call-data', function(e) {
                e.preventDefault();
                var type = $(this).data('type');
                if (type && type != "") {
                    type = $("#tasktype").val(type);
                }

                isLoading = true;
                type = $("#tasktype").val();
                var $loader = $('.infinite-scroll-products-loader');

                page = 1;
                $.ajax({
                    url: "{{url('task')}}",
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function(response) {
                        $loader.hide();
                        if (type == 'pending') {
                            $('.pending-row-render-view').html(response);
                        }
                        if (type == 'statutory_not_completed') {
                            $('.statutory-row-render-view').html(response);
                        }
                        if (type == 'completed') {
                            $('.completed-row-render-view').html(response);
                        }
                        isLoading = false;
                    },
                    error: function() {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            });
            function funTaskInformationUpdatesTime(type,id) {
                if (type == 'start_date') {
                    if (confirm('Are you sure, do you want to update?')) {
                        siteLoader(1);
                        let mdl = funGetTaskInformationModal();
                        jQuery.ajax({
                            headers: {
                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ route('task.update.start-date') }}",
                            type: 'POST',
                            data: {
                                task_id: id,
                                value: $('input[name="start_dates'+id+'"]').val(),
                            }
                        }).done(function(res) {
                            siteLoader(0);
                            siteSuccessAlert(res);
                        }).fail(function(err) {
                            siteLoader(0);
                            siteErrorAlert(err);
                        });
                    }
                } else if (type == 'due_date') {
                    if (confirm('Are you sure, do you want to update?')) {
                        siteLoader(1);
                        let mdl = funGetTaskInformationModal();
                        jQuery.ajax({
                            headers: {
                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ route('task.update.due-date') }}",
                            type: 'POST',
                            data: {
                                task_id: id,
                                value: $('input[name="due_dates'+id+'"]').val(),
                            }
                        }).done(function(res) {
                            siteLoader(0);
                            siteSuccessAlert(res);
                        }).fail(function(err) {
                            siteLoader(0);
                            siteErrorAlert(err);
                        });
                    }
                } else if (type == 'cost') {
                    if (confirm('Are you sure, do you want to update?')) {
                        siteLoader(1);
                        let mdl = funGetTaskInformationModal();
                        jQuery.ajax({
                            headers: {
                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ route('task.update.cost') }}",
                            type: 'POST',
                            data: {
                                task_id: id,
                                cost: mdl.find('input[name="cost"]').val(),
                            }
                        }).done(function(res) {
                            siteLoader(0);
                            siteSuccessAlert(res);
                        }).fail(function(err) {
                            siteLoader(0);
                            siteErrorAlert(err);
                        });
                    }
                } else if (type == 'approximate') {
                    if (confirm('Are you sure, do you want to update?')) {
                        siteLoader(1);
                        let mdl = funGetTaskInformationModal();
                        jQuery.ajax({
                            headers: {
                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ route('task.update.approximate') }}",
                            type: 'POST',
                            data: {
                                task_id: id,
                                approximate: $('input[name="approximates'+id+'"]').val(),
                                remark: mdl.find('textarea[name="remark"]').val(),
                            }
                        }).done(function(res) {
                            siteLoader(0);
                            siteSuccessAlert(res);
                        }).fail(function(err) {
                            siteLoader(0);
                            siteErrorAlert(err);
                        });
                    }
                }
            }

            $('#task_subject, #task_details').autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(taskSuggestions, request.term);
                    response(results.slice(0, 10));
                }
            });
            $('#task_search').autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(searchSuggestions, request.term);
                    response(results.slice(0, 10));
                }
            });
            var hash = window.location.hash.substr(1);
            if (hash == '3') {
                $('a[href="#3"]').click();
            }
            $('.selectpicker').selectpicker({
                selectOnTab: true
            });
            $('#multi_users').select2({
                placeholder: 'Select a User',
            });
            $('#multi_contacts').select2({
                placeholder: 'Select a Contact',
            });
            $(document).on('change', '.is_statutory', function() {
                if ($(".is_statutory").val() == 1) {
                    $("#calendar-task").hide();
                    $('#appointment-container').hide();
                    if (!isAdmin)
                        $('select[name="task_asssigned_to"]').html('<option value="${current_userid}">${ current_username }</option>');
                    $('#recurring-task').show();
                } else if ($(".is_statutory").val() == 2) {
                    $("#calendar-task").show();
                    $('#recurring-task').hide();
                    $('#appointment-container').hide();
                } else if ($(".is_statutory").val() == 3) {
                    $("#calendar-task").hide();
                    $('#recurring-task').hide();
                    $('#appointment-container').show();
                } else {
                    // $("#completion-datetime").show();
                    $("#calendar-task").hide();
                    $('#appointment-container').hide();
                    let select_html = '';
                    for (user of users)
                        select_html += `<option value="${user['id']}">${ user['name'] }</option>`;
                    $('select[name="task_asssigned_to"]').html(select_html);
                    $('#recurring-task').hide();
                }
            });
            jQuery('#userList').select2({
                placeholder: 'All user'
            });
            let r_s = '';
            let r_e = "{{ date('y - m - d ') }}";
            let start = r_s ? moment(r_s, 'YYYY-MM-DD') : moment().subtract(6, 'days');
            let end = r_e ? moment(r_e, 'YYYY-MM-DD') : moment();
            jQuery('input[name="range_start"]').val(start.format('YYYY-MM-DD'));
            jQuery('input[name="range_end"]').val(end.format('YYYY-MM-DD'));
            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
            $('#reportrange').daterangepicker({
                startDate: start,
                maxYear: 1,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);
            cb(start, end);
            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
                jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));
            });
            $(".table").tablesorter();
            var container = $("div#message-container");
            var suggestion_container = $("div#suggestion-container");
            // var sendBtn = $("#waMessageSend");
            var erpUser = "{{ Auth::id() }}";
            var addElapse = false;
            function errorHandler(error) {
                console.error("error occured: ", error);
            }
        });

        $(document).on('click', '.send-message', function() {
            var thiss = $(this);
            var data = new FormData();
            var task_id = $(this).data('taskid');
            // var message = $(this).siblings('input').val();
            if ($(this).hasClass("onpriority")) {
                var message = $('#getMsgPopup' + task_id).val();
            } else {
                var message = $('#getMsg' + task_id).val();
            }
            if (message != "") {
                $("#message_confirm_text").html(message);
                $("#confirm_task_id").val(task_id);
                $("#confirm_message").val(message);
                $("#confirm_status").val(1);
                $("#confirmMessageModal").modal();
            }
        });
        $(document).on('click', '.confirm-messge-button', function() {
            var thiss = $(this);
            var data = new FormData();
            var task_id = $("#confirm_task_id").val();
            var message = $("#confirm_message").val();
            var status = $("#confirm_status").val();
            var is_audio=$("#is_audio_"+task_id).val();
            //    alert(message)
            data.append("task_id", task_id);
            data.append("message", message);
            data.append("status", status);
            data.append("is_audio", is_audio);
            // var checkedValue = $('.send_message_recepients:checked').val();
            var checkedValue = [];
            var i = 0;
            $('.send_message_recepients:checked').each(function() {
                checkedValue[i++] = $(this).val();
            });
            data.append("send_message_recepients", checkedValue);
            //  console.log(checkedValue);
            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        //  url: '/whatsapp/sendMessage/task',
                        url: "{{ route('whatsapp.send','task')}}",
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
                        $('#confirmMessageModal').modal('hide');
                        if (cached_suggestions) {
                            suggestions = JSON.parse(cached_suggestions);
                            if (suggestions.length == 10) {
                                suggestions.push(message);
                                suggestions.splice(0, 1);
                            } else {
                                suggestions.push(message);
                            }
                            localStorage['message_suggestions'] = JSON.stringify(suggestions);
                            cached_suggestions = localStorage['message_suggestions'];
                            console.log('EXISTING');
                            console.log(suggestions);
                        } else {
                            suggestions.push(message);
                            localStorage['message_suggestions'] = JSON.stringify(suggestions);
                            cached_suggestions = localStorage['message_suggestions'];
                            console.log('NOT');
                            console.log(suggestions);
                        }
                        // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
                        //   .done(function( data ) {
                        //
                        //   }).fail(function(response) {
                        //     console.log(response);
                        //     alert(response.responseJSON.message);
                        //   });
                        $(thiss).attr('disabled', false);
                    }).fail(function(errObj) {
                        $('#confirmMessageModal').modal('hide');
                        $(thiss).attr('disabled', false);
                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
        $(document).on('click', '.send-message-lead', function() {
            var thiss = $(this);
            var task_id = $(this).data('taskid');
            var message = $(this).siblings('input').val();
            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: '/whatsapp/sendMessage/task_lead',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            task_id: task_id,
                            message: message,
                            status: 2
                        },
                        beforeSend: function() {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function(response) {
                        console.log(response);
                        $(thiss).siblings('input').val('');
                        $(thiss).attr('disabled', false);
                    }).fail(function(errObj) {
                        console.log(errObj);
                        $(thiss).attr('disabled', false);
                        toastr['error'](errObj.responseJSON.message);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
        $(document).on('click', '.expand-row-btn', function() {
            $(this).closest("tr").find(".expand-col").toggleClass('dis-none');
        });
        
        $(document).on("click", ".set-remark, .sub_remark", function(e) {
            var thiss = $(this);
            var task_id = $(this).data('task_id');
            var remark = $('.remark_pop').val();
            if (task_id != "") {
                $.ajax({
                    type: "POST",
                    url: "{{route('task.create.get.remark')}}",
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        task_id: task_id,
                        remark: remark,
                        type: "TASK",
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    }
                }).done(function(response) {
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
                }).fail(function(response) {
                    $("#loading-image").hide();
                    $("#preview-task-create-get-modal").modal("show");
                    $(".task-create-get-list-view").html("");
                    toastr['error'](response.message);
                });
            } else {
                toastr['error']("Task not Found!");
            }
        });
        $(document).on("click", ".copy_remark", function(e) {
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
        $(document).on('click', '.make-private-task', function() {
            var task_id = $(this).data('taskid');
            var thiss = $(this);
            $.ajax({
                type: "POST",
                url: "{{ url('task') }}/" + task_id + "/makePrivate",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function() {
                    $(thiss).text('Changing...');
                }
            }).done(function(response) {
                if (response.task.is_private == 1) {
                    $(thiss).html('<img src="/images/private.png" />');
                } else {
                    $(thiss).html('<img src="/images/not-private.png" />');
                }
            }).fail(function(response) {
                $(thiss).html('<img src="/images/not-private.png" />');
                console.log(response);
                alert('Could not make task private');
            });
        });
        $(document).on('click', ".collapsible-message", function() {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                var short_message = $(this).data('messageshort');
                var message = $(this).data('message');
                var status = $(this).data('expanded');
                if (status == false) {
                    $(this).addClass('expanded');
                    $(this).html(message);
                    $(this).data('expanded', true);
                    // $(this).siblings('.thumbnail-wrapper').remove();
                    $(this).closest('.talktext').find('.message-img').removeClass('thumbnail-200');
                    $(this).closest('.talktext').find('.message-img').parent().css('width', 'auto');
                } else {
                    $(this).removeClass('expanded');
                    $(this).html(short_message);
                    $(this).data('expanded', false);
                    $(this).closest('.talktext').find('.message-img').addClass('thumbnail-200');
                    $(this).closest('.talktext').find('.message-img').parent().css('width', '200px');
                }
            }
        });
        var selected_messages = [];
        $(document).on('click', '.select-message', function() {
            var message_id = $(this).data('id');
            if ($(this).prop('checked')) {
                selected_messages.push(message_id);
            } else {
                var index = selected_messages.indexOf(message_id);
                selected_messages.splice(index, 1);
            }
            console.log(selected_messages);
        });
        $('#assignMessagesButton').on('click', function(e) {
            e.preventDefault();
            if (selected_messages.length > 0) {
                $('#selected_messages').val(JSON.stringify(selected_messages));
                if ($(this).closest('form')[0].checkValidity()) {
                    $(this).closest('form').submit();
                } else {
                    $(this).closest('form')[0].reportValidity();
                }
            } else {
                alert('Please select atleast 1 message');
            }
        });
        var timer = 0;
        var delay = 200;
        var prevent = false;
        
        $(document).on('click', '.task-verify', function(e) {
            e.preventDefault();
            e.stopPropagation();
            clearTimeout(timer);
            prevent = true;
            var thiss = $(this);
            var task_id = $(this).data('id');
            var image = $(this).html();
            var url = "/task/complete/" + task_id;
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    type: 'clear'
                },
                beforeSend: function() {
                    $(thiss).text('Clearing...');
                }
            }).done(function(response) {
                if (response.task.is_verified != null) {
                    $(thiss).html('<img src="/images/completed.png" />');
                } else if (response.task.is_completed != null) {
                    $(thiss).html('<img src="/images/completed-green.png" />');
                } else {
                    $(thiss).html('<img src="/images/incomplete.png" />');
                }
                $(thiss).attr('disabled', true);
            }).fail(function(response) {
                $(thiss).html(image);
                alert('Could not clear the task!');
                console.log(response);
            });
        });
        $(document).on('click', '.resend-message', function() {
            var id = $(this).data('id');
            var thiss = $(this);
            $.ajax({
                type: "POST",
                url: "{{ url('whatsapp') }}/" + id + "/resendMessage",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function() {
                    $(thiss).text('Sending...');
                }
            }).done(function(response) {
                $(thiss).html('<img src="/images/resend.png" />');
            }).fail(function(response) {
                $(thiss).html('<img src="/images/resend.png" />');
                console.log(response);
                alert('Could not resend message');
            });
        });
        $(document).on('click', '#addNoteButton', function() {
            var note_html = `<div class="form-group d-flex">
            <input type="text" class="form-control input-sm" name="note[]" placeholder="Note" value="">
            <button type="button" class="btn btn-image remove-note">x</button>
          </div>`;
            $('#note-container').append(note_html);
        });
        $(document).on('click', '.remove-note', function() {
            $(this).closest('.form-group').remove();
        });
        
        $(document).on('click', '.convert-task-appointment', function() {
            var thiss = $(this);
            var id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "{{ url('task') }}/" + id + "/convertTask",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function() {
                    $(thiss).text('Converting...');
                }
            }).done(function(response) {
                $(thiss).closest('tr').addClass('row-highlight');
                $(thiss).remove();
            }).fail(function(response) {
                $(thiss).html('<img src="/images/details.png" />');
                console.log(response);
                alert('Could not convert a task');
            });
        });
        var selected_tasks = [];
        $(document).on('click', '.select_task_checkbox', function() {
            var checked = $(this).prop('checked');
            var id = $(this).data('id');
            if (checked) {
                selected_tasks.push(id);
            } else {
                var index = selected_tasks.indexOf(id);
                selected_tasks.splice(index, 1);
            }
            console.log(selected_tasks);
        });
        $(document).on('click', '.submit-category-status', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            $.ajax({
                type: "POST",
                url: form.attr("action"),
                data: form.serialize(),
            }).done(function(response) {
                toastr["success"](response.message);
                $('#allTaskCategoryModal').modal('hide');
            }).fail(function(response) {});
        });
        
        $(".btn-send-brodcast-message").on("click", function() {
            if (selected_tasks.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('tasks/send-brodcast') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        selected_tasks: selected_tasks,
                        message: $(".message-for-brodcast").val()
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr["success"](response.message);
                        $("#send-message-text-box").modal("hide");
                    } else {
                        toastr["error"](response.message);
                    }
                }).fail(function(response) {
                    $("#loading-image").hide();
                    console.log(response);
                    toastr["error"]("Request has been failed due to the server , please contact administrator");
                });
            } else {
                $("#loading-image").hide();
                toastr["error"]("Please select atleast 1 task!");
            }
        });
        
        
        $(document).on('click', '.whatsapp-group', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $("#task_id").val(id);
            $("#Preloader").show();
            $.ajax({
                type: "POST",
                async: false,
                url: "{{ route('task.add.whatsapp.group') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                }
            }).done(function(response) {
                console.log(response);
                $("#group_id").val(response.group_id);
                $("#Preloader").hide();
            })
        });
        $(document).on('keyup', '.save-milestone', function(event) {
            if (event.keyCode != 13) {
                return;
            }
            let id = $(this).attr('data-id');
            let total = $(this).val();
            $.ajax({
                url: "{{action([\App\Http\Controllers\TaskModuleController::class, 'saveMilestone'])}}",
                data: {
                    total: total,
                    task_id: id
                },
                success: function() {
                    toastr["success"]("Milestone updated successfully!", "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message")
                    console.log(error.responseJSON.message);
                }
            });
        });
        $(document).on("change", ".select2-task-disscussion", function() {
            var $this = $(this);
            if ($this.val() != 0) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('task.json.details') }}",
                    data: {
                        task_id: $this.val()
                    },
                    dataType: "json"
                }).done(function(response) {
                    if (response.code == 200) {
                        $("#saveNewNotes").removeClass("dis-none");
                    } else {
                        alert(response.message);
                        $("#saveNewNotes").addClass("dis-none");
                    }
                }).fail(function(response) {
                    alert('Could not update!!');
                });
            } else {
                $("#saveNewNotes").addClass("dis-none");
            }
        });
        $(document).on("click", "#saveNewNotes", function() {
            var $this = $(this);
            $.ajax({
                beforeSend: function() {
                    toastr['info']('Sending data!!', 'info');
                },
                type: 'POST',
                url: "{{ route('task.json.saveNotes') }}",
                data: $("#taskCreateForm").serialize(),
                dataType: "json"
            }).done(function(response) {
                if (response.code == 200) {
                    //toastr['success']('Success!!', 'success');
                    location.reload();
                }
            }).fail(function(response) {
                alert('Could not update!!');
            });
        });
        $(document).on("click", ".delete-task-btn", function() {
            var $this = $(this);
            var taskId = $this.data("id");
            if (taskId > 0) {
                $.ajax({
                    beforeSend: function() {
                        $("#loading-image").show();
                    },
                    type: 'POST',
                    url: "/tasks/deleteTask",
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: taskId
                    },
                    dataType: "json"
                }).done(function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        $this.closest("td").remove();
                    }
                }).fail(function(response) {
                    $("#loading-image").hide();
                    alert('Could not update!!');
                });
            }
        });
        $(document).on("click", ".show-finished-task", function() {
            var $this = $(this);
            if ($this.is(":checked")) {
                $this.closest("table").find("tbody tr").hide();
                $this.closest("table").find("tbody tr").filter(function() {
                    return $(this).find('.task-complete img').attr('src') === "/images/completed-green.png";
                }).show();
            } else {
                $this.closest("table").find("tbody tr").show();
            }
        });
        $(document).on('change', '#is_milestone', function() {
            var is_milestone = $('#is_milestone').val();
            if (is_milestone == '1') {
                $('#no_of_milestone').attr('required', 'required');
            } else {
                $('#no_of_milestone').removeAttr('required');
            }
        });
        $(document).on('change', '.assign-master-user', function() {
            let id = $(this).attr('data-id');
            let lead = $(this).attr('data-lead');
            let userId = $(this).val();
            if (userId == '') {
                return;
            }
            $.ajax({
                url: '{{ route("task.asign.master-user"); }}',
                data: {
                    master_user_id: userId,
                    issue_id: id,
                    lead: lead
                },
                success: function() {
                    toastr["success"]("Master User assigned successfully!", "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message")
                }
            });
        });
        var uploadedDocumentMap = {}
        Dropzone.options.documentDropzone = {
            url: '{{ route("task.upload-documents") }}',
            maxFilesize: 20, // MB
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(file, response) {
                $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
                uploadedDocumentMap[file.name] = response.name
            },
            removedfile: function(file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedDocumentMap[file.name]
                }
                $('form').find('input[name="document[]"][value="' + name + '"]').remove()
            },
            init: function() {
            }
        }
        $(document).on("click", ".btn-save-documents", function(e) {
            e.preventDefault();
            var $this = $(this);
            var formData = new FormData($this.closest("form")[0]);
            $.ajax({
                url: '/task/save-documents',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: "json",
                data: $this.closest("form").serialize(),
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(data) {
                $("#loading-image").hide();
                if (data.code == 500) {
                    toastr["error"](data.message);
                } else {
                    toastr["success"]("Document uploaded successfully");
                    //location.reload();
                }
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr["error"](jqXHR.responseJSON.message);
                $("#loading-image").hide();
            });
        });
        function humanizeDuration(input, units) {
            // units is a string with possible values of y, M, w, d, h, m, s, ms
            var duration = moment().startOf('day').add(units, input),
                format = "";
            if (duration.hour() > 0) {
                format += "H:";
            }
            if (duration.minute() > 0) {
                format += "m:";
            }
            format += "s";
            return duration.format(format);
        }
        $(document).on('click', '.show-tracked-history', function() {
            var issueId = $(this).data('id');
            var type = $(this).data('type');
            $('#time_tracked_div table tbody').html('');
            $.ajax({
                url: "{{ route('task.time.tracked.history') }}",
                data: {
                    id: issueId,
                    type: type
                },
                success: function(data) {
                    console.log(data);
                    if (data != 'error') {
                        $.each(data.histories, function(i, item) {
                            var sec = parseInt(item['total_tracked']);
                            $('#time_tracked_div table tbody').append(
                                '<tr>\
                                    <td>' + moment(item['starts_at_date']).format('DD-MM-YYYY') + '</td>\
                                    <td>' + ((item['name'] != null) ? item['name'] : '') + '</td>\
                                    <td>' + humanizeDuration(sec, 's') + '</td>\
                                </tr>'
                            );
                        });
                    }
                }
            });
            $('#time_tracked_modal').modal('show');
        });
        $(document).on('click', '.create-hubstaff-task', function() {            
            var issueId = $(this).data('id');
            var type = $(this).data('type');
            $("#issueId").val( issueId );
            $("#type").val( type );
            $('#create-d-task-modal').modal('show');

            $(this).css('display', 'none');
        });
        $(document).on("keyup", ".search-category", function() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            ul = document.getElementById("myUL");
            li = ul.getElementsByTagName("li");
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        });
        
        $(document).on("click", ".link-send-document", function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            var user_id = $(this).closest("tr").find(".send-message-to-id").val();
            var doc_id = $(this).data("media-id");
            $.ajax({
                url: '/task/send-document',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: "json",
                data: {
                    id: id,
                    user_id: user_id,
                    doc_id: doc_id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(data) {
                $("#loading-image").hide();
                toastr["success"]("Document sent successfully");
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr["error"](jqXHR.responseJSON.message);
                $("#loading-image").hide();
            });
        });
        $(document).on("click", ".link-send-task", function(e) {
            var id = $(this).data("id");
            var task_id = $(this).data("media-id");
            var taskdata = $(this).parent().find("#selector_id").val();
            console.log(task_id, taskdata);
            var type = $(this).parent().find('#selector_id option[value="' + taskdata + '"]').html().includes('DEVTASK') ? 'DEVTASK' : 'TASK';
            if ($(this).parent().find("#selector_id").val() == '') {
                toastr["error"]('Please Select Task Or DevTask', "Message")
                return false;
            }
            $.ajax({
                url: '/task/send',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: "json",
                data: {
                    id: id,
                    task_id: task_id,
                    taskdata: taskdata,
                    type: type
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    toastr["success"]("File sent successfully");
                },
                error: function(error) {
                    toastr["error"];
                }
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
                    toastr["success"]("File Added Successfully In Sop");
                },
                error: function(error) {
                    toastr["error"];
                }
            });
        });
        // on status change
        $(document).on('change', '.change-task-status', function() {
            let id = $(this).attr('data-id');
            let status = $(this).val();
            $.ajax({
                url: "{{route('task.change.status')}}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: "json",
                data: {
                    'task_id': id,
                    'status': status
                },
                success: function(response) {
                    toastr["success"](response.message, "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message")
                }
            });
        });
        $(document).on('change', '.assign-user', function() {
            let id = $(this).attr('data-id');
            let userId = $(this).val();
            if (userId == '') {
                return;
            }
            $.ajax({
                url: "{{route('task.AssignTaskToUser')}}",
                data: {
                    user_id: userId,
                    issue_id: id
                },
                success: function() {
                    toastr["success"]("User assigned successfully!", "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message")
                }
            });
        });
        $(document).on('click', '.show-user-history', function() {
            var issueId = $(this).data('id');
            $('#user_history_div table tbody').html('');
            $.ajax({
                url: "{{ route('task/user/history') }}",
                data: {
                    id: issueId
                },
                success: function(data) {
                    $.each(data.users, function(i, item) {
                        $('#user_history_div table tbody').append(
                            '<tr>\
                                    <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                    <td>' + ((item['user_type'] != null) ? item['user_type'] : '-') + '</td>\
                                    <td>' + ((item['old_name'] != null) ? item['old_name'] : '-') + '</td>\
                                    <td>' + ((item['new_name'] != null) ? item['new_name'] : '-') + '</td>\
                                    <td>' + item['updated_by'] + '</td>\
                                </tr>'
                        );
                    });
                }
            });
            $('#user_history_modal').modal('show');
        });
        $(document).on('click', '.cmn-toggle', function() {
            let id = $(this).attr('task-id');
            var showstatus = "";
            $.ajax({
                url: "{{route('task.CommunicationTaskStatus')}}",
                data: {
                    task_id: id
                },
                success: function(response) {
                    if (response.communication_status == 1) {
                        $('#getMsg' + id).prop("readonly", true);
                        $('#sendMsg' + id).prop("readonly", true);
                        showstatus = "Off";
                    }
                    if (response.communication_status == 0) {
                        $('#getMsg' + id).prop("readonly", false);
                        $('#sendMsg' + id).prop("readonly", false);
                        showstatus = "On";
                    }
                    toastr["success"]("Communication message status is " + showstatus + " successfully", "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message")
                }
            });
        });
        $(document).on('click', '.recurring-history-btn', function() {
            var task_id = $(this).data('id');
            console.log(task_id);
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('task.recurringHistory') }}",
                data: {
                    task_id: task_id,
                },
            }).done(response => {
                $('#recurring-history-modal').find('.recurring-history-list-view').html('');
                if (response.success == true) {
                    $('#recurring-history-modal').find('.recurring-history-list-view').html(response.html);
                    $('#recurring-history-modal').modal('show');
                }
            }).fail(function(response) {
                alert('Could not fetch payments');
            });
        });

        $(document).on('change', '.task_for_modal', function(e) {
            var getTask = $(this).val();
            if(getTask == 'time_doctor'){
                $('.time_doctor_project_section_modal').show();
                $('.time_doctor_account_section_modal').show();
            } else {
                $('.time_doctor_project_section_modal').hide();
                $('.time_doctor_account_section_modal').hide();
            }
        });

        $(document).on('submit', '#assign_task_form', function(event) {
            event.preventDefault();
            $.ajax({
                url: "{{route('task.create.hubstaff_task')}}",
                type: 'POST',
                data: $(this).serialize(),
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    toastr['success']('created successfully!');
                    $('#create-d-task-modal').modal('hide');
                    $("#loading-image").hide();
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message);
                    $('#create-d-task-modal').modal('hide');
                    $("#loading-image").hide();
                }
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
            applyDateTimePicker(jQuery('.cls-start-due-date'));
            $(document).on('click', ".create-task-document", function () {
                let task_id = $(this).data('id');
                if(task_id != "") {
                    $("#task_id").val($(this).data('id'));
                    $("#taskGoogleDocModal").modal('show');
                } else {
                    toastr["error"]("Task id not found.");
                }
            });

            $(document).on('click', ".show-created-task-document", function () {
                let task_id = $(this).data('id');
                if(task_id != "") {
                    $.ajax({
                        type: "GET",
                        url: "{{route('google-docs.task.show')}}",
                        data: {
                            task_id,
                            task_type: "TASK"
                        },
                        beforeSend: function() {
                            $("#loading-image").show();
                            // $("#btnCreateTaskDocument").attr('disabled', true)
                        },
                        success: function (response) {
                            $("#loading-image").hide();
                            if(typeof response.data != 'undefined') {
                                $("#taskGoogleDocListModal tbody").html(response.data);
                            } else {
                                // display unauthorized permission message
                                $("#taskGoogleDocListModal tbody").html(response);
                            }
                            $("#taskGoogleDocListModal").modal('show');
                        },
                        error: function(response) {
                            toastr["error"]("Something went wrong!");
                            $("#loading-image").hide();
                        }
                    });
                } else {
                    toastr["error"]("Task id not found.");
                }
            });
            
            $(document).on('click', "#btnCreateTaskDocument", function () {
                let doc_type = $("#doc-type").val();
                let doc_name = $("#doc-name").val();
                // let doc_category = $("#doc-category").val();
                let task_id = $("#task_id").val();
                
                if(doc_type.trim() == "") {
                    toastr["error"]("Select document type.");
                    return
                }
                if(doc_name.trim() == "") {
                    toastr["error"]("Insert document name.");
                    return
                }
                // if(doc_category.trim() == "") {
                //     toastr["error"]("Insert document category.");
                //     return
                // }

                $.ajax({
                    type: "POST",
                    url: "{{route('google-docs.task')}}",
                    data: {
                        _token: "{{csrf_token()}}",
                        // doc_category,
                        doc_type,
                        doc_name,
                        task_id,
                        task_type: "TASK",
                        attach_task_detail: true
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                        $("#btnCreateTaskDocument").attr('disabled', true)
                    },
                    success: function (response) {
                        if(response.status == true) {
                            toastr["success"](response.message);
                        } else {
                            toastr["error"](response.message);
                        }
                        $("#loading-image").hide();
                        $("#btnCreateTaskDocument").removeAttr('disabled')
                        $("#taskGoogleDocModal").modal('hide');
                        $("#doc-type").val(null);
                        $("#doc-name").val(null);
                        $("#doc-category").val(null);
                        $("#task_id").val(null);
                    },
                    error: function(response) {
                        toastr["error"]("Something went wrong!");
                        $("#loading-image").hide();
                        $("#btnCreateTaskDocument").removeAttr('disabled')
                    }
                });

            });

            $(document).on("click", ".upload-task-files-button", function (e) {
                e.preventDefault();
                let task_id = $(this).data("task_id");
                $("#uploadeTaskFileModal #upload_task_id").val(task_id || 0);
                $("#uploadeTaskFileModal").modal("show")
            });

            $(document).on("click", ".view-task-files-button", function (e) {
                e.preventDefault();
                let task_id = $(this).data("task_id");
                $.ajax({
                    type: "get",
                    url: "{{route('task.files.record')}}",
                    data: {
                        task_id
                    },
                    success: function (response) {
                        if(typeof response.data != 'undefined') {
                            $("#taskFileUploadedData").html(response.data);
                        } else {
                            // display unauthorized permission message
                            $("#taskFileUploadedData").html(response);
                        }
                        
                        $("#displayTaskFileUpload").modal("show")
                    },
                    error: function (response) {
                        toastr['error']("Something went wrong!");
                    }
                });
            });

            $(document).on('click', '.btn-trigger-rvn-modal',function () {
                var id=$(this).attr('data-id')
                var tid=$(this).attr('data-tid')
                $("#record-voice-notes #rvn_id").val(id);
                $("#record-voice-notes #rvn_tid").val(tid);
                $("#record-voice-notes").modal("show");
            });
            $('#record-voice-notes').on('hidden.bs.modal', function () {
                $("#rvn_stopButton").trigger("click");
                $("#formats").html("Format: start recording to see sample rate");
                $("#rvn_id").val(0);
                $("#rvn_tid").val(0);
                setTimeout(function () {
                    $("#recordingsList").html('');
                }, 2500);
            })
            $(document).on('click', '.show-hubtask-log-history', function() {
                var id = $(this).attr('data-id');
                $.ajax({
                    url: '{{ route("task.log.histories.show", '') }}/' + id,
                    dataType: "json",
                    data: {
                        id:id,
                    },
                    success: function(response) {
                        if (response.status) {
                            var html = "";
                            $.each(response.data, function(k, v) {
                                html += `<tr>
                                            <td> ${k + 1} </td>
                                            <td> ${v.task ? v.task.task_subject : ''} </td>
                                            <td> ${v.error_message ? v.error_message : ''} </td>
                                            <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                            <td> ${v.created_at} </td>
                                        </tr>`;
                            });
                            $("#task-create-log-listing").find(".task-log-listing-view").html(html);
                            $("#task-create-log-listing").modal("show");
                        } else {
                            toastr["error"](response.error, "Message");
                        }
                    }
                });
            });
        });
    </script>
@endsection