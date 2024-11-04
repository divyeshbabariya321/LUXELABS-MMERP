<style>
    .tickets .btn-group-xs>.btn,
    .btn-xs {
        padding: 1px 2px !important;
        font-size: 15px !important;
    }

    .tickets .select2-container .select2-selection--single {
        height: 32px !important;
        border: 1px solid #ddd !important;
        color: #757575;
        padding-left: 6px;
    }

    .tickets .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }

    .tickets .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
        color: #757575;
    }

    .tickets>tbody>tr>td,
    .tickets>tbody>tr>th,
    .tickets>tfoot>tr>td,
    .tickets>tfoot>tr>th,
    .tickets>thead>tr>td,
    .tickets>thead>tr>th {
        padding: 6px !important;
    }

    .tickets .page-heading {
        font-size: 16px;
        text-align: left;
        margin-bottom: 10px;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-weight: 600;
    }

    .form-control {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }

    .row.tickets .form-group input {
        font-size: 13px;
        height: 32px;
    }

    .container.container-grow {
        padding: 0 !important;
    }

    #quick-sidebar {
        padding-top: 0 !important;
    }

    #quick-sidebar .fa-2x {
        font-size: 1.4em;
        margin-bottom: 0;
        height: auto !important;
    }

    #quick-sidebar {
        min-width: 35px !important;
        max-width: 30px !important;
        margin-left: 0px !important;
    }

    .container.container-grow {
        width: 100% !important;
        max-width: 100% !important;
    }

    .flex {
        display: flex;
    }

    .list-unstyled.components li {
        padding: 8px 6px;
        border-bottom: 1px solid #ddd;
        cursor: pointer;
        text-align: center;
    }

    .list-unstyled.components li:hover {
        background: #dddddd78;
    }

    #quick-sidebar a {
        text-align: center;
    }

    .list-unstyled.components {
        border-top: 1px solid #ddd;
        border-right: 1px solid #ddd;
        margin-right: 10px;
        margin-left: 10px;
        border-left: 1px solid #ddd;
        border-radius: 4px;
    }

    .navbar-laravel {
        box-shadow: none !important;
    }

    .space-right {
        padding-right: 10px;
        padding-left: 10px;
    }

    .row.tickets {
        font-size: 13px !important;
    }

    td {
        padding: 5px !important;
    }

    td a {
        color: #2f2f2f;
    }

    tbody td {
        background: #ddd3;
    }

    .select2-container .select2-search--inline .select2-search__field {
        margin-top: 0px !important;
    }

    .select2-search--inline {
        display: contents;
        /*this will make the container disappear, making the child the one who sets the width of the element*/
    }

    .select2-search__field:placeholder-shown {
        width: 100% !important;
        /*makes the placeholder to be 100% of the width while there are no options selected*/
    }
</style>
@extends('layouts.app')

@section('content')
    @php($users = $query = \App\User::get())

    @include('partials.flash_messages')
    <div class="row tickets m-0">
        <div class="col-lg-12 margin-tb pr-0 pl-0">
            <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">
                {{ isset($title) ? ucfirst($title) : 'Tickets' }} (<span id="list_count">{{ $data->total() }}</span>)
                <div class="margin-tb" style="flex-grow: 1;">
                    <div class="pull-right ">
                        
                    </div>
                </div>
            </h2>
        </div>
        {{-- <div class="col-lg-12 margin-tb pl-3">
            <div class="form-group mb-3">
                <div class="row">
                    <div class="col-md-2 pr-0 mb-3">
                        <select class="form-control globalSelect21"  name="users_id" id="users_id">
                           
                            @foreach ($users as $key => $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 pl-3  pr-0">
                        <select class="form-control globalSelect22" name="ticket_id" id="ticket">
                            
                            @foreach ($data as $key => $ticket)
                            <option value="{{ $ticket->ticket_id }}">{{ $ticket->ticket_id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 pl-3 pr-0">
                        {{ html()->select("status_id", \App\TicketStatuses::pluck("name", "id")->toArray(), request('status_id'))->class("form-control globalSelect24")->id("status_id") }}
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <div class='input-group date' id='filter_date'>
                            <input placeholder="Select Date" type='text' class="form-control" id="date" name="date" value="" />

                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-2 pl-3 pr-0">
                        <select class="form-control globalSelect23" name="term" id="term">
                            
                            @foreach ($data as $key => $user_name)
                                <option value="{{ $user_name->name }}">{{ $user_name->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <select class="form-control globalSelect25" name="user_email" id="user_email">
                            
                            @foreach ($data as $key => $user_email)
                                <option value="{{ $user_email->email }}">{{ $user_email->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <input name="user_message" type="text" class="form-control"
                                    placeholder="Search Message" id="user_message">
                    </div>
                    <div class="col-md-2 pl-3 pr-3">
                        <input name="serach_inquiry_type" type="text" class="form-control"
                                value="{{ isset($serach_inquiry_type) ? $serach_inquiry_type : '' }}"
                                placeholder="Inquiry Type" id="serach_inquiry_type">
                    </div>
                    <div class="col-md-2  pr-0">
                        <input name="search_country" type="text" class="form-control"
                                value="{{ isset($search_country) ? $search_country : '' }}"
                                placeholder="Country" id="search_country">
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <input name="search_order_no" type="text" class="form-control"
                                value="{{ isset($search_order_no) ? $search_order_no : '' }}"
                                placeholder="Order No." id="search_order_no">
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <input name="search_phone_no" type="text" class="form-control"
                                value="{{ isset($search_phone_no) ? $search_phone_no : '' }}"
                                placeholder="Phone No." id="search_phone_no">
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <input name="search_source" type="text" class="form-control"
                                value="{{ isset($search_source) ? $search_source : '' }}"
                                placeholder="Source." id="search_source">
                    </div>

                    <!-- <div class="col-md-2">
                        <input name="search_category" type="text" class="form-control"
                                value="{{ isset($search_category) ? $search_category : '' }}"
                                placeholder="Category" id="search_category">
                    </div> -->
                    <div>
                    <button type="button" class="btn btn-image mt-2" onclick="submitSearch()"><img src="{{ asset('images/filter.png')}}"/></button>
                    </div>
                    <div >
                        <button type="button" class="btn btn-image mt-2" id="resetFilter" onclick="resetSearch()"><img src="{{ asset('images/resend2.png')}}"/></button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-image mt-2" id="send-message"><img src="{{ asset('images/whatsapp-logo.png')}}"/></button>
                    </div>
                    <div>
                        <button class="btn btn-xs btn-secondary mt-2" style="color:white;" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
                    </div>
                </div>


            </div>

        </div> --}}

    </div>

    <div class="space-right chat-list-table">

        <div class="infinite-scroll" style="overflow-y: auto">
            <table class="table table-bordered table-striped" style="font-size: 14px;">
                <thead>
                    <tr>

                        <th>Id</th>
                        <th>Store Website</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Type of inquiry</th>
                        <th>Product</th>
                        <th>Product Price</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Update Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="content_data" class="infinite-scroll-pending-inner">
                    @include('ticket.partials.table_list')
                </tbody>
            </table>
        </div>
    </div>


    <div id="loading-image"
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
              50% 50% no-repeat;display:none;">
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
@endsection

@section('scripts')
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $(".globalSelect21").select2({
                multiple: true,
                placeholder: "Select Users",
            });
            $(".globalSelect22").select2({
                multiple: true,
                placeholder: "Select Ticket",
            });
            $(".globalSelect23").select2({
                multiple: true,
                placeholder: "Select User Name",
            });
            $(".globalSelect24").select2({
                multiple: true,
                placeholder: "Select Status",
            });
            $(".globalSelect25").select2({
                multiple: true,
                placeholder: "Select User Email",
            });
            $(".globalSelect26").select2({
                multiple: true,
                placeholder: "Select User Message",
            });

            $("#user_email option").each(function() {
                $(this).siblings('[value="' + this.value + '"]').remove();
            });
            $("#term option").each(function() {
                $(this).siblings('[value="' + this.value + '"]').remove();
            });
        });

        

        

        function opnModal(message) {
            $(document).find('#more-content').html(message);
        }
        $(document).on('click', '.send-notification-btn', function(e) {
            e.preventDefault();
            var $this = $(this);
            var type = $(this).data('type');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/email/resendMail/' + $this.data("id"),
                type: 'post',
                data: {
                    type: type
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
            }).done(function(response) {
                toastr['success'](response.message);
                $("#loading-image").hide();
            }).fail(function(errObj) {
                $("#loading-image").hide();
            });
        });


        $('#filter_date').datetimepicker({
            format: 'YYYY-MM-DD'
        });
      
        function submitSearch(page = 1) {
            //src = "{{ url('whatsapp/pollTicketsCustomer') }}";
            src = "{{ url('livechat/tickets') }}";
            term = $('#term').val();
            user_email = $('#user_email').val();
            user_message = $('#user_message').val();
            erp_user = 152;
            serach_inquiry_type = $('#serach_inquiry_type').val();
            search_country = $('#search_country').val();
            search_order_no = $('#search_order_no').val();
            search_phone_no = $('#search_phone_no').val();
            //search_category = $('#search_category').val();
            ticket_id = $('#ticket').val();
            status_id = $('#status_id').val();
            date = $('#date').val();
            users_id = $('#users_id').val();
            search_source = $('#search_source').val();
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    erpUser: erp_user,
                    term: term,
                    user_email: user_email,
                    user_message: user_message,
                    serach_inquiry_type: serach_inquiry_type,
                    search_country: search_country,
                    search_order_no: search_order_no,
                    search_phone_no: search_phone_no,
                    ticket_id: ticket_id,
                    status_id: status_id,
                    date: date,
                    users_id: users_id,
                    search_source: search_source,
                    page: page // Include the page parameter in the data
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
            }).done(function(message) {
                $("#loading-image").hide();
                $('#ticket').val(ticket_id);
                $('#content_data').html(message.tbody);
                $('#pagination-container').html(message.links);
                var rendered = renderMessage(message, tobottom);

                // Update the URL in the browser's address bar
                history.pushState(null, null, src + '?page=' + page);
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        }

        function resetSearch() {
            $("#loading-image").hide();
            $('#term').val('');
            $('#serach_inquiry_type').val('');
            $('#search_country').val('');
            $('#search_order_no').val('');
            $('#search_phone_no').val('');
            $('#ticket').val('');
            $('#users_id').val('');
            location.reload();
        }


       
        $(document).on('click', '.cc-delete-button', function(e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function() {
                parent.remove();
                var n = 0;

                $('.cc-input').each(function() {
                    n++;
                });

                if (n == 0) {
                    $('#cc-label').fadeOut();
                }
            });
        });

        // bcc

        


        function resolveIssue(obj, task_id) {
            let id = task_id;
            let status = $(obj).val();
            let self = this;


            $.ajax({
                url: "{{ route('tickets.status.change') }}",
                method: "POST",
                data: {
                    id: id,
                    status: status
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    toastr["success"]("Status updated!", "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message);
                }
            });
        }

        
        $(document).on('click', '.send-message1', function() {

            var thiss = $(this);
            var data = new FormData();
            var ticket_id = $(this).data('ticketid');
            var message = $("#messageid_" + ticket_id).val();
            if (message != "") {
                $("#message_confirm_text").html(message);
                $("#confirm_ticket_id").val(ticket_id);
                $("#confirm_message").val(message);
                $("#confirm_status").val(1);
                $("#confirmMessageModal").modal();
            }
        });
        $(document).on('click', '.confirm-messge-button', function() {
            var thiss = $(this);
            var data = new FormData();
            //    var ticket_id = $(this).data('ticketid');
            //    var message = $("#messageid_"+ticket_id).val();
            var ticket_id = $("#confirm_ticket_id").val();
            var message = $("#confirm_message").val();
            var status = $("#confirm_status").val();

            data.append("ticket_id", ticket_id);
            data.append("message", message);
            data.append("status", 1);

            var checkedValue = [];
            var i = 0;
            $('.send_message_recepients:checked').each(function() {
                checkedValue[i++] = $(this).val();
            });
            data.append("send_ticket_options", checkedValue);
            //  alert(data);

            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL + '/whatsapp/sendMessage/ticket',
                        type: 'POST',
                        "dataType": 'json', // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function() {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function(response) {
                        //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                        $('#confirmMessageModal').modal('hide');
                        if (message.length > 30) {
                            var res_msg = message.substr(0, 27) + "...";
                            $("#message-chat-txt-" + ticket_id).html(res_msg);
                            $("#message-chat-fulltxt-" + ticket_id).html(message);
                        } else {
                            $("#message-chat-txt-" + ticket_id).html(message);
                            $("#message-chat-fulltxt-" + ticket_id).html(message);
                        }

                        $("#messageid_" + ticket_id).val('');

                        $(thiss).attr('disabled', false);
                    }).fail(function(errObj) {
                        $(thiss).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
    
        $(document).on("click", "#send-message", function() {
            $("#send-message-text-box").modal("show");
        });

        $(".btn-send-brodcast-message").on("click", function() {

            var selected_tasks = [];

            $.each($(".selected-ticket-ids:checked"), function(k, v) {
                selected_tasks.push($(v).val());
            });

            if (selected_tasks.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('tickets/send-brodcast') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        selected_tasks: selected_tasks,
                        message: $(".message-for-brodcast").val()
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr["success"](response.message);
                        $("#send-message-text-box").modal("hide");
                    } else {
                        toastr["error"](response.message);
                    }
                }).fail(function(response) {
                    $("#loading-image").hide();
                    console.log(response);
                    toastr["error"](
                        "Request has been failed due to the server , please contact administrator");
                });
            } else {
                $("#loading-image").hide();
                toastr["error"]("Please select atleast 1 task!");
            }
        });
    </script>
    
    <script>
        
        // Load tickets on initial page load
        $(document).ready(function() {
            loadTickets('{{ Request::url() }}');
        });

        // Add an event listener to the pagination links
        $(document).on('click', '#pagination-container .page-item .page-link', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            loadTickets(url);
        });

        function loadTickets(url) {
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',

                success: function(response) {
                    $('#content_data').html(response.tbody);
                    $('#pagination-container').html(response.links);
                },
                error: function(xhr, status, error) {
                    alert('error')
                }
            });
        }
    </script>

    {{-- <script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
    <script type="text/javascript">
        var siteHelpers = {

            quickCategoryAdd: function(ele) {
                var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
                var quickCategoryId = quickCategory.children("option:selected").data('id');
                var textBox = ele.closest("div").find(".quick_category");
                if (textBox.val() == "") {
                    alert("Please Enter Category!!");
                    return false;
                }
                var params = {
                    method: 'post',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        name: textBox.val(),
                        quickCategoryId: quickCategoryId
                    },
                    url: "/add-reply-category"
                };

                if (quickCategoryId != '') {
                    siteHelpers.sendAjax(params, "afterQuickSubCategoryAdd");
                } else {
                    siteHelpers.sendAjax(params, "afterQuickCategoryAdd");
                }
            },
            afterQuickSubCategoryAdd: function(response) {
                $(".quick_category").val('');
                $(".quickSubCategory").append('<option value="[]" data-id="' + response.data.id + '">' + response
                    .data.name + '</option>');
            },
            afterQuickCategoryAdd: function(response) {
                $(".quick_category").val('');
                $(".quickCategory").append('<option value="[]" data-id="' + response.data.id + '">' + response.data
                    .name + '</option>');
            },
            deleteQuickCategory: function(ele) {
                var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
                if (quickCategory.val() == "") {
                    alert("Please Select Category!!");
                    return false;
                }
                var quickCategoryId = quickCategory.children("option:selected").data('id');
                if (!confirm("Are sure you want to delete category?")) {
                    return false;
                }
                var params = {
                    method: 'post',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: quickCategoryId
                    },
                    url: "/destroy-reply-category"
                };
                siteHelpers.sendAjax(params, "pageReload");
            },
            deleteQuickSubCategory: function(ele) {
                var quickSubCategory = ele.closest("#shortcutsIds").find(".quickSubCategory");
                if (quickSubCategory.val() == "") {
                    alert("Please Select Sub Category!!");
                    return false;
                }
                var quickSubCategoryId = quickSubCategory.children("option:selected").data('id');
                if (!confirm("Are sure you want to delete sub category?")) {
                    return false;
                }
                var params = {
                    method: 'post',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: quickSubCategoryId
                    },
                    url: "/destroy-reply-category"
                };
                siteHelpers.sendAjax(params, "pageReload");
            },
            deleteQuickComment: function(ele) {
                var quickComment = ele.closest("#shortcutsIds").find(".quickCommentEmail");
                if (quickComment.val() == "") {
                    alert("Please Select Quick Comment!!");
                    return false;
                }
                var quickCommentId = quickComment.children("option:selected").data('id');
                if (!confirm("Are sure you want to delete comment?")) {
                    return false;
                }
                var params = {
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/reply/" + quickCommentId,
                };
                siteHelpers.sendAjax(params, "pageReload");
            },
            pageReload: function(response) {
                location.reload();
            },
            quickCommentAdd: function(ele) {
                var textBox = ele.closest("div").find(".quick_comment");
                var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
                var quickSubCategory = ele.closest("#shortcutsIds").find(".quickSubCategory");
                if (textBox.val() == "") {
                    alert("Please Enter New Quick Comment!!");
                    return false;
                }
                if (quickCategory.val() == "") {
                    alert("Please Select Category!!");
                    return false;
                }
                var quickCategoryId = quickCategory.children("option:selected").data('id');
                var quickSubCategoryId = quickSubCategory.children("option:selected").data('id');
                var formData = new FormData();
                formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
                formData.append("reply", textBox.val());
                formData.append("category_id", quickCategoryId);
                formData.append("sub_category_id", quickSubCategoryId);
                formData.append("model", 'Approval Lead');
                var params = {
                    method: 'post',
                    data: formData,
                    url: "/reply"
                };
                siteHelpers.sendFormDataAjax(params, "afterQuickCommentAdd");
            },
            afterQuickCommentAdd: function(reply) {
                $(".quick_comment").val('');
                $('.quickCommentEmail').append($('<option>', {
                    value: reply,
                    text: reply
                }));
            },
            changeQuickCategory: function(ele) {

                var selectedOption = ele.find('option:selected');
                var dataValue = selectedOption.data('value');

                ele.closest("#shortcutsIds").find('.quickSubCategory').empty();
                ele.closest("#shortcutsIds").find('.quickSubCategory').append($('<option>', {
                    value: '',
                    text: 'Select Sub Category'
                }));
                dataValue.forEach(function(category) {
                    ele.closest("#shortcutsIds").find('.quickSubCategory').append($('<option>', {
                        value: category.name,
                        text: category.name,
                        'data-id': category.id
                    }));
                });

                if (ele.val() != "") {
                    var replies = JSON.parse(ele.val());
                    ele.closest("#shortcutsIds").find('.quickCommentEmail').empty();
                    ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                        value: '',
                        text: 'Quick Reply'
                    }));
                    replies.forEach(function(reply) {
                        ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                            value: reply.reply,
                            text: reply.reply,
                            'data-id': reply.id
                        }));
                    });
                }
            },
            changeQuickComment: function(ele) {
                $('#messageid_' + ele.attr('data-id')).val(ele.val());

                var userEmaillUrl = '/email/email-frame-info/' + $('#reply_email_id').val();;
                var senderName = 'Hello ' + $('#sender_email_address').val().split('@')[0] + ',';

                $("#reply-message").val(senderName)

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: userEmaillUrl,
                    type: 'get',
                }).done(function(response) {
                    $("#reply-message").val(senderName + '\n\n' + ele.val() + '\n\n' + response)
                }).fail(function(errObj) {})

            },
            changeQuickSubCategory: function(ele) {
                var selectedOption = ele.find('option:selected');
                var dataValue = selectedOption.data('id');

                var userEmaillUrl = '/livechat-replise/' + dataValue;

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: userEmaillUrl,
                    type: 'get',
                }).done(function(response) {

                    if (response != '') {
                        var replies = JSON.parse(response);
                        ele.closest("#shortcutsIds").find('.quickCommentEmail').empty();
                        ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                            value: '',
                            text: 'Quick Reply'
                        }));
                        replies.forEach(function(reply) {
                            ele.closest("#shortcutsIds").find('.quickCommentEmail').append($(
                                '<option>', {
                                    value: reply.reply,
                                    text: reply.reply,
                                    'data-id': reply.id
                                }));
                        });
                    }

                }).fail(function(errObj) {})
            },
        };

        $.extend(siteHelpers, common);

        $(document).on('click', '.quick_category_add', function() {
            siteHelpers.quickCategoryAdd($(this));
        });
        $(document).on('click', '.delete_category', function() {
            siteHelpers.deleteQuickCategory($(this));
        });
        $(document).on('click', '.delete_sub_category', function() {
            siteHelpers.deleteQuickSubCategory($(this));
        });
        $(document).on('click', '.delete_quick_comment', function() {
            siteHelpers.deleteQuickComment($(this));
        });
        $(document).on('click', '.quick_comment_add', function() {
            siteHelpers.quickCommentAdd($(this));
        });
        $(document).on('change', '.quickCategory', function() {
            siteHelpers.changeQuickCategory($(this));
        });
        $(document).on('change', '.quickCommentEmail', function() {
            siteHelpers.changeQuickComment($(this));
        });
        $(document).on('change', '.quickSubCategory', function() {
            siteHelpers.changeQuickSubCategory($(this));
        });

        $(document).on('click', '.expand-row-msg', function() {
            var name = $(this).data('name');
            var id = $(this).data('id');
            var full = '.expand-row-msg .show-short-' + name + '-' + id;
            var mini = '.expand-row-msg .show-full-' + name + '-' + id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });
    </script> --}}
@endsection
