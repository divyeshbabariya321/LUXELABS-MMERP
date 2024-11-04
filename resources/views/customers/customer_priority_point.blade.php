@extends('layouts.app')
 
@section('styles')
<style>
div#credit_logs .modal-dialog table { table-layout: fixed; }
div#credit_histories .modal-dialog table { table-layout: fixed; }
div#credit_logs .modal-dialog table tr >* { word-break: break-all; }
div#credit_histories .modal-dialog table tr >* { word-break: break-all; }

</style>

 <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection
 
@section('content')
 
   
   @include('partials.flash_messages')

   <div class="table-responsive mt-3">
    <h2 class="page-heading">Customer Priority Point</h2>
    <div class="pull-right top-head-btn mr-2" style="flex-grow: 1; text-align: right">
      <a class="btn btn-xs btn-secondary" href="#" id="add_customer_priority_btn" data-bs-toggle="modal" data-bs-target="#add_customer_priority_point">+ Add Priority</a>
    </div>
    <br/><br/><br/>
    @if (!empty($custPriority))
    <div class="table-responsive" style="border-top-left-radius:0">
      <table class="table table-bordered m-0">
        <thead>
          <tr>
            <th>Website</th>
            <th>Base Points</th>
            <th>Lead Points</th>
            <th>Order Points</th>
            <th>Refund Points</th>
            <th>Ticket Points</th>
            <th>Return Points</th>
            <th>Date</th>
          </tr>
        </thead>
  
        <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
          @foreach ($custPriority as $c) 
            <tr>
              <td>{{ $c->website}}</td>
              <td>{{ $c->website_base_priority }}</td>
              <td>{{ $c->lead_points }}</td>
              <td>{{ $c->order_points }}</td>
              <td>{{ $c->refund_points }}</td>
              <td>{{ $c->ticket_points }} </td>
              <td>{{ $c->return_points }}</td>
              <td>@if($c->created_at != null) {{ date("d-m-Y",strtotime($c->created_at)) }} @endif</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
     @endif
   </div>
 
  <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />
  <div id="add_customer_priority_point" class="modal fade" role="dialog" style="display: none;"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Create Points</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
                <form action="" method="post" id="add_priority_point_form">
                    <div class="col-md-12" id="">
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>Store Website</label>
                                  <select id="store_website_id" name="store_website_id"  class="selectpicker form-control" data-live-search="true" data-size="15">
                                    <option value="">---</option>
                                    @foreach ($storeWebsite as $storeWebsiteData)
                                      <option value="{{ $storeWebsiteData->id}}" >{{ $storeWebsiteData->website}}</option>    
                                    @endforeach
                                </select>
                              </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                                <label>Base Points</label>
                                <input type="number" id="website_base_priority" name="website_base_priority" class="form-control form-control-sm" step="0.1" value="1" max="10"/>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                                <label>Order Points</label>
                                <input type="number" id="order_points" name="order_points" class="form-control form-control-sm" step="0.1" value="1" max="10"/>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                                <label>Lead Points</label>
                                <input type="number" id="lead_points" name="lead_points" class="form-control form-control-sm" step="0.1" value="1" max="10"/>
                            </div>
                          </div>
                          <div class="col-md-12">
                           <div class="form-group">
                                <label>Refund Points</label>
                                <input type="number" id="refund_points" name="refund_points" class="form-control form-control-sm" step="0.1" value="1" max="10"/>
                            </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                              <label>Ticket Points</label>
                              <input type="number" id="ticket_points" name="ticket_points" class="form-control form-control-sm" step="0.1" value="1" max="10"/>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <label>Return Points</label>
                              <input type="number" id="return_points" name="return_points" class="form-control form-control-sm" step="0.1" value="1" max="10"/>
                          </div>
                      </div>
                      <div class="my-4 col-md-12" style="text-align: right;">
                        <button class="btn btn-primary add_customer_address-btn">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
 
@section('scripts')
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
 <script>
        $('#add_customer_priority_btn').on('click', function(e) {
          e.preventDefault();
          $('#add_customer_priority_point').modal('show');
        });
        
        $(document).on("change","#store_website_id",function(e) {
          e.preventDefault();
          let store_website_id = $("#store_website_id").val();
          $.ajax({
               url: "{{route('customer.get.priority.points')}}/"+store_website_id,
               type: 'GET',
               headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
               data: {
                "id" : store_website_id,
               },
               beforeSend : function() {
                    $('#loading-image').show();
               },
               success: function (data) {
                   $('#loading-image').hide();
                   if(data.code == 200)  {
                    var res = data.data.custPriority[0];
                      if(res) {
                        $("#website_base_priority").val(res.website_base_priority);
                        $("#lead_points").val(res.lead_points);
                        $("#order_points").val(res.order_points);
                        $("#refund_points").val(res.refund_points);
                        $("#ticket_points").val(res.ticket_points);
                        $("#return_points").val(res.return_points);
                      }
                   }else {
                      toastr["error"](data.message, "Message")
                   }
               },
               error: function () {
                   //$('#loading-image').hide();
                   toastr["error"]("Oops something went wrong", "Message")
               }
            });
        });

        $('.select-2').select2({width:'99%'});
        $(document).on("submit","#add_priority_point_form",function(e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
               url: "{{route('customer.add.priority.points')}}",
               type: 'GET',
               headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
               data: $('#add_priority_point_form').serialize(),
               beforeSend : function() {
                    $('#loading-image').show();
               },
               success: function (data) {
                   $('#loading-image').hide();
                   if(data.code == 200)  {
                      toastr["success"](data.message, "Message")
                      $('#add_customer_priority_point').modal('hide');
                      location.reload(true);
                   }else {
                     toastr["error"](data.message, "Message")
                   }
               },
               error: function () {
                   $('#loading-image').hide();
                   toastr["error"]("Oops something went wrong", "Message")
               }
            });
        });
 </script>  
 @endsection
 
 
