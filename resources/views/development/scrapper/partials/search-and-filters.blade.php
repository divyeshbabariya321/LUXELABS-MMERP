<div class="row">
    <div class="col-md-12 mb-4">
        <div class="form-inline">
            <form action="" id="searchScrapDevelopmentMonitoring">
                <div class="form-group">
                    <input class="form-control" type="text" name="name" id="scrapperName" placeholder="Search Name">
                </div>

                <div class="form-group select2-form-group">
                    <select class="form-control select2" name="task_id">
                        <option value="">Select Task</option>
                        @foreach ($tasks as $key => $task)
                            <option value="{{ $task->id }}">{{ $task->id }}</option>
                        @endforeach
                    </select>
                </div>

                @if (auth()->user()->isAdmin())
                    <div class="form-group select2-form-group">
                        <select class="form-control select2" name="user_id">
                            <option value="">Select Resource</option>
                            @foreach ($users as $key => $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="form-group">
                    <select class="form-control" name="need_proxy">
                        <option value="">Need Proxy</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <select class="form-control" name="move_to_aws">
                        <option value="">Moved to Aws</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-default">
                    <l class="fa fa-search"></i>
                </button>

            </form>
        </div>
    </div>
</div>
