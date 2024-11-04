<div id="vendorCreateModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ url('vendors/store') }}" method="POST" id="createVendorForm">
        @csrf

        <input type="hidden" id="vendor_organization_id" name="organization_id">
        <div class="modal-header">
          <h4 class="modal-title">Store a Vendor</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="modal-body">
              <div class="col-md-6">
                <div class="form-group">
                  <select class="form-control" name="category_id" placeholder="Category:">
                    <option value="">Select a Category</option>
                    @foreach ($vendor_categories as $category)
                    <option value="{{ $category->id }}" {{ $category->id == old('category_id') ? 'selected' : '' }}>{{ $category->title }}</option>
                    @endforeach
                  </select>
                  @if ($errors->has('category_id'))
                  <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <select class="form-control" name="type" placeholder="Type:">
                    <option value="">Select a Type</option>
                    <option value="Freelancer">Freelancer</option>
                    <option value="Agency">Agency</option>
                  </select>
                  @if ($errors->has('type'))
                  <div class="alert alert-danger">{{$errors->first('type')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                    <label for="body_framework" class="label-btn">Frameworks
                      <button type="button" class="add-framework" data-toggle="modal" data-target="#addFramewrokModel">Add Framework</button>
                    </label>
                    <select name="framework[]" value="" class="form-control select-multiple-f selectpicker" id="framework" multiple>
                      @foreach ($frameworkVer as $fVer)
                        <option value="{{$fVer->id}}">{{$fVer->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="name" class="form-control" placeholder="Name:" value="{{ old('name') }}" required>
                  @if ($errors->has('name'))
                  <div class="alert alert-danger">{{$errors->first('name')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="address" class="form-control" placeholder="Address:" value="{{ old('address') }}">
                  @if ($errors->has('address'))
                  <div class="alert alert-danger">{{$errors->first('address')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="number" name="phone" class="form-control" placeholder="Phone:" value="{{ old('phone') }}">
                  @if ($errors->has('phone'))
                  <div class="alert alert-danger">{{$errors->first('phone')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="email" name="email" class="form-control" placeholder="Email:" value="{{ old('email') }}"> @if ($errors->has('email'))
                  <div class="alert alert-danger">{{$errors->first('email')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="email" name="gmail" class="form-control" placeholder="Gmail:" value="{{ old('gmail') }}"> @if ($errors->has('gmail'))
                  <div class="alert alert-danger">{{$errors->first('gmail')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="social_handle" class="form-control" placeholder="Social Handle:" value="{{ old('social_handle') }}">
                  @if ($errors->has('social_handle'))
                  <div class="alert alert-danger">{{$errors->first('social_handle')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="website" class="form-control" placeholder="Website:" value="{{ old('website') }}">
                  @if ($errors->has('website'))
                  <div class="alert alert-danger">{{$errors->first('website')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="login" class="form-control" placeholder="Login:" value="{{ old('login') }}">
                  @if ($errors->has('login'))
                  <div class="alert alert-danger">{{$errors->first('login')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="password" name="password" class="form-control" placeholder="Password:" value="{{ old('password') }}">
                  @if ($errors->has('password'))
                  <div class="alert alert-danger">{{$errors->first('password')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="gst" class="form-control" placeholder="GST:" value="{{ old('gst') }}">
                  @if ($errors->has('gst'))
                  <div class="alert alert-danger">{{$errors->first('gst')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="account_name" placeholder="Account Name:" class="form-control" value="{{ old('account_name') }}">
                  @if ($errors->has('account_name'))
                  <div class="alert alert-danger">{{$errors->first('account_name')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="account_iban" placeholder="IBAN:" class="form-control" value="{{ old('account_iban') }}">
                  @if ($errors->has('account_iban'))
                  <div class="alert alert-danger">{{$errors->first('account_iban')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="account_swift" class="form-control" placeholder="SWIFT:" value="{{ old('account_swift') }}">
                  @if ($errors->has('account_swift'))
                  <div class="alert alert-danger">{{$errors->first('account_swift')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="bank_name" placeholder="Bank Name:" class="form-control" value="{{ old('bank_name') }}">
                  @if ($errors->has('bank_name'))
                  <div class="alert alert-danger">{{$errors->first('bank_name')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <textarea name="bank_address" placeholder="Bank Address:" class="form-control" style="height:34px">{{ old('bank_address') }}</textarea>
                  @if ($errors->has('bank_address'))
                  <div class="alert alert-danger">{{$errors->first('bank_address')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="city" class="form-control" placeholder="City:" value="{{ old('city') }}">
                  @if ($errors->has('city'))
                  <div class="alert alert-danger">{{$errors->first('city')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="country" class="form-control" placeholder="Country:" value="{{ old('country') }}">
                  @if ($errors->has('country'))
                  <div class="alert alert-danger">{{$errors->first('country')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="ifsc_code" class="form-control" placeholder="IFSC:" value="{{ old('ifsc_code') }}">
                  @if ($errors->has('ifsc_code'))
                  <div class="alert alert-danger">{{$errors->first('ifsc_code')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                    <label for="body_framework" class="label-btn">Frequency of Payment
                      <button type="button" class="add-frequency" data-toggle="modal" data-target="#addFrequencyModel">Add Frequency</button>
                    </label>
                    <select name="frequency_of_payment[]" value="" class="form-control select-multiple-ff selectpicker" id="frequency_of_payment_update" multiple>
                      @foreach ($vendorFrequency as $fVer)
                        <option value="{{$fVer->id}}">{{$fVer->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                        <select name="currency" value="" class="form-control" id="currency">
                            <option value="">Select a currency</option>
                      @foreach ($currency as $curr)
                        <option value="{{$curr->name}}">{{$curr->name}}</option>
                      @endforeach
                    </select>                    
                    </div>
                </div>
                <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="price" class="form-control" placeholder="Price:" value="{{ old('price') }}">
                  @if ($errors->has('price'))
                  <div class="alert alert-danger">{{$errors->first('price')}}</div>
                  @endif
                </div>
              </div>
             <div class="col-md-12">
                <div class="form-group">
                  <input type="text" name="price_remarks" class="form-control" placeholder="Price Remark:" value="{{ old('price_remarks') }}">
                  @if ($errors->has('price_remarks'))
                  <div class="alert alert-danger">{{$errors->first('price_remarks')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <textarea name="remark" class="form-control" placeholder="Remark:" style="height:34px;">{{ old('remark') }}</textarea>
                  @if ($errors->has('remark'))
                  <div class="alert alert-danger">{{$errors->first('remark')}}</div>
                  @endif
                </div>
              </div>
              <div class="add-vendor-div"></div>
              <div class="col-md-12">
                <div class="form-group text-right">
                  <button class="btn btn-success" type="button" onclick="addVendor()" title="Add Vendor"><i class="fa fa-plus"></i></button>
                  <input type="hidden" id="vendor_count" name="vendor_count" value="" />
                </div>
              </div>
              <div class="col-md-12">
                <div class="col-md-4">
                  <div class="form-group d-flex">
                    <span>Create User:</span>
                    <input type="checkbox" name="create_user" class="">
                    @if ($errors->has('create_user'))
                    <div class="alert alert-danger">{{$errors->first('create_user')}}</div>
                    @endif
                  </div>
                </div>
                <div class="col-md-4 d-flex">
                  <div class="form-group">
                    <span>Invite (Github):</span>
                    <input type="checkbox" name="create_user_github" class="">
                    @if ($errors->has('create_user'))
                    <div class="alert alert-danger">{{$errors->first('create_user_github')}}</div>
                    @endif
                  </div>
                </div>
                <div class="col-md-4 d-flex">
                  <div class="form-group d-flex">
                    <span>Invite (Hubstaff):</span>
                    <input type="checkbox" name="create_user_hubstaff" class="">
                    @if ($errors->has('create_user'))
                    <div class="alert alert-danger">{{$errors->first('create_user_hubstaff')}}</div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-secondary">Add</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="vendorEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h4 class="modal-title">Update a Vendor</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <select class="form-control" name="category_id" id="vendor_category" placholder="Category:">
                  <option value="">Select a Category</option>
                  @foreach ($vendor_categories as $category)
                  <option value="{{ $category->id }}" {{ $category->id == old('category_id') ? 'selected' : '' }}>{{ $category->title }}</option>
                  @endforeach
                </select>
                @if ($errors->has('category_id'))
                <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <select class="form-control" name="type" placeholder="Type:" id="vendor_type">
                    <option value="">Select a Type</option>
                    <option value="Freelancer" {{ old('type')=="Freelancer" ? 'selected' : '' }}>Freelancer</option>
                    <option value="Agency" {{ old('type')=="Agency" ? 'selected' : '' }}>Agency</option>
                  </select>
                  @if ($errors->has('type'))
                  <div class="alert alert-danger">{{$errors->first('type')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                    <label for="body_framework" class="label-btn">Frameworks
                      <button type="button" class="add-framework" data-toggle="modal" data-target="#addFramewrokModel">Add Framework</button>
                    </label>
                    <select name="framework[]" value="" class="form-control select-multiple-f selectpicker" id="framework_update" multiple>
                      @foreach ($frameworkVer as $fVer)
                        <option value="{{$fVer->id}}" {{ $fVer->id == old('framework') ? 'selected' : '' }}>{{$fVer->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required id="vendor_name" placeholder="Name:">@if ($errors->has('name'))
                <div class="alert alert-danger">{{$errors->first('name')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="address" class="form-control" value="{{ old('address') }}" id="vendor_address" placeholder="Address:">
                @if ($errors->has('address'))
                <div class="alert alert-danger">{{$errors->first('address')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" id="vendor_phone" placeholder="Phone:">
                @if ($errors->has('phone'))
                <div class="alert alert-danger">{{$errors->first('phone')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" id="vendor_email" placeholder="Email:">
                @if ($errors->has('email'))
                <div class="alert alert-danger">{{$errors->first('email')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="social_handle" class="form-control" value="{{ old('social_handle') }}" id="vendor_social_handle" placeholder="Social Handle:">
                @if ($errors->has('social_handle'))
                <div class="alert alert-danger">{{$errors->first('social_handle')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="website" class="form-control" value="{{ old('website') }}" id="vendor_website" placeholder="Website:">
                @if ($errors->has('website'))
                <div class="alert alert-danger">{{$errors->first('website')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="login" class="form-control" value="{{ old('login') }}" id="vendor_login" placeholder="Login:">
                @if ($errors->has('login'))
                <div class="alert alert-danger">{{$errors->first('login')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="password" name="password" class="form-control" value="{{ old('password') }}" id="vendor_password" placeholder="Password:">
                @if ($errors->has('password'))
                <div class="alert alert-danger">{{$errors->first('password')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="gst" class="form-control" value="{{ old('gst') }}" id="vendor_gst" placeholder="GST:">
                @if ($errors->has('gst'))
                <div class="alert alert-danger">{{$errors->first('gst')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="account_name" class="form-control" value="{{ old('account_name') }}" id="vendor_account_name" placeholder="Account Name:">
                @if ($errors->has('account_name'))
                <div class="alert alert-danger">{{$errors->first('account_name')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="account_iban" class="form-control" value="{{ old('account_iban') }}" id="vendor_account_iban" placeholder="IBAN:">
                @if ($errors->has('account_iban'))
                <div class="alert alert-danger">{{$errors->first('account_iban')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="account_swift" class="form-control" value="{{ old('account_swift') }}" id="vendor_account_swift" placeholder="SWIFT:">
                @if ($errors->has('account_swift'))
                <div class="alert alert-danger">{{$errors->first('account_swift')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="body_framework" class="label-btn">Frequency of Payment
                  <button type="button" class="add-frequency" data-toggle="modal" data-target="#addFrequencyModel">Add Frequency</button>
                </label>
                <select name="frequency_of_payment[]" value="" class="form-control select-multiple-ff selectpicker" id="frequency_of_payment" multiple>
                  @foreach ($vendorFrequency as $fVer)
                    <option value="{{$fVer->id}}" {{ $fVer->id == old('frequency_of_payment') ? 'selected' : '' }}>{{$fVer->name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                    <select name="currency" value="" class="form-control" id="vendor_currency">
                        <option value="">Select a currency</option>
                  @foreach ($currency as $curr)
                    <option value="{{$curr->name}}" {{ $curr->name == old('currency') ? 'selected' : '' }}>{{$curr->name}}</option>
                  @endforeach
                </select>                    
                </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="price" class="form-control" value="{{ old('price') }}" id="vendor_price" placeholder="Price:">
                @if ($errors->has('price'))
                <div class="alert alert-danger">{{$errors->first('price')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <input type="text" name="price_remarks" class="form-control" value="{{ old('price_remarks') }}" id="vendor_price_remarks" placeholder="Price Remark:">
                @if ($errors->has('price_remarks'))
                <div class="alert alert-danger">{{$errors->first('price_remarks')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}" id="vendor_bank_name" placeholder="Bank Name:">
                @if ($errors->has('bank_name'))
                <div class="alert alert-danger">{{$errors->first('bank_name')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <textarea name="bank_address" class="form-control" placeholder="Bank Address:" id="vendor_bank_address" style="height:34px;">{{ old('bank_address') }}</textarea>
                @if ($errors->has('bank_address'))
                <div class="alert alert-danger">{{$errors->first('bank_address')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="city" class="form-control" value="{{ old('city') }}" id="vendor_city" placeholder="City:">
                @if ($errors->has('city'))
                <div class="alert alert-danger">{{$errors->first('city')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="country" class="form-control" value="{{ old('country') }}" id="vendor_country" placeholder="Country:">
                @if ($errors->has('country'))
                <div class="alert alert-danger">{{$errors->first('country')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code') }}" id="vendor_ifsc_code" placeholder="IFSC:">
                @if ($errors->has('ifsc_code'))
                <div class="alert alert-danger">{{$errors->first('ifsc_code')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <textarea name="remark" class="form-control" placeholder="Remark:" id="vendor_remark" style="height: 34px;">{{ old('remark') }}</textarea>
                @if ($errors->has('remark'))
                <div class="alert alert-danger">{{$errors->first('remark')}}</div>
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
    </div>

  </div>
</div>
<div id="addFramewrokModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Framework</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="postmanform" method="post">
              @csrf
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="frameworkName">Name</label>
                  <input type="text" name="frameworkName" required value="" class="form-control" id="frameworkName" placeholder="Enter framework Name">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-secondary vendors-addframework">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="addFrequencyModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Frequency</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="postmanform" method="post">
              @csrf
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="frequencyName">Name</label>
                  <input type="text" name="frequencyName" required value="" class="form-control" id="frequencyName" placeholder="Enter frequency Name">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-secondary vendors-addfrequency">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>