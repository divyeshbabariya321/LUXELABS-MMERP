@extends('layouts.app')

@section('styles')

    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="col-lg-12">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Role Management (<span id="roles_count">{{ $roles->total() }}</span>)</h2>
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group cls_filter_inputbox" style="width: 200px;">
                                <select name="term[]" class="form-control selectpicker" title="select roles" multiple id="term">
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="{{asset('/images/filter.png')}}"/></button>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="{{asset('/images/resend2.png')}}"/></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                @if(auth()->user()->checkPermission('roles-create'))
                    <button class="btn btn-secondary" style="color:white;" data-toggle="modal" data-target="#newCreateRole"> +</button>
                @endif
            </div>
        </div>
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered" id="roles-table">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th width="280px">Action</th>
            </tr>
            </thead>
            <tbody>
            @include('roles.partials.list-roles')
            </tbody>
        </table>
    </div>


    {!! $roles->render() !!}
    </div>
    <div id="newCreateRole" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {{ html()->form('POST', route('roles.store'))->open() }}
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Role Management</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" style="text-align: -webkit-right;">
                            <div class="form-group cls_filter_inputbox" style="width: 200px;">
                                {{ html()->text('search_role')->placeholder('Search Permission')->class('form-control search_role') }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 text-right">
                            <button type="submit" class="btn btn-primary form-save-btn">Save changes</button>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                {{ html()->text('name')->placeholder('Name')->class('form-control') }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group permissions">
                                <strong>Permission:</strong>
                                <br/>
                                @foreach($permission as $value)
                                    <label>{{ html()->checkbox('permission[]', false, $value->id)->class('name mt-3 h-auto') }}
                                        <span> {{ $value->name }} </span></label>
                                    <br/>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="ajaxModelexaedit" tabindex="-1" aria-labelledby="ModalLabel"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Edit</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form id="postForm1" name="postForm1">
                        <div class="col-xs-12 col-sm-12 col-md-12 text-right">
                            <button type="button" class="add btn btn-primary" name="Editdata" id="Editdata" value="create">Submit</button>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Name:</strong>
                                    <input type="hidden" name="product_id" id="product_id">
                                    <input type="text" name="role_name" class="form-control" id="role_name" placeholder="Enter Name" value="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Permission:</strong>
                                    <br/>
                                    @foreach($permission as $value)
                                        <label><input class="name mt-3 h-auto" name="permission1[]" type="checkbox" value="{{ $value->id }}" id="check{{ $value->id }}">
                                            {{ $value->name }}</label>
                                        <br/>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="newCreateRole" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {{ html()->form('POST', route('roles.store'))->open() }}
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Role Management</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12" style="text-align: -webkit-right;">
                                <div class="form-group cls_filter_inputbox" style="width: 200px;">
                                    {{ html()->text('search_role')->placeholder('Search Permission')->class('form-control search_role') }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Name:</strong>
                                    {{ html()->text('name')->placeholder('Name')->class('form-control') }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group permissions">
                                    <strong>Permission:</strong>
                                    <br/>
                                    @foreach($permission as $value)
                                            <label>{{ html()->checkbox('permission[]', false, $value->id)->class('name mt-3 h-auto') }}
                                               <span> {{ $value->name }} </span></label>
                                            <br/>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary form-save-btn">Save changes</button>
                </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript">
        $('.select-multiple').select2({width: '100%'});

        function submitSearch(){
            src = '/roles'
            term = $('#term').val()
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term : term,
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                $("#roles-table tbody").empty().html(data.tbody);
                $("#roles_count").text(data.count);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });

        }

        function resetSearch(){
            src = '/roles'
            blank = ''
            $.ajax({
                url: src,
                dataType: "json",
                data: {

                    blank : blank,

                },
                beforeSend: function () {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                $('#term').val('')
                $('#user-select').val('')
                $("#roles-table tbody").empty().html(data.tbody);
                $("#roles_count").text(data.count);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        }

        $('body').on('click', '.edit', function () {
            var id = $(this).val();
            $('.modal-title').text('Edit');
            $('#action_button').val('Edit');
            $('#action').val('Edit');
            $('#form_result').html('');
            $('#ajaxModelexaedit').modal('show');
            $('#product_id').val(id);
            var url = '{{ route('roles.edit',':id') }}';
            url = url.replace(':id', id);
            $.get(url, function (data) {
                $('#role_name').val(data.role.name);
                $.each(data.rolePermissions, function (key, val) {
                    if (val.is_active == 1)
                    {
                        $('#check'+val.id).prop('checked', true);
                    }else{
                        $('#check'+val.id).prop('checked', false);
                    }
                });
                $('#modelHeading').html("Edit Product");
                $('#saveBtn').val("edit-user");
                $('#ajaxModelexaedit').modal('show');
            })
        });

        $('body').on('click', '.view', function () {
            var id = $(this).val();
            $('.modal-title').text('Edit');
            $('#action_button').val('Edit');
            $('#action').val('Edit');
            $('#form_result').html('');
            $('#ajaxModelexaview').modal('show');
            $('#product_id').val(id);
            var url = '{{ route('roles.show',':id') }}';
            url = url.replace(':id', id);
            $.get(url, function (data) {
                console.log(data.role.name);
                $('.view_role_name').html(data.role.name);
                var aa = [];
                $.each(data.rolePermissions, function (key, val) {
                    if (val.is_active == 1)
                    {
                        aa.push('<label class="label label-success mr-2">'+val.name+'</label>');
                    }
                });
                $('.view_permission').html(aa);
                $('#modelHeading').html("Edit Product");
                $('#saveBtn').val("edit-user");
                $('#ajaxModelexaview').modal('show');
            })
        });

        $('#Editdata').click(function (e) {
            e.preventDefault();
            $(this).html('Sending..');
            var id = $("#product_id").val();
            var url = '{{ route("roles.update", ":id") }}';
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                method:'POST',
                dataType: 'json',
                data: $('#postForm1').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    location.reload();
                },
            });
        });

        $('.search_role').on('keyup',function(){
            var search_role = $('.search_role').val();
            var permission = $.map($(':checkbox[name=permission\\[\\]]:checked'), function(n, i){
                return n.value;
            }).join(',');

            $.ajax({
                url: "{{route('search_role')}}",
                dataType: "json",
                data: {search_role: search_role,permission:permission},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                $('.permissions').html(data);
            });
        });
    

        $('.search_role').on('keyup',function(){
            var search_role = $('.search_role').val();
            $.ajax({
                url: "{{route('search_role')}}",
                dataType: "json",
                data: {search_role: search_role},
                beforeSend: function () {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                var html = '<strong>Permission:</strong><br/>';
                $.each(data, function(k, v) {
                    html +='<label><input class="name mt-3 h-auto" name="permission[]" type="checkbox" value="'+ k.id +'"><span style="padding-left: 4px;">'+ v.name+ '</span></label><br/>';
                });
                console.log(html);
                $('.permissions').html(html);

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        });
</script>

@endsection
