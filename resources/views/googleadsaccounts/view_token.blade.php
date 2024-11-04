@extends('layouts.app')
@section('title', 'Google Ads Account')
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
<div class="container " style="max-width: 100%;width: 100%;">
    <div class="row">
    <div class="col-md-12 p-0">
    <h4 class="page-heading">Google AdWords Account Access/Refresh Token </h4>
    </div>
    </div>
    <div class="pull-left">
        <div class="form-group">
            <div class="row"> 
                <div class="col-md-12">
                    Please update these tokens details in adsapi_php.ini file with client id & client secret key received from google cloud console.
                </div>
            </div>
            <div class="row"> 
                <div class="col-md-12">
                    Refresh Token is <strong style="color: #0037ff">{{ $refresh_token }}</strong> 
                </div>
            </div>
            <div class="row"> 
                <div class="col-md-12">
                    Access token is <strong style="color: #0037ff">{{ $access_token }}</strong>
                </div>
            </div>
            <div class="row mt-4"> 
                <div class="col-md-12">
                    <a href="{{ route('googleadsaccount.index') }}" class="btn btn-secondary">Back to Google Ads Account</a>
                </div>
            </div>
        </div>
    </div>    
</div>
@endsection

@section('scripts')

@endsection
