<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Email Id</th>
                <th class="text-center">Log</th>
                <th class="text-center">Message</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($data as $key => $log)
                <tr>
                    <td class="text-center">{{ $log->id }}</td>
                    <td class="text-center">{{ $log->email_id }}</td>
                    <td class="text-center">{{ $log->email_log }}</td>
                    <td style="max-width: 1000px; width: 1000px" class="text-center show-log-info">
                        <span class="log-message-full hidden"
                            style="overflow-wrap: break-word;word-wrap: break-word;hyphens: auto;white-space: normal;">{{ $log->message }}</span>
                        <span class="log-message-limit">{{ \Str::limit($log->message, 50) }}</span>
                    </td>
                </tr>
            @endforeach
            @if ($data->isEmpty())
                <tr>
                    <td colspan="4" class="text-center">No Record Available</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="text-center">
        <div class="text-center">
            {!! $data->links() !!}
        </div>
    </div>
</div>
