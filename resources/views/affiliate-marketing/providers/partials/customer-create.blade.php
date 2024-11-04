<form id="add-group-form" method="POST"
      action="{{route('affiliate-marketing.provider.customer.create', ['provider_account' => $provider->id])}}">
    @csrf
    <input type="hidden" id="provider_id" name="provider_account" value="{!! $provider->id !!}">
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Referral Code</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="referral_code" name="referral_code"
                   placeholder="Referral Code" value="{{ old('referral_code') }}">
            @if ($errors->has('referral_code'))
                <span class="text-danger">{{$errors->first('referral_code')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Tracking Id</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="tracking_id" name="tracking_id"
                   placeholder="Tracking Id" value="{{ old('tracking_id') }}">
            @if ($errors->has('tracking_id'))
                <span class="text-danger">{{$errors->first('tracking_id')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Click Id</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="click_id" name="click_id"
                   placeholder="Click Id" value="{{ old('click_id') }}">
            @if ($errors->has('click_id'))
                <span class="text-danger">{{$errors->first('click_id')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Coupon</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="coupon" name="coupon"
                   placeholder="Coupon" value="{{ old('coupon') }}">
            @if ($errors->has('coupon'))
                <span class="text-danger">{{$errors->first('coupon')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">
            Affiliate
            <small style="color:red">*</small>
        </label>
        <div class="col-sm-10">
            <select name="asset_id" id="asset_id" class="form-control">
                <option value="">Select</option>
                @foreach($affiliates as $aff)
                    <option value="{{$aff->asset_id}}">{{$aff->firstname.' '.$aff->lastname}}</option>
                @endforeach
            </select>
            <span id="assetErr" class="err text-danger">
                {{$errors->has('asset_id')? $errors->first('asset_id'):''}}
            </span>
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">
            Customer
            <small style="color:red">*</small>
        </label>
        <div class="col-sm-10">
            <select name="customer_id" id="customer_id" class="form-control">
                <option value="">Select</option>
                @foreach($customers as $customer)
                    <option value="{{$customer->id}}">{{$customer->name}} ({!! $customer->email !!})</option>
                @endforeach
            </select>
            <span id="customerErr" class="err text-danger">
                {{$errors->has('customer_id')? $errors->first('customer_id'):''}}
            </span>
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">User Agent</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="user_agent" name="user_agent"
                   placeholder="User Agent" value="{{ old('user_agent') }}">
            @if ($errors->has('user_agent'))
                <span class="text-danger">{{$errors->first('user_agent')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">IP</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="ip" name="ip"
                   placeholder="IP" value="{{ old('ip') }}">
            @if ($errors->has('ip'))
                <span class="text-danger">{{$errors->first('ip')}}</span>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" onclick="return validateCreateCustomer()" class="float-right custom-button btn">Create</button>
    </div>
</form>
