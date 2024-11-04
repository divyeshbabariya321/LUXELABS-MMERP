@foreach ($logs as $log)
            <tr>
                <td>{{ $log->created_at }}</td>
                <td style="overflow-wrap: anywhere;">{{ $log->command_server }}</td>
                <td style="overflow-wrap: anywhere;">{{ $log->command }}</td>
                <td style="overflow-wrap: anywhere;">{{ $log->command_output }}</td>
                <td>{{ $log->job_id }}</td>
                <td>{{ $log->status }}</td>
            </tr>
        @endforeach