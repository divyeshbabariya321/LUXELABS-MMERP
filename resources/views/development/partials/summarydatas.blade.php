@foreach ($issues as $key => $issue)
    @if($issue->created_by == auth()->user()->id || $issue->master_user_id == auth()->user()->id || $issue->assigned_to == auth()->user()->id)
        @php
            $developerTime = $issue->developerTime;
            $time_history = $issue->time_history;
        @endphp
    @endif
    @include($issue->view)
@endforeach
