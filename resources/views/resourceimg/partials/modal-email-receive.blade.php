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
            <input type="hidden" name="module_name" value="resource">
            <div class="modal-body">
              <div class="form-group">
                <label class="control-label col-sm-3" for="email">Email:</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control" id="res_receiver_email" placeholder="Enter email" name="receiver_email" value="{{($emailReceivRec ? $emailReceivRec->email : '')}}">
                </div>
              </div>
              @php
                $cat_selected = "";
                $sub_cat_selected = "";
                if($emailReceivRec && $emailReceivRec->configs ) {
                    $emailReceivConf = json_decode($emailReceivRec->configs);
                    if($emailReceivConf->cat) {
                        $cat_selected = $emailReceivConf->cat;
                    }
                    if($emailReceivConf->sub_cat) {
                        $sub_cat_selected = $emailReceivConf->sub_cat;
                    }
                }
              @endphp
              <div class="form-group">
                <label class="control-label col-sm-3" for="email">Category:</label>
                <div class="col-sm-9">
                    <select name="configs[cat]" id="rec_config_cat" class="form-control">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option @if($cat_selected == $category->id) {{'selected'}} @endif value="{{$category->id}}">{{$category->title}}</option>
                        @endforeach
                    </select>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-3" for="email">Sub Category:</label>
                <div class="col-sm-9">
                    <select name="configs[sub_cat]" id="rec_config_sub_cat" class="form-control">
                        <option value="">Select Sub Category</option>
                        @if($emailReceivRec && $emailReceivRec->sub_cat_list)
                            @foreach($emailReceivRec->sub_cat_list as $sub_cat)
                            <option @if($sub_cat_selected == $sub_cat->id) {{'selected'}} @endif value="{{$sub_cat->id}}">{{$sub_cat->title}}</option>
                            @endforeach
                        @endif
                    </select>
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
        $('#rec_config_cat').change(function (e) { 
            $('#rec_config_sub_cat').html('<option value="">Select Sub Category</option>');
            
            var selected = $(this).val();
            if (selected.length > 0) {
                $.ajax({
                    url: "{{ url('/api/values-as-per-category') }}",
                    method: "POST",
                    data: {
                        selected: selected,
                        '_token': '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        $("#loading-image-preview").show();
                    },
                    success: function(data) {

                        $('#rec_config_sub_cat').append(data);
                    },
                    complete : function(xhr,status) {
                        $("#loading-image-preview").hide();
                    }
                })
            }
        });

        $("#email-receive-save-btn").click(function(){

            
            var email_form_process = false;
            if($("#res_receiver_email").val()) {
                var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                if (!($("#res_receiver_email").val().match(validRegex))) {
                    alert("Please enter valid email");
                } else if($("#rec_config_cat").val() == "") {
                    alert("Please Select Category");
                } else {
                    
                    email_form_process = true;
                }
            } else {
                if($("#rec_config_cat").val()) {
                    alert("Please enter email");
                } else {
                    email_form_process = true;
                }
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

                        
                        

                        // $('#rec_config_sub_cat').append(data);
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