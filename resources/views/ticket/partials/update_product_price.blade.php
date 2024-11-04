<form id="update-price-form" method="POST" action="{{route('ticket.update.price')}}">
    @csrf
    <div class="modal-body">
        <input type="hidden" id="ticket_id" name="ticket_id" value="" />
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Price</label>
            <div class="col-sm-10">
                <input type="number" min="1" max="9999999" class="form-control" id="product_price" name="product_price"
                       placeholder="Price" value="{{ old('product_price') }}">
                @if ($errors->has('product_price'))
                    <span class="text-danger">{{$errors->first('product_price')}}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal" aria-label="Close">Close </button>
        <button type="submit" class="float-right update-button btn">Update</button>
    </div>
</form>
