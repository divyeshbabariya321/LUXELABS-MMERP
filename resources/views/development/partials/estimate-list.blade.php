<div class="modal-dialog modal-xl" style="width: 100% !important; max-width: 1700px !important;">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Estimation</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body shortcut-estimate-search-container">
            <div class="from-group row">
                <div class="col-xs-2">
                    <label for="filterDropdown">Search</label>
                    <br/>
                    <select name="task_id" id="shortcut-estimate-search" class="form-control">
                        <option @if(empty($taskId)) selected @endif value="">Select task</option>
                        @foreach ($d_taskList as $val)
                            <option value="{{ 'DEVTASK-' . $val }}" @if($taskId === 'DEVTASK-' . $val) selected @endif>
                                {{ 'DEVTASK-' . $val }}
                            </option>
                        @endforeach
                        @foreach ($g_taskList as $val)
                            <option value="{{ 'TASK-' . $val }}" @if($taskId === 'TASK-' . $val) selected @endif>
                                {{ 'TASK-' . $val }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-table">
                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Task ID</th>
                                <th>Task Details</th>
                                <th>Developer</th>
                                <th>Estimate Min</th>
                                <th>Start Date</th>
                                <th>End date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($developerTaskHistory as $task)
                                <tr>
                                    <td>{{'DEVTASK-'.$task->task_id}}</td>
                                    <!-- <td>{{$task->task}}</td> -->
                                    @if (strlen($task->task) > 25)
                                        <td style="word-break: break-word;" data-log_message="{!!$task->task !!}" class="task-task_details">{{ substr($task->task,0,25) }}...</td>
                                    @else
                                        <td style="word-break: break-word;">{!!$task->task !!}</td>
                                    @endif
                                    <td>{{$task->user->name}}</td>
                                    <td>{{$task->estimate_minutes}}</td>
                                    <td>{{$task->start_date}}</td>
                                    <td>{{$task->estimate_date}}</td>
                                    <td>
                                        @if (isset($task->is_approved) &&$task->is_approved != 1)
                                            <form action="approveEstimateFromshortcut" style="display: inline-block">
                                                <button data-task="{{$task->task_id}}" data-id="{{$task->id}}" title="approve" data-type="DEVTASK" class="btn btn-sm approveEstimateFromshortcutButton">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button class="btn btn-sm estimate-history" title="History" data-task="DEVTASK" data-id="{{$task->task_id}}">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </button>
                                        
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($developerTaskHistory as $task)
                                <tr>
                                    <td>TASK-{{$task->task_id}}</td>
                                    <!-- <td>{{ substr($task->task_details, 0,  25) }} {{strlen($task->task_details) > 25 ? '...' : '' }}</td> -->
                                    @if (strlen($task->task_details) > 25)
                                        <td style="word-break: break-word;" data-log_message="{!!$task->task_details !!}" class="task-task_details">{{ substr($task->task_details,0,25) }}...</td>
                                    @else
                                        <td style="word-break: break-word;">{!!$task->task_details !!}</td>
                                    @endif
                                    <td>{{$task->user->name}}</td>
                                    <td>{{$task->approximate}}</td>
                                    <td>{{$task->start_date}}</td>
                                    <td>{{$task->due_date}}</td>
                                    <td>
                                        @if (isset($task->is_approved) &&$task->is_approved != 1)
                                            <form action="approveEstimateFromshortcut" style="display: inline-block">
                                                <button data-task="{{$task->task_id}}" data-id="{{$task->id}}" title="approve" data-type="TASK" class="btn btn-sm approveEstimateFromshortcutButton">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button class="btn btn-sm estimate-history" title="History" data-task="TASK" data-id="{{$task->task_id}}">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Log Messages Modal -->
<div id="taskDetailsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on('click','.task-task_details',function(){
        $('#taskDetailsModal').modal('show');
        $('#taskDetailsModal p').text($(this).data('log_message'));
    })
    @php
        $route = request()->route()->getName();
    @endphp
    var configsest = {
        routes : {
            'task_estimate_list':"{{route('task.estimate.list')}}"
        }
    };
    @if (empty($taskId))
        $("#shortcut-estimate-search").select2();
    @elseif (!empty($taskId))
        $("#shortcut-estimate-search").val("{{ $taskId }}").select2();
    @endif
    $("#shortcut-estimate-search").change(function (e) {
            e.preventDefault();
            let task_id = $(this).val();
            @if ($route == "development.issue.index")
                var  tasktype = "DEVTASK";
            @else
                var tasktype = "TASK";
            @endif
            $.ajax({
                type: "GET",
                url: configsest.routes.task_estimate_list,
                data: {
                    task: tasktype,
                    task_id
                },
                success: function (response) {

                    $("#modal-container").load("/showLatestEstimateTime", function () {
                        $("#showLatestEstimateTime").html(response);
                        $("#showLatestEstimateTime").modal('show');
      });
                },
                error: function (error) {
                    toastr["error"]("Error while fetching data.");
                }

            });
        });
</script>