@extends('layouts.app')

@section('favicon', 'password-manager.png')

@section('title', 'To Do List')

@section('styles')
    <style>
        .users {
            display: none;
        }
    </style>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Todo list search</h2>
            <div class="pull-left">
                <form action="{{ route('todolist') }}" method="GET" class="form-inline align-items-start">
                    <div class="form-group mr-3 mb-3">
                        <input name="search_title" type="text" class="form-control global" id="search_title"
                            value="{{ isset($search_title) ? $search_title : '' }}" placeholder="Title">
                    </div>
                    <div class="form-group mr-3 mb-3">
                        <select class="form-control global" id="search_status" name="search_status">
                            <option value="">Select Status</option>
                            @foreach($statuses as $status)
                                <option value="{{$status['id']}}" @if($status['id'] == $search_status) selected @endif>{{$status['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group ml-3">
                        <div class='input-group date' id='filter-date'>
                            <input type='text' class="form-control global" name="search_date"
                                value="{{ isset($search_date) ? $search_date : '' }}" placeholder="Date" id="search_date" />

                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group ml-3">
                        <select class="form-control global" id="search_todo_category_id" name="search_todo_category_id">
                            <option value="">Select Category</option>
                            @foreach($todoCategories as $todoCategory)
                                <option value="{{$todoCategory->id}}" @if($todoCategory->id == $search_todo_category_id) selected @endif>{{$todoCategory->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                </form>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-toggle="modal"
                    data-target="#todolistCreateModal">+</a>
                </button>
                &nbsp;
                <button type="button" class="btn btn-primary" data-toggle="modal"
                data-target="#statusList">List Status</a> </button>
                &nbsp;
                <button type="button" class="btn btn-primary" data-toggle="modal"
                    data-target="#todolistStatusCreateModal">Add Status</a> </button>
                <button type="button" class="btn btn-primary" data-toggle="modal"
                    data-target="#todolistCategoryCreateModal">Add Category</a> </button> &nbsp; &nbsp;
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Todo list ({{$todolists_count}})</h2>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="todolist-table">
            <thead>
                <tr>
                    <th width="4%">#</th>
                    <th width="20%">Title</th>
                    <th width="10%">Subject</th>
                    <th width="15%">Category</th>
                    <th width="10%">Status</th>
                    <th width="5%">Date</th>
                    <th width="15%">Remark</th>
                    <th width="9%">Actions</th>
                </tr>

                
            </thead>

            <tbody>

                @include('todolist.data')

                {!! $todolists->render() !!}

            </tbody>
        </table>
    </div>



    <div id="todolistCreateModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('todolist.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title">Create Todo List</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Title:</strong>
                            <input type="text" name="title" class="form-control add_title" value="{{ old('title') }}">

                            @if ($errors->has('title'))
                                <div class="alert alert-danger">{{ $errors->first('title') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Subject:</strong>
                            <input type="text" name="subject" class="form-control add_subject" value="{{ old('subject') }}">

                            @if ($errors->has('subject'))
                                <div class="alert alert-danger">{{ $errors->first('subject') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Category:</strong>
                            {{-- <input type="text" name="" class="form-control" value="{{ old('') }}" required> --}}
                            <select name="todo_category_id" class="form-control">
                            <option value="">Select Category</option>
                               @foreach($todoCategories as $todoCategory)
                                   <option value="{{$todoCategory->id}}" @if($todoCategory->id == old('todo_category_id')) selected @endif>{{$todoCategory->name}}</option>
                               @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="alert alert-danger">{{ $errors->first('status') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Status:</strong>
                            {{-- <input type="text" name="status" class="form-control" value="{{ old('status') }}" required> --}}
                            <select name="status" class="form-control">
                                @foreach ($statuses as $status )
                                <option value="{{$status['id']}}" @if (old('status') == $status['id']) selected @endif>{{$status['name']}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="alert alert-danger">{{ $errors->first('status') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Date:</strong>

                            <div class='input-group date' id='todo-date-1'>
                                <input type="text" class="form-control global" name="todo_date" placeholder="Date"
                                    value="{{ old('todo_date') }}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>

                            @if ($errors->has('todo_date'))
                                <div class="alert alert-danger">{{ $errors->first('todo_date') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Remark:</strong>
                            <input type="text" name="remark" class="form-control" value="{{ old('remark') }}">

                            @if ($errors->has('remark'))
                                <div class="alert alert-danger">{{ $errors->first('remark') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Store</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div id="todolistUpdateModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('todolist.update') }}" method="POST" id="edittodolist">
                    @csrf
                    <input type="hidden" name="id" />
                    <div class="modal-header">
                        <h4 class="modal-title">Update Todo List</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Title:</strong>
                            <input type="text" name="title" class="form-control edit_title" value="{{ old('title') }}">

                            @if ($errors->has('title'))
                                <div class="alert alert-danger">{{ $errors->first('title') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Subject:</strong>
                            <input type="text" name="subject" class="form-control edit_subject" value="{{ old('subject') }}">

                            @if ($errors->has('subject'))
                                <div class="alert alert-danger">{{ $errors->first('subject') }}</div>
                            @endif
                        </div>
                      <div class="form-group">
                            <strong>Category:</strong>
                            {{-- <input type="text" name="" class="form-control" value="{{ old('') }}" required> --}}
                            <select name="todo_category_id" class="form-control">
                            <option value="">Select Category</option>
                               @foreach($todoCategories as $todoCategory)
                                   <option value="{{$todoCategory->id}}" @if($todoCategory->id == old('todo_category_id')) selected @endif>{{$todoCategory->name}}</option>
                               @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="alert alert-danger">{{ $errors->first('status') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Status:</strong>
                            {{-- <input type="text" name="status" class="form-control" value="{{ old('status') }}" required> --}}
                            <select name="status" class="form-control">
                                @foreach ($statuses as $status )
                                    <option value="{{$status['id']}}" @if (old('status') == $status['id']) selected @endif>{{$status['name']}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="alert alert-danger">{{ $errors->first('status') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Date:</strong>

                            <div class='input-group date' id='todo-update-date'>
                                <input type="text" class="form-control global" name="todo_date" placeholder="Date"
                                    value="{{ old('todo_date') }}">

                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>

                            @if ($errors->has('todo_date'))
                                <div class="alert alert-danger">{{ $errors->first('todo_date') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Remark:</strong>
                            <input type="text" name="remark" class="form-control" value="{{ old('remark') }}" required>
                            <input type="hidden" name="old_remark" value=""/>
                            @if ($errors->has('remark'))
                                <div class="alert alert-danger">{{ $errors->first('remark') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Update</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div id="getRemarkHistory" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remark History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Remark</th>
                                <th>Created At</th>

                            </tr>
                        </thead>
                        <tbody class="table" id="data">


                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>

        </div>
    </div>

    <div id="todolistStatusCreateModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('todolist.status.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title">Create Todo List Status</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="status_name" class="form-control" value="{{ old('status_name') }}">

                            @if ($errors->has('status_name'))
                                <div class="alert alert-danger">{{ $errors->first('status_name') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Color:</strong>
                            <input type="color" name="status_color" class="form-control" value="{{ old('status_color') }}">
                        </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Store</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
  </div>

  <div id="statusList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">List Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('todolist-color-updates') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                        </tr>
                        <?php
                        foreach ($statuses as $status) { ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;<?php echo $status['name'] ?></td>
                            <td class="text-center"><?php echo $status['color']; ?></td>
                            <td class="text-center"><input type="color" name="color_name[<?php echo $status['id'] ?>]" class="form-control" data-id="<?php echo $status['id']; ?>" id="color_name_<?php echo  $status['id']; ?>" value="<?php echo $status['color']; ?>" style="height:30px;padding:0px;"></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit-status-color">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div id="todolistCategoryCreateModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('todolist.category.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title">Create Todo List Category</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="todo_category_name" class="form-control" value="{{ old('todo_category_name') }}">

                            @if ($errors->has('todo_category_name'))
                                <div class="alert alert-danger">{{ $errors->first('todo_category_name') }}</div>
                            @endif
                        </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Store</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
  </div>
@endsection


@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            $(".select-multiple").multiselect();
            $(".select-multiple2").select2();
        });

        $('#filter-date').datetimepicker({
            format: 'YYYY-MM-DD',
        });

        $('#todo-date-1').datetimepicker({
            format: 'YYYY-MM-DD',
        });

        $('#todo-update-date').datetimepicker({
            format: 'YYYY-MM-DD',
        });

        // $('.date').change(function(){
        //     alert('date selected');
        // });

        function changetodolist(id) {
            //$("#passwordEditModal" + id + "").modal('show');
           // $(document).on("click", ".edit-postman-btn", function(e) {
                //e.preventDefault();
                var $this = $(this);
                var id = id;
                $.ajax({
                url: "/todolist/edit/",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                }
                }).done(function(response) {
                if (response.code = '200') {
                    form = $('#edittodolist');
                    $.each(response.data, function(key, v) {
                        if (form.find('[name="' + key + '"]').length) {
                            form.find('[name="' + key + '"]').val(v);
                            if(key == 'remark')
                                form.find('[name="old_remark"]').val(v);
                        }
                    });
                    $('#todolistUpdateModal').modal('show');
                    toastr['success']('Edited successfully!!!', 'success');

                } else {
                    toastr['error'](response.message, 'error');
                }
                }).fail(function(errObj) {
                $('#loading-image').hide();
                $("#todolistUpdateModal").hide();
                toastr['error'](errObj.message, 'error');
                });
           // });
        }
        $(".check").change(function() {
            if (this.checked) {
                $(".users").show();
            } else {
                $(".users").hide();
            }
        });

        function getRemarkHistoryData(id) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ route('todolist.remark.history') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                dataType: "json",
                success: function(message) {
                    $c = message.length;
                    if ($c == 0) {
                        alert('No History Exist');
                    } else {
                        var detials = "";
                        $.each(message.data, function(key, value) {

                            detials += "<tr><td>" + value.id + "</td><td>" + value.username.name +
                                "</td><td>" + value.remark + "</td><td>" + value.created_at + "</td><tr>";
                        });
                        console.log(detials);
                        $('#getRemarkHistory #data').html(detials);
                        $("#getRemarkHistory").modal('show');
                    }
                },
                error: function() {

                }

            });
        }

        $(document).ready(function() {
            src = "{{ route('password.index') }}";
            $(".search").autocomplete({
                source: function(request, response) {
                    website = $('#website').val();
                    username = $('#username').val();
                    password = $('#password').val();
                    registered_with = $('#registered_with').val();


                    $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                            website: website,
                            username: username,
                            password: password,
                            registered_with: registered_with,

                        },
                        beforeSend: function() {
                            $("#loading-image").show();
                        },

                    }).done(function(data) {
                        $("#loading-image").hide();
                        console.log(data);
                        $("#passwords-table tbody").empty().html(data.tbody);
                        if (data.links.length > 10) {
                            $('ul.pagination').replaceWith(data.links);
                        } else {
                            $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                        }

                    }).fail(function(jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                },
                minLength: 1,

            });
        });


        $(document).ready(function() {
            src = "{{ route('todolist') }}";
            $(".global").autocomplete({
                source: function(request, response) {
                    search_title = $('#search_title').val();
                    search_status= $('#search_status').val();
                    search_date = $('#search_date').val();



                    $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                            search_title: search_title,
                            search_status:search_status,
                            search_date:search_date,
                            date: date,

                        },
                        beforeSend: function() {
                            $("#loading-image").show();
                        },

                    }).done(function(data) {
                        $("#loading-image").hide();
                        console.log(data);
                        $("#todolist-table tbody").empty().html(data.tbody);
                        if (data.links.length > 10) {
                            $('ul.pagination').replaceWith(data.links);
                        } else {
                            $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                        }

                    }).fail(function(jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                },
                minLength: 1,

            });
        });
        $('.checkbox_ch').change(function() {
            var values = $('input[name="userIds[]"]:checked').map(function() {
                return $(this).val();
            }).get();
            $('#userIds').val(values);
        });



        function statusChange(id, xvla) {
            $.ajax({
                type: "POST",
                url: "{{ route('todolist.status.update') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id,
                    "status":xvla
                },
                dataType: "json",
                success: function(message) {
                    $c = message.length;
                    if ($c == 0) {
                        alert('No History Exist');
                    } else {
                        toastr['success'](message.message, 'success');
                    }
                },
                error: function(error) {
                    toastr['error'](error, 'error');
                }

            });

        }

        $(document).ready(function () {
            $('.add_title').change(function () {
                if ($('.add_subject').val() == "") {
                    $('.add_subject').val("");
                    $('.add_subject').val($('.add_title').val());
                }
            })
            $('.edit_title').change(function () {
                if ($('.edit_subject').val() == "") {
                    $('.edit_subject').val("");
                    $('.edit_subject').val($('.edit_title').val());
                }
            })
        })

        function todoCategoryChange(id, todo_category_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('todolist.category.update') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id,
                    "todo_category_id":todo_category_id
                },
                dataType: "json",
                success: function(message) {
                    $c = message.length;
                    if ($c == 0) {
                        alert('No History Exist');
                    } else {
                        toastr['success'](message.message, 'success');
                    }
                },
                error: function(error) {
                    toastr['error'](error, 'error');
                }

            });
        }

        $(document).on('click', '.remarks-message', function() {
            var thiss = $(this);
            var data = new FormData();
            var todo_list_id = $(this).data('todolistid');

            var message = $("#remarks_" + todo_list_id).val();
            data.append("todo_list_id", todo_list_id);
            data.append("remarks", message);
            data.append("_token", "{{ csrf_token() }}");

            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: "{{ route('todolist.remark.history.post') }}",
                        type: 'POST',
                        "dataType": 'json', // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function() {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function(response) {
                        toastr['success']('Remarks Added successfully!!!', 'success');

                        $("#remarks_" + vendor_id).val('');

                    }).fail(function(errObj) {
                        $(thiss).attr('disabled', false);

                        alert("Could not send remarks");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a remarks first');
            }
        });
    </script>
@endsection
