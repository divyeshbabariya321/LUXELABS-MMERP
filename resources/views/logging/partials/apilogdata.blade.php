@foreach ($logs as $log)

    <tr class="currentPage" data-page="{{$logs->currentPage()}}">

        <td>{{$log->id}}</td>

        <td>{{$log->ip}}</td>

        @if($log->api_name)
            @php 
                $api_name = explode('@', $log->api_name);
            @endphp
            <td>{{ wordwrap($api_name[0], 30) }}</td>
        @else
        <td></td>
        @endif
        <td>{{ $log->method_name }}</td>
        <td>{{ $log->method }}</td>
        <td class="expand-row table-hover-cell">
            <span class="td-mini-container">
            {{ strlen( $log->url ) > 50 ? substr( $log->url , 0, 50).'...' :  $log->url }}
            </span>
            <span class="td-full-container hidden">
            {{ $log->url }}
            </span>
        </td>
            
        <td class="expand-row table-hover-cell">
            <span class="td-mini-container">
                {{ strlen( $log->message) > 60 ? substr( $log->message, 0, 60).'...' :  $log->message}}
            </span>
        </td>
        <td>{{ $log->status_code }}</td>
        <td>{{ $log->time_taken }} s</td>
        
        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d-m-y H:i:s')  }}</td>

        <td><button class="btn btn-warning showModalResponse" data-id="{{$log->id}}">View</button></td>
    </tr>
@endforeach