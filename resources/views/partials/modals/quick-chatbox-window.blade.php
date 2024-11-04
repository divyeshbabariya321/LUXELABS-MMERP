<!-- Modal -->
<style>
    #quick-chatbox-window-modal .card_chat {
        border-radius: 8px !important;
    }

    #quick-chatbox-window-modal .contacts_body {
        padding: 0 !important;
    }

    #quick-chatbox-window-modal .contacts li {
        margin: 0 !important;
        padding: 10px !important;
    }

    #quick-chatbox-window-modal .card-footer {
        border-radius: 0 !important;
        /*background: transparent !important;*/
    }

    #quick-chatbox-window-modal .chat li:last-child {
        border-bottom: none !important;
    }

    #quick-chatbox-window-modal .chat-righbox {
        padding: 11px 17px 4px;
        margin-bottom: 10px;
    }

    #quick-chatbox-window-modal .chat-righbox .title {
        font-size: 17px;
        font-weight: 400;
    }

    #quick-chatbox-window-modal h5 {
        margin-top: 0 !important;
    }

    #customer_order_details {
        padding: 10px 0 !important;
    }

    #quick-chatbox-window-modal .card {
        margin-bottom: 1rem;
    }

    #quick-chatbox-window-modal .card-header {
        padding: 0.5rem 1.25rem;
        border-bottom: 1px solid #ddd !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        text-align: left;
    }

    .msg_time {
        font-size: 9px;
        color: #757575;
    }

    .chat-rightbox.mt-4 {
        display: flex;
    }

    .chat-rightbox.mt-4 button {
        margin-left: 10px;
    }

    #quick-chatbox-window-modal .btn-link {
        color: gray;
    }

    .remove-bottom-scroll .user_inital {
        height: 35px;
        width: 35px;
        line-height: 35px;
        margin-top: 4px;
    }

    .button-round {
        border-radius: 50% !important;
        background-color: rgba(0, 0, 0, 0.3) !important;
        border: 0 !important;
        color: white !important;
        cursor: pointer;
        width: 25px;
        height: 25px;
        margin-bottom: 5px;
        margin-left: 7px;
    }

    #quick-chatbox-window-modal .button-round i {
        font-size: 15px;
    }

    #quick-chatbox-window-modal .card-footer {
        /* padding:8px 5px !important; */
        padding: 2px 5px !important;
    }

    .selectedValue {
        flex-grow: 1;
        text-align: right;
    }

    #autoTranslate {
        width: 220px !important;
        justify-content: flex-end;
        margin: 0 0 0 auto;
    }

    #quick-chatbox-window-modal .card-header.msg_head {
        background: #f1f1f1;
    }

    #quick-chatbox-window-modal .msg_card_body {
        background: #ffffff;
    }

    #quick-chatbox-window-modal .typing-indicator {
        display: none;
    }

    #quick-chatbox-window-modal .card-header {
        border-radius: 9px 9px 0 0 !important;
    }

    .io.action {
        padding-left: 10px;
        display: flex;
        align-items: center;
    }

    .video_cam {
        margin-left: 0;
        display: flex;
        align-items: center;
    }

    .video_cam i {
        color: #757575;
    }

    .video_cam span {
        margin-right: 10px;
    }

    .selectedValue select.globalSelect2+span.select2 {
        width: 200px !important;
    }
    .count-highlight {
        background-color: yellow;
    }
</style>
<div id="quick-chatbox-window-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width:90%; max-width: 90%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3 chat pr-0" style="margin-top : 0px !important;">
                        <div class="card_chat mb-sm-3 mb-md-0 contacts_card" style="height:680px !important;">
                            <div class="card-header">
                                <h3>Chats</h3>
                            </div>
                            <div class="card-body contacts_body">
                                @php
                                    $chatIds = $customerLiveChatIds;
                                @endphp

                                <ul class="contacts" id="customer-list-chat">
                                    @if(count($chatIds) > 0)
                                        @foreach ($chatIds as $chatId)
                                            @php $customer = $chatId->customer; @endphp
                                            @if(!empty($customer))
                                                @php
                                                    $customerInital = substr($customer->name, 0, 1);
                                                    $websiteName = (isset($websiteData[$customer->store_website_id]) ? $websiteData[$customer->store_website_id] : '');
                                                @endphp

                                                <input type="hidden" id="live_selected_customer_store" value="{{ $customer->store_website_id }}" />
                                                <li onclick="getChats('{{ $customer->id }}')" id="user{{ $customer->id }}" style="cursor: pointer;">

                                                    <div class="d-flex bd-highlight">
                                                        <div class="img_cont">
                                                            <span class="online_icon @if($chatId->status == 0) offline @endif "></span>
                                                        </div>
                                                        <div class="user_info">
                                                            <span>{{ $customer->name }}</span>
                                                            <h5>{{ $customer->phone ?? '' }} </h5>
                                                            <!-- <p>{{ $customer->name }} is @if($chatId->status == 0) offline @else online @endif </p> -->
                                                            <h5>{{ $websiteName ?? '' }} </h5>
                                                        </div>
                                                        <!-- @if($chatId->seen == 0)<span class="new_message_icon"></span>@endif -->
                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                            <div class="card-footer"></div>
                        </div>
                    </div>
                    <div class="col-md-6 chat">
                        <div class="card_chat">
                            <div class="card-header msg_head" style="display: flex">
                                <div class="d-flex bd-highlight align-items-center " style="flex-grow: 1">
                                    <div class="img_cont">
                                        <soan class="rounded-circle user_inital" id="user_inital"></soan>
                                    </div>
                                    <div class="user_info" id="user_name"></div>
                                    @php
                                    $path = storage_path('/');
                                    $content = File::get($path."languages.json");
                                    $language = json_decode($content, true);
                                    @endphp
                                    <div class="selectedValue">
                                        <select id="autoTranslate" class="form-control auto-translate globalSelect2">
                                            <option value="">Translation Language</option>
                                            @foreach ($language as $key => $value)
                                            <option value="{{$value}}">{{$key}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <button style="padding-right:2px;" type="button" class="btn rt btn-image load-communication-modal" data-is_admin="1" data-is_hod_crm="1" data-object="customer" data-id="{{ @$customer->id }}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>

                                <div class="video_cam">
                                    <span><i class="fa fa-video"></i></span>
                                    <span><i class="fa fa-phone"></i></span>
                                </div>
                                <span class="io action" id="action_menu_btn "><i style="font-size: 17px" class="fa fa-ellipsis-v"></i></span>
                                <div class="action_menu">
                                </div>
                            </div>
                            <div class="card-body msg_card_body" id="message-recieve">

                            </div>
                            <div class="typing-indicator" id="typing-indicator"></div>
                            <div class="card-footer">
                                <div class="input-group">
                                    <div class="card-footer">
                                        <div class="input-group">

                                            <input type="hidden" id="message-id" name="message-id" />

                                            <span style="display: flex">
                                                <div style="flex-grow: 1">
                                                    <textarea name="" class="form-control type_msg" placeholder="Type your message..." id="message"></textarea>

                                                </div>
                                                <div>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text attach_btn button-round" onclick="sendImage()"><i class="fa fa-paperclip"></i></span>
                                                        <input type="file" id="imgupload" style="display:none" />
                                                    </div>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text send_btn button-round" onclick="sendMessage()"><i class="fa fa-location-arrow"></i></span>
                                                    </div>
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="rows">
                                    <div class="col-md-4">
                                        <div class="chat-rightbox">
                                            <select class="form-control globalSelect2" id="live_quick_replies">
                                                <option value="">Quick Reply</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="chat-rightbox">
                                            <select class="form-control globalSelect2" id="keyword_category">
                                                <option value="">Select Category</option>
                                                @if(isset($allReplyCategories))
                                                @foreach ($allReplyCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="chat-rightbox" style="display:flex;">
                                            <input type="text" name="quick_comment_live" placeholder="New Quick Comment" class="form-control quick_comment_live">
                                            <span><button class="btn btn-secondary quick_comment_add_live ml-2" style="height:34px!important;">+</button></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card-body " id="customer_order_details"></div>
                    </div>
                    <div class="col-md-3 customer-info pl-0">

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="">
                                <thead>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>General Info</th>
                                        <td id="chatCustomerInfo">
                                            </th>
                                    </tr>
                                    <tr>
                                        <td>Visited Pages</th>
                                        <td id="chatVisitedPages">
                                            </th>
                                    </tr>
                                    <tr>
                                        <td>Additional info</th>
                                        <td id="chatAdditionalInfo">
                                            </th>
                                    </tr>
                                    <tr>
                                        <td>Technology</th>
                                        <td id="chatTechnology">
                                            </th>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="chat-list-history" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Communication</h4>
                <input type="text" name="search_chat_pop" class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                <span id="total-count" class="count-highlight"></span>
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
