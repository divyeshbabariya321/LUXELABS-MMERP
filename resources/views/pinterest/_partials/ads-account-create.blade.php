<form id="add-group-form" method="POST"
      action="{{route('pinterest.accounts.adsAccount.create', [$pinterestBusinessAccountMail->id])}}">
    @csrf
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="name" name="name"
                   placeholder="Name" value="{{ old('name') }}">
            @if ($errors->has('name'))
                <span class="text-danger">{{$errors->first('name')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Country</label>
        <div class="col-sm-10">
            <select name="country" id="country" class="form-control">
                <option value="">Select</option>
                @foreach($countries as $key => $country)
                    <option value="{{$key}}">{{$country}}</option>
                @endforeach
            </select>
            @if ($errors->has('country'))
                <span class="text-danger">{{$errors->first('country')}}</span>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Create</button>
    </div>
</form>
