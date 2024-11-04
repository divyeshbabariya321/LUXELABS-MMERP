@extends('layouts.app')

@section('title', 'Python Site Log List')

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
    <div id="manage-log-instance" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Instagram Logs</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Python Site Logs</h2>
            
           

        </div>
     
    </div>

    <div class="pull-left1">
        <div class="row">
            <form name="get-logs" id="get-logs" style="width: 100%;">

                    <div class="col-md-2">
                        <select class="form-control" tabindex="-1" aria-hidden="true" name="website" id="store_website">
                            <option value="">Select Website</option>
                            @foreach($storeWebsites as $websiteRow)
                                @if(isset($request->website) && $websiteRow->id==$request->website)
                                    <option value="{{$websiteRow}}" selected="selected">{{$websiteRow}}</option>
                                @else
                                <option value="{{$websiteRow}}">{{$websiteRow}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        {{ html()->select('device', ["desktop" => "Desktop", "mobile" => "Mobile", "tablet" => "Tablet"], request('device'))->class("form-control")->id("store_devide") }}
                    </div>
                    <div class="col-md-2">
                    <input type="date" name="date" id="date11" class="form-control"style="width:200px !important">
                    </div>
                    
                    <div class="col-md-1">
                    <button type="submit" class="btn btn-info btn-log-instances">
                        GetLogs
                    </button>
                    </div>
            </form>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th style="width:7%">ID</th>
                <th width="10%">Website</th>
                <th width="25%">Device</th>
                <th width="25%">Log Date</th>
                <th width="10%">LogCreated</th>
                <th width="10%">Log</th>
                
            </tr>
            <tr>
                <th style="width:7%"></th>
                <th width="10%"><input type="text" name="website" class="search form-control" id="website"></th>
                <th></th>
                <th></th>
                <th> <div class='input-group' id='log-created-date1'>
                        <input type='text' class="form-control " name="created_at" value="" placeholder="Date" id="created-date" />
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                </th>
                <th></th>
                
            </tr>
            </thead>

            <tbody id="content_data">
             @include('scrap.partials.python_logdata')
            </tbody>

            {!! $logs->render() !!}

        </table>
    </div>
  

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <script type="text/javascript">



    //Ajax Request For Search
    $(document).ready(function () {
          
        $(document).on("click", ".show_error_logs", function() {
            var id = $(this).data('id');
            $.ajax({
            method: "GET",
            url: "{{ route('logging.flow.detail') }}" ,
            data: {
                "_token": "{{ csrf_token() }}",
                "id" : id,
            },
            dataType: 'html'
            })
            .done(function(result) {
            $('#ErrorLogModal').modal('show');
            $('.error-log-data').html(result);
            });

        });

        //Filter by date
        count = 0;
        $('#created-date').datetimepicker({ format: 'YYYY/MM/DD' }).on('dp.change', function (e) {
          //  alert("dddd");
            //if(count > 0){    
             var formatedValue = e.date.format(e.date._f);
                created_at = $('#created_date').val();
                website = $('#website').val();
               // message = $('#message').val();
                src = "{{ route('get.python.log') }}";
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        created_at : created_at,
                        website : website,
                   
                      
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },

                }).done(function (data) {
                    $("#loading-image").hide();
                $("#content_data").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                    

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });  

           // } 
            //count++;       
        });


        //Search    
        src = "{{ route('get.python.log') }}";
        $(".search").autocomplete({
            source: function(request, response) {
             
                website = $('#website').val();
                created_at = $('#created_date').val();
                
                $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                            created_at : created_at,
                            website : website,
                            
                        },
                        beforeSend: function() {
                            $("#loading-image").show();
                        },
                    
                    }).done(function (data) {
                        $("#loading-image").hide();
                        console.log(data);
                        $("#log-table tbody").empty().html(data.tbody);
                        if (data.links.length > 10) {
                            $('ul.pagination').replaceWith(data.links);
                        } else {
                            $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                        }
                        
                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
            },
            minLength: 1,
       
        });
    });
    src = "{{ route('logging.flow.log') }}";
    function refreshPage() {
        blank = ''
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                blank : blank
            },
            beforeSend: function() {
                    $("#loading-image").show();
            },
        
        }).done(function (data) {
                $("#loading-image").hide();
            console.log(data);
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
    $(document).on("click",".btn-log-instances",function(e) {
            e.preventDefault();
            var $store_website=  $('#store_website').val();
            var $store_devide=  $('#store_devide').val();
            var $date=  $('#date11').val();

            $.ajax({
                url: '{{url("scrap/python/get-log")}}',
                method:"get",
                data : {
                    website:$store_website,
                    date:$date,
                    device:$store_devide
                },
                success: function (data) {
                    if(data.type=="success"){
                          $("#manage-log-instance").find(".modal-body").html(data.response);
                          $("#manage-log-instance").modal('show'); 
                    }else{
                        alert(data.response)                     }
                },
            });
        });

    </script>
@endsection