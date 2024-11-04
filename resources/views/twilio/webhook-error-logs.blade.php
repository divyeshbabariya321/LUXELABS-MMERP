@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection
@section('large_content')
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
								<th>Level</th>
								<th>Payload Type</th>
								<th>Payload</th>
								<th>Sid</th>
								<th>Account Sid</th>
								<th>Parent Account Sid</th>
								<th>Time</th>
							</tr>
							@foreach ($logs as $val )
								<tr>
									<td>{{$val->id}}</td>
									<td>{{$val->level}}</td> 
									<td>{{$val->payload_type}}</td> 
									<td>{{$val->payload}}</td>
									<td>{{$val->sid}}</td>
									<td>{{$val->account_sid}}</td>
									<td>{{$val->parent_account_sid}}</td>
									<td>{{$val->timestamp}}</td>
								</tr>
							@endforeach
						</table>
						{{ $logs->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>	
@endsection
