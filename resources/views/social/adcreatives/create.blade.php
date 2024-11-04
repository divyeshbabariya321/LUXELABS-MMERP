<form id="create-form" action="{{ route('social.adcreative.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="modal-header">
        <h4 class="modal-title">Create AdCreative</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="">Config</label>
            <select class="form-control" name="config_id" required id="config_id">
                <option value="">-----Select Config-----</option>
                @foreach($configs as $key=>$val)
                    <option value="{{$key}}">{{$val}}</option>
                @endforeach
            </select>

            @if ($errors->has('config_id'))
                <p class="text-danger">{{$errors->first('config_id')}}</p>
            @endif
        </div>
        <input type="hidden" name="object_story_title" id="object_story_title" value="" />
        <div class="form-group">
            <label for="">Choose Post</label>
            <select class="form-control" name="object_story_id" required id="post_id">
                <option value="">-----Select Post-----</option>
            </select>

            @if ($errors->has('object_story_id'))
                <p class="text-danger">{{$errors->first('object_story_id')}}</p>
            @endif
        </div>


        <div class="form-group">
            <label for="">AdCreative Name</label>
            <input type="text" name="name" class="form-control" placeholder="Type your AdCreative name" required>
            @if ($errors->has('name'))
                <p class="text-danger">{{$errors->first('name')}}</p>
            @endif
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-secondary">Create AdCreative</button>
    </div>
</form>
