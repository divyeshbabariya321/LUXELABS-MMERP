<style type="text/css">
#vendorShortcutCreateModal .select2-container {width: 100% !important;}
</style>
<div id="vendorShortcutCreateModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ url('vendors/storeshortcut') }}" method="POST" id="createVendorForm">
        @csrf
        <input type="hidden" id="vendor_organization_id" name="organization_id">
        <div class="modal-header">
          <h4 class="modal-title">Store a Vendor</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="modal-body">
              <div class="col-md-6">
                <div class="form-group">
                    {{ html()->select("category_id", \App\Helpers\VendorHelper::getVendorCategory(), request('category_id'))->class("form-control")->placeholder("Category") }}
                  @if ($errors->has('category_id'))
                  <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <select class="form-control" name="type" placeholder="Type:">
                    <option value="">Select a Type</option>
                    <option value="Freelancer">Freelancer</option>
                    <option value="Agency">Agency</option>
                  </select>
                  @if ($errors->has('type'))
                  <div class="alert alert-danger">{{$errors->first('type')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                    <label for="body_framework" class="label-btn">Frameworks
                      <button type="button" class="add-framework" data-toggle="modal" data-target="#addFramewrokShortcutModel">Add Framework</button>
                    </label>
                    @php
                    $frameworkVer = \App\Helpers\VendorHelper::getVendorFrameworks();
                    @endphp
                    <select name="framework[]" value="" class="form-control select-multiple-s selectpicker" id="framework_s" multiple>
                      @foreach ($frameworkVer as $fVer)
                        <option value="{{$fVer->id}}">{{$fVer->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="name" class="form-control" placeholder="Name:" value="{{ old('name') }}" required id="popup_name">
                  @if ($errors->has('name'))
                  <div class="alert alert-danger">{{$errors->first('name')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="number" name="phone" class="form-control" placeholder="Phone:" value="{{ old('phone') }}">
                  @if ($errors->has('phone'))
                  <div class="alert alert-danger">{{$errors->first('phone')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="email" name="email" class="form-control" placeholder="Email:" value="{{ old('email') }}" id="popup_email"> @if ($errors->has('email'))
                  <div class="alert alert-danger">{{$errors->first('email')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="email" name="gmail" class="form-control" placeholder="Gmail:" id="popup_gmail" value="{{ old('gmail') }}"> @if ($errors->has('gmail'))
                  <div class="alert alert-danger">{{$errors->first('gmail')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="website" class="form-control" placeholder="Website:" value="{{ old('website') }}">
                  @if ($errors->has('website'))
                  <div class="alert alert-danger">{{$errors->first('website')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="url" class="form-control" placeholder="URL:" value="{{ old('url') }}">
                  @if ($errors->has('url'))
                  <div class="alert alert-danger">{{$errors->first('url')}}</div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
              <div class="form-group d-flex">
                <span>Create User:</span>
                <input type="checkbox" name="create_user" class="" style=" height: 14px;">
                @if ($errors->has('create_user'))
                <div class="alert alert-danger">{{$errors->first('create_user')}}</div>
                @endif
              </div>
              </div>
              <div class="col-md-12">
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-secondary">Add</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="addFramewrokShortcutModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Framework</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="postmanform" method="post">
              @csrf
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="frameworkName">Name</label>
                  <input type="text" name="frameworksName" required value="" class="form-control" id="frameworksName" placeholder="Enter framework Name">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-secondary vendors-addframeworks">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).on('submit', '#createVendorForm', function (e) {
    e.preventDefault();
    $.ajax({
      url: "{{ url('vendors/storeshortcut') }}",
      method: "POST",
      data: $(this).serialize(),
      success: function (resp) {
        switch (resp.status) {
          case 'success':
            toastr.success(resp.msg);
            break;
        }
      },
      error: function (error) {
        for (let msg of Object.values(error.responseJSON.errors)) {
          toastr.error(msg);
        }
      },
    });
  });

  $(document).on("click", ".vendors-addframeworks", function(e) {
      e.preventDefault();
      var frameworksName = $('#frameworksName').val();
      $.ajax({
        url: "vendors/add/framwork",
        type: "post",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          framework_name: frameworksName
        }
      }).done(function(response) {
        if (response.code = '200') {
          $('#framework_s').append(`<option value='${response.data.id}'> ${response.data.name} </option>`);
          toastr['success']('Framework Added successfully!!!', 'success');
        } else {
          toastr['error'](response.message, 'error');
        }
      }).fail(function(errObj) {
        $('#loading-image').hide();
        toastr['error'](errObj.message, 'error');
      });
    });
</script>
