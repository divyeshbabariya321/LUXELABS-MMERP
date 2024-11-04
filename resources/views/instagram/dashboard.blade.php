@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram Dashboard</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card text-white bg-dark mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Automated Reply / DM</h3>
                    <h1 class="card-title">{{ $automatedMessages }}</h1>
                    <div class="text-right">
                        <a class="text-light" href="{{ action([\App\Http\Controllers\InstagramAutomatedMessagesController::class, 'index']) }}">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Comments Today</h3>
                    <h1 class="card-title">{{ $commentsToday }}</h1>
                    <div class="text-right">
                        <a class="text-light" href="{{ action([\App\Http\Controllers\InstagramAutomatedMessagesController::class, 'show'], '1') }}">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Comments Total</h3>
                    <h1 class="card-title">{{ $commentsTotal }}</h1>
                    <div class="text-right">
                        <a class="text-dark" href="{{ action([\App\Http\Controllers\InstagramAutomatedMessagesController::class, 'show'], 1) }}">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Normal Accounts</h3>
                    <h1 class="card-title">{{ $accounts }}</h1>
                    <div class="text-right">
                        <a href="{{ action([\App\Http\Controllers\InstagramController::class, 'accounts']) }}" class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Influencers Accounts</h3>
                    <h1 class="card-title">{{ $influencersTotal }}</h1>
                    <div class="text-right">
                        <a href="{{ action([\App\Http\Controllers\InfluencersController::class, 'index']) }}" class="text-dark">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Influencers DM Today</h3>
                    <h1 class="card-title">{{ $infDmToday }}</h1>
                    <div class="text-right">
                        <a href="{{ action([\App\Http\Controllers\InfluencersController::class, 'show'], 1) }}" class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Influencers DM Total</h3>
                    <h1 class="card-title">{{ $infDm }}</h1>
                    <div class="text-right">
                        <a href="{{ action([\App\Http\Controllers\InfluencersController::class, 'show'], 1)  }}" class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Posts Today</h3>
                    <h1 class="card-title">{{ $accounts }}</h1>
                    <div class="text-right">
                        <a class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Brand Tagged Users</h3>
                    <h1 class="card-title">{{ \App\BrandTaggedPosts::get()->count() }}</h1>
                    <div class="text-right">
                        <a class="text-light" href="{{ action([\App\Http\Controllers\BrandTaggedPostsController::class, 'index']) }}">Show</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-dark mb-3" style="width: 100%;">
                <div class="card-body">
                    <h3 class="card-title">Filtered Comments</h3>
                    <h1 class="card-title">{{ $accounts }}</h1>
                    <div class="text-right">
                        <a class="text-light">Show</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/media-card.css') }} ">
    <style>
        .card {
            display: inline-block;
        }
    </style>
@endsection

@section('scripts')

@endsection