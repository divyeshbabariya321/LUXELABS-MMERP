@extends('layouts.app')

@section('styles')
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
    <style src="https://cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.css"></style>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
@endsection
<style type="text/css">
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
        .select2{
            width: 100% !important;
        }
</style>
@section('content')
<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
</div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Passwords Manager (<span id="passwords_count">{{ $users->total() }}</span>)</h2>
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4">
                            {{ html()->form('POST', route('password.change'))->open() }}
                                <input type="hidden" name="users" id="userIds">
                                <button type="submit" class="btn btn-secondary"> Generate password </button>
                            {{ html()->form()->close() }}
                        </div>
                        <div class="col-md-3">
                            {{ html()->multiselect('username[]', [], request("username", []))->data('placeholder', 'Search Username')->class('form-control w-100')->id('username') }}
                        </div>
                        <div class="col-md-3">
                            {{ html()->multiselect('email[]', [], request("email", []))->data('placeholder', 'Search Email')->class('form-control w-100')->id('email') }}
                        </div>
                        <div class="col-md-1">
                           <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png"/></button>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png"/></button>    
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" onclick="showPasswordCreateModal()">+</button>
            </div>
            
        </div>
    </div>

    @include('partials.flash_messages')
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
              <table class="table table-bordered" id="passwords-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Send WhatsApp</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                    @include('passwords.partials.change-password')
                </tbody>
              </table>
            </div>
        </div>
        {!! $users->render() !!}
       
        

       

    </div>
<div id="passwordSendEmailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('password.send.email') }}" method="POST">
            @csrf

            <div class="modal-header">
              <h4 class="modal-title">Send Email</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="value">Send To</label>
                    <input type="email" name="email" id="email" class="form-control"  placeholder="Enter Email" required>
                    @if ($errors->has('email'))
                    <div class="alert alert-danger">{{$errors->first('email')}}</div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="value">From Mail</label>
                    <select class="form-control" name="from_email" id="from_email" required>
                        @foreach ($emailAddressArr as $emailAddress)
                        <option value="{{ $emailAddress->from_address }}">{{ $emailAddress->from_name }} - {{ $emailAddress->from_address }} </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Send</button>
            </div>
          </form>
        </div>

    </div>
</div>

<div id="passwordSendEmailHistoryModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Password Email History</h4>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
                <th style="width:20%">Modal Type </th>
                <th style="width:20%">To Email</th>
                <th style="width:20%">From Email</th>
                <th style="width:20%">Subject</th>
                <th style="width:25%">Message</th>
                <th style="width:25%">Error Message</th>
                <th style="width:10%">Date</th>
            </thead>
            <tbody class="password-email-history">

            </tbody>
            </table>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
{{-- @include('partials.modals.password-create-modal') --}}


@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/multiselect/2.2.9/js/multiselect.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">

$(document).ready(function () {
            $('#username').select2({
            tags: true, // Allow the user to add new tags (SKUs)
            ajax: {
                url: '/search/username', // Your autosuggest route
                dataType: 'json',
                delay: 500,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {
                    var results = data.map(function (item) {
                        return { id: item, text: item };
                    });
                    return {
                        results: results
                    };
                },
                cache: true
            },
        });

        $('#email').select2({
            tags: true, // Allow the user to add new tags (SKUs)
            ajax: {
                url: '/search/email', // Your autosuggest route
                dataType: 'json',
                delay: 500,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {
                    var results = data.map(function (item) {
                        return { id: item, text: item };
                    });
                    return {
                        results: results
                    };
                },
                cache: true
            },
        });
    });   
    function submitSearch(){
            src = "{{route('password.manage')}}";
            username = $('#username').val();
            var jsonSelectedUsernames = JSON.stringify(username);

            email = $('#email').val();
            var jsonSelectedEmails = JSON.stringify(email);

            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    username : jsonSelectedUsernames,
                    email : jsonSelectedEmails,
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                $("#passwords-table tbody").empty().html(data.tbody);
                $("#passwords_count").text(data.count);
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
        src = "{{route('password.manage')}}"
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
            $('#username').val(null).trigger('change');
            $('#email').val(null).trigger('change');
            $("#passwords-table tbody").empty().html(data.tbody);
            $("#passwords_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }

        // $(document).ready( function () {
        //     $('#passwords-table').DataTable();
        // });

         $('.checkbox_ch').change(function(){
             var values = $('input[name="userIds[]"]:checked').map(function(){return $(this).val();}).get();
             $('#userIds').val(values);
         });

        jQuery(document).ready(function($) {
            $('#keepRenderingSort').multiselect({
                keepRenderingSort: true
            });
        });

        $(".send_password_email").on("click", function(){
            var email = $(this).data('email');
            $("#email").val(email);
        });

        $(document).on("click", ".show_password_history", function() {
            var email = $(this).data('email');
            $.ajax({
                method: "GET",
                url: "{{ route('password.email.history') }}" ,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "email": email,
                },
                dataType: 'html'
                })
            .done(function(result) {
                $('#passwordSendEmailHistoryModal').modal('show');
                $('.password-email-history').html(result);
            });
        });

        $('#from_email').select2({
            placeholder: 'Select an Email',
        });
    </script>
@endsection