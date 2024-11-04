<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-typeahead.css">
<div id="newPositionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Position</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form style="padding:10px;" action="{{ route('positions.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" name="title" placeholder="Position Title" value="{{ old('title') }}" required>

                    @if ($errors->has('name'))
                        <div class="alert alert-danger">{{$errors->first('name')}}</div>
                    @endif
                </div>

                <div class="form-group">
                    <input type="text" class="form-control" name="criteria" placeholder="Criteria" value="{{ old('criteria') }}" data-role="tagsinput" required>
                </div>

                <button type="submit" class="btn btn-secondary ml-3">Add Status</button>
            </form>
        </div>
    </div>
</div>