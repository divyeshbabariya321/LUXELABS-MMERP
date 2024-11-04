<form id="update-group-form" method="POST"
      action="{{route('pinterest.accounts.board.update', [$pinterestBusinessAccountMail->id])}}">
    @csrf
    <input type="hidden" name="edit_board_id" id="edit_board_id" value="">
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Ads Account</label>
        <div class="col-sm-10">
            <select name="edit_pinterest_ads_account_id" id="edit_pinterest_ads_account_id" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestAdsAccount as $key => $adsAccount)
                    <option value="{{$key}}">{{$adsAccount}}</option>
                @endforeach
            </select>
            @if ($errors->has('edit_pinterest_ads_account_id'))
                <span class="text-danger">{{$errors->first('edit_pinterest_ads_account_id')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="edit_name" name="edit_name"
                   placeholder="Name" value="{{ old('edit_name') }}">
            @if ($errors->has('edit_name'))
                <span class="text-danger">{{$errors->first('edit_name')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="edit_description" name="edit_description"
                      placeholder="Description">{{ old('edit_description') }}</textarea>
            @if ($errors->has('edit_description'))
                <span class="text-danger">{{$errors->first('edit_description')}}</span>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Update</button>
    </div>
</form>
