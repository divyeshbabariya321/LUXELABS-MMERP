@php $width = 20; @endphp
@foreach($compitetors as $i=>$compitetorsDetail)
	<tr>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="domain" data-id="{{$i}}">
			<span class="show-short-domain-{{$i}}">{{ Str::limit($compitetorsDetail['domain'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-domain-{{$i}} hidden">{{ $compitetorsDetail['domain'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="common_keywords" data-id="{{$i}}">
			<span class="show-short-common_keywords-{{$i}}">{{ Str::limit($compitetorsDetail['common_keywords'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-common_keywords-{{$i}} hidden">{{$compitetorsDetail['common_keywords']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="keywords" data-id="{{$i}}">
			<span class="show-short-keywords-{{$i}}">{{ Str::limit($compitetorsDetail['keywords'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-keywords-{{$i}} hidden">{{$compitetorsDetail['keywords']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="traffic" data-id="{{$i}}">
			<span class="show-short-traffic-{{$i}}">{{ Str::limit($compitetorsDetail['traffic'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-traffic-{{$i}} hidden">{{$compitetorsDetail['traffic']}}</span>
		</td>
	</tr>
@endforeach