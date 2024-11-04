<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: auto">ID</th>
            <th style="width: auto">Job Name</th>
            <th style="width: auto">Status</th>
            <th style="width: auto">Conclusion</th>
            <th style="width: auto">Steps</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($githubActionRunJobs->jobs as $job)
            <tr>
                <td>{{ $job->id }}</td>
                <td>{{ $job->name }}</td>
                <td>{{ $job->status }}</td>
                <td>{{ $job->conclusion }}</td>
                <td>
                    <div class="scrollable-steps">
                        <ul>
                            @foreach ($job->steps as $step)
                                @if ($step->conclusion != "success")
                                    <li>{{ $step->name }} - {{ $step->conclusion }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>