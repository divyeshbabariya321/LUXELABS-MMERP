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
            <h2 class="page-heading">Google AdWords - Ads Report ( <span id="ads_campaign_count">{{$totalNumEntries}} </span>)</h2>

                <form action="{{route('googleadreport.index')}}" method="get">

                    <div class="col-md-1 pr-2">
                        <select  name="account_id" class="form-control" id="account_id">
                            <option value="">Account Name</option>
                            @foreach(@$accounts as $account)
                                <option value="{{$account->id}}" {{($account->id == @$_GET['account_id'])? 'selected' :''}}>{{$account->account_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 pr-2">
                        <select  name="campaign_id" class="form-control" id="campaign_id">
                            <option value="">Campaign Name</option>
                            @foreach(@$campaigns as $campaign)
                                <option value="{{$campaign->google_campaign_id}}" {{($campaign->google_campaign_id == @$_GET['campaign_id'])? 'selected' :''}}>{{$campaign->campaign_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 pr-2">
                        <select  name="adgroup_id" class="form-control" id="adgroup_id">
                            <option value="">Ad Group Name</option>
                            @foreach(@$adgroups as $adgroup)
                                <option value="{{$adgroup->google_adgroup_id}}" {{($adgroup->google_adgroup_id == @$_GET['adgroup_id'])? 'selected' :''}}>{{$adgroup->ad_group_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 pr-2">
                        <select  name="campaign_status" class="form-control" id="campaign_status">
                            <option value="">Campaign Status</option>
                            <option value="ENABLED" {{( @$_GET['campaign_status'] == "ENABLED" )? 'selected' :''}} >Enabled</option>
                            <option value="PAUSED" {{( @$_GET['campaign_status'] == "PAUSED" )? 'selected' :''}} >Paused</option>
                        </select>
                    </div>

                    <div class="col-md-1 pr-2">
                        <input name="start_date" type="date" class="form-control" value="{{isset($_GET['start_date'])?$_GET['start_date']:''}}" placeholder="Start Date" id="start_date">
                    </div>

                    <div class="col-md-1 pr-2">
                        <input name="end_date" type="date" class="form-control" value="{{isset($_GET['end_date'])?$_GET['end_date']:''}}" placeholder="End Date" id="end_date">
                    </div>

                    <div class="col-md-1 pr-1">
                        <button type="submit" class="btn btn-image"><img src="{{asset('/images/filter.png')}}" /></button>
                        <a href="{{route('googleadreport.index')}}" type="button" class="btn btn-image refresh-table" title="Refresh"><img src="{{asset('/images/resend2.png')}}" /></a>
                    </div>
                </form>
        </div>
    </div>

    <div class="container-fluid p-0" style="margin-top: 10px">

        <div class="pl-3 pr-3">
            <div class="table-responsive mt-3">
                {{ $records->links() }}
                <table class="table table-bordered" id="adscampaign-table">
                    <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Account Id</th>
                        <th>Account Name</th>
                        <th>Google Customer Id</th>
                        <th>Campaign Name</th>
                        <th>Google Campaign Id</th>
                        <th>Channel Type</th>
                        <th>Ad Group Name</th>
                        <th>Google Ad Group Id</th>
                        <th>Ad Headline1</th>
                        <th>Google Ad Id</th>
                        <th>Campaign Status</th>
                        <th>Impression</th>
                        <th>Click</th>
                        <th>Cost Micros</th>
                        <th>Average CPC</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($records as $record)
                        <tr>
                            <td>{{ $record->id}}</td>
                            <td>{{ $record->account->id ?? ""}}</td>
                            <td>{{ $record->account->account_name ?? ""}}</td>
                            <td>{{ $record->account->google_customer_id ?? ""}}</td>
                            <td>{{ $record->campaign->campaign_name ?? ""}}</td>
                            <td>{{ $record->campaign->google_campaign_id ?? ""}}</td>
                            <td>{{ ucwords(strtolower(str_replace("_", " ", $record->campaign->channel_type ?? "")))}}</td>
                            <td>{{ $record->adgroup->ad_group_name ?? ""}}</td>
                            <td>{{ $record->adgroup->google_adgroup_id ?? ""}}</td>
                            <td>{{ $record->search_ad->headline1 ?? $record->display_ad->headline1 ?? $record->multi_channel_ad->headline1 ?? ""}}</td>
                            <td>{{ $record->google_ad_id ?? ""}}</td>
                            <td>{{ $record->campaign->status ?? ""}}</td>
                            <td>{{ $record->sum_impression ?? ""}}</td>
                            <td>{{ $record->sum_click ?? ""}}</td>
                            <td>{{ $record->sum_cost_micros ?? ""}}</td>
                            <td>{{ $record->sum_average_cpc ?? ""}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection