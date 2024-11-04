<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <th>Date</th>
            <th>Task Id</th>
            <th>Scrapper Name</th>
            <th>Resource Name</th>
            <th>Scrapper Fixed</th>
            <th>Scrapper Data Verified</th>
            <th>Scrapper Full Run Tested</th>
            <th>Need Proxy</th>
            <th>Moved to AWS</th>
            <th>Remark</th>
        </thead>

        <tbody>
            @if (!$data->isEmpty())
                @foreach ($data as $monitoring)
                    <tr>
                        <td>{{ $monitoring->created_at->toDateString() }}</td>
                        <td>{{ $monitoring->task_id }}</td>
                        <td>{{ $monitoring->scrapper_name }}</td>
                        <td>{{ $monitoring->user->name }}</td>
                        <td>
                            @if ($monitoring->task->developerTaskHistories)
                                @if ($monitoring->task->developerTaskHistories->contains('new_value', \App\DeveloperTask::DEV_TASK_STATUS_SCRAPPER_FIXED))
                                    Fixed the category
                                @else
                                    Not Fixed
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($monitoring->task->developerTaskHistories)
                                @if ($monitoring->task->developerTaskHistories->contains('new_value', \App\DeveloperTask::DEV_TASK_STATUS_SCRAPPER_DATA_VERIFIED))
                                    Verified
                                @else
                                    Not Verified
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($monitoring->task->developerTaskHistories)
                                @if ($monitoring->task->developerTaskHistories->contains('new_value', \App\DeveloperTask::DEV_TASK_STATUS_SCRAPPER_FULL_RUN_TESTED))
                                    Yes
                                @else
                                    No
                                @endif
                            @endif
                        </td>
                        <td>{{ $monitoring->need_proxy ? 'Yes' : 'No' }}</td>
                        <td>{{ $monitoring->move_to_aws ? 'Yes' : 'No' }}</td>
                        <td>{{ $monitoring->remarks }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="10" class="text-center">No Records Found</td>
                </tr>
            @endif
        </tbody>

    </table>
    {{ $data->links() }}
</div>
