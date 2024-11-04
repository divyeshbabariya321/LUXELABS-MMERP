<div id="bugtrackingCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="edit-h3">Add Test Suite</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            {{ html()->form('POST', route('test-suites.store', ))->open() }}

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label> Name </label>
                  <textarea class="form-control" id="name_bug" name="name"></textarea>
                <span class="text-danger">{{ $errors->first('name') }}</span>
            </div>
			
			 <div class="form-group {{ $errors->has('test_cases') ? 'has-error' : '' }}">
                <label> Test Cases </label>                
				<textarea class="form-control" id="test_cases_bug" name="test_cases"></textarea>
                <span class="text-danger">{{ $errors->first('test_cases') }}</span>
            </div>

            <div class="form-group {{ $errors->has('step_to_reproduce') ? 'has-error' : '' }}">
                <label> Step To Reproduce </label>                
				<textarea class="form-control" id="step_to_reproduce_bug" name="step_to_reproduce"></textarea>
                <span class="text-danger">{{ $errors->first('step_to_reproduce') }}</span>
            </div>

            <div class="form-group {{ $errors->has('url') ? 'has-error' : '' }}">
                <label> ScreenShot/ Video Url </label>
                <input class="form-control" id="url_bug" name="url" type="text">
                <span class="text-danger"></span>
            </div>

           

             <div class="form-group" style="padding-bottom: 58px !important;">
                <div class="col-md-6" style="padding-left: 0px !important;" {{ $errors->has('bug_environment_id') ? 'has-error' : '' }}>
                    <label> Environment </label>
                    <select class="form-control" id="bug_environment_id_bug" name="bug_environment_id">
                        <option value="">Select Environment</option>
                        @foreach($bugEnvironments as  $bugEnvironment)
                            <option value="{{$bugEnvironment->id}}">{{$bugEnvironment->name}} </option>
                        @endforeach
                    </select>
					<span class="text-danger"></span>
                </div>

                <div class="col-md-6"  style="padding-right: 0px !important;"  {{ $errors->has('bug_environment_id') ? 'has-error' : '' }}>
                    <label> Environment Version </label>
                    <input class="form-control" id="bug_environment_ver_bug" name="bug_environment_ver" type="text">
                    <span class="text-danger">{{ $errors->first('bug_environment_ver') }}</span>
                </div>
                
            </div>
           
            <div class="form-group" {{ $errors->has('assign_to') ? 'has-error' : '' }}>
                <label> Assign To </label>
                <select class="form-control" id="assign_to_bug" name="assign_to">
                    <option value="">Select Assign To</option>
                    @foreach($users as  $user)
                        <option value="{{$user->id}}">{{$user->name}} </option>
                    @endforeach
                </select>
				<span class="text-danger"></span>
            </div>
           
            <div class="form-group" {{ $errors->has('bug_status_id') ? 'has-error' : '' }}>
                <label> Status </label>
                <select class="form-control" id="bug_status_id_bug" name="bug_status_id">
                    <option value="">Select Status</option>
                    @foreach($bugStatuses as  $bugStatus)
                        <option value="{{$bugStatus->id}}">{{$bugStatus->name}} </option>
                    @endforeach
                </select>
				<span class="text-danger"></span>
            </div>
            <div class="form-group" {{ $errors->has('module_id') ? 'has-error' : '' }}>
                <label> Module/Feature </label>
                <select class="form-control" id="module_id_bug" name="module_id">
                    <option value="">Select Module/Feature</option>
                    @foreach($filterCategories as  $filterCategory)
                        <option value="{{$filterCategory}}">{{$filterCategory}} </option>
                    @endforeach
                </select>
				<span class="text-danger"></span>
            </div>

            <div class="form-group  {{ $errors->has('remark') ? 'has-error' : '' }}">
                <label> Remark </label>
                <textarea class="form-control" id="remark_bug" name="remark"></textarea>
                <span class="text-danger">{{ $errors->first('remark') }}</span>
				<span class="text-danger"></span>
            </div>
            <div class="form-group" {{ $errors->has('website') ? 'has-error' : '' }}>
                <label> Website </label>
                <select class="form-control" id="website_bug" name="website">
                    <option value="">Select Website</option>
                    @foreach($filterWebsites as  $filterWebsite)
                        <option value="{{$filterWebsite->id}}">{{$filterWebsite->title}} </option>
                    @endforeach
                </select>
				<span class="text-danger"></span>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-secondary btn-save-bug">Store</button>
            </div>
            {{ html()->form()->close() }}

            </div>
        </div>
    </div>
</div>

