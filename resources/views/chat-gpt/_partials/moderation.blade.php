<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Model</label>
    <div class="col-sm-10">
        <select name="moderations_model" id="moderations_model" class="form-control">
            <option value="">Select</option>
            <option value="text-moderation-stable">text-moderation-stable</option>
            <option value="text-moderation-latest">text-moderation-latest</option>
        </select>
        @if ($errors->has('moderations_model'))
            <span class="text-danger">{{$errors->first('moderations_model')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Input</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="moderations_input" name="moderations_input"
               placeholder="Input" value="{{ old('moderations_input') }}">
        @if ($errors->has('moderations_input'))
            <span class="text-danger">{{$errors->first('moderations_input')}}</span>
        @endif
    </div>
</div>
