<?php $isAdmin = auth()->user()->isAdmin();  ?>
<?php foreach($items as $date => $raw){ ?>
  <h4><?php echo $date; ?></h4>
    <div class="row">
    <?php 
      $rowCount = 0;
    ?>  
    <?php foreach($raw as $key=>$product) { ?>
      <?php 
        /*if($rowCount % 4 == 0) { echo '<div class="row">'; } 
           $rowCount++;  */
      ?>
      <div class="col-md-3 col-xs-6 text-left mb-5">
          <a href="{{ route( 'products.show', $product->id ) }}">
            @php
              $imageDetails = $product->getMedia($attach_image_tag)->first();
              $image = "";
              if($imageDetails) {
                $imageDetails->directory .= '/thumbnail';
                $imageDetails->filename .= '_thumb';
                $image = getMediaUrl($imageDetails);
                // $image = convertToThumbUrl(getMediaUrl($imageDetails),$imageDetails->extension);
              }
              @endphp
            <img style="object-fit: cover;max-width:75%;" src="{{ $image }}" class="img-responsive grid-image" alt="...">
          </a>      
          <div class="card-body">
            <p class="card-text">SKU : <span id="skuValue-{{$key}}">{{ $product->sku }}</span> &nbsp;
              <span class="glyphicon glyphicon-duplicate" style="cursor: copy" aria-hidden="true" onclick="copySKU('skuValue-{{$key}}')"></span>
            </p>
            <a href="{{ route( 'products.show', $product->id ) }}">
              <p class="card-text">Id : {{ $product->id }}</p>
              <p class="card-text">Size : {{ $product->size }}</p>
              @if($isAdmin)
                <p class="card-text">Price : {{ $product->price }}</p>
                <p class="card-text">Discounted % : {{ $product->discounted_percentage }}%</p>
              @endif
              <p class="card-text">Price INR : {{ $product->price_inr }}</p>  
              <p class="card-text">Status : {{ (new \App\Stage)->getNameById( $product->stage ) }}</p>
              <p class="card-text">Ref. Category : {{ $product->reference_category }}</p>
            </a>
            @if($isAdmin)
            <p class="card-text">Category : {{ html()->select("category", $categoryArray, $product->category)->class("form-control update-product")->id("category_" . $product->id)->data('id', $product->id) }}</p>
            @else 
              <p class="card-text">Category : {{ ($product->categories) ? $product->categories->title : "N/A" }}</p>
            @endif  
            <p class="card-text">Ref. Color : @if (is_array($product->reference_color)) {{ implode(" ", $product->reference_color) }} @else {{ $product->reference_color }} @endif</p>
            @if($isAdmin)
              <p class="card-text">Color : {{ html()->select("color", $sampleColors, $product->color)->class("form-control update-color")->id("id_" . $product->id)->data('id', $product->id) }}</p>
            @else 
              <p class="card-text">Color : {{ $product->color }}</p>
            @endif
            @if(auth()->user()->isAdmin())
              <p class="card-text">Supplier : {{ $product->supplier }}</p>
              <p class="card-text">Suppliers : {{ $product->supplier_list }}</p>
            @endif
              <p class="card-text">
                <input type="checkbox" class="select-product-edit" name="product_id" data-id="{{ $product->id }}">
                <a href="{{ route('product-inventory.fetch.img.google',['name'=> $product->name, 'id' => $product->id]) }}" class="btn btn-secondary btn-sm"><i class="fa fa-picture-o"></i></a>

                <i class="fa fa-check-square-o add_purchase_product" title="Purchase Product" style="cursor: pointer;" data-id="{{ $product->id }}" aria-hidden="true"></i>
              </p>
            
          </div>
        </div>
      <?php //if($rowCount % 4 == 0) { echo '</div>'; } ?>
    <?php } ?>
    </div>
<?php } ?>
<script>
  function copySKU(skuId) {
      // Get the SKU value
      var skuValueElement = document.getElementById(skuId);

    if (skuValueElement) {
        var skuValue = skuValueElement.innerText;

        // Create a temporary input element
        var inputElement = document.createElement("input");
        inputElement.value = skuValue;
        document.body.appendChild(inputElement);

        // Select the text in the input
        inputElement.select();

        // Copy the text to the clipboard
        document.execCommand("copy");

        // Remove the input element
        document.body.removeChild(inputElement);
    }

  }
</script>