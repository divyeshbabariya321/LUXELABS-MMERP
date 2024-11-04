@extends('layouts.app')

@section('styles')

<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">

<style type="text/css">
    .select2-search__field{
        padding-left: 5px;
    }
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
        #reason-select{
            display: none;
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
                        <a class="text-dark" data-toggle="collapse" href="#collapse1">Crop Reference Grid (<span id="total">0</span>) ({{ $pendingProduct }}) ({{ $pendingCategoryProduct }})</a>
                    </h2>
                </div>

            <div class="pull-left">
                <form method="GET" action="crop-references-grid" class="form-inline align-items-start">

                    <div class="form-group mr-3">
                        <select data-placeholder="Status Type" class="form-control select-multiple2" name="status" id="status">
                            <optgroup label="Status Type">
                                <option value="0">Select Status</option>
                                <option value="4">AutoCrop</option>
                                <option value="18">Crop Rejected</option>
                                <option value="12">Manual Image Upload</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group mr-3 mb-3">
                        <select data-placeholder="Select Category" style="width: 250px" class="ajax-get-categories category-cls form-control " id="category" name="category[]">
                            <option value="">Select Category</option>
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        <select data-placeholder="Product id" style="width: 250px; height: 10px" class="ajax-get-product-ids form-control " id="filter-id" name="filter_id[]">
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        <select data-placeholder="Select Brands" style="width: 250px" class="ajax-get-brands form-control " id="brand" name="brand[]">
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        <select data-placeholder="Select Supplier" style="width: 250px" class="ajax-get-supplier supplier-cls form-control " id="supplier" name="supplier[]">
                        </select>
                    </div>

                    {{-- <button type="submit" class="btn btn-image"><img src="{{asset('/images/filter.png')}}"/></button> --}}
                    <button id="refresh_page" type="button" class="btn btn-image"><img src="{{asset('/images/resend2.png')}}" /></button>
                </form>
            </div>

            <div class="pull-right">
                 <button onclick="addTask()" class="btn btn-secondary">Add Issue</button>
                 <button onclick="rejectImage()" class="btn btn-secondary">Reject Image</button>
                 <button class="btn btn-secondary btn-instances-manage">Instances</button>
                 <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#cropdatatablecolumnvisibilityList">Column Visiblity</button>
                 <a target="_blank" href="{{route('crop-references.logs')}}" class="btn btn-secondary"> Image Crop Logs History</a>

                 <select class="form-control-sm form-control bg-secondary text-light" name="reject_cropping" id="reason-select">
                    <option value="0">Select...</option>
                    <option value="Images Not Cropped Correctly">Images Not Cropped Correctly</option>
                    <option value="No Images Shown">No Images Shown</option>
                    <option value="Grid Not Shown">Grid Not Shown</option>
                    <option value="Blurry Image">Blurry Image</option>
                    <option value="First Image Not Available">First Image Not Available</option>
                    <option value="Dimension Not Available">Dimension Not Available</option>
                    <option value="Wrong Grid Showing For Category">Wrong Grid Showing For Category</option>
                    <option value="Incorrect Category">Incorrect Category</option>
                    <option value="Only One Image Available">Only One Image Available</option>
                    <option value="Image incorrect">Image incorrect</option>
                    <option value="Auto rejected">Image incorrect</option>
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="panel-group">
                <div class="panel mt-5 panel-default">

                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a type="button" data-toggle="collapse" href="#collapse1">Crop Stats</a>
                        </h4>
                    </div>

                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="pull-right">
                            <form action="crop-references-grid" method="GET">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12 d-flex">
                                                <div id="reportrange_phone" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                                        <input type="hidden" name="customer_range" id="customer_range">
                                                        <i class="fa fa-calendar"></i>&nbsp;
                                                        <span></span> <i class="fa fa-caret-down"></i>
                                                </div>
                                            <button class="btn btn-image" type="button"><img src="{{asset('/images/filter.png')}}" onclick="getCount()"></button>
                                        </div>
                                 </div>
                             </div>
                         </form>
                     </div>
                            <table class="table table-bordered table-striped" id="phone-table">
                                <thead>
                                <tr>
                                    <th>Count</th>
                                    <th>Date Time Range</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <th><span id="count_images"></span></th>
                                    <th><span id="date_time"></span></th>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table-striped table-bordered table" id="crop-reference-grid">
                    <thead>
                    <tr style="width: auto;">
                        <th>ID <input type="checkbox" name="" id="globalCheckbox"></th>
                        <th>Pro. Id</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Brand</th>
                        <th>Store Website</th>
                        <th>Image Score</th>
                        <th>Original Image</th>
                        <th>Cropped Image</th>
                        <th>Time</th>
                        <th>Date</th>
                        <th>Action</th>
                        <th>Issue</th>
                    </tr>
                     </thead>
                    <tbody id="content_data">

                    </tbody>
                </table>
            </div>
        </div>

        
    </div>

    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="show-http-status" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width:100%;max-width:96%">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">HTTP Status</h4>
                </div>
                <div class="modal-body">
                    <h4>Request:</h4>
                    <div class="request-body"></div>

                    <h4>Response:</h4>
                    <div class="response-body"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="manage-crop-instance" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Manage Crop Instances</h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="manage-log-instance" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Log Instances</h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
@include('partials.modals.task-module')
@include('partials.modals.large-image-modal')
@include("image_references.partials.column-visibility-modal")
@endsection

@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
    $(function () {
        
        getGridData();
        
        function getGridData(action_type = '') {
            if(action_type == '') {
                var params = {
                        brand: $('#brand').val(),
                        category: $('.category-cls').val(),
                        crop : $('#crop').val(),
                        supplier : $('.supplier-cls').val(),
                        status : $('#status').val(),
                        filter_id : $('#filter-id').val(),
                    };
            } else {
                var params = {};
            }
            
            var table = $('#crop-reference-grid').DataTable({
                destroy:true,
                processing: true,
                serverSide: true,
                ajax: { 
                    url:"{{ route('grid.reference') }}",
                    data:params
                },
                columns: [
                    {data: 'id', name: 'id', 'visible' : @if(in_array('ID', $dynamicColumnsToShowCrop)) false @else true @endif},
                    {data: 'product_id', name: 'product_id', 'visible' : @if(in_array('Pro. Id', $dynamicColumnsToShowCrop)) false @else true @endif},
                    {data: 'product_category_title', name: 'product_category_title', 'visible' : @if(in_array('Category', $dynamicColumnsToShowCrop)) false @else true @endif},
                    {data: 'product_supplier', name: 'product_supplier', 'visible' : @if(in_array('Supplier', $dynamicColumnsToShowCrop)) false @else true @endif},
                    {data: 'product_brands_name', name: 'product_brands_name', 'visible' : @if(in_array('Brand', $dynamicColumnsToShowCrop)) false @else true @endif},
                    {
                        data: 'store_website',
                        name: 'store_website',
                        render: function(data, type, row, meta) {                           
                            let datas =
                                `<div class="data-content">
                                        ${data == null ? '' : `<div class="expand-row module-text"><div class="flex items-center justify-left td-mini-container">${setStringLength(data, 18)}</div><div class="flex items-center justify-left td-full-container hidden">${data}</div></div>`}
                                </div>`;

                            return `<div class="flex flex-center-block" style="position: relative;">${datas}</div>`;
                        }
                    },
                    {data: 'image_score', name: 'image_score',orderable: false,searchable: false, 'visible' : @if(in_array('Image Score', $dynamicColumnsToShowCrop)) false @else true @endif},
                    {data: 'original_image', name: 'original_image',orderable: false,searchable: false, 'visible' : @if(in_array('Original Image', $dynamicColumnsToShowCrop)) false @else true @endif},
                    {data: 'cropped_image', name: 'cropped_image',orderable: false,searchable: false, 'visible' : @if(in_array('Cropped Image', $dynamicColumnsToShowCrop)) false @else true @endif},
                    {data: 'speed', name: 'speed',orderable: false,searchable: false, 'visible' : @if(in_array('Time', $dynamicColumnsToShowCrop)) false @else true @endif},
                    {data: 'date', name: 'date',orderable: false,searchable: false, 'visible' : @if(in_array('Date', $dynamicColumnsToShowCrop)) false @else true @endif},
                    {data: 'action', name: 'action',orderable: false,searchable: false, 'visible' : @if(in_array('Action', $dynamicColumnsToShowCrop)) false @else true @endif},
                    {data: 'issue', name: 'issue',orderable: false,searchable: false, 'visible' : @if(in_array('Issue', $dynamicColumnsToShowCrop)) false @else true @endif},
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
        
        $('#brand,#category,#crop,#supplier,#status,#filter-id').on('change', function () {
            getGridData();
        });
        
        $('#refresh_page').on('click', function () {
            getGridData('refresh');
        });  
    });
</script>

<script type="text/javascript">
      $(document).ready(function () {
             $(".select-multiple").multiselect();
             $(".select-multiple2").select2();
        });     
    function bigImg(img){
        $('#image_crop').attr('src',img);
        $('#largeImageModal').modal('show');
    }
    function normalImg(){
        $('#largeImageModal').modal('hide');
    }
    function addTask() {
       var id = [];
            $.each($("input[name='issue']:checked"), function(){
                id.push($(this).val());
            });
        if(id.length == 0){
            alert('Please Select Image');
        }else{
            $('#taskModal').modal('show');
            $('#task_subject').val('Image ID '+id);
            $('#module').val('41');
            $('#response').val(0);
        }   
        
    }
     
    $('#globalCheckbox').click(function(){
            if($(this).prop("checked")) {
                $(".checkBox").prop("checked", true);
            } else {
                $(".checkBox").prop("checked", false);
            }                
        });
        var start = moment().subtract(29, 'days');
        var end = moment();
        function cs(start, end) {
            if(start.format('YYYY-MM-DD') == '1995-12-25'){
                $('#reportrange_phone span').html(end.format('MMMM D, YYYY'));
            }else{
                $('#reportrange_phone span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
            $('#customer_range').val(start.format('YYYY/MM/DD')+' - '+end.format('YYYY/MM/DD'));
        }
        $('#reportrange_phone').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
             'Past Hour' : [moment('1995-12-25'), moment()],  
             'Today': [moment(), moment()],
             'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
             'Last 7 Days': [moment().subtract(6, 'days'), moment()],
             'Last 30 Days': [moment().subtract(29, 'days'), moment()],
             'This Month': [moment().startOf('month'), moment().endOf('month')],
             'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cs)
        cs(start, end);
</script>

 <script type="text/javascript">
 var page = 1;

        function getCount(){
            $.ajax({
                    url: '/crop-references-grid/cropStats',
                    dataType: "json",
                    data: {
                        customer_range: $('#customer_range').val(),
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                }).done(function (data) {
                    $("#loading-image").hide();
                    console.log(data);
                    $("#count_images").text(data.count);
                    string = $('#customer_range').val();
                    $("#date_time").text(string.replace('1995/12/25',''));
                   
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    alert('No response from server');
                });
        }
        $(document).on('change', '.reject-cropping', function (event) {
            let pid = $(this).data('id');
            let remark = $(this).val();
            if (remark == 0 || remark == '0') {
                return;
            }
            let self = this;
            $.ajax({
                url: '/products/auto-cropped/' + pid + '/reject',
                data: {
                    remark: remark,
                    _token: "{{csrf_token()}}",
                    senior: 1
                },
                type: 'GET',
                success: function () {
                    toastr['success']('Crop rejected successfully!', 'Success');
                    removeIdFromArray(pid);
                    $(self).removeAttr('disabled');
                },
                error: function () {
                    $(self).removeAttr('disabled');
                },
                beforeSend: function () {
                    $(self).attr('disabled');
                }
            });
        });
        function rejectImage(){
            var id = [];
            $.each($("input[name='issue']:checked"), function(){
                id.push($(this).val());
            });
            if(id.length == 0){
                alert('Please Select Image');
            }else{
                $('#reason-select').show();
            }
        }
         $(document).on('change', '#reason-select', function (event) {
            let remark = $(this).val();
            $.each($("input[name='issue']:checked"), function(){
            if (remark == 0 || remark == '0') {
                return;
            }
            pid = $(this).attr('data-id');
            $.ajax({
                url: '/products/auto-cropped/' + pid + '/reject',
                data: {
                    remark: remark,
                    _token: "{{csrf_token()}}",
                    senior: 1
                },
                type: 'GET',
                success: function () {
                    toastr['success']('Crop rejected successfully!', 'Success');
                    $(self).removeAttr('disabled');
                },
                error: function () {
                    $(self).removeAttr('disabled');
                },
                beforeSend: function () {
                    $(self).attr('disabled');
                }
            });
            });
            $('#reason-select').hide();
        });
        $('.ajax-get-product-ids').select2({
            tags: true,
            multiple: true,
            language: {
                "noResults": function(){
                    return "Please enter Product id";
                }
            }
        });
        const $brandsSelect = $('.ajax-get-brands'),
            $supplierSelect = $('.ajax-get-supplier'),
            $categoriesSelect = $('.ajax-get-categories');
        $brandsSelect.select2();
        $supplierSelect.select2();
        $categoriesSelect.select2();
        const ajaxGetSupplier = () => {
            return $.ajax({
                url: '{{url('/crop-references-grid/getSupplier')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function (data) {
                    $supplierSelect.addClass('select-multiple2');
                    $supplierSelect.attr('multiple','multiple');
                    return {
                        results: data.result
                    };
                }
            })
        }
        const ajaxGetCategories = () => {
            return $.ajax({
                url: '{{url('/crop-references-grid/getCategories')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                },
            })
        }
        const ajaxGetBrands = () => {
            return $.ajax({
                url: '{{url('/crop-references-grid/getBrands')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function (data) {
                    $brandsSelect.addClass('select-multiple2');
                    $brandsSelect.attr('multiple','multiple');
                    return {
                        results: data.result
                    };
                },
            })
        }
        ajaxGetBrands().done((result)=>{
            $brandsSelect.select2({
                multiple:true,
                data:result.result
            })
        })
        ajaxGetSupplier().done((result)=>{
            $supplierSelect.select2({
                multiple:true,
                data:result.result
            })
        })
        ajaxGetCategories().done((result)=>{
            $categoriesSelect.select2({
                data:result.result,
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(data) {
                    return data.text;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            })
        })
        $(document).on("click",".btn-instances-manage",function() {
            $.ajax({
                url: '{{url('crop-references-grid/manage-instances')}}',
                success: function (data) {
                    $("#manage-crop-instance").find(".modal-body").html(data);
                    $("#manage-crop-instance").modal("show");
                },
            });
        });
        $(document).on("click",".add-instance",function(e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                url: '/crop-references-grid/add-instance',
                method:"post",
                data : $this.closest('form').serialize(),
                success: function (data) {
                    $("#manage-crop-instance").find(".modal-body").html(data);
                },
            });
        });
        $(document).on("click",".btn-delete-manage-instances",function(e) {
            e.preventDefault();
            var $id = $(this).data("id");
            $.ajax({
                url: '/crop-references-grid/delete-instance',
                method:"get",
                data : {
                    id : $id
                },
                success: function (data) {
                    $("#manage-crop-instance").find(".modal-body").html(data);
                },
            });
        });
        $(document).on("click",".btn-log-instances",function(e) {
            e.preventDefault();
            var $id = $(this).data("id");
            var $date=  $('#date11').val();
            $.ajax({
                url: '{{url('crop-references-grid/log-instance')}}',
                method:"get",
                data : {
                    id : $id,
                    date : $date
                },
                success: function (data) {
                   $("#manage-log-instance").find(".modal-body").html(data);
                   $("#manage-log-instance").modal('show'); 
                },
            });
        });
       
        $(document).on("click",".btn-start-manage-instances",function(e) {
            e.preventDefault();
            var $id = $(this).data("id");
            $.ajax({
                url: '/crop-references-grid/start-instance',
                method:"get",
                data : {
                    id : $id
                },
                dataType:"json",
                success: function (data) {
                    if(data.code == 200) {
                        toastr['success'](data.message, 'Success');
                    }else{
                        toastr['error'](data.message, 'Error');
                    }
                },
                error: function (jqXHR, exception) {
                    toastr['error'](jqXHR.responseText, 'Error');
                }
            });
        });
        $(document).on("click",".btn-stop-manage-instances",function(e) {
            e.preventDefault();
            var $id = $(this).data("id");
            $.ajax({
                url: '/crop-references-grid/stop-instance',
                method:"get",
                data : {
                    id : $id
                },
                dataType:"json",
                success: function (data) {
                    if(data.code == 200) {
                        toastr['success'](data.message, 'Success');
                    }else{
                        toastr['error'](data.message, 'Error');
                    }
                },
                error: function (jqXHR, exception) {
                    toastr['error'](jqXHR.responseText, 'Error');
                }
            });
        });
        $('#show-http-status').on('show.bs.modal', function (e) {
            $(this).find('.request-body').text(JSON.stringify($(e.relatedTarget).data('request')));
            $(this).find('.response-body').text(JSON.stringify($(e.relatedTarget).data('response')));
        });
//START - Load More functionality
	var isLoading = false;
	//var page = 1;
	$(document).ready(function () {
		/*$(window).scroll(function() {
			if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
				loadMore();
			}
		});*/
		function loadMore() {
			if (isLoading)
				return;
			isLoading = true;
			var $loader = $('.infinite-scroll-products-loader');
			page = page + 1;
			$.ajax({
				url: '{{url('/crop-references-grid')}}',
				type: 'GET',
				data: {
                    brand: $('#brand').val(),
                    category: $('#category').val(),
                    crop : $('#crop').val(),
                    supplier : $('#supplier').val(),
                    status : $('#status').val(),
                    filter_id : $('#filter-id').val(),
                    page : page,
                },
				beforeSend: function() {
					$loader.show();
				},
				success: function (data) {
					$loader.hide();
					$('#content_data').append(data.tbody);
					isLoading = false;
					if(data.tbody == "") {
						isLoading = true;
					}
				},
				error: function () {
					$loader.hide();
					isLoading = false;
				}
			});
		}
	});

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
	//End load more functionality
    </script>

@endsection