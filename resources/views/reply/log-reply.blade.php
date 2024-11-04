@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Replay log ({{$replyLogs->total()}})</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">S.No</th>
			    	<th width="20%">Message</th>
			        <th width="10%">Type</th>
			        <th width="10%">Created At</th>
                </tr>
		    	<tbody>
                    @foreach ($replyLogs as $key =>$replyLog)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td width="30%">{{$replyLog->message}}</td>
                            <td>{{$replyLog->type}}</td>
                            <td>{{$replyLog->created_at}}</td>
                        </tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $replyLogs->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

@endsection