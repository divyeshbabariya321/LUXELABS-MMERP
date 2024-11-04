@extends('layouts.app')
@section('large_content')

@section('link-css')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style type="text/css">
        .chat-righbox a{
            color: #555 !important;
            font-size: 18px;
        }
        .type_msg.message_textarea {
            width: 90%;
            height: 60px;
        }
        .cls_remove_rightpadding{
            padding-right: 0px !important;
        }
        .cls_remove_leftpadding{
            padding-left: 0px !important;
        }
        .cls_remove_padding{
            padding-right: 0px !important;
            padding-left: 0px !important;
        }
        .cls_quick_commentadd_box{
            padding-left: 5px !important;   
            margin-top: 3px;
        }
        .cls_quick_commentadd_box button{
            font-size: 12px;
            padding: 5px 9px;
            margin-left: -8px;
            background: none;
        }
        .send_btn {
            margin-left: -5px; 
        }
        .cls_message_textarea{
            height: 35px !important;
            width: 100% !important;
        }
        .cls_quick_reply_box{
            margin-top: 5px;
        }
        .cls_addition_info {
            padding: 0px 0px;
            margin-top: -8px;
        }
        .table-responsive{
            margin-left: 10px;
            margin-right: 10px;
        }
        .chat-righbox{
            border: none;
            background: transparent;
            padding: 0;
        }
        .typing-indicator{
            height: auto;
            padding: 0;
        }
        textarea{
            border: 1px solid #ddd !important;
        }
        .send_btn{
            background-color: transparent !important;

        }
        .send_btn i{
            color: #808080;
        }
    </style>
@endsection

        <div class="row">
            <div class="col-lg-12 margin-tb p-0">
                <h2 class="page-heading">Twilio Chat</h2>
                @if ( Session::has('message') )
                  <p class="alert {{ Session::get('flash_type') }}">{{ Session::get('message') }}</p>
                @endif
                <div class="pull-right">
                    <div style="text-align: right; margin-bottom: 10px;">
                        <!-- <button type="button" class="btn btn-primary" onclick="createCoupon()">New Coupon</button> -->
                        <span>&nbsp;</span>                        
                    </div>
                </div>


                <div class="pull-left cls_filter_box">
                    <form class="form-inline" action="{{ route('twilio.get.chats') }}" method="GET">
                        <div class="form-group ml-3 cls_filter_inputbox">
                            <label for="leads_email">Phone Number</label>
                            <input type="number" class="form-control" name="number" id="leads_email" value="{{request()->get('number')}}">
                        </div>
                        <div class="form-group ml-3 cls_filter_inputbox">
                            <label for="leads_source">Send By</label>
                            <input type="number" class="form-control" name="send_by" id="leads_source" value="{{request()->get('send_by')}}">
                        </div>
                        <div class="form-group ml-3 cls_filter_inputbox">
                            <label for="leads_source">Message</label>
                            <input type="input" class="form-control" name="message" id="leads_source" value="{{request()->get('message')}}">
                        </div>
                        <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image">Search</button>
                    </form>
                </div>


            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="keywordassign_table">
                    <thead>
                        <tr>
                            <th style="width: 2%;">Sr. No.</th>
                            <th style="width: 2%;">Sender Number</th>
                            <th style="width: 2%;">Receiver Number</th>
                            <th style="width: 2%;">Customer Name</th>
                            <th style="width: 2%;">Customer Email</th>
                            <th style="width: 15%;">Message</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $srno = 1;
                        ?>

                         @if(isset($chat_message) && !empty($chat_message))
                            @foreach ($chat_message as $chatId)

                               
                                   <tr>
                                    <td><?php echo $srno; ?></td>
                                    <td><?php echo $chatId->send_by; ?></td>
                                    <td><?php echo $chatId->number; ?></td>
                                    <td><?php echo $chatId->customer_name; ?></td>
                                    <td><?php echo $chatId->customer_email; ?></td>
                                    <td><?php echo $chatId->message; ?></td>
                                    <td>
                                        <a href="{{route('twilio.chats.delete',$chatId->id)}}"><i class="fa fa-trash"></i></a>
                                        <a class="edit_tilio_list" data-id="{{$chatId->id}}" href="javascript:void(0)"><i class="fa fa-edit"></i></a>
                                    </td>
                                    
                                   </tr>

                                <?php $srno++; ?>
                            @endforeach
                        @endif   
                    </tbody>
                </table>
                {{ $chat_message->links() }}
            </div>
        </div>
    
        


<div id="erp_leads_manage_category_brand" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="set_heading" >Twilio Message</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body erp-leads-modal" id="erp_leads_manage_category_brand_form">

      </div>
    </div>
  </div>
</div>


@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <!-- New Coupon -->

    <script>




            $('.edit_tilio_list').on("click", function () {
                var id = $(this).attr("data-id");
                
                $.ajax({
                    type: 'GET',
                    url: "{{ route('twilio.chats.edit') }}",
                    // dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id' : id,
                    }
                }).done(function (data) {
                        console.log(data);
                       $("#erp_leads_manage_category_brand_form").html(data);
                       $("#erp_leads_manage_category_brand").modal("show");
                }).fail(function (response) {
                    console.log(response);
                    alert('fail');
                });
            });



$(document).on('click', '.lead-button-submit-for-category-brand', function (e) {
      e.preventDefault();
      var $this = $(this);
      var url = $('#lead_create_brands').attr('action');
      var formData = new FormData(document.getElementById("lead_create_brands"));

      $.ajax({
            type: "POST",
            data : formData,
            url: "{{route('twilio.chats.update')}}",
            contentType: false,
            processData: false
        }).done(function (data) {
          console.log(data);
           if(data.code == 200) {
               $("#erp_leads_manage_category_brand").find(".modal-body").html("");
               $("#erp_leads_manage_category_brand").modal("hide");
               location.reload(true);
           }else{
              alert(data.message);
           }
        }).fail(function (response) {
            console.log(response);
        });
    });







    </script>
@endsection