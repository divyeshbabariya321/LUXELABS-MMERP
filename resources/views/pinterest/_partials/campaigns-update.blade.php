<form id="add-group-form" method="POST"
      action="{{route('pinterest.accounts.campaign.update', [$pinterestBusinessAccountMail->id])}}">
    @csrf
    <input type="hidden" id="edit_campaign_id" name="edit_campaign_id" value="">
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Ads Account</label>
        <div class="col-sm-10">
            <select name="edit_pinterest_ads_account_id" id="edit_pinterest_ads_account_id" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestAdsAccounts as $key => $pinterestAdsAccount)
                    <option value="{{$key}}">{{$pinterestAdsAccount}}</option>
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
        <legend class="lagend">Spend Capacity</legend>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Lifetime spend Capacity</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="edit_lifetime_spend_cap" name="edit_lifetime_spend_cap"
                       placeholder="Lifetime spend Capacity" value="{{ old('edit_lifetime_spend_cap') }}">
                <p class="note">Campaign total spending cap</p>
                @if ($errors->has('edit_lifetime_spend_cap'))
                    <span class="text-danger">{{$errors->first('edit_lifetime_spend_cap')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Daily spend capacity</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="edit_daily_spend_cap" name="edit_daily_spend_cap"
                       placeholder="Daily spend capacity" value="{{ old('edit_daily_spend_cap') }}">
                <p class="note">Campaign daily spending cap.</p>
                @if ($errors->has('edit_daily_spend_cap'))
                    <span class="text-danger">{{$errors->first('edit_daily_spend_cap')}}</span>
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
            <label for="headline1" class="col-sm-2 col-form-label">Summary status</label>
            <div class="col-sm-10">
                <select name="edit_summary_status" id="edit_summary_status" class="form-control">
                    <option value="RUNNING">RUNNING</option>
                    <option value="PAUSED">PAUSED</option>
                    <option value="ARCHIVED">ARCHIVED</option>
                    <option value="NOT_STARTED">NOT_STARTED</option>
                    <option value="COMPLETED">COMPLETED</option>
                    <option value="ADVERTISER_DISABLED">ADVERTISER_DISABLED</option>
                </select>
                @if ($errors->has('edit_summary_status'))
                    <span class="text-danger">{{$errors->first('edit_summary_status')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Is Daily Budget Flexible</label>
            <div class="col-sm-10 d-flex align-items-center">
                <input type="checkbox" class="m-0 mr-1" value="true" id="edit_is_flexible_daily_budgets"
                       name="edit_is_flexible_daily_budgets">
                <label class="mb-0" for="edit_is_flexible_daily_budgets">Is Daily Budget Flexible</label>
                @if ($errors->has('edit_is_flexible_daily_budgets'))
                    <span class="text-danger">{{$errors->first('edit_is_flexible_daily_budgets')}}</span>
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
