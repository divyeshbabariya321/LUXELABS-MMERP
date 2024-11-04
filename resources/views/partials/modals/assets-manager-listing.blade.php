<form action="" method="POST">
    @csrf
    @method('PUT')

    <div class="modal-header">
        <h4 class="modal-title">Update a Assets Manager</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" id="asset_name" required>

                    @if ($errors->has('name'))
                        <div class="alert alert-danger">{{$errors->first('name')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Capacity:</strong>
                    <input type="text" name="capacity" id="capacity" class="form-control" value="{{ old('capacity') }}">

                    @if ($errors->has('capacity'))
                        <div class="alert alert-danger">{{$errors->first('capacity')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>User Name:</strong>
                    <button type="button" class="btn btn-xs show-user-history-btn" title="Show User History" ><i class="fa fa-info-circle"></i></button>

                    <input type="hidden" name="old_user_name"  id="old_user_name"  class="form-control" value="{{ old('old_user_name') }}">


                    <select class="form-control select2" name="user_name" id="user_name">
                        <option value="">Select</option>
                        @foreach($users as $key => $user)
                            <option value="{{$user['id']}}" {{ $user['id'] == old('user_name') ? 'selected' : '' }}>{{$user['name']}}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('user_name'))
                        <div class="alert alert-danger">{{$errors->first('user_name')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Password:</strong>
                    <input type="text" name="password" class="form-control password-assets-manager" value="{{ old('password') }}" id="password" required>
                    <input type="hidden" name="old_password" class="form-control oldpassword-assets-manager" value="{{ old('old_password') }}" id="old_password">

                    @if ($errors->has('password'))
                        <div class="alert alert-danger">{{$errors->first('password')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>IP:</strong>
                    <input type="text" name="ip" id="ip" class="form-control" value="{{ old('ip') }}">
                    <input type="hidden" name="old_ip" class="form-control" value="{{ old('old_ip') }}" id="old_ip">

                    @if ($errors->has('ip'))
                        <div class="alert alert-danger">{{$errors->first('ip')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>IP Name:</strong>
                    <input type="hidden" class="getUpdCount" value="0"/>
                    <div class="addUpdIpName">
                        <input type="text" name="ip_name" id="ip_name_ins" class="form-control" value="{{ old('ip_name') }}">
                    </div>
                    
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Link:</strong>
                    <div class="addUpdIpName">
                        <input type="text" name="link" id="link" class="form-control" value="{{ old('link') }}">
                    </div>
                    
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Folder Name:</strong>
                    <input type="hidden" class="getServerUpdCount" value="0"/>
                    <div class="addServerUpdate">
                        <input type="text" name="folder_name[]" id="folder_name0" class="form-control" value="">
                    </div>
                    <a href="javascript:void(0);" class="serverUpdbtn">Add Folder Name</a>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Server Password:</strong>
                    <input type="text" name="server_password" id="server_password" class="form-control" value="{{ old('server_password') }}">

                    @if ($errors->has('ip'))
                        <div class="alert alert-danger">{{$errors->first('server_password')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Assigen to</strong>
                    <select class="form-control select2" name="assigned_to" id="assigned_to">
                        <option value="">Select</option>
                        @foreach($users as $key => $user)
                            <option value="{{$user['id']}}" {{ $user['id'] == old('user_name') ? 'selected' : '' }}>{{$user['name']}}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('assigned_to'))
                        <div class="alert alert-danger">{{$errors->first('assigned_to')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Site Name</strong>
                    <select class="form-control select2" name="website_id" id="website_id" >
                        <option value="">Select</option>
                        @foreach($websites as $website)
                            <option value="{{$website->id}}" {{ $website->id == old('website_id') ? 'selected' : '' }}>{{$website->website}}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('assigned_to'))
                        <div class="alert alert-danger">{{$errors->first('website_id')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Plate Form</strong>
                    <select class="form-control select2" name="asset_plate_form_id" id="asset_plate_form_id" >
                        <option value="">Select</option>
                        @foreach($plateforms as $plateform)
                            <option value="{{$plateform->id}}" {{ $plateform->id == old('asset_plate_form_id') ? 'selected' : '' }}>{{$plateform->name}}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('asset_plate_form_id'))
                        <div class="alert alert-danger">{{$errors->first('asset_plate_form_id')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Email Address</strong>
                    <select class="form-control select2" name="email_address_id" id="email_address_id" >
                        <option value="">Select</option>
                        @foreach($emailAddress as $email)
                            <option value="{{$email->id}}" {{ $email->id == old('email_address_id') ? 'selected' : '' }}>{{$email->from_name}}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('email_address_id'))
                        <div class="alert alert-danger">{{$errors->first('email_address_id')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Phone Number</strong>
                    <select class="form-control select2" name="whatsapp_config_id" id="whatsapp_config_id" >
                        <option value="">Select</option>
                        @foreach($whatsappCon as $phone)
                            <option value="{{$phone->id}}" {{ $phone->id == old('whatsapp_config_id') ? 'selected' : '' }}>{{$phone->number}}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('whatsapp_config_id'))
                        <div class="alert alert-danger">{{$errors->first('whatsapp_config_id')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Asset Type:</strong>
                    <select class="form-control" name="asset_type" id="asset_asset_type">
                        <option value="">Select</option>
                        <option value="Hard" {{ Input::old('asset_type') == 'Hard'? 'selected' : '' }}>Hard</option>
                        <option value="Soft" {{ Input::old('asset_type') == 'Soft'? 'selected' : '' }}>Soft</option>
                    </select>
                    @if ($errors->has('asset_type'))
                        <div class="alert alert-danger">{{$errors->first('asset_type')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Category:</strong>
                    <select class="form-control" name="category_id" id="category_id2">
                        <option value="">Select</option>
                        @foreach($assets_category as $cat)
                            <option value="{{$cat->id}}" {{ $cat->id == old('category_id') ? 'selected' : '' }}>{{$cat->cat_name}}</option>
                        @endforeach
                        <option value="-1" {{ old('category_id') == '-1'? 'selected' : '' }}>Other</option>
                    </select>
                    @if ($errors->has('category_id'))
                        <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3 othercatedit" style="display: none;">
                <div class="form-group">
                    <strong>Other Category:</strong>
                    <input type="text" name="other" class="form-control" value="{{ old('other') }}">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Provider Name:</strong>
                    <input type="text" name="provider_name" class="form-control" value="{{ old('provider_name') }}" id="provider_name" required>

                    @if ($errors->has('provider_name'))
                        <div class="alert alert-danger">{{$errors->first('provider_name')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Purchase Type:</strong>
                    <select class="form-control" name="purchase_type" id="asset_purchase_type">
                        <option value="">Select</option>
                        <option value="Owned" {{ old('purchase_type') == 'Owned'? 'selected' : '' }}>Owned</option>
                        <option value="Rented" {{ old('purchase_type') == 'Rented'? 'selected' : '' }}>Rented</option>
                        <option value="Subscription" {{ old('purchase_type') == 'Subscription'? 'selected' : '' }}>Subscription</option>
                    </select>
                    @if ($errors->has('purchase_type'))
                        <div class="alert alert-danger">{{$errors->first('purchase_type')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Payment Cycle:</strong>
                    <select class="form-control" name="payment_cycle" id="asset_payment_cycle">
                        <option value="">Select</option>
                        <option value="Daily" {{ old('payment_cycle') == 'Daily'? 'selected' : '' }}>Daily</option>
                        <option value="Weekly" {{ old('payment_cycle') == 'Weekly'? 'selected' : '' }}>Weekly</option>
                        <option value="Bi-Weekly" {{ old('payment_cycle') == 'Bi-Weekly'? 'selected' : '' }}>Bi-Weekly</option>
                        <option value="Monthly" {{ old('payment_cycle') == 'Monthly'? 'selected' : '' }}>Monthly</option>
                        <option value="Yearly" {{ old('payment_cycle') == 'Yearly'? 'selected' : '' }}>Yearly</option>
                        <option value="One time" {{ old('payment_cycle') == 'One time'? 'selected' : '' }}>One time</option>
                    </select>

                    @if ($errors->has('payment_cycle'))
                        <div class="alert alert-danger">{{$errors->first('payment_cycle')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Amount:</strong>
                    <input type="number" name="amount" id="asset_amount" class="form-control" value="{{ old('amount') }}" step=".01">

                    @if ($errors->has('amount'))
                        <div class="alert alert-danger">{{$errors->first('amount')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Start Date:</strong>
                    <input type="date" name="start_date" id="start_date" class="form-control start_date" value="{{ old('start_date') }}" required>
                    <input type="hidden" name="old_start_date" id="old_start_date" class="form-control" value="{{ old('old_start_date') }}" required>

                    @if ($errors->has('start_date'))
                        <div class="alert alert-danger">{{$errors->first('start_date')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Currency:</strong>
                    <input type="text" name="currency" class="form-control" value="{{ old('currency') }}" id="currency" required>

                    @if ($errors->has('currency'))
                        <div class="alert alert-danger">{{$errors->first('currency')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Location:</strong>
                    <input type="text" name="location" class="form-control" value="{{ old('location') }}" id="location" required>

                    @if ($errors->has('location'))
                        <div class="alert alert-danger">{{$errors->first('location')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Usage:</strong>
                    <input type="text" id="usage" name="usage" class="form-control" value="{{ old('usage') }}">

                    @if ($errors->has('usage'))
                        <div class="alert alert-danger">{{$errors->first('usage')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Client Id:</strong>
                    <input type="text" id="client_id" name="client_id" class="form-control" value="{{ old('client_id') }}">

                    @if ($errors->has('client_id'))
                        <div class="alert alert-danger">{{$errors->first('client_id')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Account Username:</strong>
                    <input type="text" id="account_username" name="account_username" class="form-control" value="{{ old('account_username') }}">

                    @if ($errors->has('account_username'))
                        <div class="alert alert-danger">{{$errors->first('account_username')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Account Password:</strong>
                    <input type="text" id="account_password" name="account_password" class="form-control" value="{{ old('account_password') }}">

                    @if ($errors->has('account_password'))
                        <div class="alert alert-danger">{{$errors->first('account_password')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Monit Api URL:</strong>
                    <input type="text" name="monit_api_url" id="monit_api_url" class="form-control" value="{{ old('monit_api_url') }}" >

                    @if ($errors->has('monit_api_url'))
                        <div class="alert alert-danger">{{$errors->first('monit_api_url')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Monit Api Username:</strong>
                    <input type="text" name="monit_api_username" id="monit_api_username" class="form-control" value="{{ old('monit_api_username') }}">

                    @if ($errors->has('monit_api_username'))
                        <div class="alert alert-danger">{{$errors->first('monit_api_username')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <strong>Monit Api Password:</strong>
                    <input type="text" name="monit_api_password" id="monit_api_password" class="form-control" value="{{ old('monit_api_password') }}">

                    @if ($errors->has('monit_api_password'))
                        <div class="alert alert-danger">{{$errors->first('monit_api_password')}}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-secondary">Update</button>
    </div>
</form>