@foreach( $data['task']['completed'] as $task)
    @php $row_type = "completed"; @endphp
    @include("task-module.partials.task-row",compact('task', 'row_type'))
@endforeach