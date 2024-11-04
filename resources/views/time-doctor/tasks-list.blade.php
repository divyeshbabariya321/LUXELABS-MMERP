@foreach($tasks as $task)
    <tr>
        <td style="vertical-align:middle;">{{ $loop->iteration }}</td>
        <td style="vertical-align:middle;">{{ $task->time_doctor_task_id }}</td>
        <td style="vertical-align:middle;">{{ $task->summery }}</td>
        <td style="vertical-align:middle;">
            @if (isset($task->account) && isset($task->account->id))
            {{$task->account->time_doctor_email}}
            @else
                -
            @endif
        </td>
        <td style="vertical-align:middle;">{{ $task->created_at }}</td>
        <td style="vertical-align:middle;"><button type="button" class="btn btn-secondary edit_task" data-id="{{ $task->id }}">Edit Task</button></td>
    </tr>
@endforeach