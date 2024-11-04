@extends('layouts.app')

@section('title', 'Log List Magento')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style type="text/css">

  #loading-image {
    position: fixed;
    top: 50%;
    left: 50%;
    margin: -50px 0px 0px -50px;
  }
  input {
    width: 100px;
  }
  .btn-primary, .btn-primary:hover{
    background: #fff;
    color:#757575;
    border: 1px solid #ddd;
  }
  .select2-container{
    width: 100% !important;
  }
  .select2-container--default .select2-selection--multiple{
    border: 1px solid #ddd !important;
  }
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 5px;
  }
 label{
   margin-top:5px;
   margin-bottom:4px;
   margin-left:3px;
 }
.export_btn {
	margin: 27px 0 0 0;
}
@media(max-width:767px){
	div#Export_popup .col-xs-12{padding:0;}
	.export_btn{margin:10px 0 0 0;}
}
    .ac-btns button{
      height: 20px;
        width: auto;
    }
</style>
@endsection

@section('content')
  <div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
  </div>
  <div class="row m-0">
    <div class="col-lg-12 margin-tb p-0">
      <h2 class="page-heading">Log List Magento ({{ $total_count }})
      <div class="pull-right">
        <button type="button" class="btn btn-image pr-0" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
        <a href="/logging/magento-product-api-call" target="__blank">
          <button type="button" class="btn btn-image"><img src="/images/details.png" /></button>
        </a>

        <button class="btn btn-primary ml-3" id="submit-show-report">
          Show Error Report
        </button>

        <button class="btn btn-primary" id="retry-failed-job">
          Retry failed job
        </button>

            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#Export_popup">Export</button>

            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#listmagentodatatablecolumnvisibilityList">Column Visiblity</button>

      </div>
      </h2>
  </div>
  </div>

  <div class="col-md-12 pl-3 pr-3">
    <div class="mb-3">
      <div class="panel-body p-0">
        <form action="{{ route('list.magento.logging') }}" method="GET" class="handle-search">
          <div class="row m-0">
            <div class="col-md-2 pl-0">
                <input 
                  type="text" class="form-control" id="select_date" placeholder='Date Range' 
                  autocomplete="off"
                  value="{{ 
                      isset($start_date) ? $start_date . ' - ' : ''
                    }}{{ 
                      isset($end_date) ? $end_date : ''
                    }}" 
                />
                <input type="hidden" class='start_date' name='start_date' />
                <input type="hidden" class='end_date' name='end_date' />
            </div>
            <div class="col-md-1 pl-0">
              <select class="form-control" id="product_id" name="product_id">
                @isset($filters['product_id'])
                  <option>{{ $filters['product_id'] }}</option>
                @endisset
              </select>
            </div>
            <div class="col-md-1 pl-0">
              <select class="form-control" id="sku" name="sku">
                @isset($filters['sku'])
                  <option>{{ $filters['sku'] }}</option>
                @endisset
              </select>
            </div>
            <div class="col-md-1 pl-0">
              <select class="form-control" id="brand" name="brand">
                @isset($filters['brand'])
                  <option>{{ $filters['brand'] }}</option>
                @endisset
              </select>
            </div>
            <div class="col-md-1 pl-0">
              {!! $category_suggestion !!}
            </div>

            <div class="col-md-1 pl-0">
              <select class="form-control" name="status">
                <option value="" disabled selected>Status</option>
                <option value=''>All</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
              </select>
            </div>

              <div class="col-md-1 pl-0">
                  <select class="form-control" name="sync_status">
                      <option value="" disabled selected>Sync Status</option>
                      <option value=''>All</option>
                      <option value="success" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'success' ? 'selected' : '' }}>Success</option>
                      <option value="error" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'error' ? 'selected' : '' }}>Error</option>
                      <option value="waiting" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'waiting' ? 'selected' : '' }}>Waiting</option>
                      <option value="started_push" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'started_push' ? 'selected' : '' }}>Sync Status</option>
                      <option value="size_chart_needed" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'size_chart_needed' ? 'selected' : '' }}>Size chart needed</option>
                      <option value="image_not_found" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'image_not_found' ? 'selected' : '' }}>Image not found</option>
                      <option value="translation_not_found" {{ isset($filters['sync_status']) && $filters['sync_status'] == 'translation_not_found' ? 'selected' : '' }}>Translation not found</option>
                  </select>
              </div>
              <div class="col-md-1 pl-0">
                  {{ html()->select("queue", [null => "Queue List"] + $queueNames, request('queue'))->class("form-control") }}
              </div>

          <div class="col-md-1 pl-0">
            <select class="form-control" name="size_info">
              <option value="" disabled selected>Size info</option>
              <option value=''>All</option>
              <option value="yes" {{ isset($filters['size_info']) && $filters['size_info'] == 'yes' ? 'selected' : '' }}>Yes</option>
              <option value="no" {{ isset($filters['size_info']) && $filters['size_info'] == 'no' ? 'selected' : '' }}>No</option>
            </select>
          </div>
        </div>
        <div class="row m-0 mt-3">
          <div class="col-md-2 pl-0">
            <input type="text" name="job_start_date" class="form-control datepicker" id="job_start_date" placeholder="Enter Job Start Date" value="{{isset($request->job_start_date) ? $request->job_start_date : ''}}">
          </div>
          <div class="col-md-1 pl-0">
            <select class="form-control" name="user">
              <option value="" disabled selected>Users</option>
              <option value=''>All</option>
              @foreach($users as $user)
                <option value="{{$user->id}}" {{ isset($filters['user']) && $filters['user'] == $user->id ? 'selected' : '' }}>{{$user->name}}</option>
              @endforeach
              <option >
            </select>
          </div>

          <div class="col-md-2 pl-0" style="align-items: flex-end;justify-content: space-between;">
            <input type="hidden" class="range_start_filter" value="<?php echo request()->get('crop_start_date'); ?>" name="crop_start_date" />
            <input type="hidden" class="range_end_filter" value="<?php echo request()->get('crop_end_date'); ?>" name="crop_end_date" />
            <div id="filter_date_range_" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ddd; width: 100%;border-radius:4px;">
              <!-- <i class="fa fa-calendar"></i>&nbsp;
              <span  id="date_current_show"></span><i class="fa fa-caret-down"></i> -->
              <i class="fa fa-calendar"></i>&nbsp;
              <span class="d-none" id="date_current_show"></span> <p style="display:contents;" id="date_value_show"> {{request()->get('crop_start_date') ? request()->get('crop_start_date') .' '.request()->get('crop_end_date') : 'Crop Image Date'}}</p><i class="fa fa-caret-down pull-right"></i>
            </div>
          </div>
          <div class="col-md-4 pl-0">
            <button class="btn btn-primary text-dark" style="height: 34px" id="submit">
                <span class="fa fa-filter"></span>&nbsp;Filter Results
              </button>
              <button class="btn btn-primary text-dark"  style="height: 34px" id="send-product-for-live-checking">
                <span class="fa fa-send"></span>&nbsp;Send Live Product
              </button>
              <button class="btn btn-primary text-dark" style="height: 34px" onclick="event.preventDefault();" data-toggle="modal" data-target="#syncStatusColor"> 
                Sync Status Color
              </button>
          </div>
        </div>
      </form>
    </div>
  </form>
</div>




     <div class="row m-0">
        <div class="table-responsive table-horizontal-scroll">
          <table id="magento_list_tbl_895" class="table table-bordered table-hover" style="table-layout: fixed">
            <thead>
                @if(!empty($dynamicColumnsToShowListmagento))
                    @if (!in_array('ID', $dynamicColumnsToShowListmagento))
                        <th width="4%">ID</th>
                    @endif

                    @if (!in_array('SKU', $dynamicColumnsToShowListmagento))
                        <th width="5%">SKU</th>
                    @endif

                    @if (!in_array('Brand', $dynamicColumnsToShowListmagento))
                        <th width="5%">Brand</th>
                    @endif

                    @if (!in_array('Category', $dynamicColumnsToShowListmagento))
                        <th width="6%">Category</th>
                    @endif

                    @if (!in_array('Price', $dynamicColumnsToShowListmagento))
                        <th width="4%">Price</th>
                    @endif

                    @if (!in_array('Message', $dynamicColumnsToShowListmagento))
                        <th width="6%">Message</th>
                    @endif

                    @if (!in_array('D&T', $dynamicColumnsToShowListmagento))
                        <th width="6%">D&T</th>
                    @endif

                    @if (!in_array('Website', $dynamicColumnsToShowListmagento))
                        <th width="6%">Website</th>
                    @endif

                    @if (!in_array('Status', $dynamicColumnsToShowListmagento))
                        <th width="3%">Status</th>
                    @endif

                    @if (!in_array('Lang Id', $dynamicColumnsToShowListmagento))
                        <th width="4%">Lang Id</th>
                    @endif

                    @if (!in_array('Sync Sts', $dynamicColumnsToShowListmagento))
                        <th width="6%">Sync Sts</th>
                    @endif

                    @if (!in_array('Job Start', $dynamicColumnsToShowListmagento))
                        <th width="6%">Job Start</th>
                    @endif

                    @if (!in_array('Job End', $dynamicColumnsToShowListmagento))
                        <th width="6%">Job End</th>
                    @endif

                    @if (!in_array('Total', $dynamicColumnsToShowListmagento))
                        <th width="3%">Total</th>
                    @endif

                    @if (!in_array('Success', $dynamicColumnsToShowListmagento))
                        <th width="4%;">Success</th>
                    @endif

                    @if (!in_array('Failure', $dynamicColumnsToShowListmagento))
                        <th width="3%">Failure</th>
                    @endif

                    @if (!in_array('User', $dynamicColumnsToShowListmagento))
                        <th width="6%;">User</th>
                    @endif

                    @if (!in_array('Time', $dynamicColumnsToShowListmagento))
                        <th width="3%;">Time</th>
                    @endif

                    @if (!in_array('Size', $dynamicColumnsToShowListmagento))
                        <th width="2%;">Size</th>
                    @endif

                    @if (!in_array('Queue', $dynamicColumnsToShowListmagento))
                        <th width="5%;">Queue</th>
                    @endif

                    @if (!in_array('Try', $dynamicColumnsToShowListmagento))
                        <th width="2%">Try</th>
                    @endif

                    @if (!in_array('Action', $dynamicColumnsToShowListmagento))
                        <th width="3%">Action</th>
                    @endif
                @else 
                    <th width="4%">ID</th>
                    <th width="5%">SKU</th>
                    <th width="5%">Brand</th>
                    <th width="6%">Category</th>
                    <th width="4%">Price</th>
                    <th width="6%">Message</th>
                    <th width="6%">D&T</th>
                    <th width="6%">Website</th>
                    <th width="3%">Status</th>
                    <th width="4%">Lang Id</th>
                    <th width="6%">Sync Sts</th>
                    <th width="6%">Job Start</th>
                    <th width="6%">Job End</th>
                    <th width="3%">Total</th>
                    <th width="4%;">Success</th>
                    <th width="3%">Failure</th>
                    <th width="6%;">User</th>
                    <th width="3%;">Time</th>
                    <th width="2%;">Size</th>
                    <th width="5%;">Queue</th>
                    <th width="2%">Try</th>
                    <th width="3%">Action</th>
                @endif
            </thead>
            <tbody class="infinite-scroll-pending-inner">
				@include("logging.partials.magento_product_data")
            </tbody>
          </table>
        </div>
          {{ $logListMagentos->links() }}
     </div>

@include("logging.partials.modal-sync-status-color")
@include("logging.partials.column-visibility-modal")

  <div id="ErrorLogModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Magento push error log</h4>
        </div>
        <div class="modal-body">
          <table class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
              <th style="width:7%">Product ID</th>
              <th style="width:6%">Date</th>
              <th style="width:11%">Website</th>
              <th style="width:20%">Message</th>
              <th style="width:25%">Request data</th>
              <th style="width:25%">Response Data</th>
              <th style="width:6%">Status</th>
            </thead>
            <tbody class="error-log-data">

            </tbody>
          </table>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

<div id="pushJourney" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Product push journey</h4>
        </div>
        <div class="modal-body">
          <table class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
              <th style="width:7%">Step </th>
              <th style="width:6%">Is checked</th>

            </thead>
            <tbody class="push_journey_logs">

            </tbody>
          </table>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>


  <div id="pushJourneyHorizontal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Product push  journey</h4>
        </div>
        <div class="modal-body" style="overflow-x: auto;">
          <table class="table table-bordered table-hover">
            
            <tbody class="push_journey_horizontal_logs">

            </tbody>
          </table>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  <div id="PriceModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Price details</h4>
        </div>
        <div class="modal-body">
          <table class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
              <th style="width:7%">Product ID</th>
              <th style="width:7%">Default Price</th>
              <th style="width:7%">Price Discount %</th>
              <th style="width:7%">Segment Discount</th>
              <th style="width:7%">Segment Discount %</th>
              <th style="width:7%">Duty Price</th>
              <th style="width:7%">IVA Price</th>
              <th style="width:7%">Override Price</th>
              <th style="width:7%">Override Price % </th>
              <th style="width:6%">Status</th>
              <th style="width:6%">Web Store</th>
              <th style="width:11%">Store Website</th>
            </thead>
            <tbody class="price-data">

            </tbody>
          </table>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  <div id="Export_popup" class="modal fade" role="dialog">
    <div class="modal-dialog " style="padding: 0px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Export</h4>
        </div>
        <div class="modal-body">
		  {{ html()->form('GET', url('/logging/list-magento/export'))->open() }}
				<div class="col-md-8 col-xs-12 pl-0">
					<label for="sku">Select Date</label>
					<input type="hidden" class="start_date" name="start_date" />
					<input type="hidden" class="end_date" name="end_date" />
					<div id="filter_date_range_new" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ddd; width: 100%;border-radius:4px;">
						<!-- <i class="fa fa-calendar"></i>&nbsp;
						<span  id="date_current_show"></span><i class="fa fa-caret-down"></i> -->
						<i class="fa fa-calendar"></i>&nbsp;
						<span class="d-none" id="date_current_show_new"></span> <p style="display:contents;" id="date_value_show_new"> </p><i class="fa fa-caret-down"></i>
					</div>
				</div>
				<div class="col-md-4 pl-0 col-xs-12 export_btn">
					<button type="submit" class="btn btn-primary">Export</button>
				</div>
			</form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  <div id="update_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Update Product</h4>
        </div>
        <form role="form" action="{{route('product.update.magento')}}" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            @csrf
            <input type="hidden" name="update_product_id" id="update_product_id" value="">
            <div class="form-group col-md-6">
              <input name="name" type="text" class="form-control" id="update_name" value=""placeholder="Name" required>
            </div>

            <div class="form-group col-md-6">
              <input name="size" type="text" class="form-control" id="update_size" value=""placeholder="Size">
            </div>
            <div class="form-group col-md-12">
              <textarea name="short_description" class="form-control" id="update_short_description"placeholder="Short Description"style="height: 34px;"></textarea>
            </div>
            <div class="form-group col-md-4">
               <input name="price" type="text" class="form-control" id="update_price" value="" placeholder="price"   required>
            </div>
            <div class="form-group col-md-4">
               <input name="price_eur_special" type="text" class="form-control" id="update_price_eur_special" value=""placeholder="Price Special"  required>
            </div>
            <div class="form-group col-md-4">
              <input name="price_eur_discounted" type="text" class="form-control" id="update_price_eur_discounted" value="" placeholder="Price Discounted" required>
            </div>
            <div class="form-group col-md-4">
              <input name="price_inr" type="text" class="form-control" id="update_price_inr" value="" placeholder="Price INR" required>
            </div>
            <div class="form-group col-md-4">
             <input name="price_inr_special" type="text" class="form-control" id="update_price_inr_special" value="" placeholder="Price Special" required>
            </div>
            <div class="form-group col-md-4">
              <input name="price_inr_discounted" type="text" class="form-control" id="update_price_inr_discounted" value="" pl placeholder="Price Discounted"      required>
            </div>
            <div class="form-group col-md-4">
              <input name="measurement_size_type" type="text" class="form-control" id="update_measurement_size_type" value="" placeholder="Measurement Type">
            </div>
            <div class="form-group col-md-4">
              <input name="lmeasurement" type="text" class="form-control" id="update_lmeasurement" value=""placeholder="L Measurement">
            </div>
            <div class="form-group col-md-4">
              <input name="hmeasurement" type="text" class="form-control" id="update_hmeasurement" value=""placeholder="H Measurement">
            </div>
            <div class="form-group col-md-4">
              <input name="dmeasurement" type="text" class="form-control" id="update_dmeasurement" value=""placeholder="D Measurement">
            </div>
            <div class="form-group col-md-4">
              <input name="composition" type="text" class="form-control" id="update_composition" value="" placeholder="Composition" required>
            </div>
            <div class="form-group col-md-4">
              <input name="made_in" type="text" class="form-control" id="update_made_in" value=""placeholder="Made In">
            </div>
            <div class="form-group col-md-4">
              <select name="brand" class="form-control" id="update_brand"placeholder="Brand">
                <option value=""></option>
                @foreach($brands as $brand)
                  <option value="{{$brand->id}}">{{$brand->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-4">
              <select name="category" class="form-control" id="update_category"placeholder="Category">
                <option value=""></option>
                @foreach($categories as $cat)
                  <option value="{{$cat->id}}">{{$cat->title}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-4">
              <input name="supplier" type="text" class="form-control" id="update_supplier" placeholder="Supplier">
            </div>
            <div class="form-group col-md-6">

              <input name="supplier_link" type="text" class="form-control" id="update_supplier_link"placeholder="Supplier Link">
            </div>
            <div class="form-group col-md-6">
              <input name="product_link" type="text" class="form-control" id="update_product_link"placeholder="Product Link">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary pull-left">Update Product</button>
          </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  <div id="show-error-count" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Error with count</h4>
        </div>
          <div class="modal-body">


          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          </div>
      </div>
    </div>
  </div>
  <div id="show-product-information" class="modal fade" role="dialog" style="margin: 150px;">
    <div class="modal-dialog modal-lg" style="margin: 0px;">
      <div class="modal-content" style="width: 1500px">
        <div class="modal-header">
          <h4 class="modal-title">Product information</h4>
        </div>
          <div class="modal-body">
              <table class="table table-bordered table-hover" style="table-layout:fixed;">
                <thead>
                  <th width="10%">SKU</th>
                  <th width="15%">Description</th>
                  <th>Name</th>
                  <th>Price</th>
                  <th>Composition</th>
                  <th>Material</th>
                  <th>Manufracturer.</th>
                  <th>Brand</th>
                  <th>Sizes</th>
                  <th>Dimensions</th>
                  <th width="5%">Stock</th>
                  <th width="5%">Min day</th>
                  <th width="5%">Max day</th>
                </thead>
                <tbody class="product-information-data">

                </tbody>
              </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          </div>
      </div>
    </div>
  </div>

  <div id="retry-failed-job-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Retry failed job request</h4>
            </div>
            <form class="retry-failed-job-modal-form">
              {!! csrf_field() !!}
              <div class="modal-body">
                  <div class="row">
                      <div class="col">
                          <div class="form-group">
                              <strong>Start Date&nbsp;:&nbsp;</strong>
                              <input type="text" name="start_date" value="" class="form-control start-date-picker">
                          </div>
                        </div>
                        <div class="col">
                          <div class="form-group">
                              <strong>End Date&nbsp;:&nbsp;</strong>
                              <input type="text" name="end_date" value="" class="form-control end-date-picker">
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col">
                          <div class="form-group">
                              <strong>Store website&nbsp;:&nbsp;</strong>
                              {{ html()->select("store_website_id", [null => "- Select -"] + $storeWebsites)->class("form-control select2") }}
                          </div>
                        </div>
                        <div class="col">
                          <div class="form-group">
                              <strong>Product id&nbsp;:&nbsp;</strong>
                              <input type="text" name="keyword" value="" class="form-control">
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary pull-left btn-secondary retry-failed-job-modal-btn">Retry</button>
              </div>
            </form>
        </div>
    </div>
  </div>

  <div id="send-live-product-check-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Send product to check website</h4>
            </div>
            <form class="retry-failed-job-modal-form">
              {!! csrf_field() !!}
              <div class="modal-body">
                  <div class="row">
                      <div class="col">
                          <div class="form-group">
                              <strong>Start Date&nbsp;:&nbsp;</strong>
                              <input type="text" name="start_date" value="" class="form-control start-date-picker">
                          </div>
                        </div>
                        <div class="col">
                          <div class="form-group">
                              <strong>End Date&nbsp;:&nbsp;</strong>
                              <input type="text" name="end_date" value="" class="form-control end-date-picker">
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col">
                          <div class="form-group">
                              <strong>Store website&nbsp;:&nbsp;</strong>
                              {{ html()->select("store_website_id", [null => "- Select -"] + $storeWebsites)->class("form-control select2") }}
                          </div>
                        </div>
                        <div class="col">
                          <div class="form-group">
                              <strong>Product id&nbsp;:&nbsp;</strong>
                              <input type="text" name="keyword" value="" class="form-control">
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary pull-left btn-secondary send-live-product-check-btn">Send</button>
              </div>
            </form>
        </div>
    </div>
  </div>

  <div id="print-live-product-screenshot-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Live Screenshot</h4>
            </div>
           <div class="modal-body">
              <table class="table table-bordered table-hover" style="table-layout:fixed;">
                <thead>
                  <th>SKU</th>
                  <th>Website</th>
                  <th>Status</th>
                  <th>Image</th>
                  <th>Created at</th>
                </thead>
                <tbody class="screenshot-modal-information-data">

                </tbody>
              </table>
           </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
      </div>
  </div>

  <div id="product-translation-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="margin:0; padding: 0;">
        <div class="modal-content" style="width: 1500px;">
            <div class="modal-header">
              <h4 class="modal-title">Product Translation</h4>
            </div>
           <div class="modal-body">

           </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
      </div>
  </div>



@endsection

@section('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function Showactionbtn(id){
            $(".action-btn-tr-"+id).toggleClass('d-none')
        }
        $("#select_date").datepicker({
      	  	format: 'yyyy-mm-dd'
      	});

        $("#job_start_date").datepicker({
          format: 'yyyy-mm-dd'
        });

        $(".start-date-picker").datepicker({
            format: 'yyyy-mm-dd'
        });

        $(".end-date-picker").datepicker({
            format: 'yyyy-mm-dd'
        });


    </script>
  <script type="text/javascript">
  var product = []
  if (localStorage.getItem("luxury-product-data-asin") !== null) {
     var data = JSON.parse(localStorage.getItem('luxury-product-data-asin'));
     product = data
     $.each(product,function(i,e){
      console.log(e)
      $(".selectProductCheckbox_class[value='"+e.sku+"']").attr("checked",true);
     });
  }

  $('#magento_list_tbl_895 tbody').on('click', 'tr', function () {
    var ref = $(this);
    ref.find('td:eq(15)').children('.selectProductCheckbox_class').change(function(){
      if($(this).is(':checked')){
        var val = $(this).val()
        //if(!product.includes($(this).val())){
          if(product.length > 0){
            $.each(product,function(i,e){
            if(e.sku == val){
              product.splice(i,1)
            }
          })
          }

          var item = {"sku":$(this).val(),"websiteid":$(this).attr("websiteid")}
            product.push(item)

        //  }
          localStorage.setItem('luxury-product-data-asin', JSON.stringify(product));
      } else {
        //var index = product.indexOf($(this).val())
        var val = $(this).val()
        $.each(product,function(i,e){
          if(e.sku == val){
            product.splice(i,1)
          }
        })
        // if(index !=-1){
        //   product.splice(index,1)
        // }
        localStorage.setItem('luxury-product-data-asin', JSON.stringify(product));
      }
    })
    console.log(product)
  })
  $(document).on("click", ".show_error_logs", function () {
      var id = $(this).data('id');
      var store_website_id = $(this).data('website');
      $.ajax({
          method: "GET",
          url: "/logging/show-error-log-by-id/" + id,
          data: {
              "_token": "{{ csrf_token() }}"
          },
          dataType: 'html'
      })
          .done(function (result) {
              $('#ErrorLogModal').modal('show');
              $('#ErrorLogModal .modal-title').text('Magento product push error logs');
              $('.error-log-data').html(result);
          });

  });

  $(document).on("click", ".show_product_push_logs", function () {
      var id = $(this).data('id');
      var store_website_id = $(this).data('website');
      $.ajax({
          method: "GET",
          url: "/logging/show-product-push-log/" + id,
          data: {
              "_token": "{{ csrf_token() }}"
          },
          dataType: 'html'
      }).done(function (result) {
          $('#ErrorLogModal').modal('show');
          $('#ErrorLogModal .modal-title').text('Magento product push logs');
          $('.error-log-data').html(result);
      });
  });

  $(document).on("click", ".push_journey", function() {
    var id = $(this).data('id');
    var store_website_id = $(this).data('website');
    $.ajax({
      method: "GET",
      url: "/logging/call-journey-by-id/" + id,
      data: {
        "_token": "{{ csrf_token() }}"
      },
      dataType: 'html'
    })
    .done(function(result) {
      $('#pushJourney').modal('show');
      $('.push_journey_logs').html(result);
    });

  });

  $(document).on("click", ".push_journey_horizontal", function() {
    var id = $(this).data('id');
    var store_website_id = $(this).data('website');
    var prodSku = $(this).data('sku');
    var productId = $(this).data('product_id');
    $.ajax({
      method: "GET",
      url: "/logging/call-journey-horizontal-by-id/" + id,
      data: {
        "_token": "{{ csrf_token() }}",
        "sku_name" : prodSku,
        "product_id" : productId,
      },
      dataType: 'html'
    })
    .done(function(result) {
      $('#pushJourneyHorizontal').modal('show');
      $('.push_journey_horizontal_logs').html(result);
    });function Showactionbtn(id){
        $(".action-btn-tr-"+id).toggleClass('d-none')
    }

  });

  $(document).on("click", ".show_prices", function() {
    var id = $(this).data('id');
    var store_website_id = $(this).data('website');
    var product = $(this).data('product');
    $.ajax({
      method: "GET",
      url: "/logging/show-prices/" + product,
      data: {
        "_token": "{{ csrf_token() }}"
      },
      dataType: 'html'
    })
    .done(function(result) {
      $('#PriceModal').modal('show');
      $('.price-data').html(result);
    });

  });

  $(document).on("click", ".update_modal", function() {
    var data = $(this).data('id');

    var detail = $(this).data('id');
    //alert(JSON.stringify(detail));
    $("#single_product_image").attr("src", detail['image_url']);
    $("#update_product_id").val(detail['product_id']);
    $("#update_name").val(detail['name']);
    $("#update_short_description").val(detail['short_description']);
    $("#update_size").val(detail['size']);
    $("#update_price").val(detail['price']);
    $("#update_price_eur_special").val(detail['price_eur_special']);
    $("#update_price_eur_discounted").val(detail['price_eur_discounted']);

    $("#update_price_inr").val(detail['price_inr']);
    $("#update_price_inr_special").val(detail['price_inr_special']);
    $("#update_price_inr_discounted").val(detail['price_inr_discounted']);

    $("#update_measurement_size_type").val(detail['measurement_size_type']);
    $("#update_lmeasurement").val(detail['lmeasurement']);
    $("#update_hmeasurement").val(detail['hmeasurement']);
    $("#update_dmeasurement").val(detail['dmeasurement']);

    $("#update_composition").val(detail['composition']);
    $("#update_made_in").val(detail['made_in']);
    $("#update_brand").val(detail['brand']);
    $("#update_category").val(detail['category']);
    $("#update_supplier").val(detail['supplier']);
    $("#update_supplier_link").val(detail['supplier_link']);
    $("#update_product_link").val(detail['product_link']);
  });

  $(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  });

  function changeMagentoStatus(logId, newStatus) {
    if (!newStatus) {
      return;
    }
    $.ajax({
      method: "POST",
      url: "/logging/list-magento/" + logId,
      data: {
        "_token": "{{ csrf_token() }}",
        status: newStatus
      }
    })
    .done(function(msg) {
      console.log("Data Saved: ", msg);
    });
  }

  $(document).on("click","#submit-show-report",function(e){
    e.preventDefault();
    $.ajax({
      method: "GET",
      url: "/logging/list-magento/error-reporting"
    })
    .done(function(response) {
        $("#show-error-count").find(".modal-body").html(response);
        $("#show-error-count").modal("show");
    });
  });

  $(document).on("click",".show-product-information",function (e) {
    e.preventDefault();
    var id  = $(this).data("id");
    $.ajax({
      method: "GET",
      url: "/logging/list-magento/product-information",
      data : {
         product_id : id
      }
    })
    .done(function(response) {
        $(".product-information-data").html(response);
        $("#show-product-information").modal("show");
    });
  });

  $(document).on("click","#retry-failed-job",function(e) {
     e.preventDefault();
     $("#retry-failed-job-modal").modal("show");
  });

  $(document).on("click",".retry-failed-job-modal-btn",function(e) {
    e.preventDefault();
      var form = $(this).closest('form');
      $.ajax({
        method: "GET",
        url: "/logging/list-magento/retry-failed-job",
        data : form.serialize(),
        dataType:"json",
        beforeSend : function( ) {
           $("#loading-image").show();
        }
      })
      .done(function(response) {
          $("#loading-image").hide();
          if(response.code == 200) {
            $("#retry-failed-job-modal").modal("hide");
            toastr["success"](response.message);
          }else{
            toastr["error"](response.message);
          }
      });
  });

  $(function() {

    $('#select_date').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('#select_date').on('apply.daterangepicker', function(ev, picker) {
        const startDate = picker.startDate.format('YYYY-MM-DD');
        const endDate = picker.endDate.format('YYYY-MM-DD');
        
        $('.start_date').val(startDate);
        $('.end_date').val(endDate);

        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('#select_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('.start_date').val('');
        $('.end_date').val('');
    });

  });

  $(".select-multiple").select2({tags:true});

  $("#product_id").select2({
      placeholder: 'Product ID',
      allowClear: true,
      ajax: {
          url: "{{ route('list.magento.logging.filterOption') . '?option=product_id' }}",
          dataType: 'json',
          processResults: function (data) {
            let items = data.map(element => {
                return {
                    id: String(element.id), 
                    text: element.id
                };
            });
            return {
              results: items 
            };
         }
      }
  });

  $("#sku").select2({
      placeholder: 'SKU',
      allowClear: true,
      ajax: {
          url: "{{ route('list.magento.logging.filterOption') . '?option=sku' }}",
          dataType: 'json',
          processResults: function (data) {
            let items = data.map(element => {
                return {
                    id: String(element.sku), 
                    text: element.sku
                };
            });
            return {
              results: items
            };
         }
      }
  });

  $("#brand").select2({
      placeholder: 'Brand',
      allowClear: true,
      ajax: {
          url: "{{ route('list.magento.logging.filterOption') . '?option=brand' }}",
          dataType: 'json',
          processResults: function (data) {
            let items = data.map(element => {
                return {
                    id: element.name, 
                    text: element.name
                };
            });
            return {
              results: items
            };
         }
      }
  });

  $(document).on("click",".send-live-product-check-btn",function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      $.ajax({
        method: "GET",
        url: "/logging/list-magento/send-live-product-check",
        data : form.serialize(),
        dataType:"json",
        beforeSend : function( ) {
           $("#loading-image").show();
        }
      })
      .done(function(response) {
          $("#loading-image").hide();
          if(response.code == 200) {
            $("#send-live-product-check-modal").modal("hide");
            toastr["success"](response.message);
          }else{
            toastr["error"](response.message);
          }
      });
  });


  $(document).on("click",".btn-product-screenshot",function(e) {
      var $this = $(this);
      $.ajax({
        method: "GET",
        url: "/logging/list-magento/get-live-product-screenshot",
        data : {
          id : $this.data("id")
        },
        beforeSend : function(response) {
           $("#loading-image").show();

        }
      })
      .done(function(response) {
          $("#loading-image").hide();
          $(".screenshot-modal-information-data").html(response);
          $("#print-live-product-screenshot-modal").modal("show");
      });
  });


  let r_s = "";
  let r_e = "";

  let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(0, 'days');
  let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();

  jQuery('input[name="range_start"]').val();
  jQuery('input[name="range_end"]').val();

  function cb(start, end) {
      $('#filter_date_range_ span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  }

  $('#filter_date_range_').daterangepicker({
        startDate: start,
        maxYear: 1,
        endDate: end,
        //parentEl: '#filter_date_range_',
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
  }, cb);

  //cb(start, end);


  $('#filter_date_range_').on('apply.daterangepicker', function(ev, picker) {
      let startDate=   jQuery('input[name="crop_start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
      let endDate =    jQuery('input[name="crop_end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
      $("#date_current_show").removeClass("d-none");
      $("#date_value_show").css("display", "none");
  });


  jQuery('input[name="start_date"]').val();
  jQuery('input[name="end_date"]').val();

  function cb1(start, end) {
      $('#filter_date_range_new span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  }

  $('#filter_date_range_new').daterangepicker({
        startDate: start,
        maxYear: 1,
        endDate: end,
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
  }, cb1);

  cb1(start, end);


  $('#filter_date_range_new').on('apply.daterangepicker', function(ev, picker) {
      let startDate=   jQuery('input[name="start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
      let endDate =    jQuery('input[name="end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
      $("#date_current_show_new").removeClass("d-none");
      $("#date_value_show_new").css("display", "none");
  });



  $(document).on('click', '.upload-single', function () {
      $this = $(this);
      var id = $(this).data('id');
      var thiss = $(this);
      $(this).addClass('fa-spinner').removeClass('fa-upload')
      url = "{{ url('products') }}/" + id + '/listMagento';
      $.ajax({
          type: 'POST',
          url: url,
          data: {
              _token: "{{ csrf_token() }}",
          },
          beforeSend: function () {
              // $(thiss).text('Loading...');
              // $(thiss).html('<i class="fa fa-spinner" aria-hidden="true"></i>');
          }
      }).done(function (response) {
          thiss.removeClass('fa-spinner').addClass('fa-upload')
          toastr['success']('Request Send successfully', 'Success')
          $('#product' + id).hide();
      }).fail(function (response) {
          console.log(response);
          thiss.removeClass('fa-spinner').addClass('fa-upload')
          toastr['error']('Internal server error', 'Failure')
          $('#product' + id).hide();
          //alert('Could not update product on magento');
      })
  });

  $(document).on('click', '.get-translation-product', function () {
      $this = $(this);
      var id = $(this).data('id');
      var thiss = $(this);
      url = "{{ url('products') }}/" + id + '/get-translation-product';
      $.ajax({
          type: 'GET',
          url: url,
          data: {
              _token: "{{ csrf_token() }}",
          },
          beforeSend: function () {
              $("#loading-image").show();
          }
      }).done(function (response) {
          $("#loading-image").hide();
          $("#product-translation-modal").find(".modal-body").html(response);
          $("#product-translation-modal").modal("show");
      }).fail(function (response) {
          $("#loading-image").hide();
      })
  });




  /** infinite loader **/
	/*var isLoading = false;
	var page = 1;
	$(document).ready(function () {
		$(window).scroll(function() {
			if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
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
				url: "/logging/list-magento?type=product_log_list&page="+page,
				type: 'GET',
				data: $('.handle-search').serialize(),
				beforeSend: function() {
					$loader.show();
				},
				success: function (data) {
					//console.log(data);
					$loader.hide();
					$('.infinite-scroll-pending-inner').append(data.tbody);
					isLoading = false;
					if(data.tbody == "") {
						isLoading = true;
					}
				},
				error: function () {
					$loader.hide();
					isLoading = false;
				}
			});
		}
	});*/
	//End load more functionality
</script>
@if (Session::has('errors'))
  <script>
  toastr["error"]("{{ $errors->first() }}", "Message")
</script>
@endif
@if (Session::has('success'))
  <script>
  toastr["success"]("{{Session::get('success')}}", "Message")
</script>
@endif

@endsection
