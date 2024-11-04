<div id="passwordCreateModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form class="password-create-form" action="{{ route('password.store') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Store a Password</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Website:</strong>
            <input type="text" name="website" class="form-control" value="{{ old('website') }}">

            @if ($errors->has('website'))
              <div class="alert alert-danger">{{$errors->first('website')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>URL:</strong>
            <input type="text" name="url" class="form-control" value="{{ old('url') }}" required>

            @if ($errors->has('url'))
              <div class="alert alert-danger">{{$errors->first('url')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Username:</strong>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
            @if ($errors->has('username'))
              <div class="alert alert-danger">{{$errors->first('username')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Password:</strong> <a href="javascript:void(0);" class="generatepasswordadd" style=" float: right;">Generate Password</a>
            <input type="text" name="password" class="form-control password-add" value="{{ old('password') }}" required>
            @if ($errors->has('password'))
              <div class="alert alert-danger">{{$errors->first('password')}}</div>
            @endif
          </div>
          <div class="form-group">
                <strong>Registered With:</strong>
                <input type="text" name="registered_with" class="form-control"  required>
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
