@foreach($websites as $websiteId=>$website) 
	@if(isset($domainOverview[$websiteId]) || isset($backlinkreports[$websiteId]) || isset($siteAudits[$websiteId]))
		<tr>
			@if(isset($domainOverview[$websiteId]) || isset($backlinkreports[$websiteId]) || isset($siteAudits[$websiteId]))
				<td>{{$website}}</td>
			@else
				<td>---</td>
			@endif
			@if(isset($siteAudits[$websiteId]))
				@php $siteAudit = $siteAudits[$websiteId]; @endphp
				<td><a href="{{route('site-audit-details', $websiteId)}}"><span>{{$siteAudit['pages_crawled']}}</span></a></td>
				<td><a href="{{route('site-audit-details', $websiteId)}}"><span> {{$siteAudit['errors']}}</span></a></td>
				<td><a href="{{route('site-audit-details', $websiteId)}}"><span>{{$siteAudit['warnings']}}</span></a></td>
			@else
				<td>---</td>
				<td>---</td>
				<td>---</td>
			@endif
			@if(isset($domainOverview[$websiteId]))
				@php $overview = $domainOverview[$websiteId]; @endphp
				<td><a href="{{route('domain-details', $websiteId)}}"><span>{{$overview['organic_keywords']}}</span></a></td>
				<td><a href="{{route('domain-details', $websiteId)}}"><span> {{$overview['organic_traffic']}}</span></a></td>
				<td><a href="{{route('domain-details', $websiteId)}}"><span>{{$overview['organic_cost']}}</span></a></td>
			@else
				<td>---</td>
				<td>---</td>
				<td>---</td>
			@endif
			@if(isset($backlinkreports[$websiteId]))
				@php $backlinkreport = $backlinkreports[$websiteId]; @endphp
				<td><a href="{{route('backlink-details', $websiteId)}}"><span>{{$backlinkreport['ascore']}}</span></a></td>
				<td><a href="{{route('backlink-details', $websiteId)}}"><span> {{$backlinkreport['follows_num']}}</span></a></td>
				<td><a href="{{route('backlink-details', $websiteId)}}"><span>{{$backlinkreport['nofollows_num']}}</span></a></td>
			@else
				<td>---</td>
				<td>---</td>
				<td>---</td>
			@endif
		</tr>
	@endif
@endforeach