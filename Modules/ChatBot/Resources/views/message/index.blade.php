@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Message List  | Chatbot')

@section('content')

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/dialog-node-editor.css">
    <style type="text/css">
        .panel-img-shorts {
            width: 80px;
            height: 80px;
            display: inline-block;
        }

        .panel-img-shorts .remove-img {
            display: block;
            float: right;
            width: 15px;
            height: 15px;
        }
        form.chatbot .col{
            flex-grow: unset !important;
        }
        .chatbot-form-1{
            justify-content: space-between;
        }
        table th:not(.modal-dialog table th), table td:not(.modal-dialog table td){
            white-space: nowrap;
        }
        .message-input-box{
            width: 70%;
        }
        .message-input-box-icons{
            width: 30%;
        }
        .chatboat-message-status {
            width: 130px;
        }
        .chatboat-message-type{
            width: 200px;
        }
        /* / Responsive styles / */
        /* / // X-Small devices (portrait phones, less than 576px) / */
        @media (max-width: 575.98px) { 
            form.chatbot {
                padding: 0 12px;
                margin: 0 !important;
            }
            .chatboat-message-type {
                width: 100% !important;
            }
            form.chatbot .chatbot-form-1 .col{
                flex-grow: 1 !important;
                padding: 0;
            }
            .chatbot-form-1 > div{
                width: 100%;
            }
            .chatboat-message-status {
                width: 100%;
            }
            .chatboat-message-type{
                width: 100%;
            }
            .message-input-box{
                width: 150px !important;
            } 
            .message-input-box-icons{
                width: 100% !important;
            }
            .chatbot-form-1 button[type=submit]{
                padding: 0;
            }
            .chatbot-send-field{
                width: 100%;
            }
            .chatbot-send-field .select2.select2-container.select2-container--default{
                width: 100% !important;

            }
        }

        /* / // Small devices (landscape phones, 576px and up) / */
        @media (min-width: 576px) and (max-width: 767.98px) { 
            .message-input-box{
                width: 150px !important;
            } 
            .message-input-box-icons{
                width: 100% !important;
            }
        }

        /* / // Medium devices (tablets, 768px and up) / */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .message-input-box{
                width: 150px !important;
            } 
            .message-input-box-icons{
                width: 100% !important;
            }
        }

        /* / // Large devices (desktops, 992px and up) / */
        @media (min-width: 992px) and (max-width: 1199.98px) { 
            .message-input-box, .message-input-box-icons{
                width: 100% !important;
            }
        }

        /* / // Extra large devices (large desktops, 1200px and up) / */
        @media (min-width: 1200px) and (max-width: 1400.98px) {
        
        }

        /* / // For 2k Monitors, (more than 1401 px) / */
        @media(max-width: 1599.98px) {
            .chatbot-form-1{
                justify-content: start;
                gap: 6px;
            }
        }

        @media (min-width: 1600px) and (max-width: 2559.98px) {
        
        }

        @media (min-width: 2560px) {

        }
    </style>
    <div class="row m-0">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">Message List | Chatbot</h2>
        </div>
    </div>

    <div class="row m-0">
        <div class="col-lg-12 margin-tb pl-3 pr-3" style="margin-bottom: 10px;">
            <div class="">
                <div class="form-inline">
                    <form method="get" class="chatbot mr-3 w-100">
                        <div class="row chatbot-form-1" >
                            <div class="col pr-0">
                                {{ html()->text('search', request('search', null))->class('form-control')->placeholder('Enter input here..') }}
                            </div>
                            <div class="">
                                <select name="status" class="chatboat-message-status form-control">
                                    <option value="">Select Status</option>
                                    <option value="1" {{request()->get('status') == '1' ? 'selected' : ''}}>
                                        Approved
                                    </option>
                                    <option value="0" {{request()->get('status') == '0' ? 'selected' : ''}}>
                                        Unapproved
                                    </option>
                                </select>
                            </div>
                            <div class="">
                                <select name="message_type" class="chatboat-message-type form-control">
                                    <option value="">Select Message Type</option>
                                    <option value="email" {{request()->get('message_type') == 'email' ? 'selected' : ''}}>Email</option>
                                    <option value="task" {{request()->get('message_type') == 'task' ? 'selected' : ''}}>Task</option>
                                    <option value="dev_task" {{request()->get('message_type') == 'dev_task' ? 'selected' : ''}}>Dev Task</option>
                                    <option value="ticket" {{request()->get('message_type') == 'ticket' ? 'selected' : ''}}>Ticket</option>
                                    <option value="IG_DMS" {{request()->get('message_type') == 'IG_DMS' ? 'selected' : ''}}>IG_DMS</option>
                                    <option value="FB_DMS" {{request()->get('message_type') == 'FB_DMS' ? 'selected' : ''}}>FB_DMS</option>
                                    <option value="IG_COMMENT" {{request()->get('message_type') == 'IG_COMMENT' ? 'selected' : ''}}>IG_COMMENT</option>
                                    <option value="FB_COMMENT" {{request()->get('message_type') == 'FB_COMMENT' ? 'selected' : ''}}>FB_COMMENT</option>
                                </select>
                            </div>

                            <!-- START - Purpose : Set unreplied messages - DEVATSK=4350 -->
                            <div style="display: flex;align-items: center" class="ml-sm-4 ml-0">

                                    @if(isset($_REQUEST['unreplied_msg']) && $_REQUEST['unreplied_msg']== true)
                                        @php $check_status = 'checked'; @endphp
                                    @else
                                        @php $check_status = ''; @endphp
                                    @endif

                                <input class="mt-0 mr-2" type="checkbox" id="unreplied_msg" name="unreplied_msg" {{$check_status}} value="true"> Unreplied Messages
                            </div>
                            <div style="display: flex;align-items: center" class="ml-sm-4 ml-0">
                                    @if(request("unread_message") == "true")
                                        @php $check_status = 'checked'; @endphp
                                    @else
                                        @php $check_status = ''; @endphp
                                    @endif

                                <input class="mt-0 mr-2" type="checkbox" id="unread_message" name="unread_message" {{$check_status}} value="true"> Unread Messages
                            </div>
                            <!-- END - DEVATSK=4350 -->
							<div style="display: flex;align-items: center" class="ml-sm-4 ml-0">
                                 <input class="mt-0 mr-2" type="checkbox"  name="search_type[]" value="customer" @if(request()->get('search_type') != null and in_array('customer', request()->get('search_type'))) checked @endif > Customer
                            </div>
							<div style="display: flex;align-items: center" class="ml-sm-4 ml-0">
                                 <input class="mt-0 mr-2" type="checkbox"  name="search_type[]" value="vendor" @if(request()->get('search_type') != null and in_array('vendor', request()->get('search_type'))) checked @endif> Vendor
                            </div>
							<div style="display: flex;align-items: center" class="ml-sm-4 ml-0">
                                 <input class="mt-0 mr-2" type="checkbox"  name="search_type[]" value="supplier" @if(request()->get('search_type') != null and in_array('supplier', request()->get('search_type'))) checked @endif> Supplier
                            </div>
							<div style="display: flex;align-items: center" class="ml-sm-4 ml-0">
                                 <input class="mt-0 mr-2" type="checkbox"  name="search_type[]" value="task" @if(request()->get('search_type') != null and in_array('task', request()->get('search_type'))) checked @endif> Task
                            </div>
							<div style="display: flex;align-items: center" class="ml-sm-4 ml-0">
                                 <input class="mt-0 mr-2" type="checkbox"  name="search_type[]" value="dev_task" @if(request()->get('search_type') != null and in_array('dev_task', request()->get('search_type'))) checked @endif> Dev Task
                            </div>
                            {{ html()->select('customer_id[]', [], null)->class('form-control customer-search-select-box')->multiple()->style('width:300px;') }}

                            <button type="submit" style="display: inline-block;width: auto" class="btn btn-sm btn-image">
                                <img src="/images/search.png" style="cursor: default;">
                            </button>
                            <button type="button" style="display: inline-block;width: auto;font-size:14px;" class="btn btn-sm btn-image" onclick="refresh();">
                                Reset
                            </button>
                            <button type="button" style="display: inline-block;width: auto;font-size:14px;" class="btn btn-sm btn-image" onclick="refresh();">
                                Reset
                            </button>
                        </div>
                    </form>
                  <!--  <form method="post" class="pt-3 d-flex align-items-center chatbot-send-field">
                       
                        <button type="submit" style="display: inline-block;width: 10%"
                                class="btn btn-sm btn-image btn-forward-images">
                            <i class="glyphicon glyphicon-send"></i>
                        </button>
                    </form> -->


                    <button type="button" class="btn custom-button float-right mr-3" data-toggle="modal" data-target="#chatbotmessagesdatatablecolumnvisibilityList">Column Visiblity</

                    <div>
                        @if($isElastic)
                            <a class="btn btn-xs btn-secondary" href="{{ route('chatbot.messages.list') }}">Switch to Database</a>
                        @else
                            <a class="btn btn-xs btn-warning" href="{{ route('chatbot.messages.list.elastic') }}/elastic">Switch to Elastic data</a>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="col-md-12 pl-3 pr-3">
        <div class="table-responsive-lg" id="page-view-result">
            @include("chatbot::message.partial.list")
        </div>
    </div>

    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
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
    <div id="record-voice-notes" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Record & Send Voice Message</h4>
                </div>
                <div class="modal-body" >
                    <Style>
                    #rvn_status:after {
                        overflow: hidden;
                        display: inline-block;
                        vertical-align: bottom;
                        -webkit-animation: ellipsis steps(4, end) 900ms infinite;
                        animation: ellipsis steps(4, end) 900ms infinite;
                        content: "\2026";
                        /* ascii code for the ellipsis character */
                        width: 0px;
                        }

                        @keyframes ellipsis {
                        to {
                            width: 40px;
                        }
                        }

                        @-webkit-keyframes ellipsis {
                        to {
                            width: 40px;
                        }
                        }
                    </style>
                    <input type="hidden" name="rvn_id" id="rvn_id" value="">
                    <input type="hidden" name="rvn_tid" id="rvn_tid" value="">
                    <button id="rvn_recordButton" class="btn btn-s btn-secondary">Start Recording</button>
                    <button id="rvn_pauseButton" class="btn btn-s btn-secondary"disabled>Pause Recording</button>
                    <button id="rvn_stopButton" class="btn btn-s btn-secondary"disabled>Stop Recording</button>
                    <div id="formats">Format: start recording to see sample rate</div>
                    <div id="rvn_status">Status: Not started...</div>
                    <div id="recordingsList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="rvn-btn-close-modal" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @include("partials.customer-new-ticket")
    @include("chatbot::message.partial.column-visibility-modal")
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
    </div>
    <script src="/js/bootstrap-toggle.min.js"></script>
    <script type="text/javascript" src="/js/jsrender.min.js"></script>
    <script type="text/javascript" src="/js/common-helper.js"></script>
    <script type="text/javascript" src="/js/recorder.js"></script>
    <script type="text/javascript" src="/js/record-voice-notes.js"></script>
    <script type="text/javascript">

        var callQuickCategory = function () {
            $(".select-quick-category").select2({tags:true,"width" : 200}).on("change",function(e){
                var $this = $(this);
                var id = $this.select2({tags:true,"width" : 200}).find(":selected").data("id");
                if(id == undefined) {
                    //siteHelpers.quickCategoryAdd($this);
                    var params = {
                        method : 'post',
                        data : {
                            _token : $('meta[name="csrf-token"]').attr('content'),
                            name : $this.val()
                        },
                        url: "/add-reply-category"
                    };
                    siteHelpers.sendAjax(params,"afterQuickCategoryAdd");
                }else{
                    var replies = JSON.parse($this.val());
                        $this.closest(".communication").find('.quickComment').empty();
                        $this.closest(".communication").find('.quickComment').append($('<option>', {
                            value: '',
                            text: 'Quick Reply'
                        }));
                        replies.forEach(function (reply) {
                            $this.closest(".communication").find('.quickComment').append($('<option>', {
                                value: reply.reply,
                                text: reply.reply,
                                'data-id': reply.id
                            }));
                        });
                }
            });
        }


        var callCategoryComment = function () {
            $(".select-quick-reply").select2({tags:true,"width" : 200}).on("change",function(e){
                var $this = $(this);
                var id = $this.select2({tags:true,"width" : 200}).find(":selected").data("id");
                if(id == undefined) {
                    var quickCategory = $this.closest(".communication").find(".quickCategory");

                    if (quickCategory.val() == "") {
                        toastr["error"]("Please Select Category!!", "error");
                        return false;
                    }
                    var quickCategoryId = quickCategory.children("option:selected").data('id');
                    var formData = new FormData();
                    formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
                    formData.append("reply", $this.val());
                    formData.append("category_id", quickCategoryId);
                    formData.append("model", 'Approval Lead');
                    var params = {
                        method : 'post',
                        data : formData,
                        url: "/reply"
                    };
                    siteHelpers.sendFormDataAjax(params,"afterQuickCommentAdd");
                    $this.closest('.customer-raw-line').find('.quick-message-field').val($this.val());

                }else{
                    $this.closest('.customer-raw-line').find('.quick-message-field').val($this.val());
                }
            });
        }

        callQuickCategory();
        callCategoryComment();


        $(document).on("click", ".approve-message", function () {
            var $this = $(this);
            $.ajax({
                type: 'POST',
                url: "/chatbot/messages/approve",
                beforeSend: function () {
                    $("#loading-image").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    id: $this.data("id"),
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    $this.remove();
                    toastr['success'](response.message, 'success');
                }
            }).fail(function (response) {
                $("#loading-image").hide();
            });
        });

        $(document).on("click", ".messages-reindex", function (e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                type: 'GET',
                url: "{{ route('chatbot.messages.reindex') }}",
                beforeSend: function () {
                    $("#loading-image").show();
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');
                }
            }).fail(function (response) {
                $("#loading-image").hide();
                toastr['error'](response.responseJSON.message, 'error');
            });
        });

        var getResults = function (href) {
            $.ajax({
                type: 'GET',
                url: href,
                beforeSend: function () {
                    $("#loading-image").show();
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    var removePage = response.page;
                    if (removePage > 0) {
                        var pageList = $("#page-view-result").find(".page-template-" + removePage);
                        pageList.nextAll().remove();
                        pageList.remove();
                    }
                    if (removePage > 1) {
                        $("#page-view-result").find(".pagination").first().remove();
                    }
                    if (isMessagesPage() === true) {
                        $("#page-view-result").empty();
                    }
                    $("#page-view-result").append(response.tpl);
                    callQuickCategory();
                    callCategoryComment();
                }
            }).fail(function (response) {
                $("#loading-image").hide();
            });
        };

        $("#page-view-result").on("click", ".page-link", function (e) {
            e.preventDefault();

            var activePage = $(this).closest(".pagination").find(".active").text();
            var clickedPage = $(this).text();
            if (clickedPage == "‹" || clickedPage < activePage) {
                $('html, body').animate({scrollTop: ($(window).scrollTop() - 50) + "px"}, 200);
                getResults($(this).attr("href"));
            } else {
                getResults($(this).attr("href"));
            }

        });

        $(window).scroll(function () {
            if (isMessagesPage() === true) {
                return;
            }
            if ($(window).scrollTop() > ($(document).height() - $(window).height() - 10)) {
                $("#page-view-result").find(".pagination").find(".active").next().find("a").click();
            }
        });

        var isMessagesPage = function()
        {
            let path = '/chatbot/messages';
            let pathName = window.location.pathname;
            if (pathName.indexOf(path) !== -1) {
                return true;
            }

            return false;
        }

        $(document).on("click", ".delete-images", function () {

            var tr = $(this).closest("tr");
            var checkedImages = tr.find(".remove-img:checkbox:checked").closest(".panel-img-shorts");
            var form = tr.find('.remove-images-form');
            $.ajax({
                type: 'POST',
                url: form.attr("action"),
                data: form.serialize(),
                beforeSend: function () {
                    $("#loading-image").show();
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    $.each(checkedImages, function (k, e) {
                        $(e).remove();
                    });
                    toastr['success'](response.message, 'success');
                }
            }).fail(function (response) {
                $("#loading-image").hide();
            });
        });

        $(document).on("click", ".add-more-images", function () {
            var $this = $(this);
            var id = $this.data("id");

            $.ajax({
                type: 'GET',
                url: "{{ route('chatbot.messages.attach-images') }}",
                data: {chat_id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    if (response.data.length > 0) {
                        var html = "";
                        $.each(response.data, function (k, img) {
                            html += '<div class="panel-img-shorts">';
                            html += '<input type="checkbox" name="delete_images[]" value="' + img.mediable_id + '_' + img.id + '" class="remove-img" data-media-id="' + img.id + '" data-mediable-id="' + img.mediable_id + '">';
                            html += '<img width="50px" heigh="50px" src="' + img.url + '">';
                            html += '</div>';
                        });
                        $this.closest("tr").find(".images-layout").find("form").append(html);
                    }
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function (response) {
                $("#loading-image").hide();
            });
        });

        $(document).on("click", ".check-all", function () {
            var tr = $(this).closest("tr");
            tr.find(".remove-img").trigger("click");
        });

        $(document).on("click", ".btn-forward-images", function (e) {
            e.preventDefault();
            var selectedImages = $("#page-view-result").find(".remove-img:checkbox:checked");
            var imagesArr = [];
            $.each(selectedImages, function (k, v) {
                imagesArr.push($(v).data("media-id"));
            });
            $.ajax({
                type: "POST",
                url: "/chatbot/messages/forward-images",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'images': imagesArr,
                    'customer': $(".customer-search-select-box").val()
                }
            }).done(function (response) {
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
            });

        });

        $(document).on('click', '.send-message1', function () {
            var thiss = $(this);
            var data = new FormData();

            var field = "customer_id";
            var tr  = $(this).closest("tr").find("td").first();
            var typeId = tr.data('customer-id');
            var id = $(this).data('id');
            var page = tr.data('page');
            var chatMessageReplyId = tr.data('chat-message-reply-id')
            var type = tr.data("context");
            var data_chatbot_id = tr.data('chatbot-id');
            var is_audio=0;
            if( $("#is_audio_"+id).length )  {
                is_audio=$("#is_audio_"+id).val();
            }
            
            data.append("chat_id", id);

            var message= $('#message_'+id).val();


            if(parseInt(tr.data("vendor-id")) > 0) {
                type = "vendor";
                typeId = tr.data("vendor-id");
                field = "vendor_id";

                //START - Purpose : Add vendor content - DEVTASK-4203
                var message = thiss.closest(".cls_textarea_subbox").find("textarea").val();
                data.append("vendor_id", typeId);
                data.append("message", message);
                data.append("status", 2);
                data.append("sendTo", 'to_developer');
                data.append("chat_reply_message_id", chatMessageReplyId)
                //END - DEVTASK-4203
            }

            var customer_id = typeId;
            //var message = thiss.closest(".cls_textarea_subbox").find("textarea").val();

            var message= $('#message_'+id).val();


            if(type === 'email'){
                typeId = tr.data('email-id');
                data.append("email_id", typeId);
                data.append("message", message);
                data.append("status", 1);
            }else if(type === 'customer'){
                data.append("customer_id", typeId);
                data.append("message", message);
                data.append("status", 1);

            }else if(type === 'issue'){

                data.append('issue_id', typeId);
                data.append("message", message);
                data.append("is_audio", is_audio);
                data.append("sendTo", 'to_developer');
                data.append("status", 2)
                data.append("chat_reply_message_id", chatMessageReplyId)

            }else if(type === 'issue'){
                data.append('issue_id', typeId);
                data.append("message", message);
                data.append("is_audio", is_audio);
                data.append("status", 1)
                data.append("chat_reply_message_id", chatMessageReplyId)
            }
            //START - Purpose : Task message - DEVTASK-4203
            else if(type === 'task'){
                data.append('task_id', typeId);
                data.append("message", message);
                data.append("is_audio", is_audio);
                data.append("status", 2)
                data.append("sendTo", 'to_developer');
                data.append("chat_reply_message_id", chatMessageReplyId)
            }
            //END - DEVTASK-4203

             //STRAT - Purpose : send message - DEVTASK-18280
            else if(type === 'chatbot'){
                data.append('customer_id', typeId);
                data.append("message", message);
                data.append("status", 1)
                data.append("chat_reply_message_id", data_chatbot_id)

                id = typeId;
                var scrolled=0;
                $.ajax({
                    url: "{{ route('livechat.send.message') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: { id : id ,
                        message : message,
                        from:'chatbot_replay',
                    _token: "{{ csrf_token() }}"
                    },
                })
                .done(function(data) {
                    // thiss.closest(".cls_textarea_subbox").find("textarea").val("");
                    // toastr['success']("Message sent successfully", 'success');
                })
            }
            //END - DEVTASK-18280


            var add_autocomplete  = thiss.closest(".cls_textarea_subbox").find("[name=add_to_autocomplete]").is(':checked') ;
            data.append("add_autocomplete", add_autocomplete);
            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/'+type,
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);

                        }
                    }).done(function (response) {
                        $(thiss).attr('disabled', false);
                        thiss.closest(".cls_textarea_subbox").find("textarea").val("");
                        toastr['success']("Message sent successfully", 'success');

                        $(".pam-" + id).find(".user-input").each(function() {
                            $(this).text(message);
                        });
                    }).fail(function (errObj) {
                        getResults("{{ route('chatbot.messages.list') }}?page=" + page);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });

        var siteHelpers = {
            quickCategoryAdd : function(ele) {
                var textBox = ele.closest("div").find(".quick_category");
                if (textBox.val() == "") {
                    toastr["error"]("Please Enter Category!!", "error");
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
                siteHelpers.sendAjax(params,"afterQuickCategoryAdd");
            },
            afterQuickCategoryAdd : function(response) {
                callQuickCategory();
            },
            deleteQuickCategory : function(ele) {
                var quickCategory = ele.closest(".communication").find(".quickCategory");
                if (quickCategory.val() == "") {
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
            deleteQuickComment : function(ele) {
                var quickComment = ele.closest(".communication").find(".quickComment");
                if (quickComment.val() == "") {
                    toastr["error"]("Please Select Quick Comment!!!", "error");
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
            quickCommentAdd : function(ele) {
                var textBox = ele.closest("div").find(".quick_comment");
                var quickCategory = ele.closest(".communication").find(".quickCategory");
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
                var params = {
                    method : 'post',
                    data : formData,
                    url: "/reply"
                };
                siteHelpers.sendFormDataAjax(params,"afterQuickCommentAdd");
            },
            afterQuickCommentAdd : function(reply) {
                /*$(".quick_comment").val('');
                $('.quickComment').append($('<option>', {
                    value: reply,
                    text: reply
                }));*/
                callCategoryComment();
            },
            changeQuickCategory : function (ele) {
                if (ele.val() != "") {
                    var replies = JSON.parse(ele.val());
                    ele.closest(".communication").find('.quickComment').empty();
                    ele.closest(".communication").find('.quickComment').append($('<option>', {
                        value: '',
                        text: 'Quick Reply'
                    }));
                    replies.forEach(function (reply) {
                        ele.closest(".communication").find('.quickComment').append($('<option>', {
                            value: reply.reply,
                            text: reply.reply,
                            'data-id': reply.id
                        }));
                    });
                }
            },
            changeQuickComment : function (ele) {
                ele.closest('.customer-raw-line').find('.quick-message-field').val(ele.val());
            },
            pageReload : function() {
                location.reload();
            }

        };
        $.extend(siteHelpers, common)

        $(document).on('click', '.quick_category_add', function () {
            siteHelpers.quickCategoryAdd($(this));
        });
        $(document).on('click', '.delete_category', function () {
            siteHelpers.deleteQuickCategory($(this));
        });
        $(document).on('click', '.delete_quick_comment', function () {
            siteHelpers.deleteQuickComment($(this));
        });
        $(document).on('click', '.quick_comment_add', function () {
            siteHelpers.quickCommentAdd($(this));
        });
        /*$(document).on('change', '.quickCategory', function () {
            siteHelpers.changeQuickCategory($(this));
        });*/
        /*$(document).on('change', '.quickComment', function () {
            siteHelpers.changeQuickComment($(this));
        });*/

        $('document').on('click', '.create-customer-ticket-modal', function () {
            $('#ticket_customer_id').val($(this).attr('data-customer_id'));
        });

        $(document).on('click', '.create_short_cut',function () {
            $('.sop_description').val("");
            let message = '';
            message = $(this).attr('data-msg');
            $('.sop_description').val(message);
        });
        $( document ).ready(function() {
            $(document).on('click', '.btn-trigger-rvn-modal',function () {
                var id=$(this).attr('data-id')
                var tid=$(this).attr('data-tid')
                $("#record-voice-notes #rvn_id").val(id);
                $("#record-voice-notes #rvn_tid").val(tid);
                $("#record-voice-notes").modal("show");
            });
            $('#record-voice-notes').on('hidden.bs.modal', function () {
                $("#rvn_stopButton").trigger("click");
                $("#formats").html("Format: start recording to see sample rate");
                $("#rvn_id").val(0);
                $("#rvn_tid").val(0);
                setTimeout(function () {
                     $("#recordingsList").html('');
                }, 2500);
            })
        });
        function refresh(){
            window.location.href = "{{route('chatbot.messages.list')}}";
        }
 
    </script>
@endsection
