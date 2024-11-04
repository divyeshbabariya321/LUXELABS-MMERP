@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection
@section('large_content')
   <div class = "row">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">Chatbot log Journey</h2>
		</div>
	</div>
	
    <div class="row">
        <div class="col-lg-12 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-body">
						<table class="table table-bordered table-striped">
							<tr>
								<th>#</th>
								<th>Chatbot message log id</th>
								<th>Question created</th>
								<th>Question example created</th>
								<th>Question reply created</th>
								<th>Question pushed</th>
								<th>Dialog created</th>
								
								
							</tr>
							@foreach ($watsonJourney as $key=>$log )
								<tr>
									<td>{{ $key+1 }}</td>
									<td>{{$log->chatbot_message_log_id}}</td> 
									<td>@if($log->question_created == 1) Yes @else No @endif</td>
									<td>@if($log->question_example_created == 1) Yes @else No @endif</td>
									<td>@if($log->question_reply_inserted == 1) Yes @else No @endif</td>
									<td>@if($log->question_pushed == 1) Yes @else No @endif</td>
									<td>@if($log->dialog_inserted == 1) Yes @else No @endif</td>
									
								</tr>
							@endforeach
						</table>
						{{ $watsonJourney->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>	
@endsection
