@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', "Magento Modules")

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
	
	/*Toggle Switch*/
	.mml_switch {
		position: relative;
		display: inline-block;
		width: 32px;
		height: 16px;
	}

	.mml_switch input { 
		opacity: 0;
		width: 0;
		height: 0;
	}

	.mml_switch .slider {
		position: absolute;
		cursor: pointer;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: red;
		-webkit-transition: .4s;
		transition: .4s;
	}

	.mml_switch .slider:before {
		position: absolute;
		content: "";
		height: 9px;
		width: 9px;
		left: 4px;
		bottom: 4px;
		background-color: white;
		-webkit-transition: .4s;
		transition: .4s;
	}

	.mml_switch input:checked + .slider {
	background-color: green;
	}

	.mml_switch input:focus + .slider {
	box-shadow: 0 0 1px #2196F3;
	}

	.mml_switch input:checked + .slider:before {
	-webkit-transform: translateX(16px);
	-ms-transform: translateX(16px);
	transform: translateX(16px);
	}

	/* Rounded sliders */
	.mml_switch .slider.round {
	border-radius: 34px;
	}

	.mml_switch .slider.round:before {
	border-radius: 50%;
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
	.ui-widget.ui-widget-content{z-index: 9999;}
</style>
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">
        	Magento Modules ({{$magento_modules_count}})
        	<a style="float: right;" href="{{ route('magento_module_listing_logs') }}" class="btn btn-image" id="" target="_blank">Sync Logs</a>
        	<!-- <a style="float: right;" title="Sync Logs" type="button" id="sync-logs" class="btn btn-image" style="padding: 0px 1px;">Sync Logs</a> -->
        </h2>
		<div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-6">
                    <form action="{{ route('magento_module_listing') }}" method="get" class="search">
                        <div class="row">
                            
                            <div class="col-md-2 pd-sm">
								{{ html()->select('module_name', $allMagentoModules, request()->get('module_name'))->placeholder('Module Name')->class('form-control') }}
                            </div>
							<div class="col-md-3">
								{{ html()->multiselect('store_webs[]', $all_store_websites, $selecteStoreWebsites)->class('form-control  globalSelect22')->placeholder('-- All Website --') }}
							</div>
                            <div class="col-md-2 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('magento_module_listing') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form>
                </div>
				<div class="col-4">
					{{ html()->form('POST', route('magento_module.sync-modules'))->class('form-inline')->open() }}
						<div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
							<select class="form-control websites globalSelect22" name="store_website_id[]" data-placeholder="Please select website" style="width:200px !important;" multiple>
								<option value=""></option>
								@foreach($all_store_websites as $wId => $wTitle)
									<option value="{{ $wId }}">{{ $wTitle }}</option>
								@endforeach
							</select>
						</div> 
						<div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
							<button title="Sync Magento Modules" type="submit" style="" class="btn btn-default"><i class="fa fa-refresh" aria-hidden="true"></i></button>
						</div> 
					{{ html()->form()->close() }} 
				</div>
				<div class="col-2">
					<button type="button" class="btn custom-button float-right mr-3" data-toggle="modal" data-target="#mmdatatablecolumnvisibilityList">Column Visiblity</button>
				</div>
            </div>
        </div>
    </div>
    <br>
    @include('partials.flash_messages', ['extraDiv' => true])
    <div class="col-lg-12 margin-tb">
		<div class="col-md-12 margin-tb" id="page-view-result">
			<div class="row table-horizontal-scroll">
				<table class="table table-bordered" id="env-table">
					<thead>
				      <tr>
                        <th width="20%">Name</th>
                        <th width="20%">Description</th>
				        <?php foreach ($storeWebsites as $storeWebsiteId => $storeWebsiteTitle) { ?>
							<th title="{{$storeWebsiteTitle}}" class="expand-row" style="width: 10% !important;">
							<span class="td-mini-container">
								{{ strlen($storeWebsiteTitle) > 5 ? trim(substr($storeWebsiteTitle, 0, 5)).'...' :  $storeWebsiteTitle }}
							</span>
							<span class="td-full-container hidden">
								{{$storeWebsiteTitle}}
							</span>
							</th >
				        <?php } ?>	
				      </tr>
				    </thead>
				    <tbody id="environment_data">
						<?php
                        if ($magento_modules) {
                            foreach ($magento_modules as $mmkey => $magento_module) { ?>
								<tr class="trrow">
									<td width="10%" class="expand-row">
										<span class="td-mini-container">
											{{ strlen($magento_module->module) > 15 ? substr($magento_module->module, 0, 15).'...' :  $magento_module->module }}
										</span>
										<span class="td-full-container hidden">
											{{$magento_module->module}}
										</span>
									</td>

									<td width="25%" class="expand-row">
										<span class="td-mini-container">
											{{ strlen( $magento_module->module_description) > 25 ? substr( $magento_module->module_description, 0, 25).'...' :  $magento_module->module_description }}
										</span>
										<span class="td-full-container hidden">
											{{ $magento_module->module_description}}
										</span>
									</td>

									<?php
                                    foreach ($storeWebsites as $store_Website_id => $store_website_title) {
                                        $search_array = [];
                                        if (isset($magento_modules_array[$store_Website_id])) {
                                            $search_array = $magento_modules_array[$store_Website_id];
                                        }

                                        $key = array_search($magento_module->module, array_column($search_array, 'module')); ?>

										<td>
											@if($key !== false)
												<?php
                                                    $status = $magento_modules_array[$store_Website_id][$key]['status'];
                                        $magento_module_id = $magento_modules_array[$store_Website_id][$key]['id'];
                                        ?>
												<label class="mml_switch">
													<input type="checkbox" {{ $status ? 'checked' : '' }} class="magento_module_toggle_switch" data-store_Website_id="{{$store_Website_id}}" data-magento_module_id="{{$magento_module_id}}" id="mm_status_{{$store_Website_id}}_{{$magento_module_id}}" name="mm_status_[{{$store_Website_id}}][{{$magento_module_id}}]" value="{{$status}}">
													<span class="slider round"></span>
												</label><br>
												<button type="button" title="History" data-store_Website_id="{{$store_Website_id}}" data-magento_module_id="{{$magento_module_id}}" class="btn btn-history" style="padding: 0px 5px !important;">
													<i class="fa fa-eye" aria-hidden="true"></i>
												</button>

												<button type="button" title="History" data-store_Website_id="{{$store_Website_id}}" data-magento_module_id="{{$magento_module_id}}" class="btn btn-check-status" style="padding: 0px 5px !important;">
													<i class="fa fa-check-circle-o" aria-hidden="true"></i>
												</button>
											@endif
										</td>
									<?php
                                    } ?>
							<?php
                            } ?>
						</tr>
						<?php } ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="preview-history-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 100%;max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Magento Modules Logs</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Command</th>
								<th>Job Id</th>
								<th>Status</th>
								<th>Response</th>
                                <th>Updated By</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody id="preview-history-tbody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('magento_module.partials.list-sync-logs-modal')
@include("magento_module.partials.column-visibility-modal")

<script type="text/javascript" src="{{ mix('webpack-dist/js/jsrender.min.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery-ui.js') }} "></script>
<script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/store-website-brand.js') }} "></script>
<script>
	
	$("#search-input").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		$("#env-table tr.trrow").filter(function() {
		$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});

		var hasVisibleRows = $("#env-table tr:visible").length > 0;
		$("#env-table thead").toggle(hasVisibleRows);
	});

	$( document ).ready(function() {
		$('.globalSelect22').select2({
                multiple: true,
            });
			
		$(document).on('click', '.expand-row', function () {
			var selection = window.getSelection();
			if (selection.toString().length === 0) {
				$(this).find('.td-mini-container').toggleClass('hidden');
				$(this).find('.td-full-container').toggleClass('hidden');
			}
		});
		$(document).on("click",".magento_module_toggle_switch",function(event){
			event.preventDefault();
			var var_return=false;
			var ele=$(this);
			console.log(ele.attr('class'))
			var status=0;
			var temp_m="Are you sure you want to disable the Magento Module?"
			if ($(this).is(':checked')) {
				var status=1;
				var temp_m="Are you sure you want to enable the Magento Module?"
			}
			
			if(confirm(temp_m)){
				var store_website_id=$(this).attr("data-store_website_id");
				var magento_module_id=$(this).attr("data-magento_module_id");
				let formData = new FormData();

				formData.append('_token', "{{ csrf_token() }}");
				formData.append('store_website_id', store_website_id);
				formData.append('magento_module_id', magento_module_id);
				formData.append('status', status);

				//var_return=true;
				$.ajax({
					url:"{{route('magentoModuleUpdateStatus')}}",
					method: 'post',
					data: formData,
					processData: false,
					contentType: false,
					//enctype: 'multipart/form-data',
					dataType: 'json',
					beforeSend: function () {
						$("#loading-image").show();
					}
				}).done(function (response) {
					$("#loading-image").hide();
					
					if (response.code == 200) {
						if(response.data){
							$.each( response.data, function( key, value ) {
								if (value.code == 200) {
									toastr['success'](value.message);
									var checkele=$("#mm_status_"+value.store_website_id+"_"+value.magento_module_id);
									if(checkele.length){
										if (checkele.is(':checked')) {
											checkele.prop('checked', false);
										}else{
											checkele.prop('checked', true);
										}
									}
								}else{
									toastr['error'](value.message);
								}
							})
						}else{
							if (response.code == 200) {
								toastr['success'](response.message);
								if (ele.is(':checked')) {
									ele.prop('checked', false);
								}else{
									ele.prop('checked', true);
								}
							}else{
								toastr['error'](response.message);
							}
						}
						
					} else {
						toastr['error'](response.message);
					}
				}).fail(function () {
					console.log("error");
					$("#loading-image").hide();
				});
			}
		});
		$(document).on("click", ".btn-history", function (e) {
			e.preventDefault();
			let page = $(this).data("id");
			
			var store_website_id=$(this).attr("data-store_website_id");
			var magento_module_id=$(this).attr("data-magento_module_id");
			let formData = new FormData();

			formData.append('_token', "{{ csrf_token() }}");
			formData.append('store_website_id', store_website_id);
			formData.append('magento_module_id', magento_module_id);
			

			$.ajax({
				url: "/magento_modules/update-status/logs",
				method: 'post',
				data: formData,
				processData: false,
				contentType: false,
				//enctype: 'multipart/form-data',
				dataType: 'json',
				beforeSend: function () {
					$("#loading-image").show();
				}
			}).done(function (response) {
				$("#loading-image").hide();
				
				var html = '';
				if (response.code == 200) {
					$.each(response.data, function (k, v) {
						var user = v.user ? v.user.name : "";
						html +=
						`<tr>
									<td>` +
						v.id +
						`</td>
									<td>` +
						v.command +
						`</td>
									<td>` +
						v.job_id +
						`</td>
									<td>` +
						v.status +
						`</td>
									<td>` +
						v.response +
						`</td>
									<td>` +
						v.userName +
						`</td>
									<td>` +
						v.created_at +
						`</td>
								</tr>`;
					});
					$("#preview-history-tbody").html(html);
					$("#loading-image").hide();
					$(".preview-history-modal").modal("show");
				}
				
			}).fail(function () {
				console.log("error");
				$("#loading-image").hide();
				
			});
			
		});

		$(document).on("click", ".btn-check-status", function (e) {
			e.preventDefault();
			let page = $(this).data("id");
			
			var store_website_id=$(this).attr("data-store_website_id");
			var magento_module_id=$(this).attr("data-magento_module_id");
			let formData = new FormData();

			formData.append('_token', "{{ csrf_token() }}");
			formData.append('store_website_id', store_website_id);
			formData.append('magento_module_id', magento_module_id);
			

			$.ajax({
				url: "/magento_modules/check-status",
				method: 'post',
				data: formData,
				processData: false,
				contentType: false,
				//enctype: 'multipart/form-data',
				dataType: 'json',
				beforeSend: function () {
					$("#loading-image").show();
				}
			}).done(function (response) {
				$("#loading-image").hide();
				
				
			}).fail(function () {
				console.log("error");
				$("#loading-image").hide();
				
			});
			
		});
	});

	$(document).on('click','#sync-logs',function(e){
        e.preventDefault();
        $('#sync-logs-modal').modal('show');
        getSyncLogs(1);
    });

    function getSyncLogs(pageNumber = 1) {

    	var module_name_sync = $("#module_name_sync").val(); // selected

    	var selected_date = $("#selected_date").val(); // selected

        $.ajax({
            type: "GET",
            headers: {
	            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          	},
            url: "{{route('magento_modules.ajax-sync-logs')}}",
            data: {
	            page: pageNumber,
	            module_name_sync: module_name_sync,
	            selected_date: selected_date
          	},
          	dataType: "json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();

			var html = "";
			var startIndex = (response.data.current_page - 1) * response.data.per_page;
			$.each(response.data.data, function (index, cronData) {
				var sNo = startIndex + index + 1; 
				html += "<tr>";
					@if(!empty($dynamicColumnsToShow))
						@if (!in_array('Id', $dynamicColumnsToShow))
							html += "<td>" + sNo + "</td>";
						@endif

						@if (!in_array('Module Name', $dynamicColumnsToShow))
							html += "<td>" + cronData.module + "</td>";
						@endif

						@if (!in_array('Command', $dynamicColumnsToShow))
							html += "<td>" + cronData.command + "</td>";
						@endif

						@if (!in_array('Job Id', $dynamicColumnsToShow))
							if(cronData.job_id!=null){
								html += "<td>" + cronData.job_id + "</td>";
							} else {
								html += "<td>-</td>";
							}
						@endif

						@if (!in_array('Status', $dynamicColumnsToShow))
							html += "<td>" + cronData.status + "</td>";
						@endif

						@if (!in_array('Response', $dynamicColumnsToShow))
							html += "<td>" + cronData.response + "</td>";
						@endif

						@if (!in_array('Created At', $dynamicColumnsToShow))
							html += "<td>" + $.datepicker.formatDate('yy-mm-dd', new Date(cronData.created_at)) + "</td>";
						@endif

						@if (!in_array('Updated At', $dynamicColumnsToShow))
							html += "<td>" + $.datepicker.formatDate('yy-mm-dd', new Date(cronData.updated_at)) + "</td>";
						@endif
					@else
						html += "<td>" + sNo + "</td>";
						html += "<td>" + cronData.module + "</td>";
						html += "<td>" + cronData.command + "</td>";

						if(cronData.job_id!=null){
							html += "<td>" + cronData.job_id + "</td>";
						} else {
							html += "<td>-</td>";
						}
						html += "<td>" + cronData.status + "</td>";
						html += "<td>" + cronData.response + "</td>";
						html += "<td>" + $.datepicker.formatDate('yy-mm-dd', new Date(cronData.created_at)) + "</td>";
						html += "<td>" + $.datepicker.formatDate('yy-mm-dd', new Date(cronData.updated_at)) + "</td>";
					@endif

				html += "</tr>";
			});
			$("#sync_logs_list_table_data").html(html);
			$("#sync-logs-modal").modal("show");
			renderMangetoErrorPaginationSync(response.data);

        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

    function renderMangetoErrorPaginationSync(data) {
        var paginationContainer = $(".pagination-container-sync");
        var currentPage = data.current_page;
        var totalPages = data.last_page;
        var html = "";
        var maxVisiblePages = 10;

        if (totalPages > 1) {
            html += "<ul class='pagination'>";
            if (currentPage > 1) {
            html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeMagnetoSyncLogs(" + (currentPage - 1) + ")'>Previous</a></li>";
            }

            var startPage = 1;
            var endPage = totalPages;

            if (totalPages > maxVisiblePages) {
            if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
                endPage = maxVisiblePages;
            } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
                startPage = totalPages - maxVisiblePages + 1;
            } else {
                startPage = currentPage - Math.floor(maxVisiblePages / 2);
                endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
            }

            if (startPage > 1) {
                html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeMagnetoSyncLogs(1)'>1</a></li>";
                if (startPage > 2) {
                html += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                }
            }
            }

            for (var i = startPage; i <= endPage; i++) {
            html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changeMagnetoSyncLogs(" + i + ")'>" + i + "</a></li>";
            }
            html += "</ul>";
        }
        paginationContainer.html(html);
    }

    function changeMagnetoSyncLogs(pageNumber) {
        getSyncLogs(pageNumber);
    }

</script> 

@endsection

