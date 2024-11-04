<tr class="customer-raw-line"  data-cmid="{{$pam->id}}">
@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHod  = Auth::user()->hasRole('HOD of CRM');

    $context = 'email';
    $issueID = null;
    if($pam->chatBotReplychat){

        $reply = json_decode($pam->chatBotReplychat->reply);

        if(isset($reply->context)){
            $context = $reply->context;
            $issueID = $reply->issue_id;
        }
    }

@endphp


        <td data-context="{{ $context }}" data-url={{ route('whatsapp.send', ['context' => $context]) }} {{ $pam->taskUser ? 'data-chat-message-reply-id='.$pam->chat_bot_id : '' }}  data-chat-id="{{ $pam->chat_id }}" data-customer-id="{{$pam->customer_id ?? ( $pam->taskUser ? $issueID : '')}}" data-vendor-id="{{$pam->vendor_id}}" data-supplier-id="{{$pam->supplier_id}}" data-chatbot-id="{{$pam->chat_bot_id}}" data-email-id="{{$pam->email_id}}">
            @if($pam->supplier_id > 0)
                @if (strlen($pam->supplier_name) > 5)
               <p data-log_message="{{ $pam->supplier_name }}" class="user-inputt p-0 m-0">{{  substr($pam->supplier_name,0,4)   }}..</p>
                @else
                <p class="p-0 m-0">{{  $pam->supplier_name  }}</p>
                @endif
        </td>

             @else
            @if (isset($pam->taskUser) && ( strlen($pam->taskUser->name) > 5) || strlen($pam->customer_name) > 5 || $pam->vendor_id > 0 && strlen($pam->vendors_name) > 5)
            <p  data-log_message="{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}" class="user-inputt p-0 m-0">{{  ($pam->vendor_id > 0 ) ? substr($pam->vendors_name,0,6) : ( $pam->taskUser ? substr($pam->taskUser->name,0,4) : substr($pam->customer_name,0,4)  )  }}..</p>
            @else

                @if(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))
                    <p class="p-0 m-0">{{ $pam->from_name }}</p>
                @else
                    <p class="p-0 m-0">{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name  : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}</p>
                @endif

            @endif

           </td>
            @endif
                @if (strlen($pam->website_title) > 5)
                    <td  data-log_message="{{ $pam->website_title }}" class="log-website-popup user-iput">
                        <p class="p-0 m-0">{{ substr($pam->website_title,0,5) }}...</p>
                    </td>
                @else
                    <td>{{ $pam->website_title }}</td>
                @endif
        <!-- DEVTASK-23479 display message type -->
        <td>
            @if($pam->message_type!='')
                {{ucfirst($pam->message_type)}}
            @elseif ($pam->is_email>0)
                {{'Email'}}
            @elseif ($pam->task_id>0)
                {{'Task'}}
            @elseif ($pam->developer_task_id>0)
                {{'Dev Task'}}
            @elseif ($pam->ticket_id>0)
                {{'Ticket'}}
            @elseif ($pam->user_id > 0)
                {{'User'}}
            @elseif ($pam->supplier_id > 0)
                {{'Supplier'}}
            @elseif ($pam->customer_id > 0)
                {{'Customer'}}
            @endif
        </td>
        <!-- DEVTASK-23479 display message type -->
        <!-- Purpose : Add question - DEVTASK-4203 -->
        @if($pam->is_audio)
            <td class="user-input" ><audio controls="" src="{{ \App\Helpers::getAudioUrl($pam->message) }}"></audio></td>
        @elseif(strlen($pam->question) > 10)
            <td   class="log-message-popup user-input" data-log_message="{!!$pam->question!!}">{{ substr($pam->question,0,15) }}...
                @if($pam->chat_read_id == 1)
                    <a href="javascript:;" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                        <i class="fa fa-check-square-o text-dark"></i>

                    </a>
                @else
                    <a href="javascript:;" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                        <i class="fa fa-check-square-o text-secondary"></i>

                    </a>
                @endif
            </td>

        @elseif(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))
            <td class="user-input" >{{ $pam->message }}</td>
        @else
            @if (strlen($pam->question) > 10)
                <td   class="log-message-popup user-input" data-log_message="{!!$pam->question!!}">{{ substr($pam->question,0,15) }}...
                    @if($pam->chat_read_id == 1)
                        <a href="javascript:;" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                            <i class="fa fa-check-square-o text-dark"></i>

                        </a>
                    @else
                        <a href="javascript:;" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                            <i class="fa fa-check-square-o text-secondary"></i>

                        </a>
                    @endif
                </td>
            @else
                <td class="user-input" >{{ $pam->question }}
                    @if($pam->chat_read_id == 1)
                        <a href="javascript:;" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                            <i class="fa fa-check-square-o text-dark"></i>

                        </a>
                    @else
                        <a href="javascript:;" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                            <i class="fa fa-check-square-o text-secondary"></i>

                        </a>
                    @endif
                </td>
            @endif
        @endif
{{--            {{ $pam->question }}--}}

        @if($pam->answer_is_audio)
        <td class="boat-replied"><audio controls="" src="{{ \App\Helpers::getAudioUrl($pam->answer) }}"></audio></td>
        @else
            <td class="boat-replied">{{ $pam->answer }}
            </td>
        @endif



                    @if (strlen($pam->suggested_replay) > 10)
                        <td data-log_message="{{ $pam->suggested_replay }}"
                            class="bot-suggested-reply-popup boat-replied">{{ substr( $pam->suggested_replay ,0,15) }}...

                        @if($pam->is_approved == false && $pam->is_reject == false)
                                <div class="suggested_replay_action d-inline">
                                <a href="javascript:;" class="send_suggested_replay" data-value="0"
                                   data-id="{{ $pam->tmp_replies_id }}">
                                    <i class="fa fa-window-close-o text-secondary px-1 py-2" aria-hidden="true"></i>
                                </a>
                                <a href="javascript:;" class="send_suggested_replay" data-value="1"
                                   data-id="{{ $pam->tmp_replies_id }}">
                                    <i class="fa fa-check-square-o text-secondary px-1 py-2"></i>
                                </a>
                                </div>
                        @endif
                        </td>
                    @else
                        <td class="boat-replied">{{ $pam->suggested_replay }}
                            @if($pam->suggested_replay && $pam->is_approved == false && $pam->is_reject == false)
                                <a href="javascript:;" class="read-message" data-value="0"
                                   data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-window-close-o text-secondary p-2" aria-hidden="true"></i>
                                </a>
                                <a href="javascript:;" class="read-message" data-value="0"
                                   data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-check-square-o text-secondary p-2"></i>
                                </a>
                            @endif
                        </td>
                    @endif


        <td class="message-input p-0 pt-2 pl-3">
            <div class=" cls_textarea_subbox">
                <div class="btn-toolbar" role="toolbar">
                    <div class="w-75">
                        <textarea rows="1" class="form-control quick-message-field cls_quick_message addToAutoComplete" data-id="{{ $pam->id }}" data-customer-id="{{ $pam->customer_id }}" name="message" id="message_{{$pam->id}}" placeholder="Message"></textarea>
                    </div>
                    <div class="w-25 pl-2" role="group" aria-label="First group">
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1">
                            <input name="add_to_autocomplete" class="add_to_autocomplete" type="checkbox" value="true">
                        </button>
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image send-message1" id="send-message_{{ $pam->id }}" data-id="{{ $pam->id }}"  data-customer-id="{{ !empty($pam->customer_id) ? $pam->customer_id : '' }}" data-email-id={{ !empty($pam->email_id) ? $pam->email_id : ''}}>
                            <img src="/images/filled-sent.png">
                        </button>
                        @if($pam->task_id > 0 )
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="task" data-id="{{$pam->task_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                        <input type="hidden" name="is_audio" id="is_audio_{{$pam->id}}" value="0" >
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image btn-trigger-rvn-modal" data-id="{{$pam->id}}" data-tid="{{$pam->task_id}}" data-load-type="text" data-all="1" title="Record & Send Voice Message"><img src="{{asset('images/record-voice-message.png')}}" alt=""></button>
                        @elseif($pam->developer_task_id > 0 )
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="developer_task" data-id="{{$pam->developer_task_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                        <input type="hidden" name="is_audio" id="is_audio_{{$pam->id}}" value="0" >
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image btn-trigger-rvn-modal" data-id="{{$pam->id}}" data-tid="{{$pam->developer_task_id}}" data-load-type="text" data-all="1" title="Record & Send Voice Message"><img src="{{asset('images/record-voice-message.png')}}" alt=""></button>
                        @elseif($pam->vendor_id > 0 )
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="vendor" data-id="{{$pam->vendor_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>

                        @elseif(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))

                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="email" data-id="{{$pam->email_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>

                        @else
                        <button   type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="customer" data-id="{{$pam->customer_id }}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                        <button   type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-object="customer" data-id="{{$pam->customer_id }}" data-attached="1" data-limit="10" data-load-type="images" data-all="1" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                        @endif
                        @if($pam->is_email==1 )
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image editmessagebcc"  data-to_email="{{$pam->to_email}}" data-from_email="{{$pam->from_email}}" data-id="{{$pam->id}}" data-cc_email="{{$pam->cc_email}}" data-all="1" title=""><i class="fa fa-edit"></i></button>
                        @endif
                        <div id="fa_microphone_slash_{{$pam->id}}" style="display: none" ><i class="fa fa-microphone-slash" aria-hidden="true"></i></div>
                        <button type="button" style="font-size: 16px" data-id="{{$pam->id}}" class="btn btn-sm m-0 p-0 mr-1 speech-button"  id="speech-button_{{$pam->id}}"><i class="fa fa-microphone" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </td>
        <td class="boat-replied">{{ $pam->reply_from }}</td>
        <td class="communication p-0 pt-2 pl-3">
          <div class="row m-0">
              <div class="col-6 d-inline form-inline p-0">
                  <div style="float:left;width: calc(100% - 5px)">
                      <select name="quickCategory" class="form-control mb-2 quickCategory select-quick-category">
                            <option value="">Select Category</option>
                            @foreach($reply_categories as $category)
                                @if(!empty($pam->vendor_id) && $category->default_for=='vendors')
                                    <option value="{{ $category->approval_leads }}" selected data-id="{{$category->id}}">{{ $category->name }}</option>
                                @elseif (!empty($pam->customer_id) && $category->default_for=='customers')
                                    <option value="{{ $category->approval_leads }}" selected data-id="{{$category->id}}">{{ $category->name }}</option>
                                @elseif (!empty($pam->user_id) && $category->default_for=='users')
                                    <option value="{{ $category->approval_leads }}" selected data-id="{{$category->id}}">{{ $category->name }}</option>
                                @else
                                    <option value="{{ $category->approval_leads }}" data-id="{{$category->id}}">{{ $category->name }}</option>
                                @endif
                          @endforeach
                      </select>
                  </div>
{{--                  <div style="float:right;width: 20px;">--}}
{{--                      <a style="padding: 5px 0;" class="btn btn-image delete_category"><img src="/images/delete.png"></a>--}}
{{--                  </div>--}}
              </div>
              <div class="col-6 d-inline form-inline p-0">
                  <div style="float: left; width:calc(100% - 5px)" class="mt-0">
                      <select name="quickComment" class="form-control quickComment select-quick-reply">
                          <option value="">Quick Reply</option>
                      </select>
                  </div>
{{--                  <div style="float: right;width: 20px;">--}}
{{--                      <a style="padding: 5px 0;" class="btn btn-image delete_quick_comment"><img src="/images/delete.png"></a>--}}
{{--                  </div>--}}
              </div>
          </div>
        </td>
        <td>
            <div class="actions">
                <a href="javascript:;" style="display: inline-block" class="resend-to-bot btns"
                   data-id="{{ $pam->id }}">
                    <i style="color: #757575c7;" class="fa fa-refresh" title="Resend to bot" aria-hidden="true"></i>

                </a>
                <a href="javascript:;" style="display: inline-block" class="approve_message  btns  pt-2"
                   data-id="{{ $pam->chat_id }}">
                    <i style="color: #757575c7;" class="fa fa-plus" aria-hidden="true"></i>
                </a>

                @if($pam->vendor_id > 0)
                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image" onclick="changeSimulatorSetting('vendor', {{ $pam->vendor_id }}, {{ $pam->vendor_auto_simulator == 0 }})">
                        <i style="color: #757575c7;" class="fa fa-{{$pam->vendor_auto_simulator == 0 ? 'play' : 'pause'}}" aria-hidden="true"></i>
                    </button>
                @elseif($pam->customer_id > 0)
                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image" onclick="changeSimulatorSetting('customer', {{ $pam->customer_id }}, {{ $pam->customer_auto_simulator == 0 }})">
                        <i style="color: #757575c7;" class="fa fa-{{$pam->customer_auto_simulator == 0 ? 'play' : 'pause'}}" aria-hidden="true"></i>
                    </button>
                @elseif($pam->supplier_id > 0)
                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image" onclick="changeSimulatorSetting('supplier', {{ $pam->supplier_id }}, {{ $pam->supplier_auto_simulator == 0 }})">
                        <i style="color: #757575c7;" class="fa fa-{{$pam->supplier_auto_simulator == 0 ? 'play' : 'pause'}}" aria-hidden="true"></i>
                    </button>
                @endif

                @if($pam->vendor_id > 0)
                    <a href="{{  route('simulator.message.list', ['object' => 'vendor', 'object_id' =>  $pam->vendor_id]) }}" title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></a>
                @elseif($pam->customer_id > 0)
                    <a href="{{  route('simulator.message.list', ['object' => 'customer', 'object_id' =>  $pam->customer_id]) }}" title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></a>
                @elseif($pam->supplier_id > 0)
                    <a href="{{  route('simulator.message.list', ['object' => 'supplier', 'object_id' =>  $pam->supplier_id]) }}" title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></a>
                @endif

            @if($pam->approved == 0)
            <a href="javascript:;" style="display: inline-block" class="approve-message btns " data-id="{{ !empty($pam->chat_id) ? $pam->chat_id : $pam->id  }}">
{{--                <img width="15px" height="15px" src="/images/completed.png">--}}
                <i  style="color: #757575c7;" class="fa fa-check-square" aria-hidden="true"></i>

            </a>
            @endif
            @if($pam->suggestion_id)
                <a href="javascript:;"  style="display: inline-block" class="add-more-images btns" data-id="{{ $pam->chat_id }}">
                    <img width="15px" title="Attach More Images" height="15px" src="/images/customer-suggestion.png">
                </a>
            @endif
            @if($pam->customer_id > 0)
                @if($pam->customer_do_not_disturb == 1)
                    <button type="button" class="btn btn-xs btn-image do_not_disturb" data-id="{{$pam->customer_id}}">
                        <i style="color: #c10303;" class="fa fa-ban" aria-hidden="true"></i>
                    </button>
                @else
                    <button type="button" class="btn btn-xs btn-image do_not_disturb" data-id="{{$pam->customer_id}}">
                        <i style="color: #757575c7;" class="fa fa-ban" aria-hidden="true"></i>
                    </button>
                @endif
                <a class="create-customer-ticket-modal btns pt-2" style="display: inline-block" href="javascript:;" data-customer_id="{{$pam->customer_id}}" data-toggle="modal" data-target="#create-customer-ticket-modal" title="Create Ticket"><i style="color: #757575c7;" class="fa fa-ticket" aria-hidden="true"></i></a>
            @endif

            @if($pam->reply_from == "reminder")
                @if($pam->task_id > 0 )
                    <a href="javascript:;" data-id="{{$pam->task_id}}" data-type="task" class="pd-5 stop-reminder" >
                        <i class="fa fa-bell background-grey" aria-hidden="true"></i>
                    </a>
                @elseif($pam->developer_task_id > 0)
                    <a href="javascript:;" data-id="{{$pam->developer_task_id}}" data-type="developer_task" class="pd-5 stop-reminder" >
                        <i class="fa fa-bell background-grey" aria-hidden="true"></i>
                    </a>
                @endif
            @endif


            <!-- <span class="check-all" data-id="{{ $pam->chat_id }}">
              <i class="fa fa-indent" aria-hidden="true"></i>
            </span> -->
        </div>
        </td>
    </tr>
    
    
 
