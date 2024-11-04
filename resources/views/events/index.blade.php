@extends('layouts.app')

@section('title', 'Events')

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/timepicker@1.14.0/jquery.timepicker.min.css">  

    <style type="text/css">
        .search_duration .select2-container, .date-range-type .select2-container {
        display: block;
    }
    </style>
@endsection

@section('content')
    <br />
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="col-md-12 pl-3 pr-3">
        <div class="row m-0">
            <div class="col-lg-12 margin-tb p-0">
                <h2 class="page-heading"> Events Lists</h2>
                <div class="mt-3 col-md-12">
                    <form action="{{route('event.public')}}" method="get" class="search">
                        <div class="col-lg-2">
                            <input class="form-control" type="text" id="search_name" placeholder="Search Name" name="search_name" value="{{ request('search_name') ?? "" }}">
                        </div>
                        <div class="col-md-2 pd-sm">
                            <select name="search_event_type" id="search_event_type" class="form-control globalSelect">
                                <option value="">-- Select Event type --</option>
                                <option value="PU" {{ (request('search_event_type') == "PU") ? "selected" : "" }}>public</option>
                                <option value="PR" {{ (request('search_event_type') == "PR") ? "selected" : "" }}>private</option>
                                <option value="ToDo" {{ (request('search_event_type') == "ToDo") ? "selected" : "" }}>Todo list</option>
                            </select>
                        </div>      
                        <div class="col-lg-2">
                            <input class="form-control" type="text" id="search_error" placeholder="Search Description" name="search_description" value="{{ request('search_description') ?? "" }}">
                        </div>
                        <div class="col-md-2 pd-sm">
                            <select name="search_duration" id="search_duration" class="form-control globalSelect">
                                <option value="">-- Select duration --</option>
                                <option value="15" {{ (request('search_duration') == "15") ? "selected" : "" }}>15min</option>
                                <option value="30" {{ (request('search_duration') == "30") ? "selected" : "" }}>30min</option>
                                <option value="45" {{ (request('search_duration') == "45") ? "selected" : "" }} >45min</option>
                                <option value="60" {{ (request('search_duration') == "60") ? "selected" : "" }}>60min</option>
                            </select>
                        </div>  
                        <div class="col-md-2 pd-sm">
                            <select name="search_date_range_type" id="search_date_range_type" class="form-control globalSelect">
                                <option value="">-- Select date range type --</option>
                                <option value="within" {{ (request('search_date_range_type') == "within") ? "selected" : "" }}>Within a date range</option>
                                <option value="indefinitely" {{ (request('search_date_range_type') == "indefinitely") ? "selected" : "" }}>Indefinitely into the future</option>
                            </select>
                        </div>        
                        <div class="col-lg-2">
                            <input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
                        </div>
            
                        <div class="col-lg-2"><br>
                            <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                               <img src="{{ asset('images/search.png') }}" alt="Search">
                           </button>
                           <a href="{{route('event.public')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                        </div>
                    </form>
                </div>
                <div class="pull-right">
                    <a class="btn btn-secondary" href="{{route('event.index')}}">Back to calendar</a>
                </div>
            </div>
            <div class="table-responsive">
                <table id="magento_list_tbl_895" class="table table-bordered table-hover">
                    <thead>
                        <th>Name</th>
                        <th>Event Type</th>
                        <th>Public Link</th>
                        <th>Description</th>
                        <th>Duration (min)</th>
                        <th>Date Range Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Created At</th>
                        <th>Remark</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                        @php 
                            if($event->deleted_at) {
                                $backgroundColor = " ";
                            } else{
                                $backgroundColor =  $event->statuscolor?->color;
                            }
                        @endphp
                        <tr style="background-color: {{ $backgroundColor }};">
                                <td> {{ $event->name }} </td>
                                <td> {{ $event->event_type == "PU" ? "Public" : "Private"}} </td>
                                <td class="expand-row"> 
                                    <span class="td-mini-container">
                                        {{ strlen($event->link) > 10 ? substr($event->link, 0, 10).'...' : $event->link }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$event->link}}
                                    </span>
                                </td>
                                <td> {{ $event->description }} </td>
                                <td> {{ $event->duration_in_min }} </td>
                                <td> {{ $event->date_range_type_full_name }} </td>
                                <td> {{ $event->start_date }} </td>
                                <td> {{ $event->end_date }} </td>
                                <td> {{ $event->created_at }} </td>
                                <td>
                                    <div style="width: 100%;">
                                      <div class="d-flex">
                                        <input type="text" name="event_remark_pop" class="form-control remark-event{{$event->id}}" placeholder="Please enter remark" style="margin-bottom:5px;width:100%;display:inline;">
                                        <button type="button" class="btn btn-sm btn-image add_event_remark" title="Send message" data-event_type="event-list" data-event_id="{{$event->id}}">
                                            <img src="{{asset('images/filled-sent.png')}}">
                                        </button>
                                      <button data-event_id="{{$event->id}}" data-event_type="event-list" class="btn btn-xs btn-image show-event-remark" title="Remark"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                      </div>
                                    </div>
                                  </td>
                                  <td style="text-align: center;">
                                    @if($event->deleted_at)
                                        Cancelled Event
                                    @else
                                        <select name="status" id="status" class="form-control" onchange="statusEventsChange(this)" data-id="{{$event->id}}" data-type="event">
                                            <option value="">Select Status</option>
                                            @foreach ($todolistStatus as $todolistStat)
                                                <option value="{{$todolistStat->id}}" @if($event->statuscolor?->id == $todolistStat->id) selected @endif>{{$todolistStat->name}}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </td>
                                <td>
                                    <i class="fa fa-calendar reschedule-event" data-id="{{ $event->id }}"></i>
                                    <i class="fa fa-trash fa-trash-bin-record" data-id="{{ $event->id }}"></i>
                                    @if ($event->date_range_type == "indefinitely")
                                    <i class="fa fa-stop-circle stop-recurring-event" data-id="{{ $event->id }}" title="Stop Recurring"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach()
                        @foreach ($todoLists as $todoList)
                        <tr style="background-color: {{$todoList->color?->color}}";>
                            <td> {{ $todoList->title }} </td>
                            <td> Todo List</td>
                            <td class="expand-row"> 
                              -
                            </td>
                            <td> {{ $todoList->subject }} </td>
                            <td> - </td>
                            <td> Within a date range</td>
                            <td> {{ $todoList->todo_date }} </td>
                            <td> {{ $todoList->todo_date }} </td>
                            <td> {{ $todoList->created_at }} </td>
                            <td>
                                <div style="width: 100%;">
                                  <div class="d-flex">
                                    <input type="text" name="event_remark_pop" class="form-control remark-event{{$todoList->id}}" placeholder="Please enter remark" style="margin-bottom:5px;width:100%;display:inline;">
                                    <button type="button" class="btn btn-sm btn-image add_event_remark" title="Send message" data-event_type="todo-list" data-event_id="{{$todoList->id}}">
                                        <img src="{{asset('images/filled-sent.png')}}">
                                    </button>
                                  <button data-event_id="{{$todoList->id}}" class="btn btn-xs btn-image show-event-remark" data-event_type="todo-list" title="Remark"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                  </div>
                                </div>
                              </td>
                            <td>
                                <select name="status" id="status" class="form-control"  onchange="statusEventsChange(this)" data-id="{{$todoList->id}}"  data-type="todo">
                                    <option  Value="">Select Status</option>
                                    @foreach ($todolistStatus as $todolistStat)
                                    <option  Value="{{$todolistStat->id}}"  @if($todoList->color?->id == $todolistStat->id)
                                        selected
                                    @endif>{{$todolistStat->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td> - </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
               @if($todoLists)
                 {{ $todoLists->appends(Request::except('page'))->links() }}
                @endif
            </div>
        </div>
    </div>
    @include('partials.modals.reschedule-event')
    <div id="loading-image-preview" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')50% 50% no-repeat;display:none;">
    </div>

    <div id="event-list-remark-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Task Remark</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width:1%;">ID</th>
                                <th style=" width: 12%">Update By</th>
                                <th style="word-break: break-all; width:12%">Remark</th>
                                <th style="width: 11%">Created at</th>
                                <th style="width: 11%">Action</th>
                            </tr>
                            </thead>
                            <tbody class="event-remark-list-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
      </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/timepicker@1.14.0/jquery.timepicker.min.js"></script> 
    <script>
        /** infinite loader **/
        var isLoading = false;
        var page = 1;
        $(document).ready(function() {
            $('.select2').select2();
            $('input.timepicker').timepicker({}); 
            $('.event-dates').datetimepicker({
                format: 'YYYY-MM-DD'
            });

            $(document).on('click', '.expand-row', function () {
                var selection = window.getSelection();
                if (selection.toString().length === 0) {
                    $(this).find('.td-mini-container').toggleClass('hidden');
                    $(this).find('.td-full-container').toggleClass('hidden');
                }
            });

            $(window).scroll(function() {
                if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                    loadMore();
                }
            });

            function loadMore() {
                if (isLoading)
                    return;
                isLoading = true;
                var $loader = $('.infinite-scroll-products-loader');
                page = page + 1;
                $.ajax({
                    url: "/event?page=" + page,
                    type: 'GET',
                    // data: $('.handle-search').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function(data) {
                        //console.log(data);
                        $loader.hide();
                        $('.infinite-scroll-pending-inner').append(data.tbody);
                        isLoading = false;
                        if (data.tbody == "") {
                            isLoading = true;
                        }
                    },
                    error: function() {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }

            $('input[name="daterange"]').daterangepicker({
				opens: 'left'
			}, function(start, end, label) {
				console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
			});

            $('#reschedule-event-submit-form select[name="date_range_type"]').on('change', function() {
                if($(this).val() == 'within') {
                    $('#reschedule-event-submit-form #event-end-date').val("");
                    $('#reschedule-event-submit-form #end-date-div').removeClass('hide')
                } else {
                    $('#reschedule-event-submit-form #end-date-div').addClass('hide');
                } 
            });

            // Reschedule Event
            $('.reschedule-event').on('click', function() {
                $('#reschedule-event-submit-form #event-id').val($(this).data("id"));
                $("#reschedule-event-modal").modal("show");
            });

            // Stop Recurring Event
            $('.stop-recurring-event').on('click', function() {
                if (confirm('Are you sure you want to stop recurring for this event?')) {
                    var $this = $(this);

                    $.ajax({
                        url: '{{ route('event.stop-recurring', '') }}/' + $this.data("id"),
                        type: 'PUT',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: $this.data("id")
                        },
                        beforeSend: function() {
                            $("#loading-image-preview").show();
                        }
                    }).done( function(response) {
                        $("#loading-image-preview").hide();
                        if(response.code == 200) {
                            toastr["success"](response.message);
                            location.reload();
                        } else{
                            toastr["error"](response.message);
                        }
                    }).fail(function(errObj) {
                        $("#loading-image-preview").hide();
                    });
                }
            });

            $(document).on("submit", "#reschedule-event-submit-form", function(e) {
                e.preventDefault();
                var $form = $(this).closest("form");
                $.ajax({
                    type: "POST",
                    url: $form.attr("action"),
                    data: $form.serialize(),
                    dataType: "json",
                    success: function(data) {
                        if (data.code == 200) {
                            $form[0].reset();
                            $("#reschedule-event-modal").modal("hide");
                            toastr['success'](data.message, 'Message');
                            location.reload();
                        } else {
                            toastr['error'](data.message, 'Message');
                        }
                    },
                    error: function(xhr, status, error) {
                        var errors = xhr.responseJSON;
                        $.each(errors, function(key, val) {
                            $("#reschedule-event-submit-form " + "#" + key + "_error").text(val[0]);
                        });
                    }
                });
            });

            $(document).on("click",".fa-trash-bin-record",function() {
                if (confirm('Are you sure you want to delete this?')) {
                    var $this = $(this);

                    $.ajax({
                        url: '{{ route('event.destroy', '') }}/' + $this.data("id"),
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: $this.data("id")
                        },
                        beforeSend: function() {
                            $("#loading-image-preview").show();
                        }
                    }).done( function(response) {
                        $("#loading-image-preview").hide();
                        if(response.code == 200) {
                            toastr["success"](response.message);
                            location.reload();
                        } else{
                            toastr["error"]('Record is unable to delete!');
                        }
                    }).fail(function(errObj) {
                        $("#loading-image-preview").hide();
                    });
                }
            });
        });
        //End load more functionality

        $(document).on("click",".add_event_remark",function(e) {
            e.preventDefault();
            var thiss = $(this);
            var event_id = $(this).data('event_id');
            var event_type = $(this).data('event_type');
            var remark = $(`.remark-event`+event_id).val();

            $.ajax({
                type: "POST",
                url: "{{ route('event.remark.add') }}",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                event_id : event_id,
                remark : remark,
                event_type : event_type,
                },
                beforeSend: function () {
                    $("#loading-image-preview").show();
                }
            }).done(function (response) {
                    $("#loading-image-preview").hide();
                    toastr['success'](response.message);
                    location.reload();
            }).fail(function (response) {
                $("#loading-image-preview").hide();
                toastr['error'](response.message);
            });
         });

    $(document).on("click",".show-event-remark",function(e) {
        e.preventDefault();
        var event_id = $(this).data('event_id');
        var event_type = $(this).data('event_type');

        $.ajax({
            type: "POST",
            url: "{{ route('event.remark.list') }}",
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            data: {
              eventId : event_id,
              event_type : event_type,
            },
            beforeSend: function () {
                $("#loading-image-preview").show();
            }
        }).done(function (response) {
                $("#loading-image-preview").hide();
                $("#event-list-remark-modal").modal("show");
                $(".event-remark-list-view").html(response.data);
                toastr['success'](response.message);
        }).fail(function (response) {
            toastr['error'](response.message);
        });
    });

    function statusEventsChange(selectElement) {
        var event_id = selectElement.getAttribute('data-id');
        var event_type = selectElement.getAttribute('data-type');
        var selectedValue = selectElement.value; 
            $.ajax({
                type: "POST",
                url: "{{ route('allevents.status.update') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": event_id,
                    "status":selectedValue,
                    "type":event_type,
                },
                dataType: "json",
                success: function(message) {
                    $c = message.length;
                    if ($c == 0) {
                        alert('No History Exist');
                    } else {
                        toastr['success'](message.message, 'success');
                        location.reload();
                    }
                },
                error: function(error) {
                    toastr['error'](error, 'error');
                }

            });

        }

    </script>
@endsection
