@php $width = 10; @endphp
@foreach($keywordsearch as $i=>$keywordDetail)
	<tr>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="keyword" data-id="{{$i}}">
			<span class="show-short-keyword-{{$i}}">{{ Str::limit($keywordDetail['keyword'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-keyword-{{$i}} hidden">{{ $keywordDetail['keyword'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="database" data-id="{{$i}}">
			<span class="show-short-database-{{$i}}">{{ Str::limit($keywordDetail['database'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-database-{{$i}} hidden">{{ $keywordDetail['database'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="position" data-id="{{$i}}">
			<span class="show-short-position-{{$i}}">{{ Str::limit($keywordDetail['position'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-position-{{$i}} hidden">{{ $keywordDetail['position'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="cpc" data-id="{{$i}}">
			<span class="show-short-cpc-{{$i}}">{{ Str::limit($keywordDetail['cpc'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-cpc-{{$i}} hidden">{{ $keywordDetail['cpc'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="traffic_percentage" data-id="{{$i}}">
			<span class="show-short-traffic_percentage-{{$i}}">{{ Str::limit($keywordDetail['traffic_percentage'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-traffic_percentage-{{$i}} hidden">{{ $keywordDetail['traffic_percentage'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="search_volume" data-id="{{$i}}">
			<span class="show-short-search_volume-{{$i}}">{{ Str::limit($keywordDetail['search_volume'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-search_volume-{{$i}} hidden">{{ $keywordDetail['search_volume'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="competition" data-id="{{$i}}">
			<span class="show-short-competition-{{$i}}">{{ Str::limit($keywordDetail['competition'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-competition-{{$i}} hidden">{{ $keywordDetail['competition'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="traffic_cost" data-id="{{$i}}">
			<span class="show-short-traffic_cost-{{$i}}">{{ Str::limit($keywordDetail['traffic_cost'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-traffic_cost-{{$i}} hidden">{{ $keywordDetail['traffic_cost'] }}</span>
		</td>
	</tr>
@endforeach
	<tr>
		<td colspan="12">
			{{ $keywordsearch->appends(request()->except("page"))->links() }}
		</td>
    </tr>