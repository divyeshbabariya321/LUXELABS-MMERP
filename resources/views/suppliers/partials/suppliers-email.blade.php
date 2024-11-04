@if(!empty($emails))
    @foreach ($emails as $key => $email)   
        <tr id="{{ $email->id }}-email-row" class="search-rows">
           
            <td>{{ Carbon\Carbon::parse($email->created_at)->format('d-m-Y H:i:s') }}</td>

            <td data-toggle="modal">
                {{ $email->from}}
            </td>

            <td data-toggle="modal">
                {{ $email->to}}
            </td>

            <td>
                @if(array_key_exists($email->model_type, $emailModelTypes))
                    {{$email->model_type? $emailModelTypes[$email->model_type] : 'N/A' }}
                @else
                    {{ $email->model_type }}
                @endif
            </td>

            <td>{{ $email->type }}</td>
            
            <td data-toggle="modal" data-target="#view-quick-email"  onclick="openQuickMsg({{$email}})" style="cursor: pointer;">{{ $email->subject }}</td>

            <td data-toggle="modal" data-target="#view-quick-email"  onclick="openQuickMsg({{$email}})" style="cursor: pointer;"> {{ substr(strip_tags($email->message), 0,  120) }} {{strlen(strip_tags($email->message)) > 110 ? '...' : '' }}</td>

            <td class="chat-msg ">{{ ($email->is_draft == 1) ? "Yes" : "No" }}</td>
        </tr>
    @endforeach
@endif