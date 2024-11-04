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
{{--    <div class="container" style="margin-top: 10px">--}}
    <div class="col-md-12">
    <h4 class="page-heading">Google Ads (<span id="ads_count">{{$totalNumEntries}}</span>) for {{$groupname}} AdsGroup <button class="btn-image" onclick="window.location.href='/google-campaigns/{{$campaignId}}/adgroups';">Back to Ad groups</button></h4>


    <div class="pull-left">
        <div class="form-group">
            <div class="row">
                
            <div class="col-md-2">
                    <input name="headline" type="text" class="form-control" value="{{ isset($headline) ? $headline : '' }}" placeholder="Headline" id="headline">
                </div>
                
                <div class="col-md-2">
                    <input name="description" type="text" class="form-control" value="{{ isset($description) ? $description : '' }}" placeholder="Description" id="description">
                </div>

                <div class="col-md-2">
                    <input name="final_url" type="text" class="form-control" value="{{ isset($final_url) ? $final_url : '' }}" placeholder="Final URL" id="final_url">
                </div>

                <div class="col-md-2">
                    <input name="path" type="text" class="form-control" value="{{ isset($path) ? $path : '' }}" placeholder="Path" id="path">
                </div>

                <div class="col-md-2">
                    <select class="browser-default custom-select" id="ads_status" name="ads_status" style="height: auto">
                    <option value="">--Status--</option>
                    <option value="ENABLED">Enabled</option>
                    <option value="PAUSED">Paused</option>
                </select>

                </div>

                <div class="col-md-1">
                    <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png" /></button>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" /></button>
                </div>
            </div>
        </div>
    </div>

    
{{--    <form method="get" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/ads/create">--}}
{{--        <button type="submit" class="float-right mb-3">New Ads</button>--}}
{{--    </form>    --}}
        <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#adsmodal">New Ads</button>

        <table class="table table-bordered" id="ads-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Headline 1</th>
                <th>Headline 2</th>
                <th>Headline 3</th>
                <th>Description 1</th>
                <th>Description 2</th>
                <th>Final Url</th>
                <th>Path 1</th>
                <th>Path 2</th>
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
                    <td>{{$ad->headline2}}</td>
                    <td>{{$ad->headline3}}</td>
                    <td>{{$ad->description1}}</td>
                    <td>{{$ad->description2}}</td>
                    <td>{{$ad->final_url}}</td>
                    <td>{{$ad->path1}}</td>
                    <td>{{$ad->path2}}</td>
                    <td>{{$ad->status}}</td>
                    <td>{{$ad->created_at}}</td>
                    <td>
                    {{ html()->form('DELETE', route('ads.deleteAd', [$campaignId, $adGroupId, $ad['google_ad_id']]))->style('display:inline')->open() }}
                <button type="submit" class="btn btn-image"><img src="/images/delete.png"></button>
                {{ html()->form()->close() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $ads->links() }}
    </div>

<div class="modal fade" id="adsmodal" role="dialog" style="z-index: 3000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="container">
                <div class="page-header" style="width: 69%">
                    <h2>Create Ad</h2>
                </div>
{{--                <form action="{{route('ads.craeteAd',['id'=> $campaignId , 'adgroups'=> $adGroupId])}}" method="POST">--}}
                <form action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/ads/create" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="form-group row">
                        <label for="headline-part1" class="col-sm-2 col-form-label">Headline part 1</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="headline-part1" name="headlinePart1" placeholder="Headline">
                            @if ($errors->has('headlinePart1'))
                                <span class="text-danger">{{$errors->first('headlinePart1')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="headline-part2" class="col-sm-2 col-form-label">Headline part 2</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="headline-part2" name="headlinePart2" placeholder="Headline">
                            @if ($errors->has('headlinePart2'))
                                <span class="text-danger">{{$errors->first('headlinePart2')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="headline-part3" class="col-sm-2 col-form-label">Headline part 3</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="headline-part3" name="headlinePart3" placeholder="Headline">
                            @if ($errors->has('headlinePart3'))
                                <span class="text-danger">{{$errors->first('headlinePart3')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description1" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="description1" name="description1" placeholder="Description">
                            @if ($errors->has('description1'))
                                <span class="text-danger">{{$errors->first('description1')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description2" class="col-sm-2 col-form-label">Description 2</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="description2" name="description2" placeholder="Description">
                            @if ($errors->has('description2'))
                                <span class="text-danger">{{$errors->first('description2')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="finalUrl" class="col-sm-2 col-form-label">Final URL</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="finalUrl" name="finalUrl" placeholder="http://www.example.com">
                            @if ($errors->has('finalUrl'))
                                <span class="text-danger">{{$errors->first('finalUrl')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="path1" class="col-sm-2 col-form-label">Path 1</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="path1" name="path1" placeholder="E.g path1 (for this kind of URL http://www.example.com/path1/)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="path2" class="col-sm-2 col-form-label">Path 2</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="path2" name="path2" placeholder="E.g path2 (for this kind of URL http://www.example.com/path1/path2/)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ad-status" class="col-sm-2 col-form-label">Ad status</label>
                        <div class="col-sm-6">
                            <select class="browser-default custom-select" id="ad-status" name="adStatus" style="height: auto">
                                <option value="0" selected>Enabled</option>
                                <option value="1">Paused</option>
                                {{-- <option value="2">Disabled</option> --}}
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-8">
                            <button type="submit" class="mb-2 float-right">Create</button>
                        </div>
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
    $('.select-multiple').select2({width: '100%'});

    function submitSearch(){
        src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/ads';
        headline = $('#headline').val();
        description = $('#description').val();
        final_url = $('#final_url').val();
        path = $('#path').val();
        ads_status = $('#ads_status').val();

        $.ajax({
            url: src,
            dataType: "json",
            data: {
                headline : headline,
                description :description,
                final_url :final_url,
                path :path,
                ads_status :ads_status,
            },
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

    function resetSearch(){
        src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/ads';
        blank = ''
        $.ajax({
            url: src,
            dataType: "json",
            data: {
               
               blank : blank, 

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            headline = $('#headline').val('');
            description = $('#description').val('');
            final_url = $('#final_url').val('');
            path = $('#path').val('');
            ads_status = $('#ads_status').val('');

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
