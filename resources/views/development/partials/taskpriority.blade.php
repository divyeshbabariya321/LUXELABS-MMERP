@foreach ($issues as $issue)
<?php
    $isReviwerLikeAdmin =  auth()->user()->isReviwerLikeAdmin();
        $userID =  Auth::user()->id;
?>
<tr>
    <td><input type="hidden" name="priority[]" value="{{$issue['id']}}"/>{{$issue['id']}}</td>;
    <td>{{$issue['module']}}</td>
    <td>{{$issue['subject']}}</td>
    <td>{{$issue['task']}}</td>
    @if($isReviwerLikeAdmin)
        <td class="expand-row">
            <!-- class="expand-row" -->
            <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? 'text-danger' : '' }}" style="word-break: break-all;">{{  \Illuminate\Support\Str::limit($issue->message, 150, $end='...') }}</span>
            <input type="text" class="form-control send-message-textbox addToAutoComplete" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" style="margin-bottom:5px"/>
            <input class="" name="add_to_autocomplete" class="add_to_autocomplete" type="checkbox" value="true">
            {{ html()->select("send_message_" . $issue->id, ["to_developer" => "Send To Developer", "to_master" => "Send To Master Developer", "to_team_lead" => "Send To Team Lead", "to_tester" => "Send To Tester"])->class("form-control send-message-number")->style("width:30% !important;display: inline;") }}
              
            <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message"  data-id="{{$issue->id}}" ><img src="/images/filled-sent.png"/></button>
        
                <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" style="margin-top:-0%;margin-left: -3%;" title="Load messages"><img src="/images/chat.png" alt=""></button>
            <br>
                <div class="td-full-container hidden">
                    <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send Attachment</button>
                    <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send Images</button>
                    <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple/>
                 </div>
            </td>

    @elseif($issue->created_by == $userID || $issue->master_user_id == $userID || $issue->assigned_to == $userID)
    <td class="expand-row">
        <!--span style="word-break: break-all;">{{  \Illuminate\Support\Str::limit($issue->message, 150, $end='...') }}</span>
            @if ($issue->getMedia(config('constants.media_tags'))->first())
                <br>
                @foreach ($issue->getMedia(config('constants.media_tags')) as $image)
                    <a href="{{ getMediaUrl($image) }}" target="_blank" class="d-inline-block">
                        <img src="{{ getMediaUrl($image) }}" class="img-responsive" style="width: 50px" alt="">
                    </a>
                @endforeach
            @endif
            <div>
                <div class="panel-group">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" href="#collapse_{{$issue->id}}">Messages({{count($issue->messages)}})</a>
                            </h4>
                        </div>
                    </div>
                </div>
            </div-->
        
        <!-- class="expand-row" -->
        <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? 'text-danger' : '' }}" style="word-break: break-all;">{{  \Illuminate\Support\Str::limit($issue->message, 150, $end='...') }}</span>
        <input type="text" class="form-control send-message-textbox" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" style="margin-bottom:5px"/>
        {{ html()->select("send_message_" . $issue->id, ["to_master" => "Send To Master Developer", "to_developer" => "Send To Developer", "to_team_lead" => "Send To Team Lead", "to_tester" => "Send To Tester"])->class("form-control send-message-number")->style("width:85% !important;display: inline;") }}
        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message"  data-id="{{$issue->id}}" ><img src="/images/filled-sent.png"/></button>
    
      
            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" style="margin-top: 2%;" title="Load messages"><img src="/images/chat.png" alt=""></button>
        <br>
            <div class="td-full-container hidden">
                <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send Attachment</button>
                <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send Images</button>
                <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple/>
             </div>
        
        </td>
        @endif

    
        <td>{{$issue['created_at']}}</td>
    
        <td>{{$issue['created_by']}}</td>
    <td><a href="javascript:;" class="delete_priority" data-id="{{$issue['id']}}">Remove<a></td>
</tr>   
@endforeach
