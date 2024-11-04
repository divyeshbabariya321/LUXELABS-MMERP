@extends('layouts.app')


@section('favicon', 'productstats.png')


@section('title', 'Product Status Log')


@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">
                Total Product found ({{ $products_count }})

                <button type="button" class="btn custom-button float-right mr-3" data-toggle="modal" data-target="#pdatatablecolumnvisibilityList">Column Visiblity</button>
            </h2>
        </div>
    </div>
    <form action="{{ action([\App\Http\Controllers\ProductController::class, 'productScrapLog']) }}" method="get">
        <div class="row mb-5">
            <div class="col-md-2">
                <div class="form-group">
                    <input type="text" name="select_date" class="form-control datepicker" id="select_date"
                        placeholder="Enter Date" value="{{ isset($request->select_date) ? $request->select_date : '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <input type="text" name="product_id" class="form-control" id="product_id" placeholder="Enter Product ID"
                    value="{{ isset($request->product_id) ? $request->product_id : '' }}">
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <select class="form-control" name="status" id="status">
                        <option value="">Status</option>
                        @foreach ($status as $k => $val)
                            <option {{ $request->get('status') == $k ? 'selected' : '' }} value="{{ $k }}">
                                {{ ucwords($val) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <input type="text" name="sku" class="form-control" id="sku" placeholder="Enter Sku"
                    value="{{ isset($request->sku) ? $request->sku : '' }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-image btn-default">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $products->appends($request->except('page'))->links() }}.
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <tr>
                    @if(!empty($dynamicColumnsToShowp))
                        @if (!in_array('Date', $dynamicColumnsToShowp))
                            <th>Date</th>
                        @endif
                        @if (!in_array('Product ID', $dynamicColumnsToShowp))
                            <th>Product ID</th>
                        @endif
                        @if (!in_array('Suppliers', $dynamicColumnsToShowp))
                            <th>Suppliers</th>
                        @endif
                        @if (!in_array('Scrape', $dynamicColumnsToShowp))
                            <th>Scrape</th>
                        @endif
                        @if (!in_array('Auto crop', $dynamicColumnsToShowp))
                            <th>Auto crop</th>
                        @endif
                        @if (!in_array('Final approval', $dynamicColumnsToShowp))
                            <th>Final approval</th>
                        @endif
                        @if (!in_array('Is being cropped', $dynamicColumnsToShowp))
                            <th>Is being cropped</th>
                        @endif
                        @if (!in_array('Is being scraped', $dynamicColumnsToShowp))
                            <th>Is being scraped</th>
                        @endif
                        @if (!in_array('Pending products without category', $dynamicColumnsToShowp))
                            <th>Pending products without category</th>
                        @endif
                        @if (!in_array('Request For external Scraper', $dynamicColumnsToShowp))
                            <th>Request For external Scraper</th>
                        @endif
                        @if (!in_array('Send external Scraper', $dynamicColumnsToShowp))
                            <th>Send external Scraper</th>
                        @endif
                        @if (!in_array('Finished external Scraper', $dynamicColumnsToShowp))
                            <th>Finished external Scraper</th>
                        @endif
                        @if (!in_array('Unknown Color', $dynamicColumnsToShowp))
                            <th>Unknown Color</th>
                        @endif
                        @if (!in_array('Unknown Size', $dynamicColumnsToShowp))
                            <th>Unknown Size</th>
                        @endif
                        @if (!in_array('Unknown Composition', $dynamicColumnsToShowp))
                            <th>Unknown Composition</th>
                        @endif
                        @if (!in_array('Unknown Composition', $dynamicColumnsToShowp))
                            <th>Unknown Composition</th>
                        @endif
                    @else
                        <th>Date</th>
                        <th>Product ID</th>
                        <th>Suppliers</th>
                        <th>Scrape</th>
                        <th>Auto crop</th>
                        <th>Final approval</th>
                        <th>Is being cropped</th>
                        <th>Is being scraped</th>
                        <th>Pending products without category</th>
                        <th>Request For external Scraper</th>
                        <th>Send external Scraper</th>
                        <th>Finished external Scraper</th>
                        <th>Unknown Color</th>
                        <th>Unknown Size</th>
                        <th>Unknown Composition</th>
                        <th>Unknown Measurement</th>
                @endif
                </tr>
                @foreach ($products as $product)
                    <tr>
                        @if(!empty($dynamicColumnsToShowp))
                            @if (!in_array('Date', $dynamicColumnsToShowp))
                            <td>{{ isset($request->select_date) ? $request->select_date : date('Y-m-d') }}</td>
                            @endif

                            @if (!in_array('Product ID', $dynamicColumnsToShowp))
                            <td>
                                <a
                                    href="{{ action([\App\Http\Controllers\ProductController::class, 'show'], $product->id) }}">{{ $product->id }}</a>
                            </td>
                            @endif

                            @if (!in_array('Suppliers', $dynamicColumnsToShowp))
                                <td>
                                    <a href="javascript:void(0)" data-id="{{ $product->id }}" id="view-product-suppliers"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                </td>
                            @endif

                            @if (!in_array('Scrape', $dynamicColumnsToShowp))
                            <td>
                                
                                @if (isset($product->alllog_status[2]) && $product->alllog_status[2]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[2]) && $product->alllog_status[2]['pending_status']==0)
                                    {{ $product->alllog_status[2]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif

                            @if (!in_array('Auto crop', $dynamicColumnsToShowp))
                            <td>                                
                                @if (isset($product->alllog_status[4]) && $product->alllog_status[4]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[4]) && $product->alllog_status[4]['pending_status']==0)
                                    {{ $product->alllog_status[4]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif

                            @if (!in_array('Final approval', $dynamicColumnsToShowp))
                            <td>                                
                                @if (isset($product->alllog_status[9]) && $product->alllog_status[9]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[9]) && $product->alllog_status[9]['pending_status']==0)
                                    {{ $product->alllog_status[9]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif

                            @if (!in_array('Is being cropped', $dynamicColumnsToShowp))
                            <td>                                
                                @if (isset($product->alllog_status[15]) && $product->alllog_status[15]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[15]) && $product->alllog_status[15]['pending_status']==0)
                                    {{ $product->alllog_status[15]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif

                            @if (!in_array('Is being scraped', $dynamicColumnsToShowp))
                            <td>                                
                                @if (isset($product->alllog_status[20]) && $product->alllog_status[20]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[20]) && $product->alllog_status[20]['pending_status']==0)
                                    {{ $product->alllog_status[20]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif

                            @if (!in_array('Pending products without category', $dynamicColumnsToShowp))
                            <td>                                
                                @if (isset($product->alllog_status[33]) && $product->alllog_status[33]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[33]) && $product->alllog_status[33]['pending_status']==0)
                                    {{ $product->alllog_status[33]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif

                            @if (!in_array('Request For external Scraper', $dynamicColumnsToShowp))
                            <td>                                
                                @if (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==0)
                                    {{ $product->alllog_status[35]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif

                            @if (!in_array('Send external Scraper', $dynamicColumnsToShowp))
                            <td>                                
                                @if (isset($product->alllog_status[46]) && $product->alllog_status[46]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[46]) && $product->alllog_status[46]['pending_status']==0)
                                    {{ $product->alllog_status[46]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif

                            @if (!in_array('Finished external Scraper', $dynamicColumnsToShowp))
                            <td>                                
                                @if (isset($product->alllog_status[47]) && $product->alllog_status[47]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[47]) && $product->alllog_status[47]['pending_status']==0)
                                    {{ $product->alllog_status[47]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif

                            @if (!in_array('Unknown Color', $dynamicColumnsToShowp))
                            <td>
                                @if (isset($product->all_pending_log_status[35]) || isset($product->all_pending_log_status[37]))
                                    {{ 'Pending' }}
                                @elseif (isset($product->sub_status_id) && $product->sub_status_id == 37 && $product->alllog_status[35]['created_at'])
                                    {{ $product->alllog_status[35]['created_at'] }}
                                @elseif (isset($product->alllog_status[37]['created_at']))
                                    {{ $product->alllog_status[37]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif

                            @if (!in_array('Unknown Size', $dynamicColumnsToShowp))
                            <td>                                

                                @if (isset($product->alllog_status[38]) && $product->alllog_status[38]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[38]) && $product->alllog_status[38]['pending_status']==0)
                                    {{ $product->alllog_status[38]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif

                            </td>
                            @endif

                            @if (!in_array('Unknown Composition', $dynamicColumnsToShowp))
                            <td>                                
                                @if (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==0)
                                    {{ $product->alllog_status[35]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif

                            @if (!in_array('Unknown Composition', $dynamicColumnsToShowp))
                            <td>                                
                                @if (isset($product->alllog_status[40]) && $product->alllog_status[40]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[40]) && $product->alllog_status[40]['pending_status']==0)
                                    {{ $product->alllog_status[40]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            @endif
                        @else
                            <td>{{ isset($request->select_date) ? $request->select_date : date('Y-m-d') }}</td>
                            <td>
                                <a href="{{ action([\App\Http\Controllers\ProductController::class, 'show'], $product->id) }}">{{ $product->id }}</a>
                            </td>
                            <td>
                                <a href="javascript:void(0)" data-id="{{ $product->id }}" id="view-product-suppliers"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>
                            <td>                                
                                @if (isset($product->alllog_status[2]) && $product->alllog_status[2]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[2]) && $product->alllog_status[2]['pending_status']==0)
                                    {{ $product->alllog_status[2]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            <td>                                
                                @if (isset($product->alllog_status[4]) && $product->alllog_status[4]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[4]) && $product->alllog_status[4]['pending_status']==0)
                                    {{ $product->alllog_status[4]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            <td>                                
                                @if (isset($product->alllog_status[9]) && $product->alllog_status[9]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[9]) && $product->alllog_status[9]['pending_status']==0)
                                    {{ $product->alllog_status[9]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            <td>                                
                                @if (isset($product->alllog_status[15]) && $product->alllog_status[15]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[15]) && $product->alllog_status[15]['pending_status']==0)
                                    {{ $product->alllog_status[15]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            <td>                                
                                @if (isset($product->alllog_status[20]) && $product->alllog_status[20]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[20]) && $product->alllog_status[20]['pending_status']==0)
                                    {{ $product->alllog_status[20]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            <td>                                
                                @if (isset($product->alllog_status[33]) && $product->alllog_status[33]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[33]) && $product->alllog_status[33]['pending_status']==0)
                                    {{ $product->alllog_status[33]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            <td>                                
                                @if (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==0)
                                    {{ $product->alllog_status[35]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            <td>                                
                                @if (isset($product->alllog_status[46]) && $product->alllog_status[46]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[46]) && $product->alllog_status[46]['pending_status']==0)
                                    {{ $product->alllog_status[46]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            <td>                                
                                @if (isset($product->alllog_status[47]) && $product->alllog_status[47]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[47]) && $product->alllog_status[47]['pending_status']==0)
                                    {{ $product->alllog_status[47]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            <td>
                                @if (isset($product->all_pending_log_status[35]) || isset($product->all_pending_log_status[37]))
                                    {{ 'Pending' }}
                                @elseif (isset($product->sub_status_id) && $product->sub_status_id == 37 && $product->alllog_status[35]['created_at'])
                                    {{ $product->alllog_status[35]['created_at'] }}
                                @elseif (isset($product->alllog_status[37]['created_at']))
                                    {{ $product->alllog_status[37]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            <td>                                

                                @if (isset($product->alllog_status[38]) && $product->alllog_status[38]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[38]) && $product->alllog_status[38]['pending_status']==0)
                                    {{ $product->alllog_status[38]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif

                            </td>
                            <td>                                
                                @if (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[35]) && $product->alllog_status[35]['pending_status']==0)
                                    {{ $product->alllog_status[35]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                            <td>                                
                                @if (isset($product->alllog_status[40]) && $product->alllog_status[40]['pending_status']==1)
                                    {{ 'Pending' }}
                                @elseif (isset($product->alllog_status[40]) && $product->alllog_status[40]['pending_status']==0)
                                    {{ $product->alllog_status[40]['created_at'] }}
                                @else
                                    {{ 'NA' }}
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $products->appends($request->except('page'))->links() }}.
        </div>
    </div>

    <div id="product-supplier-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Suppliers</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">     
                        <thead>
                            <th>Supplier Name</th>
                            <th>Action</th>
                        </thead>                       
                        <tbody class="product-supplier-list-view">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="show-content-model-table" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                       
                    </div>
                </div>
            </div>
      </div>
@endsection

@include("products.partials.column-visibility-modal")
@section('scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $("#select_date").datepicker({
            format: 'yyyy-mm-dd'
        });

        $(document).on('click', '#view-product-suppliers', function() {
            var product_id = $(this).attr('data-id');

            $.ajax({
                url: "{{route('products.getsuppliers')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'product_id' :product_id,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        /*html += `<tr>
                                <td> ${response.supplier} </td>
                            </tr>`;*/
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.supplier} </td>
                                        <td> <button data-id="`+v.supplier_id+`" type="button" class="btn btn-xs get-tasks-remote bg-transparent" title="Task list"><i class="fa fa-tasks"></i></button> </td>
                                    </tr>`;
                        });
                        $("#product-supplier-list").find(".product-supplier-list-view").html(html);
                        $("#product-supplier-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on("click",".get-tasks-remote",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '{{ route("scrap.task-list")}}',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Task List");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        $(document).on('click', '.send-message1', function () {
            var thiss = $(this);
            var data = new FormData();
            var task = $(this).data('task-id');
            var message = $("#messageid_"+task).val();
            data.append("issue_id", task);
            data.append("message", message);
            data.append("status", 1);
            data.append("sendTo", $(".send-message-number-"+task).val());

            if (message.length > 0) {
                if (!$(this).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/issue',
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                            $("#loading-image").show();
                        }
                    }).done(function (response) {
                        //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                        $("#message-chat-txt-"+task).html(response.message.message);
                        $("#messageid_"+task).val('');
                        $("#loading-image").hide();
                        $(this).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(this).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                        $("#loading-image").hide();
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
    </script>
@endsection
