@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection
@section('large_content')
   <div class = "row">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">Twilio Speech to Text Logs</h2>
			
            <div class="pull-left cls_filter_box">
				{{ html()->modelForm($input, 'GET', route('twilio-speech-to-text-logs'))->class('form-inline')->open() }}
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Caller</label>
						{{ html()->text('caller')->class('form-control') }}
                    </div>
					<div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Log</label>
						{{ html()->text('log')->class('form-control') }}
                    </div>
					<div class="form-group ml-3 cls_filter_inputbox margin-top">
						<button type='submit' class="btn btn-default">Search</button>
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
                          Twilio speech to text logs
                        </h4>
                    </div>
					<div class="panel-body">
						<table class="table table-bordered table-hover" style="table-layout:fixed;">
							<thead>
								<tr>
									<th style="width:10%">ID</th>
									<th style="width:20%">Date</th>
									<th style="width:20%">Caller</th>
									<th style="width:50%">Log</th>
								</tr>
							</thead>
							<tbody>
							@foreach ($twilioLogs as $val )
								<tr id = "row_{{$val->id}}">
									<td style="width:10%">{{$val->id}}</td>
									<td style="width:20%">{{$val->created_at}}</td>
									<td style="width:20%">{{$val->phone}}</td> 
									<td style="width:50%">{!! $val->log !!}</td>
								</tr>
							@endforeach
							</tbody>
						</table>
						{{ $twilioLogs->appends($input)->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>	
@endsection
