<form action="{{ url("development/list") }}" method="get" class="search">
    <div class="row">
        @if(auth()->user()->isReviwerLikeAdmin())
            <div class="col-md-2 pd-sm">
                <label for="assiged users">Assigned User </label>
                <select class="form-control select2" multiple name="assigned_to[]" id="assigned_to">
                    @foreach($users as $id=>$user)
                        <option value="{{$id}}"
                                @if(is_array(request('assigned_to')) && in_array($id, request('assigned_to')))
                                    selected
                                @endif>{{ $user }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(auth()->user()->isReviwerLikeAdmin())
            <div class="col-md-2 pd-sm">
                <label for="assiged users">Lead Developer</label>
                <select class="form-control select2" , multiple name="master_user_id[]" id="master_user_id">
                    @foreach($users as $id=>$user)
                        <option value="{{$id}}"
                                @if(is_array(request('master_user_id')) && in_array($id, request('master_user_id')))
                                    selected
                                @endif>{{ $user }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(auth()->user()->isReviwerLikeAdmin())
            <div class="col-md-2 pd-sm">
                <label for="assiged users">Team lead</label>
                <select class="form-control select2" , multiple name="team_lead_id[]" id="team_lead_id">
                    @foreach($users as $id=>$user)
                        <option value="{{$id}}"
                                @if(is_array(request('team_lead_id')) && in_array($id, request('team_lead_id')))
                                    selected
                                @endif>{{ $user }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(auth()->user()->isReviwerLikeAdmin())
            <div class="col-md-2 pd-sm">
                <label for="assiged users">Tester</label>
                <select class="form-control select2" , multiple name="tester_id[]" id="tester_id">
                    @foreach($users as $id=>$user)
                        <option value="{{$id}}"
                                @if(is_array(request('tester_id')) && in_array($id, request('tester_id')))
                                    selected
                                @endif>{{ $user }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-md-2 pd-sm"><br>
            <select name="module" id="module_id" class="form-control">
                <option value="">Module</option>
                @foreach($modules as $module)
                    <option {{ $request->get('module') == $module->id ? 'selected' : '' }} value="{{ $module->id }}">{{ $module->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 pd-sm"><br>
            <input type="text" name="subject" id="subject_query" placeholder="Issue Id / Subject" class="form-control"
                   value="{{ (!empty(app('request')->input('subject'))  ? app('request')->input('subject') : '') }}">
        </div>
        <div class="col-md-2 pd-sm status-selection"><br>
            {{ html()->multiselect("task_status[]", $statusList, request()->get('task_status', []))->class("form-control multiselect") }}
        </div>
        <div class="col-md-2 pd-sm"><br>
            <select name="order" id="order_query" class="form-control">
                <option {{$request->get('order')== "" ? 'selected' : ''}} value="">Latest Communication</option>
                <option {{$request->get('order')== "latest_task_first" ? 'selected' : ''}} value="latest_task_first">
                    Latest Task First
                </option>
                <option {{$request->get('order')== "priority" ? 'selected' : ''}} value="priority">Sort by priority
                </option>
                <option {{$request->get('order')== "oldest_first" ? 'selected' : ''}} value="oldest_first">Olderst
                    First
                </option>

            </select>
        </div>
        <div class="col-md-2 pd-sm"><br>
            <select name="tasktype" id="tasktype" class="form-control">
                <option {{$type == "all" ? 'selected' : ''}} value="all">All</option>
                <option {{$type == "devtask" ? 'selected' : ''}} value="devtask">Devtask</option>
                <option {{$type == "issue" ? 'selected' : ''}} value="issue">Issue</option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-top:10px;">
        <div class="col-md-2 pd-sm">
            <label class="for">
                Last Communicated &nbsp;&nbsp;
                {{ html()->checkbox("last_communicated", request()->get('last_communicated', "off") == "on", "on")->class("ml-2") }}
            </label>
        </div>


        <div class="col-md-2 pd-sm">
            <input placeholder="E. Date" type="text" class="form-control estimate-date_picker" id="estimate_date_picker"
                   name="estimate_date">
        </div>

        <div class="col-md-2 pd-sm">
            <select class="form-control" name="is_estimated" id="is_estimated">
                <option {{$request->get('is_estimated')=='' ? 'selected' : ''}} value="">All</option>
                <option {{$request->get('is_estimated')=='null' ? 'selected' : ''}} value="null">Not Estimated Yet
                </option>
                <option {{$request->get('is_estimated')=='not_approved' ? 'selected' : ''}} value="not_approved">Not
                    Approved By Admin
                </option>
            </select>
        </div>

        <div class="col-md-2 pd-sm">
            <select class="form-control" name="repo_id" id="repo_id">
                <option value="">Select repository</option>
                @foreach ($respositories as $repository)
                    <option value="{{ $repository->id }}" {{ $repository->id == request()->get('repo_id') ? 'selected' : '' }}>{{ $repository->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-1 pd-sm">
            <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                <img src="{{ asset('images/search.png') }}" alt="Search">
            </button>
            <a href="{{ url("development/list") }}" class="btn btn-image" id="">
                <img src="/images/resend2.png"
                     style="cursor: nwse-resize;"></a>
            <input type="hidden" id="download" name="download" value="1">
        </div>
    </div>
</form>
