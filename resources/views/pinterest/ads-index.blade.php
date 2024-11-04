@extends('layouts.app')
@section('title', 'Pinterest Ads')
@section('styles')
    <style type="text/css">
        #myDiv {
            width: 100%;
            position: fixed;
            z-index: 99999;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }

        #loading-image {
            position: fixed;
            top: 50%;
            right: 50%;
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

        legend {
            display: block;
            width: auto;
            max-width: 100%;
            padding: 0;
            margin-bottom: 0;
            font-size: 1.5rem;
            line-height: inherit;
            color: inherit;
            white-space: normal;
            border-bottom: none !important;
        }

        fieldset {
            padding: 10px 10px;
            margin: 0 2px;
            border: 1px solid #c0c0c07a;
            border-radius: 4px;
        }

        .note {
            font-size: 10px;
        }
    </style>
@endsection
@section('content')
    <div id="myDiv" style="display:none;">
        <img id="loading-image" src="/images/pre-loader.gif"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                {!! $pinterestBusinessAccountMail->pinterest_account !!} Ads (<span
                        id="affiliate_count">{{ $pinterestAds->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('pinterest.accounts.ads.index', [$pinterestBusinessAccountMail->id])}}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <input name="name" type="text" class="form-control"
                                       value="{{ request('name') }}" placeholder="Search name">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('pinterest.accounts.ads.index', [$pinterestBusinessAccountMail->id])}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 pl-0 float-right">
                <button data-toggle="modal" data-target="#create-board" type="button"
                        class="float-right mb-3 mr-2 btn-secondary">New Ad
                </button>
                <a href="{!! route('pinterest.accounts.dashboard', [$pinterestBusinessAccountMail->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">Back to Dashboard
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
                <th>Ads Account</th>
                <th>Pin</th>
                <th>Ads Group</th>
                <th>Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($pinterestAds as $key => $pinterestAd)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $pinterestAd->account->ads_account_name }}</td>
                    <td>{{ $pinterestAd->pin->title }}</td>
                    <td>{{ $pinterestAd->adsGroup->name }}</td>
                    <td>{{ $pinterestAd->name }}</td>
                    <td>{{ $pinterestAd->status }}</td>
                    <td>
                        <button type="button" data-toggle="modal" data-target="#update-board"
                                onclick="editData('{!! $pinterestAd->id !!}')"
                                class="btn btn-image"><img src="/images/edit.png"></button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $pinterestAds->render() !!}
    <div class="modal fade" id="create-board" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Ad</h2>
                    </div>
                    @include('pinterest._partials.ads-create')
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="update-board" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Update Ad</h2>
                    </div>
                    @include('pinterest._partials.ads-update')
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
        let showEditPopup;
        @if(Session::get('create_popup'))
            showPopup = true;
        @endif
                @if(Session::get('update_popup'))
            showEditPopup = true;
        @endif

        if (showPopup) {
            $('#create-board').modal('show');
        }

        if (showEditPopup) {
            $('#update-board').modal('show');
        }

        function editData(id) {
            let url = "{{ route('pinterest.accounts.ads.get', [$pinterestBusinessAccountMail->id, ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                url,
                type: 'GET',
                beforeSend: function () {
                    $("#myDiv").show();
                },
                success: function (response) {
                    $("#myDiv").hide();
                    if (!response.status) {
                        toastr["error"](response.message);
                        $('#update-board').modal('hide');
                    } else {
                        $('#edit_ads_id').val(id);
                        $('#edit_pinterest_ad_group_id').val(response.data.pinterest_ads_group_id);
                        $('#edit_pinterest_pin_id').val(response.data.pinterest_pin_id);
                        $('#edit_name').val(response.data.name);
                        $('#edit_status').val(response.data.status);
                        $('#edit_creative_type').val(response.data.creative_type);
                        $('#edit_destination_url').val(response.data.destination_url);
                    }
                }
            })
        }
    </script>
@endsection
