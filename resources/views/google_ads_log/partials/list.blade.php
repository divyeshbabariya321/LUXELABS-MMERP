@foreach ($logs as $log)
    <tr>
        <td>{{ $log->user->name ?? "-" }}</td>
        <td>
            <b class="{{ ($log->type == 'SUCCESS') ? 'text-success' : 'text-danger' }}">{{ $log->type }}</b>
        </td>
        <td>{{ $log->module }}</td>
         <td style="width: 30%" class="expand-row table-hover-cell">
            <span class="td-mini-container">
            {{ strlen( $log->message ) > 110 ? substr( $log->message , 0, 110).'...' :  $log->message }}
            </span>
            <span class="td-full-container hidden">
            {{ $log->message }}
            </span>
        </td>
        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d-m-y H:i:s')  }}</td>
    </tr>
@endforeach