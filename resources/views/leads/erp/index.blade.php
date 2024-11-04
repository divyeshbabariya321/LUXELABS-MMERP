@extends('layouts.app')

@section('title', 'Erp Leads')

@section("styles")

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
<style>
  .checkbox {
    margin-left: -20px;
    margin-top: 0px !important;
    margin-bottom: 0px !important;
  }

  .erp-leads {
    font-size: 14px;
  }
</style>

@section('content')

<div class="row">
  <div class="col-lg-12 margin-tb">
    <h2 class="page-heading">Erp Leads 
        <div style="float: right;">
            <a class="btn btn-secondary btn-sm editor_create" href="javascript:;"><i class="fa fa-plus"></i></a>
        </div>
    </h2>
  </div>
</div>
<div class="container-fluid">
  <div class="row">
    <?php /*
    <div class="col-lg-12 margin-tb">
      <form id="search" method="GET" class="form-inline">
          <input name="term" type="text" class="form-control"
                value="{{request()->get('term')}}"
                placeholder="Search" id="customer-search">

          <div class="form-group ml-3">
              <input placeholder="Shoe Size" type="text" name="shoe_size" value="{{request()->get('shoe_size')}}" class="form-control-sm form-control">
          </div>
          <div class="form-group ml-3">
              <input placeholder="Clothing Size" type="text" name="clothing_size" value="{{request()->get('clothing_size')}}" class="form-control-sm form-control">
          </div>
          <div class="form-group ml-3">
              <select class="form-control" name="shoe_size_group">
                  <option value="">Select</option>
                  <?php foreach ($shoe_size_group as $shoe_size => $customerCount) {
                      echo '<option value="'.$shoe_size.'" '.($shoe_size == request()->get('shoe_size_group') ? 'selected' : '').'>('.$shoe_size.' Size) '.$customerCount.' Customers</option>';
                  } ?>
              </select>
          </div>
          <div class="form-group ml-3">
              <select class="form-control" name="clothing_size_group">
                  <option value="">Select</option>
                  <?php foreach ($clothing_size_group as $clothing_size => $customerCount) {
                      echo '<option value="'.$clothing_size.'" '.($shoe_size == request()->get('shoe_size_group') ? 'selected' : '').'>('.$clothing_size.' Size) '.$customerCount.' Customers</option>';
                  } ?>
              </select>
          </div>
          <input type="hidden" name="lead_customer">
          <input type="hidden" name="lead_brand">
          <input type="hidden" name="lead_category">
          <input type="hidden" name="lead_color">
          <input type="hidden" name="lead_shoe_size">
          <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
      </form>
    </div>
    */ ?>
    @include('partials.flash_messages', ['extraDiv' => true])

    <?php $base_url = URL::to('/'); ?>
    <div class="col-md-12 cls_filter_box">
      <form action="{{ route('erp-leads.erpLeads') }}" method="GET">
        @csrf
        <div class="">
          <div class="form-group col-md-2 cls_filter_inputbox p-0 mr-3">
            <div class="w-100">
              <select class="form-control lead_status multi_lead_status" name="status_id[]" multiple="">
                <option value="">Status</option>
                @foreach($erpLeadStatus as $status)
                <option value="{{$status['id']}}">{{$status['name']}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group col-md-1 cls_filter_inputbox p-0 mr-3">
            <input type="text" class="form-control-sm cls_commu_his form-control field_search lead_customer input-size" value="{{ request('lead_customer') }}" name="lead_customer" placeholder="Customer" />
          </div>
          <div class="form-group col-md-1 cls_filter_inputbox p-0 mr-3">
            <select placeholder="Brand" class="form-control lead_brand multi_lead_status input-size" name="brand_id[]" multiple="">
              Brand
              <option value="" default>Brand</option>
              @foreach($brands as $brand_item)
              <option value="{{$brand_item['id']}}" {{ in_array($brand_item['id'], request('brand_id', [])) ? 'selected' : '' }}>{{$brand_item['name']}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-1 cls_filter_inputbox p-0 mr-3">
            <input type="text" class="form-control-sm cls_commu_his form-control input-size field_search brand_segment" value="{{ request('brand_segment') }}" name="brand_segment" placeholder="Brand Segment" />
          </div>
          <div class="form-group col-md-1 cls_filter_inputbox p-0 mr-3">
            <input type="text" class="form-control-sm cls_commu_his form-control input-size field_search lead_category" value="{{ request('lead_category') }}" name="lead_category" placeholder="Category" />
          </div>
          <div class="form-group col-md-1 cls_filter_inputbox p-0 mr-3">
            <input type="text" class="form-control-sm cls_commu_his form-control input-size field_search lead_color" value="{{ request('lead_color') }}" name="lead_color" placeholder="Color" />

          </div>
          <div class="form-group col-md-1 cls_filter_checkbox p-0 mr-3">
            <input type="text" class="field_search lead_shoe_size form-control-sm cls_commu_his form-control input-size" value="{{ request('lead_shoe_size') }}" name="lead_shoe_size" placeholder="Size" />
          </div>
          <div class="form-group col-md-1 cls_filter_checkbox p-0 mr-3">
            <select class="form-control lead_type multi_lead_type" name="lead_type[]" multiple="">
              <option value="">Status</option>
              @foreach($erpLeadTypes as $type)
              <option value="{{$type['type']}}" {{ in_array($type['type'], request('lead_type', [])) ? 'selected' : '' }}>{{$type['type']}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-1 cls_filter_checkbox m-0 pt-2">
            <button type="submit" class="btn btn-xs" id="btnFileterErpLeads"><i class="fa fa-filter"></i></button>
          </div>
        </div>
      </form>
    </div>

    <!--Add Status Modal -->
    <div class="modal fade" id="addStatusModal" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('erpLeads.status.create') }}">
            <div class="modal-header">
              <h4 class="modal-title">Add Status</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <input type="text" name="add_status" class="form-control input-blog" placeholder="Add Status" style="width: 100%" required>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Create</button>
            </div>
          </form>
        </div>

      </div>
    </div>

    <div class="col-md-12 margin-tb mb-3">
      <label class="text-secondary mr-3">
        <input type="checkbox" class="all_customer_check" style="height: auto;"> Select This Page
      </label>
      <label class="text-secondary mr-3">
        <input type="checkbox" class="all_page_check" style="height: auto;"> Select All Page
      </label>
      <a class="btn btn-secondary btn-xs create_broadcast" href="javascript:;">Create Broadcast</a>
      <button type="button" data-toggle="modal" data-target="#addStatusModal" class="btn btn-secondary btn-xs">Add Status</button>
      <button class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
      <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#eldatatablecolumnvisibilityList">Column Visiblity</button>
      <a href="javascript:;" class="btn btn-xs images_attach"><i class="fa fa-paperclip"></i></a>

      <label class="ml-5"><input type="checkbox" value="1" onclick="funEnableDisableLeads(this)" {{$statusErpLeadsSave ? 'checked' : ''}} style="height: auto;"> Enable Leads Cron to save on ERP</label>
    </div>


    <div class="col-md-12 infinite-scroll22">
        <div class="table-responsive mt-3">
            <table class="table table-bordered erp-leads" id="vendor-table">
                <thead>
                    <tr>
                        @if(!empty($dynamicColumnsToShowel))
                            @if (!in_array('Checkbox', $dynamicColumnsToShowel))
                                <th></th>
                            @endif
                            @if (!in_array('ID', $dynamicColumnsToShowel))
                                <th>ID</th>
                            @endif
                            @if (!in_array('Date', $dynamicColumnsToShowel))
                                <th>Date</th>
                            @endif
                            @if (!in_array('Status', $dynamicColumnsToShowel))
                                <th>Status</th>
                            @endif
                            @if (!in_array('Cust', $dynamicColumnsToShowel))
                                <th>Cust</th>
                            @endif
                            @if (!in_array('C Email', $dynamicColumnsToShowel))
                                <th>C Email</th>
                            @endif
                            @if (!in_array('C WApp', $dynamicColumnsToShowel))
                                <th>C WApp</th>
                            @endif
                            @if (!in_array('Store', $dynamicColumnsToShowel))
                                <th>Store</th>
                            @endif
                            @if (!in_array('Image', $dynamicColumnsToShowel))
                                <th>Image</th>
                            @endif
                            @if (!in_array('Pro ID', $dynamicColumnsToShowel))
                                <th>Pro ID</th>
                            @endif
                            @if (!in_array('Sku', $dynamicColumnsToShowel))
                                <th>Sku</th>
                            @endif
                            @if (!in_array('Pro name', $dynamicColumnsToShowel))
                                <th>Pro name</th>
                            @endif
                            @if (!in_array('Brand', $dynamicColumnsToShowel))
                                <th>Brand</th>
                            @endif
                            @if (!in_array('B Sgmt', $dynamicColumnsToShowel))
                                <th>B Sgmt</th>
                            @endif
                            @if (!in_array('Category', $dynamicColumnsToShowel))
                                <th>Category</th>
                            @endif
                            @if (!in_array('Color', $dynamicColumnsToShowel))
                                <th>Color</th>
                            @endif
                            @if (!in_array('Size', $dynamicColumnsToShowel))
                                <th>Size</th>
                            @endif
                            @if (!in_array('Type', $dynamicColumnsToShowel))
                                <th>Type</th>
                            @endif
                            @if (!in_array('Communication', $dynamicColumnsToShowel))
                                <th>Communication</th>
                            @endif
                            @if (!in_array('Action', $dynamicColumnsToShowel))
                                <th>Action</th>
                            @endif
                        @else
                            <th></th>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Cust</th>
                            <th>C Email</th>
                            <th>C WApp</th>
                            <th>Store</th>
                            <th>Image</th>
                            <th>Pro ID</th>
                            <th>Sku</th>
                            <th>Pro name</th>
                            <th>Brand</th>
                            <th>B Sgmt</th>
                            <th>Category</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th>Type</th>
                            <th>Communication</th>
                            <th>Action</th>
                        @endif
                    </tr>
                </thead>

                <tbody id="vendor-body">

                    @foreach ($sourceData as $source)
                        @php
                          $status_color = $source->erpLeadStatus ? $source->erpLeadStatus->status_color : new stdClass();
                        @endphp
                        @if(!empty($dynamicColumnsToShowel))
                            <tr style="background-color: {{$status_color->status_color ?? ""}}!important;">
                                @if (!in_array('Checkbox', $dynamicColumnsToShowel))
                                <td>
                                    <input name="customer_message[]" class="customer_message" type="checkbox" value="{{$source['customer_id']}}">
                                </td>
                                @endif

                                @if (!in_array('ID', $dynamicColumnsToShowel))
                                <td>
                                    {{$source['id']}}
                                </td>
                                @endif

                                @if (!in_array('Date', $dynamicColumnsToShowel))
                                <td>
                                    {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $source['created_at'])->format('d-m-y')}}
                                </td>
                                @endif

                                @if (!in_array('Status', $dynamicColumnsToShowel))
                                <td>
                                    <select class="form-control update-Erp-Status" name="ErpStatus" data-id="{{$source['id']}}">
                                        @foreach($erpLeadStatus as $erp_status)
                                        <option value="{{ $erp_status['id'] }}" {{ $source['status_name'] == $erp_status['name'] ? 'selected' : '' }}>{{ $erp_status['name'] }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                @endif

                                @if (!in_array('Cust', $dynamicColumnsToShowel))
                                <td class="expand-row-msg" data-name="customer_name" data-id="{{$source['id']}}">
                                    <a href="/customer/{{$source['customer_id']}}" target="_blank">
                                        <span class="show-short-customer_name-{{$source['id']}}">{{ Str::limit($source['customer_name'], 5, '..')}}</span>
                                        <span style="word-break:break-all;" class="show-full-customer_name-{{$source['id']}} hidden">{{$source['customer_name']}}</span>
                                    </a>
                                </td>
                                @endif
                                
                                @if (!in_array('C Email', $dynamicColumnsToShowel))
                                <td class="expand-row-msg" data-name="customer_email" data-id="{{$source['id']}}">
                                    <span class="show-short-customer_email-{{$source['id']}}">{{ Str::limit($source['customer_email'], 7, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-customer_email-{{$source['id']}} hidden">{{$source['customer_email']}}</span>
                                </td>
                                @endif

                                @if (!in_array('C WApp', $dynamicColumnsToShowel))
                                <td class="expand-row-msg" data-name="customer_whatsapp_number" data-id="{{$source['id']}}">
                                    <span class="show-short-customer_whatsapp_number-{{$source['id']}}">{{ Str::limit($source['customer_whatsapp_number'], 5, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-customer_whatsapp_number-{{$source['id']}} hidden">{{$source['customer_whatsapp_number']}}</span>
                                </td>
                                @endif

                                @if (!in_array('Store', $dynamicColumnsToShowel))
                                <td class="expand-row-msg" data-name="website" data-id="{{$source['id']}}">
                                    <span class="show-short-website-{{$source['id']}}">{{ Str::limit($source['website'], 7, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-website-{{$source['id']}} hidden">{{$source['website']}}</span>
                                </td>
                                @endif

                                @if (!in_array('Image', $dynamicColumnsToShowel))
                                <td>
                                    @if($source['media_url']) <img class="lazy" alt="" src="{{$source['media_url']}}" style="width:50px;"> @else {{''}} @endif
                                </td>
                                @endif

                                @if (!in_array('Pro ID', $dynamicColumnsToShowel))
                                <td>
                                    {{$source['product_id']}}
                                </td>
                                @endif

                                @if (!in_array('Sku', $dynamicColumnsToShowel))
                                <td class="expand-row-msg" data-name="product_sku" data-id="{{$source['id']}}">
                                    <span class="show-short-product_sku-{{$source['id']}}">{{ Str::limit($source['product_sku'], 8, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-product_sku-{{$source['id']}} hidden">{{$source['product_sku']}}</span>
                                </td>
                                @endif

                                @if (!in_array('Pro name', $dynamicColumnsToShowel))
                                <td class="expand-row-msg" data-name="product_name" data-id="{{$source['id']}}">
                                    <span class="show-short-product_name-{{$source['id']}}">{{ Str::limit($source['product_name'], 8, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-product_name-{{$source['id']}} hidden">{{$source['product_name']}}</span>
                                </td>
                                @endif

                                @if (!in_array('Brand', $dynamicColumnsToShowel))
                                <td class="expand-row-msg" data-name="brand_name" data-id="{{$source['id']}}">
                                    <span class="show-short-brand_name-{{$source['id']}}">{{ Str::limit($source['brand_name'], 8, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-brand_name-{{$source['id']}} hidden">{{$source['brand_name']}}</span>

                                    <a class="multi_brand_category_create text-secondary" data-id="{{$source['id']}}" data-url="{{route('manage.leads.brand')}}" href="javascript:;">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </td>
                                @endif

                                @if (!in_array('B Sgmt', $dynamicColumnsToShowel))
                                <td>
                                    {{$source['brand_segment']}}
                                </td>
                                @endif

                                @if (!in_array('Category', $dynamicColumnsToShowel))
                                <td class="expand-row-msg" data-name="cat_title" data-id="{{$source['id']}}">
                                    <?php
                                    if ($source['cat_title'] != null) { ?>
                                        <span class="show-short-cat_title-{{$source['id']}}">{{ Str::limit($source['cat_title'], 5, '..')}}</span>
                                        <span style="word-break:break-all;" class="show-full-cat_title-{{$source['id']}} hidden">{{$source['cat_title']}}</span>
                                    <?php
                                    } ?>
                                    <a class="multi_brand_category_create text-secondary" data-id="{{$source['id']}}" data-url="{{route('manage.leads.category')}}" href="javascript:;">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </td>
                                @endif

                                @if (!in_array('Color', $dynamicColumnsToShowel))
                                <td>
                                    {{$source['color']}}
                                </td>
                                @endif
                                
                                @if (!in_array('Size', $dynamicColumnsToShowel))
                                <td>
                                    {{$source['size']}}
                                </td>
                                @endif

                                @if (!in_array('Type', $dynamicColumnsToShowel))
                                <td class="expand-row-msg" data-name="type" data-id="{{$source['id']}}">
                                    <?php $type = ucwords(str_replace('-', ' ', $source['type'])); ?>
                                    <span class="show-short-type-{{$source['id']}}">{{ Str::limit($type, 5, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-type-{{$source['id']}} hidden">{{$type}}</span>
                                </td>
                                @endif

                                @if (!in_array('Communication', $dynamicColumnsToShowel))
                                <td class="communication-td">
                                    @if($source['customer_whatsapp_number'])
                                        <input type="text" class="form-control send-message-textbox w-50 pull-left" data-id="{{$source['customer_id']}}" id="send_message_{{$source['customer_id']}}" name="send_message_{{$source['id']}}" placeholder="whatsapp message..." />
                                        <button class="btn btn-sm btn-xs send-message-open p-0 pull-left" type="submit" id="submit_message" data-id="{{$source['id']}}"><i class="fa fa-paper-plane"></i></button>
                                        <button type="button" class="btn btn-xs load-communication-modal p-0 pull-left" data-object='customer' data-id="{{ $source['customer_id'] }}" title="Load messages"><i class="fa fa-comments"></i></button>
                                    @endif
                                </td>
                                @endif
                                
                                @if (!in_array('Action', $dynamicColumnsToShowel))
                                <td>
                                    <a style="color:black;" href="javascript:;" data-id="{{ $source['id'] }}" class="supplier-discount-info view-supplier-details text-secondary">
                                        <i class="fa fa-shopping-cart"></i>
                                    </a>
                                </td>
                                @endif
                            </tr>
                        @else
                            <tr style="background-color: {{$status_color->status_color ?? ""}}!important;">
                                <td>
                                    <input name="customer_message[]" class="customer_message" type="checkbox" value="{{$source['customer_id']}}">
                                </td>

                                <td>
                                    {{$source['id']}}
                                </td>

                                <td>
                                    {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $source['created_at'])->format('d-m-y')}}
                                </td>

                                <td>
                                    <select class="form-control update-Erp-Status" name="ErpStatus" data-id="{{$source['id']}}">
                                        @foreach($erpLeadStatus as $erp_status)
                                        <option value="{{ $erp_status['id'] }}" {{ $source['status_name'] == $erp_status['name'] ? 'selected' : '' }}>{{ $erp_status['name'] }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="expand-row-msg" data-name="customer_name" data-id="{{$source['id']}}">
                                    <a href="/customer/{{$source['customer_id']}}" target="_blank">
                                        <span class="show-short-customer_name-{{$source['id']}}">{{ Str::limit($source['customer_name'], 5, '..')}}</span>
                                        <span style="word-break:break-all;" class="show-full-customer_name-{{$source['id']}} hidden">{{$source['customer_name']}}</span>
                                    </a>
                                </td>

                                <!-- 08-09-2021 -->
                                <td class="expand-row-msg" data-name="customer_email" data-id="{{$source['id']}}">
                                    <span class="show-short-customer_email-{{$source['id']}}">{{ Str::limit($source['customer_email'], 7, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-customer_email-{{$source['id']}} hidden">{{$source['customer_email']}}</span>
                                </td>

                                <td class="expand-row-msg" data-name="customer_whatsapp_number" data-id="{{$source['id']}}">
                                    <span class="show-short-customer_whatsapp_number-{{$source['id']}}">{{ Str::limit($source['customer_whatsapp_number'], 5, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-customer_whatsapp_number-{{$source['id']}} hidden">{{$source['customer_whatsapp_number']}}</span>
                                </td>

                                <td class="expand-row-msg" data-name="website" data-id="{{$source['id']}}">
                                    <span class="show-short-website-{{$source['id']}}">{{ Str::limit($source['website'], 7, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-website-{{$source['id']}} hidden">{{$source['website']}}</span>
                                </td>

                                <td>
                                    @if($source['media_url']) <img class="lazy" alt="" src="{{$source['media_url']}}" style="width:50px;"> @else {{''}} @endif
                                </td>

                                <td>
                                    {{$source['product_id']}}
                                </td>

                                <td class="expand-row-msg" data-name="product_sku" data-id="{{$source['id']}}">
                                    <span class="show-short-product_sku-{{$source['id']}}">{{ Str::limit($source['product_sku'], 8, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-product_sku-{{$source['id']}} hidden">{{$source['product_sku']}}</span>
                                </td>
                                <td class="expand-row-msg" data-name="product_name" data-id="{{$source['id']}}">
                                    <span class="show-short-product_name-{{$source['id']}}">{{ Str::limit($source['product_name'], 8, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-product_name-{{$source['id']}} hidden">{{$source['product_name']}}</span>
                                </td>

                                <td class="expand-row-msg" data-name="brand_name" data-id="{{$source['id']}}">
                                    <span class="show-short-brand_name-{{$source['id']}}">{{ Str::limit($source['brand_name'], 8, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-brand_name-{{$source['id']}} hidden">{{$source['brand_name']}}</span>

                                    <a class="multi_brand_category_create text-secondary" data-id="{{$source['id']}}" data-url="{{route('manage.leads.brand')}}" href="javascript:;">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </td>

                                <td>
                                    {{$source['brand_segment']}}
                                </td>

                                <td class="expand-row-msg" data-name="cat_title" data-id="{{$source['id']}}">
                                    <?php
                                    if ($source['cat_title'] != null) { ?>
                                        <span class="show-short-cat_title-{{$source['id']}}">{{ Str::limit($source['cat_title'], 5, '..')}}</span>
                                        <span style="word-break:break-all;" class="show-full-cat_title-{{$source['id']}} hidden">{{$source['cat_title']}}</span>
                                    <?php
                                    } ?>
                                    <a class="multi_brand_category_create text-secondary" data-id="{{$source['id']}}" data-url="{{route('manage.leads.category')}}" href="javascript:;">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </td>

                                <td>
                                    {{$source['color']}}
                                </td>
                                
                                <td>
                                    {{$source['size']}}
                                </td>

                                <td class="expand-row-msg" data-name="type" data-id="{{$source['id']}}">
                                    <?php $type = ucwords(str_replace('-', ' ', $source['type'])); ?>
                                    <span class="show-short-type-{{$source['id']}}">{{ Str::limit($type, 5, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-type-{{$source['id']}} hidden">{{$type}}</span>
                                </td>

                                <td class="communication-td">
                                    @if($source['customer_whatsapp_number'])
                                        <input type="text" class="form-control send-message-textbox w-50 pull-left" data-id="{{$source['customer_id']}}" id="send_message_{{$source['customer_id']}}" name="send_message_{{$source['id']}}" placeholder="whatsapp message..." />
                                        <button class="btn btn-sm btn-xs send-message-open p-0 pull-left" type="submit" id="submit_message" data-id="{{$source['id']}}"><i class="fa fa-paper-plane"></i></button>
                                        <button type="button" class="btn btn-xs load-communication-modal p-0 pull-left" data-object='customer' data-id="{{ $source['customer_id'] }}" title="Load messages"><i class="fa fa-comments"></i></button>
                                    @endif
                                </td>
                                
                                <td>
                                    <a style="color:black;" href="javascript:;" data-id="{{ $source['id'] }}" class="supplier-discount-info view-supplier-details text-secondary">
                                        <i class="fa fa-shopping-cart"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                {{ $sourceData->appends(Request::except('page'))->links() }}
            </div>
        </div>
    </div>
</div>
</div>



<div id="chat-list-history" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Communication</h4>
        <input type="text" name="search_chat_pop" class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
        <input type="hidden" id="chat_obj_type" name="chat_obj_type">
        <input type="hidden" id="chat_obj_id" name="chat_obj_id">
        <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
      </div>
      <div class="modal-body" style="background-color: #999999;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<div id="erp-leads" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">

      </div>
    </div>
  </div>
</div>

<div id="erp_leads_manage_category_brand" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="set_heading">Erp Leads</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body erp-leads-modal" id="erp_leads_manage_category_brand_form">

      </div>
    </div>
  </div>
</div>

<div id="create_broadcast" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send Message to Customers</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form enctype="multipart/form-data" id="send_message" method="POST">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
          <div class="form-group">
            <strong> Selected Product :</strong>
            <select name="selected_product[]" class="ddl-select-product form-control" multiple="multiple"></select>
            <strong> Attach Image :</strong>
            <div class='input-group date' id='schedule-datetime'>
              <input type='file' class="form-control" name="image" id="image" value="" />
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-file"></span>
              </span>
            </div>

            <strong>Schedule Date:</strong>
            <div class='input-group date' id='schedule-datetime'>
              <input type='text' class="form-control" name="sending_time" id="sending_time_field" value="{{ date('Y-m-d H:i') }}" required />
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>
          </div>
          <div class="form-group">
            <strong>Message</strong>
            <textarea name="message" id="message_to_all_field" rows="8" cols="80" class="form-control"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Send Message</button>
        </div>
      </form>
    </div>

  </div>
</div>

<div id="update-status-message-tpl" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div class="modal-header">
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
              <div class="col-md-8">
                <div class="form-group">
                  <textarea cols="45" class="form-control" id="order-template-status-tpl" name="message"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-secondary update-status-with-message">With Message</button>
          <button type="button" class="btn btn-secondary update-status-without-message">Without Message</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="purchaseCommonModal" class="modal fade" role="dialog" style="padding-top: 0px !important;
    padding-right: 12px;
    padding-bottom: 0px !important;">
  <div class="modal-dialog" style="width: 100%;
    max-width: none;
    height: auto;
    margin: 0;">
    <div class="modal-content " style="
    border: 0;
    border-radius: 0;">
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
@include("leads.erp.column-visibility-modal")
@include("leads.erp.modal-status-color")
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://cdn.datatables.net/scroller/2.0.2/js/dataTables.scroller.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script type="text/javascript">
  $(document).on('click', '.expand-row-msg', function() {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-' + name + '-' + id;
    var mini = '.expand-row-msg .show-full-' + name + '-' + id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  });


  var customers = [];
  var allLeadCustomersId = [];
  $(document).ready(function() {
    $('.multi_brand').select2();
    $('.multi_lead_status').select2({
      placeholder: "Brand",
      // allowClear: true
    });

    $('.lead_status').select2({
      placeholder: "Select Category",
      // allowClear: true
    });

    $('.lead_type').select2({
      placeholder: "Select Type",
      // allowClear: true
    });



    $(".all_customer_check").click(function() {
      $('.customer_message').prop('checked', this.checked);
      $(".customer_message").each(function() {
        if ($(this).prop("checked") == true) {
          if (customers.indexOf($(this).val()) === -1) {
            customers.push($(this).val());
          }
        } else {
          var tmpCustomers = [];
          for (var k in customers) {
            if (customers[k] != $(this).val()) {
              tmpCustomers.push(customers[k]);
            }
          }
          customers = tmpCustomers;
        }
      });
    });


    $(".all_page_check").click(function() {
      $('.customer_message').prop('checked', this.checked);
      customers = [];
      if (this.checked) {
        for (var k in allLeadCustomersId) {
          if (customers.indexOf(allLeadCustomersId[k]) === -1) {
            customers.push(allLeadCustomersId[k]);
          }
        }
      }
    });

    $(document).on('change', '.customer_message', function() {
      if ($(this).prop("checked") == true) {
        if (customers.indexOf($(this).val()) === -1) {
          customers.push($(this).val());
        }
      } else {
        var tmpCustomers = [];
        for (var k in customers) {
          if (customers[k] != $(this).val()) {
            tmpCustomers.push(customers[k]);
          }
        }
        customers = tmpCustomers;
      }
    });
    $(document).on('click', '.block_customer', function() {
      var customer_id = $(this).data('customer_id');
      var column = $(this).data('column');
      var lead_product_freq = $(this).closest('td').find("input[name='lead_product_freq']").val();
      var data = {
        lead_product_freq: lead_product_freq,
        customer_id: customer_id,
        column: column,

      };

      $.ajax({
        url: "{{ route('leads.block.customer') }}",
        type: "POST",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: data,
        dataType: 'json',
      }).done(function() {
        alert('Leads for Customer are blocked');
        // window.location.reload();
      }).fail(function(response) {


        alert('Could not say No!');
        console.log(response);
      });
    });

    $(".images_attach").click(function(e) {
      e.preventDefault();
      if (customers.length == 0) {
        alert('Please select costomer');
        return false;
      }
      url = "{{ route('attachImages', ['selected_customer', 'CUSTOMER_IDS', 1]) }}";
      url = url.replace("CUSTOMER_IDS", customers.toString());

      window.location.href = url;

    });

    $('.infinite-scroll').jscroll({

      autoTrigger: true,
      // debug: true,
      loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
      padding: 20,
      nextSelector: '.pagination li.active + li a',
      contentSelector: 'div.infinite-scroll',
      callback: function() {
        $('ul.pagination').first().remove();
        $('ul.pagination').hide();
      }
    });

    $("#send_message").submit(function(e) {
      e.preventDefault();
      var formData = new FormData($(this)[0]);

      if (customers.length == 0) {
        alert('Please select costomer');
        return false;
      }

      if ($("#send_message").find("#message_to_all_field").val() == "") {
        alert('Please type message ');
        return false;
      }

      /*if ($("#send_message").find(".ddl-select-product").val() == "") {
        alert('Please select product');
        return false;
      }*/

      for (var i in customers) {
        formData.append("customers[]", customers[i]);
      }

      $.ajax({
        type: "POST",
        url: "{{ route('erp-leads-send-message') }}",
        data: formData,
        contentType: false,
        processData: false
      }).done(function() {
        // window.location.reload();
      }).fail(function(response) {
        $(thiss).text('No');

        alert('Could not say No!');
        console.log(response);
      });
    });
    jQuery('.ddl-select-product').select2({
      ajax: {
        url: '/productSearch/',
        dataType: 'json',
        delay: 750,
        data: function(params) {
          return {
            q: params.term, // search term
          };
        },
        processResults: function(data, params) {

          params.page = params.page || 1;

          return {
            results: data,
            pagination: {
              more: (params.page * 30) < data.total_count
            }
          };
        },
      },
      placeholder: 'Search for Product by id, Name, Sku',
      escapeMarkup: function(markup) {
        return markup;
      },
      minimumInputLength: 5,
      width: '100%',
      templateResult: formatProduct,
      templateSelection: function(product) {
        return product.text || product.name;
      },

    });


    // var table = $('.dataTable').DataTable({
    //       processing: true,
    //       serverSide: true,
    //       searching: false,
    //       ordering: false,
    //       deferRender:    true,
    //   scrollY:        200,
    //   scrollCollapse: true,
    //   scroller:       true,
    //       // bScrollInfinite: true,
    //       // bScrollCollapse: true,
    //       // sScrollY: "200px",
    //       ajax: {
    //         "url" : '{{ route('leads.erpLeadsResponse') }}',
    //         data: function ( d ) {
    //           console.log(d, "opopop");
    //           d.lead_customer = $('.lead_customer').val();
    //           d.lead_brand = $('.lead_brand').val();
    //           d.lead_category = $('.lead_category').val();
    //           d.lead_color = $('.lead_color').val();
    //           d.lead_shoe_size = $('.lead_shoe_size').val();
    //           d.brand_segment = $('.brand_segment').val();
    //           d.lead_status = $('.lead_status').val();
    //           $('.all_customer_check').prop('checked', false);
    //         },
    //         dataSrc : function ( response ) {
    //           allLeadCustomersId = response.allLeadCustomersId;
    //           return response.data;
    //         }
    //       },
    //       columns: [
    //         {
    //           data: 'id',
    //           render : function ( data, type, row ) {
    //                 // Combine the first and last names into a single table field
    //                 return '<div class="checkbox"><label class="checkbox-inline"><input name="customer_message[]" class="customer_message" type="checkbox" value="'+row.customer_id+'">'+data+'aa</label></div>';
    //          }       
    //         },
    //         {data: 'status_name', name: 'status_name'},
    //         {
    //             data: null,
    //             render : function ( data, type, row ) {
    //                 return '<a href="/customer/' + data.customer_id + '" target="_blank">' + data.customer_name + '</a>';
    //             }
    //         },
    //         {
    //             data: null,
    //             render : function ( data, type, row ) {
    //                 return data.media_url ? '<img class="lazy" alt="" src="' + data.media_url + '" style="width:50px;">' : '';
    //             }
    //         },
    //         {data: 'brand_name', name: 'brand_name'},
    //         {data: 'brand_segment', name: 'brand_segment'},
    //         {data: 'cat_title', name: 'cat_title'},
    //         {data: 'color', name: 'color'},
    //         {data: 'size', name: 'size'}
    //     ]
    //   });

    // $( '.field_search' ).on( 'keyup change', function () {
    //     table.draw();
    // });btnFileterErpLeads
    // $( '#btnFileterErpLeads' ).on( 'click', function () {
    //     table.draw();
    // });

    // $( '.multi_brand' ).on( 'change', function () {
    //     table.draw();
    // });

  });

  $(document).on('click', '.create_broadcast', function() {
    if (customers.length == 0) {
      alert('Please select costomer');
      return false;
    }
    $("#create_broadcast").modal("show");
  });

  // start to search for customer

  var customerSearch = function() {
    $(".customer-search-box").select2({
      tags: true,
      ajax: {
        url: '/erp-leads/customer-search',
        dataType: 'json',
        delay: 750,
        data: function(params) {
          return {
            q: params.term, // search term
          };
        },
        processResults: function(data, params) {

          params.page = params.page || 1;

          return {
            results: data,
            pagination: {
              more: (params.page * 30) < data.total_count
            }
          };
        },
      },
      placeholder: 'Search for Customer by id, Name, No',
      escapeMarkup: function(markup) {
        return markup;
      },
      minimumInputLength: 2,
      templateResult: formatCustomer,
      templateSelection: (customer) => customer.text || customer.name,

    });
  };

  //scrip

  $(document).on('click', '.multi_brand_category_create', function() {

    var url = $(this).attr('data-url');
    var lead_id = $(this).attr('data-id');

    $.ajax({
      type: "GET",
      url: url + '?lead_id=' + lead_id
    }).done(function(data) {
      console.log(data);
      $("#erp_leads_manage_category_brand_form").html(data);
      $("#lead_create_brands").append('<input type="hidden" name="lead_id" value=' + lead_id + '>');
      $("#erp_leads_manage_category_brand").modal("show");

      $('.multi_lead_status_brands').select2({
        placeholder: "Select Brand",
        // allowClear: true
      });

    }).fail(function(response) {
      console.log(response);
    });
  });



  $(document).on('click', '.lead-button-submit-for-category-brand', function(e) {
    e.preventDefault();
    var $this = $(this);

    var url = $('#lead_create_brands').attr('action');

    var formData = new FormData(document.getElementById("lead_create_brands"));
    $.ajax({
      type: "POST",
      data: formData,
      url: url,
      contentType: false,
      processData: false
    }).done(function(data) {
      console.log(data);
      if (data.code == 200) {
        $("#erp_leads_manage_category_brand").find(".modal-body").html("");
        $("#erp_leads_manage_category_brand").modal("hide");
        location.reload(true);
      } else {
        alert(data.message);
      }
    }).fail(function(response) {
      console.log(response);
    });
  });




  $(document).on('click', '.editor_create', function() {
    var $this = $(this);
    $.ajax({
      type: "GET",
      url: "{{ route('leads.erpLeads.create') }}"
    }).done(function(data) {
      $("#erp-leads").find(".modal-body").html(data);
      customerSearch();
      $('.multi_brand_select').select2({
        width: '100%'
      });
      $('.brand_segment_select').select2({
        width: '100%'
      });

      $(".multi_brand_select").change(function() {
        var brand_segment = [];
        $(this).find(':selected').each(function() {
          if ($(this).data('brand-segment') && brand_segment.indexOf($(this).data('brand-segment')) == '-1') {
            brand_segment.push($(this).data('brand-segment'));
          }
        })
        $(".brand_segment_select").val(brand_segment).trigger('change');
      });

      $('#category_id').select2({
        width: '100%'
      });
      $("#erp-leads").modal("show");
    }).fail(function(response) {
      console.log(response);
    });
  });

  $(document).on('click', '.editor_remove', function() {
    var r = confirm("Are you sure you want to delete this lead?");
    if (r == true) {
      var $this = $(this);
      $.ajax({
        type: "GET",
        data: {
          id: $this.data("lead-id")
        },
        url: "{{ route('leads.erpLeads.delete') }}"
      }).done(function(data) {
        $("#erp-leads").find(".modal-body").html("");
        $("#erp-leads").modal("hide");
        location.reload(true);
      }).fail(function(response) {
        console.log(response);
      });
    }
  });

  $(document).on('click', '.editor_edit', function() {
    var $this = $(this);
    $.ajax({
      type: "GET",
      data: {
        id: $this.data("lead-id")
      },
      url: "{{ route('leads.erpLeads.edit') }}"
    }).done(function(data) {
      $("#erp-leads").find(".modal-body").html(data);
      customerSearch();
      $("#erp-leads").modal("show");
    }).fail(function(response) {
      console.log(response);
    });
  });

  $(document).on('click', '.lead-button-submit-form', function(e) {
    e.preventDefault();
    var $this = $(this);
    var formData = new FormData(document.getElementById("lead_create"));
    $.ajax({
      type: "POST",
      data: formData,
      url: "{{ route('leads.erpLeads.store') }}",
      contentType: false,
      processData: false
    }).done(function(data) {
      if (data.code == 1) {
        $("#erp-leads").find(".modal-body").html("");
        $("#erp-leads").modal("hide");
        location.reload(true);
      } else {
        alert(data.message);
      }
    }).fail(function(response) {
      console.log(response);
    });
  });

  function formatProduct(product) {
    if (product.loading) {
      return product.sku;
    }

    if (product.sku) {
      return "<p> <b>Id:</b> " + product.id + (product.name ? " <b>Name:</b> " + product.name : "") + " <b>Sku:</b> " + product.sku + " </p>";
    }

  }

  function formatCustomer(customer) {
    if (customer.loading) {
      return customer.name;
    }

    if (customer.name) {
      return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
    }

  }

  $(document).on("change", ".update-Erp-Status", function() {
    var id = $(this).data("id");
    var status_id = $(this).val();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('erpLeads.status.update') }}",
      type: "post",
      data: {
        'id': id,
        'status_id': status_id
      },
    }).done(function(response) {
      if (response.code == 200) {
        $("#update-status-message-tpl").modal("show");
        $("#order-id-status-tpl").val(id);
        $("#order-status-id-status-tpl").val(status_id);
        $("#order-template-status-tpl").val(response.template);
      }

    })
  });

  $(document).on("click", ".update-status-with-message", function(e) {
    e.preventDefault();
    $.ajax({
      url: "{{ route('erpLeads.status.change') }}",
      type: "GET",
      async: false,
      data: {
        id: $("#order-id-status-tpl").val(),
        status: $("#order-status-id-status-tpl").val(),
        sendmessage: '1',
        message: $("#order-template-status-tpl").val(),
      }
    }).done(function(response) {
      $("#update-status-message-tpl").modal("hide");
    })
  });

  $(document).on("click", ".update-status-without-message", function(e) {
    e.preventDefault();
    $.ajax({
      url: "{{ route('erpLeads.status.change') }}",
      type: "GET",
      async: false,
      data: {
        id: $("#order-id-status-tpl").val(),
        status: $("#order-status-id-status-tpl").val(),
        sendmessage: '0',
        message: $("#order-template-status-tpl").html(),
      }
    }).done(function(response) {
      $("#update-status-message-tpl").modal("hide");
    }).fail(function(errObj) {
      alert("Could not change status");
    });
  });


  $(document).on('click', '.send-message-open', function(event) {
    var textBox = $(this).closest(".communication-td").find(".send-message-textbox");
    let customerId = textBox.attr('data-id');
    let message = textBox.val();
    if (message == '') {
      return;
    }

    let self = textBox;

    $.ajax({
      url: "{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'customer')}}",
      type: 'POST',
      data: {
        "customer_id": customerId,
        "message": message,
        "_token": "{{csrf_token()}}",
        "status": 2
      },
      dataType: "json",
      success: function(response) {
        toastr["success"]("Message sent successfully!", "Message");
        $('#message_list_' + customerId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
        $(self).removeAttr('disabled');
        $(self).val('');
      },
      beforeSend: function() {
        $(self).attr('disabled', true);
      },
      error: function() {
        alert('There was an error sending the message...');
        $(self).removeAttr('disabled', true);
      }
    });
  });

  $(document).on('click', '.view-supplier-details', function(e) {
    e.preventDefault();
    var lead_id = $(this).data('id');
    var type = 'GET';
    $.ajax({
      url: '/purchase-product/lead-supplier-details/' + lead_id,
      type: type,
      dataType: 'html',
      beforeSend: function() {
        $("#loading-image").show();
      }
    }).done(function(response) {
      $("#loading-image").hide();
      $("#purchaseCommonModal").modal("show");
      $("#common-contents").html(response);
    }).fail(function(errObj) {
      $("#loading-image").hide();
    });
  });

  $(document).on('keyup', '.supplier-discount', function(event) {
    if (event.keyCode != 13) {
      return;
    }
    let id = $(this).data('id');
    let product_id = $(this).data('product');
    let discount = $("#supplier_discount-" + id).val();
    let lead_id = $(this).data('lead-id');
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{action([\App\Http\Controllers\PurchaseProductController::class, 'saveDiscount'])}}",
      type: 'POST',
      data: {
        discount: discount,
        supplier_id: id,
        product_id: product_id,
        lead_id: lead_id
      },
      success: function(data) {
        toastr["success"]("Discount updated successfully!", "Message");
        $("#common-contents").html(data.html);
      }
    });

  });


  $(document).on('keyup', '.supplier-fixed-price', function(event) {
    if (event.keyCode != 13) {
      return;
    }
    let id = $(this).data('id');
    let fixed_price = $("#supplier_fixed_price_" + id).val();
    let product_id = $(this).data('product');
    let lead_id = $(this).data('lead-id');
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{action([\App\Http\Controllers\PurchaseProductController::class, 'saveFixedPrice'])}}",
      type: 'POST',
      data: {
        fixed_price: fixed_price,
        supplier_id: id,
        product_id: product_id,
        lead_id: lead_id
      },
      success: function(data) {
        toastr["success"]("Fixed price updated successfully!", "Message");
        $("#common-contents").html(data.html);
      }
    });

  });

  function funEnableDisableLeads(chk) {
    if (confirm(chk.checked ? 'Are you sure, do you want to enable this function?' : 'Are you sure, do you want to disable this function?')) {
      siteLoader(1);
      jQuery.ajax({
        headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('erp-leads.enable-disable') }}",
        type: 'POST',
        data: {
          status: chk.checked ? 1 : 0
        }
      }).done(function(res) {
        siteLoader(0);
        siteSuccessAlert(res);
      }).fail(function(err) {
        siteLoader(0);
        siteErrorAlert(err);
      });
    } else {
      chk.checked = !chk.checked;
    }
  }
</script>
@endsection