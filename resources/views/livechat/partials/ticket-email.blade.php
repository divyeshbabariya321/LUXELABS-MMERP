
    @foreach ($emails as $key => $email)
        <tr id="{{ $email->id }}-email-row" class="search-rows">
            <td>@if($email->status != 'bin')
                    <input name="selector[]" id="ad_Checkbox_{{ $email->id }}" class="ads_Checkbox" type="checkbox" value="{{ $email->id }}" style="margin-left: 41%;" />
                @endif
            </td>
            
            <td>{{ Carbon\Carbon::parse($email->created_at)->format('d-m-Y H:i:s') }}</td>
            
            <td data-toggle="modal" data-target="#viewMore"  onclick="opnModal('{{$email->from}}')"> 
            {{ substr($email->from, 0,  10) }} {{strlen($email->from) > 10 ? '...' : '' }}
            </td>
            
            <td  data-toggle="modal" data-target="#viewMore"  onclick="opnModal('{{$email->to}}')">
                {{ substr($email->to, 0,  10) }} {{strlen($email->to) > 10 ? '...' : '' }}
            </td>
            
            <td>{{ $email->type }}</td>
			
            <td data-toggle="modal" data-target="#viewMail"  onclick="opnMsg({{$email}})" style="cursor: pointer;">{{ substr($email->subject, 0,  10) }} {{strlen($email->subject) > 10 ? '...' : '' }}</td>
			
            <td>{{ substr($email->message, 0,  10) }} {{strlen($email->message) > 10 ? '...' : '' }}</td>
            
            <td width="1%">
            @if($email->status != 'bin')
                <select class="select selecte2 status">
                    <option  value="" >Please select</option> 
                    @foreach($email_status as $status)
                            @if($status->id == (int)$email->status)
                                <option  value="{{ $status->id }}" data-id="{{$email->id}}"   selected>{{ $status->email_status }}</option> 
                            @else
                                <option  value="{{ $status->id }}" data-id="{{$email->id}}" >{{$status->email_status }}</option> 
                            @endif
                    @endforeach
                </select>
            @else
                Deleted
            @endif
			</td>
            <td>{{ ($email->is_draft == 1) ? "Yes" : "No" }}</td>
            
			<td>		
                <a title="Resend" class="btn-image resend-email-btn" data-type="resend" data-id="{{ $email->id }}" >
                    <i class="fa fa-repeat"></i>
                </a>
            </td>
        </tr>
    @endforeach



