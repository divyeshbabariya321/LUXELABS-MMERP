<div class="row">
    <div class="col-md-12 form-inline">
        <textarea rows="1" class="form-control send-message-textbox" name="message" data-customerid="{{ $customer->id }}" placeholder="Message"></textarea>
        <button class="btn btn-xs text-gray send-message-open" data-customerid="{{ $customer->id }}">
            <i class="fa fa-paper-plane"></i>
        </button>
        <button class="btn btn-xs send-with-audio-message text-gray" data-customerid="{{ $customer->id }}">
            <i class="fa fa-phone"></i>
        </button>
        <button type="button" class="btn btn-xs text-gray load-communication-modal" data-object="customer" data-load-type="text" data-limit="10" data-id="{{$customer->id}}" data-is_admin="1" data-is_hod_crm="" data-all="1" title="Load messages">
            <i class="fa fa-comments"></i>
        </button>
        @if($customer->do_not_disturb == 0)
            <button type="button" class="btn btn-xs text-gray add_to_customer_dnd" data-id="{{$customer->id}}">
                <i class="fa fa-ban"></i>
            </button>
        @else
            <button type="button" class="btn btn-xs text-gray add_to_customer_dnd" data-id="{{$customer->id}}">
                <i class="fa fa-ban" style="color: red;"></i>
            </button>
        @endif
        <a class="create-customer-ticket-modal ml-2 mr-2 text-gray " href="javascript:;" data-customer_id="{{$customer->id}}" data-user-id="{{$customer->user_id}}" data-toggle="modal" data-target="#create-customer-ticket-modal" title="Create Ticket">
            <i class="fa fa-ticket" style="color:gray"></i>
        </a>
        <button type="button" class="btn btn-xs text-gray show-customer-tickets-modal" title="Show Tickets" data-toggle="modal" data-customer_id="{{$customer->id}}" data-target="#show-customer-tickets-modal">
            <i class="fa fa-info-circle"></i>
        </button>
        
    </div>
	<div class="col-md-12 expand-row dis-none">
	</div>
</div>