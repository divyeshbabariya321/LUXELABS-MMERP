<div id="quickDevelopmentModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add New Task</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="{{ route('development.store') }}" method="POST" enctype="multipart/form-data" id="quickDevelopmentForm">
        @csrf
        <input type="hidden" name="priority" value="3">
        <input type="hidden" name="status" value="Discussing">

        <div class="modal-body">
          @if(auth()->user()->checkPermission('development-list'))
            <div class="form-group">
              <strong>User:</strong>
              <select class="form-control" name="assigned_to" required>
                @foreach ($quickDevUsersArray as $id => $name)
                  <option value="{{ $id }}" {{ old('assigned_to') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
             </select>

              @if ($errors->has('assigned_to'))
                  <div class="alert alert-danger">{{$errors->first('assigned_to')}}</div>
              @endif
            </div>
          @endif

          <div class="form-group">
            <strong>Module:</strong>
            <select class="form-control" name="module_id" >
              <option value>Select a Module</option>
              @foreach ($quickTasksModules as $module)
                <option value="{{ $module->id }}" {{ $module->id == old('module_id') ? 'selected' : '' }}>{{ $module->name }}</option>
              @endforeach
           </select>

            @if ($errors->has('module_id'))
                <div class="alert alert-danger">{{$errors->first('module_id')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Task:</strong>
            <textarea class="form-control" name="task" rows="8" cols="80" required id="quick_development_task_textarea">{{ old('task') }}</textarea>
           </select>

            @if ($errors->has('task'))
              <div class="alert alert-danger">{{$errors->first('task')}}</div>
            @endif
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary" id="quickDevelopmentSubmit">Add</button>
        </div>
      </form>
    </div>

  </div>
</div>
