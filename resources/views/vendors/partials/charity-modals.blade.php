<div id="charityCreateModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    @php
    $storewebsite = getAllStoreWebsite();
    @endphp
    <!-- Modal content-->
    <div class="modal-content"> 
      <form action="{{ route('customer.charity.post') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Store Charity</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body"> 

          <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @if ($errors->has('name'))
              <div class="alert alert-danger">{{$errors->first('name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Store Website:</strong>
            <select name="store_website_id" class="form-control" required>
              @foreach($storewebsite as $w)
              <option value="{{$w->id}}">{{$w->title}}</option>
              @endforeach
            </select>  
           
            @if ($errors->has('store_website_id'))
              <div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Address:</strong>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
            @if ($errors->has('address'))
              <div class="alert alert-danger">{{$errors->first('address')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Phone:</strong>
            <input type="number" name="phone" class="form-control" value="{{ old('phone') }}">
            @if ($errors->has('phone'))
              <div class="alert alert-danger">{{$errors->first('phone')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Email:</strong>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            @if ($errors->has('email'))
              <div class="alert alert-danger">{{$errors->first('email')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Websites (to upload)</strong>
            <div class="form-group mr-3">
              <select class="form-control globalSelect2" data-placeholder="Select Websites" data-ajax="{{ route('select2.websites',['sort'=>true]) }}" name="websites[]" multiple>
                <option value=""></option> 
              </select>
              @if ($errors->has('websites'))
                <div class="alert alert-danger">{{$errors->first('websites')}}</div>
              @endif
          </div> 
          </div>
          <div class="form-group">
            <strong>Website Stores</strong>
            <select class="form-control globalSelect2 website_stores" data-placeholder="Select Website Stores" name="website_stores[]" multiple>
                <option value=""></option> 
              </select>
              @if ($errors->has('website_stores'))
                <div class="alert alert-danger">{{$errors->first('website_stores')}}</div>
              @endif
          </div>     
          <div class="form-group">
            <strong>Social Handle:</strong>
            <input type="text" name="social_handle" class="form-control" value="{{ old('social_handle') }}">
            @if ($errors->has('social_handle'))
              <div class="alert alert-danger">{{$errors->first('social_handle')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Website:</strong>
            <input type="text" name="website" class="form-control" value="{{ old('website') }}">
            @if ($errors->has('website'))
              <div class="alert alert-danger">{{$errors->first('website')}}</div>
            @endif
          </div>  
          <div class="form-group">
            <strong>GST:</strong>
            <input type="text" name="gst" class="form-control" value="{{ old('gst') }}">
            @if ($errors->has('gst'))
              <div class="alert alert-danger">{{$errors->first('gst')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Account Name:</strong>
            <input type="text" name="account_name" class="form-control" value="{{ old('account_name') }}">
            @if ($errors->has('account_name'))
              <div class="alert alert-danger">{{$errors->first('account_name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>IBAN:</strong>
            <input type="text" name="account_iban" class="form-control" value="{{ old('account_iban') }}">
            @if ($errors->has('account_iban'))
              <div class="alert alert-danger">{{$errors->first('account_iban')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>SWIFT:</strong>
            <input type="text" name="account_swift" class="form-control" value="{{ old('account_swift') }}">
            @if ($errors->has('account_swift'))
              <div class="alert alert-danger">{{$errors->first('account_swift')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Frequency of Payment:</strong>
            <input type="text" name="frequency_of_payment" class="form-control" value="{{ old('frequency_of_payment') }}">
            @if ($errors->has('frequency_of_payment'))
              <div class="alert alert-danger">{{$errors->first('frequency_of_payment')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Bank Name:</strong>
            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
            @if ($errors->has('bank_name'))
              <div class="alert alert-danger">{{$errors->first('bank_name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Bank Address:</strong>
            <textarea name="bank_address" class="form-control">{{ old('bank_address') }}</textarea>
            @if ($errors->has('bank_address'))
              <div class="alert alert-danger">{{$errors->first('bank_address')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>City:</strong>
            <input type="text" name="city" class="form-control" value="{{ old('city') }}">
            @if ($errors->has('city'))
              <div class="alert alert-danger">{{$errors->first('city')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Country:</strong>
            <input type="text" name="country" class="form-control" value="{{ old('country') }}">
            @if ($errors->has('country'))
              <div class="alert alert-danger">{{$errors->first('country')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>IFSC:</strong>
            <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code') }}">
            @if ($errors->has('ifsc_code'))
              <div class="alert alert-danger">{{$errors->first('ifsc_code')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Remark:</strong>
            <textarea name="remark" class="form-control">{{ old('remark') }}</textarea>
            @if ($errors->has('remark'))
              <div class="alert alert-danger">{{$errors->first('remark')}}</div>
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="charityEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST">
        @csrf 

        <div class="modal-header">
          <h4 class="modal-title">Update Charity</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body"> 

          <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required id="vendor_name">
            @if ($errors->has('name'))
              <div class="alert alert-danger">{{$errors->first('name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Store Website:</strong>
            <select name="store_website_id" id="vendor_store_website_id" class="form-control" required>
              @foreach($storewebsite as $w)
              <option value="{{$w->id}}">{{$w->title}}</option>
              @endforeach
            </select>  
           
            @if ($errors->has('store_website_id'))
              <div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Address:</strong>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}" id="vendor_address">
            @if ($errors->has('address'))
              <div class="alert alert-danger">{{$errors->first('address')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Phone:</strong>
            <input type="number" name="phone" class="form-control" value="{{ old('phone') }}" id="vendor_phone">
            @if ($errors->has('phone'))
              <div class="alert alert-danger">{{$errors->first('phone')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Email:</strong>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" id="vendor_email">
            @if ($errors->has('email'))
              <div class="alert alert-danger">{{$errors->first('email')}}</div>
            @endif
          </div>
          <div class="form-group">
              <strong>Websites (for upload)</strong>
              <select class="form-control globalSelect2 websites" data-placeholder="Select Websites" name="websites[]" multiple>
                <option value=""></option> 
              </select>
          </div>  
          <div class="form-group">
              <strong>Website Stores</strong>
              <select class="form-control globalSelect2 website_stores" data-placeholder="Select Website Stores" name="website_stores[]" multiple>
                <option value=""></option> 
              </select>
          </div>     
          <div class="form-group">
            <strong>Social Handle:</strong>
            <input type="text" name="social_handle" class="form-control" value="{{ old('social_handle') }}" id="vendor_social_handle">
            @if ($errors->has('social_handle'))
              <div class="alert alert-danger">{{$errors->first('social_handle')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Website:</strong>
            <input type="text" name="website" class="form-control" value="{{ old('website') }}" id="vendor_website">
            @if ($errors->has('website'))
              <div class="alert alert-danger">{{$errors->first('website')}}</div>
            @endif
          </div> 
          <div class="form-group">
            <strong>GST:</strong>
            <input type="text" name="gst" class="form-control" value="{{ old('gst') }}" id="vendor_gst">
            @if ($errors->has('gst'))
              <div class="alert alert-danger">{{$errors->first('gst')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Account Name:</strong>
            <input type="text" name="account_name" class="form-control" value="{{ old('account_name') }}" id="vendor_account_name">
            @if ($errors->has('account_name'))
              <div class="alert alert-danger">{{$errors->first('account_name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>IBAN:</strong>
            <input type="text" name="account_iban" class="form-control" value="{{ old('account_iban') }}" id="vendor_account_iban">
            @if ($errors->has('account_iban'))
              <div class="alert alert-danger">{{$errors->first('account_iban')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>SWIFT:</strong>
            <input type="text" name="account_swift" class="form-control" value="{{ old('account_swift') }}" id="vendor_account_swift">
            @if ($errors->has('account_swift'))
              <div class="alert alert-danger">{{$errors->first('account_swift')}}</div>
            @endif
		  </div>
		  <div class="form-group">
            <strong>Frequency of Payment:</strong>
            <input type="text" name="frequency_of_payment" class="form-control" value="{{ old('frequency_of_payment') }}" id="frequency_of_payment">
            @if ($errors->has('frequency_of_payment'))
              <div class="alert alert-danger">{{$errors->first('frequency_of_payment')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Bank Name:</strong>
            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}" id="bank_name">
            @if ($errors->has('bank_name'))
              <div class="alert alert-danger">{{$errors->first('bank_name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Bank Address:</strong>
            <textarea name="bank_address" class="form-control" id="bank_address">{{ old('bank_address') }}</textarea>
            @if ($errors->has('bank_address'))
              <div class="alert alert-danger">{{$errors->first('bank_address')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>City:</strong>
            <input type="text" name="city" class="form-control" value="{{ old('city') }}" id="city">
            @if ($errors->has('city'))
              <div class="alert alert-danger">{{$errors->first('city')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Country:</strong>
            <input type="text" name="country" class="form-control" value="{{ old('country') }}" id="country">
            @if ($errors->has('country'))
              <div class="alert alert-danger">{{$errors->first('country')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>IFSC:</strong>
            <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code') }}" id="ifsc_code">
            @if ($errors->has('ifsc_code'))
              <div class="alert alert-danger">{{$errors->first('ifsc_code')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Remark:</strong>
            <textarea name="remark" class="form-control" id="remark">{{ old('remark') }}</textarea>
            @if ($errors->has('remark'))
              <div class="alert alert-danger">{{$errors->first('remark')}}</div>
            @endif
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
