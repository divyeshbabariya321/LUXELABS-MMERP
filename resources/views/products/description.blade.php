@extends('layouts.app')


@section('favicon' , 'productstats.png')


@section('title', 'Product Description')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<style type="text/css">
  .modal-lg{
            max-width: 1500px !important; 
  }
  .modal-xl {
    max-width: 90% !important;
}
        </style>
@endsection
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
@section('content')
    <div class="row">
        <div class="col-md-12">
            
            <h2 class="page-heading">Total Product found ({{$products_count}})</h2>
        </div>
    </div>
<div class="row">
    <div class="col-md-12 pl-5 pr-5">
    <form action="{{ action([\App\Http\Controllers\ProductController::class, 'productDescription']) }}" method="get">
        <div class="row mb-5">
			<div class="col-md-2 pd-sm">
                <h5>Search Suppliers </h5>	
                <select class="form-control globalSelect2" multiple="true" id="supplier" name="supplier[]" placeholder="Select suppliers">
                    <option value="">Select Suppliers</option>
                    @foreach($supplier as $supplier)
                    <option value="{{ $supplier->id }}" @if(in_array($supplier->id, $request->input('supplier', []))) selected @endif>{{ $supplier->supplier }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 pd-sm">
                <h5>Search Colors </h5>	
            {{ html()->multiselect("colors[]", [])->class("form-control globalSelect2")->style("width: 100%;")->data('ajax', route('select2.productsColors'))->data('placeholder', 'Select colors') }}
            </div>
            <div class="col-md-2 pd-sm">
                <h5>Search Size system </h5>	
                {{ html()->multiselect("sizeSystem[]", [])->class("form-control globalSelect2")->style("width: 100%;")->data('ajax', route('select2.productsSizesystem'))->data('placeholder', 'Select Sizesystem') }}
            </div>
            
			<div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_id" class="form-control" id="product_id" placeholder="Enter Product ID" value="{{isset($request->product_id) ? $request->product_id : ''}}">
            </div>
			<div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="sku" class="form-control" id="sku" placeholder="Enter SKU" value="{{isset($request->sku) ? $request->sku : ''}}">
            </div>
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_title" class="form-control" id="product_title" placeholder="Enter Title" value="{{isset($request->product_title) ? $request->product_title : ''}}">
            </div>
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_description" class="form-control" id="product_description" placeholder="Enter Description" value="{{isset($request->product_description) ? $request->product_description : ''}}">
            </div>
            
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_size" class="form-control" id="product_size" placeholder="Enter Size" value="{{isset($request->product_size) ? $request->product_size : ''}}">
            </div>
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_composition" class="form-control" id="product_composition" placeholder="Enter Composition" value="{{isset($request->product_composition) ? $request->product_composition : ''}}">
            </div>
            
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_price" class="form-control" id="product_price" placeholder="Enter Product price" value="{{isset($request->product_price) ? $request->product_price : ''}}">
            </div>
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_discount" class="form-control" id="product_discount" placeholder="Enter Product Discounts" value="{{isset($request->product_discount) ? $request->product_discount : ''}}">
            </div>
			<div class="col-md-2 pd-sm"><br><br>
                <button class="btn btn-image mt-0">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                <a href="{{route('products.description')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </div>
    </form>
</div>
<div class="col-md-12 ml-1 description">
    <div class="form-group small-field change-list-all-replace_description-wrap">
        <div class="col-md-2 pd-sm">
            <input type="text" name="replace_description" class="form-control replace_description" id="replace_description" placeholder="Enter Replace Keyword" value="">
        </div>
        <button type="button" class="btn btn-secondary update-description-selected">Update Selected</button>
    </div>
</div>
</div>
    
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $products->appends($request->except('page'))->links() }}.
        </div>
    </div>
    <div class="col-md-12 ">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered" id="quick-reply-list" style="table-layout: fixed;">
                <tr>
                    <th width="3%"><span><input type="checkbox" class="check-all-btn mr-2">&nbsp;</span></th>
                    <th width="6%">Product ID</th>
                    <th width="5%">SKU</th>
                    <th width="4%">Supplier</th>
                    <th width="5%">Title</th>
                    <th width="6%">Description</th>
                    <th width="6%">Color</th>
                    <th width="3%">Size</th>
                    <th width="5%">Category</th>
                    <th width="6%">Composition</th>
                    <th width="4%">Price</th>
                    <th width="6%">Size System</th>
                    <th width="5%">Discount</th>
                    <th width="6%">Dimensions</th>
                    <th width="10%">Date Time</th>                    
                    <th width="5%">Update History</th>
                    <th width="5%">Functions</th>
                </tr>
                @foreach($products as $product)
                    <tr>
                        <td><input type="checkbox" name="product[]" value="{{ $product->product_id }}" class="product-checkbox mr-2"></td>
                        <td>
                            <a target="__blank" href="{{$product->supplier_link}}" style="color:black;">{{$product->product_id}}</a>
                        </td>
                        <td class="Website-task visible-app" title="{{isset($product->product->sku) ? $product->product->sku : "-"}}">
                            {{isset($product->product->sku) ? $product->product->sku : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->supplier->supplier) ? $product->supplier->supplier : "-"}}">
                            {{isset($product->supplier->supplier) ? $product->supplier->supplier : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->title) ? $product->title : "-"}}">
                            {{isset($product->title) ? $product->title : "-"}}
                        </td>
                        <td class="Website-task product_description" data-id="{{ $product->product_id }}" title="{{isset($product->description) ? $product->description : "-"}}">
                            {{isset($product->description) ? $product->description : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->color) ? $product->color : "-"}}">
                            {{isset($product->color) ? $product->color : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->size) ? $product->size : "-"}}">
                            {{isset($product->size) ? $product->size : "-"}}
                        </td>
                        <td class="Website-task"title="{{isset($product->product->categories) ? $product->product->categories->title : "-"}}">
                            {{isset($product->product->categories) ? $product->product->categories->title : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->composition) ? $product->composition : "-"}}">
                            {{isset($product->composition) ? $product->composition : "-"}}
                        </td>
                        <td>
                            {{isset($product->price) ? $product->price : "-"}}
                        </td>
                        <td>
                            {{isset($product->size_system) ? $product->size_system : "-"}}
                        </td>
                        <td>
                            {{isset($product->price_discounted) ? $product->price_discounted : "-"}}
                        </td>
                        <td>
                            {{isset($product->product) ? $product->product->lmeasurement.",".$product->product->hmeasurement.",".$product->product->dmeasurement : "-"}}
                        </td>
                        <td>
                            {{isset($product->last_started_at) ? $product->last_started_at : "-"}}
                        </td>
                        <td>
                            @php
                            $result_count = app('App\Loggers\LogScraper')->getProductFromSku(isset($product->product->sku) ? $product->product->sku : "");
                            @endphp
                
                            <button style="padding: 1px" data-id="{{ isset($product->product->sku) ? $product->product->sku : "" }}" type="button" class="btn btn-image d-inline get-product-history" title="Products History">
                                {{$result_count}} <i class="fa fa-eye"></i>
                           </button>
                        </td>
                        <td>
                            <button style="padding: 1px" data-id="{{ $product->scraper_id }}" type="button" class="btn btn-image d-inline get-tasks-remote" title="Task list">
                                <i class="fa fa-tasks"></i>
                           </button>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $products->appends($request->except('page'))->links() }}.
        </div>
    </div>
    <div id="show-content-model-table" class="modal fade scrp-task-list" role="dialog">
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
  <div id="show-content-product-history-table" class="modal fade product-history-list" role="dialog">
    <div class="modal-dialog modal-xl">
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

     <!-- The Modal -->
  <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Change Description</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <div class="form-group">
              <label for="input1">Description:</label>
              <input type="text" class="form-control" name="description" id="input1" required>
            </div>
            <div class="form-group">
              <label for="input2">Change Description:</label>
              <input type="text" class="form-control" name="change_description" id="input2" required>
              <input type="hidden" class="form-control" name="product_id[]" id="product_id">
            </div>
            <button type="submit" class="btn btn-primary update-description-select">Submit</button>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

      </div>
    </div>
  </div>
@endsection


@section('scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    @include('partials.script_developer_task')
    <script>
        document.addEventListener('mouseup', function(event) {
          // Check if the clicked element has the 'selected-row' class
          if (event.target.classList.contains('product_description')) {
            var selectedText = getSelectedText();
            if (selectedText) {
              var dataIsAttribute = event.target.getAttribute('data-id');
              $('#myModal').modal('show');
              $('#input1').val(selectedText);
              $('#product_id').val(dataIsAttribute);
              console.log('Selected Text:', selectedText);
              console.log('Custom Attribute (data-id):', dataIsAttribute);
            }
          }
        });
    
        function getSelectedText() {
          var text = '';
    
          if (window.getSelection) {
            text = window.getSelection().toString();
          } else if (document.selection && document.selection.type !== 'Control') {
            text = document.selection.createRange().text;
          }
    
          return text;
        }
      </script>
    <script>
        $("#select_date").datepicker({
	  	format: 'yyyy-mm-dd'
	});


    $(document).off('click', '.update-description-select').on('click', '.update-description-select', function() {
        $("#loading-image").show();
        var changefrom = $("#input1").val();
        var changeto = $("#input2").val();
        var changesIds = $("#product_id").val();
        var idArray = changesIds.split(',');
        console.log('Selected changesIds:', idArray);
        
            $.ajax({
                type: 'POST',
                url: '/products/description/update',
                beforeSend: function() {
                    $("#loading-image").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    from: changefrom,
                    to: changeto,
                    ids: idArray,
                },
                dataType: "json"
            }).done(function(response) {
                console.log(response);
                $("#loading-image").hide();
                if (response.code == 200) {
                    if (response.html != "") {
                        $('#myModal').modal('hide');
                        toastr['success'](response.message, 'success');
                        $("#loading-image").show();
                        var redirectUrl = '/products/description';
                        window.location.href = redirectUrl;
                    } else {
                        $("#loading-image").hide();
                        toastr['error']('Sorry, something went wrong', 'error');
                    }
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                toastr['error']('Sorry, something went wrong', 'error');
            });
    });



    $(document).off('click', '.update-description-selected').on('click', '.update-description-selected', function() {
        $("#loading-image").show();
        var changefrom = $("#product_description").val();
        var changeto = $(".replace_description").val();
        var changesIds = $(".product-checkbox:checked");
        var checkedValues = [];
        changesIds.each(function() {
            checkedValues.push($(this).val());
        });
        if (changefrom == '') {
            $("#loading-image").hide();
            toastr['error']('Sorry, Please enter description and search result', 'error');
        } else if(changeto == ''){
            $("#loading-image").hide();
            toastr['error']('Sorry, Please enter replace keyword', 'error');
        } else if(checkedValues.length <= 0){
            $("#loading-image").hide();
            toastr['error']('Sorry, Please select at least one product', 'error');
        } else {
            $.ajax({
                type: 'POST',
                url: '/products/description/update',
                beforeSend: function() {
                    $("#loading-image").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    from: changefrom,
                    to: changeto,
                    ids: checkedValues,
                },
                dataType: "json"
            }).done(function(response) {
                console.log(response);
                $("#loading-image").hide();
                if (response.code == 200) {
                    if (response.html != "") {
                        toastr['success'](response.message, 'success');
                        $("#loading-image").show();
                        var redirectUrl = '/products/description';
                        window.location.href = redirectUrl;
                    } else {
                        $("#loading-image").hide();
                        toastr['error']('Sorry, something went wrong', 'error');
                    }
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                toastr['error']('Sorry, something went wrong', 'error');
            });
        }
    });
    $(document).ready(function() {
    // When the check-all-btn is clicked
        $(".check-all-btn").on("change", function() {
            // If check-all-btn is checked, check all product-checkbox
            if ($(this).prop("checked")) {
                $(".product-checkbox").prop("checked", true);
            } else {
                // If check-all-btn is unchecked, uncheck all product-checkbox
                $(".product-checkbox").prop("checked", false);
            }
        });

        // When any product-checkbox is clicked
        $(".product-checkbox").on("change", function() {
            // Check if all product-checkbox are checked, then check the check-all-btn
            if ($(".product-checkbox:checked").length === $(".product-checkbox").length) {
                $(".check-all-btn").prop("checked", true);
            } else {
                // If any product-checkbox is unchecked, uncheck the check-all-btn
                $(".check-all-btn").prop("checked", false);
            }
        });
    });

 </script>
@endsection