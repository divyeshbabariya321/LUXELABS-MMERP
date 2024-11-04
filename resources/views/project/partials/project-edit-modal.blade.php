<div id="project-edit" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="project-edit-form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Edit Project</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <strong>Project Name :</strong>
                    {{ html()->hidden('id')->id('id')->class('form-control') }}
                    {{ html()->text('name')->placeholder('Project Name')->id('name')->class('form-control')->required() }}
                </div>
                <div class="form-group">
                    <strong>Project Type:</strong>
                    <select name="project_type" id="project_type" class="form-control select2" style="width: 100%!important">
                        <option value="" selected disabled>-- Select a project type --</option>
                        @forelse($projecttype as $id => $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <div class="form-group">
                    <strong>Job Name :</strong>
                    {{ html()->text('job_name')->placeholder('Job Name')->id('job_name')->class('form-control')->required() }}
                </div>
                <div class="form-group">
                    <strong>Store Website:</strong>
                    <select name="store_website_id[]" id="assign-new-website" class="form-control select2" style="width: 100%!important" multiple>
                        @forelse($store_websites as $website_id => $website_name)
                            <option value="{{ $website_id }}">{{ $website_name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <div class="form-group">
                    <strong>Serverenv:</strong>
                    <select name="serverenv" id="serverenv" class="form-control select2" style="width: 100%!important">
                        @forelse($serverenvs as $id => $serverenvs)
                            <option value="{{ $serverenvs }}">{{ $serverenvs }}</option>
                        @empty
                        @endforelse
                    </select>
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

    $(document).on('submit', '#project-edit-form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("project-edit-form"));
        var button = $(this).find('[type="submit"]');
        var id = $("#project-edit-form #id").val();
        $.ajax({
            url: '{{ route("project.update", "") }}/' + id,
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