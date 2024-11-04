@extends('layouts.app')
@section('content')
<br>
<style>
   .save-list-table {
      display: flex;
      padding: 0px 15px;
      gap: 15px;
      flex-wrap: wrap;
   }
   .save-list-table .form-group {
      width: 200px;
      margin-bottom: 0;
   }
   .save-list-table .btn.btn-image {
      margin-top: 5px;
      display: flex;
      align-items: center;
   }
</style>
<div class="col-md-12">

<div class="row pl-0">
    <form class="save-list-table" action="" method="get">
       
            <div class="form-group">
               <label for="invoicenumber">From Date</label>
                <input type="text" onfocus="(this.type = 'date')"  class="form-control" name="from_date" value="@if(request('from_date') != null){{request('from_date')}} @endif" placeholder="Select Date" />
            </div>
            <div class="form-group">
               <label for="invoicenumber">To Date</label>
                <input type="text" onfocus="(this.type = 'date')"  class="form-control" name="to_date" value="@if(request('to_date') != null){{request('to_date')}} @endif" placeholder="Select Date" />
            </div>
            <div class="form-group">
               <label for="invoicenumber">Invoice Number</label>
               <select class="form-control globalSelect2" multiple="true" id="invoice_num" name="invoice_num[]">
                  @foreach($invoiceNumber as $num)
                  <option value="{{ $num['invoice_number']}}" 
                  @if(is_array(request('invoice_num')) && in_array($num['invoice_number'], request('invoice_num')))
                     selected
                  @endif >{{ $num['invoice_number'] }}</option>
                  @endforeach
            </select> 
         </div>
         <div class="form-group">
            <label for="customer_name">customer Name</label>
            <select class="form-control globalSelect2" multiple="true" id="customer_name" name="customer_name[]">
                @foreach($customerName as $customer)
                    <option value="{{ $customer['name'] }}" @if(is_array(request('customer_name')) && in_array($customer['name'], request('customer_name'))) selected @endif>
                        {{ $customer['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        

        <button type="submit"  class="btn btn-image btn-call-data"><img src="{{asset('/images/filter.png')}}"style="margin-top:-6px;"></button>
        <a href="{{ url('order/invoices/saveLaterList') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
    </form>
</div>
<div class="col-12">
    <div class="row custom-row-desr">
          <button type="button" title="Download" class="btn btn-primary download-selected-btn selectd-action-btns" style="display: none">
            Print Invoices
          </button>
    </div>
 </div>

    <div class="table-responsive" style="margin-top:20px;">
       <table class="table table-bordered" style="border: 1px solid #ddd;">
          <thead>
             <tr>
               <th> <input type="checkbox" name="checkAll" id="checkAll"> </th>
                <th>Print Created Date</th>
                <th>Invoice Number</th>
                <th>Customer Name</th>
                <th>Invoice Value</th>
                <th>Action</th>
             </tr>
          </thead>
          <tbody>
             @foreach ($invoices as $key => $invoice)
             <tr>
               <td> <input type="checkbox" name="checkedIds[]" class="checkboxes" value="{{$invoice->id}}"> </td>

               <td>{{ date('Y-m-d',strtotime($invoiceList[$key]->created_at)) }}</td>
               <td>{{ $invoice->invoice_number }}</td>
               <td>{{ $invoice->orders[0]->customer->name ?? '' }}</td>
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
                  <td>
                     <a title="View Invoice" target="_blank" class="btn btn-image" href="{{url('order/invoices/ViewsaveLaterList/'.$invoice->id)}}">
                        <img title="View Invoice" src="/images/view.png" />
                     </a>
                </td>
             </tr>
             @endforeach
          </tbody>
       </table>
       {{$invoiceList->links()}}
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;"></div>
    @include("partials.modals.edit-invoice-modal")
    @include("partials.modals.invoice-without-order-model")
</div>
<script>
   
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
      let url = '{{ url("order/invoices/ViewsaveLaterList/") }}';
      for(let i = 0; i < check.length; i++){
         window.open(url+'/'+check[i]);
      }
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
</script>
@endsection