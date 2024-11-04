@extends('layouts.app')
@section('title', 'Magento push status')

@section('large_content')
    @include('partials.flash_messages')
    <div class="row">

        <div class="col-md-12 margin-tb">
            <form method="get" id="magentoPushStatusForm">
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
                        <input type="text" name="composition" class="form-control" id="composition"
                            placeholder="Composition" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="color" class="form-control" id="color" placeholder="Color" />
                    </div>
                </div>
                <div class="row mb-3">
                  
                    <div class="col-md-2 mt-3">
                        <input type="text" name="price" class="form-control" id="price" placeholder="Price" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="status" class="form-control" id="status" placeholder="status" />
                    </div>
                    <div class="col-md-4 mt-3 mb-3">
                        <a class="filter-data" href="#"><i class="fa fa-search"></i></a>
                    </div>
                </div>
            </form>
            <h2 class="page-heading">Magento push status </h2>
            <div class="infinite-scroll table-responsive mt-5 infinite-scroll-data appendDataPushMagento">

            </div>
            <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..."
                style="display: none" />
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            var formData = new FormData($("#magentoPushStatusForm")[0]);
            const urlSearchParams = new URLSearchParams(window.location.search);
            const params = Object.fromEntries(urlSearchParams.entries());
            $.ajax({
                url: "{{ route('products.magentoPushStatus') }}",
                type: 'GET',
                data: params,
                success: function(response) {
                    $(".appendDataPushMagento").html(response);
                }
            })
        })
        function filterFunction(field, val) {
            var fieldData = field;
            $.ajax({
                url: "{{ route('products.autocompleteSearchPushStatus') }}",
                type: 'POST',
                data: {
                    "filedname": field,
                    "search_value": val,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    $("#" + field).autocomplete({
                        source: response.data,
                        select: function(event, ui) {
                            var url = '/products/listing/magento-push-status?' + field + '=' + ui.item
                                .value
                            $.ajax({
                                url: url,
                                type: 'GET',
                                success: function(response) {
                                    $(".appendDataPushMagento").html(response);
                                }
                            })
                        }
                    });
                }
            })
        }
        $("input[name='id']").keyup(function() {
            var value = $(this).val();
            filterFunction("id", value);
        })
        $("input[name='name']").keyup(function() {
            var value = $(this).val();
            filterFunction('name', value);
        })
        $("input[name='brand']").keyup(function() {
            var value = $(this).val();
            filterFunction('brand', value);
        })
        $("input[name='category']").keyup(function() {
            var value = $(this).val();
            filterFunction('category', value);
        })
        $("input[name='composition']").keyup(function() {
            var value = $(this).val();
            filterFunction('composition', value);
        })
        $("input[name='color']").keyup(function() {
            var value = $(this).val();
            filterFunction('color', value);
        })
        $("input[name='price']").keyup(function() {
            var value = $(this).val();
            filterFunction('price', value);
        })
        $("input[name='status']").keyup(function() {
            var value = $(this).val();
            filterFunction('status', value);
        })
        $(".filter-data").on('click', function() {
            let id = $("#id").val();
            let compisition = $("#compisition").val();
            let color = $("#color").val();
            let status = $("#status").val();
            let price = $("#price").val();
            let name = $("#name").val();
            var url = '/products/listing/magento-push-status?id=' + id + "&name=" + name 
            +"&composition=" + composition + "&color=" + color + "&status=" + status + "&price=" + price;
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $(".appendDataPushMagento").html(response);
                }
            })
        });
    </script>
@endpush