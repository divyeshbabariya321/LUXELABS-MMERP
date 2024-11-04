<form id="create-form" action="{{ route('social.adset.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="modal-header">
        <h4 class="modal-title">Create Adset</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="">Config</label>
            <select class="form-control" name="config_id" required>
                @foreach($configs as $key=>$val)
                    <option value="{{$key}}">{{$val}}</option>
                @endforeach
            </select>

            @if ($errors->has('config_id'))
                <p class="text-danger">{{$errors->first('config_id')}}</p>
            @endif
        </div>
        <div class="form-group">
            <label for="">Choose Existing Campaign</label>
            <select class="form-control" name="campaign_id" id="campaign_id" required>
                <option value="">Select Campaign</option>
                @foreach ($campaingns as $campaign)
                    <option value="{{$campaign->id}}" data-objective="{{ $campaign->objective_name }}">{{$campaign->name}}</option>
                @endforeach
            </select>

            @if ($errors->has('campaign_id'))
                <p class="text-danger">{{$errors->first('campaign_id')}}</p>
            @endif
        </div>


        <div class="form-group">
            <label for="">Adset Name</label>
            <input type="text" name="name" class="form-control" placeholder="Type your Adset name" required>
            @if ($errors->has('name'))
                <p class="text-danger">{{$errors->first('name')}}</p>
            @endif
        </div>
        <div class="form-group" id="destination_type">
            <label for="">Destination Type</label>
            <select class="form-control" name="destination_type">
                <option value="UNDEFINED">Select Destination Type</option>
                <option value="WEBSITE">Website</option>
                <option value="MESSENGER">Messenger</option>
            </select>

            @if ($errors->has('destination_type'))
                <p class="text-danger">{{$errors->first('destination_type')}}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="">Optimization Goal</label>
            <select class="form-control" name="optimization_goal" id="optimization_goal">
                <option value="">Select Optimization Goal</option>
            </select>

            @if ($errors->has('optimization_goal'))
                <p class="text-danger">{{$errors->first('optimization_goal')}}</p>
            @endif
        </div>
        <div class="form-group">
            <label for="">Billing Event</label>
            <select class="form-control" name="billing_event" required>
                @foreach ($billing_events as $event)
                    <option value="{{ $event }}">{{ str_replace("_", " ", $event) }}</option>
                @endforeach
            </select>

            @if ($errors->has('billing_event'))
                <p class="text-danger">{{$errors->first('billing_event')}}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="">Start Time</label>
            <input type="date" class="form-control" name="start_time">

            @if ($errors->has('start_time'))
                <p class="text-danger">{{$errors->first('start_time')}}</p>
            @endif
        </div>
        <div class="form-group">
            <label for="">End Time</label>
            <input type="date" class="form-control" name="end_time" required>

            @if ($errors->has('end_time'))
                <p class="text-danger">{{$errors->first('end_time')}}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="">Bid Amount</label>
            <input type="number" class="form-control" name="bid_amount">
            @if ($errors->has('bid_amount'))
                <p class="text-danger">{{$errors->first('bid_amount')}}</p>
            @endif
        </div>
        <div class="form-group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="status" id="inlineRadio1" value="ACTIVE">
                <label class="form-check-label" for="inlineRadio1">ACTIVE</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" checked type="radio" name="status" id="inlineRadio2" value="PAUSED">
                <label class="form-check-label" for="inlineRadio2">PAUSED</label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-secondary">Create Adset</button>
    </div>
</form>


<script>
    $(document).ready(function() {
        var destination_type_html = $('#destination_type').html()

        $('#campaign_id').on('change', function() {
            var selectedObjective = $(this).val();
            if(selectedObjective) {
                $('#optimization_goal').empty();

                $.ajax({
                    url: "{{ url('social/adset/get-optimization-goals/') }}/" + selectedObjective,
                    type:"GET",
                   
                    dataType: "json",

                    success: function(response) {
                        if(response != null) {
                            if(response.destination_type == 0){
                                $('#destination_type').hide()
                            } else {
                                $('#destination_type').show()
                            }
                            $(response.goals).each(function(key, value) {
                                $('#optimization_goal').append('<option value="' + value + '">' + value + '</option>');
                            });
                        }
                    }
                });
            }

        });
    });
</script>