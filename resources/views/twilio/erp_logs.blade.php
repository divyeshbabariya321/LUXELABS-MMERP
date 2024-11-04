@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection
@section('large_content')
   <div class = "row">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">Twilio ERP Logs</h2>
			
            <div class="pull-left cls_filter_box">
				{{ html()->modelForm($input, 'GET', route('twilio.erp_logs'))->class('form-inline')->open() }}
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Caller</label>
						{{ html()->text('caller', isset($input['caller']) ? $input['caller'] : null)->class('form-control') }}
                    </div>
					<div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Log</label>
						{{ html()->text('log')->class('form-control') }}
                    </div>
					<div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Created At</label>
						{{ html()->input('date', 'date')->class('form-control') }}
                    </div>
					<div class="form-group ml-3 cls_filter_inputbox margin-top"><br>
						<button type='submit' class="btn btn-default">Search</button>
						<a href="{{route('twilio.erp_logs')}}" class="btn btn-default">Clear</a>
                    </div>
				</form>
            </div>
        </div>
	</div>
	
    <div class="row">
        <div class="col-lg-12 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                          Logsname
                        </h4>
                    </div>
					<div class="panel-body">
						<table class="table table-bordered table-striped">
							<tr>
								<th>ID</th>
								<th>Caller</th>
								<th>Created At</th>
								<th>Log</th>
							</tr>
							@foreach ($twilioLogs as $val )
								<tr id = "row_{{$val->id}}">
									<td>{{$val->id}}</td>
									<td class="name">{{$val->phone}}</td> 
									<td class="name">{{$val->created_at}}</td> 
									<td class="val">{{$val->log}}</td>
								</tr>
							@endforeach
						</table>
						{{ $twilioLogs->appends($input)->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>	
@endsection
