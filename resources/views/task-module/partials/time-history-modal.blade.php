<div id="status_history_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status History</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="row">
                <div class="col-md-12" id="status_history_div">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Old Status</th>
                                <th>New Status</th>
                                <th>Updated by</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div id="time_history_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Estimated Time History - <span class="text-danger"> Add new value should be old value + additional time </span> </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" id="approve-time-btn" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="developer_task_id" id="developer_task_id">

                        <div id="time_history_div" class="col-md-12">
                            <table class="table table table-bordered" style="font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Old Value</th>
                                        <th>New Value</th>
                                        <th>Updated by</th>
                                        <th>Remark</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--Purpose : add style - DEVTASK-4354 -->
                <div class="modal-footer1 mx-5 d-flex justify-content-between" style="margin-bottom:10px;">
                    <div class="b">
                        @if(\Auth::user()->isAdmin())
                        <button type="button" class="btn btn-secondary remind_btn" title="Remind"><i class="fa fa-bell-o" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-secondary revise_btn" title=">Revise"><i class="fa fa-repeat" aria-hidden="true"></i></button>
                        @endif
                        <button type="button" class="btn btn-secondary approved_history" title="Approve History" title="History"><i class="fa fa-list" aria-hidden="true"></i></button>
                    </div>
                    <div class="a">
                        <button type="button" class="btn btn-default close_btn" data-dismiss="modal" title="Close"><i class="fa fa-window-close" aria-hidden="true"></i></button>
                        <?php if (auth()->user()->isReviwerLikeAdmin() || isAdmin()) { ?>
                            <button type="submit" class="btn btn-secondary confirm_btn" title="Confirm"><i class="fa fa-check" aria-hidden="true"></i></button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="ApprovedHistory" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approved History</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="row">
                <div class="col-md-12" id="approve_history_form">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Old Status</th>
                                <th>New Status</th>
                                <th>Approve by</th>

                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" title="Close"><i class="fa fa-window-close" aria-hidden="true"></i></button>
            </div>
            </form>
        </div>
    </div>
</div>

<div id="lead_time-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Estimated Date History</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" id="approve-lead-date-btn" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="lead_developer_task_id" id="lead_developer_task_id">
                        <div class="col-md-12" id="lead_time_history_div">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Old Value</th>
                                        <th>New Value</th>
                                        <th>Updated by</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    @if(auth()->user()->isReviwerLikeAdmin())
                    <button type="submit" class="btn btn-secondary">Confirm</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    jQuery(document).on('click', '.show-time-history', function() {
        var userId = jQuery(this).attr('data-userid');
        var issueId = jQuery(this).attr('data-id');
        jQuery('#time_history_div table tbody').html('');

        // const hasText = jQuery(this).siblings('input').val()
        // if (!hasText) {
        //     jQuery('#time_history_modal .revise_btn').prop('disabled', true);
        //     jQuery('#time_history_modal .remind_btn').prop('disabled', false);
        // }

        jQuery.ajax({
            url: "{{ route('task.time.history') }}",
            data: {
                id: issueId,
                user_id: userId
            },
            success: function(data) {
                if (data != 'error') {
                    jQuery('input[name="developer_task_id"]').val(issueId);
                    jQuery.each(data, function(i, item) {
                        if (item['is_approved'] == 1) {
                            var checked = 'checked';
                        } else {
                            var checked = '';
                        }
                        jQuery('#time_history_div table tbody').append(
                            `<tr>
                                    <td>${ item['created_at'] }</td>
                                    <td>${ ((item['old_value'] != null) ? item['old_value'] : '-') }</td>
                                    <td>${item['new_value']}</td>
                                    <td>${item['name']}</td>
                                    <td align="center"><span title="${item['remark']}">${(item['remark'] !== null ) ? item['remark'] : '-'}</td>
                                    <td><input type="radio" name="approve_time" value="${item['id']}" ${checked} class="approve_time"/></td>
                                </tr>`
                        );
                    });
                    jQuery('#time_history_div table tbody').append(
                        '<input type="hidden" name="user_id" value="' + userId + '" class=" "/>'
                    );
                }
                jQuery('#time_history_modal').modal('show');
            }
        });
    });

    jQuery(document).on('click', '.remind_btn', function() {
        var issueId = jQuery('#approve-time-btn input[name="developer_task_id"]').val();
        var userId = jQuery('#approve-time-btn input[name="user_id"]').val();

        jQuery('#time_history_div table tbody').html('');
        jQuery.ajax({
            url: "{{ route('task.time.history.approve.sendRemindMessage') }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                id: issueId,
                user_id: userId,
            },
            success: function(data) {
                toastr['success'](data.message, 'success');
            }
        });
        jQuery('#time_history_modal').modal('hide');
    });

    jQuery(document).on('click', '.revise_btn', function() {
        var issueId = jQuery('#approve-time-btn input[name="developer_task_id"]').val();
        var userId = jQuery('#approve-time-btn input[name="user_id"]').val();

        jQuery('#time_history_div table tbody').html('');
        $.ajax({
            url: "{{ route('task.time.history.approve.sendMessage') }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                id: issueId,
                user_id: userId,
            },
            success: function(data) {
                toastr['success'](data.message, 'success');
            }
        });
        jQuery('#time_history_modal').modal('hide');
    });

    jQuery(document).on('click', '.approved_history', function() {
        var data = jQuery(this).data('history');
        //var issueId = jQuery(this).data('id');
        var issueId = jQuery('#developer_task_id').val();

        jQuery('#approve_history_form table tbody').html('');
        jQuery.ajax({
            url: "{{ route('development/time/history/approved') }}",
            data: {
                id: issueId
            },
            success: function(data) {
                if (data != 'error') {
                    jQuery('input[name="developer_task_id"]').val(issueId);
                    jQuery.each(data, function(i, item) {
                        if (item['is_approved'] == 1) {
                            var checked = 'checked';
                        } else {
                            var checked = '';
                        }
                        jQuery('#approve_history_form table tbody').append(
                            '<tr>\
                            <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                            <td>' + ((item['old_value'] != null) ? item['old_value'] : '-') + '</td>\
                            <td>' + item['new_value'] + '</td>\<td>' + item['name'] + '</td>\
                        </tr>'
                        );
                    });
                }
            }
        });

        jQuery('#ApprovedHistory').modal('show');
    });

    jQuery(document).on('submit', '#approve-time-btn', function(event) {
        event.preventDefault();
        <?php if (auth()->user()->isAdmin()) { ?>
            jQuery.ajax({
                url: "{{route('task.time.history.approve')}}",
                type: 'POST',
                data: jQuery(this).serialize(),
                success: function(response) {
                    toastr['success']('Successfully approved', 'success');
                    jQuery('#time_history_modal').modal('hide');
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message);
                }
            });
        <?php } ?>
    });
</script>
@endpush