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
                <h2 class="page-heading">Crop Rejected Final Approval Images (<span id="lbl_total_record_count">0</span>)</h2>
            </div>
        
            <hr>
            <div class="table-responsive">
                <table class="table-striped table-bordered table" id="crop-rejected-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Website</th>
                            <th>Rejected By</th>
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
<script type="text/javascript">
  $(function () {
    $(".select-multiple2").select2({
        width: '100%'
    });
    getGridData();
    
    function getGridData() {
        var status_id =  0;
        var table = $('#crop-rejected-table').DataTable({
            destroy:true,
            processing: true,
            serverSide: true,
            ajax: { 
                url:"{{ route('crop-rejected-final-approval-images') }}",
                data:{status_id:status_id}
            },
            columns: [
                {data: 'product_name', name: 'product.name'},
                {data: 'product_sku', name: 'product.sku'},
                {data: 'store_website_title', name: 'store_website.title'},
                {data: 'rejected_by', name: 'user.name'},
            ],
            drawCallback: function() {
                var api = this.api();
                var recordsTotal = api.page.info().recordsTotal;
                var records_displayed = api.page.info().recordsDisplay;
                $("#lbl_total_record_count").text(recordsTotal);
                // now do something with those variables
            },
        });
    }
    
  });
  
</script>
@endsection