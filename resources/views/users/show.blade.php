@extends('layouts.app')

@section('styles')
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">

  <style>
      .inbox_people {
          background: #f8f8f8 none repeat scroll 0 0;
          float: left;
          overflow: hidden;
          width: 40%; border-right:1px solid #c4c4c4;
      }
      .inbox_msg {
          border: 1px solid #c4c4c4;
          clear: both;
          overflow: hidden;
      }
      .top_spac{ margin: 20px 0 0;}


      .recent_heading {float: left; width:40%;}
      .srch_bar {
          display: inline-block;
          text-align: right;
          width: 60%; padding:
      }
      .headind_srch{ padding:10px 29px 10px 20px; overflow:hidden; border-bottom:1px solid #c4c4c4;}

      .recent_heading h4 {
          color: #3595d7;
          font-size: 21px;
          margin: auto;
      }
      .srch_bar input{ border:1px solid #cdcdcd; border-width:0 0 1px 0; width:80%; padding:2px 0 4px 6px; background:none;}
      .srch_bar .input-group-addon button {
          background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
          border: medium none;
          padding: 0;
          color: #707070;
          font-size: 18px;
      }
      .srch_bar .input-group-addon { margin: 0 0 0 -27px;}

      .chat_ib h5{ font-size:15px; color:#464646; margin:0 0 8px 0;}
      .chat_ib h5 span{ font-size:13px; float:right;}
      .chat_ib p{ font-size:14px; color:#989898; margin:auto}
      .chat_img {
          float: left;
          width: 11%;
      }
      .chat_ib {
          float: left;
          padding: 0 0 0 15px;
          width: 88%;
      }

      .chat_people{ overflow:hidden; clear:both;}
      .chat_list {
          border-bottom: 1px solid #c4c4c4;
          margin: 0;
          padding: 18px 16px 10px;
      }
      .inbox_chat { height: 550px; overflow-y: scroll;}

      .active_chat{ background:#ebebeb;}

      .incoming_msg_img {
          display: inline-block;
          width: 6%;
      }
      .received_msg {
          display: inline-block;
          padding: 0 0 0 10px;
          vertical-align: top;
          width: 92%;
      }
      .received_withd_msg p {
          background: #ebebeb none repeat scroll 0 0;
          border-radius: 3px;
          color: #646464;
          font-size: 14px;
          margin: 0;
          padding: 5px 10px 5px 12px;
          width: 100%;
      }
      .time_date {
          color: #747474;
          display: block;
          font-size: 12px;
          margin: 8px 0 0;
      }
      .received_withd_msg { width: 57%;}
      .mesgs {
          padding: 30px 15px 10px 25px;
          width: 100%;
          background: #F9F9F9;
          margin-bottom: 50px;
      }

      .sent_msg p {
          background: #3595d7 none repeat scroll 0 0;
          border-radius: 3px;
          font-size: 14px;
          margin: 0; color:#fff;
          padding: 5px 10px 5px 12px;
          width:100%;
      }
      .outgoing_msg{ overflow:hidden; margin:26px 0 26px;}
      .sent_msg {
          float: right;
          width: 46%;
      }
      .input_msg_write input {
          background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
          border: medium none;
          color: #4c4c4c;
          padding: 15px 2px;
          font-size: 15px;
          min-height: 48px;
          outline: none !important;
          width: 100%;
      }

      .type_msg {border-top: 1px solid #c4c4c4;position: relative;}
      .msg_send_btn {
          background: #3595d7 none repeat scroll 0 0;
          border: medium none;
          border-radius: 50%;
          color: #fff;
          cursor: pointer;
          font-size: 17px;
          height: 33px;
          position: absolute;
          right: 0;
          top: 11px;
          width: 33px;
      }
      .messaging { padding: 0 0 50px 0;}
      .msg_history {
          height: 516px;
          overflow-y: auto;
          padding-bottom: 15px;
      }

      .remove-screenshot {
        position: absolute;
        top: 0px;
        right: 0px;
      }

      .floating-arrows {
        position: fixed;
        z-index: 9;
        top: 50%;
      }

      .floating-arrows.left {
        left: 20px;
      }

      .floating-arrows.right {
        right: 20px;
      }

      #message-wrapper {
        height: 450px;
        overflow-y: scroll;
      }

      .show-images-wrapper {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
      }
  </style>
@endsection

@section('content')


<div class="row">
  <div class="col-lg-12 margin-tb">
    <div class="pull-left">
      <h3>User Page</h3>
    </div>
    <div class="pull-right mt-4">      
    </div>
  </div>
</div>

@include('partials.flash_messages')

<div class="row">
  <div class="col-xs-12 col-md-4 border">
    {{ html()->modelForm($user, 'PATCH', route('users.update', [$user->id]))->class('my-3')->open() }}
      <div class="form-group">
        {{ html()->text('name')->placeholder('Name')->class('form-control input-sm') }}
      </div>

      <div class="form-group">
        {{ html()->text('email')->placeholder('Email')->class('form-control input-sm') }}
      </div>

      <div class="form-group">
        <input type="number" class="form-control input-sm" name="phone" placeholder="Phone Number" value="{{ $user->phone }}" />
        @if ($errors->has('phone'))
          <div class="alert alert-danger">{{$errors->first('phone')}}</div>
        @endif
      </div>

      <div class="form-group">
        <select name="whatsapp_number" class="form-control input-sm">
          <option value>Whatsapp Number</option>
          <option value="971569119192" {{ '971569119192' == $user->whatsapp_number ? ' selected' : '' }}>971569119192 Indian</option>
          <option value="971502609192" {{ '971502609192' == $user->whatsapp_number ? ' selected' : '' }}>971502609192 Dubai</option>          
        </select>

        @if ($errors->has('whatsapp_number'))
          <div class="alert alert-danger">{{$errors->first('whatsapp_number')}}</div>
        @endif
      </div>

      <div class="form-group">
        {{ html()->password('password')->attribute('placeholder', 'Password')->class('form-control input-sm') }}
      </div>

      <div class="form-group">
        {{ html()->password('confirm-password')->attribute('placeholder', 'Confirm Password')->class('form-control input-sm') }}
      </div>

      <div class="form-group">
        <strong>Role:</strong>
        {{ html()->multiselect('roles[]', $roles, $userRole)->class('form-control input-sm') }}
      </div>

      <div class="form-group">
        <strong>Agent Role:</strong>
        {{ html()->multiselect('agent_role[]', $agent_roles, $user_agent_roles)->class('form-control input-sm') }}
      </div>

      <div class="form-group">
        <select name="responsible_user" class="form-control input-sm">
          <option value="">Select Responsible User</option>
          @foreach($users as $useritem)
            <option value="{{$useritem->id}}" {{ $useritem->id == $user->responsible_user ? 'selected' : '' }}>{{$useritem->name}}</option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="btn btn-xs btn-secondary">Save</button>
    {{ html()->closeModelForm() }}
  </div>



  <div class="col-xs-12 col-md-4 mb-3">
    <div class="border">
      <form action="{{ route('task.assign.messages') }}" method="POST">
        @csrf

        <input type="hidden" name="selected_messages" id="selected_messages" value="">

        <div class="form-group">
          <select class="form-control input-sm" name="task_id" required>
            <option value="">Select Task</option>

            @foreach ($pending_tasks as $task)
              <option value="{{ $task->id }}">{{ $task->id }} from {{ $users_array[$task->assign_from] }} {{ $task->task_subject }} {{ $task->task_details }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-xs btn-secondary" id="assignMessagesButton">Assign</button>
        </div>

      </form>      

    </div>
  </div>

  <div class="col-xs-12 col-md-4">
    <div class="border">      

      <div class="row">
        <div class="col-12 my-3" id="message-wrapper">
          <div id="message-container"></div>
        </div>

        <div class="col-xs-12 text-center hidden">
          <button type="button" id="load-more-messages" data-nextpage="1" class="btn btn-secondary">Load More</button>
        </div>
      </div>
    </div>
  </div>
</div>



@endsection

@section('scripts')  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>

  <script type="text/javascript">
    jQuery(document).ready(function( $ ) {
      $('.dropify').dropify();
    })

    var selected_product_images = [];

    $(document).on('click', '.select-product-image', function() {
      var checked = $(this).prop('checked');
      var id = $(this).data('id');

      if (checked) {
        selected_product_images.push(id);
      } else {
        var index = selected_product_images.indexOf(id);

        selected_product_images.splice(index, 1);
      }

      console.log(selected_product_images);
    });

    $('#date, #report-completion-datetime').datetimepicker({
      format: 'YYYY-MM-DD HH:mm'
    });

    $(document).on('click', ".collapsible-message", function() {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
        var short_message = $(this).data('messageshort');
        var message = $(this).data('message');
        var status = $(this).data('expanded');

        if (status == false) {
          $(this).addClass('expanded');
          $(this).html(message);
          $(this).data('expanded', true);
          // $(this).siblings('.thumbnail-wrapper').remove();
          $(this).closest('.talktext').find('.message-img').removeClass('thumbnail-200');
          $(this).closest('.talktext').find('.message-img').parent().css('width', 'auto');
        } else {
          $(this).removeClass('expanded');
          $(this).html(short_message);
          $(this).data('expanded', false);
          $(this).closest('.talktext').find('.message-img').addClass('thumbnail-200');
          $(this).closest('.talktext').find('.message-img').parent().css('width', '200px');
        }
      }
    });

        $(document).ready(function() {
        var container = $("div#message-container");
        var suggestion_container = $("div#suggestion-container");
        // var sendBtn = $("#waMessageSend");
        var erpUser = "{{ $user->id }}";
             var addElapse = false;
             function errorHandler(error) {
                 console.error("error occured: " , error);
             }
             function approveMessage(element, message) {
               if (!$(element).attr('disabled')) {
                 $.ajax({
                   type: "POST",
                   url: "/whatsapp/approve/user",
                   data: {
                     _token: "{{ csrf_token() }}",
                     messageId: message.id
                   },
                   beforeSend: function() {
                     $(element).attr('disabled', true);
                     $(element).text('Approving...');
                   }
                 }).done(function( data ) {
                   element.remove();
                   console.log(data);
                 }).fail(function(response) {
                   $(element).attr('disabled', false);
                   $(element).text('Approve');

                   console.log(response);
                   alert(response.responseJSON.message);
                 });
               }
             }

        function renderMessage(message, tobottom = null) {
            var domId = "waMessage_" + message.id;
            var current = $("#" + domId);
            var is_admin = "{{ Auth::user()->hasRole('Admin') }}";
            var is_hod_crm = "{{ Auth::user()->hasRole('HOD of CRM') }}";
            var users_array = @json($users_array);
            var leads_assigned_user = "";

            if ( current.get( 0 ) ) {
              return false;
            }

           // CHAT MESSAGES
           var row = $("<div class='talk-bubble'></div>");
           var body = $("<span id='message_body_" + message.id + "'></span>");
           var text = $("<div class='talktext'></div>");
           var edit_field = $('<textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea' + message.id + '" style="display: none;">' + message.message + '</textarea>');
           var p = $("<p class='collapsible-message'></p>");

           var forward = $('<button class="btn btn-image forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="' + message.id + '"><img src="/images/forward.png" /></button>');

           if (message.status == 0 || message.status == 5 || message.status == 6) {
             var meta = $("<em>" + users_array[message.user_id] + " " + moment(message.created_at).format('DD-MM H:mm') + " </em>");
             var mark_read = $("<a href data-url='/whatsapp/updatestatus?status=5&id=" + message.id + "' style='font-size: 9px' class='change_message_status'>Mark as Read </a><span> | </span>");
             var mark_replied = $('<a href data-url="/whatsapp/updatestatus?status=6&id=' + message.id + '" style="font-size: 9px" class="change_message_status">Mark as Replied </a>');

             // row.attr("id", domId);
             p.appendTo(text);

             // $(images).appendTo(text);
             meta.appendTo(text);

             if (message.status == 0) {
               mark_read.appendTo(meta);
             }

             if (message.status == 0 || message.status == 5) {
               mark_replied.appendTo(meta);
             }

             text.appendTo(row);

             if (tobottom) {
               row.appendTo(container);
             } else {
               row.prependTo(container);
             }

             forward.appendTo(meta);

           } else if (message.status == 4) {
             var row = $("<div class='talk-bubble' data-messageid='" + message.id + "'></div>");
             var chat_friend =  (message.assigned_to != 0 && message.assigned_to != leads_assigned_user && message.user_id != message.assigned_to) ? ' - ' + users_array[message.assigned_to] : '';
             var meta = $("<em>" + users_array[message.user_id] + " " + chat_friend + " " + moment(message.created_at).format('DD-MM H:mm') + " <img id='status_img_" + message.id + "' src='/images/1.png' /> &nbsp;</em>");

             // row.attr("id", domId);

             p.appendTo(text);
             $(images).appendTo(text);
             meta.appendTo(text);

             text.appendTo(row);
             if (tobottom) {
               row.appendTo(container);
             } else {
               row.prependTo(container);
             }
           } else {
             if (message.sent == 0) {
               var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message.user_id] : "Unknown") + " " + moment(message.created_at).format('DD-MM H:mm') + " </em>";
             } else {
               var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message.user_id] : "Unknown") + " " + moment(message.created_at).format('DD-MM H:mm') + " <img id='status_img_" + message.id + "' src='/images/1.png' /></em>";
             }

             var error_flag = '';
             if (message.error_status == 1) {
               error_flag = "<a href='#' class='btn btn-image fix-message-error' data-id='" + message.id + "'><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend</a>";
             } else if (message.error_status == 2) {
               error_flag = "<a href='#' class='btn btn-image fix-message-error' data-id='" + message.id + "'><img src='/images/flagged.png' /><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend</a>";
             }



             var meta = $(meta_content);

             edit_field.appendTo(text);

             if (!message.approved) {
                 var approveBtn = $("<button class='btn btn-xs btn-secondary btn-approve ml-3'>Approve</button>");
                 var editBtn = ' <a href="#" style="font-size: 9px" class="edit-message whatsapp-message ml-2" data-messageid="' + message.id + '">Edit</a>';
                 approveBtn.click(function() {
                     approveMessage( this, message );
                 } );
                 if (is_admin || is_hod_crm) {
                   approveBtn.appendTo( meta );
                   $(editBtn).appendTo( meta );
                 }
             }

             forward.appendTo(meta);

             $(error_flag).appendTo(meta);
           }

           row.attr("id", domId);

           p.attr("data-messageshort", message.message);
           p.attr("data-message", message.message);
           p.attr("data-expanded", "true");
           p.attr("data-messageid", message.id);
           // console.log("renderMessage message is ", message);
           if (message.message) {
             p.html(message.message);
           } else if (message.media_url) {
               var splitted = message.content_type.split("/");
               if (splitted[0]==="image" || splitted[0] === 'm') {
                   var a = $("<a></a>");
                   a.attr("target", "_blank");
                   a.attr("href", message.media_url);
                   var img = $("<img></img>");
                   img.attr("src", message.media_url);
                   img.attr("width", "100");
                   img.attr("height", "100");
                   img.appendTo( a );
                   a.appendTo( p );
                   // console.log("rendered image message ", a);
               } else if (splitted[0]==="video") {
                   $("<a target='_blank' href='" + message.media_url+"'>"+ message.media_url + "</a>").appendTo(p);
               }
           }

           var has_product_image = false;

           if (message.images) {
             var images = '';
             message.images.forEach(function (image) {
               images += image.product_id !== '' ? '<a href="/products/' + image.product_id + '" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Special Price: </strong>' + image.special_price + '<br><strong>Size: </strong>' + image.size + '<br><strong>Supplier: </strong>' + image.supplier_initials + '">' : '';
               images += '<div class="thumbnail-wrapper"><img src="' + image.image + '" class="message-img thumbnail-200" /><span class="thumbnail-delete whatsapp-image" data-image="' + image.key + '">x</span></div>';
               images += image.product_id !== '' ? '<input type="checkbox" name="product" style="width: 20px; height: 20px;" class="d-block mx-auto select-product-image" data-id="' + image.product_id + '" /></a>' : '';

               if (image.product_id !== '') {
                 has_product_image = true;
               }
             });

             images += '<br>';

             if (has_product_image) {
               var show_images_wrapper = $('<div class="show-images-wrapper hidden"></div>');
               var show_images_button = $('<button type="button" class="btn btn-xs btn-secondary show-images-button">Show Images</button>');

               $(images).appendTo(show_images_wrapper);
               $(show_images_wrapper).appendTo(text);
               $(show_images_button).appendTo(text);
             } else {
               $(images).appendTo(text);
             }

           }

           p.appendTo(body);
           body.appendTo(text);
           meta.appendTo(text);

           var select_box = $('<input type="checkbox" name="selected_message" class="select-message" data-id="' + message.id + '" />');

           select_box.appendTo(meta);

           if (has_product_image) {
             var create_lead = $('<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-lead">+ Lead</a>');
             var create_order = $('<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-order">+ Order</a>');

             create_lead.appendTo(meta);
             create_order.appendTo(meta);
           }

           text.appendTo( row );

           if (message.status == 7) {
             if (tobottom) {
               row.appendTo(suggestion_container);
             } else {
               row.prependTo(suggestion_container);
             }
           } else {
             if (tobottom) {
               row.appendTo(container);
             } else {
               row.prependTo(container);
             }
           }


           return true;
        }

        function pollMessages(page = null, tobottom = null, addElapse = null) {
                 var qs = "";
                 qs += "?erpUser=" + erpUser;
                 if (page) {
                   qs += "&page=" + page;
                 }
                 if (addElapse) {
                     qs += "&elapse=3600";
                 }
                 var anyNewMessages = false;

                 return new Promise(function(resolve, reject) {
                     $.getJSON("/whatsapp/pollMessagesCustomer" + qs, function( data ) {

                         data.data.forEach(function( message ) {
                             var rendered = renderMessage( message, tobottom );
                             if ( !anyNewMessages && rendered ) {
                                 anyNewMessages = true;
                             }
                         } );

                         if (page) {
                           $('#load-more-messages').text('Load More');
                           can_load_more = true;
                         }

                         if ( anyNewMessages ) {
                             scrollChatTop();
                             anyNewMessages = false;
                         }
                         if (!addElapse) {
                             addElapse = true; // load less messages now
                         }


                         resolve();
                     });

                 });
        }
             function scrollChatTop() {
                 // console.log("scrollChatTop called");
                 // var el = $(".chat-frame");
                 // el.scrollTop(el[0].scrollHeight - el[0].clientHeight);
             }
        function startPolling() {
          setTimeout( function() {
                     pollMessages(null, null, addElapse).then(function() {
                         startPolling();
                     }, errorHandler);
                 }, 1000);
        }
        // function sendWAMessage() {
        //   var data = createMessageArgs();
        //          //var data = new FormData();
        //          //data.append("message", $("#waNewMessage").val());
        //          //data.append("lead_id", leadId );
        //   $.ajax({
        //     url: '/whatsapp/sendMessage/customer',
        //     type: 'POST',
        //              "dataType"    : 'text',           // what to expect back from the PHP script, if anything
        //              "cache"       : false,
        //              "contentType" : false,
        //              "processData" : false,
        //              "data": data
        //   }).done( function(response) {
        //       $('#waNewMessage').val('');
        //       $('#waNewMessage').closest('.form-group').find('.dropify-clear').click();
        //       pollMessages();
        //     // console.log("message was sent");
        //   }).fail(function(errObj) {
        //     alert("Could not send message");
        //   });
        // }

        // sendBtn.click(function() {
        //   sendWAMessage();
        // } );
        startPolling();

         $(document).on('click', '.send-communication', function(e) {
           e.preventDefault();

           var thiss = $(this);
           var url = $(this).closest('form').attr('action');
           var token = "{{ csrf_token() }}";
           var file = $($(this).closest('form').find('input[type="file"]'))[0].files[0];
           var status = $(this).closest('form').find('input[name="status"]').val();
           var screenshot_path = $('#screenshot_path').val();
           var user_id = {{ $user->id }};
           var formData = new FormData();

           formData.append("_token", token);
           formData.append("image", file);
           formData.append("message", $(this).closest('form').find('textarea').val());
           // formData.append("moduletype", $(this).closest('form').find('input[name="moduletype"]').val());
           formData.append("user_id", user_id);
           formData.append("assigned_to", $(this).closest('form').find('select[name="assigned_to"]').val());
           formData.append("status", status);
           formData.append("screenshot_path", screenshot_path);

           // if (status == 4) {
           //   formData.append("assigned_user", $(this).closest('form').find('select[name="assigned_user"]').val());
           // }

           if ($(this).closest('form')[0].checkValidity()) {
             $.ajax({
               type: 'POST',
               url: url,
               data: formData,
               processData: false,
               contentType: false
             }).done(function(response) {
               console.log(response);
               pollMessages();
               $(thiss).closest('form').find('textarea').val('');
               $('#paste-container').empty();
               $('#screenshot_path').val('');
               $(thiss).closest('form').find('.dropify-clear').click();

               if ($(thiss).hasClass('received-customer')) {
                 $(thiss).closest('form').find('#customerMessageButton').removeClass('hidden');
                 $(thiss).closest('form').find('textarea').addClass('hidden');
               }
             }).fail(function(response) {
               console.log(response);
               alert('Error sending a message');
             });
           } else {
             $(this).closest('form')[0].reportValidity();
           }

         });

         var can_load_more = true;

         $('#message-wrapper').scroll(function() {
           var top = $('#message-wrapper').scrollTop();
           var document_height = $(document).height();
           var window_height = $('#message-container').height();

           console.log($('#message-wrapper').scrollTop());
           console.log($(document).height());
           console.log($('#message-container').height());

           // if (top >= (document_height - window_height - 200)) {
           if (top >= (window_height - 1500)) {
             console.log('should load', can_load_more);
             if (can_load_more) {
               var current_page = $('#load-more-messages').data('nextpage');
               $('#load-more-messages').data('nextpage', current_page + 1);
               var next_page = $('#load-more-messages').data('nextpage');
               console.log(next_page);
               $('#load-more-messages').text('Loading...');

               can_load_more = false;

               pollMessages(next_page, true);
             }
           }
         });

         $(document).on('click', '#load-more-messages', function() {
           var current_page = $(this).data('nextpage');
           $(this).data('nextpage', current_page + 1);
           var next_page = $(this).data('nextpage');
           $('#load-more-messages').text('Loading...');

           pollMessages(next_page, true);
         });
      });

      $(document).on('click', '.change_message_status', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var token = "{{ csrf_token() }}";
        var thiss = $(this);

        if ($(this).hasClass('wa_send_message')) {
          var message_id = $(this).data('messageid');
          var message = $('#message_body_' + message_id).find('p').data('message').toString().trim();

          $.ajax({
            url: "{{ url('whatsapp/updateAndCreate') }}",
            type: 'POST',
            data: {
              _token: token,
              moduletype: "task",
              message_id: message_id
            },
            beforeSend: function() {
              $(thiss).text('Loading');
            }
          }).done( function(response) {
          }).fail(function(errObj) {
            console.log(errObj);
            alert("Could not create whatsapp message");
          });
        }
          $.ajax({
            url: url,
            type: 'GET'
          }).done( function(response) {
            $(thiss).remove();
          }).fail(function(errObj) {
            alert("Could not change status");
          });



      });

      $(document).on('click', '.edit-message', function(e) {
        e.preventDefault();
        var thiss = $(this);
        var message_id = $(this).data('messageid');

        $('#message_body_' + message_id).css({'display': 'none'});
        $('#edit-message-textarea' + message_id).css({'display': 'block'});

        $('#edit-message-textarea' + message_id).keypress(function(e) {
          var key = e.which;

          if (key == 13) {
            e.preventDefault();
            var token = "{{ csrf_token() }}";
            var url = "{{ url('message') }}/" + message_id;
            var message = $('#edit-message-textarea' + message_id).val();

            if ($(thiss).hasClass('whatsapp-message')) {
              var type = 'whatsapp';
            } else {
              var type = 'message';
            }

            $.ajax({
              type: 'POST',
              url: url,
              data: {
                _token: token,
                body: message,
                type: type
              },
              success: function(data) {
                $('#edit-message-textarea' + message_id).css({'display': 'none'});
                $('#message_body_' + message_id).text(message);
                $('#message_body_' + message_id).css({'display': 'block'});
              }
            });
          }
        });
      });

      $(document).on('click', '.thumbnail-delete', function(event) {
        event.preventDefault();
        var thiss = $(this);
        var image_id = $(this).data('image');
        var message_id = $(this).closest('.talk-bubble').find('.collapsible-message').data('messageid');
        // var message = $(this).closest('.talk-bubble').find('.collapsible-message').data('message');
        var token = "{{ csrf_token() }}";
        var url = "{{ url('message') }}/" + message_id + '/removeImage';
        var type = 'message';

        if ($(this).hasClass('whatsapp-image')) {
          type = "whatsapp";
        }

        // var image_container = '<div class="thumbnail-wrapper"><img src="' + image + '" class="message-img thumbnail-200" /><span class="thumbnail-delete" data-image="' + image + '">x</span></div>';
        // var new_message = message.replace(image_container, '');

        // if (new_message.indexOf('message-img') != -1) {
        //   var short_new_message = new_message.substr(0, new_message.indexOf('<div class="thumbnail-wrapper">')).length > 150 ? (new_message.substr(0, 147)) : new_message;
        // } else {
        //   var short_new_message = new_message.length > 150 ? new_message.substr(0, 147) + '...' : new_message;
        // }

        $.ajax({
          type: 'POST',
          url: url,
          data: {
            _token: token,
            image_id: image_id,
            message_id: message_id,
            type: type
          },
          success: function(data) {
            $(thiss).parent().remove();
            // $('#message_body_' + message_id).children('.collapsible-message').data('messageshort', short_new_message);
            // $('#message_body_' + message_id).children('.collapsible-message').data('message', new_message);
          }
        });
      });

      $(document).ready(function() {
        $("body").tooltip({ selector: '[data-toggle=tooltip]' });
      });

      $('#approval_reply').on('click', function() {
        $('#model_field').val('Approval Lead');
      });

      $('#internal_reply').on('click', function() {
        $('#model_field').val('Internal Lead');
      });

      $('#approvalReplyForm').on('submit', function(e) {
        e.preventDefault();

        var url = "{{ route('reply.store') }}";
        var reply = $('#reply_field').val();
        var category_id = $('#category_id_field').val();
        var model = $('#model_field').val();

        $.ajax({
          type: 'POST',
          url: url,
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          data: {
            reply: reply,
            category_id: category_id,
            model: model
          },
          success: function(reply) {
            // $('#ReplyModal').modal('hide');
            $('#reply_field').val('');
            if (model == 'Approval Lead') {
              $('#quickComment').append($('<option>', {
                value: reply,
                text: reply
              }));
            } else {
              $('#quickCommentInternal').append($('<option>', {
                value: reply,
                text: reply
              }));
            }

          }
        });
      });

      $(document).on('click', '.forward-btn', function() {
        var id = $(this).data('id');
        $('#forward_message_id').val(id);
      });

      $(document).on('click', '.complete-call', function(e) {
        e.preventDefault();

        var thiss = $(this);
        var token = "{{ csrf_token() }}";
        var url = "{{ route('instruction.complete') }}";
        var id = $(this).data('id');
        var assigned_from = $(this).data('assignedfrom');
        var current_user = {{ Auth::id() }};

        $.ajax({
          type: 'POST',
          url: url,
          data: {
            _token: token,
            id: id
          },
          beforeSend: function() {
            $(thiss).text('Loading');
          }
        }).done( function(response) {
          // $(thiss).parent().html(moment(response.time).format('DD-MM HH:mm'));
          $(thiss).parent().html('Completed');


        }).fail(function(errObj) {
          console.log(errObj);
          alert("Could not mark as completed");
        });
      });

      $('#quickCategory').on('change', function() {
        var replies = JSON.parse($(this).val());
        $('#quickComment').empty();

        $('#quickComment').append($('<option>', {
          value: '',
          text: 'Quick Reply'
        }));

        replies.forEach(function(reply) {
          $('#quickComment').append($('<option>', {
            value: reply.reply,
            text: reply.reply
          }));
        });
      });

      $('#quickCategoryInternal').on('change', function() {
        var replies = JSON.parse($(this).val());
        $('#quickCommentInternal').empty();

        $('#quickCommentInternal').append($('<option>', {
          value: '',
          text: 'Quick Reply'
        }));

        replies.forEach(function(reply) {
          $('#quickCommentInternal').append($('<option>', {
            value: reply.reply,
            text: reply.reply
          }));
        });
      });

      $(document).on('click', '.collapse-fix', function() {
        if (!$(this).hasClass('collapsed')) {
          var target = $(this).data('target');
          var all = $('.collapse-element').not($(target));

          Array.from(all).forEach(function(element) {
            $(element).removeClass('in');
          });
        }
      });

      $('.add-task').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#add-remark input[name="id"]').val(id);
      });

      $('#addRemarkButton').on('click', function() {
        var id = $('#add-remark input[name="id"]').val();
        var remark = $('#add-remark textarea[name="remark"]').val();

        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('task.addRemark') }}',
            data: {
              id:id,
              remark:remark,
              module_type: 'instruction'
            },
        }).done(response => {
            alert('Remark Added Success!')
            window.location.reload();
        }).fail(function(response) {
          console.log(response);
        });
      });


      $(".view-remark").click(function () {
        var id = $(this).attr('data-id');

          $.ajax({
              type: 'GET',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: '{{ route('task.gettaskremark') }}',
              data: {
                id:id,
                module_type: "instruction"
              },
          }).done(response => {
              var html='';

              $.each(response, function( index, value ) {
                html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
                html+"<hr>";
              });
              $("#viewRemarkModal").find('#remark-list').html(html);
          });
      });

      $('#createInstructionReplyButton').on('click', function(e) {
       e.preventDefault();

       var url = "{{ route('reply.store') }}";
       var reply = $('#instruction_reply_field').val();

       $.ajax({
         type: 'POST',
         url: url,
         headers: {
             'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
         },
         data: {
           reply: reply,
           category_id: 1,
           model: 'Instruction'
         },
         success: function(reply) {
           $('#instruction_reply_field').val('');
           $('#instructionComment').append($('<option>', {
             value: reply,
             text: reply
           }));
         }
       });
      });

        // if ($(this).is(":focus")) {
        // Created by STRd6
        // MIT License
        // jquery.paste_image_reader.js
        (function($) {
          var defaults;
          $.event.fix = (function(originalFix) {
            return function(event) {
              event = originalFix.apply(this, arguments);
              if (event.type.indexOf('copy') === 0 || event.type.indexOf('paste') === 0) {
                event.clipboardData = event.originalEvent.clipboardData;
              }
              return event;
            };
          })($.event.fix);
          defaults = {
            callback: $.noop,
            matchType: /image.*/
          };
          return $.fn.pasteImageReader = function(options) {
            if (typeof options === "function") {
              options = {
                callback: options
              };
            }
            options = $.extend({}, defaults, options);
            return this.each(function() {
              var $this, element;
              element = this;
              $this = $(this);
              return $this.bind('paste', function(event) {
                var clipboardData, found;
                found = false;
                clipboardData = event.clipboardData;
                return Array.prototype.forEach.call(clipboardData.types, function(type, i) {
                  var file, reader;
                  if (found) {
                    return;
                  }
                  if (type.match(options.matchType) || clipboardData.items[i].type.match(options.matchType)) {
                    file = clipboardData.items[i].getAsFile();
                    reader = new FileReader();
                    reader.onload = function(evt) {
                      return options.callback.call(element, {
                        dataURL: evt.target.result,
                        event: evt,
                        file: file,
                        name: file.name
                      });
                    };
                    reader.readAsDataURL(file);
                    return found = true;
                  }
                });
              });
            });
          };
        })(jQuery);

          var dataURL, filename;
          $("html").pasteImageReader(function(results) {
            console.log(results);

            // $('#message-body').on('focus', function() {
            	filename = results.filename, dataURL = results.dataURL;

              var img = $('<div class="image-wrapper position-relative"><img src="' + dataURL + '" class="img-responsive" /><button type="button" class="btn btn-xs btn-secondary remove-screenshot">x</button></div>');

              $('#paste-container').empty();
              $('#paste-container').append(img);
              $('#screenshot_path').val(dataURL);
            // });

          });

          $(document).on('click', '.remove-screenshot', function() {
            $(this).closest('.image-wrapper').remove();
            $('#screenshot_path').val('');
          });
        // }


      $(document).on('click', '.change-history-toggle', function() {
        $(this).siblings('.change-history-container').toggleClass('hidden');
      });

      $('#customerMessageButton').on('click', function() {
        $(this).siblings('textarea').removeClass('hidden');
        $(this).addClass('hidden');
      });

      $('#updateCustomerButton').on('click', function() {
        var id = {{ $user->id }};
        var thiss = $(this);
        var name = $('#customer_name').val();
        var phone = $('#customer_phone').val();
        var whatsapp_number = $('#whatsapp_change').val();
        var address = $('#customer_address').val();
        var city = $('#customer_city').val();
        var country = $('#customer_country').val();
        var pincode = $('#customer_pincode').val();
        var email = $('#customer_email').val();
        var insta_handle = $('#customer_insta_handle').val();
        var rating = $('#customer_rating').val();
        var shoe_size = $('#customer_shoe_size').val();
        var clothing_size = $('#customer_clothing_size').val();
        var gender = $('#customer_gender').val();

        $.ajax({
          type: "POST",
          url: "{{ url('customer') }}/" + id + '/edit',
          data: {
            _token: "{{ csrf_token() }}",
            name: name,
            phone: phone,
            whatsapp_number: whatsapp_number,
            address: address,
            city: city,
            country: country,
            pincode: pincode,
            email: email,
            insta_handle: insta_handle,
            rating: rating,
            shoe_size: shoe_size,
            clothing_size: clothing_size,
            gender: gender,
          },
          beforeSend: function() {
            $(thiss).text('Saving...');
          }
        }).done(function() {
          $(thiss).text('Save');
          $(thiss).removeClass('btn-secondary');
          $(thiss).addClass('btn-success');

          setTimeout(function () {
            $(thiss).addClass('btn-secondary');
            $(thiss).removeClass('btn-success');
          }, 2000);
        }).fail(function(response) {
          $(thiss).text('Save');
          console.log(response);
          alert('Could not update customer');
        });
      });

      $('#showActionsButton').on('click', function() {
        $('#actions-container').toggleClass('hidden');
      });

      $(document).on('click', '.show-images-button', function() {
        $(this).siblings('.show-images-wrapper').toggleClass('hidden');
      });

      $(document).on('click', '.fix-message-error', function() {
        var id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ url('whatsapp') }}/" + id + "/fixMessageError",
          data: {
            _token: "{{ csrf_token() }}",
          },
          beforeSend: function() {
            $(thiss).text('Fixing...');
          }
        }).done(function() {
          $(thiss).remove();
        }).fail(function(response) {
          $(thiss).html('<img src="/images/flagged.png" />');

          console.log(response);

          alert('Could not mark as fixed');
        });
      });

      $(document).on('click', '.resend-message', function() {
        var id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ url('whatsapp') }}/" + id + "/resendMessage",
          data: {
            _token: "{{ csrf_token() }}",
          },
          beforeSend: function() {
            $(thiss).text('Sending...');
          }
        }).done(function() {
          $(thiss).remove();
        }).fail(function(response) {
          $(thiss).text('Resend');

          console.log(response);

          alert('Could not resend message');
        });
      });

      var selected_messages = [];
      $(document).on('click', '.select-message', function() {
        var message_id = $(this).data('id');

        if ($(this).prop('checked')) {
          selected_messages.push(message_id);
        } else {
          var index = selected_messages.indexOf(message_id);

          selected_messages.splice(index, 1);
        }

        console.log(selected_messages);
      });

      $('#assignMessagesButton').on('click', function(e) {
        e.preventDefault();

        if (selected_messages.length > 0) {
          $('#selected_messages').val(JSON.stringify(selected_messages));

          if ($(this).closest('form')[0].checkValidity()) {
            $(this).closest('form').submit();
          } else {
            $(this).closest('form')[0].reportValidity();
          }
        } else {
          alert('Please select atleast 1 message');
        }
      });
  </script>
@endsection
