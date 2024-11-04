<table class="table">
    <thead>
        <tr>
            <th>Downloaded Date</th>
            <th>Downloaded By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($downloadHistory as $log)
            <tr>
                <td>{{ $log->downloaded_at }}</td>
                <td>{{ $log->user->name }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2">No record found</td>
            </tr>
        @endforelse
    </tbody>
</table>