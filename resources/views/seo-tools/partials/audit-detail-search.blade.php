@if($siteAudit!=null) 
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
@endif
