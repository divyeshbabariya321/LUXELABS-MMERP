<div id="childImageAddModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="magento_frontend_child_image_form" class="form mb-15" enctype="multipart/form-data">
            @csrf
            {{ html()->hidden('magento_backend_id')->id('magento_backend_id') }}
            <div class="modal-header">
                <h4 class="modal-title">Add Admin Config file</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Admin Config file</label>
                                <input type="file" name="admin_config_image[]" id="admin_config_image" class="form-control input-sm" placeholder="Upload File" style="height: fit-content;" multiple>       
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Add</button>
            </div>
            </form>
        </div>
    </div>
</div>


    <script>

    $(document).on('submit', '#magento_frontend_child_image_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_frontend_child_image_form"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("magento-backend-admin-upload") }}',
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                button.html(spinner_html);
                button.prop('disabled', true);
                button.addClass('disabled');
            },
            complete: function() {
                button.html('Add');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#magento_frontend_child_image_form').trigger('reset');
                magentofrontendTable.draw();
                toastr["success"](response.message);
            },
            error: function(xhr, status, error) { // if error occured
                if(xhr.status == 422){
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                }
                else{
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });

    </script>

