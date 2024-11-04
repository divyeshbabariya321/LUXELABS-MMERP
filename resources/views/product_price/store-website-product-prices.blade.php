@extends('layouts.app')

@section('title','Store Website product prices')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<style>
    .model-width{
        max-width: 1250px !important;
    }
    .btn-secondary, .btn-secondary:hover, .btn-secondary:focus{
        background: #fff;
        color: #757575;
        border: 1px solid #ddd;
        outline: none;
        box-shadow: none;
    }
    table.dataTable thead th, table.dataTable thead td{
        border-bottom : 1px solid #ddd;
    }
    .table-bordered th, .table-bordered td {
        border-right: 1px solid #dee2e6;
        border-left: none !important;
        border-top: none !important;
    }
    table.dataTable thead th, table.dataTable thead td {
        padding: 3px 18px 3px 7px;
    }
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 5px 5px;
    }
    #product-price_filter{
        /*position: absolute;*/
        /*top: -10px;*/
        /*right: 10px;*/
    }
    .form-group{
        margin-bottom:0 !important;
    }
    .suppliers input{
        width:170px !important
    }
    .border-none{
        border: none !important;
    }
    table input{
        padding: 2px 6px !important;
        height: 26px !important;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ddd !important;
    }
</style>
<div class = "row m-0">
    <div class="col-lg-12 margin-tb p-0">
        <h2 class="page-heading">Store Website product prices</h2>
    </div>
</div>


@include('partials.flash_messages')
<div class = "row m-0">
    <div class="pl-3 pr-3 margin-tb">
        <div class="pull-left cls_filter_box">
            <form class="form-inline filter_form" action="" method="GET">
                <div class="form-group mr-3">
                    <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Enter Product Or SKU" id="tag-input">
                </div>
                <div class="form-group mr-3">
                    <select name="country_code" class="form-control globalSelect2">
                        @php $country = request('country_code','') @endphp
                        <option value="">Select country code</option>
                        @foreach ($countryGroups as $key => $item)
                            <option value="{{ $key }}" {{ ( $country == $key ) ? 'selected' : '' }} >{{ $item }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-3 suppliers">
                    
                    <select class="form-control globalSelect2" data-placeholder="Select Suppliers" data-ajax="{{ route('select2.suppliers',['sort'=>true]) }}"
                        name="supplier[]" multiple>
                        
                            @if ($selected_suppliers)        
                                @foreach($selected_suppliers as $supplier )
                                    <option value="{{ $supplier->id }}" selected>{{ $supplier->supplier }}</option>
                                @endforeach
                            @endif
                        </select>
                </div>
                <div class="form-group mr-3">
                    <select class="form-control globalSelect2" data-placeholder="Select Brands" data-ajax="{{ route('select2.brands',['sort'=>true]) }}"
                    name="brand_names[]" multiple>
                    <option value="">Select Brands</option>
                        @if ($selected_brands)        
                            @foreach($selected_brands as $brand)
                                <option value="{{ $brand->id }}" selected>{{ $brand->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div> 
                <div class="form-group mr-3">
                    <select class="form-control globalSelect2" data-placeholder="Select Websites" data-ajax="{{ route('select2.websites',['sort'=>true]) }}"
                    name="websites[]" multiple>
                    <option value="">Select Websites</option>
                        @if ($selected_websites)        
                            @foreach($selected_websites as $website)
                                <option value="{{ $website->id }}" selected>{{ $website->title }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>  
                <div class="form-group mr-3">
                    {{ html()->select("random", ["" => "No", "Yes" => "Yes"], request('random'))->class("form-control globalSelect2") }}
                </div>
                
                <div class="form-group mr-3">
                    <button type="submit" class="btn btn-secondary form-control">Get record</button>
                </div>
                <div class="form-group mr-3">
                    <a href="{{url('/store-website-product-prices')}}" class="fa fa-refresh form-control" aria-hidden="true" ></a>
                </div>
                <div class="form-group mr-3">
                <button type="button" class="btn btn-secondary" onclick="approve()">Approve</button>
                </div>
            </form> 
        </div>
    </div>  

</div>
<div class="row m-0">
    <div class="col-lg-12 margin-tb pl-3 pr-3"> 
        <div class="panel-group" style="margin-bottom: 5px;">

            <div class="table-responsive mt-3">

                   <table class="table table-bordered table-striped" id="product-price" style="table-layout: fixed">
                       <thead>
                       <tr>
                           <th style="width: 3%"><input id="checkAll" type="checkbox" ></th>
                           <th style="width: 9%">SKU</th>
                           <th style="width: 5%">Product ID</th>
                           <th style="width: 7%">Country</th>
                           <th style="width: 9%">Brand</th>
                           <th style="width: 4%;word-break: break-all">Segment</th>
                           <th style="width: 15%">Main Website</th>
                           <th  style="width: 5%">Default price</th>
                           <th  style="width: 5%">Segment Discount</th>
                           <th  style="width: 5%">Duty Price</th>
                           <th  style="width: 5%">Override price</th>
                           <th  style="width: 5%">Final price</th>
                           <th  style="width: 5%">Status</th>
                           <th  style="width: 5%"></th>
                        <!--   <th style="width: 7%">EURO Price</th>
                          
                           @foreach($category_segments as $category_segment)
                    <th width="3%">{{ $category_segment->name }}</th>
                @endforeach
                           <th style="width: 5%">Less IVA</th>
                           <th style="width: 5%">Net Sale Price</th>
                           <th style="width: 7%">Add Duty (Default)</th>
                           <th style="width: 13%">Add Profit</th>
                           <th style="width: 7%">Final Price</th> !-->
                       </tr>
                       </thead>
                       <tbody>
                       @php $i=1; @endphp
                       @forelse ($product_list as $key)
                           <tr data-storeWebsitesID="{{$key['storeWebsitesID']}}" data-id="{{$i}}" data-country_code="{{$key['country_code']}}" class="tr_{{$i++}}">
                           <td><input  type="checkbox" class="checkboxClass" name="selectcheck" value='{{ $key["store_website_product_prices_id"] }}'></td> 
                               <td class="expand-row" style="word-break: break-all">



                                   <span class="td-mini-container">
                                                {{ strlen( $key['sku']) > 9 ? substr( $key['sku'], 0, 9).'...' :  $key['sku'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                                {{  $key['sku'] }}
                                            </span>



                               </td>
                               <td class="product_id">{{ $key['id'] }}</td>
                               <td class="expand-row" style="word-break: break-all">
                                      <span class="td-mini-container">
                                                {{ strlen( $key['countries']) > 9 ? substr( $key['countries'], 0, 8).'...' :  $key['countries'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                               {{ $key['countries'] }}
                                            </span>
                                   </td>
                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $key['brand']) > 15 ? substr( $key['brand'], 0, 15).'...' :  $key['brand'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                               {{ $key['brand'] }}
                                            </span>
                                   </td>
                               <td>{{ $key['segment'] }}</td>
                               <td class="expand-row" style="word-break: break-all">

                                   <span class="td-mini-container">
                                                {{ strlen( $key['website']) > 22 ? substr( $key['website'], 0, 22).'...' :  $key['website'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                                {{  $key['website'] }}
                                            </span>
                               </td>

                               <td>{{ $key['default_price'] }}</td>
                               <td>{{ $key['segment_discount'] }}</td>
                               <td>{{ $key['duty_price'] }}</td>
                               <td>{{ $key['override_price'] }}</td>
                               <td>{{ $key['final_price'] }}</td>
                               <td> @if($key["status"]==1)
               approved
             @else
               pending 
             @endif     
        </td>
        <td>
        <a  class="btn btn-secondary" onclick="openhistory({{ $key['store_website_product_prices_id'] }});" >Histrory</a>   
        </td>
                              <!-- <td>{{ $key['eur_price'] }}</td>
                               
                               
                                   @foreach($category_segments as $category_segment)
                                    <td>
                                        @php
                                            $category_segment_discount = (isset($key['category_segment_discount_array'][$key['brand_id']]) && isset($key['category_segment_discount_array'][$key['brand_id']][$category_segment->id])) ? $key['category_segment_discount_array'][$key['brand_id']][$category_segment->id] : null;
                                        @endphp

                                        @if($category_segment_discount)
                                            <input type="text" class="form-control" value="{{ $category_segment_discount->amount }}" onchange="store_amount({{$key['brand_id'] }}, {{ $category_segment->id }})"></th>
                                        @else
                                            <input type="text" class="form-control" value="" onchange="store_amount({{ $key['brand_id']}}, {{ $category_segment->id }})"></th>
                                        @endif
                                    


                                    </td>
                                @endforeach 
                               
                               <td>{{ $key['iva'] }}</td>
                               <td>{{ $key['net_price'] }}</td>
                               <td>
                                   <div class="form-group">
                                       <div class="input-group">
                                           <input style="width: 75%;border-radius: 4px;" placeholder="add duty" data-ref="{{str_replace(' ', '_', $key['country_name'])}}" value="{{ str_replace('%', '', $key['add_duty']) }}" type="text" class="form-control add_duty {{str_replace(' ', '_', $key['country_name'])}}" name="add_duty">
                                           <div class="ml-2" style="float: left;">%</div>
                                       </div>
                                   </div>
                               </td>
                               <td>
                                  <div style="align-items: center">
                                      <span style="min-width:50px;">{{ $key['add_profit'] }}</span>
                                      <div class="ml-2" style="float: right;">%</div>
                                      <div style="float: right;width:50%;">
                                          <input style="padding: 6px" placeholder="add profit" data-ref="web_{{ $key['storeWebsitesID']}}" value="{{ $key['add_profit_per'] }}" type="text" class="form-control add_profit web_{{ $key['storeWebsitesID']}}" name="add_profit">
                                      </div>
                                  </div>
                               </td>
                               <td>{{ $key['final_price'] }}</td>
                               !-->
                           </tr>
                       @empty
                           <tr>
                               <td colspan="11"> NO data found </td>
                           </tr>
                       @endforelse
                       </tbody>
                   </table>

              </div>
        </div>
    </div>
</div>

<div id="history" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-body" id="history_data">
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>

<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
            50% 50% no-repeat;display:none;">
</div>
@endsection
    
@section('scripts')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
 
    var page = 1;
	$(window).scroll(function() {
	    if($(window).scrollTop() + $(window).height() >= $(document).height()) {
	        page++;
	        loadMoreData(page);
	    }
	});

    let data = $('.filter_form').serialize();
	function loadMoreData(page){ 
	  $.ajax(
	        {
	            url: '?page=' + page + '&count=' + {{$i}} + '&' + data,
	            type: "get",
	            beforeSend: function()
	            {
	                $('#loading-image').show();
	            }
	        })
	        .done(function(data)
	        {
                $('#loading-image').hide();
	            if(data.html == " "){
	                $('.ajax-load').html("No more records found");
	                return;
	            }
	            $('.ajax-load').hide();
	            $("tbody").append(data.html);
	        })
	        .fail(function(jqXHR, ajaxOptions, thrownError)
	        {
	              alert('server not responding...');
	        });
	}

    // $(".select-multiple").multiselect();
    $(".select-multiple2").select2();
    
    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            // if ($(this).data('switch') == 0) {
            //   $(this).text($(this).data('details'));
            //   $(this).data('switch', 1);
            // } else {
            //   $(this).text($(this).data('subject'));
            //   $(this).data('switch', 0);
            // }
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
    // $(document).ready( function () {
    //     $('#product-price').DataTable({
    //         "paging":   false,
    //         "ordering": true,
    //         "info":     false
    //     });
    // } );

    $(document).on('keyup', '.seg_discount', function () {
        if (event.keyCode != 13) {
            return;
        }
        let seg_discount = $(this).val();
        let ref_name = $(this).data('ref');
        let rows = $('.'+ref_name).closest('tr');
        let product_array = [];
        for(let i=0; i< rows.length; i++){
            product_array[i] = {
                'row_id' : $(rows[i]).attr('data-id'),
                'storewebsitesid' : $(rows[i]).attr('data-storewebsitesid'),
                'product_id' : $(rows[i]).find('.product_id').text(),
                'country_code' : $(rows[i]).attr('data-country_code'),
                'add_duty' : $(rows[i]).find('.add_duty').val().replace('%', ''),
            };
        }
        $.ajax({
            url: "{{route('product.pricing.update.segment')}}",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
                product_array: JSON.stringify(product_array),
                seg_discount: seg_discount,
                row_id: $(this).closest('tr').attr('data-id'),
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function(response) { 
            $("#loading-image").hide();
            response.data.forEach(function(item, index) {
                let row = $(`.tr_${item.row_id}`);
                $(row).find('td:nth-child(8) span').html(item.seg_discount);
                $(row).find('.seg_discount').val(seg_discount);
                $(row).find('td:nth-child(13)').html(item.price);
            }); 
            toastr["success"]("segment discount updated successfully!", "Message");
        });

    });

    $(document).on('keyup', '.add_duty', function () {
        if (event.keyCode != 13) {
            return;
        }
        let add_duty = $(this).val();
        let ref_name = $(this).data('ref');
        let rows = $('.'+ref_name).closest('tr');
        let product_array = [];
        for(let i=0; i< rows.length; i++){
            product_array[i] = {
                'row_id' : $(rows[i]).attr('data-id'),
                'storewebsitesid' : $(rows[i]).attr('data-storewebsitesid'),
                'add_duty' : $(this).closest('tr').find('.add_duty').val().replace('%', ''),
                'product_id' : $(rows[i]).find('.product_id').text(),
                'country_code' : $(rows[i]).attr('data-country_code'), 
                'seg_discount' : $(rows[i]).find('.seg_discount').val().replace('%', ''),
            };
        }
        $.ajax({
            url: "{{route('product.pricing.update.add_duty')}}",
            type: 'post', 
            data: {
                _token: '{{csrf_token()}}',
                product_array: JSON.stringify(product_array),
                add_duty: add_duty,
                row_id: $(this).closest('tr').attr('data-id'),
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function(response) { 
            $("#loading-image").hide();
            response.data.forEach(function(item, index) {
                let row = $(`.tr_${item.row_id}`);
                $(row).find('.add_duty').val(add_duty);
                $(row).find('td:nth-child(13)').html(item.price);
            }); 
            toastr["success"]("duty updated successfully!", "Message");
        });

    }); 

    $(document).on('keyup', '.add_profit', function () {
        if (event.keyCode != 13) {
            return;
        }
        let add_profit = $(this).val().replace('%', '');
        let ref_name = $(this).data('ref');
        let rows = $('.'+ref_name).closest('tr');
        let product_array = [];
        for(let i=0; i< rows.length; i++){
            product_array[i] = {
                'row_id' : $(rows[i]).attr('data-id'),
                'storewebsitesid' : $(rows[i]).attr('data-storewebsitesid'),
                'product_id' : $(rows[i]).closest('tr').find('.product_id').text(),
                'add_duty' : $(rows[i]).closest('tr').find('.add_duty').val().replace('%', ''),
                'product_id' : $(rows[i]).find('.product_id').text(),
                'add_profit' : $(rows[i]).closest('tr').find('.add_profit').val().replace('%', ''),
                'country_code' : $(rows[i]).attr('data-country_code'), 
            };
        }

        $.ajax({
            url: "{{route('product.pricing.update.add_profit')}}",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
                product_array: JSON.stringify(product_array),
                add_profit: add_profit,
                row_id: $(this).closest('tr').attr('data-id'),
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            if(response.status == false){
                toastr["error"](response.message + " is not exist!", "Message");
            }else{
                response.data.forEach(function(item, index) {
                    if(item.status){
                        let row = $(`.tr_${item.row_id}`); 
                        $(row).find('td:nth-child(12) span').html(item.add_profit);
                        $(row).find('.add_profit').val(add_profit);
                        $(row).find('td:nth-child(13)').html(item.price);
                    }
                }); 
                toastr["success"]("profit updated successfully!", "Message");
            }
        });

    }); 



    function approve()
    {
            str='';
            $(".checkboxClass:checked").each(function(){
               
                if (str=='')
                   str=$(this).val();
                else
                   str= str + "," + $(this).val();  
            });
            if (str=='')
              alert('First Select Product')
            else
            {
                    $.ajax({
                        method : "POST",
                        url: "{{url('/store-website-product-prices/approve')}}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: str,
                         },
                        dataType: "json",
                        beforeSend : function(){
                            $("#loading-image").show();
                        },
                        success: function (response) {
                            $("#loading-image").hide();
                            if(response.code == 200) {
                                toastr["success"]("approved successfully", "Message")
                                location.reload();
                            }else{
                                toastr["error"](response.message, "Message");
                            }
                        }
                    });
            }
            
        
    }

    $("#checkAll").click(function(){
         $('input:checkbox').not(this).prop('checked', this.checked);
    });

    function openhistory(id)
       {
           src = "{{ url('store-website-product-prices/history') }}";
            reset = '';
            $.ajax({
                url: src,
                data: {
                    id : id,
                },
                beforeSend: function() {
                       
                },
            
            }).done(function (data) {
                 
                $('#history_data').html(data);
                $("#history").modal("show"); 
                
            })
       }

    $(document).ready(function($) {
        // Now you can use $ safely within this block
        $("#tag-input").autocomplete({
            source: function(request, response) {
                // Send an AJAX request to the server-side script
                $.ajax({
                    url: '{{ route('store-website-product-skus') }}',
                    dataType: 'json',
                    data: {
                        term: request.term // Pass user input as 'term' parameter
                    },
                    success: function(data) {
                        response(data); // The server returns filtered suggestions as JSON
                    }
                });
            },
            minLength: 1, // Minimum characters before showing suggestions
            select: function(event, ui) {
                // Handle the selection if needed
            }
        });
    })
</script>

@endsection
