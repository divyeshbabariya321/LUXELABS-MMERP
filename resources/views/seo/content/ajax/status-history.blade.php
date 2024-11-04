<div class="">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Old Status</th>
                <th>New Status</th>
                <th>User</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @php
                $oldHistory = null;
            @endphp
            @foreach ($statusHistory as $history)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $oldHistroy->status->label ?? '-' }}</td>
                    <td>{{ $history->status->label ?? '-' }}</td>
                    <td>{{ $history->user->name ?? '-' }}</td>
                    <td>{{ date('Y-m-d h:i A', strtotime($history->created_at)) }}</td>
                </tr>
                @php
                    $oldHistroy = $history;
                @endphp
            @endforeach
        </tbody>
    </table>
</div>