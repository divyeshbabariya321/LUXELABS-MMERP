@extends('layouts.app')
@section('title', 'Conditions checked Products')
@section('styles')
    <style>
        .btn-link {
            color: #337ab7 !important;
        }
    </style>
@endsection
@section('large_content')
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2 class="page-heading">Conditions checked Products(<span id="lbl_product_count">0</span>) </h2>
            <form method="get" id="checkconditionForm">
                <div class="row">
                    <div class="col-md-2 mt-3">
                        <input type="text" name="id" class="form-control" id="id" placeholder="Product Id" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="name" class="form-control" id="name"
                            placeholder="Product Name" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="brand" class="form-control" id="brand" placeholder="Brand" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="category" class="form-control" id="category" placeholder="Category" />
                    </div>

                    <div class="col-md-2 mt-3">
                        <input type="text" name="title" class="form-control" id="producttitle"
                            placeholder="Product Title" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="composition" class="form-control" id="composition"
                            placeholder="Composition" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 mt-3">
                        <input type="text" name="color" class="form-control" id="color" placeholder="Color" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="price" class="form-control" id="price" placeholder="Price" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="status" class="form-control" id="status" placeholder="status" />
                    </div>
                    <div class="col-md-4 mt-3">
                        <a class="filter-data" href="#"><i class="fa fa-search"></i></a>
                    </div>
                    <div class="col-md-2 mt-3">
                        <button type="button" class="btn btn-secondary float-right push-to-magento">Push to magento</button>
                    </div>
                </div>
            </form>
            <div class="infinite-scroll table-responsive mt-3 infinite-scroll-data appendData">

            </div>
            <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..."
                style="display: none" />
        </div>
    </div>
    <div id="conditionCheckLogModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h3 class="modal-title">Product conditions check logs</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Message</th>
                                <th scope="col">Status</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody id="logData">


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div id="logListMagentoDetailModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h3 class="modal-title">Log message</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loglist_message"></div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            var formData = new FormData($("#checkconditionForm")[0]);
            var current_page = {{ (request('page'))?request('page'):1 }};
            $.ajax({
                url: "{{ route('products.magentoConditionsCheck') }}?page="+current_page,
                type: 'GET',
                dataType:'json',
                success: function(response) {
                    if(response.status == 200) {
                        $(".appendData").html(response.data.view);
                        $("#lbl_product_count").html(response.data.productsCount);
                    }
                }
            })
        })

        function filterMagento(field, val) {
            var fieldData = field;
            $.ajax({
                url: "{{ route('products.autocompleteForFilter') }}",
                type: 'POST',
                data: {
                    "filedname": field,
                    "value": val,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    $("#" + field).autocomplete({
                        source: response.data,
                        select: function(event, ui) {
                            var url = '/products/listing/conditions-check?' + field + '=' + ui.item
                                .value
                            $.ajax({
                                url: url,
                                type: 'GET',
                                success: function(response) {
                                    $(".appendData").html(response);
                                }
                            })
                        }
                    });

                }
            })
        }

        $("input[name='id']").keyup(function() {
            var value = $(this).val();
            filterMagento("id", value);
        })
        $("input[name='name']").keyup(function() {
            var value = $(this).val();
            filterMagento('name', value);
        })
        $("input[name='brand']").keyup(function() {
            var value = $(this).val();
            filterMagento('brand', value);
        })
        $("input[name='category']").keyup(function() {
            var value = $(this).val();
            filterMagento('category', value);
        })
        $("input[name='title']").keyup(function() {
            var value = $(this).val();
            filterMagento('title', value);
        })
        $("input[name='composition']").keyup(function() {
            var value = $(this).val();
            filterMagento('composition', value);
        })
        $("input[name='color']").keyup(function() {
            var value = $(this).val();
            filterMagento('color', value);
        })
        $("input[name='price']").keyup(function() {
            var value = $(this).val();
            filterMagento('price', value);
        })
        $("input[name='status']").keyup(function() {
            var value = $(this).val();
            filterMagento('status', value);
        })

        $(".filter-data").on('click', function() {
            let id = $("#id").val();
            let compisition = $("#compisition").val();
            let color = $("#color").val();
            let status = $("#status").val();
            let price = $("#price").val();
            let title = $("#title").val();
            let name = $("#name").val();
            var url = '/products/listing/conditions-check?id=' + id + "&name=" + name + "&title=" + title +
                "&composition=" + composition + "&color=" + color + "&status=" + status + "&price=" + price;
            $.ajax({
                url: url,
                type: 'GET',
                dataType:'json',
                success: function(response) {
                    if(response.status == 200) {
                        $(".appendData").html(response.data.view);
                        $("#lbl_product_count").html(response.data.productsCount);
                    }
                }
            })
        });

        $(document).on('click', '.push-to-magento', function() {
            $(self).hide();
            var ajaxes = [];
            url = "{{ route('products.pushToMagento') }}";
            ajaxes.push($.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function () {
                    $("#loading-image-preview").show();
                }
            }).done(function (response) {
                if(response.code == 500) {
                    toastr['error'](response.message, 'Error');
                } else {
                    toastr['success'](response.message, 'Success');
                }
                $("#loading-image-preview").hide();
                $('#product' + id).hide();
            }).fail(function (response) {
                toastr['error']('Internal server error', 'Failure')
                $('#product' + id).hide();
            }));
            $.when.apply($, ajaxes)
                .done(function() {
                    //location.reload();
                });
        });

        function getConditionCheckLog(pID, swId) {
            $.ajax({
                url: "{{ url('products/listing/conditions-check-logs/') }}" + "/" + pID + "/" + swId,
                type: "get"
            }).done(function(response) {
                if (response.code = '200') {
                    let html = '';
                    $.each(response.data, function(key, val) {
                        html += '<tr><td>' + val.id + '</td>' +
                            '<td>' + val.message + '</td>' +
                            '<td>' + val.response_status + '</td>' +
                            '<td>' + val.created_at + '</td>' +
                            '</tr>';
                    });
                    $('#logData').html(html);
                    $('#conditionCheckLogModal').modal('show');
                } else {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function(errObj) {
                $('#loading-image').hide();
                $("#todolistUpdateModal").hide();
                toastr['error'](errObj.message, 'error');
            });
        }
        
        function getLogListMagentoDetail(llm_id) {
            var request_url = "{{ route('products.getLogListMagentoDetail', ':llm_id') }}";
            request_url = request_url.replace(':llm_id', llm_id);
            $.ajax({
                url: request_url,
                type: "get"
            }).done(function(response) {
                if (response.code == '200') {
                    $('#logListMagentoDetailModal').modal('show');
                    $("#loglist_message").text(response.data.message);
                }
                if (response.code == '500') {
                    toastr['error'](response.msg, 'error');
                } 
                
            }).fail(function(errObj) {
                toastr['error'](errObj.message, 'error');
            });
        }
    </script>
@endsection
