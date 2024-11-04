@php
    $row_class = "";
    if ($row_type === "completed") {
        $row_class = \App\Http\Controllers\TaskModuleController::getClasses($task) .  " completed"; 
    }
@endphp
<tr style="background-color: {{$task->taskStatus->task_color}}!important;" class="{{ $row_class }}" id="task_{{ $task->id }}">
    <td class="p-2">
        @if( $row_type === "statutory" && auth()->user()->isAdmin())
            <input type="checkbox" name="selected_issue[]" title="Task is in priority" value="{{$task->id}}" {{in_array($task->id, $priority) ? 'checked' : ''}}>
        @endif
        {{ $task->id }}
    </td>

    <td class="p-2">
        {{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}
        
        <br>
        @if ($row_type === "completed" && $task->customer_id)
            Cus-{{$task->customer_id}} <br>
            @if(Auth::user()->isAdmin())
                @php
                    $customer = getCustomerById($task->customer_id);
                @endphp
                <span>
                    {{ isset($customer ) ? $customer->name : '' }}
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

    <td class="expand-row table-hover-cell p-2" data-subject="{{$task->task_subject ? $task->task_subject : 'Task Details'}}" data-details="{{$task->task_details}}" data-switch="0" style="word-break: break-all;">
        <span class="td-mini-container">
            {{ $task->task_subject ? substr($task->task_subject, 0, 18) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
        </span>
        <span class="td-full-container hidden">
            <strong>{{ $task->task_subject ? $task->task_subject : 'Task Details' }}</strong>
            {{ $task->task_details }}
        </span>
    </td>


    <!-- <td class="expand-row table-hover-cell p-2">
        @if (array_key_exists($task->assign_from, $users))
            @if ($task->assign_from == Auth::id())
                <span class="td-mini-container">
                    <a href="{{ route('users.show', $task->assign_from) }}">{{ strlen($users[$task->assign_from]) > 4 ? substr($users[$task->assign_from], 0, 4) : $users[$task->assign_from] }}</a>
                </span>
                <span class="td-full-container hidden">
                    <a href="{{ route('users.show', $task->assign_from) }}">{{ $users[$task->assign_from] }}</a>
                </span>
            @else
                <span class="td-mini-container">
                    {{ strlen($users[$task->assign_from]) > 4 ? substr($users[$task->assign_from], 0, 4) : $users[$task->assign_from] }}
                </span>
                <span class="td-full-container hidden">
                    {{ $users[$task->assign_from] }}
                </span>
            @endif
        @else
            Doesn't Exist
        @endif
    </td> -->

    <td class="expand-row table-hover-cell p-2">
        @php
            $special_task = $task; 
            $users_list = \App\Helpers::getTaskUserList($task, $users);
        @endphp
        <span class="td-mini-container">
            {{ strlen($users_list) > 6 ? substr($users_list, 0, 6) : $users_list }}
        </span>
        <span class="td-full-container hidden">
            {{ $users_list }}
        </span>
    </td>


    @if ($row_type === "completed")
        <td>{{ Carbon\Carbon::parse($task->is_completed)->format('d-m H:i') }}</td>
    @elseif ($row_type === "statutory")
        <td class="p-2">
            {{ strlen($task->recurring_type) > 6 ? substr($task->recurring_type, 0, 6) : $task->recurring_type }}
        </td>
        <td>
            @if(auth()->user()->id == $task->assign_to || auth()->user()->isAdmin())
                <button type="button" style="width:10%;display:inline-block;padding:0px;" class="btn btn-xs show-time-history" title="Show Estimation History" data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
            @else
                <span class="apx-val">{{$task->approximate}}</span>
            @endif
        </td>
    @endif


    <td class="expand-row table-hover-cell p-2 {{ $task->message && $task->message_status == 0 ? 'text-danger' : '' }}">
        @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
            @php 
                $text_box = ""; 
                $readonly = ""; 
                if ($task->communication_status == 1) $readonly = "readonly";
            @endphp

            @if ($row_type === "statutory")
                <div class="d-flex">
                    <input type="text" id="getMsg{{$task->id}}" style="width: 100%;" class="form-control quick-message-field input-sm" name="message" placeholder="Message" value="" {{ $readonly }}>
                    <button class="btn btn-sm btn-image send-message" id="sendMsg{{$task->id}}" data-taskid="{{ $task->id }}"><img src="/images/filled-sent.png" {{ $readonly }} /></button>
                    @include('task-module.partials.task-action-button',compact('task', 'row_type'))
                </div>
                
                @if (isset($task->message))
                    <div style="margin-bottom:10px;width: 100%;">
                        <div class="d-flex justify-content-between">
                            @if (isset($task->is_audio) && $task->is_audio)
                                <audio controls="" src="{{ \App\Helpers::getAudioUrl($task->message) }}"></audio> 
                            @else
                            <span class="td-mini-container">
                                {{ strlen($task->message) > 25 ? substr($task->message, 0, 25) . '...' : $task->message }}
                            </span>
                            <span class="td-full-container hidden">
                                {{ $task->message }}
                            </span>
                            @endif
                        </div>
                    </div>
                @endif
            @elseif ($row_type === "completed")
                @if (isset($task->message))
                    <div class="d-flex">
                        @if (isset($task->is_audio) && $task->is_audio)
                            <p style="width:85%" class="td-full-container">
                                <audio controls="" src="{{ \App\Helpers::getAudioUrl($task->message) }}"></audio>
                            </p>
                        @else
                            <p style="width:85%" class="td-mini-container">
                                {{ strlen($task->message) > 32 ? substr($task->message, 0, 29) . '...' : $task->message }}
                            </p>
                            <p style="width:85%" class="td-full-container hidden">
                                {{ $task->message }}
                            </p>
                        @endif
                        @include('task-module.partials.task-action-button',compact('task', 'row_type'))
                    </div>
                @endif
            @endif
        @else
            Private
        @endif
    </td>

    <td id="shortcutsIds">
        @include('task-module.partials.shortcuts')
    </td>
    <td>
        <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$task->id}}')"><i class="fa fa-arrow-down"></i></button>
    </td>
</tr>
<tr class="action-btn-tr-{{$task->id}} d-none">
    <td class="font-weight-bold">Action</td>
    <td colspan="5">

        <div class="row" style="margin:0px;">
            @if ( $row_type === "statutory" && ($special_task->users->contains(Auth::id()) || $task->assign_from == Auth::id()))
                @if ($task->is_completed == '')
                    <button type="button" class="btn btn-image task-complete pd-5" data-id="{{ $task->id }}"><img src="/images/incomplete.png" /></button>
                @else 
                    @if ($task->assign_from == Auth::id())
                        <button type="button" class="btn btn-image task-complete pd-5" data-id="{{ $task->id }}"><img src="/images/completed-green.png" /></button>
                    @else
                        <button type="button" class="btn btn-image pd-5"><img src="/images/completed-green.png" /></button>
                    @endif
                @endif
            @endif

            @if ((!$special_task->users->contains(Auth::id()) && $special_task->contacts()->count() == 0))
                @if ($task->is_private == 1)
                    <button disabled type="button" class="btn btn-image pd-5"><img src="/images/private.png"/></button>
                @endif
            @endif


            @if ($special_task->users->contains(Auth::id()) 
                || ($task->assign_from == Auth::id() && $task->is_private == 0) 
                || ($task->assign_from == Auth::id() && $special_task->contacts()->count() > 0)
            )
                <a href="{{ route('task.show', $task->id) }}" class="btn btn-image pd-5" href=""><img src="/images/view.png"/></a>
            @endif

            @if ( $row_type === "statutory")
                @php 
                    $checked = "";
                    if ($task->communication_status == 0) $checked = "checked";
                @endphp

                <button type="button" onClick="return confirm('Are you sure you want to delete this task ?');" data-id="{{ $task->id }}" class="btn btn-image delete-task-btn pd-5"><img src="/images/delete.png" /></button>

                <div class="switch">
                    <input id="cmn-toggle- {{ $task->id }}" task-id="{{ $task->id }}" class="cmn-toggle cmn-toggle-round" type="checkbox" {{ $checked }}>
                    <label for="cmn-toggle-{{ $task->id }}"></label>
                </div>

                <button type="button" title="Recurring history" class="btn recurring-history-btn btn-xs pull-left" data-id="{{ $task->id }}">
                    <i class="fa fa-history"></i>
                </button>
            @elseif ( $row_type === "completed" )
                @if ($task->assign_from == Auth::id() && $task->is_verified)
                    <button type="button" title="Reopen the task" class="btn btn-image task-verify pd-5" data-id="{{ $task->id }}"><img src="/images/completed.png"/></button>     
                @endif

                <form action="{{ route('task.archive', $task->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-image pd-5"><img src="/images/archive.png"/></button>
                </form>
            @endif

            <button class="btn btn-image mt-2 create-task-document" title="Create document" data-id="{{$task->id}}">
                <i class="fa fa-file-text" aria-hidden="true"></i>
            </button>
            <button class="btn btn-image mt-2 show-created-task-document" title="Show created document" data-id="{{$task->id}}">
                <i class="fa fa-list" aria-hidden="true"></i>
            </button>
        </div>
    </td>
</tr>