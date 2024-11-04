
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{asset('tickets.css')}}">

@php($users = $query = \App\User::get())

@include('partials.flash_messages')
    <div class="row tickets m-0">
        <div class="col-lg-12 margin-tb pr-0 pl-0">
        <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">{{ (isset($title)) ? ucfirst($title) : "Tickets"}} (<span id="list_count">{{ $data->total() }}</span>)
                <div class="margin-tb" style="flex-grow: 1;">
                    <div class="pull-right ">
                        <button style="background: #fff;color: #757575;border: 1px solid #ccc;" type="button" class="btn btn-secondary mr-2" data-toggle="modal" data-target="#AddStatusModal">Add Status</button>
                        <button style="background: #fff;color: #757575;border: 1px solid #ccc;" type="button" class="btn btn-secondary mr-2" data-toggle="modal" data-target="#ltdatatablecolumnvisibilityList">Column Visiblity</button>
                    </div>
                </div>
            </h2>
        </div>
        <div class="col-lg-12 margin-tb pl-3">
            <div class="form-group mb-3">
                <div class="row">
                    <div class="col-md-2 pr-0 mb-3">
                        <select class="form-control globalSelect21"  name="users_id" id="users_id">
                           
                            @foreach($users as $key => $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 pl-3  pr-0">
                        <select class="form-control globalSelect22" name="ticket_id" id="ticket">
                            
                            @foreach($data as $key => $ticket)
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
                            
                            @foreach($data as $key => $user_name)
                                <option value="{{ $user_name->name }}">{{ $user_name->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <select class="form-control globalSelect25" name="user_email" id="user_email">
                            
                            @foreach($data as $key => $user_email)
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

        </div>

    </div>

    <div class="space-right chat-list-table">

        <div class="infinite-scroll" style="overflow-y: auto">
            <table class="table table-bordered table-striped" style="font-size: 14px;">
                <thead>
                <tr>
                    @if(!empty($dynamicColumnsToShowLt))
                        @if (!in_array('Checkbox', $dynamicColumnsToShowLt))
                            <th></th>
                        @endif
                        @if (!in_array('Id', $dynamicColumnsToShowLt))
                            <th>Id</th>
                        @endif
                        @if (!in_array('Source', $dynamicColumnsToShowLt))
                            <th>Source</th>
                        @endif
                        @if (!in_array('Name', $dynamicColumnsToShowLt))
                            <th>Name</th>
                        @endif
                        @if (!in_array('Email', $dynamicColumnsToShowLt))
                            <th>Email</th>
                        @endif
                        @if (!in_array('Subject', $dynamicColumnsToShowLt))
                            <th>Subject</th>
                        @endif
                        @if (!in_array('Message', $dynamicColumnsToShowLt))
                            <th>Message</th>
                        @endif
                        @if (!in_array('Asg name', $dynamicColumnsToShowLt))
                            <th>Asg name</th>
                        @endif
                        @if (!in_array('Brand', $dynamicColumnsToShowLt))
                            <th>Brand</th>
                        @endif
                        @if (!in_array('Country', $dynamicColumnsToShowLt))
                            <th>Country</th>
                        @endif
                        @if (!in_array('Ord no', $dynamicColumnsToShowLt))
                            <th>Ord no</th>
                        @endif
                        @if (!in_array('Ph no', $dynamicColumnsToShowLt))
                            <th>Ph no</th>
                        @endif
                        @if (!in_array('Msg Box', $dynamicColumnsToShowLt))
                            <th>Msg Box</th>
                        @endif
                        @if (!in_array('Images', $dynamicColumnsToShowLt))
                            <th>Images</th>
                        @endif
                        @if (!in_array('Resolution Date', $dynamicColumnsToShowLt))
                            <th>Resolution Date</th>
                        @endif
                        @if (!in_array('Status', $dynamicColumnsToShowLt))
                            <th>Status</th>
                        @endif
                        @if (!in_array('Created', $dynamicColumnsToShowLt))
                            <th>Created</th>
                        @endif
                        @if (!in_array('Shortcuts', $dynamicColumnsToShowLt))
                            <th>Shortcuts</th>
                        @endif
                        @if (!in_array('Action', $dynamicColumnsToShowLt))
                            <th>Action</th>
                        @endif
                    @else
                        <th></th>
                        <th>Id</th>
                        <th>Source</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Asg name</th>
                        <th>Brand</th>
                        <th>Country</th>
                        <th>Ord no</th>
                        <th>Ph no</th>
                        <th>Msg Box</th>
                        <th>Images</th>
                        <th>Resolution Date</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Shortcuts</th>
                        <th>Action</th>
                    @endif
                </tr>
                </thead>
                <tbody id="content_data" class="infinite-scroll-pending-inner">
                    @include('livechat.partials.ticket-list')
                </tbody>
            </table>
        </div>


    </div>

    @include("livechat.partials.column-visibility-modal")
    @include('livechat.partials.model-email')
    @include('livechat.partials.model-assigned')
    @include('livechat.partials.modal_ticket_send_option')
    @include('livechat.partials.modal-status-color')
    @include('partials.modals.create-sop-shortcut')

    <div id="AddStatusModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
            <div class="modal-content">
               <div class="modal-header">
                    <h4 class="modal-title">Add Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>

               <form action="{{ route('tickets.add.status') }}"  method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">

                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Status</strong>
                            <input type="text" name="name"  class="form-control"  required>
                        </div>
                    </div>

                    <div class="modal-footer">
                         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-secondary">Send</button>
                    </div>
               </form>
          </div>

        </div>
    </div>

    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="send-message-text-box" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
               <form action="{{ route('task.send-brodcast') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="task_id" id="hidden-task-id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Send Brodcast Message</h4>
                    </div>
                    <div class="modal-body" style="background-color: #999999;">
                        @csrf
                        <div class="form-group">
                            <label for="document">Message</label>
                            <textarea class="form-control message-for-brodcast" name="message" placeholder="Enter your message"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-send-brodcast-message">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="model_email" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width:1000px">
            <div class="modal-content"  style="width:1000px">
                <div class="modal-header">
                    <h4 class="modal-title">Email</h4>
                </div>
                <div class="modal-body" id="model_email_txt">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
              50% 50% no-repeat;display:none;">
    </div>

     <div id="ticketsEmails" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title">Emails sent</h4>
                    </div>
                    <div class="modal-body" >
                        <div class="table-responsive" style="margin-top:20px;">
                            <table class="table table-bordered text-nowrap" style="border: 1px solid #ddd;" id="email-table">
                                <thead>
                                  <tr>
                                    <th>Bulk <br> Action</th>
                                    <th>Date</th>
                                    <th>Sender</th>
                                    <th>Receiver</th>
                                    <th>Mail <br> Type</th>
                                    <th>Subject</th>
                                    <th>Body</th>
                                    <th>Status</th>
                                    <th>Draft</th>
                                    <th>Action</th>
                                  </tr>
                                </thead>
                                <tbody id="ticketEmailData">

                                </tbody>
                              </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>
<div id="viewMore" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View More</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <p><span id="more-content"></span> </p>
            </div>
        </div>
    </div>
</div>
<div id="viewMail" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View Email</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <p><strong>Subject : </strong> <span id="emailSubject"></span> </p>
              <p><strong>Message : </strong> <span id="emailMsg"></span> </p>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
$( document ).ready(function() {
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

    /*$('.globalSelect21').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect22').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect23').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect24').val($('.globalSelect21 option:eq(1)').val()).trigger('change');
    $('.globalSelect25').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect26').val($('option:eq(1)').val()).trigger('change');*/

    $("#user_email option").each(function() {
        $(this).siblings('[value="'+ this.value +'"]').remove();
    });
    $("#term option").each(function() {
        $(this).siblings('[value="'+ this.value +'"]').remove();
    });
});

function opnMsg(email) {
      console.log(email);
      $('#emailSubject').html(email.subject);
      $('#emailMsg').html(email.message);

      // Mark email as seen as soon as its opened
      if(email.seen ==0 || email.seen=='0'){
        // Mark email as read
        var $this = $(this);
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: '/email/'+email.id+'/mark-as-read',
              type: 'put'
            }).done( function(response) {

            }).fail(function(errObj) {

            });
      }

    }


        function getDocumentHeight() {
            const body = document.body;
            const html = document.documentElement;

            return Math.max(
                body.scrollHeight, body.offsetHeight,
                html.clientHeight, html.scrollHeight, html.offsetHeight
            );
        };

        function showEmails(ticketId) {
            $('#ticketEmailData').html('');
            $.get(window.location.origin+"/tickets/emails/"+ticketId, function(data, status){
                $('#ticketEmailData').html(data);
                $('#ticketsEmails').modal('show');
            });
        }
        function opnModal(message){
          $(document).find('#more-content').html(message);
        }
        $(document).on('click', '.resend-email-btn', function(e) {
            e.preventDefault();
            var $this = $(this);
            var type = $(this).data('type');
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: '/email/resendMail/'+$this.data("id"),
              type: 'post',
              data: {
                type:type
              },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done( function(response) {
              toastr['success'](response.message);
              $("#loading-image").hide();
            }).fail(function(errObj) {
              $("#loading-image").hide();
            });
        });


    $(document).on('click', '.row-ticket', function () {
        /*DEVTASK-22731-START*/
        ticket_id = $(this).data('ticket-id');
        getTicketData(ticket_id);
        $('#viewmorechatmessages').modal("show");
        /*DEVTASK-22731-END*/
    });


   $(document).on('click', '.send-email-to-vender', function () {

            $('#subject').val($(this).data('subject'));
            $('#to_email').val($(this).data('email'));
            $('#emailModal').find('form').find('input[name="ticket_id"]').val($(this).data('id'));
            $('#emailModal').modal("show");
        });
    $(document).on('click', '.btn-assigned-to-ticket', function () {

        $('#assignedModal').find('form').find('input[name="id"]').val($(this).data('id'));
        $('#assignedModal').modal("show");

    });

    /*DEVTASK-22731-START*/
    $(document).on('click', '.editTicket', function () {
        id = $(this).data('id');
        $("#spanMsg_"+id).css('display','none');
        $("#inputMsg_"+id).css('display','inline-block');
        $("#txtMsg_"+id).css('display','inline-block');

        $('#editTicket_'+id).css('display','none');
        $("#updateTicket_"+id).css('display','inline-block');
    });

    
    $(document).on('click', '.approveTicket', function () {
        ticket_id = $(this).data('ticket-id');
        id = $(this).data('id');
        src = "{{url('livechat/tickets/approve-ticket')}}";
        Swal.fire({
            title: 'Do you want to send message to ticket?',
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: 'Yes',
                denyButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method : "POST",
                        url : src,
                        data : {"id":id,"ticket_id":ticket_id},
                        dataType : "json",
                        success : function(response){
                            getTicketData(ticket_id);
                        },error : function(error){
                            console.log(error);
                        }
                    });
                } else if (result.isDenied) {
                    
                }
            });
    });

    $(document).on('click', '.updateTicket', function () {
        ticket_id = $(this).data('ticket-id');
        message = $("#txtMsg_"+id).val();
        id = $(this).data('id');
        src = "{{url('livechat/tickets/update-ticket')}}";
        $.ajax({
            method : "POST",
            url : src,
            data : {"id":id,"ticket_id":ticket_id,"message":message},
            dataType : "json",
            success : function(response){
                getTicketData(ticket_id);
                $("#spanMsg_"+id).css('display','table-cell');
                $("#inputMsg_"+id).css('display','none');
                $('#editTicket_'+id).css('display','inline-block');
                $("#updateTicket_"+id).css('display','none');
            },error : function(error){
                console.log(error);
            }
        });
    });
    /*DEVTASK-22731-END*/

   $('#filter_date').datetimepicker({
        format: 'YYYY-MM-DD'
    });

   /*DEVTASK-22731-START*/
    function getTicketData(ticket_id){
        alert('l');
        html = '';
        button = '';
        src = "{{url('livechat/tickets/ticket-data')}}";
        $.ajax({
            method : "POST",
            url : src,
            data : {"ticket_id":ticket_id},
            dataType : "json",
            success : function(response){
                if(response.count > 0) {
                    $("#viewmorechatmessages").find(".modal-dialog").css({"width":"1000px","max-width":"1000px"});
                    $("#viewmorechatmessages").find(".modal-body").css({"background-color":"white"});
                    $.each(response.data, function(k, v) {
                        button = '';

                        if(v['out'] == true) {
                            html += '<tr style="background-color:grey !important">';
                        } else {
                            html += '<tr style="background-color:#999999 !important">';
                        }
                        html += '<td style="width:50%"><span class="copy_message'+v['id']+'"" id="spanMsg_'+v['id']+'">'+v['message']+'</span> <p id="inputMsg_'+v['id']+'"><input type="text" style="display:none" id="txtMsg_'+v['id']+'" value="'+v['message']+'"></p>'+((v['message_score']) ? 'Score: '+ v['message_score'] : '' )+'</td>';

                        if (!v['approved'] && v['out'] == true) {

                            button += "<a href='javascript:void(0)' title='Edit Message' id='editTicket_"+v['id']+"' data-id='"+v['id']+"' class='btn btn-xs btn-secondary editTicket'><i class='fa fa-edit' aria-hidden='true'></i></a> ";

                            button += "<a href='javascript:void(0)' title='Update Message' style='display:none' id='updateTicket_"+v['id']+"' data-id='"+v['id']+"' data-ticket-id='"+ticket_id+"' class='btn btn-xs btn-secondary updateTicket'><i class='fa fa-save' aria-hidden='true'></i></a>";

                            button += "<a href='javascript:void(0)' id='approveTicket_"+v['id']+"' title='Send Message To Ticket' data-id='"+v['id']+"' data-ticket-id='"+ticket_id+"' class='btn btn-xs btn-secondary approveTicket'><i class='fa fa-check' aria-hidden='true'></i></a>";

                            button += "<button title='Approve' class='btn btn-xs btn-secondary btn-approve ml-3' data-messageid='" + v['id'] + "'><i class='fa fa-thumbs-up' aria-hidden='true'></i></button>";
                        }

                        if (v['status'] == 0 || v['status'] == 5 || v['status'] == 6) {

                            if (v['status'] == 0) {
                                button += "<a title='Mark as Read' href='javascript:;' data-url='/whatsapp/updatestatus?status=5&id=" + v['id'] + "' class='btn btn-xs btn-secondary ml-1 change_message_status'><i class='fa fa-check' aria-hidden='true'></i></a>";
                            }

                            if (v['status'] == 0 || v['status'] == 5) {
                                button += '<a href="javascript:;" style="padding:4px!important;" title="Mark as Replied" data-url="/whatsapp/updatestatus?status=6&id=' + v['id'] + '" class="btn btn-xs btn-secondary ml-1 change_message_status"> <img src="/images/2.png" /> </a>';
                            }

                            button += '&nbsp;<button title="forward"  class="btn btn-secondary btn-xs forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="' + v['id'] + '"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>&nbsp;<button title="Resend" data-id="'+v['id']+'" class="btn btn-xs btn-secondary resend-message-js"><i class="fa fa-repeat" aria-hidden="true"></i></button>';

                        } else {
                            if (v['error_status'] == 1) {
                                button +="<a href='javascript:;' class='btn btn-xs btn-image fix-message-error' data-id='" + v['id'] + "'><img src='/images/flagged.png' /></a><a href='#' title='Resend' class='btn btn-xs btn-secondary ml-1 resend-message-js' data-id='" + v['id'] + "'><i class='fa fa-repeat' aria-hidden='true'></i></a>";
                            } else if (v['error_status'] == 2) {
                                button += "<a href='javascript:;' class='btn btn-xs btn-secondary btn-image fix-message-error' data-id='" + v['id'] + "'><img src='/images/flagged.png' /><img src='/images/flagged.png' /></a><a title='Resend' href='#' class='btn btn-xs btn-secondary ml-1 resend-message-js' data-id='" + v['id'] + "'><i class='fa fa-repeat' aria-hidden='true'></i></a>";
                            }

                            button += '&nbsp;<button title="Forward" class="btn btn-xs btn-secondary forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="' + v['id'] + '"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>&nbsp;<button title="Resend" data-id="'+v['id']+'" class="btn btn-xs btn-secondary resend-message"><i class="fa fa-repeat" aria-hidden="true"></i></button>';
                        }

                        button += '<a title="Dialog" href="javascript:;" class="btn btn-xs btn-secondary ml-1 create-dialog"><i class="fa fa-plus" aria-hidden="true"></i></a>';

                        if(v['is_reviewed'] != 1) {
                            button += '&nbsp;<button title="Mark as reviewed" class="btn btn-xs btn-secondary review-btn" data-id="' + v['id'] + '"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
                        }


                        button += '<a title="Add Sop" href="javascript:;" data-toggle="modal" data-target="#create-sop-shortcut" class="create-sop-shortcut-modal btn btn-xs btn-secondary ml-1 create_short_cut" data-category="'+v['sop_category']+'" data-name="'+v['sop_name']+'" data-message="'+v['sop_content']+'" data-id="' + v['id'] + '" data-msg="'+v['message']+'"><i class="fa fa-asterisk" data-message="'+v['message']+'" aria-hidden="true"></i></a>';


                        button += '<a title="Remove" href="javascript:;" class="btn btn-xs btn-secondary ml-1 delete-message" data-id="' + v['id'] + '"><i class="fa fa-trash" aria-hidden="true"></i></a>';

                        button += '<a title="Copy Messages" href="javascript:;" class="btn btn-xs btn-secondary ml-1 btn-copy-messages" onclick="CopyToClipboard('+v['id']+')" data-message="'+v['message']+'" data-id="' + v['id'] + '"><i class="fa fa-copy" aria-hidden="true"></i></a>';

                        html += '<td style="width:30%">'+button+'</td>';

                        html += '<td style="width:40%">'+v['datetime']+'</td>';
                        html += '</tr>';

                    });
                } else {
                    html += '<tr>';
                    html += '<td colspan="4">No Records Found</td>';
                    html += '</tr>';
                }
                $("#ticketData").html(html);
            },error : function(error){
                console.log(error);
            }
        });
    }
    /*DEVTASK-22731-END*/

    function submitSearch(page = 1) {
           //src = "{{url('whatsapp/pollTicketsCustomer')}}";
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
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done(function (message) {
        $("#loading-image").hide();
        $('#ticket').val(ticket_id);
        $('#content_data').html(message.tbody);
        $('#pagination-container').html(message.links);
        var rendered = renderMessage(message, tobottom);

        // Update the URL in the browser's address bar
        history.pushState(null, null, src + '?page=' + page);
    }).fail(function (jqXHR, ajaxOptions, thrownError) {
        alert('No response from server');
    });
}

    function resetSearch(){
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


    $(document).on('click', '.add-cc', function (e) {
            e.preventDefault();

            if ($('#cc-label').is(':hidden')) {
                $('#cc-label').fadeIn();
            }

            var el = `<div class="row cc-input">
            <div class="col-md-10">
                <input type="text" name="cc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image cc-delete-button"><img src="{{asset('images/delete.png')}}"></button>
            </div>
        </div>`;

            $('#cc-list').append(el);
        });

        $(document).on('click', '.cc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.cc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#cc-label').fadeOut();
                }
            });
        });

        // bcc

        $(document).on('click', '.add-bcc', function (e) {
            e.preventDefault();

            if ($('#bcc-label').is(':hidden')) {
                $('#bcc-label').fadeIn();
            }

            var el = `<div class="row bcc-input">
            <div class="col-md-10">
                <input type="text" name="bcc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image bcc-delete-button"><img src="{{asset('images/delete.png')}}"></button>
            </div>
        </div>`;

            $('#bcc-list').append(el);
        });

        $(document).on('click', '.bcc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.bcc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#bcc-label').fadeOut();
                }
            });
        });

        function resolveIssue(obj, task_id) {
            let id = task_id;
            let status = $(obj).val();
            let self = this;


            $.ajax({
                url: "{{ route('tickets.status.change')}}",
                method: "POST",
                data: {
                    id: id,
                    status: status
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function () {
                    toastr["success"]("Status updated!", "Message")
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                }
            });
        }

        function changeDate(obj, ticket_id) {
            let id = ticket_id;
            let date = $(obj).val();
            let self = this;

            $.ajax({
                url: "{{ route('tickets.date.change')}}",
                method: "POST",
                data: {
                    id: id,
                    date: date
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function () {
                    toastr["success"]("Date updated!", "Message")
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                }
            });
        }
        $(document).on('click', '.send-message1', function () {

           var thiss = $(this);
           var data = new FormData();
           var ticket_id = $(this).data('ticketid');
           var message = $("#messageid_"+ticket_id).val();
           if(message!=""){
               $("#message_confirm_text").html(message);
               $("#confirm_ticket_id").val(ticket_id);
               $("#confirm_message").val(message);
               $("#confirm_status").val(1);
               $("#confirmMessageModal").modal();
           }
       });
       $(document).on('click', '.confirm-messge-button', function () {
            var thiss = $(this);
            var data = new FormData();
            var ticket_id = $("#confirm_ticket_id").val();
            var message = $("#confirm_message").val();
            var status = $("#confirm_status").val();

            data.append("ticket_id", ticket_id);
            data.append("message", message);
            data.append("status", 1);

            var checkedValue = [];
            var i=0;
            $('.send_message_recepients:checked').each(function () {
                checkedValue[i++] = $(this).val();
            });
            data.append("send_ticket_options",checkedValue);

            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/ticket',
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
                        //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                        $('#confirmMessageModal').modal('hide');
                        if(message.length > 30)
                        {
                            var res_msg = message.substr(0, 27)+"...";
                            $("#message-chat-txt-"+ticket_id).html(res_msg);
                            $("#message-chat-fulltxt-"+ticket_id).html(message);
                        }
                        else
                        {
                            $("#message-chat-txt-"+ticket_id).html(message);
                            $("#message-chat-fulltxt-"+ticket_id).html(message);
                        }

                        $("#messageid_"+ticket_id).val('');

                        $(thiss).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(thiss).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });


        $(document).on('click', '.send-message1_bk', function () {
            var thiss = $(this);
            var data = new FormData();
            var ticket_id = $(this).data('ticketid');
            var message = $("#messageid_"+ticket_id).val();
            data.append("ticket_id", ticket_id);
            data.append("message", message);
            data.append("status", 1);
            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/ticket',
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
                        //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                        if(message.length > 30)
                        {
                            var res_msg = message.substr(0, 27)+"...";
                            $("#message-chat-txt-"+ticket_id).html(res_msg);
                            $("#message-chat-fulltxt-"+ticket_id).html(message);
                        }
                        else
                        {
                            $("#message-chat-txt-"+ticket_id).html(message);
                            $("#message-chat-fulltxt-"+ticket_id).html(message);
                        }

                        $("#messageid_"+ticket_id).val('');

                        $(thiss).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(thiss).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });

      $(document).on("click","#send-message",function() {
         $("#send-message-text-box").modal("show");
      });

      $(".btn-send-brodcast-message").on("click",function () {

            var selected_tasks = [];

            $.each($(".selected-ticket-ids:checked"),function(k,v) {
                selected_tasks.push($(v).val());
            });

            if (selected_tasks.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('tickets/send-brodcast') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        selected_tasks: selected_tasks,
                        message : $(".message-for-brodcast").val()
                    },
                    beforeSend : function() {
                        $("#loading-image").show();
                    }
                }).done(function (response) {
                    $("#loading-image").hide();
                    if(response.code == 200) {
                        toastr["success"](response.message);
                        $("#send-message-text-box").modal("hide");
                    }else{
                        toastr["error"](response.message);
                    }
                }).fail(function (response) {
                    $("#loading-image").hide();
                    console.log(response);
                    toastr["error"]("Request has been failed due to the server , please contact administrator");
                });
            } else {
                $("#loading-image").hide();
                toastr["error"]("Please select atleast 1 task!");
            }
        });

</script>
<script>
    $(document).on("click","#softdeletedata",function() {

       var id = $(this).data("id");

       if(confirm('Do you really want to delete this record'))
       {
           $.ajax({
               type: "POST",
               url: "{{ url('tickets/delete_tickets/') }}",
               data: {
                   _token: "{{ csrf_token() }}",
                   id:id,
               },
               beforeSend : function() {
                   $("#loading-image").show();
               }
           }).done(function (response) {
               toastr["success"](response.message);
               $("#loading-image").hide();
               location.reload();

           }).fail(function (response) {
               $("#loading-image").hide();

           });
       }
   });
</script>
<script>
 function message_show(t)
 {
    $('#model_email_txt ').html($(t).data('content'));
    $("#model_email").modal("show");
 }

  $(document).on('click', '.resend-email-btn', function(e) {
      e.preventDefault();
      var $this = $(this);
      var type = $(this).data('type');
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/resendMail/'+$this.data("id"),
          type: 'post',
          data: {
            type:type
          },
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          toastr['success'](response.message);
          $("#loading-image").hide();
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });
    /*DEVTASK-22731-START*/
    $(document).on('click', '.create_short_cut',function () {
            $('.sop_description').val("");
            let message = '';
            message = $(this).attr('data-msg');
            $('.sop_description').val(message);
        });
    /*DEVTASK-22731-START*/

    // It is used to collapse action menu on right side of table
    function Ticketsbtn(id){
        $(".action-ticketsbtn-tr-"+id).toggleClass('d-none')
    }

    function Shortcutsbtn(id){
        $(".action-shortcuts-tr-"+id).toggleClass('d-none')
    }

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

<script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
<script type="text/javascript">
var siteHelpers = {
            
    quickCategoryAdd : function(ele) {
        var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
        var quickCategoryId = quickCategory.children("option:selected").data('id');
        var textBox = ele.closest("div").find(".quick_category");
        if (textBox.val() == "") {
            alert("Please Enter Category!!");
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                name : textBox.val(),
                quickCategoryId : quickCategoryId
            },
            url: "/add-reply-category"
        };

        if(quickCategoryId!=''){
            siteHelpers.sendAjax(params,"afterQuickSubCategoryAdd");
        } else {
            siteHelpers.sendAjax(params,"afterQuickCategoryAdd");
        }
    },
    afterQuickSubCategoryAdd : function(response) {
        $(".quick_category").val('');
        $(".quickSubCategory").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
    },
    afterQuickCategoryAdd : function(response) {
        $(".quick_category").val('');
        $(".quickCategory").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
    },
    deleteQuickCategory : function(ele) {
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
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                id : quickCategoryId
            },
            url: "/destroy-reply-category"
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    deleteQuickSubCategory : function(ele) {
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
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                id : quickSubCategoryId
            },
            url: "/destroy-reply-category"
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    deleteQuickComment : function(ele) {
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
    quickCommentAdd : function(ele) {
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
            method : 'post',
            data : formData,
            url: "/reply"
        };
        siteHelpers.sendFormDataAjax(params,"afterQuickCommentAdd");
    },
    afterQuickCommentAdd : function(reply) {
        $(".quick_comment").val('');
        $('.quickCommentEmail').append($('<option>', {
            value: reply,
            text: reply
        }));
    },
    changeQuickCategory : function (ele) {

        var selectedOption = ele.find('option:selected');
        var dataValue = selectedOption.data('value');

        ele.closest("#shortcutsIds").find('.quickSubCategory').empty();
        ele.closest("#shortcutsIds").find('.quickSubCategory').append($('<option>', {
            value: '',
            text: 'Select Sub Category'
        }));
        dataValue.forEach(function (category) {
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
            replies.forEach(function (reply) {
                ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                    value: reply.reply,
                    text: reply.reply,
                    'data-id': reply.id
                }));
            });
        }
    },
    changeQuickComment : function (ele) {
        $('#messageid_'+ele.attr('data-id')).val(ele.val());

        var userEmaillUrl = '/email/email-frame-info/'+$('#reply_email_id').val();;
        var senderName = 'Hello '+$('#sender_email_address').val().split('@')[0]+',';

        $("#reply-message").val(senderName)

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: userEmaillUrl,
            type: 'get',
        }).done( function(response) {
            $("#reply-message").val(senderName+'\n\n'+ele.val()+'\n\n'+response)
        }).fail(function(errObj) {
        })
        
    },
    changeQuickSubCategory : function (ele) {
        var selectedOption = ele.find('option:selected');
        var dataValue = selectedOption.data('id');

        var userEmaillUrl = '/livechat-replise/'+dataValue;

        $.ajax({        
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: userEmaillUrl,
            type: 'get',
        }).done( function(response) {

            if(response!=''){
                var replies = JSON.parse(response);
                ele.closest("#shortcutsIds").find('.quickCommentEmail').empty();
                ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                    value: '',
                    text: 'Quick Reply'
                }));
                replies.forEach(function (reply) {
                    ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                        value: reply.reply,
                        text: reply.reply,
                        'data-id': reply.id
                    }));
                });
            }
            
        }).fail(function(errObj) {
        })
    },
};

$.extend(siteHelpers, common);

$(document).on('click', '.quick_category_add', function () {
    siteHelpers.quickCategoryAdd($(this));
});
$(document).on('click', '.delete_category', function () {
    siteHelpers.deleteQuickCategory($(this));
});
$(document).on('click', '.delete_sub_category', function () {
    siteHelpers.deleteQuickSubCategory($(this));
});
$(document).on('click', '.delete_quick_comment', function () {
    siteHelpers.deleteQuickComment($(this));
});
$(document).on('click', '.quick_comment_add', function () {
    siteHelpers.quickCommentAdd($(this));
});
$(document).on('change', '.quickCategory', function () {
    siteHelpers.changeQuickCategory($(this));
});
$(document).on('change', '.quickCommentEmail', function () {
    siteHelpers.changeQuickComment($(this));
});
$(document).on('change', '.quickSubCategory', function () {
    siteHelpers.changeQuickSubCategory($(this));
});

$(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
});
</script>
@endsection