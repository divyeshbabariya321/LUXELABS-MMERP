@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection
@section('large_content')
   <div class = "row">
		<div class="col-lg-12 margin-tb">
			<?php $base_url = URL::to('/');?>
			<h2 class="page-heading">Conversion Rate</h2>
			@if ($message = Session::get('message'))
				<div class="alert alert-success">
					<p>{{ $message }}</p>
				</div>
			@endif
			<div class="pull-right mt-3">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#create_setting_model">Create Setting</button>
            </div>
        </div>
	</div>
	
    <div class="row">
        <div class="col-lg-12 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                           Conversion Rate
                        </h4>
                    </div>
					<div class="panel-body">
						<table class="table table-bordered table-striped">
							<tr>
								<th>ID</th>
								<th>Currency</th>
								<th>To currency</th>
								<th>Value</th>
								<th>Action</th>
							</tr>
							@foreach ($conversionRates as $key => $val )
								<tr id = "row_{{$val->id}}">
									<td>{{$val->id}}</td>
									<td class="currency">{{$val->currency}}</td> 
									<td class="to_currency">{{$val->to_currency}}</td>
									<td class="price">{{$val->price}}</td>
									<td><button type="button" class="btn btn-default edit_setting" data-id="{{$val->id}}" data-toggle="modal" data-target="#create_setting_model">Edit Conversion Rate</button></td>
								</tr>
							@endforeach
						</table>
						{{ $conversionRates->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>
	
	<div id="create_setting_model" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<form action="{{ url('conversion/rate/update') }}" method="POST">
				@csrf
				<div class="modal-header">
					<h4 class="modal-title">Conversion Rate</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id" id="conversion_rate_id">
					<div class="form-group">
						<strong>Currency:</strong>
						<input type="text" name="currency" id="currency" class="form-control" required>
						@if ($errors->has('currency'))
							<div class="alert alert-danger">{{$errors->first('currency')}}</div>
						@endif
					</div>
					<div class="form-group">
						<strong>To Currency:</strong>
						<input type="text" name="to_currency" id="to_currency" class="form-control" required>
						@if ($errors->has('to_currency'))
							<div class="alert alert-danger">{{$errors->first('to_currency')}}</div>
						@endif
					</div>
					<div class="form-group">
						<strong>Value:</strong>
						<input type="text" name="price" id="price" class="form-control" required>
						@if ($errors->has('price'))
							<div class="alert alert-danger">{{$errors->first('price')}}</div>
						@endif
					</div>
					
				</div>
				<div class="modal-footer">
				  <button type="button" class="btn btn-default close-setting" data-dismiss="modal">Close</button>
				  <button type="submit" class="btn btn-secondary">Submit</button>
				</div>
			</form>
        </div>
      </div>
    </div>
	
@endsection


@section('scripts')
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$('.edit_setting').on('click', function() {
		var id = $(this).data('id');
		$("#conversion_rate_id").val(id);
		$("#currency").val($("#row_"+id+" td.currency").text());
		$("#to_currency").val($("#row_"+id+" td.to_currency").text());
		$("#price").val($("#row_"+id+" td.price").text());
    });
	
	$('.close-setting').on('click', function() {
		console.log("hello");
		$("#conversion_rate_id, #currency, #to_currency, #price").val('');
	});
	
});
</script>
@endsection
