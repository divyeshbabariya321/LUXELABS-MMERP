<div id="moduleTypeCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="module_type_form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Add Module Type</h4>
                <button type="button" class="close" data-dismiss="modal">&times; </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <strong>Module Type :</strong>
                    {{ html()->text('magento_module_type')->id('magento_module_type')->placeholder('Module Type')->class('form-control')->required() }}
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

<div id="moduleTypeEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="module_type_edit_form" class="form mb-15" >
                @csrf
                @method('PUT')
                {{ html()->hidden('id')->id('id') }}
                <div class="modal-header">
                    <h4 class="modal-title">Edit Module Type</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Module Type :</strong>
                        {{ html()->text('magento_module_type')->id('magento_module_type')->placeholder('Module Type')->class('form-control')->required() }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')

<script>

    $(document).on('submit', '#module_type_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("module_type_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento_module_types.store") }}',
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
                $('#moduleTypeCreateModal #module_type_form').trigger('reset');
                $('#moduleTypeCreateModal #module_type_form').find('.error-help-block').remove();
                $('#moduleTypeCreateModal #module_type_form').find('.invalid-feedback').remove();
                $('#moduleTypeCreateModal #module_type_form').find('.alert').remove();
                toastr["success"](response.message);
                oTable.draw();
                $('#moduleTypeCreateModal').modal('hide');
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

    $(document).on('click', '.edit-magento-module-type', function() {
        var module_type = $(this).data('row');
          console.log({module_type})
          console.log(module_type.magento_module_type);
          $('#module_type_edit_form #id').val(module_type.id);
          $('#module_type_edit_form #magento_module_type').val(module_type.magento_module_type);
          $('#moduleTypeEditModal').modal('show');
    });

    $(document).on('submit', '#module_type_edit_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("module_type_edit_form"));
        var module_type_id = $('#module_type_edit_form #id').val();
        console.log(formData, module_type_id);
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento_module_types.update", '') }}/' + module_type_id,
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
                button.html('Update');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#moduleCreateModal #module_type_edit_form').find('.error-help-block').remove();
                $('#moduleCreateModal #module_type_edit_form').find('.invalid-feedback').remove();
                $('#moduleCreateModal #module_type_edit_form').find('.alert').remove();
                oTable.draw();
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

@endpush