<form id="update-group-form" method="POST"
      action="{{route('pinterest.accounts.boardSections.update', [$pinterestBusinessAccountMail->id])}}">
    @csrf
    <input type="hidden" name="edit_board_section_id" id="edit_board_section_id" value="">
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Boards</label>
        <div class="col-sm-10">
            <select name="edit_board_id" id="edit_board_id" class="form-control">
                <option value="">Select</option>
                @foreach($pinterestBoards as $key => $pinterestBoard)
                    <option value="{{$key}}">{{$pinterestBoard}}</option>
                @endforeach
            </select>
            @if ($errors->has('edit_board_id'))
                <span class="text-danger">{{$errors->first('edit_board_id')}}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="edit_name" name="edit_name"
                   placeholder="Name" value="{{ old('edit_name') }}">
            @if ($errors->has('edit_name'))
                <span class="text-danger">{{$errors->first('edit_name')}}</span>
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
