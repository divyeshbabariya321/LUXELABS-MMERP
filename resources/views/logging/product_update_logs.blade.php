@extends('layouts.app')

@section('title', 'Scraper Log List')

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
        .pagination {
     margin: 0px 0;
    width: 100%;
}
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div id="myDiv">
        <img id="loading-image" src="{{asset('images/pre-loader.gif')}}" style="display:none;z-index:9999;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Product Update Logs ( {{ $productLogs->total() }})</h2>
             <div class="pull-right">
                <button type="button" class="btn btn-image" onclick="location.reload()"><img src="{{ asset('images/resend2.png') }}" /></button>
            </div>
        </div>
    </div>
    
        <div class="row">
            <div class="col-md-12">
                    @include('partials.flash_messages')
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="log-table">
                            <thead>
                            <tr>
                                <th width="5%">Id</th>
                                <th width="5%">Log</th>
                                <th width="5%">Product Updated By</th>
                                <th width="10%">Created at</th>
                            </tr>
                            </thead>
                
                            <tbody id="content_data" class="infinite-scroll">
                                @include('logging.partials.product_update_logs')
                            </tbody>
                        </table>
						{{$productLogs->links()}}
                    </div>
					
            </div>
        </div>
    
</div>



@endsection

