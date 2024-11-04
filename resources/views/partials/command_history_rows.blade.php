@forelse($postHis as $log)
    <tr>
        <td>{{ $log->id }}</td>
        <td>{{ $log->userName }}</td>
        <td class="expand-row-msg" data-name="command" data-id="{{ $log->id }}">
            <span class="show-short-command-{{ $log->id }}">{{ Str::limit($log->command_name, 10) }}...</span>
            <span style="word-break:break-all;" class="show-full-command-{{ $log->id }} hidden">{{ $log->command_name }}</span>
        </td>
        <td>{{ $log->status }}</td>
        <td class="expand-row-msg" data-name="response" data-id="{{ $log->id }}">
            <span class="show-short-response-{{ $log->id }}">{{ Str::limit($log->response, 10) }}...</span>
            <span style="word-break:break-all;" class="show-full-response-{{ $log->id }} hidden">{{ $log->response }}</span>
        </td>
        <td>{{ $log->job_id }}</td>
        <td>{{ $log->created_at }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">No history found</td>
    </tr>
@endforelse
