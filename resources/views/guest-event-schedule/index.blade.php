@extends('layouts.app')
@section('favicon' , '')

@section('title', 'Schedule event')

@section('styles')

@endsection

@section('content')
    <div class="col-md-12">
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">Schedule event</h2>
        </div>
        <div class="col-12" style="text-align: center">
            @if (\Session::has('success_data'))
                @php
                    $data = \Session::get('success_data');
                    // $data = [];
                    // $data['eventschedule'] = App\Models\EventSchedule::first();
                    // $data['eventschedule'] = App\Models\EventSchedule::first();
                    // $data['eventschedule']['schedule_date'] = Carbon\Carbon::now();
                @endphp
                <div id="success-mesage-box" class="mb-3 display-inline">
                    <h4 class="mt-0 mb-2">{{$data["message"] ?? "-"}}</h4>
                    <p class="m-0">
                        {{$data['eventschedule']["start_at"] ?? ""}} - {{$data['eventschedule']["end_at"] ?? ""}}, 
                        {{$data['eventschedule']['schedule_date']->format('l') ?? ""}},
                        {{$data['eventschedule']['schedule_date']->format('M d, Y') ?? ""}}
                    </p>
                </div>
            @endif
        </div>
        <div class="col-md-4 col-6 m-auto">
            <div class="from-group">
                <label for="" class="text-center w-100">Select Event date:</label>
                <div id="event-date" class="d-flex justify-content-center"></div>
                {{-- <input type="text" name="" class="form-control" autocomplete="off"> --}}
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="modal fade" id="guest-schedule-event" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {{-- <div class="page-header" style="width: 69%">
                    <h4>Schedule Event</h4>
                </div> --}}
                <div class="modal-header">
                    <h4 class="modal-title">Schedule Event</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="" method="POST" action="{{route('guest.create-schedule')}}">
                        @csrf
                        <input type="hidden" name="schedule-date" class="schedule-date">
                        <div class="guest-schedule-event-body">
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                                    aria-label="Close">Close
                            </button>
                            <button type="submit" class="float-right custom-button btn">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .ui-datepicker{
            font-size: 22px
        }
        .custom-radio input[type=radio]{
            position: absolute;
            left: -999px;
        }
        .custom-radio input[type=radio] + label{
            cursor: pointer;
            display: inline-block;
            margin-right: 10px;
            background: rgb(220, 220, 220);
            border-radius: 2px;
            padding: 5px 10px;
        }
        .custom-radio input[type=radio]:checked + label{
            background: rgb(154, 154, 154);
        }
        .custom-radio input[type=radio]:disabled + label{
            background: rgb(255, 255, 255);
        }
        #success-mesage-box{
            padding: 10px;
            border: 1px solid grey;
            border-radius: 5px;
            display: inline-block;
            background: #ebebeb;
            margin-top: 15px
        }
    </style>
@endsection

@section('scripts')
    <script>
        let availableDays = {{json_encode($availableDays ?? [])}};
        $(document).ready(function () {
            $("#event-date").datepicker({
                minDate: new Date("{{$event->start_date}}"),
                maxDate: new Date("{{$event->end_date}}"),
                dateFormat: 'yy-mm-dd',
                autoclose: false,
                onSelect: function(date) {
                    let scheduleDate = date;
                    $('#guest-schedule-event').modal('hide');
                    $("#guest-schedule-event .schedule-date").val(date);
                    date = new Date(date);
                    $.ajax({
                        type: "get",
                        url: "{{route('guest.schedule-event-slot')}}",
                        data: {
                            event_id: "{{$event->id}}",
                            day: date.getDay(),
                            scheduleDate
                        },
                        success: function (response) {
                            $('.guest-schedule-event-body').html(response);
                            $('#guest-schedule-event').modal('show');
                        },
                        error: function(error) {
                            alert("Something went wrong.");
                        }
                    });
                },
                beforeShowDay: function (date) {
                    if (availableDays.includes(date.getDay())) {
                        return [true, ''];
                    } else {
                        return [false, ''];
                    }
                },
                beforeShow: function(){     
                }
            });
        });
    </script>
@endsection
