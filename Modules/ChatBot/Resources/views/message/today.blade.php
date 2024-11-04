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
    </style>
    <style type="text/css">
        .cls_remove_rightpadding {
            padding-right: 0px !important;
        }
        .cls_remove_allpadding {
            padding-left: 0px !important;
            padding-right: 0px !important;
        }
        #chat-list-history tr {
            word-break: break-word;
        }
        .reviewed_msg {
            word-break: break-word;
        }
        .chatbot .communication{
    
        }
        .background-grey {
            color: grey;
        }
        @media(max-width:1400px){
            .btns{
    padding: 3px 2px;
            }
        }
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ddd !important;
        }
        .d-inline.form-inline .select2-container{
            max-width: 100% !important;
            /*width: unset !important;*/
        }
        .actions{
            display: flex !important;
            align-items: center;
        }
        .actions a{
            padding: 0 3px !important;
            display: flex !important;
            align-items: center;
        }
        .actions .btn-image img{
            width: 13px !important;
        }
        .read-message{
            float: right;
        }
    </style>
    <div class="row m-0">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">Message List | Chatbot</h2>
        </div>
    </div>

    <div class="row m-0">
        <div class="col-md-12 pl-3 pr-3">
            <div class="table-responsive-lg" id="page-view-result">
                <div class="table-responsive">
                    <div class="col-md-12 text-right">
                        <a href="#" class="btn btn-default m-3" data-totalpage="{{$totalpage}}" id="RefreshPage" data-page="1" >Refresh</a>
                    </div>
                    <table class="table table-bordered chatbot page-template-{{ $page }}">
                        <thead>
                        <tr>
                            <th width="2%">Name</th>
                            <th width="2%">Website</th>
                            <th width="2%">Message Type</th>
                            <th width="8%">User input</th>
                            <th width="8%">Bot Replied</th>
                            <th width="8%">Bot Suggested Reply</th>
                            <th width="30%">Message Box </th>
                            <th width="2%">From</th>
                            <th width="2%">Shortcuts</th>
                            <th width="2%">Action</th>

                        </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($pendingApprovalMsg)) {?>
                                <?php foreach ($pendingApprovalMsg as $index => $pam) { ?>
                                    @include("chatbot::message.partial.today-list")
                                <?php }?>
                            <?php }?>
                        </tbody>
                        
                    </table>
                    <div class="col-md-12 text-center">
                        <a href="#" class="btn btn-default m-3" data-totalpage="{{$totalpage}}" id="loadMore" data-page="2" >Load More</a>
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
                    $("#page-view-result").append(response.tpl);
                    callQuickCategory();
                    callCategoryComment();
                }
            }).fail(function (response) {
                $("#loading-image").hide();
            });
        };
        /*setInterval(function() {
            var cmid=$("#page-view-result table > tbody >tr:first ").attr("data-cmid");

            var href="/chatbot/messages/today-check-new";
            $.ajax({
                type: 'GET',
                url: href,
                data:{
                    'cmid':cmid
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                page=response.page+1;
                if(response.totalpage<page){
                    $("#loadMore").remove();
                }else{
                    $("#loadMore").attr("data-page",page);
                }
                $("#page-view-result table tbody").append(response.tpl);
                    callQuickCategory();
                    callCategoryComment();
            }).fail(function (response) {
                $("#loading-image").hide();
            });
        },1000 * 60 * 1);*/

        $("#page-view-result").on("click", "#loadMore", function (e) {
            e.preventDefault();
            var page=$(this).attr('data-page');
            var href="/chatbot/messages/today?page="+page;
            $.ajax({
                type: 'GET',
                url: href,
                beforeSend: function () {
                    $("#loading-image").show();
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                if(response.code=="200"){
                   
                    page=response.page+1;

                    if(response.page=='' || response.totalpage<page){
                        $("#loadMore").remove();
                    }else{
                        $("#loadMore").attr("data-page",page);
                    }
                    $("#page-view-result table tbody").append(response.tpl);
                        callQuickCategory();
                        callCategoryComment();
                }else{
                    $("#loadMore").remove();
                }
                
            }).fail(function (response) {
                $("#loading-image").hide();
            });

        });
        $("#page-view-result").on("click", "#RefreshPage", function (e) {
            e.preventDefault();
            var page=$(this).attr('data-page');
            var href="/chatbot/messages/today?page="+page;
            $.ajax({
                type: 'GET',
                url: href,
                beforeSend: function () {
                    $("#loading-image").show();
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                page=response.page+1;
                if(response.totalpage<page){
                    $("#loadMore").remove();
                }else{
                    $("#loadMore").attr("data-page",page);
                }
                $("#page-view-result table tbody").html(response.tpl);
                    callQuickCategory();
                    callCategoryComment();
            }).fail(function (response) {
                $("#loading-image").hide();
            });
        });

        /*$(window).scroll(function () {
            if ($(window).scrollTop() > ($(document).height() - $(window).height() - 10)) {
                $("#page-view-result").find(".pagination").find(".active").next().find("a").click();
            }
        });*/

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

                    }).fail(function (errObj) {

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
 
    </script>

    
<div id="approve-reply-popup" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?php echo route('chatbot.question.save'); ?>">
                <?php echo csrf_field(); ?>
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
            <form method="post" action="<?php echo route('chatbot.question.save'); ?>">
                <?php echo csrf_field(); ?>

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

<script type="text/javascript">
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

    $(document).on('click','.log-message-popup',function(){
        $('#logMessageModel p').text($(this).data('log_message'));
        $('#logMessageModel').modal('show');

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

    $(document).on("click",".send_suggested_replay",function () {
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

    function changeSimulatorSetting(object, objectId, simulator) {
        $.ajax({
            type: "POST",
            url: "/chatbot/messages/update-simulator-setting",
            data: {
                '_token': "{{ csrf_token() }}",
                'object' : object,
                'objectId' : objectId,
                'auto_simulator': simulator
            },
            dataType : "json"
        }).done(function(response) {
            if(response.code == 200) {
                toastr['success'](response.messages);
            }else{
                toastr['error'](response.messages);
            }
        }).fail(function(response) {
            toastr['error']('Could not update simulator status');
        });
    }

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

</script>
    
@endsection

