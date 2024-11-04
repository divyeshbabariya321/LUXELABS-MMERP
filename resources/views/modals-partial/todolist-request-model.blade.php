<div id="todolist-request-model" class="modal fade" role="dialog">
        <div class="modal-content modal-dialog modal-md">
            <form action="{{ route('todolist.store') }}" method="POST" onsubmit="return false;">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title">Create Todo List</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body show-list-records" id="todolist-request">
                    <div class="form-group">
                        <strong>Title:</strong>
                        <input type="text" name="title" class="form-control add_todo_title"
                            value="{{ old('title') }}" required="">
                        <span class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <strong>Subject:</strong>
                        <input type="text" name="subject" class="form-control add_todo_subject"
                            value="{{ old('subject') }}" required="">
                        <span class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <strong>Category:</strong>
                        
                        <select name="todo_category_id" class="form-control add_todo_category">

                        </select>
                        <span class="text-danger"></span>
                    </div>

                    <div class="form-group othercat" style="display: none;">
                        <strong>Add New Category:</strong>
                        <input type="text" name="other" class="form-control add_todo_other"
                            value="{{ old('other') }}">
                        <span class="text-danger"></span>
                    </div>


                    <div class="form-group">
                        <strong>Status:</strong>
                        
                        <select name="status" class="form-control add_todo_status">
                            {{-- @foreach ($statuses as $status)
                                <option value="{{ $status['id'] }}"
                                    @if (old('status') == $status['id']) selected @endif>{{ $status['name'] }}
                                </option>
                            @endforeach --}}
                        </select>
                        <span class="text-danger"></span>
                    </div>
                    <div class="form-group" style="margin-bottom: 0px;">
                        <strong>Date:</strong>

                        <div class='input-group date' id='todo-date' required="">
                            <input type="text" class="form-control global add_todo_date" name="todo_date"
                                placeholder="Date" value="{{ old('todo_date') }}">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <span class="text-danger text-danger-date"></span>

                    <div class="form-group" style="margin-top: 15px;">
                        <strong>Remark:</strong>
                        <input type="text" name="remark" class="form-control add_todo_remark"
                            value="{{ old('remark') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary submit-todolist-button">Store</button>
                </div>
            </form>
        </div>
    </div>