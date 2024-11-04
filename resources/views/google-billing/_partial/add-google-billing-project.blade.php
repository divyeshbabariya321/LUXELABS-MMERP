<form id="add-group-form" method="POST" enctype="multipart/form-data" action="{{route('google.billing.add.project')}}">
    @csrf
    <div class="modal-body">
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Google Billing Account</label>
            <div class="col-sm-10">
                <select name="google_billing_master_id" id="" class="form-control">
                    <option value="">Select</option>
                    @foreach($googleBillingMaster as $billingId => $billingValue)
                        <option value="{{$billingId}}">{{$billingValue}}</option>
                    @endforeach
                </select>
                @if ($errors->has('google_billing_master_id'))
                    <span class="text-danger">{{$errors->first('google_billing_master_id')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Project ID</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="project_id" name="project_id"
                       placeholder="Project ID" value="{{ old('project_id') }}">
                @if ($errors->has('project_id'))
                    <span class="text-danger">{{$errors->first('project_id')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Service type</label>
            <div class="col-sm-10">
                <select name="service_type" id="" class="form-control">
                    <option value="">Select</option>
                    @foreach($serviceType as $serviceKey => $serviceValue)
                        <option value="{{$serviceKey}}">{{$serviceValue}}</option>
                    @endforeach
                </select>
                @if ($errors->has('service_type'))
                    <span class="text-danger">{{$errors->first('service_type')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Dataset ID</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="dataset_id" name="dataset_id"
                       placeholder="Dataset ID" value="{{ old('dataset_id') }}">
                @if ($errors->has('dataset_id'))
                    <span class="text-danger">{{$errors->first('dataset_id')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Table ID</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="table_id" name="table_id"
                       placeholder="Table ID" value="{{ old('table_id') }}">
                @if ($errors->has('table_id'))
                    <span class="text-danger">{{$errors->first('table_id')}}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Create</button>
    </div>
</form>
