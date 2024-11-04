@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
@php
$auth = auth()->user();
@endphp
<h2 class="page-heading">
    {{ $moduleName }} ({{$total_seo}})
    <div style="float: right;">
        @if($auth->hasRole(['Admin', 'User', 'Seo Head']))
            <a href="javascript:;" class="btn btn-secondary statusListBtn">Status</a>
            <a href="javascript:;" class="btn btn-secondary addNewBtn">Add new</a>
        @endif
        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#seodatatablecolumnvisibilityList">Column Visiblity</button>
        <button class="btn btn-secondary" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
    </div>
</h2>

<div class="container-fluid">
    <div class="d-flex justify-content-end">
        
    </div>
    <div class="mt-3">
        <div class="row">
            <div class="col-md-2">
                <label for="">Select website</label>
                <select name="website_id[]" class="form-control websiteFilter select2-ele" multiple="multiple">
                    <option value="">-- SELECT --</option>
                    @foreach ($websites as $item)
                        <option value="{{ $item->id }}">{{ $item->website }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="">Select price status</label>
                <select name="website_id" class="form-control priceStatusFilter">
                    <option value="">-- SELECT --</option>
                    <option value="1">Approved</option>
                    <option value="2">Unapproved</option>
                </select>
            </div>
            @if($auth->hasRole(['Admin', 'Seo Head']))
                <div class="col-md-2">
                    <label for="">Select user</label>
                    <select name="website_id[]" class="form-control userFilter select2-ele" multiple="multiple">
                        <option value="">-- SELECT --</option>
                        @foreach ($users as $item)
                            @if(!$item->hasRole(['user']))
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-2">
                <label for="">Status</label>
                <select name="website_id" class="form-control statusFilter">
                    <option value="">-- SELECT --</option>
                    <option value="planned">Planned</option>
                    <option value="admin_approve">Admin Approved</option>
                </select>
            </div>


            <div class="col-md-2 mt-5">
                <label for="">&nbsp;</label>
                <button class="btn btn-image search ui-autocomplete-input searchBtn">
                    <img src="{{ url('images/search.png')}}" alt="Search" style="cursor: default;">
                </button>
            </div>
        </div>
        <div class="card-body" style="padding: 1.25rem 0px;">
            <div class="table-responsive-lg" style="overflow-x:auto;">
                <table class="table table-bordered" id="seoProcessTbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Website</th>
                            <th>Keywords</th>
                            <th>User</th>
                            <th>Price</th>
                            <th>Document Link</th>
                            <th>Word count</th>
                            <th>Suggestion</th>
                            <th>Status</th>
                            <th>SEO Checklist</th>
                            <th>Publish Checklist</th>
                            <th>Live Status Link</th>
                            <th>Publish Date</th>
                            <th>Actions</th>
                            <th>Status Color</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Add Form Modal -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary saveFormBtn">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@include('seo.content.modal')
@include("seo.content.column-visibility-modal")
@include("seo.content.modal-status-color")
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $(".select2-ele").select2();
});
</script>
@include('seo.content.script')
@endsection
