@extends('layouts.app')

@section('title', $title)

@section('content')

<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
</div>
@if(!empty($users))
<div class="row" style="margin-bottom: 10px">
    <div class="col-lg-3 margin-tb">
        <div class="col-md-12">
            <div class="col-md-12 margin-tb">
                <select name="time_doctor_account" id="time_doctor_account" class="form-control">
                    <option value="">Select Time Doctor Account</option>
                    @foreach ($members as $member )
                        <option value="{{$member->id}}">{{$member->time_doctor_email}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-lg-1 margin-tb">
        <button type="button" class="btn btn-secondary float-right-addbtn" id="bulk_action"> Bulk Create</button>
    </div>
</div>
<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
        <div class="col-md-12">
            <div class="col-md-12 margin-tb">
                <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>User Id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    @foreach($users as $user)
                    <tbody>
                      <tr>
                        <td style="vertical-align:middle;">
                            <input type="checkbox" name="bulk_user_action[]" class="d-inline bulk_user_action" value="{{ $user->id }}">
                            <span class="d-inline" >{{ $user->id }}</span>
                        </td>
                        <td style="vertical-align:middle;">{{ $user->name }}</td>
                        <td style="vertical-align:middle;">{{ $user->email }}</td>
                        <td style="vertical-align:middle;">
                            <button type="button" data-email="{{ $user->email }}" data-name="{{ $user->name }}" data-userid="{{ $user->id }}" class="btn btn-secondary action">Create</button>
                        </td>
                      </tr>
                    </tbody>
                    @endforeach
                  </table>
            </div>
        </div>
    </div>
</div>
@endif
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div id="records-modal" class="modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px !important; width: 100% !important;">
        <div class="modal-content" id="record-content">
        </div>
    </div>
</div>



<script type="text/javascript">
$(document).on("click", ".action" , function(){
    var email = $(this).data("email");
    var name = $(this).data("name");
    var userId = $(this).data("userid");
    var tda = $('#time_doctor_account').val();
    if (tda != '') {
        $.ajax({
            type: "POST",
            url: "{{ route('time-doctor.send-invitation') }}",   
            data: { email: email, name: name, userId: userId, tda:tda, _token: "{{ csrf_token() }}"},  
            success: function(response) {
                if (response.code == 200) {
                    toastr['success'](response.message);
                } else {
                    toastr['error'](response.message);
                }
            }
        });
    } else {
        toastr['error']('Select Time Doctor Account');
    }
});

$(document).on("click", "#bulk_action", function() {
        var tda = $('#time_doctor_account').val();
        if (tda != '') {
            let checkIds = [];
            $('.bulk_user_action').each(function(){
                if($(this).is(':checked')) {
                    checkIds.push($(this).val());
                }
            });
            if (checkIds.length) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('time-doctor.send-bulk-invitation') }}",   
                    data: { ids: checkIds, tda:tda, _token: "{{ csrf_token() }}"},  
                    success: function(response) {
                        if (response.code == 200) {
                            toastr['success'](response.message);
                        } else {
                            toastr['error'](response.message);
                        }
                    }
                });
            } else {
                toastr['error']('Please select atleast one user.');
            }
        } else {
            toastr['error']('Select Time Doctor Account');
        }
});
</script>
@endsection