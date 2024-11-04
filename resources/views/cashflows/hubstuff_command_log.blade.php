@extends('layouts.app')

@section('title', 'Hubstuff Command Log List')

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
            <h2 class="page-heading">Hubstuff Command Logs</h2>
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
                <th style="width:7%">ID</th>
                <th width="10%">Date</th>
                <th width="10%">Day</th>
                <th width="10%">Weekly</th>
                <th width="10%">Biweekly</th>
                <th width="10%">Fornightly</th>
                <th width="10%">Monthly</th>
                <th width="10%">Total User</th>
                <th width="25%">Message</th>
                <th width="10%">Action</th>
            </tr>
          
            </thead>

            <tbody id="content_data">
             @include('cashflows.hubstuff_command_log_data')
            </tbody>

            {!! $logs->render() !!}

        </table>
    </div>
    <div id="ErrorLogModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Hubstuff Command Log Detail</h4>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover" style="table-layout:fixed;">
                <thead>
                    
                  <th style="width:10%">User</th>
                  <th style="width:20%">Frequency</th>
                  <th style="width:20%">Start Date</th>
                  <th style="width:25%">End Date</th>
                  <th style="width:15%">Message</th>
                 
                </thead>
                <tbody class="error-log-data">
    
                </tbody>
              </table>
    
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
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
            url: "{{ route('cashflow.hubstuff.detail') }}" ,
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
        
        //Search    
        src = "{{ route('cashflow.hubstuff.log') }}";
        $(".search").autocomplete({
            source: function(request, response) {
                $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                          
                        
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
    src = "{{ route('cashflow.hubstuff.log') }}";
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

    </script>
@endsection