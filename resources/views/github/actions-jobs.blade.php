<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: auto">Action</th>
            <th style="width: auto">Job ID</th>
            <th style="width: auto">Job Name</th>
            <th style="width: auto">Job Status</th>
            <th style="width: auto">Job Conclusion</th>
            <th style="width: auto">Steps</th>
        </tr>
    </thead>
    <tbody>
        @if (count($actions) > 0)
            @foreach ($actions as $action)
                @if (count($action['jobs']) > 0)
                    @foreach ($action['jobs'] as $job)
                        <tr>
                            @if ($loop->first)
                            <td rowspan="{{ count($action['jobs']) }}">{{ $action['name'] }}</td>
                            @endif
                            <td>{{ $job['id'] }}</td>
                            <td>{{ $job['name'] }}</td>
                            <td>{{ $job['status'] }}</td>
                            <td>{{ $job['conclusion'] }}</td>
                            <td>
                                @if (count($job['steps']) > 0)
                                    <div class="scrollable-steps">
                                        <ul>
                                            @foreach ($job['steps'] as $step)
                                                <li>{{ $step['name'] }} - {{ $step['conclusion'] }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        @endif
    </tbody>
</table>