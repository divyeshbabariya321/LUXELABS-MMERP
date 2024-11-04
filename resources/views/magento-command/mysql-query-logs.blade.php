@extends('layouts.app')

@section('title', 'Magento Command')

@section('styles')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<style>
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
        <h2 class="page-heading">MySql Query Logs</h2>
    </div>

    <div class="col-12 mb-3">
        <div class="pull-left">
        </div>
        <div class="pull-right">
            <!-- <a title="add new domain" class="btn btn-secondary add-new-btn">+</a> -->
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
                        <th style="width: 10%;">Websites</th>
                        <th style="width: 20%;">Command</th>
                        <th style="width: 10%;">Job Id</th>
                        <th style="width: 30%;">Response</th>
                        <th style="width: 5%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mysqlCommandRunLog as $key => $logs)
                    <tr>
                        <td>{{$logs->id}}</td>
                        <td>{{$logs->user?->name}}</td>
                        <td>
                            @if($logs->website_ids!='ERP')
                            {{$logs->website->website}}
                            @else
                            {{$logs->website_ids}}
                            @endif  
                        </td>
                        
                        <td>{{$logs->command}}</td>
                        <td>{{$logs->job_id}}</td>
                        <td>{{$logs->response}}</td>
                        <td>{{$logs->status}}</td>
                        

                            
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-center">
                {!! $mysqlCommandRunLog->appends(Request::except('page'))->links() !!}
            </div>
        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
    </div>
</div>
@endsection

<link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.min.css')}}">
{{-- <link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.css')}}"> --}}
@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>

<script type="text/javascript" src="{{ mix('webpack-dist/js/mock.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.dropdown.min.js') }} "></script>
{{-- <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.dropdown.js') }} "></script> --}}


<script>
    var Random = Mock.Random;
    var json1 = Mock.mock({
        "data|10-50": [{
            name: function() {
                //return Random.name(true)
            }
            , "id|+1": 1
            , "disabled|1-2": true
            , groupName: 'Group Name'
            , "groupId|1-4": 1
            , "selected": true
        }]
    });
   

</script>

@endsection
