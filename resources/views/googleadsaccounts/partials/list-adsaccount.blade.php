@foreach($googleadsaccount as $googleadsac)
<tr>
    <td>{{$loop->iteration}}</td>
    <td>{{$googleadsac->account_name}}</td>
    <td>{{$googleadsac->store_websites}}</td>
    <td>{{$googleadsac->google_customer_id}}</td>
    <td>{{$googleadsac->notes}}</td>
    <td>{{$googleadsac->status}}</td>
    <td>{{$googleadsac->created_at}}</td>
    <td>
        <a href="/google-campaigns/ads-account/update/{{$googleadsac->id}}" class="btn-image"><img src="/images/edit.png"></a>
        @if(Auth::user()->hasRole('Admin'))
        {{ html()->form('DELETE', route('googleadsaccount.deleteGoogleAdsAccount', [$googleadsac->id]))->style('display:inline')->open() }}
            <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
        {{ html()->form()->close() }}
        @endif
        <a href="/google-campaigns?account_id={{$googleadsac->id}}" class="btn btn-sm">create campaign</a>
    </td>
</tr>
@endforeach