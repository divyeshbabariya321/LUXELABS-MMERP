@extends('layouts.app')
 
@section('styles')
<style>
div#credit_logs .modal-dialog table { table-layout: fixed; }
div#credit_histories .modal-dialog table { table-layout: fixed; }
div#credit_logs .modal-dialog table tr >* { word-break: break-all; }
div#credit_histories .modal-dialog table tr >* { word-break: break-all; }

</style>

 <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection
 
@section('content')
 
   <div class="row">
       <div class="col-lg-12 margin-tb">
           <h2 class="page-heading">Store Credit</h2>
           <div class="pull-left">
             <form class="form-inline" action="{{url('customer/credit')}}" method="GET">
               <div class="col">
                 <div class="form-group">
                   <div class='input-group'>
                     <input type='text' placeholder="Search name" class="form-control" name="name" list="name-lists"  value="{{ isset($_GET['name'])?$_GET['name']:''}}" />
                       <datalist id="name-lists">
                           @foreach($users as $user)
                               <option value="{{$user->name}}">
                           @endforeach
                       </datalist>
                   </div>
                 </div>
               </div>
               <div class="col">
                 <div class="form-group">
                   <div class='input-group'>
                    
                     <input type='text' placeholder="Search Email" class="form-control" name="email" list="email-lists" value="{{ isset($_GET['email'])?$_GET['email']:''}}"  />
                       <datalist id="email-lists">
                           @foreach($users as $user)
                               <option value="{{$user->email}}">
                           @endforeach
                       </datalist>
                   </div>
                 </div>
               </div>
               <div class="col">
                 <div class="form-group">
                   <div class='input-group'>
                    
                    
                     <input type='text' placeholder="Search phone" class="form-control" name="phone"  value="{{ isset($_GET['phone'])?$_GET['phone']:''}}"  />
 
                   
                   </div>
                 </div>
               </div>
 
               <div class="col">
                 <div class="form-group">
                   <div class='input-group'>
                     <select class="form-control" name="store_website" >
                       <option value="">Select Store Website</option>
                     @foreach($store_website as $s)
                       @php
                         $sel='';
                         if( isset($_GET['store_website']) && $_GET['store_website']==$s->id)
                             $sel="selected='selected'";
                       @endphp     
 
                     <option {{ $sel}} value="{{$s->id}}">{{$s->title}} </option>
                    @endforeach
                    </select>
                   
                   </div>
                 </div>
               </div>
 
              
 
               <div class="col">
                 <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                   <a href="/customer/credit" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
               </div>
             </form>
           </div>
           <div class="pull-right">
          
           </div>
           <button type="button" class="btn custom-button float-right mr-3 open-customer-credit" data-toggle="modal" data-target="#create-customer-credit-modal">Add Credit</button>
       </div>
   </div>
 
   @include('partials.flash_messages')
 
   
 
   <div class="table-responsive mt-3">
     <table class="table table-bordered">
       <thead>
         <tr>
           <th>Name</th>
           <th>Email</th>
           <th>Phone</th>
           <th>Store Website</th>
           <th>Date</th>
           <th>Credit</th>
           <th>Utilised</th>
           <th>Balance</th>
           <th>View logs</th>
           <th>View histories</th>
          
           
         </tr>
       </thead>
 
       <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
         @foreach ($customers_all as $c) 
         @php
          $used_credit = (string) $c->usedCredit->sum('used_credit');
          $credit_in = (string) $c->creditIn->sum('used_in');
        
         @endphp
           <tr>
             <td>{{ $c->name }}</td>
             <td>{{ $c->email }}</td>
             <td>{{ $c->phone }}</td>
             <td>{{ $c->title }}</td>
             <td>@if($c->date != null) {{ date("d-m-Y",strtotime($c->date)) }} @endif</td>
             <td>{{ $c->credit  + $credit_in }}</td>
             <td>{{ $used_credit }}</td>
             <td>{{ ($c->credit + $credit_in ) - $used_credit }}</td>
             <td><a href="#" onclick="getLogs('{{ $c->id}}')"><i class="fa fa-eye"></i></a></td>
             <td>
                <a href="#" onclick="getHistories('{{ $c->id}}')"><i class="fa fa-eye"></i></a> |
                <a href="#" ><i class="fa fa-envelope-square get_email_log" data-cust_id="{{$c->id}}"></i></a>
            </td>
           </tr>
         @endforeach
       </tbody>
     </table>
   </div>
 
   <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />
 <div id="credit_logs" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Credit Logs</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            
                    <div class="col-md-12" id="">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width='25%'>Date</th>
                                    <th width='25%'>Request </th>
                                    <th width='25%'>Response</th>
                                    <th width='25%'>Status</th>
                                    <th width='25%'>Repush</th>
                                </tr>
                            </thead>
                            <tbody id="display_logs"></tbody>
                        </table>
                    </div>
                
                </div>
        </div>
    </div>

<div id="credit_histories" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
          <div class="modal-header">
          <h5 class="modal-title">Credit Histories</h5>
              <button type="button" class="close" data-dismiss="modal">×</button>
          </div>
          
                  <div class="col-md-12" id="">
                      <table class="table">
                          <thead>
                              <tr>
                                  <th width='25%'>ID</th>
                                  <th width='25%'>User Credit </th>
                                  <th width='25%'>Used In</th>
                                  <th width='25%'>Type</th>
                                  <th width='25%'>Date</th>
                              </tr>
                          </thead>
                          <tbody id="display_histories"></tbody>
                      </table>
                  </div>
              
              </div>
      </div>
  </div>

<div class="modal fade" id="create-customer-credit-modal" role="dialog" aria-labelledby="create-customer-credit-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="create-customer-credit-modal-label">Create Credit</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>

          <div class="modal-body">
              <form id="credit_form">
                  <input type="hidden" id="source_of_credit" name="source_of_credit" value="customer">
                  <div class="form-group">
                      <label for="credit" class="col-form-label">Website:</label>
                      <select class="form-control select2" name="store_website_id" id="store_website_id" style="width: 100%;" >
                            <option value="">-- Select Store website --</option>
                            @foreach ($store_website as $website)
                                <option value="{{$website->id}}">{{$website->website}}</option>
                            @endforeach
                            <!-- <option value="Others">Others</option> -->
                      </select>
                      <span class="text-danger" id="store_website_id_error"></span>
                  </div>

                  <div class="form-group">
                      <label for="credit" class="col-form-label">Customer:</label>
                      <select class="form-control select2" name="credit_customer_id" id="credit_customer_id" style="width: 100%;" >
                        
                      </select>
                      <span class="text-danger" id="credit_customer_id_error"></span>
                  </div>
                  <div class="form-group">
                    <label for="credit" class="col-form-label">Credit:</label>
                    <input type="number" min="0" class="form-control" name="credit" id="credit">
                    <span class="text-danger" id="credit_error"></span>
                  </div>
                  <div class="form-group">
                      <input type="radio" class="d-inline" name="credit_type" value="PLUS" checked id="">PLUS
                      <input type="radio" class="d-inline" name="credit_type" value="MINUS" id="">MINUS
                  </div>
                  <div class="form-group">
                      <label for="currency" class="col-form-label">Currency:</label>
                      {{ html()->select('currency', \App\Currency::pluck('name', 'code')->toArray(), request('currency', 'EUR'))->class('form-control select2')->style("width:250px;")->attribute('tabindex', 1) }}
                      <span class="text-danger" id="currency_error"></span>
                  </div>
              </form>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" id="submit_credit_form" class="btn btn-primary">Submit</button>
          </div>
      </div>
  </div>
</div>
<div id="credit_email_log" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog modal-lg">
  <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Credit Email Histories</h5>
            <button type="button" class="close" data-dismiss="modal">×</button>
        </div>
        <div class="col-md-12" id="">
            <table class="table">
                <thead>
                    <tr>
                        <th width='25%'>ID</th>
                        <th width='25%'>From </th>
                        <th width='25%'>To</th>
                        <th width='25%'>Date</th>
                    </tr>
                </thead>
                <tbody id="email_log_histories"></tbody>
            </table>
        </div>
      </div>
    </div>
</div>

 
@endsection
 
@section('scripts')
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
 <script>
         var isLoading = false;
         var page = 1;
        $(document).ready(function () {
           $(window).scroll(function() {
               if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
                   loadMore();
               }
           });
 
           function loadMore() {
               if (isLoading)
                   return;
               isLoading = true;
               var $loader = $('.infinite-scroll-products-loader');
               page = page + 1;
               $.ajax({
                   url: "{{url('customer/credit')}}?ajax=1&page="+page,
                   type: 'GET',
                   data: $('.form-search-data').serialize(),
                   beforeSend: function() {
                       $loader.show();
                   },
                   success: function (data) {
                      
                       $loader.hide();
                       if('' === data.trim())
                           return;
                       $('.infinite-scroll-cashflow-inner').append(data);
                      
 
                       isLoading = false;
                   },
                   error: function () {
                       $loader.hide();
                       isLoading = false;
                   }
               });
           }           
        });

        $('#store_website_id').change(function (){
            var storeWebsiteId = $(this).val();
            
            $('#credit_customer_id').empty();

            if(storeWebsiteId.length > 0){
                $.ajax({
                    url: "{{ url('customer/websites') }}",
                    type: 'POST',
                    
                    data: {
                        _token: '{{ csrf_token() }}',
                        store_website_id: storeWebsiteId
                    },
                    success: function (customers) {
                        if(customers.length > 0){
                            $.each(customers, function (k, v){
                                $('#credit_customer_id').append(`<option value="${v.id}">${v.name} Email - ${v.email}</option>`);
                            });
                        }
                    },  
                    error: function () {
                        //
                    }
                });
            }
        });

        $('.open-customer-credit').click(function (e) { 
            $('#credit_customer_id').select2();
        });

        $('#submit_credit_form').click(function (e) {
            e.preventDefault();
            if ($('#credit').val() == '') {
                $('#credit_error').text('Credit filed is required.');
                return false;
            } else {
                $('#credit_error').text('');
            }
            
            $.ajax({
                type: "POST",
                url: window.location.origin + '/livechat/create-credit',
                data: $('#credit_form').serialize(), // serializes the form's elements.
                success: function (data)
                {
                    if (data.status == 'success') {
                        alert('credit updated successfully.');
                        $('#credit_form').trigger("reset");
                        $('#create-customer-credit-modal').modal('toggle');
                    }else{
                        console.log(data);
                        //var msg = JSON.parse(JSON.parse(data[0]));
                        alert(data.msg);
                    }
                }, error: function (jqXHR, exception) {
                    var msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    alert(msg);
                }
            });
        });

        $('.get_email_log').click(function (e) {
        var cust_id = $(this).data('cust_id');
        //alert(cust_id);
        $.ajax({
            type: "POST",
            url: "{{route('credit.get.email.log')}}",
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                cust_id : cust_id
            },
            success: function (data)
            {
                if (data.code == '200') {
                    //alert('credit updated successfully.');
                    $('#email_log_histories').html(data.data);
                    $('#credit_email_log').modal('toggle');
                }else{
                    //console.log(data);
                    //var msg = JSON.parse(JSON.parse(data[0]));
                    alert(data.msg);
                }
            }, error: function (jqXHR, exception) {
                alert(jqXHR.msg);
            }
        });
        });
    
		function getLogs(customerId) {
			$('#display_logs').html('');
               $.ajax({
                   url: "{{url('customer/credit/logs')}}/"+customerId,
                   type: 'GET',
                   success: function (data) {
                      $('#display_logs').html(data.data);
                      $('#credit_logs').modal('show');
                   },
                   error: function () {
                       $loader.hide();
                       isLoading = false;
                   }
               });
           }

        function getHistories(customerId) {
            $('#display_histories').html('');
                    $.ajax({
                        url: "{{url('customer/credit/histories')}}/"+customerId,
                        type: 'GET',
                        success: function (data) {
                            $('#display_histories').html(data);
                            $('#credit_histories').modal('show');
                        },
                        error: function () {
                            $loader.hide();
                            isLoading = false;
                        }
                    });
                } 

        $(document).on("click",".repush-credit-balance",function(e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
               url: $this.attr("href"),
               type: 'GET',
               beforeSend : function() {
                    $('#loading-image').show();
               },
               success: function (data) {
                   $('#loading-image').hide();
                   if(data.code == 200)  {
                      toastr["success"](data.message, "Message")
                   }else {
                     toastr["error"](data.message, "Message")
                   }
               },
               error: function () {
                   $('#loading-image').hide();
                   toastr["error"]("Oops something went wrong", "Message")
               }
            });
        });
 </script>  
 @endsection
 
 
