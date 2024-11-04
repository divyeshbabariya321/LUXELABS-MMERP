@extends('layouts.app')

@section('title', 'Google Vision Data')

@section("styles")
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <style>
        /* Custom CSS for text wrapping */
        table.dataTable td {
            white-space: normal;
            word-wrap: break-word;
        }
        select.dt-input {
            width: 50px !important; /* Adjust the width as needed */
        }
    </style>
@endsection

@section('large_content')
    <div class="row ">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">Google Vision Data (<span id="total-records-count"></span>)</h2>
        </div>
    </div>

    <div class="row no-gutters">
        <div class="col-md-12 pl-4 pr-4">
            <div class="">
                <table class="table table-bordered table-striped sort-priority-scrapper" id="google-vision-table" style="table-layout:fixed;">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">Product ID</th>
                            <th width="10%">AI Name</th>
                            <th width="25%">Media Input</th>
                            <th width="25%">Result Scraper</th>
                            <th width="25%">Result AI</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var table = new DataTable('#google-vision-table', {
			processing: true,
			serverSide: true,
			ajax: '{{ route("google.vision.data") }}',
			columns: [
                { data: 'id', name: 'id', width: '5%' },
                { data: 'product_id', name: 'product_id', width: '10%' },
                { data: 'ai_name', name: 'ai_name', width: '10%' },
                { data: 'media_input', name: 'media_input', width: '25%' },
                { data: 'result_scraper', name: 'result_scraper', width: '25%' },
                { data: 'result_ai', name: 'result_ai', width: '25%' },
            ]
		});

        // Wait for table to be fully initialized before retrieving info
        table.on('init.dt', function() {
            // Use the DataTables API to get the total records count
            var info = table.page.info();
            var totalRecords = info.recordsTotal;

            // display the total records count in the UI
            $('#total-records-count').text(totalRecords);
        });
        $('.minmaxCon').remove();
    });
</script>
@endsection