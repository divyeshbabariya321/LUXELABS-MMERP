<div id="project-theme-create" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="project-theme-create-form" class="form mb-15" >
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create Project Theme</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Project:</strong>
                        <select name="project_id" class="form-control select2" style="width: 100%!important">
                            <option value="" selected disabled>-- Select a Project --</option>
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
                    <button type="submit" class="btn btn-secondary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(document).on('submit', '#project-theme-create-form', function(e){
            e.preventDefault();
            var self = $(this);
            let formData = new FormData(document.getElementById("project-theme-create-form"));
            var button = $(this).find('[type="submit"]');
            $.ajax({
                url: '{{ route("project-theme.store") }}',
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