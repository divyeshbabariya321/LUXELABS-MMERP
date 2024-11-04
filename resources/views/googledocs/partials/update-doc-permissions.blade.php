<div id="updateGoogleDocPermissionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Google Doc Permission</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('google-docs.permission.update') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <input type="hidden" name="file_id" id = "file_id">
                    <input type="hidden" name="id" id = "id">
                    <div class="form-group custom-select2">
                        <label>Read Permission for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_permission_read" multiple="multiple" name="read[]">
                                @foreach($users as $val)
                                <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
                    </div>
                    <div class="form-group custom-select2">
                        <label>Write Permission for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_permission_write" multiple="multiple" name="write[]">
                                @foreach($users as $val)
                                <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
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
<div id="updatemultipleGoogleDocPermissionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Google Doc Permission</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('google-docs.addMulitpleDocPermission') }}" id="updatemultipleGoogleDocPermissionForm" method="POST">
                @csrf

                <div class="modal-body">
                    <input type="hidden" name="add_doc_ids" id = "add_doc_ids">
                    <div class="form-group custom-select2">
                        <label>Read Permission for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_mulitiple_permission_read" multiple="multiple" name="read[]">
                                @foreach($users as $val)
                                <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
                    </div>
                    <div class="form-group custom-select2">
                        <label>Write Permission for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_mulitiple_permission_write" multiple="multiple" name="write[]">
                                @foreach($users as $val)
                                <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
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
<div id="GoogleDocRemovePermissionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Remove Google Doc Permission</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('google-docs.googleDocRemovePermission') }}" id="GoogleDocRemovePermissionForm" method="POST">
                @csrf

                <div class="modal-body">
                    <input type="hidden" name="remove_doc_ids" id = "remove_doc_ids">
                    <div class="form-group custom-select2">
                        <label>Read Permission remove for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_doc_remove_permission_read" multiple="multiple" name="read[]">
                                @foreach($users as $val)
                                <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
                    </div>
                    <div class="form-group custom-select2">
                        <label>Write Permission remove for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_doc_remove_permission_write" multiple="multiple" name="write[]">
                                @foreach($users as $val)
                                <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Remove</button>
                </div>
            </form>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$("#id_label_permission_read").select2();
$("#id_label_permission_write").select2();
$("#id_label_mulitiple_permission_read").select2();
$("#id_label_mulitiple_permission_write").select2();
$("#id_label_doc_remove_permission_read").select2();
$("#id_label_doc_remove_permission_write").select2();
</script>
