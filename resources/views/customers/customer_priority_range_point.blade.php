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
    <h2 class="page-heading">Customer Priority Range Point</h2>
    <div class="pull-right top-head-btn mr-2" style="flex-grow: 1; text-align: right">
      <a class="btn btn-xs btn-secondary" href="#" id="add_customer_priority_btn" data-bs-toggle="modal" data-bs-target="#add_customer_priority_point">+ Add Priority Range</a>
    </div>
    <br/><br/><br/>
    @if (!empty($custRangePoint))
    <div class="table-responsive" style="border-top-left-radius:0">
      <table class="table table-bordered m-0">
        <thead>
          <tr>
            <th>Website</th>
            <th>Level Name</th>
            <th>Min Point</th>
            <th>Max Point</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
  
        <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
          @foreach ($custRangePoint as $c) 
            <tr>
              <td>{{ $c->website}}</td>
              <td>{{ $c->priority_name }}</td>
              <td>{{ $c->min_point }}</td>
              <td>{{ $c->max_point }}</td>
              <td>@if($c->created_at != null) {{ date("d-m-Y",strtotime($c->created_at)) }} @endif</td>
              <td>
                <a class="btn btn-xs " href="#" id="edit" data-id="{{$c->id}}" data-bs-toggle="modal" data-bs-target="#add_customer_priority_point"><i class="fa fa-edit" aria-hidden="true"></i></a>
                <a href="{{route('customer.delete.priority.range.points')}}/{{$c->id}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
              </td>
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
                                  <select id="store_website_id" name="store_website_id"  class="form-control" data-live-search="true" data-size="15">
                                    <option value="">---</option>
                                    @foreach ($storeWebsite as $storeWebsiteData)
                                      <option value="{{ $storeWebsiteData->id}}" >{{ $storeWebsiteData->website}}</option>    
                                    @endforeach
                                </select>
                              </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                                <label>Min Points</label>
                                <input type="number" id="min_point" name="min_point" class="form-control form-control-sm" step="0.1" value="1" max="10"/>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                                <label>Max Points</label>
                                <input type="number" id="max_point" name="max_point" class="form-control form-control-sm" step="0.1" value="1" max="10"/>
                            </div>
                          </div>

                          <div class="col-md-12">
                            <div class="form-group">
                                <label>Range Level</label>
                                <select id="twilio_priority_id" name="twilio_priority_id"  class="form-control" data-size="15">
                                  
                              </select>
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
               url: "{{route('customer.all.select.priority.range.points')}}/"+store_website_id,
               type: 'GET',
               headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
               
               beforeSend : function() {
                    $('#loading-image').show();
               },
               success: function (data) {
                   $('#loading-image').hide();
                   if(data.code == 200)  {
                      if (data.data.length > 0) {
                          var html = "";
                          $.each(data.data, function (k, twi) {
                              $('#twilio_priority_id')
                              .append($("<option></option>")
                                .attr("value", twi.id)
                                .text(twi.priority_name)); 
                          });
                      } else {
                        $('#twilio_priority_id').empty();
                      }
                   }else {
                      toastr["error"](data.message, "Message")
                   }
               },
               error: function () {
                   toastr["error"]("Oops something went wrong", "Message")
               }
            });
        });

        $(document).on("click","#edit",function(e) {
          $('#add_customer_priority_point').modal('show');
          e.preventDefault();
          var $this = $(this);
          var id = $this.data("id");
          $.ajax({
               url: "{{route('customer.get.select.priority.range.points')}}/"+id,
               type: 'GET',
               headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
               
               beforeSend : function() {
                    $('#loading-image').show();
               },
               success: function (data) {
                   $('#loading-image').hide();
                   if(data.code == 200)  {
                    //var obj = $.parseJSON( data );
                      if (Object.keys(data.data.custRangePoint).length > 0) {
                        
                        var dataVal = data.data;
                        $("#store_website_id").val(dataVal.custRangePoint.store_website_id);
                        $('#min_point').val(dataVal.custRangePoint.min_point);
                        $('#max_point').val(dataVal.custRangePoint.max_point);
                        $.each(dataVal.twilioPriority, function (k, twi) {
                              $('#twilio_priority_id')
                              .append($("<option></option>")
                                .attr("value", twi.id)
                                .text(twi.priority_name)); 
                          });
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
               url: "{{route('customer.add.priority.range.points')}}",
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
 
 
