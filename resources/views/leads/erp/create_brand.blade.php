<form method="POST" id="lead_create_brands" data-url="<?php echo route("save.leads.brands"); ?>" action="<?php echo route("save.leads.brands"); ?>">
  
  <?php echo csrf_field(); ?>
  
  <div class="form-group">
    <label for="brand_ids">Brand:</label>
    <select placeholder="Brand" class="form-control multi_lead_status_brands input-size" id="brand_ids" name="brand_ids[]" multiple="" style="width: 150px; border-radius: 2px;">
        <option disabled="true" >Select Brand</option>
        @foreach($brands as $brand_item)
          <option value="{{$brand_item['id']}}" @if(in_array($brand_item['id'], $brand_ids) ) selected="true" @endif  >{{$brand_item['name']}}</option>
        @endforeach
    </select>
  </div>
  
  <button type="submit" class="btn btn-default lead-button-submit-for-category-brand">Submit</button>
</form>