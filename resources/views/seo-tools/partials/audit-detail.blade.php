@extends('layouts.app')
@section('content')
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
	
	<div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Site Audit </h2>
                </div>
            </div>
        </div>
    </div>
@if($siteAudit!=null) 
	<div class="row mx-1">
		<div class="col-lg-12 margin-tb p-3">
			<div class="col-md-2">
				<div class='input-group date' id='filter_date'>
					<input type='text' class="form-control" id="search_name" name="search_name" value="{{old('search_name')}}" placeholder="Enter Name" />
				</div>
			</div>
			<div class="col-md-2">
				<div class='input-group date' id='filter_date'>
					<input type='text' class="form-control" id="search_status" name="search_status" value="{{old('search_status')}}" placeholder="Enter Status" />
				</div>
			</div>
			
			<div class="col-md-2">
				<button type="button" class="btn btn-image" onclick="submitSearch();"><img src="{{ asset('images/filter.png')}}"/></button>
				<button type="button" class="btn btn-image pl-0" id="resetFilter" onclick="resetSearch()"><img src="{{ asset('images/resend2.png')}}"/></button>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="table-responsive" >	
				<table class="table table-striped table-bordered" id="content_data"> 
					<tr>
						<td>Site Audit</td>
						<td>Name</td>
						<td>{{$siteAudit['name']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Status</td>
						<td>{{$siteAudit['status']}}</td>
					</tr>
					@if($viewTypeName == 'errors')
					<tr>
						<td>Site Audit</td>
						<td>Errors</td>    
						<td>{{$siteAudit['errors']}}</td>
					</tr>
					@endif
					@if($viewTypeName == 'warnings')
					<tr>	
						<td>Site Audit</td>			
						<td>Warnings</td>  
						<td>{{$siteAudit['warnings']}}</td>
					</tr>
					@endif
					<tr>
						<td>Site Audit</td>				
						<td>Notices</td> 
						<td>{{$siteAudit['notices']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>				
						<td>Broken</td> 
						<td>{{$siteAudit['broken']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Blocked</td>
						<td>{{$siteAudit['blocked']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Redirected</td>
						<td>{{$siteAudit['redirected']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Healthy</td>
						<td>{{$siteAudit['healthy']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Have issues</td>
						<td>{{$siteAudit['haveIssues']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Have issues delta</td>
						<td>{{$siteAudit['haveIssuesDelta']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Defects</td>
						<td>{{$siteAudit['defects']}}</td>
					</tr>
					<!--<tr>
						<td>Site Audit</td>
						<td>Markups</td>
						<td>
							@foreach(json_decode($siteAudit['markups']) as $markup=>$val)
							{{$markup}} : {{$val}} <br>
							@endforeach
						</td>
					</tr>-->
					<tr>
						<td>Site Audit</td>
						<td>Depths</td>
						<td>{{$siteAudit['depths']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Crawl subdomains</td>
						<td>{{$siteAudit['crawlSubdomains']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Respect Crawl delay</td>
						<td>{{$siteAudit['respectCrawlDelay']}}</td>
					</tr>
					<tr>
						<td>Canonical</td>
						<td>{{$siteAudit['canonical']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>User agent type</td>
						<td>{{$siteAudit['user_agent_type']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Last audit</td>
						<td>{{$siteAudit['last_audit']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Last failed audit</td>
						<td>{{$siteAudit['last_failed_audit']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Next audit</td>
						<td>{{$siteAudit['next_audit']}}</td>
					</tr>
					@if($viewTypeName == 'pages_crawled')
					<tr>
						<td>Site Audit</td>
						<td>Running pages crawled</td>
						<td>{{$siteAudit['running_pages_crawled']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Running pages limit</td>
						<td>{{$siteAudit['running_pages_limit']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Pages crawled</td>
						<td>{{$siteAudit['pages_crawled']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td >Pages limit</td>
						<td>{{$siteAudit['pages_limit']}}</td>
					</tr>
					@endif
					
					<tr>
						<td>Site Audit</td>
						<td >Total checks</td>
						<td>{{$siteAudit['total_checks']}}</td>
					</tr>
					@if($viewTypeName == 'errors')
					<tr>
						<td>Site Audit</td>
						<td>Errors delta</td>
						<td>{{$siteAudit['errors_delta']}}</td>
					</tr>
					@endif
					@if($viewTypeName == 'warnings')
					<tr>
						<td>Site Audit</td>
						<td >Warnings delta</td>
						<td>{{$siteAudit['warnings_delta']}}</td>
					</tr>
					@endif
					<tr>
						<td>Site Audit</td>
						<td >Notices delta</td>
						<td>{{$siteAudit['notices_delta']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td>Mask allow</td>
						<td>{{$siteAudit['mask_allow']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td >Mask disallow</td>
						<td>{{$siteAudit['mask_disallow']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td >Removed parameters</td>
						<td>{{$siteAudit['removedParameters']}}</td>
					</tr>
					<tr>
						<td>Site Audit</td>
						<td >Excluded checks</td> 	
						<td>{{$siteAudit['excluded_checks']}}</td>				
					</tr>
				</table>
			</div>
		</div>
	</div>
@endif
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
		//This function use for search record by ajax
		function submitSearch() 
		{
			var websiteId = "{{$id}}";
			var viewId = "{{$viewId}}";
			var viewTypeName = "{{$viewTypeName}}";
			var src = "{{url('seo/site-audit/search/')}}/"+websiteId+"/"+viewId+"/"+viewTypeName;
			var searchName = $('#search_name').val();
			var searchStatus = $('#search_status').val();
			data = {
				"search_websiteId" : websiteId,
				"search_name" : searchName,
				"search_status" : searchStatus
			};
			$.ajax({
				url: src,
				type: "post",
				dataType: "json",
				data: data,
				headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
				beforeSend: function () {
					$("#loading-image").show();
				},
			}).done(function (message) {
				$("#loading-image").hide();
				$("#content_data").html(message.tbody);
				var rendered = renderMessage(message, tobottom);
			}).fail(function (jqXHR, ajaxOptions, thrownError) {
				$("#loading-image").hide();
				alert(jqXHR.message);
			});
		}

		//This function use for reset filter data
		function resetSearch()
		{
        	location.reload();
    	}
	</script>
@endsection