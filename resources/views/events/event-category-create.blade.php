<div class="modal fade" id="event-create-category-modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Create Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" id="event-create-category" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>category</label>
							<input type="text" class="form-control" name="category" placeholder="category name">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
	$(document).on('submit', '#event-create-category', function(e) {
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("event-create-category"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route('event.category.store') }}',
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
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
            success: function(data) {
                $('#event-create-category-modal').modal('hide');
                toastr["success"](data.message);
            },
            error: function(xhr, status, error) { // if error occured
                if (xhr.status == 422) {
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                } else {
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });
</script>