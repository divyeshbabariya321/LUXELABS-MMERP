<form id="add-group-form" method="POST"
      action="{{route('pinterest.accounts.ads.create', [$pinterestBusinessAccountMail->id])}}">
    @csrf
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Ads Group</label>
        <div class="col-sm-10">
            <select name="pinterest_ad_group_id" id="pinterest_ad_group_id" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestAdsGroups as $key => $pinterestAdsGroup)
                    <option value="{{$key}}">{{$pinterestAdsGroup}}</option>
                @endforeach
            </select>
            @if ($errors->has('pinterest_ad_group_id'))
                <span class="text-danger">{{$errors->first('pinterest_ad_group_id')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Pins</label>
        <div class="col-sm-10">
            <select name="pinterest_pin_id" id="pinterest_pin_id" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestPins as $key => $pinterestPin)
                    <option value="{{$key}}">{{$pinterestPin}}</option>
                @endforeach
            </select>
            @if ($errors->has('pinterest_pin_id'))
                <span class="text-danger">{{$errors->first('pinterest_pin_id')}}</span>
            @endif
        </div>
    </div>
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
        <label for="headline1" class="col-sm-2 col-form-label">Status</label>
        <div class="col-sm-10">
            <select name="status" id="status" class="form-control">
                <option value="ACTIVE">ACTIVE</option>
                <option value="PAUSED">PAUSED</option>
                <option value="ARCHIVED">ARCHIVED</option>
            </select>
            @if ($errors->has('status'))
                <span class="text-danger">{{$errors->first('status')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Creative type</label>
        <div class="col-sm-10">
            <select name="creative_type" id="creative_type" class="form-control">
                <option value="REGULAR">REGULAR</option>
                <option value="VIDEO">VIDEO</option>
                <option value="SHOPPING">SHOPPING</option>
                <option value="CAROUSEL">CAROUSEL</option>
                <option value="MAX_VIDEO">MAX_VIDEO</option>
                <option value="SHOP_THE_PIN">SHOP_THE_PIN</option>
                <option value="IDEA">IDEA</option>
            </select>
            @if ($errors->has('creative_type'))
                <span class="text-danger">{{$errors->first('creative_type')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Destination Url</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="destination_url" name="destination_url"
                   placeholder="Destination URL" value="{{ old('destination_url') }}">
            @if ($errors->has('destination_url'))
                <span class="text-danger">{{$errors->first('destination_url')}}</span>
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
