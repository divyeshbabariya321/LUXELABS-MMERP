@php $width = 20; @endphp
@foreach($domainorganicpage as $i=>$domainorganicpagedetails) 
	<tr>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="domain-organic-page" data-id="{{$i}}">
				<span class="show-short-domain-organic-page-{{$i}}">{{ Str::limit('Domain Organic Page', $width, '...')}}</span>
				<span style="word-break:break-all;" class="show-full-domain-organic-page-{{$i}} hidden">Domain Organic Page</span>
			</td>	
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="url" data-id="{{$i}}">
			<span class="show-short-url-{{$i}}">{{ Str::limit($domainorganicpagedetails['url'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-url-{{$i}} hidden">{{$domainorganicpagedetails['url']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="number_of_keywords" data-id="{{$i}}">
			<span class="show-short-number_of_keywords-{{$i}}">{{ Str::limit($domainorganicpagedetails['number_of_keywords'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-number_of_keywords-{{$i}} hidden">{{ $domainorganicpagedetails['number_of_keywords'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="traffic" data-id="{{$i}}">
			<span class="show-short-traffic-{{$i}}">{{ Str::limit($domainorganicpagedetails['traffic'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-traffic-{{$i}} hidden">{{$domainorganicpagedetails['traffic']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="traffic_percentage" data-id="{{$i}}">
			<span class="show-short-traffic_percentage-{{$i}}">{{ Str::limit($domainorganicpagedetails['traffic_percentage'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-traffic_percentage-{{$i}} hidden">{{$domainorganicpagedetails['traffic_percentage']}}</span>
		</td>
	</tr>
@endforeach