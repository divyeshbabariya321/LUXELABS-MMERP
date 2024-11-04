@foreach($data as $prop)
    {
    <tr>
        <td>{{ $prop->id }}</td>
        <td>{{ $prop->created_at_date }}</td>
        <td>{{ $prop->user->name }}</td>
        <td>{{ $prop->userrequest->name }}</td>
        <td>
            {{ $prop->requested_time }}
            @if(!empty($prop->remarks))
                <button type="button" data-id="{{ $prop->id }}" class="btn requested-remarks-view"
                        style="padding:1px 0px;">
                    <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                </button>
            @endif
        </td>
        <td>
            @if($prop->request_status==0)
                {{'Requested'}}
            @elseif($prop->request_status==1)
                {{'Accepeted'}}
            @elseif($prop->request_status==2)
                {{'Decline'}}
                @if(!empty($prop->decline_remarks))
                    <button type="button" data-id="{{ $prop->id }}" class="btn decline_remarks-view"
                            style="padding:1px 0px;">
                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                    </button>
                @endif
            @endif
        </td>
    </tr>
    }
@endforeach