@if($passwords->isEmpty())
  <tr>
      <td colspan="7" class="text-center">
          No Result Found
      </td>
  </tr>
@else
  @foreach ($passwords as $password)
    <tr>
      <td class="text-center">
        <input type="checkbox" class="checkbox_ch" id="u{{ $password->id }}" name="userIds[]" value="{{ $password->id }}">
      </td>
      <td>
        {{ $password->website }}
        <br>
        <a href="{{ $password->url }}" target="_blank"><small class="text-muted">{{ $password->url }}</small></a>
      </td>
      <td>{{ $password->username }}
        <button type="button" data-id="" class="btn btn-copy-username btn-sm"  data-value="{{ $password->username }}">
            <i class="fa fa-clone" aria-hidden="true" ></i>
        </button>
      </td>
      <td><span class="user-password">{{ Crypt::decrypt($password->password) }}</span>
        <button type="button" data-id="" class="btn btn-copy-password btn-sm"  data-value="{{ Crypt::decrypt($password->password) }}">
          <i class="fa fa-clone" aria-hidden="true"></i>
        </button>
      </td>
      <td>{{ $password->registered_with }}</td>
      <td>
          <div style="width: 100%;">
          <div class="d-flex">
            <input type="text" name="remark_pop" class="form-control remark_pop{{$password->id}}" placeholder="Please enter remark" style="margin-bottom:5px;width:100%;display:inline;">
            <button type="button" class="btn btn-sm btn-image add_remark pointer" title="Send message" data-password_id="{{$password->id}}">
                <img src="{{asset('images/filled-sent.png')}}">
            </button>
          <button data-password_id="{{ $password->id }}" data-password_type="Quick-dev-task" class="btn btn-xs btn-image show-remark" title="Remark"><img src="{{asset('images/chat.png')}}" alt=""></button>
          </div>
          @if (isset($password_remark))
              <div style="width: 100%;">
                  <div class="expand-row-msg" data-id="{{$password->id}}">
                      <div class="d-flex justify-content-between expand-row-msg" data-id="{{$password->id}}">
                            <span class="td-password-remark{{$password->id}}" style="margin:0px;">
                                @php($i = 1)
                                @foreach($password_remark as $pwd_remark)
                                    @if($pwd_remark->password_id === $password->id)
                                    {{$i}}.{{$pwd_remark->remark}}
                                    @endif
                                    @php($i++)
                                @endforeach
                            </span>
                      </div>
                  </div>
              </div>
          @endif
          </div>
      </td>
      <td>
        <div class="row">
            <button onclick="changePassword({{ $password->id }})" data-id="{{ $password->id }}" class="btn btn-image" title="Change"><i class="fa fa-pencil"></i></button>
            <button onclick="getData({{ $password->id }})" class="btn btn-image" title="History"><i class="fa fa-info-circle"></i></button>
            <button type="button" onclick="sendtoWhatsapp({{ $password->id }})" data-id="{{ $password->id }}" class="btn btn-image" title="Send to Whatsapp"><i class="fa fa-whatsapp"></i></button>
        </div>
      </td>
    </tr>
  @endforeach
@endif
@include('passwords.search-password')
<script>
    $(document).on("click",".add_remark",function(e) {
        e.preventDefault();
        var thiss = $(this);
        var password_id = $(this).data('password_id');
        var remark = $(`.remark_pop`+password_id).val();
        $.ajax({
            type: "POST",
            url: passGetRemark,
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            data: {
                password_id : password_id,
                remark : remark,
                type : "Quick-dev-task",
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function (response) {
            if(response.code == 200) {
                $("#loading-image").hide();
                if (remark == ''){
                    $("#preview-task-create-get-modal").modal("show");
                }
                $(".task-create-get-list-view").html(response.data);
                $(`.td-password-remark`+password_id).html(response.remark_data);
                $(`.remark_pop`+password_id).val("");
                toastr['success'](response.message);
            }else{
                $("#loading-image").hide();
                if (remark == '') {
                    $("#preview-task-create-get-modal").modal("show");
                }
                $(".task-create-get-list-view").html("");
                toastr['error'](response.message);
            }

        }).fail(function (response) {
            $("#loading-image").hide();
            $("#preview-task-create-get-modal").modal("show");
            $(".task-create-get-list-view").html("");
            toastr['error'](response.message);
        });
    });
    $(document).on("click",".show-remark",function(e) {
        e.preventDefault();
        var password_id = $(this).data('password_id');
        var remark = $(`.remark_pop`+password_id).val();
        // alert(remark );
        $.ajax({
            type: "POST",
            url: passGetRemark,
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            data: {
                password_id : password_id,
                remark : remark,
                type : "Quick-dev-task",
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function (response) {
            if(response.code == 200) {
                $("#loading-image").hide();
                if (remark == ''){
                    $("#preview-task-create-get-modal").modal("show");
                }
                $(".task-create-get-list-view").html(response.data);
                $(`.td-password-remark`+password_id).html(response.remark_data);
                $(`.remark_pop`+password_id).val("");
                toastr['success'](response.message);
            }else{
                $("#loading-image").hide();
                if (remark == '') {
                    $("#preview-task-create-get-modal").modal("show");
                }
                $(".task-create-get-list-view").html("");
                toastr['error'](response.message);
            }

        }).fail(function (response) {
            $("#loading-image").hide();
            $("#preview-task-create-get-modal").modal("show");
            $(".task-create-get-list-view").html("");
            toastr['error'](response.message);
        });
    });

</script>
