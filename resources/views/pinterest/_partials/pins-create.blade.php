<form id="add-group-form" method="POST"
      action="{{route('pinterest.accounts.pin.create', [$pinterestBusinessAccountMail->id])}}">
    @csrf
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Title</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="title-1" name="title"
                   placeholder="Title" value="{{ old('title') }}">
            @if ($errors->has('title'))
                <span class="text-danger">{{$errors->first('title')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="description" name="description"
                      placeholder="Description">{{ old('description') }}</textarea>
            @if ($errors->has('description'))
                <span class="text-danger">{{$errors->first('description')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Alternate Text</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="alt_text" name="alt_text"
                   placeholder="Alternate Text" value="{{ old('alt_text') }}">
            @if ($errors->has('alt_text'))
                <span class="text-danger">{{$errors->first('alt_text')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Link</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="link" name="link"
                   placeholder="Link" value="{{ old('link') }}">
            @if ($errors->has('link'))
                <span class="text-danger">{{$errors->first('link')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Board</label>
        <div class="col-sm-10">
            <select name="pinterest_board_id" id="pinterest_board_id" onchange="getSections(this, false)" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestBoards as $key => $pinterestBoard)
                    <option value="{{$key}}">{{$pinterestBoard}}</option>
                @endforeach
            </select>
            @if ($errors->has('pinterest_board_id'))
                <span class="text-danger">{{$errors->first('pinterest_board_id')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Board Sections</label>
        <div class="col-sm-10">
            <select name="pinterest_board_section_id" id="pinterest_board_section_id" class="form-control">
                <option value="">Select</option>
            </select>
            @if ($errors->has('pinterest_board_section_id'))
                <span class="text-danger">{{$errors->first('pinterest_board_section_id')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Media</label>
        <div class="col-sm-10">
            <input type="file" accept="image/*" id="media" name="media" onchange="updateValues()">
        </div>
    </div>
    <input type="hidden" name="media_source_type" value="image_base64">
    <input type="hidden" name="media_content_type" id="media_content_type" value="">
    <input type="hidden" name="media_data" id="media_data" value="">
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Create</button>
    </div>
</form>
