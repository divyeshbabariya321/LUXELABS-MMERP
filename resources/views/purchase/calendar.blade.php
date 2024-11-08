@extends('layouts.app')

@section('title', 'Purchase Calendar')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

<div class="row">
  <div class="col-12 margin-tb mb-3">
    <h2 class="page-heading">Purchase Calendar</h2>

    

    <div class="pull-right">
      
    </div>

    

  </div>
</div>

<div class="text-right mb-4">
  <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#user-event-model">Create Event</button>
</div>

@include('partials.flash_messages')

<div class="row">
  <div class="col-12">
    <div id="calendar"></div>
  </div>
</div>

@include('purchase.partials.modal-calendar-detail')

@include('partials.modals.user-event-modal')

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.select2').select2();
    $('#calendar').fullCalendar({
      editable: true,
      eventColor: '#eeeeee',
      header: {
        right: "month,agendaWeek,agendaDay, today prev,next",
      },
      events: [
        @foreach($purchase_data as $purchase) {
          title: "{{ $purchase['customer_city'] }} - Customer ID {{ $purchase['customer_id'] }}",
          start: "{{ $purchase['shipment_date']}}",
          order_product_id: "{{ $purchase['order_product_id'] }}",
          customer_name: "{{ $purchase['customer_name'] }}",
          customer_city: "{{ $purchase['customer_city'] }}",
          product_name: "{{ $purchase['product_name'] }}",
          reschedule_count: "{{ $purchase['reschedule_count'] }}",
          is_order_priority: "{{ $purchase['is_order_priority'] }}",
        },
        @endforeach
      ],
      eventDrop: function(event, delta, revertFunc) {
        $.ajax({
          type: "POST",
          url: "{{ url('purchase') }}/" + event.order_product_id + "/updateDelivery",
          data: {
            _token: "{{ csrf_token() }}",
            shipment_date: event.start.format('Y-MM-DD H:mm')
          }
        }).done(function(response) {

        }).fail(function(response) {
          alert('Could not update delivery date!');
          console.log(response);
        });
      },
      eventClick: function(calEvent, jsEvent, view) {
        $('#calendar_detail_body').empty();

        // var image = '<div class="row">';
        // calEvent.image_names.forEach(function(img) {
        //   image += '<div class="col-md-4"><img src="' + img.name + '" class="img-responsive" /></div>';
        //   download_images.push(img.id);
        // });
        // image += '</div>';
        console.log(calEvent);
        var image = "<div>";
        // image += calEvent.customer_name;
        image += calEvent.customer_name + " expects " + calEvent.product_name + " to be delivered to " + calEvent.customer_city + " on " + moment(calEvent.start).format('DD-MM') + ". Rescheduled: <strong>" + calEvent.reschedule_count + "</strong> times.";
        image += '</div>';

        $('#calendar_detail_body').append($(image));

        $('#calendarDetailModal').modal('toggle');
      },
      eventRender: function(event, eventElement) {
        if (event.is_order_priority == 1) {
          eventElement.css({
            'background-color': 'rgba(163,103,126,1)',
            'color': 'white'
          });
        }
        // if (event.image_names) {
        //   event.image_names.forEach(function(image) {
        //     eventElement.find("div.fc-content").prepend("<img src='" + image.name +"' width='50' height='50'>");
        //   });
        // }
      },
    });
  });
</script>
@endsection