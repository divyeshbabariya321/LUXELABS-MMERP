<tr style="background-color:{{$issue->taskStatus->task_color ?? ''}};">
    <td style="display:table-cell;vertical-align: baseline;">
        {{ $issue->id }}
    </td>
    <td>
        <a data-toggle="modal" data-target="#task_subject{{ $issue->id }}" class="btn pd-5 task-set-reminder" style="overflow: hidden;display: inline-block;text-overflow: ellipsis;white-space: nowrap;width: 100%;">
            {{ $issue->subject }}
        </a>
        <div id="task_subject{{ $issue->id }}" class="modal fade" role="dialog">
            <div class="modal-dialog" style=" display: flex;justify-content: center;align-items: center; height: 100%;flex-wrap: wrap;">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-body">
                        {{ $issue->subject }}
                    </div>
                    <div class="modal-footer" style="padding-top: 8px; padding-bottom: 8px;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td>
        <div class="d-flex">
            <!--   <label for="" style="font-size: 12px;">Assigned To :</label>-->
            <select class="form-control assign-user select2" data-id="{{$issue->id}}" name="assigned_to" id="user_{{$issue->id}}">
                <option value="">Select...</option>
                <?php $assignedId = isset($issue->assignedUser->id) ? $issue->assignedUser->id : 0; ?>
                @foreach($users as $id => $name)
                @if( $assignedId == $id )
                <option value="{{$id}}" selected>{{ $name }}</option>
                @else
                <option value="{{$id}}">{{ $name }}</option>
                @endif
                @endforeach
            </select>
            <button style="float:right;padding-right:0px" type="button" class="btn btn-xs show-user-history" title="Show History" data-id="{{$issue->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button>
            <!--     <label for="" style="font-size: 12px;margin-top:10px;">Lead :</label>-->
        </div>
    </td>
    <td>
        @if (isset($issue->timeSpent) && $issue->timeSpent->task_id > 0)
        Developer : {{ formatDuration($issue->timeSpent->tracked) }}

        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history" title="Show tracked time History" data-id="{{$issue->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button>
        @endif

        @if (isset($issue->leadtimeSpent) && $issue->leadtimeSpent->task_id > 0)
        Lead : {{ formatDuration($issue->leadtimeSpent->tracked) }}

        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history" title="Show tracked time History" data-id="{{$issue->id}}" data-type="lead"><i class="fa fa-info-circle"></i></button>
        @endif

        @if (isset($issue->testertimeSpent) && $issue->testertimeSpent->task_id > 0)
        Tester : {{ formatDuration($issue->testertimeSpent->tracked) }}

        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history" title="Show tracked time History" data-id="{{$issue->id}}" data-type="tester"><i class="fa fa-info-circle"></i></button>
        @endif
        <button style="float:right;padding-right:0px;" onclick="funDevInformationModal(this, '{{$issue->id}}')" data-userId="{{$issue->user_id}}" type="button" class="btn btn-xs" title="Show time data" data-id="{{$issue->id}}" data-type="tester"><i class="fa fa-refresh"></i></button>
    </td>
    <td>
        {{ $issue->estimate_minutes }}
        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-time-history" title="Show History" data-id="{{$issue->id}}" data-userId="{{$issue->user_id}}"><i class="fa fa-info-circle"></i></button>
    </td>
    <td>
        <div class="d-flex">
            <div>
                {{ $issue->estimate_date }}
            </div>
            <button style="padding-right:0px;margin-left: auto;" type="button" class="btn btn-xs show-date-history" title="Show time History" data-id="{{$issue->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button>
        </div>
    </td>
    <td>
        {{ $issue->due_date }}
    </td>
    <td class="communication-td devtask-com" style="display: block;">
        <!-- class="expand-row" -->

        <div class="d-flex">
            <input type="text" class="form-control send-message-textbox" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" style="margin-bottom:5px;width:calc(100% - 24px);display:block;" />
            <button type="submit" class="btn btn-xs btn-image send-message-open" id="submit_message" data-id="{{$issue->id}}"><img src="/images/filled-sent.png" /></button>
            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" title="Load messages"><img src="/images/chat.png" alt=""></button>
        </div>
        <div class="d-flex">

            
            <div style="width: 100%; display:block">
                <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? '' : '' }} justify-content-between expand-row-msg" style="word-break: break-all;margin-top:6px;" data-id="{{$issue->id}}">
                    <span class="td-mini-container-{{$issue->id}}" style="margin:0px;">
                        {{ \Illuminate\Support\Str::limit($issue->message, 25, $end='...') }}
                    </span>
                </span>
            </div>


        </div>
        <div class="expand-row-msg" data-id="{{$issue->id}}">
            <span class="td-full-container-{{$issue->id}} hidden">
                {{ $issue->message }}
                <br>
                <div class="td-full-container">
                    <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }})">Send Attachment</button>
                    <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}})">Send Images</button>
                    <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple />
                </div>
            </span>
        </div>
    </td>
    <td>
        <div class="d-flex">
            @if($issue->is_resolved)
            <strong>Done</strong>
            @else
            {{ html()->select("task_status", $statusList, $issue->status)->class("form-control resolve-issue")->attribute('onchange', "resolveIssue(this," . $issue->id . ")") }}
            @endif
        </div>
    </td>
<td>
    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Quickbtn('{{$issue->id}}')"><i class="fa fa-arrow-down"></i></button>
</td>
</tr>

<tr class="action-quickbtn-tr-{{$issue->id}} d-none">
    <td class="font-weight-bold">Action</td>
    <td colspan="9">
        <button type="button" title="Tasktime history" class="btn tasktime-history-btn btn-xs pull-left" data-id="{{$issue->id}}">
            <i class="fa fa-comments-o"></i>
        </button>

        <button type="button" title="LogTasktime history" class="btn logtasktime-history-btn btn-xs pull-left" data-id="{{$issue->id}}">
            <i class="fa fa-history"></i>
        </button>

        @if ($issue->is_flagged == 1)
            <button type="button" title="Flag Task" class="btn btn-image flag-task btn-xs pull-left mt-0" data-type="DEVTASK" data-id="{{ $issue->id }}"><img src="{{asset('images/flagged.png')}}" /></button>
        @else
            <button type="button" title="Flag Task" class="btn btn-image flag-task btn-xs pull-left mt-0" data-type="DEVTASK" data-id="{{ $issue->id }}"><img src="{{asset('images/unflagged.png')}}" /></button>
        @endif

        <button type="button" data-type="develop" class="btn btn-xs show-status-history" title="Show Status History" data-id="{{$issue->id}}">
            <i class="fa fa-info-circle"></i>
        </button>

        <button class="btn upload-task-files-button p-0" type="button" title="Uploaded Files" data-task_type="DEVTASK" data-task_id="{{$issue->id}}">
            <i class="fa fa-cloud-upload" aria-hidden="true"></i>
        </button>
        <button class="btn btn-image view-task-files-button p-0" type="button" title="View Uploaded Files" data-task_type="DEVTASK" data-task_id="{{$issue->id}}">
            <img src="/images/google-drive.png" style="cursor: nwse-resize; width: 10px;">
        </button>
        <button class="btn create-task-document p-0" title="Create document" data-task_type="DEVTASK" data-id="{{$issue->id}}">
            <i class="fa fa-file-text" aria-hidden="true"></i>
        </button>
        <button class="btn show-created-task-document p-0" title="Show created document" data-task_type="DEVTASK" data-id="{{$issue->id}}">
            <i class="fa fa-list" aria-hidden="true"></i>
        </button>
    </td>
</tr>

{{-- @section('scripts') --}}
    <script>
        if(typeof Quickbtn === "undefined") {
            function Quickbtn(id){
                $(".action-quickbtn-tr-"+id).toggleClass('d-none')
            }
        }
    </script>
{{-- @endsection --}}