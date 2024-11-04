@php
$i = isset($i) && $i > 0 ? $i : 1;
@endphp
@foreach ($logs as $log)
    <tr>
        
        <td>{{ $log->id }}</td>
        <td>{{ $log->flow_name }}</td>
        <td>{{ $log->modalType }}</td>
        <td>{{ $log->lead_name }}</td>
        <td>{{ $log->website }}</td>
        <td>{{ $log->flow_description }}</td>
        <td>{{ $log->messages }}</td>
        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d-m-y H:i:s') }}</td>
        <td>
            <button class="btn btn-xs btn-none-border show_error_logs" data-id="{{ $log->id }}"><i
                    class="fa fa-eye"></i></button>
        </td>
    </tr>
@endforeach
