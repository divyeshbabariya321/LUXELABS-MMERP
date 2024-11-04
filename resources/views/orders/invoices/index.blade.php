@extends('layouts.app')
@section('content')
<br>


<style>
.invoice-form {
   margin-top: -20px;
   display: flex;
    align-items: flex-end;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}
.invoice-form .form-group {
   width: 230px;
   margin: 0;
}
.invoice-form .pd-l0 {
   padding-left: 0px;
}
.invoice-form .select2-container .select2-selection--multiple {
   min-height: 34px;
   border: 1px solid #ddd !important;
}
.invoice-form .form-group ul.select2-selection__rendered {
    display: flex !important;
}

.invoice-form .select2-container .select2-search--inline .select2-search__field {
   height: 21px;
    padding-left: 10px;
    margin-left: 0;
}
.invoice-form  .btn-call-data {
    margin-top: 3px !important;
    margin-left: -10px;
}
.custom-row-desr {
   gap: 10px;
}
@media (max-width:1500px) {
   .invoice-form .form-group {
     margin-bottom: 0
   }
}
@media (max-width:1365px) {
   .invoice-form {
      padding-left: 15px;
      margin-top: 20px;
   }
   .custom-row {
      width: 100%;
   }
}
@media (max-width:1280px) {
   .invoice-form .form-group {
      width: 226px;
   }
}
</style>
<div class="col-md-12">

<a style="color:white;" title="Add invoice" class="btn btn-secondary add-invoice-btn pd-5 pull-right" data-id='q'>
+ Add New
</a>
<a style="color:white;" title="+ Add New Invoice without Order" data-toggle="modal" data-target="#invoicewithoutordermoder890" class="btn btn-warning pull-right addInvoiceWithoutOrderBtn mr-2">
+ Add New Invoice without Order
</a>
<br>

<div class="row pl-0 custom-row">
    <form action="" method="get" class="invoice-form">
       
            <div class="form-group">
               <label>From Date</label>
               <input type="text" onfocus="(this.type = 'date')"  class="form-control" name="invoice_date" value="@if(request('invoice_date') != null){{request('invoice_date')}} @endif" placeholder="Select Date" />
              
            </div>
            <div class="form-group">
               <label>To Date</label>
               <input type="text" onfocus="(this.type = 'date')"  class="form-control" name="invoice_to_date" value="@if(request('invoice_to_date') != null){{request('invoice_to_date')}} @endif" placeholder="Select Date" />
            </div>
        
            <div class="form-group cls_task_subject">
               <label>Select Invoice Number</label>
                <select class="form-control globalSelect2" name="invoice_number[]" id="invoice_number" data-placeholder="Select Invoice Number.." multiple>
                    @foreach($invoiceNumber as $number)
                        <option value="{{$number->invoice_number}}" {{ isset($_GET['invoice_number']) && in_array($number->invoice_number,$_GET['invoice_number']) ? 'selected' : '' }}>{{$number->invoice_number}}</option>
                    @endforeach
                </select>
            </div>
       
       
            <div class="form-group cls_task_subject">
               <label>Select Customer</label>
                <select class="form-control" name="customer_id[]" id="customer_id" data-placeholder="Select Customer Name.." multiple>
                   
                </select>
            </div>
       
        
            <div class="form-group cls_task_subject">
               <label>Select Website</label>
                <select class="form-control  globalSelect2" name="store_website_id[]" id="store_website_id" data-placeholder="Select Website Name.." multiple>
                    @foreach($websiteName as $website)
                        <option value="{{$website->id}}" {{ isset($_GET['store_website_id']) && in_array($website->id,$_GET['store_website_id']) ? 'selected' : '' }}>{{$website->website}}</option>
                    @endforeach
                </select>
            
        </div>
        <button type="submit"  class="btn btn-image btn-call-data"><img src="{{asset('/images/filter.png')}}"style="margin-top:-6px;"></button>
        <button  type="button" title="Clear Filter" class="btn btn-secondary clear-filter">
         Clear Filter
         </button>
    </form>
</div>

<div class="col-12">
   <div class="row custom-row-desr">
         <button type="button" title="Download" class="btn btn-primary download-selected-btn selectd-action-btns" style="display: none">
            Download Selected Invoices
         </button>
         <button  type="button" title="Print Invoice" class="btn btn-warning print-selected-btn selectd-action-btns" style="display: none">
            Resend Selected Invoices
         </button>
   </div>
</div>
    <div class="table-responsive" style="margin-top:20px;">
       <table class="table table-bordered" style="border: 1px solid #ddd;">
          <thead>
             <tr>
               <th> <input type="checkbox" name="checkAll" id="checkAll"> </th>
                <th>Date</th>
                <th>Invoice Number</th>
                <th>Customer Name</th>
                <th>Invoice Value</th>
                 <!-- <th>Price</th> -->
                 <th>Shipping</th>
                <th>Duty</th>
                <th>Currency</th>
                <th>Website</th>
                <th>Action</th>
             </tr>
          </thead>
          <tbody>
             @foreach ($invoices as $key => $invoice)
             <tr>
               <td> <input type="checkbox" name="checkedIds[]" class="checkboxes" value="{{$invoice->id}}"> </td>
                <td>{{ $invoice->invoice_date }}</td>
                <td>{{ $invoice->invoice_number }}</td>
                <td>
                   {{ $invoice->orders[0]->customer->name ?? '' }}
                </td>
                <td>
                   @php
                      $final_price=0;
                      $orderProductPrice = 0;
                      $productQty = 0;

                      foreach ($invoice->orders as $ord):
                         if(!$ord->order_product->isEmpty())  {
                            foreach ($ord->order_product as $item):
                               $final_price +=$item->product_price;
                               $orderProductPrice = $item->product_price;
                            endforeach;

                            $productQty = count($ord->order_product);
                         }
                      endforeach;

                   @endphp
                   {{ $final_price}}
                </td>
                 <!-- <td>{{$orderProductPrice * $productQty}}</td> -->
                 <td>{{$duty_shipping[$invoice->id]['shipping']}}</td>
                  <td>{{$duty_shipping[$invoice->id]['duty']}}</td>
                <td>
                    {{ $invoice->orders[0]->currency ?? '--' }}
                </td>
                <td>
                    {{ $invoice->orders[0]->customer->storeWebsite->website ?? '--' }}
                </td>


                <td>
                   <a class="btn btn-image open-invoice-email-popup" data-id="{{ $invoice->id }}">
                   <img title="Resend Invoice" src="/images/purchase.png" />
                   </a>
                   <a title="Edit Invoice" data-toggle="modal" data-target="#editInvoice" class="btn btn-image edit-invoice-btn" href="{{ route('order.edit.invoice',$invoice->id) }}">
                   <i class="fa fa-edit"></i>
                   </a>
                   </a>
                   <a title="Update Invoice Addresses"
                      data-address="{{$invoice->orders[0]->customer->address ?? ''}}"
                      data-city="{{$invoice->orders[0]->customer->city ?? ''}}"
                      data-country="{{$invoice->orders[0]->customer->country ?? ''}}"
                      data-pincode="{{$invoice->orders[0]->customer->pincode ?? ''}}"
                      data-codex="{{$invoice->orders[0]->customer->id ?? ''}}"
                      class="btn btn-image UpdateInvoiceAddresses"
                      data-id="{{$invoice->id}}"
                      >
                   <i class="fa fa-address-card-o"></i>
                   </a>
                   <a title="View Invoice" class="btn btn-image" href="{{ route('order.view.invoice',$invoice->id) }}">
                   <img title="View Invoice" src="/images/view.png" />
                   </a>
                   <a title="Download Invoice" class="btn btn-image" href="{{ route('order.download.invoice',$invoice->id) }}">
                   <i class="fa fa-download"></i>
                   </a>
                  <a title="Save For Later" class="btn btn-image saveLaterButton" invoiceNumber="{{$invoice->invoice_number}}" invoiceId="{{$invoice->id}}" href="javascript:void(0)">
                     <i class="fa fa-clock-o"></i>
                  </a>
                </td>
             </tr>
             @endforeach
          </tbody>
       </table>
       {{$invoices->links()}}
    </div>
    <div id="addInvoice" class="modal fade" role="dialog">
       <div class="modal-dialog modal-lg">
          <!-- Modal content-->
          <div class="modal-content ">
             <div class="modal-header">
                <div class="row" style="width:100%;">
                   <div class="form-group" style="width:100%;">
                      <label for="">Search :</label>
                      <select name="term" type="text" class="form-control" placeholder="Search" id="order-search" data-allow-clear="true">
                      <?php
                         if (request()->get('term')) {
                             echo '<option value="'.request()->get('term').'" selected>'.request()->get('term').'</option>';
                         }
                         ?>
                      </select>
                   </div>
                </div>
             </div>
             <div id="add-invoice-content" style="min-height:200px;">
             </div>
          </div>
       </div>
    </div>
    <div id="addInvoiceEmail"  class="modal" tabindex="-1" role="dialog">
       <div class="modal-dialog" role="document">
         <div class="modal-content">
           <div class="modal-header">
             <h5 class="modal-title">Invoice Email</h5>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
             </button>
           </div>
           <div class="modal-body">
                <div class="form-group" style="width:100%;">
                   <label for="">Email :</label>
                   <input type="text" name="invoice_email" class="form-control" placeholder="Enter email-address" id="invoice_email" data-allow-clear="true">
                   <input type="hidden" name="invoice_id" id="invoice_id" value="" />
                </div>
             </div>
           <div class="modal-footer">
                <button type="button" name="send_invoice_email" class="btn btn-secondary" id="send_invoice_email" data-allow-clear="true">Send invoice</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
           </div>
         </div>
       </div>
     </div>
     <div id="addInvoiceEmailSelected"  class="modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Invoice Email</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
               <form id="multi-select-invoice-form" class="multi-select-print">

               </form>
          </div>
          <div class="modal-footer">
               <button type="button" name="send_invoice_email_select" class="btn btn-secondary" id="send_invoice_email_select" data-allow-clear="true">Send invoice</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <div id="updateInvoiceAddressesold" class="modal fade" role="dialog">
       <div class="modal-dialog modal-lg">
          <!-- Modal content-->
          <div class="modal-content ">
             <div class="modal-header">
                <h4>Update Invoice Addresses</h4>
             </div>
             <div class="modal-body">
                <div id="update-invoice-address">
                   <form method="post" action="{{route('order.update.customer.address')}}">
                      @csrf
                      <div class="form-group">
                         <strong>Address:</strong>
                         <textarea id="address45" name="address" class="form-control"></textarea>
                      </div>
                      <div class="form-group">
                         <strong>City:</strong>
                         <input id="city45" name="city" class="form-control"/>
                         <input id="codex45" name="codex" type="hidden" class="form-control"/>
                      </div>
                      <div class="form-group">
                         <strong>Country:</strong>
                         <input id="country45" name="country" class="form-control"/>
                      </div>
                      <div class="form-group">
                         <strong>Pincode:</strong>
                         <input id="pincode45" name="pincode" class="form-control"/>
                      </div>
                      <button type="submit" name="update_details" class="btn btn-primary btn-sm">Update Address</button>
                   </form>
                </div>
             </div>
          </div>
       </div>
    </div>
    <div id="updateInvoiceAddresses-modal" class="modal fade" role="dialog">
       <div class="modal-dialog modal-lg">
          <div class="modal-content ">
             <div class="modal-header">
                <h4>Update Invoice</h4>
             </div>
             <div class="modal-body">

             </div>
          </div>
       </div>
    </div>

    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;"></div>
    @include("partials.modals.edit-invoice-modal")
    @include("partials.modals.invoice-without-order-model")
</div>
<script>
   $(document).on("click",".open-invoice-email-popup",function(e){
         e.preventDefault();
         var $this = $(this);
         $.ajax({
            url: "/order/"+$this.data("id")+"/get-invoice-customer-email",
            type: "get"
          }).done(function(response) {
            $('#addInvoiceEmail').modal('show');
            $("#invoice_email").val(response.email);
            $("#invoice_id").val(response.id);
          }).fail(function(errObj) {
             $("#addInvoiceEmail").hide();
          });
   });

   $(document).on("click","#send_invoice_email_select",function(e){
      e.preventDefault();
      var $this = $(this);
      var id = $("#invoice_id").val();
      $.ajax({
      headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/order/mail-invoice-multi-select",
      type: "get",
      data:$('#multi-select-invoice-form').serialize(),
      beforeSend: function() {
         $("#loading-image").show();
      }
      }).done(function(response) {
         if(response.code == 200) {
         toastr['success'](response.message);
         }else{
         toastr['error'](response.message);
         }
         $("#loading-image").hide();
         $("#addInvoiceEmail").modal('hide');
      }).fail(function(xhr, status, error) {
            $("#loading-image").hide();
            var err = eval("(" + xhr.responseText + ")");
            toastr['error']( err.message );
            $("#addInvoiceEmail").modal('hide');
      });
   });

   $(document).on("click","#send_invoice_email",function(e){
      e.preventDefault();
      var $this = $(this);
      var id = $("#invoice_id").val();
      $.ajax({
      headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/order/"+id+"/mail-invoice",
      type: "get",
      beforeSend: function() {
         $("#loading-image").show();
      }
      }).done(function(response) {
         if(response.code == 200) {
         toastr['success'](response.message);
         }else{
         toastr['error'](response.message);
         }
         $("#loading-image").hide();
         $("#addInvoiceEmail").modal('hide');
      }).fail(function(xhr, status, error) {
            $("#loading-image").hide();
            var err = eval("(" + xhr.responseText + ")");
            toastr['error']( err.message );
            $("#addInvoiceEmail").modal('hide');

      });
   });

      $(document).on("click",".edit-invoice-btn",function(e){
         e.preventDefault();
         var $this = $(this);
         $.ajax({
            url: "/order/"+$this.data("id")+"/edit-invoice",
            type: "get"
          }).done(function(response) {
             $("#edit-invoice-content").html(response);
          }).fail(function(errObj) {
            // $("#editInvoice").hide();
          });
      });

      $(document).on("click",".add-invoice-btn",function(e){
         e.preventDefault();
         $('#addInvoice').modal('show');
      });

   //Invoice without order --START

      $(document).on("click",".addInvoiceWithoutOrderBtn",function(e){

         e.preventDefault();
         var $this = $(this);
         $.ajax({
            url: "/invoice/without-order/",
            type: "get"
          }).done(function(response) {
             $("#invoice-without-order-content").html(response);
          }).fail(function(errObj) {
            // $("#editInvoice").hide();
          });
      });
   //Invoice without order --END
      $('#order-search').select2({
              tags: true,
              width : '100%',
              ajax: {
                  url: '/order/order-search',
                  dataType: 'json',
                  delay: 750,
                  data: function (params) {
                      return {
                          q: params.term, // search term
                      };
                  },
                  processResults: function (data, params) {
                      for (var i in data) {
                          data[i].id = data[i].id ? data[i].id : data[i].order_id;
                      }

                      params.page = params.page || 1;

                      return {
                          results: data,
                          pagination: {
                              more: (params.page * 30) < data.total_count
                          }
                      };
                  },
              },
              placeholder: 'Search by Order id, customer id,name, no',
              escapeMarkup: function (markup) {
                  return markup;
              },
              minimumInputLength: 1,
              templateResult: function (order) {
                  if (order.loading) {
                      return order.order_id;
                  }
                  if (order.order_id) {
                      return "<p><b>Order id:</b> " + order.order_id + ": <b>Name:</b> " + order.name + " <b>Phone:</b> " + order.phone +"</p>";
                  }
              },
              templateSelection: (order) => order.text || order.order_id,

          });

          $('#order-search').on('select2:select', function (e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                url: "/order/"+$this.val()+"/add-invoice",
                type: "get"
              }).done(function(response) {
                $("#add-invoice-content").html(response);
              }).fail(function(errObj) {
                toastr['error'](errObj.responseJSON.message);
              });
          });
   $('.UpdateInvoiceAddresses456').on('click',function(){
      var address = $(this).data('address')
      var country = $(this).data('country')
      var city = $(this).data('city')
      var pincode = $(this).data('pincode')
      var codex = $(this).data('codex')

      $('#country45').attr("value",country)
      $('#pincode45').attr("value",pincode)
      $('#city45').attr("value",city)
      $('#address45').text(address)
      $('#codex45').attr("value",codex)
   });

   $(document).on("click",".UpdateInvoiceAddresses",function() {
      var invoiceId = $(this).data("id");
      $.ajax({
        url : "/order/invoices/"+invoiceId+"/get-details",
        type: "get"
      }).done(function(response) {
         //$("#add-invoice-content").html(response);
          $("#updateInvoiceAddresses-modal").find(".modal-body").html(response);
          $("#updateInvoiceAddresses-modal").modal("show");
      }).fail(function(errObj) {
          //toastr['error'](errObj.responseJSON.message);
      });
   });

   $(document).on("click",".btn-update-invoice",function(e) {
      e.preventDefault();
      var invoiceId = $(this).data("id");
      var form = $(this).closest("form");
      $.ajax({
        url : "/order/invoices/"+invoiceId+"/update-details",
        type: "post",
        beforeSend: function() {
          $("#loading-image").show();
        },
        data: form.serialize(),
        dataType:"json"
      }).done(function(response) {
        $("#loading-image").hide();
        if(response.code == 200) {
          toastr['success'](response.message);
        }else{
          toastr['error'](response.message);
        }
      }).fail(function(errObj) {
        $("#loading-image").hide();
          toastr['error'](errObj.responseJSON.message);
      });
   });
   
   $(document).on('click','.saveLaterButton',function(){
      let invoiceId = $(this).attr('invoiceId');
      let invoiceNumber = $(this).attr('invoiceNumber');
      $.ajax({
         type:"get",
         url:"{{ url('order/invoices/saveLater') }}",
         data:{invoiceId:invoiceId,invoiceNumber:invoiceNumber},
         success:function(data){
            toastr['success']('Invoice saved for print later!.');
         }
      })
   })   
   
   $(document).on('change','input[name="checkAll"]',function(){
      if($(this).is(':checked')){
         $('.selectd-action-btns').show();
         $('.checkboxes').prop('checked',true);
      }else{
         $('.selectd-action-btns').hide();
         $('.checkboxes').prop('checked',false);
      }
   })
   function checkCheckboxIsChecked(){
      let checkIds = [];
      $('.checkboxes').each(function(){
         if($(this).is(':checked')){
            checkIds.push($(this).val());
         }
      });
      return checkIds;
   }
   $(document).on('click','.download-selected-btn',function(){
      let check = checkCheckboxIsChecked();
      if(check.length === 0){
         alert('Please select atleast one record');
         return;
      }
      let url = '{{ url("order/download-invoice/") }}';
      for(let i = 0; i < check.length; i++){
         window.open(url+'/'+check[i]);
      }
   })

   $(document).on('click','.print-selected-btn',function(){
      let check = checkCheckboxIsChecked();
      if(check.length === 0){
         alert('Please select atleast one record');
         return;
      }
      $.ajax({
            url: "{{ url('order/get-invoice-customer-email-selected') }}"+"?ids="+check,
            type: "get",
      }).done(function(response) {
         let html = '';
         for(var i = 0; i < response.length; i++) {
            html += `<div class="form-group" style="width:100%;">
                   <label for="">Email :</label>
                   <input type="text" name="invoice_email[]" class="form-control" placeholder="Enter email-address" value="${response[i].email}" data-allow-clear="true">
                   <input type="hidden" name="invoice_id[]" value="${response[i].id}" />
                </div>`;
         }
         $('.multi-select-print').html(html);
         $('#addInvoiceEmailSelected').modal('show');
      }).fail(function(errObj) {
            $("#addInvoiceEmailSelected").hide();
      });
   })
   
   $(document).on('change','.checkboxes',function(){
      let check = checkCheckboxIsChecked();
      if(check.length === 0){
         $('.selectd-action-btns').hide();
         $('input[name="checkAll"]').prop('checked',false);
      }else{
         $('.selectd-action-btns').show();
      }
   })
   
   $(document).on('click','.clear-filter',function(){
      window.location.href="{{url('order/invoices')}}"
   })

   $(document).on('keyup','#customer_id',function(){
      alert();
      select2Functions();	
   })
   function select2Functions(){
      $("#customer_id").select2({
         ajax: {
            url: "{{ url('order/get-order-invoice-users')}}",
            type: "get",
            dataType: 'json',
            delay: 250,
            data: function(params) {
               return {
                  searchTerm: params.term,// search term
               };
            },
            processResults: function(response) {
               return {
                  results: response
               };
            },
            cache: true
         }
      });
   }
   select2Functions();
   
</script>
@endsection