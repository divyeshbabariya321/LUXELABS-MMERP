<div id="unitTestStatusCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="unit_test_status_form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Add Unit Test Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <strong>Unit Test Status :</strong>
                    {{ html()->text('unit_test_status_name')->placeholder('Unit Test Status')->id('unit_test_status_name')->class('form-control')->required() }}
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
    $(document).on('submit', '#unit_test_status_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("unit_test_status_form"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("magento_modules.store-unit-test-status") }}',
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
                $('#unitTestStatusCreateModal #unit_test_status_form').trigger('reset');
                $('#unitTestStatusCreateModal #unit_test_status_form').find('.error-help-block').remove();
                $('#unitTestStatusCreateModal #unit_test_status_form').find('.invalid-feedback').remove();
                $('#unitTestStatusCreateModal #unit_test_status_form').find('.alert').remove();
                toastr["success"](response.message);
                oTable.draw();
                $('#unitTestStatusCreateModal').modal('hide');
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