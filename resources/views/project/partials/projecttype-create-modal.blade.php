<div id="projecttype-create" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="projecttype-create-form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Project Type</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <strong>Project Type:</strong>
                    {{ html()->text('name')->placeholder('Project Type')->id('name')->class('form-control')->required() }}
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

    $(document).on('submit', '#projecttype-create-form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("projecttype-create-form"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("project.projectTypeStore") }}',
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