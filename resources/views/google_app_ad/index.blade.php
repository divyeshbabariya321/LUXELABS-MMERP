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
        <h4 class="page-heading">Google App Ads (<span id="ads_count">{{$totalNumEntries}}</span>) for {{$groupname}} AdsGroup <button class="btn-image float-right custom-button" onclick="window.location.href='/google-campaigns/{{$campaignId}}/adgroups';">Back to Ad groups</button></h4>


        <div class="pull-left" style="margin-top:15px;">
            <div class="form-group">
                <div class="row">
                    
                    <div class="col-md-4">
                        <input name="headline" type="text" class="form-control" value="{{ isset($headline) ? $headline : '' }}" placeholder="Headline" id="headline">
                    </div>

                    <div class="col-md-4">
                        <select class="browser-default custom-select" id="ads_status" name="ads_status" style="height: auto">
                        <option value="">--Status--</option>
                        <option value="ENABLED">Enabled</option>
                        {{-- <option value="PAUSED">Paused</option> --}}
                        {{-- <option value="DISABLED">Disabled</option> --}}
                    </select>

                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="{{asset('/images/filter.png')}}" /></button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="{{asset('/images/resend2.png')}}" /></button>
                    </div>
                </div>
            </div>
        </div>


        @if(count($ads) == 0)
            <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#app-ad-create">Create</button>
        @endif

        <table class="table table-bordered" id="ads-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Headline 1</th>
                <th>Headline 2</th>
                <th>Headline 3</th>
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
                    <td>{{$ad->status}}</td>
                    <td>{{$ad->created_at}}</td>
                    <td>
                        <div class="d-flex">
                            <a href="{{ route('app-ad.show', [$campaignId, $adGroupId, $ad['google_ad_id']]) }}" class="btn btn-image text-dark" title="View"><i class="fa fa-eye"></i></a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $ads->links() }}
    </div>

    <div class="modal fade" id="app-ad-create" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create App Ad</h2>
                    </div>
                    <form method="POST" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/app-ad/create" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Headline 1</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="headline1" name="headline1" placeholder="Headline" value="{{ old('headline1') }}">
                                @if ($errors->has('headline1'))
                                    <span class="text-danger">{{$errors->first('headline1')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="headline2" class="col-sm-2 col-form-label">Headline 2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="headline2" name="headline2" placeholder="Headline" value="{{ old('headline2') }}">
                                @if ($errors->has('headline2'))
                                    <span class="text-danger">{{$errors->first('headline2')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="headline3" class="col-sm-2 col-form-label">Headline 3</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="headline3" name="headline3" placeholder="Headline" value="{{ old('headline3') }}">
                                @if ($errors->has('headline3'))
                                    <span class="text-danger">{{$errors->first('headline3')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description1" class="col-sm-2 col-form-label">Description 1</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="description1" name="description1" placeholder="Description" value="{{ old('description1') }}">
                                @if ($errors->has('description1'))
                                    <span class="text-danger">{{$errors->first('description1')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description2" class="col-sm-2 col-form-label">Description 2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="description2" name="description2" placeholder="Description" value="{{ old('description2') }}">
                                @if ($errors->has('description2'))
                                    <span class="text-danger">{{$errors->first('description2')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="images" class="col-sm-2 col-form-label">Images</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="images" name="images[]" multiple>
                                <span class="text-muted">Note: Valid image types are GIF, JPEG, and PNG. You can upload up to 20 image.</span><br>

                                @if ($errors->has('images'))
                                    <span class="text-danger">{{$errors->first('images')}}</span>
                                @endif

                                @if ($errors->has('images.*'))
                                    <span class="text-danger">Please upload valid images.</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="youtube_video_ids" class="col-sm-2 col-form-label">Youtube Video IDs</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="youtube_video_ids" name="youtube_video_ids" placeholder="Example: K4fdufdUd3Y,En5nHj7sR8g" value="{{ old('youtube_video_ids') }}">
                                <span class="text-muted">Note: YouTube video id is the 11 character string value used in the YouTube video URL. You can add up to 20 id.</span><br>
                                @if ($errors->has('youtube_video_ids'))
                                    <span class="text-danger">{{$errors->first('youtube_video_ids')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ad-status" class="col-sm-2 col-form-label">Ad status</label>
                            <div class="col-sm-10">
                                <select class="browser-default custom-select" id="ad-status" name="adStatus" style="height: auto">
                                    <option value="0" selected>Enabled</option>
                                    {{-- <option value="1">Paused</option> --}}
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal" aria-label="Close">Close</button>
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
    $('.select-multiple').select2({width: '100%'});

    function submitSearch(){
        src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/app-ad';
        headline = $('#headline').val();
        ads_status = $('#ads_status').val();

        $.ajax({
            url: src,
            dataType: "json",
            data: {
                headline : headline,
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
        src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/app-ad';
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
