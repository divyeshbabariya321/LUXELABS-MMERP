<form id="add-group-form" method="POST" enctype="multipart/form-data" action="{{route('google.billing.add')}}">
    @csrf
    <div class="modal-body">
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Billing Account Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="billing_account_name" name="billing_account_name"
                       placeholder="Billing Account Name" value="{{ old('billing_account_name') }}">
                @if ($errors->has('billing_account_name'))
                    <span class="text-danger">{{$errors->first('billing_account_name')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="email" name="email"
                       placeholder="email address" value="{{ old('email') }}">
                @if ($errors->has('email'))
                    <span class="text-danger">{{$errors->first('email')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Service file</label>
            <div class="col-sm-10">
                <input type="file" accept="application/json" name="service_file"/>
                @if ($errors->has('service_file'))
                    <span class="text-danger">{{$errors->first('service_file')}}</span>
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
