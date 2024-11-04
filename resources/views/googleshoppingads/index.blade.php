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
    </style>
@endsection
@section('content')
    <div class="col-md-12" style="margin-top: 10px">
        <h4 class="page-heading">Google Shopping Ads (<span id="ads_count">{{$totalNumEntries}}</span>)
            for {{$groupname}} AdsGroup
            <button class="btn-image float-right custom-button"
                    onclick="window.location.href='/google-campaigns/{{$campaignId}}/adgroups';">Back to Ad groups
            </button>
        </h4>
        <div class="pull-left" style="margin-top:15px;">
            <div class="form-group">
                <div class="row">

                    <div class="col-md-4">
                        <input name="adname" type="text" class="form-control"
                               value="{{ isset($adname) ? $adname : '' }}" placeholder="Ad name" id="adname">
                    </div>

                    <div class="col-md-4">
                        <select class="browser-default custom-select" id="ads_status" name="ads_status"
                                style="height: auto">
                            <option value="">--Status--</option>
                            <option value="ENABLED">Enabled</option>
                            <option value="PAUSED">Paused</option>
                        </select>

                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-image" onclick="submitSearch()"><img
                                    src="{{asset('/images/filter.png')}}"/></button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img
                                    src="{{asset('/images/resend2.png')}}"/></button>
                    </div>
                </div>
            </div>
        </div>


        <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal"
                data-target="#responsive-display-ad-create">Create
        </button>

        <table class="table table-bordered" id="ads-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            @foreach($ads as $ad)
                <tr>
                    <td>{{$ad->id}}</td>
                    <td>{{$ad->headline1}}</td>
                    <td>{{$ad->status}}</td>
                    <td>{{$ad->created_at}}</td>
                    <td>
                        <div class="d-flex">
                            {{ html()->form('DELETE', route('shopping-ads.deleteAd', [$campaignId, $adGroupId, $ad['google_ad_id']]))->style('display:inline')->open() }}
                            <button type="submit" class="btn btn-image" title="Delete"><img src="/images/delete.png">
                            </button>
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $ads->links() }}
    </div>

    <div class="modal fade" id="responsive-display-ad-create" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Shopping Display Ad</h2>
                    </div>
                    <form method="POST"
                          action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/shopping-ad/create"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="adname" name="adname"
                                       placeholder="Ad name" value="{{ old('adname') }}">
                                @if ($errors->has('adname'))
                                    <span class="text-danger">{{$errors->first('adname')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ad-status" class="col-sm-2 col-form-label">Ad status</label>
                            <div class="col-sm-10">
                                <select class="browser-default custom-select" id="ad-status" name="adStatus" style="height: auto">
                                    <option value="0" selected>Enabled</option>
                                    <option value="1">Paused</option>
                                    {{-- <option value="2">Disabled</option> --}}
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                                    aria-label="Close">Close
                            </button>
                            <button type="submit" class="float-right custom-button btn">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript">

        function submitSearch() {
            let src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/shopping-ad';
            let adname = $('#adname').val();

            $.ajax({
                url: src,
                dataType: "json",
                data: {adname},
                beforeSend: function () {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                $("#ads-table tbody").empty().html(data.tbody);
                $("#ads_count").text(data.count);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });

        }

        function resetSearch() {
            src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/shopping-ad';
            blank = ''
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    blank: blank,
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                $('#adname').val('');
                $("#ads-table tbody").empty().html(data.tbody);
                $("#ads_count").text(data.count);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        }
    </script>
@endsection
