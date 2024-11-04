@foreach($data as $key=>$datum)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>@if(isset($datum['gmailDataMedia']['page_url']))<a href="{{ $datum['gmailDataMedia']['page_url'] }}" target="_blank">Visit</a>@else <a href="{{ $datum->page_url }}" target="_blank">Visit</a> @endif</td>
                        <td>{{ $datum->sender }}</td>
                        <td>{{ $datum->received_at }}</td>
                        <td>
                            @if(isset($datum->images[0]))
                            <a href="{{ $datum->images }}">
                                    <img src="{{ $datum->images }}" alt="" style="width: 100px;height: 100px;">
                                </a>
                            @endif
                            
                        </td>                        
                    </tr>
                @endforeach