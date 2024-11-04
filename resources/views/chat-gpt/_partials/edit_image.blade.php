<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Image</label>
    <div class="col-sm-10">
        <input type="file" name="image_edit_image" accept="image/png">
        <p class="note">The image to edit. Must be a valid PNG file, less than 4MB, and square. If mask is not provided,
            image must have transparency, which will be used as the mask.</p>
        @if ($errors->has('image_edit_image'))
            <span class="text-danger">{{$errors->first('image_edit_image')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Mask</label>
    <div class="col-sm-10">
        <input type="file" name="image_edit_mask" accept="image/png">
        <p class="note">An additional image whose fully transparent areas (e.g. where alpha is zero) indicate where
            image should be edited. Must be a valid PNG file, less than 4MB, and have the same dimensions as image.</p>
        @if ($errors->has('image_edit_mask'))
            <span class="text-danger">{{$errors->first('image_edit_mask')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Prompt</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="image_edit_prompt" name="image_edit_prompt"
               placeholder="Prompt" value="{{ old('image_edit_prompt') }}">
        <p class="note">A text description of the desired image(s). The maximum length is 1000 characters.</p>
        @if ($errors->has('image_edit_prompt'))
            <span class="text-danger">{{$errors->first('image_edit_prompt')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Number of images</label>
    <div class="col-sm-10">
        <input type="number" class="form-control" id="image_edit_n" name="image_edit_n"
               placeholder="Number of responses" value="{{ old('image_edit_n', 1) }}">
        <p class="note">
            The number of images to generate. Must be between 1 and 10.
        </p>
        @if ($errors->has('image_edit_n'))
            <span class="text-danger">{{$errors->first('image_edit_n')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Size</label>
    <div class="col-sm-10">
        <select name="image_edit_size" id="image_edit_size" class="form-control">
            <option value="1024x1024">1024x1024</option>
            <option value="512x512">512x512</option>
            <option value="256x256">256x256</option>
        </select>
        @if ($errors->has('image_edit_size'))
            <span class="text-danger">{{$errors->first('image_edit_size')}}</span>
        @endif
    </div>
</div>
<input type="hidden" value="url" name="image_edit_response_format" id="image_edit_response_format">
