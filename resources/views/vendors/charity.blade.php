@extends('layouts.app')
@section('favicon' , 'vendor.png')
@section('title', 'Charity Info')
@section('styles')
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style type="text/css">
    .numberSend {
          width: 160px;
          background-color: transparent;
          color: transparent;
          text-align: center;
          border-radius: 6px;
          position: absolute;
          z-index: 1;
          left: 19%;
          margin-left: -80px;
          display: none;
    }
 .input-sm{
    width: 60px;
    }
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    .cls_filter_inputbox{
        width: 12%;
        text-align: center;
    }
    .message-chat-txt {
        color: #333 !important;
    }
    .cls_remove_leftpadding{
        padding-left: 0px !important;
    }
    .cls_remove_rightpadding{
        padding-right: 0px !important;
    }
    .cls_action_btn .btn{
        padding: 6px 12px;
    } 
    .cls_remove_allpadding{
        padding-left: 0px !important;
        padding-right: 0px !important;   
    }
    .cls_quick_message{
        width: 100% !important;
        height: 35px !important;
    }
    .cls_filter_box{
        width: 100%;
    }
    .select2-selection.select2-selection--single{
        height: 35px;
    }
    .cls_action_btn .btn-image img {
        width: 13px !important;
    }
    .cls_action_btn .btn {
        padding: 6px 2px;
    }
    .cls_textarea_subbox{
        width: 100%;
    }
    .btn.btn-image.delete_quick_comment {
        padding: 4px;
    }
    .vendor-update-status-icon {
        padding: 0px;
    }
    .cls_commu_his{
        width: 100% !important;
    }
    .vendor-update-status-icon{
        margin-top: -7px;
    }
    .clsphonebox .btn.btn-image{
        padding: 5px;
    }
    .clsphonebox {
        margin-top: -8px;
    }
    .send-message1{
        padding: 0px 10px;
    }
    .load-communication-modal{
        margin-top: -6px;
        margin-left: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 35px;
    }
    .select2-selection__arrow{
        display: none;
    }
    .cls_mesg_box{
        margin-top: -7px;
        font-size: 12px;
    }
    .td-full-container{
        color: #333;
    }
  </style>
@endsection
@section('large_content')
    <div id="myDiv">
       <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <?php $base_url = URL::to('/'); ?>
            <h2 class="page-heading">Charity Info ({{ $totalVendor }})</h2>
            <div class="pull-left cls_filter_box charity-custom">
                <form class="form-inline" action="{{ route('customer.charity') }}" method="GET">
                    <div class="form-group ml-3 cls_filter_inputbox"style="width:175px;">
                        <select name="term" type="text" class="form-control" placeholder="Search" id="charity-search" data-allow-clear="true">
                            <?php
                                if (request()->get('term')) {
                                    echo '<option value="'.request()->get('term').'" selected>'.request()->get('term').'</option>';
                                }
            ?>
                        </select>
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox"style="width:175px;">
                        <select name="email" type="text" class="form-control" placeholder="Search" id="charity-email" data-allow-clear="true">
                            <?php
                if (request()->get('email')) {
                    echo '<option value="'.request()->get('email').'" selected>'.request()->get('email').'</option>';
                }
            ?>
                        </select>
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox"style="width:175px;">
                        <select name="phone" type="text" class="form-control" placeholder="Search" id="charity-phone-number" data-allow-clear="true">
                            <?php
                if (request()->get('phone')) {
                    echo '<option value="'.request()->get('phone').'" selected>'.request()->get('phone').'</option>';
                }
            ?>
                        </select>
                    </div> 
                    <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px; width:175px;">
                       <input placeholder="Communication History" type="text" name="communication_history" value="{{request()->get('communication_history')}}" class="form-control-sm cls_commu_his form-control">
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox"style="width:175px;">
                        {{ html()->select("status", ["" => "- Select -", "0" => "De-Active", "1" => "Active"], request('status'))->class("form-control") }}
                    </div> 
                    <button type="submit" style="padding: 5px;" class="btn btn-image"><img src="<?php echo $base_url; ?>/images/filter.png"/></button>
                </form>
                  <div class="pull-right"> 
                      <button type="button" class="btn btn-secondary create-charity" data-toggle="modal" data-target="#charityCreateModal">Create Charity</button>
                 </div>
            </div>
        </div>
          
    </div>
    @include('partials.flash_messages') 
    <div class="infinite-scroll charity-custom-table">
    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="vendor-table">
            <thead>
            <tr>
                <th width="3%"><a href="/products/customer/charity{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'ASC') ? '&orderby=DESC' : '' }}">ID</a></th>
                <th width="10%">Product</th>
                <th width="10%">Phone</th>
                <th width="15%">Email</th>
                <th width="8%">Store Website</th>            
               
                <th width="25%">Communication</th>
                <th width="10%">Action</th>
            </tr>
            </thead>
            <tbody id="vendor-body">
            @include('vendors.partials.charity-data')
                     </tbody>
        </table>
    </div>
    {!! $vendors->appends(Request::except('page'))->links() !!}
    </div>
    @include('partials.modals.remarks')
    @include('vendors.partials.modal-emailToAll')
    @include('common.commonEmailModal')
    @include('vendors.partials.charity-modals')
    @include('vendors.partials.add-vendor-info-modal')    
    @include('vendors.partials.vendor-category-modals')
    @include('vendors.partials.modal-conference')
    @include('vendors.partials.change-hubstaff-role')
    <div id="reminderModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Set/Edit Reminder</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="frequency">Frequency</label>
                        {{ html()->select("frequency", drop_down_frequency())->class("form-control")->id("frequency") }}
                    </div>
                    <div class="form-group">
                        <label for="frequency">Reminder Start From</label>
                        <input type="text" name="reminder_from" id="reminder_from" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="reminder_message">Check Last Message?</label>
                        <label class="radio-inline">
                          <input type="radio" id="reminder_last_reply" name="reminder_last_reply" value="1" checked>Yes
                        </label>
                        <label class="radio-inline">
                          <input type="radio" id="reminder_last_reply_no" name="reminder_last_reply" value="0">No
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="reminder_message">Reminder Message</label>
                        <textarea name="reminder_message" id="reminder_message" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-secondary save-reminder">Save</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                    <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                    <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                    <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="email-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Email Communication</h4>
                    <input type="text" name="search_email_pop"  class="form-control search_email_pop" placeholder="Search Email" style="width: 200px;">
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="charity-country" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Charity Countries</h4>
                </div>
                <div class="row m-3">
                    <div class="col-md-6">
                        <input type="text" name="search_by_country" placeholder="search by country"  class="form-control"  style="width: 200px;">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="global_price" placeholder="global price"  class="form-control" placeholder="Search Email" style="width: 200px;">
                    </div>
                </div>
                <form>
                    <div class="modal-body" style="background-color: #999999;">
                    </div>
                </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-secondary charity-country-submit">Submit</button>
                    </div>
                </div>
            </div>
    </div>
    @include('customers.zoomMeeting')
    <div id="forwardModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('whatsapp.forward') }}" method="POST">
            @csrf
            <input type="hidden" name="message_id" id="forward_message_id" value="">
            <div class="modal-header">
              <h4 class="modal-title">Forward Message</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                  <strong>Client:</strong>
                  <select class="selectpicker form-control" name="customer_id[]" title="Choose a Customer" required multiple></select>
                  @if ($errors->has('customer_id'))
                      <div class="alert alert-danger">{{$errors->first('customer_id')}}</div>
                  @endif
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Forward Message</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal fade" id="createUser" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
               
                    <div class="modal-body">
                        <div><button class="btn btn-secondary m-1" id="vendor_id">Create ERP User from Vendor</button></div>
                        <div><button class="btn btn-secondary m-1" onclick="inviteGithub()">Invite to Github</button></div>
                        <div><button class="btn btn-secondary m-1" onclick="inviteHubstaff()">Invite to Hubstaff</button></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createWebsite" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
               
                    <div class="modal-body">
                <form id="create_website" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="model_charity_id"  id="model_charity_id">
                            <strong>Website</strong>
                                            <select name="website_id"  id="website_id" class="form-control" required>
                            @foreach($website1 as $w)
                            <option value="{{$w->id}}">{{$w->name}}</option>
                            @endforeach
                            </select>  
                            
                        </div>

                        <div class="form-group">
                        <strong>Price</strong>
                            <input type="text" class="form-control" name="website_price" id="website_price">            
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                       
                       
                    </div>
                </form>
                       <div id="dev_website"> </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" onclick="savewebsite();" class="btn btn-secondary">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>



    <div id="create_broadcast" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Send Message to Vendors</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="send_message" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Message</strong>
                            <textarea name="message" id="message_to_all_field" rows="8" cols="80" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="pushmagento" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Push To Magento</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="send_message" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Select Store Website</strong>
                            <input type="hidden" value="" name="vender_id" id="pushmagento_vender_id">
                            <select name="storewebsite" id="pushmagento_storewebsite">
                              @foreach ($storewebsite as $s)
                                <option value="{{$s->id}}">{{$s->title}}</option>
                              @endforeach   
                           </select>
                                           </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="upload-single" class="btn btn-secondary">Push</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/zoom-meetings.js') }} "></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    <script src="{{asset('js/common-email-send.js')}}">//js for common mail</script> 
    <script type="text/javascript">



        $(document).on('submit', '.create-charity', function () {
            $('select[name="website_stores[]"]').html('');
            $('select[name="website_stores[]"]').select2();
            $('select[name="websites[]"]').html('');
            $('select[name="websites[]"]').select2();
        });

        $(document).on('click', '.send-email-common-btn', function () {
            $('#commonEmailModal .getTemplateData').parent().remove();
        });

        $(document).on('submit', '#commonEmailModal', function () {
            if($('#commonEmailModal input[name="subject"]').val() == ''){
                toastr['error']('please add subject.', 'error');
                return false;
            }
            if($('#commonEmailModal textarea[name="message"]').val() == ''){
                toastr['error']('please add subject.', 'error');
                return false;
            }
        });

        $(document).on('click', '#upload-single', function () {
              var ajaxes = [];
            
            var id = $('#pushmagento_vender_id').val();
            var storewebsite = $('#pushmagento_storewebsite').val();
            $(this).addClass('fa-spinner').removeClass('fa-upload')
            url = "{{ url('products') }}/" + id + '/listMagento';
            ajaxes.push($.ajax({
                type: 'GET',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                    storewebsite:storewebsite,
                },
                beforeSend: function () {
                    // $(thiss).text('Loading...');
                    // $(thiss).html('<i class="fa fa-spinner" aria-hidden="true"></i>');
                }
            }).done(function (response) {
                $('#pushmagento').modal("hide");
                //$(this).removeClass('fa-spinner').addClass('fa-upload');
                toastr['success']('Charity pushed to magento sites.', 'Success')
                $('#product' + id).hide();
            }));

            // }
            $.when.apply($, ajaxes)
                .done(function () {
                    //location.reload();
                });
        });

        $('.selectpicker').select2({
            tags: true,
            width : '100%',
            ajax: {
                url: BASE_URL+'/erp-leads/customer-search',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search for Customer by id, Name, No',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {
                if (customer.loading) {
                    return customer.name;
                }
                if (customer.name) {
                    return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.name,
        });
        var vendorToRemind = null;
        $('#charity-search').select2({
            tags: true,
            width : '100%',
            ajax: {
                url: BASE_URL+'/customer-charity-search',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    for (var i in data) {
                        data[i].id = data[i].name ? data[i].name : data[i].text;
                    }
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search by name',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {
                if (customer.name) {
                    //return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                    return "<p>" + customer.name +"</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.name,
        });
        var vendorToRemind = null;
        $('#charity-phone-number').select2({
            tags: true,
            width : '100%',
            ajax: {
                url: BASE_URL+'/customer-charity-phone-number',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    for (var i in data) {
                        data[i].id = data[i].phone ? data[i].phone : data[i].text;
                    }
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search by phone number',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {
                if (customer.name) {
                    return "<p style='color:#333;'>"+ customer.phone+ "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.phone,
        });
        $('#charity-email').select2({
            tags: true,
            width : '100%',
            ajax: {
                url: BASE_URL+'/customer-charity-email',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    for (var i in data) {
                        data[i].id = data[i].email ? data[i].email : data[i].text;
                    }
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search by Email number',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {
                if (customer.name) {
                    return "<p style='color:#333;'>"+ customer.email+ "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.email,
        });
        $(document).on('click', '.emailToAllModal', function () {
            var select_vendor = [];
            $('.select_vendor').each(function(){
                if ($(this).prop("checked")) {
                    select_vendor.push($(this).val());
                }
            });
            if (select_vendor.length === 0) {
                alert('Please Select vendors!!');
                return false;
            }
            $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val(select_vendor.join());
            $('#emailToAllModal').modal("show");
        });
        $(document).on('click', '.change-hubstaff-role', function () {
            var id = $(this).data('id');
            $("#hidden-vendor-id").val(id);
            $("#userHubstaffRoleModal").modal('show');
        });
        $(document).on('submit', '#user-hubstaff-role-form', function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: BASE_URL+"/vendors/changeHubstaffUserRole",
                data: $('#user-hubstaff-role-form').serialize()
            })
            .done(function(data){
                toastr["success"](data.message);
                $("#userHubstaffRoleModal").modal('hide');
            })
            .fail(function(error) {
                toastr["error"](error.responseJSON.message);
            })
        });
        
        $(document).on('click', '.send-email-to-vender', function () {
            $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val($(this).data('id'));
            $('#emailToAllModal').modal("show");
        });
        $(document).on('click', '.set-reminder', function () {
            let vendorId = $(this).data('id');
            let frequency = $(this).data('frequency');
            let message = $(this).data('reminder_message');
            let reminder_from = $(this).data('reminder_from');
            let reminder_last_reply = $(this).data('reminder_last_reply');
            $('#frequency').val(frequency);
            $('#reminder_message').val(message);
            $("#reminderModal").find("#reminder_from").val(reminder_from);
            if(reminder_last_reply == 1) {
                $("#reminderModal").find("#reminder_last_reply").prop("checked",true);
            }else{
                $("#reminderModal").find("#reminder_last_reply_no").prop("checked",true);
            }
            vendorToRemind = vendorId;
        });
        $(document).on('click', '.save-reminder', function () {
            var reminderModal = $("#reminderModal");
            let frequency = $('#frequency').val();
            let message = $('#reminder_message').val();
            let reminder_from = reminderModal.find("#reminder_from").val();
            let reminder_last_reply = (reminderModal.find('#reminder_last_reply').is(":checked")) ? 1 : 0;
            $.ajax({
                url: "{{action([\App\Http\Controllers\VendorController::class, 'updateReminder'])}}",
                type: 'POST',
                success: function () {
                    toastr['success']('Reminder updated successfully!');
					$(".set-reminder img").css("background-color", "");
					if(frequency > 0)
					{
						$(".set-reminder img").css("background-color", "red");
					}
                },
                data: {
                    vendor_id: vendorToRemind,
                    frequency: frequency,
                    message: message,
                    reminder_from: reminder_from,
                    reminder_last_reply: reminder_last_reply,
                    _token: "{{ csrf_token() }}"
                }
            });
        });
        $(document).on('click', '.edit-vendor', function () {

            $('select[name="website_stores[]"]').html('');
            $('select[name="website_stores[]"]').select2();
            $('select[name="websites[]"]').html('');
            $('select[name="websites[]"]').select2();

            var vendor = $(this).data('vendor');
            var url = "/products/customer/charity/" + vendor.id; 
            $('#charityEditModal form').attr('action', url);
            $('#vendor_category option[value="' + vendor.category_id + '"]').attr('selected', true);
            $('#vendor_store_website_id').val( vendor.store_website_id ).prop('selected', true);;
            $('#vendor_name').val(vendor.name);
            $('#vendor_address').val(vendor.address);
            $('#vendor_phone').val(vendor.phone);
            $('#vendor_email').val(vendor.email);
            $('#vendor_social_handle').val(vendor.social_handle);
            $('#vendor_website').val(vendor.website); 
            $('#vendor_gst').val(vendor.gst);
            $('#vendor_account_name').val(vendor.account_name);
            $('#vendor_account_iban').val(vendor.account_iban);
            $('#vendor_account_swift').val(vendor.account_swift);
            $('#frequency_of_payment').val(vendor.frequency_of_payment);
            $('#bank_name').val(vendor.bank_name);
            $('#bank_address').val(vendor.bank_address);
            $('#city').val(vendor.city);
            $('#country').val(vendor.country);
            $('#ifsc_code').val(vendor.ifsc_code);
            $('#remark').val(vendor.remark);
            var website_store_id = $(this).val();
            $.ajax({
                url: '/customer-charity/get-website-store/'+vendor.id,
                method: 'GET', 
            beforeSend: function() {
                $("#loading-image").show();
            }
            }).done(function(response) {
                $("#loading-image").hide();

                var store_website_ids = [];
                response.website_stores.forEach(function(value, index){
                    store_website_ids[index] = value.website_store.website.store_website.id;
                }) 

                var website_store_ids = [];
                response.website_stores.forEach(function(value, index){
                    website_store_ids[index] = value.website_store.id;
                }) 

                var html = ''; 
                response.all_stores.forEach(function(value, index){
                    html += `<option value="${value.id}" ${website_store_ids.includes(value.id) ? 'selected' : ''}>${value.name}</option>`
                }) 
                $('select[name="website_stores[]"]').append(html);
                $('select[name="website_stores[]"]').select2();
                
                var html = ''; 
                response.all_websites.forEach(function(value, index){
                    html += `<option value="${value.id}" ${store_website_ids.includes(value.id) ? 'selected' : ''}>${value.website}</option>`
                }) 
                $('select[name="websites[]"]').append(html);
                $('select[name="websites[]"]').trigger('change');
                
            }).fail(function() {
                console.log("error");
            });
        });
        $(document).on('click', '.create-agent', function () {
            var id = $(this).data('id');
            $('#agent_vendor_id').val(id);
        });
        $(document).on('click', '.edit-agent-button', function () {
            var agent = $(this).data('agent');
            var url = "{{ url('agent') }}/" + agent.id;
            $('#editAgentModal form').attr('action', url);
            $('#agent_name').val(agent.name);
            $('#agent_address').val(agent.address);
            $('#agent_phone').val(agent.phone);
            $('#agent_email').val(agent.email);
        });
        $(document).on('click', '.make-remark', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('#add-remark input[name="id"]').val(id);
            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.gettaskremark') }}',
                data: {
                    id: id,
                    module_type: "vendor"
                },
            }).done(response => {
                var html = '';
                $.each(response, function (index, value) {
                    html += ' <p> ' + value.remark + ' <br> <small>By ' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></p>';
                    html + "<hr>";
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });
        $('#addRemarkButton').on('click', function () {
            var id = $('#add-remark input[name="id"]').val();
            var remark = $('#add-remark').find('textarea[name="remark"]').val();
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.addRemark') }}',
                data: {
                    id: id,
                    remark: remark,
                    module_type: 'vendor'
                },
            }).done(response => {
                $('#add-remark').find('textarea[name="remark"]').val('');
                var html = ' <p> ' + remark + ' <br> <small>By You updated on ' + moment().format('DD-M H:mm') + ' </small></p>';
                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function (response) {
                console.log(response);
                alert('Could not fetch remarks');
            });
        });
        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });
        $(document).on('click', '.load-email-modal', function () {
            var id = $(this).data('id');
             $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('vendors.email') }}',
                data: {
                    id: id
                },
            }).done(function (response) {
                var html = '<div class="speech-wrapper">';
                response.forEach(function (message) {
                    var content = '';
                    content += 'To : '+message.to+'<br>';
                    content += 'From : '+message.from+'<br>';
                    if (message.cc) {
                        content += 'CC : '+message.cc+'<br>';
                    }
                    if (message.bcc) {
                        content += 'BCC : '+message.bcc+'<br>';
                    }
                    content += 'Subject : '+message.subject+'<br>';
                    content += 'Message : '+message.message+'<br>';
                    if (message.attachment.length) {
                        content += 'Attachment : ';
                    }
                    for (var i = 0; i < message.attachment.length; i++) {
                        var imageUrl = message.attachment[i];
                        imageUrl = imageUrl.trim();
                        // Set empty imgSrc
                        var imgSrc = '';
                        // Set image type
                        var imageType = imageUrl.substr(imageUrl.length - 4).toLowerCase();
                        // Set correct icon/image
                        if (imageType == '.jpg' || imageType == 'jpeg') {
                            imgSrc = imageUrl;
                        } else if (imageType == '.png') {
                            imgSrc = imageUrl;
                        } else if (imageType == '.gif') {
                            imgSrc = imageUrl;
                        } else if (imageType == 'docx' || imageType == '.doc') {
                            imgSrc = '/images/icon-word.svg';
                        } else if (imageType == '.xlsx' || imageType == '.xls' || imageType == '.csv') {
                            imgSrc = '/images/icon-excel.svg';
                        } else if (imageType == '.pdf') {
                            imgSrc = '/images/icon-pdf.svg';
                        } else if (imageType == '.zip' || imageType == '.tgz' || imageType == 'r.gz') {
                            imgSrc = '/images/icon-zip.svg';
                        } else {
                            imgSrc = '/images/icon-file-unknown.svg';
                        }
                        // Set media
                        if (imgSrc != '') {
                            content += '<div class="col-4"><a href="' + message.attachment[i] + '" target="_blank"><label class="label-attached-img" for="cb1_' + i + '"><img src="' + imgSrc + '" style="max-width: 100%;"></label></a></div>';
                        }
                    }
                    if (message.inout == 'in') {
                        html += '<div class="bubble"><div class="txt"><p class="name"></p><p class="message">' + content + '</p><br/><span class="timestamp">' + message.created_at.date.substr(0, 19) + '</span></div><div class="bubble-arrow"></div></div>';
                    } else if (message.inout == 'out') {
                        html += '<div class="bubble alt"><div class="txt"><p class="name alt"></p><p class="message">' + content + '</p><br/><span class="timestamp">' + message.created_at.date.substr(0, 19) + '</span></div> <div class="bubble-arrow alt"></div></div>';
                    }
                });
                html += '</div>';
                $("#email-list-history").find(".modal-body").html(html);
                $("#email-list-history").modal("show");
            }).fail(function (response) {
                console.log(response);
                alert('Could not load email');
            });
        });
        $(document).on("keyup", '.search_email_pop', function() {
            var value = $(this).val().toLowerCase();
            $(".speech-wrapper .bubble").filter(function() {
                $(this).toggle($(this).find('.message').text().toLowerCase().indexOf(value) > -1)
            });
        });
        $(document).on('click', '.send-message', function () {
            var thiss = $(this);
            var data = new FormData();
            var vendor_id = $(this).data('vendorid');
            var message = $(this).siblings('input').val();
            data.append("vendor_id", vendor_id);
            data.append("message", message);
            data.append("status", 1);
            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/charity',
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function (response) {
                        thiss.closest('tr').find('.chat_messages').html(thiss.siblings('input').val());
                        $(thiss).siblings('input').val('');
                        $(thiss).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(thiss).attr('disabled', false);
                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
        $(document).on('click', '.send-message1', function () {
            var thiss = $(this);
            var data = new FormData();
            var vendor_id = $(this).data('vendorid');
            var message = $("#messageid_"+vendor_id).val();
            data.append("vendor_id", vendor_id);
            data.append("message", message);
            data.append("status", 1);
            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/charity',
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function (response) {
                        //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                        if(message.length > 30)
                        {
                            var res_msg = message.substr(0, 27)+"..."; 
                            $("#message-chat-txt-"+vendor_id).html(res_msg);
                            $("#message-chat-fulltxt-"+vendor_id).html(message);    
                        }
                        else
                        {
                            $("#message-chat-txt-"+vendor_id).html(message); 
                            $("#message-chat-fulltxt-"+vendor_id).html(message);      
                        }
                        
                        $("#messageid_"+vendor_id).val('');
                        
                        $(thiss).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(thiss).attr('disabled', false);
                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
        $(document).on('change', '.update-category-user', function () {
            let catId = $(this).attr('data-categoryId');
            let userId = $(this).val();
            $.ajax({
                url: '{{ action([\App\Http\Controllers\VendorController::class, 'assignUserToCategory']) }}',
                data: {
                    user_id: userId,
                    category_id: catId
                },
                success: function (response) {
                    toastr['success']('User assigned to category completely!')
                }
            });
        });
        $(document).on('click', '.add-cc', function (e) {
            e.preventDefault();
            if ($('#cc-label').is(':hidden')) {
                $('#cc-label').fadeIn();
            }
            var el = `<div class="row cc-input">
            <div class="col-md-10">
                <input type="text" name="cc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image cc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;
            $('#cc-list').append(el);
        });
        $(document).on('click', '.cc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();
            parent.hide(300, function () {
                parent.remove();
                var n = 0;
                $('.cc-input').each(function () {
                    n++;
                });
                if (n == 0) {
                    $('#cc-label').fadeOut();
                }
            });
        });
        // bcc
        $(document).on('click', '.add-bcc', function (e) {
            e.preventDefault();
            if ($('#bcc-label').is(':hidden')) {
                $('#bcc-label').fadeIn();
            }
            var el = `<div class="row bcc-input">
            <div class="col-md-10">
                <input type="text" name="bcc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image bcc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;
            $('#bcc-list').append(el);
        });
        $(document).on('click', '.bcc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();
            parent.hide(300, function () {
                parent.remove();
                var n = 0;
                $('.bcc-input').each(function () {
                    n++;
                });
                if (n == 0) {
                    $('#bcc-label').fadeOut();
                }
            });
        });
        $(document).on('click', '.block-twilio', function () {
            var vendor_id = $(this).data('id');
            var thiss = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('vendors.block') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: vendor_id
                },
                beforeSend: function () {
                    $(thiss).text('Blocking...');
                }
            }).done(function (response) {
                if (response.is_blocked == 1) {
                    $(thiss).html('<img src="/images/blocked-twilio.png" />');
                } else {
                    $(thiss).html('<img src="/images/unblocked-twilio.png" />');
                }
            }).fail(function (response) {
                $(thiss).html('<img src="/images/unblocked-twilio.png" />');
                alert('Could not block customer!');
                console.log(response);
            });
        });
         $(document).on('click', '.call-select', function() {
        var id = $(this).data('id');
        $('#show'+id).toggle();
        console.log('#show'+id);
      });
          $(document).ready(function() {
              src = "{{ route('vendors.index') }}";
              $(".search").autocomplete({
                  source: function (request, response) {
                      id = $('#id').val();
                      name = $('#name').val();
                      email = $('#email').val();
                      phone = $('#phone').val();
                      address = $('#address').val();
                      category = $('#category').val();
                      $.ajax({
                          url: src,
                          dataType: "json",
                          data: {
                              id: id,
                              name: name,
                              phone: phone,
                              email: email,
                              address: address,
                              category: category,
                          },
                          beforeSend: function () {
                              $("#loading-image").show();
                          },
                      }).done(function (data) {
                          $("#loading-image").hide();
                          console.log(data);
                          $("#vendor-table tbody").empty().html(data.tbody);
                          if (data.links.length > 10) {
                              $('ul.pagination').replaceWith(data.links);
                          } else {
                              $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                          }
                          $(".select2-quick-reply").select2({});
                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          alert('No response from server');
                      });
                  },
                  minLength: 1,
              });
              $(document).ready(function () {
                  src = "{{ route('vendors.index') }}";
                  $("#search_id").autocomplete({
                      source: function (request, response) {
                          $.ajax({
                              url: src,
                              dataType: "json",
                              data: {
                                  term: request.term
                              },
                              beforeSend: function () {
                                  $("#loading-image").show();
                              },
                          }).done(function (data) {
                              $("#loading-image").hide();
                              $("#vendor-table tbody").empty().html(data.tbody);
                              if (data.links.length > 10) {
                                  $('ul.pagination').replaceWith(data.links);
                              } else {
                                  $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                              }
                              $(".select2-quick-reply").select2({});
                          }).fail(function (jqXHR, ajaxOptions, thrownError) {
                              alert('No response from server');
                          });
                      },
                      minLength: 1,
                  });
              });
              $(document).on("change", ".quickComment", function (e) {
                  var message = $(this).val();
                  var select = $(this);
                  if ($.isNumeric(message) == false) {
                      $.ajax({
                          headers: {
                              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                          },
                          url: BASE_URL+"/vendors/reply/add",
                          dataType: "json",
                          method: "POST",
                          data: {reply: message}
                      }).done(function (data) {
                          var vendors_id =$(select).find("option[value='']").data("vendorid");
                          var message_re = data.data.reply;
                          $("textarea#messageid_"+vendors_id).val(message_re);
                          console.log(data)
                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          alert('No response from server');
                      });
                  }
                  //$(this).closest("td").find(".quick-message-field").val($(this).find("option:selected").text());
                  var vendors_id =$(select).find("option[value='']").data("vendorid");
                  var message_re = $(this).find("option:selected").html();
                  $("textarea#messageid_"+vendors_id).val($.trim(message_re));
              });
              $(".select2-quick-reply").select2({tags: true});
              $(document).on("click", ".delete_quick_comment", function (e) {
                  var deleteAuto = $(this).closest(".d-flex").find(".quickComment").find("option:selected").val();
                  if (typeof deleteAuto != "undefined") {
                      $.ajax({
                          headers: {
                              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                          },
                          url: BASE_URL+"/vendors/reply/delete",
                          dataType: "json",
                          method: "GET",
                          data: {id: deleteAuto}
                      }).done(function (data) {
                          if (data.code == 200) {
                              // $(".quickComment ")
                              //     .find('option').not(':first').remove();
                              $(".quickComment").each(function(){
                              var selecto=  $(this)
                                  $(this).children("option").not(':first').each(function(){
                                    $(this).remove();
                                  });
                                  $.each(data.data, function (k, v) {
                                      $(selecto).append("<option  value='" + k + "'>" + v + "</option>");
                                  });
                                  $(selecto).select2({tags: true});
                              });
                          }
                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          alert('No response from server');
                      });
                  }
              });
          });
          function createUserFromVendor(id, email) {
                $('#vendor_id').attr('data-id', id);
                if (email) {
                    $('#createUser').attr('data-email', email);
                }
                $('#createUser').modal('show');
            }
            $('#createUser').on('hidden.bs.modal', function() {
                $('#createUser').removeAttr('data-email');
            })
         $(document).on("click", "#vendor_id", function () {
            $('#createUser').modal('hide');
            id = $(this).attr('data-id');
                $.ajax({
                          headers: {
                              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                          },
                          url: BASE_URL+"/vendors/create-user",
                          dataType: "json",
                          method: "POST",
                          data: {id: id},
                          beforeSend: function () {
                              $("#loading-image").show();
                          },
                      }).done(function (data) {
                        $("#loading-image").hide();
                          if (data.code == 200) {
                              alert(data.data);
                          }
                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          $("#loading-image").hide(); 
                          alert('No response from server');
                      });
          });
          function inviteGithub() {
            $('#createUser').modal('hide');
            const email = $('#createUser').attr('data-email');
            $.ajax({
                type: "POST",
                url: "/vendors/inviteGithub",
                data: {
                    _token: "{{ csrf_token() }}",
                    email
                }
            })
            .done(function(data){
                alert(data.message);
            })
            .fail(function(error) {
                alert(error.responseJSON.message);
            });
            console.log(email);
        }
        function inviteHubstaff() {
            $('#createUser').modal('hide');
            const email = $('#createUser').attr('data-email');
            console.log(email);
            $.ajax({
                type: "POST",
                url: BASE_URL+"/vendors/inviteHubstaff",
                data: {
                    _token: "{{ csrf_token() }}",
                    email
                }
            })
            .done(function(data){
                toastr["success"](data.message);
            })
            .fail(function(error) {
                toastr["error"](error.responseJSON.message);
            })
        }
        $('#reminder_from').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        $(document).on("change",".vendor-update-status",function(){
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: BASE_URL +"vendors/change-status",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: $this.data("id"),
                    status : $this.prop('checked')
                }
            }).done(function(data){
                if(data.code == 200) {
                    toastr["success"](data.message);
                }
            }).fail(function(error) {
                
            })
        });
        $(document).on("click",".vendor-update-status-icon",function(){
            var $this = $(this);
            var vendor_id = $(this).attr("data-id");
            var hdn_vendorstatus = $("#hdn_vendorstatus_"+vendor_id).val();
            $.ajax({
                type: "POST",
                url: BASE_URL +"vendors/change-status",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: $this.data("id"),
                    status : hdn_vendorstatus
                }
            }).done(function(data){
                if(data.code == 200) {
                    //toastr["success"](data.message);
                    if(hdn_vendorstatus == "true")
                    {
                        var img_url = BASE_URL + 'images/do-disturb.png';
                        $("#btn_vendorstatus_"+vendor_id).html('<img src="'+img_url+'" />');
                        $("#btn_vendorstatus_"+vendor_id).attr("title","On");
                        $("#hdn_vendorstatus_"+vendor_id).val('false');    
                    }
                    else
                    {
                        var img_url = BASE_URL + 'images/do-not-disturb.png';
                        $("#btn_vendorstatus_"+vendor_id).html('<img src="'+img_url+'" />');  
                        $("#btn_vendorstatus_"+vendor_id).attr("title","Off");
                        $("#hdn_vendorstatus_"+vendor_id).val('true');    
                    }
                    
                }
            }).fail(function(error) {
                
            })
        });
        $('ul.pagination').hide();
    $('.infinite-scroll').jscroll({
        autoTrigger: true,
        // debug: true,
        loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
        padding: 20,
        nextSelector: '.pagination li.active + li a',
        contentSelector: 'div.infinite-scroll',
        callback: function () {
            $('ul.pagination').first().remove();
            $('ul.pagination').hide();
        }
    });
    $(document).on('click', '.create_broadcast', function () {
        var vendors = [];
        $(".select_vendor").each(function () {
            if ($(this).prop("checked") == true) {
                vendors.push($(this).val());
            }
        });
        if (vendors.length == 0) {
            alert('Please select vendor');
            return false;
        }
        $("#create_broadcast").modal("show");
    });
    $("#send_message").submit(function (e) {
        e.preventDefault();
        var vendors = [];
        $(".select_vendor").each(function () {
            if ($(this).prop("checked") == true) {
                vendors.push($(this).val());
            }
        });
        if (vendors.length == 0) {
            alert('Please select vendor');
            return false;
        }
        if ($("#send_message").find("#message_to_all_field").val() == "") {
            alert('Please type message ');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "{{ route('vendors/send/message') }}",
            data: {
                _token: "{{ csrf_token() }}",
                message: $("#send_message").find("#message_to_all_field").val(),
                vendors: vendors
            },
            dataType:"json",
            beforeSend: function () {
              $("#create_broadcast").modal("hide");
              $("#loading-image").show();
            }
        }).done(function (response) {
            $("#loading-image").hide();
            //window.location.reload();
            toastr['success'](response.message);
        }).fail(function (response) {
            $("#loading-image").hide();
            toastr['error']("Request was failed due to some reason please check log for more information");
        });
    });
    $(document).on('click', '.add-vendor-info', function (e) {
          e.preventDefault();
          $("#hidden_edit_vendor_id").val($(this).data('id'));
          $("#add-vendor-info-modal").modal("show");
      });
      $(document).on('click', '.btn-submit-info', function (e) {
          e.preventDefault();
          var formData = $('#add-vendor-info-modal').find('form').serialize();
          $.ajax({
            type: "POST",
            url: "{{ route('vendors.edit-vendor') }}",
            data: formData,
          }).done(function(response) {
            toastr['success'](response.message);
            $("#add-vendor-info-modal").modal("hide");
            $('#add-vendor-info-form').trigger('reset');
          }).fail(function(error) {
            toastr['error'](error.responseJSON.message);
          });
      });

      $(document).on('click', '.add-charity-country', function () {
        $('#charity-country .modal-body').html('');
        $('#charity-country input[name="search_by_country"]').val('');
        $('#charity-country input[name="global_price"]').val('');
        $('#charity-country .charity-country-submit').attr('data-id', $(this).data('id')).attr('data-product-id', $(this).data('product-id'));
        $.ajax({
            url: "/customer-charity/get-websites/"+$(this).data('id'),  
        }).done(function(response) {
            var html = '';
            response.forEach(function(value, key){
                html += `<div class="form-group">
                            <strong>${value.name}</strong>
                            <input type="number" name="${value.code}" value="${value.price}" class="form-control">
                        </div>`;
            });
            $('#charity-country .modal-body').append(html);
            $('#charity-country').modal('show');
        }).fail(function(error) {
            toastr['error'](error.responseJSON.message);
        });
    });

    $(document).on('click', '.charity-country-submit', function () {
        var formData = $('#charity-country').find('form').serialize();
        $.ajax({
            url: "/customer-charity/get-websites/"+$(this).data('id'), 
            type: "POST",
            data: {
                '_token': '{{ csrf_token() }}',
                'data': formData
            }
        }).done(function(response) {
            toastr['success'](response);
            $('#charity-country').modal('hide');
        }).fail(function(error) {
            toastr['error'](error.responseJSON.message);
        });
    });

    $(document).on('change', 'input[name="global_price"]', function(){
        $('#charity-country .modal-body input').val($(this).val());
    });

    $(document).on('keyup', 'input[name="search_by_country"]', function(){
        var input, filter, ul, li, a, i, txtValue;
        input = $(this).val();
        filter = input.toUpperCase(); 
        var inputs = $('#charity-country .modal-body .form-group');
        console.log(inputs);
        for (i = 0; i < inputs.length; i++) {
            a = inputs[i].getElementsByTagName("strong")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                inputs[i].style.display = "";
            } else {
                inputs[i].style.display = "none";
            }
        }

    });

    function addwebsite(vendor_id)
      {
       
        $.ajax({
            url: "{{url('products/customer/charity/getwebsite')}}", 
            type: "POST",
            data: {
                '_token': '{{ csrf_token() }}',
                'charity_id': vendor_id

            }
        }).done(function(response) {
           
            $("#model_charity_id").val(vendor_id);
           /* $("#website_id").val(response.website_id).prop('selected', true);;;
            $("#website_price").val(response.price); */
            
            $("#dev_website").html(response);
            $('#createWebsite').modal("show");
        }).fail(function(error) {
            toastr['error'](error.responseJSON.message);
        });
        
        
      }

      function showpushbox(vendor_id)
      {
        
        $('#pushmagento_vender_id').val(vendor_id);
        $('#pushmagento').modal("show");
      }
      function savewebsite()
      {
        
        var formData = $('#create_website').serialize();
        var charity_id= $('#model_charity_id').val();
        var website_id= $('#website_id').val();
        var website_price= $('#website_price').val();
        $.ajax({
            url: "{{url('products/customer/charity/savewebsite')}}", 
            type: "POST",
            data: {
                '_token': '{{ csrf_token() }}',
                'charity_id': charity_id,
                'website_id': website_id,
                'price': website_price

            }
        }).done(function(response) {
            toastr['success'](response);
            
            $('#createWebsite').modal("hide");
        }).fail(function(error) {
            toastr['error'](error.responseJSON.message);
        });
   
      }

   function delwebsite(id)
   {
        $.ajax({
            url: "{{url('products/customer/charity/deletewebsite')}}", 
            type: "POST",
            data: {
                '_token': '{{ csrf_token() }}',
                'id': id
                
            }
        }).done(function(response) {
            toastr['success'](response);
            
            $('#createWebsite').modal("hide");
        }).fail(function(error) {
            toastr['error'](error.responseJSON.message);
        });
   }
    $(document).on('change', 'select[name="websites[]"]', function(){
        var website_id = $(this).val();
        $.ajax({
            url: '/magento-admin-settings/website/stores',
            method: 'POST',
            data: {
                _token : '{{ csrf_token() }}',
                website_id : website_id
                },
        beforeSend: function() {
            $("#loading-image").show();
        }
        }).done(function(response) {
            $("#loading-image").hide();
            var html = '';
            response.data.forEach(function(value, index){
                html += `<option value="${value.id}">${value.name}</option>`
            }) 
            $('.website_stores').append(html);
            $('.website_stores').select2();
        }).fail(function() {
            console.log("error");
        });
    });

    </script>
@endsection
