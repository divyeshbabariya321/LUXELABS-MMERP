<div id="searchUserSchedule" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search User Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="select-user-to-check-availability">Select User</label>
                            <br>
                            <select id="select-user-to-check-availability" class="form-control w-100 mr-4">
                                <option value="" selected disabled>Select</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <span id="add-user-availability-button-container">

                            </span>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <table id="user-availability-table" class="table">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="20%" style="word-break: break-all;">From/To Date</th>
                                    <th width="15%" style="word-break: break-all;">Start/End Time</th>
                                    <th width="30%" style="word-break: break-all;">Available Days</th>
                                    <th width="15%" style="word-break: break-all;">Lunch Time</th>
                                    <th width="15%">Created at</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modalUserAvailabilityFormShortcut" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <form action="" method="post" id="addEditUserAvaibility">
          <input type="hidden" name="id" value="">
          <input type="hidden" name="user_id" id="add_avalibility_user_id">
          <div class="modal-header">
            <h4 class="modal-title">Add User Availability</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            @php
            $cls_1 = 'col-md-6';
            $cls_2 = 'col-md-6';
            @endphp

            <div class="row">
              <div class="col-md-12">
                <label>Days:</label>
                <div class="form-group mb-0">
                  <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="monday" style="height: auto;"> Monday</label>
                  <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="tuesday" required style="height: auto;"> Tuesday</label>
                  <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="wednesday" required style="height: auto;"> Wednesday</label>
                  <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="thursday" required style="height: auto;"> Thursday</label>
                  <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="friday" required style="height: auto;"> Friday</label>
                  <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="saturday" required style="height: auto;"> Saturday</label>
                  <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="sunday" required style="height: auto;"> Sunday</label>
                </div>
              </div>
            </div>

            <hr />

            <div class="row">
              <div class="{{$cls_1}}">
                <label>From Date:</label>
                <div class="form-group">
                  <div class='input-group date cls-datepicker'>
                    <input type="text" class="form-control" id="s_availability_from" name="from" value="" required />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                  </div>
                </div>
              </div>
              <div class="{{$cls_2}}">
                <label>To Date:</label>
                <div class="form-group">
                  <div class='input-group date cls-datepicker'>
                    <input type="text" class="form-control" id="s_availability_to" name="to" value="" required />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                  </div>
                </div>
              </div>
            </div>

            <hr />

            <div class="row">
              <div class="{{$cls_1}}">
                <label>From Lunch Time:</label>
                <div class="form-group">
                  <div class='input-group date cls-timepicker'>
                    <input type="text" class="form-control" id="s_availability_lunch_time_from" name="lunch_time_from" value="" required />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                  </div>
                </div>
              </div>
              <div class="{{$cls_2}}">
                <label>To Lunch Time:</label>
                <div class="form-group">
                  <div class='input-group date cls-timepicker'>
                    <input type="text" class="form-control" id="s_availability_lunch_time_to" name="lunch_time_to" value="" required />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <label>Start Time:</label>
                <div class="form-group">
                  <div class='input-group date cls-timepicker'>
                    <input type="text" class="form-control" id="s_availability_start_time" name="start_time" value="" required />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <label>End Time:</label>
                <div class="form-group">
                  <div class='input-group date cls-timepicker'>
                    <input type="text" class="form-control" id="s_availability_end_time" name="end_time" value="" required />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                  </div>
                </div>
              </div>
            </div>
            <hr />
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="funUserAvailabilitySaveShortcut(this)">Save</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<script>
    $(document).ready(function() {
        setTimeout(() => {
            $('#select-user-to-check-availability').select2();
        }, 500);
        $("#select-user-to-check-availability").change(function (e) {
            $("#user-availability-table tbody").html("<tr><td colspan='6'>Loading...</td></tr>");
            $("#add-user-availability-button-container").html("");
            funUserAvailabilitySearchShortcut();
        });
        applyDatePicker(jQuery('.cls-datepicker'));
        applyTimePicker(jQuery('.cls-timepicker'));
    });

    if (typeof funUserAvailabilitySaveShortcut !== "function") {
        function funUserAvailabilitySaveShortcut(ele) {
            let frm = jQuery(ele).closest('form');
            siteLoader(1);
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('user-avaibility.save') }}",
                type: 'POST',
                data: frm.serialize()
            }).done(function(res) {
                siteLoader(0);
                siteSuccessAlert(res);
                jQuery('#modalUserAvailabilityFormShortcut').modal('hide');
                $('#select-user-to-check-availability').trigger('change')
            }).fail(function(err) {
                siteLoader(0);
                siteErrorAlert(err);
            });
        }
    }

    if (typeof funUserAvailabilitySearchShortcut !== "function") {
        function funUserAvailabilitySearchShortcut() {
            let user_id = $("#select-user-to-check-availability").val();
            if(user_id == "" || user_id == null) {
                toastr['error']("Select user first.");
                return;
            }
            $.ajax({
                type: "get",
                url: "{{route('user-avaibility.search')}}",
                data: {
                    user_id
                },
                success: function (response) {
                    $("#user-availability-table tbody").html(response.data);
                    $("#add-user-availability-button-container").html(response.addButton);
                },
                error: function(error) {
                    $("#user-availability-table tbody").html("");
                    $("#add-user-availability-button-container").html("");
                    toastr['error']("Something went wrong.");
                }
            });
        }
    }

    if (typeof funUserAvailabilityAddShortcut !== "function") {
        function funUserAvailabilityAddShortcut(user_id = null) {
            $("#add_avalibility_user_id").val(user_id);
            let mdl = jQuery('#modalUserAvailabilityFormShortcut');
            let frm = mdl.find('form');
            frm.find('input[name="from"]').val('');
            frm.find('input[name="to"]').val('');
            frm.find('input[name="id"]').val('');
            frm.find('input[name="start_time"]').val('');
            frm.find('input[name="end_time"]').val('');
            frm.find('input[name="lunch_time"]').val('');
            frm.find('input[name="day[]"]').prop('checked', false);
            mdl.modal('show');
        }
    }
</script>
<style>
    #searchUserSchedule .select2 {
        width: 200px !important;
    }
</style>
