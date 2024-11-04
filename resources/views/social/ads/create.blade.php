<form id="create-form" action="{{ route('social.ads.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-header">
        <h4 class="modal-title">Create Ad</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="">Config</label>
            <select class="form-control" name="config_id" required id="config_id">
                <option value="">select Config</option>
                @foreach($configs as $key=>$val)
                    <option value="{{$key}}">{{$val}}</option>
                @endforeach
            </select>

            @if ($errors->has('config_id'))
                <p class="text-danger">{{$errors->first('config_id')}}</p>
            @endif
        </div>
        <div class="form-group">
            <label for="">Choose Existing Adset</label>
            <select class="form-control" name="adset_id" id="adset_id" required>
                <option value="">Select Adset</option>
            </select>
            <input type="hidden" name="ad_set_name" id="ad_set_name" />

            @if ($errors->has('adset_id'))
                <p class="text-danger">{{$errors->first('adset_id')}}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="">Choose Existing Adcreative</label>
            <select class="form-control" name="adcreative_id" id="adcreative_id" required>
                <option value="">Select Adcreative</option>
            </select>
            <input type="hidden" name="ad_creative_name" id="ad_creative_name" />

            @if ($errors->has('adset_id'))
                <p class="text-danger">{{$errors->first('adset_id')}}</p>
            @endif
        </div>
        <div class="form-group">
            <label for="">Ad Name</label>
            <input type="text" name="name" class="form-control" placeholder="Type your ad name">
            @if ($errors->has('name'))
                <p class="text-danger">{{$errors->first('name')}}</p>
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
        <button type="submit" class="btn btn-secondary">Create Ad</button>
    </div>
</form>
