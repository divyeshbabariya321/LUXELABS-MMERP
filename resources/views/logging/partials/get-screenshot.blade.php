@if($logListMagento)
    @foreach ($logListMagento->screenshot() as $logScrapper)
        <tr>
            <td>{{$logScrapper->sku}}</td>
            <td>{{$logScrapper->store_website_name}}</td>
            <td>{{$logScrapper->status}}</td>
            <td>
                @if(!empty($logScrapper->image_path))
                    <a href="{{$logScrapper->image_path}}" target="__blank">
                        <img width="25px" height="25px" src="{{$logScrapper->image_path}}">
                    </a>
                @else
                    -
                @endif
            </td>
            <td>{{$logScrapper->created_at}}</td>
        </tr>
    @endforeach
@endif
