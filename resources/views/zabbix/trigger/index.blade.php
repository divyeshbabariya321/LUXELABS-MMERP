@extends('layouts.app')

@section('content')
<style>
    .gray-td {
        background-color: gray;
        color: #fff;
    }
    .orange-td {
        background-color: orange;
        color: #fff;
    }
    .red-td {
        background-color: red;
        color: #fff;
    }
</style>
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Zabbix Triggers <span class="count-text"></span></h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb" id="page-view-result">
            <div class="col-lg-12 pl-5 pr-5">
                <div style="display: flex !important; float: right !important;">
                    <div>
                        <a href="#" class="btn btn-xs btn-secondary create-new-trigger">Create</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 pl-5 pr-5">
            <form action="/store-website/generate-api-token" method="post">
                <?php echo csrf_field(); ?>

                <div class="col-md-12">
                    <div class="table-responsive mt-3" id="ajax-content">
                        @include('zabbix.trigger.list')
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="modal fade" id="trigger-create-new" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Save trigger</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form action="" method="post">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive mt-3">
                                            <input hidden type="text" class="form-control" name="id"
                                                   placeholder="Enter id" id="trigger-id">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" class="form-control" name="name"
                                                       placeholder="Enter name" id="trigger-name">
                                            </div>
                                            <div class="form-group">
                                                <label>Event name</label>
                                                <input type="text" class="form-control" name="event_name"
                                                       placeholder="Enter event name" id="trigger-event-name">
                                            </div>
                                            <div class="form-group">
                                                <label>Expression</label>
                                                <input type="text" class="form-control" name="expression"
                                                       placeholder="Enter expression" id="trigger-expression">
                                            </div>
                                            <div class="form-group">
                                                <label>Priority</label>
                                                <input type="text" class="form-control" name="severity"
                                                       placeholder="Enter priority" id="trigger-priority">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit"
                                                    class="btn btn-secondary submit_create_trigger float-right float-lg-right">
                                                Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).on("click", ".create-new-trigger", function (e) {
            e.preventDefault();
            $('#trigger-create-new').modal('show');
            restoreForm();
        });
        $("#trigger-template-id").select2({ width: 'resolve' });
        $(document).on("click", ".submit_delete_trigger", function (e) {
            e.preventDefault();
            var url = "{{ route('zabbix.trigger.index') }}";
            var formData = $(this).closest('form').serialize();

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (resp) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#store-create-project').modal('hide');
                    if (resp.code == 200) {
                        toastr["success"](resp.message);
                    } else {
                        toastr["error"](resp.message);
                    }
                },
                error: function (err) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#trigger-create-new').modal('hide');
                    toastr["error"](err.responseJSON.message);
                }
            })
        });

        $(document).on("click", ".btn-status-trigger", function (e) {
            e.preventDefault();
            let userId = $(this).attr('data-id');
            var url = "{{ route('zabbix.trigger.status') }}?id="+userId+"";
            var formData = $(this).closest('form').serialize();

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (resp) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#store-create-project').modal('hide');
                    if (resp.code == 200) {
                        toastr["success"](resp.message);
                    } else {
                        toastr["error"](resp.message);
                    }
                },
                error: function (err) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#user-create-new').modal('hide');
                    toastr["error"](err.responseJSON.message);
                }
            })
        });

        $(document).on("click", ".submit_create_trigger", function (e) {
            e.preventDefault();
            var url = "{{ route('zabbix.trigger.save') }}";
            var formData = $(this).closest('form').serialize();

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (resp) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#store-create-project').modal('hide');
                    if (resp.code == 200) {
                        toastr["success"](resp.message);
                    } else {
                        toastr["error"](resp.message);
                    }
                },
                error: function (err) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#trigger-create-new').modal('hide');
                    toastr["error"](err.responseJSON.message);
                }
            })
        });

        $('a.btn-edit-trigger').click(function(e) {
            e.preventDefault();
            $('#trigger-create-new').modal('show');

            restoreForm();

            $('#trigger-id').val($(this).attr('data-id'));

            let data = JSON.parse($(this).attr('data-json'));

            $('#trigger-name').val(data.name);
            $('#trigger-event-name').val(data.event_name);
            $('#trigger-expression').val(data.expression);
            $('#trigger-id').val(data.id);
            $('#trigger-priority').val(data.priority);
            $("#trigger-template-id option[value='" + data.template_id + "']").prop("selected", true);
            $('.submit_delete_trigger').attr('data-id', data.id);
            $("#trigger-template-id").select2({ width: '100%' });
        });

        var restoreForm = function() {
            $('#trigger-id').val('');
            $('#trigger-name').val('');
            $('#trigger-event-name').val('');
            $('#trigger-expression').val('');
            $('#trigger-priority').val('');
        }

        // $('.page-link').click(function (e) {
        //     e.preventDefault();

        //     var href = $(e).attr('href');

        //     $.ajax({
        //         url: href,
        //         method: 'GET',
        //         success: function (resp) {
        //             console.log(resp);
        //             $('#loading-image-preview').hide();
        //             $('#website-project-name').val("");
        //             $('#store-create-project').modal('hide');
        //             if (resp.code == 200) {
        //                 $('#fresh-page').empty();
        //                 $('#fresh-page').html(resp.tpl);
        //             } else {
        //                 toastr["error"](resp.message);
        //             }
        //         },
        //         error: function (err) {
        //             $('#loading-image-preview').hide();
        //             $('#website-project-name').val("");
        //             $('#trigger-create-new').modal('hide');
        //             toastr["error"](err.responseJSON.message);
        //         }
        //     })
        // })
    </script>
@endsection