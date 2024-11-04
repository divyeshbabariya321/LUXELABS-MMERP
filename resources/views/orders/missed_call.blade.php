@extends('layouts.app')

@section('content')

    <style>
        td audio {
            height: 30px;
        }

        td {
            padding: 5px 8px 0 !important;

        }
        #customer_order_details{
            padding: 10px 0 !important;
        }

    </style>

<h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">
    Missed Call
    <div class="margin-tb" style="flex-grow: 1;">
        <div class="pull-right ">
            <div class="d-flex justify-content-between  mx-3">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#reservedCalls">
                    Waiting Calls({{count($reservedCalls)}})  
                </button>  &nbsp;
                
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#add-status">
                     Add Status
                </button>
            </div>
        </div>
    </div>
</h2>


<form method="get" action="{{route('order.missed-calls')}}">

    <div class="form-group">
        <div class="row ml-2">
    
   
            <div class="col-md-2">

                <select name="filterWebsite" class="set-call-status select2" >
                    <option value="{{ null }}">Select website</option>
                    @foreach ($storeWebsite as $key=> $website)
                        <option value="{{ $key }}"  {{ $key == $selectedWebsite ? 'selected': '' }}>{{ $website }}</option>
                    @endforeach
                </select>

        </div>


            <div class="col-md-2">

                    <select name="filterStatus" class="set-call-status select2" >
                        <option value="{{ null }}">Select status</option>
                        @foreach ($allStatuses as $status)
                            <option value="{{ $status->id }}" {{ $selectedStatus == $status->id ? 'selected' :'' }} >{{ $status->name }}</option>
                        @endforeach
                    </select>

            </div>

           
            <div class="col-md-1 d-flex justify-content-between">
                <button type="submit" class="btn btn-image" ><img src="/images/filter.png"></button>
            </div>
        </div>
    </div>
</form>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

      
     


    <div class="col-md-12">
        <div class="table-responsive missed-call-table">
            <table class="table table-bordered" style="table-layout:fixed;">
                <tr>
                    <th style="width:9%">Mobile Number</th>
                    <th style="width:7%">Message</th>
                    <th style="width:9%">Website name</th>
                    <th style="width:10%">From</th>
                    <th style="width:10%">To</th>
                    <th style="width:10%">Agent</th>
                    <th style="width:18%">Call Recording</th>
                    <th style="width:10%">Call Time</th>
                    <th style="width:8%">Status</th>
                    <th  style="width:4%">Action</th>
                </tr>


                @foreach ($callBusyMessages['data'] as $key => $callBusyMessage)
                    <tr class="">
                        <td>
                            @if (isset($callBusyMessage['customer_name']))
                                {{ $callBusyMessage['customer_name'] }}
                            @else
                                {{ $callBusyMessage['twilio_call_sid'] }}
                            @endif
                        </td>
                        <td>{{ $callBusyMessage['message'] }}</td>
                        <td>{{ !empty($callBusyMessage['store_website_name']) ? $callBusyMessage['store_website_name'] : ' ' }}
                        </td>
                        <td>{{$callBusyMessage['from']}} 
                            @if($callBusyMessage['call_data'] == 'client') 
                                <i class="fa fa-user" aria-hidden="true" title="Call From Customer"></i> 
                            @elseif($callBusyMessage['call_data'] == 'agent') 
                                <i class="fa fa-desktop" aria-hidden="true" title="Call From Agent"></i> 
                            @elseif($callBusyMessage['call_data'] == 'leave_message') 
                                <i class="fa fa-envelope" aria-hidden="true" title="Message From Customer"></i> 
                            @endif
                        </td>
                        <td>{{$callBusyMessage['to']}}</td>
                        <td>{{(isset($callBusyMessage['agent']) ? $callBusyMessage['agent'] : '' )}}</td>
                        <td>
                            <div class="d-flex pb-2">
                                <audio src="{{$callBusyMessage['recording_urls']}}" controls preload="metadata">
                                <p>Alas, your browser doesn't support html5 audio.</p>
                                </audio>
                                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-http-status" title="Http Status" data-toggle="modal" data-target="#show-recording-text{{$key}}" data-request="N/A" data-response="N/A">
                                    <i class="fa fa-headphones"></i>
                                </button>
                                <div id="show-recording-text{{$key}}" class="modal fade" role="dialog" >
                                    <div class="modal-dialog" style="width:100%;max-width:96%">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Audio Text</h4>
                                            </div>
                                            <div class="modal-body">
                                                {{ $callBusyMessage['audio_text'] }}
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                        <td>{{ $callBusyMessage['created_at'] }}</td>

                        <td>

                            <select name="status" class="set-call-status select2" data-call-id = {{ $callBusyMessage['id']  }}>
                                <option value="{{ null }}">Select status</option>
                                @foreach ($allStatuses as $status)
                                    <option value="{{ $status->id }}"  {{ $status->id == $callBusyMessage['call_busy_message_statuses_id'] ? 'selected':''  }}>{{ $status->name }}</option>
                                @endforeach
                            </select>

                        </td>

                        <td>
                            <!-- <i class="fa fa-info-circle show-histories" type="button" data-product-id="1709"
                            title="Status Logs" aria-hidden="true" data-id="107" data-name="Status"
                            style="cursor: pointer;" data-call-message-id={{ $callBusyMessage['id'] }}></i> -->
                         
                         
                            <i class="fa fa-envelope-o send-mail-or-whatsup-message-button pl-1" type="button" data-product-id="1709"
                            title="Send Message" aria-hidden="true" data-id="107" data-name="Status"
                            style="cursor: pointer;" data-call-message-id={{ $callBusyMessage['id']}}   data-number="{{ $callBusyMessage['twilio_call_sid'] }}" data-fullname="{{ isset($callBusyMessage['customer_name']) ? $callBusyMessage['customer_name'] : null }}" data-customer-id="{{ isset($callBusyMessage['customerid']) ? $callBusyMessage['customerid'] : null }}"></i>

                            
                            <a type="button" class="btn btn-xs btn-image load-communication-modal pl-1" data-object="customer" data-id="{{isset($callBusyMessage['customerid']) ? $callBusyMessage['customerid'] : null}}" data-load-type="text" data-all="1" title="Load messages" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}"><img src="/images/chat.png" class="pt-0" alt=""></a>

                            @if (isset($callBusyMessage['customerid']))
                            <i class="fa fa-info-circle show_customer_histories" data-customer_id="{{ $callBusyMessage['customerid'] }}" style="cursor: pointer;" title="Customer Information" aria-hidden="true"></i>
                            @endif

                            @if (isset($callBusyMessage['customerid']))
                                <!-- <a class="btn btn-image p-0 pt-0"
                                    href="{{ route('customer.show', $callBusyMessage['customerid']) }}"><img
                                        src="/images/view.png" /></a> -->
                            @endif

                            @if (isset($callBusyMessage['customerid']))
                            <i class="fa fa-ticket create-customer-ticket-modal" onclick="showticket('{{ $callBusyMessage['customerid'] }}');" data-customer_id="{{ $callBusyMessage['customerid'] }}" style="cursor: pointer;" title="Create Ticket" aria-hidden="true"></i>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    
    <div class="col-md-12">
        {{ $callBusyMessages_pagination->links() }}
    </div>
    @include("partials.customer-new-ticket")
    <!-- Customer Detail Modal -->
    <div class="modal fade" id="customer-information" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                        <div class="card-body" style="height:100px !important;">
                            <h5 class="card-title customer_info_name"></h5>
                            <h5 class="card-subtitle mb-2 text-muted cutsomer_info_phone"></h5>
                            <h5 class="card-subtitle mb-2 text-muted cutsomer_info_email"></h5>
                            <h5 class="card-subtitle mb-2 text-muted cutsomer_info_website"></h5>
                           
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                        <div class="card-body" style="height:100px !important;">
                            <h5 class="card-title cutsomer_info_addess"></h5>
                            
                        </div>
                        </div>
                    </div>
                </div>

                <div class="card-body " id="customer_order_data">

                </div>
                
            </div>
            
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="order-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Orders</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="show-ordres-table" class="table table-bordered table-hover" style="table-layout:fixed;">
                        <thead>
                            <th>Order id</th>
                            <th>Order type</th>
                            <th>Order status</th>
                            <th>Payment mode</th>
                            <th>Price</th>
                            <th>Currency</th>
                            <th>Order date </th>
                            
                        </thead>
                        <tbody class="show-ordres-body">

                        </tbody>
                    </table>
                </div>
              
            </div>
        </div>
    </div>

 <!-- Add status  Modal -->
     <div class="modal fade" id="reservedCalls" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel">Reserved Calls</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body add-status-body reserved-calls-table">
                <table id="show-ordres-table" class="table table-bordered table-hover" style="table-layout:fixed;">
                    <thead class="reserved-calls">
                        <tr>
                        <th>Customer Name</th>
                        <th>Customer Email</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Call Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @foreach($reservedCalls as $reservedCall)
                            <tr>
                            <td>
                                {{$reservedCall->name}}
                            </td>
                            <td>
                                {{$reservedCall->email}}
                            </td>
                            <td>
                                {{$reservedCall->from}}
                            </td>
                            <td>
                                {{$reservedCall->to}}
                            </td>
                            <td>{{$reservedCall->created_at}}</td>
                            </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
          </div>
        </div>
      </div>
    
     <!-- Add status  Modal -->
     <div class="modal fade" id="add-status" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel">Add status</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body add-status-body">
              
                <form action="{{ route('order.store.add-status') }}" method="POST" id="add-status-form">
                    @csrf
                    <div class="form-group">
                        <input type="text" class="form-control" id="exampleInputEmail1" name="name" class="form-text text-muted" placeholder="Add status" required>
                    </div>
    
                    <div class="float-right">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button  class="btn btn-secondary">Add status</button>
                    </div>
                </form>
    
            </div>
          </div>
        </div>
      </div>
    
     <!-- Send whats-up /email message  -->
     <div class="modal fade" id="send-mail-or-whatsup-message" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel">Send message</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body add-status-body">
              
                <form action="{{ route('order.send-message.whatsapp-or-email') }}" method="POST" id="send-message-whatsup-or-email">
                    @csrf
                    

                    <div class="form-group">
                        <label for="formGroupExampleInput">Message</label>
                        <textarea type="text" class="form-control" name="message" id="formGroupExampleInput" placeholder="Enter message" required></textarea>
                      </div>

                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="whatsapp-checked" value="whatsapp" name="whatsapp" checked>
                            <label class="form-check-label" for="whatsapp-checked">Whatsapp</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="email-checked" value="email" name="email" checked>
                            <label class="form-check-label" for="email-checked">Email</label>
                          </div>
                      </div>

                      <div class="float-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button  class="btn btn-secondary">Send</button>
                      </div>

                </form>
    
            </div>
          </div>
        </div>
      </div>
    
        {{-- chat history modal  --}}
        <div id="chat-list-history" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Communication</h4>&nbsp;&nbsp;
                        <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 50%;">&nbsp;&nbsp;
                        <!-- <input type="text" name="search_chat_pop_time"  class="form-control search_chat_pop_time" placeholder="Search Time" style="width: 200px;"> -->
            <input style="min-width: 30px;" placeholder="Search by date" value="" type="text" class="form-control search_chat_pop_time" name="search_chat_pop_time">
                        
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
let userData  = null

        $('.set-call-status').select2()

        jQuery(document).ready(function($) {
            $('audio').on("play", function(me) {
                $('audio').each(function(i, e) {
                    if (e !== me.currentTarget) {
                        this.pause();
                    }
                });
            });
        })



        $(document).on('click', '.show-histories', function() {

         const customer_id = $(this).data("call-message-id")

            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/order/missed-calls/orders/" +customer_id,
                   
                })
                .done(function(response) {
                    let html = null
                    if (response.length) {

                        response.forEach((element) => {
                            console.log(element)
                            const final_html = `
                                  <tr>
                                    <td style="word-break: break-word;">${element.order_id  ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.order_type ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.order_status ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.payment_mode ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.price ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.currency ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.order_date ?? '-'}</td>
                                </tr>
                                `
                            html += final_html

                        })

                        $('.show-ordres-body').html(html)
                    }

                });
            $('#order-details').modal('show')
        })


    $(document).on('submit','#add-status-form',function(e){
                e.preventDefault()
                $.ajax({
                    method:'POST',
                    url: "/order/calls/add-status",
                    data:$(this).serialize()
                   
                })
                .done(function(response) {
                    toastr['success'](response.message, 'success');

                        const html = ` <option value=${response.data.id}>${response.data.name}</option> `
                        $('.set-call-status').append(html);
                        $('#add-status-form')[0].reset()
 
                    $('#add-status').modal('hide')
                   
                });

    })

    $(document).on('change','.set-call-status',function(e){
                e.preventDefault()
        let select_id = $(this).val();
        let select_call_id = $(this).data('call-id')
        let fullname = $(this).data('fullname')
        let data_number = $(this).data('number')


                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method:'POST',
                    url: "/order/calls/store-status/"+select_call_id,
                    data:{
                        select_id,
                        select_call_id,
                        fullname,
                        data_number

                    }
                   
                })
                .done(function(response) {
                    toastr['success'](response.message, 'success');
                   
                });

    })


    $(document).on('click','.send-mail-or-whatsup-message-button',function(e){
        const fullName = $(this).data('fullname')
        const fullNumber = $(this).data('number')
        const fullcustomerId = $(this).data('customer-id')

        userData = {fullName,fullNumber,fullcustomerId}
        e.preventDefault()
        $('#send-mail-or-whatsup-message').modal('show')

    })



    $(document).on('submit','#send-message-whatsup-or-email',function(e){

        e.preventDefault()
const formData = $(this).serialize()
// const formData = new FormData(document.getElementById("send-message-whatsup-or-email"))
// console.log(formData)
        
            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                    $("#loading-image-preview").show();
                },
                    method:'POST',
                    url: "/order/calls/send-message",
                    data: {
                        formData,
                        fullName : userData.fullName,
                        fullNumber: userData.fullNumber,
                        customerId:userData.fullcustomerId
                    }
                   
                })
                .done(function(response) {
                    $("#loading-image-preview").hide();

                    if(response.error){
                        toastr['error'](response.error, 'error');
                        return
                    }
                    if(response.message){
                        $('#send-message-whatsup-or-email')[0].reset()
                        toastr['success'](response.message, 'success');
                        $('#send-mail-or-whatsup-message').modal('hide')
                    }

                }).fail(function (response) {
                $("#loading-image-preview").hide();
                console.log("Sorry, something went wrong");
            });;

    })



    $(document).on('click','.show_customer_histories',function(e){
        var customer_id = $(this).data('customer_id');

        $.ajax({
            method:'get',
            url: "{{ route('customer.getcustomerinfo') }}",
            data:{
                customer_id : customer_id,
            }
        })
        .done(function(response) {
            if(response.status == 200){
                $('.customer_info_name').html(response.data.name);
                $('.cutsomer_info_phone').html(response.data.phone);
                $('.cutsomer_info_email').html(response.data.email);
                $('.cutsomer_info_website').html(response.data.website);
                $('.cutsomer_info_addess').html(response.data.address);
            }
        });

        $.ajax({
            url: "{{ route('livechat.getorderdetails') }}",
            type: 'GET',
            dataType: 'json',
            data: { customer_id : customer_id ,   _token: "{{ csrf_token() }}" },
        })
        .done(function(data) {
            if (data[0] == true) {
                let information = data[1];
                let name = information.customer.name;
                let number = information.customer.phone;
                let email = information.customer.email ;
                let accordion_data = '';
                // if (information.leads_total){
                    accordion_data = get_leads_table_data(information.leads,information.leads_total)
                // }
                // if (information.orders_total){
                    accordion_data += get_orders_table_data(information.orders,information.orders_total)
                // }
                // if (information.exchanges_return_total){
                    accordion_data += get_exchanges_return_table_data(information.exchanges_return,information.exchanges_return_total)
                // }

                $('#customer_order_data').html(accordion_data);

            } else {
                $('#customer_order_data').html('');
            }
        })
        .fail(function() {
            console.log("error");
            
        });
        $('#customer-information').modal('show')

    })

    function showticket(c)
    {
        $('#ticket_customer_id').val(c);
        $('#create-customer-ticket-modal').modal('show');
       
    }
    </script>

@endsection
