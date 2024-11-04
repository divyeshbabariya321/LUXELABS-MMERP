<div id="personal-meeting-update" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="personal-meeting-update-form" class="form mb-15" >
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Personal Meeting</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Meeting ID :</strong>
                        {{ html()->text('meeting_id')->placeholder('Meeting ID')->id('meeting_id')->class('form-control')->required() }}
                    </div>
                    <div class="form-group">
                        <strong>Meeting Topic :</strong>
                        {{ html()->text('meeting_topic')->placeholder('Meeting Topic')->id('meeting_topic')->class('form-control')->required() }}
                    </div>
                    <div class="form-group">
                        <strong>Join Meeting URL :</strong>
                        {{ html()->text('join_meeting_url')->placeholder('Join Meeting URL')->id('join_meeting_url')->class('form-control')->required() }}
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
    $(document).on('submit', '#personal-meeting-update-form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("personal-meeting-update-form"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("meetings.update.personal") }}',
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