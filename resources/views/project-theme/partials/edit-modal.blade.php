<div id="project-theme-edit" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="project-theme-edit-form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Edit Project Theme</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <strong>Project Name :</strong>
                    {{ html()->hidden('id')->id('id')->class('form-control') }}
                    <select name="project_id" id="project_id" class="form-control select2" style="width: 100%!important">
                        @forelse($projects as $id => $project)
                            <option value="{{ $id }}">{{ $project }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <div class="form-group">
                    <strong>Theme Name :</strong>
                    {{ html()->text('name')->placeholder('Theme Name')->id('name')->class('form-control')->required() }}
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Edit</button>
            </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>

    $(document).on('submit', '#project-theme-edit-form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("project-theme-edit-form"));
        formData.append('_method', 'PUT');
        var button = $(this).find('[type="submit"]');
        var ajaxUrl = "{{ route('project-theme.update', ['project_theme' => ':id']) }}";
        ajaxUrl = ajaxUrl.replace(':id', $("#project-theme-edit-form #id").val());

        $.ajax({
            url: ajaxUrl,
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
                button.html('Edit');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                toastr["success"](response.message);
                location.reload();
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