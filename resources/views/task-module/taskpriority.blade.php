@foreach ($issues as $task)
<tr>
    <td><input type="hidden" name="priority[]" value="{{$task['id']}}"/>{{$task['id']}}</td>;
    <td>{{$task['task_subject']}}</td>
    <td class="Website-task"title="{{$task['task_details']}}">{{$task['task_details']}}</td>
    <td class="table-hover-cell p-2 {{ ($task->message && $task->message_status == 0) || $task->message_is_reminder == 1 || ($task->message_user_id == $task->assign_from && $task->assign_from != Auth::id()) ? 'text-danger' : '' }}">
        @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
            <div class="d-flex">
                <?php
                $text_box = "100";
                // if(isset($task->message))
                // {
                //     $text_box = "50";
                // }
                // else
                // {
                //     $text_box = "100";
                // }
                ?>
                
                    <input type="text" style="width: <?php echo $text_box;?>%;" class="form-control quick-message-field input-sm " id="getMsgPopup{{$task->id}}" name="message" placeholder="Message" value="">
                
                <div width="10%">
                    <button class="btn btn-sm btn-image send-message onpriority" title="Send message" data-taskid="{{ $task->id }}"style="margin-top:-1px;margin-left: 5px;"><img src="{{asset('images/filled-sent.png')}}"/></button>
                    @if (isset($task->message))
                        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='task' data-id="{{ $task->id }}" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                    @endif
                </div>
                
                <div width="50%">
                    @if (isset($task->message))
                        <div class="d-flex justify-content-between expand-row-msg" data-id="{{$task->id}}">
                            <span class="td-mini-container-{{$task->id}}" style="margin:0px;">
                            
                                <?php 
                                if(!empty($task->message) && !empty($task->task_subject)) {
                                    $pos = strpos($task->message,$task->task_subject);
                                    $length = strlen($task->task_subject);
                                    if($pos) {
                                        $start = $pos + $length + 1;
                                    }
                                    else {
                                        $start = 0;
                                    }
                                }else{
                                    $start = 0;
                                }
                                ?>
                                {{substr($task->message, $start,28)}}
                            </span>
                        </div>
                    @endif 
                </div>
            </div>
            <div class="expand-row-msg" data-id="{{$task->id}}">
                <span class="td-full-container-{{$task->id}} hidden">
                    {{ $task->message }}
                </span>
            </div>
            <div class="expand-col dis-none">
            <br>
            @if(auth()->user()->isAdmin())
            <label for="">Lead:</label>
                <div class="d-flex">
                    <input type="text" style="width: <?php echo $text_box;?>%;" class="form-control quick-message-field input-sm" id="getMsgPopup{{$task->id}}" name="message" placeholder="Message" value="">
                    <button class="btn btn-sm btn-image send-message-lead onpriority" title="Send message" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}"/></button>
                  
                </div>
                @endif
            </div>
        @else
            Private
        @endif
    </td>    <td>{{$task['created_at']}}</td>
    <td>{{$task['created_by']}}</td>
    <td><a href="javascript:;" class="delete_priority" data-id="{{$task['id']}}">Remove<a></td>
</tr>   
@endforeach