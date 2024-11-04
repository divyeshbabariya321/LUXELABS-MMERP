<div id="email-receive-modal" class="modal fade in" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title">Email Receiver</h4>
        <button type="button" class="close" data-dismiss="modal">×</button>
        </div>
        @if($emailReceivRec)
        <form class="form-horizontal" action="{{route('email-receiver-master.update',$emailReceivRec->id)}}" method="POST" id="email-receive-form">
            @method('PUT')
        @else
        <form class="form-horizontal" action="{{route('email-receiver-master.store')}}" method="POST" id="email-receive-form">
        @endif
            @csrf
            <input type="hidden" name="module_name" value="blog">
            <div class="modal-body">
              <div class="form-group">
                <label class="control-label col-sm-3" for="email">Email:</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control" id="blog_receiver_email" placeholder="Enter email" name="receiver_email" value="{{($emailReceivRec ? $emailReceivRec->email : '')}}">
                </div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="email-receive-close-btn">Close</button>
                <button type="button" class="btn btn-primary email-receive-save-btn" id="email-receive-save-btn">Save</button>
            </div>
        </form>
      </div>

    </div>
  </div>

  <script>
    $(document).ready(function() {
       
        $("#email-receive-save-btn").click(function(){

            
            var email_form_process = false;
            if($("#blog_receiver_email").val()) {
                var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                if (!($("#blog_receiver_email").val().match(validRegex))) {
                    alert("Please enter valid email");
                } else {
                    
                    email_form_process = true;
                }
                
            } else {
              
                email_form_process = true;
            }
            if(email_form_process) {
                let form_data = $("#email-receive-form").serialize();
                let ajax_url = $("#email-receive-form").attr('action');
                $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: form_data,
                    beforeSend: function() {
                        $("#loading-image-preview").show();
                    },
                    dataType : 'json',
                    success: function(data) {
                        if(data.status) {
                            toastr['success']('Record Updated successfully!!!', 'success');
                            $("#email-receive-close-btn").trigger("click");

                        } else {
                            toastr['error']('Error! Please try again', 'error');
                        }
                    },
                    complete : function(xhr,status) {
                        $("#loading-image-preview").hide();
                    },
                    error: function(xhr, status, error) {
                        // Handle the error here
                        toastr['error'](error, 'error');
                    }
                })
            }

        })
    });
  </script>