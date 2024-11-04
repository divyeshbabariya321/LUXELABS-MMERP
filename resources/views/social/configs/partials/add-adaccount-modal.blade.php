<div id="AdConfigCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('social.config.adStore') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Social Ad Account Config</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Website:</strong>
                        <select class="form-control" name="store_website_id" required>
                            <option value="0">Select Website</option>
                            @foreach($websites as $website)
                                <option value="{{ $website->id }}">{{ $website->title }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('website'))
                            <div class="alert alert-danger">{{$errors->first('website')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @if ($errors->has('name'))
                            <div class="alert alert-danger">{{$errors->first('name')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Account ID:</strong>
                        <input type="text" name="ad_account_id" class="form-control" value="{{ old('ad_account_id') }}" required>
                        @if ($errors->has('ad_account_id'))
                            <div class="alert alert-danger">{{$errors->first('ad_account_id')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>API Key:</strong>
                        <input type="text" name="api_key" class="form-control" value="{{ old('api_key') }}">

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
                        <input type="text" name="page_token" class="form-control" value="{{ old('page_token') }}">

                        @if ($errors->has('page_token'))
                            <div class="alert alert-danger">{{$errors->first('page_token')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Status:</strong>
                        <select class="form-control" name="status" required>
                            <option>Select Status</option>
                            <option value="1">Active</option>
                            <option value="2">Blocked</option>
                            <option value="0">Inactive</option>
                        </select>
                        @if ($errors->has('status'))
                            <div class="alert alert-danger">{{$errors->first('status')}}</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Store</button>
                </div>
            </form>
        </div>
    </div>
</div>
