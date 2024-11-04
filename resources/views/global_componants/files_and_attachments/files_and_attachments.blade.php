<div id="FilesAndAttachments" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg " style="width: 1000px; max-width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Files And Attachments List</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive mt-3">
                  <table id="global_files_and_attachments_list" class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Title</th>
                        <th>Filename</th>
                        <th>URL</th>
                        <th>Created By</th>
                        <th>Created Date</th>
                      </tr>
                    </thead>

                    <tbody id="FilesAndAttachmentsdata">
                    
                    </tbody>
                  </table>
                </div>

                <form name="global_files_and_attachments_form" id="global_files_and_attachments_form" enctype="multipart/form-data" class="mt-5" >
                    @csrf
                    <input type="hidden" id="global_files_and_attachments_module_id" name="module_id" value="">
                    <input type="hidden" id="global_files_and_attachments_module" name="module" value="{{$module}}">
                    <div class="row">
                        <label class="ml-4">Add New File</label>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="col-md-3">
                                <label class="d-block">Title</label>
                                <input type="text" class="form-control" name="title">

                                <div id="global_files_and_attachments_module_title" ></div>
                                
                            </div>

                            <div class="col-md-5">
                                <label>File</label>
                                <input type="file" class="form-control" name="filename">
                                
                                <div id="global_files_and_attachments_module_filename" ></div>
                                
                            </div>
                            <div class="col-md-3" style="margin-top:27px;">
                                <button type="button" id="global_files_and_attachments_submit" class="btn btn-secondary">submit</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
function GlobalFilesAndAttachments(module_id) {
    $('#FilesAndAttachmentsdata').html('<tr><td colspan="5">Processing..</td></tr>');
    $('#FilesAndAttachments').modal('show');
    $('#global_files_and_attachments_module_id').val(module_id);

    get_data_GlobalFilesAndAttachments(module_id);
}


function get_data_GlobalFilesAndAttachments(module_id){
    var global_files_and_attachments_module = $('#global_files_and_attachments_module').val();
 
    $.ajax({
        url: "{{route('global_files_and_attachments')}}",
        type: 'POST',
        data: {module_id: module_id,module: global_files_and_attachments_module},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#FilesAndAttachmentsdata').html(response);
            console.log(response);
        },
        error: function(error) {
            // Handle error response
            console.error(error);
        }
    });
}

$("#global_files_and_attachments_submit").click(function() {
    var formData = new FormData($('#global_files_and_attachments_form')[0]);
    
    $.ajax({
        url: "{{route('global_files_and_attachments_store')}}",
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            var global_files_and_attachments_module_id = $('#global_files_and_attachments_module_id').val();
            get_data_GlobalFilesAndAttachments(global_files_and_attachments_module_id);
            // Handle success response
            $('#global_files_and_attachments_form')[0].reset();
            console.log(response);
        },
        error: function(error) {
            $('#global_files_and_attachments_module_title').html("<span class='text-danger'>"+ error.responseJSON.errors.title +"</span>");
            $('#global_files_and_attachments_module_filename').html("<span class='text-danger'>"+error.responseJSON.errors.filename +"</span>");
            // Handle error response
            console.error(error);
        }
    });
});
</script>