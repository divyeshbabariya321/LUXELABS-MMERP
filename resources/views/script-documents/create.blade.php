<div id="scriptdocumentsCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="edit-h3">Add Script Document</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            {{ html()->form('POST', route('script-documents.store', ))->open() }}

            <div class="form-group {{ $errors->has('script_type') ? 'has-error' : '' }}">
                <label> Script Type </label>
                <input class="form-control" id="script_type" name="script_type" type="text" required>
                <span class="text-danger">{{ $errors->first('script_type') }}</span>
            </div>

            <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
                <label> File </label>
                <input class="form-control" id="file" name="file" type="text" required>
                <span class="text-danger">{{ $errors->first('file') }}</span>
            </div>

            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                <label> Description </label>
                <textarea class="form-control" id="description" name="description" required></textarea>
                <span class="text-danger">{{ $errors->first('description') }}</span>
            </div>

            <div class="form-group {{ $errors->has('usage_parameter') ? 'has-error' : '' }}">
                <label> Usage Parameter </label>
                <textarea class="form-control" id="usage_parameter" name="usage_parameter" required></textarea>
                <span class="text-danger">{{ $errors->first('usage_parameter') }}</span>
            </div>

            <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                <label> Category</label>                
				<input class="form-control" id="category" name="category" type="text" required>
                <span class="text-danger">{{ $errors->first('category') }}</span>
            </div>

            <div class="form-group {{ $errors->has('comments') ? 'has-error' : '' }}">
                <label> Comments</label>                
                <textarea class="form-control" id="comments" name="comments" required></textarea>
                <span class="text-danger">{{ $errors->first('comments') }}</span>
            </div>

            <div class="form-group {{ $errors->has('author') ? 'has-error' : '' }}">
                <label> Author</label>                
                <input class="form-control" id="author" name="author" type="text" required value="{{\Auth::user()->name}}">
                <span class="text-danger">{{ $errors->first('author') }}</span>
            </div>

            <div class="form-group {{ $errors->has('location') ? 'has-error' : '' }}">
                <label> Location</label>                
                <input class="form-control" id="location" name="location" type="text" required>
                <span class="text-danger">{{ $errors->first('location') }}</span>
            </div>

            <div class="form-group {{ $errors->has('last_run') ? 'has-error' : '' }}">
                <label> Last Run</label>                
                <input class="form-control" id="last_run" name="last_run" type="text" required>
                <span class="text-danger">{{ $errors->first('last_run') }}</span>
            </div>

            <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                <label> Status</label>                
                <input class="form-control" id="status" name="status" type="text" required>
                <span class="text-danger">{{ $errors->first('status') }}</span>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-secondary btn-save-script-document">Store</button>
            </div>
            {{ html()->form()->close() }}

            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.btn-save-script-document', function() {
	$('.text-danger').html('');
	if($('#file').val() == '') {
		$('#file').next().text("Please enter the file");
		return false;
	}

    if($('#script_type').val() == '') {
        $('#script_type').next().text("Please enter the script type");
        return false;
    }

    if($('#description').val() == '') {
        $('#description').next().text("Please enter the description");
        return false;
    }
	
    if($('#usage_parameter').val() == '') {
        $('#usage_parameter').next().text("Please enter the usage parameter");
        return false;
    }

    if($('#category').val() == '') {
        $('#category').next().text("Please enter the category");
        return false;
    }

    if($('#comments').val() == '') {
        $('#comments').next().text("Please enter the comments");
        return false;
    }

    if($('#author').val() == '') {
        $('#author').next().text("Please enter the author");
        return false;
    }

    if($('#location').val() == '') {
        $('#location').next().text("Please enter the location");
        return false;
    }

    if($('#last_run').val() == '') {
        $('#last_run').next().text("Please enter the last run");
        return false;
    }

    if($('#status').val() == '') {
        $('#status').next().text("Please enter the status");
        return false;
    }
    
	return true;

});

</script>