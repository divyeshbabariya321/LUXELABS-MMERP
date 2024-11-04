@foreach ($category as $cat)
    @php
        $latest_messages = App\ChatMessage::where('user_feedback_id', $user_id)->where('user_feedback_category_id', $cat->id)->orderBy('id','DESC')->first();
        if ($latest_messages) {
            $latest_msg = $latest_messages->message;
            if (strlen($latest_msg) > 20) {
                $latest_msg = substr($latest_messages->message,0,20).'...';
            }
        }
        $feedback_status = App\UserFeedbackStatusUpdate::where('user_id', $user_id)->where('user_feedback_category_id', $cat->id)->first();
        $status_id = 0;
        if ($feedback_status) {
            $status_id = $feedback_status->user_feedback_status_id;
        }
    @endphp
    <tr data-cat_id="{{ $cat->id }}" data-user_id="{{ $user_id }}">
        <td>{{ $cat->category }}</td>
        <td class="communication-td">
            <input type="text" class="form-control send-message-textbox" data-id="{{$user_id}}" id="send_message_{{$user_id}}" name="send_message_{{$user_id}}" placeholder="Enter Message...." style="margin-bottom:5px;width:77%;display:inline;" @if (!Auth::user()->isAdmin()) {{ "readonly" }} @endif/>
            <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image send-message-open" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$user_id}}" ><img src="/images/filled-sent.png"/></button></button>
            @if ($latest_messages && $latest_messages->user_feedback_category_id == $cat->id)
                <span class="latest_message">@if ($latest_messages->send_by) {{ $latest_msg }} @endif</span>
            @else
                <span class="latest_message"></span>
            @endif
        </td>
        <td class="communication-td">
            <input type="text" class="form-control send-message-textbox" data-id="{{$user_id}}" id="send_message_{{$user_id}}" name="send_message_{{$user_id}}" placeholder="Enter Message...." style="margin-bottom:5px;width:77%;display:inline;" @if (Auth::user()->isAdmin()) {{ "readonly" }} @endif/>
            <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image send-message-open" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$user_id}}" ><img src="/images/filled-sent.png"/></button></button>
            @if ($latest_messages && $latest_messages->user_feedback_category_id == $cat->id)
                <span class="latest_message">@if (!$latest_messages->send_by) {{ $latest_msg }} @endif</span>
            @else
                <span class="latest_message"></span>
            @endif
        </td>
        <td>
            <select class="form-control user_feedback_status">
                <option value="">Select</option>
                @foreach ($status as $st)
                    <option value="{{$st->id}}" @if ($st->id == $status_id) {{ "selected" }} @endif>{{ $st->status }}</option>
                @endforeach
            </select>
        </td>
        <td><button type="button" class="btn btn-xs btn-image load-communication-modal" data-feedback_cat_id="{{$cat->id}}" data-object='user-feedback' data-id="{{$user_id}}" style="mmargin-top: -0%;margin-left: -2%;" title="Load messages"><img src="/images/chat.png" alt=""></button></td>
    </tr>
@endforeach