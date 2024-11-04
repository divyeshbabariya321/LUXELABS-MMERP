@php($statusList = \App\TicketStatuses::all()->pluck('name','id')->toArray())
@foreach ($data as $key => $ticket)
        @switch($ticket->status_id)
            @case('1')
                @php($textClass = 'text-info')
                @break
            @case('2')
                @php($textClass = 'text-danger')
                @break
            @case('3')
                @php($textClass = 'text-warning')
                @break
            @case('4')
                @php($textClass = 'text-success')
                @break
        @endswitch 

    <tr style="background-color: {{ !empty($ticket->ticketStatus) ? $ticket->ticketStatus->ticket_color : '#f4f4f6'}} !important;">

        <td>{{ substr($ticket->ticket_id, -5) }}</td>
        <td class="expand-row-msg" data-name="source_of_ticket" data-id="{{$ticket->id}}">
            <span class="show-short-source_of_ticket-{{$ticket->id}}">{{ Str::limit($ticket->source_of_ticket, 10, '..')}}</span>
            <span style="word-break:break-all;" class="show-full-source_of_ticket-{{$ticket->id}} hidden">{{$ticket->source_of_ticket}}</span>
        </td>
        <td class="expand-row-msg" data-name="name" data-id="{{$ticket->id}}">
            <span class="show-short-name-{{$ticket->id}}">{{ Str::limit($ticket->name, 10, '..')}}</span>
            <span style="word-break:break-all;" class="show-full-name-{{$ticket->id}} hidden">{{$ticket->name}}</span>
        </td>
        <td class="expand-row-msg" data-name="email" data-id="{{$ticket->id}}">
            <span class="show-short-email-{{$ticket->id}}">{{ Str::limit($ticket->email, 10, '..')}}</span>
            <span style="word-break:break-all;" class="show-full-email-{{$ticket->id}} hidden">{{$ticket->email}}</span>
        </td>
        <td class="expand-row-msg" data-name="subject" data-id="{{$ticket->id}}">
            <span class="show-short-subject-{{$ticket->id}}">{{ Str::limit($ticket->subject, 10, '..')}}</span>
            <span style="word-break:break-all;" class="show-full-subject-{{$ticket->id}} hidden">{{$ticket->subject}}</span>
        </td>
        <td class="expand-row-msg" data-name="message" data-id="{{$ticket->id}}">
            <span class="show-short-message-{{$ticket->id}}">{{ Str::limit($ticket->message, 10, '..')}}</span>
            <span style="word-break:break-all;" class="show-full-message-{{$ticket->id}} hidden">{{$ticket->message}}</span>
        </td>
       
        <td class="expand-row-msg" data-content="Brand : {{ !empty($ticket->brand) ? $ticket->brand : 'N/A' }}">
            <span class="show-short-type_of_inquiry-{{$ticket->id}}">{{$ticket->type_of_inquiry}}</span>
            <span style="word-break:break-all;" class="show-full-type_of_inquiry-{{$ticket->id}} hidden">{{$ticket->type_of_inquiry}}</span>
        </td>
        <td>
            @php($product = \App\Product::where('sku',$ticket->sku)->first())
            {{ $product['name'] ?? ''}}
        </td>
        <td>
            {{ $product['price'] ?? ''}}
        </td>
        <td class="{{ $textClass }}"> 
            @if (array_key_exists($ticket->status_id,$statusList))
                {{ $statusList[$ticket->status_id] }}
            @endif
        </td>
        <td>
            {{ date('d-m-Y', strtotime($ticket->created_at))}}
        </td>
        <td>
            <button type="button" class="btn btn-secondary btn-sm mt-2 update_price" data-target="#update_price" data-toggle="modal" data-id="{{ $ticket->id }}">Update Price</button>
        </td>
        <td>
            <button type="button" class="btn btn-secondary btn-sm mt-2 btn-send-notification" data-id="{{ $ticket->id }}">Send Notification</button>
        </td>
    </tr>

    <tr class="action-ticketsbtn-tr-{{$ticket->id}} d-none">
        <td class="font-weight-bold">Action</td>
    </tr>
@endforeach    

<div id="pagination-container">
    <!-- Pagination links will be dynamically loaded here -->
</div>
<!--Update Price Modal -->
<div class="modal fade" id="update_price" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">Update Price</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ticket.partials.update_product_price')
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ mix('webpack-dist/js/simulator.js') }} "></script>
<style>
    #images-carousel > img {
        margin: 5px;
    }
</style>
<script>
    $('.update_price').on('click',function(){
        $('#ticket_id').val(($(this).data('id')))
    });

    $('.update-button').on('click',function(){
        $("#loading-image").show();

    });
</script>

<script>
    $('.btn-send-notification').on('click',function(){
        var id = $(this).data('id');
        $.ajax({
            type: 'GET',
            url: "/tickets/send-notification/"+id,
            beforeSend: function () {
                $("#loading-image").show();
            },
            dataType: "json"
        }).done(function (response) {
            $("#loading-image").hide();
            if(response.code == 1){
                toastr['success'](response.message, 'success');
            }else{
                toastr['error'](response.message, 'error');
            }
        });
    });
</script>
