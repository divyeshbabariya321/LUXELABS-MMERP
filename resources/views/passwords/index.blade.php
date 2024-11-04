@extends('layouts.app')

@section('favicon' , 'password-manager.png')

@section('title', 'Passwords Manager Info')

@section('styles')
    <style>
        .users {
            display: none;
        }

    </style>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <style type="text/css">
         #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
         /*.table-responsive {*/
         /*     overflow-x: auto !important;*/
         /*}*/
    </style>
@endsection


@section('content')
    <div id="myDiv">
       <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Passwords Manager ({{$passwords->count()}})</h2>
            <div class="pull-left p-0">
                <form action="{{ route('password.index') }}" method="GET" class="form-inline align-items-start">
                    <div class="form-group mr-3 mb-3">
                        <input name="term" type="text" class="form-control global" id="term"
                               value="{{ isset($_GET['term'])?$_GET['term']:'' }}"
                               placeholder="website , username, password">
                    </div>
                    <div class="form-group ml-3">
                        <div class='input-group date' id='filter-date'>
                            <input type='text' class="form-control global" name="date" value="{{ isset($_GET['date'])?$_GET['date']:'' }}"  placeholder="Date" id="date" />
                            <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-image m-0"><img src="{{asset('/images/filter.png')}}" /></button>
                </form>
            </div>
            <div class="pull-right">
                <div class="pull-left mr-3">
                    {{ html()->form('POST', route('passwords.change'))->open() }}
                    <input type="hidden" name="users" id="userIds">
                    <button type="submit" class="btn btn-secondary"> Generate password </button>
                    {{ html()->form()->close() }}
                </div>
              <button type="button" class="btn btn-secondary" onclick="showPasswordCreateModal()">+</button>
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


    </div>
    <div class="col-md-12">
        <div class="table-responsive mt-3">
      <table class="table table-bordered" id="passwords-table">
        <thead>
          <tr>
            <th width="3%" class="text-center">#</th>
            <th width="8%">Website</th>
            <th width="10%">Username</th>
            <th width="10%">Password</th>
            <th width="10%">Registered With</th>
            <th width="15%">Remark</th>
            <th width="8%">Actions</th>

          </tr>

          <tr>

            <th></th>
            <th><input type="text" id="website" class="search form-control"></th>
            <th><input type="text" id="username" class="search form-control"></th>
            <th></th>
            <th><input type="text" id="registered_with" class="search form-control"></th>
            <th></th>
            <th></th>
          </tr>
        </thead>

        <tbody>

       @include('passwords.data')

          {!! $passwords->render() !!}

        </tbody>
      </table>
    </div>
    </div>



    @if($passwords->isEmpty())


    @else

        @include('passwords.password-editmodal')
        @include('passwords.password-sendtowhatsapp')

    @endif
    @include('passwords.password-gethistory')

    @include('passwords.password-previewtask')
    {{-- @include('partials.modals.password-create-modal') --}}
	@endsection


@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      <script> 
        var passHistory = "{{ route('password.history') }}";
        var passGetRemark = "{{route('password.create.get.remark')}}";
        var passwordIndex = "{{ route('password.index') }}";
        var showPasswordEdit = "{{ route('password.show.edit-data') }}";

        $(document).ready(function () {
            src = passwordIndex;
            $(".global").autocomplete({
                source: function (request, response) {
                term = $("#term").val();
                date = $("#date").val();

                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                    term: term,
                    date: date,
                    },
                    beforeSend: function () {
                    $("#loading-image").show();
                    },
                })
                    .done(function (data) {
                    $("#loading-image").hide();
                    console.log(data);
                    $("#passwords-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $("ul.pagination").replaceWith(data.links);
                    } else {
                        $("ul.pagination").replaceWith('<ul class="pagination"></ul>');
                    }
                    })
                    .fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert("No response from server");
                    });
                },
                minLength: 1,
            });
        });

        $(document).ready(function () {
            src = passwordIndex;
            $(".search").autocomplete({
                source: function (request, response) {
                website = $("#website").val();
                username = $("#username").val();
                password = $("#password").val();
                registered_with = $("#registered_with").val();

                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                    website: website,
                    username: username,
                    password: password,
                    registered_with: registered_with,
                    },
                    beforeSend: function () {
                    $("#loading-image").show();
                    },
                })
                .done(function (data) {
                    $("#loading-image").hide();
                    console.log(data);
                    $("#passwords-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $("ul.pagination").replaceWith(data.links);
                    } else {
                        $("ul.pagination").replaceWith('<ul class="pagination"></ul>');
                    }
                    })
                    .fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert("No response from server");
                    });
                 },
                minLength: 1,
            });
        });
    
        $( ".generatepasswordadd" ).bind( "click", function() {
            var newPassword = generatePassword(8);
            $(".password-add").val(newPassword);
        });

        $( ".generatepasswordedit" ).bind( "click", function() {
            var newPassword = generatePassword(8);
            $(".password-edit").val(newPassword);
        });

        function generatePassword(length) {
            var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+{}:?><,./;'[]\|-=";
            var password = "";
            for (var i = 0; i < length; i++) {
              var randomChar = Math.floor(Math.random() * charset.length);
              password += charset.substring(randomChar, randomChar + 1);
            }
            return password;
        }
    </script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/custom-passwords.js') }} "></script>
@endsection
