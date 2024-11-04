<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }} ">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/timepicker@1.14.0/jquery.timepicker.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/timepicker@1.14.0/jquery.timepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<!-- Modal -->
<div id="quick-user-event-notification-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Quick User Event Notification</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="notification-submit-form" action="{{route('calendar.event.create')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="notification-date">Date</label>
                        <input id="notification-date" name="date" class="form-control" type="text">
                        <span id="date_error" class="text-danger"></span>
                    </div>
                
                    <div class="form-group">
                        <label for="notification-time">Time</label>
                        <input id="notification-time" name="time" class="form-control" type="text">
                        <span id="time_error" class="text-danger"></span>
                    </div>
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="notification-time">Repeat</label>
                            <select name="repeat" class="form-control">
                                <option value="">Select option</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="form-group col-6 hide" id="repeat_on">
                            <label for="notification-time">Repeat on</label>
                            <select name="repeat_on" class="form-control">
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                                <option value="sunday">Sunday</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-6 hide" id="ends_on">
                            <label for="notification-time">Ends</label>
                            <select name="ends_on" class="form-control">
                                <option value="">Select option</option>
                                <option value="never">Never</option>
                                <option value="on">On</option>
                            </select>
                        </div>
                        <div class="form-group col-6 hide" id="repeat_end_date">
                            <label for="repeat_end_date">Select date</label>
                            <input id="repeat_end" name="repeat_end_date" class="form-control" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notification-subject">Subject</label>
                        <input id="notification-subject" name="subject" class="form-control" type="text">
                        <span id="subject_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="notification-description">Description</label>
                        <input id="notification-description" name="description" class="form-control" type="text">
                        <span id="description_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="notification-participants">Participants(vendor)</label>
                        <select class="form-control selectx-vendor" name="vendors[]" id="vendors" multiple="" style="width:100%">
                            @foreach ($vendorsArray as $keyVendor=>$valueVendor)
                                <option value="{{$keyVendor}}">{{$valueVendor}}</option>
                            @endforeach
                        </select>
                        <span id="vendor_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="notification-participants">Provider(user)</label>
                        <select class="form-control selectx-users" name="users[]" id="users" multiple="" style="width:100%">
                            @foreach ($usersArray as $keyUser=>$valueUser)
                                <option value="{{$keyUser}}">{{$valueUser}}</option>
                            @endforeach
                        </select>
                        <span id="user_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="timezone">Participants Time zone</label>
                        <select name="timezone" id="timezone" class="form-control">
                            <option value="">Select option</option>
                            @foreach (timezone_identifiers_list() as $zone)
                                <option value="{{$zone}}">{{$zone}}</option>
                            @endforeach
                        </select>
                        <span id="timezone_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="type">Select Type</label>
                        <select name="type" class="form-control">
                            <option value="event">For Event</option>
                            <option value="learning">For Learning</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">Cost</label>
                        <input id="cost" name="cost" class="form-control" type="text">
                    </div>
                    <div class="form-group">
                        <label for="type">Currency</label>
                        <select name="currency" class="form-control">
                                @foreach($currencyData as $currency)
                            <option value="{{$currency->code}}">{{$currency->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <button id="notification-submit" class="btn btn-secondary" type="button">Submit</button>
                    </div>
                </form>
           </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#notification-date").datetimepicker({
            format: "YYYY-MM-DD",
        });

        $("#notification-time").datetimepicker({
            format: "HH:mm",
        });

        $("#repeat_end").datetimepicker({
            format: "YYYY-MM-DD",
        });

        $(".selectx-vendor").select2({
            tags: true,
        });
        $(".selectx-users").select2({
            tags: true,
        });

        $("#notification-submit").on("click", function(e) {
            e.preventDefault();
            var formData = $('#notification-submit-form').serialize(); 
            $.ajax({
                type: "POST",
                url: $("#notification-submit-form").attr("action"),
                data: formData,
                dataType: "json",
                success: function (data) {
                    if (data.code == 200) {
                        $("#quick-user-event-notification-modal").modal("hide");
                        toastr["success"](data.message, "Message");
                    } else {
                        toastr["error"](data.message, "Message");
                    }
                },
                error: function (xhr, status, error) {
                    var errors = xhr.responseJSON;
                    $.each(errors, function (key, val) {
                        $("#" + key + "_error").text(val[0]);
                    });
                },
            });
        });
    });
    $('select[name="repeat"]').on("change", function () {
        $(this).val() == "weekly"
            ? $("#repeat_on").removeClass("hide")
            : $("#repeat_on").addClass("hide");
    });

    $('select[name="ends_on"]').on("change", function () {
    $(this).val() == "on"
        ? $("#repeat_end_date").removeClass("hide")
        : $("#repeat_end_date").addClass("hide");
    });

    $('select[name="repeat"]').on("change", function () {
    $(this).val().length > 0
        ? $("#ends_on").removeClass("hide")
        : $("#ends_on").addClass("hide");
    });
</script>
