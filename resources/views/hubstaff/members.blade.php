@extends('layouts.app')

@section('link-css')
<style type="text/css">
  .form-group {
    padding: 10px;
  }
</style>
@endsection

@section('content')

@if(Session::has('message'))
<div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>{{ Session::get('message') }}</strong>
</div>
@endif

<div class="col-lg-12 margin-tb">
    <h2 class="page-heading">Users List from Hubstaff</h2>
</div>

<div class="col-lg-12 margin-tb margin-l" style="margin-left: 1%;">
		<form class="filterTaskSummary" action="{{ url('hubstaff/members') }}" method="GET">
			<div class="row filter_drp">
				<div class="form-group col-lg-2">
					<select class="form-control globalSelect2" data-ajax="{{ route('hubstaff.userList') }}" name="user_filter[]" data-placeholder="Search Customer By Name" multiple >
          @if($usersFilter)
            @foreach($usersFilter as $usersfilter)
              <option value="{{$usersfilter['id']}}" selected>{{$usersfilter->name}}</option>
            @endforeach
          @endif
          </select>
				</div>

        <div class="form-group col-lg-2">
          <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
        </div>
      </div>
    </form>
</div>

<!-- <h2 class="text-center">Users List from Hubstaff </h2> -->
  <form class="form-inline  float-right" method="POST" action="/hubstaff/refresh_users">
    @csrf
    <button type="submit" class="btn-danger" id="refresh_users">Refresh Users From Hubstaff</button>
  </form>
  @if(!empty($members))
  <div class="row">
    <div class="col-md-12 pr-5 pl-5">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>HubStaff Id</th>
          <th>Hubstaff Email</th>
          <th>Minimum Activity</th>
          <th>User</th>

        </tr>
      </thead>
      <tbody>
      @foreach($members as $member)
        <tr>
          <td style="vertical-align:middle;">{{ $member->hubstaff_user_id }}</td>
          <td style="vertical-align:middle;">{{ $member->email }}</td>
          <td>
            <div class="form-group"style="margin-top: -10px;margin-bottom:-10px;">
              <input type="text" data-member-id="{{ $member->id }}" class="form-control change-activity-percentage" name="min_activity_percentage" value="{{ $member->min_activity_percentage }}">
            </div>
          </td>
          <td>
           <div class="form-group"style="margin-top: -10px;margin-bottom:-10px;">
                <select onchange="saveUser(this)"class="form-control change-activity-percentage">
                 <option value="unassigned">Unassigned</option>
                 @foreach($users as $user)
                 <option value="{{$user->id}}|{{ $member->hubstaff_user_id }}" <?= ($member->user_id == $user->id) ? 'selected' : '' ?>>{{$user->name}}</option>
                 @endforeach
                </select>
          </div>
          </td>

        </tr>
      @endforeach
      </tbody>
    </table>
    <br>
    <hr>
  </div>
  </div>
  @else
  <div style="text-align: center;color: red;font-size: 14px;">
    {{$members['error_description']}}
  </div>
  @endif

@endsection
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
</div>
@section("scripts")
<script type="text/javascript">
  function saveUser(a) {
    var selectedValue = (a.value || a.options[a.selectedIndex].value); //crossbrowser solution =)
    console.log('selectedValue', selectedValue);
    if (selectedValue != 'unassigned') {
      var splitValues = selectedValue.split('|');
      var userId = splitValues[0];
      var hubstaffUserId = splitValues[1];

      var xhr = new XMLHttpRequest();
      var url = "linkuser";
      xhr.open("POST", url, true);
      xhr.setRequestHeader("Content-Type", "application/json");
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          var json = JSON.parse(xhr.responseText);
          console.log(json.email + ", " + json.password);
        }
      };
      var data = JSON.stringify({
        "user_id": userId,
        "hubstaff_user_id": hubstaffUserId
      });
      xhr.send(data);
    }
  }
  $(document).on("focusout",".change-activity-percentage",function(e){
     e.preventDefault();
     var $this = $(this);
     var memberId = $this.data("member-id");
     $.ajax({
        type: 'POST',
        url: "/hubstaff/members/"+memberId+"/save-field",
        data: {
          _token: "{{ csrf_token() }}",
          field_name : "min_activity_percentage",
          field_value : $this.val()
        },
        dataType:"json",
        beforeSend : function(response) {
          $(".loading-image").show();
          
        }
      }).done(function(response) {
        $(".loading-image").hide();
        if(response.code == 200) {
          toastr["success"](response.message);
        }else{
          toastr["error"](response.message);
        } 
      }).fail(function(response) {
          console.log(response);
      });
  });

</script>
@endsection