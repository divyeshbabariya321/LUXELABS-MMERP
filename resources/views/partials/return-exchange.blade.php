<form action="{{  route('return-exchange.save',[$id]) }}" method="POST" enctype="multipart/form-data" class="" id="return-exchange-form" data-reload='1'>
    @csrf
    <input type="hidden" name="customer_id" value="{{ $id }}">
    <div class="row">
        <div class="col">
            <div class="form-group">
                <strong>Order&nbsp;:&nbsp;</strong>
                <select name="order_product_id" class="form-control select-multiple" style="width: 100%;">
                    @foreach($orderData as $order)
                        <option value="{{ $order['id'] }}">{{ $order['id'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <strong>Or Product&nbsp;:&nbsp;</strong>
                <select name="product_id" class="form-control select-multiple-product" style="width: 100%;">
                    <option value='0'>- Search Product -</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="form-group">
                <strong>Type&nbsp;:&nbsp;</strong>
                <span><input type="radio" name="type" value="refund" />Refund</span>
                <span><input type="radio" name="type" value="exchange" />Exchange</span>
            </div>
        </div>
    </div>

    <div class="row refund-section" style="display: none">
        <div class="col">
            <div class="form-group">
                <strong>Reason for refund&nbsp;:&nbsp;</strong>
                <input type="text" class="form-control" name="reason_for_refund"></textarea>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <strong>Refund Amount&nbsp;:&nbsp;</strong>
                <input type="text" class="form-control" name="refund_amount"></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="form-group">
                <strong>Status&nbsp;:&nbsp;</strong>
                <select name="status" class="form-control select-multiple" style="width: 100%;">
                    @foreach($status as $key => $stat)
                        <option value="{{ $key }}">{{ $stat }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="form-group">
                <strong>Pickup Address&nbsp;:&nbsp;</strong>
                <textarea class="form-control" name="pickup_address"></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="form-group">
                <strong>Due Date&nbsp;:&nbsp;</strong>
                <input type="text" class="form-control due-date" name="est_completion_date"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-group">
                <strong>Send Email&nbsp;:&nbsp;</strong>
                <span><input type="radio" name="send_email" value="yes" checked />Yes</span>
                <span><input type="radio" name="send_email" value="no" />No</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-group">
                <strong>Remarks&nbsp;:&nbsp;</strong>
                <textarea class="form-control" name="remarks"></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary" id="btn-return-exchage-request">Submit</button>
      </div>
    </div>
</form>

@section('scripts')

@endsection
