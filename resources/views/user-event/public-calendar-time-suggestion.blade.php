@extends('layouts.app')


@section('content')
<link rel="stylesheet" href="{{ mix('webpack-dist/libs/fullcalendar/core/main.css') }} ">
<link rel="stylesheet" href="{{ URL::asset('libs/fullcalendar/daygrid/main.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('libs/fullcalendar/timegrid/main.css') }}" />

<div class="col-lg-12 margin-tb page-heading">
    <h2>Choose event preferred timing</h2>
</div>


<h4>Event details:</h4>


<div class="row">
    <div class="col-lg-4">
        {{ html()->form('POST', url('/calendar/public/event/suggest-time/' . $invitationId))->open() }}
        <div class="form-group">
            {{ html()->label('Host:', 'host') }}
            {{ html()->text('host', $attendee->event->user->name)->class('form-control')->disabled('') }}
        </div>

        <div class="form-group">
            {{ html()->label('Subject:', 'subject') }}
            {{ html()->text('subject', $attendee->event->subject)->class('form-control')->disabled('') }}
        </div>

        <div class="form-group">
            {{ html()->label('Description:', 'description') }}
            {{ html()->text('description', $attendee->event->description)->class('form-control')->disabled('') }}
        </div>
        <div class="form-group">
            {{ html()->label('Date:', 'date') }}
            {{ html()->text('date', $attendee->event->date)->class('form-control')->disabled('') }}
        </div>


        <div class="form-group">
            {{ html()->label('Time:', 'time') }}
            {{ html()->text('time', $attendee->suggested_time)->class('form-control') }}
        </div>

        <div>
            <input type="submit" class="btn btn-primary" data-dismiss="modal" value="Save" />
        </div>
        {{ html()->form()->close() }}
    </div>
</div>
@include('partials.flash_messages', [withInfo => true])

<div id="calendar"></div>


<script type="text/javascript" src="{{ mix('webpack-dist/libs/fullcalendar/core/main.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/libs/fullcalendar/daygrid/main.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/libs/fullcalendar/timegrid/main.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/libs/fullcalendar/interaction/main.js') }} "></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#time').datetimepicker({
            format: 'HH:mm'
        });
    })

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: ['timeGrid'],
            defaultView: 'timeGridDay',
            header: false,
            allDaySlot: false,
            eventSources: [{
                url: '/calendar/public/events/1',
                method: 'GET',
                failure: function() {
                    alert('there was an error while fetching events!');
                }
            }]
        });
        calendar.render();
    });
</script>

@endsection