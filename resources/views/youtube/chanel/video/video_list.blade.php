@extends('layouts.app')

@section('title', __('New post'))

@section('content')

<link rel="stylesheet" href="{{ mix('webpack-dist/css/instagram.css') }} ">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropme@latest/dist/cropme.min.css">   
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">


<style>
    .imagecheck-image{
        height: 100px !important;
        width: 100px !important
    }
    .light-blue.lighten-2 {
        background-color: #4fc3f7 !important;
        margin: 0px 4px;
    }   
    .light-blue.lighten-2 a {
        color: #fff;
        
    }
    div#auto {
        padding: 15px 0px;
    }
    div#show_hashtag_field {
        padding: 15px 0px;
    }
    #loading-image {
        position: fixed;
        top: 50%;
        left: 50%;
        margin: -50px 0px 0px -50px;
        z-index: 60;
    }
    .toast-success {
        background-color: rgb(81, 163, 81);
    }
</style>

<?php /* @includeWhen($accounts->count() == 0, 'partials.no-accounts') */ ?>


<div class = "row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Instagram Posts</h2>
    </div>
</div>
@if(Session::has('message'))
    <p class="alert alert-info">{{ Session::get('message') }}</p>
@endif
<div class="col-md-12 pl-xl-0">
<div class = "row">
    <div class="col-md-10 margin-tb">
        <div class="cls_filter_box">
            <form class="form-inline" action="{{ url('instagram/post/create') }}" method="GET">
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" id="select_date" name="select_date" value="Select Date"  class="form-control">
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <select name="acc" class="form-control">
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" @if(request()->get('acc')==$account->id) selected @endif>{{ $account->last_name }}</option>
                        @endforeach
                   </select>
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" name="comm" class="form-control" value="{{request()->get('comm')}}" placeholder="Comment">
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" name="tags" class="form-control" value="{{request()->get('tags')}}" placeholder="Hashtags">
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <select name="type" class="form-control">
                        <option value="">Select Type</option>
                        <option value="post" @if(request()->get('type')=='post') selected @endif>Post</option>
                        <option value="album" @if(request()->get('type')=='album') selected @endif>Album</option>
                        <option value="story" @if(request()->get('type')=='story') selected @endif>Story</option>
                    </select>
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" name="loc" class="form-control" value="{{request()->get('loc')}}" placeholder="Location">
                </div>
                <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
            </form> 
        </div>
    </div>  
    <div class="col-md-2 margin-tb">
        <div class="pull-right mt-3"style="margin-top:2px !important;">

            <button type="button" class=" btn btn-success btn-block custom-button btn-publish mt-0" data-toggle="modal" data-target="#add-vendor-info-modal" title="" data-id="1" >Create Post</button>
        </div>
    </div>
</div>
</div>
<div class="col-md-12 -5">
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="panel mt-3 panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                       Posts
                    </h4>
                </div>
    </div>
</div>
<div class="col-md-12">
    <div class="row">
                <div class="mt-3">
                    <table class="table table-bordered table-striped"style="table-layout: fixed;">
                        <tr>
                            <th width="9%">Date</th>
                            <th width="8%">Account</th>
                            <th width="13%">Comment</th>
                            <th width="8%">Hash Tags</th>
                            <th width="7%">Schedule date</th>
                            <th width="8%">Type</th>
                            <th width="5%">Location</th>
                            <th width="7%">Instagram Link</th>
                            <th width="6%">Status</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($posts as $post)
                            
                            <tr id="{{$post->id}}" class="post-row">
                                <td>{{$post->created_at}}</td>

                                <td>
                                    <select class="form-control post_account_id">
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" @if($post->account_id==$account->id) selected @endif>{{ $account->last_name }}</option>
                                        @endforeach
                                   </select>
                                </td>
                                <td>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control post_comment" value="{{$post->comment}}">
                                    </div>
                                    <button class="btn btn-sm mt-0 btn-image btn-update-comment"><img src="/images/filled-sent.png"></button>
                                </td>
                                <td>
                                    <button type="button" data-hashtag = '{{$post->hashtags}}' data-id = '{{$post->id}}' class="btn btn-primary custom-button" data-toggle="modal" data-target="#show-hashtag-model" title=""style="border: 1px solid #bfbaba;">Show Hashtags</button>
                                </td>
                                <td>{{$post->scheduled_at}}</td>
                                <td>
                                    <select name="post_type" class="form-control post-type-select">
                                        <option value="post" @if($post->type=='post') selected @endif>Post</option>
                                        <option value="album" @if($post->type=='album') selected @endif>Album</option>
                                        <option value="story" @if($post->type=='story') selected @endif>Story</option>
                                    </select>
                                </td>
                                <td>{{$post->location}}</td>
                                <!--td>{{$post->ig}}</td-->
                               
                                <td class="Website-task">
                                    @foreach($accounts as $account)
                                        @if($post->account_id==$account->id)
                                            <a href='https://www.instagram.com/{{$account->last_name}}'style="color: black;">https://www.instagram.com/{{$account->last_name}}</a>
                                            @php
                                            break;
                                            @endphp
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{$post->status == 1 ? "Published" : "Not Published"}}</td>
                                <td>
                                    <a href="{{url('instagram/post/publish-post')}}/{{$post->id}}" class="btn custom-button btn-primary"style="border: 1px solid #bfbaba;" >Publish</a>
                                    <!--button type="button" class="btn-post-save" data-toggle="modal" title="" data-id="{{$post->id}}">Update</button-->
                                    <!--button type="button" class="btn-post-publish" data-toggle="modal" data-id="{{$post->id}}">Publish</button-->
                                    
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
        </div>
    </div>
</div>