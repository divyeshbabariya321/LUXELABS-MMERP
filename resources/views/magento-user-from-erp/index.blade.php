@extends('layouts.app')

@section('title', 'Magento Users Manager')

@section('content')

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<div class="row">
  <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Magento Users Manager</h2>
  </div>
</div>

@include('partials.flash_messages')

@php
    $isAdmin = auth()->user()->isAdmin();
    // $hasSiteDevelopment = auth()->user()->hasRole('Site-development');
    $userId = auth()->user()->id;
    $pagrank = $storeWebsites->perPage() * ($storeWebsites->currentPage() - 1) + 1;
@endphp

<div class="row mb-3">
  <div class="col-xs-12 pl-5">
      <form class="form-search-data">
          <div class="row">
            <div class="col-2 pd-2">
              <div class="form-group username mb-0">
                 {{ html()->select('store_website_id', ["" => "-- Select website --"] + $allStoreWebsites, request('store_website_id'))->class('form-control form-control-sm') }}
              </div>
            </div>
            <div class="col-2 pd-2">
              <div class="form-group username mb-0">
                 {{ html()->text('username', request('username'))->class('form-control form-control-sm')->placeholder('Username') }}
              </div>
            </div>
            <div class="col-2 pd-2">
              <div class="form-group username mb-0">
                 {{ html()->select('role', ["" => "-- Select Role --"] + $magentoRoles, request('role'))->class('form-control form-control-sm') }}
              </div>
            </div>
            <div class="col-2 pd-2">
              <div class="form-group status mb-0">
                <button type="submit" class="btn btn-xs mt-1 ml-3"><i class="fa fa-filter"></i></button>
                <a href="/magento-users" class="btn btn-image mt-auto" id=""><img src="{{asset('/images/resend2.png')}}" style="cursor: nwse-resize;"></a>
                &nbsp;&nbsp;
                <button id="addnew" class="btn btn-default">Add User</button>
              </div>
            </div>
          </div>
      </form>
  </div>
</div>

<div class="mt-3 col-md-12">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Sr. No</th>
        <th>Date</th>
        <th>Username</th>
        @if ($isAdmin)
          <th>Password</th>
        @endif
        <th>Website</th>
        <th>Title</th>
        <th>Website Mode</th>
        <th>Admin Panel</th>
        <th>Role</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
      @if (count($storeWebsites) > 0)
        @foreach ( $storeWebsites as $website)
          <tr>
            <td>{{ $pagrank++ }}</td>
            <td>{{ date('d-m-Y', strtotime($website->created_at)) }}</td>
            <td>{{ $website->username }}</td>
            @if ($isAdmin)
              <td>{{ $website->password }}</td>
            @endif
            <td>{{ $website->website }}</td>
            <td>{{ $website->title }}</td>
            <td>{{ $website->website_mode }}</td>
            <td>{{ $website->magento_url }}</td>
            <td>{{ $website->user_role_name }} </td>
            <td>
              <input value="{{ $website->id }}" class="change-status" type="checkbox" data-toggle="toggle" data-on="Enabled" data-off="Disabled" {{ $website->is_active ? 'checked' : ''}} >
            </td>
          </tr>
        @endforeach
      @else
      <tr>
        <td colspan="8">No users found.</td>
      </tr>
      @endif
    </tbody>
  </table>
</div>

<div class="col-md-12 margin-tb text-center">
  {!! $storeWebsites->appends(request()->capture()->except('page', 'pagination') + ['pagination' => true])->render() !!}
</div>

<div id="addNewUser" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add User</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div id="add-user-form">
        <div class="modal-body">
            <form name="form-create-users" id="form-create-users" method="post">
              <?php echo csrf_field(); ?>
              <div class="form-group">
                  <div class="row">
                    <div class="col-sm-6">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control userName" id="magento_username" placeholder="Enter Username">
                    </div>
                    <div class="col-sm-6">
                        <label for="userEmail">Email</label>
                        <input type="email" name="userEmail" class="form-control userEmail" id="magento_userEmail" placeholder="Enter Email">
                    </div>
                  </div>
              </div>
              <div class="form-group">
                  <div class="row">
                    <div class="col-sm-6">
                        <label for="firstName">First Name</label>
                        <input type="text" name="firstName" class="form-control firstName" id="magento_firstName" placeholder="Enter First Name">
                    </div>
                    <div class="col-sm-6">
                        <label for="lastName">Last Name</label>
                        <input type="text" name="lastName" class="form-control lastName" id="magento_lastName" placeholder="Enter Last Name">
                    </div>
                  </div>
              </div>
              <div class="form-group">
                  <div class="row">
                    <div class="col-sm-6">
                        <label for="webSite">Website</label>
                        <select name="webSite" id="magento_webSite" class="form-control webSite">
                          <option value="">Please select one</option>
                          @if (count($allStoreWebsites) > 0)
                            @foreach ( $allStoreWebsites as $all_web_key => $allweb)
                            <option value="{{ $all_web_key }}">{{ $allweb }}</option>
                            @endforeach
                          @endif
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="userRole">Role</label>
                        <select name="userRole" id="magento_userRole" class="form-control userRole" readonly>
                          <option value="">Select website first</option>
                        </select>
                    </div>
                  </div>
              </div>
              <div class="form-group">
                  <div class="row">
                    <div class="col-sm-6">
                        <label for="websiteMode">Website Mode</label>
                        <select name="websiteMode" id="magento_websiteMode" class="form-control websiteMode">
                          <option value="production">Production</option>
                          <option value="staging">Staging</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="password">Password</label>
                        <input type="password" name="password" value="" class="form-control user-password" id="magento_password" placeholder="Enter Password">
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary submit-create-user">Add New</button>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).on('click', '#addnew', function (e){
    e.preventDefault();
    // toastr["error"]("Please enter token for website", "error");
    $("#addNewUser").modal('show');
  });

  // Get roles by website.
  $( "#magento_webSite" ).on( "change", function( event ) {
    event.preventDefault();
    var website_id = $(this).val();
    $.ajax({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      method: 'POST',
      dataType: 'json',
      data: {
        website_id: website_id,
      },
      url: "<?php echo url("/magento-users/roles"); ?>",
      beforeSend: function() {
        $("#loading-image-preview").show();
        $('.submit-create-user').prop('disabled', true);
      },
      success: function(response) {
        
        $('.submit-create-user').prop('disabled', false);
        $("#loading-image-preview").hide();
        if( response.roles.length > 0 ){
          var roles = '';
          $.each( response.roles, function( key, value ) {
              roles += '<option value="'+value.role_id+'">'+value.role_name+'</option>';
          });
          if( roles.length > 0 ){
              $('#magento_userRole').html(roles).attr("readonly", false);
          }
        }else{
          toastr["error"](response.error);
        }
      },
      error: function(xhr) { // if error occured
        $("#loading-image-preview").hide();
        $('.submit-create-user').prop('disabled', false);
        toastr["error"]("Roles not found.", "error");
        $('#magento_userRole').html('<option value="">Select website first</option>').attr("readonly", true);
      },
    });
  });

  // On form submit.
  $( "#form-create-users" ).on( "submit", function( event ) {
    event.preventDefault();
    var magento_username = $('#magento_username').val();
    var magento_userEmail = $('#magento_userEmail').val();
    var magento_firstName = $('#magento_firstName').val();
    var magento_lastName = $('#magento_lastName').val();
    var magento_webSite = $('#magento_webSite').val();
    var magento_userRole = $('#magento_userRole').val();
    var magento_websiteMode = $('#magento_websiteMode').val();
    var magento_password = $('#magento_password').val();
    var user_role_name = $('#magento_userRole').find(":selected").text();
    $.ajax({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      method: 'POST',
      dataType: 'json',
      data: {
        username: magento_username,
        userEmail: magento_userEmail,
        firstName: magento_firstName,
        lastName: magento_lastName,
        website: magento_webSite,
        userrole: magento_userRole,
        websitemode: magento_websiteMode,
        password: magento_password,
        userRoleName: user_role_name
      },
      url: "<?php echo url("/magento-users/create"); ?>",
      beforeSend: function() {
        $("#loading-image-preview").show();
      },
      success: function(response) {
        $("#loading-image-preview").hide();
        if( response.error.length > 0 ){
          toastr["error"](response.error);
        }else{
          location.reload();
        }
      },
      error: function(xhr) { // if error occured
        $("#loading-image-preview").hide();
        toastr["error"]("Error occured.please try again.", "error");
      },
    });
  });

  // Change status for user account.
  $( ".change-status" ).on( "change", function() {
    var get_status = $(this).prop('checked');
    var current_action = $(this);

    $.ajax({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      method: 'POST',
      dataType: 'json',
      data: {
        status: get_status,
        update_id: $(this).val(),
      },
      url: "<?php echo url("/magento-users/account-status"); ?>",
      beforeSend: function() {
        $("#loading-image-preview").show();
      },
      success: function(response) {
        $("#loading-image-preview").hide();
        
        if( response.error.length > 0 ){
          toastr["error"](response.error);
        }
      },
      error: function(xhr) { // if error occured
        $("#loading-image-preview").hide();
        toastr["error"]("Error occured.please try again.", "error");
      },
    });
  });
</script>
@endsection