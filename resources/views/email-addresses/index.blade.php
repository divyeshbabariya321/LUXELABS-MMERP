@extends('layouts.app')

@section('title', 'Email Addresses List')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Email Addresses List ({{$emailAddress->total()}})</h2>
        </div>
    </div>

    @include('partials.flash_messages')
    <div class='ajax-errors col'></div>

    <div class="row mb-3">
        <div class="col-xs-12 pl-5">
            <form class="form-search-data">
                <div class="row">
                    <div class="col-2 pd-2">
                        <div class="form-group cls_task_subject mb-0">
                            <input type="text" name="keyword" placeholder="Search Keyword" class="form-control" value="{{ request('keyword') }}">
                        </div>
                    </div>
                    <div class="col-2 pd-2">
                      <div class="form-group username mb-0">
                      <input type="text" name="username" placeholder="Search User Name" class="form-control" value="{{ request('username') }}" list="username-lists">
                        <datalist id="username-lists">
                          @foreach($userEmails as $emailAdd)
                            <option value="{{$emailAdd}}">
                          @endforeach
                        </datalist>
                      </div>
                    </div>
                    <div class="col-2 pd-2">
                      <div class="form-group status mb-0">
                          <select name="status" class="form-control">
                              <option value="">--Select Status--</option>
                              <option value="0" @if(request('status') == '0') selected @endif>Error</option>
                              <option value="1" @if(request('status') == 1) selected @endif>Success</option>
                          </select>
                      </div>
                  </div>
                    <div class="col-2 pd-2">
                    <div class="form-group status mb-0">
                      <Select name="website_id" class="form-control">
                        <option value>-- Select Website --</option>
                        @foreach ($allStores as $key => $val)
                          <option value="{{ $key }}" @if(request('website_id') == $key) selected @endif>{{ $val }}</option>
                        @endforeach
                      </Select>
                    </div>
                    </div>
                    <div class="col-1 pd-2">
                    <div class="form-group status mb-0">
                      <button type="submit" class="btn btn-xs mt-1 ml-3">
                        <i class="fa fa-filter"></i>
                      </button>
                      <a href="/email-addresses" class="btn btn-image mt-auto" id=""><img src="{{asset('/images/resend2.png')}}" style="cursor: nwse-resize;"></a>
                    </div>
                    </div>
                    <!-- Language Selection -->
                    <div class="col-3 d-flex" style=" justify-content: end !important;padding-right: 22px;">
                        <div class="form-group mb-0">
                          <a href="{{route('email-addresses.run-histories-listing')}}" class="btn btn-xs btn-secondary" target="_blank">View Email Run Job </a>
                  			   <a href="{{route('email-addresses.run-histories-truncate')}}" class="btn btn-xs btn-secondary" onclick="return confirm('{{ __('Are you sure you want to truncate the datas? Note : It will remove all the datas') }}')">Truncate Data ({{$runHistoriesCount}})</a>
                            <button type="button" class="btn btn-xs btn-secondary error-email-history ml-3 ">View Errors</button>
                            <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#emailAddressModal">
                              <i class="fa fa-plus" style=" width:25px"></i>
                            </button>
                          </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Email address Passwords Manager</h2>
            <div class="pull-left ml-3">
                {{ html()->form('POST', route('email.password.change'))->class('password-generate-form')->open() }}
                    <input type="hidden" name="users" id="userIds">
                    <button type="submit" class="btn btn-xs btn-secondary"> Generate password </button>
                {{ html()->form()->close() }}
            </div>
            <div class="pull-left pl-1">
                <button type="button" class="btn btn-xs btn-secondary" style="width: 25px" onclick="showPasswordCreateModal()"><i class="fa fa-plus"></i></button>
            </div>
            <div class="pull-left pl-1">
              <button class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#store-api-token">Email Update</button>
            </div>
        </div>
    </div>

    <div class="mt-3 col-md-12 mb-5">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th width="2%">ID</th>
            <th width="9%">Username</th>
            <th width="9%">Password</th>
            <th width="7%">Rec Phone</th>
            <th width="7%">Rec Email</th>
            <th width="5%">Driver</th>
            <th width="8%">Host</th>
            <th width="5%">Port</th>
            <th width="10%">Send Grid Token</th>
            <th width="10%">Encryp</th>
            <th width="10%">Str Website</th>
            <th width="5%">Status</th>
            <th width="5%">Error Description</th>
            <th width="5%">Action</th>
          </tr>
        </thead>

        <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
          @foreach ($emailAddress as $server)
          @php $bgColor = ""; @endphp
          @if ($server->history_last_message_error != "") 
            @php $bgColor = "#f4aeae!important";  @endphp
          @endif
            <tr style="background-color:{{$bgColor}}">
               <td>
				        <input type="checkbox" class="checkbox_ch" id="u{{ $server->id }}" name="userIds[]" value="{{ $server->id }}">
              </td>
              <td class="expand-row-msg" data-name="username" data-id="{{$server->id}}">
                  <span class="show-short-username-{{$server->id}}">{{ Str::limit($server->username, 12, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-username-{{$server->id}} hidden">{{$server->username}}</span>
                <button type="button"  class="btn btn-copy-username btn-sm float-right" data-id="{{$server->username}}">
                  <i class="fa fa-clone" aria-hidden="true"></i>
                </button>
              </td>
              <td class="expand-row-msg" data-name="password" data-id="{{$server->id}}">
                  <span class="show-short-password-{{$server->id}}">{{ Str::limit($server->password, 10, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-password-{{$server->id}} hidden">{{$server->password}}</span>
                <button type="button"  class="btn btn-copy-password btn-sm float-right" data-id="{{$server->password}}">
                  <i class="fa fa-clone" aria-hidden="true"></i>
                </button>
              </td>
              <td>
                  {{ $server->recovery_phone }}
              </td>
              <td>
                  {{ $server->recovery_email }}
              </td>
              <td>
                  {{ $server->driver }}
              </td>
              <td class="expand-row-msg" data-name="host" data-id="{{$server->id}}">
                  <span class="show-short-host-{{$server->id}}">{{ Str::limit($server->host, 10, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-host-{{$server->id}} hidden">{{$server->host}}</span>
              </td>
              <td>
                  {{ $server->port }}
              </td>
              <td>
                  {{ $server->send_grid_token??'N/A' }}
                <button type="button"  class="btn btn-copy-token btn-sm float-right" data-id="{{$server->send_grid_token}}">
                  <i class="fa fa-clone" aria-hidden="true"></i>
                </button>
              </td>
              <td>
                  {{ $server->encryption }}
              </td>
              <td>
                  @if($server->website){{ $server->website->title }} @endif
              </td>
              <td>
                @if($server->history_last_message->is_success ?? '' == 1) {{ 'Success' }} @elseif(isset($server->history_last_message->is_success)) {{'Error'}} @else {{'-'}} @endif
              </td>
              <td>
                {{( isset($server->history_last_message) && isset($server->history_last_message->message) ) ? $server->history_last_message->message : '-'}}
             </td>
              <td>
                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="EmailAddressesbtn('{{$server->id}}')"><i class="fa fa-arrow-down"></i></button>
              </td>
            </tr>
            <tr class="action-emailaddressesbtn-tr-{{$server->id}} d-none">
              <td class="font-weight-bold">Action</td>
              <td colspan="12">
                <button type="button" class="btn btn-xs assign-users p-0 m-0 text-secondary mr-2"  title="Assign users"  data-toggle="modal" data-target="#assignUsersModal{{$server->id}}" data-email-id="{{ $server->id }}" data-users="{{json_encode($server->email_assignes)}}">
                  <i class="fa fa-plus"></i>
                </button>
                <button type="button" class="btn btn-xs edit-email-addresses p-0 m-0 text-secondary mr-2"  data-toggle="modal" data-target="#emailAddressEditModal" data-email-addresses="{{ json_encode($server) }}">
                  <i class="fa fa-edit"></i>
                </button>
                <button type="button" class="btn btn-xs view-email-history p-0 m-0 text-secondary mr-2" data-id="{{ $server->id }}">
                  <i class="fa fa-eye"></i>
                </button>
                {{ html()->form('DELETE', route('email-addresses.destroy', [$server->id]))->style('display:inline')->open() }}
                <button type="submit" class="btn btn-xs p-0 m-0 text-secondary mt-0 mr-2">
                  <i class="fa fa-trash"></i>
                </button>
                {{ html()->form()->close() }}
                <a href="javascript:;" data-id="{{ $server->from_address }}" class="show-related-accounts text-secondary mr-2" title="Show Account">
                  <i class="fa fa-eye"></i>
                </a>
                <a href="javascript:;" onclick="sendtoWhatsapp({{ $server->id }})" title="Send to Whatsapp" class="btn btn-xs p-0 m-0 text-secondary mr-2">
                  <i class="fa fa-send-o"></i>
                </a>
                <a href="javascript:;" data-id="{{$server->id}}"  title="run cron" class="btn btn-xs p-0 m-0 text-secondary mr-2 single-email-run-cron">
                  <i class="fa fa-send-o"></i>
                </a>
                <div id="sendToWhatsapp{{$server->id}}" class="modal fade" role="dialog">
                  <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title">Send to Whatsapp</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <form action="{{ route('email.password.sendwhatsapp') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                          <div class="form-group">
                            <select class="form-control" name="user_id">
                              {!!$uops!!}
                            </select>
                            <input type="hidden" name="id" value="{{ $server->id }}"/>
                            <input type="hidden" name="send_message" value="1">
                            <input type="hidden" name="send_on_whatsapp" value="1">
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

                <button style="padding:3px;" title="Email Auto Acknowledgement" type="button" class="btn btn-xs p-0 m-0 text-secondary mr-2 create-email-auto-acknowledgement" data-id="{{ $server->id }}"><i class="fa fa-envelope" aria-hidden="true"></i></button>

                <button style="padding-left: 0;padding-left:3px;" type="button" class="btn btn-xs p-0 m-0 text-secondary mr-2  count-email-acknowledgement" title="Email Auto Acknowledgement history" data-id="{{ $server->id }}"><i class="fa fa-info-circle"></i></button>
                @if(auth()->user()->isAdmin())
                <label class="switchAN" title="Email Alert">
                    <input data-id={{$server->id}} class ="emailAlertSwitch" type="checkbox"  @if($server->email_alert) {{'checked'}} @endif>
                    <span class="slider round"></span>
                    <span class="text @if($server->email_alert) {{'textLeft'}} @else {{'textRight'}} @endif" >@if($server->email_alert) {{'On'}} @else {{'Off'}} @endif</span>
                </label>
                @endif
              </td>
            </tr>

          @endforeach
        </tbody>
      </table>
      {{$emailAddress->links()}}
    </div>
    <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />

    @foreach ($emailAddress as $server)
    @php
      $assignids = [];
      $assigned_users = json_decode($server->email_assignes);
      foreach($assigned_users as $_assigned){
        $assignids[] = $_assigned->user_id;
      }
    @endphp
    <div id="assignUsersModal{{$server->id}}" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <form action="" method="POST" enctype="multipart/form-data" >
            @csrf
            @method('POST')
            <input type="hidden" name="email_id" id="email_id" value=''>
            <div class="modal-header">
              <h4 class="modal-title">Assign users</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

          <div class="form-group"> 
                  <strong>Users:</strong>
            <Select name="users[]" id="users" multiple class="form-control select-multiple globalSelect2">
              <option value = ''>None</option>
              @foreach ($users as $key => $val)
                <option value="{{ $val['id'] }}" {{in_array($val['id'],$assignids)?'selected':''}}>{{ $val['name'] }}</option>
              @endforeach
            </Select>
            @if ($errors->has('users'))
                <div class="alert alert-danger">{{$errors->first('users')}}</div>
            @endif
          </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Assign</button>
            </div>
          </form>
        </div>

      </div>
</div>
@endforeach
<div id="emailAddressModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('email-addresses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title">Store a Email Address</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-4">
              <strong>From Name<span class="text-danger">*</span>:</strong>
              <input type="text" name="from_name" class="form-control" value="{{ old('from_name') }}" required>

              @if ($errors->has('from_name'))
                <div class="alert alert-danger">{{$errors->first('from_name')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>From Address<span class="text-danger">*</span>:</strong>
              <input list="fromaddress-lists" type="text" name="from_address" class="form-control" value="{{ old('from_address') }}" required>
              <datalist id="fromaddress-lists">
                @foreach($fromAddresses as $fromAddress)
                  <option value="{{$fromAddress}}">
                @endforeach
              </datalist>

              @if ($errors->has('from_address'))
                <div class="alert alert-danger">{{$errors->first('from_address')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Recovery Phone:</strong>
              <input type="text" name="recovery_phone" class="form-control" value="{{ old('recovery_phone') }}">

              @if ($errors->has('recovery_phone'))
                <div class="alert alert-danger">{{$errors->first('recovery_phone')}}</div>
              @endif
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-4">
              <strong>Recovery Email:</strong>
              <input type="text" name="recovery_email" class="form-control" value="{{ old('recovery_email') }}">

              @if ($errors->has('recovery_email'))
                <div class="alert alert-danger">{{$errors->first('recovery_email')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
                    <strong>Store Website:</strong>
              <Select name="store_website_id" class="form-control">
                <option value>None</option>
                @foreach ($allStores as $key => $val)
                  <option value="{{ $key }}">{{ $val }}</option>
                @endforeach
              </Select>
              @if ($errors->has('store_website_id'))
                  <div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Driver<span class="text-danger">*</span>:</strong>
              <input type="text" name="driver" class="form-control" value="{{ old('driver', $defaultDriver) }}" required>

              @if ($errors->has('driver'))
                <div class="alert alert-danger">{{$errors->first('driver')}}</div>
              @endif
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-4">
              <strong>Incoming Driver<span class="text-danger">*</span>:</strong>
              <input type="text" name="incoming_driver" class="form-control" value="{{ old('incoming_driver', $defaultDriver) }}" required>

              @if ($errors->has('incoming_driver'))
                <div class="alert alert-danger">{{$errors->first('incoming_driver')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Outgoing Driver<span class="text-danger">*</span>:</strong>
              <input type="text" name="driver" class="form-control" value="{{ old('driver', $defaultDriver) }}" required>

              @if ($errors->has('driver'))
                <div class="alert alert-danger">{{$errors->first('driver')}}</div>
              @endif
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-4">
              <strong>Host<span class="text-danger">*</span>:</strong>
              <input type="text" name="host" class="form-control" value="{{ old('host', $defaultHost) }}" required>

              @if ($errors->has('host'))
                <div class="alert alert-danger">{{$errors->first('host')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Port<span class="text-danger">*</span>:</strong>
              <input type="text" name="port" class="form-control" value="{{ old('port', $defaultPort) }}" required>

              @if ($errors->has('port'))
                <div class="alert alert-danger">{{$errors->first('port')}}</div>
              @endif
            </div>

            <div class="form-group col-md-4">
              <strong>Encryption<span class="text-danger">*</span>:</strong>
              <input type="text" name="encryption" class="form-control" value="{{ old('encryption', $defaultEncryption) }}" required>

              @if ($errors->has('encryption'))
                <div class="alert alert-danger">{{$errors->first('encryption')}}</div>
              @endif
            </div>
          </div>
          <div class="row">
          <div class="form-group col-md-4">
              <strong>Send Grid Token:</strong>
              <input type="text" name="send_grid_token" class="form-control" value="{{ old('send_grid_token') }}">

              @if ($errors->has('send_grid_token'))
                <div class="alert alert-danger">{{$errors->first('send_grid_token')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Username<span class="text-danger">*</span>:</strong>
              <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>

              @if ($errors->has('username'))
                <div class="alert alert-danger">{{$errors->first('username')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Password<span class="text-danger">*</span>:</strong>
              <input type="text" name="password" class="form-control" value="{{ old('password') }}" required>

              @if ($errors->has('password'))
                <div class="alert alert-danger">{{$errors->first('password')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Signature Name:</strong>
              <input type="text" name="signature_name" class="form-control" value="{{ old('signature_name') }}" >

              @if ($errors->has('signature_name'))
                <div class="alert alert-danger">{{$errors->first('signature_name')}}</div>
              @endif
            </div>

            <div class="form-group col-md-4">
              <strong>Signature Title:</strong>
              <input type="text" name="signature_title" class="form-control" value="{{ old('signature_title') }}" >

              @if ($errors->has('signature_title'))
                <div class="alert alert-danger">{{$errors->first('signature_title')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Signature Phone:</strong>
              <input type="text" name="signature_phone" class="form-control" value="{{ old('signature_phone') }}" >

              @if ($errors->has('signature_phone'))
                <div class="alert alert-danger">{{$errors->first('signature_title')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Signature Email:</strong>
              <input type="text" name="signature_email" class="form-control" value="{{ old('signature_email') }}" >

              @if ($errors->has('signature_email'))
                <div class="alert alert-danger">{{$errors->first('signature_email')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Signature Website:</strong>
              <input type="text" name="signature_website" class="form-control" value="{{ old('signature_website') }}" >

              @if ($errors->has('signature_website'))
                <div class="alert alert-danger">{{$errors->first('signature_website')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Twilio Recovery Code:</strong>
              <input type="text" name="twilio_recovery_code" class="form-control" value="{{ old('twilio_recovery_code') }}">

              @if ($errors->has('twilio_recovery_code'))
                <div class="alert alert-danger">{{$errors->first('twilio_recovery_code')}}</div>
              @endif
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <strong>Signature Address:</strong>
              <textarea name="signature_address" id="address" class="form-control" >{{ old('signature_address') }}</textarea>

              @if ($errors->has('signature_address'))
                <div class="alert alert-danger">{{$errors->first('signature_address')}}</div>
              @endif
            </div>
            <div class="form-group col-md-6">
              <strong>Signature Social Tag:</strong>

              <textarea id="social" name="signature_social" class="form-control" >{{ old('signature_social') }}</textarea>
              @if ($errors->has('signature_tag'))
                <div class="alert alert-danger">{{$errors->first('signature_tag')}}</div>
              @endif
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <strong>Signature Logo:</strong>
              <input type="file" name="signature_logo" class="form-control" value="{{ old('signature_logo') }}" >

              @if ($errors->has('signature_logo'))
                <div class="alert alert-danger">{{$errors->first('signature_logo')}}</div>
              @endif
            </div>
            <div class="form-group col-md-6">
              <strong>Signature Image:</strong>
              <input type="file" name="signature_image" class="form-control" value="{{ old('signature_image') }}" >

              @if ($errors->has('signature_image'))
                <div class="alert alert-danger">{{$errors->first('signature_image')}}</div>
              @endif
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit"  class="btn btn-secondary">Add</button>
        </div>
      </form>
    </div>

  </div>
</div>

<div id="EmailRunHistoryModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email Run History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
      <div class="modal-body">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Id</th>
                <th>From Name</th>
                <th>Status</th>
                <th>Message</th>
                <th>Created</th>
              </tr>
            </thead>

            <tbody>

            </tbody>
          </table>
        </div>
      </div>
			</div>
		</div>
	</div>

</div><div id="ErrorHistoryModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">

        <h4 class="modal-title">Email Error History</h4>

        <a class="btn btn-secondary ml-3" href="{{ route('email.failed.download') }}">Download</a>

        <button type="button" class="close" data-dismiss="modal">&times;</button>

      </div>
      <div class="modal-body">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Id</th>
                <th>From Name</th>
                <th>Status</th>
                <th>Message</th>
                <th>Created</th>
              </tr>
            </thead>

            <tbody>

            </tbody>
          </table>
        </div>
      </div>
      </div>
    </div>
  </div>
</div>



<div id="emailAddressEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST" enctype="multipart/form-data" >
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h4 class="modal-title">Update a Email Address</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-4">
              <strong>From Name<span class="text-danger">*</span>:</strong>
              <input type="text" name="from_name" class="form-control" value="{{ old('from_name') }}" required>

              @if ($errors->has('from_name'))
                <div class="alert alert-danger">{{$errors->first('from_name')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>From Address<span class="text-danger">*</span>:</strong>
              <input type="text" name="from_address" class="form-control" value="{{ old('from_address') }}" required>

              @if ($errors->has('from_address'))
                <div class="alert alert-danger">{{$errors->first('from_address')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Recovery Phone:</strong>
              <input type="text" name="recovery_phone" class="form-control" value="{{ old('recovery_phone') }}">

              @if ($errors->has('recovery_phone'))
                <div class="alert alert-danger">{{$errors->first('recovery_phone')}}</div>
              @endif
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-4">
              <strong>Recovery Email:</strong>
              <input type="text" name="recovery_email" class="form-control" value="{{ old('recovery_email') }}">

              @if ($errors->has('recovery_email'))
                <div class="alert alert-danger">{{$errors->first('recovery_email')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
                    <strong>Store Website:</strong>
              <Select name="store_website_id" id="edit_store_website_id" class="form-control">
                <option value = ''>None</option>
                @foreach ($allStores as $key => $val)
                  <option value="{{ $key }}">{{ $val }}</option>
                @endforeach
              </Select>
              @if ($errors->has('store_website_id'))
                  <div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
              @endif
            </div>
            
          </div>
          <div class="row">
            <div class="form-group col-md-4">
              <strong>Incoming Driver<span class="text-danger">*</span>:</strong>
              <Select name="incoming_driver" id="edit_incoming_driver" class="form-control required">
                <option value = ''>Select Driver</option>
                @foreach ($allIncomingDriver as $driver)
                  <option value="{{ $driver }}">{{ $driver }}</option>
                @endforeach
              </Select>
              @if ($errors->has('incoming_driver'))
                <div class="alert alert-danger">{{$errors->first('incoming_driver')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Outgoing Driver<span class="text-danger">*</span>:</strong>
              <Select name="driver" id="edit_driver" class="form-control required">
                <option value = ''>Select Driver</option>
                @foreach ($allDriver as $driver)
                  <option value="{{ $driver }}">{{ $driver }}</option>
                @endforeach
              </Select>
              @if ($errors->has('driver'))
                <div class="alert alert-danger">{{$errors->first('driver')}}</div>
              @endif
            </div>
          </div>
          <div class="row">
            
            <div class="form-group col-md-4">
              <strong>Host<span class="text-danger">*</span>:</strong>
              <input type="text" name="host" class="form-control" value="{{ old('host') }}" required>

              @if ($errors->has('host'))
                <div class="alert alert-danger">{{$errors->first('host')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Port<span class="text-danger">*</span>:</strong>
              <Select name="port" id="edit_port" class="form-control required">
                <option value = ''>Select Port</option>
                @foreach ($allPort as $port)
                  <option value="{{ $port }}">{{ $port }}</option>
                @endforeach
              </Select>

              @if ($errors->has('port'))
                <div class="alert alert-danger">{{$errors->first('port')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Encryption<span class="text-danger">*</span>:</strong>
              <Select name="encryption" id="edit_encryption" class="form-control required">
                <option value = ''>Select Encryption</option>
                @foreach ($allEncryption as $encryption)
                  <option value="{{ $encryption }}">{{ $encryption }}</option>
                @endforeach
              </Select>

              @if ($errors->has('encryption'))
                <div class="alert alert-danger">{{$errors->first('encryption')}}</div>
              @endif
            </div>
          </div>
          <div class="row">
          <div class="form-group col-md-4">
              <strong>Send Grid Token:</strong>
              <input type="text" name="send_grid_token" class="form-control" value="{{ old('send_grid_token') }}">

              @if ($errors->has('send_grid_token'))
                <div class="alert alert-danger">{{$errors->first('send_grid_token')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Username<span class="text-danger">*</span>:</strong>
              <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>

              @if ($errors->has('username'))
                <div class="alert alert-danger">{{$errors->first('username')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Password<span class="text-danger">*</span>:</strong>
              <input type="text" name="password" class="form-control" value="{{ old('password') }}" required>

              @if ($errors->has('password'))
                <div class="alert alert-danger">{{$errors->first('password')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Signature Name:</strong>
              <input type="text" name="signature_name" class="form-control" value="{{ old('signature_name') }}" >

              @if ($errors->has('signature_name'))
                <div class="alert alert-danger">{{$errors->first('signature_name')}}</div>
              @endif
            </div>

            <div class="form-group col-md-4">
              <strong>Signature Title:</strong>
              <input type="text" name="signature_title" class="form-control" value="{{ old('signature_title') }}" >

              @if ($errors->has('signature_title'))
                <div class="alert alert-danger">{{$errors->first('signature_title')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Signature Phone:</strong>
              <input type="text" name="signature_phone" class="form-control" value="{{ old('signature_phone') }}" >

              @if ($errors->has('signature_phone'))
                <div class="alert alert-danger">{{$errors->first('signature_title')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Signature Email:</strong>
              <input type="text" name="signature_email" class="form-control" value="{{ old('signature_email') }}" >

              @if ($errors->has('signature_email'))
                <div class="alert alert-danger">{{$errors->first('signature_email')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Signature Website:</strong>
              <input type="text" name="signature_website" class="form-control" value="{{ old('signature_website') }}" >

              @if ($errors->has('signature_website'))
                <div class="alert alert-danger">{{$errors->first('signature_website')}}</div>
              @endif
            </div>
            <div class="form-group col-md-4">
              <strong>Twilio Recovery Code:</strong>
              <input type="text" name="twilio_recovery_code" class="form-control" value="{{ old('twilio_recovery_code') }}" >

              @if ($errors->has('twilio_recovery_code'))
                <div class="alert alert-danger">{{$errors->first('twilio_recovery_code')}}</div>
              @endif
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <strong>Signature Address:</strong>
              <textarea name="signature_address" id="address1" class="form-control" >{{ old('signature_address') }}</textarea>

              @if ($errors->has('signature_address'))
                <div class="alert alert-danger">{{$errors->first('signature_address')}}</div>
              @endif
            </div>
            <div class="form-group col-md-6">
              <strong>Signature Social Tag:</strong>

              <textarea id="social1" name="signature_social" class="form-control" >{{ old('signature_social') }}</textarea>
              @if ($errors->has('signature_tag'))
                <div class="alert alert-danger">{{$errors->first('signature_tag')}}</div>
              @endif
            </div>

          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <strong>Signature Logo:</strong>
              <input type="file" name="signature_logo" class="form-control" value="{{ old('signature_logo') }}" >
              <img src="" id="img1" style="width:100px;height:100px">

              @if ($errors->has('signature_logo'))
                <div class="alert alert-danger">{{$errors->first('signature_logo')}}</div>
              @endif
            </div>
            <div class="form-group col-md-6">
              <strong>Signature Image:</strong>
              <input type="file" name="signature_image" class="form-control" value="{{ old('signature_image') }}" >
              <img src="" id="img2" style="width:100px;height:100px">
              @if ($errors->has('signature_image'))
                <div class="alert alert-danger">{{$errors->first('signature_image')}}</div>
              @endif
            </div>
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

<div id="show-content-model-table" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title"></h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">

              </div>
          </div>
      </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
         50% 50% no-repeat;display:none;">
</div>

<div class="modal fade" id="store-api-token" role="dialog">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><b>Store Api Token</b></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-12">
                <div style="display:flex !important; float:right !important;"><input type="text" class="form-control email-address-search" name="search"
                                                                                     placeholder="Search">
                  &nbsp;
                  <button style="display: inline-block;width: 10%"
                          class="btn btn-sm btn-image btn-secondary btn-search-email-address">
                    <img src="{{asset('/images/search.png')}}" style="cursor: default;">
                  </button>
                  &nbsp;
                  <button style="display: inline-block;width: 10%"
                          class="btn btn-sm btn-image btn-secondary btn-refresh-email-address">
                    <img src="{{asset('/images/resend2.png')}}" style="cursor: default;">
                  </button>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                <form class="email-update-form" action="{{route('email.address.update')}}" method="post">
                  @csrf
                  <div class="row">
                    <div class="col-md-12">
                      <div class="table-responsive mt-3">
                        <table class="table table-bordered overlay email-address-table">
                          <thead>
                          <tr>
                            <th>Id</th>
                            <th width="50%">Email</th>
                            <th width="50%">Password</th>
                          </tr>
                          </thead>
                          <tbody>
                          @include('email-addresses.partials.email-address')
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <button type="submit" class="btn btn-secondary submit float-right float-lg-right">Update</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

<div id="create-email-auto-acknowledgement" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('email-addresses.create.acknowledgement') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Email Auto Acknowledgement</h4>
                </div>
                <div class="modal-body">
                    <input class="form-control" type="hidden" name="email_addresses_id" id="email_addresses_id" />

                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Start Date:</strong>
                            <input type="datetime-local" name="start_date" id="start_date" class="form-control start_date" value="" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>End Date:</strong>
                            <input type="datetime-local" name="end_date" id="end_date" class="form-control end_date" value="" required>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Message</label>
                            <textarea id="ack_message" name="ack_message" class="form-control" ></textarea>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Type</label>
                            <select name="ack_status" class="form-control" id="ack_status">
                                <option value="">--Select Status--</option>
                                <option value="1">Auto</option>
                                <option value="2">Manual</option>
                          </select>
                        </div>
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default create-acknowledgement">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="acknowledgement_statistics" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Email Acknowledgement statistics</h2>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" id="acknowledgement_statistics_content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Created Date</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- @include('partials.modals.password-create-modal') --}}
@endsection



@section('scripts')
<script src="https://cdn.tiny.cloud/1/{{config('settings.tiny_mce_api_key')}}/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  function EmailAddressesbtn(id){
    $(".action-emailaddressesbtn-tr-"+id).toggleClass('d-none')
  }
  function checkRunHistoryCount(data_count){
    if(data_count > 50000){
      alert("Too large data in email_run_histories table.")
    }
  }
  checkRunHistoryCount({{$runHistoriesCount}})
</script>
<script>
    tinymce.init({
        selector: '#address',
        menubar: false
    });
    tinymce.init({
        selector: '#social',
        menubar: false
    });
    tinymce.init({
        selector: '#ack_message',
        menubar: false
    });

    $(document).on('click', '.create-email-auto-acknowledgement', function() {
        var $this = $(this);
        id = $(this).data("id");
        $("#create-email-auto-acknowledgement").modal("show");
        $('#email_addresses_id').val(id);
    });

    $(document).on("click", ".create-acknowledgement", function(e) {

        var ack_message = tinymce.get('ack_message').getContent();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var email_addresses_id = $('#email_addresses_id').val();
        var ack_status = $('#ack_status').val();

        e.preventDefault();
        var form = $(this).closest("form");
        $.ajax({
            url: form.attr("action"),
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                ack_message : ack_message,
                start_date : start_date,
                end_date : end_date,
                email_addresses_id : email_addresses_id,
                ack_status : ack_status,
            },
            beforeSend: function() {
                $(this).text('Loading...');
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    form[0].reset();
                    toastr['success'](response.message);
                    $("#create-email-auto-acknowledgement").modal("hide");
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            $("#loading-image").hide();
            toastr['error'](response.responseJSON.message);
        });
    });

    $(document).on("click", ".count-email-acknowledgement", function() {

        var $this = $(this);
        var email_addresses_id = $(this).data("id");

        $.ajax({
            type: 'get',
            url: 'email-addresses/countemailacknowledgement/' + email_addresses_id,
            dataType: "json",
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(data) {
                $("#acknowledgement_statistics").modal("show");
                var table = `<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Message</th>
                            <th>Type</th>
                            <th>Created Date</th>
                        </tr>`;
                for (var i = 0; i < data.EMailAcknowledgement.length; i++) {
                    var ack_status = data.EMailAcknowledgement[i].ack_status;
                    if (typeof ack_status == 1 ) {
                        ack_status = 'Auto'
                    } else {
                        ack_status = 'Manual'
                    };
                    table = table + '<tr><td>' + data.EMailAcknowledgement[i].start_date + '</td><td>' +
                        data.EMailAcknowledgement[i].end_date +
                        '</td>';

                    if(data.EMailAcknowledgement[i].ack_message!=null){
                        table = table + '<td>' + data.EMailAcknowledgement[i].ack_message +'</td>';
                    } else {
                        table = table + '<td>--</td>';
                    }

                    table = table + '<td>' + ack_status +'</td><td>' + data.EMailAcknowledgement[i]
                        .created_at +'</td>';

                    table = table + '</tr>';
                }
                table = table + '</table></div>';
                $("#loading-image").hide();
                $(".modal").css("overflow-x", "hidden");
                $(".modal").css("overflow-y", "auto");
                $("#acknowledgement_statistics_content").html(table);
            },
            error: function(error) {
                console.log(error);
                $("#loading-image").hide();
            }
        });
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

    $(document).on('click','.btn-search-email-address',function(){
      search = $('.email-address-search').val()
      $.ajax({
        url: "{{route('email.address.search')}}",
        dataType: "json",
        type: "GET",
        data: {
          search : search,
        },
        beforeSend: function () {
          $("#loading-image").show();
        },
      }).done(function (data) {
        $("#loading-image").hide();
        $(".email-address-table tbody").empty().html(data.tbody);
      });
    });
    $(document).on('click','.btn-refresh-email-address',function(){
      search = '';
      $.ajax({
        url: "{{route('email.address.search')}}",
        dataType: "json",
        type: "GET",
        data: {
          search : search,
        },
        beforeSend: function () {
          $("#loading-image").show();
        },
      }).done(function (data) {
        $("#loading-image").hide();
        $('.email-address-search').val('');
        $(".email-address-table tbody").empty().html(data.tbody);
      });
    });

    $(document).on('click', '.expand-row-msg', function () {
      var name = $(this).data('name');
      var id = $(this).data('id');
      var full = '.expand-row-msg .show-short-'+name+'-'+id;
      var mini ='.expand-row-msg .show-full-'+name+'-'+id;
      $(full).toggleClass('hidden');
      $(mini).toggleClass('hidden');
    });

    $(document).on('click', '.edit-email-addresses', function() {
      var emailAddress = $(this).data('email-addresses');
      var url = "{{ route('email-addresses.index') }}/" + emailAddress.id;

      $('#emailAddressEditModal form').attr('action', url);
      $('#emailAddressEditModal').find('input[name="from_name"]').val(emailAddress.from_name);
      $('#emailAddressEditModal').find('input[name="from_address"]').val(emailAddress.from_address);

      // $('#emailAddressEditModal').find('input[name="driver"]').val(emailAddress.driver);

      $('#emailAddressEditModal').find('input[name="host"]').val(emailAddress.host);
      $('#emailAddressEditModal').find('input[name="send_grid_token"]').val(emailAddress.send_grid_token);

      // $('#emailAddressEditModal').find('input[name="port"]').val(emailAddress.port);

      // $('#emailAddressEditModal').find('input[name="encryption"]').val(emailAddress.encryption);

      $('#emailAddressEditModal').find('input[name="username"]').val(emailAddress.username);
      $('#emailAddressEditModal').find('input[name="password"]').val(emailAddress.password);

      $('#emailAddressEditModal').find('input[name="recovery_phone"]').val(emailAddress.recovery_phone);
      $('#emailAddressEditModal').find('input[name="recovery_email"]').val(emailAddress.recovery_email);

      $('#emailAddressEditModal').find('input[name="signature_name"]').val(emailAddress.signature_name);
      $('#emailAddressEditModal').find('input[name="signature_title"]').val(emailAddress.signature_title);
      $('#emailAddressEditModal').find('input[name="signature_email"]').val(emailAddress.signature_email);
      $('#emailAddressEditModal').find('input[name="signature_phone"]').val(emailAddress.signature_phone);
      $('#emailAddressEditModal').find('input[name="signature_website"]').val(emailAddress.signature_website);
      $('#emailAddressEditModal').find('input[name="twilio_recovery_code"]').val(emailAddress.twilio_recovery_code);
      $('#emailAddressEditModal').find('textarea[name="signature_address"]').val(emailAddress.signature_address);
      $('#emailAddressEditModal').find('textarea[name="signature_social"]').val(emailAddress.signature_social);
      $("#img1").hide();
      $("#img2").hide();
      if(emailAddress && emailAddress.signature_logo){
        $("#img1").attr("src",  "{{url('/')}}/uploads/" + emailAddress.signature_logo);
        $("#img1").show();
      }
      if(emailAddress && emailAddress.signature_logo){
        $("#img2").attr("src",  "{{url('/')}}/uploads/" + emailAddress.signature_image);
        $("#img2").show();
      }
      
      
      tinymce.init({
    selector: '#address1',
    menubar: false
  });
  tinymce.init({
    selector: '#social1',
    menubar: false
  });

	  $('#edit_store_website_id').val(emailAddress.store_website_id).trigger('change');

    $('#edit_incoming_driver').val(emailAddress.incoming_driver).trigger('change');
    $('#edit_driver').val(emailAddress.driver).trigger('change');
    $('#edit_port').val(emailAddress.port).trigger('change');
    $('#edit_port').val(emailAddress.port).trigger('change');
    $('#edit_encryption').val(emailAddress.encryption).trigger('change');

    });

    $(document).on('click', '.assign-users', function() {
      var emailId = $(this).data('email-id');
      var users = $(this).data('users');
      var url = "{{ route('email-addresses.assign') }}";
      $('#assignUsersModal'+emailId).find('input[name="email_id"]').val(emailId);
      $('#assignUsersModal'+emailId+' form').attr('action', url);

    });


    $(document).on('click', '.view-email-history', function(e) {
        var id = $(this).attr('data-id');
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/getemailhistory/'+id,
          dataType: 'json',
          type: 'post',
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          // Show data in modal
          $('#EmailRunHistoryModal tbody').html(response.data);
          $('#EmailRunHistoryModal').modal('show');

          $("#loading-image").hide();
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });

    $(document).on('click', '.error-email-history', function(e) {
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/geterroremailhistory',
          dataType: 'json',
          type: 'post',
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          // Show data in modal
          $('#ErrorHistoryModal tbody').html(response.data);
          $('#ErrorHistoryModal').modal('show');

          $("#loading-image").hide();
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });


    $(document).on("click",".show-related-accounts",function(e){
        var id = $(this).data('id');
        $.ajax({
          url: '/email/get-related-account/'+id,
          type: 'get',
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          // Show data in modal

          var model  = $("#show-content-model-table");
              model.find(".modal-title").html("Email Used Places");
              model.find(".modal-body").html(response);
              model.modal("show");

          $("#loading-image").hide();
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });
  $('.checkbox_ch').change(function(){
             var values = $('input[name="userIds[]"]:checked').map(function(){return $(this).val();}).get();
             $('#userIds').val(values);
         });
function sendtoWhatsapp(password_id) {
		$("#sendToWhatsapp"+ password_id +"" ).modal('show');
	}
  </script>
  <script>

        var isLoading = false;
        var page = 1;
        $(document).ready(function () {

            var $loader = $('.infinite-scroll-products-loader');

            function ajaxFormJSONRequest(formClassName) {
                $(formClassName)
                    .on('submit',  e => {
                        e.preventDefault();

                        const data = {};
                        const formData = new FormData(e.target);
                        for (let row of formData) {
                            data[row[0]] = row[1]
                        }

                        $.ajax({
                            url: `${e.target.action}?ajax=1`,
                            type: e.target.method.toUpperCase(),
                            data: data,
                            dataType: 'json',
                            beforeSend: function() {
                                $loader.show();
                            },
                            success: function (response) {
                                $loader.hide();
                                isLoading = false;

                                switch(response.status) {
                                    case 'success':
                                        toastr.success(response.msg);
                                        $(formClassName).closest('.modal').modal('hide');
                                        break
                                    case 'error':
                                        toastr.error(response.msg);
                                        break
                                }
                            },
                            error: function (error) {
                                $loader.hide();
                                isLoading = false;
                                toastr.error(error);
                            }
                        });
                    })
            }

            ajaxFormJSONRequest('.password-generate-form');
            ajaxFormJSONRequest('.email-update-form');
            ajaxFormJSONRequest('.password-create-form');

            
            $('.form-search-data') 
                .on("submit", e => {
                    e.preventDefault();
                    $.ajax({
                        url: "{{url('email-addresses')}}",
                        type: 'GET',
                        data: $('.form-search-data').serialize(),
                        beforeSend: function() {
                            $loader.show();
                        },
                        success: function (data) {
                            $loader.hide();
                            if('' === data.trim())
                                return;
                            $('.infinite-scroll-cashflow-inner').html(data);


                            isLoading = false;
                        },
                        error: function () {
                            console.log('here');
                            $loader.hide();
                            isLoading = false;
                        }
                    });
                })


            function loadMore() {
                if (isLoading)
                    return;
                isLoading = true;
                var $loader = $('.infinite-scroll-products-loader');
                page = page + 1;
                $.ajax({
                    url: "{{url('email-addresses')}}?ajax=1&page="+page,
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function (data) {
                        $loader.hide();
                        if('' === data.trim())
                            return;
                        $('.infinite-scroll-cashflow-inner').append(data);


                        isLoading = false;
                    },
                    error: function () {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }
        });

        $(document).on("click",".btn-copy-password",function() {
          var password = $(this).data('id');
          var $temp = $("<input>");
          $("body").append($temp);
          $temp.val(password).select();
          document.execCommand("copy");
          $temp.remove();
          alert("Copied!");
        });

        $(document).on("click",".btn-copy-username",function() {
          var password = $(this).data('id');
          var $temp = $("<input>");
          $("body").append($temp);
          $temp.val(password).select();
          document.execCommand("copy");
          $temp.remove();
          alert("Copied!");
        });

        $(document).on("click",".btn-copy-token",function() {
          var password = $(this).data('id');
          var $temp = $("<input>");
          $("body").append($temp);
          $temp.val(password).select();
          document.execCommand("copy");
          $temp.remove();
          alert("Copied!");
        });
        $(document).on("click",".single-email-run-cron",function() {
          var data = {
            "_token": "{{ csrf_token() }}",
            'id': $(this).attr('data-id'),
          };
          $.ajax({
            type: 'POST',
            url: '/email-addresses/single-email-run-cron',
            data: data,
            success: function(data) {
              window.location.reload();
              if(data.status = "success"){
                toastr["success"](data.message);
              }else{
                toastr["error"]('Something went wrong');
              }
            },
            error: function (response) {
              window.location.reload();
              toastr['error']("Something went wrong", 'error');
            }
          });
        });

        $(document).on('click','.emailAlertSwitch',function(){
            $(this).parent().find('span .text').removeClass('textLeft');
            $(this).parent().find('span .text').removeClass('textRight');

            var isChecked = $(this).prop('checked');
            var availabilityText = isChecked ? 'Online' : 'Offline';
            var alignmentText = isChecked ? 'textLeft' : 'textRight';

            // Update the text content within the toggle switch
            $(this).parent().find('span .text').text(availabilityText);
            var availabilityTextNew = isChecked ? 'On' : 'Off';
            $(this).parent().find('span .text').addClass(availabilityTextNew);
            let email_address_id = $(this).attr('data-id');

            $.ajax({
                type: 'POST',
                url: "{{route('email-addresses.email-alert')}}",
                beforeSend: function () {
                    $("#loading-image").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    id : email_address_id,
                    email_alert : isChecked
                },
                dataType: "json",
            }).done(function (response) {
                $("#loading-image").hide();
                toastr['success'](response.message, 'success');
            }).fail(function (response) {
                $("#loading-image").hide();
                toastr['error']('Error', 'error');

            });
        })
  </script>
@endsection
