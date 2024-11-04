@extends('layouts.app')
@section('styles')
<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">

<style type="text/css">
    .table-responsive {
        overflow-x: auto !important;
        padding: 0 20px 0;
    }
    
    input[type=checkbox] {
        height: 12px;
    }
    
    .success-job {
        color:green;
    }
    
    .failed-job {
        color:red;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        height:22px;
        padding-left:5px !important;
    }
</style>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h2 class="page-heading">Email Logs (<span id="lbl_total_record_count">0</span>)</h2>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-2">
                        <input type="text" name="search" id="global_search" class="form-control" placeholder="Global Search" />
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" id="from_date" class="form-control" placeholder="From Date" />
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" id="to_date" class="form-control" placeholder="To Date" />
                    </div>
                    <div class="col-md-2">
                        <select class="form-control selectpicker" id="module" name="module" data-live-search="true">
                            <option value="0">Select Module</option>
                            @if ($modules && !empty($modules))
                                @foreach($modules as $module)
                                    <option value="{{ $module }}">{{ $module }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" name="filter" id="filter" class="btn custom-button"><i class="fa fa-search"></i></button>
                        <button type="button" name="refresh" id="refresh" class="btn custom-button" style="margin-left:10px"><i class="fa fa-refresh"></i></button>
                    </div>
                    <div class="col-md-3 text-right">
                        <!-- Add a button to select all checkboxes -->
                        <button id="delete-selected" class="btn custom-button">Bulk Delete</button>
                        <button id="delete-all" class="btn custom-button">Empty Logs</button>
                    </div>
                </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table-striped table-bordered table" id="crop-rejected-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Log Message</th>
                            <th>Module Type</th>
                            <th>Sender</th>
                            <th>Receiver</th>
                            <th>Action</th>
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
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="{{asset('js/email-log.js')}}" defer></script>
    
@endsection
