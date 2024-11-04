<div id="moduleCategoryCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="module_category_form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Add Module Category</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <strong>Module Category :</strong>
                    {{ html()->text('category_name')->placeholder('Module Category')->id('category_name')->class('form-control')->required() }}
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

<div id="moduleCategoryEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="module_category_edit_form" method="POST">
                @csrf
                @method('PUT')
                {{ html()->hidden('id')->id('id') }}
                <div class="modal-header">
                    <h4 class="modal-title">Edit Module Category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Module Category :</strong>
                            {{ html()->text('category_name')->id('category_name')->placeholder('Module Type')->class('form-control')->required() }}
                            @if ($errors->has('category_name'))
                                <div class="alert alert-danger">{{ $errors->first('category_name') }}</div>
                            @endif
                        </div>
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

    $(document).on('submit', '#module_category_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("module_category_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento_module_categories.store") }}',
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
                $('#moduleCategoryCreateModal #module_category_form').trigger('reset');
                $('#moduleCategoryCreateModal #module_category_form').find('.error-help-block').remove();
                $('#moduleCategoryCreateModal #module_category_form').find('.invalid-feedback').remove();
                $('#moduleCategoryCreateModal #module_category_form').find('.alert').remove();
                toastr["success"](response.message);
                oTable.draw();
                $('#moduleCategoryCreateModal').modal('hide');
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

    $(document).on('click', '.edit-magento-module-category', function() {
        var module_category = $(this).data('row');
          console.log({module_category})
          console.log(module_category.category_name);
          $('#module_category_edit_form #id').val(module_category.id);
          $('#module_category_edit_form #category_name').val(module_category.category_name);
          $('#moduleCategoryEditModal').modal('show');
    });

    $(document).on('submit', '#module_category_edit_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("module_category_edit_form"));
        var module_category_id = $('#module_category_edit_form #id').val();
        console.log(formData, module_category_id);
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento_module_categories.update", '') }}/' + module_category_id,
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
                $('#moduleCreateModal #module_category_edit_form').find('.error-help-block').remove();
                $('#moduleCreateModal #module_category_edit_form').find('.invalid-feedback').remove();
                $('#moduleCreateModal #module_category_edit_form').find('.alert').remove();
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