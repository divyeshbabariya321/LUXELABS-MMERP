<div id="resource-email-description" class="modal fade in" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title">Resource Description</h4>
        <button type="button" class="close" data-dismiss="modal">×</button>
        </div>
        <div class="modal-body">
            <div id="modal-res-descr-body"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
       
      </div>

    </div>
  </div>

  <script>
      function showResDescription(res_id) {
          let res_img_url = "{{route('resourceimg.show',':id')}}";

          let ajax_url = res_img_url.replace(':id',res_id);
          $('#modal-res-descr-body').html("");

          $.ajax({
                  url: ajax_url,
                  method: "GET",
                  dataType : 'json',
                  beforeSend: function() {
                      $("#loading-image-preview").show();
                  },
                  success: function(data) {
                    console.log(data);
                      if(data.data) {
                          $('#modal-res-descr-body').html(data.data.description);
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
    
  </script>