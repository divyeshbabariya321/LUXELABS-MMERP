    <form  id="create-form" action="{{ route('social.campaign.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="modal-header">
            <h4 class="modal-title">Create Campaign</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
                    <div class="form-group">
                        <label for="">Config</label>
                        <select class="form-control" name="config_id" required >
                            @foreach($configs as $key=>$val)
                            <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('config_id'))
                        <p class="text-danger">{{$errors->first('config_id')}}</p>
                        @endif
                    </div>
                 <div class="form-group">
                    <label for="">Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Type your campaign name" required>
                    @if ($errors->has('name'))
                    <p class="text-danger">{{$errors->first('name')}}</p>
                    @endif
                  </div>
                  <div class="form-group">
                        <label for="">Objective</label>
                        <select class="form-control" name="objective" required >
                            @foreach ($objectives as $objective)
                                <option value="{{ $objective }}">{{ str_replace("_", " ", $objective) }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('objective'))
                        <p class="text-danger">{{$errors->first('objective')}}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="">Buying Type</label>
                        <select class="form-control" name="buying_type" id="buying_type">
                            <option selected value="AUCTION">AUCTION</option>
                            <option value="RESERVED">RESERVED</option>

                        </select>

                        @if ($errors->has('buying_type'))
                        <p class="text-danger">{{$errors->first('buying_type')}}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="">Special Ad Categories</label>
                        <select class="form-control" name="special_ad_categories[]" id="special_ad_categories" multiple>
                            @foreach ($special_ad_categories as $special_ad_category)
                                <option value="{{ $special_ad_category }}">{{ str_replace("_", " ", $special_ad_category) }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('special_ad_categories'))
                        <p class="text-danger">{{$errors->first('special_ad_categories')}}</p>
                        @endif
                    </div>
                    <div class="form-group" id="daily_budget">
                        <label for="">Daily Budget</label>
                        <input type="number" class="form-control" name="daily_budget" min="500">
                        @if ($errors->has('daily_budget'))
                        <p class="text-danger">{{$errors->first('daily_budget')}}</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="inlineRadio1" value="ACTIVE">
                            <label class="form-check-label"  for="inlineRadio1">ACTIVE</label>
                        </div>
                        <div class="form-check form-check-inline">
                        <input class="form-check-input" checked type="radio" name="status" id="inlineRadio2" value="PAUSED">
                        <label class="form-check-label"  for="inlineRadio2">PAUSED</label>
                    </div>
               </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Create Campaign</button>
        </div>
    </form>

    <script>
        $("#special_ad_categories").select2({
            placeholder: "Select Special Ad Category"
        });

        $(document).ready(function() {
            $("#buying_type").change(function() {
                if ($(this).val() === "RESERVED") {
                    $("#daily_budget").hide();
                } else {
                    $("#daily_budget").show();
                }
            });
        });
    </script>
