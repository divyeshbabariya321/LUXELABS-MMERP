@extends('layouts.app')

@section('title', __('Posts'))

@section('content')
<?php

?>
    @if($collection)

        <div class="page-header">
            <h1 class="page-title">
                {{ __('Posts') }}
            </h1>
            

        @if($collection)
        <div class="row row-cards">
            @foreach($collection as $key=>$post) 
           
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    @if($post["url"])
                        <a href="" target="_blank">
                            <img src="{{ $post['url'] }}" alt="" height="250px" width="250px">
                        </a>
                    @else
                        
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/1200px-No_image_available.svg.png" alt="" height="250px" width="250px">
                        
                    @endif

                    <div class="card-body d-flex flex-column p-3">
                    @if(!empty($post["text"]))
                    <div><a href="" target="_blank" class="text-default"><strong>{{ $post["text"] }}</strong></a></div>

                    @else
                    <div><a href="" target="_blank" class="text-default"><strong>{{ $post["message"] }}</strong></a></div>

                    @endif
                        
                      
                    </div>

                </div>
            </div> 
            @endforeach
        </div>
        @endif

        @if(!$collection)
            <div class="alert alert-primary text-center">
                <i class="fe fe-alert-triangle mr-2"></i> {{ __('No posts found') }}
            </div>
        @endif

       

    @endif

@stop