@if (isset($task->message) && $row_type == 'statutory')
    <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='task' data-id="{{ $task->id }}" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
@elseif ($row_type != 'statutory')
<button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='task' data-id="{{ $task->id }}" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
@endif

<button class="btn btn-image upload-task-files-button ml-2" 
    type="button" title="Uploaded Files" data-task_id="{{$task->id}}">
    <i class="fa fa-cloud-upload" aria-hidden="true"></i>
</button>
<button class="btn btn-image view-task-files-button ml-2" type="button" 
title="View Uploaded Files" data-task_id="{{$task->id}}">
    <img src="/images/google-drive.png" style="cursor: nwse-resize; width: 10px;">
</button>