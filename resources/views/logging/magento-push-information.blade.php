@extends('layouts.app')

@section('title', 'Log List Magento')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <style type="text/css">
  #loading-image {
    position: fixed;
    top: 50%;
    left: 50%;
    margin: -50px 0px 0px -50px;
    z-index: 100000
  }
  input {
    width: 100px;
  }

	th,td{
    word-break: break-word
  	}
	.btn-secondary{
		border: 1px solid #ddd;
		color: #757575;
		background: #fff !important;
		padding: 5px 10px !important;
	}
	.fa-info-circle{
		cursor: pointer;
	}

</style>
@endsection

@section('content')
  <div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
  </div>
  <div class="row">
        <div class="col-lg-12 margin-tb">
             <h2 class="page-heading">Product information update ({{ $total_count }})
						
						  <div class="pull-right">
								<button type="button" class="btn btn-xs btn-secondary read_csv_file" data-toggle="modal" data-target="#product-push-information-summery">
									Summery  
								</button>
								<button type="button" class="btn btn-xs btn-secondary read_csv_file" data-toggle="modal" data-target="#product-push-infomation-modal">
										Read csv    
								</button>
						</div>
						
						</h2>
                
         </div>


				 <div class="col-10" style="padding-left:0px;padding-bottom:10px;">
					<div class="pl-4">
							<form class="form-inline" action="" method="GET">
									<div class="form-group mr-3">
											
											<input style="width:100%;" name="filter_product_id" type="text" class="form-control" value="{{ isset($_REQUEST['filter_product_id']) ? $_REQUEST['filter_product_id'] : '' }}" placeholder="Product id">
									</div>
									<div class="form-group mr-3">
											<input style="width:100%;" name="filter_product_sku" type="text" class="form-control" value="{{ isset($_REQUEST['filter_product_sku']) ? $_REQUEST['filter_product_sku'] : '' }}" placeholder="SKU">
									</div>
									<div class="form-group mr-3">  
                      <select name="filter_product_status" class="form-control">
                          <option value="">Select status</option>

                              @foreach ($dropdownList as $item)
                                  
                              <option value="{{ $item['status'] }}" {{ (isset($_REQUEST['filter_product_status']) &&( $_REQUEST['filter_product_status']==$item['status'] )) ? 'selected' : '' }}>{{ $item['status'] }}</option>
                              @endforeach

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
                      <select class="form-control globalSelect2" data-placeholder="Select Categories" data-ajax="{{ route('select2.categories',['sort'=>true]) }}"
                      name="category_names[]" multiple>
                      <option value="">Select Categories</option>
                          @if ($selected_categories)        
                              @foreach($selected_categories as $category)
                                  <option value="{{ $category->id }}" selected>{{ $category->title }}</option>
                              @endforeach
                          @endif
                      </select>
                  </div> 
                  <div class="form-group mr-3">
                      <select class="form-control globalSelect2" data-placeholder="Select Website" data-ajax="{{ route('select2.websites',['sort'=>true]) }}"
                      name="website_name" >
                      <option value="">Select Website</option>
                          @if (isset($selected_website))        
                                <option value="{{ $selected_website->id }}" selected>{{ $selected_website->title }}</option>
                          @endif
                      </select>
                  </div> 
									<div class="form-group col-md-1 pd-3">
											<button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>

											<a href="{{ route('list.magento.product-push-information') }}" class="fa fa-refresh" aria-hidden="true"></a>
									</div>
							</form>
					</div>
			</div>

				 

      


  <div class="col-md-12 px-5">
    <div class="panel panel-default">

      <div class="panel-body p-0">

        <div class="table-responsive">
          <table id="magento_list_tbl_895" class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
             
							<th>Product Id</th>
              <th>Website</th>
              <th>Brand </th>
              <th>Category</th>
							<th>Sku</th>
							<th>Status</th>
							<th>Is Available</th>
							<th>Pushed by erp</th>
							<th>Quantity</th>
							<th>Stock Status</th>
							<th>Action</th>

            </thead>
            <tbody>
              @foreach($logListMagentos as $item)
              
							<tr data-id="{{ $item->store_website_id }}">

								<td>{{ $item->product_id }}</td>
                <td>{{$item && $item->storeWebsite ? $item->storeWebsite->title : '' }}</td>
          
                <td>{{ $item->product()->count() && $item->product->brands ? $item->product->brands->name : '' }}</td>
                <td>{{   $item->product()->count() && $item->product->categories ? $item->product->categories->title : '' }}</td>

								<td>{{ $item->sku }}</td>
								<td>{{ $item->status }}</td>
								<td>{{ $item->is_available ? 'Yes'  :'No'}}</td>
								<td>{{ $item->is_added_from_csv ? 'No'  :'Yes'}}</td>
								<td>{{ $item->quantity }}</td>
								<td>{{ $item->stock_status }}</td>

								<td>
									

									<i class="fa fa-info-circle show-histories" data-product-id={{$item ? $item->product_id  :''}} title="Status Logs" aria-hidden="true" data-id="107" data-name="Status"></i>
								</td>

							</tr>

              @endforeach()
            </tbody>
          </table>

          <div class="text-center">
            {!! $logListMagentos->links() !!}
          </div>
        </div>
      </div>
    </div>
  </div>


 {{-- product information  --}}
  <div class="modal fade" id="product-push-infomation-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel">Choose Product CSV</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

            <form action="{{ route('update.magento.product-push-website') }}" method="POST" id="store-product-push-website">
                @csrf

                @foreach ($allWebsiteUrl as $website)
             
                  <div class="d-flex justify-content-between main-row">
                        <div class="form-group mr-3" style="width: 20%">
                            <input type="text" class="form-control"  value="{{ $website->title }}" readonly>
                        </div>
                        <div class="form-group mr-2" style="width: 70%">
                            <input type="url" class="form-control website_url" name="{{ $website->id }}" value="{{ isset($website->productCsvPath) ? $website->productCsvPath->path : '' }}" >
                        </div>
                        <div class="form-group" style="width: 10%">
                          <button type="button" data-store_website_id="{{ $website->id }}" class="btn  store-product-push-website" ><img src="/images/filled-sent.png" width="16px" style="cursor: pointer;">

                          </button>
                        </div>
                    </div>
             
                @endforeach

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn  btn-secondary">Update data</button>
                </div>
            </form>

        </div>

      </div>
    </div>
  </div>

  
	{{-- show histoies modal --}}
	<div class="modal fade" id="show-histories-data-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel">Product History</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
              <table id="show-histories-table" class="table table-bordered table-hover" style="table-layout:fixed;">
                <thead>
                  <th>Product Id</th>
                  <th>Sku</th>
                  <th>Status</th>
                  <th>Quantity</th>
                  <th>Is avilable</th>
                  <th>Pushed by erp</th>
                  <th>Stock Status</th>
                  <th>Updated by</th>
                  <th>Created At</th>
                </thead>
                <tbody class="show-histories-body">
                
                </tbody>
              </table>
        </div>
      </div>
    </div>
  </div>
	{{-- show summery modal--}}
	<div class="modal fade" id="product-push-information-summery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel">Product History</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group col-md-6">
            <strong>Date Range  </strong>
            <input type="hidden" class="range_start_filter" value="<?php echo date("Y-m-d"); ?>" name="range_start" />
            <input type="hidden" class="range_end_filter" value="<?php echo date("Y-m-d"); ?>" name="range_end" />
            <div id="filter_date_range_" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ddd; width: 100%;border-radius:4px;">
                <i class="fa fa-calendar"></i>&nbsp;
                <span></span> <i class="fa fa-caret-down"></i>
            </div>
        </div>
					<table id="show-product-information-summery-table" class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
							<th>Store website</th>
							<th>Brand</th>
							<th>Category</th>
							<th>Product count</th>
							<th>Created At</th>
            </thead>
            <tbody class="show-product-information-summery-body">

                @include('logging.partials.product-push-information-summery')
           
            </tbody>
          </table>
            </form>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

  <script type="text/javascript">
            let r_s = "";
            let r_e = "";

            let start = r_s ? moment(r_s, 'YYYY-MM-DD') : moment().subtract(0, 'days');
            let end = r_e ? moment(r_e, 'YYYY-MM-DD') : moment();

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
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                        'month')]
                }
            }, cb);

            cb(start, end);

            $('#filter_date_range_').on('apply.daterangepicker', function(ev, picker) {

                let startDate = jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
                let endDate = jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));

                $.ajax({
                    type: 'GET',
                    url: "{{ route('update.magento.product-push-information-summery') }}" + '?startDate=' + jQuery(
                        'input[name="range_start"]').val() + '&&endDate=' + jQuery(
                        'input[name="range_end"]').val(),
                    // dataType: "json",
                    success: function(response) {
                            $('.show-product-information-summery-body').html(response);
                        },

                  
                    error: function() {
                        toastr['error']('Could not change module!');
                    }
              });

            });


$(document).on('click','.store-product-push-website',function(e){
      e.preventDefault()

      const website_url = $(this).closest('.main-row').find('.website_url').val()
      
      if(website_url.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g)){
          const sendData = {}
          sendData.website_url = website_url 
          sendData._token = "{{ csrf_token() }}"
          sendData.store_website_id = $(this).data('store_website_id');

              $.ajax({
              method: "POST",
              url: "{{ route('update.magento.product-push-information') }}",
              data: sendData,
              beforeSend: function () {
                            $("#loading-image").show();
                        },
            })
            .done(function(response) {
                  console.log("Data Saved: ", response);

                    if(response.error){
                      toastr['error'](response.error);
                    }else{
                      toastr['success'](response.message);
                setTimeout(() => {
                                      location.reload()
                }, 2000);

                    }
                  $("#loading-image").hide();


            }).fail(function(data) {
                console.log(data)
                $("#loading-image").hide();
                console.log("error");
              });
      }else{
        toastr['error']('Plese enter url');

      }

    
})


$(document).on('submit','#store-product-push-website',function(e){

      e.preventDefault()
      $.ajax({
      method: "POST",
      url: "{{ route('update.magento.product-push-website') }}",
      data: $(this).serialize(),
      beforeSend: function () {
                    $("#loading-image").show();
                },
    })
    .done(function(response) {
          console.log("Data Saved: ", response);

            if(response.error){
              toastr['error'](response.error);
            }else{
              toastr['success'](response.message);

              // location.reload()
            }
          $("#loading-image").hide();


    }).fail(function(data) {
				console.log(data)
				$("#loading-image").hide();
				console.log("error");
			});
    
})





$(document).on('change','#websites',function(){
    $('#website-path').val($(this).val())
})


$(document).on('click','.show-histories',function(){

const website_id = $(this).closest('tr').data('id')
console.log(website_id)

	$.ajax({
    headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
      method:'POST',
      url: "/logging/list-magento/product-push-histories/"+ $(this).data('product-id') ,
      data:{website_id}
    })
    .done(function(response) {
				let html = null
				if(response.length){

				response.forEach((element)=>{
					console.log(element)
					const final_html=	`
								<tr>
									<td style="word-break: break-word;">${element.product_id }</td>
									<td style="word-break: break-word;">${element.sku ?? element.old_sku}</td>
									<td style="word-break: break-word;">${element.status ?? element.old_status}</td>
									<td style="word-break: break-word;">${element.quantity ?? element.old_quantity}</td>
									<td style="word-break: break-word;">${element.is_available ?'Yes' :'No' }</td>
									<td style="word-break: break-word;">${element.is_added_from_csv ? "No" :'Yes'}</td>
									<td style="word-break: break-word;">${element.stock_status ?? element.old_status	}</td>
									<td style="word-break: break-word;">${element.user?.name ?? 'command'}</td>
									<td style="word-break: break-word;">${element.created_at ?? ''}</td>
							</tr>
							`
					html+=final_html

				})

				$('.show-histories-body').html(html)
			}

		});
		$('#show-histories-data-modal').modal('show')
})


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