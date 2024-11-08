@extends('layouts.app')
@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-md-6 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Subscribed Mailing List</h2>
					@if ( Session::has('message') )
					  <p class="alert {{ Session::get('flash_type') }}">{{ Session::get('message') }}</p>
					@endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                           Subscribed Mailing List for <b>{ {{$emailLeadData->email}} }</b>
                        </h4>
                    </div>
					<div class="panel-body">
						<table class="table table-bordered table-striped lead-table">
							<tr>
								<th>Mailing Lists</th>
								<th>Action</th>
								
							</tr>
							@foreach ($leadData as $lead )
								<tr>
									<td>{{$lead->name}}</td>
									<td><a class="btn btn-default" href="{{url('emailleads/unsubscribe/'.$lead->id.'/'.$lead->lead_list_id)}}">Unsubscribe</a> </td>
								</tr>
							@endforeach
						</table>
					</div>
                </div>
            </div>
		</div>
	</div>
	<div class="modal fade" id="assignModel" tabindex="-1" role="dialog" aria-labelledby="assignModel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
				<form action="{{url('emailleads/assign')}}" method="POST">
					@csrf
					<div class="modal-header">
						<h5 class="modal-title">Assign Mailing List</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" name="lead_id" id="lead_id">
						<div class="form-group mr-3">
							<select class="form-control select-multiple" name="list_id[]" multiple>
								<optgroup label="Maling List">
								@foreach($leadData as $list)
									<option value="{{$list->id}}">{{$list->name}}</option>
								@endforeach
								</optgroup>
							</select>
						</div>
					</div>	
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary save_list">Save changes</button>
					</div>
				</form>
            </div>
        </div>
    </div>
	<div id="importEmailLeads" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Import Leads</h4>
					<button type="button" class="close" data-dismiss="modal">×</button>
				</div>
				<form action="{{url('emailleads/import')}}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="modal-body">
						<div class="form-group">
							<input type="file" name="file" required="">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-secondary">Import</button>
					</div>
				</form>
			</div>

		</div>
	</div>
@endsection

    
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
 <script>
	$(document).ready(function()
	{
		$(".select-multiple").selectpicker();
		
		$(".assign_list").on('click',function(){
			
			$('.select-multiple option:selected').each(function() {
				$(this).prop('selected', false);
			})
			$('.select-multiple').selectpicker('refresh');
			
			console.log("hello");
			var searchIDs = $(".lead-table input:checkbox:checked").map(function(){
			  return $(this).data('leadid');
			}).get();
			
			if(searchIDs.length)
			{
				console.log(searchIDs);
				$("#lead_id").val(searchIDs);
			}else{
				$(".close-btn").trigger('click');
				alert("Please select at least one lead");
				return false;
			}
		});
	});
</script>
@endsection
