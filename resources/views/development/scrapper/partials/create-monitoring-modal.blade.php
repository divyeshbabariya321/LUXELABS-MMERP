<div id="createMonitoringModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Create</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="createMonitoringModalForm">
                @csrf
                <div class="modal-body">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="form-label">Task Id</label>
                            <select class="form-control select2" name="task_id" required>
                                @foreach ($tasks as $key => $task)
                                    <option value="{{ $task->id }}">{{ $task->id }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if (auth()->user()->isAdmin())
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form-label">Resource Name</label>
                                <select class="form-control select2" name="user_id" required>
                                    @foreach ($users as $key => $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="form-label">Scrapper Name</label>
                            <input type="text" class="form-control" name="scrapper_name">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="form-label">Need Proxy</label>
                            <select class="form-control" required name="need_proxy">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="form-label">Moved to Aws</label>
                            <select class="form-control" required name="move_to_aws">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="form-label">Remark</label>
                            <textarea class="form-control" name="remarks" rows="8" cols="80" required spellcheck="false"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitForm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
