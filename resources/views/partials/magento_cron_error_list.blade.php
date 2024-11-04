@foreach ($magentoCronErrorLists as $index => $cronData)
    @php
        $sNo = $index + 1 + (($magentoCronErrorLists->currentPage() - 1) * $magentoCronErrorLists->perPage());
        $websiteSnippet = strlen($cronData->website) > 15 ? substr($cronData->website, 0, 15) . '...' : $cronData->website;
        $jobCodeSnippet = strlen($cronData->job_code) > 15 ? substr($cronData->job_code, 0, 15) . '...' : $cronData->job_code;
        $messageSnippet = strlen($cronData->cron_message) > 15 ? substr($cronData->cron_message, 0, 15) . '...' : $cronData->cron_message;
    @endphp
    <tr>
        <td>{{ $sNo }}</td>
        <td>{{ $websiteSnippet }}</td>
        <td>{{ $cronData->cron_id }}</td>
        <td>{{ $jobCodeSnippet }}</td>
        <td>{{ $messageSnippet }}</td>
        <td>{{ $cronData->cron_created_at }}</td>
        <td>{{ $cronData->cron_scheduled_at }}</td>
        <td>{{ $cronData->cron_executed_at }}</td>
        <td>{{ $cronData->cron_finished_at }}</td>
    </tr>
@endforeach
