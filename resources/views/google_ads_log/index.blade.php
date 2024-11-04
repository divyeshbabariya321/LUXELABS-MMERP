@extends('layouts.app')

@section('title', 'Google Ads Log List')

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
    <div class="mt-3 col-md-12">
    <div id="myDiv">
        <img id="loading-image" src="{{asset('/images/pre-loader.gif')}}" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Ads Logs</h2>

            <div class="pull-left">
                <form action="{{route('googleadslogs.index')}}" method="get">

                    <div class="col-md-3 pl-0 pr-1">
                        <select class="browser-default custom-select" id="type" name="type" style="height: auto">
                            <option value="">Type</option>
                            <option value="SUCCESS" {{(request()->get('type')=='SUCCESS')?'selected':''}}>SUCCESS</option>
                            <option value="ERROR" {{(request()->get('type')=='ERROR')?'selected':''}}>ERROR</option>
                        </select>
                    </div>

                    <div class="col-md-3 pr-2">
                        <input name="user_name" type="text" class="form-control" value="{{isset($_GET['user_name'])?$_GET['user_name']:''}}" placeholder="User Name" id="user_name">
                    </div>

                    <div class="col-md-2 pr-1">
                        <input name="module" type="text" class="form-control" value="{{isset($_GET['module'])?$_GET['module']:''}}" placeholder="Module Name" id="module">
                    </div>

                    <div class="col-md-2 pr-1">
                        <input name="message" type="text" class="form-control" value="{{isset($_GET['message'])?$_GET['message']:''}}" placeholder="Message" id="message">
                    </div>

                    <div class="col-md-2 pr-1">
                        <button type="submit" class="btn btn-image"><img src="{{asset('/images/filter.png')}}" /></button>
                        <a href="{{route('googleadslogs.index')}}" type="button" class="btn btn-image refresh-table" title="Refresh"><img src="{{asset('/images/resend2.png')}}" /></a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')


        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th width="10%">User Name</th>
                <th width="10%">Type</th>
                <th width="10%">Module Name</th>
                <th width="10%">Message</th>
                <th width="10%">Created At</th>
            </tr>
            <tr>
                
                <th width="10%"><input type="text" class="search form-control" id="user_name"></th>
                <th width="10%"><input type="text" class="search form-control" id="type"></th>
                <th width="10%"><input type="text" class="search form-control" id="module"></th>
                <th width="10%"><input type="text" class="search form-control" id="message"></th>
                <th> 
                    <div class='input-group' id='created_at_div'>
                        <input type='text' class="form-control " name="phone_date" value="" id="created_at" />
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </th>
            </tr>
            </thead>

            <tbody id="content_data">
                @include('google_ads_log.partials.list')
            </tbody>

            {!! $logs->render() !!}

        </table>
    </div>
 

@endsection

@section('scripts')
    <style type="text/css">
        /*.bootstrap-datetimepicker-widget.dropdown-menu {
            display: contents;
        }*/
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <script type="text/javascript">


    //Ajax Request For Search
    $(document).ready(function () {
        //Expand Row
        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        $(document).on('click', '.refresh-table', function () {
            $('#log-table').find('input').val("");
            renderTable();
        });

        logcount = 0;
        $('#created_at_div').datetimepicker({ 
            format: 'YYYY/MM/DD' 
        }).on('dp.change', function (e){
            if(logcount > 0){    
                renderTable()
            } 
            logcount++;       
        });


        //Search
        $(".search").autocomplete({
            source: function(request, response) {
               renderTable();
            },
            minLength: 1,
        });

        function renderTable(){
            $.ajax({
                url: "{{ route('googleadslogs.index') }}",
                dataType: "json",
                data: {
                    user_name : $('#log-table').find('#user_name').val(),
                    type : $('#log-table').find('#type').val(),
                    module : $('#log-table').find('#module').val(),
                    message : $('#log-table').find('#message').val(),
                    action : $('#log-table').find('#action').val(),
                    created_at : $('#log-table').find('#created_at').val(),
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
            
            }).done(function (data) {
                $("#loading-image").hide();
                $("#log-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        }
    });

    </script>
@endsection