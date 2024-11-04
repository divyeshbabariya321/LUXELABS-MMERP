@extends('layouts.app')
@section('favicon' , 'task.png')


@section('content')

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Scrapper Log List <span class="count-text"></span></h2>
		<div class="pull-left cls_filter_box">
			{{ html()->modelForm([], 'GET', url()->current())->class('form-inline')->open() }}
			    <div class="form-group ml-3 cls_filter_inputbox">
					<label for="leads_email">Scrapper Name</label>
					{{ html()->text('scraper_name', $scrapname)->class('form-control') }}
				</div>
				<div class="form-group ml-3 cls_filter_inputbox">
					<label for="leads_email">Created Date</label>
					{{ html()->input('date', 'created_at', $scrapdate)->class('form-control') }}
				</div>
				<button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image"><img src="{{url('/images/filter.png')}}"/></button>
			</form>
		</div>
    </div>
    <br>
    <div class="table-responsive mt-3 col-lg-12 margin-tb">
		<table class="table table-bordered table-striped sort-priority-scrapper">
			<thead>
				<tr>
					<th>Scrapper name</th>
					<th>Log message</th>
					<th>Reason</th>
					<th>Type</th>
					<th>Created at</th>
				</tr>
			</thead>
			<tbody class="conent">
				@foreach ($logDetails as $log)
					<tr>
						<td>{{ $log->scraper_name }}</td>
						<td>{{ $log->log_messages }}</td>
						<td>{{ $log->reason }}</td>
						<td>{{ $log->type??'N/A' }}</td>
						<td>{{ $log->created_at }}</td>
					</tr>
				@endforeach
		   </tbody>

		</table>
		{{$logDetails->links()}}
	</div>
</div>
@endsection
