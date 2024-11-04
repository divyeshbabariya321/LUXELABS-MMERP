<form id="add-group-form" method="POST"
      action="{{route('pinterest.accounts.ads.update', [$pinterestBusinessAccountMail->id])}}">
    @csrf
    <input type="hidden" id="edit_ads_id" name="edit_ads_id" value="">
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Ads Group</label>
        <div class="col-sm-10">
            <select name="edit_pinterest_ad_group_id" id="edit_pinterest_ad_group_id" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestAdsGroups as $key => $pinterestAdsGroup)
                    <option value="{{$key}}">{{$pinterestAdsGroup}}</option>
                @endforeach
            </select>
            @if ($errors->has('edit_pinterest_ad_group_id'))
                <span class="text-danger">{{$errors->first('edit_pinterest_ad_group_id')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Pins</label>
        <div class="col-sm-10">
            <select name="edit_pinterest_pin_id" id="edit_pinterest_pin_id" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestPins as $key => $pinterestPin)
                    <option value="{{$key}}">{{$pinterestPin}}</option>
                @endforeach
            </select>
            @if ($errors->has('edit_pinterest_pin_id'))
                <span class="text-danger">{{$errors->first('edit_pinterest_pin_id')}}</span>
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
        <label for="headline1" class="col-sm-2 col-form-label">Status</label>
        <div class="col-sm-10">
            <select name="edit_status" id="edit_status" class="form-control">
                <option value="ACTIVE">ACTIVE</option>
                <option value="PAUSED">PAUSED</option>
                <option value="ARCHIVED">ARCHIVED</option>
            </select>
            @if ($errors->has('edit_status'))
                <span class="text-danger">{{$errors->first('edit_status')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Creative type</label>
        <div class="col-sm-10">
            <select name="edit_creative_type" id="edit_creative_type" class="form-control">
                <option value="REGULAR">REGULAR</option>
                <option value="VIDEO">VIDEO</option>
                <option value="SHOPPING">SHOPPING</option>
                <option value="CAROUSEL">CAROUSEL</option>
                <option value="MAX_VIDEO">MAX_VIDEO</option>
                <option value="SHOP_THE_PIN">SHOP_THE_PIN</option>
                <option value="IDEA">IDEA</option>
            </select>
            @if ($errors->has('edit_creative_type'))
                <span class="text-danger">{{$errors->first('edit_creative_type')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Destination Url</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="edit_destination_url" name="edit_destination_url"
                   placeholder="Destination URL" value="{{ old('edit_destination_url') }}">
            @if ($errors->has('edit_destination_url'))
                <span class="text-danger">{{$errors->first('edit_destination_url')}}</span>
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
