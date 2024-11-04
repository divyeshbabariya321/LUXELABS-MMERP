@extends('layouts.app')
@section('favicon' , 'task.png')
@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
        #create-compaign .modal-dialog {
            max-width: 1024px !important;
            width: 1024px !important;
        }
        .btn-secondary, .btn-secondary:focus, .btn-secondary:hover{
            background: #fff;
            color: #757575;
            border: 1px solid #ddd;
            height: 32px;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: 100;
            line-height: 10px;
        }

    </style>
@endsection
@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Ads List(<span id="ads_campaign_count">{{$totalNumEntries}}</span>)</h2>

            <form action="{{route('googlecampaigns.adslist')}}" method="get">

                <div class="col-md-1 pr-2">
                    <select name="campaign_name" class="form-control" id="campaign_name">
                        <option value="">Google Campaign Name</option>
                        @foreach($search_data->unique('adgroup_google_campaign_id') as $campaign)
                            <option value="{{@$campaign->campaign->google_campaign_id}}" {{(@$campaign->campaign->google_campaign_id == @$_GET['campaign_name'])? 'selected' :''}}>{{@$campaign->campaign->campaign_name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1 pr-2">
                    <select name="ad_group_name" class="form-control" id="ad_group_name">
                        <option value="">Ad Group Name</option>
                        @foreach($search_data->unique('google_adgroup_id') as $ad)
                            <option value="{{@$ad->adgroup->google_adgroup_id}}" {{(@$ad->adgroup->google_adgroup_id == @$_GET['ad_group_name'])? 'selected' :''}}>{{@$ad->adgroup->ad_group_name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1 pr-2">
                    <select name="google_ad_id" class="form-control" id="google_ad_id">
                        <option value="">Google Ad Id</option>
                        @foreach($search_data->unique('google_adgroup_id') as $google_ad_id)
                            <option value="{{@$google_ad_id->google_ad_id}}" {{(@$google_ad_id->google_ad_id == @$_GET['google_ad_id'])? 'selected' :''}}>{{@$google_ad_id->google_ad_id}}</option>
                        @endforeach
                    </select>
                </div>

{{--                <div class="col-md-1 pr-2">--}}
{{--                    <input name="created_at" type="date" class="form-control" value="{{isset($_GET['created_at'])?$_GET['created_at']:''}}" placeholder="Created At" id="created_at">--}}
{{--                </div>--}}

                <div class="col-md-1 pr-1">
                    <button type="submit" class="btn btn-image"><img src="{{asset('/images/filter.png')}}" /></button>
                    <a href="{{route('googlecampaigns.adslist')}}" type="button" class="btn btn-image refresh-table" title="Refresh"><img src="{{asset('/images/resend2.png')}}" /></a>
                </div>
            </form>
        </div>
    </div>

    <div class="container-fluid p-0" style="margin-top: 10px">

        <div class="pl-3 pr-3">
            <div class="table-responsive mt-3">
                {{ $adslist->links() }}
                <table class="table table-bordered" id="adscampaign-table">
                    <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Account Name</th>
                        <th>Campaign Name</th>
                        <th>Ads Group Name</th>
                        <th>Google Ad Id</th>
                        <th>Headline 1</th>
                        <th>Created At</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if(!empty($adslist))
                        @foreach($adslist as $ad)
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
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        {{--        {{ $campaignslist->links() }}--}}
    </div>
@endsection