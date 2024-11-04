<form id="add-group-form" method="POST"
      action="{{route('pinterest.accounts.adsGroup.update', [$pinterestBusinessAccountMail->id])}}">
    @csrf
    <input type="hidden" value="" id="edit_ads_group_id" name="edit_ads_group_id">
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Campaigns</label>
        <div class="col-sm-10">
            <select name="edit_pinterest_campaign_id" id="edit_pinterest_campaign_id" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestCampaigns as $key => $pinterestCampaign)
                    <option value="{{$key}}">{{$pinterestCampaign}}</option>
                @endforeach
            </select>
            @if ($errors->has('edit_pinterest_campaign_id'))
                <span class="text-danger">{{$errors->first('edit_pinterest_campaign_id')}}</span>
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
    <fieldset>
        <legend class="lagend">Budget</legend>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Budget</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="edit_budget_in_micro_currency" name="edit_budget_in_micro_currency"
                       placeholder="Budget" value="{{ old('edit_budget_in_micro_currency') }}">
                @if ($errors->has('edit_budget_in_micro_currency'))
                    <span class="text-danger">{{$errors->first('edit_budget_in_micro_currency')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Bid price</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="edit_bid_in_micro_currency" name="edit_bid_in_micro_currency"
                       placeholder="Bid Price" value="{{ old('edit_bid_in_micro_currency') }}">
                @if ($errors->has('edit_bid_in_micro_currency'))
                    <span class="text-danger">{{$errors->first('edit_bid_in_micro_currency')}}</span>
                @endif
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend class="lagend">Dates</legend>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Start date</label>
            <div class="col-sm-10">
                <input type="date" class="form-control" id="edit_start_time" name="edit_start_time"
                       placeholder="Start date" value="{{ old('edit_start_time') }}">
                @if ($errors->has('edit_start_time'))
                    <span class="text-danger">{{$errors->first('edit_start_time')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">End date</label>
            <div class="col-sm-10">
                <input type="date" class="form-control" id="edit_end_time" name="edit_end_time"
                       placeholder="End date" value="{{ old('edit_end_time') }}">
                @if ($errors->has('edit_end_time'))
                    <span class="text-danger">{{$errors->first('edit_end_time')}}</span>
                @endif
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend class="lagend">Others</legend>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Lifetime frequency</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="edit_lifetime_frequency_cap" name="edit_lifetime_frequency_cap"
                       placeholder="Lifetime frequency" value="{{ old('edit_lifetime_frequency_cap') }}">
                <p class="note">Set a limit to the number of times a promoted pin from this campaign can be impressed by
                    a pinner within the past rolling 30 days</p>
                @if ($errors->has('edit_lifetime_frequency_cap'))
                    <span class="text-danger">{{$errors->first('edit_lifetime_frequency_cap')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Budget type</label>
            <div class="col-sm-10">
                <select name="edit_budget_type" id="edit_budget_type" class="form-control">
                    <option value="DAILY">DAILY</option>
                    <option value="LIFETIME">LIFETIME</option>
                </select>
                @if ($errors->has('edit_budget_type'))
                    <span class="text-danger">{{$errors->first('edit_budget_type')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Placement group</label>
            <div class="col-sm-10">
                <select name="edit_placement_group" id="edit_placement_group" class="form-control">
                    <option value="ALL">ALL</option>
                    <option value="SEARCH">SEARCH</option>
                    <option value="BROWSE">BROWSE</option>
                    <option value="OTHER">OTHER</option>
                </select>
                @if ($errors->has('edit_placement_group'))
                    <span class="text-danger">{{$errors->first('edit_placement_group')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Pacing delivery type</label>
            <div class="col-sm-10">
                <select name="edit_pacing_delivery_type" id="edit_pacing_delivery_type" class="form-control">
                    <option value="STANDARD">STANDARD</option>
                    <option value="ACCELERATED">ACCELERATED</option>
                </select>
                @if ($errors->has('edit_pacing_delivery_type'))
                    <span class="text-danger">{{$errors->first('edit_pacing_delivery_type')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Billable event</label>
            <div class="col-sm-10">
                <select name="edit_billable_event" id="edit_billable_event" class="form-control">
                    <option value="CLICKTHROUGH">CLICKTHROUGH</option>
                    <option value="IMPRESSION">IMPRESSION</option>
                    <option value="VIDEO_V_50_MRC">VIDEO_V_50_MRC</option>
                </select>
                @if ($errors->has('edit_billable_event'))
                    <span class="text-danger">{{$errors->first('edit_billable_event')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Bid strategy type</label>
            <div class="col-sm-10">
                <select name="edit_bid_strategy_type" id="edit_bid_strategy_type" class="form-control">
                    <option value="">Select</option>
                    <option value="AUTOMATIC_BID">AUTOMATIC_BID</option>
                    <option value="MAX_BID">MAX_BID</option>
                    <option value="TARGET_AVG">TARGET_AVG</option>
                </select>
                @if ($errors->has('edit_bid_strategy_type'))
                    <span class="text-danger">{{$errors->first('edit_bid_strategy_type')}}</span>
                @endif
            </div>
        </div>
    </fieldset>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Update</button>
    </div>
</form>
