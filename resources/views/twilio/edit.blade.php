<form method="POST" id="lead_create_brands" data-url="<?php echo route("twilio.chats.update"); ?>" >
  
  <?php echo csrf_field(); ?>
  
  <div class="form-group">
    <label for="brand_ids">Phone:</label>
    <input type="text" name="number" value="{{$data->number}}" class="form-control" >
    <input type="hidden" name="id" value="{{$data->id}}" >
  </div>

  <div class="form-group">
    <label for="brand_ids">Send By:</label>
    <input type="text" name="send_by" value="{{$data->send_by}}" class="form-control" >
  </div>

  <div class="form-group">
    <label for="brand_ids">Message:</label>
    <textarea name="message" id="message" class="form-control" >{{$data->message}}</textarea>
  </div>
  
  <button type="submit" class="btn btn-default lead-button-submit-for-category-brand">Submit</button>
</form>