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
            <h2 class="page-heading">Google AdWords - Campaigns ( <span id="ads_campaign_count">{{$totalNumEntries}} </span>)</h2>

                <form action="{{route('googlecampaigns.campaignslist')}}" method="get">

                    <div class="col-md-1 pr-2 p-0">
                        <select  name="account_name" class="form-control" id="account_name">
                            <option value="">Account Name</option>
                            @foreach($search_data->unique('account_id') as $account)
                                <option value="{{@$account->account->id}}" {{(@$account->account->id == @$_GET['account_name'])? 'selected' :''}}>{{@$account->account->account_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 pr-2">
                        <select name="campaign_name" class="form-control" id="campaign_name">
                            <option value="">Campaign Name</option>
                            @foreach($search_data->unique('campaign_name') as $campaign)
                                <option value="{{@$campaign->campaign_name}}" {{(@$campaign->campaign_name == @$_GET['campaign_name'])? 'selected' :''}}>{{@$campaign->campaign_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 pr-2">
                        <select name="channel_type" class="form-control" id="channel_type">
                            <option value="">Channel Type</option>
                            @foreach($search_data->unique('channel_type') as $type)
                                <option value="{{@$type->channel_type}}" {{(@$type->channel_type == @$_GET['channel_type'])? 'selected' :''}}>{{@$type->channel_type}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 pr-2">
                        <select name="channel_sub_type" class="form-control" id="channel_sub_type">
                            <option value="">Channel Sub Type</option>
                            @foreach($search_data->unique('channel_sub_type')  as $sub_type)
                                <option value="{{@$sub_type->channel_sub_type}}" {{(@$sub_type->channel_sub_type == @$_GET['channel_sub_type'])? 'selected' :''}}>{{@$sub_type->channel_sub_type}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 pr-2">
                        <select name="status" class="form-control" id="status">
                            <option value="">Status</option>
                            @foreach($search_data->unique('status') as $status)
                                <option value="{{@$status->status}}" {{(@$status->status == @$_GET['status'])? 'selected' :''}}>{{@$status->status}}</option>
                            @endforeach
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
                        <a href="{{route('googlecampaigns.campaignslist')}}" type="button" class="btn btn-image refresh-table" title="Refresh"><img src="{{asset('/images/resend2.png')}}" /></a>
                    </div>
                </form>
        </div>
    </div>

    <div class="container-fluid p-0" style="margin-top: 10px">

        <div class="pl-3 pr-3">
            <div class="table-responsive mt-3">
                {{ $campaignslist->links() }}
                <table class="table table-bordered" id="adscampaign-table">
                    <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Account Id</th>
                        <th>Google Customer Id</th>
                        <th>Account Name</th>
                        <th>Campaign Name</th>
                        <th>Campaign Google Id</th>
                        <th>Channel Type</th>
                        <th>Channel Subtype</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Budget</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($campaignslist as $campaign)
                        <tr>
                            <td>{{$campaign->id}}</td>
                            <td>{{$campaign->account_id}}</td>
                            <td>{{$campaign->account->google_customer_id}}</td>
                            <td>{{$campaign->account->account_name}}</td>
                            <td>{{$campaign->campaign_name}}</td>
                            <td>{{$campaign->google_campaign_id}}</td>
                            <td>{{$campaign->channel_type}}</td>
                            <td>{{$campaign->channel_sub_type}}</td>
                            <td>{{$campaign->start_date}}</td>
                            <td>{{$campaign->end_date}}</td>
                            <td>{{$campaign->budget_amount}}</td>
                            <td>{{$campaign->status}}</td>
                            <td>{{$campaign->created_at}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection