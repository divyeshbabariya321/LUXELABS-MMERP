@extends('layouts.app')

@section('title', 'Product Out Of Stock History')

@section('styles')
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Inventory Sold Out Products (<span id="lbl_soldout_product_count">0</span>)</h2>
        </div>
    </div>
    <div class="row py-4">
        <div class="col-md-12">
            <form method="get" id="filter">
                <div class="row">
                    <div class="col-md-3 mt-3">
                        <input type="text" name="id" class="form-control" id="id" placeholder="Product Id" />
                    </div>
                    <div class="col-md-3 mt-3">
                        <input type="text" name="name" class="form-control" id="name"
                            placeholder="Product Name" />
                    </div>
                    <div class="col-md-3 mt-3">
                        <input type="text" name="sku" class="form-control" id="sku" placeholder="Sku" />
                    </div>
                    <div class="col-md-3 mt-3">
                        <button type="button" class="btn btn-secondary filter">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="productLogs" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Product History Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="font-weight-bold">Product Name: <span id="productName"></span></h5>
                    <h5 class="font-weight-bold">Sku: <span id="productSku"></span></h5>
                    <table class="table-striped table-bordered table out-of-stock-product-log-details">
                        <thead>
                            <tr>
                                <th>In Stock</th>
                                <th>Previous Stock</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table-striped table-bordered table out-of-stock-products-table"
                    id="out-of-stock-products-table">
                    <thead>
                        <tr>
                            <th>Product Id</th>
                            <th>Name</th>
                            <th>Sku</th>
                            <th>In Stock</th>
                            <th>Previous In Stock</th>
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

    <script type="text/javascript">
        $(function() {
            getFilterData();

            function getFilterData() {
                var id = $("#id").val();
                var name = $("#name").val();
                var sku = $("#sku").val();
                var table = $('#out-of-stock-products-table').DataTable({
                    destroy:true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('productinventory.out-of-stock') }}",
                        data: {
                            id: id,
                            name: name,
                            sku: sku
                        }
                    },
                    columns: [{
                            data: 'product_id',
                            name: 'product_id'
                        },
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'sku',
                            name: 'sku'
                        },
                        {
                            data: 'in_stock',
                            name: 'in_stock'
                        },
                        {
                            data: 'prev_in_stock',
                            name: 'prev_in_stock'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    drawCallback: function() {
                        var api = this.api();
                        var recordsTotal = api.page.info().recordsTotal;
                        var records_displayed = api.page.info().recordsDisplay;
                        $("#lbl_soldout_product_count").text(recordsTotal);
                    },
                });
            }

            $(document).on('click', '.filter', function() {
                getFilterData();
            });

            $(document).on('click', '.get-product-log-detail', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                console.log(id);
                var product_id = $(this).data('product-id');
                $.ajax({
                    type: 'GET',
                    url: "{{ route('productinventory.out-of-stock-product-log') }}",
                    data: {
                        'id': id,
                        'product': product_id,
                    },
                    // dataType : "json",
                    success: function(res) {
                        if (res.success === true) {
                            $('#productLogs').modal('show');
                            let data = res.data;
                            $("#productName").text(res.productName);
                            $("#productSku").text(res.productSku);
                            $("#productLogs tbody").html(data);
                        } else {
                            toastr.error(res.msg);
                        }
                    }
                });
            })
        });
    </script>
@endsection
