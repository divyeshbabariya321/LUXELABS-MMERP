@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', "Magento Modules")

@section('content')
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Magento Modules Logs ({{$magento_modules_count}})</h2>
    </div>
    <br>
	@include('partials.flash_messages', ['extraDiv' => true])
	<div class="col-lg-12" style="margin: 10px;">
        <form method="get" id="screen_cast_search" style="margin-left:auto">
            <div class="row">
                <div class="col-lg-4">
                    <label style=" width: 100%;">Module Name</label>
                    {{ html()->select('module_name_sync', $allMagentoModules, request()->get('module_name_sync'))->placeholder('Module Name')->class('form-control')->id('module_name_sync') }} 
                </div>    

                <div class="col-lg-4">
                    <label style=" width: 100%;">Date</label>
                    <input type="date" name="selected_date" style="width: 100%;" id="selected_date">   
                </div>    

                <div class="col-lg-4">
                    <button type="submit" class="btn btn-image search" style="margin-top: 22px;">
                        <img src="{{ asset('images/search.png') }}" alt="Search">
                    </button>

                    <a href="{{ route('magento_module_listing') }}" class="btn btn-image" id="" style="margin-top: 22px;">
                        <img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-12 margin-tb" style="margin: 10px; width: 96%;">
		<div class="col-md-12 margin-tb" id="page-view-result">
			<div class="row table-horizontal-scroll">
				<table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="10%">Id</th>
                            <th width="15%">Module Name</th>
                            <th width="20%">Command</th>
							<th width="10%">Job Id</th>
							<th width="10%">Status</th>
							<th width="20%">Response</th>
                            <th width="15%">Updated At</th>
                        </tr>
                    </thead>
                    <tbody id="preview-history-tbody">

				    <tbody id="environment_data">
						<?php
                        if ($magento_modules) {
                            foreach ($magento_modules as $mmkey => $magento_module) {
                                ?>
						<tr class="trrow">
							<td width="10%" class="expand-row">
								{{$magento_module->id}}
							</td>
							<td width="10%" class="expand-row">
								{{$magento_module->module}}
							</td>
							<td width="10%" class="expand-row">
								{{$magento_module->command}}
							</td>
							<td width="10%" class="expand-row">
								{{$magento_module->job_id}}
							</td>
							<td width="10%" class="expand-row">
								{{$magento_module->status}}
							</td>
							<td width="10%" class="expand-row">
								<button type="button" data-id="{{$magento_module->id}}" class="btn btn-primary magento-module-response-view" >
				        			<i class="fa fa-eye" aria-hidden="true"></i>
				        		</button>
							</td>
							<td width="10%" class="expand-row">
								{{$magento_module->updated_at}}
							</td>
						</tr>
						<?php } ?>
						<?php } ?>
				    </tbody>
				</table>

				{{ $magento_modules->appends(request()->except('page'))->links() }}
			</div>
		</div>
	</div>
</div>

@include('magento_module.response')

<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery-ui.js') }} "></script>
<script type="text/javascript">	
	$(document).on('click','.magento-module-response-view',function(){
        id = $(this).data('id');
		$.ajax({
            method: "GET",
            url: `{{ route('magento_module_listing_logs_details', [""]) }}/` + id,
            dataType: "json",
            success: function(response) {
               
                $("#magento_module-comment-list").find(".magento_module-comment-view").html(response.data.response);
                $("#magento_module-comment-list").modal("show");
         
            }
        });
	});
</script>
@endsection