@extends('layouts.app')

@section('styles')
<style>
.btn {
    padding: 6px 6px;
}
.small-image{max-width: 100%;max-height: 100px;}
.align-right{
    text-align:right;
}
.brand-header-row .select2-container--default{
    width: auto !important;
}
    .flex{
        display: flex;
    }
.brand-header-row .select2-container .select2-selection--single{
    height: 34px !important;
}
.brand-header-row .btn-secondary{
        color: #757575;
        border: 1px solid #ccc;
        background: #fff;
        padding: 6px 10px;
    }
.brand-list .select2-container--default .select2-selection--multiple .select2-selection__rendered{
    overflow: auto;
}
.brand-list .form-group{
    margin-bottom: 0 !important;
}
    .form-control1{
    border: 1px solid #ccc;
    height: 30px;
    max-width: -webkit-fill-available;
    border-radius: 4px;
}
</style>
@endsection

@section('content')
@php
$query = http_build_query(Request::except('page'));
$query = url()->current() . (($query == '') ? $query . '?page=' : '?' . $query . '&page=');
@endphp
<div class="form-group position-fixed hidden-xs hidden-sm" style="top: 50px; left: 20px;">
    Goto :
    <select onchange="location.href = this.value;" class="form-control" id="page-goto">
        @for($i = 1 ; $i <= $brands->lastPage() ; $i++ )
            <option value="{{ $query.$i }}" {{ ($i == $brands->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
            @endfor
    </select>
</div>
<div class="row brand-header-row m-0" >
<div class="col-md-12 pl-0 pr-0">
    <h2 class="page-heading">Brand List (<span>{{ $brands->total() }}</span>)

        <div class="col-lg-4 margin-tb align-right pull-right">

            <div class="pull-left">
            </div>
            <div>
                <a class="btn btn-secondary" data-toggle="collapse" href="#inProgressFilterCount" href="javascript:;">Number of brands per site</a>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createBrandModal">+</button>
                <a class="btn btn-secondary fetch-new" href="#">Fetch New Brands</a>
            </div>
        </div>
    </h2>
</div>
    <div class="col-12 flex pl-3 pr-3">
        <div class="form-inline">
            <div class="form-group">
                <input type="number" id="product_price" step="0.01" class="form-control" placeholder="Product price">
            </div>

            <div class="form-group ml-3">
                <select class="form-control select-multiple" id="brand" data-placeholder="Brands...">
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" data-brand="{{ $brand }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="button" id="calculatePriceButton" class="btn btn-secondary ml-3">Calculate</button>
        </div>
        <div class="form-inline pl-3">
            <form style="display: flex">
                <div class="form-group">
                    <input type="text" value="{{ request('keyword') }}" name="keyword" id="search_text" class="form-control" placeholder="Enter keyword for search">
                </div>
                <button type="submit" class="btn btn-secondary ml-3">Search</button>
            </form>
        </div>

        <div class="form-inline pl-3">
           
           <div class="form-group">
             
            {{ 
                html()->select('brand_segment_1', 
                    ["" => "--Select segment"] + \App\Brand::BRAND_SEGMENT)
                    ->class('form-control')
                    ->attribute('id', 'brand_segment_1')
                    ->attribute('data-brand-id', '')
                    ->attribute('data-placeholder', '-- Select Brand --')
            }}
            
        </div>
         
      </div>

        <div class="form-inline pl-3">
           
                <div class="form-group">
                <select class="globalSelect2" id="category_segments_1">
                @foreach($category_segments as $category_segment)
                    <option value="{{ $category_segment->id }}">{{ $category_segment->name }}</option>
                @endforeach
                </select>  </div>
              
        </div>

      

        <div class="form-inline pl-3">
            <form>
                <div class="form-group">
                   
               
                    <input type="text" value="{{ request('default_value') }}" name="default_value" id="default_value_segment" class="form-control" placeholder="Setup Default value for segment">
                </div>
                <button type="submit" class="btn btn-secondary btn-assign-default-val ml-3">Assign</button>
            </form>
        </div>

        <div id="result-container">

        </div>
    </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif

<div class="row m-0 pl-3 pr-3">


@php
    $bList = \App\Brand::pluck('name','id')->toArray();
@endphp
<div class="infinite-scroll" >
    {!! $brands->links() !!}
    <div class="table-responsive mt-3">
        <table class="table table-bordered brand-list" style="table-layout: fixed;font-size: 13px">
            <tr>
                <th width="2%"><input id="checkAll" type="checkbox" ></th>
                <th width="4%">ID</th>
                <th width="5%">Name</th>
                <th width="5%">Image</th>
                <th width="8%">Similar Brands</th>
                <th width="7%">Merge Brands</th>
                <th width="4%">Magento ID</th>
                <th width="4%">Euro to Inr</th>
                <th width="4%" style="word-break:break-all">Deduction%</th>
                <th width="6%">Segment</th>
                @foreach($category_segments as $category_segment)
                    <th width="4%">{{ $category_segment->name }}</th>
                @endforeach
                <th width="9%">Selling on</th>
                <th width="7%">Priority</th>
                <th width="7%">Next Step</th>
                <th width="5%">Status</th>
                <th width="8%">Action</th>
            </tr>
            @foreach ($brands as $key => $brand)
            <tr>
            <td><input  type="checkbox" class="checkboxClass" name="selectcheck" value='{{ $brand->id }}'></td>    
            <td>{{ $brand->id }}</td>
                <td>{{ $brand->name }}</td>
                <td>
                    @if($brand->brand_image)
                        <img src="{{ $brand->brand_image }}" class="small-image">
                    @endif
                </td>
                <td>
                    @php
                        $similar_brands = explode(',', $brand->references);
                        $similar_brands = array_filter($similar_brands, function($element) {
                            return trim($element) !== "";
                        });
                    @endphp
                    @foreach($similar_brands as $similar_brand)
                        <p><span>{!! $similar_brand !!}</span> <a href="#"><span data-id="{{ $brand->id }}" class="fa fa-close unmerge-brand"></span></a></p>
                    @endforeach
                </td>
                <td>
                    <div class="form-select">
                      

 <select class="form-control globalSelect2 merge-brand merge_brand_close_e" data-brand-id = {{ $brand->id }} data-ajax="{{ route('select2.brands',['sort'=>true])  }}"  
     name="merge_brand" data-placeholder="-- Select Brand --">
     <option value="">Select a Brand</option>
 </select>


                    </div>
                </td>
                <td class="remote-td">{{ $brand->magento_id}}</td>
                <td>{{ $brand->euro_to_inr }}</td>
                <td>{{ $brand->deduction_percentage }}</td>
                <td>
                    <div class="form-select">
                        {{ html()->select("brand_segment", ["" => "--Select segment"] + \App\Brand::BRAND_SEGMENT, $brand->brand_segment)->class("form-control change-brand-segment globalSelect2 merge_brand_close_e")->data('brand-id', $brand->id)->data('placeholder', '-- Select Brand --') }}
                    </div>
                </td>
                @foreach($category_segments as $category_segment)
                @php 
                    $matched_discount = null;
                    foreach ($category_segment_discounts as $discount) {
                        if ($discount->category_segment_id == $category_segment->id && $discount->brand_id == $brand->id) {
                            $matched_discount = $discount;
                            break;
                        }
                    }
                @endphp
                    <td>
                        @if($matched_discount)
                            <input  type="text" class="form-control1 1" value="{{ $matched_discount->amount }}" onchange="store_amount({{ $brand->id }}, {{ $category_segment->id }})"></th>
                        @else
                            <input  type="text" class="form-control1 1" value="" onchange="store_amount({{ $brand->id }}, {{ $category_segment->id }})"></th>
                        @endif
                        {{-- <input type="text" class="form-control" value="{{ $brand->pivot->amount }}" onchange="store_amount({{ $brand->id }}, {{ $category_segment->id }})"> --}} {{-- Purpose : Comment code -  DEVTASK-4410 --}}
{{-- Purpose : Comment code -  DEVTASK-4410 --}}
                    </td>
                @endforeach 
                <td class="show_brand" data-id="{{$brand->id}}" style="max-width: 150px;cursor: pointer; ">
                    <span style="word-wrap: break-word;" >{{ !empty($brand->selling_on) && !empty(explode(",", $brand->selling_on)[0]) ? (strlen($storeWebsite[explode(",", $brand->selling_on)[0]]) > 10 ? substr($storeWebsite[explode(",", $brand->selling_on)[0]], 0, 10) .' ...' : $storeWebsite[explode(",", $brand->selling_on)[0]]) : '' }}</span>
                    @if(!empty(explode(",", $brand->selling_on)[0]))

                    @endif
                    <span style="word-wrap: break-word;" >{{ !empty($brand->selling_on) && !empty(explode(",", $brand->selling_on)[1]) ? (strlen($storeWebsite[explode(",", $brand->selling_on)[1]]) > 10 ? substr($storeWebsite[explode(",", $brand->selling_on)[1]], 0, 10) .' ...' : $storeWebsite[explode(",", $brand->selling_on)[1]]) : '' }}</span>
                    @if(!empty(explode(",", $brand->selling_on)[1]))
                    @endif  
                    @if(explode(",", $brand->selling_on)[0] == '')
                    <a href="javascript:;" data-message="do you have fendi bags" class="btn btn-xs btn-image add-chat-phrases" title="Add phrases"><img src="/images/add.png" alt="" style="cursor: nwse-resize; width: 0px;"></a>
                    @endif
                </td>
               <td>
                <div class="form-group">
                    @php 
                    $priority_array=[null=>'Priority',1=>'Critical',2=>'High',3=>'Medium',4=>'Low'];
                    @endphp

                      {{ html()->select('priority', $priority_array, $brand->priority ?? '')->class('form-control input-sm mb-3 priority')->data('id', $brand->id) }}
                      
                    </div>       
                </td>
                <td>
                    <div class="form-group">
                        {{ html()->select('next_step', [null => "--Select--"] + \App\Helpers\StatusHelper::getStatus(), $brand->next_step ?? '')->class('form-control input-sm mb-3 brand-next-step')->data('id', $brand->id) }}
                    </div>       
                </td>
                <td> @if($brand->status==1)
               approved
             @else
               pending 
             @endif     
        </td>
                <td>
                    <a style="padding:1px;" class="btn btn-image" href="{{ route('brand.edit',$brand->id) }}"><img src="/images/edit.png" /></a>
                    {{ html()->form('DELETE', route('brand.destroy', [$brand->id]))->style('display:inline')->open() }}
                    <button style="padding:  1px;" type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {{ html()->form()->close() }}
                    <a style="padding: 1px;" class="btn btn-image btn-attach-website" href="javascript:;"><i class="fa fa-globe"></i></a>
                    <a style="padding:  1px;" class="btn btn-image btn-create-remote" data-id="{{ $brand->id }}" href="javascript:;"><i class="fa fa-check-circle-o"></i></a>
                    <a style="padding: 1px;" class="btn btn-image btn-activity" data-href="{{ route('brand.activities',$brand->id) }}" href="javascript:;"><i class="fa fa-info"></i></a>
                </td>
            </tr>
            <div class="modal fade brand_modal_{{$brand->id}}" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Attached Brands</h4>
                            <button type="button" class="close close_modal" data-dismiss="modal" data-id="{{$brand->id}}">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group"> 
                                                <select name="attach_brands" class="form-control input-attach-brands select-multiple select2 attach_brands" multiple style="width:100%" data-placeholder="&nbsp-- Select Website(s) --" data-brand-id="{{$brand->id}}">
                                                    <option value="">&nbsp-- Select Website(s) --</option>            
                                                    @foreach($storeWebsite as $key => $w)
                                                    <option {{!empty($brand->selling_on) && in_array($key, explode(",", $brand->selling_on)) ? 'selected' : ''}} value="{{$key}}">{{$w}}</option> 
                                                    @endforeach
                                                </select> 
                                            </div>
                                        </div>  
                                    </div>  
                                </div>  
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </table>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
</div>

<div id="ActivitiesModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Brand Activities</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<div id="upload-barnds-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title">Upload Brand Logos</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form id="upload-barnd-logos">
          <div class="modal-body">
              @csrf
              <div class="row m-0">
                <div class="col-md-10 col-md-offset-1">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Logos</label>
                                <input type="file" name="files[]" id="filecount" multiple="multiple">
                            </div>
                        </div>  
                    </div>  
                </div>  
              </div>  
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-default">Save</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
       </form>
    </div>
  </div>
</div>
<div id="createBrandModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('brand.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Brand</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Name</strong><span class="text-danger">*</span>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="name" placeholder="name" value="" required/>
                                @if ($errors->has('name'))
                                    <div class="alert alert-danger">{{$errors->first('name')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Euro To Inr</strong>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="euro_to_inr" placeholder="euro_to_inr" value=""/>
                                @if ($errors->has('euro_to_inr'))
                                    <div class="alert alert-danger">{{$errors->first('euro_to_inr')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Deduction %</strong>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" name="deduction_percentage" placeholder="deduction_percentage" value=""/>
                                @if ($errors->has('deduction_percentage'))
                                    <div class="alert alert-danger">{{$errors->first('deduction_percentage')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Sales Discount %</strong>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" name="sales_discount" placeholder="sales discount" value=""/>
                                <small class="form-text text-muted">
                                    If the product is discounted at the supplier, regardless of the percentage, this discount will be applied to the special price (original price - brand discount)
                                </small>
                                @if ($errors->has('sales_discount'))
                                    <div class="alert alert-danger">{{$errors->first('sales_discount')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Apply B2B discount above</strong>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" name="apply_b2b_discount_above" placeholder="e.g. 40" value=""/>
                                <small class="form-text text-muted">
                                    Above this percentage of discount at the supplier, the below discount will be applied
                                </small>
                                @if ($errors->has('apply_b2b_discount_above'))
                                    <div class="alert alert-danger">{{$errors->first('apply_b2b_discount_above')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>B2B Sales Discount %</strong>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" name="b2b_sales_discount" placeholder="B2B sales discount" value=""/>
                                <small class="form-text text-muted">
                                    If a B2B discount is higher than the above percentage, the sales_discount will be applied to the special price (original price - brand discount)
                                </small>
                                @if ($errors->has('b2b_sales_discount'))
                                    <div class="alert alert-danger">{{$errors->first('b2b_sales_discount')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Magento Id</strong>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="magento_id" placeholder="Magento ID" value=""/>
                                @if ($errors->has('magento_id'))
                                    <div class="alert alert-danger">{{$errors->first('magento_id')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Segment</strong>
                            </div>
                            <div class="col-md-8">
                                <select name="brand_segment" class="form-control">
                                    <option value=""></option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                                @if ($errors->has('brand_segment'))
                                    <div class="alert alert-danger">{{$errors->first('brand_segment')}}</div>
                                @endif
                            </div>
                        </div>
                            @foreach($category_segments as $category_segment)
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <strong>{{ $category_segment->name }}</strong>
                                </div>
                                <div class="col-md-8">  
                                    <input type="text" class="form-control" name="amount" placeholder="Amount" value=""/>
                                    
                                </div>
                            </div>
                            @endforeach

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <strong>Strip last # characters from SKU</strong>
                                </div>
                                <div class="col-md-8"> 
                                <input type="text" class="form-control" name="sku_strip_last" placeholder="Strip last # characters from SKU" value=""/>
                                @if ($errors->has('sku_strip_last'))
                                    <div class="alert alert-danger">{{$errors->first('sku_strip_last')}}</div>
                                @endif
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">    
                                    <strong>Add to SKU for brand site</strong>
                                </div>
                                <div class="col-md-8"> 
                                    <input type="text" class="form-control" name="sku_add" placeholder="Add to SKU for brand site" value=""/>
                                    @if ($errors->has('sku_add'))
                                        <div class="alert alert-danger">{{$errors->first('sku_add')}}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">    
                                    <strong>References</strong>
                                </div>
                                <div class="col-md-8">
                                <input type="text" class="form-control" name="references" placeholder="Add/update references in comma seperate values" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-secondary">Add</button>
                        </div>
                    </div>
                    
                </form>
            </div>

        </div>
    </div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery-ui.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.jscroll.min.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-filestyle.min.js') }} "></script>
<script type="text/javascript">

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });






    function store_amount(brand_id, category_segment_id) {
        var amount = $(this.event.target).val();
        $.ajax({
            url: '{{ route('brand.store_category_segment_discount') }}',
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                'brand_id': brand_id,
                'category_segment_id': category_segment_id,
                'amount': amount
            }
        });
    }

    jQuery(document).ready(function(){
        jQuery(".btn-activity").on("click",function(e){
            e.preventDefault();
            _this = jQuery(this);
            $.ajax({
                url: jQuery(_this).data('href'),
                method: 'GET',
                success: function(response) {
                    jQuery("#ActivitiesModal .modal-body").html(response);
                    jQuery("#ActivitiesModal").modal("show");
                },
                error: function(response){
                    toastr['error'](response.responseJSON.message, 'error');
                }
            });
        })
    });

    $(document).on('click', '.unmerge-brand', function(e) {
        e.preventDefault();
        var $this = $(this);
        if(confirm("Do you want to unmerge this brand?")) {
            var brand_name = $(this).parents().eq(1).find('span').first().text();
            var from_brand_id = $(this).data('id'); 
            $.ajax({
                url: '{{ route('brand.unmerge-brand') }}',
                method: 'POST',
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                    'brand_name': brand_name,
                    'from_brand_id': from_brand_id
                },
                success: function(response) {
                    toastr['success']((typeof response.message != "undefined") ? response.message : "Brand unmerged successfully", "success");
                    $this.closest("p").remove();
                    //location.reload();
                },
                error: function(response){
                    toastr['error'](response.responseJSON.message, 'error');
                } 
            });
        }
    });
    $(".select-multiple").select2();
    $(".select-multiple4").select2({
        tags: true
    });
    
    $('#calculatePriceButton').on('click', function() {
        var price = $('#product_price').val();
        var brand = $('#brand :selected').data('brand');
        var price_inr = Math.round(Math.round(price * brand.euro_to_inr) / 1000) * 1000;
        var price_special = Math.round(Math.round(price_inr - (price_inr * brand.deduction_percentage) / 100) / 1000) * 1000;

        var result = '<strong>INR Price: </strong>' + price_inr + '<br><strong>Special Price: </strong>' + price_special;

        $('#result-container').html(result);
    });

    $('ul.pagination').hide();
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
            padding: 2500,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                setTimeout(function(){
                    $('ul.pagination').first().remove();
                }, 2000);
                $(".select-multiple").select2();
                initialize_select2();
            }
        });
    });

    $(document).on("change", ".input-attach-brands", function(e) {
        e.preventDefault();
        var brand_id = $(this).data("brand-id"),
            website = $(this).val();
        $.ajax({
            type: 'POST',
            url: "{{url('/brand/attach-website')}}",
            data: {
                _token: "{{ csrf_token() }}",
                website: website,
                brand_id: brand_id
            }
        }).done(function(response) {
            if (response.code == 200) {
                toastr['success']('Website Attached successfully', 'success');
            }
        }).fail(function(response) {
            console.log("Could not update successfully");
        });
    });

    $(document).on("change", ".input-similar-brands", function(e) {
        e.preventDefault();
        var brand_id = $(this).data("reference-id");
        var reference = $(this).val();
        $.ajax({
            type: 'POST',
            url: "{{url('/brand/update-reference')}}",
            data: {
                _token: "{{ csrf_token() }}",
                reference: reference,
                brand_id: brand_id
            }
        }).done(function(response) {
            if (response.code == 200) {
                toastr['success']('Reference updated successfully', 'success');
            }
        }).fail(function(response) {
            console.log("Could not update successfully");
        });
    });

    $(document).on("change",".merge-brand",function(e){
// console.log($(this).val(),'this is chnge')
$('.select2-selection__clear').remove()
        if($(this).val()){
            
            var ready = confirm("Are you sure want to merge brand ?");
                if (ready) {
                    var brand_id = $(this).data("brand-id");
                    var reference = $(this).val();
                    $.ajax({
                        type: 'POST',
                        url: "/brand/merge-brand",
                        data: {
                            _token: "{{ csrf_token() }}",
                            from_brand: brand_id,
                            to_brand: reference
                        }
                    }).done(function(response) {
                        if (response.code == 200) {
                            toastr['success']('Brand merged successfully', 'success');
                            //location.reload();
                        }
                    }).fail(function(response) {
                        console.log("Could not update successfully");
                    });
                }else{
                    return false;
                }

        }
    });
    

    $(document).on("change", ".change-brand-segment", function(e) {
        e.preventDefault();
        var brand_id = $(this).data("brand-id"),
            segment = $(this).val();
        $.ajax({
            type: 'POST',
            url: "{{url('/brand/change-segment')}}",
            data: {
                _token: "{{ csrf_token() }}",
                segment: segment,
                brand_id: brand_id
            }
        }).done(function(response) {
            if (response.code == 200) {
                toastr['success']('Brand segment change successfully', 'success');
            }
        }).fail(function(response) {
            console.log("Could not update successfully");
        });
    });

    $(document).on("change", ".brand-next-step", function(e) {
        e.preventDefault();
        var brand_id = $(this).data("id"),
            next_step = $(this).val();
        $.ajax({
            type: 'POST',
            url: "{{url('/brand/next-step')}}",
            data: {
                _token: "{{ csrf_token() }}",
                next_step: next_step,
                brand_id: brand_id
            }
        }).done(function(response) {
            if (response.code == 200) {
                toastr['success']('Next change successfully', 'success');
            }
        }).fail(function(response) {
            console.log("Could not update successfully");
        });
    });

    



    $(document).on("click", ".btn-create-remote", function(e) {
        e.preventDefault();
        var $this = $(this);
        var ready = confirm("Are you sure want to create remote id ?");
        if (ready) {
            var brandId = $(this).data("id");
            $.ajax({
                type: 'GET',
                url: "{{url('/brand/')}}" + brandId + "/create-remote-id",
            }).done(function(response) {
                if (response.code == 200) {
                    $this.closest("tr").find(".remote-td").html(response.data.magento_id);
                    toastr['success'](response.message, 'success');
                } else if (response.code == 500) {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function(response) {
                console.log("Could not update successfully");
            });
        }
    });

    $(document).on('change', '.priority', function () {
        var $this = $(this);
        var brand_id = $this.data("id");
        var priority = $this.val();
        $.ajax({
            type: "PUT",
            url: "/brand/priority/"+brand_id,
            data: {
                _token: "{{ csrf_token() }}",
                supplier_id : brand_id,
                priority: priority
            }
        }).done(function (response) {
             toastr['success'](response.message, 'success');
        }).fail(function (response) {
           toastr['error'](response.message, 'error');
        });
    });
    $(document).on("submit","#upload-barnd-logos",function(e) {
        e.preventDefault();
        var form = $(this);
        var postData = new FormData(form[0]);
        $.ajax({
            method : "POST",
            url: "/brand/fetch-new/",
            data: postData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr["success"]("Logos updated!", "Message")
                    $("#upload-barnd-logos").modal("hide");
                }else{
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });
    // $(document).on('click','.fetch-new', function(event){
    //     event.preventDefault();
    //     $.ajax({
    //         type: "GET",
    //         url: "/brand/fetch-new/",
    //         data: {
    //             _token: "{{ csrf_token() }}",
    //         }
    //     }).done(function (response) {
    //          toastr['success'](response.message, 'success');
    //     }).fail(function (response) {
    //        toastr['error'](response.message, 'error');
    //     });
    // });
    $(document).on('click','.fetch-new', function(event){
        event.preventDefault();
        $.ajax({
            type: "GET",
            url: "/brand/fetch-new/",
            data: {
                _token: "{{ csrf_token() }}",
            }
        }).done(function (response) {
             toastr['success'](response.message, 'success');
        }).fail(function (response) {
           toastr['error'](response.message, 'error');
        });
    });

    $(document).on('click','.show_brand', function(event){
        $(".brand_modal_"+$(this).attr('data-id')).modal("show"); 
    }); 

    $('.modal').on('shown.bs.modal', function (e) {
        $(".attach_brands").select2({
            placeholder: " abc xyz"
        });
    })

    $(document).on("click",".btn-assign-default-val",function(e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
            method : "POST",
            url: "{{url('/brand/assign-default-value')}}",
            data: {
                _token: "{{ csrf_token() }}",
                value: $("#default_value_segment").val(),
                category_segments: $("#category_segments_1").val(),
                brand_segment: $("#brand_segment_1").val()

            },
            dataType: "json",
            beforeSend : function(){
                $("#loading-image").show();
            },
            success: function (response) {
                $("#loading-image").hide();
                if(response.code == 200) {
                    toastr["success"]("Default value assigned!", "Message")
                    location.reload();
                }else{
                    toastr["error"](response.message, "Message");
                }
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
              alert('First Select Brand')
            else
            {
                    $.ajax({
                        method : "POST",
                        url: "{{url('/brand/approve')}}",
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
</script>
@endsection
