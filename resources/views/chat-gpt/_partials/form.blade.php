<form id="add-group-form" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group row">
        <label for="headline1" class="col-sm-2 col-form-label">Type</label>
        <div class="col-sm-10">
            <select name="type" id="type" class="form-control" onchange="updateFields()">
                <option value="">Select</option>
                <option value="models">Models List</option>
                <option value="completions">Completions</option>
                <option value="edits">Edits</option>
                <option value="image_generate">Image Generate</option>
                <option value="image_edit">Image Edit</option>
                <option value="image_variation">Image Variation</option>
                <option value="moderations">Moderations</option>
            </select>
            @if ($errors->has('type'))
                <span class="text-danger">{{$errors->first('type')}}</span>
            @endif
        </div>
    </div>
    <div id="completions" style="display: none">
        @include('chat-gpt._partials.completion')
    </div>
    <div id="edits" style="display: none">
        @include('chat-gpt._partials.edits')
    </div>
    <div id="image_generate" style="display: none">
        @include('chat-gpt._partials.generate_image')
    </div>
    <div id="image_edit" style="display: none">
        @include('chat-gpt._partials.edit_image')
    </div>
    <div id="image_variation" style="display: none">
        @include('chat-gpt._partials.variation_image')
    </div>
    <div id="moderations" style="display: none">
        @include('chat-gpt._partials.moderation')
    </div>
    <div class="d-flex align-items-center">
        <input value="true" name="regenerate" type="checkbox" id="regenerate" class="m-0 mr-2">
        <label class="m-0" for="regenerate">Regenerate Response</label>
    </div>
    <div class="modal-footer">
        <button type="button" onclick="getResponse()" class="float-right custom-button btn">Create</button>
    </div>
</form>
