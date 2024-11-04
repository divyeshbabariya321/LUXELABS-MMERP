<form action="/shipment/<?= $wayBill->id; ?>/save" method="POST" enctype="multipart/form-data">
  @csrf
  <div class="modal-body">
      <div class="col-md-6">
          <div class="form-group" id="awb_no">
             <strong>AWB:</strong>
             <input type="text" name="awb" id="awb" class="form-control awb" value="<?= $wayBill->awb; ?>" required="">
             <span class="form-error"></span>
          </div>
          <div class="form-group" id="order_id">
               <strong>Order Id:</strong>
               {{ html()->select("order_id", \App\Order::pluck('order_id', 'id')->toArray(), $wayBill->order_id)->class("form-control select2") }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="from_city">
               <strong>From City:</strong>
               {{ html()->text("from_city", $wayBill->from_city)->class("form-control") }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="from_country_code">
               <strong>From Country code:</strong>
               {{ html()->text("from_country_code", $wayBill->from_country_code)->class("form-control") }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="from_customer_phone">
               <strong>From Customer Phone:</strong>
               {{ html()->text("from_customer_phone", $wayBill->from_customer_phone)->class("form-control") }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="from_customer_address_1">
               <strong>From Customer Address 1:</strong>
               {{ html()->textarea("from_customer_address_1", $wayBill->from_customer_address_1)->class("form-control")->rows(2) }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="from_customer_address_2">
               <strong>From Customer Address 2:</strong>
               {{ html()->textarea("from_customer_address_2", $wayBill->from_customer_address_2)->class("form-control")->rows(2) }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="from_customer_pincode">
               <strong>From Customer pincode:</strong>
               {{ html()->text("from_customer_pincode", $wayBill->from_customer_pincode)->class("form-control") }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="from_company_name">
               <strong>From Customer pincode:</strong>
               {{ html()->text("from_company_name", $wayBill->from_company_name)->class("form-control") }}
               <span class="form-error"></span>
           </div>

      </div>
      <div class="col-md-6">
          <div class="form-group" id="to_customer_name">
               <strong>To Customer name:</strong>
               {{ html()->text("to_customer_name", $wayBill->to_customer_name)->class("form-control") }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="to_city">
               <strong>To City:</strong>
               {{ html()->text("to_city", $wayBill->to_city)->class("form-control") }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="to_country_code">
               <strong>To Country code:</strong>
               {{ html()->text("to_country_code", $wayBill->to_country_code)->class("form-control") }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="to_customer_phone">
               <strong>To Customer Phone:</strong>
               {{ html()->text("to_customer_phone", $wayBill->to_customer_phone)->class("form-control") }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="to_customer_address_1">
               <strong>To Customer Address 1:</strong>
               {{ html()->textarea("to_customer_address_1", $wayBill->to_customer_address_1)->class("form-control")->rows(2) }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="to_customer_address_2">
               <strong>To Customer Address 2:</strong>
               {{ html()->textarea("to_customer_address_2", $wayBill->to_customer_address_2)->class("form-control")->rows(2) }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="to_customer_pincode">
               <strong>To Customer pincode:</strong>
               {{ html()->text("to_customer_pincode", $wayBill->to_customer_pincode)->class("form-control") }}
               <span class="form-error"></span>
           </div>
           <div class="form-group" id="to_company_name">
               <strong>To company name:</strong>
               {{ html()->text("to_company_name", $wayBill->to_company_name)->class("form-control") }}
               <span class="form-error"></span>
           </div>
      </div>
      <div class="col-md-12">
          <div class="col-md-6">
            <div class="form-group" id="box_length">
                 <strong>Box length:</strong>
                 {{ html()->text("box_length", $wayBill->box_length)->class("form-control") }}
                 <span class="form-error"></span>
             </div>
             <div class="form-group" id="box_width">
                 <strong>Box width:</strong>
                 {{ html()->text("box_width", $wayBill->box_width)->class("form-control") }}
                 <span class="form-error"></span>
             </div>
             <div class="form-group" id="box_height">
                 <strong>Box height:</strong>
                 {{ html()->text("box_height", $wayBill->box_height)->class("form-control") }}
                 <span class="form-error"></span>
             </div>
             <div class="form-group" id="pickup_date">
                 <strong>Pickup date:</strong>
                 {{ html()->text("pickup_date", $wayBill->pickup_date)->class("form-control") }}
                 <span class="form-error"></span>
             </div>
        </div>
        <div class="col-md-6">
            <div class="form-group" id="actual_weight">
                 <strong>Actual weight:</strong>
                 {{ html()->text("actual_weight", $wayBill->actual_weight)->class("form-control") }}
                 <span class="form-error"></span>
             </div>
             <div class="form-group" id="volume_weight">
                 <strong>Volume weight:</strong>
                 {{ html()->text("volume_weight", $wayBill->volume_weight)->class("form-control") }}
                 <span class="form-error"></span>
             </div>
             <div class="form-group" id="cost_of_shipment">
                 <strong>Cost of shipment:</strong>
                 {{ html()->text("cost_of_shipment", $wayBill->cost_of_shipment)->class("form-control") }}
                 <span class="form-error"></span>
             </div>
             <div class="form-group" id="duty_cost">
                 <strong>Duty Cost:</strong>
                 {{ html()->text("duty_cost", $wayBill->duty_cost)->class("form-control") }}
                 <span class="form-error"></span>
             </div>
        </div>
      </div>
  </div>
</form>
