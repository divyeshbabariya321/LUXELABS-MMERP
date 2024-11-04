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
                <h2 class="page-heading">Incorrect Attributes (<span id="lbl_product_count">0</span>)</h2>
            </div>
            <div class="row pl-4 pr-4">
                <div class="col-md-6 border">
                    <h3 class="text-center mt-3 mb-3">Filter</h3>
                    <div class="row p-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>By Attributes</label>
                                <br>
                                <select data-placeholder="Select Attribute"  class="form-control select-multiple2" id="filter_status">
                                    <option value="0">Select All</option>
                                    @foreach($status_list as $status_id => $status_name)
                                        <option value="{{ $status_id }}">{{ $status_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>By Stock</label>
                                <br>
                                <select data-placeholder="Select Stock"  class="form-control select-multiple2" id="filter_stock">
                                    <option value="0">Select All</option>
                                    <option value="out_of_stock">Out Of Stock</option>
                                    <option value="in_stock">In Stock</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>By Job Status</label>
                                <br>
                                <select data-placeholder="Select Job Status"  class="form-control select-multiple2" id="filter_job_status">
                                    <option value="0">Select All</option>
                                    <option value="pending">Pending</option>
                                    <option value="success">Success</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 border">
                    <form id="attribute_form">
                        <h3 class="text-center mt-3 mb-3">Bulk Update</h3>
                        <div class="row p-2">
                            <div class="form-group col-md-4">
                                <label>Attribute assignment:</label>
                                <select data-placeholder="Select Attribute"  class="form-control select-multiple2" id="attribute_id"  name="attribute_id" required>
                                    @foreach($status_list as $status_id => $status_name)
                                        <option value="{{ $status_id }}">{{ $status_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="row">
                                    <div class="form-group col-md-6 attribute_category_box d-none">
                                        <label>Original Category:</label><br>
                                        <select data-placeholder="Select Category"  class="form-control select-multiple2" id="find_category"  name="find_category">
                                            <option value="0">0</option>
                                            @foreach($categories as $category_key => $category)
                                                <option value="{{ $category->id }}">{{ $category->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 attribute_category_box d-none">
                                        <label>ERP Category:</label><br>
                                        <select data-placeholder="Select Category"  class="form-control select-multiple2" id="replace_category"  name="replace_category">
                                            <option value="0">0</option>
                                            @foreach($categories as $category_key => $category)
                                                <option value="{{ $category->id }}">{{ $category->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 attribute_color_box d-none">
                                        <label>Original Colors:</label><br>
                                        <select data-placeholder="Select Color"  class="form-control select-multiple2" id="find_color"  name="find_color">
                                            <option value="NULL">NULL</option>
                                            @foreach($colors as $color_key => $color)
                                                <option value="{{ $color }}">{{ $color}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col-md-6 attribute_color_box d-none">
                                        <label>ERP Colors:</label><br>
                                        <select data-placeholder="Select Color"  class="form-control select-multiple2" id="replace_color"  name="replace_color">
                                            <option value="NULL">NULL</option>
                                            @foreach($colors as $color_key => $color)
                                                <option value="{{ $color }}">{{ $color}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 attribute_size_box">
                                        <label>Original Size:</label>
                                        <br>
                                        <input type="text" id="find_size" name="find_size" class="form-control" placeholder="Find Size">
                                    </div>
                                    <div class="form-group col-md-6 attribute_size_box">
                                        <label>ERP Size:</label>
                                        <select data-placeholder="Select Size"  class="form-control select-multiple2" id="replace_size"  name="replace_size[]" multiple>
                                            @foreach($sizes as $size_key => $size)
                                                <option value="{{ $size->name }}">{{ $size->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 attribute_measurement_box d-none">
                                        <label>Original L measurement:</label>
                                        <br>
                                        <input type="text" id="find_lmeasurement" name="find_lmeasurement" class="form-control" placeholder="L measurement">
                                    </div>
                                    <div class="form-group col-md-6 attribute_measurement_box d-none">
                                        <label>ERP L measurement:</label>
                                        <br>
                                        <input type="text" id="replace_lmeasurement" name="replace_lmeasurement" class="form-control" placeholder="L measurement">
                                    </div>
                                    <div class="form-group col-md-6 attribute_measurement_box d-none">
                                        <label>Original H measurement:</label>
                                        <br>
                                        <input type="text" id="find_hmeasurement" name="find_hmeasurement" class="form-control" placeholder="H measurement">
                                    </div>
                                    <div class="form-group col-md-6 attribute_measurement_box d-none">
                                        <label>ERP H measurement:</label>
                                        <br>
                                        <input type="text" id="replace_hmeasurement" name="replace_hmeasurement" class="form-control" placeholder="H measurement">
                                    </div>
                                    <div class="form-group col-md-6 attribute_measurement_box d-none">
                                        <label>Original D measurement:</label>
                                        <br>
                                        <input type="text" id="find_dmeasurement" name="find_dmeasurement" class="form-control" placeholder="D measurement">
                                    </div>
                                    <div class="form-group col-md-6 attribute_measurement_box d-none">
                                        <label>ERP D measurement:</label>
                                        <br>
                                        <input type="text" id="replace_dmeasurement" name="replace_dmeasurement" class="form-control" placeholder="D measurement">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <br>
                                <button id="btn_assign" type="submit" class="btn btn-default ml-2 small-field-btn btn-secondary ">Assign</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table-striped table-bordered table" id="unknown-attribute-products-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Supplier</th>
                            <th>Attribute</th>
                            <th>Original Value</th>
                            <th>ERP Value</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div id="product-attribute-detail" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="update_attribute_form">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Attribute</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="product_id" name="product_id"/>
                        <input type="hidden" id="update_attribute_id" name="attribute_id"/>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Product ID: </label> <span id="lbl_product_id"></span>
                            </div>
                            <div class="col-md-12">
                                <label>Name: </label> <span id="lbl_product_name"></span>
                            </div>
                            <div class="col-md-12">
                                <label>SKU: </label> <span id="lbl_sku"></span>
                            </div>
                            <div class="col-md-12">
                                <label>Supplier: </label> <span id="lbl_supplier"></span>
                            </div>
                            <div class="col-md-12">
                                <label>Attribute: </label> <span id="lbl_attribute_name"></span>
                            </div>
                            <div class="col-md-12">
                                <label>Original Value: </label> <span id="lbl_original_value"></span>
                            </div>
                            <div class="col-md-12">
                                <label>Erp Value: </label> <span id="lbl_erp_value"></span>
                            </div>
                            <div class="form-group col-md-12 mt-2">
                                <label><u>Attribute Replacement</u></label>
                                <div class="row">
                                    <div class="form-group col-md-6 product_attribute_category_box d-none">
                                        <label>Select Category:</label><br>
                                        <select data-placeholder="Select Category"  class="form-control select-multiple2" id="product_replace_category"  name="replace_category">
                                            <option value="0">0</option>
                                            @foreach($categories as $category_key => $category)
                                                <option value="{{ $category->id }}">{{ $category->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 product_attribute_color_box d-none">
                                        <label>Select Color:</label><br>
                                        <select data-placeholder="Select Color"  class="form-control select-multiple2" id="product_replace_color"  name="replace_color">
                                            <option value="NULL">NULL</option>
                                            @foreach($colors as $color_key => $color)
                                                <option value="{{ $color }}">{{ $color}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12 product_attribute_size_box d-none">
                                        <label>Select Size:</label>
                                        <select class="form-control select-multiple2" id="product_replace_size"  name="replace_size[]" multiple>
                                            @foreach($sizes as $size_key => $size)
                                                <option value="{{ $size->name }}">{{ $size->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 product_attribute_measurement_box d-none">
                                        <label>L measurement:</label>
                                        <br>
                                        <input type="text" id="product_replace_lmeasurement" name="replace_lmeasurement" class="form-control" placeholder="L measurement">
                                    </div>
                                    <div class="form-group col-md-4 product_attribute_measurement_box d-none">
                                        <label>H measurement:</label>
                                        <br>
                                        <input type="text" id="product_replace_hmeasurement" name="replace_hmeasurement" class="form-control" placeholder="H measurement">
                                    </div>
                                    <div class="form-group col-md-4 product_attribute_measurement_box d-none">
                                        <label>D measurement:</label>
                                        <br>
                                        <input type="text" id="product_replace_dmeasurement" name="replace_dmeasurement" class="form-control" placeholder="D measurement">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="update_attribute" type="submit" class="btn btn-success" >Update</button>
                        <button type="reset" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="product-attribute-history" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="product_attribute_history_form">
                    <div class="modal-header">
                        <h4 class="modal-title">Show Updated History</h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Attribute Name</th>
                                        <th>Old Value</th>
                                        <th>New Value</th>
                                        <th>Updated by</th>
                                    </tr>
                                </thead>
                                <tbody id="res_attribute_updated_history">
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
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
    getAttributes();
    
    function getAttributes() {
        var status_id =  $("#filter_status").val();
        var filter_job_status =  $("#filter_job_status").val();
        var filter_stock =  $("#filter_stock").val();
        var table = $('#unknown-attribute-products-table').DataTable({
            destroy:true,
            processing: true,
            serverSide: true,
            ajax: { 
                url:"{{ route('incorrect-attributes') }}",
                data:{status_id:status_id,filter_stock:filter_stock,filter_job_status:filter_job_status}
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'sku', name: 'sku'},
                {data: 'supplier', name: 'supplier'},
                {data: 'attribute_name', name: 'attribute_name',searchable: false},
                {data: 'original_value', name: 'original_value',orderable: false, searchable: false},
                {data: 'erp_value', name: 'erp_value',orderable: false, searchable: false},
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
                $("#lbl_product_count").text(recordsTotal);
                // now do something with those variables
            },
            createdRow: function( row, data, dataIndex ) {
                var job_status_class = '';
                if(data.updated_attribute_job_status == 0) {
                    job_status_class = 'pending-job';
                } else if (data.updated_attribute_job_status == 1){
                    job_status_class = 'success-job';
                } else if (data.updated_attribute_job_status == 2){
                    job_status_class = 'failed-job';
                }
                $( row ).addClass(job_status_class);
            }
        });
    }
    
    $("#filter_status,#filter_stock,#filter_job_status").on('change',function(){
        getAttributes();
    });
    
    $("#attribute_form").on('submit',function(e){
        e.preventDefault();

        var $this = $(this);
        var formData = new FormData($this[0]);
            formData.append('_token',"{{ csrf_token() }}");
        
        $.ajax({
            type: 'POST',
            url: "{{ route('incorrect-attributes.attribute-assignment') }}",
            beforeSend: function () {
                $("#btn_assign").attr('disabled',true);
            },
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
        }).done(function (response) {
            $("#btn_assign").removeAttr('disabled');
            if (response.code == 200) {
                toastr['success'](response.message, 'success');
                $(this)[0].reset();
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function (response) {
            $("#btn_assign").removeAttr('disabled');
            console.log("Sorry, something went wrong");
        });
        
       
    });
    
    
    $(document).on('click','.get-product-attribute-detail',function(e){

        var product_id = $(this).data('id');
        var $this = $(this);
        var formData = new FormData();
            formData.append('product_id',product_id);
            formData.append('_token',"{{ csrf_token() }}");
        
        $.ajax({
            type: 'POST',
            url: "{{ route('incorrect-attributes.get_product_attribute_detail') }}",
            beforeSend: function () {
                $this.attr('disabled',true);
            },
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
        }).done(function (response) {
            $this.removeAttr('disabled');
            if (response.code == 200) {
                $("#product-attribute-detail").modal('show');
                $("#update_attribute_form")[0].reset();
                $('#update_attribute_form .select-multiple2').val('').trigger('change');
                $("#update_attribute_id").val(response.results.status_id);
                $("#product_id").val(response.results.id);
                $("#lbl_product_id").text(response.results.id);
                $("#lbl_product_name").text(response.results.name);
                $("#lbl_sku").text(response.results.sku);
                $("#lbl_supplier").text(response.results.supplier);
                $("#lbl_attribute_name").text(response.results.attribute_name);
                $("#lbl_original_value").text(response.results.original_value);
                $("#lbl_erp_value").text(response.results.erp_value);
                
                var attribute_id = response.results.status_id;
                if(attribute_id == 36) {
                    $(".product_attribute_size_box").addClass('d-none');
                    $(".product_attribute_color_box").addClass('d-none');
                    $(".product_attribute_measurement_box").addClass('d-none');
                    $(".product_attribute_category_box").removeClass('d-none');
                } else if(attribute_id == 37) {
                    $(".product_attribute_size_box").addClass('d-none');
                    $(".product_attribute_color_box").removeClass('d-none');
                    $(".product_attribute_measurement_box").addClass('d-none');
                    $(".product_attribute_category_box").addClass('d-none');
                } else if(attribute_id == 38) {
                    $(".product_attribute_size_box").removeClass('d-none');
                    $(".product_attribute_color_box").addClass('d-none');
                    $(".product_attribute_measurement_box").addClass('d-none');
                    $(".product_attribute_category_box").addClass('d-none');
                } else if(attribute_id == 40) {
                    $(".product_attribute_size_box").addClass('d-none');
                    $(".product_attribute_color_box").addClass('d-none');
                    $(".product_attribute_measurement_box").removeClass('d-none');
                    $(".product_attribute_category_box").addClass('d-none');
                }
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function (response) {
            $this.removeAttr('disabled');
            console.log("Sorry, something went wrong");
        });
       
    });
    
    $(document).on('click','.get-product-attribute-history',function(e){

        var product_id = $(this).data('id');
        var $this = $(this);
        var formData = new FormData();
            formData.append('product_id',product_id);
            formData.append('_token',"{{ csrf_token() }}");

        $.ajax({
            type: 'POST',
            url: "{{ route('incorrect-attributes.get_product_attribute_history') }}",
            beforeSend: function () {
                $this.attr('disabled',true);
            },
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
        }).done(function (response) {
            $this.removeAttr('disabled');
            if (response.code == 200) {
                $("#product-attribute-history").modal('show');
                $("#res_attribute_updated_history").html(response.results);
            } else {
                toastr['error'](response.message, 'error');
                $("#res_attribute_updated_history").html('');
            }
        }).fail(function (response) {
            $this.removeAttr('disabled');
            console.log("Sorry, something went wrong");
        });

    });
    
    $("#update_attribute_form").on('submit',function(e){
        e.preventDefault();

        var $this = $(this);
        var formData = new FormData($this[0]);
            formData.append('_token',"{{ csrf_token() }}");
        
        $.ajax({
            type: 'POST',
            url: "{{ route('incorrect-attributes.update-attribute-assignment') }}",
            beforeSend: function () {
                $("#update_attribute").attr('disabled',true);
            },
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
        }).done(function (response) {
            $("#update_attribute").removeAttr('disabled');
            if (response.code == 200) {
                toastr['success'](response.message, 'success');
                $this[0].reset();
                $("#product-attribute-detail").modal('hide');
                $('#unknown-attribute-products-table').DataTable().ajax.reload();
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function (response) {
            $("#update_attribute").removeAttr('disabled');
            console.log("Sorry, something went wrong");
        });
        
       
    });
    
    $("#attribute_id").on('change',function(e){
        var attribute_id = $(this).val();
        if(attribute_id == 36) {
            $(".attribute_size_box").addClass('d-none');
            $(".attribute_color_box").addClass('d-none');
            $(".attribute_measurement_box").addClass('d-none');
            $(".attribute_category_box").removeClass('d-none');
        } else if(attribute_id == 37) {
            $(".attribute_size_box").addClass('d-none');
            $(".attribute_color_box").removeClass('d-none');
            $(".attribute_measurement_box").addClass('d-none');
            $(".attribute_category_box").addClass('d-none');
        } else if(attribute_id == 38) {
            $(".attribute_size_box").removeClass('d-none');
            $(".attribute_color_box").addClass('d-none');
            $(".attribute_measurement_box").addClass('d-none');
            $(".attribute_category_box").addClass('d-none');
        } else if(attribute_id == 40) {
            $(".attribute_size_box").addClass('d-none');
            $(".attribute_color_box").addClass('d-none');
            $(".attribute_measurement_box").removeClass('d-none');
            $(".attribute_category_box").addClass('d-none');
        }
    });
    
  });
  
</script>
@endsection