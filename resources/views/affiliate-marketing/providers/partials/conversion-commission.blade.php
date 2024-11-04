<form id="add-group-form" method="POST"
      action="{{route('affiliate-marketing.provider.conversion.addCommission', ['provider_account' => $provider->id])}}">
    @csrf
    <input type="hidden" id="provider_id" name="provider_account" value="{!! $provider->id !!}">
    <input type="hidden" id="add_conversion_id" name="conversion_id" value="">
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Conversion sub amount</label>
        <div class="col-sm-10">
            <input type="number" required min="0" class="form-control" id="conversion_sub_amount"
                   name="conversion_sub_amount"
                   placeholder="Conversion sub amount" value="{{ old('conversion_sub_amount') }}">
            @if ($errors->has('conversion_sub_amount'))
                <span class="text-danger">{{$errors->first('conversion_sub_amount')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Commission type</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="commission_type" name="commission_type"
                   placeholder="Commission type" value="{{ old('commission_type') }}">
            @if ($errors->has('commission_type'))
                <span class="text-danger">{{$errors->first('commission_type')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Comment</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="comment" name="comment"
                   placeholder="Comment" value="{{ old('comment') }}">
            @if ($errors->has('comment'))
                <span class="text-danger">{{$errors->first('comment')}}</span>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Add</button>
    </div>
</form>
