<form id="updateAccount-group-form" enctype="multipart/form-data" method="POST" action="{{route('google-chatbot-accounts.update')}}">
    @csrf
    <div class="modal-body">
        <input type="hidden" name="account_id" value="">
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Project Id</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="edit_project_id" name="edit_project_id"
                       placeholder="Project Id" value="{{ old('edit_project_id') }}">
                @if ($errors->has('edit_project_id'))
                    <span class="text-danger">{{$errors->first('edit_project_id')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="edit_email" name="edit_email"
                       placeholder="email address" value="{{ old('email') }}">
                @if ($errors->has('email'))
                    <span class="text-danger">{{$errors->first('email')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Site</label>
            <div class="col-sm-10">
                <select name="edit_site_id" id="" class="form-control">
                    <option value="">Select</option>
                    @foreach($store_websites as $store_website)
                        <option value="{{$store_website->id}}">{{$store_website->title}}</option>
                    @endforeach
                </select>
                @if ($errors->has('edit_site_id'))
                    <span class="text-danger">{{$errors->first('edit_site_id')}}</span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Service file</label>
            <div class="col-sm-10">
                <input type="file" accept="application/json" name="edit_service_file"/>
                @if ($errors->has('site_id'))
                    <span class="text-danger">{{$errors->first('site_id')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row d-flex align-items-center">
            <label for="headline1" class="col-sm-4 col-form-label">Set default account</label>
            <input class="m-0" type="checkbox" name="default_account" value="1">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Update</button>
    </div>
</form>
