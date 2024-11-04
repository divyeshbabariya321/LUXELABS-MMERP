@extends('layouts.app')

@section('title', $title)

@section('styles')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
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
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>

    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">{{ $title }} (<span id="filter_table_count">{{ $logs->total() }}</span>)</h2>
        </div>
    </div>

    {{-- filter_table_count --}}
    



    @include('partials.flash_messages')


    <div class="row m-2">
        <div class="col-lg-12">
            <div class="cls_filter_box mt-5 mb-2">
                <form class="form-inline" action="{{ route('logging.flow.log') }}" method="GET">
                    <div class="row">
                    <div class="col">
                        <input name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}"
                            placeholder="search" id="term">
                    </div>

                    <div class="col">
                        <div class='input-group' id='log-created-date1'>
                            <input type='text' class="form-control " name="created_at" value="" placeholder="Date"
                                id="created-date" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <div class='input-group'>
                                <select class="form-control flow_name" name="flow_name[]" multiple>
                                    @foreach($flow_logs->unique() as $flow_log)
                                        @php
                                            $sel='';
                                            if(isset($_GET['flow_name']) && in_array($flow_log,$_GET['flow_name']))
                                                $sel="selected='selected'";
                                        @endphp
                                        <option {{ $sel}} value="{{$flow_log}}">{{$flow_log}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <div class='input-group'>
                                <select class="form-control website" name="website[]" multiple>
                                    @foreach($websites as $website)
                                        @php
                                            $sel='';
                                            if(isset($_GET['website']) && in_array($website,$_GET['website']))
                                                $sel="selected='selected'";
                                        @endphp
                                        <option {{ $sel}} value="{{$website}}">{{$website}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <div class='input-group'>
                                <select class="form-control leads" name="leads[]" multiple>
                                    @foreach($leads as $lead)
                                        @php
                                            $sel='';
                                            if(isset($_GET['leads']) && in_array($lead,$_GET['leads']))
                                                $sel="selected='selected'";
                                        @endphp
                                        <option {{ $sel}} value="{{$lead}}">{{$lead}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <button type="submit" class="btn btn-image"><img
                                src="{{asset('/images/filter.png')}}" /></button>

                        <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img
                                src="{{asset('/images/resend2.png')}}" /></button>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <div class="mt-3 col-md-12">
            <table class="table table-bordered table-striped" id="filter_table">
                <thead>
                    <tr>
                        
                        <th style="width:7%">ID</th>
                        <th width="10%">Flow Name</th>
                        <th width="10%">Model</th>
                        <th width="10%">Leads</th>
                        <th width="10%">Website</th>
                        <th width="10%">Description</th>
                        <th width="25%">Message</th>
                        <th width="10%">LogCreated</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>

                <tbody id="content_data">
                    @include('logging.partials.flowlogdata')
                </tbody>

                

            </table>
        </div>
        <div id="ErrorLogModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Flow Log Detail</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-hover" style="table-layout:fixed;">
                            <thead>
                                <th style="width:10%">Flow Action</th>
                                <th style="width:20%">Modal Type </th>
                                <th style="width:20%">Leads</th>
                                <th style="width:25%">Message</th>
                                <th style="width:15%">Website</th>
                                <th style="width:10%">Date</th>
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
    </div>
    <img class="infinite-scroll-products-loader center-block" src="{{ asset('/images/loading.gif') }}" alt="Loading..."
        style="display: none" />

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script type="text/javascript">
        //Ajax Request For Search
        $(document).ready(function() {
            $('.flow_name').select2({
                placeholder:'Select Flow Name',
            });
            $('.website').select2({
                placeholder:'Select Website',
            });
            $('.leads').select2({
                placeholder:'Select Leads',
            });
            $(document).on("click", ".show_error_logs", function() {
                var id = $(this).data('id');
                $.ajax({
                        method: "GET",
                        url: "{{ route('logging.flow.detail') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id,
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
            $('#created-date').datetimepicker({
                format: 'YYYY/MM/DD'
            });

            var isLoading = false;
            var page = 1;

            $(window).scroll(function() {
                if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                    loadMore();
                }
            });

            function loadMore() {
                if (isLoading)
                    return;
                isLoading = true;
                var $loader = $('.infinite-scroll-products-loader');

                page = page + 1;

                var src = "{{ route('logging.flow.log') }}?ajax=1&page=" + page;
                var term = $('#term').val()
                var created_at = $('#created-date').val()
                var flow_name = $('.flow_name').val()
                var website = $('.website').val()
                var leads = $('.leads').val()
                
                $.ajax({
                    url: src,
                    type: 'GET',
                    data: {
                        term: term,
                        created_at: created_at,
                        flow_name: flow_name,
                        website: website,
                        leads: leads,

                    },
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function(data) {
                        console.log(data);
                        $loader.hide();
                        $("#filter_table tbody").append(data.tbody);

                        isLoading = false;
                        if (data.tbody == "") {
                            isLoading = true;
                        }

                    },
                    error: function() {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }
        });


        // Filter the data
        function submitSearch() {
            var src = "{{ route('logging.flow.log') }}"
            var term = $('#term').val()
            var created_at = $('#created-date').val()
            var flow_name = $('.flow_name').val()
            var website = $('.website').val()
            var leads = $('.leads').val()


            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term: term,
                    created_at: created_at,
                    flow_name: flow_name,
                    website: website,
                    leads: leads,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },

            }).done(function(data) {
                $("#loading-image").hide();
                $("#filter_table tbody").empty().html(data.tbody);
                // page = (data.tbody.page !== undefined) ? data.tbody.page : 1
                $("#filter_table_count").text(data.count);
                page = 1;

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });

        }

        // Restart Form
        function resetSearch() {
            src = "{{ route('logging.flow.log') }}"
            blank = ''
            $.ajax({
                url: src,
                dataType: "json",
                data: {

                    blank: blank,

                },
                beforeSend: function() {
                    $("#loading-image").show();
                },

            }).done(function(data) {
                $("#loading-image").hide();
                $('#term').val('');
                $('#created-date').val('');
                $("#filter_table tbody").empty().html(data.tbody);
                $("#filter_table_count").text(data.count);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        }
    </script>
@endsection
