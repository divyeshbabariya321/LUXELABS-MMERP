<div id="ConfigEditModal{{$socialConfig->id}}" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('social.config.edit') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title">Edit Config</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="{{$socialConfig->id}}">
                    <input type="hidden" id="edit_token" name="edit_token" value="{{$socialConfig->token}}">
                    <div class="form-group">
                        <strong>Website:</strong>
                        <select class="form-control" name="store_website_id">
                            <option value="0">Select Website</option>
                            @foreach($websites as $website)
                                <option value="{{ $website->id }}"
                                        @if($website->id == $socialConfig->store_website_id) selected @endif>{{ $website->title }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('website'))
                            <div class="alert alert-danger">{{$errors->first('website')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Platform:</strong>
                        <select class="form-control" name="platform" required>
                            <option value="0">Select Platform</option>
                            <option value="facebook" @if("facebook" == $socialConfig->platform) selected @endif>
                                Facebook
                            </option>
                            <option value="instagram" @if("instagram" == $socialConfig->platform) selected @endif>
                                Instagram
                            </option>

                        </select>


                    </div>
                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="name" class="form-control" value="{{ $socialConfig->name }}" required>

                        @if ($errors->has('name'))
                            <div class="alert alert-danger">{{$errors->first('name')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="">Choose Ads Manager Account</label>
                        <select class="form-control adsmanager" name="ad_account_id">
                            @foreach($ad_accounts as $ad_account)
                                <option value="{{ $ad_account['id'] }}"
                                        @if($ad_account['id'] === $socialConfig->ad_account_id) selected="selected" @endif>{{ $ad_account['name'] }}</option>
                            @endforeach
                            <option value="" disabled>Select Ads Manager</option>
                        </select>

                        @if ($errors->has('ad_account_id'))
                            <p class="text-danger">{{$errors->first('adsmanager')}}</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Page Id:</strong>
                        <input type="text" name="page_id" class="form-control" value="{{ $socialConfig->page_id }}">

                        @if ($errors->has('token'))
                            <div class="alert alert-danger">{{$errors->first('page_id')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Account Id:</strong>
                        <input type="text" name="account_id" class="form-control" value="{{ old('account_id') }}">

                        @if ($errors->has('account_id'))
                            <div class="alert alert-danger">{{$errors->first('account_id')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>API Key:</strong>
                        <input type="text" name="api_key" class="form-control" value="{{ $socialConfig->api_key }}">

                        @if ($errors->has('api_key'))
                            <div class="alert alert-danger">{{$errors->first('api_key')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>API Secret:</strong>
                        <input type="text" name="api_secret" class="form-control" value="">

                        @if ($errors->has('api_secret'))
                            <div class="alert alert-danger">{{$errors->first('api_secret')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Page Token:</strong>
                        <input type="text" name="page_token" class="form-control"
                               value="{{ $socialConfig->page_token }}">

                        @if ($errors->has('page_token'))
                            <div class="alert alert-danger">{{$errors->first('page_token')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Language of Page::</strong>
                        <select class="form-control" name="page_language">
                            <option value="0">Select language of page</option>
                            @foreach($languages as $language)
                                <option value="{{ $language->locale }}"
                                        @if($language->locale == $socialConfig->page_language) selected @endif>{{ $language->name }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('page_language'))
                            <div class="alert alert-danger">{{$errors->first('page_language')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Webhook Verify Token:</strong>
                        <input type="text" name="webhook_token" class="form-control"
                               value="{{ $socialConfig->webhook_token }}">

                        @if ($errors->has('webhook_token'))
                            <div class="alert alert-danger">{{$errors->first('webhook_token')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Status:</strong>
                        <select class="form-control" name="status">
                            <option>Select Status</option>
                            <option value="1" @if($socialConfig->status == 1) selected @endif>Active</option>
                            <option value="2" @if($socialConfig->status == 2) selected @endif>Blocked</option>
                            <option value="0" @if($socialConfig->status == 0) selected @endif>Inactive</option>
                        </select>
                        @if ($errors->has('status'))
                            <div class="alert alert-danger">{{$errors->first('status')}}</div>
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
