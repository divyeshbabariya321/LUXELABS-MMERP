@extends('layouts.app')

@section('title', 'Orders List')

@section('styles')
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
  <link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" />
<style>
  table {
    font-size: 14px;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 14px;
    line-height: 1.42857143;
    background-color: #fff;
  }
.ajax-loader{
    position: fixed;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1060;
}
.inner_loader {
	top: 30%;
    position: absolute;
    left: 40%;
    width: 100%;
    height: 100%;
}
.pd-5 {
  padding:5px !important;
}
.pd-3 {
  padding:3px !important;
}
.status-select-cls .multiselect {
  width:100%;
}
.btn-ht {
  height:30px;
}
.status-select-cls .btn-group {
  width:100%;
  padding: 0;
}
.table.table-bordered.order-table th a{
  color:black!important;
}
</style>
@endsection

@section('large_content')
	<div class="ajax-loader" style="display: none;">
		<div class="inner_loader">
		<img src="{{ asset('/images/loading2.gif') }}">
		</div>
	</div>
  <div class="row">
        <div class="col-12" style="padding:0px;">
            <h2 class="page-heading">Orders List ({{$totalOrders}})</h2>
        </div>
           <div class="col-10" style="padding-left:0px;">
            <div>
            <form class="form-inline" action="{{ route('order.index') }}" method="GET">
                <div class="form-group col-md-2 pd-3">
                    <label style=" float: left;">Search keywords :</label>
                  <input style="width:100%;" name="term" type="text" class="form-control"
                         value="{{ isset($term) ? $term : '' }}"
                         placeholder="Search">
                </div>

                 <div class="form-group col-md-2 pd-3 status-select-cls select-multiple-checkbox">
                    <label style=" float: left;">Select Order Status :</label>
                  <select class="form-control select-multiple " name="status[]" multiple>
                      @foreach ($order_status_list as $id => $order_st)
                        <option value="{{ $id }}" {{ isset($order_status) && in_array($id, $order_status) ? 'selected' : '' }}>{{ $order_st }}</option>
                      @endforeach
                  </select>
                </div>

                 <!-- <div class="form-group col-md-2 pd-3">
                    <label style=" float: left;">Select Brands :</label>
                  {{ html()->select("brand_id[]", ["" => "-- Select Brands --"] + $brandList, request('brand_id', []))->class("form-control select2") }}
                </div> -->

                <div class="form-group col-md-1 pd-3">
                    <label style=" float: left;">Advance :</label>
                  <input style="width:100%;" name="advance_detail" type="text" class="form-control"
                         value="{{ isset($advance_detail) ? $advance_detail : '' }}"
                         placeholder="Search">
                </div>

                <div class="form-group col-md-1 pd-3">
                    <label style=" float: left;">Balance :</label>
                  <input style="width:100%;" name="balance_amount" type="text" class="form-control"
                         value="{{ isset($balance_amount) ? $balance_amount : '' }}"
                         placeholder="Search">
                </div>

                 <div class="form-group col-md-1 pd-3">
                    <label style=" float: left;">Date :</label>
                  <input type='date' class="form-control" name="date" value="{{ isset($date) ? $date : '' }}" style=" width: 100%;"/>
                </div>

                <div class="form-group col-md-1 pd-3">
                    <label style=" float: left;">ETA :</label>
                  <input type='date' class="form-control" name="estimated_delivery_date" value="{{ isset($estimated_delivery_date) ? $estimated_delivery_date : '' }}" style=" width: 100%;"/>
                </div>
                   <div class="form-group col-md-2 pd-3">
                  <div class="form-group ml-3">
                    <label style=" float: left;">Select Website :</label>
                      <select class="form-control select2 globalSelect2" multiple="true" name="store_website_id[]" id="select2Multiple" >
                      <option value="">Select Site Name</option>
                      @forelse ($registerSiteList as $key => $item)
                          @if(isset($store_site) && in_array($key, $store_site))
                                <option value="{{ $key }}" selected>{{ $item }}</option>
                              @else
                                <option value="{{ $key }}">{{ $item }}</option>
                          @endif
                      @empty
                      @endforelse
                      </select>
                  </div>
                  </div>
                   <div class="form-group col-md-1 pd-3">
                  <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                  </div>
              </form>
            </div>
             </div>
          <div class="col-md-2" style="padding:0px;">
                  <a class="btn btn-xs btn-secondary" href="{{ route('order.create') }}">+</a>
                  <a href="{{ action([\App\Http\Controllers\OrderController::class, 'downloadOrderInPdf'], Request::all()) }}" class="btn btn-secondary btn-xs">Download</a>
                  <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#ordersdatatablecolumnvisibilityList
                  ">Column Visiblity</button>
              </div>
        </div>
<div class="row">
@include('partials.flash_messages')
    <?php if (! empty($statusFilterList)) { ?>
      <div class="row col-md-12" style="font-size: smaller;">
          <?php foreach ($statusFilterList as $listFilter) { ?>
            <div class="card">
                <div class="card-header">
                <?php echo ucwords($listFilter['order_status']); ?> (<?php echo $listFilter['total']; ?>)
                </div>
                <!-- <div class="card-body">
                    <?php echo $listFilter['total']; ?>
                </div> -->
            </div>
        <?php } ?>
      </div>
    <?php } ?>
</div>

<div class="row">
  <div class="col-md-12" style="padding:0px;">
      <div class="pull-right">
        <a href="#" class="btn btn-xs btn-secondary magento-order-status">Magento Order Status Mapping</a>
        <a href="#" class="btn btn-xs btn-secondary delete-orders">Archive</a>
        <a href="#" class="btn btn-xs update-customer btn-secondary">Update</a>
        <button type="button" class="btn btn-xs btn-secondary order-status-listing" onclick="listStatusColor()">Add Color Code For Order Status</button>
    </div>
  </div>
</div>
<div class="row">
    <div class="infinite-scroll" style="width:100%;">
	<div class=" mt-2">
      <table class="table table-bordered order-table table-condensed table-striped" style="border: 1px solid #5A6268 !important;">
        <thead>
        <tr>
            <th>Select</th>

            @if(!empty($dynamicColumnsToShowPostman))
                @if (!in_array('ID', $dynamicColumnsToShowPostman))
                    <th style="width: 7%;"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">ID</a></th>
                @endif

                @if (!in_array('Date', $dynamicColumnsToShowPostman))
                    <th ><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=date{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Date</a></th>
                @endif

                @if (!in_array('Client', $dynamicColumnsToShowPostman))
                    <th ><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=client_name{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Client</a></th>
                @endif

                @if (!in_array('Site Name', $dynamicColumnsToShowPostman))
                    <th >Site Name</th>
                @endif

                @if (!in_array('Products', $dynamicColumnsToShowPostman))
                    <th >Products</th>
                @endif

                @if (!in_array('Eta', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;" title="Eta"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=estdeldate{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Eta</a></th>
                @endif

                @if (!in_array('Brands', $dynamicColumnsToShowPostman))
                    <th >Brands</th>
                @endif

                @if (!in_array('Order Status', $dynamicColumnsToShowPostman))
                    <th style="width: 10%;" title="Order Status"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Order Status</a></th>
                @endif

                @if (!in_array('Order Product Status', $dynamicColumnsToShowPostman))
                    <th title="Order Product Status">Order Product Status</th>
                @endif

                @if (!in_array('Product Status', $dynamicColumnsToShowPostman))
                    <th title="Product Status"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Product Status</a></th>
                @endif

                @if (!in_array('Advance', $dynamicColumnsToShowPostman))
                    <th><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=advance{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Advance</a></th>
                @endif

                @if (!in_array('Balance', $dynamicColumnsToShowPostman))
                    <th><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}{{ isset($order_status) ? implode('&', array_map(function($item) {return 'status[]='. $item;}, $order_status)) . '&' : '&' }}sortby=balance{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Balance</a></th>
                @endif

                @if (!in_array('Waybill', $dynamicColumnsToShowPostman))
                    <th>Waybill</th>
                @endif

                @if (!in_array('Price', $dynamicColumnsToShowPostman))
                    <th>Price</th>
                @endif

                @if (!in_array('Shipping', $dynamicColumnsToShowPostman))
                    <th>Shipping</th>
                @endif

                @if (!in_array('Duty', $dynamicColumnsToShowPostman))
                    <th>Duty</th>
                @endif
                @if (!in_array('Action', $dynamicColumnsToShowPostman))
                    <th>Action</th>
                @endif
            @else            
                <th style="width: 7%;"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">ID</a></th>
                <th ><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=date{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Date</a></th>
                <th ><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=client_name{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Client</a></th>
                <th >Site Name</th>
                <th>Products</th>
                <th style="width: 7%;"title="Eta"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=estdeldate{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Eta</a></th>
                <th>Brands</th>
                <th style="width: 10%;" title="Order Status"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Order Status</a></th>
                <th title="Order Product Status">Order Product Status</th>
                <th title="Product Status"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Product Status</a></th>
                <th><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=advance{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Advance</a></th>
                <th><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}{{ isset($order_status) ? implode('&', array_map(function($item) {return 'status[]='. $item;}, $order_status)) . '&' : '&' }}sortby=balance{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Balance</a></th>
                
                <th>Waybill</th>
                <th>Price</th>
                <th>Shipping</th>
                <th>Duty</th>
                <th>Action</th>
            @endif
         </tr>
        </thead>

        <tbody style="color:font-size: small;">
			@foreach ($orders_array as $key => $order)

             @php
               $extraProducts = [];
               $orderProductPrice = 0;
               $productQty = 0;

               if(!$order->order_product->isEmpty())  {
                  foreach($order->order_product as $orderProduct) {
                    $extraProducts[] = [
                      "sku" => $orderProduct->sku,
                      "qty" => $orderProduct->qty,
                      "product_price" => $orderProduct->product_price,
                      "name" => ($orderProduct->product) ? $orderProduct->product->name : ""
                    ];

                    $orderProductPrice = $orderProduct->product_price;

                  }
               }
               $order_items = $order->order_product;
             @endphp

            @if (count($order_items) > 0)
            @foreach ($order_items as $items)
            @if(!empty($dynamicColumnsToShowPostman))
                <tr style="background-color: {{$order->status?->color}}"; class="{{ \App\Helpers::statusClass($order->assign_status ) }}">
                    
                    <td class="text-center">
                        <span class="td-mini-container">
                            <input type="checkbox" class="selectedOrder" name="selectedOrder" value="{{$order->id}}">
                        </span>
                    </td>
                    
                    @if (!in_array('ID', $dynamicColumnsToShowPostman))
                        <td class="table-hover-cell">
                            <div class="form-inline " >
                                @if ($order->is_priority == 1)
                                <strong class="text-danger mr-1">!!!</strong>
                                @endif
                                <span class="td-mini-container">
                                    <span style="font-size:14px;" class="toggle-title-box has-small" data-small-title="<?php echo ($order->order_id) ? substr($order->order_id, 0, 15) : '' ?>" data-full-title="<?php echo ($order->order_id) ? $order->order_id :
                                    '' ?>">
                                    <?php echo (strlen($order->order_id) > 15) ? substr($order->order_id, 0, 15).'..' : $order->order_id;
    ?>
                                    </span>
                                </span>
                            </div>
                        </td>
                    @endif

                    @if (!in_array('Date', $dynamicColumnsToShowPostman))
                        <td class="Website-task" title="{{ Carbon\Carbon::parse($order->order_date)->format('d-m') }}">{{ Carbon\Carbon::parse($order->order_date)->format('d-m') }}</td>
                    @endif

                    @if (!in_array('Client', $dynamicColumnsToShowPostman))
                        <td class="expand-row table-hover-cell Website-task" style="color:grey;">
                            @if ($order->customer)
                                <span class="td-mini-container">
                                    <a style="color: #6c757d;" href="{{ route('customer.show', $order->customer->id) }}">{{ strlen($order->customer->name) > 15 ? substr($order->customer->name, 0, 13) . '...' : $order->customer->name }}</a>
                                </span>
                                <span class="td-full-container hidden">
                                    <a style="color: #6c757d;" href="{{ route('customer.show', $order->customer->id) }}">{{ $order->customer->name }}</a>
                                </span>
                            @endif
                        </td>
                    @endif

                    @if (!in_array('Site Name', $dynamicColumnsToShowPostman))
                        <td class="expand-row table-hover-cell">
                            @if ($order->storeWebsiteOrder)
                                @if ($order->storeWebsiteOrder->storeWebsite)
                                    @php
                                    $storeWebsite = $order->storeWebsiteOrder->storeWebsite;
                                    @endphp
                                    <span class="td-mini-container">
                                        <a style="color: #6c757d;" href="{{$storeWebsite->website}}" target="_blank">{{ strlen($storeWebsite->website) > 15 ? substr($storeWebsite->website, 0, 13) . '...' : $storeWebsite->website }}</a>
                                    </span>
                                    <span class="td-full-container hidden">
                                        <a style="color: #6c757d;" href="{{$storeWebsite->website}}" target="_blank">{{ $storeWebsite->website }}</a>
                                    </span>
                                @endif
                            @endif
                        </td>
                    @endif

                    @if (!in_array('Products', $dynamicColumnsToShowPostman))
                        <td class="expand-row table-hover-cell">
                            @php $count = 0; @endphp
                            <div class="d-flex">
                                <div class="">
                                @foreach ($order->order_product as $order_product)
                                    @if ($order_product->product)
                                        @if ($order_product->product->hasMedia($attach_image_tag) && $order_product->product->id == $items->product_id)
                                            <span class="td-mini-container">
                                                @if ($count == 0)
                                                <?php
                foreach ($order_product->product->getMedia($attach_image_tag) as $media) { ?>
                                                    <a data-fancybox="gallery" href="{{ getMediaUrl($media) }}">#{{$order_product->product->id}}<i class="fa fa-eye"></i></a>
                                                    <a class="view-supplier-details" data-id="{{$order_product->id}}" href="javascript:;"><i class="fa fa-shopping-cart"></i></a>
                                                    <br/>
                                                    <?php break;
                } ?>
                                                    @php ++$count; @endphp
                                                    @endif
                                                    </span>
                                                    <span class="td-full-container hidden">
                                                    @if ($count >= 1)
                                                    <?php foreach ($order_product->product->getMedia($attach_image_tag) as $media) { ?>
                                                    <a data-fancybox="gallery" href="{{ getMediaUrl($media) }}">VIEW
                                                    <?php break;
                                                    } ?>
                                                    #{{$order_product->product->id}}</a>
                                                    @php $count++; @endphp
                                                @endif
                                            </span>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    @endif

                    @if (!in_array('Eta', $dynamicColumnsToShowPostman))
                      <td>
                        <div style="display:inline;">{{($order->estimated_delivery_date)?$order->estimated_delivery_date:'---'}}</div>

                      <i style="color:#6c757d;" class="fa fa-pencil-square-o show-est-del-date" data-id="{{$order->id}}" data-new-est="{{($order->estimated_delivery_date)?$order->estimated_delivery_date:''}}" aria-hidden="true"></i>
                      <i style="color:#6c757d;" class="fa fa-info-circle est-del-date-history" data-id="{{$order->id}}"  aria-hidden="true"></i>

                      </td>
                  @endif

                  @if (!in_array('Brands', $dynamicColumnsToShowPostman))
                      <td class="Website-task">
                        <?php
                           $totalBrands = explode(',', $order->brand_name_list);
    if (count($totalBrands) > 1) {
        $str = 'Multi';
    } else {
        $str = $order->brand_name_list;
    }
    ?>
                        <span style="font-size:14px;">{{$str}}</span>
                      </td>
                  @endif

                  @if (!in_array('Order Status', $dynamicColumnsToShowPostman))
              <td class="expand-row table-hover-cell">
                <div class="form-group" style="margin-bottom:0px;">
                  <select data-placeholder="Order Status" class="form-control order-status-select order-status-select-{{$order->id}}" id="supplier" data-id={{$order->id}} >
                            <optgroup label="Order Status">
                              <option value="">Select Order Status</option>
                                @foreach ($order_status_list as $id => $status)
                                    <option value="{{ $id }}" {{ $order->order_status_id == $id ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </optgroup>
                    </select>
                </div>
              </td>
              @endif

              @if (!in_array('Order Product Status', $dynamicColumnsToShowPostman))
              <td class="expand-row table-hover-cell">
                <div class="form-group" style="margin-bottom:0px;">
                  <select data-placeholder="Order Product Status" class="form-control order-product-status-select" data-order_product_id={{$items->id}} >
                            <optgroup label="Order Product Status">
                              <option value="">Select Order Product Status</option>
                                @foreach ($order_status_list as $id => $status)
                                    <option value="{{ $id }}" {{ $items->order_product_status_id == $id ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </optgroup>
                    </select>
                </div>
              </td>
              @endif

              @if (!in_array('Product Status', $dynamicColumnsToShowPostman))
              <td class="expand-row table-hover-cell">
                <div class="form-group" style="margin-bottom:0px;">
                  <select data-placeholder="Product Status" class="form-control product_order_status_delivery" id="product_delivery_status" data-id={{$order->id}} data-order_product_item_id={{$items->id}} >
                            <optgroup label="Product Status">
                              <option value="">Select product Status</option>
                                @foreach ($order_status_list as $id => $status)
                                    <option value="{{ $id }}" {{ $items->delivery_status == $id ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </optgroup>
                    </select>
                </div>
              </td>
              @endif

              @if (!in_array('Advance', $dynamicColumnsToShowPostman))
                <td>{{ $order->advance_detail }}</td>
                @endif

                @if (!in_array('Balance', $dynamicColumnsToShowPostman))
                <td>{{ $order->balance_amount }}</td>
                @endif

              
              

              @if (!in_array('Waybill', $dynamicColumnsToShowPostman))
              <td>
                @if ($order->waybill)
                  {{ $order->waybill->awb }}
                @else
                  -
                @endif
              </td>
              @endif

              @if (!in_array('Price', $dynamicColumnsToShowPostman))
              <td>{{$orderProductPrice * $productQty}}</td>
              @endif

              @if (!in_array('Shipping', $dynamicColumnsToShowPostman))
              <td>{{$duty_shipping[$order->id]['shipping']}}</td>
              @endif

              @if (!in_array('Duty', $dynamicColumnsToShowPostman))
              <td>{{$duty_shipping[$order->id]['duty']}}</td>
              @endif

              @if (!in_array('Action', $dynamicColumnsToShowPostman))
              <td>
                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$items->id}}')"><i class="fa fa-arrow-down"></i></button>
                </td>
                @endif
            </tr>
            
            <tr class="action-btn-tr-{{$items->id}} d-none">
            <td>Action</td>
            <td colspan="16">
                <div class="align-items-center">
                    <a type="button" title="Payment history" class="btn btn-image pd-5 btn-ht cancel-transaction-btn pull-left" data-id="{{$order->id}}">
                        <i class="fa fa-close"></i>
                    </a>
                    <a type="button" title="Payment history" class="btn btn-image pd-5 btn-ht payment-history-btn pull-left" data-id="{{$order->id}}">
                        <i class="fa fa-history"></i>
                    </a>
                    <a class="btn btn-image pd-5 btn-ht" href="{{route('purchase.grid')}}?order_id={{$order->id}}">
                        <img title="Purchase Grid" style="display: inline; width: 15px;" src="{{ asset('images/customer-order.png') }}" alt="">
                    </a>
                    <a class="btn btn-image pd-5 btn-ht" href="{{ route('order.show',$order->id) }}"><img title="View order" src="{{asset('images/view.png')}}" /></a>
                    <a class="btn btn-image send-invoice-btn pd-5 btn-ht" data-id="{{ $order->id }}" href="{{ route('order.show',$order->id) }}">
                        <img title="Send Invoice" src="{{asset('images/purchase.png')}}" />
                    </a>
                    <a title="Preview Order" class="btn btn-image preview-invoice-btn pd-5 btn-ht" href="{{ route('order.perview.invoice',$order->id) }}">
                        <i class="fa fa-hourglass"></i>
                    </a>
                    @if ($order->waybill)
                        <a title="Download Package Slip pd-5 btn-ht" href="{{ route('order.download.package-slip', $order->waybill->id) }}" class="btn btn-image" href="javascript:;">
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </a>
                        <a title="Track Package Slip pd-5 btn-ht" href="javascript:;" data-id="{{ $order->waybill->id }}" data-awb="{{ $order->waybill->awb }}" class="btn btn-image track-package-slip">
                            <i class="fa fa fa-globe" aria-hidden="true"></i>
                        </a>
                    @endif
                    <a title="Generate AWB" data-order-id="<?php echo $order->id; ?>" data-items='@json($extraProducts)'  data-customer='<?php echo ($order->customer) ? json_encode($order->customer) : json_encode([]); ?>' class="btn btn-image generate-awb pd-5 btn-ht" href="javascript:;"  >
                        <i class="fa fa-truck" aria-hidden="true"></i>
                    </a>

                    <a title="Preview Sent Mails" data-order-id="<?php echo $order->id; ?>" class="btn btn-image preview_sent_mails pd-5 btn-ht" href="javascript:;"  ><i class="fa fa-eye" aria-hidden="true"></i></a>

                    <a title="View customer address" data-order-id="<?php echo $order->id; ?>"  class="btn btn-image customer-address-view pd-5 btn-ht" href="javascript:;"  >
                        <i class="fa fa-address-card" aria-hidden="true"></i>
                    </a>
                    

                    {{ html()->form('DELETE', route('order.destroy', [$order->id]))->style('display:inline;margin-bottom:0px;height:30px;')->open() }}
                    <button type="submit" class="btn btn-image pd-5 btn-ht"><img title="Archive Order" src="{{asset('images/archive.png')}}" /></button>
                    {{ html()->form()->close() }}
                    <?php
                    if ($order->auto_emailed) {
                        $title_msg = 'Resend Email';
                    } else {
                        $title_msg = 'Send Email';
                    }
    ?>
                    <a title="<?php echo $title_msg; ?>" class="btn btn-image send-order-email-btn pd-5 btn-ht" data-id="{{ $order->id }}" href="javascript:;">
                        <i class="fa fa-paper-plane" aria-hidden="true"></i>
                    </a>
                    @if(auth()->user()->checkPermission('order-delete'))
                        {{ html()->form('DELETE', route('order.permanentDelete', [$order->id]))->style('display:inline;margin-bottom:0px;height:30px;')->open() }}
                        <button type="submit" class="btn btn-image pd-5 btn-ht"><img title="Delete Order" src="{{asset('images/delete.png')}}" /></button>
                        {{ html()->form()->close() }}
                    @endif
                    @if(!$order->invoice_id)
                        <a title="Add invoice" class="btn btn-image add-invoice-btn pd-5 btn-ht" data-id='{{$order->id}}'>
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                    @endif
                    <a title="Return / Exchange" data-id="{{$order->id}}" class="btn btn-image quick_return_exchange pd-5 btn-ht">
                        <i class="fa fa-product-hunt" aria-hidden="true"></i>
                    </a>
                    <button type="button" class="btn btn-image pd-5 btn-ht send-email-common-btn" title="Send Mail" data-toemail="{{$order->cust_email}}" data-object="order" data-id="{{$order->customer_id}}"><i class="fa fa-envelope-square"></i></button>
                    <button type="button" class="btn btn-xs btn-image pd-5 btn load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="order" data-id="{{$order->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                    @if($order->cust_email)
                        <a class="btn btn-image pd-5 btn-ht" title="Order Mail PDF" href="{{route('order.generate.order-mail.pdf', ['order_id' => $order->id])}}">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>
                    @endif

                    @if($order->invoice_id)
                        <a title="Download Invoice" class="btn btn-image" href="{{ route('order.download.invoice',$order->invoice_id) }}">
                            <i class="fa fa-download"></i>
                        </a>
                    @endif
                    <button type="button" class="btn btn-xs btn-image load-log-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="order" data-id="{{$order->id}}" data-load-type="text" data-all="1" title="Show Error Log"><img src="{{asset('images/chat.png')}}" alt=""></button>
                    <button type="button" title="Payment history" class="btn btn-image pd-5 btn-ht magento-log-btn btn-xs pull-left" data-id="{{$order->id}}">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button type="button" title="Order Email send error" class="btn  btn-xs btn-image pd-5 email_exception_list" data-id="{{$order->id}}">
                        <i style="color:#6c757d;" class="fa fa-info-circle" data-id="{{$order->id}}"  aria-hidden="true"></i>
                    </button>
                    <button type="button" title="Order Email Send Log" class="btn  btn-xs btn-image pd-5 order_email_send_log" data-id="{{$order->id}}">
                        <img src="{{asset('/images/chat.png')}}" alt="">
                    </button>
                    <button type="button" title="Order SMS Send Log" class="btn  btn-xs btn-image pd-5 order_sms_send_log" data-id="{{$order->id}}">
                      <img src="{{asset('/images/chat.png')}}" alt="">
                  </button>
                    <button type="button" title="Order return true" class="btn  btn-xs btn-image pd-5 order_return" data-status="1" data-id="{{$order->id}}">
                        <i style="color:#6c757d;" class="fa fa-check" data-id="{{$order->id}}"  aria-hidden="true"></i>
                    </button>
                    <a title="Order return False" data-id="{{$order->id}}"  data-status="0" class="btn btn-image order_return pd-5 btn-ht">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </a>
                    <button type="button" data-id="{{$order->id}}" data-order_product_item_id="{{$items->id}}" class="btn btn-xs btn-image pd-5 order-status-change-history" style="padding:1px 0px;">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                    </button>
                </div>
            </td>
        </tr>
          
            @else
            <tr style="background-color: {{$order->status?->color}}"; class="{{ \App\Helpers::statusClass($order->assign_status ) }}">
              <td class="text-center"><span class="td-mini-container">
                  <input type="checkbox" class="selectedOrder" name="selectedOrder" value="{{$order->id}}">
                  </span>
                </td>
              <td class="table-hover-cell">
              <div class="form-inline " >
                  @if ($order->is_priority == 1)
                    <strong class="text-danger mr-1">!!!</strong>
                  @endif
                  <span class="td-mini-container">
                  <span style="font-size:14px;" class="toggle-title-box has-small" data-small-title="<?php echo ($order->order_id) ? substr($order->order_id, 0, 15) : '' ?>" data-full-title="<?php echo ($order->order_id) ? $order->order_id :
                  '' ?>">
                        <?php echo (strlen($order->order_id) > 15) ? substr($order->order_id, 0, 15).'..' : $order->order_id;
    ?>
                     </span>
                  </span>
                </div>
              </td>
              <td class="Website-task" title="{{ Carbon\Carbon::parse($order->order_date)->format('d-m') }}">{{ Carbon\Carbon::parse($order->order_date)->format('d-m') }}</td>
              <td class="expand-row table-hover-cell Website-task" style="color:grey;">
                @if ($order->customer)
                  <span class="td-mini-container">
                    <a style="color: #6c757d;" href="{{ route('customer.show', $order->customer->id) }}">{{ strlen($order->customer->name) > 15 ? substr($order->customer->name, 0, 13) . '...' : $order->customer->name }}</a>
                  </span>
                  <span class="td-full-container hidden">
                    <a style="color: #6c757d;" href="{{ route('customer.show', $order->customer->id) }}">{{ $order->customer->name }}</a>
                  </span>
                @endif
              </td>
              <td class="expand-row table-hover-cell">
                @if ($order->storeWebsiteOrder)
                  @if ($order->storeWebsiteOrder->storeWebsite)
                    @php
                      $storeWebsite = $order->storeWebsiteOrder->storeWebsite;
                    @endphp
                    <span class="td-mini-container">
                        <a style="color: #6c757d;" href="{{$storeWebsite->website}}" target="_blank">{{ strlen($storeWebsite->website) > 15 ? substr($storeWebsite->website, 0, 13) . '...' : $storeWebsite->website }}</a>
                    </span>
                    <span class="td-full-container hidden">
                        <a style="color: #6c757d;" href="{{$storeWebsite->website}}" target="_blank">{{ $storeWebsite->website }}</a>
                    </span>
                  @endif
                @endif
              </td>

              <td class="expand-row table-hover-cell">
                sdfsdfsd
                @php $count = 0; @endphp
                <div class="d-flex">
                  <div class="">
                    @foreach ($order->order_product as $order_product)
                      @if ($order_product->product)
                        @if ($order_product->product->hasMedia($attach_image_tag) && $order_product->product->id == $items->product_id)
                          <span class="td-mini-container">
                            @if ($count == 0)
                              <?php foreach ($order_product->product->getMedia($attach_image_tag) as $media) { ?>
                                <a data-fancybox="gallery" href="{{ getMediaUrl($media) }}">#{{$order_product->product->id}}<i class="fa fa-eye"></i></a>
                                <a class="view-supplier-details" data-id="{{$order_product->id}}" href="javascript:;"><i class="fa fa-shopping-cart"></i></a>
                                <br/>
                              <?php break;
                              } ?>
                              @php ++$count; @endphp
                            @endif
                          </span>
                          <span class="td-full-container hidden">
                            @if ($count >= 1)
                              <?php foreach ($order_product->product->getMedia($attach_image_tag) as $media) { ?>
                              <a data-fancybox="gallery" href="{{ getMediaUrl($media) }}">VIEW
                               <?php break;
                              } ?>
                              #{{$order_product->product->id}}</a>
                              @php $count++; @endphp
                            @endif
                          </span>
                        @endif
                      @endif
                    @endforeach

                </div>
              </td>
              <td>
                <div style="display:inline;">{{($order->estimated_delivery_date)?$order->estimated_delivery_date:'---'}}</div>

              <i style="color:#6c757d;" class="fa fa-pencil-square-o show-est-del-date" data-id="{{$order->id}}" data-new-est="{{($order->estimated_delivery_date)?$order->estimated_delivery_date:''}}" aria-hidden="true"></i>
              <i style="color:#6c757d;" class="fa fa-info-circle est-del-date-history" data-id="{{$order->id}}"  aria-hidden="true"></i>

              </td>
              <td class="Website-task">
                <?php
                   $totalBrands = explode(',', $order->brand_name_list);
    if (count($totalBrands) > 1) {
        $str = 'Multi';
    } else {
        $str = $order->brand_name_list;
    }
    ?>
                <span style="font-size:14px;">{{$str}}</span>
              </td>
              <td class="expand-row table-hover-cell">
                <div class="form-group" style="margin-bottom:0px;">
                  <select data-placeholder="Order Status" class="form-control order-status-select order-status-select-{{$order->id}}" id="supplier" data-id={{$order->id}} >
                            <optgroup label="Order Status">
                              <option value="">Select Order Status</option>
                                @foreach ($order_status_list as $id => $status)
                                    <option value="{{ $id }}" {{ $order->order_status_id == $id ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </optgroup>
                    </select>
                </div>
              </td>
              <td class="expand-row table-hover-cell">
                <div class="form-group" style="margin-bottom:0px;">
                  <select data-placeholder="Order Product Status" class="form-control order-product-status-select" data-order_product_id={{$items->id}} >
                            <optgroup label="Order Product Status">
                              <option value="">Select Order Product Status</option>
                                @foreach ($order_status_list as $id => $status)
                                    <option value="{{ $id }}" {{ $items->order_product_status_id == $id ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </optgroup>
                    </select>
                </div>
              </td>
              <td class="expand-row table-hover-cell">
                <div class="form-group" style="margin-bottom:0px;">
                  <select data-placeholder="Product Status" class="form-control product_order_status_delivery" id="product_delivery_status" data-id={{$order->id}} data-order_product_item_id={{$items->id}} >
                            <optgroup label="Product Status">
                              <option value="">Select product Status</option>
                                @foreach ($order_status_list as $id => $status)
                                    <option value="{{ $id }}" {{ $items->delivery_status == $id ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </optgroup>
                    </select>
                </div>
              </td>
              <td>{{ $order->advance_detail }}</td>
              <td>{{ $order->balance_amount }}</td>
              
              
              <td>
                @if ($order->waybill)
                  {{ $order->waybill->awb }}
                @else
                  -
                @endif
              </td>
              <td>{{$orderProductPrice * $productQty}}</td>
              <td>{{$duty_shipping[$order->id]['shipping']}}</td>
              <td>{{$duty_shipping[$order->id]['duty']}}</td>
              <td>
                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$items->id}}')"><i class="fa fa-arrow-down"></i></button>
                </td>
            </tr>
            <tr class="action-btn-tr-{{$items->id}} d-none">
            <td>Action</td>
            <td colspan="16">
                <div class="align-items-center">
                    <a type="button" title="Payment history" class="btn btn-image pd-5 btn-ht cancel-transaction-btn pull-left" data-id="{{$order->id}}">
                        <i class="fa fa-close"></i>
                    </a>
                    <a type="button" title="Payment history" class="btn btn-image pd-5 btn-ht payment-history-btn pull-left" data-id="{{$order->id}}">
                        <i class="fa fa-history"></i>
                    </a>
                    <a class="btn btn-image pd-5 btn-ht" href="{{route('purchase.grid')}}?order_id={{$order->id}}">
                        <img title="Purchase Grid" style="display: inline; width: 15px;" src="{{ asset('images/customer-order.png') }}" alt="">
                    </a>
                    <a class="btn btn-image pd-5 btn-ht" href="{{ route('order.show',$order->id) }}"><img title="View order" src="{{asset('images/view.png')}}" /></a>
                    <a class="btn btn-image send-invoice-btn pd-5 btn-ht" data-id="{{ $order->id }}" href="{{ route('order.show',$order->id) }}">
                        <img title="Send Invoice" src="{{asset('images/purchase.png')}}" />
                    </a>
                    <a title="Preview Order" class="btn btn-image preview-invoice-btn pd-5 btn-ht" href="{{ route('order.perview.invoice',$order->id) }}">
                        <i class="fa fa-hourglass"></i>
                    </a>
                    @if ($order->waybill)
                        <a title="Download Package Slip pd-5 btn-ht" href="{{ route('order.download.package-slip', $order->waybill->id) }}" class="btn btn-image" href="javascript:;">
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </a>
                        <a title="Track Package Slip pd-5 btn-ht" href="javascript:;" data-id="{{ $order->waybill->id }}" data-awb="{{ $order->waybill->awb }}" class="btn btn-image track-package-slip">
                            <i class="fa fa fa-globe" aria-hidden="true"></i>
                        </a>
                    @endif
                    <a title="Generate AWB" data-order-id="<?php echo $order->id; ?>" data-items='@json($extraProducts)'  data-customer='<?php echo ($order->customer) ? json_encode($order->customer) : json_encode([]); ?>' class="btn btn-image generate-awb pd-5 btn-ht" href="javascript:;"  >
                        <i class="fa fa-truck" aria-hidden="true"></i>
                    </a>

                    <a title="Preview Sent Mails" data-order-id="<?php echo $order->id; ?>" class="btn btn-image preview_sent_mails pd-5 btn-ht" href="javascript:;"  ><i class="fa fa-eye" aria-hidden="true"></i></a>

                    <a title="View customer address" data-order-id="<?php echo $order->id; ?>"  class="btn btn-image customer-address-view pd-5 btn-ht" href="javascript:;"  >
                        <i class="fa fa-address-card" aria-hidden="true"></i>
                    </a>
                    

                    {{ html()->form('DELETE', route('order.destroy', [$order->id]))->style('display:inline;margin-bottom:0px;height:30px;')->open() }}
                    <button type="submit" class="btn btn-image pd-5 btn-ht"><img title="Archive Order" src="{{asset('images/archive.png')}}" /></button>
                    {{ html()->form()->close() }}
                    <?php
        if ($order->auto_emailed) {
            $title_msg = 'Resend Email';
        } else {
            $title_msg = 'Send Email';
        }
    ?>
                    <a title="<?php echo $title_msg; ?>" class="btn btn-image send-order-email-btn pd-5 btn-ht" data-id="{{ $order->id }}" href="javascript:;">
                        <i class="fa fa-paper-plane" aria-hidden="true"></i>
                    </a>
                    @if(auth()->user()->checkPermission('order-delete'))
                        {{ html()->form('DELETE', route('order.permanentDelete', [$order->id]))->style('display:inline;margin-bottom:0px;height:30px;')->open() }}
                        <button type="submit" class="btn btn-image pd-5 btn-ht"><img title="Delete Order" src="{{asset('images/delete.png')}}" /></button>
                        {{ html()->form()->close() }}
                    @endif
                    @if(!$order->invoice_id)
                        <a title="Add invoice" class="btn btn-image add-invoice-btn pd-5 btn-ht" data-id='{{$order->id}}'>
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                    @endif
                    <a title="Return / Exchange" data-id="{{$order->id}}" class="btn btn-image quick_return_exchange pd-5 btn-ht">
                        <i class="fa fa-product-hunt" aria-hidden="true"></i>
                    </a>
                    <button type="button" class="btn btn-image pd-5 btn-ht send-email-common-btn" title="Send Mail" data-toemail="{{$order->cust_email}}" data-object="order" data-id="{{$order->customer_id}}"><i class="fa fa-envelope-square"></i></button>
                    <button type="button" class="btn btn-xs btn-image pd-5 btn load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="order" data-id="{{$order->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                    @if($order->cust_email)
                        <a class="btn btn-image pd-5 btn-ht" title="Order Mail PDF" href="{{route('order.generate.order-mail.pdf', ['order_id' => $order->id])}}">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>
                    @endif

                    @if($order->invoice_id)
                        <a title="Download Invoice" class="btn btn-image" href="{{ route('order.download.invoice',$order->invoice_id) }}">
                            <i class="fa fa-download"></i>
                        </a>
                    @endif
                    <button type="button" class="btn btn-xs btn-image load-log-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="order" data-id="{{$order->id}}" data-load-type="text" data-all="1" title="Show Error Log"><img src="{{asset('images/chat.png')}}" alt=""></button>
                    <button type="button" title="Payment history" class="btn btn-image pd-5 btn-ht magento-log-btn btn-xs pull-left" data-id="{{$order->id}}">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button type="button" title="Order Email send error" class="btn  btn-xs btn-image pd-5 email_exception_list" data-id="{{$order->id}}">
                        <i style="color:#6c757d;" class="fa fa-info-circle" data-id="{{$order->id}}"  aria-hidden="true"></i>
                    </button>
                    <button type="button" title="Order Email Send Log" class="btn  btn-xs btn-image pd-5 order_email_send_log" data-id="{{$order->id}}">
                        <img src="{{asset('/images/chat.png')}}" alt="">
                    </button>
                    <button type="button" title="Order SMS Send Log" class="btn  btn-xs btn-image pd-5 order_sms_send_log" data-id="{{$order->id}}">
                      <img src="{{asset('/images/chat.png')}}" alt="">
                  </button>
                    <button type="button" title="Order return true" class="btn  btn-xs btn-image pd-5 order_return" data-status="1" data-id="{{$order->id}}">
                        <i style="color:#6c757d;" class="fa fa-check" data-id="{{$order->id}}"  aria-hidden="true"></i>
                    </button>
                    <a title="Order return False" data-id="{{$order->id}}"  data-status="0" class="btn btn-image order_return pd-5 btn-ht">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </a>
                    <button type="button" data-id="{{$order->id}}" data-order_product_item_id="{{$items->id}}" class="btn btn-xs btn-image pd-5 order-status-change-history" style="padding:1px 0px;">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                    </button>
                </div>
            </td>
            </tr>
            @endif
            @endforeach
            @else
                @include('orders.order-list')
            @endif
          @endforeach
        </tbody>
      </table>

	{{ $orders_array->appends(request()->except('page'))->links() }}
	</div>
    </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
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

    <div id="payment-history-modal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-body">
            <div class="col-md-12">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Sl no</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody class="payment-history-list-view">
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

    <div id="order-magento-history-modal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Order Status Change Log</h4>
          </div>
          <div class="modal-body">
            <div class="col-md-12">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Log Detail</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody class="order-magento-list">
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

    <div id="order_error_log" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title">Order Logs</h4>
              </div>
              <div class="modal-body">
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Order ID</th>
                        <th style="width: 20%;">Log Type</th>
                        <th style="width: 20%;">Error Log</th>
                        <th>Date</th>
                      </tr>
                    </thead>

                    <tbody id="order_logtd">

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

  <div id="order_exception_error_log" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Order Logs</h4>
            </div>
            <div class="modal-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Order ID</th>
                      <th style="width: 20%;">Error Log</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody id="order_errorlogtd">

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

  <div id="order_email_send_log" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Order Email Logs</h4>
            </div>
            <div class="modal-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Order ID</th>
                      <th>Type</th>
                      <th>From</th>
                      <th>To</th>
                      <th>Subject</th>
                      <th>Message</th>
                      <th>Status</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody id="order_emailsendlogtd">

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

  <div id="order_sms_send_log" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Order SMS Logs</h4>
            </div>
            <div class="modal-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Order ID</th>
                      <th>Number</th>
                      <th>Message</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody id="order_smssendlogtd">

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

  <div id="order_payload_model" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Order Logs</h4>
            </div>
            <div class="modal-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Order ID</th>
                      <th>Paylod</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody id="order_payloadtd">

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

   <div id="updateCustomer" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
        <div class="modal-header">
            <h4 class="modal-title">Update Customers</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="" id="customerUpdateForm" method="POST">
          @csrf
          <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-2">
                    <strong>Status:</strong>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <select data-placeholder="Order Status" name="order_status" class="form-control select2" >
                          <optgroup label="Order Status">
                            <option value="">Select Order Status</option>
                            @foreach ($order_status_list as $id => $status)
                              <option value="{{ $id }}" {{ (isset($order->order_status_id) && $order->order_status_id == $id) ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                          </optgroup>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="col-md-2">
                    <strong>Add New Reply:</strong>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="addnewreply" placeholder="add new reply">
                      <button class="btn btn-secondary addnewreplybtn">+</button>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="col-md-2">
                    <strong>Quick Reply:</strong>
                  </div>
                  <div class="col-md-8">
                      <div class="form-group">
                        <select class="quickreply">
                          <option value="">Select quick reply</option>
                          @if($quickreply)
                            @foreach($quickreply as $quickrep)
                              <option value="{{$quickrep->id}}">{{$quickrep->reply}}</option>
                            @endforeach
                          @endif
                      </select>
                    </div>
                  </div>
                </div>
                  <div class="col-md-12">
                      <div class="col-md-2">
                          <strong>Message:</strong>
                      </div>
                      <div class="col-md-8">
                      <div class="form-group">
                        <textarea cols="45" class="form-control" name="customer_message"></textarea>
                      </div>
                      </div>
                  </div>
                  <div class="col-md-12">
                      <div class="col-md-2">
                          <strong>Update type:</strong>
                      </div>
                      <div class="col-md-8">
                      <div class="form-group">
                        <select name="update_type" class="form-control">
                          <option value="1">Only send message</option>
                          <option value="2">Send message and update status</option>
                        </select>
                      </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Update</button>
          </div>
        </form>
      </div>
    </div>
</div>


<div id="product-update-status-message-tpl" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div class="modal-header ml-4 mr-4">
          <h4 class="modal-title">Change Status</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="" id="product-update-status-message-tpl-frm" method="POST">
          @csrf
          <input type="hidden" name="order_id" id="order-status-id-tpl" value="">
          <input type="hidden" name="order_id" id="order-product-id-status-tpl" value="">
          <input type="hidden" name="order_status_id" id="order-product-status-id-status-tpl" value="">
          <input type="hidden" name="order_product_item_id" id="order_product_item_id" value="">
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-2">
                        <strong>Message:</strong>
                    </div>
                    <div class="col-md-5">
                      <div class="form-group">
                        <textarea cols="45" class="form-control" id="order-product-template-status-tpl" name="message"style="height: 35px;"></textarea>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group d-flex">
                        <div class="checkbox">
                          <label><input class="msg_platform" onclick="loadproductpreview(this);" type="checkbox" value="email">Email</label>
                        </div>
                        <div class="checkbox mt-3 ml-2">

                          <label><input class="msg_platform" type="checkbox" value="sms">SMS</label>
                        </div>
                      </div>
                  </div>
                  <div class="col-md-8">
                        <div id="product-preview" style="display:none">

                        </div>
                  </div>
              </div>
          </div>
          <div class="modal-footer pb-0">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn custom-button product-update-status-with-message">Submit</button>
              <!-- <button type="button" class="btn btn-secondary update-status-with-message">With Message</button> -->
              <!-- <button type="button" class="btn btn-secondary update-status-without-message">Without Message</button> -->
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="update-status-message-tpl" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
        <div class="modal-header ml-4 mr-4">
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
                      <div class="col-md-5">
                        <div class="form-group">
                          <textarea cols="45" class="form-control" id="order-template-status-tpl" name="message"style="height: 35px;"></textarea>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group d-flex">
                          <div class="checkbox">
                            <label><input class="msg_platform" onclick="loadpreview(this);" type="checkbox" value="email">Email</label>
                          </div>
                          <div class="checkbox mt-3 ml-2">

                            <label><input class="msg_platform" type="checkbox" value="sms">SMS</label>
                          </div>
                        </div>
                </div>
                <div class="col-md-8">
                       <div id="preview" style="display:none">

                       </div>
                </div>
                </div>
            </div>
            <div class="modal-footer pb-0">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn custom-button update-status-with-message">Submit</button>
                <!-- <button type="button" class="btn btn-secondary update-status-with-message">With Message</button> -->
                <!-- <button type="button" class="btn btn-secondary update-status-without-message">Without Message</button> -->
            </div>
          </div>
        </form>
      </div>
    </div>
</div>

<div id="purchaseCommonModal" class="modal fade" role="dialog" style="padding-top: 0px !important;padding-right: 12px; padding-bottom: 0px !important;">
  <div class="modal-dialog" style="width: 100%;max-width: none;height: auto;margin: 0;">
    <div class="modal-content " style="border: 0;border-radius: 0;">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="common-contents">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div id="order-status-list-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <div>
                  <h4 class="modal-title"><b>Order Status Color Listing</b></h4>
              </div>
              <button type="button" class="close" data-dismiss="modal">×</button>
          </div>

          <div class="modal-body">
              <div class="row">
                  <div class="col-lg-12">
                      <div class="row">
                          <div class="col-12" id="order-status-list-modal-html">

                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
<div id="estdelhistoryresponse"></div>
@endsection
@include('common.commonEmailModal')
@include("partials.modals.update-delivery-date-modal")
@include("partials.modals.tracking-event-modal")
@include("partials.modals.generate-awb-modal")
@include("partials.modals.preview_sent_mails_modal")
@include("partials.modals.customer-address-modal")
@include("partials.modals.add-invoice-modal")
@include('partials.modals.return-exchange-modal')
@include('partials.modals.return-exchange-modal')
@include('partials.modals.estimated-delivery-date-histories')
@include('orders.partials.column-visibility-modal')
@include('orders.order-status-change-history')
@section('scripts')

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript" src="{{ mix('webpack-dist/js/order-awb.js') }} "></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
  <script src="{{asset('js/common-email-send.js')}}">//js for common mail</script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
  <script type="text/javascript">
    CKEDITOR.replace('editableFile');
    CKEDITOR.replace('editableFileproduct');

  </script>
  <script type="text/javascript">

    function Showactionbtn(id){
      $(".action-btn-tr-"+id).toggleClass('d-none');
    }

    $(document).on('click','.magento-order-status',function(event){
      event.preventDefault();
      $('#order-status-map').modal('show');
    });

    $(function() {
      $(document).tooltip();
    });
    
    $(document).on("click",".toggle-title-box",function(ele) {
        var $this = $(this);
        if($this.hasClass("has-small")){
            $this.html($this.data("full-title"));
            $this.removeClass("has-small")
        }else{
            $this.addClass("has-small")
            $this.html($this.data("small-title"));
        }
    });

    $(document).ready(function() {
      $('#order-datetime').datetimepicker({
        format: 'YYYY-MM-DD'
      });
      $('#newdeldate').datetimepicker({
        minDate:new Date(),
        format: 'YYYY-MM-DD'
      });

      $(document).on("click",".btn-add-items",function(e) {
          var index = $("#generateAWBMODAL").find(".product-items-list").find(".card-body").length;
          var next  = index+1;
          var itemsHtml = `<div class="card-body">
                <div class="form-group col-md-5">
                   <strong>Name:</strong>
                   <input type="text" id="name" name="items[`+next+`][name]" class="form-control" value="">
                </div>
                <div class="form-group col-md-3">
                   <strong>Qty:</strong>
                   <input type="text" id="qty" name="items[`+next+`][qty]" class="form-control" value="">
                </div>
                <div class="form-group col-md-3">
                   <strong>Unit Price:</strong>
                   <input type="text" id="unit_price" name="items[`+next+`][unit_price]" class="form-control" value="">
                </div>
                <div class="form-group col-md-3">
                   <strong>Net Weight:</strong>
                   <input type="text" id="net_weight" name="items[`+next+`][net_weight]" class="form-control" value="1">
                </div>
                <div class="form-group col-md-3">
                   <strong>Gross Weight:</strong>
                   <input type="text" id="gross_weight" name="items[`+next+`][gross_weight]" class="form-control" value="1">
                </div>
                <div class="form-group col-md-3">
                   <strong>HS Code:</strong>
                   <input type="text" id="hs_code" name="items[`+next+`][hs_code]" class="form-control" value="">
                </div>
                <div class="form-group col-md-5">
                   <strong>Manufacturing Country Code:</strong>
                   <input type="text" id="manufacturing_country_code" name="items[`+next+`][manufacturing_country_code]" class="form-control" value="">
                </div>
                <div class="form-group col-md-1" style="margin-top:20px;">
                   <button class="btn btn-secondary btn-remove-item"><i class="fa fa-trash"></i></button>
                </div>
            </div>`;
            $("#generateAWBMODAL").find(".product-items-list").append(itemsHtml);

      });

      $(document).on("click",".btn-remove-item",function(){
          $(this).closest(".card-body").remove();
      });

      	$(document).on("click",".customer-address-view",function() {
			  console.log(this);
			var order_id = $(this).data("order-id");
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: "order/get-customer-address",
				type: "post",
				data : { order_id: order_id },
				beforeSend: function() {
					$("loading-image").show();
				}
			}).done( function(response) {
				if(response.code == 200) {
					 var t = '';
					$.each(response.data,function(k,v) {
						t += `<tr><td>`+v.address_type+`</td>`;
						t += `<td>`+v.city+`</td>`;
						t += `<td>`+v.country_id+`</td>`;
						t += `<td>`+v.email+`</td>`;
						t += `<td>`+v.firstname+`</td>`;
						t += `<td>`+v.lastname+`</td>`;
						t += `<td>`+v.postcode+`</td>`;
						t += `<td>`+v.street+`</td>`;
						t += `<td>`+v.telephone+`</td></tr>`;
					});

					$("#customer-address-modal").find(".show-list-records").html(t);
					$('#customer-address-modal').modal("show");
					$("loading-image").hide();
				}

			}).fail(function(errObj) {
				alert("Could not find any data");
			});
		});


    $(document).on("click",".email_exception_list",function() {
			  console.log(this);
        var order_id = $(this).data("id");
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '{{route("order.get.email.error.logs")}}',
          type: "post",
          data : { order_id: order_id },
          beforeSend: function() {
            $("loading-image").show();
          }
        }).done( function(response) {
          if(response.code == 200) {
            var t = '';
            $.each(response.data,function(k,v) {
              t += `<tr><td>`+v.id+`</td>`;
              t += `<td>`+v.order_id+`</td>`;
              t += `<td>`+v.exception_error+`</td>`;
              t += `<td>`+v.created_at+`</td></tr>`;
            });

            $("#order_exception_error_log").find("#order_errorlogtd").html(t);
            $('#order_exception_error_log').modal("show");
            $("loading-image").hide();
          } else if(response.code == 500){
            toastr['error']('Could not find any error Log', 'Error');
          }
        }).fail(function(error) {
          toastr['error']('Could not find any error Log', 'Error');
        });
		  });

      $(document).on("click",".order_email_send_log",function() {
			  console.log(this);
        var order_id = $(this).data("id");
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '{{route("order.get.email.send.logs")}}',
          type: "post",
          data : { order_id: order_id },
          beforeSend: function() {
            $("loading-image").show();
          }
        }).done( function(response) {
          if(response.code == 200) {
            var t = '';
            $.each(response.data,function(k,v) {
              t += `<tr><td>`+v.id+`</td>`;
              t += `<td>`+v.model_id+`</td>`;
              t += `<td>`+v.type+`</td>`;
              t += `<td>`+v.from+`</td>`;
              t += `<td>`+v.to+`</td>`;
              t += `<td>`+v.subject+`</td>`;
              t += `<td>`+v.message+`</td>`;
              t += `<td>`+v.status+`</td>`;
              t += `<td>`+v.created_at+`</td></tr>`;
            });

            $("#order_email_send_log").find("#order_emailsendlogtd").html(t);
            $('#order_email_send_log').modal("show");
            $("loading-image").hide();
          } else if(response.code == 500){
            toastr['error']('Could not find any error Log', 'Error');
          }
        }).fail(function(error) {
          toastr['error']('Could not find any error Log', 'Error');
        });
		  });

    $(document).on("click",".order_sms_send_log",function() {
      var order_id = $(this).data("id");
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "order/"+order_id+"/get-sms-send-logs",
        type: "GET",
        beforeSend: function() {
          $("loading-image").show();
        }
      }).done( function(response) {
        if(response.code == 200) {
          var t = '';
          $.each(response.data,function(k,v) {
            t += `<tr><td>`+v.id+`</td>`;
            t += `<td>`+v.order_id+`</td>`;
            t += `<td>`+v.number+`</td>`;
            t += `<td>`+v.message+`</td>`;
            t += `<td>`+v.created_at+`</td></tr>`;
          });

          $("#order_sms_send_log").find("#order_smssendlogtd").html(t);
          $('#order_sms_send_log').modal("show");
          $("loading-image").hide();
        } else if(response.code == 500){
          toastr['error']('Could not find any logs', 'Warning');
        }
      }).fail(function(error) {
        toastr['error']('Could not find any logs', 'Warning');
      });
    });

    $(document).on("click",".load-log-modal",function() {
			  console.log(this);
        var order_id = $(this).data("id");
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '{{url("order/get-error-logs")}}',
          type: "post",
          data : { order_id: order_id },
          beforeSend: function() {
            $("loading-image").show();
          }
        }).done( function(response) {
          if(response.code == 200) {
            var t = '';
            $.each(response.data,function(k,v) {
              t += `<tr><td>`+v.id+`</td>`;
              t += `<td>`+v.order_id+`</td>`;
              t += `<td>`+v.event_type+`</td>`;
              t += `<td>`+v.log+`</td>`;
              t += `<td>`+v.created_at+`</td></tr>`;
            });

            $("#order_error_log").find("#order_logtd").html(t);
            $('#order_error_log').modal("show");
            $("loading-image").hide();
          } else if(response.code == 500){
            alert(response.message);
          }
        }).fail(function(errObj) {
          alert("Could not find any data");
        });
		  });


      $(document).on("click",".generate-awb",function() {
          var customer = $(this).data("customer");
          var order_id = $(this).data("order-id");
          var items    = $(this).data("items");

            if(typeof customer != "undefined" || customer != "") {
               /* $(".input_customer_name").val(customer.name);
               $(".input_customer_phone").val(customer.phone);
               $(".input_customer_address1").val(customer.address);
               $(".input_customer_address2").val(customer.city);
               $(".input_customer_city").val(customer.city);
               $(".input_customer_pincode").val(customer.pincode); */
               $("#customer_name").val(customer.name);
               $("#customer_phone").val(customer.phone);
               $("#customer_address1").val(customer.address.substring(0, 44));
               $("#customer_address2").val(customer.city);
               $("#customer_city").val(customer.city);
               $("#customer_pincode").val(customer.pincode);
               $("#customer_email").val(customer.email);
            }

            if(items.length > 0) {
              var itemsHtml = '';
              $.each(items, function(k,v) {
                  itemsHtml += `<div class="card-body">
                              <div class="form-group col-md-5">
                                 <strong>Name:</strong>
                                 <input type="text" id="name" name="items[`+k+`][name]" class="form-control" value="`+v.name+`">
                              </div>
                              <div class="form-group col-md-3">
                                 <strong>Qty:</strong>
                                 <input type="text" id="qty" name="items[`+k+`][qty]" class="form-control" value="`+v.qty+`">
                              </div>
                              <div class="form-group col-md-3">
                                 <strong>Unit Price:</strong>
                                 <input type="text" id="unit_price" name="items[`+k+`][unit_price]" class="form-control" value="`+v.product_price+`">
                              </div>
                              <div class="form-group col-md-3">
                                 <strong>Net Weight:</strong>
                                 <input type="text" id="net_weight" name="items[`+k+`][net_weight]" class="form-control" value="1">
                              </div>
                              <div class="form-group col-md-3">
                                 <strong>Gross Weight:</strong>
                                 <input type="text" id="gross_weight" name="items[`+k+`][gross_weight]" class="form-control" value="1">
                              </div>
                              <div class="form-group col-md-3">
                                 <strong>HS Code:</strong>
                                 <input type="text" id="hs_code" name="items[`+k+`][hs_code]" class="form-control" value="">
                              </div>
                              <div class="form-group col-md-5">
                                 <strong>Manufacturing Country Code:</strong>
                                 <input type="text" id="manufacturing_country_code" name="items[`+k+`][manufacturing_country_code]" class="form-control" value="">
                              </div>
                              <div class="form-group col-md-1" style="margin-top:20px;">
                                 <button class="btn btn-secondary btn-remove-item"><i class="fa fa-trash"></i></button>
                              </div>
                          </div>`;
              });

              $("#generateAWBMODAL").find(".product-items-list").html(itemsHtml);
            }

            $("#generateAWBMODAL").find("[name='order_id']").val(order_id);
            $("#generateAWBMODAL").modal("show");
      });




      $(document).on("click",".preview_sent_mails",function() {

          var id = $(this).data("order-id");

          $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "order/preview-sent-mails",
            type: "post",
            data : {
              id: id,
            },
            beforeSend: function() {

              $("loading-image").show();
            }
          }).done( function(response) {

            $("loading-image").hide();

            if(response.code == 200) {
            var items = response.data;
            console.log(response.data);
            if(items.length > 0) {
              var itemsHtml = '';
              $.each(items, function(k,v) {
                  itemsHtml += `<tr class="in-background filter-message reviewed_msg" data-message="Greetings from Solo Luxury Ref: order number 1730030 we have updated your order with status : prepaid.">
                      <td >`+v.created_at+`</td>
                      <td >`+v.from+`</td>
                      <td >`+v.to+`</td>
                      <td >`+v.subject+`</td>
                      <td class="htmlgenerated`+k+`"></td>
                      <td >`+v.status+`</td>
                      <td >`+v.is_draft+`</td>
                      <td >`+v.error_message+`</td>
                    </tr>`;

                });

              $(".product-items-list").html(itemsHtml);
              $.each(items, function(k,v) {
                $(".htmlgenerated"+k).html(v.message);
              });
            }

            $("#previewSendMailsModal").modal("show");

            }

          }).fail(function(errObj) {
              alert("Could not change status");
          });




      });


    function ConfirmDialog(message,id,status) {
      $('<div></div>').appendTo('body')
        .html('<div><h5>' + message + '?</h5></div>')
        .dialog({
          modal: true,
          title: 'Confirm Send',
          zIndex: 10000,
          autoOpen: true,
          width: 'auto',
          resizable: false,
          buttons: {
            Yes: function() {
              $.ajax({
                url: "/order/change-status",
                type: "GET",
                async : false,
                data : {
                  id : id,
                  status : status,
                  sendmessage:'1',
                }
              }).done( function(response) {

              }).fail(function(errObj) {
                alert("Could not change status");
              });
            },
            No: function() {
              $.ajax({
              url: "/order/change-status",
              type: "GET",
              async : false,
              data : {
                id : id,
                status : status
              }
            }).done( function(response) {
              return true;
            }).fail(function(errObj) {
              alert("Could not change status");
            });
            }
          },
          close: function(event, ui) {
            $(this).remove();
          }
        });
    };
      /*$(document).on("change",".order-status-select",function() {
          var id = $(this).data("id");
          var status = $(this).val();
          var message = 'Do you want to send message to customer for status change';
          ConfirmDialog(message,id,status);
      });*/
      $(document).on("click",".order-resend-popup",function() {
          var id = $(this).data("id");
          var status = $(this).data('status_id');
          $("#preview").hide();
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
              $("#preview").html(response.preview);
              CKEDITOR.replace( 'editableFile' );
              $("#order-status-id-status-tpl").val(status);
              $("#order-template-status-tpl").val(response.template);
              $(".msg_platform").prop('checked', false);
              $("#update-status-message-tpl").modal("show");
            }

          }).fail(function(errObj) {
              alert("Could not change status");
          });
      });
      $(document).on("change",".order-status-select",function() {
          var id = $(this).data("id");
          var status = $(this).val();
          $("#preview").hide();
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
              $("#preview").html(response.preview);
              CKEDITOR.replace( 'editableFile' );
              $("#order-status-id-status-tpl").val(status);
              $("#order-template-status-tpl").val(response.template);
              $(".msg_platform").prop('checked', false);
              $("#update-status-message-tpl").modal("show");
            }

          }).fail(function(errObj) {
              alert("Could not change status");
          });
      });

      $(document).on("click",".update-status-with-message",function(e) {
          e.preventDefault();
          console.log($("#email_from_mail").val());
          console.log($("#email_to_mail").val());
          var selected_array = [];
          console.log(selected_array);
          $('.msg_platform:checkbox:checked').each(function() {
            selected_array.push($(this).val());
          });

          // The below check is not need. If user not select email or sms, Just change the status only.. 
          // if(selected_array.length == 0){
          //   alert('Please at least select one option');
          //   return;
          // }else{
            $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "/order/change-status",
            type: "post",
            async : false,
            data : {
              id : $("#order-id-status-tpl").val(),
              status : $("#order-status-id-status-tpl").val(),
              sendmessage:'1',
              message:$("#order-template-status-tpl").val(),
              custom_email_content:$("#customEmailContent").val(),
              from_mail:$("#email_from_mail").val(),
              to_mail:$("#email_to_mail").val(),
              order_via: selected_array,
            }
            }).done( function(response) {
              $("#update-status-message-tpl").modal("hide");
            }).fail(function(errObj) {
              toastr['error'](errObj.responseText);
           });
          // }

      });

      $(".order-product-status-select").change(function(){
        var orderProductId = $(this).data('order_product_id');
        var orderProductStatusId = $(this).val();

        if(orderProductStatusId == '')
        {
            toastr['error']('Status is Required');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('order.order-product-status-change') }}",
            data: {
                _token: "{{ csrf_token() }}",
                orderProductStatusId : orderProductStatusId,
                orderProductId: orderProductId,
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

      $(document).on("click", ".order_return", function(e) {

        e.preventDefault();
        let id = $(this).data("id");
        let status = $(this).data("status");
        $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "/order/change-return-status",
            type: "post",
            async : false,
            data : {
              id : id,
              status : status
            }
            }).done( function(response) {
              toastr['success'](response.message);
            }).fail(function(errObj) {
              toastr['error'](errObj.message);
           });
      });

      $(document).on("change",".product_order_status_delivery",function() {

          var id = $(this).data("id");
          var product_item_id = $(this).data("order_product_item_id");
          var status = $(this).val();

          var order_status = $(".order-status-select-"+id).val();

          $("#product-preview").hide();
          $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "order/product/change-status-temp",
            type: "post",
            data : {
              id : id,
              order_id: id,
              order_product_item_id: product_item_id,
              order_status_id : status,
              order_status : order_status,
            },
            beforeSend: function() {
              $("loading-image").show();
            }
          }).done( function(response) {
            $("loading-image").hide();
            if(response.code == 200) {
                
              $("#order-status-id-tpl").val(order_status);
              $("#order-product-id-status-tpl").val(id);
              $("#product-preview").html(response.preview);
              $("#order_product_item_id").val(product_item_id);
              CKEDITOR.replace( 'editableFileproduct' );
              $("#order-product-status-id-status-tpl").val(status);
              $("#order-product-template-status-tpl").val(response.template);
              $(".msg_platform").prop('checked', false);
              $("#product-update-status-message-tpl").modal("show");
            }

          }).fail(function(errObj) {
              alert("Could not change status"+errObj);
          });
      });

      $(document).on("click",".product-update-status-with-message",function(e) {
          e.preventDefault();
          //console.log($("#email_from_mail").val());
          //console.log($("#email_to_mail").val());
          var selected_array = [];
          console.log(selected_array);
          $('.msg_platform:checkbox:checked').each(function() {
            selected_array.push($(this).val());
          });

          if(selected_array.length == 0){
            alert('Please at least select one option');
            return;
          }else{
            $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "order/product/change-status",
            type: "post",
            async : false,
            data : {
              id : $("#order-product-id-status-tpl").val(),
              order_product_item_id : $("#order_product_item_id").val(),
              status : $("#order-product-status-id-status-tpl").val(),
              sendmessage:'1',
              message:$("#order-product-template-status-tpl").val(),
              custom_email_content:$("#editableFileproduct").val(),
              from_mail:$("#email_from_mail_product").val(),
              to_mail:$("#email_to_mail_product").val(),
              order_via: selected_array,
              order_status_id : $("#order-status-id-tpl").val(),
            }
            }).done( function(response) {
              $("#product-update-status-message-tpl").modal("hide");
              toastr['success']('Product Status updated succesfully changed.');

            }).fail(function(errObj) {
              toastr['error'](errObj.responseText);
           });
          }

      });

      $(document).on("click",".update-status-without-message",function() {
          e.preventDefault();
          $.ajax({
            url: "/order/change-status",
            type: "GET",
            async : false,
            data : {
              id : $("#order-id-status-tpl").val(),
              status : $("#order-status-id-status-tpl").val(),
              sendmessage:'0',
              message:$("#order-template-status-tpl").html(),
            }
          }).done( function(response) {
            $("#update-status-message-tpl").modal("hide");
          }).fail(function(errObj) {
            alert("Could not change status");
          });
      });

      $(".select2").select2({tags:true});

      $(".select-multiple").multiselect({
        // buttonWidth: '100%',
        // includeSelectAllOption: true
      });


	  // $('ul.pagination').hide();
	// 	$('.infinite-scroll').jscroll({
	// 		autoTrigger: true,
	// 		// debug: true,
	// 		loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
	// 		padding: 0,
	// 		nextSelector: '.pagination li.active + li a',
	// 		contentSelector: 'div.infinite-scroll',
	// 		callback: function () {
	// 			$('ul.pagination').first().remove();
	// 			$('ul.pagination').hide();
	// 		}
	// 	});
    });

    $(document).on('click', '.change_message_status', function(e) {
      e.preventDefault();
      var url = $(this).data('url');
      var thiss = $(this);
      var type = 'GET';

      if ($(this).hasClass('approve-whatsapp')) {
        type = 'POST';
      }

        $.ajax({
          url: url,
          type: type,
          beforeSend: function() {
            $(thiss).text('Loading');
          }
        }).done( function(response) {
          $(thiss).closest('tr').removeClass('row-highlight');
          $(thiss).prev('span').text('Approved');
          $(thiss).remove();
        }).fail(function(errObj) {
          alert("Could not change status");
        });
    });

    $(document).on('click', '.expand-row', function() {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
      }
    });

    $(document).on('click', '.expand-row', function() {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
      }
    });
    $(document).on("click",".send-invoice-btn",function(e){
       e.preventDefault();
       var $this = $(this);
       $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "/order/"+$this.data("id")+"/send-invoice",
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
        }).fail(function(errObj) {
           $("#loading-image").hide();
        });
    });

    $('.cancel-transaction-btn').click(function(){
          var order_id = $(this).data('id');
          console.log(order_id);
          //return false;
          $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('order.canceltransaction') }}",
            data: {
              order_id:order_id,
            },
        }).done(response => {
          //$('#payment-history-modal').find('.payment-history-list-view').html('');
            if(response.success==true){
              console.log(response);
              message = JSON.parse(response.message);
              alert(message.message);
              //$('#payment-history-modal').find('.payment-history-list-view').html(response.html);
              //$('#payment-history-modal').modal('show');
            }

        }).fail(function(response) {
          alert(response.responseJSON.message);
        });
      });

      $('.magento-log-btn').click(function(){
          var order_id = $(this).data('id');
          $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('order.magento.log.list') }}",
            data: {
              order_id:order_id,
            },
        }).done(response => {
          $('.order-magento-list').html('');
            if(response.code == '200' && response.data !=''){
              $('.order-magento-list').html(response.data);
              $('#order-magento-history-modal').modal('show');
            } else {
              alert('Could not fetch log list');
            }

        }).fail(function(response) {
          alert('Could not fetch log list');
        });
      });

 $('.payment-history-btn').click(function(){
    event.stopPropagation();
    event.stopImmediatePropagation();
          var order_id = $(this).data('id');
          $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('order.paymentHistory') }}",
            data: {
              order_id:order_id,
            },
        }).done(response => {
          $('#payment-history-modal').find('.payment-history-list-view').html('');
            if(response.success==true){
              $('#payment-history-modal').find('.payment-history-list-view').html(response.html);
              $('#payment-history-modal').modal('show');
            }

        }).fail(function(response) {

          alert('Could not fetch payments');
        });
      });
    $(document).on("click",".send-order-email-btn",function(e){
       e.preventDefault();
       var $this = $(this);
       $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "/order/"+$this.data("id")+"/send-order-email",
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
        }).fail(function(errObj) {
           $("#loading-image").hide();
        });
    });

    $(document).on("click",".add-invoice-btn",function(e){
       e.preventDefault();
       var $this = $(this);
       $.ajax({
          url: "/order/"+$this.data("id")+"/add-invoice",
          type: "get"
        }).done(function(response) {
          $('#addInvoice').modal('show');
           $("#add-invoice-content").html(response);
        }).fail(function(errObj) {
           $("#addInvoice").hide();
        });
    });

    var selected_orders = [];
         $(document).on('click', '.selectedOrder', function () {
            var checked = $(this).prop('checked');
            var id = $(this).val();
             if (checked) {
              selected_orders.push(id);
            } else {
                var index = selected_orders.indexOf(id);
                 selected_orders.splice(index, 1);
            }
        });
        $(document).on("click",".delete-orders",function(e){
          e.preventDefault();
          if(selected_orders.length < 1) {
            toastr['error']("Select some orders first");
            return;
          }
          var x = window.confirm("Are you sure, you want to delete ?");
          if(!x) {
            return;
          }
          $.ajax({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "/order/deleteBulkOrders",
              type: "post",
              data: {ids : selected_orders}
            }).done(function(response) {
              toastr['success'](response.message);
              window.location.reload();
            }).fail(function(errObj) {
            });
        });
        // $(document).on("click",".view-product",function(e){
        //   e.preventDefault();
        //   var id = $(this).data('id');
        //   $.ajax({
        //       headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //       },
        //       url: "/order/"+id+"/view-products",
        //       type: "GET"
        //     }).done(function(response) {
        //       $('#view-products').modal('show');
        //       $("#view-products-content").html(response); 
        //     }).fail(function(errObj) {
        //     });
        // });
         $(document).on("click",".update-customer",function(e){
          e.preventDefault();
          if(selected_orders.length < 1) {
            toastr['error']("Select some orders first");
            return;
          }
          $('#updateCustomer').modal('show');
        });

        $(document).on('submit', '#customerUpdateForm', function (e) {
                e.preventDefault();
                var data = $(this).serializeArray();
                data.push({name: 'selected_orders', value: selected_orders});
                $.ajax({
                    url: "{{route('order.update.customer')}}",
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        toastr['success']('Successful', 'success');
                        $('#updateCustomer').modal('hide');
                        $("#customerUpdateForm").trigger("reset");
                        $(".order-table tr").find('.selectedOrder').each(function () {
                          if ($(this).prop("checked") == true) {
                            $(this).prop("checked", false);
                          }
                        });
                        selected_orders = [];
                    },
                    error: function () {
                        alert('There was error loading priority task list data');
                    }
                });
            });
             $(document).on('click', '.quick_return_exchange', function (e) {
            let $this       = $(this),
                $modelData  = $(document).find(".return-exchange-model-data");
             $('#return-exchange-modal').modal('show');
             $.ajax({
                type: "GET",
                url: "/return-exchange/getProducts/" + $this.data("id"),
            }).done(function (response) {
              $modelData.html(response.html);
              $('.due-date').datetimepicker({
                minDate:new Date(),
                format: 'YYYY-MM-DD'
              });
            }).fail(function (response) {});
        });

        $(document).on('click', '.order_payload', function (e) {
            $('#order_payload_model').modal('show');
            $.ajax({
              type: "POST",
              url: "{{route('order.payload')}}",
              data: {
                order_id : $(this).data('id')
              },
              headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
            }).done(function (response) {
              toastr[(response.code == 200) ?'success' : 'error'](response.message);
              $("#order_payloadtd").html(response.data);
            }).fail(function (response) {
              toastr[(response.code == 200) ?'success' : 'error'](response.message);
            });
        });
        $(document).on("click","#return-exchange-form input[name='type']",function() {
            if($(this).val() == "refund") {
                $("#return-exchange-form").find(".refund-section").show();
            }else{
                $("#return-exchange-form").find(".refund-section").hide();
            }
        });
         $(document).on("click","#btn-return-exchage-request",function(e) {
            e.preventDefault();
            var form = $("#return-exchange-form");
            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                data: form.serialize(),
                dataType:"json"
            }).done(function (response) {
                toastr[(response.code == 200) ?'success' : 'error'](response.message);
                $('#return-exchange-modal').modal('hide');
                document.getElementById("return-exchange-form").reset();
            }).fail(function (response) {
                console.log(response);
            });
        });
        $(document).on("click","#btn-return-exchage-request",function(e) {
            e.preventDefault();
            var form = $("#return-exchange-form");
            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                data: form.serialize(),
                dataType:"json"
            }).done(function (response) {
                toastr[(response.code == 200) ?'success' : 'error'](response.message);
                $('#return-exchange-modal').modal('hide');
                document.getElementById("return-exchange-form").reset();
            }).fail(function (response) {
                console.log(response);
            });
        });
        $(document).on('click','.show-est-del-date',function(e){
          e.preventDefault();
          var data_new_est = $(this).data('new-est');
          var order_id = $(this).data('id');
          $('#newdeldate').val(data_new_est);
          $('#orderid').val(order_id);
          $('#update-del-date-modal').modal('show');
        })
        $(document).on('click','.update-del-date',function(e){
          e.preventDefault();
		   var selected_array = [];
          $('.msg_platform_del:checkbox:checked').each(function() {
            selected_array.push($(this).val());
          });
          var newdeldate = $('#newdeldate').val();
          if(!newdeldate){
            toastr['error']('Estimate delivery date field cannot be empty !');
            return;
          }
            var form = $("#updateDelDateForm");
			var data = form.serialize();

            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                data: {'orderid':$('#orderid').val(), 'newdeldate':$('#newdeldate').val(), 'fieldname':$('#fieldname').val(), 'order_via':selected_array},
                dataType:"json",
                beforeSend:function(data){
                  $('.ajax-loader').show();
                }
            }).done(function (response) {
              $('.ajax-loader').hide();
                toastr[(response.code == 200) ?'success' : 'error'](response.message);
                $('#update-del-date-modal').modal('hide');
                document.getElementById("updateDelDateForm").reset();
                location.reload();
            }).fail(function (response) {
              $('.ajax-loader').hide();
                console.log(response);
            });
        })
        $(document).on('click','.est-del-date-history',function(e){
          e.preventDefault();
          var order_id = $(this).data('id');
          $.ajax({
                type: "GET",
                url: "{{route('order.viewEstDelDateHistory')}}",
                data: {order_id:order_id},
                dataType:"json",
                beforeSend:function(data){
                  $('.ajax-loader').show();
                }
            }).done(function (response) {
              $('.ajax-loader').hide();
              $('#estdelhistoryresponse').empty().html(response.html);
              $('#estiate_del-history-modal').modal('show');
            }).fail(function (response) {
              $('.ajax-loader').hide();
                console.log(response);
            });

        })
        $(document).on('click','.addnewreplybtn',function(e){
          e.preventDefault();
          var replybox  = $(this).parentsUntil('#customerUpdateForm').find('.addnewreply');
          var selectreplybox = $(this).parentsUntil('#customerUpdateForm').find('.quickreply');
          var reply = $(this).parentsUntil('#customerUpdateForm').find('.addnewreply').val();
          if(!reply){
            alert('please add reply to input box !');
            return false;
          }
          $.ajax({
                type: "POST",
                url: "{{route('order.addNewReply')}}",
                data: {reply:reply},
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:"json",
                beforeSend:function(data){
                  $('.ajax-loader').show();
                }
            }).done(function (response) {
              $('.ajax-loader').hide();
              if(response.status==200){
                replybox.val('');
                selectreplybox.html(response.html);
              }
            }).fail(function (response) {
              $('.ajax-loader').hide();
                console.log(response);
            });

        })
        $('.quickreply').on('change',function(){
          var reply = $(this).find('option:selected').text();
          var replyval = $(this).find('option:selected').attr('value');
          if(replyval!=''){
            $(this).parentsUntil('#customerUpdateForm').find('textarea[name="customer_message"]').val(reply);
          }else{
            $(this).parentsUntil('#customerUpdateForm').find('textarea[name="customer_message"]').val('');
          }
        });

        $('[data-fancybox="gallery"]').fancybox({
            // Options will go here
          });

          $('#swtichForm').on('click',function(e){

        var from_customer_id=$("#from_customer_name");
        var from_customer_city=$("#from_customer_city");
        var from_customer_country=$("#from_customer_country");
        var from_customer_phone=$("#from_customer_phone");
        var from_customer_address1=$("#from_customer_address1");
        var from_customer_address2=$("#from_customer_address2");
        var from_customer_pincode=$("#from_customer_pincode");
        var from_company_name=$("#from_company_name");
        /* var from_actual_weight=$("#from_actual_weight");
        var from_box_length=$("#from_box_length");
        var from_box_width=$("#from_box_width");
        var from_box_height=$("#from_box_height");
        var from_amount=$("#from_amount");
        var from_currency=$("#from_currency");
        var from_pickup_time=$("#from_pickup_time");
        var from_service_type=$("#from_service_type"); */
        //"TO" section
        var customer_id=$("#customer_name");
        var customer_city=$("#customer_city");
        var customer_country=$("#customer_country");
        var customer_phone=$("#customer_phone");
        var customer_address1=$("#customer_address1");
        var customer_address2=$("#customer_address2");
        var customer_pincode=$("#customer_pincode");
        var company_name=$("#company_name");
        var actual_weight=$("#actual_weight");
        var box_length=$("#box_length");
        var box_width=$("#box_width");
        var box_height=$("#box_height");
        var amount=$("#amount");
        var currency=$("#currency");
        var pickup_time=$("#pickup_time");
        var service_type=$("#service_type");

        var pre_from_customer_id=from_customer_id.val();
        from_customer_id.val(customer_id.val());
        customer_id.val(pre_from_customer_id);
       /*  var pre_from_customer_id_name=from_customer_id.attr('name');
        var pre_from_customer_id_id=from_customer_id.attr('name');
        from_customer_id.attr('name',customer_id.attr('name'));
        from_customer_id.attr('id',customer_id.attr('id'));
        customer_id.attr('name',pre_from_customer_id_name);
        customer_id.attr('id',pre_from_customer_id_id); */

        var pre_from_customer_name=$("#div_from_customer_name").html();
        $("#div_from_customer_name").html($("#div_to_customer_name").html());
        $("#div_to_customer_name").html(pre_from_customer_name);

        var pre_from_customer_city=from_customer_city.val();
        from_customer_city.val(customer_city.val());
        customer_city.val(pre_from_customer_city);

        var pre_from_customer_country=from_customer_country.val();
        from_customer_country.val(customer_country.val());
        customer_country.val(pre_from_customer_country);

        var pre_from_customer_phone=from_customer_phone.val();
        from_customer_phone.val(customer_phone.val());
        customer_phone.val(pre_from_customer_phone);

        var pre_from_customer_address1=from_customer_address1.val();
        from_customer_address1.val(customer_address1.val());
        customer_address1.val(pre_from_customer_address1);

        var pre_from_customer_address2=from_customer_address2.val();
        from_customer_address2.val(customer_address2.val());
        customer_address2.val(pre_from_customer_address2);

        var pre_from_customer_pincode=from_customer_pincode.val();
        from_customer_pincode.val(customer_pincode.val());
        customer_pincode.val(pre_from_customer_pincode);

        var pre_from_company_name=from_company_name.val();
        from_company_name.val(company_name.val());
        company_name.val(pre_from_company_name);

        /* var pre_from_actual_weight=from_actual_weight.val();
        from_actual_weight.val(actual_weight.val());
        actual_weight.val(pre_from_actual_weight);

        var pre_from_box_length=from_box_length.val();
        from_box_length.val(box_length.val());
        box_length.val(pre_from_box_length);

        var pre_from_box_width=from_box_width.val();
        from_box_width.val(box_width.val());
        box_width.val(pre_from_box_width);

        var pre_from_box_height=from_box_height.val();
        from_box_height.val(box_height.val());
        box_height.val(pre_from_box_height);

        var pre_from_amount=from_amount.val();
        from_amount.val(amount.val());
        amount.val(pre_from_amount);

        var pre_from_currency=from_currency.val();
        from_currency.val(currency.val());
        currency.val(pre_from_currency);

        var pre_from_pickup_time=from_pickup_time.val();
        from_pickup_time.val(pickup_time.val());
        pickup_time.val(pre_from_pickup_time);

        var pre_from_service_type=from_service_type.val();
        from_service_type.val(service_type.val());
        service_type.val(pre_from_service_type); */

    });

    $(document).on('click', '.view-supplier-details', function(e) {
      e.preventDefault();
      var order_product_id = $(this).data('id');
      var type = 'GET';
        $.ajax({
          url: '/purchase-product/supplier-details/'+order_product_id,
          type: type,
          dataType: 'html',
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done( function(response) {
            $("#loading-image").hide();
            $("#purchaseCommonModal").modal("show");
            $("#common-contents").html(response);
        }).fail(function(errObj) {
            $("#loading-image").hide();
        });
    });

    $(document).on('keyup', '.supplier-discount', function (event) {
            if (event.keyCode != 13) {
                return;
            }
            let id = $(this).data('id');
            let product_id = $(this).data('product');
            let discount = $("#supplier_discount-"+id).val();
            let orderProductId = $(this).data('order-product');
            $.ajax({
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
                url: "{{action([\App\Http\Controllers\PurchaseProductController::class, 'saveDiscount'])}}",
                type: 'POST',
                data: {
                  discount: discount,
                  supplier_id: id,
                  product_id:product_id,
                  order_product_id:orderProductId
                },
                success: function (data) {
                    toastr["success"]("Discount updated successfully!", "Message");
                    $("#common-contents").html(data.html);
                }
            });

        });


        $(document).on('keyup', '.supplier-fixed-price', function (event) {
            if (event.keyCode != 13) {
                return;
            }
            let id = $(this).data('id');
            let fixed_price = $("#supplier_fixed_price_"+id).val();
            let product_id = $(this).data('product');
            let orderProductId = $(this).data('order-product');
            $.ajax({
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
                url: "{{action([\App\Http\Controllers\PurchaseProductController::class, 'saveFixedPrice'])}}",
                type: 'POST',
                data: {
                  fixed_price: fixed_price,
                  supplier_id: id,
                  product_id:product_id,
                  order_product_id:orderProductId
                },
                success: function (data) {
                    toastr["success"]("Fixed price updated successfully!", "Message");
                    $("#common-contents").html(data.html);
                }
            });

        });

        $(document).on('click', '.product_default_supplier', function () {
            let supplier_id = $(this).data('id');
            let order_product = $(this).data('order_product');
            let product_id = $(this).data('product');
            $.ajax({
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
                url: "{{action([\App\Http\Controllers\PurchaseProductController::class, 'saveDefaultSupplier'])}}",
                type: 'POST',
                data: {
                  supplier_id: supplier_id,
                  order_product:order_product,
                  product_id:product_id
                },
                success: function (res) {
                  if(res.code == 200) {
                    toastr["success"]("Supplier updated successfully!", "Message");
                  }
                  else {
                    toastr["error"](res.message, "Message");
                  }

                }
            });

        });

        function loadproductpreview(t)
        {
          $("#product-preview").hide();
          if (t.checked == true){
            $("#product-preview").show();
          }
        }
        function loadpreview(t)
        {
          $("#preview").hide();
          if (t.checked == true){
            $("#preview").show();
          }
        }

        function listStatusColor(pageNumber = 1) {
          var button = document.querySelector('.order-status-listing');
              $.ajax({
                  url: '{{ route('order.status.color') }}',
                  dataType: "json",
                  data: {
                      page:pageNumber,
                  },
                  beforeSend: function() {
                  $("#loading-image-preview").show();
              }
              }).done(function(response) {
                  $('#order-status-list-modal-html').empty().html(response.html);
                  $('#order-status-list-modal').modal('show');
                  renderOrderStatusPagination(response.data);
                  $("#loading-image-preview").hide();
              }).fail(function(response) {
                  $('.loading-image-preview').show();
                  console.log(response);
              });
        }

        function renderOrderStatusPagination(response) {
            var paginationContainer = $(".pagination-container-order-status");
            var currentPage = response.current_page;
            var totalPages = response.last_page;
            var html = "";
            var maxVisiblePages = 10;

            if (totalPages > 1) {
                html += "<ul class='pagination'>";
                if (currentPage > 1) {
                html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeOrderStatusPage(" + (currentPage - 1) + ")'>Previous</a></li>";
                }
                var startPage = 1;
                var endPage = totalPages;

                if (totalPages > maxVisiblePages) {
                if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
                    endPage = maxVisiblePages;
                } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
                    startPage = totalPages - maxVisiblePages + 1;
                } else {
                    startPage = currentPage - Math.floor(maxVisiblePages / 2);
                    endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
                }

                if (startPage > 1) {
                    html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeOrderStatusPage(1)'>1</a></li>";
                    if (startPage > 2) {
                    html += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                    }
                }
                }

                for (var i = startPage; i <= endPage; i++) {
                html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changeOrderStatusPage(" + i + ")'>" + i + "</a></li>";
                }
                html += "</ul>";
            }
            paginationContainer.html(html);
         }

        function changeOrderStatusPage(pageNumber) {
          listStatusColor(pageNumber);
        }

        $(document).on('click','.order-status-change-history',function(){
            var order_id = $(this).data('id');
            var product_item_id = $(this).data("order_product_item_id");
            $.ajax({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: `{{ route('order.orderChangeStatusHistory') }}`,
                dataType: "json",
                data: {
                    order_id: order_id,
                    product_item_id:product_item_id
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += "<tr>";
                            html += "<td>" + (k + 1) + "</td>";
                            html += "<td>" + v.order.order_id + "</td>";
                            html += "<td>" + v.request + "</td>";
                            html += "<td>" + v.response + "</td>";
                            html += "<td>" + v.user.name + "</td>";
                            html += "<td>" + v.created_at + "</td>";
                            html += "</tr>";
                        });
                        $("#order-status-change-histories-list").find(".order-status-change-list-view").html(html);
                        $("#order-status-change-histories-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
  </script>
@endsection
