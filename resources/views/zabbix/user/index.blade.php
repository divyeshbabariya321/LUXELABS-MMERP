@extends('layouts.app')

@section('content')
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Zabbix Users <span class="count-text"></span></h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb" id="page-view-result">
            <div class="col-lg-12 pl-5 pr-5">
                <div style="display: flex !important; float: right !important;">
                    <div>
                        <a href="#" class="btn btn-xs btn-secondary create-new-user">Create</a>
                    </div>
                    <div>
                        <a href="{{ route('zabbix.user.roles') }}" class="btn btn-xs btn-success">Roles</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 pl-5 pr-5">
            <form action="/store-website/generate-api-token" method="post">
                <?php echo csrf_field(); ?>

                <div class="col-md-12">
                    <div class="table-responsive mt-3">
                        @include('zabbix.user.list')
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="modal fade" id="user-create-new" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Save User</b></h5>
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
                                                   placeholder="Enter id" id="user-id">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" class="form-control" name="name"
                                                       placeholder="Enter name" id="user-name">
                                            </div>
                                            <div class="form-group">
                                                <label>Surname</label>
                                                <input type="text" class="form-control" name="surname"
                                                       placeholder="Enter surname" id="user-surname">
                                            </div>
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" class="form-control" name="username"
                                                       placeholder="Enter username" id="user-username">
                                            </div>
                                            <div class="form-group">
                                                <label>Roles</label>
                                                <select id="user-role-id" class="form-control input-sm career-store-websites"
                                                name="role_id" required>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role['roleid'] }}">{{ $role['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Password</label>
                                                <input type="text" class="form-control" name="password"
                                                       placeholder="Enter password" id="user-password">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit"
                                                    class="btn btn-danger submit_delete_user float-left float-lg-left"
                                                    data-id="">
                                                Delete
                                            </button>
                                            <button type="submit"
                                                    class="btn btn-secondary submit_create_user float-right float-lg-right">
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
        $(document).on("click", ".create-new-user", function (e) {
            e.preventDefault();
            $('#user-create-new').modal('show');
            restoreForm();
        });
        $("#user-role-id").select2();
        $(document).on("click", ".submit_delete_user", function (e) {
            e.preventDefault();
            let userId = $(this).attr('data-id');
            var url = "{{ route('zabbix.user.delete') }}?id="+userId+"";
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

        $(document).on("click", ".submit_create_user", function (e) {
            e.preventDefault();
            var url = "{{ route('zabbix.user.save') }}";
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
                        let user = resp.user;
                        let userId = user.id;
                        console.log('.td-description-' + userId);
                        $('.td-description-' + userId).text(user.description);
                        $('.td-type-' + userId).text(user.type);
                        $('.td-location-' + userId).text(user.location);
                        $('.td-created-at-' + userId).text(user.created_at);
                        $('.td-store-websites-' + userId).text(user.store_website_id);
                        $('.td-edit-' + userId).attr('data-json', resp.user_json);
                        if (!user.is_active) {
                            $('.td-is-active-' + userId).removeAttr('checked');
                        } else {
                            $('.td-is-active-' + userId).attr('checked', user.is_active);
                        }


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

        $('a.btn-edit-user').click(function(e) {
            e.preventDefault();
            $('#user-create-new').modal('show');

            restoreForm();

            $('#user-id').val($(this).attr('data-id'));

            let data = JSON.parse($(this).attr('data-json'));

            $('#user-name').val(data.name);
            $('#user-surname').val(data.surname);
            $('#user-username').val(data.username);
            $('#user-url').val(data.url);
            $('#user-role-id').val(data.role_id);
            $('#user-id').val(data.id);
            $('.submit_delete_user').attr('data-id', data.id);
            $("#user-role-id option[value='" + data.role_id + "']").prop("selected", true);
            $("#user-role-id").select2();
        });

        var restoreForm = function() {
            $('.submit_delete_user').val('');
            $('#user-id').val('');
            $('#user-name').val('');
            $('#user-surname').val('');
            $('#user-username').val('');
            $('#user-role-id').val('');
            $('#user-url').val('');
            $('#user-password').val('');
        }
    </script>
@endsection