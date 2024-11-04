<div id="scriptdocumentEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="edit-h3">Edit Script Document</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {{ html()->form('POST', route('script-documents.update', ))->open() }}

                <div class="form-group {{ $errors->has('script_type') ? 'has-error' : '' }}">
                    <label> Script Type </label>
                    <input class="form-control" id="script_type_update" name="script_type" type="text" required>
                    <span class="text-danger">{{ $errors->first('script_type') }}</span>
                </div>

                <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
                    <label> File </label>
                    <input class="form-control id" name="id" type="hidden">
                    <input class="form-control" id="file_update" name="file" type="text">
                    <span class="text-danger">{{ $errors->first('file') }}</span>
                </div>

                <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                    <label> Description </label>
                    <textarea class="form-control" id="description_update" name="description"></textarea>
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                </div>

                <div class="form-group {{ $errors->has('usage_parameter') ? 'has-error' : '' }}">
                    <label> Usage Parameter </label>
                    <textarea class="form-control" id="usage_parameter_update" name="usage_parameter"></textarea>
                    <span class="text-danger">{{ $errors->first('usage_parameter') }}</span>
                </div>

                <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                    <label> Category</label>                
                    <input class="form-control" id="category_update" name="category" type="text">
                    <span class="text-danger">{{ $errors->first('category') }}</span>
                </div>

                <div class="form-group {{ $errors->has('comments') ? 'has-error' : '' }}">
                    <label> Comments</label>                
                    <textarea class="form-control" id="comments_update" name="comments"></textarea>
                    <span class="text-danger">{{ $errors->first('comments') }}</span>
                </div>

                <div class="form-group {{ $errors->has('author') ? 'has-error' : '' }}">
                    <label> Author</label>                
                    <input class="form-control" id="author_update" name="author" type="text" readonly>
                    <span class="text-danger">{{ $errors->first('author') }}</span>
                </div>

                <div class="form-group {{ $errors->has('location') ? 'has-error' : '' }}">
                    <label> Location</label>                
                    <input class="form-control" id="location_update" name="location" type="text">
                    <span class="text-danger">{{ $errors->first('location') }}</span>
                </div>

                <div class="form-group {{ $errors->has('last_run') ? 'has-error' : '' }}">
                    <label> Last Run</label>                
                    <input class="form-control" id="last_run_update" name="last_run" type="text">
                    <span class="text-danger">{{ $errors->first('last_run') }}</span>
                </div>

                <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                    <label> Status</label>                
                    <input class="form-control" id="status_update" name="status" type="text">
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-secondary btn-update-bug">Store</button>
                </div>
                {{ html()->form()->close() }}

            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.btn-update-bug', function() {
	$('.text-danger').html('');
	if($('#file_update').val() == '') {
        $('#file_update').next().text("Please enter the file");
        return false;
    }

    if($('#script_type_update').val() == '') {
        $('#script_type_update').next().text("Please enter the script type");
        return false;
    }

    if($('#description_update').val() == '') {
        $('#description_update').next().text("Please enter the description");
        return false;
    }
    
    if($('#usage_parameter_update').val() == '') {
        $('#usage_parameter_update').next().text("Please enter the usage parameter");
        return false;
    }

    if($('#category_update').val() == '') {
        $('#category_update').next().text("Please enter the category");
        return false;
    }

    if($('#comments_update').val() == '') {
        $('#comments_update').next().text("Please enter the comments");
        return false;
    }

    if($('#author_update').val() == '') {
        $('#author_update').next().text("Please enter the author");
        return false;
    }

    if($('#locatio_updaten').val() == '') {
        $('#location_update').next().text("Please enter the location");
        return false;
    }

    if($('#last_run_update').val() == '') {
        $('#last_run_update').next().text("Please enter the last run");
        return false;
    }

    if($('#status_update').val() == '') {
        $('#status_update').next().text("Please enter the status");
        return false;
    }

	return true;
});
</script>