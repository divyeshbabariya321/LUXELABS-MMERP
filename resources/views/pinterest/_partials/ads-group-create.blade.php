<form id="add-group-form" method="POST"
      action="{{route('pinterest.accounts.adsGroup.create', [$pinterestBusinessAccountMail->id])}}">
    @csrf
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Campaigns</label>
        <div class="col-sm-10">
            <select name="pinterest_campaign_id" id="pinterest_campaign_id" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestCampaigns as $key => $pinterestCampaign)
                    <option value="{{$key}}">{{$pinterestCampaign}}</option>
                @endforeach
            </select>
            @if ($errors->has('pinterest_campaign_id'))
                <span class="text-danger">{{$errors->first('pinterest_campaign_id')}}</span>
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
    <fieldset>
        <legend class="lagend">Budget</legend>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Budget</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="budget_in_micro_currency" name="budget_in_micro_currency"
                       placeholder="Budget" value="{{ old('budget_in_micro_currency') }}">
                @if ($errors->has('budget_in_micro_currency'))
                    <span class="text-danger">{{$errors->first('budget_in_micro_currency')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Bid price</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="bid_in_micro_currency" name="bid_in_micro_currency"
                       placeholder="Bid Price" value="{{ old('bid_in_micro_currency') }}">
                @if ($errors->has('bid_in_micro_currency'))
                    <span class="text-danger">{{$errors->first('bid_in_micro_currency')}}</span>
                @endif
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend class="lagend">Dates</legend>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Start date</label>
            <div class="col-sm-10">
                <input type="date" class="form-control" id="start_time" name="start_time"
                       placeholder="Start date" value="{{ old('start_time') }}">
                @if ($errors->has('start_time'))
                    <span class="text-danger">{{$errors->first('start_time')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">End date</label>
            <div class="col-sm-10">
                <input type="date" class="form-control" id="end_time" name="end_time"
                       placeholder="End date" value="{{ old('end_time') }}">
                @if ($errors->has('end_time'))
                    <span class="text-danger">{{$errors->first('end_time')}}</span>
                @endif
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend class="lagend">Others</legend>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Lifetime frequency</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="lifetime_frequency_cap" name="lifetime_frequency_cap"
                       placeholder="Lifetime frequency" value="{{ old('lifetime_frequency_cap') }}">
                <p class="note">Set a limit to the number of times a promoted pin from this campaign can be impressed by
                    a pinner within the past rolling 30 days</p>
                @if ($errors->has('lifetime_frequency_cap'))
                    <span class="text-danger">{{$errors->first('lifetime_frequency_cap')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Budget type</label>
            <div class="col-sm-10">
                <select name="budget_type" id="budget_type" class="form-control">
                    <option value="DAILY">DAILY</option>
                    <option value="LIFETIME">LIFETIME</option>
                </select>
                @if ($errors->has('budget_type'))
                    <span class="text-danger">{{$errors->first('budget_type')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Placement group</label>
            <div class="col-sm-10">
                <select name="placement_group" id="placement_group" class="form-control">
                    <option value="ALL">ALL</option>
                    <option value="SEARCH">SEARCH</option>
                    <option value="BROWSE">BROWSE</option>
                    <option value="OTHER">OTHER</option>
                </select>
                @if ($errors->has('placement_group'))
                    <span class="text-danger">{{$errors->first('placement_group')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Pacing delivery type</label>
            <div class="col-sm-10">
                <select name="pacing_delivery_type" id="pacing_delivery_type" class="form-control">
                    <option value="STANDARD">STANDARD</option>
                    <option value="ACCELERATED">ACCELERATED</option>
                </select>
                @if ($errors->has('pacing_delivery_type'))
                    <span class="text-danger">{{$errors->first('pacing_delivery_type')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Billable event</label>
            <div class="col-sm-10">
                <select name="billable_event" id="billable_event" class="form-control">
                    <option value="CLICKTHROUGH">CLICKTHROUGH</option>
                    <option value="IMPRESSION">IMPRESSION</option>
                    <option value="VIDEO_V_50_MRC">VIDEO_V_50_MRC</option>
                </select>
                @if ($errors->has('billable_event'))
                    <span class="text-danger">{{$errors->first('billable_event')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Bid strategy type</label>
            <div class="col-sm-10">
                <select name="bid_strategy_type" id="bid_strategy_type" class="form-control">
                    <option value="">Select</option>
                    <option value="AUTOMATIC_BID">AUTOMATIC_BID</option>
                    <option value="MAX_BID">MAX_BID</option>
                    <option value="TARGET_AVG">TARGET_AVG</option>
                </select>
                @if ($errors->has('bid_strategy_type'))
                    <span class="text-danger">{{$errors->first('bid_strategy_type')}}</span>
                @endif
            </div>
        </div>
    </fieldset>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Create</button>
    </div>
</form>
