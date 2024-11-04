@extends('layouts.app')
@section('title', 'User Management > User Delivered')
@section('favicon', 'user-management.png')
@section('large_content')
@include('partials.flash_messages')
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;background-color:rgba(255,255,255,0.6);"></div>
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">{{ $title }} <span id="{{$table}}Count" class="count-text"></span></h2>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card d-normal">
                <div class="card-header">
                    <h4>
                        <a class="collapsed card-link" data-toggle="collapse" href="#collapseSearch" aria-expanded="false">
                            <i class="fa fa-arrow-up"></i>
                            <i class="fa fa-arrow-down"></i>
                            Filter Records
                        </a>
                    </h4>
                </div>
                <div id="collapseSearch" class="collapse">
                    <div class="card-body">
                        <form id="frm-search-crud" class="" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">Users List</label>
                                                <select class="form-control select2" name="srchUser">
                                                    <option value=""></option>
                                                    {!! makeDropdown($listUsers) !!}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">From Date</label>
                                                <div class="input-group date d-datetime">
                                                    <input type="text" class="form-control input-sm" name="srchDateFrom" value="{{ request('srchDateFrom', date('Y-m-d')) }}" />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">To Date</label>
                                                <div class="input-group date d-datetime">
                                                    <input type="text" class="form-control input-sm" name="srchDateTo" value="{{ request('srchDateTo', date('Y-m-d', strtotime('+3 days'))) }}" />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" onclick="siteDatatableSearch('#{{$table}}')">Search</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="siteDatatableClearSearch('#{{$table}}', '#frm-search-crud')">Clear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 mb-5">
            <table id="{{$table}}" class="table table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th data-data="name" data-name="name" width="13%" data-sortable="false">User Name</th>
                        <th data-data="date" data-name="date" width="9%" data-sortable="false">Date</th>
                        <th data-data="availability" data-name="availability" width="9%" data-sortable="false">Availability</th>
                        <th data-data="lunch" data-name="lunch" width="9%" data-sortable="false">Lunch</th>
                        <th data-data="planned" data-name="planned" width="30%" data-sortable="false">Work Planned</th>
                        <th data-data="actual" data-name="actual" width="30%" data-sortable="false">Actual Logins [Total Tracked / With Tasks / Without Tasks]</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{config('app.url')}}/js/common-helper.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
@endsection

@push('styles')
<style>
    .div-slot {
        display: inline-block;
        padding: 4px;
        border: 1px solid #ddd;
        border-radius: 10px;
        margin: 2px;
    }
</style>
@endpush

@push("link-css")
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
@endpush

@push("jquery")
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
@endpush

@push("scripts")
<script>
    var dtblList = null;
    jQuery(document).ready(function() {

        applySelect2(jQuery('.select2'));
        applyDatePicker(jQuery('.d-datetime'));

        // Render datatable
        dtblList = jQuery('#{{$table}}').DataTable({
            lengthChange: false,
            searching: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            paging: false,
            bInfo: false,
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ $urlLoadData }}",
                data: function(d) {
                    return siteDatatableMergeSearch(d, '#frm-search-crud');
                },
                dataSrc: function(json) {
                    return json.data;
                },
                error: function(xhr, error, code) {
                    siteErrorAlert(xhr);
                    jQuery('#{{$table}}_processing').hide();
                }
            },
            initComplete: function(settings, json) {
                jQuery('#{{$table}}Count').html('(' + dtblList.data().count() + ')');
            },
            drawCallback: function(settings) {
                jQuery('#{{$table}}Count').html('(' + dtblList.data().count() + ')');
            },
            order: [],
        });
    });
</script>
@endpush