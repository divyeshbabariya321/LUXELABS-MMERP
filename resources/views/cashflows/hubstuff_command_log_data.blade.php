@foreach ($logs as $log)

    <tr>
        <td>{{ $log->id }}</td>
        <td>{{ \Carbon\Carbon::parse($log->date)->format('d-m-y')  }}</td>
        <td>{{ \Carbon\Carbon::parse($log->date)->format('l')}}</td>
        <td>{{  $log->weekly  }}</td>
        <td>{{  $log->biweekly  }}</td>
        <td>{{  $log->fornightly  }}</td>
        <td>{{  $log->monthly  }}</td>
        <td>{{  $log->userCount  }}</td>
        <td>{{  $log->messages  }}</td>
      
        <td>
            <button class="btn btn-xs btn-none-border show_error_logs" data-id="{{ $log->id}}"><i class="fa fa-eye"></i></button>
 
        </td>
    </tr>
@endforeach