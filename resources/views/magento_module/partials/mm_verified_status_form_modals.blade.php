<div id="magentoModuleVerifiedStatus" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="module_verified_status_form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Add Verified Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <strong>Status Name :</strong>
                    {{ html()->text('name')->placeholder('Verified Status Name')->id('name')->class('form-control')->required() }}
                </div>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <strong>Status Color :</strong>
                    <input type="color" name="color" class="form-control"  id="color" value="" style="height:30px;padding:0px;">
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
@push('scripts')
    <script>

    $(document).on('submit', '#module_verified_status_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("module_verified_status_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento_modules.store-verified-status") }}',
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
                $('#magentoModuleVerifiedStatus #module_verified_status_form').trigger('reset');
                $('#magentoModuleVerifiedStatus #module_verified_status_form').find('.error-help-block').remove();
                $('#magentoModuleVerifiedStatus #module_verified_status_form').find('.invalid-feedback').remove();
                $('#magentoModuleVerifiedStatus #module_verified_status_form').find('.alert').remove();
                toastr["success"](response.message);
                oTable.draw();
                $('#magentoModuleVerifiedStatus').modal('hide');
                // location.reload();
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

@endpush