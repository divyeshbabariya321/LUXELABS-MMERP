<form id="updateAccount-group-form" enctype="multipart/form-data" method="POST" action="{{route('google.billing.update',1)}}">
    @csrf
    <div class="modal-body">
        <input type="hidden" name="id" value="">
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Billing Account Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="edit_billing_account_name" name="edit_billing_account_name"
                       placeholder="Billing Account Name" value="{{ old('edit_billing_account_name') }}">
                @if ($errors->has('edit_billing_account_name'))
                    <span class="text-danger">{{$errors->first('edit_billing_account_name')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="edit_email" name="edit_email"
                       placeholder="email address" value="{{ old('email') }}">
                @if ($errors->has('email'))
                    <span class="text-danger">{{$errors->first('email')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Service file</label>
            <div class="col-sm-10">
                <input type="file" accept="application/json" name="edit_service_file"/>
                @if ($errors->has('edit_service_file'))
                    <span class="text-danger">{{$errors->first('edit_service_file')}}</span>
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
