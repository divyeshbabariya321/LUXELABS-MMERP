@extends('layouts.app')

@section('styles')

<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">

<style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
        .table-responsive {
            overflow-x: auto !important;
        }
    </style>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />  
@endsection
@section('content')
<div class="col-md-12">
    <div id="myDiv">
        <img id="loading-image" src="{{asset('/images/pre-loader.gif')}}" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h2 class="page-heading">
                    <a class="text-dark" data-toggle="collapse" href="javascript:void(0)">Inventory Update Logs</a>
                </h2>
            </div>
            <div class="pull-left">
                <form method="POST" action="{{ route('inventory_update_log.grid.reference') }}" class="form-inline align-items-start">
                    <div class="form-group mr-3">
                        <input type='text' class="form-control" name="filterbydate" id="filterbydate" placeholder="Filter by Date" />
                    </div>
                    <button id="refresh_page" type="button" class="btn btn-image"><img src="{{asset('/images/resend2.png')}}" /></button>
                </form>
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table-striped table-bordered table" id="inventory-update-log-reference-grid">
                    <thead>
                        <tr style="width: auto;">
                            <th>Id</th>
                            <th>Log Type</th>
                            <th>Created Date</th>
                            <th>Content</th>
                        </tr>
                     </thead>
                    <tbody id="content_data">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
    
$(function () {
        $('#filterbydate').datepicker({
            format: 'yyyy-mm-dd'
        });
        getGridData();
        
        function getGridData(action_type = '') {
            if(action_type != '') {
                var params = {
                        filterbydate: $('#filterbydate').val()
                    };
            } else {
                var params = {};
            }
            
            var table = $('#inventory-update-log-reference-grid').DataTable({
                destroy:true,
                processing: true,
                serverSide: true,
                ajax: { 
                    url:"{{ route('inventory_update_log.grid.reference') }}",
                    data:params
                },
                columnDefs: [
                    { width: '10%', targets: 0 },
                    { width: '10%', targets: 1 },
                    { width: '10%', targets: 2 },
                    { width: '60%', targets: 3 },
                ],
                fixedColumns: true,
                columns: [
                    {data: 'id', name: 'id', 'visible' : true, width: '10%'},
                    {data: 'logtype', name: 'logtype', 'visible' : true, width: '10%'},
                    {data: 'created_at', name: 'created_at', 'visible' : true, width: '10%'},
                    {data: 'datacontent', name: 'datacontent', 'visible' : true, width: '60%'},
                ],
                drawCallback: function() {
                    var api = this.api();
                    var recordsTotal = api.page.info().recordsTotal;
                    var records_displayed = api.page.info().recordsDisplay;
                    $("#total").text(recordsTotal);
                    // now do something with those variables
                },
            });
        }
        $('#refresh_page').on('click', function () {
            getGridData('refresh');
        });
});
</script>

@endsection