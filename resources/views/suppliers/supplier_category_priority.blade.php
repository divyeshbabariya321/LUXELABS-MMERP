@extends('layouts.app')

@section('favicon', 'supplierlist.png')

@section('title', 'Supplier Priority List')

@section('styles')
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

@section('large_content')

    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Supplier Priority List (<span id="lbl_supplier_count">0</span>)</h2>
            <form method="get" id="filter">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="supplierName" name="supplier" placeholder="Supplier">
                    </div>
                    <div class="col-md-2">
                        <select name="priority" id="priority" class="form-control">
                            <option value="" disabled selected>Select Priority</option>
                            <option value="0">Not set</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->id }}">{{ $priority->priority }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-secondary filter">Filter</button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-secondary float-right"
                            id="btn_manage_supplier_priority">Manage
                            Supplier Priority</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="updateSupplierPriority" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Supplier Priority</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <input type="hidden" id="supplier_id" name="supplier_id">
                    <div class="modal-body mb-2">
                        <label class="font-weight-bold">Supplier: <span id="supplier"></span></label></br>
                        <label class="font-weight-bold">Category: <span id="category"></span></label></br>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="supplier_priority_list">Priority:</label>
                                <select class="form-control" id="supplier_priority_list" name="priority">
                                    <option value="" disabled selected hidden>Select Priority...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary update-supplier-priority"
                            id="btn_update_supplier_priority">Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table-striped table-bordered table supplier-priority-table" id="supplier-priority-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="manageSupplierPriorityModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Manage Supplier Priority</h4>
                </div>
                <div class="modal-body">
                    <form class="form-inline" id="frm_manage_supplier_priority">
                        <div class="form-group mx-sm-3 mb-2">
                            <label for="priority" class="sr-only">Enter Priority</label>
                            <input type="text" class="form-control" id="priority" name="priority"
                                placeholder="Enter Priority">
                        </div>
                        <button type="submit" class="btn btn-primary mb-2" id="btn_add_priority">Add</button>
                    </form>
                    <hr>

                    <table class="table table-bordered table-striped" id="priority_list_table">
                        <thead>
                            <tr>
                                <th>Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
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
                var supplier = $("#supplierName").val();
                var priority = $("#priority").val();
                var table = $('.supplier-priority-table').DataTable({
                    destroy: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('supplier-priority.list') }}",
                        data: {
                            supplier: supplier,
                            priority: priority,
                        }
                    },
                    columns: [{
                            data: 'supplier',
                            name: 'supplier'
                        },
                        {
                            data: 'supplier_category_name',
                            name: 'supplier_category.name'
                        },
                        {
                            data: 'priority',
                            name: 'priority'
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
                        $("#lbl_supplier_count").text(recordsTotal);
                    },
                });
            }

            $(document).on('click', '.filter', function() {
                getFilterData();
            });
        });
    </script>

    <script>
        $("#btn_manage_supplier_priority").on('click', function() {
            $("#manageSupplierPriorityModal").modal('show');
            getSupplierPriorityList();
        });
        $("#frm_manage_supplier_priority").on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData($("#frm_manage_supplier_priority")[0]);
            formData.append('_token', "{{ csrf_token() }}");

            $.ajax({
                url: '{{ route('supplier.add_new_priority') }}',
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                beforeSend: function() {},
                success: function(result) {
                    if (result.code == 200) {
                        toastr["success"](result.message);
                        $("#frm_manage_supplier_priority")[0].reset();
                        getSupplierPriorityList();
                    }

                    if (result.code == 500) {
                        toastr["error"](result.message);
                    }
                },
                error: function() {

                }
            });
        });

        function getSupplierPriorityList() {

            $.ajax({
                url: '{{ route('supplier.get_supplier_priority_list') }}',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {},
                success: function(result) {
                    if (result.code == 200) {
                        $("#priority_list_table tbody").html(result.html);
                    }

                    if (result.code == 500) {
                        $("#priority_list_table tbody").html('<tr><td>' + result.message + '</td></tr>');
                    }
                },
                error: function() {

                }
            });
        }

        // get supplier detail
        $(document).on('click', '.update-supplier-priority', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('#updateSupplierPriority').modal('show');
            $.ajax({
                type: 'GET',
                url: "{{ route('supplier.get_supplier') }}",
                data: {
                    'id': id,
                },
                // dataType : "json",
                success: function(res) {
                    if (res.success === true) {
                        // let data = res.data;
                        $("#supplier_id").val(res.supplier.id);
                        $("#supplier").text(res.supplier.supplier);
                        $("#category").text(res.category);
                        $(res.supplier_priority_list).each(function(index, element) {
                            $('#supplier_priority_list').append('<option value=' + element.id +
                                '>' + element.priority + '</option>');
                        });
                        //   res.supplier_priority_list;
                    } else {
                        toastr.error(res.msg);
                    }
                }
            });
        })

        // update supplier priority
        $(document).on('click', '#btn_update_supplier_priority', function(e) {
            e.preventDefault();
            var id = $("#supplier_id").val();
            var priority = $("#supplier_priority_list").val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ route('supplier.update_priority') }}",
                data: {
                    'id': id,
                    'priority': priority,
                },
                success: function(res) {
                    if (res.success === true) {
                        $('#updateSupplierPriority').modal('hide');
                        toastr.success(res.message);
                        $('.supplier-priority-table').DataTable().draw();
                    } else {
                        toastr.error(res.message);
                    }
                }
            });
        })
    </script>
@endsection
