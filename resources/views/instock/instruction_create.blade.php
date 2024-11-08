<div class="col-md-12">
    {{ html()->hidden("product_id", $productId)->class("instruction-pr-id") }}
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
            <label>Instruction Type:</label>
            {{ html()->select("instruction_type", ["location" => "Change Location", "dispatch" => "Product Dispatch"])->class("form-control instruction-type-select") }}
        </div>
      </div>
      <div class="col-md-12">
         <div class="form-group">
            <label>Instruction Message:</label>
            {{ html()->textarea("instruction_message")->class("form-control quick-message-field")->rows(2) }}
        </div>
      </div> 
    </div>
    <div class="row quick-category-sec">
      <div class="col-md-6">
        <div class="d-inline form-inline">
            <input style="width: 80%" type="text" name="category_name" placeholder="Enter New Category" class="form-control mb-3 quick_category">
            <a class="btn btn-secondary quick_category_add">+</a>
        </div>
        <div class="d-inline form-inline">
            <select style="width: 80% !important;" name="quickCategory" class="form-control mb-3 quickCategory">
                <option value="">Select Category</option>
                @foreach($reply_categories as $category)
                    <option value="{{ $category->product_dispatch }}" data-id="{{$category->id}}">{{ $category->name }}</option>
                @endforeach
            </select>
            <a class="btn btn-image delete_category"><img src="/images/delete.png"></a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="d-inline form-inline">
            <input style="width: 80%" type="text" name="quick_comment" placeholder="Enter New Quick Comment" class="form-control mb-3 quick_comment">
            <a class="btn btn-secondary quick_comment_add">+</a>
        </div>
        <div class="d-inline form-inline">
            <select style="width: 80% !important;" name="quickComment" class="form-control quickComment">
                <option value="">Quick Reply</option>
            </select>
                <a class="btn btn-image delete_quick_comment"><img src="/images/delete.png"></a>
        </div>
      </div> 
    </div>  
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label>Assign To:</label>
          {{ html()->select("assign_to", $users)->class("form-control") }}
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group dispatch-instruction dis-none">
          <label>Dispatch To:</label>
          {{ html()->select("order_id", $order)->class("form-control") }}
        </div>
      </div>  
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group dispatch-instruction dis-none">
          <label>Dispatch Direct to Customer:</label>
          {{ html()->select("customer_id", [])->class("form-control customer-search-box")->style("width:100%;") }}
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          <label>Pending Amount:</label>
          {{ html()->text("pending_amount")->class("form-control") }}
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>New Location <a target="_blank" href="<?php echo url("product-location") ?>">Create new?</a></label>
          {{ html()->select("location_name", $locations)->class("form-control") }}
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>Courier Name <a target="_blank" href="<?php echo url("courier") ?>">Create new?</a></label>
          {{ html()->select("courier_name", $couriers)->class("form-control") }}
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label>Courier Details</label>
          {{ html()->textarea("courier_details")->class("form-control")->rows(2) }}
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label>Date</label>
          {{ html()->text("date_time")->class("form-control date-time-picker") }}
        </div>
      </div>
    </div>
</div>