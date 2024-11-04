@auth
        @php
            $users   = App\User::getalluser();
        @endphp
        <div id="quickRequestZoomModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <div class="modal-content">
                    <form action="#" method="POST" id="send-request-form">
                        @csrf

                        <div class="modal-header">
                            <h4 class="modal-title">Send Request</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="title">Select User</label>
                                <select name="requested_ap_user_id" class="form-control" style="width: 100%"
                                    id="requested_ap_user_id" required>
                                    <option>--Users--</option>
                                    @foreach ($users as $key => $user)
                                        @if ($user->id != auth()->user()->id)
                                            @if ($user->isOnline() == 1)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Remarks:</label>
                                <textarea name="requested_ap_remarks" id="requested_ap_remarks" placeholder="Enter remarks" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-secondary send-ap-quick-request">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    @endauth