@foreach(  $data['task']['statutory_not_completed'] as $task)
    @php $row_type = "statutory"; @endphp
    @include("task-module.partials.task-row",compact('task', 'row_type'))
@endforeach