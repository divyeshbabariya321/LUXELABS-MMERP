@foreach ($logs as $log)

    <tr>
        <td>{{ $log->id }}</td>
        <td>{{ $log->website }}</td>
        <td>{{  $log->device  }}</td>
        <td>{{  $log->date  }}</td>
        <td>{{ \Carbon\Carbon::parse($log->log_created)->format('d-m-y H:i:s')  }}</td>
        <td>
            @if( $log->log_text == "Log File Not Found")
            {{  $log->log_text  }}
            @else
                <a href="{{  $log->log_text  }}" target="_blank">Log</a>
            @endif
        </td>
    </tr>
@endforeach