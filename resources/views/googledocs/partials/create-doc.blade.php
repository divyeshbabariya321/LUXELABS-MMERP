<div id="createGoogleDocModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Google Doc</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('google-docs.create') }}" id="createGoogleDocForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Document type:</strong>

                        <select class="form-control" name="type" required>
                            <option value="spreadsheet">Spreadsheet</option>
                            <option value="doc">Doc</option>
                            <option value="ppt">Ppt</option>
                            <option value="xps">Xps</option>
                            <option value="txt">Txt</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="doc_name" value="{{ old('doc_name') }}" class="form-control input-sm" placeholder="Document Name" required>
                        @if ($errors->has('doc_name'))
                            <div class="alert alert-danger">{{$errors->first('doc_name')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Existing Doc Id:</strong>
                        <input type="text" name="existing_doc_id" value="{{ old('existing_doc_id') }}" class="form-control input-sm" placeholder="Existing Document ID">

                        @if ($errors->has('existing_doc_id'))
                            <div class="alert alert-danger">{{$errors->first('existing_doc_id')}}</div>
                        @endif
                    </div>
                    <div class="form-group custom-select2">
                        <label>Read Permission for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_multiple" multiple="multiple" name="read[]">
                                @foreach($users as $val)
                                <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
                    </div>
                    <div class="form-group custom-select2">
                        <label>Write Permission for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_multiple_write" multiple="multiple" name="write[]">
                                @foreach($users as $val)
                                <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Create</button>
                </div>
            </form>
        </div>

    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$("#id_label_multiple").select2();
$("#id_label_multiple_write").select2();

$(document).on('submit', '#createGoogleDocForm', function (e) {
    e.preventDefault();
    if (window.location.href == configs.routes.google_doc_index) return;

    $.ajax({
        url: configs.routes.google_doc_create,
        method: "POST",
        data: $(this).serialize(),
        success: function (resp) {
            switch (resp.status) {
                case 'success':
                    toastr.success(resp.msg);
                    break;
                case 'error':
                    toastr.error(resp.msg);
                    break;
            }
            $('#createGoogleDocModal').modal('hide');
        },
        error: function (error) {
            for (let msg of Object.values(error.responseJSON.errors)) {
                toastr.error(msg);
            }
        }
    });
});

</script>
