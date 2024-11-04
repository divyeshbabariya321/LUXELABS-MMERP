@forelse ($logs as $log)
    <tr>
        <td>{{$loop->iteration}}</td>
        <td>
            {!! ($log->payload)?"<span class='lesstext'>".
                (\Illuminate\Support\Str::limit($log->url , 30, '...'))
                ."</span>":"-" !!}    
            <span class="full-text" style="display: none">
                {{$log->url}}
            </span>
            <a href="javascript:void(0)" onclick="showFullText('URL', this)" class="readmore btn btn-xs text-dark">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
        </td>
        <td>{{$log->response_code}}</td>
        <td>
            @if (isset($log->user))
                {{$log->user->name}}
            @else
                {{$log->user_id}}
            @endif
        </td>
        <td>
            @isset($log->task_id)
                #TASK-{{$log->task_id}}
            @endisset
            @isset($log->dev_task_id)
                #DEVTASK-{{$log->dev_task_id}}
            @endisset
        </td>
        <td>
            {!! ($log->payload)?"<span class='lesstext'>".
                (\Illuminate\Support\Str::limit($log->payload , 30, '...'))
                ."</span>":"-" !!}

                <span class="full-text" style="display: none">
                    {{$log->payload}}
                </span>
                <a href="javascript:void(0)" onclick="showFullText('Payload', this)" class="readmore btn btn-xs text-dark">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
                        
        </td>
        <td>
            {!! ($log->payload)?"<span class='lesstext'>".
                (\Illuminate\Support\Str::limit($log->response , 30, '...'))
                ."</span>":"-" !!}
            <span class="full-text" style="display: none">
                {{$log->response}}
            </span>
            <a href="javascript:void(0)" onclick="showFullText('Response', this)" class="readmore btn btn-xs text-dark">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
        </td>
        <td>{{$log->created_at}}</td>
    </tr>
@empty
    <tr>
        <td colspan="8" style="text-align: center">No record found</td>
    </tr>
@endforelse