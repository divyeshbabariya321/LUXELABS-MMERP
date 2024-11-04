@extends('layouts.app')
@section('large_content')

<div class="row">
    <div class="col-lg-12 margin-tb p-0">
        <h2 class="page-heading">Live Chat Event Logs</h2>
    </div>
</div>
<div class="row">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="keywordassign_table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Event type</th>
                <th>Customer</th>
                <th>Website</th>
                <th>Thread</th>
                <th>Log</th>
            </tr>
            </thead>
            <tbody>
				@foreach($logs as $log)
					<tr>
						<td> {{$log['created_at']}} </td>
						<td> {{$log['event_type']}} </td>
						<td> {{$log['customer_name']}} </td>
						<td> {{$log['website']}} </td>
						<td> {{$log['thread']}} </td>
						<td> {{$log['log']}} </td>
					</tr>
				@endforeach
            </tbody>
        </table>
		{{$logs->links()}}
    </div>
</div>



@endsection
