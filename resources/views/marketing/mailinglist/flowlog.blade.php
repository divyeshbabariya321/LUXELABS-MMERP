@extends('layouts.app')

@section('title', ' Mailinglist Flow Logs List')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
            <h2 class="page-heading">Mailinglist Flow Logs</h2>
             <div class="pull-right">
                
                <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
            </div>

        </div>
    </div>

    @include('partials.flash_messages')

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th width="7%">ID</th>
                <th width="80%">Message</th>
                <th width="10%">Date</th>
            </tr>
            <!-- <tr>
                <th style="width:7%"></th>
                <th width="10%"><input type="text" name="flow_name" class="search form-control" id="flow_name"></th>
                <th width="10%"><input type="text"  name="message" class="search form-control" id="message"></th>
                <th> <div class='input-group' id='log-created-date1'>
                        <input type='text' class="form-control " name="created_at" value="" placeholder="Date" id="created-date" />
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                </th>
                <th></th>
            </tr> -->
            </thead>

            <tbody id="content_data">
                @foreach ($logs as $log)
                 <tr>
                 <td>{{ $log->id }}</td>
                    <td>{{ $log->message }}</td>
                    <td>{{ $log->created_at }}</td>
                    </tr>   
                 @endforeach

            </tbody>
                            
       
    

            {{ $logs->appends(request()->except('page'))->links() }}

        </table>
    </div>
  
  
@endsection
