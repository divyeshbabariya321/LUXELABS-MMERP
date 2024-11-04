@php $width = 20; @endphp
@foreach($domainlandingpage as $i=>$domainlandingpagedetails) 
	<tr>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="domain-landing-page" data-id="{{$i}}">
				<span class="show-short-domain-landing-page-{{$i}}">{{ Str::limit('Domain Landing Page', $width, '...')}}</span>
				<span style="word-break:break-all;" class="show-full-domain-landing-page-{{$i}} hidden">Domain Landing Page</span>
			</td>	
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="target_url" data-id="{{$i}}">
			<span class="show-short-target_url-{{$i}}">{{ Str::limit($domainlandingpagedetails['target_url'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-target_url-{{$i}} hidden">{{$domainlandingpagedetails['target_url']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="first_seen" data-id="{{$i}}">
			<span class="show-short-first_seen-{{$i}}">{{ Str::limit($domainlandingpagedetails['first_seen'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-first_seen-{{$i}} hidden">{{ $domainlandingpagedetails['first_seen'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="last_seen" data-id="{{$i}}">
			<span class="show-short-last_seen-{{$i}}">{{ Str::limit($domainlandingpagedetails['last_seen'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-last_seen-{{$i}} hidden">{{$domainlandingpagedetails['last_seen']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="times_seen" data-id="{{$i}}">
			<span class="show-short-times_seen-{{$i}}">{{ Str::limit($domainlandingpagedetails['times_seen'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-times_seen-{{$i}} hidden">{{$domainlandingpagedetails['times_seen']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="ads_count" data-id="{{$i}}">
			<span class="show-short-ads_count-{{$i}}">{{ Str::limit($domainlandingpagedetails['ads_count'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-ads_count-{{$i}} hidden">{{$domainlandingpagedetails['ads_count']}}</span>
		</td>
	</tr>
@endforeach