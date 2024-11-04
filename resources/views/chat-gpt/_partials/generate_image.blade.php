<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Prompt</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="image_generate_prompt" name="image_generate_prompt"
               placeholder="Prompt" value="{{ old('image_generate_prompt') }}">
        <p class="note">A text description of the desired image(s). The maximum length is 1000 characters.</p>
        @if ($errors->has('image_generate_prompt'))
            <span class="text-danger">{{$errors->first('image_generate_prompt')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Number of images</label>
    <div class="col-sm-10">
        <input type="number" class="form-control" id="image_generate_n" name="image_generate_n"
               placeholder="Number of responses" value="{{ old('image_generate_n', 1) }}">
        <p class="note">
            The number of images to generate. Must be between 1 and 10.
        </p>
        @if ($errors->has('image_generate_n'))
            <span class="text-danger">{{$errors->first('image_generate_n')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Size</label>
    <div class="col-sm-10">
        <select name="image_generate_size" id="image_generate_size" class="form-control">
            <option value="1024x1024">1024x1024</option>
            <option value="512x512">512x512</option>
            <option value="256x256">256x256</option>
        </select>
        @if ($errors->has('image_generate_size'))
            <span class="text-danger">{{$errors->first('image_generate_size')}}</span>
        @endif
    </div>
</div>
<input type="hidden" value="url" name="image_generate_response_format" id="image_generate_response_format">
