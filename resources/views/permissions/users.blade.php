@extends('layouts.app')


@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <style type="text/css">
        .dtHorizontalExampleWrapper {
            max-width: 600px;
            margin: 0 auto;
        }
        #dtHorizontalExample th, td {
            white-space: nowrap;
        }

        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting:before,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_asc_disabled:after,
        table.dataTable thead .sorting_asc_disabled:before,
        table.dataTable thead .sorting_desc:after,
        table.dataTable thead .sorting_desc:before,
        table.dataTable thead .sorting_desc_disabled:after,
        table.dataTable thead .sorting_desc_disabled:before {
            bottom: .5em;
        }
        .but{
            background-color: lightblue;
            border-radius: 29px;
            border: 0;
        }
    </style>

@endsection

@section('content')
    <div class="col-12">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2> Permissions </h2>
                </div>

                <div class="pull-right">
                    <a class="btn btn-secondary mt-3" href="{{ route('permissions.index') }}"> Back</a>
                </div>
            </div>

            <div class="col-lg-12 margin-tb">
                <div class="pull-right">
                    <form action="{{ route('permissions.users') }}" method="get" class="mb-2 d-flex">
                            <div class="form-group mt-2 mr-3">
                                <button id="permission_delete" type="button" class="btn btn-secondary delete_all" value="" data-id="">Remove Permission</button>
                            </div>
                            <div class="form-group mr-2">
                                <select name="search_user[]" id="search_user" class="form-control search_user" multiple>
                                    @foreach($user_datas as $user)
                                        @if(!empty(Request::get('search_user')))
                                            <option value="{{ $user->id }}" {{ in_array($user->id,Request::get('search_user'))?'selected':''}}>{{ $user->name }}</option>
                                        @else
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <select name="assign_permission[]" id="assign_permission" class="form-control assign_permission" multiple>
                                    @if(Request::get('assign_permission'))
                                        <option value="1" {{ in_array('1',Request::get('assign_permission'))?'selected':''}}>Activated Permission</option>
                                        <option value="0" {{ in_array('0',Request::get('assign_permission'))?'selected':''}}>Deactivated Permission</option>
                                    @else
                                        <option value="1">Activated Permission</option>
                                        <option value="0">Deactivated Permission</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="search_row[]" id="search_row" class="form-control search_row select2" multiple>
                                    @foreach($permission_datas as $permission)
                                        @if(!empty(Request::get('search_row')))
                                            <option value="{{ $permission->name }}" {{ in_array($permission->name,Request::get('search_row'))?'selected':''}}>{{ $permission->name }}</option>
                                        @else
                                            <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        <button type="submit" class="btn btn-secondary ml-3 mb-3" href=""><i class="fa fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
        <div class="col-xs-12 col   -sm-12 col-md-12">
            <div class="form-group">
                <div class="table-wrapper-scroll-y my-custom-scrollbar" style="overflow: scroll;">
                    <table id="dtHorizontalExample" class="table table-striped table-bordered table-sm" cellspacing="0"
                           width="100%">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="master"></th>
                            <th>Sr</th>
                            <th>Users </th>
                            @foreach($permissions as $permission)
                                @if(in_array($permission->name, $user->permissions->pluck('name')->toArray()))
                                    <th>{{ $permission->name }}</th>
                                @else
                                    <th>{{ $permission->name }}</th>
                                @endif
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr id="tr_{{$user->id}}">
                                <td><input type="checkbox" class="sub_chk" name="user_id[]" data-id="{{$user->id}}" multiple></td>
                                <td>{{++$i }}</td>
                                <td><a href="/users/{{ $user->id }}/edit">{{ $user->name }} ({{ (!empty($user->permissions)) ? count($user->permissions) :'' }})</a></td>
                                @foreach($permissions as $permission)
                                    <td>
                                        @if(in_array($permission->name, $user->permissions->pluck('name')->toArray()))
                                            <button class="but" onclick="activatePermission({{$permission->id}},{{$user->id}},1)" style="background-color: lightgreen !important;"><img src="{{asset('/images/icons-checkmark.png') }}" height="10" width="10"/>
                                            </button>
                                        @else
                                            <button class="but" onclick="activatePermission({{$permission->id}},{{$user->id}},0)"><img src="{{asset('/images/icons-delete.png') }}" height="10" width="10"/>
                                            </button>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>

            </div>
            <div class="float-right">
                {{ $users->appends(request()->all())->links() }}
            </div>
        </div>

    </div>
    </div>
@endsection

@section('scripts')

    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        $('.select2').select2({
            placeholder: 'Select Permission',
        });
        $('.assign_permission').select2({
            placeholder: 'Select Status',
        });
        $('.search_user').select2({
            placeholder: 'Select User',
        });
        $('.select2-search__field').css('width', '100%');
        // $(document).ready(function () {
        //     $('#dtHorizontalExample').DataTable({
        //         "scrollX": true,
        //     });
        //     $('.dataTables_length').addClass('bs-select');
        // });

        function activatePermission($permission_id , $user_id , $is_Active) {
            if($permission_id == null && $user_id == null){
                alert('Failed To Update')
            }else{
            $.ajax({
                type: "POST",
                url: "/api/users/updatePermission",
                data: {"_token": "{{ csrf_token() }}","user_id": $user_id , "permission_id" : $permission_id ,"is_active" : $is_Active },
                dataType: "json",
                success: function(message) {
                    alert(message.message);
                    location.reload(true);
                }, error: function(){
                    alert('Failed adding Permission');
                }

            });
            }
        }




        $('#master').on('click', function(e) {

            if($(this).is(':checked',true))
            {
                $(".sub_chk").prop('checked', true);
            } else {
                $(".sub_chk").prop('checked',false);
            }
        });

        var x = [];
        $("input[type='checkbox']").change(function(){
            var id = $(this).attr('data-id');
            if(this.checked){
                x.push(id);
            }
            else {
                var index = x.indexOf(id);
                x.splice(index, 1);
            }
            $('#permission_delete').val(x.join(','));
        });

        var x = [];
        $("#master").change(function(){
            $.each($("input[name='user_id[]']:checked"), function(){
                x.push($(this).data('id'));
            });

            $('#permission_delete').val(x.join(','));
        });

        $('#permission_delete').on('click', function(e) {
            var $user_id = $('#permission_delete').val();

            $.ajax({
                type: "get",
                url: "/permissions/grandaccess/delete",
                data: {"_token": "{{ csrf_token() }}","user_id": $user_id},
                dataType: "json",
                success: function(message) {
                    location.reload(true);
                }
            });
        });



    </script>
@endsection
