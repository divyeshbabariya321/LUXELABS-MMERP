@extends('layouts.app')


@section('content')
<link rel="stylesheet" href="{{ mix('webpack-dist/libs/fullcalendar/core/main.css') }} ">
<link rel="stylesheet" href="{{ URL::asset('libs/fullcalendar/daygrid/main.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('libs/fullcalendar/timegrid/main.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('libs/fullcalendar/list/main.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('css/user-calendar.css') }}" />

<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        <h2 class="page-heading">User Events</h2>
    </div>
</div>
@include('partials.flash_messages')
<p class="text-secondary">Calendar link:</p>
<div class="border border-light p-2 my-3">
    {{ URL::to('calendar/public/'.$link) }}  
    <button class="btn btn-secondary"  data-toggle="modal" data-target="#calanderCommonEmailModal">
        Send Email
    </button>
</div>

<div class="text-right mb-4">
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#user-event-model">Create Event</button>
</div>

<div id="calendar"></div>
<div id="calanderCommonEmailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Email</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('common.send.clanaderLinkEmail') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id">                
                <input type="hidden" name="datatype" value="multi_user">
                <input type="hidden" name="action" class="action" value="{{route('common.getmailtemplate')}}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <strong>User type</strong>
                        <select class="form-control" name="object" required id="calander_email_object">
                            <option selected disabled value="">Select</option>
                            <option value="vendor">vendor</option>
                            <option value="user">user</option>
                            <option value="supplier">supplier</option>
                            <option value="customer">customer</option>                            
                            <option value="charity">charity</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <strong>Send To</strong>
                        <select name="send_to[]" id="send_to" multiple>
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>From Mail</strong>
                        <select class="form-control" name="from_mail">
                          <?php $emailAddressArr = getEmailAddress() ?>
                          @foreach ($emailAddressArr as $emailAddress)
                            <option value="{{ $emailAddress->from_address }}">{{ $emailAddress->from_name }} - {{ $emailAddress->from_address }} </option>
                          @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Mail Templates</strong>
                        <select class="form-control getTemplateData" name="mail_template" required>
                           <option value="">Select a template</option>
                          @foreach ($mailTemplates as $mail_template)
                            <option value="{{ $mail_template->id }}">{{ $mail_template->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <strong>Subject *</strong>
                        <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" required>
                    </div>

                    <div class="form-group">
                        <strong>Message *</strong>
                        <textarea name="message" class="form-control" rows="8" cols="80" required>{{ old('message') ?? URL::to('calendar/public/'.$link) }}</textarea>
                    </div>

                    <div class="form-group">
                        <strong>Files</strong>
                        <input type="file" name="file[]" value="" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Send</button>
                </div>
            </form>
        </div>

    </div>
</div>
<style>
    .fc-button{
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .fc-button-primary:not(:disabled).fc-button-active ,.fc-button:hover{
        background-color: #5a6268;
        border-color: #5a6268;
        color: #333;
    }
    .fc-event, .fc-event-dot {
        background-color: #6c757d;
        color: white!important;
        border: 1px solid #6c757d;
    }
</style>
@include('partials.modals.user-event-modal')

<script type="text/javascript" src="{{ mix('webpack-dist/libs/fullcalendar/core/main.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/libs/fullcalendar/daygrid/main.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/libs/fullcalendar/timegrid/main.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/libs/fullcalendar/list/main.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/libs/fullcalendar/interaction/main.js') }} "></script>
<script>
    let calendar;
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
              header: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
              },
            defaultView: 'timeGridWeek',
            allDaySlot: false,
            editable: true,
            eventSources: [{
                url: '/calendar/events',
                method: 'GET',
                failure: function() {
                    alert('there was an error while fetching events!');
                }
            }],
            eventClick: function(info) {
                console.log(info);
                console.log('jsEvent', info.jsEvent);
                console.log('el', info.el);
                addOverlay(info.el);
            },
            dateClick: function(info) {
                startCreatingNewEvent(info.jsEvent.pageX, info.jsEvent.pageY, info.dateStr);
            },
            eventResize: function(info) {
                console.log(info.event);
                updateEvent(
                    info.event.id,
                    formatDate(new Date(info.event.start)),
                    formatDate(new Date(info.event.end))
                );
            },
            eventDrop: function(info) {
                console.log(info.event);
                updateEvent(
                    info.event.id,
                    formatDate(new Date(info.event.start)),
                    formatDate(new Date(info.event.end))
                );
            },
            eventMouseEnter: function(info) {
                showEventOptions(info);
            },
            eventMouseLeave: function(info) {
                hideEventOption(info);
            }
        });
        calendar.render();
    });

    function closeCreateNewEventOverlay() {
        document.getElementById('create-overlay').style = "pointer-events: none";
        document.getElementById('new-event').style.visibility = 'hidden';
        document.getElementById('new-event-start-time').value = '';
        document.getElementById('new-event-title').value = '';
    }

    function startCreatingNewEvent(x, y, startTimeString) {
        const overlay = document.getElementById('create-overlay');
        overlay.style = "pointer-events: auto";
        document.getElementById('new-event').style.visibility = 'visible';
        const newEvent = document.getElementById('new-event');
        let xPosition = x;
        if (newEvent.offsetWidth + x >= overlay.offsetWidth - 20) {
            xPosition = x - newEvent.offsetWidth;
        }
        newEvent.style.top = y;
        newEvent.style.left = xPosition;
        document.getElementById('new-event-start-time').value = startTimeString;
        console.log(newEvent.offsetWidth);
    }

    function formatDate(date) {
        let dateFormat = '';
        dateFormat += date.getFullYear() + '-';
        let month = date.getMonth() + 1;
        if (month < 10) {
            dateFormat += '0' + month + '-';
        } else {
            dateFormat += month + '-';
        }
        if (date.getDate() < 10) {
            dateFormat += '0' + date.getDate() + '-';
        } else {
            dateFormat += date.getDate() + ' ';
        }
        if (date.getHours() < 10) {
            dateFormat += '0' + date.getHours() + ':';
        } else {
            dateFormat += date.getHours() + ':';
        }
        if (date.getMinutes() < 10) {
            dateFormat += '0' + date.getMinutes() + ':';
        } else {
            dateFormat += date.getMinutes() + ':';
        }
        if (date.getSeconds() < 10) {
            dateFormat += '0' + date.getSeconds();
        } else {
            dateFormat += date.getSeconds();
        }
        return dateFormat;
    }

    function createNewEvent() {
        let start = new Date(document.getElementById('new-event-start-time').value);
        start = formatDate(start);
        const title = document.getElementById('new-event-title').value;
        console.log({
            title,
            start
        });
        const event = calendar.addEvent({
            title,
            start
        });
        console.log(event);
        closeCreateNewEventOverlay();
        const xhttp = new XMLHttpRequest();
        const formData = new FormData();
        const token = '{{ csrf_token() }}';
        const data = {
            title,
            start,
            _token: token
        };
        for (name in data) {
            formData.append(name, data[name]);
        }
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        xhttp.open("POST", "/calendar/events");
        xhttp.setRequestHeader('X-CSRF-TOKEN', token)
        xhttp.send(formData);
    }

    function updateEvent(id, start, end) {
        const xhttp = new XMLHttpRequest();
        const token = '{{ csrf_token() }}';
        const data = {
            start,
            end,
            _token: token
        };
        let urlEncodedData = "",
            urlEncodedDataPairs = [],
            name;
        for (name in data) {
            urlEncodedDataPairs.push(encodeURIComponent(name) + '=' + encodeURIComponent(data[name]));
        }
        urlEncodedData = urlEncodedDataPairs.join('&').replace(/%20/g, '+');
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        xhttp.open("PUT", "/calendar/events/" + id, true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.setRequestHeader('X-CSRF-TOKEN', token)
        xhttp.send(urlEncodedData);
    }

    function showEventOptions(info) {
        const element = document.createElement('div');
        element.className = 'event-info';
        element.innerText = "DELETE";
        element.style.position = 'absolute';
        element.style.width = '100%';
        element.style.top = '100%';
        element.style.zIndex = 10;
        element.onclick = function() {
            removeEvent(info.event);
        }
        //document.getElementById('calendar').appendChild(element);
        info.el.appendChild(element);
        console.log(element);
    }

    function removeEvent(event) {
        event.remove();
        const xhttp = new XMLHttpRequest();
        const token = '{{ csrf_token() }}';
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        xhttp.open("DELETE", "/calendar/events/" + event.id, true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.setRequestHeader('X-CSRF-TOKEN', token)
        xhttp.send();
    }

    function hideEventOption(info) {
        const elements = info.el.getElementsByClassName('event-info');
        for (let i = 0; i < elements.length; i++) {
            elements[i].remove();
        }
    }
    $("#send_to").select2({
        multiple: true,
        width: "100%"
    });
    $(document).ready(function () {
        $("#calander_email_object").change(function (e) { 
            e.preventDefault();
            $.ajax({
                type: "get",
                url: "{{route('calendar.getObjectEmail')}}",
                data: {
                    object: $(this).val()
                },
                success: function (response) {
                    $("#send_to").empty()
                    $("#send_to").select2({multiple:true, data: response.data || []})
                }
            });
        });
    });
</script>
@endsection