<div id="priority_model" class="modal fade" role="dialog">
    <div class="modal-dialog" style="max-width: 100%; width:95%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Priority</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" id="priorityForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>User:</strong>
                            <div class="form-group">
                                @if(auth()->user()->isAdmin())
                                    <select class="form-control" id="priority_user_id">
                                        <option value="0">Select User</option>
                                        @foreach ($users as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="user_id" value="" id="sel_user_id" />
                                @else
                                    {{auth()->user()->name}}
                                @endif
                            </div>

                        </div>
                        <div class="col-md-6">
                            <strong>Remarks:</strong>
                            @if(auth()->user()->isAdmin())
                                <div class="form-group">
                                    <textarea cols="45" class="form-control" name="global_remarkes"
                                              style="height:32px !important;"></textarea>
                                </div>
                            @endif

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped" style="table-layout: fixed;">
                                <tr>
                                    <th width="4%">ID</th>
                                    <th width="16%">Subject</th>
                                    <th width="35%">Task</th>
                                    <th width="10%">Communication</th>
                                    <th width="13%">Date</th>
                                    <th width="8%">Submitted By</th>
                                    <th width="5%">Action</th>
                                </tr>
                                <tbody class="show_task_priority">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    @if(auth()->user()->isAdmin())
                        <button type="submit" class="btn btn-secondary">Confirm</button>
                    @endif
                </div>
            </form>
        </div>

    </div>
</div>