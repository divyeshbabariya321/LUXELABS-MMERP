@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}
	.push-brand {
		height: 14px;
	}
	.icon-log-history {
		margin-top: -7px !important;
		display: flex;
		/*display: table-caption;*/
	}
	#page-view-result table tr th:last-child,
	#page-view-result table tr th:nth-last-child(2) {
		width: 50px !important;
		min-width: 50px !important;
		max-width: 50px !important;
	}
	#page-view-result th,#page-view-result td {
    	white-space: nowrap;
	}
	#page-view-result th br, div#page-view-result td br{
    	display: none;
	}

</style>
<style>
	.loader-small {
		border: 2px solid #b9b7b7;
		border-radius: 50%;
		border-top: 4px dotted #4e4949;
		width: 21px;
		height: 21px;
	  	-webkit-animation: spin 2s linear infinite; /* Safari */
	  	animation: spin 2s linear infinite;
	  	float: left;
		margin: 8px;
		display: none;
	}
	
	/* Safari */
	@-webkit-keyframes spin {
	  0% { -webkit-transform: rotate(0deg); }
	  100% { -webkit-transform: rotate(360deg); }
	}
	
	@keyframes spin {
	  0% { transform: rotate(0deg); }
	  100% { transform: rotate(360deg); }
	}
	</style>
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{ $title }} (<span id="count">{{ $brands->count() }}</span>)</h2>
    </div>
    <br>
    @if(session()->has('success'))
	    <div class="col-lg-12 margin-tb">
		    <div class="alert alert-success">
		        {{ session()->get('success') }}
		    </div>
		</div>    
	@endif
    <div class="col-lg-12 margin-tb">
    	<div class="row" style="margin-bottom: 10px;">
	    	<div class="col col-md-3">
		    	<div class="row">

		  			<form class="form-inline message-search-handler" action="{{route('store-website.brand.list')}}" method="get">
		  				<input type="hidden" name="push" value="1">
						<div class="form-group" style="margin-left: 30px;">
						    <label for="keyword" style="justify-content: start;">Store Wesbite:</label>
                            {{ html()->select('store_website_id', \App\StoreWebsite::pluck('title', 'id')->toArray())->class('form-control select2')->placeholder('Select Website')->value(request('store_website_id')) }}
                        </div>
						<button style="margin-top: 22px; padding-right:0px;" type="button" class="btn btn-xs show-reconsile-history" title="Show History" data-type_history="push-brand"><i class="fa fa-info-circle"></i></button>
				  		&nbsp;
						<div class="form-group">
					  		<label for="button">&nbsp;</label>
					  		<button type="submit" class="btn btn-secondary">
					  			Push Brand
					  		</button>
					  	</div>		
			  		</form>
				 </div>
		    </div>
		    <div class="col col-md-3 reconsile-brand-form">
				<div class="loader-small"></div>
					<div class="h" style="margin-bottom:10px;">
					<div class="row">
						<div class="form-group mb-0">
							<label for="keyword" style="justify-content: start;">Store Wesbite:</label>
                            {{ html()->select('store_website_id[]', \App\StoreWebsite::pluck('title', 'id')->toArray())->class('form-control select2 store-website-id')->placeholder('Select Website')->multiple(true)->attribute('data-placeholder', 'Select Websites')->value(request()->input('store_website_id')) }}
                        </div>
						<button style="margin-top: 22px; padding-right:0px;" type="button" class="btn btn-xs show-reconsile-history" title="Show History" data-type_history="reconsile"><i class="fa fa-info-circle"></i></button>
						&nbsp;
						<div class="form-group mb-0" style="margin-top: 22px;">
							<button class="btn btn-secondary btn-reconsile-brand">Reconsile</button>
						</div>
					</div>
		    	</div>

		    </div>

			<div class="col col-md-6">
				<form class="form-inline message-search-handler handle-search" method="get">
					<div class="col">
						<div class="form-group">
							<label for="keywords">Search keywords</label>
							{{ html()->text('keyword', request('keyword'))->class('form-control')->placeholder('Enter keyword') }}
						</div>

						<div class="form-group ml-3 text-center"><br>
							<label for="no-inventory">No Inventory</label>
							<input type="checkbox" name="no-inventory" value="1" {{ request()->has('no-inventory') ? 'checked' : '' }} />
						</div>


						<div class="form-group ml-3">
							<label for="category">Search Category</label>
							{{ html()->select('category_id', $categories)->class('form-control select2')->placeholder('Select Category')->value(request('category_id')) }}
						</div>

						<div class="form-group ml-3">
							<label for="websites">Search store websites</label>
							{{ html()->select('brd_store_website_id[]', $storeWebsite)->class('form-control globalSelect2')->multiple()->attribute('data-placeholder', 'Select Store Websites')->value(request()->input('brd_store_website_id')) }}
						</div>

						<div class="form-group ml-3 text-center"><br>
							<label for="no_brand">No Available brand</label>
							<input type="checkbox" name="no_brand" value="1" {{ request()->has('no_brand') ? 'checked' : '' }} />
						</div>
						<div class="form-group">
							<label for="button">&nbsp;</label>
							<button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
								<img src="/images/search.png" style="cursor: default;">
							</button>
							<a href="{{route('store-website.brand.list')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>

							<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#datatablecolumnvisibilityList">Column Visiblity</button>
						</div>
					</div>
				</form>
			</div>

	    </div>
		<div class="col col-md-6">
			<div class="row">
				<button style="display: inline-block;width: 10% " class="btn btn-sm btn-image btn-add-action">
					<img src="/images/add.png" style="cursor: default;">
				</button>
			</div>
		</div>
		<div class="col-md-12 margin-tb" id="page-view-result">
			<div class="row table-horizontal-scroll">
				<table class="table table-bordered">
				<thead>
			      	<tr>
				      	@if(!empty($dynamicColumnsToShow))
					      	@if (!in_array('Id', $dynamicColumnsToShow))
					      		<th width="3%">Id</th>
				      		@endif

				      		@if (!in_array('Brand', $dynamicColumnsToShow))
					        <th width="10%">Brand</th>
					        @endif

					        @if (!in_array('Min Price', $dynamicColumnsToShow))
					        	<th width="5%">Min Price</th>
				        	@endif

				        	@if (!in_array('Max Price', $dynamicColumnsToShow))
					        	<th width="5%">Max Price</th>
				        	@endif

					        <?php
                            foreach ($storeWebsite as $k => $title) {

                                if (! in_array($k, $dynamicColumnsToShow)) {

                                    $title = str_replace(' & ', '&', $title);
                                    $title = str_replace(' - ', '-', $title);
                                    $title = str_replace('&', ' & ', $title);
                                    $title = str_replace('-', ' - ', $title);
                                    $words = explode(' ', $title);
                                    $is_short_title = 0;
                                    if (count($words) >= 2) {
                                        $title = '';
                                        foreach ($words as $word) {
                                            $title .= strtoupper(substr($word, 0, 1));
                                        }
                                        $is_short_title = 1;
                                    } ?>

						        	<th data-id="{{$k}}" width="4%">
										<?php echo $title; ?>
										<br>
						        		<a class="brand-history btn p-0"  data-id="{{$k}}" href="javascript:;" ><i class="fa fa-info-circle" aria-hidden="true"></i></a>
						        		<a class="missing-brand-history text-dark" data-id="{{$k}}" href="javascript:;" ><i class="fa fa-close" aria-hidden="true"></i></a>
						        	</th>
					        <?php
                                }
                            } ?>	
				    	@else 
					      	<th width="3%">Id</th>
				      		
					        <th width="10%">Brand</th>
					        
					        <th width="5%">Min Price</th>
				        	
					        <th width="5%">Max Price</th>
				        	
					        <?php
                            foreach ($storeWebsite as $k => $title) {

                                $title = str_replace(' & ', '&', $title);
                                $title = str_replace(' - ', '-', $title);
                                $title = str_replace('&', ' & ', $title);
                                $title = str_replace('-', ' - ', $title);
                                $words = explode(' ', $title);
                                $is_short_title = 0;
                                if (count($words) >= 2) {
                                    $title = '';
                                    foreach ($words as $word) {
                                        $title .= strtoupper(substr($word, 0, 1));
                                    }
                                    $is_short_title = 1;
                                } ?>
					        	<th data-id="{{$k}}" width="4%">
									<?php echo $title; ?>
									<br>
					        		<a class="brand-history btn p-0"  data-id="{{$k}}" href="javascript:;" ><i class="fa fa-info-circle" aria-hidden="true"></i></a>
					        		<a class="missing-brand-history text-dark" data-id="{{$k}}" href="javascript:;" ><i class="fa fa-close" aria-hidden="true"></i></a>
					        	</th>
					        <?php
                            } ?>
				    	@endif
			      	</tr>
				    </thead>
				    <thead>
				      <tr>

				      	@php
			      	 	$colspan = 4;
			      	 	if(!empty($dynamicColumnsToShow)){
				      	 	if(in_array('Id', $dynamicColumnsToShow)){
					      		$colspan =  ($colspan-1);
				      	 	}
					      	
					      	if(in_array('Brand', $dynamicColumnsToShow)){
					      		$colspan =  ($colspan-1);
				      	 	}

				      	 	if(in_array('Min Price', $dynamicColumnsToShow)){
					      		$colspan =  ($colspan-1);
				      	 	}

				      	 	if(in_array('Max Price', $dynamicColumnsToShow)){
					      		$colspan =  ($colspan-1);
				      	 	}
			      	 	}
			      	 	@endphp

				      	<th colspan="{{$colspan}}"></th>
				        <?php
                        if (! empty($dynamicColumnsToShow)) {
                            foreach ($storeWebsite as $k => $title) {
                                if (! in_array($k, $dynamicColumnsToShow)) { ?>
									<th data-id="{{$k}}" width="4%">
										<?php if (isset($apppliedResultCount[$k])) { ?> 
										{{count($apppliedResultCount[$k])}}
										<?php } ?>
									</th>
					        <?php
                                }
                            }
                        } else {
                            foreach ($storeWebsite as $k => $title) { ?>
								<th data-id="{{$k}}" width="4%">
									<?php if (isset($apppliedResultCount[$k])) { ?> 
									{{count($apppliedResultCount[$k])}}
									<?php } ?>
								</th>
					        <?php
                            }
                        } ?>	
				      </tr>
				    </thead>
				    <tbody id="brand_data">
				    	@include("storewebsite::brand.partials.brand_data")
				    </tbody>
				</table>
				 <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
			</div>
		</div>
	</div>

<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
  	<div class="modal-dialog" role="document">
  	</div>	
</div>


<div id="HistoryModal" class="modal fade" role="dialog">
  	<div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
	        <div class="modal-header">
	        	<h4 class="modal-title">History</h4>
	          	<button type="button" class="close" data-dismiss="modal">&times;</button>
	        </div>
	        <div class="modal-body"></div>
    	</div>
	</div>
</div>

<div id="brand-live-data" class="modal fade" role="dialog">
  	<div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
	        <div class="modal-header">
	        	<h4 class="modal-title">Brand Live<span class="brand-live-data-count"></span></h4>
	          	<button type="button" class="close" data-dismiss="modal">&times;</button>
	        </div>
	        <div class="modal-body"></div>
    	</div>
	</div>
</div>

<div id="brand-historys" class="modal fade" role="dialog">
	<div class="modal-dialog">
	  <!-- Modal content-->
	  <div class="modal-content">
		  <div class="modal-header">
			  <h4 class="modal-title"></h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
		  </div>
		  <div class="modal-body brand-historys-data"></div>
	  </div>
  </div>
</div>

<div id="missing-live-data" class="modal fade" role="dialog">
  	<div class="modal-dialog">
	    <!-- Modal content-->
	    
	</div>
</div>

@include("storewebsite::brand.partials.column-visibility-modal")
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript" src="/js/store-website-brand.js"></script>

<script type="text/javascript">
	page.init({
		bodyView : $("#common-page-layout"),
		baseUrl : "<?php echo url('/'); ?>"
	});

	function refresh() {
		$.ajax({
            url: '{{ route('store-website.refresh-min-max-price')}}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
            },beforeSend: function() {
              
            },
            success: function(response) {
                $("#loading-image").hide();
                alert(response);
            }
        });
	}
	jQuery(document).ready(function(){
		jQuery(".log_history").on("click",function(){
			$("#loading-image").show();
			var _this = jQuery(this);
			$.ajax({
	            url: jQuery(_this).data('href'),
	            type: 'GET',
	            success: function(response) {
	                jQuery("#loading-image").hide();
	                jQuery("#HistoryModal .modal-body").html(response);
	                jQuery("#HistoryModal").modal("show");
	            },
	            error: function(response) {
					toastr["error"]("Something went wrong, please try after sometime.", "error");
	            }
        	});
		});
	});

	$(document).on("click",".brand-history",function(e) {
		e.preventDefault();
		var $this = $(this);
		$.ajax({
            url: "/store-website/brand/live-brands",
            type: 'GET',
            data : {
            	store_website_id: $this.data("id")
            },
            beforeSend : function() {
            	$("#loading-image").show();
            },
            success: function(response) {
            	$("#loading-image").hide();
                $("#brand-live-data").find(".modal-dialog").html(response);
                $("#brand-live-data").modal("show");
            },
            error: function(response) {
                $("#loading-image").hide();
				toastr["error"](response.responseJSON.message);
            }
    	});
	});
	
	$(document).on("click",".push-brand",function(e) {
		e.preventDefault();
		var ele = $(this);
		var brand = ele.data("brand");
        var store = ele.data("sw");
		$.ajax({
            url: "/store-website/brand/push-to-store",
            type: 'POST',
            data : {
				_token: '{{ csrf_token() }}',
            	brand: brand,
            	store: store,
				active : ele.is(":checked")
            },
            beforeSend : function() {
            	$("#loading-image").show();
            },
            success: function(response) {
				$.each(response.storeWebsites, function (index, item) {
					let element = $('.push-brand[data-brand="' + brand + '"][data-sw="'+item+'"]');
					if(ele.is(":checked") && store !== item) {
						element.prop('checked', true);
					} else if(!ele.is(":checked") && store !== item) {
						element.prop('checked', false);
					}
				});
            	$("#loading-image").hide();
				if($('#'+ele.attr('id')).prop("checked") == true){
					$('#'+ele.attr('id')).prop('checked', false);
				}
				else if($('#'+ele.attr('id')).prop("checked") == false){
					$('#'+ele.attr('id')).prop('checked', true);
				}
				//alert(response.message);
				toastr["success"](response.message);
            },
            error: function(response) {
                $("#loading-image").hide();
				toastr["error"](response.message);
            	//alert(response.message);
            }
    	});
	});

	$(document).on("click",".missing-brand-history",function(e) {
		e.preventDefault();
		var $this = $(this);
		$.ajax({
            url: "/store-website/brand/missing-brands",
            type: 'GET',
            data : {
            	store_website_id: $this.data("id")
            },
            beforeSend: function() {
              $("#loading-image").show();
            },
            success: function(response) {
            	$("#loading-image").hide();
                $("#missing-live-data").find(".modal-dialog").html(response);
                $("#missing-live-data").modal("show");
            },
            error: function(response) {
            	$("#loading-image").hide();
				toastr["error"](response.responseJSON.message);
            }
    	});
	});

    $(document).on("click",".btn-reconsile-brand",function(e) {
        e.preventDefault();
        if(confirm("Are you sure you want to do reconsile?")) {
            var $this = $(this);
            var swi = $(".reconsile-brand-form").find(".store-website-id").val();
            $.ajax({
                url: "/store-website/brand/reconsile-brand",
                type: 'POST',
                data : {
                    store_website_id: swi,
                    _token : "{{ csrf_token() }}"
                },
                beforeSend: function() {
                  $(".loader-small").show();
                },
                success: function(response) {
                    $(".loader-small").hide();
                    if(response.code == 200) {
                        toastr["success"](response.message);
                    }else{
                        toastr["error"](response.message);
                    }
                },
                error: function(response) {
                    $(".loader-small").hide();
                    toastr["error"]("Oops, something went wrong");
                }
            });
        }
    });
//START - Load More functionality
	var isLoading = false;
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
				url: "/store-website/brand?page="+page,
				type: 'GET',
				data: $('.handle-search').serialize(),
				beforeSend: function() {
					$loader.show();
				},
				success: function (data) {
					$loader.hide();					
					$('#brand_data').append(data.tbody);
					isLoading = false;
					if(data.tbody == "") {
						isLoading = true;
					} else {
						var total_count = Number($('#count').html()) + Number(data.count);
						$('#count').html(total_count);
					}
				},
				error: function () {
					$loader.hide();
					isLoading = false;
				}
			});
		}
	});
	//End load more functionality

	
	$(document).on("click",".show-reconsile-history",function(e) {
	    e.preventDefault();
		var $this = $(this);
		var type_history = $this.data("type_history");
		if(type_history == 'reconsile'){
			var urls = "/store-website/brand/reconsile-brand-history-log"; 
			var title = 'Reconsile Brand History Log'
		} else {
			var urls = "/store-website/brand/push-brand-history-log"; 
			var title = 'Push Brand History Log'
		}
		$.ajax({
			url: urls,
			type: 'POST',
			data : {
				_token : "{{ csrf_token() }}",
			},
			beforeSend: function() {
				$("#loading-image").show();
			},
			success: function(response) {
				$("#loading-image").hide();
				if(response.code == 200) {
					
					toastr["success"](response.message);
					var t = '';
					t += '<table class="table table-bordered">';
					t += '<tr>';
					t += '<th>ID</th><th>Store Webite ID</th><th>Error Type</th><th>Error</th><th>Date</th>';
					t += '</tr>'
					$.each(response.data,function(k,v) {
						t += `<tr><td>`+v.id+`</td>`;
						t += `<td>`+v.websiteName+`</td>`;
						t += `<td>`+v.error_type+`</td>`;
						t += `<td>`+v.error+`</td>`;
						t += `<td>`+v.created_at+`</td></tr>`;
					});
					t += '</table>';
					
					$("#brand-historys").find(".modal-body").html("");
					$("#brand-historys").find(".modal-body").html(t);
					$("#brand-historys").find(".modal-title").html(title);
					$('#brand-historys').modal("show");
					$("#loading-image").hide();
				}else{
					$("#loading-image").hide();
					toastr["error"](response.message);
				}
			},
			error: function(response) {
				$("#loading-image").hide();
				toastr["error"]("Oops, something went wrong");
			}
		});
    });
</script>

@endsection

