<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Model</label>
    <div class="col-sm-10">
        <select name="edits_model" id="edits_model" class="form-control">
            <option value="">Select</option>
            <option value="text-davinci-edit-001">text-davinci-edit-001</option>
            <option value="code-davinci-edit-001">code-davinci-edit-001</option>
        </select>
        @if ($errors->has('edits_model'))
            <span class="text-danger">{{$errors->first('edits_model')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Input</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="edits_input" name="edits_input"
               placeholder="Input" value="{{ old('edits_input') }}">
        @if ($errors->has('edits_input'))
            <span class="text-danger">{{$errors->first('edits_input')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Instruction</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="edits_instruction" name="edits_instruction"
               placeholder="Instruction" value="{{ old('edits_instruction') }}">
        <p class="note">
            The instruction that tells the model how to edit the prompt.
        </p>
        @if ($errors->has('edits_instruction'))
            <span class="text-danger">{{$errors->first('edits_instruction')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Temperature</label>
    <div class="col-sm-10">
        <input type="number" class="form-control" id="edits_temperature" name="edits_temperature"
               placeholder="Temperature" value="{{ old('edits_temperature', 0.7) }}">
        <p class="note">
            What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random,
            while lower values like 0.2 will make it more focused and deterministic.
            We generally recommend altering this or top_p but not both.
        </p>
        @if ($errors->has('edits_temperature'))
            <span class="text-danger">{{$errors->first('edits_temperature')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Top p</label>
    <div class="col-sm-10">
        <input type="number" class="form-control" id="edits_top_p" name="edits_top_p"
               placeholder="Top p" value="{{ old('edits_top_p', 1) }}">
        <p class="note">
            An alternative to sampling with temperature, called nucleus sampling, where the model considers the results
            of the tokens with top_p probability mass. So 0.1 means only the tokens comprising the top 10% probability
            mass are considered.
            We generally recommend altering this or temperature but not both.
        </p>
        @if ($errors->has('edits_top_p'))
            <span class="text-danger">{{$errors->first('edits_top_p')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Number of responses</label>
    <div class="col-sm-10">
        <input type="number" class="form-control" id="edits_n" name="edits_n"
               placeholder="Number of responses" value="{{ old('edits_n', 1) }}">
        <p class="note">
            How many edits to generate for the input and instruction.
        </p>
        @if ($errors->has('edits_n'))
            <span class="text-danger">{{$errors->first('edits_n')}}</span>
        @endif
    </div>
</div>
