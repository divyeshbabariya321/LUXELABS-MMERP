@php $width = 20; @endphp
@foreach($backlink_domains as $i=>$backlink_domaindetails) 
	<tr>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="backlink-report" data-id="{{$i}}">
				<span class="show-short-backlink-report-{{$i}}">{{ Str::limit('Backlink Report', $width, '...')}}</span>
				<span style="word-break:break-all;" class="show-full-backlink-report-{{$i}} hidden">Backlink Report</span>
			</td>	
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="database" data-id="{{$i}}">
			<span class="show-short-database-{{$i}}">{{ Str::limit($backlink_domaindetails['database'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-database-{{$i}} hidden">{{ $backlink_domaindetails['database'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="domain" data-id="{{$i}}">
			<span class="show-short-domain-{{$i}}">{{ Str::limit($backlink_domaindetails['domain'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-domain-{{$i}} hidden">{{$backlink_domaindetails['domain']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="domain_ascore" data-id="{{$i}}">
			<span class="show-short-domain_ascore-{{$i}}">{{ Str::limit($backlink_domaindetails['domain_ascore'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-domain_ascore-{{$i}} hidden">{{$backlink_domaindetails['domain_ascore']}}</span>
		</td>
		
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="backlinks_num" data-id="{{$i}}">
			<span class="show-short-pbacklinks_num-{{$i}}">{{ Str::limit($backlink_domaindetails['backlinks_num'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-backlinks_num-{{$i}} hidden">{{$backlink_domaindetails['backlinks_num']}}</span>
		</td>
	</tr>
@endforeach
