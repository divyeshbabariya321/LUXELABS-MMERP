<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Image</label>
    <div class="col-sm-10">
        <input type="file" name="image_variation_image" accept="image/png">
        <p class="note">The image to use as the basis for the variation(s). Must be a valid PNG file, less than 4MB, and square.</p>
        @if ($errors->has('image_variation_image'))
            <span class="text-danger">{{$errors->first('image_variation_image')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Number of images</label>
    <div class="col-sm-10">
        <input type="number" class="form-control" id="image_variation_n" name="image_variation_n"
               placeholder="Number of responses" value="{{ old('image_variation_n', 1) }}">
        <p class="note">
            The number of images to generate. Must be between 1 and 10.
        </p>
        @if ($errors->has('image_variation_n'))
            <span class="text-danger">{{$errors->first('image_variation_n')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Size</label>
    <div class="col-sm-10">
        <select name="image_variation_size" id="image_variation_size" class="form-control">
            <option value="1024x1024">1024x1024</option>
            <option value="512x512">512x512</option>
            <option value="256x256">256x256</option>
        </select>
        @if ($errors->has('image_variation_size'))
            <span class="text-danger">{{$errors->first('image_variation_size')}}</span>
        @endif
    </div>
</div>
<input type="hidden" value="url" name="image_variation_response_format" id="image_variation_response_format">
