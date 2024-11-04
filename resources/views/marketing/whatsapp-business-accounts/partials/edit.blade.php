<div class="modal fade" id="whatsapp-business-edit" role="dialog" style="z-index: 3000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="col-md-12">
                <div class="page-header" style="width: 69%">
                    <h2>Update Whatsapp Business Account</h2>
                </div>
                <form id="add-group-form" method="POST" enctype="multipart/form-data"
                      action="{{route('whatsapp.business.account.update')}}">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" value="">
                    <div class="form-group row">
                        <label for="headline1" class="col-sm-2 col-form-label">Business phone number</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_business_phone_number"
                                   name="business_phone_number"
                                   placeholder="Business phone number" value="{{ old('business_phone_number') }}">
                            @if ($errors->has('business_phone_number'))
                                <span class="text-danger">{{$errors->first('business_phone_number')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="headline1" class="col-sm-2 col-form-label">Business account id</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_business_account_id" name="business_account_id"
                                   placeholder="Business account id" value="{{ old('business_account_id') }}">
                            @if ($errors->has('business_account_id'))
                                <span class="text-danger">{{$errors->first('business_account_id')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="headline1" class="col-sm-2 col-form-label">Business access token</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_business_access_token"
                                   name="business_access_token"
                                   placeholder="Business access token" value="{{ old('business_access_token') }}">
                            @if ($errors->has('business_access_token'))
                                <span class="text-danger">{{$errors->first('business_access_token')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="headline1" class="col-sm-2 col-form-label">Business phone id</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_business_phone_number_id"
                                   name="business_phone_number_id"
                                   placeholder="Business phone id" value="{{ old('business_phone_number_id') }}">
                            @if ($errors->has('business_phone_number_id'))
                                <span class="text-danger">{{$errors->first('business_phone_number_id')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="headline1" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="edit_email"
                                   name="email"
                                   placeholder="Email" value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <span class="text-danger">{{$errors->first('email')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="headline1" class="col-sm-2 col-form-label">About</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_about"
                                   name="about"
                                   placeholder="About" value="{{ old('about') }}">
                            @if ($errors->has('about'))
                                <span class="text-danger">{{$errors->first('about')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="headline1" class="col-sm-2 col-form-label">Address</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="edit_address"
                                      name="address" placeholder="Address">{{ old('address') }}</textarea>
                            @if ($errors->has('address'))
                                <span class="text-danger">{{$errors->first('address')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="headline1" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="edit_description"
                                      name="description" placeholder="Description">{{ old('description') }}</textarea>
                            @if ($errors->has('description'))
                                <span class="text-danger">{{$errors->first('description')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="headline1" class="col-sm-2 col-form-label">Websites</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_websites"
                                   name="websites"
                                   placeholder="Websites" value="{{ old('websites') }}">
                            @if ($errors->has('websites'))
                                <span class="text-danger">{{$errors->first('websites')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="float-right ml-2 custom-button btn"
                                data-dismiss="modal" aria-label="Close">Close
                        </button>
                        <button type="submit" class="float-right custom-button btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
