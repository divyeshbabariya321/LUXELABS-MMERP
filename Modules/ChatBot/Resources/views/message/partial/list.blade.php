<style type="text/css">
.cls_remove_rightpadding{padding-right:0!important}
.cls_remove_allpadding{padding-left:0!important;padding-right:0!important}
#chat-list-history tr{word-break:break-word}
.reviewed_msg{word-break:break-word}
.background-grey{color:grey}
.btn-toolbar{align-items:center}
.add_to_autocomplete{height:auto}
@media(max-width:1400px) {
.btns{padding:3px 2px}
}
.select2-container--default .select2-selection--multiple{border:1px solid #ddd!important}
.d-inline.form-inline .select2-container{max-width:100%!important}
.actions{display:flex!important;align-items:center}
.actions a{padding:0 3px!important;display:flex!important;align-items:center}
.actions .btn-image img{width:13px!important}
.read-message{float:right}
.actions .btn.btn-image{line-height:unset;margin-top:unset}
@media (min-width: 576px) and (max-width: 767.98px) {
.d-inline.form-inline .select2-container{width:260px!important}
}
</style>
@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHod  = Auth::user()->hasRole('HOD of CRM');

@endphp
<div class="table-responsive">
<table class="table table-bordered table-striped chatbot page-template-{{ $pendingApprovalMsg->currentPage() }}">
    <thead>
    <tr>
        @if(!empty($dynamicColumnsToShowPostman))
            @if (!in_array('Name', $dynamicColumnsToShowPostman))
                <th width="5%">Name</th>
            @endif

            @if (!in_array('Website', $dynamicColumnsToShowPostman))
                <th width="auto">Website</th>
            @endif

            @if (!in_array('Message Type', $dynamicColumnsToShowPostman))
                <th width="auto">Message Type</th>
            @endif

            @if (!in_array('Name', $dynamicColumnsToShowPostman))
                <th width="10%">User input</th>
            @endif

            @if (!in_array('Bot Replied', $dynamicColumnsToShowPostman))
                <th width="auto">Bot Replied</th>
            @endif

            @if (!in_array('Bot Suggested Reply', $dynamicColumnsToShowPostman))
                <th width="auto">Bot Suggested Reply</th>
            @endif

            @if (!in_array('Message Box', $dynamicColumnsToShowPostman))
                <th width="25%">Message Box </th>
            @endif

            @if (!in_array('From', $dynamicColumnsToShowPostman))
                <th width="5%">From</th>
            @endif

            @if (!in_array('Shortcuts', $dynamicColumnsToShowPostman))
                <th width="15%">Shortcuts</th>
            @endif

            <th width="auto">Is elastic</th>

            @if (!in_array('Action', $dynamicColumnsToShowPostman))
                <th width="auto">Action</th>
            @endif
        @else
            <th width="5%">Name</th>
            <th width="auto">Website</th>
            <th width="auto">Message Type</th>
            <th width="10%">User input</th>
            <th width="auto">Bot Replied</th>
            <th width="auto">Bot Suggested Reply</th>
            <th width="25%">Message Box </th>
            <th width="5%">From</th>
            <th width="15%">Shortcuts</th>
            <th width="auto">Is elastic</th>
            <th width="auto">Action</th>
        @endif

    </tr>
    </thead>
    <tbody>
   @if ($pendingApprovalMsg->isNotEmpty())
        @foreach ($pendingApprovalMsg as $index =>$pam)
            @if(!empty($dynamicColumnsToShowPostman))
                <tr class="customer-raw-line pam-{{ $pam->cid }}">
                @php
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

                @if (!in_array('Name', $dynamicColumnsToShowPostman))
                    <td data-context="{{ $context }}" data-url={{ route('whatsapp.send', ['context' => $context]) }} {{ $pam->taskUser ? 'data-chat-message-reply-id='.$pam->chat_bot_id : '' }}  data-chat-id="{{ $pam->chat_id }}" data-customer-id="{{$pam->customer_id ?? ( $pam->taskUser ? $issueID : '')}}" data-vendor-id="{{$pam->vendor_id}}" data-supplier-id="{{$pam->supplier_id}}" data-chatbot-id="{{$pam->chat_bot_id}}" data-email-id="{{$pam->email_id}}" data-page="{{ $pendingApprovalMsg->currentPage() }}">
                        
                        @if($pam->supplier_id > 0)
                            @if (strlen($pam->supplier_name) > 5)
                           <p data-log_message="{{ $pam->supplier_name }}" class="user-inputt p-0 m-0" title="{{ $pam->supplier_name }}">{{  substr($pam->supplier_name,0,4)   }}..</p>
                            @else
                            <p class="p-0 m-0" title="{{ $pam->supplier_name }}">{{  /*"#".$pam->supplier_id." ".*/$pam->supplier_name  }}</p>
                            @endif
                        
                            @elseif(in_array($pam->message_type, ['FB_COMMENT', 'IG_COMMENT']))
                            <p  title ="{{ $pam->socialComment->commented_by_user }}" class="p-0 m-0">{{ $pam->socialComment->commented_by_user  }}</p>
                        @elseif(in_array($pam->message_type, ['IG_DMS', 'FB_DMS']))
                            <p  title ="{{ $pam->socialContact->name }}" class="p-0 m-0">{{ $pam->socialContact->name  }}</p>
                        @else
                            @if (isset($pam->taskUser) && ( strlen($pam->taskUser->name) > 5) || strlen($pam->customer_name) > 5 || $pam->vendor_id > 0 && strlen($pam->vendors_name) > 5)
                                <p  title="{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}" data-log_message="{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}" class="user-inputt p-0 m-0">{{  ($pam->vendor_id > 0 ) ? substr($pam->vendors_name,0,6) : ( $pam->taskUser ? substr($pam->taskUser->name,0,4) : substr($pam->customer_name,0,4)  )  }}..</p>
                            @else

                                @if(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))
                                    <p  title ="{{ $pam->from_name }}" class="p-0 m-0">{{ $pam->from_name }}</p>
                                @else
                                    <p class="p-0 m-0" title="{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name  : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}">{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name  : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}</p>
                                @endif

                            @endif
                        @endif
                    </td>
                @endif

                @if (!in_array('Website', $dynamicColumnsToShowPostman))
                    @if (strlen($pam->website_title) > 5)
                        <td  data-log_message="{{ $pam->website_title }}" class="log-website-popup user-iput">
                            <p class="p-0 m-0">{{ substr($pam->website_title,0,5) }}...</p>
                        </td>
                    @else
                        <td>{{ $pam->website_title }}</td>
                    @endif
                @endif
                
                <!-- DEVTASK-23479 display message type -->

                @if (!in_array('Message Type', $dynamicColumnsToShowPostman))
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
                @endif

                <!-- DEVTASK-23479 display message type -->
                <!-- Purpose : Add question - DEVTASK-4203 -->

                @if (!in_array('User input', $dynamicColumnsToShowPostman))
                    @if($pam->is_audio)
                        <td class="user-input" ><audio controls="" src="{{ \App\Helpers::getAudioUrl($pam->message) }}"></audio></td>
                    @elseif(!empty($pam->question) && strlen($pam->question) > 10)
                        <td   class="log-message-popup user-input" data-log_message="{!!$pam->question!!}">{{ substr($pam->question,0,15) }}...
                            @if($pam->chat_read_id == 1)
                                <a href="javascript:void(0);" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-check-square-o text-dark"></i>

                                </a>
                            @else
                                <a href="javascript:void(0);" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-check-square-o text-secondary"></i>

                                </a>
                            @endif
                        </td>
                    @elseif(!empty($pam->question) && strlen($pam->question) <= 10)
                        <td class="user-input" >{{ $pam->question }}
                            @if($pam->chat_read_id == 1)
                                <a href="javascript:void(0);" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-check-square-o text-dark"></i>

                                </a>
                            @else
                                <a href="javascript:void(0);" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-check-square-o text-secondary"></i>
                                    @if(strlen($pam->message) > 50) <a class="user-message" data-log_message="{{ $pam->message }}" style="color:#337ab7;">View more</a> @endif
                                </a>
                            @endif
                        </td>
                    @else
                    <td class="user-input" >
                        <span data-toggle="tooltip" data-placement="top" title="{{$pam->message}}"> 
                            {{ substr($pam->message, 0, 50) }}{{ strlen($pam->message) > 50 ? '...' : '' }} 
                        </span></td>
                    @endif
                @endif

                @if (!in_array('Bot Replied', $dynamicColumnsToShowPostman))
                    @if($pam->answer_is_audio)
                        <td class="boat-replied"><audio controls="" src="{{ \App\Helpers::getAudioUrl($pam->answer) }}"></audio></td>
                    @else
                        <td class="boat-replied expand-row" style="word-break: break-all">
                            <span class="td-mini-container">
                                {{ strlen($pam->answer) > 15 ? substr($pam->answer, 0, 15).'...' :  $pam->answer }}
                             </span>
                             <span class="td-full-container hidden">
                                 {{ $pam->answer }}
                             </span>
                        </td>
                    @endif
                @endif

                @if (!in_array('Bot Suggested Reply', $dynamicColumnsToShowPostman))
                    @if (strlen($pam->suggested_reply) > 10)
                        <td data-log_message="{{ $pam->suggested_reply }}"
                            class="bot-suggested-reply-popup boat-replied">{{ substr( $pam->suggested_reply ,0,15) }}...

                        @if($pam->is_approved == false && $pam->is_reject == false)
                                <div class="suggested_reply_action d-inline">
                                <a href="javascript:void(0);" class="send_suggested_reply" data-value="0"
                                   data-id="{{ $pam->tmp_replies_id }}">
                                    <i class="fa fa-window-close-o text-secondary px-1 py-2" aria-hidden="true"></i>
                                </a>
                                <a href="javascript:void(0);" class="send_suggested_reply" data-value="1"
                                   data-id="{{ $pam->tmp_replies_id }}">
                                    <i class="fa fa-check-square-o text-secondary px-1 py-2"></i>
                                </a>
                                </div>
                        @endif
                        </td>
                    @else
                        <td class="boat-replied">{{ $pam->suggested_reply }}
                            @if($pam->suggested_reply && $pam->is_approved == false && $pam->is_reject == false)
                                <a href="javascript:void(0);" class="read-message" data-value="0"
                                   data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-window-close-o text-secondary p-2" aria-hidden="true"></i>
                                </a>
                                <a href="javascript:void(0);" class="read-message" data-value="0"
                                   data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-check-square-o text-secondary p-2"></i>
                                </a>
                            @endif
                        </td>
                    @endif
                @endif

                @if (!in_array('Message Box', $dynamicColumnsToShowPostman))
                    <td class="message-input py-2 pl-3">
                        <div class=" cls_textarea_subbox">
                            <div class="btn-toolbar" role="toolbar">
                                <div class="pl-2 my-auto message-input-box-icons" role="group" aria-label="First group">
                                    @if($context != 'email')
                                        <div class="message-input-box">
                                            <textarea rows="1" class="form-control quick-message-field cls_quick_message addToAutoComplete" data-id="{{ $pam->cid }}" data-customer-id="{{ $pam->customer_id }}" name="message" id="message_{{$pam->cid}}" placeholder="Message"></textarea>
                                        </div>
                                        <button type="button" class="btn btn-sm m-0 p-0 mr-1">
                                            <input name="add_to_autocomplete" class="add_to_autocomplete" type="checkbox" value="true">
                                        </button>
                                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image send-message1" id="send-message_{{ $pam->cid }}" data-id="{{ $pam->cid }}"  data-customer-id="{{ !empty($pam->customer_id) ? $pam->customer_id : '' }}" data-email-id={{ !empty($pam->email_id) ? $pam->email_id : ''}}>
                                            <img src="/images/filled-sent.png">
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image send-message1" onclick="viewQuickEmailmpdel({{ $pam}})">
                                            <img src="/images/filled-sent.png">
                                        </button>
                                    @endif
                                    @if($pam->task_id > 0 )
                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="task" data-id="{{$pam->task_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                    <input type="hidden" name="is_audio" id="is_audio_{{$pam->cid}}" value="0" >
                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image btn-trigger-rvn-modal" data-id="{{$pam->cid}}" data-tid="{{$pam->task_id}}" data-load-type="text" data-all="1" title="Record & Send Voice Message"><img src="{{asset('images/record-voice-message.png')}}" alt=""></button>
                                    {{-- IG/FB Comment and DMS --}}
                                    @elseif(in_array($pam->message_type, ['FB_COMMENT', 'IG_COMMENT', 'IG_DMS', 'FB_DMS']))
                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="{{$pam->message_type}}" data-id="{{$pam->message_type_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                    {{-- IG/FB Comment and DMS --}}
                                    @elseif($pam->developer_task_id > 0 )
                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="developer_task" data-id="{{$pam->developer_task_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                    <input type="hidden" name="is_audio" id="is_audio_{{$pam->cid}}" value="0" >
                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image btn-trigger-rvn-modal" data-id="{{$pam->cid}}" data-tid="{{$pam->developer_task_id}}" data-load-type="text" data-all="1" title="Record & Send Voice Message"><img src="{{asset('images/record-voice-message.png')}}" alt=""></button>
                                    @elseif($pam->vendor_id > 0 )
                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="vendor" data-id="{{$pam->vendor_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>

                                    @elseif(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))

                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="email" data-id="{{$pam->email_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>

                                    @else
                                    <button   type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="customer" data-id="{{$pam->customer_id }}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                    <button   type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-object="customer" data-id="{{$pam->customer_id }}" data-attached="1" data-limit="10" data-load-type="images" data-all="1" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                                    @endif
                                    @if($pam->is_email==1 )
                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image editmessagebcc"  data-to_email="{{$pam->to_email}}" data-from_email="{{$pam->from_email}}" data-id="{{$pam->cid}}" data-cc_email="{{$pam->cc_email}}" data-all="1" title=""><i class="fa fa-edit"></i></button>
                                    @endif
                                    <div id="fa_microphone_slash_{{$pam->cid}}" style="display: none" ><i class="fa fa-microphone-slash" aria-hidden="true"></i></div>
                                    <button type="button" style="font-size: 16px" data-id="{{$pam->cid}}" class="btn btn-sm m-0 p-0 mr-1 speech-button"  id="speech-button_{{$pam->cid}}"><i class="fa fa-microphone" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                    </td>
                @endif

                @if (!in_array('From', $dynamicColumnsToShowPostman))
                    <td class="boat-replied">{{ $pam->reply_from }}</td>
                @endif

                @if (!in_array('Shortcuts', $dynamicColumnsToShowPostman))
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
                @endif

                <td>
                    {{ isset($isElastic) ? 'Yes' : 'No' }}
                </td>

                @if (!in_array('Action', $dynamicColumnsToShowPostman))
                    <td>
                        <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$pam->cid}}')"><i class="fa fa-arrow-down"></i></button>
                    </td>
                @endif
            </tr>
            <tr class="action-btn-tr-{{$pam->cid}} d-none">
                <td>Action</td>
                <td colspan="9">
                    <div class="actions">
                        <a href="javascript:void(0);" style="display: inline-block" class="resend-to-bot btns"
                           data-id="{{ $pam->cid }}">
                            <i style="color: #757575c7;" class="fa fa-refresh" title="Resend to bot" aria-hidden="true"></i>

                        </a>
                        <a href="javascript:void(0);" style="display: inline-block" class="approve_message  btns  pt-2"
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
                        <a href="javascript:void(0);" style="display: inline-block" class="approve-message btns " data-id="{{ !empty($pam->chat_id) ? $pam->chat_id : $pam->cid  }}">
            {{--                <img width="15px" height="15px" src="/images/completed.png">--}}
                            <i  style="color: #757575c7;" class="fa fa-check-square" aria-hidden="true"></i>

                        </a>
                        @endif
                        @if($pam->suggestion_id)
                            <a href="javascript:void(0);"  style="display: inline-block" class="add-more-images btns" data-id="{{ $pam->chat_id }}">
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
                            <a class="create-customer-ticket-modal btns pt-2" style="display: inline-block" href="javascript:void(0);" data-customer_id="{{$pam->customer_id}}" data-toggle="modal" data-target="#create-customer-ticket-modal" title="Create Ticket"><i style="color: #757575c7;" class="fa fa-ticket" aria-hidden="true"></i></a>
                        @endif

                        @if($pam->reply_from == "reminder")
                            @if($pam->task_id > 0 )
                                <a href="javascript:void(0);" data-id="{{$pam->task_id}}" data-type="task" class="pd-5 stop-reminder" >
                                    <i class="fa fa-bell background-grey" aria-hidden="true"></i>
                                </a>
                            @elseif($pam->developer_task_id > 0)
                                <a href="javascript:void(0);" data-id="{{$pam->developer_task_id}}" data-type="developer_task" class="pd-5 stop-reminder" >
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
            @else
            <tr class="customer-raw-line">
                @php
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

                <td data-context="{{ $context }}" data-url={{ route('whatsapp.send', ['context' => $context]) }} {{ $pam->taskUser ? 'data-chat-message-reply-id='.$pam->chat_bot_id : '' }}  data-chat-id="{{ $pam->chat_id }}" data-customer-id="{{$pam->customer_id ?? ( $pam->taskUser ? $issueID : '')}}" data-vendor-id="{{$pam->vendor_id}}" data-supplier-id="{{$pam->supplier_id}}" data-chatbot-id="{{$pam->chat_bot_id}}" data-email-id="{{$pam->email_id}}" data-page="{{ $pendingApprovalMsg->currentPage() }}">

                    @if($pam->supplier_id > 0)
                        @if (strlen($pam->supplier_name) > 5)
                       <p data-log_message="{{ $pam->supplier_name }}" class="user-inputt p-0 m-0" title="{{ $pam->supplier_name }}">{{  substr($pam->supplier_name,0,4)   }}..</p>
                        @else
                        <p class="p-0 m-0" title="{{ $pam->supplier_name }}">{{  /*"#".$pam->supplier_id." ".*/$pam->supplier_name  }}</p>
                        @endif

                        @elseif(in_array($pam->message_type, ['FB_COMMENT', 'IG_COMMENT']))
                        <p  title ="{{ $pam->socialComment->commented_by_user }}" class="p-0 m-0">{{ $pam->socialComment->commented_by_user  }}</p>
                    @elseif(in_array($pam->message_type, ['IG_DMS', 'FB_DMS']))
                        <p  title ="{{ $pam->socialContact->name }}" class="p-0 m-0">{{ $pam->socialContact->name  }}</p>
                    @else
                        @if (isset($pam->taskUser) && ( strlen($pam->taskUser->name) > 5) || strlen($pam->customer_name) > 5 || $pam->vendor_id > 0 && strlen($pam->vendors_name) > 5)
                            <p title="{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}"  data-log_message="{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}" class="user-inputt p-0 m-0">{{  ($pam->vendor_id > 0 ) ? substr($pam->vendors_name,0,6) : ( $pam->taskUser ? substr($pam->taskUser->name,0,4) : substr($pam->customer_name,0,4)  )  }}..</p>
                        @else

                            @if(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))
                                <p title="{{ $pam->from_name }}" class="p-0 m-0">{{ $pam->from_name }}</p>
                            @else
                                <p class="p-0 m-0" title="{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name  : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}">{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name  : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}</p>
                            @endif

                        @endif
                    @endif
                </td>

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
                    @elseif(!empty($pam->question) && strlen($pam->question) > 10)
                        <td   class="log-message-popup user-input" data-log_message="{!!$pam->question!!}">{{ substr($pam->question,0,15) }}...
                            @if($pam->chat_read_id == 1)
                                <a href="javascript:void(0);" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-check-square-o text-dark"></i>

                                </a>
                            @else
                                <a href="javascript:void(0);" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-check-square-o text-secondary"></i>

                                </a>
                            @endif
                        </td>
                    @elseif(!empty($pam->question) && strlen($pam->question) <= 10)
                        <td class="user-input" >{{ $pam->question }}
                            @if($pam->chat_read_id == 1)
                                <a href="javascript:void(0);" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-check-square-o text-dark"></i>

                                </a>
                            @else
                                <a href="javascript:void(0);" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-check-square-o text-secondary"></i>
                                </a>
                            @endif
                        </td>
                    @else
                    <td class="user-input" >
                        <span data-toggle="tooltip" data-placement="top" title="{{$pam->message}}"> 
                            {{ substr($pam->message, 0, 50) }}{{ strlen($pam->message) > 50 ? '...' : '' }} 
                            @if(strlen($pam->message) > 50) <a class="user-message" data-log_message="{{ $pam->message }}" style="color:#337ab7;">View more</a> @endif
                        </span>
                    </td>
                @endif

                @if($pam->answer_is_audio)
                    <td class="boat-replied"><audio controls="" src="{{ \App\Helpers::getAudioUrl($pam->answer) }}"></audio></td>
                @else
                    <td class="boat-replied expand-row" style="word-break: break-all">
                        <span class="td-mini-container">
                            {{ strlen($pam->answer) > 15 ? substr($pam->answer, 0, 15).'...' :  $pam->answer }}
                         </span>
                         <span class="td-full-container hidden">
                             {{ $pam->answer }}
                         </span>
                    </td>
                @endif

                @if (strlen($pam->suggested_reply) > 10)
                    <td data-log_message="{{ $pam->suggested_reply }}"
                        class="bot-suggested-reply-popup boat-replied">{{ substr( $pam->suggested_reply ,0,15) }}...

                    @if($pam->is_approved == false && $pam->is_reject == false)
                            <div class="suggested_reply_action d-inline">
                            <a href="javascript:void(0);" class="send_suggested_reply" data-value="0"
                               data-id="{{ $pam->tmp_replies_id }}">
                                <i class="fa fa-window-close-o text-secondary px-1 py-2" aria-hidden="true"></i>
                            </a>
                            <a href="javascript:void(0);" class="send_suggested_reply" data-value="1"
                               data-id="{{ $pam->tmp_replies_id }}">
                                <i class="fa fa-check-square-o text-secondary px-1 py-2"></i>
                            </a>
                            </div>
                    @endif
                    </td>
                @else
                    <td class="boat-replied">{{ $pam->suggested_reply }}
                        @if($pam->suggested_reply && $pam->is_approved == false && $pam->is_reject == false)
                            <a href="javascript:void(0);" class="read-message" data-value="0"
                               data-id="{{ $pam->chat_bot_id }}">
                                <i class="fa fa-window-close-o text-secondary p-2" aria-hidden="true"></i>
                            </a>
                            <a href="javascript:void(0);" class="read-message" data-value="0"
                               data-id="{{ $pam->chat_bot_id }}">
                                <i class="fa fa-check-square-o text-secondary p-2"></i>
                            </a>
                        @endif
                    </td>
                @endif

                <td class="message-input py-2 pl-3">
                    <div class=" cls_textarea_subbox">
                        <div class="btn-toolbar" role="toolbar">
                            <div class="pl-2 my-auto message-input-box-icons" role="group" aria-label="First group">
                                @if($context != 'email')
                                    <div class="message-input-box">
                                        <textarea rows="1" class="form-control quick-message-field cls_quick_message addToAutoComplete" data-id="{{ $pam->cid }}" data-customer-id="{{ $pam->customer_id }}" name="message" id="message_{{$pam->cid}}" placeholder="Message"></textarea>
                                    </div>
                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1">
                                        <input name="add_to_autocomplete" class="add_to_autocomplete" type="checkbox" value="true">
                                    </button>
                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image send-message1" id="send-message_{{ $pam->cid }}" data-id="{{ $pam->cid }}"  data-customer-id="{{ !empty($pam->customer_id) ? $pam->customer_id : '' }}" data-email-id={{ !empty($pam->email_id) ? $pam->email_id : ''}}>
                                        <img src="/images/filled-sent.png">
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image send-message1" onclick="viewQuickEmailmpdel({{ $pam}})">
                                        <img src="/images/filled-sent.png">
                                    </button>
                                @endif
                                @if($pam->task_id > 0 )
                                <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="task" data-id="{{$pam->task_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                <input type="hidden" name="is_audio" id="is_audio_{{$pam->cid}}" value="0" >
                                <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image btn-trigger-rvn-modal" data-id="{{$pam->cid}}" data-tid="{{$pam->task_id}}" data-load-type="text" data-all="1" title="Record & Send Voice Message"><img src="{{asset('images/record-voice-message.png')}}" alt=""></button>
                                {{-- IG/FB Comment and DMS --}}
                                @elseif(in_array($pam->message_type, ['FB_COMMENT', 'IG_COMMENT', 'IG_DMS', 'FB_DMS']))
                                <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="{{$pam->message_type}}" data-id="{{$pam->message_type_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                {{-- IG/FB Comment and DMS --}}
                                @elseif($pam->developer_task_id > 0 )
                                <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="developer_task" data-id="{{$pam->developer_task_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                <input type="hidden" name="is_audio" id="is_audio_{{$pam->cid}}" value="0" >
                                <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image btn-trigger-rvn-modal" data-id="{{$pam->cid}}" data-tid="{{$pam->developer_task_id}}" data-load-type="text" data-all="1" title="Record & Send Voice Message"><img src="{{asset('images/record-voice-message.png')}}" alt=""></button>
                                @elseif($pam->vendor_id > 0 )
                                <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="vendor" data-id="{{$pam->vendor_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>

                                @elseif(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))

                                <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="email" data-id="{{$pam->email_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>

                                @else
                                <button   type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="customer" data-id="{{$pam->customer_id }}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                <button   type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-object="customer" data-id="{{$pam->customer_id }}" data-attached="1" data-limit="10" data-load-type="images" data-all="1" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                                @endif
                                @if($pam->is_email==1 )
                                <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image editmessagebcc"  data-to_email="{{$pam->to_email}}" data-from_email="{{$pam->from_email}}" data-id="{{$pam->cid}}" data-cc_email="{{$pam->cc_email}}" data-all="1" title=""><i class="fa fa-edit"></i></button>
                                @endif
                                <div id="fa_microphone_slash_{{$pam->cid}}" style="display: none" ><i class="fa fa-microphone-slash" aria-hidden="true"></i></div>
                                <button type="button" style="font-size: 16px" data-id="{{$pam->cid}}" class="btn btn-sm m-0 p-0 mr-1 speech-button"  id="speech-button_{{$pam->cid}}"><i class="fa fa-microphone" aria-hidden="true"></i></button>
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
                    {{ isset($isElastic) ? 'Yes' : 'No' }}
                </td>

                <td>
                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$pam->cid}}')"><i class="fa fa-arrow-down"></i></button>
                </td>
            </tr>
            <tr class="action-btn-tr-{{$pam->cid}} d-none">
                <td>Action</td>
                <td colspan="9">
                    <div class="actions">
                        <a href="javascript:void(0);" style="display: inline-block" class="resend-to-bot btns"
                           data-id="{{ $pam->cid }}">
                            <i style="color: #757575c7;" class="fa fa-refresh" title="Resend to bot" aria-hidden="true"></i>

                        </a>
                        <a href="javascript:void(0);" style="display: inline-block" class="approve_message  btns  pt-2"
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
                        <a href="javascript:void(0);" style="display: inline-block" class="approve-message btns " data-id="{{ !empty($pam->chat_id) ? $pam->chat_id : $pam->cid  }}">
            {{--                <img width="15px" height="15px" src="/images/completed.png">--}}
                            <i  style="color: #757575c7;" class="fa fa-check-square" aria-hidden="true"></i>

                        </a>
                        @endif
                        @if($pam->suggestion_id)
                            <a href="javascript:void(0);"  style="display: inline-block" class="add-more-images btns" data-id="{{ $pam->chat_id }}">
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
                            <a class="create-customer-ticket-modal btns pt-2" style="display: inline-block" href="javascript:void(0);" data-customer_id="{{$pam->customer_id}}" data-toggle="modal" data-target="#create-customer-ticket-modal" title="Create Ticket"><i style="color: #757575c7;" class="fa fa-ticket" aria-hidden="true"></i></a>
                        @endif

                        @if($pam->reply_from == "reminder")
                            @if($pam->task_id > 0 )
                                <a href="javascript:void(0);" data-id="{{$pam->task_id}}" data-type="task" class="pd-5 stop-reminder" >
                                    <i class="fa fa-bell background-grey" aria-hidden="true"></i>
                                </a>
                            @elseif($pam->developer_task_id > 0)
                                <a href="javascript:void(0);" data-id="{{$pam->developer_task_id}}" data-type="developer_task" class="pd-5 stop-reminder" >
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
        @endif
    @endforeach
    @else
    <tr><td colspan="{{ !empty($dynamicColumnsToShowPostman) ? 11 - count($dynamicColumnsToShowPostman) : 11 ;}}" align="center"> Data Not Found</td> </tr>
    @endif
    </tbody>
    <tfoot>
    <tr>
        <td class="p-0" colspan="9">{{ $pendingApprovalMsg->links() }}</td>
    </tr>
    </tfoot>
</table>

</div>
<div id="approve-reply-popup" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route("chatbot.question.save"); }}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Create Intent</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="chat_message_id" value="{{ isset($pam) ? $pam->chat_id : null}}">
                    @include('chatbot::partial.form.value')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary form-save-btn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!--Log Messages Modal -->
<div id="logMessageModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">User Input</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>



<div id="botReply" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bot Replied</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>


<div id="botSuggestedReply" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bot Suggested Replied</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>



<div id="chatbotname" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Name</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>



<div id="website" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Website</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<div id="editmessagebcc" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Email/Message</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <form method="post" action="{{ route("chatbot.question.save"); }}">
                {{ csrf_field() }}

                <div class="modal-body">

                    <input type="hidden" name="chat_id"  id="chat_id">
                    <div class="form-group">
                            <label for="value">To</label>
                            <input type="email" name="to_email" id="to_email" class="form-control"  placeholder="Enter To Email" required>
                        </div>
                        <div class="form-group">
                            <label for="value">From</label>
                            <input type="email" name="from_email"  id="from_email" class="form-control"  placeholder="Enter from email" required>
                        </div>
                        <div class="form-group">
                            <label for="value">Cc</label>
                            <input type="email" name="cc_email"  id="cc_email" class="form-control"  placeholder="Enter cc">
                        </div>
                        <div class="form-group">
                            <label for="value">Message</label>
                            <input type="email" name="message1"  id="message1" class="form-control"  placeholder="Enter cc">
                        </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary form-edit-email-btn">Save changes</button>
                </div>
            </form>
            </div>

        </div>

    </div>
</div>

<div id="chat_bot_reply_list" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Simulator Replay</h4>
                <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
            </div>
            <div class="modal-body" style="background-color: #999999;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="chatbotUserMessage" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">User Message</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<div id="viewQuickEmail" class="modal" tabindex="-1" role="dialog" style="z-index: 99999;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View Email</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-6 d-inline form-inline">
                            <input style="width: 87%" type="text" name="category_name" placeholder="Enter New Category" class="form-control mb-3 quick_category">
                            <button class="btn btn-secondary quick_category_add_data" style="position: absolute;  margin-left: 8px;">+</button>
                        </div>
                        <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                            <div style="float: left; width: 86%">
                                <select name="quickCategory" class="form-control mb-3 quickCategoryData">
                                    <option value="">Select Category</option>

                                    @php
                                    $reply_categories = \App\ReplyCategory::select('id', 'name')->with('approval_leads')->orderby('name', 'ASC')->get();
                                    @endphp

                                    @foreach($reply_categories as $category)
                                        <option value="{{ $category->approval_leads }}" data-id="{{$category->id}}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="float: right; width: 14%;">
                                <a class="btn btn-image delete_category_data"><img src="/images/delete.png"></a>
                            </div>
                        </div>
                        <div class="col-6 d-inline form-inline">
                            <input style="width: 87%" type="text" name="quick_comment" placeholder="Enter New Quick Comment" class="form-control mb-3 quick_comment">
                            <button class="btn btn-secondary quick_comment_add_data" style="position: absolute;  margin-left: 8px;">+</button>
                        </div>
                        <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                            <div style="float: left; width: 86%">
                                <select name="quickComment" class="form-control quickCommentEmailData">
                                    <option value="">Quick Reply</option>
                                </select>
                            </div>
                            <div style="float: right; width: 14%;">
                                <a class="btn btn-image delete_quick_comment_data"><img src="/images/delete.png"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <p><strong>Subject : </strong><input type="text" id="email-subject" name="subject" class="form-control"></p>
                <p><strong>Body : </strong><textarea id="email-message" name="message" class="form-control reply-email-message"></textarea></p>
                </br>
                <span><strong>From : </strong> - <span id="quickemailFrom"></span></span><br>
                <span><strong>To : </strong> - <span id="quickemailTo"></span></span><br>
                <span><strong>CC : </strong> - <span id="quickemailCC"></span></span><br>

                <input type="hidden" id="receiver_email" name="receiver_email">
                <input type="hidden" id="sender_email_address" name="sender_email_address">
                <input type="hidden" id="cc_email" name="cc_email">
            </div>
            <div class="modal-footer" style=" width: 100%; display: inline-block;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-default submit-reply-in-email">Reply</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/js/simulator.js"></script>
<script type="text/javascript">
    var csrftoken = "{{ csrf_token() }}";
    $(document).on('click','.speech-button',function(e){
        const speechInput = document.querySelector('#message_'+ $(this).attr("data-id"));
        var recognition = new webkitSpeechRecognition()
        recognition.interimResults = true;

        /* start voice */
        recognition.start();
        if(speechInput){
            var microphoneSlash = document.getElementById('fa_microphone_slash_'+$(this).attr("data-id"));
            var microphone = document.getElementById('speech-button_'+$(this).attr("data-id"));
            microphone.style.display = "none";
            microphoneSlash.style.display = "block";
        }

        /* convert voice to text*/
        recognition.addEventListener('result', (event) => {
            speechInput.value = event.results[0][0].transcript;
        });

        /* stop voice */
        recognition.addEventListener('end', () => {
            recognition.stop();
            microphone.style.display = "block";
            microphoneSlash.style.display = "none";
        });
    })

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    $(document).on('click','.log-message-popup',function(){
        $('#logMessageModel p').text($(this).data('log_message'));
        $('#logMessageModel').modal('show');

    })
    $(document).on('click', '.user-message', function() {
            $('#chatbotUserMessage').modal('show');
            $('#chatbotUserMessage p').text($(this).data('log_message'));
    })
    $(document).on('click','.editmessagebcc',function(){
        $('#chat_id').val($(this).data('id'));
        $('#from_email').val($(this).data('from_email'));
        $('#to_email').val($(this).data('to_email'));
        $('#cc_email').val($(this).data('cc_email'));

        var message = $(this).closest(".cls_textarea_subbox").find("textarea").val();
        $('#message1').val(message);
        $('#editmessagebcc').modal('show');

    })

    $(document).on("click",".form-edit-email-btn",function () {
        let chatID =  $('#chat_id').val();
        let fromemail=$('#from_email').val();
        let toemail=$('#to_email').val();
        let ccemail=$('#cc_email').val();
        $('#message_'+chatID).val($('#message1').val());
         $.ajax({
            type: "GET",
            url: "{{url('/chatbot/messages/update-emailaddress')}}",
            data: {
                chat_id : chatID,
                fromemail:fromemail,
                toemail:toemail,
                ccemail:ccemail

            },
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                    $('#send-message_'+chatID).trigger('click');
                    $('#editmessagebcc').modal('hide');
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Record not Update successfully!');
            }
        });
    });



    $(document).on('click','.bot-reply-popup',function(){
        $('#botReply').modal('show');
        $('#botReply p').text($(this).data('log_message'));
    })

    $(document).on('click','.bot-suggested-reply-popup',function(){
        $('#botSuggestedReply').modal('show');
        $('#botSuggestedReply p').text($(this).data('log_message'));
    })

    $(document).on('click','.user-inputt',function(){
        $('#chatbotname').modal('show');
        $('#chatbotname p').text($(this).data('log_message'));
    })
    $(document).on('click','.log-website-popup',function(){
        $('#website').modal('show');
        $('#website p').text($(this).data('log_message'));
    })



    $(".approve_message").on("click", function () {
        var $this = $(this);
        $("#approve-reply-popup").modal("show");
        $('.user-input').text();
        $('#approve-reply-popup [name="question[]"').val($this.closest("tr").find('.user-input').text())
    });
    $('#entity_details').hide();
    $('#erp_details').hide();

    $(".form-save-btn").on("click",function(e) {
        e.preventDefault();

        var form = $(this).closest("form");
        $.ajax({
            type: form.attr("method"),
            url: form.attr("action"),
            data: form.serialize(),
            dataType : "json",
            success: function (response) {
               //location.reload();
                if(response.code == 200) {
                    toastr['success']('data updated successfully!');
                    window.location.replace(response.redirect);
                }else{
                    if(response.error != "") {
                        var message = ``;
                        $.each(response.error,function(k,v) {
                            message += v+`<br>`;
                        });
                        toastr['error'](message);
                    }else{
                        errorMessage = response.error ? response.error : 'data is not correct or duplicate!';
                        toastr['error'](errorMessage);
                    }
                }
            },
            error: function () {
                toastr['error']('Could not change module!');
            }
        });
    });

    $(document).on("click",".resend-to-bot",function () {
        let chatID = $(this).data("id");
         $.ajax({
            type: "GET",
            url: "/chatbot/messages/resend-to-bot",
            data: {
                chat_id : chatID

            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(document).on("click",".read-message",function () {
        let chatID = $(this).data("id");
        let value = $(this).data("value");
        var $this = $(this);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "/chatbot/messages/update-read-status",
            data: {
                chat_id : chatID,
                value  : value
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                    if(value == 1) {
                        $this.html('<img width="15px" title="Mark as unread" height="15px" src="/images/completed-green.png">');
                        $this.data("value",0);
                    }else{
                        $this.html('<img width="15px" title="Mark as read" height="15px" src="/images/completed.png">');
                        $this.data("value",1);
                    }
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(document).on("click",".send_suggested_reply",function () {
        let tmpReplayId = $(this).data("id");
        let value = $(this).data("value");
        var $this = $(this);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('chatbot.send.suggested.message') }}",
            data: {
                tmp_reply_id : tmpReplayId,
                value  : value
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                    if(value == 1) {
                        $this.remove();
                    }else{
                        $this.remove();
                    }
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Suggested replay not found');
            }
        });
    });

    $(document).on('click', '.do_not_disturb', function() {
        var id = $(this).data('id');
        var thiss = $(this);
        $.ajax({
            type: "POST",
            url: "{{ url('customer') }}/" + id + '/updateDND',
            data: {
                _token: "{{ csrf_token() }}",
                // do_not_disturb: option
            },
            beforeSend: function() {
                //$(thiss).text('DND...');
            }
        }).done(function(response) {
          if (response.do_not_disturb == 1) {
            var img_url = "/images/do-not-disturb.png";
            $(thiss).html('<img src="'+img_url+'" />');
          } else {
            var img_url = "/images/do-disturb.png";
            $(thiss).html('<img src="'+img_url+'" />');
          }
        }).fail(function(response) {
          alert('Could not update DND status');
        });
  });

    $(document).on("click",".stop-reminder",function() {
        var id = $(this).data("id");
        var type = $(this).data("type");

        $.ajax({
            type: "GET",
            url: "/chatbot/messages/stop-reminder",
            data: {
                _token: "{{ csrf_token() }}",
                id : id,
                type : type
                // do_not_disturb: option
            },
            beforeSend: function() {
                //$(thiss).text('DND...');
            },
            dataType : "json"
        }).done(function(response) {
            if(response.code == 200) {
                toastr['success'](response.messages);
            }else{
                toastr['error'](response.messages);
            }
        }).fail(function(response) {
          toastr['error']('Could not update DND status');
        });
    });


    $(document).on("click", ".show_message_list", function () {
        $('#chat_bot_reply_list').modal('show');
        var thiss = $(this);
        var object_type = $(this).data("object");
        var object_id = $(this).data("id");
        var load_attached = $(this).data("attached");
        var load_all = $(this).data("all");
        load_type = $(this).data("load-type");
        is_admin = $(this).data("is_admin");
        var is_hod_crm = $(this).data("is_hod_crm");
        var limit = 20;
        if (typeof $(this).data("limit") != "undefined") {
            limit = $(this).data("limit");
        }

        var base_url = BASE_URL;
        // var base_url ="http://localhost:8000";
        thiss.parent().find(".td-full-container").toggleClass("hidden");
        currentChatParams.url =
            base_url +
            "/message-list/" +
            object_type +
            "/" +
            object_id;
        currentChatParams.data = {
            limit: limit,
            load_all: load_all,
            load_attached: load_attached,
            load_type: load_type,
            page: 1,
            hasMore: true,
            object_name: object_type,
            object_val: object_id,
        };

        $.ajax({
            type: "GET",
            url: "{{ route('chatbot.message.list') }}" + "/"  + object_type + "/" + object_id,
            data: {
                limit: limit,
                load_all: load_all,
                load_attached: load_attached,
                load_type: load_type,
            },
            beforeSend: function () {
                $(thiss).text("Loading...");
                $(thiss).html("");
                $(thiss).html(
                    '<i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i><div class="spinner-border" role="status"><span class="">Loading...</span></div>'
                );
            },
        })
            .done(function (response) {
                $(".spinner-border").css("display", "none");
                var li = getHtml(response, 'simulator-message-list');
                if ($("#chat_bot_reply_list").length > 0) {
                    $("#chat_bot_reply_list")
                        .find(".modal-dialog")
                        .css({ width: "1000px", "max-width": "1000px" });
                    $("#chat_bot_reply_list")
                        .find(".modal-body")
                        .css({ "background-color": "white" });
                    $("#chat_bot_reply_list").find(".modal-body").html(li);
                    $("#chat_bot_reply_list").find("#chat_obj_type").val(object_type);
                    $("#chat_bot_reply_list").find("#chat_obj_id").val(object_id);
                    $("#chat_bot_reply_list")
                        .find(".message")
                        .css({ "white-space": "pre-wrap", "word-wrap": "break-word" });
                    $("#chat_bot_reply_list").modal("show");
                } else {
                    $("#chat_bot_reply_list")
                        .find(".modal-dialog")
                        .css({ width: "1000px", "max-width": "1000px" });
                    $("#chat_bot_reply_list")
                        .find(".modal-body")
                        .css({ "background-color": "white" });
                    $("#chat_bot_reply_list").find("#chat_obj_type").val(object_type);
                    $("#chat_bot_reply_list").find("#chat_obj_id").val(object_id);
                    $("#chat-history").html(li);
                }

                var searchterm = $(".search_chat_pop").val();
                if (searchterm && searchterm != "") {
                    var value = searchterm.toLowerCase();
                    $(".filter-message").each(function () {
                        if ($(this).text().search(new RegExp(value, "i")) < 0) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    });
                }
            })
            .fail(function (response) {
                //$(thiss).text('Load More');
                $(".spinner-border").css("display", "none");
                toastr["error"]("Could not load messages", "error");
            });
    });
    
    function Showactionbtn(id){
      $(".action-btn-tr-"+id).toggleClass('d-none')
      $("#asset_user_name").select2('destroy');
    }
    $(function(){
            $("#email-message").summernote({
                height: 300, // Set the height of the editor
                placeholder: "Write your content here...", // Placeholder text
                toolbar: [
                    ["style", ["style"]],
                    ["font", ["bold", "italic", "underline", "clear"]],
                    ["fontname", ["fontname"]],
                    ["fontsize", ["fontsize"]],
                    ["color", ["color"]],
                    ["para", ["ul", "ol", "paragraph"]],
                    ["height", ["height"]],
                    ["insert", ["link", "picture", "video"]],
                    ["view", ["fullscreen", "codeview"]],
                    ["help", ["help"]]
                ]
            });

            $(document).on('click', '.quick_category_add_data', function () {
                siteHelpers.quickCategoryAddData($(this));
            });
            $(document).on('click', '.delete_category_data', function () {
                siteHelpers.deleteQuickCategoryData($(this));
            });
            $(document).on('click', '.delete_quick_comment_data', function () {
                siteHelpers.deleteQuickCommentData($(this));
            });
            $(document).on('click', '.quick_comment_add_data', function () {
                siteHelpers.quickCommentAddData($(this));
            });
            $(document).on('change', '.quickCategoryData', function () {
                siteHelpers.changeQuickCategoryData($(this));
            });
            $(document).on('change', '.quickCommentEmailData', function () {
                siteHelpers.changeQuickCommentData($(this));
            });

            var siteHelpers = {
            quickCategoryAddData : function(ele) {
                var textBox = ele.closest("div").find(".quick_category");
                if (textBox.val() == "") {
                    toastr["error"]("Please Enter Category1!!", "error");
                    return false;
                }
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        name : textBox.val()
                    },
                    url: "/add-reply-category"
                };
                siteHelpers.sendAjax(params,"afterQuickCategoryAddData");
            },
            afterQuickCategoryAddData : function(response) {
                $(".quick_category").val('');
                $(".quickCategoryData").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
            },
            deleteQuickCategoryData : function(ele) {
              const quickCategory = ele.closest("#viewQuickEmail").find(".quickCategory");
              if (quickCategory.val() === "") {
                    toastr["error"]("Please Select Category!!", "error");
                    return false;
                }
                var quickCategoryId = quickCategory.children("option:selected").data('id');
                if (!confirm("Are sure you want to delete category?")) {
                    return false;
                }
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        id : quickCategoryId
                    },
                    url: "/destroy-reply-category"
                };
                siteHelpers.sendAjax(params,"pageReload");
            },
            deleteQuickCommentData : function(ele) {
                var quickComment = ele.closest("#viewQuickEmail").find(".quickCommentEmailData");
                if (quickComment.val() == "") {
                    toastr["error"]("Please Select Quick Comment!!", "error");
                    return false;
                }
                var quickCommentId = quickComment.children("option:selected").data('id');
                if (!confirm("Are sure you want to delete comment?")) {
                    return false;
                }
                var params = {
                    method : 'DELETE',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/reply/" + quickCommentId,
                };
                siteHelpers.sendAjax(params,"pageReload");
            },
            pageReload : function(response) {
                location.reload();
            },
            quickCommentAddData : function(ele) {
                var textBox = ele.closest("div").find(".quick_comment");
                var quickCategory = ele.closest("#viewQuickEmail").find(".quickCategoryData");
                if (textBox.val() == "") {
                    toastr["error"]("Please Enter New Quick Comment!!", "error");
                    return false;
                }
                if (quickCategory.val() == "") {
                    toastr["error"]("Please Select Category!!", "error");
                    return false;
                }
                var quickCategoryId = quickCategory.children("option:selected").data('id');
                var formData = new FormData();
                formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
                formData.append("reply", textBox.val());
                formData.append("category_id", quickCategoryId);
                formData.append("model", 'Approval Lead');
                formData.append("type", 'with-extra-attributes');
                var params = {
                    method : 'post',
                    data : formData,
                    url: "/reply"
                };
                siteHelpers.sendFormDataAjax(params,"afterQuickCommentAddData");
            },
            afterQuickCommentAddData : function(reply) {
                let selectedOption = $('.quickCategoryData option:selected');
                let selectedOptionId = selectedOption.data('id');
                let data = JSON.parse(selectedOption.val());
                data.push({
                    id : reply.id,
                    category_id: selectedOptionId,
                    reply : reply.reply,
                    model: 'Approval Lead',
                });
                selectedOption.val(JSON.stringify(data));
                $(".quick_comment").val('');
                $('.quickCommentEmailData').append($('<option>', {
                    value: reply.reply,
                    text: reply.reply,
                    'data-id': reply.id,
                }));
            },
            changeQuickCategoryData : function (ele) {
                if (ele.val() != "") {
                    var replies = JSON.parse(ele.val());
                    ele.closest("#viewQuickEmail").find('.quickCommentEmailData').empty();
                    ele.closest("#viewQuickEmail").find('.quickCommentEmailData').append($('<option>', {
                        value: '',
                        text: 'Quick Reply'
                    }));
                    replies.forEach(function (reply) {
                        ele.closest("#viewQuickEmail").find('.quickCommentEmailData').append($('<option>', {
                            value: reply.reply,
                            text: reply.reply,
                            'data-id': reply.id
                        }));
                    });
                }
            },
            changeQuickCommentData : function (ele) {
                var message = ele.closest('#viewQuickEmail').find('#email-message').val();

                var val = ele.val();
                ele.closest('#viewQuickEmail').find('#email-message').summernote('code',message + '<p>' + val + '</p>');
            }
        };
        $.extend(siteHelpers, common);
            $('.submit-reply-in-email').click(function(){
                $('.submit-reply-in-email').prop('disabled', true);
                var data = {
                    subject : $('#viewQuickEmail #email-subject').val(),
                    message : $('#viewQuickEmail #email-message').val(),
                    receiver_email : $('#viewQuickEmail #receiver_email').val(),
                    sender_email_address : $('#viewQuickEmail #sender_email_address').val(),
                    cc_email : $('#viewQuickEmail #cc_email').val(),
                };

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("message.send-mail-reply") }}',
                    type: 'POST',
                    data: data,
                }).done( function(response) {
                    $('.submit-reply-in-email').prop('disabled', false);
                    if (response.errors) {
                        toastr['error'](response.errors);
                    } else {
                        toastr['success'](response.message);
                    }
                }).fail(function(errObj) {
                    toastr['error'](errObj);
                });

            });
        });
		
		  function viewQuickEmailmpdel(data){
            $('#viewQuickEmail #quickemailFrom').text(data.from_email);
            $('#viewQuickEmail #quickemailTo').text(data.to_email);
            $('#viewQuickEmail #quickemailCC').text(data.cc_email);
            $('#viewQuickEmail #receiver_email').val(data.to_email);
            $('#viewQuickEmail #email-message').val(data.message);
            $('#viewQuickEmail #email-message').summernote('code',data.message);
            $('#viewQuickEmail #sender_email_address').val(data.from_email);
            $('#viewQuickEmail').modal('show');
        }
</script>
