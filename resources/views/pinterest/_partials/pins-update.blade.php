<form id="update-group-form" method="POST"
      action="{{route('pinterest.accounts.pin.update', [$pinterestBusinessAccountMail->id])}}">
    @csrf
    <input type="hidden" name="edit_pin_id" id="edit_pin_id" value="">
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Title</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="edit_title" name="edit_title"
                   placeholder="Title" value="{{ old('edit_title') }}">
            @if ($errors->has('edit_title'))
                <span class="text-danger">{{$errors->first('edit_title')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="edit_description" name="edit_description"
                      placeholder="Description">{{ old('edit_description') }}</textarea>
            @if ($errors->has('edit_description'))
                <span class="text-danger">{{$errors->first('edit_description')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Alternate Text</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="edit_alt_text" name="edit_alt_text"
                   placeholder="Alternate Text" value="{{ old('edit_alt_text') }}">
            @if ($errors->has('edit_alt_text'))
                <span class="text-danger">{{$errors->first('edit_alt_text')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Link</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="edit_link" name="edit_link"
                   placeholder="Link" value="{{ old('edit_link') }}">
            @if ($errors->has('edit_link'))
                <span class="text-danger">{{$errors->first('edit_link')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Board</label>
        <div class="col-sm-10">
            <select name="edit_pinterest_board_id" id="edit_pinterest_board_id" onchange="getSections(this, true)" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestBoards as $key => $pinterestBoard)
                    <option value="{{$key}}">{{$pinterestBoard}}</option>
                @endforeach
            </select>
            @if ($errors->has('edit_pinterest_board_id'))
                <span class="text-danger">{{$errors->first('edit_pinterest_board_id')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Board Sections</label>
        <div class="col-sm-10">
            <select name="edit_pinterest_board_section_id" id="edit_pinterest_board_section_id" class="form-control">
                <option value="">Select</option>
            </select>
            @if ($errors->has('edit_pinterest_board_section_id'))
                <span class="text-danger">{{$errors->first('edit_pinterest_board_section_id')}}</span>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Update</button>
    </div>
</form>
