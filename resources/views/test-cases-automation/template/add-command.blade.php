<form id="add-command-form" method="POST" action="{{route('test-case-automation.add.command')}}">
    @csrf
    <input type="hidden" value="" name="test_case_id" id="test_case_id"/>
    <div class="modal-body">
        <div class="form-group row">
            <label for="headline1" class="col-sm-4 col-form-label">Site To Use</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="site_to_use" name="site_to_use"
                       placeholder="Site To Use" value="{{ old('site_to_use') }}" readonly>
                @if ($errors->has('site_to_use'))
                    <span class="text-danger">{{$errors->first('site_to_use')}}</span>
                @endif
            </div>
        </div>
        {{-- <div class="form-group row">
            <label for="headline1" class="col-sm-4 col-form-label">Site To Use</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="website_url" name="website_url" value="" readonly>
            </div>
        </div> --}}
        <div class="form-group row">
            <label for="headline1" class="col-sm-4 col-form-label">Website Name</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="website_name" name="website_name" value="" readonly>
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-4 col-form-label">Environment</label>
            <div class="col-sm-8">
                <label class="ml-3 pt-2">o2t</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-4 col-form-label">Browser</label>
            <div class="col-sm-8">
                <select name="browser" id="" class="form-control">
                    <option value="">Select</option>
                    @foreach($browsers as $browsersId => $browsersValue)
                        <option value="{{$browsersId}}">{{$browsersValue}}</option>
                    @endforeach
                </select>
                @if ($errors->has('browsers'))
                    <span class="text-danger">{{$errors->first('browsers')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-4 col-form-label">Headless</label>
            <div class="col-sm-8">
                <select name="headless" id="" class="form-control">
                    <option value="">Select</option>
                    <option value="false">False</option>
                    <option value="true">True</option>
                </select>
                @if ($errors->has('browsers'))
                    <span class="text-danger">{{$errors->first('browsers')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-4 col-form-label">Test</label>
            <div class="col-sm-8">
                <select name="test" id="" class="form-control">
                    <option value="">Select</option>
                    @foreach($testList as $testId => $testValue)
                        <option value="{{$testId}}">{{$testValue}}</option>
                    @endforeach
                </select>
                @if ($errors->has('test'))
                    <span class="text-danger">{{$errors->first('test')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-4 col-form-label">Test Flow</label>
            <div class="col-sm-8">
                <select name="test_flow" id="" class="form-control">
                    <option value="">Select</option>
                    @foreach($testFlow as $testFlowId => $testFlowValue)
                        <option value="{{$testFlowId}}">{{$testFlowValue}}</option>
                    @endforeach
                </select>
                @if ($errors->has('test_flow'))
                    <span class="text-danger">{{$errors->first('test_flow')}}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Create</button>
    </div>
</form>