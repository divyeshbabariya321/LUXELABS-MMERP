<div id="updateGoogleFilePermissionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Google File Permission</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            
                @csrf

                <div class="modal-body">
                    <input type="hidden" name="file_id" id = "file_id">
                    <input type="hidden" name="id" id = "id">
                    @php       
                    $users =  \App\User::select('id', 'name', 'email', 'gmail')->whereNotNull('gmail')->get();
                    @endphp
                    <div class="form-group custom-select2">
                        <label>Read Permission for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_file_permission_read" multiple="multiple" name="read[]">
                                @foreach($users as $val)
                                <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
                    </div>
                    <div class="form-group custom-select2">
                        <label>Write Permission for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_file_permission_write" multiple="multiple" name="write[]">
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
            
        </div>

    </div>
</div>