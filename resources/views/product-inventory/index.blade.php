@extends('layouts.app')
@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection

@php
	$roletype = "Inventory";
  $isAdmin = auth()->user()->isAdmin();
@endphp

@section('favicon' , 'inventory.png')
@section('title', 'Products Grid - ERP Sololuxury')

@section('content')
<div class="inventory-list-layout">
  <div class="row">
        <div class="col-lg-12 margin-tb">
        	<h2 class="page-heading">Product Inventory ({{ $products->total() }})</h2>
        </div>
        @if(auth()->user()->isAdmin())
          @if( $roletype != 'Selection' && $roletype != 'Sale' )
          	  	<div class="col-lg-4 margin-tb">	
	              	<div class="pt-2 col-md-3 pb-3 pr-0">
	              		<a href="{{ route('pending',$roletype) }}" class="text-secondary"><strong>Pending: </strong> {{ \App\Product::getPendingProductsCount($roletype) }}</a>
	              	</div>
	              	@if ($roletype == 'Inventory')
                  <div class="col-md-9 import-form pl-0">
	                	<form class="form-inline mb-3" action="{{ route('productinventory.import') }}" method="POST" enctype="multipart/form-data">
	                  		@csrf
	                  		<div class="form-group">
	                    		<input type="file" name="file" class="form-control-file" required>
                        <button type="submit" class="btn btn-secondary ml-3 custom-button">Import Inventory</button>
	                  		</div>
	                	</form>
                  </div>
	              @endif
        		</div>
          @endif
        @endif
        @if(auth()->user()->isAdmin())
          <div class="col-lg-8 margin-tb brand-update-form pl-0"> 
              <form class="form-inline mb-3" action="{{ route('productinventory.merge-scrap-brand') }}" method="POST">
                  @csrf
                  <div class="col-md-auto pl-0">
                  <div class="form-group">
                    
                    <input type="text" name="scraper_brand" placeholder="Enter scraper Brand" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-auto pl-0">
                  <div class="form-group">
                    
                    <input type="text" name="product_brand" placeholder="Enter product brand" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-auto pl-0">
                  <button type="submit" class="btn btn-secondary ml-3 custom-button">Update Brand</button>
                </div>
              </form>
          </div>
        @endif
        <div class="col-lg-12 margin-tb">
          @include('partials.flash_messages')

        	<form action="?" method="GET" class="form-inline align-items-start w-100">
            <div class="w-100 custom-form">
        		<div class="d-flex flex-wrap">
                <div class="form-group mb-3 col-md-3 col-12">      
                        {{ html()->multiselect('sku[]', [], request("sku", []))->data('placeholder', 'Select a Sku')->class('form-control w-100')->id('product-skus') }}
            	</div>
                
        		<div class="form-group mb-3 col-md-3 col-12">
                  {!! $category_selection !!}
                </div>
                <div class="form-group mb-3 col-md-3 col-12">
                  @php $brands = \App\Brand::getAll(); @endphp
                  {{ html()->multiselect('brand[]', $brands, request("brand", []))->data('placeholder', 'Select a Brand')->class('form-control w-100 select-multiple2') }}
                </div>
                <div class="form-group mb-3 col-md-3 col-12">
                  @php $colors = new \App\Colors(); @endphp
                  {{ html()->multiselect('color[]', $colors->all(), request("color", []))->data('placeholder', 'Select a Color')->class('form-control w-100 select-multiple2')->style("width:250px;") }}
                </div>
                @if($isAdmin)
                  <div class="form-group mb-3 col-md-3 col-12">
                  	{{ html()->multiselect('supplier[]', $suppliersDropList, request("supplier", []))->data('placeholder', 'Select a Supplier')->class('form-control w-100 select-multiple2') }}
                  </div>
                  <div class="form-group mb-3 col-md-3 col-12">
                  	{{ html()->multiselect('scrapper[]', $scrapperDropList, request("scrapper", []))->data('placeholder', 'Select a Scrapper')->class('form-control w-100 select-multiple2') }}
                  </div>
                  <div class="form-group mb-3 col-md-3 col-12">
                  	{{ html()->multiselect('type[]', $typeList, request("type", []))->data('placeholder', 'Select a Type')->class('form-control select-multiple2 w-100') }}
                  </div>
                @endif
                <div class="form-group mb-3 col-md-3 col-12">
                    <input name="size" type="text" class="form-control w-100" value="{{ request('size',null) }}" placeholder="Size">
                  </div>
                 <div class="form-group mb-3 col-md-3 col-12">
                    <input type="text" name="price_min" class="form-control w-100" placeholder="min. price" value="{{ isset($_GET['price_min']) ? $_GET['price_min'] : '' }}">
                 </div>
                 <div class="form-group mb-3 col-md-3 col-12">
                    <input type="text" name="price_max" class="form-control w-100" placeholder="max. price" value="{{ isset($_GET['price_max']) ? $_GET['price_max'] : '' }}">
                 </div>
                 <div class="form-group mb-3 col-md-3 col-12">
                    <input type="text" name="discounted_percentage_min" class="form-control w-100" placeholder="min. price" value="{{ isset($_GET['discounted_percentage_min']) ? $_GET['discounted_percentage_min'] : '' }}">
                 </div>
                 <div class="form-group mb-3 col-md-3 col-12">
                    <input type="text" name="discounted_percentage_max" class="form-control w-100" placeholder="max. price" value="{{ isset($_GET['discounted_percentage_max']) ? $_GET['discounted_percentage_max'] : '' }}">
                 </div>
                 <div class="form-group mb-3 col-md-2 col-12">
                    <div class='input-group date' id='filter-date'>
                        <input type='text' class="form-control w-100" name="date" value="{{ request('date','') }}" placeholder="Date" />
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                 </div>
                 <div class="form-group mb-3 col-md-7 col-12 d-flex justify-content-between">
                    <div class="d-flex"><input {{ (request('is_on_sale')) ? 'checked' : '' }} type="checkbox" name="is_on_sale" id="is_on_sale" class="mr-2"><label for="is_on_sale">Sale</label></div>
                    <div  class="d-flex"><input {{ (request('without_category')) ? 'checked' : '' }} ? 'checked' : '' }} type="checkbox" name="without_category" id="without_category" class="mr-2"><label for="without_category">Without Category?</label></div>
                    <div  class="d-flex">
                     <input {{ (request('without_color')) ? 'checked' : '' }} ? 'checked' : '' }} type="checkbox" name="without_color" id="without_color" class="mr-2"><label for="without_color">Without Color?</label>
                  </div>
                  <div  class="d-flex">
                     <input {{ (request('without_composition')) ? 'checked' : '' }} ? 'checked' : '' }} type="checkbox" name="without_composition" id="without_composition" class="mr-2"><label for="without_composition">Without Composition?</label>
                  </div>
                  <div  class="d-flex">
                     <input {{ (request('final_approval')) ? 'checked' : '' }} ? 'checked' : '' }} type="checkbox" name="final_approval" id="final_approval" class="mr-2"><label for="final_approval">Final Approved?</label>
                  </div  class="d-flex">
                  @if (isset($customer_id) && $customer_id != null)
                    <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                  @endif
                  <div  class="d-flex"><button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button></div>
                 </div>
                </div>
                </div>
        	</form>
        </div>
       
        
        <div class="col-lg-12 margin-tb">
	        <div class="productGrid " id="productGrid">
        		{{-- <div class="infinite-scroll-products" style="padding-bottom: 60px"> --}}

                    {{-- <div class="infinite-scroll-products-inner"> --}}
                        @include("product-inventory.partials.grid")
                    {{-- </div> --}}
            	   <div class="row">
            	   		{!! $products->appends(Request::except('page'))->links() !!}
            	   </div>
                   {{-- <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" /> --}}
            	{{-- </div>   --}}
	        </div>
        </div>
  </div>
</div>
@endsection
@include('partials.modals.category')
@include('partials.modals.color')
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
{{--  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>--}}
  <script>
 
        $(document).ready(function () {
            $('#product-skus').select2({
            tags: true, // Allow the user to add new tags (SKUs)
            ajax: {
                url: '/product-inventory/search/sku', // Your autosuggest route
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {
                    var results = data.map(function (item) {
                        return { id: item, text: item };
                    });

                    return {
                        results: results
                    };
                },
                cache: true
            },
        });

        function getUrlParameterArray(name) {
            // Use regular expression to get all values of the parameter from the URL
            var regex = new RegExp('[?&]' + name + '=([^&#]*)', 'g');
            var results = [];
            var match;

            // Loop through all occurrences of the parameter
            while (match = regex.exec(window.location.search)) {
                results.push(decodeURIComponent(match[1].replace(/\+/g, ' ')));
            }

            return results.length > 0 ? results : null;
        }

        var skuArray = getUrlParameterArray('sku%5B%5D');

        // Append selected values to the Select2 dropdown
        skuArray.forEach(function (value) {
            var option = new Option(value, value, true, true);
            $('#product-skus').append(option).trigger('change');
        })

        });

        var isLoadingProducts = false;
    	var loadCount = $('.container-grow').find('.card-body').length;
    	$(document).ready(function() {
       		$(".select-multiple").multiselect();
       		$(".select-multiple2").select2();
    	});

        $(document).on('mouseover', 'select.update-product', function() { 
            $(this).select2().select2('open');
        });

        $(document).on('mouseover', 'select.update-color', function() { 
            $(this).select2().select2('open');
        });
        
        //$(document),on()
       /* $(function () {
            $(window).scroll(function() {
                if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
                    loadMoreProducts();
                }
            });

            $('.infinite-scroll').jscroll({
                debug: true,
                autoTrigger: true,
                loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
                padding: 2500,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.infinite-scroll',
                callback: function () {
                	$('ul.pagination').not(":last").remove();
                   	//$(".select-multiple").multiselect();
       				//$(".select-multiple2").select2();
                }
            });
        });*/

        $(document).on('change', '.update-product', function () {    
            product_id 	= $(this).attr('data-id');
            category 	= $(this).find('option:selected').text();
            category_id = $(this).val();
            //Getting Scrapped Category
            $.ajax({
                url: '/products/'+product_id+'/originalCategory',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function () {
                  $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();
                    $('#categoryUpdate').modal('show');
                    if(result[0] == 'success'){
                        $('#old_category').text(result[1]);
                        $('#changed_category').text(category);
                        $('#product_id').val(product_id);
                        $('#category_id').val(category_id);
                        if(typeof result[2] != "undefined") {
                            $("#no_of_product_will_affect").html(result[2]);
                        }
                    }else{
                        $('#old_category').text('No Scraped Product Present');
                        $('#changed_category').text(category);
                        $('#product_id').val(product_id);
                        $('#category_id').val(category_id);
                        $("#no_of_product_will_affect").html(0);
                    }
                },
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                    $('#categoryUpdate').modal('show');
                    $('#old_category').text('No Scraped Product Present');
                    $('#changed_category').text(category);
                    $('#product_id').val(product_id);
                    $('#category_id').val(category_id);
                    $("#no_of_product_will_affect").html(0);
                }
            });
        });

      /*function loadMoreProducts() {
          var product_count = "{{$productCount}}";
          //console.log(product_count +'=='+ loadCount );
          if (isLoadingProducts)
              return;
        if(parseInt(loadCount) == parseInt(product_count))
              return;
          isLoadingProducts = true;

          var $loader = $('.infinite-scroll-products-loader');

          $.ajax({
              url: $('.pagination li.active + li a').attr('href'),
              type: 'GET',
              beforeSend: function() {
                  $loader.show();
                  $('ul.pagination').remove();
              }
          })
          .done(function(data) {
              if('' === data.trim())
                  return;
                loadCount += $(data).find('.card-body').length
                console.log(loadCount);
              $loader.hide();
              $('.infinite-scroll-products-inner').append(data);
              isLoadingProducts = false;
          })
          .fail(function(jqXHR, ajaxOptions, thrownError) {
              console.error('something went wrong');

              isLoadingProducts = false;
          });
      }*/

      function changeSelected(){
            product_id 	= $('#product_id').val();
            category 	= $('#category_id').val();
            $.ajax({
                url: '/products/'+product_id+'/updateCategory',
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    category : category
                },
                beforeSend: function () {
                      $('#categoryUpdate').modal('hide');  
                      $("#loading-image").show();
                      //$("#loading-image").hide();
                },
                success: function(result){
                    toastr['success']('Request Sent successfully', 'success');
                    $("#loading-image").hide();
                }, 
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                }
            });
        }

        function changeAll(){
            product_id = $('#product_id').val();
            category = $('#category_id').val();
            $.ajax({
                url: '/products/'+product_id+'/changeCategorySupplier',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    category : category
                },
                beforeSend: function () {
                              $('#categoryUpdate').modal('hide');  
                              $("#loading-image").show();
                          },
                success: function(result){
                    toastr['success']('Request Sent successfully', 'success');
                     $("#loading-image").hide();
            	}, 
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                }
         	});
        }


        function changeSelectedColor(){
            product_id = $('#product_id').val();
            color = $('#color_id').val();
            $.ajax({
                url: '/products/'+product_id+'/updateColor',
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    color : color
                },
                beforeSend: function () {
	                $("#loading-image").show();
	                $("#loading-image").hide();
	            },
                success: function(result){
                    $('#colorUpdate').modal('hide');
                    toastr['success']('Request Sent successfully', 'success');
                     $("#loading-image").hide();
                }, 
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                }
            });
        
        }


        function changeAllColors(){
            product_id = $('#product_id').val();
            color = $('#color_id').val();
            $.ajax({
                url: '/products/'+product_id+'/changeColorSupplier',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    color : color
                },
                beforeSend: function () {
                   $('#colorUpdate').modal('hide');  
                   $("#loading-image").show();
                },
                success: function(result){
                   toastr['success']('Request Sent successfully', 'success');
                    $("#loading-image").hide();
             	}, 
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                }
         	});
        } 


     $(document).on('change', '.update-color', function () {    
            product_id = $(this).attr('data-id');
            color = $(this).find('option:selected').text();
            color_id = $(this).val();
            //Getting Scrapped Category
            $.ajax({
                url: '/products/'+product_id+'/originalColor',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function () {
                  $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();
                    $('#colorUpdate').modal('show');
                    if(result[0] == 'success'){
                        $('#old_color').text(result[1]);
                        $('#changed_color').text(color);
                        $('#product_id').val(product_id);
                        $('#color_id').val(color_id);
                        if(typeof result[2] != "undefined") {
                            $("#no_of_product_will_affect_color").html(result[2]);
                        }
                    }else{
                        $('#old_color').text('No Scraped Product Present');
                        $('#changed_color').text(color);
                        $('#product_id').val(product_id);
                        $('#color_id').val(color_id);
                        $("#no_of_product_will_affect_color").html(0);
                    }
                },
                error: function (){
                    toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                    $('#colorUpdate').modal('show');
                    $('#old_color').text('No Scraped Product Present');
                    $('#changed_color').text(color);
                    $('#product_id').val(product_id);
                    $('#color_id').val(color_id);
                    $("#no_of_product_will_affect_color").html(0);
                }
            });
        });

        $(document).on('click', '.add_purchase_product', function () {    
            product_id = $(this).attr('data-id');
           
            $.ajax({
                url: '/products/add/def_cust/'+product_id,
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function () {
                  $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();
                    if(result.code == 200)
                    {
                        toastr['success'](result.message, 'success');
                    }
                },
                error: function (){
                    // toastr['error']('Oops, Something went wrong', 'error');
                    $("#loading-image").hide();
                }
            });
        });
  </script>
@endsection    