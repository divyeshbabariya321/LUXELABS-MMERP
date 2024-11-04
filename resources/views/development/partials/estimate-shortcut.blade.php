<script>
if(typeof estimateCurrTaskInformationTaskId === 'undefined') {
    var estimateCurrTaskInformationTaskId = 0;
    var estimatetaskType = 0;
}

if(typeof funGetTaskInformationModal === 'undefined') {
    function funGetTaskInformationModal() {
        return jQuery('#estimateModalTaskInformationUpdates');
    }
}

if(typeof estimatefunTaskInformationModal === 'undefined') {
    function estimatefunTaskInformationModal(ele, taskId, tasktype) {
        siteLoader(1);
        estimatetaskType = tasktype;
        estimateCurrTaskInformationTaskId = taskId;

        let url = '';
        let mdl = null;
        if (tasktype == "DEVTASK") {
            url = "{!! route('development.task.get') !!}";
            mdl = jQuery('#estimateModalTaskInformationUpdates');
        } else {
            mdl = jQuery('#estimateGeneralmodalTaskInformationUpdates');
            url = "{!! route('task.information.get') !!}";
        }
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            data: {
                id: taskId,
            },
        }).done(function(res) {
            siteLoader(0);
            if (res.data) {
                if(estimatetaskType == "DEVTASK") {
                    mdl.find('input[name="start_date"]').val(res.data.start_date);
                    mdl.find('input[name="estimate_date"]').val(res.data.estimate_date);
                    mdl.find('input[name="cost"]').val(res.data.cost);
                    mdl.find('input[name="estimate_minutes"]').val(res.data.estimate_minutes);
                    mdl.find('input[name="lead_estimate_time"]').val(res.data.lead_estimate_time);
                    mdl.find('input[name="remark"]').val('');
                    mdl.find('input[name="lead_remark"]').val('');

                    mdl.find('.cls-actual_start_date').html(res.data.actual_start_date ? res.data.actual_start_date : '-');
                    mdl.find('.cls-actual_end_date').html(res.data.actual_end_date ? res.data.actual_end_date : '-');

                    mdl.find('.estimate-show-time-history').attr('data-id', res.data.id);
                    mdl.find('.estimate-show-time-history').attr('data-userid', res.data.user_id);

                    if (mdl.find('.show-lead-time-history').length) {
                        mdl.find('.show-lead-time-history').attr('data-id', res.data.id);
                    }
                } else {
                    mdl.find('input[name="start_date"]').val(res.data.task_start_date); //start_date
                    mdl.find('input[name="due_date"]').val(res.data.task_new_due_date); //due_date
                    mdl.find('input[name="cost"]').val(res.data.cost);
                    mdl.find('input[name="approximate"]').val(res.data.approximate);
                    mdl.find('textarea[name="remark"]').val(res.data.task_remark);

                    mdl.find('.cls-actual_start_date').html(res.data.actual_start_date ? res.data.actual_start_date : '-');
                    mdl.find('.cls-actual_end_date').html(res.data.actual_end_date ? res.data.actual_end_date : '-');

                    mdl.find('.estimate-general-show-time-history').attr('data-id', res.data.id);
                    mdl.find('.estimate-general-show-time-history').attr('data-user_id', res.data.assign_to);
                    mdl.modal("show");
                }

                if(res.user){
                    mdl.find(".task_user").html(`(${res.user.name || '-'})`)
                } else {
                    mdl.find(".task_user").html(``)
                }
                mdl.modal("show");
            } else {
                siteErrorAlert(res);
            }
        }).fail(function(err) {
            siteLoader(0);
            siteErrorAlert(err);
        });
    }
}
if(typeof estimatefunTaskInformationUpdates === 'undefined') {
    function estimatefunTaskInformationUpdates(type) {
        if (type == 'start_date') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = $('#estimateModalTaskInformationUpdates');
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.start-date') }}",
                    type: 'POST',
                    data: {
                        id: estimateCurrTaskInformationTaskId,
                        value: mdl.find('input[name="start_date"]').val(),
                        estimatedEndDateTime: mdl.find('input[name="estimate_date"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'estimate_date') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = $('#estimateModalTaskInformationUpdates');
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.estimate-date') }}",
                    type: 'POST',
                    data: {
                        id: estimateCurrTaskInformationTaskId,
                        value: mdl.find('input[name="estimate_date"]').val(),
                        remark: mdl.find('input[name="remark"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'estimate_minutes') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = $("#estimateModalTaskInformationUpdates");
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.estimate-minutes') }}",
                    type: 'POST',
                    data: {
                        issue_id: estimateCurrTaskInformationTaskId,
                        estimate_minutes: mdl.find('input[name="estimate_minutes"]').val(),
                        remark: mdl.find('textarea[name="remark"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        }

    }
}

function estimateGeneralfunTaskInformationUpdates(type,id) {
    if (type == 'start_date') {
        if (confirm('Are you sure, do you want to update?')) {
            siteLoader(1);
            let mdl = funGetTaskInformationModal();
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('task.update.start-date') }}",
                type: 'POST',
                data: {
                    task_id: id,
                    value: $('input[name="start_dates'+id+'"]').val(),
                }
            }).done(function(res) {
                siteLoader(0);
                siteSuccessAlert(res);
            }).fail(function(err) {
                siteLoader(0);
                siteErrorAlert(err);
            });
        }
    } else if (type == 'due_date') {
        if (confirm('Are you sure, do you want to update?')) {
            siteLoader(1);
            let mdl = funGetTaskInformationModal();
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('task.update.due-date') }}",
                type: 'POST',
                data: {
                    task_id: id,
                    value: $('input[name="due_dates'+id+'"]').val(),
                }
            }).done(function(res) {
                siteLoader(0);
                siteSuccessAlert(res);
            }).fail(function(err) {
                siteLoader(0);
                siteErrorAlert(err);
            });
        }
    } else if (type == 'cost') {
        if (confirm('Are you sure, do you want to update?')) {
            siteLoader(1);
            let mdl = funGetTaskInformationModal();
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('task.update.cost') }}",
                type: 'POST',
                data: {
                    task_id: id,
                    cost: mdl.find('input[name="cost"]').val(),
                }
            }).done(function(res) {
                siteLoader(0);
                siteSuccessAlert(res);
            }).fail(function(err) {
                siteLoader(0);
                siteErrorAlert(err);
            });
        }
    } else if (type == 'approximate') {
        if (confirm('Are you sure, do you want to update?')) {
            siteLoader(1);
            let mdl = funGetTaskInformationModal();
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('task.update.approximate') }}",
                type: 'POST',
                data: {
                    task_id: id,
                    approximate: $('input[name="approximates'+id+'"]').val(),
                    remark: mdl.find('textarea[name="remark"]').val(),
                }
            }).done(function(res) {
                siteLoader(0);
                siteSuccessAlert(res);
            }).fail(function(err) {
                siteLoader(0);
                siteErrorAlert(err);
            });
        }
    }
}

if(typeof estimatefunTaskHistories === 'undefined') {
    function estimatefunTaskHistories(type) {
        if (type == 'start_date' || type == 'estimate_date' || type == 'cost') {
            siteLoader(1);
            let mdl = jQuery('#estimateModalTaskHistories');
            mdl.find('input[name="type"]').val(type);

            let url = '';

            if (type == 'start_date') {
                mdl.find('.modal-title').html('Estimated Start Datetime History');
                url = "{{ route('development.history.start-date.index') }}";
            } else if (type == 'estimate_date') {
                mdl.find('.modal-title').html('Estimated End Datetime History');
                url = "{{ route('development.history.estimate-date.index') }}";
            } else if (type == 'cost') {
                mdl.find('.modal-title').html('Cost History');
                url = "{{ route('development.history.cost.index') }}";
            }

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                data: {
                    id: estimateCurrTaskInformationTaskId
                }
            }).done(function(response) {
                mdl.find('.modal-body').html(response.data);
                mdl.modal('show');
                siteLoader(0);
            }).fail(function(err) {
                siteLoader(0);
                siteErrorAlert(err);
            });
        }
    }
}

jQuery(document).on('click', '.estimate-show-time-history', function() {

        var userId = jQuery(this).attr('data-userid');
        var issueId = jQuery(this).attr('data-id');
        jQuery('#estimate_time_history_div table tbody').html('');

        jQuery.ajax({
            url: "{{ route('development/time/history') }}",
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
                        jQuery('#estimate_time_history_div table tbody').append(
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
                    jQuery('#estimate_time_history_div table tbody').append(
                        '<input type="hidden" name="user_id" value="' + userId + '" class=" "/>'
                    );
                }
                $("#hidden_task_type").val("DEVTASK")
                jQuery('#estimate_time_history_modal').modal('show');
            }
        });
    });

    function estimateGeneralTaskInformationUpdatesTime(type) {
        if (type == 'start_date') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = $("#estimateGeneralmodalTaskInformationUpdates");
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('task.update.start-date') }}",
                    type: 'POST',
                    data: {
                        task_id: estimateCurrTaskInformationTaskId,
                        value: mdl.find('input[name="start_date"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'due_date') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = $("#estimateGeneralmodalTaskInformationUpdates");
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('task.update.due-date') }}",
                    type: 'POST',
                    data: {
                        task_id: estimateCurrTaskInformationTaskId,
                        value: mdl.find('input[name="due_date"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        }  else if (type == 'approximate') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = $("#estimateGeneralmodalTaskInformationUpdates");
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('task.update.approximate') }}",
                    type: 'POST',
                    data: {
                        task_id: estimateCurrTaskInformationTaskId,
                        approximate: mdl.find('input[name="approximate"]').val(),
                        remark: mdl.find('textarea[name="remark"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        }
    }

    jQuery(document).on('click', '.estimate-general-show-time-history', function() {
        var userId = jQuery(this).attr('data-user_id');
        var issueId = jQuery(this).attr('data-id');
        jQuery('#estimate_time_history_div table tbody').html('');
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
                        jQuery('#estimate_time_history_div table tbody').append(
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
                    jQuery('#estimate_time_history_div table tbody').append(
                        '<input type="hidden" name="user_id" value="' + userId + '" class=" "/>'
                    );
                }
                $("#hidden_task_type").val("TASK")
                jQuery('#estimate_time_history_modal').modal('show');
            }
        });
    });


    function estimateGeneralfunTaskHistories(type) {
        if (type == 'start_date' || type == 'due_date' || type == 'cost') {
            siteLoader(1);
            let mdl = jQuery('#estimateModalTaskHistories');
            let url = '';

            mdl.find('.cls-save').removeClass('d-none');
            mdl.find('input[name="type"]').val(type);

            if (type == 'start_date') {
                mdl.find('.modal-title').html('Estimated Start Datetime History');
                url = "{{ route('task.history.start-date.index') }}";
            } else if (type == 'due_date') {
                mdl.find('.modal-title').html('Estimated End Datetime History [Due Datetime]');
                url = "{{ route('task.history.due-date.index') }}";
            } else if (type == 'cost') {
                mdl.find('.modal-title').html('Cost History');
                url = "{{ route('task.history.cost.index') }}";
                mdl.find('.cls-save').addClass('d-none');
            }

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                data: {
                    id: estimateCurrTaskInformationTaskId
                }
            }).done(function(response) {
                mdl.find('.modal-body').html(response.data);
                mdl.modal('show');
                siteLoader(0);
            }).fail(function(err) {
                siteLoader(0);
                siteErrorAlert(err);
            });
        }
    }

    $(document).on("submit", "#estimate-approve-time-btn", function (event) {
        event.preventDefault();
        var type = $("#hidden_task_type").val();
        siteLoader(1);
        if (type == "TASK") {
            $.ajax({
                url: "/task/time/history/approve",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    siteLoader(0);
                    toastr["success"]("Successfully approved", "success");
                    $("#estimate_time_history_modal").modal("hide");
                },
                error: function (error) {
                    siteLoader(0);
                    toastr["error"](error.responseJSON.message);
                },
            });
        } else {
            $.ajax({
                url: "/development/time/history/approve",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    toastr["success"]("Successfully approved", "success");
                    $("#estimate_time_history_modal").modal("hide");
                    siteLoader(0);
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                    siteLoader(0);
                },
            });
        }
    });

</script>
<div id="estimateModalTaskInformationUpdates" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task's Information Update <span class="task_user"></span></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @php
                $cls_1 = 'col-md-8';
                $cls_2 = 'col-md-4';
                @endphp
                <div class="row">
                    <div class="col-md-4">
                        <label>Estimated Time: [In Minutes]</label>
                        <div class="form-group">
                            <input type="number" class="form-control" name="estimate_minutes" value="" min="1" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Remark:</label>
                        <div class="form-group">
                            <textarea class="form-control" name="remark" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="estimatefunTaskInformationUpdates('estimate_minutes')">Update</button>
                            <button type="button" class="btn btn-default estimate-show-time-history">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Estimated Start Datetime:</label>
                        <div class="form-group">
                            <div class='input-group date cls-start-due-date'>
                                <input type="text" class="form-control" name="start_date" value="" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="estimatefunTaskInformationUpdates('start_date')">Update</button>
                            <button type="button" class="btn btn-default" onclick="estimatefunTaskHistories('start_date')">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Estimated End Datetime: [Due Date]</label>
                        <div class="form-group">
                            <div class='input-group date cls-start-due-date'>
                                <input type="text" class="form-control" name="estimate_date" value="" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="estimatefunTaskInformationUpdates('estimate_date')">Update</button>
                            <button type="button" class="btn btn-default" onclick="estimatefunTaskHistories('estimate_date')">History</button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="estimateGeneralmodalTaskInformationUpdates" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task's Information Update  <span class="task_user"></span></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @php
                $cls_1 = 'col-md-8';
                $cls_2 = 'col-md-4';
                @endphp
                <div class="row">
                    <div class="col-md-4">
                        <label>Estimated Time: [In Minutes]</label>
                        <div class="form-group">
                            <input type="number" class="form-control" name="approximate" value="" min="1" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Remark:</label>
                        <div class="form-group">
                            <textarea class="form-control" name="remark" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="estimateGeneralTaskInformationUpdatesTime('approximate')">Update</button>
                            <button type="button" class="btn btn-default estimate-general-show-time-history">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Estimated Start Datetime:</label>
                        <div class="form-group">
                            <div class='input-group date cls-start-due-date'>
                                <input type="text" class="form-control" name="start_date" value="" autocomplete="off" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="estimateGeneralTaskInformationUpdatesTime('start_date')">Update</button>
                            <button type="button" class="btn btn-default" onclick="estimateGeneralfunTaskHistories('start_date')">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Estimated End Datetime: [Due Date]</label>
                        <div class="form-group">
                            <div class='input-group date cls-start-due-date'>
                                <input type="text" class="form-control" name="due_date" value="" autocomplete="off" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="estimateGeneralTaskInformationUpdatesTime('due_date')">Update</button>
                            <button type="button" class="btn btn-default" onclick="estimateGeneralfunTaskHistories('due_date')">History</button>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="estimate_time_history_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Estimated Time History</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" id="estimate-approve-time-btn" method="POST">
                @csrf
                <input type="hidden" name="hidden_task_type" id="hidden_task_type">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="developer_task_id" id="developer_task_id">

                        <div class="col-md-12" id="estimate_time_history_div">
                            <table class="table">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    @if (auth()->user()->isReviwerLikeAdmin())
                    <button type="submit" class="btn btn-secondary">Confirm</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
<div id="estimateModalTaskHistories" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <style>
            #estimateModalTaskHistories tbody tr td:first-child{
                display: none
            }
            #estimateModalTaskHistories thead tr th:first-child{
                display: none
            }
        </style>
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="type" value="">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="estimateGeneralmodalTaskInformationUpdates" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task's Information Update</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @php
                $cls_1 = 'col-md-8';
                $cls_2 = 'col-md-4';
                @endphp
                <div class="row">
                    <div class="col-md-4">
                        <label>Estimated Time: [In Minutes]</label>
                        <div class="form-group">
                            <input type="number" class="form-control" name="approximate" value="" min="1" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Remark:</label>
                        <div class="form-group">
                            <textarea class="form-control" name="remark" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('approximate')">Update</button>
                            <button type="button" class="btn btn-default estimate-general-show-time-history">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Estimated Start Datetime:</label>
                        <div class="form-group">
                            <div class='input-group date cls-start-due-date'>
                                <input type="text" class="form-control" name="start_date" value="" autocomplete="off" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('start_date')">Update</button>
                            <button type="button" class="btn btn-default" onclick="funTaskHistories('start_date')">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Estimated End Datetime: [Due Date]</label>
                        <div class="form-group">
                            <div class='input-group date cls-start-due-date'>
                                <input type="text" class="form-control" name="due_date" value="" autocomplete="off" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('due_date')">Update</button>
                            <button type="button" class="btn btn-default" onclick="funTaskHistories('due_date')">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Cost:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="cost" value="" autocomplete="off" />
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('cost')">Update</button>
                            <button type="button" class="btn btn-default" onclick="funTaskHistories('cost')">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="col-md-6">
                        <label>Actual Start Time:</label>
                        <div class="form-group cls-actual_start_date"></div>
                    </div>
                    <div class="col-md-6">
                        <label>Actual End Time:</label>
                        <div class="form-group cls-actual_end_date"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
