@foreach ($tasks as $task)
            <tr>
                <td class="Website-task">
                    @if(isset($task->user)) {{  $task->user->name }} @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($task->date)->format('d-m-Y') }}</td>
                <td class="Website-task">{{ Str::limit($task->details, $limit = 100, $end = '...') }}</td>
                <td>@if($task->task_id) Task #{{$task->task_id}} @elseif($task->developer_task_id) Devtask #{{$task->developer_task_id}} @else Manual @endif </td>
                <td>{{ $task->estimate_minutes }} </td>
                <td>{{ number_format((float)$task->estimate_minutes/60, 2, '.', '') }} </td>
                <td>{{ isset($task->user)?$task->user->hourly_rate:'' }} </td>
                <td style="display:flex;border-bottom: none;">  {{ $task->rate_estimated }} </td>
                <td>{{ $task->currency }}</td>
              </tr>
@endforeach