@foreach ($productLogs as $log)
    <tr>
        <td>{{ $log->id }}</td>
        <td>{{ $log->log }}</td>
        <td>{{ $log->product_updated_by }}</td>
        <td>
            {{ $log->created_at }} 
		</td>
    </tr>
@endforeach