@foreach($data['task']['pending'] as $task)
    @include("task-module.partials.task-search-row",compact('task'))
@endforeach