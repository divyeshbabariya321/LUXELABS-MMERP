@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}
</style>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">{{$title}}</h2>
	</div>
	<br>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="table-responsive mt-3">
	<table class="table table-bordered">
	    <thead>
	      <tr>
	      	<th width="2%">Id</th>
	        <th width="5%">Log case id</th>
	        <th width="5%">Store website id</th>
	        <th width="10%">Username</th>
	        <th width="10%">Useremail</th>
	        <th width="10%">Password</th>
	        <th width="10%">First name</th>
	        <th width="10%">Last name</th>
	        <th width="10%">Website mode</th>
	        <th width="10%">Log msg</th>
	    </tr>
	    </thead>
	    <tbody>
	    	@foreach($logstorewebsiteuser as $key=> $value)
		      <tr>
		      	<td>{{ $key+1 }}</td>
		      	<td>{{ $value->log_case_id }}</td>
		      	<td>{{ $value->store_website_id }}</td>
		        <td>{{ $value->username }}</td>
		        <td>{{ $value->useremail }}</td>
		        <td>{{ $value->password }}</td>
		        <td>{{ $value->first_name }}</td>
		        <td>{{ $value->last_name }}</td>
		        <td>{{ $value->website_mode }}</td>
		        <td>{{ $value->log_msg }}</td>
		      </tr>
		    @endforeach 
	    </tbody>
	</table>
</div>

</div>

</div>


<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/store-website.js') }}"></script>

<script type="text/javascript">
	page.init({
		bodyView: $("#common-page-layout"),
		baseUrl: "<?php echo url("/"); ?>"
	});

	$(document).on("click", ".open-build-process-history", function(href) {
		$.ajax({
			url: 'store-website/' + $(this).data('id') + '/build-process/history',
			success: function(data) {
				$('#buildHistory').html(data);
				$('#buildHistoryModal').modal('show');
			},
		});
	});

	$(document).on("click", ".sync_stage_to_master", function(href) {
		$.ajax({
			url: 'store-website/' + $(this).data('id') + '/sync-stage-to-master',
			success: function(data) {
				if (data.code == 200) {
					toastr["success"](data.message);
				} else {
					toastr["error"](data.message);
				}
			},
		});
	});

	function fnc(value, min, max) {
		if (parseFloat(value) < (0).toFixed(2) || isNaN(value))
			return null;
		else if (parseFloat(value) > (100).toFixed(2))
			return "Between 0 To 100 !";
		else return value;
	}
</script>

@endsection