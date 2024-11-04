@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Create App Ad</h2>
        
        <div class="mt-2">
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
                <button type="submit" class="mb-2 float-right custom-button btn">Create</button>
                <a href="{{ url("/google-campaigns/$campaignId/adgroups/$adGroupId/app-ad")}} " class="mb-2 mr-4 float-right custom-button btn">Back</a>
            </form>
        </div>

    </div>

@endsection