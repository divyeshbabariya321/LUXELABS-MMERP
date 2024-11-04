<div id="testCaseCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="edit-h3">Add Test Cases</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {{ html()->form('POST', route('test-cases.store', ))->open() }}

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif

                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label> Name </label>
                    <input class="form-control" name="name" type="text" required>
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                </div>

                <div class="form-group {{ $errors->has('suite') ? 'has-error' : '' }}">
                    <label> Suite </label>
                    <input class="form-control" name="suite" type="text" required>
                    <span class="text-danger">{{ $errors->first('suite') }}</span>
                </div>


                <div class="form-group" {{ $errors->has('module_id') ? 'has-error' : '' }}>
                    <label> Module </label>
                    <select class="form-control" name="module_id" required>
                        <option value="">Select Module</option>
                        @foreach($filterCategories as  $filterCategory)
                            <option value="{{$filterCategory}}">{{$filterCategory}} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group {{ $errors->has('precondition') ? 'has-error' : '' }}">
                    <label> Precondition </label>
                    <textarea class="form-control" name="precondition" required> </textarea>
                    <span class="text-danger">{{ $errors->first('precondition') }}</span>
                </div>

                <div class="form-group">
                    <label for="assign_to">Assign To</label>
                    <select name="assign_to" class="form-control" required>
                        <option value="">-- N/A --</option>
                        <?php
                        foreach ($users as  $user) {
                            echo "<option {{if data.assign_to == '".$user->id."'}} selected {{/if}} value='".$user->id."'>".$user->name.'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group {{ $errors->has('step_to_reproduce') ? 'has-error' : '' }}">
                    <label> Step To Reproduce </label>
                    <textarea class="form-control" name="step_to_reproduce" required> </textarea>
                    <span class="text-danger">{{ $errors->first('step_to_reproduce') }}</span>
                </div>

                <div class="form-group {{ $errors->has('expected_result') ? 'has-error' : '' }}">
                    <label> Expected Result </label>
                    <input class="form-control" name="expected_result" type="text" required>
                    <span class="text-danger">{{ $errors->first('expected_result') }}</span>
                </div>


                <div class="form-group" {{ $errors->has('test_status_id') ? 'has-error' : '' }}>
                    <label> Status </label>
                    <select class="form-control" name="test_status_id" required>
                        <option value="">Select Status</option>
                        @foreach($testCaseStatuses as  $testCaseStatus)
                            <option value="{{$testCaseStatus->id}}">{{$testCaseStatus->name}} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" {{ $errors->has('website') ? 'has-error' : '' }}>
                    <label> Website </label>
                    <select class="form-control" name="website" required>
                        <option value="">Select Website</option>
                        @foreach($filterWebsites as  $key => $filterWebsite)
                            <option value="{{$key}}">{{$filterWebsite}} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-secondary">Store</button>
                </div>
                {{ html()->form()->close() }}

            </div>
        </div>
    </div>
</div>