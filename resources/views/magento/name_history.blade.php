<table class="table table-bordered text-nowrap" style="border: 1px solid #ddd;">
    <thead>
        <tr>
            <th>Date</th>
            <th>Old Value</th>
            <th>New Value</th>
            <th>Created By</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($logs as $log)
            <tr>
                <td>{{ $log->updated_at }}</td>
                <td>{{ $log->old_value }}</td>
                <td>{{ $log->new_value }}</td>
                <td>{{ $log->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>