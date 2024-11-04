<?php
$srno = 1;
?>
@if(isset($chatIds) && !empty($chatIds))
    @foreach ($chatIds as $chatId)
        @php
            $customer = $chatId->customer;
            if(!$customer) {
                \Log::info("Need to delete chat id for customer #".$chatId->customer_id);
                continue;
            }
            $customerInital = substr($customer->name, 0, 1);
        @endphp
        <tr>
            <td><?php echo $srno; ?></td>
            <td><?php echo $chatId->website; ?></td>
            <td>{{ $chatId->created_at->isoFormat('D-M-YYYY h:mm A') }}</td>
            <td><?php echo $customer->name; ?></td>
            <td class="expand-row">
                                        <span class="td-mini-container">
                                          {{ strlen($customer->email) > 15 ? substr($customer->email, 0, 15) : $customer->email }}
                                        </span>
                <span class="td-full-container hidden">
                                          {{ $customer->email }}
                                        </span>
            </td>
            <td><?php echo $customer->phone; ?></td>

            @php
                $path = storage_path('/');
                $content = File::get($path."languages.json");
                $language = json_decode($content, true);
                $lang='';
            @endphp

            <td>
                <div class="selectedValue">
                    <select id="autoTranslate" style="width: 100% !important" class="form-control auto-translate">
                        <option value="">Translation Language</option>
                        @foreach ($language as $key => $value)

                            @if($value==$customer->language)
                                @php $lang=$key; @endphp
                            @endif

                            <option value="{{$value}}">{{$key}}</option>
                        @endforeach
                    </select>
                </div>
            </td>

            <td>{{$lang??''}}</td>

            <td class="cls_remove ">
                @php
                    $chat_last_message = (!empty($customer->lastMessages) && count($customer->lastMessages) > 0) ? $customer->lastMessages[0] : null;
                @endphp

                @if(!empty($chat_last_message))
                    <div class="typing-indicator" id="typing-indicator">{{isset($chat_last_message)?$chat_last_message->message:''}}</div>
                @endif

                <div class="row quick margin-left-right-set">
                    <div class="cls_remove_rightpadding">
                        <textarea name="" class="form-control type_msg message_textarea cls_message_textarea" placeholder="Type your message..." id="message" rows="1" style="height:auto !important"></textarea>
                        <input type="hidden" id="message-id" name="message-id" />
                    </div>

                </div>
                <div class="row quick margin-left-right-set">

                    <div class="col-md-2">
                        <div class="input-group-append">
                            <button   type="button" class="btn btn-xs btn-image load-communication-modal conversation-modal  load-body-class" data-is_admin="1" data-is_hod_crm="1" data-object="customer" data-id="{{ @$customer->id }}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>

                            <a href="/attachImages/live-chat/{{ @$customer->id }}" class="ml-2 mt-2 mr-2 btn-xs text-dark"><i class="fa fa-paperclip"></i></a>
                            <a class="mt-2 btn-xs text-dark send_msg_btn" href="javascript:;" data-id="{{ @$customer->id }}"><i class="fa fa-location-arrow"></i></a>
                        </div>
                    </div>
                </div>

                

            </td>

            <td class="">

                <div class="row cls_quick_reply_box margin-left-right-set mt-0">

                    <div class="col-md-6 cls_remove_rightpadding pl-3">
                        <select class="form-control auto-translate" id="categories">
                            <option value="">Select Category</option>
                            @if(isset($all_categories))
                                @foreach ($all_categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6 pl-3">
                        <div class="row margin-left-right-set">
                            <div class="cls_remove_rightpadding ">
                                <select class="form-control" id="quick_replies">
                                    <option value="">Quick Reply</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Livechatbtn('{{$chatId->id}}')"><i class="fa fa-arrow-down"></i></button>
            </td>
        </tr>

        <tr class="action-livebtn-tr-{{$chatId->id}} d-none">
            <td class="font-weight-bold">Action</td>
            <td colspan="10">
                <div >
                    <a href="javascript:;" class="mt-1 mr-1 btn-xs text-dark" title="General Info" onclick="openPopupGeneralInfo(<?php echo $chatId->id; ?>)" >
                        <i class="fa fa-info" aria-hidden="true"></i>
                    </a>
                    <a href="javascript:;" class="mt-1 mr-1 btn-xs text-dark" title="Visited Pages" onclick="openPopupVisitedPages(<?php echo $chatId->id; ?>)" >
                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                    </a>
                    <a href="javascript:;" class="mt-1 mr-1 btn-xs text-dark" class="btn cls_addition_info" title="Additional info" onclick="openPopupAdditionalinfo(<?php echo $chatId->id; ?>)" >
                        <i class="fa fa-clipboard"></i>
                    </a>
                    <a href="javascript:;" class="mt-1 mr-1 btn-xs text-dark" title="Technology" onclick="openPopupTechnology(<?php echo $chatId->id; ?>)" >
                        <i class="fa fa-lightbulb-o" aria-hidden="true"></i>
                    </a>

                    <a href="javascript:;" class="mt-1 mr-1 btn-xs text-dark" title="Chat Logs" onclick="openChatLogs(<?php echo $chatId->id; ?>)" >
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                    </a>

                    <a href="javascript:;" class="mt-1 mr-1 btn-xs text-dark" title="Chat Logs Event" onclick="openChatEventLogs('<?php echo $chatId->thread; ?>')" >

                        <i class="fa fa-history" aria-hidden="true"></i>
                    </a>
                    <button type="button" class="btn btn-image send-coupon p-1" data-toggle="modal" data-id="{{ $chatId->id }}" data-customerid="{{ $customer->id }}" ><i class="fa fa-envelope"></i></button>
                </div>

            </td>
        </tr>


        <?php $srno++; ?>
    @endforeach
@endif

@section('scripts')
    <script>
        function Livechatbtn(id){
            $(".action-livebtn-tr-"+id).toggleClass('d-none')
        }
    </script>
@endsection
