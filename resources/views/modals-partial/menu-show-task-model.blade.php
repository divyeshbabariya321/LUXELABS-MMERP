<div id="menu-show-task-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Task & Activity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="database-form">
                                @csrf
                                <div class="row">
                                    <div class="col-12 pb-3">
                                        <input type="text" name="task_search" class="task-search-table"
                                            class="form-control" placeholder="Enter Task Id & Keyword">
@php
    $users   = \App\User::getalluser();
    $userLists      = \App\User::getActiveUsersExcept();
@endphp
                                        <select class="form-control col-md-2 ml-3 ipusersSelect"
                                            name="task_user_id" id="task_user_id">
                                            <option value="">Select user</option>
                                            @foreach ($userLists as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                            <option value="other">Other</option>
                                        </select>
                                        <button type="button" class="btn btn-secondary btn-task-search-menu"><i
                                                class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-12">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="10%">Assign To</th>
                                                    <th width="10%">Communication</th>
                                                </tr>
                                            </thead>
                                            <tbody class="show-search-task-list">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>