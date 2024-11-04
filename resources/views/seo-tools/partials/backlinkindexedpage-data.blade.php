@php $width = 7; @endphp
@foreach($backlink_indexed_page as $i=>$backlink_indexed_pagedetails) 
	<tr>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="backlink-indexed-page" data-id="{{$i}}">
				<span class="show-short-backlink-indexed-page-{{$i}}">{{ Str::limit('Backlink Indexed Page', $width, '...')}}</span>
				<span style="word-break:break-all;" class="show-full-backlink-indexed-page-{{$i}} hidden">Backlink Indexed Page</span>
			</td>	
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="source_url" data-id="{{$i}}">
			<span class="show-short-source_url-{{$i}}">{{ Str::limit($backlink_indexed_pagedetails['source_url'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-source_url-{{$i}} hidden">{{$backlink_indexed_pagedetails['source_url']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="source_title" data-id="{{$i}}">
			<span class="show-short-source_title-{{$i}}">{{ Str::limit($backlink_indexed_pagedetails['source_title'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-source_title-{{$i}} hidden">{{ $backlink_indexed_pagedetails['source_title'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="response_code" data-id="{{$i}}">
			<span class="show-short-response_code-{{$i}}">{{ Str::limit($backlink_indexed_pagedetails['response_code'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-response_code-{{$i}} hidden">{{$backlink_indexed_pagedetails['response_code']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="backlinks_num" data-id="{{$i}}">
			<span class="show-short-backlinks_num-{{$i}}">{{ Str::limit($backlink_indexed_pagedetails['backlinks_num'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-backlinks_num-{{$i}} hidden">{{$backlink_indexed_pagedetails['backlinks_num']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="domains_num" data-id="{{$i}}">
			<span class="show-short-domains_num-{{$i}}">{{ Str::limit($backlink_indexed_pagedetails['domains_num'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-domains_num-{{$i}} hidden">{{$backlink_indexed_pagedetails['domains_num']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="last_seen" data-id="{{$i}}">
			<span class="show-short-last_seen-{{$i}}">{{ Str::limit($backlink_indexed_pagedetails['last_seen'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-last_seen-{{$i}} hidden">{{$backlink_indexed_pagedetails['last_seen']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="external_num" data-id="{{$i}}">
			<span class="show-short-external_num-{{$i}}">{{ Str::limit($backlink_indexed_pagedetails['external_num'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-external_num-{{$i}} hidden">{{$backlink_indexed_pagedetails['external_num']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="internal_num" data-id="{{$i}}">
			<span class="show-short-internal_num-{{$i}}">{{ Str::limit($backlink_indexed_pagedetails['internal_num'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-internal_num-{{$i}} hidden">{{$backlink_indexed_pagedetails['internal_num']}}</span>
		</td>
	</tr>
@endforeach