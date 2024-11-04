<div id="passwordEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->

        <div class="modal-content">
            <form action="{{ route('update.password') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title">Store a Password</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Website:</strong>
                        <input type="text" name="website" id="pass-website" class="form-control">

                        @if ($errors->has('website'))
                            <div class="alert alert-danger">{{$errors->first('website')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>URL:</strong>
                        <input type="text" name="url" id="pass-url" class="form-control" required>

                        @if ($errors->has('url'))
                            <div class="alert alert-danger">{{$errors->first('url')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Username:</strong>
                        <input type="text" name="username" id="pass-username" class="form-control" required>

                        @if ($errors->has('username'))
                            <div class="alert alert-danger">{{$errors->first('username')}}</div>
                        @endif
                    </div>
                        <input type="hidden" name="id" id="passwordsId"/>
                    <div class="form-group">
                        <strong>Password:</strong> <a href="javascript:void(0);" class="generatepasswordedit" style=" float: right;">Generate Password</a>
                        <input type="text" name="password" id="pass-password" class="form-control password-edit" required>

                        @if ($errors->has('password'))
                            <div class="alert alert-danger" >{{$errors->first('password')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Registered With:</strong>
                        <input type="text" name="registered_with" id="pass-registered_with" class="form-control">

                        @if ($errors->has('password'))
                            <div class="alert alert-danger" >{{$errors->first('password')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <input type="checkbox" class="check" value="1" name="send_message"> Send Via WhatsApp
                    </div>
                    <div class="form-group users">
                        <select class="form-control" name="user_id">
                            @foreach($users as $user)
                            <option class="form-control" value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
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