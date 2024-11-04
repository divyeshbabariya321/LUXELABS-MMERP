@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}
	tbody tr .Website-task-warp{
		overflow: hidden !important;
		white-space: normal !important;
		word-break: break-all;
	}
</style>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">{{$title}}</h2>
	</div>
	<br>

	@if ($message = Session::get('success'))
		<div class="col-lg-12  pl-5 pr-5">
	        <div class="alert alert-success">
	            <p>{{ $message }}</p>
	        </div>
        </div>
    @endif

    @if ($errors->any())
	    <div class="col-lg-12  pl-5 pr-5">
	        <div class="alert alert-danger">
	            <strong>Whoops!</strong> There were some problems with your input.<br><br>
	            <ul>
	                @foreach ($errors->all() as $error)
	                    <li>{{ $error }}</li>
	                @endforeach
	            </ul>
	        </div>
        </div>
    @endif
	
	<div class="col-lg-12 pl-5 pr-5">
		<form action="/store-website/generate-api-token" method="post">
			<?php echo csrf_field(); ?>
			
			<div class="col-md-12">
				<div class="table-responsive mt-3">
					<table class="table table-bordered overlay admin-password-table" id="tblAdminPassword">
						<thead>
						<tr>
							<th>Id</th>
							<th width="40%">Request Data</th>
							<th width="40%">Response Data</th>
							<th width="10%">Created Date</th>						
						</tr>
						</thead>
						<tbody>
							@include('image_references.logslist')
						</tbody>
					</table>
					{{ $CropImageGetRequest->appends(request()->except('page'))->links() }}
				</div>
			</div>
		</form>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>

<script type="text/javascript" src="{{ mix('webpack-dist/js/jsrender.min.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery-ui.js') }} "></script>
<script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/store-website.js') }} "></script>

<script type="text/javascript">
	page.init({
		bodyView: $("#common-page-layout"),
		baseUrl: "<?php echo url('/'); ?>"
	});

	$(document).on('click', '.expand-row-msg', function () {
		var name = $(this).data('name');
		var id = $(this).data('id');
		var full = '.expand-row-msg .show-short-'+name+'-'+id;
		var mini ='.expand-row-msg .show-full-'+name+'-'+id;
		$(full).toggleClass('hidden');
		$(mini).toggleClass('hidden');
    });
</script>

@endsection