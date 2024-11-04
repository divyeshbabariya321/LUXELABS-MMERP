@extends('layouts.app')

@section('title', 'Order Journey')

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row m-0">
        <div class="col-md-12 p-0">
            <h2 class="page-heading">
                Order Journey
                <div style="float: right;">
                    <button type="button" class="btn custom-button float-right mr-3" data-toggle="modal" data-target="#odatatablecolumnvisibilityList">Column Visiblity</button>
                </div>
            </h2>
        </div>
    </div>
   
    <div class="col-md-12 pl-3 pr-3">
        <div class="row m-0">

            <div class="col-md-12 p-0">
                <form class="form-inline" action="{{ route('order.get.order.journey') }}" method="GET">

                    <div class="form-group col-md-2 pl-0">
                        <label style="float: left;">Order ID</label>
                        <input style="width:100%;" name="filter_order" type="text" class="form-control" value="{{request()->get('filter_order')}}" placeholder="Search Order ID">
                    </div>

                    <div class="form-group col-md-2 pl-0">
                        <label style="float: left;">Select Customers</label>
                        {{ html()->multiselect("filer_customer_list[]", $customer_list, request('filer_customer_list', []))->class("form-control select2")->id('filer_customer_list') }}
                    </div>

                    <div class="form-group col-md-1 pl-0">
                        <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                        <a href="{{route('order.get.order.journey')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                    </div>
                </form></br>
            </div>

            <div class="" style="overflow: scroll;" style="overflow-y: auto">
                <table id="magento_list_tbl_895" class="table table-bordered table-hover">
                    <thead>
                        @if(!empty($dynamicColumnsToShowoj))
                            @if (!in_array('Order ID', $dynamicColumnsToShowoj))
                                <th>Order ID</th>
                            @endif
                            @if (!in_array('Products', $dynamicColumnsToShowoj))
                                <th>Products</th>
                            @endif
                            @if (!in_array('Customer', $dynamicColumnsToShowoj))
                                <th>Customer</th>
                            @endif
                            @foreach ($orderStatusList as $orderStatus)
                                @if (!in_array($orderStatus, $dynamicColumnsToShowoj))
                                    <td style="width: 10%">{{ $orderStatus }}</td>
                                @endif
                            @endforeach
                        @else
                            <th>Order ID</th>
                            <th>Products</th>
                            <th>Customer</th>
                            @foreach ($orderStatusList as $orderStatus)
                                <td style="width: 10%">{{ $orderStatus }}</td>
                            @endforeach
                        @endif
                    </thead>
                    <tbody class="infinite-scroll-pending-inner">
                        @foreach ($orders as $order)
                            @if(!empty($dynamicColumnsToShowoj))
                                <tr>
                                    @if (!in_array('Order ID', $dynamicColumnsToShowoj))
                                        <td> {{ $order->order_id }} </td>
                                    @endif
                                    @if (!in_array('Products', $dynamicColumnsToShowoj))
                                        <td><a href="javascript:void(0)" data-id="{{ $order->id }}" id="order-products"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                                    @endif
                                    @if (!in_array('Customer', $dynamicColumnsToShowoj))
                                        <td>@if(!empty($order->customer->name)) {{$order->customer->name}} @else {{'-'}} @endif</td>
                                    @endif
                                    <?php $orderStatusHistories = $order->orderStatusHistories->pluck('created_at', 'new_status')->toArray(); ?>
                                    @foreach ($orderStatusList as $key => $orderStatus)
                                        @if (!in_array($orderStatus, $dynamicColumnsToShowoj))
                                            <td>
                                                @if (array_key_exists($key, $orderStatusHistories))
                                                    {{ $orderStatusHistories[$key] }}
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach()
                                </tr>
                            @else 
                                <tr>
                                    <td> {{ $order->order_id }} </td>
                                    <td><a href="javascript:void(0)" data-id="{{ $order->id }}" id="order-products"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                                    <td>@if(!empty($order->customer->name)) {{$order->customer->name}} @else {{'-'}} @endif</td>
                                    <?php $orderStatusHistories = $order->orderStatusHistories->pluck('created_at', 'new_status')->toArray(); ?>
                                    @foreach ($orderStatusList as $key => $orderStatus)
                                        <td>
                                            @if (array_key_exists($key, $orderStatusHistories))
                                                {{ $orderStatusHistories[$key] }}
                                            @endif
                                        </td>
                                    @endforeach()
                                </tr>
                            @endif
                        @endforeach()
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="order-product-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Order Products</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="90%">Product Name</th>                                    
                                </tr>
                            </thead>
                            <tbody class="order-product-list-view">
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

    @include("orders.partials.column-visibility-modal-order-journey")
@endsection

@section('scripts')
    <script>
        /** infinite loader **/
        var isLoading = false;
        var page = 1;
        $(document).ready(function() {
            $(window).scroll(function() {
                if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
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
                    url: "/order/get-order-journey?page=" + page,
                    type: 'GET',
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function(data) {
                        $loader.hide();
                        $('.infinite-scroll-pending-inner').append(data.tbody);
                        isLoading = false;
                        if (data.tbody == "") {
                            isLoading = true;
                        }
                    },
                    error: function() {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }
        });

        $(document).on('click', '#order-products', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{route('orders.journey.products')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'id' :id,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                <td> ${k + 1} </td>                                
                                <td> ${v.name} </td>
                            </tr>`;
                        });
                        $("#order-product-list").find(".order-product-list-view").html(html);
                        $("#order-product-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $("#filer_customer_list").select2({
            multiple: true,
            placeholder: "Select Customers"
        });
        
        //End load more functionality
    </script>
@endsection
