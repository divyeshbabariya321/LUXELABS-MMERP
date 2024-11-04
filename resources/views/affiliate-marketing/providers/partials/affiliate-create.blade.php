<form id="add-group-form" method="POST"
      action="{{route('affiliate-marketing.provider.affiliate.create', ['provider_account' => $provider->id])}}">
    @csrf
    <input type="hidden" id="provider_id" name="affiliate_account_id" value="{!! $provider->id !!}">
    <div class="form-group row">
        <label for="headline1" class="col-sm-3 col-form-label">
            First name
            <small style="color:red">*</small>
        </label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="firstName" name="firstName"
                   placeholder="First Name" value="{{ old('firstName') }}">
            <span class="text-danger" id="firstNameErr">
                {{$errors->has('firstName')? $errors->first('firstName') : ''}}
            </span>
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-3 col-form-label">
            Last name
            <small style="color:red">*</small>
        </label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="lastName" name="lastName"
                   placeholder="Last Name" value="{{ old('lastName') }}">
            <span class="text-danger" id="lastNameErr">
                {{$errors->has('lastName')? $errors->first('lastName'):''}}
            </span>
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-3 col-form-label">
            Email
            <small style="color:red">*</small>
        </label>
        <div class="col-sm-9">
            <input type="email" class="form-control" id="affiliateEmail" name="email"
                   placeholder="Email" value="{{ old('email') }}">
            <span class="text-danger" id="affiliateEmailErr">
                {{$errors->has('email')? $errors->first('email'):''}}
            </span>
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-3 col-form-label">
            Affiliate Group
            <small style="color:red">*</small>
        </label>
        <div class="col-sm-9">
            <select name="affiliate_group_id" id="affiliateGroup" class="form-control">
                <option value="">Select</option>
                @foreach($affiliateGroups as $group)
                    <option value="{{$group->id}}">{{$group->title}}</option>
                @endforeach
            </select>
            <span class="text-danger" id="affiliateGroupErr">
                {{$errors->has('affiliate_group_id')? $errors->first('affiliate_group_id'):''}}
            </span>
        </div>
    </div>
    <fieldset>
        <legend class="lagend">Company Details</legend>
        <div class="form-group row">
            <label for="headline1" class="col-sm-3 col-form-label">Company Name</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="company_name" name="company_name"
                       placeholder="Company Name" value="{{ old('company_name') }}">
                @if ($errors->has('company_name'))
                    <span class="text-danger">{{$errors->first('company_name')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-3 col-form-label">Company Description</label>
            <div class="col-sm-9">
                <textarea class="form-control" id="company_description" name="company_description"
                          placeholder="Company description">{{ old('company_description') }}</textarea>
                @if ($errors->has('company_description'))
                    <span class="text-danger">{{$errors->first('company_description')}}</span>
                @endif
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend class="lagend">Address</legend>
        <div class="form-group row">
            <label for="headline1" class="col-sm-3 col-form-label">Address 1</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="address_one" name="address_one"
                       placeholder="Address 1" value="{{ old('address_one') }}">
                @if ($errors->has('address_one'))
                    <span class="text-danger">{{$errors->first('address_one')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-3 col-form-label">Address 2</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="address_two" name="address_two"
                       placeholder="Address 2" value="{{ old('address_two') }}">
                @if ($errors->has('address_two'))
                    <span class="text-danger">{{$errors->first('address_two')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-3 col-form-label">Postal Code</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="address_postal_code" name="address_postal_code"
                       placeholder="Postal Code" value="{{ old('address_postal_code') }}">
                @if ($errors->has('address_postal_code'))
                    <span class="text-danger">{{$errors->first('address_postal_code')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-3 col-form-label">City</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="address_city" name="address_city"
                       placeholder="City" value="{{ old('address_city') }}">
                @if ($errors->has('address_city'))
                    <span class="text-danger">{{$errors->first('address_city')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-3 col-form-label">State</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="address_state" name="address_state"
                       placeholder="State" value="{{ old('address_state') }}">
                @if ($errors->has('address_state'))
                    <span class="text-danger">{{$errors->first('address_state')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-3 col-form-label">Country Code</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="address_country_code" name="address_country_code"
                       placeholder="Country Code" value="{{ old('address_country_code') }}">
                @if ($errors->has('address_country_code'))
                    <span class="text-danger">{{$errors->first('address_country_code')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-3 col-form-label">Country Name</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="address_country_name" name="address_country_name"
                       placeholder="Country name" value="{{ old('address_country_name') }}">
                @if ($errors->has('address_country_name'))
                    <span class="text-danger">{{$errors->first('address_country_name')}}</span>
                @endif
            </div>
        </div>
    </fieldset>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" onclick="return validateCreateAffiliate()" class="float-right custom-button btn">Create</button>
    </div>
</form>
