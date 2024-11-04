<form id="add-group-form" method="POST"
      action="{{route('affiliate-marketing.provider.conversion.update', ['provider_account' => $provider->id])}}">
    @csrf
    <input type="hidden" id="provider_id" name="provider_account" value="{!! $provider->id !!}">
    <input type="hidden" id="conversion_id" name="conversion_id" value="">
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Amount</label>
        <div class="col-sm-10">
            <input type="number" class="form-control" id="edit-amount" name="amount"
                   placeholder="amount" value="{{ old('amount') }}">
            @if ($errors->has('amount'))
                <span class="text-danger">{{$errors->first('amount')}}</span>
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
