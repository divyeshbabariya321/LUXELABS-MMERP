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
        <h4 class="page-heading">Google Responsive Display Ads (<span id="ads_count">{{$totalNumEntries}}</span>) for {{$groupname}} AdsGroup <button class="btn-image float-right custom-button" onclick="window.location.href='/google-campaigns/{{$campaignId}}/adgroups';">Back to Ad groups</button></h4>


        <div class="pull-left" style="margin-top:15px;">
            <div class="form-group">
                <div class="row">
                    
                <div class="col-md-2">
                        <input name="headline" type="text" class="form-control" value="{{ isset($headline) ? $headline : '' }}" placeholder="Headline" id="headline">
                    </div>
                    
                    <div class="col-md-2">
                        <input name="business_name" type="text" class="form-control" value="{{ isset($business_name) ? $business_name : '' }}" placeholder="Business name" id="business_name">
                    </div>

                    <div class="col-md-2">
                        <input name="final_url" type="text" class="form-control" value="{{ isset($final_url) ? $final_url : '' }}" placeholder="Final URL" id="final_url">
                    </div>

                    <div class="col-md-2">
                        <select class="browser-default custom-select" id="ads_status" name="ads_status" style="height: auto">
                        <option value="">--Status--</option>
                        <option value="ENABLED">Enabled</option>
                        <option value="PAUSED">Paused</option>
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


        <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#responsive-display-ad-create">Create</button>

        <table class="table table-bordered" id="ads-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Headline 1</th>
                <th>Business Name</th>
                <th>Final Url</th>
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
                    <td>{{$ad->business_name}}</td>
                    <td>{{$ad->final_url}}</td>
                    <td>{{$ad->status}}</td>
                    <td>{{$ad->created_at}}</td>
                    <td>
                        <div class="d-flex">
                            <a href="{{ route('responsive-display-ad.show', [$campaignId, $adGroupId, $ad['google_ad_id']]) }}" class="btn btn-image text-dark" title="View"><i class="fa fa-eye"></i></a>
                            {{ html()->form('DELETE', route('responsive-display-ad.deleteAd', [$campaignId, $adGroupId, $ad['google_ad_id']]))->style('display:inline')->open() }}
                                <button type="submit" class="btn btn-image" title="Delete"><img src="/images/delete.png"></button>
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
                        <h2>Create Responsive Display Ad</h2>
                    </div>
                    <form method="POST" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/responsive-display-ad/create" enctype="multipart/form-data">
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
                            <label for="final_url" class="col-sm-2 col-form-label">Final URL</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="final_url" name="final_url" placeholder="http://www.example.com" value="{{ old('final_url') }}">
                                @if ($errors->has('final_url'))
                                    <span class="text-danger">{{$errors->first('final_url')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="long_headline" class="col-sm-2 col-form-label">Long headline</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="long_headline" name="long_headline" placeholder="Long headline" value="{{ old('long_headline') }}">
                                @if ($errors->has('long_headline'))
                                    <span class="text-danger">{{$errors->first('long_headline')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="business_name" class="col-sm-2 col-form-label">Business name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="business_name" name="business_name" placeholder="Business name" value="{{ old('business_name') }}">
                                @if ($errors->has('business_name'))
                                    <span class="text-danger">{{$errors->first('business_name')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="marketing_images" class="col-sm-2 col-form-label">Marketing Images</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="marketing_images" name="marketing_images[]" multiple>
                                <span class="text-muted">Note: Valid image types are GIF, JPEG, and PNG. The minimum size is 600x314 and the aspect ratio must be 1.91:1 (+-1%). Allow maximum is 15 image.</span><br>

                                @if ($errors->has('marketing_images'))
                                    <span class="text-danger">{{$errors->first('marketing_images')}}</span>
                                @endif

                                @if ($errors->has('marketing_images.*'))
                                    <span class="text-danger">Please upload valid images.</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="square_marketing_images" class="col-sm-2 col-form-label">Square Marketing Images</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="square_marketing_images" name="square_marketing_images[]" multiple>
                                <span class="text-muted">Note: Valid image types are GIF, JPEG, and PNG. The minimum size is 300x300 and the aspect ratio must be 1:1 (+-1%). Allow maximum is 15 image.</span><br>

                                @if ($errors->has('square_marketing_images'))
                                    <span class="text-danger">{{$errors->first('square_marketing_images')}}</span>
                                @endif

                                @if ($errors->has('square_marketing_images.*'))
                                    <span class="text-danger">Please upload valid images.</span>
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
        src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/responsive-display-ad';
        headline = $('#headline').val();
        business_name = $('#business_name').val();
        final_url = $('#final_url').val();
        ads_status = $('#ads_status').val();

        $.ajax({
            url: src,
            dataType: "json",
            data: {
                headline : headline,
                business_name :business_name,
                final_url :final_url,
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
        src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/responsive-display-ad';
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
            business_name = $('#business_name').val('');
            final_url = $('#final_url').val('');
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
