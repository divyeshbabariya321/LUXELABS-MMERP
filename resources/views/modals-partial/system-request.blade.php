<div id="system-request" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 1000px; max-width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">System IPs</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12" id="permission-request">
                    @php

                    $users   = App\User::getalluser();
                    $userLists      = App\User::getActiveUsersExcept();
                    $final_array = App\User::getFirewallList();
                    @endphp

                    <div id="select-user">
                        <input type="text" name="add-ip" class="form-control col-md-3" placeholder="Add IP here...">
                        <select class="form-control col-md-2 ml-3 ipusersSelect" name="user_id" id="ipusers">
                            <option value="">Select user</option>
                            @foreach ($userLists as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                            <option value="other">Other</option>
                        </select>
                        <input type="text" name="other_user_name" id="other_user_name"
                            class="form-control col-md-2 ml-3" style="display:none;" placeholder="other name">
                        <input type="text" name="ip_comment" class="form-control col-md-2 ml-3 mr-3""
                            placeholder="Add comment...">
                        <button class="btn-success btn addIp ml-3 mb-5">Add</button>
                        <button class="btn-warning btn bulkDeleteIp ml-3 mb-5">Delete All IPs</button>
                    </div>



                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Index</th>
                                <th>IP</th>
                                <th>User</th>
                                <th>Source</th>
                                <th>Comment</th>
                                <th>Command</th>
                                <th>Status</th>
                                <th>Message</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="userAllIps">
                        </tbody>

                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
