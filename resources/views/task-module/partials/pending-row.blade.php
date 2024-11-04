@if (Auth::user()->isAdmin())
    @if(!empty($dynamicColumnsToShowTask))
        <tr style="background-color: {{isset($task->taskStatus) ? $task->taskStatus->task_color : ''}}!important;"
            class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} {{ !$task->due_date ? 'no-due-date' : '' }} {{ $task->is_statutory == 3 ? 'row-highlight' : '' }}"
            id="task_{{ $task->id }}">

            @if (!in_array('ID', $dynamicColumnsToShowTask))
                <td class="p-2">
                    @if(auth()->user()->isAdmin())
                        <input type="checkbox" name="selected_issue[]" value="{{$task->id}}"
                               title="Task is in priority" {{in_array($task->id, $priority) ? 'checked' : ''}}>
                    @endif
                    <input type="checkbox" title="Select task" class="select_task_checkbox" name="task"
                           data-id="{{ $task->id }}" value="">
                    {{ $task->id }}
                </td>
            @endif

            @if (!in_array('Date', $dynamicColumnsToShowTask))
                <td class="p-2">{{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}
                    <br>
                    @if($task->customer_id)
                        Cus-{{$task->customer_id}}
                        <br>
                        @if(Auth::user()->isAdmin())
                            <span>
                            {{ isset($task->customer ) ? $task->customer->name : '' }}
                        </span>
                        @endif
                    @endif
                </td>
            @endif

            @if (!in_array('Category', $dynamicColumnsToShowTask))
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
            @endif

            @if (!in_array('Subject', $dynamicColumnsToShowTask))
                <td class="expand-row" data-subject="{{$task->task_subject ? $task->task_subject : 'Task Details'}}"
                    data-details="{{$task->task_details}}" data-switch="0">
                <span class="td-mini-container">
                    {{ $task->task_subject ? substr($task->task_subject, 0, 15) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
                </span>
                    <span class="td-full-container hidden">
                    {{ $task->task_subject ? $task->task_subject : 'Task Details' }}
                </span>
                </td>
            @endif

            @if (!in_array('Assign To', $dynamicColumnsToShowTask))
                <td class="table-hover-cell p-2">
                    @php
                        $special_task = $task;
                        $users_list = \App\Helpers::getTaskUserList($task, $users);
                    @endphp

                    @if(auth()->user()->isAdmin())
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
                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history"
                            title="Show History" data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
                    <div class="col-md-12 expand-col-lead{{$task->id}} dis-none" style="padding:0px;">
                        <br>
                        <label for="" style="font-size: 12px;margin-top:10px;">Lead :</label>
                        @php
                            $selectBoxId = 'master_user_id';
                            $selectClass = "assign-master-user";
                            $type="master-user";
                        @endphp
                        @include('task-module.partials.select-user',compact('task', 'users', 'selectBoxId', 'selectClass', 'type'))
                        <br />
                        @if(auth()->user()->isAdmin())
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


                        <label for="" style="font-size: 12px;margin-top:10px;">Due date :</label>
                        <div class="d-flex">
                            <div class="form-group" style="padding-top:5px;">
                                <div class='input-group date due-datetime'>
                                    <input type="text" class="form-control input-sm due_date_cls" name="due_date"
                                           value="{{$task->due_date}}" />
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-image set-due-date" title="Set due date"
                                    data-taskid="{{ $task->id }}"><img style="padding: 0;margin-top: -14px;"
                                                                       src="{{asset('images/filled-sent.png')}}" />
                            </button>
                        </div>

                        @if($task->is_milestone)
                            <p style="margin-bottom:0px;">Total : {{$task->no_of_milestone}}</p>
                            @if($task->no_of_milestone == $task->milestone_completed)
                                <p style="margin-bottom:0px;">Done : {{$task->milestone_completed}}</p>
                            @else
                                <input type="number" name="milestone_completed" id="milestone_completed_{{$task->id}}"
                                       placeholder="Completed..." class="form-control save-milestone"
                                       value="{{$task->milestone_completed}}" data-id="{{$task->id}}">
                            @endif
                        @else
                            <p>No milestone</p>
                        @endif
                    </div>
                </td>
            @endif

            @if (!in_array('Status', $dynamicColumnsToShowTask))
                <td>
                    <select id="master_user_id" class="form-control change-task-status select2" data-id="{{$task->id}}"
                            name="master_user_id" id="user_{{$task->id}}">
                        <option value="">Select...</option>
                            <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                        @if(!empty($task_statuses))
                            @foreach($task_statuses as $index => $status)
                                @if( $status->id == $task->status )
                                    <option value="{{$status->id}}" selected>{{ $status->name }}</option>
                                @else
                                    <option value="{{$status->id}}">{{ $status->name }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </td>
            @endif

            @if (!in_array('Tracked time', $dynamicColumnsToShowTask))
                <td>
                    <div class="d-flex">
                        <button type="button" class="btn btn-xs show-time-history" title="Show Estimation History"
                                data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
                    </div>
                    @if (isset($special_task->timeSpent) && $special_task->timeSpent->task_id > 0)
                        {{ formatDuration($special_task->timeSpent->tracked) }}

                        <button style="float:right;padding-right:0px;" type="button"
                                class="btn btn-xs show-tracked-history" title="Show tracked time History"
                                data-id="{{$task->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button>
                    @endif
                </td>
            @endif

            @if (!in_array('Communication', $dynamicColumnsToShowTask))
                <td class="table-hover-cell p-2 {{ ($task->message && $task->message_status == 0) || $task->message_is_reminder == 1 || ($task->message_user_id == $task->assign_from && $task->assign_from != Auth::id()) ? 'text-danger' : '' }}">
                    @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))

                        <div style="margin-bottom:10px;width: 100%;">
                                <?php $text_box = '100'; ?>
                            <input type="text" style="width: 100%;" class="form-control quick-message-field input-sm"
                                   id="getMsg{{$task->id}}" name="message" placeholder="Message" value="">
                            <div class="d-flex">
                                <div style="">
                                    <button id="send-message_{{ $task->id }}" class="btn btn-sm btn-image send-message"
                                            title="Send message" data-taskid="{{ $task->id }}"><img
                                                src="{{asset('images/filled-sent.png')}}" /></button>

                                    <input type="hidden" name="is_audio" id="is_audio_{{$task->id}}" value="0">
                                    <button type="button"
                                            class="btn btn-sm m-0 p-0 mr-1 btn-image btn-trigger-rvn-modal"
                                            data-id="{{$task->id}}" data-tid="{{$task->id}}" data-load-type="text"
                                            data-all="1" title="Record & Send Voice Message"><img
                                                src="{{asset('images/record-voice-message.png')}}" alt=""></button>
                                </div>
                                @if (isset($task->message))
                                    <div style="max-width: 30px;">
                                        <button type="button" class="btn btn-xs btn-image load-communication-modal"
                                                data-object='task' data-id="{{ $task->id }}" title="Load messages"><img
                                                    src="{{asset('images/chat.png')}}" alt=""></button>
                                    </div>
                                @endif
                                <button class="btn btn-image upload-task-files-button ml-2" type="button"
                                        title="Uploaded Files" data-task_id="{{$task->id}}">
                                    <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                </button>
                                <button class="btn btn-image view-task-files-button ml-2" type="button"
                                        title="View Uploaded Files" data-task_id="{{$task->id}}">
                                    <img src="/images/google-drive.png" style="cursor: nwse-resize; width: 10px;">
                                </button>
                            </div>
                            @if (isset($task->message))

                                <div style="margin-bottom:10px;width: 100%;">
                                    @if (isset($task->is_audio) && $task->is_audio)
                                        <audio controls=""
                                               src="{{ \App\Helpers::getAudioUrl($task->message) }}"></audio>
                                    @else
                                        <div class="d-flex justify-content-between expand-row-msg"
                                             data-id="{{$task->id}}">
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
                                {{substr($task->message, $start,100)}}
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
                    @else
                        Private
                    @endif
                </td>
            @endif

            @php
                $single = \App\Helpers\DevelopmentHelper::getTaskHistoryByTaskID($task->id);
            @endphp

            @if (!in_array('Estimated Time', $dynamicColumnsToShowTask))
                <td class="p-2">
                    <div style="margin-bottom:10px;width: 100%;">
                        <div class="d-flex">
                            <input type="number" class="form-control" name="approximates{{$task->id}}"
                                   value="{{$task->approximate}}" min="1" autocomplete="off">
                            <div style="max-width: 30px;">
                                <button class="btn btn-sm btn-image send-approximate-lead" title="Send approximate"
                                        onclick="funTaskInformationUpdatesTime('approximate',{{$task->id}})"
                                        data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                                </button>
                            </div>
                        </div>
                    </div>

                        <?php
                            $time_history = \App\Helpers\DevelopmentHelper::getFirstDeveloperTaskHistoryByDevTaskIDAndModel($task->id);
                            ?>

                    @if(!empty($time_history))
                        @if (isset($time_history->is_approved) && $time_history->is_approved != 1)
                            <button data-task="{{$time_history->developer_task_id}}" data-id="{{$time_history->id}}"
                                    title="approve" data-type="TASK"
                                    class="btn btn-sm approveEstimateFromshortcutButtonTaskPage">
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </button>
                        @endif

                        @if($task->task_start!=1)
                            <button data-task="{{$task->id}}" title="Start Task" data-type="TASK"
                                    class="btn btn-sm startDirectTask" data-task-type="1">
                                <i class="fa fa-play" aria-hidden="true"></i>
                            </button>
                        @else
                            <input type="hidden" value="{{$task->m_start_date}}" class="m_start_date_"
                                   id="m_start_date_{{$task->id}}" data-id="{{$task->id}}" data-value="{{$task->id}}">
                            <button data-task="{{$task->id}}" title="Start Task" data-type="TASK"
                                    class="btn btn-sm startDirectTask" data-task-type="2">
                                <i class="fa fa-stop" aria-hidden="true"></i>
                            </button>
                            <div id="time-counter_{{$task->id}}"></div>
                        @endif

                        <button type="button" class="btn btn-xs show-timer-history" title="Show timer History"
                                data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
                    @endif
                </td>
            @endif

            @if (!in_array('Estimated Start Datetime', $dynamicColumnsToShowTask))
                <td class="p-2">
                    <div class="form-group d-flex">
                        <div class='input-group date cls-start-due-date'>
                            <input type="text" class="form-control" name="start_dates{{$task->id}}"
                                   value="{{$single->taskhistoryForStartDate->first() ? $single->taskhistoryForStartDate->first()->new_value : ''}}"
                                   autocomplete="off" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                        <div style="max-width: 30px;">
                            <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                                    onclick="funTaskInformationUpdatesTime('start_date',{{$task->id}})"
                                    data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                            </button>
                        </div>
                    </div>
                    @if(!empty($single->taskhistoryForStartDate->first()->new_value) && $single->taskhistoryForStartDate->first()->new_value!='0000-00-00 00:00:00')
                        {{$single->taskhistoryForStartDate->first()->new_value}}
                    @endif

                    <div class="form-group d-flex">
                        <div class='input-group date cls-start-due-date'>
                            <input type="text" class="form-control" name="due_dates{{$task->id}}"
                                   value="{{$single->taskDueDateHistoryLogs->first() ? $single->taskDueDateHistoryLogs->first()->new_due_date : ''}}"
                                   autocomplete="off" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                        <div style="max-width: 30px;">
                            <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                                    onclick="funTaskInformationUpdatesTime('due_date',{{$task->id}})"
                                    data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                            </button>
                        </div>
                    </div>

                    @if(!empty($single->taskDueDateHistoryLogs->first()->new_due_date) && $single->taskDueDateHistoryLogs->first()->new_due_date!='0000-00-00 00:00:00')
                        {{$single->taskDueDateHistoryLogs->first()->new_due_date}}
                    @endif
                </td>
            @endif

            @if (!in_array('Shortcuts', $dynamicColumnsToShowTask))
                <td id="shortcutsIds">
                    @include('task-module.partials.shortcuts')
                </td>
            @endif

            @if (!in_array('ICON', $dynamicColumnsToShowTask))
                <td class="p-2">
                    <button type="button" class="btn btn-secondary btn-sm mt-2"
                            onclick="Showactionbtn('{{$task->id}}')"><i class="fa fa-arrow-down"></i></button>
                </td>
            @endif
        </tr>
        @if (!in_array('ICON', $dynamicColumnsToShowTask))
            <tr class="action-btn-tr-{{$task->id}} d-none">
                <td class="font-weight-bold">Action</td>
                <td colspan="11">
                    <div class="row cls_action_box" style="margin:0px;">
                        @if(auth()->user()->isAdmin())
                            <button type="button" class='btn btn-image whatsapp-group pd-5' data-id="{{ $task->id }}"
                                    data-toggle='modal' data-target='#whatsAppMessageModal'><img
                                        src="{{asset('images/whatsapp.png')}}" /></button>
                        @endif

                        <button data-toggle="modal" data-target="#taskReminderModal" class='btn pd-5 task-set-reminder'
                                data-id="{{ $task->id }}"
                                data-frequency="{{ !empty($task->reminder_message) ? $task->frequency : '60' }}"
                                data-reminder_message="{{ !empty($task->reminder_message) ? $task->reminder_message : 'Plz update' }}"
                                data-reminder_from="{{ $task->reminder_from }}"
                                data-reminder_last_reply="{{ ($task && !empty($task->reminder_last_reply)) ? $task->reminder_last_reply : '' }}">
                            <i class="fa fa-bell @if(!empty($task->reminder_message) && $task->frequency > 0) {{ 'green-notification'  }} @else {{ 'red-notification' }} @endif"
                               aria-hidden="true"></i>
                        </button>

                        @if ($special_task->users->contains(Auth::id()) || $task->assign_from == Auth::id() || $task->master_user_id == Auth::id())
                            <button type="button" title="Complete the task by user"
                                    class="btn btn-image task-complete pd-5" data-id="{{ $task->id }}"><img
                                        src="/images/incomplete.png" /></button>
                            @if ($task->assign_from == Auth::id())
                                <button type="button" title="Verify the task by admin"
                                        class="btn btn-image task-complete pd-5" data-id="{{ $task->id }}"><img
                                            src="/images/completed-green.png" /></button>
                            @else
                                <button type="button" class="btn btn-image pd-5"><img
                                            src="{{asset('/images/completed-green.png')}}" /></button>
                            @endif

                            @include('task-module.partials.show-status-history-btn')

                            <button type="button" class='btn btn-image ml-1 reminder-message pd-5'
                                    data-id="{{ $task->message_id }}" data-toggle='modal'
                                    data-target='#reminderMessageModal'><img src='/images/reminder.png' /></button>

                            <button type="button" data-id="{{ $task->id }}" class="btn btn-file-upload pd-5">
                                <i class="fa fa-upload" aria-hidden="true"></i>
                            </button>

                            <button type="button" class="btn preview-img-btn pd-5" data-id="{{ $task->id }}">
                                <i class="fa fa-list" aria-hidden="true"></i>
                            </button>
                        @endif
                        @if ((!$special_task->users->contains(Auth::id()) && $special_task->contacts()->count() == 0))
                            @if ($task->is_private == 1)
                                <button disabled type="button" class="btn btn-image pd-5">
                                    <img
                                            src="{{asset('images/private.png')}}" /></button>
                            @endif
                        @endif

                        @if ($special_task->users->contains(Auth::id()) || ($task->assign_from == Auth::id() && $task->is_private == 0) || ($task->assign_from == Auth::id() && $special_task->contacts()->count() > 0) || Auth::id() == 6)
                            <a href="{{ route('task.show', $task->id) }}" class="btn btn-image pd-5" href=""><img
                                        src="{{asset('images/view.png')}}" /></a>
                        @endif

                        @if ($task->is_flagged == 1)
                            <button type="button" class="btn btn-image flag-task pd-5" data-id="{{ $task->id }}">
                                <img
                                        src="{{asset('images/flagged.png')}}" /></button>
                        @else
                            <button type="button" class="btn btn-image flag-task pd-5" data-id="{{ $task->id }}">
                                <img
                                        src="{{asset('images/unflagged.png')}}" /></button>
                        @endif
                        <button class="btn btn-image expand-row-btn-lead" data-task_id="{{ $task->id }}"><img
                                    src="{{asset('/images/forward.png')}}"></button>

                        <button class="btn btn-image mt-2 create-task-document" title="Create document"
                                data-id="{{$task->id}}">
                            <i class="fa fa-file-text" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-image mt-2 show-created-task-document" title="Show created document"
                                data-id="{{$task->id}}">
                            <i class="fa fa-list" aria-hidden="true"></i>
                        </button>

                        <a title="Task Information: Update" class="btn btn-image mt-2" href="javascript:void(0);"
                           onclick="funTaskInformationModal(this, '{{$task->id}}')">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endif
    @else
        <tr style="background-color: {{isset($task->taskStatus) ? $task->taskStatus->task_color : ''}}!important;"
            class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} {{ !$task->due_date ? 'no-due-date' : '' }} {{ $task->is_statutory == 3 ? 'row-highlight' : '' }}"
            id="task_{{ $task->id }}">
            <td class="p-2">
                @if(auth()->user()->isAdmin())
                    <input type="checkbox" name="selected_issue[]" value="{{$task->id}}"
                           title="Task is in priority" {{in_array($task->id, $priority) ? 'checked' : ''}}>
                @endif
                <input type="checkbox" title="Select task" class="select_task_checkbox" name="task"
                       data-id="{{ $task->id }}" value="">
                {{ $task->id }}
            </td>
            <td class="p-2">{{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}
                <br>
                @if($task->customer_id)
                    Cus-{{$task->customer_id}}
                    <br>
                    @if(Auth::user()->isAdmin())
                        <span>
                            {{ isset($task->customer ) ? $task->customer->name : '' }}
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
            <td class="expand-row" data-subject="{{$task->task_subject ? $task->task_subject : 'Task Details'}}"
                data-details="{{$task->task_details}}" data-switch="0">
                <span class="td-mini-container">
                    {{ $task->task_subject ? substr($task->task_subject, 0, 15) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
                </span>
                <span class="td-full-container hidden">
                    {{ $task->task_subject ? $task->task_subject : 'Task Details' }}
                </span>
            </td>
            <td class="table-hover-cell p-2">
                @php
                    $special_task = $task;
                    $users_list = \App\Helpers::getTaskUserList($task, $users);
                @endphp

                @if(auth()->user()->isAdmin())
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
                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history"
                        title="Show History" data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
                <div class="col-md-12 expand-col-lead{{$task->id}} dis-none" style="padding:0px;">
                    <br>
                    <label for="" style="font-size: 12px;margin-top:10px;">Lead :</label>
                    @php
                        $selectBoxId = 'master_user_id';
                        $selectClass = "assign-master-user";
                        $type="master-user";
                    @endphp
                    @include('task-module.partials.select-user',compact('task', 'users', 'selectBoxId', 'selectClass', 'type'))
                    <br />
                    @if(auth()->user()->isAdmin())
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


                    <label for="" style="font-size: 12px;margin-top:10px;">Due date :</label>
                    <div class="d-flex">
                        <div class="form-group" style="padding-top:5px;">
                            <div class='input-group date due-datetime'>
                                <input type="text" class="form-control input-sm due_date_cls" name="due_date"
                                       value="{{$task->due_date}}" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-image set-due-date" title="Set due date"
                                data-taskid="{{ $task->id }}"><img style="padding: 0;margin-top: -14px;"
                                                                   src="{{asset('images/filled-sent.png')}}" /></button>
                    </div>

                    @if($task->is_milestone)
                        <p style="margin-bottom:0px;">Total : {{$task->no_of_milestone}}</p>
                        @if($task->no_of_milestone == $task->milestone_completed)
                            <p style="margin-bottom:0px;">Done : {{$task->milestone_completed}}</p>
                        @else
                            <input type="number" name="milestone_completed" id="milestone_completed_{{$task->id}}"
                                   placeholder="Completed..." class="form-control save-milestone"
                                   value="{{$task->milestone_completed}}" data-id="{{$task->id}}">
                        @endif
                    @else
                        <p>No milestone</p>
                    @endif
                </div>
            </td>
            <td>
                <select id="master_user_id" class="form-control change-task-status select2" data-id="{{$task->id}}"
                        name="master_user_id" id="user_{{$task->id}}">
                    <option value="">Select...</option>
                        <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                    @if(!empty($task_statuses))
                        @foreach($task_statuses as $index => $status)
                            @if( $status->id == $task->status )
                                <option value="{{$status->id}}" selected>{{ $status->name }}</option>
                            @else
                                <option value="{{$status->id}}">{{ $status->name }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </td>
            <td>
                <div class="d-flex">
                    <button type="button" class="btn btn-xs show-time-history" title="Show Estimation History"
                            data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
                </div>
                @if (isset($special_task->timeSpent) && $special_task->timeSpent->task_id > 0)
                    {{ formatDuration($special_task->timeSpent->tracked) }}

                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                            title="Show tracked time History" data-id="{{$task->id}}" data-type="developer"><i
                                class="fa fa-info-circle"></i></button>
                @endif
            </td>
            <td class="table-hover-cell p-2 {{ ($task->message && $task->message_status == 0) || $task->message_is_reminder == 1 || ($task->message_user_id == $task->assign_from && $task->assign_from != Auth::id()) ? 'text-danger' : '' }}">
                @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))

                    <div style="margin-bottom:10px;width: 100%;">
                            <?php $text_box = '100'; ?>
                        <input type="text" style="width: 100%;" class="form-control quick-message-field input-sm"
                               id="getMsg{{$task->id}}" name="message" placeholder="Message" value="">
                        <div class="d-flex">
                            <div style="">
                                <button id="send-message_{{ $task->id }}" class="btn btn-sm btn-image send-message"
                                        title="Send message" data-taskid="{{ $task->id }}"><img
                                            src="{{asset('images/filled-sent.png')}}" /></button>

                                <input type="hidden" name="is_audio" id="is_audio_{{$task->id}}" value="0">
                                <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image btn-trigger-rvn-modal"
                                        data-id="{{$task->id}}" data-tid="{{$task->id}}" data-load-type="text"
                                        data-all="1" title="Record & Send Voice Message"><img
                                            src="{{asset('images/record-voice-message.png')}}" alt=""></button>
                            </div>
                            @if (isset($task->message))
                                <div style="max-width: 30px;">
                                    <button type="button" class="btn btn-xs btn-image load-communication-modal"
                                            data-object='task' data-id="{{ $task->id }}" title="Load messages"><img
                                                src="{{asset('images/chat.png')}}" alt=""></button>
                                </div>
                            @endif
                            <button class="btn btn-image upload-task-files-button ml-2" type="button"
                                    title="Uploaded Files" data-task_id="{{$task->id}}">
                                <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-image view-task-files-button ml-2" type="button"
                                    title="View Uploaded Files" data-task_id="{{$task->id}}">
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
                                {{substr($task->message, $start,100)}}
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
                @else
                    Private
                @endif
            </td>
            @php
                $single = \App\Helpers\DevelopmentHelper::getTaskHistoryByTaskID($task->id);
            @endphp
            <td class="p-2">
                <div style="margin-bottom:10px;width: 100%;">
                    <div class="d-flex">
                        <input type="number" class="form-control" name="approximates{{$task->id}}"
                               value="{{$task->approximate}}" min="1" autocomplete="off">
                        <div style="max-width: 30px;">
                            <button class="btn btn-sm btn-image send-approximate-lead" title="Send approximate"
                                    onclick="funTaskInformationUpdatesTime('approximate',{{$task->id}})"
                                    data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                            </button>
                        </div>
                    </div>
                </div>

                    <?php
                        $time_history = \App\Helpers\DevelopmentHelper::getFirstDeveloperTaskHistoryByDevTaskIDAndModel($task->id);
                            ?>

                @if(!empty($time_history))
                    @if (isset($time_history->is_approved) && $time_history->is_approved != 1)
                        <button data-task="{{$time_history->developer_task_id}}" data-id="{{$time_history->id}}"
                                title="approve" data-type="TASK"
                                class="btn btn-sm approveEstimateFromshortcutButtonTaskPage">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                    @endif

                    @if($task->task_start!=1)
                        <button data-task="{{$task->id}}" title="Start Task" data-type="TASK"
                                class="btn btn-sm startDirectTask" data-task-type="1">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </button>
                    @else
                        <input type="hidden" value="{{$task->m_start_date}}" class="m_start_date_"
                               id="m_start_date_{{$task->id}}" data-id="{{$task->id}}" data-value="{{$task->id}}">
                        <button data-task="{{$task->id}}" title="Start Task" data-type="TASK"
                                class="btn btn-sm startDirectTask" data-task-type="2">
                            <i class="fa fa-stop" aria-hidden="true"></i>
                        </button>
                        <div id="time-counter_{{$task->id}}"></div>
                    @endif

                    <button type="button" class="btn btn-xs show-timer-history" title="Show timer History"
                            data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
                @endif
            </td>
            <td class="p-2">
                <div class="form-group d-flex">
                    <div class='input-group date cls-start-due-date'>
                        <input type="text" class="form-control" name="start_dates{{$task->id}}"
                               value="{{$single->taskhistoryForStartDate->first() ? $single->taskhistoryForStartDate->first()->new_value : ''}}"
                               autocomplete="off" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                    <div style="max-width: 30px;">
                        <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                                onclick="funTaskInformationUpdatesTime('start_date',{{$task->id}})"
                                data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button>
                    </div>
                </div>
                @if(!empty($single->taskhistoryForStartDate->first()->new_value) && $single->taskhistoryForStartDate->first()->new_value!='0000-00-00 00:00:00')
                    {{$single->taskhistoryForStartDate->first()->new_value}}
                @endif

                <div class="form-group d-flex">
                    <div class='input-group date cls-start-due-date'>
                        <input type="text" class="form-control" name="due_dates{{$task->id}}"
                               value="{{$single->taskDueDateHistoryLogs->first() ? $single->taskDueDateHistoryLogs->first()->new_due_date : ''}}"
                               autocomplete="off" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                    <div style="max-width: 30px;">
                        <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                                onclick="funTaskInformationUpdatesTime('due_date',{{$task->id}})"
                                data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button>
                    </div>
                </div>

                @if(!empty($single->taskDueDateHistoryLogs->first()->new_due_date) && $single->taskDueDateHistoryLogs->first()->new_due_date!='0000-00-00 00:00:00')
                    {{$single->taskDueDateHistoryLogs->first()->new_due_date}}
                @endif
            </td>

            <td id="shortcutsIds">
                @include('task-module.partials.shortcuts')
            </td>
            <td class="p-2">
                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$task->id}}')"><i
                            class="fa fa-arrow-down"></i></button>
            </td>
        </tr>
        <tr class="action-btn-tr-{{$task->id}} d-none">
            <td class="font-weight-bold">Action</td>
            <td colspan="11">
                <div class="row cls_action_box" style="margin:0px;">
                    @if(auth()->user()->isAdmin())
                        <button type="button" class='btn btn-image whatsapp-group pd-5' data-id="{{ $task->id }}"
                                data-toggle='modal' data-target='#whatsAppMessageModal'><img
                                    src="{{asset('images/whatsapp.png')}}" /></button>
                    @endif

                    <button data-toggle="modal" data-target="#taskReminderModal" class='btn pd-5 task-set-reminder'
                            data-id="{{ $task->id }}"
                            data-frequency="{{ !empty($task->reminder_message) ? $task->frequency : '60' }}"
                            data-reminder_message="{{ !empty($task->reminder_message) ? $task->reminder_message : 'Plz update' }}"
                            data-reminder_from="{{ $task->reminder_from }}"
                            data-reminder_last_reply="{{ ($task && !empty($task->reminder_last_reply)) ? $task->reminder_last_reply : '' }}">
                        <i class="fa fa-bell @if(!empty($task->reminder_message) && $task->frequency > 0) {{ 'green-notification'  }} @else {{ 'red-notification' }} @endif"
                           aria-hidden="true"></i>
                    </button>

                    @if ($special_task->users->contains(Auth::id()) || $task->assign_from == Auth::id() || $task->master_user_id == Auth::id())
                        <button type="button" title="Complete the task by user" class="btn btn-image task-complete pd-5"
                                data-id="{{ $task->id }}"><img src="/images/incomplete.png" /></button>
                        @if ($task->assign_from == Auth::id())
                            <button type="button" title="Verify the task by admin"
                                    class="btn btn-image task-complete pd-5" data-id="{{ $task->id }}"><img
                                        src="/images/completed-green.png" /></button>
                        @else
                            <button type="button" class="btn btn-image pd-5"><img
                                        src="{{asset('/images/completed-green.png')}}" /></button>
                        @endif

                        @include('task-module.partials.show-status-history-btn')

                        <button type="button" class='btn btn-image ml-1 reminder-message pd-5'
                                data-id="{{ $task->message_id }}" data-toggle='modal'
                                data-target='#reminderMessageModal'><img src='/images/reminder.png' /></button>

                        <button type="button" data-id="{{ $task->id }}" class="btn btn-file-upload pd-5">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </button>

                        <button type="button" class="btn preview-img-btn pd-5" data-id="{{ $task->id }}">
                            <i class="fa fa-list" aria-hidden="true"></i>
                        </button>
                    @endif
                    @if ((!$special_task->users->contains(Auth::id()) && $special_task->contacts()->count() == 0))
                        @if ($task->is_private == 1)
                            <button disabled type="button" class="btn btn-image pd-5">
                                <img src="{{asset('images/private.png')}}" /></button>
                        @endif
                    @endif

                    @if ($special_task->users->contains(Auth::id()) || ($task->assign_from == Auth::id() && $task->is_private == 0) || ($task->assign_from == Auth::id() && $special_task->contacts()->count() > 0) || Auth::id() == 6)
                        <a href="{{ route('task.show', $task->id) }}" class="btn btn-image pd-5" href=""><img
                                    src="{{asset('images/view.png')}}" /></a>
                    @endif

                    @if ($task->is_flagged == 1)
                        <button type="button" class="btn btn-image flag-task pd-5" data-id="{{ $task->id }}"><img
                                    src="{{asset('images/flagged.png')}}" /></button>
                    @else
                        <button type="button" class="btn btn-image flag-task pd-5" data-id="{{ $task->id }}"><img
                                    src="{{asset('images/unflagged.png')}}" /></button>
                    @endif
                    <button class="btn btn-image expand-row-btn-lead" data-task_id="{{ $task->id }}"><img
                                src="{{asset('/images/forward.png')}}"></button>

                    <button class="btn btn-image mt-2 create-task-document" title="Create document"
                            data-id="{{$task->id}}">
                        <i class="fa fa-file-text" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-image mt-2 show-created-task-document" title="Show created document"
                            data-id="{{$task->id}}">
                        <i class="fa fa-list" aria-hidden="true"></i>
                    </button>

                    <a title="Task Information: Update" class="btn btn-image mt-2" href="javascript:void(0);"
                       onclick="funTaskInformationModal(this, '{{$task->id}}')">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                    </a>
                </div>
            </td>
        </tr>
    @endif
@else
    <tr style="background-color: {{isset($task->taskStatus) ? $task->taskStatus->task_color : ''}}!important;"
        class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} {{ !$task->due_date ? 'no-due-date' : '' }} {{ $task->is_statutory == 3 ? 'row-highlight' : '' }}"
        id="task_{{ $task->id }}">
        <td class="p-2">
            @if(auth()->user()->isAdmin())
                <input type="checkbox" name="selected_issue[]" value="{{$task->id}}"
                       title="Task is in priority" {{in_array($task->id, $priority) ? 'checked' : ''}}>
            @endif
            <input type="checkbox" title="Select task" class="select_task_checkbox" name="task"
                   data-id="{{ $task->id }}" value="">
            {{ $task->id }}
        </td>
        <td class="p-2">{{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}
            <br>
            @if($task->customer_id)
                Cus-{{$task->customer_id}}
                <br>
                @if(Auth::user()->isAdmin())
                    <span>
                        {{ isset($task->customer ) ? $task->customer->name : '' }}
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
        <td class="expand-row" data-subject="{{$task->task_subject ? $task->task_subject : 'Task Details'}}"
            data-details="{{$task->task_details}}" data-switch="0">
            <span class="td-mini-container">
                {{ $task->task_subject ? substr($task->task_subject, 0, 15) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
            </span>
            <span class="td-full-container hidden">
                {{ $task->task_subject ? $task->task_subject : 'Task Details' }}
            </span>
        </td>
        <td class="table-hover-cell p-2">
            @php
                $special_task = $task;
                $users_list = \App\Helpers::getTaskUserList($task, $users);
            @endphp

            @if(auth()->user()->isAdmin())
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
            <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history"
                    title="Show History" data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
            <div class="col-md-12 expand-col-lead{{$task->id}} dis-none" style="padding:0px;">
                <br>
                <label for="" style="font-size: 12px;margin-top:10px;">Lead :</label>
                @php
                    $selectBoxId = 'master_user_id';
                    $selectClass = "assign-master-user";
                    $type="master-user";
                @endphp
                @include('task-module.partials.select-user',compact('task', 'users', 'selectBoxId', 'selectClass', 'type'))
                <br />
                @if(auth()->user()->isAdmin())
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


                <label for="" style="font-size: 12px;margin-top:10px;">Due date :</label>
                <div class="d-flex">
                    <div class="form-group" style="padding-top:5px;">
                        <div class='input-group date due-datetime'>
                            <input type="text" class="form-control input-sm due_date_cls" name="due_date"
                                   value="{{$task->due_date}}" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-image set-due-date" title="Set due date"
                            data-taskid="{{ $task->id }}"><img style="padding: 0;margin-top: -14px;"
                                                               src="{{asset('images/filled-sent.png')}}" /></button>
                </div>

                @if($task->is_milestone)
                    <p style="margin-bottom:0px;">Total : {{$task->no_of_milestone}}</p>
                    @if($task->no_of_milestone == $task->milestone_completed)
                        <p style="margin-bottom:0px;">Done : {{$task->milestone_completed}}</p>
                    @else
                        <input type="number" name="milestone_completed" id="milestone_completed_{{$task->id}}"
                               placeholder="Completed..." class="form-control save-milestone"
                               value="{{$task->milestone_completed}}" data-id="{{$task->id}}">
                    @endif
                @else
                    <p>No milestone</p>
                @endif
            </div>
        </td>
        <td>
            <select id="master_user_id" class="form-control change-task-status select2" data-id="{{$task->id}}"
                    name="master_user_id" id="user_{{$task->id}}">
                <option value="">Select...</option>
                    <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                @if(!empty($task_statuses))
                    @foreach($task_statuses as $index => $status)
                        @if( $status->id == $task->status )
                            <option value="{{$status->id}}" selected>{{ $status->name }}</option>
                        @else
                            <option value="{{$status->id}}">{{ $status->name }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            <div class="d-flex">
                <button type="button" class="btn btn-xs show-time-history" title="Show Estimation History"
                        data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
            </div>
            @if (isset($special_task->timeSpent) && $special_task->timeSpent->task_id > 0)
                {{ formatDuration($special_task->timeSpent->tracked) }}

                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                        title="Show tracked time History" data-id="{{$task->id}}" data-type="developer"><i
                            class="fa fa-info-circle"></i></button>
            @endif
        </td>
        <td class="table-hover-cell p-2 {{ ($task->message && $task->message_status == 0) || $task->message_is_reminder == 1 || ($task->message_user_id == $task->assign_from && $task->assign_from != Auth::id()) ? 'text-danger' : '' }}">
            @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))

                <div style="margin-bottom:10px;width: 100%;">
                        <?php $text_box = '100'; ?>
                    <input type="text" style="width: 100%;" class="form-control quick-message-field input-sm"
                           id="getMsg{{$task->id}}" name="message" placeholder="Message" value="">
                    <div class="d-flex">
                        <div style="">
                            <button id="send-message_{{ $task->id }}" class="btn btn-sm btn-image send-message"
                                    title="Send message" data-taskid="{{ $task->id }}"><img
                                        src="{{asset('images/filled-sent.png')}}" /></button>

                            <input type="hidden" name="is_audio" id="is_audio_{{$task->id}}" value="0">
                            <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image btn-trigger-rvn-modal"
                                    data-id="{{$task->id}}" data-tid="{{$task->id}}" data-load-type="text" data-all="1"
                                    title="Record & Send Voice Message"><img
                                        src="{{asset('images/record-voice-message.png')}}" alt=""></button>
                        </div>
                        @if (isset($task->message))
                            <div style="max-width: 30px;">
                                <button type="button" class="btn btn-xs btn-image load-communication-modal"
                                        data-object='task' data-id="{{ $task->id }}" title="Load messages"><img
                                            src="{{asset('images/chat.png')}}" alt=""></button>
                            </div>
                        @endif
                        <button class="btn btn-image upload-task-files-button ml-2" type="button" title="Uploaded Files"
                                data-task_id="{{$task->id}}">
                            <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-image view-task-files-button ml-2" type="button"
                                title="View Uploaded Files" data-task_id="{{$task->id}}">
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
                            {{substr($task->message, $start,100)}}
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
            @else
                Private
            @endif
        </td>
        @php
            $single = \App\Helpers\DevelopmentHelper::getTaskHistoryByTaskID($task->id);
        @endphp
        <td class="p-2">
            <div style="margin-bottom:10px;width: 100%;">
                <div class="d-flex">
                    <input type="number" class="form-control" name="approximates{{$task->id}}"
                           value="{{$task->approximate}}" min="1" autocomplete="off">
                    <div style="max-width: 30px;">
                        <button class="btn btn-sm btn-image send-approximate-lead" title="Send approximate"
                                onclick="funTaskInformationUpdatesTime('approximate',{{$task->id}})"
                                data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button>
                    </div>
                </div>
            </div>

            <?php
                $time_history = \App\Helpers\DevelopmentHelper::getFirstDeveloperTaskHistoryByDevTaskIDAndModel($task->id);
                            ?>

            @if(!empty($time_history))
                @if (isset($time_history->is_approved) && $time_history->is_approved != 1)
                    <button data-task="{{$time_history->developer_task_id}}" data-id="{{$time_history->id}}"
                            title="approve" data-type="TASK"
                            class="btn btn-sm approveEstimateFromshortcutButtonTaskPage">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </button>
                @endif

                @if($task->task_start!=1)
                    <button data-task="{{$task->id}}" title="Start Task" data-type="TASK"
                            class="btn btn-sm startDirectTask" data-task-type="1">
                        <i class="fa fa-play" aria-hidden="true"></i>
                    </button>
                @else
                    <input type="hidden" value="{{$task->m_start_date}}" class="m_start_date_"
                           id="m_start_date_{{$task->id}}" data-id="{{$task->id}}" data-value="{{$task->id}}">
                    <button data-task="{{$task->id}}" title="Start Task" data-type="TASK"
                            class="btn btn-sm startDirectTask" data-task-type="2">
                        <i class="fa fa-stop" aria-hidden="true"></i>
                    </button>
                    <div id="time-counter_{{$task->id}}"></div>
                @endif

                <button type="button" class="btn btn-xs show-timer-history" title="Show timer History"
                        data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
            @endif
        </td>
        <td class="p-2">
            <div class="form-group d-flex">
                <div class='input-group date cls-start-due-date'>
                    <input type="text" class="form-control" name="start_dates{{$task->id}}"
                           value="{{$single->taskhistoryForStartDate->first() ? $single->taskhistoryForStartDate->first()->new_value : ''}}"
                           autocomplete="off" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
                <div style="max-width: 30px;">
                    <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                            onclick="funTaskInformationUpdatesTime('start_date',{{$task->id}})"
                            data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button>
                </div>
            </div>
            @if(!empty($single->taskhistoryForStartDate->first()->new_value) && $single->taskhistoryForStartDate->first()->new_value!='0000-00-00 00:00:00')
                {{$single->taskhistoryForStartDate->first()->new_value}}
            @endif

            <div class="form-group d-flex">
                <div class='input-group date cls-start-due-date'>
                    <input type="text" class="form-control" name="due_dates{{$task->id}}"
                           value="{{$single->taskDueDateHistoryLogs->first() ? $single->taskDueDateHistoryLogs->first()->new_due_date : ''}}"
                           autocomplete="off" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
                <div style="max-width: 30px;">
                    <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                            onclick="funTaskInformationUpdatesTime('due_date',{{$task->id}})"
                            data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button>
                </div>
            </div>

            @if(!empty($single->taskDueDateHistoryLogs->first()->new_due_date) && $single->taskDueDateHistoryLogs->first()->new_due_date!='0000-00-00 00:00:00')
                {{$single->taskDueDateHistoryLogs->first()->new_due_date}}
            @endif
        </td>

        <td class="p-2">
            <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$task->id}}')"><i
                        class="fa fa-arrow-down"></i></button>
        </td>
    </tr>
    <tr class="action-btn-tr-{{$task->id}} d-none">
        <td class="font-weight-bold">Action</td>
        <td colspan="11">
            <div class="row cls_action_box" style="margin:0px;">
                @if(auth()->user()->isAdmin())
                    <button type="button" class='btn btn-image whatsapp-group pd-5' data-id="{{ $task->id }}"
                            data-toggle='modal' data-target='#whatsAppMessageModal'><img
                                src="{{asset('images/whatsapp.png')}}" /></button>
                @endif

                <button data-toggle="modal" data-target="#taskReminderModal" class='btn pd-5 task-set-reminder'
                        data-id="{{ $task->id }}"
                        data-frequency="{{ !empty($task->reminder_message) ? $task->frequency : '60' }}"
                        data-reminder_message="{{ !empty($task->reminder_message) ? $task->reminder_message : 'Plz update' }}"
                        data-reminder_from="{{ $task->reminder_from }}"
                        data-reminder_last_reply="{{ ($task && !empty($task->reminder_last_reply)) ? $task->reminder_last_reply : '' }}">
                    <i class="fa fa-bell @if(!empty($task->reminder_message) && $task->frequency > 0) {{ 'green-notification'  }} @else {{ 'red-notification' }} @endif"
                       aria-hidden="true"></i>
                </button>

                @if ($special_task->users->contains(Auth::id()) || $task->assign_from == Auth::id() || $task->master_user_id == Auth::id())
                    <button type="button" title="Complete the task by user" class="btn btn-image task-complete pd-5"
                            data-id="{{ $task->id }}"><img src="/images/incomplete.png" /></button>
                    @if ($task->assign_from == Auth::id())
                        <button type="button" title="Verify the task by admin" class="btn btn-image task-complete pd-5"
                                data-id="{{ $task->id }}"><img src="/images/completed-green.png" /></button>
                    @else
                        <button type="button" class="btn btn-image pd-5"><img
                                    src="{{asset('/images/completed-green.png')}}" /></button>
                    @endif

                    @include('task-module.partials.show-status-history-btn')

                    <button type="button" class='btn btn-image ml-1 reminder-message pd-5'
                            data-id="{{ $task->message_id }}" data-toggle='modal' data-target='#reminderMessageModal'>
                        <img src='/images/reminder.png' /></button>

                    <button type="button" data-id="{{ $task->id }}" class="btn btn-file-upload pd-5">
                        <i class="fa fa-upload" aria-hidden="true"></i>
                    </button>

                    <button type="button" class="btn preview-img-btn pd-5" data-id="{{ $task->id }}">
                        <i class="fa fa-list" aria-hidden="true"></i>
                    </button>
                @endif
                @if ((!$special_task->users->contains(Auth::id()) && $special_task->contacts()->count() == 0))
                    @if ($task->is_private == 1)
                        <button disabled type="button" class="btn btn-image pd-5"><img
                                    src="{{asset('images/private.png')}}" /></button>
                    @endif
                @endif

                @if ($special_task->users->contains(Auth::id()) || ($task->assign_from == Auth::id() && $task->is_private == 0) || ($task->assign_from == Auth::id() && $special_task->contacts()->count() > 0) || Auth::id() == 6)
                    <a href="{{ route('task.show', $task->id) }}" class="btn btn-image pd-5" href=""><img
                                src="{{asset('images/view.png')}}" /></a>
                @endif

                @if ($task->is_flagged == 1)
                    <button type="button" class="btn btn-image flag-task pd-5" data-id="{{ $task->id }}"><img
                                src="{{asset('images/flagged.png')}}" /></button>
                @else
                    <button type="button" class="btn btn-image flag-task pd-5" data-id="{{ $task->id }}"><img
                                src="{{asset('images/unflagged.png')}}" /></button>
                @endif
                <button class="btn btn-image expand-row-btn-lead" data-task_id="{{ $task->id }}"><img
                            src="{{asset('/images/forward.png')}}"></button>

                <button class="btn btn-image mt-2 create-task-document" title="Create document" data-id="{{$task->id}}">
                    <i class="fa fa-file-text" aria-hidden="true"></i>
                </button>
                <button class="btn btn-image mt-2 show-created-task-document" title="Show created document"
                        data-id="{{$task->id}}">
                    <i class="fa fa-list" aria-hidden="true"></i>
                </button>

                <a title="Task Information: Update" class="btn btn-image mt-2" href="javascript:void(0);"
                   onclick="funTaskInformationModal(this, '{{$task->id}}')">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                </a>
            </div>
        </td>
    </tr>
@endif
