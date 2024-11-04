@extends('layouts.app')
@section('title', 'Pinterest Ads Account')
@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

        .btn-secondary, .btn-secondary:focus, .btn-secondary:hover {
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

        .link-button, .link-button:hover, .link-button:focus {
            text-decoration: none;
            line-height: 1.4;
        }
    </style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                {!! $pinterestBusinessAccountMail->pinterest_account !!} Ads account (<span
                        id="affiliate_count">{{ $pinterestAdsAccounts->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('pinterest.accounts.dashboard', [$pinterestBusinessAccountMail->id])}}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <input name="name" type="text" class="form-control"
                                       value="{{ request('name') }}" placeholder="Search name">
                            </div>
                            <div class="col-md-3">
                                <input name="country" type="text" class="form-control"
                                       value="{{ request('country') }}" placeholder="Search country">
                            </div>
                            <div class="col-md-3">
                                <input name="currency" type="text" class="form-control"
                                       value="{{ request('currency') }}" placeholder="Search currency">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('pinterest.accounts.dashboard', [$pinterestBusinessAccountMail->id])}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 pl-0 float-right">
                <button data-toggle="modal" data-target="#create-ads-account" type="button"
                        class="float-right mb-3 mr-2 btn-secondary">New Ads Account
                </button>
                <a href="{!! route('pinterest.accounts.board.index', [$pinterestBusinessAccountMail->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Boards
                </a>
                <a href="{!! route('pinterest.accounts.boardSections.index', [$pinterestBusinessAccountMail->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Board Sections
                </a>
                <a href="{!! route('pinterest.accounts.pin.index', [$pinterestBusinessAccountMail->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Pins
                </a>
                <a href="{!! route('pinterest.accounts.campaign.index', [$pinterestBusinessAccountMail->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Campaigns
                </a>
                <a href="{!! route('pinterest.accounts.adsGroup.index', [$pinterestBusinessAccountMail->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Ads Group
                </a>
                <a href="{!! route('pinterest.accounts.ads.index', [$pinterestBusinessAccountMail->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Ads
                </a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="affiliates-table">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Country</th>
                <th>Currency</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($pinterestAdsAccounts as $key => $pinterestAdsAccount)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $pinterestAdsAccount->ads_account_name }}</td>
                    <td>{{ $pinterestAdsAccount->ads_account_country }}</td>
                    <td>{{ $pinterestAdsAccount->ads_account_currency }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $pinterestAdsAccounts->render() !!}
    <div class="modal fade" id="create-ads-account" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Ads Account</h2>
                    </div>
                    @include('pinterest._partials.ads-account-create')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

    <script type="text/javascript">
        let showPopup;
        @if(Session::get('create_popup'))
            showPopup = true;
        @endif

        if (showPopup) {
            $('#create-ads-account').modal('show');
        }
    </script>
@endsection
