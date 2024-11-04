@foreach($campaigns as $campaign)
    <tr>
        <td>{{$campaign->id}}</td>
        <td>{{$campaign->google_campaign_id}}</td>
        <td>{{$campaign->campaign_name}}</td>
        <td>{{$campaign->budget_amount}}</td>
        <td>{{$campaign->start_date}}</td>
        <td>{{$campaign->end_date}}</td>
        <td>{{$campaign->budget_uniq_id}}</td>
        <td>{{$campaign->status}}</td>
        <td>{{$campaign->created_at}}</td>
        <td>
        <form method="GET" action="/google-campaigns/{{$campaign['google_campaign_id']}}/adgroups">
            <button type="submit" class="btn btn-sm btn-link">Ad Groups</button>
        </form>
        <form method="GET" action="/google-campaigns/{{$campaign['google_campaign_id']}}/google-campaign-location">
            <button type="submit" class="btn btn-sm btn-link">Location</button>
        </form>
        {{ html()->form('DELETE', route('googlecampaigns.deleteCampaign', [$campaign['google_campaign_id']]))->style('display:inline')->open() }}
        <input type="hidden" id="delete_account_id" name="delete_account_id" value='{{$campaign->account_id}}'/>
            <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
        {{ html()->form()->close() }}
        {{ html()->form('GET', route('googlecampaigns.updatePage', [$campaign['google_campaign_id']]))->style('display:inline')->open() }}
        <input type="hidden" id="account_id" name="account_id" value='{{$campaign->account_id}}'/>
        <button type="submit" class="btn-image"><img src="/images/edit.png"></button>
        {{ html()->form()->close() }}
        </td>
    </tr>
@endforeach