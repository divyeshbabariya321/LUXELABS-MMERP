    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Scraped Products ( {{ count($products)}} )</h2>
        </div>
    </div>
    
    <div class="col-md-12 ">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered" id="quick-reply-list" style="table-layout: fixed;">
                <tr>
                    <th width="2%">#</th>
                    <th width="6%">Product Name</th>
                    <th width="3%">Product Brand</th>
                    <th width="3%">Website</th>
                    <th width="10%">Description</th>
                    <th width="5%"><input type="button" onclick="setProductDescription()" data-sku="{{$sku}}" class="btn btn-secondary setProductDescription" value="Update"></th>
                </tr>
                @foreach($products as $key=>$product)
                    <tr>
                        <td>{{ $product->sid }}</td>
                        <td class="Website-task visible-app">
                            {{$product->pname}}
                        </td>
                        <td class="Website-task visible-app">
                            {{$product->bname}}
                        </td>
                        <td class="Website-task visible-app">
                            {{$product->website}}
                        </td>
                        <td class="Website-task visible-app">
                             {{$product->description}}
                        </td>
                        <td class="Website-task visible-app clickable">
                            <input type="text" name="product" value="{{ $product->sort_order == 0 ? $key + 1 : $product->sort_order }}" data-id="{{ $product->sid }}" class="product-checkbox mr-2"> 
                        </td>
                        
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to proceed with this action?
                <b><span id="modal-body-content"></span></b>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="confirmNoBtn">No</button>
                <button type="button" class="btn btn-primary" id="confirmYesBtn">Yes</button>
            </div>
        </div>
    </div>
</div>
<script>
    function setProductDescription() {
        $("#loading-image").show();
        // Get the value of data-sku
        var sku = $(".setProductDescription").data("sku");
        var productData = [];
        $("input.product-checkbox").each(function () {
            var productId = $(this).data("id");
            var productValue = $(this).val();
            productData.push({ id: productId, value: productValue });
        });
        $.ajax({
        headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
        url: '{{ route("products.multidescription.skucheck")}}',
        method: "POST",
        data: { sku: sku, productData:productData},
        success: function (data) {
            $("#loading-image").hide();
            var productCount = data.result;
            if (productCount > 0) {
                $("#modal-body-content").html('Record exists also update on product description if click NO only update scraped product');
                $("#confirmationModal").modal("show");
                $("#confirmYesBtn").on("click", function () {
                    $("#loading-image").show();
                    // Call a function with the parameter "yes"
                    UpdateProduct(sku, productData, 1);
                });
                $("#confirmNoBtn").on("click", function () {
                    $("#loading-image").show();
                    // Call a function with the parameter "no"
                    UpdateProduct(sku, productData, 0);
                });
            }else{
                $("#loading-image").show();
                UpdateProduct(sku, productData, 0);
            }            
        },
        error: function (error) {
            console.log(error);
        }
        });
    }

    function UpdateProduct(sku, productData, condition) {
        $.ajax({
            headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
            url: '{{ route("products.multidescription.update")}}',
            method: "POST",
            data: { sku: sku, productData:productData, condition:condition},
            success: function (data) {
                $("#loading-image").hide();
                $("#confirmationModal").modal("hide");
                $("#show-content-model-table").modal("hide");
                toastr['success'](data.message, 'success');
                var redirectUrl = '/products/multi-description';
                window.location.href = redirectUrl;          
            },
            error: function (error) {
                console.log(error);
            }
        });
        }
</script>