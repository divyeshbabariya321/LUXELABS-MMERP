@extends('layouts.app')

@section('title', 'Post man Request')

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
  .select2{
    width:200px !important;
  }
  #postmanform label{
    text-transform: capitalize;
    line-height: 31px;
  }
  .label-btn{
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .label-btn .btn{
    height: 30px !important;
    line-height: 17px;
    margin: 0 !important;
  }

  .modal-header .close {
    font-size: 23px;
    color: #000;
    opacity: 1;
    margin: 0rem 0rem 0rem auto;
  }
  .modal-content .modal-header .close{
    margin-top: -15px !important;

  }

  .modal-header .modal-title {
    font-size: 18px;
    font-weight: 600;
  }

  .custom-select + .select2.select2-container.select2-container--default{
    height: 32px;
  }

  .custom-error-message {
    color: red;
    margin-top: 5px !important;
  }
</style>

@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <h2 class="page-heading">Postman Request ({{$counter}})</h2>
  </div>

  <div class="col-12 mb-3">
    <div class="pull-left">
    </div>
    <div class="pull-right">
      <!-- <a title="add new domain" class="btn btn-secondary add-new-btn">+</a> -->
    </div>
  </div>
</div>
<div class=" row " style="margin-left:20px;">
  <form class="form-inline" action="/postman/search" method="GET">
    <div class="col">
      <div class="form-group">
        <div class="input-group">
          <?php
            //dd(request('folder_name'));
            if (request('folder_name')) {
                $folder_nameArr = request('folder_name');
            } else {
                $folder_nameArr = [];
            }
          ?>
          <select name="folder_name[]" class="form-control select2 folder-name-filter" multiple id="folder_name_filter">
            <option value="" @if(count($folder_nameArr)==0) selected @endif>-- Select Folder --</option>
            <?php
            $ops = 'id';

          foreach ($folders as $folder) {
              $selected = '';
              if (in_array($folder->id, $folder_nameArr)) {
                  $selected = 'selected';
              }
              echo '<option value="'.$folder->id.'" '.$selected.'>'.$folder->name.'</option>';
          }
          ?>
          </select>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <div class="input-group">
          <?php
          //dd(request('folder_name'));
          if (request('request_name')) {
              $request_nameArr = request('request_name');
          } else {
              $request_nameArr = [];
          }
          ?>
          <select name="request_name[]" class="form-control request-name-filter select2" multiple id="request_name_filter">
            <option value="" @if(count($request_nameArr)==0) selected @endif>-- Select Request Name --</option>
            @foreach ($listRequestNames as $key => $reqName)
            <?php
            $selected = '';
          if (in_array($reqName, $request_nameArr)) {
              $selected = 'selected = "selected"';
          }
          ?>
            <option {{$selected}} value="{{$reqName}}">{{$reqName}}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <div class="input-group">
          <?php
          //dd(request('folder_name'));
          if (request('request_type')) {
              $request_typeArr = request('request_type');
          } else {
              $request_typeArr = [];
          }
          ?>
          <select name="request_type[]" value="" class="form-control request-types-filter select2" multiple id="request_types_filter">

            <option value="" @if(count($request_typeArr)==0) selected @endif>-- Select Method --</option>
            <option value="GET" <?php if (in_array('GET', $request_typeArr)) {
                echo 'selected';
            } ?>>GET</option>
            <option value="POST" <?php if (in_array('POST', $request_typeArr)) {
                echo 'selected';
            } ?>>POST</option>
            <option value="PUT" <?php if (in_array('PUT', $request_typeArr)) {
                echo 'selected';
            } ?>>PUT</option>
            <option value="PATCH" <?php if (in_array('PATCH', $request_typeArr)) {
                echo 'selected';
            } ?>>PATCH</option>
            <option value="DELETE" <?php if (in_array('DELETE', $request_typeArr)) {
                echo 'selected';
            } ?>>DELETE</option>
          </select>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <div class="input-group">
          <input type="text" placeholder="Search By ID" class="form-control" name="search_id" value="{{request('search_id')}}">
        </div>
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <div class="input-group">
          <input type="text" placeholder="Search By Keyword" class="form-control" name="keyword" value="{{request('keyword')}}">
        </div>
      </div>
    </div>
    <div class="col">
      <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
      <a href="/postman" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
    </div>
  </form>
  <button type="button" class="btn custom-button float-right mr-3 openmodeladdpostman" data-toggle="modal" data-target="#addPostman">Add Postman request</button>
  <a href="/postman/folder" class="btn custom-button float-right mr-3">Add Folder</a>
  <a href="/postman/workspace" class="btn custom-button float-right mr-3">Add Workspace</a>
  <a href="/postman/collection" class="btn custom-button float-right mr-3">Add Collection</a>
  <button type="button" class="btn custom-button float-right mr-3 openmodeladdpostman" data-toggle="modal" data-target="#status-create">Add Status</button>
<button type="button" class="btn custom-button float-right mr-3" data-toggle="modal" data-target="#postmandatatablecolumnvisibilityList">Column Visiblity</button>
<button class="btn custom-button mr-3" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
<!-- <a href="postman/request/history" class="btn custom-button mr-3" target="_blank">Request History</a> -->
    <a href="postman/response/history" class="btn custom-button mr-3" target="_blank">Response History</a>
    <button type="button" class="btn custom-button float-right mr-3" data-toggle="modal" data-target="#runRequestUrl">Run Request URL</button>
  <div class="col-12">
    <h3>Assign Permission to User</h3>
    <form class="form-inline" id="update_user_permission" action="/postman/user/permission" method="POST">
      <div class="form-group">
        <div class="input-group">
          <select name="per_folder_name" class="form-control" id="per_folder_name" required>
            <option value="">--select folder for Permission--</option>
            <?php
            $ops = 'id';
          foreach ($folders as $folder) {
              $selected = '';
              if ($folder->id == request('per_folder_name')) {
                  $selected = 'selected';
              }
              echo '<option value="'.$folder->id.'" '.$selected.'>'.$folder->name.'</option>';
          }
          ?>
          </select>
        </div>
      </div> &nbsp;&nbsp;&nbsp;
      <div class="form-group">
        <div class="input-group">
          <select name="per_user_name" class="form-control" id="per_user_name" required>
            <option value="">--select user for Permission--</option>
            <?php
          foreach ($users as $user) {
              $selected = '';
              if ($user->id == request('per_user_name')) {
                  $selected = 'selected';
              }
              echo '<option value="'.$user->id.'" '.$selected.'>'.$user->name.'</option>';
          }
          ?>
          </select>
        </div>
      </div> &nbsp;&nbsp;
      <button type="submit" class="btn custom-button update-userpermission">Update User Permission</button>
    </form>
  </div>

</div>

</br>

@include('postman.partials.table.table')

@endsection
 <!-- Stuatus Create  Modal content-->
 <div id="status-create" class="modal fade in" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Add Stauts</h4>
      <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <form  method="POST" id="status-create-form">
        @csrf
        @method('POST')
          <div class="modal-body">
            <div class="form-group">
              {{ html()->label('Name', 'status_name')->class('form-control-label') }}
              {{ html()->text('status_name')->class('form-control')->required()->attribute('rows', 3) }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary status-save-btn">Save</button>
          </div>
        </div>
      </form>
    </div>

  </div>
</div>

<div id="postmanHistory" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Postman History</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Updated by</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody class="tbodayPostmanHistory">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanRequesteHistoryModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Postman Request History</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User Name</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody class="tbodayPostmanRequestHistory">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="postmanRemarkHistoryModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Postman Remark History</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User Name</th>
                  <th>Remarks</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody class="tbodayPostmanRemarkHistory">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanUserDetailsModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">URLs</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body postmanUserDetailsModelBody">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanUrlDetailsModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Users</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" id="multiUrls">
              <div class="postmanUrlDetailsBody"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary postman-send-request-btn" data-id="null">Send</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanResponseHistoryModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Postman Response History</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User Name</th>
                  <th>Response</th>
                  <th>Response code</th>
                  <th>Request</th>
                  <th>Parameters</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody class="tbodayPostmanResponseHistory">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanEditHistoryModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Postman Edit History</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User Name</th>
                  <th>Parmiters</th>
                  <th>Urls</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody class="tbodayPostmanEditHistory">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanErrorModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Postman Error History</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Error Type</th>
                    <th>Error</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody class="tbodayPostmanErrorHistory">
                </tbody>
              </table>  
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<div id="addPostman" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><span id="titleUpdate">Add</span> Postman Request</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="postmanform" method="post" class="mb-0">
              @csrf
              <div class="form-row">
                <input type="hidden" id="post_id" name="id" value="" />
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="title">User Name</label>
                    <div class="dropdown-sin-1 postman-dropdown-display">
                      
                      <select name="user_permission[]" multiple class="folder_name form-control dropdown-mul-1" id="user_permission" required >
                        <option>--Users--</option>
                        <?php
                      foreach ($users as $user) {
                          echo '<option value="'.$user->id.'" data-folder_name="'.$user->name.'">'.$user->name.'</option>';
                      }
          ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="title">Folder Name</label>
                    <input type="hidden" name="folder_real_name" id="folder_real_name">
                    <select name="folder_name" class="form-control folder_name" id="folder_name" required>
                      <option value="">--Folder--</option>
                      <?php
                      $ops = 'id';
          foreach ($folders as $folder) {
              echo '<option value="'.$folder->id.'" data-folder_name="'.$folder->name.'">'.$folder->name.'</option>';
          }
          ?>
                    </select>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="request_name">Request Name</label>
                    <input type="text" name="request_name" value="" class="form-control" id="request_name" placeholder="Enter request name">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="request_types">Request Type</label>
                    <select name="request_types" value="" class="form-control" id="request_types">
                      <option value="GET">GET</option>
                      <option value="POST">POST</option>
                      <option value="PUT">PUT</option>
                      <option value="PATCH">PATCH</option>
                      <option value="DELETE">DELETE</option>
                    </select>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-12">
                    <label for="request_url">Request Url <a href="javascript:void(0)" class="btn-show-request-url">Show / Hide</a></label>
                    <div class="form-group add_more_urls_div">
                      <input type="text" name="request_url[]" value="" class="form-control" id="request_url" placeholder="Enter request url">
                    </div>
                    <br>
                    <a style="cursor: pointer;" class="add_more_urls"><i class="fa fa-plus"> Add more</i></a>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="controller_name">Controller Name</label>
                    <input type="text" name="controller_name" value="" class="form-control" id="controller_name" placeholder="Enter Controller Name">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="method_name">Method Name</label>
                    <input type="text" name="method_name" value="" class="form-control" id="method_name" placeholder="Enter Method Name">
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="remark">Remark</label>
                    <input type="text" name="remark" value="" class="form-control" id="remark" placeholder="Enter Remark">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="params">Params</label>
                    <textarea name="params" value="" class="form-control" id="params" placeholder="Enter params ex. filedName1: value1, filedName2: value2" style="height: 34px;"></textarea>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="authorization_type">Authorization type</label>
                    <select name="authorization_type" value="" class="form-control" id="authorization_type">
                      <option value="Bearer Token">Bearer Token</option>
                      <option value="Basic Auth">Basic Auth</option>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="authorization_token">Authorization token</label>
                    <input type="text" name="authorization_token" value="" class="form-control" id="authorization_token" placeholder="Enter authorization token">
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="request_headers">headers</label>
                    <textarea name="request_headers" value="" class="form-control" id="request_headers" placeholder="Enter headers ex. filedName1: value1, filedName2: value2" style="height: 34px;"></textarea>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="body_type">Body type</label>
                    <select name="body_type" value="" class="form-control" id="body_type">
                      <option value="raw">Raw</option>
                    </select>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="body_json" class="label-btn">Body Json
                      <button type="button" class="btn custom-button float-right mr-3 add-json" data-toggle="modal" data-target="#addPostmanJsonModel">Add Json</button>
                    </label>
                    <select name="body_json" value="" class="form-control" id="body_json">
                      <option value="">select Json</option>
                      @foreach ($postJsonVer as $jsonVer)
                      <?php $name = $jsonVer->json_Name ?? substr($jsonVer->request_data, 0, 60); ?>
                      <option value="{{$jsonVer->request_data}}">{{$jsonVer->version_json.' '.$name}}</option>
                      @endforeach
                    </select>
                    
                  </div>
                  <div class="form-group col-md-6">
                    <label for="pre_request_script">Pre request script</label>
                    <textarea name="pre_request_script" value="" class="form-control" id="pre_request_script" placeholder="Enter pre_request_script" style="height: 34px;"></textarea>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="tests">Tests</label>
                    <input type="text" name="tests" value="" class="form-control" id="tests" placeholder="Enter tests">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="end_point">End Point</label>
                    <input type="text" name="end_point" value="" class="form-control" id="end_point" placeholder="Enter end point">
                  </div>
                </div>

                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="grumphp_errors">Grumphp Errors</label>
                    <input type="text" name="grumphp_errors" value="" class="form-control" id="grumphp_errors" placeholder="Enter Grumphp Errors">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="magento_api_standards">Magento API Standards</label>
                    <input type="text" name="magento_api_standards" value="" class="form-control" id="magento_api_standards" placeholder="Enter Magento API Standards">
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="swagger_doc_block">Swagger DocBlock</label>
                    <input type="text" name="swagger_doc_block" value="" class="form-control" id="swagger_doc_block" placeholder="Enter Swagger DocBlock">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="used_for">Used for</label>
                    <input type="text" name="used_for" value="" class="form-control" id="used_for" placeholder="Enter Used for">
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="user_in">Used in</label>
                    <select name="user_in" value="" class="form-control" id="user_in">
                      <option value="ERP">ERP</option>
                      <option value="Mobile">Mobile</option>
                      <option value="Both">Both</option>
                      <option value="Unknown">Unknown</option>
                    </select>
                  </div>
                </div>

              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-secondary submit-form">Save</button>
          </div>
          <div id="addPostman" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
              <!-- Modal content-->
              <div class="modal-content ">
                <div id="add-mail-content">

                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title"><span id="titleUpdate">Add</span> Postman Request</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <form id="postmanform" method="post">
                        @csrf
                        <div class="form-row">
                          <input type="hidden" id="post_id" name="id" value="" />

                          <div class="form-group col-md-12">
                            <label for="title">User Name</label>
                            <input type="hidden" name="folder_real_name" id="folder_real_name">
                            <div class="dropdown-sin-1">
                              
                              <select name="user_permission[]" multiple class="form-control folder_name dropdown-mul-1" id="user_permission" required>
                                <option>--Users--</option>
                                <?php
                      foreach ($users as $user) {
                          echo '<option value="'.$user->id.'" data-folder_name="'.$user->name.'">'.$user->name.'</option>';
                      }
          ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="title">Folder Name</label>
                            <input type="hidden" name="folder_real_name" id="folder_real_name">
                            <select name="folder_name" class="form-control folder_name" id="folder_name" required>
                              <option value="">--Folder--</option>
                              <?php
                              $ops = 'id';
          foreach ($folders as $folder) {
              echo '<option value="'.$folder->id.'" data-folder_name="'.$folder->name.'">'.$folder->name.'</option>';
          }
          ?>
                            </select>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="request_name">Request Name</label>
                            <input type="text" name="request_name" value="" class="form-control" id="request_name" placeholder="Enter request name">
                          </div>
                          <div class="form-group col-md-12">
                            <label for="request_types">Request Type</label>
                            <select name="request_types" value="" class="form-control" id="request_types">
                              <option value="GET">GET</option>
                              <option value="POST">POST</option>
                              <option value="PUT">PUT</option>
                              <option value="PATCH">PATCH</option>
                              <option value="DELETE">DELETE</option>
                            </select>
                          </div>
                          <div class="form-group col-md-12 ">
                            <label for="request_url">Request Url</label>
                            <div class="form-group  add_more_urls_div">
                              <input type="text" name="request_url[]" value="" class="form-control request_url" id="request_url" placeholder="Enter request url">
                            </div>
                            <br>
                            <a style="cursor: pointer;" class="add_more_urls"><i class="fa fa-plus">Add more</i></a>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="controller_name">Controller Name</label>
                            <input type="text" name="controller_name" value="" class="form-control" id="controller_name" placeholder="Enter Controller Name">
                          </div>
                          <div class="form-group col-md-12">
                            <label for="method_name">Method Name</label>
                            <input type="text" name="method_name" value="" class="form-control" id="method_name" placeholder="Enter Method Name">
                          </div>
                          <div class="form-group col-md-12">
                            <label for="remark">Remark</label>
                            <input type="text" name="remark" value="" class="form-control" id="remark" placeholder="Enter Remark">
                          </div>
                          <div class="form-group col-md-12">
                            <label for="params">Params</label>
                            <textarea name="params" value="" class="form-control" id="params" placeholder="Enter params ex. filedName1: value1, filedName2: value2"></textarea>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="authorization_type">Authorization type</label>
                            <select name="authorization_type" value="" class="form-control" id="authorization_type">
                              <option value="Bearer Token">Bearer Token</option>
                              <option value="Basic Auth">Basic Auth</option>
                            </select>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="authorization_token">Authorization token</label>
                            <input type="text" name="authorization_token" value="" class="form-control" id="authorization_token" placeholder="Enter authorization token">
                          </div>
                          <div class="form-group col-md-12">
                            <label for="request_headers">headers</label>
                            <textarea name="request_headers" value="" class="form-control" id="request_headers" placeholder="Enter headers ex. filedName1: value1, filedName2: value2"></textarea>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="body_type">Body type</label>
                            <select name="body_type" value="" class="form-control" id="body_type">
                              <option value="raw">Raw</option>
                            </select>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="body_json">Body Json
                              <a href="#" id="addJson"> Add</a>
                            </label>
                            
                            <select name="body_json" class="form-control" id="body_json">
                              <option value="">select Json</option>
                              @foreach ($postJsonVer as $jsonVer)
                              <option value="{{$jsonVer->request_data}}">{{$jsonVer->version_json.' '.$jsonVer->request_data}}</option>
                              @endforeach
                            </select>
                            
                          </div>
                          <div class="form-group col-md-12">
                            <label for="pre_request_script">Pre request script</label>
                            <textarea name="pre_request_script" value="" class="form-control" id="pre_request_script" placeholder="Enter pre_request_script"></textarea>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="tests">Tests</label>
                            <input type="text" name="tests" value="" class="form-control" id="tests" placeholder="Enter tests">
                          </div>
                        </div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-secondary submit-form">Save</button>
                    </div>

                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<div id="create-quick-task" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo route('task.create.multiple.task.shortcutpostman'); ?>" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create Task</h4>
                </div>
                <div class="modal-body">

                    <input class="form-control" value="53" type="hidden" name="category_id" />
                    <input class="form-control" value="" type="hidden" name="category_title" id="category_title" />
                    <input class="form-control" type="hidden" name="site_id" id="site_id" />
                    <div class="form-group">
                        <label for="">Subject</label>
                        <input class="form-control" type="text" id="hidden-task-subject" name="task_subject" />
                    </div>
                    <div class="form-group">
                        <select class="form-control" style="width:100%;" name="task_type" tabindex="-1" aria-hidden="true">
                            <option value="0">Other Task</option>
                            <option value="4">Developer Task</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="repository_id">Repository:</label>
                        <br>
                        <select style="width:100%" class="form-control  " id="repository_id" name="repository_id">
                            <option value="">-- select repository --</option>
                            @foreach ($githubRepository as $repository)
                            <option value="{{ $repository->id }}">{{ $repository->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Details</label>
                        <input class="form-control text-task-development" type="text" name="task_detail" />
                    </div>

                    <div class="form-group">
                        <label for="">Cost</label>
                        <input class="form-control" type="text" name="cost" />
                    </div>

                    <div class="form-group">
                        <label for="">Assign to</label>
                        <select name="task_asssigned_to" class="form-control assign-to select2">
                            @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Create Review Task?</label>
                        <div class="form-group">
                            <input type="checkbox" name="need_review_task" value="1" />
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label for="">Websites</label>
                        <div class="form-group website-list row">
                           
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default create-task">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="dev_task_statistics" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Dev Task statistics</h2>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" id="dev_task_statistics_content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Task type</th>
                                <th>Task Id</th>
                                <th>Assigned to</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addPostmanJsonModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Json</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="postmanform" method="post">
              @csrf
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="jsonVersion">Tests</label>
                  <input type="text" name="jsonVersion" required value="" class="form-control" id="jsonVersion" placeholder="Enter Json Here">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="jsonName">Name</label>
                  <input type="text" name="jsonName" required value="" class="form-control" id="jsonName" placeholder="Enter Json Name">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-secondary postman-addJson">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="view-domain" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="view-domain-content">

      </div>
    </div>
  </div>
</div>

<div id="preview-task-image" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered" style="table-layout: fixed">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Sl no</th>
                                <th style=" width: 30%">Files</th>
                                <th style="word-break: break-all; width: 40%">Send to</th>
                                <th style="width: 10%">User</th>
                                <th style="width: 10%">Created at</th>
                                <th style="width: 15%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="task-image-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="postmanShowFullTextModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Full text view</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="postmanShowFullTextBody postManViewDiv">

            </div>
            <div class="postmanFieldEditDiv postManEditDiv">
              <form action="#" id="updatePostManField" method="POST">
                @csrf
                <div class="form-group">
                  <textarea name="body_json" id="postManFieldEdit" cols="30" rows="10" class="form-control"></textarea>
                  <input type="hidden" name="id" id="postManFieldId" value="">
                </div>
              </form>
              
            </div>
            
          </div>
          <div class="modal-footer postManShowEdit">
            <button type="button" class="btn btn-default " id="postManFieldEditBtn">Edit</button>
          </div>
          <div class="modal-footer postManEditDiv">
            <button type="button" class="btn btn-default " id="postManFieldSaveBtn">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- /var/www/html/erp/resources/views/postman/postman-status-history.blade.php --}}
@include('postman.postman-status-history')
@include('postman.postman-api-issue-fix-done-history')
@include("postman.column-visibility-modal")
@include("postman.partials.modal-status-color")
@include("postman.partials.modal-add-remark")
@include("postman.partials.modal-responses-history")
@include("postman.run-request-url-modal")
<link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.min.css')}}">
{{-- <link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.css')}}"> --}}
@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
{{-- <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

{{-- <script type="text/javascript" src="{{ mix('webpack-dist/js/mock.js') }} "></script> --}}
<script src="https://cdn.jsdelivr.net/npm/mockjs@1.1.0/dist/mock.js"></script>
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

<script type="text/javascript">
    function Showactionbtn(id){
        $(".action-btn-tr-"+id).toggleClass('d-none')
    }
  // $('ul.pagination').hide();
  //   $('.infinite-scroll').jscroll({
  //     autoTrigger: true,
  //     // debug: true,
  //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
  //     padding: 0,
  //     nextSelector: '.pagination li.active + li a',
  //     contentSelector: 'div.infinite-scroll',
  //     callback: function () {
  //       $('ul.pagination').first().remove();
  //       $('ul.pagination').hide();
  //     }
  // });



  $('.multiselect').multiselect({
    enableClickableOptGroups: true
  });
  $(document).on("click", ".openmodeladdpostman", function(e) {
    $('#titleUpdate').html("Add");
    $('.add_more_urls_div').html('');
    $('.add_more_urls_div').append('<input type="text" name="request_url[]" value="" class="form-control" id="request_url" placeholder="Enter request url">');
    $('#postmanform').find("input[type=text], textarea").val("");
  });
  $(document).on("click", ".add_more_urls", function(e) {
    $('.add_more_urls_div').append('<input type="text" name="request_url[]" value="" class="form-control" id="request_url" placeholder="Enter request url">');
  });

  $(document).on("click", "#see_users", function(e) {
    e.preventDefault();
    //debugger;
    var $this = $(this);
    var id = $this.data('user_details');
    $('.postmanUserDetailsModelBody').html(id);
  });

  $(document).on("change", ".folder_name", function(e) {
    e.preventDefault();
    var folder_name = $(this).find(':selected').attr('data-folder_name');
    //debugger;
    $('#folder_real_name').val(folder_name);
  });

  $(document).on("click", ".delete-postman-btn", function(e) {
    e.preventDefault();
    if (confirm("Are you sure?")) {
      var $this = $(this);
      var id = $this.data('id');
      $.ajax({
        url: "/postman/delete",
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
  $(document).on("click", ".submit-form", function(e) {
    e.preventDefault();
    $('#loading-image').show();
    clearAllFormErrors();

    var $this = $(this);
    if ($('#titleUpdate').text() == 'Add')
      $("#post_id").val("");
    $.ajax({
      url: "/postman/create/",
      type: "post",
      data: $('#postmanform').serialize()
    }).done(function(response) {
      if (response.code == '200') {
        $('#loading-image').hide();
        $('#addPostman').modal('hide');
        toastr['success']('Postman added successfully!!!', 'success');

        let page_url = '/postman?page=' + $('#pagination .active .page-link').html();;
        refreshPagination(page_url);
      }else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {

      $('#loading-image').hide();
      //$("#addMail").hide();
      if(errObj.status == 422){
        toastr['error'](errObj.responseJSON.message, 'error');
        
        setFormErrors(errObj.responseJSON.errors);
      }else{
        toastr['error'](errObj.message, 'error');
      }
    });
  });

  $(document).on("click", ".edit-postman-btn", function(e) {
    e.preventDefault();
    $('#titleUpdate').html("Update");
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/edit/",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        form = $('#postmanform');
        $.each(response.data, function(key, v) {
          if (form.find('[name="' + key + '"]').length) {
            form.find('[name="' + key + '"]').val(v);
          } else if (key == 'request_type') {
            form.find('[name="' + key + 's"]').val(v);
          } else if (key == 'request_url') {
            //form.find('[name="'+key+'[]"]').val(v);
            $('.add_more_urls_div').html('');
            $('.add_more_urls_div').css('display', 'none');
            $('.add_more_urls_div').append('<input type="text" id="searchInput" placeholder="Search URL" class="form-control"><br/>');
            $.each(response.postmanUrl, function(i, e) {
              $('.add_more_urls_div').append('<input type="text" name="request_url[]" value="' + e.request_url + '" class="form-control urlInput" id="request_url" placeholder="Enter request url">');
            });
          } else if (key == 'folder_name') {
            $("#folder_name").val(v);
          } else if (form.find('[name="' + key + '[]"]').length) {
            form.find('[name="' + key + '[]"]').val(v);
            //debugger;
            $.each(v.split(","), function(i, e) {
              $("#user_permission option[value='" + e + "']").prop("selected", true);
            });
          }
        });
        $("#folder_name").html(response.ops);
        $('#addPostman').modal('show');
        toastr['success']('Postman added successfully!!!', 'success');

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      $("#addPostman").hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".postman-history-btn", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/history/",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        var t = '';
        $.each(response.data, function(key, v) {
          t += '<tr><td>' + v.id + '</td>';
          t += '<td>' + v.userName + '</td>';
          t += '<td>' + v.created_at + '</td></tr>';
        });
        $(".tbodayPostmanHistory").html(t);
        $('#postmanHistory').modal('show');
        toastr['success']('Postman added successfully!!!', 'success');

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      $("#postmanHistory").hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".postman-list-url-btn", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/get/mul/request",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        $(".postmanUrlDetailsBody").html('');
        $(".postmanUrlDetailsBody").html(response.data);
        if ($('#postmanUrlDetailsModel').length) {
          $('#postmanUrlDetailsModel .postman-send-request-btn').attr('data-id', id);
          $('#postmanUrlDetailsModel').modal('show');
        }else{
          toastr['error']('Modal open failed', 'error');
        }
        toastr['success']('Postman Url listed successfully!!!', 'success');
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".postman-send-request-btn", function(e) {
    e.preventDefault();
    var clicked = $(this);
    var id = clicked.attr('data-id');
    var historyPopup = $(`.responses-history[data-id="${id}"]`)
    $('#loading-image').show();

    $.ajax({
      url: "/postman/send/request",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: $('#multiUrls').serialize()
    }).done(function(response) {
      if (response.code == '200') {
        toastr['success']('Postman requested successfully!!!', 'success');
        $('#postmanUrlDetailsModel .postman-send-request-btn').attr('data-id', null);
        $('#postmanUrlDetailsModel').modal('hide');
        $('#loading-image').hide();
        historyPopup.click();
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });

    $(document).on("click",".preview_response",function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
          url: "/postman/response/history/",
          type: "post",
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          data:{
            id:id
          }
        }).done(function(response) {
          if(response.code = '200') {
            var t = '';
            $.each(response.data, function(key, v) {
              var responseString  = '';
              if(v.response)
                responseString = v.response.substring(0,10);
              var request_data_val  = '';
              if(v.request_data)
                request_data_val = v.request_data.substring(0,10);
              var request_url_val  = '';
              if(v.request_data)
              request_url_val = v.request_url.substring(0,10)


              t += '<tr><td>'+v.id+'</td>';
              t += '<td>'+v.userName+'</td>';
              t += '<td  class="expand-row-msg" data-name="response" data-id="'+v.id+'" ><span class="show-short-response-'+v.id+'">'+responseString+'...</span>    <span style="word-break:break-all;" class="show-full-response-'+v.id+' hidden">'+v.response+'</span></td>';
              t += '<td>'+v.response_code+'</td>';
              t += '<td  class="expand-row-msg" data-name="request_url" data-id="'+v.id+'" ><span class="show-short-request_url-'+v.id+'">'+request_url_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_url-'+v.id+' hidden">'+v.request_url+'</span></td>';
              t += '<td  class="expand-row-msg" data-name="request_data" data-id="'+v.id+'" ><span class="show-short-request_data-'+v.id+'">'+request_data_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_data-'+v.id+' hidden">'+v.request_data+'</span></td>';
              t += '<td>'+v.created_at+'</td></tr>';
            });
            $(".tbodayPostmanResponseHistory").html(t);
            $('#postmanResponseHistoryModel').modal('show');
            toastr['success']('Postman response listed successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#postmanResponseHistory").hide();
           toastr['error'](errObj.message, 'error');
        });
    });

    
    $(document).on("click",".preview_postman_error",function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
          url: "/postman/get/error/history",
          type: "post",
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          data:{
            id:id
          }
        }).done(function(response) {
          if(response.code = '200') {
            var t = '';
            $.each(response.data, function(key, v) {
              var parent_id_type  = '';
              if(v.parent_id_type)
                parent_id_type = v.parent_id_type.substring(0,10);
              var error  = '';
              if(v.error)
              error = v.error.substring(0,10)


              t += '<tr><td>'+v.id+'</td>';
              t += '<td>'+v.userName+'</td>';
              t += '<td  class="expand-row-msg" data-name="type" data-id="'+v.id+'" ><span class="show-short-type-'+v.id+'">'+parent_id_type+'...</span>    <span style="word-break:break-all;" class="show-full-type-'+v.id+' hidden">'+v.parent_id_type+'</span></td>';
              t += '<td  class="expand-row-msg" data-name="error" data-id="'+v.id+'" ><span class="show-short-error-'+v.id+'">'+error+'...</span>    <span style="word-break:break-all;" class="show-full-error-'+v.id+' hidden">'+v.error+'</span></td>';
              t += '<td>'+v.created_at+'</td></tr>';
            });
            $(".tbodayPostmanErrorHistory").html(t);
            $('#postmanErrorModel').modal('show');
            toastr['success']('Postman Error history listed successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#postmanErrorModel").hide();
           toastr['error'](errObj.message, 'error');
        });
    });
    

  $(document).on("click", ".preview_edit_history", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/edit/history/",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        var t = '';
        $.each(response.data, function(key, v) {
          var body_json_data = '';
          if (v.body_json)
            body_json_data = v.body_json.substring(0, 10);
          var request_data_val = '';
          if (v.request_data)
            request_data_val = v.request_data.substring(0, 10);
          var request_url_val = '';
          if (v.request_url)
            request_url_val = v.request_url.substring(0, 10)


          t += '<tr><td>' + v.id + '</td>';
          t += '<td>' + v.userName + '</td>';
          t += '<td  class="expand-row-msg" data-name="body_json" data-id="' + v.id + '" ><span class="show-short-body_json-' + v.id + '">' + body_json_data + '...</span>    <span style="word-break:break-all;" class="show-full-body_json-' + v.id + ' hidden">' + v.body_json + '</span></td>';
          //t += '<td>'+v.response_code+'</td>';
          t += '<td  class="expand-row-msg" data-name="request_url" data-id="' + v.id + '" ><span class="show-short-request_url-' + v.id + '">' + request_url_val + '...</span>    <span style="word-break:break-all;" class="show-full-request_url-' + v.id + ' hidden">' + v.request_url + '</span></td>';
          //t += '<td  class="expand-row-msg" data-name="request_data" data-id="'+v.id+'" ><span class="show-short-request_data-'+v.id+'">'+request_data_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_data-'+v.id+' hidden">'+v.request_data+'</span></td>';
          t += '<td>' + v.created_at + '</td></tr>';
        });
        $(".tbodayPostmanEditHistory").html(t);
        $('#postmanEditHistoryModel').modal('show');
        toastr['success']('Postman Edit History listed successfully!!!', 'success');

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      $("#postmanResponseHistory").hide();
      toastr['error'](errObj.message, 'error');
    });
  });


  $(document).on("click", ".preview_requested", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/requested/history/",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        var t = '';
        $.each(response.data, function(key, v) {
          t += '<tr><td>' + v.id + '</td>';
          t += '<td>' + v.userName + '</td>';
          t += '<td>' + v.created_at + '</td></tr>';
        });
        $(".tbodayPostmanRequestHistory").html(t);
        $('#postmanRequesteHistoryModel').modal('show');
        toastr['success']('Postman Requeste listed successfully!!!', 'success');

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      $("#postmanRequesteHistory").hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".preview_remark_history", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/remark/history/",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        var t = '';
        $.each(response.data, function(key, v) {
          t += '<tr><td>' + v.id + '</td>';
          t += '<td>' + v.userName + '</td>';
          t += '<td>' + v.remark + '</td>';
          t += '<td>' + v.created_at + '</td></tr>';
        });
        $(".tbodayPostmanRemarkHistory").html(t);
        $('#postmanRemarkHistoryModel').modal('show');
        toastr['success']('Postman Remark History listed successfully!!!', 'success');

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      $("#postmanRemarkHistoryModel").hide();
      toastr['error'](errObj.message, 'error');
    });
  });
  $(document).on("click", "addJson", function(e) {
    $('#addPostmanJsonModel').modal('show');
  });
  $(document).on("click", ".postman-addJson", function(e) {
    e.preventDefault();
    var jsonData = $('#jsonVersion').val();
    var jsonName = $('#jsonName').val();
    $.ajax({
      url: "postman/add/json/version",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        json_data: jsonData,
        json_name: jsonName
      }
    }).done(function(response) {
      if (response.code = '200') {
        $('#body_json').append(`<option value='${response.data.request_data}'>
                                       ${response.data.version_json+' '+response.data.json_Name}
                                  </option>`);
        toastr['success']('Json Added successfully!!!', 'success');
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".removeuser", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    var user_id = $this.data('user_permision_id');
    $.ajax({
      url: "postman/removeuser/permission",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id,
        user_id: user_id
      }
    }).done(function(response) {
      if (response.code = '200') {
        $("#" + id + user_id).css('display', 'none');
        toastr['success']('User Removed successfully!!!', 'success');
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".update-userpermission", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    var per_folder_name = $('#per_folder_name').val();
    var per_user_name = $('#per_user_name').val();
    if (per_folder_name && per_user_name) {
      $.ajax({
        url: "{{route('postman.permission')}}",
        type: "post",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          per_folder_name: per_folder_name,
          per_user_name: per_user_name
        }

      }).done(function(response) {
        $('#loading-image').hide();
        if (response.code = '200') {
          toastr['success'](response.message, 'success');
          location.reload();
        } else {
          toastr['error'](response.message, 'error');
        }
      }).fail(function(errObj) {
        $('#loading-image').hide();
        toastr['error'](errObj.message, 'error');
      });
    } else {
      if (per_folder_name == '')
        $('#per_folder_name').addClass("alert alert-danger");
      if (per_user_name == '')
        $('#select2-per_user_name-container').addClass("alert alert-danger");
      setTimeout(function() {
        $('#per_folder_name').removeClass("alert alert-danger");
        $('#select2-per_user_name-container').removeClass("alert alert-danger");
      }, 1000);
      toastr['error']("Please Select Required fileds", 'error');
    }
  });

  $(document).on('click', '.expand-row-msg', function() {
    $('#postmanShowFullTextModel').modal('toggle');
    $(".postmanShowFullTextBody").html("");
    var id = $(this).data('id');
    var name = $(this).data('name');
    var full = '.expand-row-msg .show-full-' + name + '-' + id;
    var fullText = $(full).html();
    $(".postmanShowFullTextBody").html(fullText == 'None' ? '' : fullText);


    $(".postManShowEdit").hide();
    $('.postManEditDiv').hide();
    if(name == 'paramiters') {
      $(".postManShowEdit").show();
      $("#postManFieldId").val(id);
    }
  });

  $(document).on('click','#postManFieldEditBtn',function() {
    $('.postManViewDiv').hide();
    $(".postManShowEdit").hide();
    $('.postManEditDiv').show();
    $("#postManFieldEdit").val($(".postmanShowFullTextBody").html());
  });

  $(document).on('click','#postManFieldSaveBtn',function(e) {
    e.preventDefault();
    let val = $('#postManFieldEdit').val();
    // if(val != '') {
      $("#loading-image").show();
      $.ajax({
      url: "{{route('postman.updateField')}}",
      type: "post",
      data: $('#updatePostManField').serialize()
    }).done(function(response) {
        $('#loading-image').hide();
        $('#postmanShowFullTextModel').modal('hide');
        $('.expand-row-msg .show-short-paramiters-' + $("#postManFieldId").val()).html(val == ''? 'None' : val.substring(0,5) + '...');
        $('.expand-row-msg .show-full-paramiters-' + $("#postManFieldId").val()).html(val == ''? 'None' : val);
        toastr['success']('Updated successfully!!!', 'success');
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });

    // }else{
    //   toastr['error']("Please Enter Value", 'error');
    // }

  });

  /*$(document).on('click', '.expand-row-msg', function() {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-' + name + '-' + id;
    var mini = '.expand-row-msg .show-full-' + name + '-' + id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  }); */
  $(document).ready(function() {
    $(".select2").select2();
    $('#per_user_name').select2();
    $('#per_folder_name ').select2();
    $(".folder-name-filter").select2();
    $(".request-name-filter").select2();
    $(".request-types-filter").select2();
  });

  $(document).on("click", ".status-save-btn", function(e) {
    e.preventDefault();
    var $this = $(this);
    $.ajax({
      url: "{{route('postman.status.create')}}",
      type: "post",
      data: $('#status-create-form').serialize()
    }).done(function(response) {
      if (response.code = '200') {
        $('#loading-image').hide();
        $('#addPostman').modal('hide');
        toastr['success']('Status  Created successfully!!!', 'success');
        location.reload();

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });


  $(document).ready(function() {
    $(document).on('change', '.status-dropdown', function(e) {
      e.preventDefault();
      var postId = $(this).data('id');
      var selectedStatus = $(this).val();
      console.log("Dropdown data-id:", postId);
      console.log("Selected status:", selectedStatus);


      // Make an AJAX request to update the status
      $.ajax({
        url: '/postman/update-status',
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          postId: postId,
          selectedStatus: selectedStatus
        },
        success: function(response) {
          toastr['success']('Status  Created successfully!!!', 'success');
          console.log(response);
        },
        error: function(xhr, status, error) {
          // Handle the error here
          console.error(error);
        }
      });
    });

    $(document).on('change', '.api-issue-fix-done-dropdown', function(e) {
      e.preventDefault();
      var postId = $(this).data('id');
      var selectedValue = $(this).val();

      // Make an AJAX request to update the status
      $.ajax({
        url: '/postman/update-api-issue-fix-done',
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          postId: postId,
          selectedValue: selectedValue
        },
        success: function(response) {
          toastr['success'](response.message, 'success');
        },
        error: function(xhr, status, error) {
          // Handle the error here
          console.error(error);
        }
      });
    });
  });

    // Load settings value Histories
    $(document).on('click', '.status-history-show', function() {
            var id = $(this).attr('data-id');
                $.ajax({
                    method: "GET",
                    url: `{{ route('postman.status.histories', [""]) }}/` + id,
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            var html = "";
                            $.each(response.data, function(k, v) {
                                html += `<tr>
                                            <td> ${k + 1} </td>
                                            <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                            <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                            <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                            <td> ${v.created_at} </td>
                                        </tr>`;
                            });
                            $("#postman-status-histories-list").find(".postman-status-histories-list-view").html(html);
                            $("#postman-status-histories-list").modal("show");
                        } else {
                            toastr["error"](response.error, "Message");
                        }
                    }
                });
      });

      // Load settings value Histories
    $(document).on('click', '.api-issue-fix-done-history-show', function() {
      var id = $(this).attr('data-id');
      $.ajax({
          method: "GET",
          url: `{{ route('postman.api-issue-fix-done.histories', [""]) }}/` + id,
          dataType: "json",
          success: function(response) {
              if (response.status) {
                  var html = "";
                  $.each(response.data, function(k, v) {
                      html += `<tr>
                                  <td> ${k + 1} </td>
                                  <td> ${v.old_value_text} </td>
                                  <td> ${v.new_value_text} </td>
                                  <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                  <td> ${v.created_at} </td>
                              </tr>`;
                  });
                  $("#postman-api-issue-fix-done-histories-list").find(".postman-api-issue-fix-done-histories-list-view").html(html);
                  $("#postman-api-issue-fix-done-histories-list").modal("show");
              } else {
                  toastr["error"](response.error, "Message");
              }
          }
      });
    });

    $(document).on('click', '.create-quick-task', function() {
        var $this = $(this);
        site = $(this).data("id");
        title = $(this).data("title");
        cat_title = $(this).data("category_title");
        development = $(this).data("development");
        if (!title || title == '') {
            toastr["error"]("Please add title first");
            return;
        }
        //debugger;
        /*let val = $("#change_website1").select2("val");
        $.ajax({
            url: '/task/get/websitelist',
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: val,
                cat_title:cat_title
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            //$this.siblings('input').val("");
            $('.website-list').html(response.data);
            //toastr["success"]("Remarks fetched successfully");
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
        });*/

        $("#create-quick-task").modal("show");

        var selValue = $(".save-item-select").val();
        if (selValue != "") {
            $("#create-quick-task").find(".assign-to option[value=" + selValue + "]").attr('selected',
                'selected')
            $('.assign-to.select2').select2({
                width: "100%"
            });
        }

        $("#hidden-task-subject").val(title);
        $(".text-task-development").val(development);
        $('#site_id').val(site);
    });

    $(document).on("click", ".create-task", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        $.ajax({
            url: form.attr("action"),
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: form.serialize(),
            beforeSend: function() {
                $(this).text('Loading...');
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    form[0].reset();
                    toastr['success'](response.message);
                    $("#create-quick-task").modal("hide");
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);
        });
    });

    $(document).on("click", ".count-dev-customer-tasks", function() {

        var $this = $(this);
        // var user_id = $(this).closest("tr").find(".ucfuid").val();
        var site_id = $(this).data("id");
        var category_id = $(this).data("category");
        $("#site-development-category-id").val(category_id);
        $.ajax({
            type: 'get',
            url: 'postman/countdevtask/' + site_id,
            dataType: "json",
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(data) {
                $("#dev_task_statistics").modal("show");
                var table = `<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th width="4%">Tsk Typ</th>
                            <th width="4%">Tsk Id</th>
                            <th width="7%">Asg to</th>
                            <th width="12%">Desc</th>
                            <th width="12%">Sts</th>
                            <th width="33%">Communicate</th>
                            <th width="10%">Action</th>
                        </tr>`;
                for (var i = 0; i < data.taskStatistics.length; i++) {
                    var str = data.taskStatistics[i].subject;
                    var res = str.substr(0, 100);
                    var status = data.taskStatistics[i].status;
                    if (typeof status == 'undefined' || typeof status == '' || typeof status ==
                        '0') {
                        status = 'In progress'
                    };
                    table = table + '<tr><td>' + data.taskStatistics[i].task_type + '</td><td>#' +
                        data.taskStatistics[i].id +
                        '</td><td class="expand-row-msg" data-name="asgTo" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-asgTo-' + data
                        .taskStatistics[i].id + '">' + data.taskStatistics[i].assigned_to_name
                        .replace(/(.{6})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-asgTo-' + data
                        .taskStatistics[i].id + ' hidden">' + data.taskStatistics[i]
                        .assigned_to_name +
                        '</span></td><td class="expand-row-msg" data-name="res" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-res-' + data
                        .taskStatistics[i].id + '">' + res.replace(/(.{7})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-res-' + data
                        .taskStatistics[i].id + ' hidden">' + res + '</span></td><td>' + status +
                        '</td><td><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message" title="Send message" data-taskid="' +
                        data.taskStatistics[i].id +
                        '"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id +
                        '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
                    table = table + '<a href="javascript:void(0);" data-task-type="' + data
                        .taskStatistics[i].task_type + '" data-id="' + data.taskStatistics[i].id +
                        '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
                    table = table +
                        '<button type="button" class="btn btn-xs  preview-img pd-5" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id + '" data-dismiss="modal"><i class="fa fa-list"></i></button></td>';
                    table = table + '</tr>';
                }
                table = table + '</table></div>';
                $("#loading-image").hide();
                $(".modal").css("overflow-x", "hidden");
                $(".modal").css("overflow-y", "auto");
                $("#dev_task_statistics_content").html(table);
            },
            error: function(error) {
                console.log(error);
                $("#loading-image").hide();
            }
        });
    

    });

    $(document).on('click', '.send-message', function() {
        var thiss = $(this);
        var data = new FormData();
        var task_id = $(this).data('taskid');
        var message = $(this).closest('tr').find('.quick-message-field').val();
        var mesArr = $(this).closest('tr').find('.quick-message-field');
        $.each(mesArr, function(index, value) {
            if ($(value).val()) {
                message = $(value).val();
            }
        });

        data.append("task_id", task_id);
        data.append("message", message);
        data.append("status", 1);

        if (message.length > 0) {
            if (!$(thiss).is(':disabled')) {
                $.ajax({
                    url: '/whatsapp/sendMessage/task',
                    type: 'POST',
                    "dataType": 'json', // what to expect back from the PHP script, if anything
                    "cache": false,
                    "contentType": false,
                    "processData": false,
                    "data": data,
                    beforeSend: function() {
                        $(thiss).attr('disabled', true);
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                    $("#loading-image").hide();
                    thiss.closest('tr').find('.quick-message-field').val('');

                    toastr["success"]("Message successfully send!", "Message")
                    // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
                    //   .done(function( data ) {
                    //
                    //   }).fail(function(response) {
                    //     console.log(response);
                    //     alert(response.responseJSON.message);
                    //   });

                    $(thiss).attr('disabled', false);
                }).fail(function(errObj) {
                    $(thiss).attr('disabled', false);

                    alert("Could not send message");
                    console.log(errObj);
                });
            }
        } else {
            alert('Please enter a message first');
        }
    });

    $(document).on("click", ".delete-dev-task-btn", function() {
        var x = window.confirm("Are you sure you want to delete this ?");
        if (!x) {
            return;
        }
        var $this = $(this);
        var taskId = $this.data("id");
        var tasktype = $this.data("task-type");
        if (taskId > 0) {
            $.ajax({
                beforeSend: function() {
                    $("#loading-image").show();
                },
                type: 'get',
                url: "/site-development/deletedevtask",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: taskId,
                    tasktype: tasktype
                },
                dataType: "json"
            }).done(function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    $this.closest("tr").remove();
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                alert('Could not update!!');
            });
        }

    });

    $(document).on('click', '.expand-row-msg', function() {
        var name = $(this).data('name');
        var id = $(this).data('id');
        console.log(name);
        var full = '.expand-row-msg .show-short-' + name + '-' + id;
        var mini = '.expand-row-msg .show-full-' + name + '-' + id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });

    $(document).on('click', '.preview-img', function(e) {
        e.preventDefault();
        id = $(this).data('id');
        if (!id) {
            alert("No data found");
            return;
        }
        $.ajax({
            url: "/task/preview-img-task/" + id,
            type: 'GET',
            success: function(response) {
                $("#preview-task-image").modal("show");
                $(".task-image-list-view").html(response);
                initialize_select2()
            },
            error: function() {}
        });
    });

    $(document).on("click", ".send-to-sop-page", function() {
        var id = $(this).data("id");
        var task_id = $(this).data("media-id");

        $.ajax({
            url: '/task/send-sop',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                id: id,
                task_id: task_id
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.success) {
                    toastr["success"](response.message);
                } else {
                    toastr["error"](response.message);
                }

            },
            error: function(error) {
                toastr["error"];
            }

        });
    });

    $(document).on('click', '.previewDoc', function() {
        $('#previewDocSource').attr('src', '');
        var docUrl = $(this).data('docurl');
        var type = $(this).data('type');
        var type = jQuery.trim(type);
        if (type == "image") {
            $('#previewDocSource').attr('src', docUrl);
        } else {
            $('#previewDocSource').attr('src', "https://docs.google.com/gview?url=" + docUrl +
                "&embedded=true");
        }
        $('#previewDoc').modal('show');
    });

    $(document).on("click", ".btn-show-request-url", function () {
        $(".add_more_urls_div").toggle();
    });

    $(document).on('input', '#searchInput', function(e) {
        e.preventDefault();
        const searchInput = $(this).val();
        const urlInputs = $(".urlInput");

        urlInputs.each(function() {
            const urlInput = $(this);
            const url = urlInput.val();
            if (url.includes(searchInput)) {
                urlInput.removeClass("hidden");
            } else {
                urlInput.addClass("hidden");
            }
        });
    });

    $(document).on("click", "#run-request-save-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        $('#loading-image').show();
        $.ajax({
            url: "{{route('postman.runrequesturl')}}",
            type: "post",
            data: $('#postman-run-request-url').serialize()
        }).done(function(response) {
            if (response.code == '200') {
                $('#loading-image').hide();
                $('#runRequestUrl').modal('hide');
                toastr['success']('Postman Request Url run successfully!!!', 'success');
                location.reload();
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function(errObj) {
            $('#loading-image').hide();
            toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on('click',".add-remark",function(){
        var req_id = $(this).attr('data-id');
        $("#remark-create").modal("show");
        $("#remarkId").val(req_id);
    });

    $(document).on('submit','#remark-create-form',function(e){
      e.preventDefault();
      var this_form = $(this);
      var form_data = this_form.serialize();
      var ajax_url = this_form.attr('action');
      $.ajax({
            url: ajax_url,
            type: 'POST',
            data: form_data,
            beforeSend: function() {
                $("#loading-image").show();
            },
            dataType : 'json',
            success: function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    this_form[0].reset();
                    $("#remark-create").modal("hide");
                    toastr['success'](response.message);
                } else {
                    toastr['error']('Error');
                }
            },
            error: function (data) {
              $("#loading-image").hide();
              toastr['error'](response.responseJSON.message);
            }
        });
    });

    $(document).on('click','.responses-history',function(){
      //postman.responsesHistory
      let postman_id = $(this).attr('data-id');
      let res_ur = "{{route('postman.responsesHistory',':id')}}";
      let ajax_url = res_ur.replace(":id",postman_id);
      $.ajax({
            url: ajax_url,
            type: 'GET',
            beforeSend: function() {
              $(".tbodayPostmanResponsesHistory").html('');
              $("#loading-image").show();

            },
            dataType : 'json',
            success: function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                  let res_data = response.data;
                  var content = '';
                  for (var i = 0; i < res_data.length; i++) {
                    content += '<tr id="res_data_' + res_data[i].id + '">';
                      content += '<td>' + res_data[i].id + '</td>';
                      content += '<td>' + res_data[i].request_url + '</td>';
                      content += '<td>' + res_data[i].request_data + '</td>';
                      content += '<td>' + res_data[i].response + '</td>';
                      content += '<td>' + res_data[i].response_code + '</td>';
                      content += '<td>' + res_data[i].userName + '</td>';
                      content += '<td>' + res_data[i].created_at + '</td>';
                    content += '</tr>';
                    
                  }

                  $(".tbodayPostmanResponsesHistory").html(content);

                  $("#responses-history-modal").modal("show");
                  
                } else {
                    toastr['error']('Error');
                }
            },
            error: function (data) {
              $("#loading-image").hide();
              toastr['error'](response.responseJSON.message);
            }
        });
    })

    $(document).on('click','.page-link',function(event){
      event.preventDefault();
      
      let page_url_split = $(this).attr('href').split('?page=');
      let page_url = '/postman?page=' + page_url_split[1];
      
      refreshPagination(page_url);

    });

    function refreshPagination(page_url) {
      $.ajax({
        url: page_url,
        dataType: "json",
        beforeSend: function () {
          $("#loading-image").show();
        },
        }).done(function (data) {
          $("#loading-image").hide();

          $('#postman-table tbody').html(data.tbody);
          $('#pagination').html(data.pagination);
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
          $("#loading-image").hide();
          alert('No response from server');
        });
    }

    function setFormErrors(errors) {
      $.each(errors, function(key, value) {
            var newSpan = $('<span>').addClass('custom-error-message postman-form-' + key).html(value[0]);
            if($('[name="' + key + '"]').length > 0){
              $('[name="' + key + '"]').parent().append(newSpan);
            }else{
              $('[name="' + key + '[]' + '"]').parent().append(newSpan);
            }
      });
    }

    function clearAllFormErrors() {
      $('.custom-error-message').hide();
    }

</script>
@endsection