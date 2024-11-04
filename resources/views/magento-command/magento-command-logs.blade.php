@extends('layouts.app')

@section('title', 'Magento Command')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid black;
        padding: 10px;
        text-align: left;
        /* Ensure text wraps inside the cell */
        word-wrap: break-word; 
        word-break: break-all;
        white-space: normal;
    }
    .multiselect {
        width: 100%;
    }

    .multiselect-container li a {
        line-height: 3;
    }

    /* Pagination style */
    .pagination>li>a,
    .pagination>li>span {
        color: #343a40!important // use your own color here
    }

    .pagination>.active>a,
    .pagination>.active>a:focus,
    .pagination>.active>a:hover,
    .pagination>.active>span,
    .pagination>.active>span:focus,
    .pagination>.active>span:hover {
        background-color: #343a40 !important;
        border-color: #343a40 !important;
        color: white !important
    }

</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="page-heading">Magento Command Logs</h2>
    </div>

    <div class="col-12 mb-3">
        <div class="pull-left">
        </div>
        <div class="pull-right">
        </div>
    </div>
</div>
<div class="row m-0">
    <div class="col-12" style="border: 1px solid;border-color: #dddddd;">
        <div class="table-responsive mt-2">
            <table class="table table-bordered" style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th style="width: 2%;">ID</th>
                        <th style="width: 5%;">User</th>
                        <th style="width: 12%;">Websites</th>
                        <th style="width: 8%;">Command</th>
                        <th style="width: 10%;">Request</th>
                        <th style="width: 30%;">Response</th>
                        <th style="width: 5%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($magentoCommandRunLog as $key => $logs)
                    <tr>
                        <td>{{$logs->id}}</td>
                        <td>{{$logs->user?->name}}</td>
                        <td>{{$logs->website?->website}}</td>
                        <td>{{$logs->command?->command_type}}</td>
                        <td>{{$logs->request}}</td>
                        <td>{{!! $logs->formatted_response !!}}</td>
                        <td>
                            @if ($logs->status)
                                <button class="btn btn-success btn-sm">Success</button>
                            @else
                                <button class="btn btn-danger btn-sm">Failed</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-center">
                {!! $magentoCommandRunLog->appends(Request::except('page'))->links() !!}
            </div>
        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
    </div>
</div>
@endsection
<link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.min.css')}}">
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/mock.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.dropdown.min.js') }} "></script>
@endsection
