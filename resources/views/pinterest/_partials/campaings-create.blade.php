<form id="add-group-form" method="POST"
      action="{{route('pinterest.accounts.campaign.create', [$pinterestBusinessAccountMail->id])}}">
    @csrf
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Ads Account</label>
        <div class="col-sm-10">
            <select name="pinterest_ads_account_id" id="pinterest_ads_account_id" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestAdsAccounts as $key => $pinterestAdsAccount)
                    <option value="{{$key}}">{{$pinterestAdsAccount}}</option>
                @endforeach
            </select>
            @if ($errors->has('pinterest_ads_account_id'))
                <span class="text-danger">{{$errors->first('pinterest_ads_account_id')}}</span>
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
        <legend class="lagend">Spend Capacity</legend>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Lifetime spend Capacity</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="lifetime_spend_cap" name="lifetime_spend_cap"
                       placeholder="Lifetime spend Capacity" value="{{ old('lifetime_spend_cap') }}">
                <p class="note">Campaign total spending cap</p>
                @if ($errors->has('lifetime_spend_cap'))
                    <span class="text-danger">{{$errors->first('lifetime_spend_cap')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Daily spend capacity</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="daily_spend_cap" name="daily_spend_cap"
                       placeholder="Daily spend capacity" value="{{ old('daily_spend_cap') }}">
                <p class="note">Campaign daily spending cap.</p>
                @if ($errors->has('daily_spend_cap'))
                    <span class="text-danger">{{$errors->first('daily_spend_cap')}}</span>
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
            <label for="headline1" class="col-sm-2 col-form-label">Summary status</label>
            <div class="col-sm-10">
                <select name="summary_status" id="summary_status" class="form-control">
                    <option value="RUNNING">RUNNING</option>
                    <option value="PAUSED">PAUSED</option>
                    <option value="ARCHIVED">ARCHIVED</option>
                    <option value="NOT_STARTED">NOT_STARTED</option>
                    <option value="COMPLETED">COMPLETED</option>
                    <option value="ADVERTISER_DISABLED">ADVERTISER_DISABLED</option>
                </select>
                @if ($errors->has('summary_status'))
                    <span class="text-danger">{{$errors->first('summary_status')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Objective Type</label>
            <div class="col-sm-10">
                <select name="objective_type" id="objective_type" class="form-control">
                    <option value="AWARENESS">AWARENESS</option>
                    <option value="CONSIDERATION">CONSIDERATION</option>
                    <option value="VIDEO_VIEW">VIDEO_VIEW</option>
                    <option value="WEB_CONVERSION">WEB_CONVERSION</option>
                    <option value="CATALOG_SALES">CATALOG_SALES</option>
                </select>
                @if ($errors->has('objective_type'))
                    <span class="text-danger">{{$errors->first('objective_type')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Is Daily Budget Flexible</label>
            <div class="col-sm-10 d-flex align-items-center">
                <input type="checkbox" class="m-0 mr-1" value="true" id="is_flexible_daily_budgets"
                       name="is_flexible_daily_budgets">
                <label class="mb-0" for="is_flexible_daily_budgets">Is Daily Budget Flexible</label>
                @if ($errors->has('is_flexible_daily_budgets'))
                    <span class="text-danger">{{$errors->first('is_flexible_daily_budgets')}}</span>
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
