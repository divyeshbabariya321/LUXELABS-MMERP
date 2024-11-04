@extends('layouts.app')

@section('title', 'Vendor Info')

@section('large_content')
<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>

<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">
            Master Flow Charts
            <div style="float: right;">
            </div>
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12 p-5">
            <div class="table-responsive mt-3">
                <table class="table table-bordered" id="vendor-table" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('vendors.flowchart-master.table-data')
                    </tbody>
                </table>
            </div>

            <div>
                {{ $flowchart_master->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection