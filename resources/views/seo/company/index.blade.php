@extends('layouts.app')

@section('content')
<style>
    .select2.select2-container {
        width: 100% !important
    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
@php
$auth = auth()->user();
@endphp
<h2 class="page-heading">
    {{ $moduleName }} ({{$totalSeoCompanies}})
    <div style="float:right;">
        @if($auth->hasRole(['Admin']))
            <a href="javascript:;"  class="btn btn-secondary mr-2 typeModuleBtn">Types</a>
        @endif
        <a href="javascript:;" data-url="{{ route('seo.company.create') }}" class="btn btn-secondary addNewBtn">Add new</a>
        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#scdatatablecolumnvisibilityList">Column Visiblity</button>
        <button class="btn btn-secondary" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
    </div>
</h2>

<div class="container-fluid">
    <div class="mt-3">
        <div class=" mt-4">
            <div class="row mb-3">
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control typeSelect">
                        <option value=""> -- SELECT --</option>
                        @foreach ($companyTypes as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Website</label>
                    <select name="type" class="form-control websiteFilter">
                        <option value=""> -- SELECT --</option>
                        @foreach ($websites as $item)
                            <option value="{{ $item->id }}">{{ $item->website }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">User</label>
                    <select name="type" class="form-control userFilter">
                        <option value=""> -- SELECT --</option>
                        @foreach ($users as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                     @php
                        $statusArr = [
                            'pending',
                            'approved',
                            'rejected',
                        ];
                    @endphp
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control statusFilter">
                        <option value="">-- SELECT --</option>
                        @foreach ($statusArr as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mt-5">
                    <label for="">&nbsp;</label>
                    <button class="btn btn-image search ui-autocomplete-input searchBtn">
                        <img src="{{ url('images/search.png')}}" alt="Search" style="cursor: default;">
                    </button>
                </div>
            </div>
            <table class="table table-bordered " id="seoTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Website</th>
                        <th>DA</th>
                        <th>PA</th>
                        <th>SS</th>
                        <th>User</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Live link</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Status Color</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!--  Form Modal -->
<div class="modal fade" id="companyFormModal"  role="dialog" aria-hidden="true" data-rowid="" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SEO Company History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-notification" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary saveFormBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Type Module Modal -->
<div class="modal fade" id="typeModal"  role="dialog" aria-hidden="true" data-rowid="" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Company Type <a style=" text-decoration: underline;  font-weight: bold;  color: blue;  padding: 0;" href="javascript:;" data-url="{{ route('seo.company-type.create')}}" class="btn addNewTypeBtn">Add</a></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table bordered" id="typeTable">
                    <thead>
                        <th>#</th>
                        <th>Type</th>
                        <th>Action</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Type Form Modal -->
<div class="modal fade" id="typeFormModal"  role="dialog" aria-hidden="true" data-rowid="" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Company Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-notification" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary saveBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-hidden="true" data-rowid="" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SEO Company History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered mt-3" id="historyTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Website</th>
                            <th>DA</th>
                            <th>PA</th>
                            <th>SS</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Live link</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@include("seo.company.column-visibility-modal")
@include("seo.company.modal-status-color")
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
@include('seo.company.script')
@endsection
