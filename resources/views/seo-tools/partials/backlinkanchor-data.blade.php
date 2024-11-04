@php $width = 20; @endphp
@foreach($backlink_anchors as $i=>$backlink_anchorsdetails) 
	<tr>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="backlink-anchor-report" data-id="{{$i}}">
				<span class="show-short-backlink-anchor-report-{{$i}}">{{ Str::limit('Backlink Report', $width, '...')}}</span>
				<span style="word-break:break-all;" class="show-full-backlink-anchor-report-{{$i}} hidden">Backlink Anchor Report</span>
			</td>	

		<td class="expand-row-msg" style="width:{{$width}}%" data-name="database" data-id="{{$i}}">
			<span class="show-short-database-{{$i}}">{{ Str::limit($backlink_anchorsdetails['database'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-database-{{$i}} hidden">{{ $backlink_anchorsdetails['database'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="anchor" data-id="{{$i}}">
			<span class="show-short-anchor-{{$i}}">{{ Str::limit($backlink_anchorsdetails['anchor'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-anchor-{{$i}} hidden">{{$backlink_anchorsdetails['anchor']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="domains_num" data-id="{{$i}}">
			<span class="show-short-domains_num-{{$i}}">{{ Str::limit($backlink_anchorsdetails['domains_num'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-domains_num-{{$i}} hidden">{{$backlink_anchorsdetails['domains_num']}}</span>
		</td>
		
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="backlinks_num" data-id="{{$i}}">
			<span class="show-short-pbacklinks_num-{{$i}}">{{ Str::limit($backlink_anchorsdetails['backlinks_num'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-backlinks_num-{{$i}} hidden">{{$backlink_anchorsdetails['backlinks_num']}}</span>
		</td>
	</tr>
@endforeach
