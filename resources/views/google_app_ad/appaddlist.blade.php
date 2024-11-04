@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="col-md-12">

        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h4 class="page-heading">Google App Ads(<span id="ads_account_count">{{ $totalentries }}</span>)</h4>

                <form action="{{route('googleadsaccount.appadlist')}}" method="get">

                    <div class="col-md-1 pr-2 p-0">
                        <select name="campaign_name" class="form-control" id="campaign_name">
                            <option value="">Campaign Name</option>
                            @foreach($search_data->unique('adgroup_google_campaign_id') as $campaign)
                                <option value="{{(@$campaign->campaign->google_campaign_id)}}" {{(@$campaign->campaign->google_campaign_id == @$_GET['campaign_name'])? 'selected' :''}}>{{@$campaign->campaign->campaign_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 pr-2">
                        <select name="google_adgroup_id" class="form-control" id="google_adgroup_id">
                            <option value="">Ad Group Name</option>
                            @foreach($search_data->unique('google_adgroup_id') as $ad)
                                <option value="{{@$ad->adgroup->google_adgroup_id}}" {{(@$ad->adgroup->google_adgroup_id == @$_GET['google_adgroup_id'])? 'selected' :''}}>{{@$ad->adgroup->ad_group_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 pr-2">
                        <select name="headline1" class="form-control" id="headline1">
                            <option value="">Headline 1</option>
                            @foreach($search_data->unique('headline1') as $headline1)
                                <option value="{{@$headline1->headline1}}" {{(@$headline1->headline1 == @$_GET['headline1'])? 'selected' :''}}>{{@$headline1->headline1}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 pr-1">
                        <button type="submit" class="btn btn-image"><img src="{{asset('/images/filter.png')}}" /></button>
                        <a href="{{route('googleadsaccount.appadlist')}}" type="button" class="btn btn-image refresh-table" title="Refresh"><img src="{{asset('/images/resend2.png')}}" /></a>
                    </div>
                </form>
            </div>
        </div>

        {{ $googleappadd->links() }}
        <table class="table table-bordered" id="adsgroup-table">
            <thead>
            <tr>
                <th>#ID1</th>
                <th>Account name</th>
                <th>Google Campaign name</th>
                <th>Ads Group Name</th>
                <th>Google Ads Id</th>
                <th>Headline 1</th>
                <th>Created At</th>
            </tr>
            </thead>

            <tbody>
            @foreach($googleappadd as $ad)
                <tr>
                    <td>{{$ad->id}}</td>
                    <td>{{$ad->campaign->account->account_name ?? "N/A"}}</td>
                    <td>{{$ad->campaign->campaign_name ?? "N/A"}}</td>
                    <td>{{$ad->adgroup->ad_group_name ?? "N/A"}}</td>
                    <td>{{$ad->google_ad_id}}</td>
                    <td>{{$ad->headline1}}</td>
                    <td>{{$ad->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
