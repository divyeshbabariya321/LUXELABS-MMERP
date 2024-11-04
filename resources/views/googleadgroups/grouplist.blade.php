@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="col-md-12">

        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h4 class="page-heading">Google Ads Group List(<span id="ads_account_count">{{ $totalentries }}</span>)</h4>

                <form action="{{route('googleadsaccount.adsgroupslist')}}" method="get">

                    <div class="col-md-1 pr-2 p-0">
                        <select name="campaign_name" class="form-control" id="campaign_name">
                            <option value="">Google Campaign Name</option>
                            @foreach($search_data->unique('adgroup_google_campaign_id') as $campaign)
                                <option value="{{@$campaign->campaign->google_campaign_id}}" {{(@$campaign->campaign->google_campaign_id == @$_GET['campaign_name'])? 'selected' :''}}>{{@$campaign->campaign->campaign_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 pr-2">
                        <select name="channel_type" class="form-control" id="channel_type">
                            <option value="">Ad Group Name</option>
                            @foreach($search_data->unique('ad_group_name') as $ad_group_name)
                                <option value="{{@$ad_group_name->ad_group_name}}" {{(@$ad_group_name->ad_group_name == @$_GET['channel_type'])? 'selected' :''}}>{{@$ad_group_name->ad_group_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 pr-2">
                        <input name="created_at" type="date" class="form-control" value="{{isset($_GET['created_at'])?$_GET['created_at']:''}}" placeholder="Created At" id="created_at">
                    </div>

                    <div class="col-md-1 pr-1">
                        <button type="submit" class="btn btn-image"><img src="{{asset('/images/filter.png')}}" /></button>
                        <a href="{{route('googleadsaccount.adsgroupslist')}}" type="button" class="btn btn-image refresh-table" title="Refresh"><img src="{{asset('/images/resend2.png')}}" /></a>
                    </div>
                </form>
            </div>
        </div>

        {{ $adsgroups->links() }}
        <table class="table table-bordered" id="adsgroup-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Account name</th>
                <th>Google Campaign Name</th>
                <th>Google Campaign Id</th>
                <th>Google Adgroupd Id</th>
                <th>Ads Group Name</th>
                <th>Created At</th>
            </tr>
            </thead>

            <tbody>
            @foreach($adsgroups as $adsGroup)
                <tr>
                    <td>{{$adsGroup->id}}</td>
                    <td>{{$adsGroup->campaign->account->account_name}}</td>
                    <td>{{$adsGroup->campaign->campaign_name}}</td>
                    <td>{{$adsGroup->adgroup_google_campaign_id}}</td>
                    <td>{{$adsGroup->google_adgroup_id}}</td>
                    <td>{{$adsGroup->ad_group_name}}</td>
                    <td>{{$adsGroup->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
