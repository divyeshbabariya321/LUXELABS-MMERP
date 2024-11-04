@extends('layouts.app')
@section('title', 'Chat GPT')
@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 9999;
        }

        .btn-secondary, .btn-secondary:focus, .btn-secondary:hover {
            background: #fff;
            color: #757575;
            border: 1px solid #ddd;
            height: 32px;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: 100;
            text-decoration: none;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                Chat GPT Logs
            </h2>
            <div class="pull-left">
                <form action="{{route('chatgpt.index')}}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <input name="prompt" type="text" class="form-control"
                                       value="{{ request('prompt') }}" placeholder="Search prompt">
                            </div>
                            <div class="col-md-4">
                                <input name="response" type="text" class="form-control"
                                       value="{{ request('response') }}" placeholder="Search response">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('chatgpt.index')}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-2 pl-0 float-right">
                <a href="{!! route('chatgpt.request') !!}" type="button"
                   class="float-right mb-3 btn-secondary">New ChatGPT Request
                </a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="affiliates-table">
            <thead>
            <tr>
                <th>No</th>
                <th>Prompt</th>
                <th>Response</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($responses as $key => $response)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $response->prompt }}</td>
                    <td>{!! @unserialize($response->response) ? json_encode(unserialize($response->response)): $response->response !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $responses->render() !!}
@endsection
