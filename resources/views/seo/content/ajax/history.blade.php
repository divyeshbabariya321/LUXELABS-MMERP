<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Old {{ $seoType == 'user' ? 'User' : 'Price' }}</th>
            <th>New {{ $seoType == 'user' ? 'User' : 'Price' }}</th>
            <th>Date</th>
        </tr>
    </thead>

    @php
        $oldHistroy = null;
    @endphp
    @foreach ($seoHistory as $ky => $history)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $history->user->name ?? '-' }}</td>
            @if($history->type == 'user')
                <td>{{ $oldHistroy->msgUser->name ?? '-' }}</td>
                <td>{{ $history->msgUser->name ?? '-' }}</td>
            @else
                <td>{{ $oldHistroy->message ?? '-' }}</td>
                <td>{{ $history->message ?? '-' }}</td>
            @endif
            <td>{{ date('Y-m-d H:i A', strtotime($history->created_at)) }}</td>
        </tr>
        @php
            $oldHistroy = $history;
        @endphp
    @endforeach
</table>