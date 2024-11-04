@extends('layouts.app')

@section('title', 'Update Log')

@section('styles')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<style>
  .multiselect {
    width: 100%;
  }

  .multiselect-container li a {
    line-height: 3;
  }
</style>

@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <h2 class="page-heading">Update Log({{$updateLog->total()}})</h2>
  </div>
  <div class="col-12 mb-3">
    <div class="pull-left">
    </div>
    <div class="pull-right">
      <!-- <a title="add new domain" class="btn btn-secondary add-new-btn">+</a> -->
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-lg-12">
      <div class="card d-normal">
        <div class="card-header">
          <h4>
            <a class="collapsed card-link" data-toggle="collapse" href="#collapseSearch" aria-expanded="true">
              <i class="fa fa-arrow-up"></i>
              <i class="fa fa-arrow-down"></i>
              Filter
            </a>
          </h4>
        </div>
        <div id="collapseSearch" class="collapse show">
          <div class="card-body">
            <form action="/updateLog/search" method="GET">
              <div class="row">
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="form-label">API Urls</label>
                        <select class="form-control select2" name="api_url">
                          <option value=""></option>
                          {!! makeDropdown($listApiUrls ?? [], request('api_url')) !!}
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="form-label">Devices</label>
                        <select class="form-control select2" name="device">
                          <option value=""></option>
                          {!! makeDropdown($listDevices ?? [], request('device')) !!}
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="form-label">API Methods</label>
                        <select class="form-control select2" name="api_type">
                          <option value=""></option>
                          {!! makeDropdown($listApiMethods ?? [], request('api_type')) !!}
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="form-label">Response Codes</label>
                        <select class="form-control select2" name="response_code">
                          <option value=""></option>
                          {!! makeDropdown($listResponseCodes ?? [], request('response_code')) !!}
                        </select>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="col-md-12">
                  <button type="submit" class="btn btn-primary">Search</button>
                  <a href="/updateLog" class="btn btn-outline-secondary">Clear</a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12">

      <div class="table-responsive mt-2" style="overflow-x: auto !important;">
        <table class="table table-bordered" style="border: 1px solid #ddd !important;">
          <thead>
            <tr>
              <th style="width: 3%;">ID</th>
              <th style="width: 15%;">API Url</th>
              <th style="width: 7%;">Device</th>
              <th style="width: 6%;">Api Type</th>
              <th style="width: 10%;">Email</th>
              <th style="width: 6%;">Response Code</th>
              <th style="width: 15%;">Request Headers</th>
              <th style="width: 6%;">User Id</th>
              <th style="width: 6%;">App Version</th>
              <th style="width: 8%;">Start Time</th>
              <th style="width: 8%;">End Time</th>
              <th style="width: 8%;">Created At</th>
              <th style="width: 6%;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($updateLog->count()) { ?>
              <?php foreach ($updateLog as $key => $logData) { ?>
                <tr>
                  <td>{!! $logData->id !!}</td>
                  <td> 
                    <a href="{!! $logData->api_url !!}" target="_blank">
                      {!! strlen($logData->api_url) > 10 ? substr($logData->api_url, 0, 30).'...' : $logData->api_url !!}
                    </a>
                    @if(!is_null($logData->api_url))
                    <i class="fa fa-eye show_logs show-logs-icon" data-full_api_url="{{ $logData->api_url }}" data-id="{{ $logData->id }}" style="color: #808080;float: right;"></i> 
                    @endif
                  </td>
                  <td>{!! $logData->device !!}</td>
                  <td>{!! $logData->api_type !!}</td>
                  <td>{!! $logData->email !!}</td>
                  <td>{!! $logData->response_code !!}</td>
                  <td> {!! strlen($logData->request_header) > 10 ? substr($logData->request_header, 0, 30).'...' : $logData->request_header !!}
                    @if(!is_null($logData->request_header))<i class="fa fa-eye show_logs show-request-icon" data-id="{{ $logData->id }}" style="color: #808080;float: right;"></i>  @endif</td>
                  <td>{!! $logData->user_id !!}</td>
                  <td>{!! $logData->app_version !!}</td>                  
                  <td>{!! $logData->start_time ? date('Y-m-d', strtotime($logData->start_time)) : 0 !!}</td>
                  <td>{!! $logData->end_time ?date('Y-m-d', strtotime($logData->end_time)): 0 !!}</td>
                  <td>{!! date('Y-m-d', strtotime($logData->created_at)) !!}</td>
                  <td>
                    <a class="btn btn-xs" href="javascript:void(0);" onclick="funViewLog(this)" title="View Record"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-xs delete-updateLog-btn" title="Delete Record" data-id="{{ $logData->id }}" href="#"><i class="fa fa-trash"></i></a>

                    <div class="hidden-vals" style="display: none !important;">
                      <?php
                      $dataArr = [
                          'cls-api_url' => $logData->api_url,
                          'cls-device' => $logData->device,
                          'cls-api_type' => $logData->api_type,
                          'cls-user_id' => $logData->user_id,
                          'cls-email' => $logData->email,
                          'cls-app_version' => $logData->app_version,
                          'cls-other' => $logData->other,
                          'cls-start_time' => $logData->start_time ?: 0,
                          'cls-end_time' => $logData->end_time ?: 0,
                          'cls-created_at' => $logData->created_at,
                          'cls-request_header' => $logData->request_header,
                          'cls-request_body' => $logData->request_body,
                          'cls-response_code' => $logData->response_code,
                          'cls-response_body' => $logData->response_body,
                      ];
                  foreach ($dataArr as $key => $value) { ?>
                        <div class="hidden-val" data-key=".{{$key}}" style="display: none;"><code>{!! $value !!}</code></div>
                      <?php } ?>
                    </div>
                  </td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="10">No records found.</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <div class="text-center">
          {!! $updateLog->appends(Request::except('page'))->links() !!}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="api_url_modal">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">API URL</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-12" id="api_url_div">
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="request_modal">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Request Headers</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-12" id="request_modal_div">
                      <table class="table">
                          <thead>
                              <tr>
                              </tr>
                          </thead>
                          <tbody>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</div>

<div id="modalUpdateLog" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg" style="max-width: none !important;width: 85% !important;">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Update Log</h2>
        <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body" style="overflow-y: scroll;height: 650px;">
        <div class="table-responsive">
          <table class="table table-bordered" style="border: 1px solid #ddd !important;">
            <thead>
              <tr>
                <th width="25%">Parameter Name</th>
                <th width="75%">Parameter Value</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $dataArr = [
                  'cls-api_url' => 'Api Url',
                  'cls-api_type' => 'Api Type',
                  'cls-device' => 'Device',
                  'cls-email' => 'Email',
                  'cls-user_id' => 'User id',
                  'cls-app_version' => 'App version',
                  'cls-other' => 'Other',
                  'cls-start_time' => 'Start Time',
                  'cls-end_time' => 'End Time',
                  'cls-created_at' => 'Created at',
                  'cls-request_header' => 'Request header',
                  'cls-request_body' => 'Request body',
                  'cls-response_code' => 'Response code',
                  'cls-response_body' => 'Rresponse body',
              ];
            foreach ($dataArr as $key => $value) {
                echo '<tr>
                  <td>'.$value.'</td>
                  <td class="'.$key.'" style="word-break: break-word;"></td>
                </tr>';
            }
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

<link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.min.css')}}">
{{-- <link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.css')}}"> --}}
@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>

<script type="text/javascript" src="{{ mix('webpack-dist/js/mock.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.dropdown.min.js') }} "></script>
{{-- <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.dropdown.js') }} "></script> --}}


<script>
  var Random = Mock.Random;
  var json1 = Mock.mock({
    "data|10-50": [{
      name: function() {
        return Random.name(true)
      },
      "id|+1": 1,
      "disabled|1-2": true,
      groupName: 'Group Name',
      "groupId|1-4": 1,
      "selected": true
    }]
  });
  $('.dropdown-mul-1').dropdown({
    data: json1.data,
    limitCount: 40,
    multipleMode: 'label',
    choice: function() {
      // console.log(arguments,this);
    }
  });

  $('.dropdown-sin-1').dropdown({
    readOnly: true,
    input: '<input type="text" maxLength="20" placeholder="Search">'
  });
</script>
</div>

<script type="text/javascript">
  $('.multiselect').multiselect({
    enableClickableOptGroups: true
  });
  $(document).on("click", ".openmodeladdpostman", function(e) {
    $('#titleUpdate').html("Add");
    $('#postmanform').find("input[type=text], textarea").val("");
  });


  $(document).on("click", ".delete-updateLog-btn", function(e) {
    e.preventDefault();
    if (confirm("Are you sure?")) {
      var $this = $(this);
      var id = $this.data('id');
      $.ajax({
        url: "/updateLog/delete",
        type: "delete",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          id: id
        }
      }).done(function(response) {
        if (response.code = '200') {
          toastr['success']('Postman deleted successfully!!!', 'success');
          location.reload();
        } else {
          toastr['error'](response.message, 'error');
        }
      }).fail(function(errObj) {
        $('#loading-image').hide();
        $("#addPostman").hide();
        toastr['error'](errObj.message, 'error');
      });
    }
  });

  $(document).ready(function() {
    $('#per_user_name').select2();
  });

  function funViewLog(ele) {
    let mdl = jQuery('#modalUpdateLog');
    jQuery(ele).closest('tr').find('.hidden-val').each(function() {
      mdl.find(jQuery(this).attr('data-key')).html(jQuery(this).html());
    });
    mdl.modal('show');
  }

  jQuery(document).ready(function() {
    applySelect2(jQuery('.select2'));
  });

  $(document).on('click', '.show-logs-icon', function() {
      var id = $(this).data('id');
      var fullApiUrl = $(this).data('full_api_url');
      $('#api_url_modal').modal('show');
      $('#api_url_div').text(fullApiUrl);
  });

    $(document).on('click', '.show-request-icon', function() {
		var id = $(this).data('id');
			$.ajax({
				url: '{{route('updateLog.request.header.show')}}',
				method: 'GET',
				data: {
					id: id
				},
				success: function(response) {
					$('#request_modal').modal('show');
					$('#request_modal_div').html(response);
				},
				error: function(xhr, status, error) {
					alert("Error occured.please try again");
				}
			});
		});

</script>
@endsection