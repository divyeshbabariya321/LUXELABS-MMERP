<div class="col-md-12">
    {{ html()->hidden("product_id", $productId)->class("instruction-pr-id") }}
    <div class="form-group">
        <label>Mode of Shipment:</label>
        {{ html()->text("modeof_shipment")->class("form-control instruction-type-select") }}
    </div>
     <div class="form-group">
        <label>Delivery Person:</label>
        {{ html()->text("delivery_person")->class("form-control") }}
    </div>
     <div class="form-group">
        <label>AWB:</label>
        {{ html()->text("awb")->class("form-control") }}
    </div>
    <div class="form-group">
      <label>ETA:</label>
      {{ html()->text("eta")->class("form-control") }}
    </div>
    <!-- <div class="form-group">
      <label>Date</label>
      {{ html()->text("date_time")->class("form-control date-time-picker") }}
    </div> -->
    <?php for ($i=0; $i < 5; $i++) { ?> 
      <div class="form-group">
        <label><?php echo "Image : ". ($i+1); ?></label>
        {{ html()->file("file[]", ["class" => "form-control"])->attributes(null) }}
      </div>
    <?php } ?>
</div>