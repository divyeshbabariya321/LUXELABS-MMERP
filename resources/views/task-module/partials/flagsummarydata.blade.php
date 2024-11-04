<?php /*@foreach ($tasks as $key => $task)
@endforeach*/ ?>
@foreach($tasks as $key => $task)
    <?php
    // $special_task = \App\Task::find($task->id);
//    $task = $issue;
    $special_task = $task;
    ?>
    <tr style="color:grey;">
    <td><input type="checkbox" name="taskIds[]" class="rowCheckbox" value="{{ $task->id }}"></td>
    <td>
        {{ $task->id }}
    </td>
    <td>
        {{ $task->created_at }}
    </td>
    <td style="word-break: break-all">
        {{ $task->website }}
    </td>

    <td>
        @if (isset($special_task->timeSpent) && $special_task->timeSpent->task_id > 0)
        {{ formatDuration($special_task->timeSpent->tracked) }}

        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history_task" title="Show tracked time History" data-id="{{ $task->id }}" data-type="developer"><i class="fa fa-info-circle"></i></button>
        @endif
            <button style="float:right;padding-right:0px;" onclick="funTaskInformationModal(this, '{{$task->id}}')" type="button" class="btn btn-xs" title="Show tracked time History" data-id="{{$task->id}}" data-type="tester"><i class="fa fa-refresh"></i></button>
    </td>
    <td style="word-break: break-all">{{$task->task_subject}}</td>
    <td>
        <div class="d-flex">
            <select class="form-control assign-task-user select2" data-id="{{ $task->id }}" name="assign_to" id="user_{{ $task->id }}">
                <option value="">Select...</option>
                <?php $assignedId = isset($task->assign_to) ? $task->assign_to : 0; ?>
                @foreach ($users as $id => $name)
                    @if ($assignedId == $id)
                        <option value="{{ $id }}" selected>{{ $name }}</option>
                    @else
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endif
                @endforeach
            </select>
            <button style="float:right;padding-right:0px; background: none;" type="button" class="btn btn-xs show-user-history" title="Show History" data-id="{{ $task->id }}" data-type="task"><i class="fa fa-info-circle"></i></button>
        </div>
    </td>
    <td>
        @php
        if($task->due_date != '0') { echo $task->due_date; }
        @endphp
        <button type="button" class="btn btn-xs show-date-history" title="Show tracked time History" data-id="{{ $task->id }}" data-type="task" style="float:right;margin-left: auto;"><i class="fa fa-info-circle"></i></button>
    </td>
    <td>
        @php
            if($task->approximate != '0') { echo $task->approximate; }
        @endphp
        <button type="button" style="float:right;" class="btn btn-xs show-time-history-task" title="Show History" data-id="{{ $task->id }}" data-user_id="{{ $task->assign_to }}" style="background: none;"><i class="fa fa-info-circle"></i></button>
    </td>
    <td>
        {{$task->due_date}}
    </td>

    <td>
        <div class="d-flex">
            <input type="text" class="form-control send-message-textbox" data-id="{{ $task->id }}" id="send_message_{{ $task->id }}" name="send_message_{{ $task->id }}" style="margin-bottom:5px;width:calc(100% - 24px);display:inline;" />
            <button type="submit" class="btn btn-xs btn-image send-message-open" id="submit_message" data-id="{{ $task->id }}" ><img src="{{asset('/images/filled-sent.png')}}" /></button>
            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='task' data-id="{{ $task->id }}" title="Load messages"><img src="/images/chat.png" alt=""></button>
        </div>
        <div class="d-flex">
            <span class="Website-task pr-0 {{ ($task->message && $task->message_status == 0) ||$task->message_is_reminder == 1 ||($task->sent_to_user_id == Auth::id() && $task->message_status == 0)? '': '' }} justify-content-between expand-row-msg" style="word-break: break-all;margin-top:6px; width:100%; margin-right:-13px;" data-id="{{ $task->id }}">
                <span class="td-mini-container-{{ $task->id }} Website-task" style="margin:0px;">
                    {{ \Illuminate\Support\Str::limit($task->message, 25, $end = '...') }}
                </span>
            </span>
        </div>
        <div class="expand-row-msg" data-id="{{ $task->id }}">
            <span class="td-full-container-{{ $task->id }} hidden">
                {{ $task->message }}
                <br>
                <div class="td-full-container">
                    <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $task->id }})">Send
                        Attachment</button>
                    <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{ $task->id }})">Send
                        Images</button>
                    <input id="file-input{{ $task->id }}" type="file" name="files" style="display: none;" multiple />
                </div>
            </span>
        </div>
    </td>

    <td>
            <select id="master_user_id" class="form-control change-task-status select2" data-id="{{ $task->id }}" name="master_user_id" id="user_{{ $task->id }}">
                @if (!empty($task_statuses))
                @foreach ($task_statuses as $index => $status)
                @if ($status->id == $task->status)
                <option value="{{ $status->id }}" selected>{{ $status->name }}</option>
                @else
                <option value="{{ $status->id }}">{{ $status->name }}</option>
                @endif
                @endforeach
                @endif
            </select>
    </td>



    
        <td>
            <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Taskbtn('{{$task->id}}')"><i class="fa fa-arrow-down"></i></button>
        </td>

</tr>
    <tr class="action-taskbtn-tr-{{$task->id}} d-none">
        <td class="pl-1">Action</td>
        <td colspan="11">
            <button type="button" title="history" class="btn btn-xs pull-left" data-id="{{$task->id}}">
                <i class="fa fa-comments-o" aria-hidden="true"></i>
            </button>
            <button type="button" title="LogTasktime history" class="btn btn-xs pull-left" data-id="{{$task->id}}">
                <i class="fa fa-history" aria-hidden="true"></i>
            </button>
            @if ($task->is_flagged == 1)
                <button type="button" class="btn btn-xs pull-left btn-image flag-task mt-0" data-type="task" data-id="{{ $task->id }}"><img src="{{ asset('images/flagged.png') }}" style="filter: grayscale(1);" /></button>
            @else
                <button type="button" class="btn btn-xs pull-left btn-image flag-task mt-0" data-type="task" data-id="{{ $task->id }}"><img src="{{ asset('images/unflagged.png') }}"/></button>
            @endif
            <button type="button" class="btn btn-xs pull-left show-status-history mt-0" title="Show Status History" data-id="{{ $task->id }}" data-type="task">
                <i class="fa fa-info-circle"></i>
            </button>
        </td>
    </tr>
@endforeach