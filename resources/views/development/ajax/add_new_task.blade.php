<!-- Modal -->
<div id="newTaskModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div style="padding: 10px;border-bottom: 1px solid #e5e5e5;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Task</h4>
            </div>
            <form id="frmaddnewtask" action="{{ route('development.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        @if(auth()->user()->checkPermission('development-list'))
                        <div class="form-group col-md-6">
                            <strong>Assigned To:</strong>
                            <select class="form-control" name="assigned_to" required>
                                @foreach ($users as $key => $obj)
                                <option value="{{ $key }}" {{ old('assigned_to') == $key ? 'selected' : '' }}>{{ $obj }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('assigned_to'))
                            <div class="alert alert-danger">{{$errors->first('assigned_to')}}</div>
                            @endif
                        </div>
                        @endif

                        <div class="form-group col-md-6">
                            <label for="" class="form-label">Organization</label>
                            <select name="organizationId" id="organizationId" class="form-control">
                                @foreach ($githubOrganizations as $githubOrganization)
                                    <option value="{{ $githubOrganization->id }}" data-repos='{{ $githubOrganization->repos }}' {{ ($githubOrganization->name == 'MMMagento' ? 'selected' : '' ) }}>{{  $githubOrganization->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="repository_id">Repository:</label>
                            <br>
                            <select style="width:100%" class="form-control select2" id="repository_id" name="repository_id">
                               
                            </select>

                            @if ($errors->has('repository_id'))
                            <div class="alert alert-danger">{{$errors->first('repository_id')}}</div>
                            @endif
                        </div>
                        <!-- 
                        <div class="form-group">
                            <strong>Attach files:</strong>
                            <input type="file" name="images[]" class="form-control" multiple>
                            @if ($errors->has('images'))
                            <div class="alert alert-danger">{{$errors->first('images')}}</div>
                            @endif
                        </div> -->

                        <div class="form-group col-md-6">
                            <label for="module_id">Module:</label>
                            <br>
                            <select style="width:100%" class="form-control" id="module_id" name="module_id" required>
                                <option value>Select a Module</option>
                                @foreach ($modules as $module)
                                <option value="{{ $module->id }}" {{ $module->id == old('module_id',9) ? 'selected' : '' }}>{{ $module->name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('module_id'))
                            <div class="alert alert-danger">{{$errors->first('module_id')}}</div>
                            @endif
                        </div>

                        <!-- <div class="form-group">
                        <label for="priority">Priority:</label>
                        <select class="form-control" name="priority" id="priority" required>
                            <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>Normal</option>
                            <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>Urgent</option>
                            <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>Critical</option>
                        </select>

                        @if ($errors->has('priority'))
                        <div class="alert alert-danger">{{$errors->first('priority')}}</div>
                        @endif
                        </div> -->

                        <div class="form-group col-md-6">
                            <label for="priority">Type:</label>
                            <select class="form-control" name="task_type_id" id="task_type_id" required>
                                @foreach($tasksTypes as $taskType)
                                <option value="{{$taskType->id}}">{{$taskType->name}}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('priority'))
                            <div class="alert alert-danger">{{$errors->first('priority')}}</div>
                            @endif
                        </div>

                        <div class="form-group col-md-6">
                            <label for="task_for">Task For:</label>
                            <br>
                            <select name="task_for" class="form-control task_for" style="width:100%;">
                                <option value="hubstaff">Hubstaff</option>
                                <option value="time_doctor">Time Doctor</option>
                            </select>

                            @if ($errors->has('task_for'))
                            <div class="alert alert-danger">{{$errors->first('task_for')}}</div>
                            @endif
                        </div>

                        @php 
                            $accountList = \App\TimeDoctor\TimeDoctorAccount::select('id', 'time_doctor_email')->where('auth_token','!=','')->get();
                        @endphp
                        <div class="form-group col-md-6 time_doctor_project_section">
                            <label for="time_doctor_account">Time Doctor Account:</label>
                            <select class="form-control" name="time_doctor_account" id="time_doctor_account">
                                <option value="">Select Account</option>
                                @foreach($accountList as $account)
                                <option value="{{$account->id}}">{{$account->time_doctor_email}}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('time_doctor_account'))
                            <div class="alert alert-danger">{{$errors->first('time_doctor_account')}}</div>
                            @endif
                        </div>

                        @php 
                            $projectList = \App\TimeDoctor\TimeDoctorProject::select('time_doctor_project_id', 'time_doctor_project_name')->get();
                        @endphp
                        <div class="form-group col-md-6 time_doctor_project_section">
                            <label for="time_doctor_project">Time Doctor Project:</label>
                            <?php //echo Form::select("time_doctor_project",['' => ''],null,["class" => "form-control time_doctor_project globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.time_doctor_projects'), 'data-placeholder' => 'Project']); ?>
                            <select class="form-control" name="time_doctor_project" id="time_doctor_project">
                                <option value="">Select Project</option>
                            </select>

                            @if ($errors->has('time_doctor_project'))
                            <div class="alert alert-danger">{{$errors->first('time_doctor_project')}}</div>
                            @endif
                        </div>

                        <div class="form-group col-md-12">
                            <strong>Subject:</strong>
                            <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" />
                            
                            @if ($errors->has('subject'))
                            <div class="alert alert-danger">{{$errors->first('subject')}}</div>
                            @endif
                        </div>

                        <div class="form-group col-md-12">
                            <strong>Task:</strong>
                            <textarea class="form-control" name="task" rows="8" cols="80" required>{{ old('task') }}</textarea>
                            
                            @if ($errors->has('task'))
                            <div class="alert alert-danger">{{$errors->first('task')}}</div>
                            @endif
                        </div>

                        <!-- <div class="form-group">
                        <strong>Cost:</strong>
                        <input type="number" class="form-control" name="cost" value="{{ old('cost') }}" />
                        </select>

                        @if ($errors->has('cost'))
                        <div class="alert alert-danger">{{$errors->first('cost')}}</div>
                        @endif
                        </div> -->

                        <div class="form-group col-md-6">
                            <strong>Status:</strong>
                            <select class="form-control" name="status" required>
                                <!-- <option value="Planned" {{ old('status') == 'Planned' ? 'selected' : '' }}>Planned</option>
                            <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Done" {{ old('status') == 'Done' ? 'selected' : '' }}>Done</option> -->
                                @foreach($statusList as $key => $status)
                                <option value="{{$key}}" {{ old('status','In Progress') == $status ? 'selected' : '' }}>{{$status}}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('status'))
                            <div class="alert alert-danger">{{$errors->first('status')}}</div>
                            @endif
                        </div>

                        <div class="form-group col-md-6">
                            <strong>Is Milestone ?:</strong>
                            <select id="is_milestone" class="form-control" name="is_milestone" required>
                                <option value="0" {{ old('is_milestone') == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_milestone') == 1 ? 'selected' : '' }}>Yes</option>
                            </select>

                            @if ($errors->has('is_milestone'))
                            <div class="alert alert-danger">{{$errors->first('is_milestone')}}</div>
                            @endif
                        </div>

                        <div class="form-group col-md-6">
                            <strong>No of milestone:</strong>
                            <input type="number" class="form-control" id="no_of_milestone" name="no_of_milestone" value="{{ old('no_of_milestone') }}" />
                            @if ($errors->has('no_of_milestone'))
                            <div class="alert alert-danger">{{$errors->first('no_of_milestone')}}</div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <strong>Create Review Task?</strong>
                            <div class="form-group">
                                <input type="checkbox" name="need_review_task" value="1" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary ">Add</button>
                </div>
            </form>
        </div>

    </div>
</div>
<script>
    var defaultRepositoryId = '{{ $defaultRepositoryId }}';
    
    $(document).on("change", "#organizationId", function() {
        getRepositories();
    });

    function getRepositories(){
        var isSelect2Destroy = 0;

        if ($('#repository_id').data('select2')){
            $('#repository_id').select2('destroy');
            isSelect2Destroy = 1;
        }

        var repos = $.parseJSON($('#organizationId option:selected').attr('data-repos'));

        $('#repository_id').empty();

        console.log(repos);

        if(repos.length > 0){
            $.each(repos, function (k, v){
                $('#repository_id').append('<option value="'+v.id+'" '+(defaultRepositoryId == v.id ? 'selected' : '')+'>'+v.name+'</option>');
            });
        }

        if (isSelect2Destroy == 1){
            $('#repository_id').select2();
        }
    }

    getRepositories();
</script>