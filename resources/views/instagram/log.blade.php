@extends('layouts.app')

@section('title', 'Instagram Log List')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
     <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Instagram Logs</h2>
        </div>
    </div>

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th width="15%">Created_at</th>
                <th width="35%">Log Title</th>
                <th width="50%">Log</th>
            </tr>
            </thead>

            <tbody id="content_data">
				@foreach($logs as $log) 
					<tr>
						<td>{{ $log['created_at'] }}</td>
						<td>{{ $log['log_title'] }}</td>
						<td>{{ $log['log_description'] }}</td>
					</tr>
				@endforeach
            </tbody>

            {!! $logs->render() !!}

        </table>
    </div>
 

@endsection