<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Model</label>
    <div class="col-sm-10">
        <select name="completions_model" id="completions_model" class="form-control">
            <option value="">Select</option>
            @foreach($modelsList as $model)
                <option value="{!! $model !!}">{!! $model !!}</option>
            @endforeach
        </select>
        @if ($errors->has('completions_model'))
            <span class="text-danger">{{$errors->first('completions_model')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Prompt</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="completions_prompt" name="completions_prompt"
               placeholder="Prompt" value="{{ old('completions_prompt') }}">
        @if ($errors->has('completions_prompt'))
            <span class="text-danger">{{$errors->first('completions_prompt')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Suffix</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="completions_suffix" name="completions_suffix"
               placeholder="Suffix" value="{{ old('completions_suffix') }}">
        <p class="note">The suffix that comes after a completion of inserted text.</p>
        @if ($errors->has('completions_suffix'))
            <span class="text-danger">{{$errors->first('completions_suffix')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Max tokens</label>
    <div class="col-sm-10">
        <input type="number" class="form-control" id="completions_max_tokens" name="completions_max_tokens"
               placeholder="Max tokens" value="{{ old('completions_max_tokens', 1024) }}">
        <p class="note">
            The maximum number of tokens to generate in the completion.
            The token count of your prompt plus max_tokens cannot exceed the model's context length. Most models have a
            context length of 2048 tokens (except for the newest models, which support 4096).
        </p>
        @if ($errors->has('completions_max_tokens'))
            <span class="text-danger">{{$errors->first('completions_max_tokens')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Temperature</label>
    <div class="col-sm-10">
        <input type="number" class="form-control" id="completions_temperature" name="completions_temperature"
               placeholder="Temperature" value="{{ old('completions_temperature', 0.7) }}" min="0" max="2" >
        <p class="note">
            What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random,
            while lower values like 0.2 will make it more focused and deterministic.
            We generally recommend altering this or top_p but not both.
        </p>
        @if ($errors->has('completions_temperature'))
            <span class="text-danger">{{$errors->first('completions_temperature')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Top p</label>
    <div class="col-sm-10">
        <input type="number" class="form-control" id="completions_top_p" name="completions_top_p"
               placeholder="Top p" value="{{ old('completions_top_p', 1) }}">
        <p class="note">
            An alternative to sampling with temperature, called nucleus sampling, where the model considers the results
            of the tokens with top_p probability mass. So 0.1 means only the tokens comprising the top 10% probability
            mass are considered.
            We generally recommend altering this or temperature but not both.
        </p>
        @if ($errors->has('completions_top_p'))
            <span class="text-danger">{{$errors->first('completions_top_p')}}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="headline1" class="col-sm-2 col-form-label">Number of responses</label>
    <div class="col-sm-10">
        <input type="number" class="form-control" id="completions_n" name="completions_n"
               placeholder="Number of responses" value="{{ old('completions_n', 1) }}">
        <p class="note">
            How many completions to generate for each prompt.
            Note: Because this parameter generates many completions, it can quickly consume your token quota. Use
            carefully and ensure that you have reasonable settings for max_tokens and stop.
        </p>
        @if ($errors->has('completions_n'))
            <span class="text-danger">{{$errors->first('completions_n')}}</span>
        @endif
    </div>
</div>
