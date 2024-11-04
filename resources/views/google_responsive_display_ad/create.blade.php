@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Create Responsive Display Ad</h2>
        
        <div class="mt-2">
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
                <button type="submit" class="mb-2 float-right custom-button btn">Create</button>
                <a href="{{ url("/google-campaigns/$campaignId/adgroups/$adGroupId/responsive-display-ad")}} " class="mb-2 mr-4 float-right custom-button btn">Back</a>
            </form>
        </div>

    </div>

@endsection