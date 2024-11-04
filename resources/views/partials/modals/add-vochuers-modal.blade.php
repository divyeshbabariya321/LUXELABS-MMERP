<div id="addvoucherModel" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="" method="POST" id="addupdate" >
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <h4 class="modal-title">Add / Update Voucher</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                        <div class="form-group">
                            {{ html()->label('Plateform', 'plateform_id')->class('form-control-label') }}
                            {{ html()->select("plateform_id", ['' => ''])->id("plateform_id_new")->class("form-control globalSelect2")->style("width:100%;")->data('ajax', route('select2.vochers_platforms'))->data('placeholder', 'Add Platforms') }}
                                @if($errors->has('plateform_id'))
                                  <div class="form-control-plateform">{{$errors->first('plateform_id')}}</div>
                                @endif
                        </div>


                        <div class="form-group">
                          {{ html()->label('Email', 'email_id')->class('form-control-label') }}
                          {{ html()->select("email_id", ['' => ''])->class("form-control globalSelect2")->style("width:100%;")->data('ajax', route('select2.vochers_emails'))->data('placeholder', 'Select Email') }}
                              @if($errors->has('email_id'))
                                <div class="form-control-plateform">{{$errors->first('email_id')}}</div>
                              @endif
                        </div>

                        <div class="form-group">
                            {{ html()->label('Number', 'whatsapp_config_id')->class('form-control-label') }}
                            {{ html()->select("whatsapp_config_id", ['' => ''])->class("form-control globalSelect2")->style("width:100%;")->data('ajax', route('select2.vochers_whatsapp_config'))->data('placeholder', 'Select Number') }}
                              @if($errors->has('whatsapp_config_id'))
                                <div class="form-control-plateform">{{$errors->first('whatsapp_config_id')}}</div>
                              @endif
                        </div>

                        <div class="form-group">
                          {{ html()->label('Password', 'password')->class('form-control-label') }}
                          <input type="text" class="form-control" name="password" id="password" style="width: 100%;"/>
                            
                            @if($errors->has('password'))
                              <div class="form-control-plateform">{{$errors->first('password')}}</div>
                            @endif
                      </div>
                    </div>
                
                  <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-danger save-voucher">Submit</button>
                  </div>
                </div>
            </form>
        </div>

    </div>


    <script>
      $(document).on("click",".save-voucher",function(e){
          e.preventDefault();
          var $this = $(this);
          var formData = new FormData($this.closest("form")[0]);
          $.ajax({
            url: '{{route("voucher.coupon.store")}}',
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
              dataType:"json",
            data: $this.closest("form").serialize(),
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (data) {
            $("#loading-image").hide();
            toastr["success"](data.message);
            location.reload();
          }).fail(function (jqXHR, ajaxOptions, thrownError) {      
            toastr["error"](jqXHR.responseJSON.message);
            $("#loading-image").hide();
          });
     });

    
    </script>