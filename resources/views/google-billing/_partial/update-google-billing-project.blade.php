<form id="updateProject-group-form" enctype="multipart/form-data" method="POST" action="{{route('google.billing.update.project',1)}}">
    @csrf
    <div class="modal-body">
        <input type="hidden" name="id" value="">
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Google Billing Account</label>
            <div class="col-sm-10">
                <select name="edit_google_billing_master_id" id="" class="form-control">
                    <option value="">Select</option>
                    @foreach($googleBillingMaster as $billingId => $billingValue)
                        <option value="{{$billingId}}">{{$billingValue}}</option>
                    @endforeach
                </select>
                @if ($errors->has('edit_google_billing_master_id'))
                    <span class="text-danger">{{$errors->first('edit_google_billing_master_id')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Project ID</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="edit_project_id" name="edit_project_id"
                       placeholder="Project ID" value="{{ old('edit_project_id') }}">
                @if ($errors->has('edit_project_id'))
                    <span class="text-danger">{{$errors->first('edit_project_id')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Site</label>
            <div class="col-sm-10">
                <select name="edit_service_type" id="" class="form-control">
                    <option value="">Select</option>
                    @foreach($serviceType as $serviceKey => $serviceValue)
                        <option value="{{$serviceKey}}" {{ $serviceKey == old('edit_service_key') ? 'selected="selected"' : '' }} >{{$serviceValue}}</option>
                    @endforeach
                </select>
                @if ($errors->has('edit_service_type'))
                    <span class="text-danger">{{$errors->first('edit_service_type')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Dataset ID</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="edit_dataset_id" name="edit_dataset_id"
                       placeholder="Dataset ID" value="{{ old('edit_dataset_id') }}">
                @if ($errors->has('edit_dataset_id'))
                    <span class="text-danger">{{$errors->first('edit_dataset_id')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Table ID</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="edit_table_id" name="edit_table_id"
                       placeholder="Table ID" value="{{ old('edit_table_id') }}">
                @if ($errors->has('edit_table_id'))
                    <span class="text-danger">{{$errors->first('edit_table_id')}}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Update</button>
    </div>
</form>
