@foreach($ads as $ad)
    <tr>
        <td>{{$ad->id}}</td>
        <td>{{$ad->headline1}}</td>
        <td>{{$ad->business_name}}</td>
        <td>{{$ad->final_url}}</td>
        <td>{{$ad->status}}</td>
        <td>{{$ad->created_at}}</td>
        <td>
            <div class="d-flex">
                <a href="{{ route('responsive-display-ad.show', [$campaignId, $adGroupId, $ad['google_ad_id']]) }}" class="btn btn-image text-dark" title="View"><i class="fa fa-eye"></i></a>
                {{ html()->form('DELETE', route('responsive-display-ad.deleteAd', [$campaignId, $adGroupId, $ad['google_ad_id']]))->style('display:inline')->open() }}
                    <button type="submit" class="btn btn-image" title="Delete"><img src="/images/delete.png"></button>
                {{ html()->form()->close() }}
            </div>
        </td>
    </tr>
@endforeach