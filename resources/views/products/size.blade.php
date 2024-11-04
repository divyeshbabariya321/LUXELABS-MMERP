@extends('layouts.app')


@section('favicon' , 'productstats.png')


@section('title', 'Product Size')
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
    <form action="{{ action([\App\Http\Controllers\ProductController::class, 'productSizeLog']) }}" method="get">
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
            <div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="product_id" class="form-control" id="product_id" placeholder="Enter Product ID" value="{{isset($request->product_id) ? $request->product_id : ''}}">
            </div>
			<div class="col-md-2 pd-sm"><br><br>
                <input type="text" name="sku" class="form-control" id="sku" placeholder="Enter SKU" value="{{isset($request->sku) ? $request->sku : ''}}">
            </div>
			<div class="col-md-2 pd-sm"><br><br>
                <button class="btn btn-image mt-0">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                <a href="{{route('products.size')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </div>
    </form>
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
                    <th width="6%">Product ID</th>
                    <th width="5%">SKU</th>
                    <th width="4%">Supplier</th>
                    <th width="5%">Title</th>
                    <th width="5%">Category</th>
                    <th width="8%">Size</th>
                    <th width="6%">Product Size</th>
                </tr>
                @foreach($products as $product)
                    <tr>
                       <td>
                           {{$product->product_id}}
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
                        <td class="Website-task"title="{{isset($product->product->categories) ? $product->product->categories->title : "-"}}">
                            {{isset($product->product->categories) ? $product->product->categories->title : "-"}}
                        </td>
                        <td class="Website-task" title="{{isset($product->size) ? $product->size : "-"}}">
                            {{isset($product->size) ? $product->size : "-"}}
                        </td>
                        <td class="Website-task"title="{{isset($product->product) ? $product->product->size : "-"}}">
                            {{isset($product->product) ? $product->product->size : "-"}}
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
@endsection


@section('scripts')    
    <script>
        $("#select_date").datepicker({
	  	format: 'yyyy-mm-dd'
	});


 </script>
@endsection