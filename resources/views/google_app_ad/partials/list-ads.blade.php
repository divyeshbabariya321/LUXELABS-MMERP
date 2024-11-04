@foreach($ads as $ad)
    <tr>
        <td>{{$ad->id}}</td>
        <td>{{$ad->headline1}}</td>
        <td>{{$ad->headline2}}</td>
        <td>{{$ad->headline3}}</td>
        <td>{{$ad->status}}</td>
        <td>{{$ad->created_at}}</td>
        <td>
            <div class="d-flex">
                <a href="{{ route('app-ad.show', [$campaignId, $adGroupId, $ad['google_ad_id']]) }}" class="btn btn-image text-dark" title="View"><i class="fa fa-eye"></i></a>
            </div>
        </td>
    </tr>
@endforeach