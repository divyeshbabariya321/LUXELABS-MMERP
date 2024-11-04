<div>
    <div class="row">
        <div class="col-12">
            <input type="hidden" name="event_availability" value="{{$availability->id}}">
            <input type="hidden" name="event" value="{{$event->id}}">
            <div class="from-group">
                <label for="">Slot</label>
                <div class="row">
                    <div class="col-12 d-flex flex-wrap">
                        @foreach ($slots as $slot)
                            {{-- <div class="col-3"> --}}
                                <div class="form-group custom-radio d-flex align-items-center">
                                    <input type="radio" class="m-0" name="schedule-slot" value="{{$slot}}" id="schedule-slot-{{$slot}}" {{in_array($slot, $occupiedSlot) ? "disabled" : ""}} required>
                                    <label for="schedule-slot-{{$slot}}">{{$slot}}</label>
                                </div>
                            {{-- </div> --}}
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="from-group">
                <label for="guest-user-name">Name</label>
                <input type="text" id="guest-user-name" class="form-control" name="guest-user-name" required>
            </div>
            <div class="from-group">
                <label for="guest-user-email">Email</label>
                <input type="email" id="guest-user-email" class="form-control" name="guest-user-email" required>
            </div>
            <div class="from-group">
                <label for="guest-user-reark">Remark</label>
                <textarea class="form-control" name="guest-user-reark" id="guest-user-reark" cols="30" rows="10" required></textarea>
            </div>
        </div>
    </div>
</div>