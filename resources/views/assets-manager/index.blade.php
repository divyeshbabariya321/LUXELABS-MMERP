@extends('layouts.app')

@section('title', 'Assets Manager List')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Assets Manager List</h2>
            <div class="pull-left">
              <form class="form-inline" action="{{ route('assets-manager.index') }}" method="GET">
                <div class="form-group ml-3">
                    <br>
                    <?php //echo Form::text("search", request()->get("search", ""), ["class" => "form-control", "placeholder" => "Enter keyword for search"]); ?>

                    <div class="form-group m-1">
                        <input name="search" list="search-lists" type="text" class="form-control" placeholder="Enter keyword for search" value="{{request()->get('search')}}" />
                        <datalist id="search-lists">
                            @foreach ($assets as $key => $val )
                                <option value="{{$val->name}}">
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="form-group ml-3">
                  <br>
                  <select class="form-control" name="archived">
                    <option value="">Select</option>
                    <option value="1" {{ isset($archived) && $archived == 1 ? 'selected' : '' }}>Archived</option>
                  </select>
                </div>
                <div class="form-group ml-3">
                  <br>
                  {{ html()->select("asset_type", \App\AssetsManager::assertTypeList(), request("asset_type", ""))->class("form-control") }}
                </div>
                <div class="form-group ml-3">
                  <br>
                  {{ html()->select("purchase_type", \App\AssetsManager::purchaseTypeList(), request("purchase_type", ""))->class("form-control") }}
                </div>
                <div class="form-group ml-3">
                  <br>
                    {{ html()->select("payment_cycle", \App\AssetsManager::paymentCycleList(), request("payment_cycle", ""))->class("form-control") }}
                </div>
                <div class="col-md-1">
                  <br>
                  <select class="form-control" id="createdAt-select">
                    <option value="">Select SortBy CreatedAt</option>
                    <option value="asc">Asc</option>
                    <option value="desc">Desc</option>
                  </select>
                </div>
                <div class="form-group ml-3">
                  Select Created Users
                  <br>
                  {{ html()->multiselect("user_ids[]", \App\User::orderBy('name')->pluck('name', 'id')->toArray(), request('user_ids'))->class("form-control select2") }}
                </div>
                <div class="form-group ml-3">
                  Select Ips
                  <br>
                  {{ html()->multiselect("ip_ids[]", \App\AssetsManager::pluck('ip', 'ip')->toArray(), request('ip_ids'))->class("form-control select2") }}
                </div>
                <br>
                  <button type="submit" class="btn ml-2"><i class="fa fa-filter"></i></button>
                <a href="{{route('assets-manager.index')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
              </form>
            </div>
            <div class="pull-right">
              <br>
                <button type="button" class="btn btn-secondary btn-sm text-white mr-4" data-toggle="modal" data-target="#asdatatablecolumnvisibilityList">Column Visiblity</button>
            </div>

            <div class="pull-right">
              <br>
                <a class="btn btn-secondary btn-sm text-white mr-4" href="{{ route('user-accesses.index') }}" target="_blank">User Access</a>
            </div>

            <div class="pull-right">
              <br>
                <button type="button" class="btn btn-secondary btn-sm text-white mr-4 assets-create-modal"><i class="fa fa-plus"></i></button>
            </div>
            <div class="pull-right">
              <br>
                <button type="button" class="btn btn-xs ml-3 mr-3 mt-1" data-toggle="modal" data-target="#cashflows">Cash Flows</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')
    @include('partials.modals.user-access-request')
    @include('partials.modals.user-access-response')

    <div class="mt-3 col-md-12">
      <div class="infinite-scroll" style="overflow-y: auto">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
                @if(!empty($dynamicColumnsToShowAM))
                    @if (!in_array('ID', $dynamicColumnsToShowAM))
                        <th style="width: 3%;">ID</th>
                    @endif

                    @if (!in_array('Name', $dynamicColumnsToShowAM))
                        <th width="6%">Name</th>
                    @endif

                    @if (!in_array('Capacity', $dynamicColumnsToShowAM))
                        <th width="6%">Capacity</th>
                    @endif

                    @if (!in_array('User Name', $dynamicColumnsToShowAM))
                        <th width="5%">User Name</th>
                    @endif

                    @if (!in_array('Pwd', $dynamicColumnsToShowAM))
                        <th width="6%">Pwd</th>
                    @endif

                    @if (!in_array('Ast Type', $dynamicColumnsToShowAM))
                        <th width="7%">Ast Type</th>
                    @endif

                    @if (!in_array('Cat', $dynamicColumnsToShowAM))
                        <th width="5%">Cat</th>
                    @endif

                    @if (!in_array('Pro Name', $dynamicColumnsToShowAM))
                        <th width="7%">Pro Name</th>
                    @endif

                    @if (!in_array('Pur Type', $dynamicColumnsToShowAM))
                        <th width="7%">Pur Type</th>
                    @endif

                    @if (!in_array('Pymt Cycle', $dynamicColumnsToShowAM))
                        <th width="6%">Pymt Cycle</th>
                    @endif

                    @if (!in_array('Due Date', $dynamicColumnsToShowAM))
                        <th width="5%">Due Date</th>
                    @endif

                    @if (!in_array('Amount', $dynamicColumnsToShowAM))
                        <th width="5%">Amount</th>
                    @endif

                    @if (!in_array('Currency', $dynamicColumnsToShowAM))
                        <th width="5%">Currency</th>
                    @endif

                    @if (!in_array('Location', $dynamicColumnsToShowAM))
                        <th width="3%">Location</th>
                    @endif

                    @if (!in_array('Usage', $dynamicColumnsToShowAM))
                        <th width="5%">Usage</th>
                    @endif

                    @if (!in_array('Link', $dynamicColumnsToShowAM))
                        <th width="10%">Link</th>
                    @endif

                    @if (!in_array('IP', $dynamicColumnsToShowAM))
                        <th width="10%">IP</th>
                    @endif

                    @if (!in_array('IP Name', $dynamicColumnsToShowAM))
                        <th width="10%">IP Name</th>
                    @endif

                    @if (!in_array('Account Name', $dynamicColumnsToShowAM))
                        <th width="10%">Account Name</th>
                    @endif

                    @if (!in_array('Account password', $dynamicColumnsToShowAM))
                        <th width="10%">Account password</th>
                    @endif

                    @if (!in_array('Monit Api URL', $dynamicColumnsToShowAM))
                        <th width="10%">Monit Api URL</th>
                    @endif

                    @if (!in_array('Monit Api Username', $dynamicColumnsToShowAM))
                        <th width="10%">Monit Api Username</th>
                    @endif

                    @if (!in_array('Monit Api Password', $dynamicColumnsToShowAM))
                        <th width="10%">Monit Api Password</th>
                    @endif

                    @if (!in_array('VNC Ip', $dynamicColumnsToShowAM))
                        <th width="10%">VNC Ip</th>
                    @endif

                    @if (!in_array('VNC Port', $dynamicColumnsToShowAM))
                        <th width="10%">VNC Port</th>
                    @endif

                    @if (!in_array('VNC Password', $dynamicColumnsToShowAM))
                        <th width="10%">VNC Password</th>
                    @endif

                    @if (!in_array('Created By', $dynamicColumnsToShowAM))
                        <th width="5%">Created By</th>
                    @endif

                    @if (!in_array('Action', $dynamicColumnsToShowAM))
                        <th width="5%">Action</th>
                    @endif
                @else 
                    <th width="3%">ID</th>
                    <th width="6%">Name</th>
                    <th width="6%">Capacity</th>
                    <th width="5%">User Name</th>
                    <th width="6%">Pwd</th>
                    <th width="7%">Ast Type</th>
                    <th width="5%">Cat</th>
                    <th width="7%">Pro Name</th>
                    <th width="7%">Pur Type</th>
                    <th width="6%">Pymt Cycle</th>
                    <th width="5%">Due Date</th>
                    <th width="5%">Amount</th>
                    <th width="5%">Currency</th>
                    <th width="3%">Location</th>
                    <th width="5%">Usage</th>
                    <th width="10%">Link</th>
                    <th width="10%">IP</th>
                    <th width="10%">IP Name</th>
                    <th width="10%">Account Name</th>
                    <th width="10%">Account password</th>
                    <th width="10%">Monit Api URL</th>
                    <th width="10%">Monit Api Username</th>
                    <th width="10%">Monit Api Password</th>
                    <th width="10%">VNC Ip</th>
                    <th width="10%">VNC Port</th>
                    <th width="10%">VNC Password</th>
                    <th width="5%">Created By</th>
                    <th width="5%">Action</th>
              @endif
            </tr>
          </thead>

          <tbody>
            @foreach ($assets as $k => $asset)
                @if(!empty($dynamicColumnsToShowAM))
                    <tr>
                        @if (!in_array('ID', $dynamicColumnsToShowAM))
                            <td>{{ $asset->id }}</td>
                        @endif

                        @if (!in_array('Name', $dynamicColumnsToShowAM))
                            <td class="expand-row-msg" data-name="name" data-id="{{$asset->id}}">
                                <span class="show-short-name-{{$asset->id}}">{{ Str::limit($asset->name, 8, '..')}}</span>
                                <span style="word-break:break-all;" class="show-full-name-{{$asset->id}} hidden">{{$asset->name}}</span>
                            </td>
                        @endif

                        @if (!in_array('Capacity', $dynamicColumnsToShowAM))
                            <td class="expand-row-msg" data-name="capacity" data-id="{{$asset->id}}">
                                <span class="show-short-capacity-{{$asset->id}}">{{ Str::limit($asset->capacity, 10, '..')}}</span>
                                <span style="word-break:break-all;" class="show-full-capacity-{{$asset->id}} hidden">{{$asset->capacity}}</span>
                            </td>
                        @endif

                        @if (!in_array('User Name', $dynamicColumnsToShowAM))
                            <td class="expand-row-msg" data-name="user_name" data-id="{{$asset->id}}">
                                <span class="show-short-user_name-{{$asset->id}}">{{ Str::limit($asset->user_name, 10, '..')}}</span>
                                <span style="word-break:break-all;" class="show-full-user_name-{{$asset->id}} hidden">{{$asset->user_name}}</span>

                                @if($asset->user_name!='-' && !empty($asset->user_name))
                                <button type="button"  class="btn btn-copy-username btn-sm float-right" data-id="{{$asset->user_name}}">
                                <i class="fa fa-clone" aria-hidden="true"></i>
                                @endif
                            </td>
                        @endif

                        @if (!in_array('Pwd', $dynamicColumnsToShowAM))
                            <td class="expand-row-msg" data-name="password" data-id="{{$asset->id}}">
                                <span class="show-short-password-{{$asset->id}}">{{ Str::limit($asset->password, 3, '..')}}</span>
                                <span style="word-break:break-all;" class="show-full-password-{{$asset->id}} hidden">{{$asset->password}}</span>
                                @if($asset->password!='-' && !empty($asset->password))
                                <button type="button"  class="btn btn-copy-password btn-sm float-right" data-id="{{$asset->password}}">
                                <i class="fa fa-clone" aria-hidden="true"></i>
                                @endif
                                </button>
                            </td>
                        @endif

                        @if (!in_array('Ast Type', $dynamicColumnsToShowAM))
                            <td>{{ $asset->asset_type }}</td>
                        @endif

                        @if (!in_array('Cat', $dynamicColumnsToShowAM))
                            <td>@if(isset($asset->category)) {{ $asset->category->cat_name }} @endif</td>
                        @endif

                        @if (!in_array('Pro Name', $dynamicColumnsToShowAM))
                            <td>{{ $asset->provider_name }}</td>
                        @endif

                        @if (!in_array('Pur Type', $dynamicColumnsToShowAM))
                            <td>{{ $asset->purchase_type }}</td>
                        @endif

                        @if (!in_array('Pymt Cycle', $dynamicColumnsToShowAM))
                            <td>{{ $asset->payment_cycle }}</td>
                        @endif

                        @if (!in_array('Due Date', $dynamicColumnsToShowAM))
                            <td>{{ ($asset->due_date)?$asset->due_date:'--' }}</td>
                        @endif

                        @if (!in_array('Amount', $dynamicColumnsToShowAM))
                            <td>{{ $asset->amount }}</td>
                        @endif

                        @if (!in_array('Currency', $dynamicColumnsToShowAM))
                            <td>{{ $asset->currency }}</td>
                        @endif

                        @if (!in_array('Location', $dynamicColumnsToShowAM))
                            <td>{{ $asset->location }}</td>
                        @endif

                        @if (!in_array('Usage', $dynamicColumnsToShowAM))
                            <td class="expand-row-msg" data-name="usage" data-id="{{$asset->id}}">
                                <span class="show-short-usage-{{$asset->id}}">{{ Str::limit($asset->usage, 9, '..')}}</span>
                                <span style="word-break:break-all;" class="show-full-usage-{{$asset->id}} hidden">{{$asset->usage}}</span>
                            </td>
                        @endif

                        @if (!in_array('Link', $dynamicColumnsToShowAM))
                            <td><a href="{{ $asset->link }}" target="_blank">{{ $asset->link }}</a></td>
                        @endif

                        @if (!in_array('IP', $dynamicColumnsToShowAM))
                            <td>
                                {{ $asset->ip }}
                                <button class="ipButton btn btn-xs edit-assets pull-left" data-value="{{$asset->ip}}" data-id="{{$asset->id}}"><i class="fa fa-files-o" aria-hidden="true"></i></button>
                                <span class="ipButton-{{$asset->id}}" style="color: green;"></span>
                            </td>
                        @endif

                        @if (!in_array('IP Name', $dynamicColumnsToShowAM))
                            <td class="expand-row-msg" data-name="ip_name" data-id="{{$asset->id}}">
                                <span class="show-short-ip_name-{{$asset->id}}">{{ Str::limit($asset->ip_name, 10, '..')}}</span>
                                <span style="word-break:break-all;" class="show-full-ip-name-{{$asset->id}} hidden">{{$asset->ip_name}}</span>
                            </td>
                        @endif

                        @if (!in_array('Account Name', $dynamicColumnsToShowAM))
                            <td>{{ $asset->account_username }}</td>
                        @endif

                        @if (!in_array('Account password', $dynamicColumnsToShowAM))
                            <td>{{ $asset->account_password }}</td>
                        @endif

                        @if (!in_array('Monit Api URL', $dynamicColumnsToShowAM))
                            <td>{{ $asset->monit_api_url }}</td>
                        @endif

                        @if (!in_array('Monit Api Username', $dynamicColumnsToShowAM))
                            <td>{{ $asset->monit_api_username }}</td>
                        @endif

                        @if (!in_array('Monit Api Password', $dynamicColumnsToShowAM))
                            <td>{{ $asset->monit_api_password }}</td>
                        @endif

                        @if (!in_array('VNC Ip', $dynamicColumnsToShowAM))
                            <td>{{ $asset->vnc_ip }}</td>
                        @endif

                        @if (!in_array('VNC Port', $dynamicColumnsToShowAM))
                            <td>{{ $asset->vnc_port }}</td>
                        @endif

                        @if (!in_array('VNC Password', $dynamicColumnsToShowAM))
                            <td>{{ $asset->vnc_password }}</td>
                        @endif

                        @if (!in_array('Created By', $dynamicColumnsToShowAM))
                            <td>{{ $asset->user?->name }}</td>
                        @endif

                        @if (!in_array('Action', $dynamicColumnsToShowAM))
                            <td>
                                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$asset->id}}')"><i class="fa fa-arrow-down"></i></button>
                                <!--   <a href="{{ route('assets-manager.show', $asset->id) }}" class="btn  d-inline btn-image" href=""><img src="/images/view.png" /></a> -->
                            </td>
                        @endif
                    </tr>

                    @if (!in_array('Action', $dynamicColumnsToShowAM))
                        <tr class="action-btn-tr-{{$asset->id}} d-none">
                            <td>Action</td>
                            <td colspan="15">
                            @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('asset-manager'))
                            <button type="button" class="btn btn-xs edit-assets pull-left" data-toggle="modal" data-target="#assetsEditModal" title="Edit Assets" data-assets="{{ json_encode($asset) }}"><i class="fa fa-edit"></i></button>
                            @endif
                            <button type="button" class="btn btn-xs make-remark pull-left" data-toggle="modal" data-target="#makeRemarkModal" title="Make Remark" data-id="{{ $asset->id }}"><i class="fa fa-clipboard"></i></button>
                            @if(auth()->user()->hasRole('Admin'))
                            {{ html()->form('DELETE', route('assets-manager.destroy', [$asset->id]))->style('display:inline')->open() }}
                            <button type="submit" class="btn btn-xs pull-left" title="Delete Assets" onclick="return confirm('{{ __('Are you sure you want to Delete?') }}')"><i class="fa fa-trash"></i></button>
                            {{ html()->form()->close() }}
                            @endif
                            <button type="button" title="Payment history" class="btn payment-history-btn btn-xs pull-left" title="Payment History" data-id="{{$asset->id}}">
                            <i class="fa fa-history"></i>
                            </button>
                            <button type="button" class="btn btn-xs show-assets-history-log pull-left" data-toggle="modal" title="Show Assets History" data-target="#showAssetsHistoryLogModel"  data-assets_id="{{ $asset->id }}"><i class="fa fa-eye"></i></button>

                            <a style="padding:1px;" class="btn d-inline btn-image execute-bash-command-select-folder" data-folder_name="{{$asset->folder_name}}" data-id="{{$asset->id}}" href="#"  title="Execute Bash Command">
                            <img src="{{asset('/images/send.png')}}" style="color:gray; cursor: nwse-resize; width: 0px;">
                            </a>
                            <button title="Response History" data-id="{{$asset->id}}" type="button"  class="btn execute_bash_command_response_history"style="padding:1px 0px;">
                            <a href="javascript:void(0);"style="color:gray;"><i class="fa fa-history"></i></a>
                            </button>
                            @if(auth()->user()->hasRole('Admin'))
                            <button type="button" class="btn btn-xs send-assets-email" data-toggle="modal" data-assetid="{{$asset->id}}" data-target="#assetsSendEmailModal" title="Send Email" ><i class="fa fa-envelope"></i></button>
                            <button type="button" class="btn btn-xs assets-manager-record-permission" data-toggle="modal" data-assetid="{{$asset->id}}" data-target="#assetsPermissionModal" title="Record Permission" ><i class="fa fa-lock"></i></button>
                            @endif
                            <button type="button" title="Update status" data-id="{{$asset->id}}" onclick="updateUserActiveForAssetManager(this)" class="btn" style="padding: 0px 1px;">
                            <i class="fa fas fa-toggle-{{$asset->active == 1 ? 'on' : 'off  '}}"></i>
                            </button>

                            <button type="button" class="btn show-users-access-modal" id="show-users-access-modal-{{$asset->id}}" data-id="{{$asset->id}}" data-value="{{$asset->ip}}" data-toggle="modal" data-target="#userAccessModal" title="Create User Access" style="padding: 0px 1px;">
                            <i class="fa fas fa-universal-access"></i>
                            </button>

                            <button type="button" class="btn show-terminal-user-modal" id="show-terminal-user-modal-{{$asset->id}}" data-id="{{$asset->id}}" title="Create Terminal User Access" style="padding: 0px 1px;">
                            <i class="fa fa-user-plus" aria-hidden="true"></i>
                            </button>
                            </td>
                        </tr>
                    @endif

                @else
                    <tr>
                        <td>{{ $asset->id }}</td>
                        <td class="expand-row-msg" data-name="name" data-id="{{$asset->id}}">
                        <span class="show-short-name-{{$asset->id}}">{{ Str::limit($asset->name, 8, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-name-{{$asset->id}} hidden">{{$asset->name}}</span>
                        </td>
                        <td class="expand-row-msg" data-name="capacity" data-id="{{$asset->id}}">
                        <span class="show-short-capacity-{{$asset->id}}">{{ Str::limit($asset->capacity, 10, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-capacity-{{$asset->id}} hidden">{{$asset->capacity}}</span>
                        </td>
                        <td class="expand-row-msg" data-name="user_name" data-id="{{$asset->id}}">
                        <span class="show-short-user_name-{{$asset->id}}">{{ Str::limit($asset->user_name, 10, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-user_name-{{$asset->id}} hidden">{{$asset->user_name}}</span>

                        @if($asset->user_name!='-' && !empty($asset->user_name))
                        <button type="button"  class="btn btn-copy-username btn-sm float-right" data-id="{{$asset->user_name}}">
                        <i class="fa fa-clone" aria-hidden="true"></i>
                        @endif
                        </td>

                        <td class="expand-row-msg" data-name="password" data-id="{{$asset->id}}">
                        <span class="show-short-password-{{$asset->id}}">{{ Str::limit($asset->password, 3, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-password-{{$asset->id}} hidden">{{$asset->password}}</span>
                        @if($asset->password!='-' && !empty($asset->password))
                        <button type="button"  class="btn btn-copy-password btn-sm float-right" data-id="{{$asset->password}}">
                        <i class="fa fa-clone" aria-hidden="true"></i>
                        @endif
                        </button>

                        </td>
                        <td>{{ $asset->asset_type }}</td>
                        <td>@if(isset($asset->category)) {{ $asset->category->cat_name }} @endif</td>

                        <td>{{ $asset->provider_name }}</td>
                        <td>{{ $asset->purchase_type }}</td>
                        <td>{{ $asset->payment_cycle }}</td>
                        <td>{{ ($asset->due_date)?$asset->due_date:'--' }}</td>
                        <td>{{ $asset->amount }}</td>
                        <td>{{ $asset->currency }}</td>
                        <td>{{ $asset->location }}</td>
                        <td class="expand-row-msg" data-name="usage" data-id="{{$asset->id}}">
                        <span class="show-short-usage-{{$asset->id}}">{{ Str::limit($asset->usage, 9, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-usage-{{$asset->id}} hidden">{{$asset->usage}}</span>
                        </td>
                        <td><a href="{{ $asset->link }}" target="_blank">{{ $asset->link }}</a></td>
                        <td>
                        {{ $asset->ip }}
                        <button class="ipButton btn btn-xs edit-assets pull-left" data-value="{{$asset->ip}}" data-id="{{$asset->id}}"><i class="fa fa-files-o" aria-hidden="true"></i></button>
                        <span class="ipButton-{{$asset->id}}" style="color: green;"></span>
                        </td>
                        <td class="expand-row-msg" data-name="ip_name" data-id="{{$asset->id}}">
                        <span class="show-short-ip_name-{{$asset->id}}">{{ Str::limit($asset->ip_name, 10, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-ip-name-{{$asset->id}} hidden">{{$asset->ip_name}}</span>
                        </td>
                        <td>{{ $asset->account_username }}</td>
                        <td>{{ $asset->account_password }}</td>
                        <td>{{ $asset->monit_api_url }}</td>
                        <td>{{ $asset->monit_api_username }}</td>
                        <td>{{ $asset->monit_api_password }}</td>
                        <td>{{ $asset->vnc_ip }}</td>
                        <td>{{ $asset->vnc_port }}</td>
                        <td>{{ $asset->vnc_password }}</td>
                        <td>{{ $asset->user?->name }}</td>
                        <td>
                        <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$asset->id}}')"><i class="fa fa-arrow-down"></i></button>
                        <!--   <a href="{{ route('assets-manager.show', $asset->id) }}" class="btn  d-inline btn-image" href=""><img src="/images/view.png" /></a> -->
                        </td>
                    </tr>
                    <tr class="action-btn-tr-{{$asset->id}} d-none">
                        <td>Action</td>
                        <td colspan="15">
                        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('asset-manager'))
                        <button type="button" class="btn btn-xs edit-assets pull-left" data-toggle="modal" data-target="#assetsEditModal" title="Edit Assets" data-assets="{{ json_encode($asset) }}"><i class="fa fa-edit"></i></button>
                        @endif
                        <button type="button" class="btn btn-xs make-remark pull-left" data-toggle="modal" data-target="#makeRemarkModal" title="Make Remark" data-id="{{ $asset->id }}"><i class="fa fa-clipboard"></i></button>
                        @if(auth()->user()->hasRole('Admin'))
                        {{ html()->form('DELETE', route('assets-manager.destroy', [$asset->id]))->style('display:inline')->open() }}
                        <button type="submit" class="btn btn-xs pull-left" title="Delete Assets" onclick="return confirm('{{ __('Are you sure you want to Delete?') }}')"><i class="fa fa-trash"></i></button>
                        {{ html()->form()->close() }}
                        @endif
                        <button type="button" title="Payment history" class="btn payment-history-btn btn-xs pull-left" title="Payment History" data-id="{{$asset->id}}">
                        <i class="fa fa-history"></i>
                        </button>
                        <button type="button" class="btn btn-xs show-assets-history-log pull-left" data-toggle="modal" title="Show Assets History" data-target="#showAssetsHistoryLogModel"  data-assets_id="{{ $asset->id }}"><i class="fa fa-eye"></i></button>

                        <a style="padding:1px;" class="btn d-inline btn-image execute-bash-command-select-folder" data-folder_name="{{$asset->folder_name}}" data-id="{{$asset->id}}" href="#"  title="Execute Bash Command">
                        <img src="{{asset('/images/send.png')}}" style="color:gray; cursor: nwse-resize; width: 0px;">
                        </a>
                        <button title="Response History" data-id="{{$asset->id}}" type="button"  class="btn execute_bash_command_response_history"style="padding:1px 0px;">
                        <a href="javascript:void(0);"style="color:gray;"><i class="fa fa-history"></i></a>
                        </button>
                        @if(auth()->user()->hasRole('Admin'))
                        <button type="button" class="btn btn-xs send-assets-email" data-toggle="modal" data-assetid="{{$asset->id}}" data-target="#assetsSendEmailModal" title="Send Email" ><i class="fa fa-envelope"></i></button>
                        <button type="button" class="btn btn-xs assets-manager-record-permission" data-toggle="modal" data-assetid="{{$asset->id}}" data-target="#assetsPermissionModal" title="Record Permission" ><i class="fa fa-lock"></i></button>
                        @endif
                        <button type="button" title="Update status" data-id="{{$asset->id}}" onclick="updateUserActiveForAssetManager(this)" class="btn" style="padding: 0px 1px;">
                        <i class="fa fas fa-toggle-{{$asset->active == 1 ? 'on' : 'off  '}}"></i>
                        </button>

                        <button type="button" class="btn show-users-access-modal" id="show-users-access-modal-{{$asset->id}}" data-id="{{$asset->id}}" data-value="{{$asset->ip}}" data-toggle="modal" data-target="#userAccessModal" title="Create User Access" style="padding: 0px 1px;">
                        <i class="fa fas fa-universal-access"></i>
                        </button>
                        </td>
                    </tr>
                @endif
            @endforeach
          </tbody>
        </table>
        {{ $assets->appends(request()->except('page'))->links() }}
      </div>
    </div>
    @include('partials.modals.remarks')
    @include('assets-manager.partials.payment-history')
    @include('assets-manager.partials.assets-modals')
    @include("assets-manager.partials.column-visibility-modal")

    <div id="amtua-remarks-histories-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remarks Histories</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Remarks</th>
                                    <th width="20%">Updated By</th>
                                    <th width="30%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="amtua-remarks-histories-list-view">
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

    <div id="execute_bash_command_select_folderModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Send Bash Command</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="table-responsive mt-3">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Command Name</th>
                    <th>Send</th>
                  </tr>
                </thead>
                <tbody id="execute_select_folder_tbody">

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
    <div id="magentoDevScriptUpdateHistoryModal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
          <div class="modal-header">
              <h4 class="modal-title">User History</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="table-responsive mt-3">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="20%">Created At</th>
                      <th width="20%">Ip</th>
                      <th width="30%">Response</th>
                      <th width="30%">Command Name</th>
                    </tr>
                  </thead>
                  <tbody id="magentoDevScriptUpdateHistory">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="showUserHistoryModel" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
          <div class="modal-header">
              <h4 class="modal-title">Magento Setting Update Rersponse History</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="table-responsive mt-3">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="20%">ID</th>
                      <th width="30%">Change By</th>
                      <th width="30%">User Name</th>
                      <th width="20%">Created At</th>
                    </tr>
                  </thead>
                  <tbody class="showUserHistoryModelView">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="assetsSendEmailModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('asset.manage.send.email') }}" method="POST">
            @csrf

            <div class="modal-header">
              <h4 class="modal-title">Send Email</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <input type="hidden" name="assets_manager_id" id="assets-manager-id">
                    <label for="value">Send To(Users)</label>
                    <select class="form-control select2" name="user_name" >
                        <option value="">Select</option>
                        @foreach($users as $key => $user)
                          <option value="{{$user['id']}}">{{$user['name']}}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('user_name'))
                    <div class="alert alert-danger">{{$errors->first('user_name')}}</div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="value">From Mail</label>
                    <select class="form-control" name="from_email" id="from_email" required>
                        @foreach ($emailAddress as $emailAddress)
                        <option value="{{ $emailAddress->from_address }}">{{ $emailAddress->from_name }} - {{ $emailAddress->from_address }} </option>
                        @endforeach
                    </select>
                    @if ($errors->has('from_email'))
                    <div class="alert alert-danger">{{$errors->first('from_email')}}</div>
                    @endif
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
    <div id="assetsPermissionModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('asset.manage.records.permission') }}" method="POST">
            @csrf

            <div class="modal-header">
              <h4 class="modal-title">Records Permission</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <input type="hidden" name="assets_manager_id" id="permission-assets-manager-id">
                    <label for="value">Users</label>
                    <select class="form-control select2" multiple id="asset_user_name" name="user_name[]" >
                        <option value="">Select</option>
                        @foreach($users as $key => $user)
                          <option value="{{$user['id']}}">{{$user['name']}}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('user_name'))
                    <div class="alert alert-danger">{{$errors->first('user_name')}}</div>
                    @endif
                </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
@endsection

@section('scripts')
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

  <script>
    $(document).ready(function () {

        $(".ipButton").click(function () {
            var textToCopy = $(this).data("value");
            var id = $(this).data("id");
            $('.ipButton-'+id).text('copied.');
            var $tempInput = $("<input>");
            $("body").append($tempInput);
            $tempInput.val(textToCopy).select();
            document.execCommand("copy");
            $tempInput.remove();

            setTimeout(function () {
                 $('.ipButton-'+id).text('');
             }, 1500);
        });
    });
</script>
  <script type="text/javascript">
    function Showactionbtn(id){
      $(".action-btn-tr-"+id).toggleClass('d-none')
      $("#asset_user_name").select2('destroy');
    }
   $('.select-multiple').select2({width: '100%'});
   $('.select2').select2();
    // $('ul.pagination').hide();
    // $(function() {
    //   $('.infinite-scroll').jscroll({
    //     autoTrigger: true,
    //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
    //     padding: 2500,
    //     nextSelector: '.pagination li.active + li a',
    //     contentSelector: 'div.infinite-scroll',
    //     callback: function() {
    //       $('ul.pagination').first().remove();
    //       $(".select-multiple").select2();
    //       initialize_select2();
    //     }
    //   });
    // });

    $(document).on("click", ".execute-bash-command-select-folder", function(href) {

      var folder_name = $(this).data('folder_name');
      var id = $(this).data('id');
      var html = '';
      $('#execute_select_folder_tbody').html("");
      let result =  Array.isArray(folder_name);
      if(result){
        $.each(folder_name,function(key,value){
          if(value){
            html = '<tr><td>'+value+'  </td><td><a style="padding:1px;" class="btn d-inline btn-image execute-bash-command" data-folder_name="'+value+'" href="#" class="ip_name'+key+'" data-id="'+id+'" title="Execute Bash Command"><img src="/images/send.png" style="color: gray; cursor: nwse-resize; width: 0px;"></a></td></tr>';
            $('#execute_select_folder_tbody').append(html);
          }
        });
        $('#execute_bash_command_select_folderModal').modal('show');
      } else {
        alert("Please Check Record Site Folder Name.");
      }


	});
	$(document).on("click", ".execute-bash-command", function(href) {
		if(confirm ("Do you want to run this script???")){
			$.ajax({
				type: 'POST',
				url: 'assets-manager/magento-dev-script-update',
				beforeSend: function () {
					$("#loading-image").show();
				},
				data: {
					_token: "{{ csrf_token() }}",
					id: $(this).data('id'),
					folder_name : $(this).data('folder_name')
				},
				dataType: "json"
			}).done(function (response) {
				$("#loading-image").hide();
				if (response.code == 200) {
					toastr['success'](response.message, 'success');
				}
			}).fail(function (response) {
				$("#loading-image").hide();
				toastr['error'](response.message, 'error');
			});
		}
	});

  $(document).on("click", ".execute_bash_command_response_history", function(href) {

      $.ajax({
        type: 'POST',
        url: 'assets-manager/magento-dev-update-script-history/'+ $(this).data('id') ,
        beforeSend: function () {
          $("#loading-image").show();
        },
        data: {
          _token: "{{ csrf_token() }}",
          id: $(this).data('id'),
        },
        dataType: "json"
      }).done(function (response) {
        $("#loading-image").hide();
        if (response.code == 200) {
          $('#magentoDevScriptUpdateHistory').html(response.data);
          $('#magentoDevScriptUpdateHistoryModal').modal('show');
          toastr['success'](response.message, 'success');
        }
      }).fail(function (response) {
        $("#loading-image").hide();
      });

  });

    $(document).on('click', '.edit-assets', function() {
      var asset = $(this).data('assets');
      var url = "{{ url('assets-manager') }}/" + asset.id;
      var d = new Date(asset.start_date);
      var day = (d.getDate() < 10 ? '0' : '') + d.getDate();
      var mon = ((d.getMonth()+1) < 10 ? '0' : '') + (d.getMonth()+1);
      var str =  d.getFullYear()+ '-' + mon + '-' + day;
      $('#assetsEditModal form').attr('action', url);
      $('#asset_name').val(asset.name);
      $('#user_name').val(asset.user_name);
      if(asset.user_name)
        $(".select-multiple").select2("val", asset.user_name);
      else
        $(".select-multiple").select2("val", "");

      $('#old_user_name').val(asset.user_name);
      $('.password-assets-manager').val(asset.password);
      $('.oldpassword-assets-manager').val(asset.password);
      $('#ip').val(asset.ip);
      $('#old_ip').val(asset.ip);
      $('#assigned_to').val(asset.assigned_to);
      $('#provider_name').val(asset.provider_name);
      $('#location').val(asset.location);
      $('#currency').val(asset.currency);
      if(asset.start_date !='0000-00-00' && asset.start_date != null){
        $('.start_date').val(''+str+'');
        $('#old_start_date').val(''+str+'');
      }else{
        $('.start_date').val("dd/mm/yyyy");
        $('#old_start_date').val("yyyy-mm-dd");
      }
      $('#asset_asset_type').val(asset.asset_type);
      $('#category_id2').val(asset.category_id);
      $('#asset_purchase_type').val(asset.purchase_type);
      $('#asset_payment_cycle').val(asset.payment_cycle);
      $('#asset_amount').val(asset.amount);
      $('#usage').val(asset.usage);
      $('#capacity').val(asset.capacity);
      $('#link').val(asset.link);
      $('#client_id').val(asset.client_id);
      $('#account_username').val(asset.account_username);
      $('#account_password').val(asset.account_password);
      $('#monit_api_url').val(asset.monit_api_url);
      $('#monit_api_username').val(asset.monit_api_username);
      $('#monit_api_password').val(asset.monit_api_password);
      $('#vnc_ip').val(asset.vnc_ip);
      $('#vnc_port').val(asset.vnc_port);
      $('#vnc_password').val(asset.vnc_password);

      $('#ip_name_ins').val(asset.ip_name);

      $(".addServerUpdate").html("");
      var addserver = '';
      let folderName = JSON.parse(asset.folder_name);
      $.each(folderName,function(key,value){
        addserver = addserver+'<input type="text" name="folder_name[]" id="folder_name'+key+'" class="form-control"  value="'+value+'" >';

      });
      $(".addServerUpdate").append(addserver);
      $('#server_password').val(asset.server_password);
      $('.show-user-history-btn').attr('data-id', asset.id);
    });
    $(document).on('click', '.expand-row-msg', function () {
      var name = $(this).data('name');
      var id = $(this).data('id');
      var full = '.expand-row-msg .show-short-'+name+'-'+id;
      var mini ='.expand-row-msg .show-full-'+name+'-'+id;
      $(full).toggleClass('hidden');
      $(mini).toggleClass('hidden');
    });

    $(document).on('click', '.make-remark', function(e) {
      e.preventDefault();

      var id = $(this).data('id');
      $('#add-remark input[name="id"]').val(id);

      $.ajax({
          type: 'GET',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.gettaskremark') }}',
          data: {
            id:id,
            module_type: "assets-manager"
          },
      }).done(response => {
          var html='';

          $.each(response, function( index, value ) {
            html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
            html+"<hr>";
          });
          $("#makeRemarkModal").find('#remark-list').html(html);
      });
    });

    $('#addRemarkButton').on('click', function() {
      var id = $('#add-remark input[name="id"]').val();
      var remark = $('#add-remark').find('textarea[name="remark"]').val();

      $.ajax({
          type: 'POST',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.addRemark') }}',
          data: {
            id:id,
            remark:remark,
            module_type: 'assets-manager'
          },
      }).done(response => {
          $('#add-remark').find('textarea[name="remark"]').val('');

          var html =' <p> '+ remark +' <br> <small>By You updated on '+ moment().format('DD-M H:mm') +' </small></p>';

          $("#makeRemarkModal").find('#remark-list').append(html);
      }).fail(function(response) {

        alert('Could not fetch remarks');
      });
    });

    $(document).ready(function() {
      // Change category on create page logic
      $('#category_id').on('change', function(){
          var category_id = $('#category_id').val();
          if( category_id != '' && category_id == '-1')
          {
              $('.othercat').show();
          }
          else{
            $('.othercat').hide();
          }
      });
      // Change categoryon create page logic
      $('#category_id2').on('change', function(){
          var category_id = $('#category_id2').val();
          if( category_id != '' && category_id == '-1')
          {
              $('.othercatedit').show();
          }
          else{
            $('.othercatedit').hide();
          }
      });

    });

    $(document).ready(function() {
          $('.payment-history-btn').click(function(){
            var asset_id = $(this).data('id');
            $.ajax({
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ route('assetsmanager.paymentHistory') }}",
              data: {
                asset_id:asset_id,
              },
          }).done(response => {
            $('#payment-history-modal').find('.payment-history-list-view').html('');
              if(response.success==true){
                $('#payment-history-modal').find('.payment-history-list-view').html(response.html);
                $('#payment-history-modal').modal('show');
              }

          }).fail(function(response) {

            alert('Could not fetch payments');
          });
        });

      });

      $(document).ready(function() {
          $('.show-user-history-btn').click(function(){
            var asset_id = $(this).attr('data-id');//$(this).data('id');
            $.ajax({
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ route('assetsmanager.userchange.history') }}",
              data: {
                asset_id:asset_id,
              },
          }).done(response => {
            $('#showUserHistoryModel').find('.showUserHistoryModelView').html('');
              if(response.success==true){
                $('#showUserHistoryModel').find('.showUserHistoryModelView').html(response.html);
                $('#showUserHistoryModel').modal('show');
              }
          }).fail(function(response) {
            alert('Could not fetch Data');
          });
        });
      });

      $(document).ready(function() {
          $('.show-assets-history-log').click(function(){
            var asset_id = $(this).data('assets_id');
            $.ajax({
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ route('assetsmanager.assetManamentLog') }}",
              data: {
                asset_id:asset_id,
              },
          }).done(response => {
            $('#payment-history-modal').find('.payment-history-list-view').html('');
              if(response.success==true){
                $('#showAssetsHistoryLogModel').find('#showAssetsHistoryLogView').html(response.html);
                $('#showAssetsHistoryLogModel').modal('show');
              }

          }).fail(function(response) {

            alert('Could not fetch Log');
          });
        });

      });
      $( ".updIpNamebtn" ).bind( "click", function() {
          var getCount = $(".getUpdCount").val();
          getCount = (parseInt(getCount) + parseInt(1));
          $(".getUpdCount").val(getCount);
          var addIpName = '<br/><input type="text" name="ip_name[]" id="ip_name'+getCount+'" class="form-control"  value="" >';
          $(".addUpdIpName").append(addIpName);
      });

      $( ".insIpNamebtn" ).bind( "click", function() {
          var getCount = $(".getInsCount").val();
          getCount = (parseInt(getCount) + parseInt(1));
          $(".getInsCount").val(getCount);
          var addIpName = '<br/><input type="text" name="ip_name[]" id="ip_name'+getCount+'" class="form-control"  value="" >';
          $(".addInsIpName").append(addIpName);
      });

      $( ".serverUpdbtn" ).bind( "click", function() {
          var getServerUpdCount = $(".getServerUpdCount").val();
          getServerUpdCount = (parseInt(getServerUpdCount) + parseInt(1));
          $(".getServerUpdCount").val(getServerUpdCount);
          var addServerUpdate = '<input type="text" name="folder_name[]" id="folder_name'+getServerUpdCount+'" class="form-control"  value="" >';
          $(".addServerUpdate").append(addServerUpdate);
      });

      $( ".serverInsbtn" ).bind( "click", function() {
          var getInsServerCount = $(".getInsServerCount").val();
          getInsServerCount = (parseInt(getInsServerCount) + parseInt(1));
          $(".getInsServerCount").val(getInsServerCount);
          var addInsServerUpdate = '<br/><input type="text" name="folder_name[]" id="folder_name'+getInsServerCount+'" class="form-control"  value="" >';
          $(".addInsServerUpdate").append(addInsServerUpdate);
      });

      $(".send-assets-email, .assets-manager-record-permission").on("click", function(){
        var assetid = $(this).data('assetid');
        $("#assets-manager-id").val(assetid);
        $("#permission-assets-manager-id").val(assetid);
        $("#asset_user_name option:selected").removeAttr('selected');
        $("#asset_user_name").select2();

        $.ajax({
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ route('assetsmanager.linkuser.list') }}",
              data: {
                asset_id:assetid,
              },
          }).done(response => {
            var ids = response.data.userids;
            ids = ids.split(',');
            for (let index = 0; index < ids.length; index++) {
              const element = ids[index];
              $("#asset_user_name option[value="+element+"]").attr('selected', 'selected');
            }
            $("#asset_user_name").select2();

          }).fail(function(response) {

            alert('Could not fetch Log');
          });
      });

      $(".assets-create-modal").on("click", function(){
        $("#assetsCreateModal").modal("show");
      });

      $("#asset_user_name").select2({
        placeholder: 'Select Users'
      });

      var active_btn = null;
      function updateUserActiveForAssetManager(ele) {
        active_btn = jQuery(ele);
        let asset_id = active_btn.data("id");
        // let is_task_planned = btn.data("is_task_planned");

        if (
          confirm("Are you sure want to update?")
        ) {
          jQuery.ajax({
            headers: {
              "X-CSRF-TOKEN": jQuery('meta[name="csrf-token"]').attr("content"),
            },
            url: "{{route('assets-manager.update-status')}}",
            type: "POST",
            data: {
              asset_id,
            },
            dataType: "json",
            beforeSend: function () {
              jQuery("#loading-image").show();
            },
            success: function (res) {
              toastr["success"](res.message);
              jQuery("#loading-image").hide();
              if(active_btn.find(".fa").hasClass("fa-toggle-on")) {
                active_btn.find(".fa").removeClass("fa-toggle-on").addClass("fa-toggle-off");
              } else {
                active_btn.find(".fa").removeClass("fa-toggle-off").addClass("fa-toggle-on");
              }
            },
            error: function (res) {
              if (res.responseJSON != undefined) {
                toastr["error"](res.responseJSON.message);
              }
              jQuery("#loading-image").hide();
            },
          });
        }
      }

    $(".btn-copy-username").click(function() {
        var username = $(this).data('id');
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(username).select();
        document.execCommand("copy");
        $temp.remove();
    });

    $(".btn-copy-password").click(function() {
        var password = $(this).data('id');
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(password).select();
        document.execCommand("copy");
        $temp.remove();
    });

    $(document).ready(function() {
        $('.show-users-access-modal').click(function(){

            var generatedPassword = generateRandomPassword(12); // Change the number to set the desired password length
            $('.ua_password').val(generatedPassword);

            var assets_management_id = $(this).data('id');

            var assets_management_ip = $(this).data('value');

            $.ajax({
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('assetsmanager.assetManamentUsersAccess') }}",
                data: {
                    assets_management_id : assets_management_id
                },
            }).done(response => {

                if(response.success==true){
                    $('#showAssetsManagementUsersModel').find('#showAssetsManagementUsersView').html(response.html);
                    $('#showAssetsManagementUsersModel #assets_management_id').val(assets_management_id);
                    $('#showAssetsManagementUsersModel #assets_management_ip_address').val(assets_management_ip);
                    $('#showAssetsManagementUsersModel').modal('show');
                }

            }).fail(function(response) {

                alert('Could not fetch Log');
            });
        });

        $('.show-terminal-user-modal').click(function(){

            var assets_management_id = $(this).data('id');

            $.ajax({
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('assetsmanager.assetManamentTerminalUsersAccess') }}",
                data: {
                    assets_management_id : assets_management_id
                },
            }).done(response => {

                if(response.success==true){
                    $('#showAssetsManagementTerminalUsersModel').find('#showAssetsManagementTerminalUsersView').html(response.html);
                    $('#showAssetsManagementTerminalUsersModel #tua_assets_management_id').val(assets_management_id);
                    $('#showAssetsManagementTerminalUsersModel').modal('show');
                }

            }).fail(function(response) {

                alert('Could not fetch Log');
            });
        });

    });

    function deleteTerminalUserAccess(id) {
        if(confirm ("Do you want to delete this access???")){
            $.ajax({
                type: 'POST',
                url: 'assets-manager/terminal-user-access-delete',
                beforeSend: function () {
                    $("#loading-image-modal").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    id : id
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image-modal").hide();
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');
                }

                setTimeout(function() {
                    location.reload();
                }, 1000);

            }).fail(function (response) {
                $("#loading-image-modal").hide();
                toastr['error'](response.message, 'error');
            });
        }
    }

    function saveRemarks(asset_manager_terminal_user_accesses_id){

        var remarks = $("#remark_"+asset_manager_terminal_user_accesses_id).val();

        if(remarks==''){
            alert('Please enter remarks.');
            return false;
        }

        $.ajax({
            url: "{{route('assetsmanager.saveremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'amtua_id' :asset_manager_terminal_user_accesses_id,
                'remarks' :remarks,
            },
            beforeSend: function() {
                $(this).text('Loading...');
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');

                    $("#remark_"+asset_manager_terminal_user_accesses_id).val('');
                }
            }
        }).fail(function(response) {
            $("#loading-image").hide();
            toastr['error'](response.responseJSON.message);
        });
    }

    function updateUsernamePassword(asset_manager_terminal_user_accesses_id, type){

        var username = $("#user_access_username_"+asset_manager_terminal_user_accesses_id).val();
        var password = $("#user_access_password_"+asset_manager_terminal_user_accesses_id).val();

        if(remarks==''){
            alert('Please enter remarks.');
            return false;
        }

        $.ajax({
            url: "{{route('assetsmanager.updateup')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'amtua_id' :asset_manager_terminal_user_accesses_id,
                'type' :type,
                'username' :username,
                'password' :password,
            },
            beforeSend: function() {
                $(this).text('Loading...');
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');

                    $("#remark_"+asset_manager_terminal_user_accesses_id).val('');
                } else if (response.code == 500) {
                    toastr['error'](response.message, 'username already exists.');
                }
            }
        }).fail(function(response) {
            $("#loading-image").hide();
            toastr['error'](response.responseJSON.message);
        });
    }

    $(document).on('click', '.remarks-history-show', function() {
        var amtua_id = $(this).attr('data-id');

        $.ajax({
            url: "{{route('assetsmanager.getremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'amtua_id' :amtua_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.remarks != null) ? v.remarks : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#amtua-remarks-histories-list").find(".amtua-remarks-histories-list-view").html(html);
                    $("#amtua-remarks-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    function generateRandomPassword(length) {
        var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        var password = "";
        for (var i = 0; i < length; i++) {
            var randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }

        return password;
    }


    $(document).on("click", "#create-user-acccess-btn", function(href) {

        $('.text-danger-access').html('');
        if($('.ua_user_ids').val() == '') {
            $('.ua_user_ids').next().text("Please select user");
            return false;
        }

        if($('.ua_username').val() == '') {
            $('.ua_username').next().text("Please enter user name");
            return false;
        }

        if($('.ua_password').val() == '') {
            $('.ua_password').next().text("Please enter password");
            return false;
        }

        if($('.ua_user_ids').val() != '' && $('.ua_username').val() != '' && $('.ua_password').val() != '' && $('#assets_management_id').val() != '' && $('.ua_user_role').val() != '' && $('.ua_login_type').val() != '' && $('#assets_management_ip_address').val() != '') {

            $.ajax({
                type: 'POST',
                url: 'assets-manager/user-access-create',
                beforeSend: function () {
                    $("#loading-image-modal").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id : $('.ua_user_ids').val(),
                    username : $('.ua_username').val(),
                    password : $('.ua_password').val(),
                    user_role : $('.ua_user_role').val(),
                    login_type : $('.ua_login_type').val(),
                    key_type : $('.ua_key_type').val(),
                    assets_management_id : $('#assets_management_id').val(),
                    server_var : $('#assets_management_ip_address').val(),
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image-modal").hide();
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');
                }

                $('#createUserAccess')[0].reset();

                setTimeout(function() {
                    location.reload();
                }, 1000);

            }).fail(function (response) {
                $("#loading-image-modal").hide();
                toastr['error'](response.message, 'error');
            });
        } else{
            $('.text-danger-all').next().text("Something went wrong. Please try again.");
            return false
        }
    });

    $(document).on("click", "#create-terminal-user-acccess-btn", function(href) {

        $('.text-danger-access').html('');

        if($('.tua_username').val() == '') {
            $('.tua_username').next().text("Please enter user name");
            return false;
        }

        if($('.tua_password').val() == '') {
            $('.tua_password').next().text("Please enter password");
            return false;
        }

        if($('.tua_username').val() != '' && $('.tua_password').val() != '' && $('#tua_assets_management_id').val() != '') {

            $.ajax({
                type: 'POST',
                url: 'assets-manager/terminal-user-access-create',
                beforeSend: function () {
                    $("#loading-image-modal").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    username : $('.tua_username').val(),
                    password : $('.tua_password').val(),
                    assets_management_id : $('#tua_assets_management_id').val(),
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image-modal").hide();
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');
                    $('#createTerminalUserAccess')[0].reset();

                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else if (response.code == 500) {
                    toastr['error'](response.message, 'username already exists.');
                }

            }).fail(function (response) {
                $("#loading-image-modal").hide();
                toastr['error'](response.message, 'error');
            });
        } else{
            $('.text-danger-all').next().text("Something went wrong. Please try again.");
            return false
        }
    });

    function deleteUserAccess(id) {
        $.ajax({
            type: 'POST',
            url: 'assets-manager/user-access-delete',
            beforeSend: function () {
                $("#loading-image-modal").show();
            },
            data: {
                _token: "{{ csrf_token() }}",
                id : id
            },
            dataType: "json"
        }).done(function (response) {
            $("#loading-image-modal").hide();
            if (response.code == 200) {
                toastr['success'](response.message, 'success');
            }

            setTimeout(function() {
                location.reload();
            }, 1000);

        }).fail(function (response) {
            $("#loading-image-modal").hide();
            toastr['error'](response.message, 'error');
        });
    }

    $(document).ready(function($) {
        // Now you can use $ safely within this block
        $("#tag-input").autocomplete({
            source: function(request, response) {
                // Send an AJAX request to the server-side script
                $.ajax({
                    url: '{{ route('assetsmanager.users') }}',
                    dataType: 'json',
                    data: {
                        term: request.term // Pass user input as 'term' parameter
                    },
                    success: function(data) {
                        response(data); // The server returns filtered suggestions as JSON
                    }
                });
            },
            minLength: 1, // Minimum characters before showing suggestions
            select: function(event, ui) {
                // Handle the selection if needed
            }
        });
    })

    $(document).on('click','.user-access-request-view',function(){
        id = $(this).data('id');
        $.ajax({
              method: "GET",
              url: `{{ route('assetsmanager.user_access_request', [""]) }}/` + id,
              dataType: "json",
              success: function(response) {

                    $("#user-access-request-list-header").find(".user-access-request-header-view").html(response.request_data);
                    $("#user-access-request-list-header").modal("show");

              }
          });
    });

    $(document).on('click','.user-access-response-view',function(){
        id = $(this).data('id');
        $.ajax({
              method: "GET",
              url: `{{ route('assetsmanager.user_access_request', [""]) }}/` + id,
              dataType: "json",
              success: function(response) {

                    $("#user-access-response-list-header").find(".user-access-response-header-view").html(response.response_data);
                    $("#user-access-response-list-header").modal("show");

              }
          });
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

    $( ".generatepasswordadd" ).bind( "click", function() {
        var newPassword = generatePassword(8);
        $(".password-assets-manager-add").val(newPassword);
    });

    $( ".generatepasswordedit" ).bind( "click", function() {
        var newPassword = generatePassword(8);
        $(".password-assets-manager").val(newPassword);
    });

    $( ".generatepasswordaddterminal" ).bind( "click", function() {
        var newPassword = generatePassword(8);
        $(".tua_password").val(newPassword);
    });
  </script>
@endsection
