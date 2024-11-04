@extends('layouts.app')

@section('styles')

    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">{{ $account->name }}</h2>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-3">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Caption</th>
                <th>Type</th>
                <th>Verb</th>
                <th>Created At</th>
                <th>Action</th>
            </thead>
            <tbody>
            @forelse($posts as $key => $value)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td style="width:40%">
                        <div style="word-break: break-word;">
                            @if($value->message) {{ $value->message }} @else <small class="text-secondary">(No caption added)</small> @endif
                        </div>
                    </td>
                    <td>{{ $value->item }}</td>
                    <td>{{ $value->verb }}</td>
                    <td>{{ $value->time }}</td>
                    <td>
                        <a href="{{ route('social.account.comments', $value->post_id) }}">View Comments</a>
                    </td>
                </tr>
            @empty
            <tr>
                <td colspan="6" align="center">No Posts found</td>
            </tr>
            @endforelse
            </tbody>
        </table>
        @if(isset($posts))
            {{ $posts->links() }}
        @endif
    </div>
@endsection