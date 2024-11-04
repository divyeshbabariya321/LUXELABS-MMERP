@extends('layouts.app')

@section('content')
<?php $base_url = URL::to('/');?>
<div class="row">
  <div class="col-lg-12 margin-tb">
    <h2 class="page-heading">Order/Refund Status Messages</h2>
      <div class="pull-left cls_filter_box">
          <form class="form-inline" action="{{ route('order.status.messages') }}" method="GET">
              <div class="form-group ml-3 cls_filter_inputbox">
                  <label for="with_archived">Search Order/Refund No.</label>
                  <input name="order_id" type="text" class="form-control" placeholder="Search" id="order-search" data-allow-clear="true">
              </div>
              <div class="form-group ml-3 cls_filter_inputbox">
                  <label for="with_archived">Search Customer Name</label>
                  <input name="customer_name" type="text" class="form-control" placeholder="Search" id="customer-name" data-allow-clear="true">
              </div>
              <div class="form-group ml-3 cls_filter_inputbox">
                <label for="with_archived">Order Status</label>
                  <select data-placeholder="Order Status" name="flt_order_status" class="form-control select2">
                  <optgroup label="Order Status">
                    <option value="">Select Order Status</option>
                    @foreach ($order_status_list as $id => $status)
                    <option value="{{ $id }}">{{ $status }}</option>
                    @endforeach
                  </optgroup>
                </select>
              </div>
              <div class="form-group ml-3 cls_filter_inputbox">
                  <label for="with_archived">Estimate Delivery Date</label>
                  <input name="flt_estimate_date" type="date" class="form-control" placeholder="Select Date" id="flt_estimate_date" data-allow-clear="true">
              </div>
              <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image"><img src="<?php echo $base_url;?>/images/filter.png"/></button>
          </form>
      </div>
  </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif
<div class="row">
  <div class="col-md-12 mb-3" style="">
      <div class="pull-right">
        <a href="#" class="btn btn-xs btn-secondary magento-order-status">Magento Order Status Mapping</a>
      </div>
  </div>
</div>
<div class="table-responsive">
  <table class="table table-bordered">
    <thead>
      <th width="5%">Order/Refund No.</th>
      <th width="5%">Type</th>
      <th >Customer Name</th>
      <th width="15%">Updated status</th>
      <th width="30%">Communication</th>
      <th >Approve</th>
      <th >Est. Delivery Date</th>
      <th>Actions</th>
    </thead>
    <tbody>
      @foreach($order_n_refunds as $order_n_refund)
        <tr>
          <td>{{ $order_n_refund->id }}</td>
          <td>{{ $order_n_refund->type }}</td>
          <td>{{ $order_n_refund->name }}</td>
          <td>
            <div class="form-groups">
              @if($order_n_refund->type == 'order')
                <select data-placeholder="Order Status" name="order_status" class="form-control select2 order-status-select" data-id="{{ $order_n_refund->id }}">
                  <optgroup label="Order Status">
                    <option value="">Select Order Status</option>
                    @foreach ($order_status_list as $id => $status)
                    <option value="{{ $id }}" {{ (isset($order_n_refund->order_status_id) && $order_n_refund->order_status_id == $id) ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                  </optgroup>
                </select>
              @endif
            </div>
          </td>
          <td>
            <input type="text" rows="1" style="width: 87%;display: inline-block;vertical-align: inherit;" class="form-control quick-message-field" name="message" placeholder="Message">
            <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message" data-orderid="{{ $order_n_refund->id }}" data-customerid="{{ $order_n_refund->customer_id }}"><img src="/images/filled-sent.png"/></button>
          </td>
          <td>
            <div class="row">
    <div class="col-md-12">
         @php
         $chatMessage = $order_n_refund->chatMessage;
         @endphp
         @if($chatMessage)
         {{ substr($chatMessage->message,0,40) }}... 
         @if(isset($chatMessage->is_reviewed) && $chatMessage->is_reviewed == 0)
         <button type="button" title="Please review this" class="btn btn-xs btn-secondary approve-messages review-btn" data-id="{{ $chatMessage->id }}"><i class="fa fa-check" aria-hidden="true"></i></button>
         @endif
         @endif
       </div>
          </td>
          <td>{{ $order_n_refund->estimated_delivery_date }}</td>
          <td>

            <button type="button" class="btn pr-0 btn-image order-flag pd-5" data-type="order_status" data-id="{{ $order_n_refund->id }}" data-flag="{{ $order_n_refund->is_flag }}">
              @if($order_n_refund->is_flag == 0)
                <img src="/images/unflagged.png">
              @else
                <img src="/images/flagged.png">
              @endif
            </button>

            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="order" data-id="{{$order_n_refund->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>

            <!-- START - Purpose : Go To Direct Mail -  DEVTASK-18283 -->
            <a href='{{ url("email/order_data/{$order_n_refund->email}") }}' target="_blank" data-email="{{ $order_n_refund->email }}" style="color:#000;"><i class="fa fa-envelope-o" aria-hidden="true"></i></a>
            <!-- END -  DEVTASK-18283 -->
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div id="chat-list-history" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Communication</h4>
        <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
      </div>
      <div class="modal-body" style="background-color: #999999;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div id="update-status-message-tpl" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
        <div class="modal-header">
            <h4 class="modal-title">Change Status</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="" id="update-status-message-tpl-frm" method="POST">
            @csrf
            <input type="hidden" name="order_id" id="order-id-status-tpl" value="">
            <input type="hidden" name="order_status_id" id="order-status-id-status-tpl" value="">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-2">
                            <strong>Message:</strong>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                              <textarea cols="45" class="form-control" id="order-template-status-tpl" name="message"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                      <div class="col-md-2">
                      <div class="form-group">
                          <div class="checkbox">
                                                    <label><input class="msg_platform msg_platform_del" type="checkbox" value="email" id="del_date_email">Email</label>  
                          </div>
                          <div class="checkbox">
                                                   <label><input class="msg_platform msg_platform_del" type="checkbox" value="sms">SMS</label>
                          </div>
                              
                      </div>
                      </div>
                          
                    </div>
                    
                   
                
        </div>
      </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary update-status-with-message">With Message</button>
                <button type="button" class="btn btn-secondary update-status-without-message">Without Message</button>
            </div>
        </form>
      </div>

<div id="order-status-map" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Magento Order Status Mapping</h4>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th style="width: 20%;">Status</th>
                  <th style="width: 20%;">Magento Status</th>
                  <th>Message Text Template</th>
                </tr>
              </thead>

              <tbody>
               @foreach($orderStatusList as $orderStatus)
               <tr>
                <td>{{ $orderStatus->id }}</td>
                <td>{{ $orderStatus->status }}</td>
                <td><input type="text" value="{{ $orderStatus->magento_status }}" class="form-control" onfocusout="updateStatus({{ $orderStatus->id }})" id="status{{ $orderStatus->id }}"></td>
                <td>
                  <textarea class="form-control message-text-tpl" name="message_text_tpl">{{ !empty($orderStatus->message_text_tpl) ? $orderStatus->message_text_tpl : \App\Order::ORDER_STATUS_TEMPLATE }}</textarea>
                  <button type="button" class="btn btn-image edit-vendor" onclick="updateStatus({{ $orderStatus->id }})"><i class="fa fa-arrow-circle-right fa-lg"></i></button>
                </td>
              </tr>
              @endforeach
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


<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
  $(document).on('click','.magento-order-status',function(event){ 
    event.preventDefault();
    $('#order-status-map').modal('show');
  });
  $(document).on('click', '.send-message', function () {
    var thiss = $(this);
    var data = new FormData();
    var customerid = $(this).data('customerid');
    var message = $(this).siblings('input').val();

    data.append("customer_id", customerid);
    data.append("order_id", $(this).data('orderid'));
    data.append("message", message);
    data.append("status", 1);

    if (message.length > 0) {
      if (!$(thiss).is(':disabled')) {
        $.ajax({
          url: '/whatsapp/sendMessage/customer',
          type: 'POST',
            "dataType": 'json',
            "cache": false,
            "contentType": false,
            "processData": false,
            "data": data,
            beforeSend: function () {
              $(thiss).attr('disabled', true);
            }
          }).done(function (response) {
            $(thiss).siblings('input').val('');
            toastr["success"]('Message sent successfully');
            $(thiss).attr('disabled', false);
          }).fail(function (errObj) {
            $(thiss).attr('disabled', false);
            alert("Could not send message");
            toastr["error"](errObj.message);
          });
        }
      } else {
        alert('Please enter a message first');
      }
    });
  $(document).on("change",".order-status-select",function() {
        console.log($(this).data("id"));
        var id = $(this).data("id");
        var status = $(this).val();

        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "order/"+id+"/change-status-template",
          type: "post",
          data : {
            order_id: id, 
            order_status_id : status
          },
          beforeSend: function() {
            $("loading-image").show();
          }
        }).done( function(response) {
          $("loading-image").hide();
          if(response.code == 200) {
            $("#order-id-status-tpl").val(id);
            $("#order-status-id-status-tpl").val(status);
            $("#order-template-status-tpl").val(response.template);
            $("#update-status-message-tpl").modal("show");
          }
          
        }).fail(function(errObj) {
            alert("Could not change status");
        });
    });
  $(document).on("click",".update-status-with-message",function(e) {
          e.preventDefault();
          var selected_array = [];
          $('.msg_platform_del:checkbox:checked').each(function() {
            selected_array.push($(this).val());
          });
          $.ajax({
            url: "/order/change-status",
            type: "GET",
            async : false,
            data : {
              id : $("#order-id-status-tpl").val(),
              status : $("#order-status-id-status-tpl").val(),
              sendmessage:'1',
              message:$("#order-template-status-tpl").val(),
              order_via:selected_array,

            },
            beforeSend: function() {
              $("#loading-image").show();
            }
          }).done( function(response) {
            $("#loading-image").hide();
            toastr["success"]('Message sent successfully');
            $("#update-status-message-tpl").modal("hide");
          }).fail(function(errObj) {
            $("#loading-image").hide();
            alert("Could not change status");
          });
      });

      $(document).on("click",".update-status-without-message",function(e) {
          e.preventDefault();
          var selected_array = [];
          $('.msg_platform_del:checkbox:checked').each(function() {
            selected_array.push($(this).val());
          });
          $.ajax({
            url: "/order/change-status",
            type: "GET",
            async : false,
            data : {
              id : $("#order-id-status-tpl").val(),
              status : $("#order-status-id-status-tpl").val(),
              sendmessage:'0',
              message:$("#order-template-status-tpl").html(),
              order_via:selected_array,
            },
            beforeSend: function() {
              $("#loading-image").show();
            }
          }).done( function(response) {
            $("#loading-image").hide();
            toastr["success"]('Message sent successfully');
            $("#update-status-message-tpl").modal("hide");
          }).fail(function(errObj) {
            $("#loading-image").hide();
            alert("Could not change status");
          });
      });


  $(document).on('click', '.order-flag', function () {
            var id = $(this).data('id');
            var thiss = $(this);

            $.ajax({
                type: "POST",
                url: "{{ route('order.status.flag') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                beforeSend: function () {
                    $(thiss).text('Changing');
                }
            }).done(function (response) {
                if (response.is_flagged == 1) {
                    // var badge = $('<span class="badge badge-secondary">Flagged</span>');
                    //
                    // $(thiss).parent().append(badge);
                    $(thiss).html('<img src="/images/flagged.png" />');
                } else {
                    $(thiss).html('<img src="/images/unflagged.png" />');
                    // $(thiss).parent().find('.badge').remove();
                }

                // $(thiss).remove();
            }).fail(function (response) {
                $(thiss).html('<img src="/images/unflagged.png" />');

                alert('Could not flag!');

                console.log(response);
            });
        });
  </script>
@endsection
