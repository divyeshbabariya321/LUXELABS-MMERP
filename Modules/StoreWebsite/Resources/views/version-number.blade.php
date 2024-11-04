@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', "Store website versions")

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
		<h2 class="page-heading">Store website versions <span class="count-text"></span></h2>
	</div>
	<br>
	<div class="col-lg-12 margin-tb">
		@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
		@endif
		<div class="margin-tb" id="page-view-result">
			<div class="col-md-12 pl-5 pr-5">
				<div class="table-responsive">
					<table class="table table-bordered"style="table-layout: fixed;">
						<thead>
						  <tr>
							<th width="10%" class="Website-task" title="Title">Title</th>
							@for ($i = 1; $i <= 10; $i++)
								<th class="Website-task">Version {{ $i }}</th>
							@endfor
						  </tr>
						</thead>
						<tbody>
							@foreach ($storeWebsites as $storeWebsite)
								<tr>
									<td style="word-break: break-all; word-wrap: break-word">{{ $storeWebsite->title }}</td>
									@foreach ($storeWebsite->latestTenVersions as $version)
										<td style="word-break: break-all; word-wrap: break-word">
											{{ $version->version }} </br>
											<strong>(Build {{ $version->build_id }})</strong>
										</td>
									@endforeach
									@if (count($storeWebsite->latestTenVersions) < 10)
										@for ($i = 0; $i < 10 - count($storeWebsite->latestTenVersions); $i++)
											<td></td>
										@endfor
									@endif
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
@endsection