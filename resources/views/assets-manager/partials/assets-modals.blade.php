<div id="assetsCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <style type="text/css">
            #assetsCreateModal .select2-container, #assetsEditModal .select2-container{width: 100% !important;}
        </style>
        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('assets-manager.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title">Store a Assets Manager</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>

                                @if ($errors->has('name'))
                                    <div class="alert alert-danger">{{$errors->first('name')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Capacity:</strong>
                                <input type="text" name="capacity" class="form-control" value="{{ old('capacity') }}">

                                @if ($errors->has('capacity'))
                                    <div class="alert alert-danger">{{$errors->first('capacity')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>User Name:</strong>

                                <select class="form-control select2" name="user_name" >
                                    <option value="">Select</option>
                                    @foreach($users as $key => $user)
                                        <option value="{{$user['id']}}" {{ $user['id'] == old('user_name') ? 'selected' : '' }}>{{$user['name']}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('user_name'))
                                    <div class="alert alert-danger">{{$errors->first('user_name')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Password:</strong>
                                <input type="text" name="password" class="form-control password-assets-manager-add" value="{{ old('password') }}" >
                                <a href="javascript:void(0);" class="generatepasswordadd">Generate Password</a>
                                @if ($errors->has('password'))
                                    <div class="alert alert-danger">{{$errors->first('password')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>IP:</strong>
                                <input type="text" name="ip"  class="form-control" value="{{ old('ip') }}">

                                @if ($errors->has('ip'))
                                    <div class="alert alert-danger">{{$errors->first('ip')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>IP Name:</strong>
                                <input type="hidden" class="getInsCount" value="0"/>
                                <input type="text" name="ip_name" id="ip_name" class="form-control" value="{{ old('ip_name') }}">
                                <div class="addInsIpName"></div>

                                @if ($errors->has('ip'))
                                    <div class="alert alert-danger">{{$errors->first('ip_name')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Link:</strong>
                                <input type="text" name="link"  class="form-control" value="{{ old('link') }}">

                                @if ($errors->has('link'))
                                    <div class="alert alert-danger">{{$errors->first('link')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Folder Name:</strong>
                                <input type="hidden" class="getInsServerCount" value="0"/>
                                <div class="addInsServerUpdate">
                                    <input type="text" name="folder_name[]" id="folder_name0" class="form-control" value="">
                                </div>
                                <a href="javascript:void(0);" class="serverInsbtn">Add Folder Name</a>
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Assigen to</strong>
                                <select class="form-control select2" name="assigned_to" >
                                    <option value="">Select</option>
                                    @foreach($users as $key => $user)
                                        <option value="{{$user['id']}}" {{ $user['id'] == old('user_name') ? 'selected' : '' }}>{{$user['name']}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('assigned_to'))
                                    <div class="alert alert-danger">{{$errors->first('assigned_to')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">  
                            <div class="form-group">
                                <strong>Site Name</strong>
                                <select class="form-control select2" name="website_id" >
                                <option value="">Select</option>
                                    @foreach($websites as $website)
                                        <option value="{{$website->id}}" {{ $website->id == old('website_id') ? 'selected' : '' }}>{{$website->website}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('assigned_to'))
                                    <div class="alert alert-danger">{{$errors->first('assigned_to')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">  
                            <div class="form-group">
                                <strong>Plate Form</strong>
                                <select class="form-control select2" name="asset_plate_form_id" >
                                    <option value="">Select</option>
                                    @foreach($plateforms as $plateform)
                                        <option value="{{$plateform->id}}" {{ $plateform->id == old('asset_plate_form_id') ? 'selected' : '' }}>{{$plateform->name}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('asset_plate_form_id'))
                                    <div class="alert alert-danger">{{$errors->first('asset_plate_form_id')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">  
                            <div class="form-group">
                                <strong>Email Address</strong>
                                <select class="form-control select2" name="email_address_id">
                                    <option value="">Select</option>
                                    @foreach($emailAddress as $email)
                                    <option value="{{$email->id}}" {{ $email->id == old('email_address_id') ? 'selected' : '' }}>{{$email->from_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('email_address_id'))
                                    <div class="alert alert-danger">{{$errors->first('email_address_id')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Phone Number</strong>
                                <select class="form-control select2" name="whatsapp_config_id" >
                                <option value="">Select</option>
                                    @foreach($whatsappCon as $phone)
                                        <option value="{{$phone->id}}" {{ $phone->id == old('whatsapp_config_id') ? 'selected' : '' }}>{{$phone->number}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('whatsapp_config_id'))
                                    <div class="alert alert-danger">{{$errors->first('whatsapp_config_id')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Asset Type:</strong>
                                <select class="form-control" name="asset_type" id="asset_type">
                                    <option value="">Select</option>
                                    <option value="Hard" {{ Input::old('asset_type') == 'Hard'? 'selected' : '' }}>Hard</option>
                                    <option value="Soft" {{ Input::old('asset_type') == 'Soft'? 'selected' : '' }}>Soft</option>
                                </select>

                                @if ($errors->has('asset_type'))
                                    <div class="alert alert-danger">{{$errors->first('asset_type')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">  
                            <div class="form-group">
                                <strong>Category:</strong>
                                <select class="form-control" name="category_id" id="category_id">
                                    <option value="">Select</option>
                                    @foreach($assets_category as $cat)
                                        <option value="{{$cat->id}}" {{ $cat->id == old('category_id') ? 'selected' : '' }}>{{$cat->cat_name}}</option>
                                    @endforeach
                                    <option value="-1" {{ old('category_id') == '-1'? 'selected' : '' }}>Other</option>
                                </select>

                                @if ($errors->has('category_id'))
                                    <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3 othercat" style="display: none;">
                            <div class="form-group">
                                <strong>Other Category:</strong>
                                <input type="text" name="other" class="form-control" value="{{ old('other') }}">
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Provider Name:</strong>
                                <input type="text" name="provider_name" class="form-control" value="{{ old('provider_name') }}" required>

                                @if ($errors->has('provider_name'))
                                <div class="alert alert-danger">{{$errors->first('provider_name')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">  
                            <div class="form-group">
                                <strong>Purchase Type:</strong>
                                <select class="form-control" name="purchase_type" id="purchase_type">
                                    <option value="">Select</option>
                                    <option value="Owned" {{ old('purchase_type') == 'Owned'? 'selected' : '' }}>Owned</option>
                                    <option value="Rented" {{ old('purchase_type') == 'Rented'? 'selected' : '' }}>Rented</option>
                                    <option value="Subscription" {{ old('purchase_type') == 'Subscription'? 'selected' : '' }}>Subscription</option>
                                </select>

                                @if ($errors->has('purchase_type'))
                                    <div class="alert alert-danger">{{$errors->first('purchase_type')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Payment Cycle:</strong>
                                <select class="form-control" name="payment_cycle" id="payment_cycle">
                                    <option value="">Select</option>
                                    <option value="Daily" {{ old('payment_cycle') == 'Daily'? 'selected' : '' }}>Daily</option>
                                    <option value="Weekly" {{ old('payment_cycle') == 'Weekly'? 'selected' : '' }}>Weekly</option>
                                    <option value="Bi-Weekly" {{ old('payment_cycle') == 'Bi-Weekly'? 'selected' : '' }}>Bi-Weekly</option>
                                    <option value="Monthly" {{ old('payment_cycle') == 'Monthly'? 'selected' : '' }}>Monthly</option>
                                    <option value="Yearly" {{ old('payment_cycle') == 'Yearly'? 'selected' : '' }}>Yearly</option>
                                    <option value="One time" {{ old('payment_cycle') == 'One time'? 'selected' : '' }}>One time</option>
                                </select>

                                @if ($errors->has('payment_cycle'))
                                    <div class="alert alert-danger">{{$errors->first('payment_cycle')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3"> 
                            <div class="form-group">
                                <strong>Amount:</strong>
                                <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" step=".01">

                                @if ($errors->has('amount'))
                                    <div class="alert alert-danger">{{$errors->first('amount')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Start Date:</strong>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required>

                                @if ($errors->has('start_date'))
                                    <div class="alert alert-danger">{{$errors->first('start_date')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3"> 
                            <div class="form-group">
                                <strong>Currency:</strong>
                                <input type="text" name="currency" class="form-control" value="{{ old('currency') }}" required>

                                @if ($errors->has('currency'))
                                    <div class="alert alert-danger">{{$errors->first('currency')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Location:</strong>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}">

                                @if ($errors->has('location'))
                                    <div class="alert alert-danger">{{$errors->first('location')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">                         
                            <div class="form-group">
                                <strong>Usage:</strong>
                                <input type="text" name="usage" class="form-control" value="{{ old('usage') }}">

                                @if ($errors->has('usage'))
                                <div class="alert alert-danger">{{$errors->first('usage')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Client Id:</strong>
                                <input type="text" name="client_id" class="form-control" value="{{ old('client_id') }}">

                                @if ($errors->has('client_id'))
                                    <div class="alert alert-danger">{{$errors->first('client_id')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">                         
                            <div class="form-group">
                                <strong>Account Username:</strong>
                                <input type="text" name="account_username" class="form-control" value="{{ old('account_username') }}">

                                @if ($errors->has('account_username'))
                                <div class="alert alert-danger">{{$errors->first('account_username')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Account Password:</strong>
                                <input type="text" name="account_password" class="form-control" value="{{ old('account_password') }}">

                                @if ($errors->has('account_password'))
                                    <div class="alert alert-danger">{{$errors->first('account_password')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Monit Api URL:</strong>
                                <input type="text" name="monit_api_url" class="form-control" value="{{ old('monit_api_url') }}">

                                @if ($errors->has('monit_api_url'))
                                    <div class="alert alert-danger">{{$errors->first('monit_api_url')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Monit Api Username:</strong>
                                <input type="text" name="monit_api_username" class="form-control" value="{{ old('monit_api_username') }}">

                                @if ($errors->has('monit_api_username'))
                                    <div class="alert alert-danger">{{$errors->first('monit_api_username')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Monit Api Password:</strong>
                                <input type="text" name="monit_api_password" class="form-control" value="{{ old('monit_api_password') }}">

                                @if ($errors->has('monit_api_password'))
                                    <div class="alert alert-danger">{{$errors->first('monit_api_password')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>VNC Ip:</strong>
                                <input type="text" name="vnc_ip" class="form-control" value="{{ old('vnc_ip') }}">

                                @if ($errors->has('vnc_ip'))
                                    <div class="alert alert-danger">{{$errors->first('vnc_ip')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>VNC Port:</strong>
                                <input type="text" name="vnc_port" class="form-control" value="{{ old('vnc_port') }}">

                                @if ($errors->has('vnc_port'))
                                    <div class="alert alert-danger">{{$errors->first('vnc_port')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>VNC Password:</strong>
                                <input type="text" name="vnc_password" class="form-control" value="{{ old('vnc_password') }}">

                                @if ($errors->has('vnc_password'))
                                    <div class="alert alert-danger">{{$errors->first('vnc_password')}}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="cashflows" class="modal fade" role="dialog" >
  <div class="modal-dialog" style="max-width:100%;width:70%">

    <!-- Modal content-->
    <div class="modal-content">


        <div class="modal-header">
          <h4 class="modal-title">All Cash Flows</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
        <div class="mt-3 col-md-12">
      <table class="table table-responsive table-bordered table-striped">
        <thead>
          <tr>
            <th width="3%">ID</th>
            <th width="5%">User</th>
            <th width="5%">Date</th>
            <th width="5%">Desc</th>
            <th width="5%">Amt</th>
            <th width="7%">E Amt</th>
            <th width="10%">E EU Amt</th>
            <th width="8%">EU Amt</th>
            <th width="11%">DU EU Amt</th>
            <th width="5%">Type</th>
            <th width="4%">Curr</th>
            <th width="7%">Ast Id</th>
            <th width="8%">Ast Typ</th>
            <th width="8%">Ord Sts</th>
            <th width="5%">Created</th>
          </tr>
        </thead>

        <tbody>
          @php $i=1; @endphp
          @foreach ($cashflows as $cash)
            <tr>
              <td>{{ $i }}</td>
              <td class="expand-row-msg" data-name="user_id" data-id="{{$i}}">
                <span class="show-short-user_id-{{$i}}">{{ Str::limit($cash->user_id, 3, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-user_id-{{$i}} hidden">{{$cash->user_id}}</span>
              </td>
              <td class="expand-row-msg" data-name="date" data-id="{{$i}}">
                <span class="show-short-date-{{$i}}">{{ Str::limit($cash->date, 3, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-date-{{$i}} hidden">{{$cash->date}}</span>
              </td>
              <td class="expand-row-msg" data-name="description" data-id="{{$i}}">
                <span class="show-short-description-{{$i}}">{{ Str::limit($cash->description, 4, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-description-{{$i}} hidden">{{$cash->description}}</span>
              </td>
              <td>{{ $cash->amount }}</td>
              <td>{{ $cash->erp_amount??'N/A' }}</td>

              <td>{{ $cash->erp_eur_amount??'N/A' }}</td>
              <td>{{ $cash->amount_eur??'N/A' }}</td>
              <td>{{ $cash->due_amount_eur??'N/A' }}</td>
              <td class="expand-row-msg" data-name="type" data-id="{{$i}}">
                <span class="show-short-type-{{$i}}">{{ Str::limit($cash->type, 3, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-type-{{$i}} hidden">{{$cash->type}}</span>
              </td>
              <td>{{ $cash->currency }}</td>
              <td>{{ $cash->cash_flow_able_id }}</td>
              <td class="expand-row-msg" data-name="cash_flow_able_type" data-id="{{$i}}">
                <span class="show-short-cash_flow_able_type-{{$i}}">{{ Str::limit($cash->cash_flow_able_type, 4, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-cash_flow_able_type-{{$i}} hidden">{{$cash->cash_flow_able_type}}</span>
              </td>
              <td class="expand-row-msg" data-name="order_status" data-id="{{$i}}">
                <span class="show-short-order_status-{{$i}}">{{ Str::limit($cash->order_status, 3, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-order_status-{{$i}} hidden">{{$cash->order_status}}</span>
              </td>
              <td class="expand-row-msg" data-name="created_at" data-id="{{$i}}">
                <span class="show-short-created_at-{{$i}}">{{ Str::limit($cash->created_at->format('Y-m-d'), 3, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-created_at-{{$i}} hidden">{{$cash->created_at->format('Y-m-d')}}</span>
              </td>
            </tr>
            @php $i++; @endphp
          @endforeach
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

<div id="assetsEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <form action="" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h4 class="modal-title">Update a Assets Manager</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>  
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" id="asset_name" required>

                                @if ($errors->has('name'))
                                    <div class="alert alert-danger">{{$errors->first('name')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Capacity:</strong>
                                <input type="text" name="capacity" id="capacity" class="form-control" value="{{ old('capacity') }}">

                                @if ($errors->has('capacity'))
                                    <div class="alert alert-danger">{{$errors->first('capacity')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>User Name:</strong>
                                <button type="button" class="btn btn-xs show-user-history-btn" title="Show User History" ><i class="fa fa-info-circle"></i></button>
                                
                                <input type="hidden" name="old_user_name"  id="old_user_name"  class="form-control" value="{{ old('old_user_name') }}">


                                <select class="form-control select2" name="user_name" id="user_name">
                                    <option value="">Select</option>
                                    @foreach($users as $key => $user)
                                        <option value="{{$user['id']}}" {{ $user['id'] == old('user_name') ? 'selected' : '' }}>{{$user['name']}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('user_name'))
                                    <div class="alert alert-danger">{{$errors->first('user_name')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Password:</strong>
                                <input type="text" name="password" class="form-control password-assets-manager" value="{{ old('password') }}" id="password" required>
                                <input type="hidden" name="old_password" class="form-control oldpassword-assets-manager" value="{{ old('old_password') }}" id="old_password">
                                <a href="javascript:void(0);" class="generatepasswordedit">Generate Password</a>
                                @if ($errors->has('password'))
                                    <div class="alert alert-danger">{{$errors->first('password')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>IP:</strong>
                                <input type="text" name="ip" id="ip" class="form-control" value="{{ old('ip') }}">
                                <input type="hidden" name="old_ip" class="form-control" value="{{ old('old_ip') }}" id="old_ip">

                                @if ($errors->has('ip'))
                                    <div class="alert alert-danger">{{$errors->first('ip')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>IP Name:</strong>
                                <input type="hidden" class="getUpdCount" value="0"/>
                                <div class="addUpdIpName">
                                    <input type="text" name="ip_name" id="ip_name_ins" class="form-control" value="{{ old('ip_name') }}">
                                </div>

                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Link:</strong>
                                <div class="addUpdIpName">
                                    <input type="text" name="link" id="link" class="form-control" value="{{ old('link') }}">
                                </div>

                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Folder Name:</strong>
                                <input type="hidden" class="getServerUpdCount" value="0"/>
                                <div class="addServerUpdate">
                                    <input type="text" name="folder_name[]" id="folder_name0" class="form-control" value="">
                                </div>
                                <a href="javascript:void(0);" class="serverUpdbtn">Add Folder Name</a>
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Server Password:</strong>
                                <input type="text" name="server_password" id="server_password" class="form-control" value="{{ old('server_password') }}">

                                @if ($errors->has('ip'))
                                    <div class="alert alert-danger">{{$errors->first('server_password')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Assigen to</strong>
                                <select class="form-control select2" name="assigned_to" id="assigned_to">
                                    <option value="">Select</option>
                                    @foreach($users as $key => $user)
                                        <option value="{{$user['id']}}" {{ $user['id'] == old('user_name') ? 'selected' : '' }}>{{$user['name']}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('assigned_to'))
                                    <div class="alert alert-danger">{{$errors->first('assigned_to')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Site Name</strong>
                                <select class="form-control select2" name="website_id" id="website_id" >
                                    <option value="">Select</option>
                                    @foreach($websites as $website)
                                        <option value="{{$website->id}}" {{ $website->id == old('website_id') ? 'selected' : '' }}>{{$website->website}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('assigned_to'))
                                    <div class="alert alert-danger">{{$errors->first('website_id')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Plate Form</strong>
                                <select class="form-control select2" name="asset_plate_form_id" id="asset_plate_form_id" >
                                    <option value="">Select</option>
                                    @foreach($plateforms as $plateform)
                                        <option value="{{$plateform->id}}" {{ $plateform->id == old('asset_plate_form_id') ? 'selected' : '' }}>{{$plateform->name}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('asset_plate_form_id'))
                                    <div class="alert alert-danger">{{$errors->first('asset_plate_form_id')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Email Address</strong>
                                <select class="form-control select2" name="email_address_id" id="email_address_id" >
                                <option value="">Select</option>
                                    @foreach($emailAddress as $email)
                                        <option value="{{$email->id}}" {{ $email->id == old('email_address_id') ? 'selected' : '' }}>{{$email->from_name}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('email_address_id'))
                                    <div class="alert alert-danger">{{$errors->first('email_address_id')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Phone Number</strong>
                                <select class="form-control select2" name="whatsapp_config_id" id="whatsapp_config_id" >
                                    <option value="">Select</option>
                                    @foreach($whatsappCon as $phone)
                                        <option value="{{$phone->id}}" {{ $phone->id == old('whatsapp_config_id') ? 'selected' : '' }}>{{$phone->number}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('whatsapp_config_id'))
                                    <div class="alert alert-danger">{{$errors->first('whatsapp_config_id')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Asset Type:</strong>
                                <select class="form-control" name="asset_type" id="asset_asset_type">
                                    <option value="">Select</option>
                                    <option value="Hard" {{ Input::old('asset_type') == 'Hard'? 'selected' : '' }}>Hard</option>
                                    <option value="Soft" {{ Input::old('asset_type') == 'Soft'? 'selected' : '' }}>Soft</option>
                                </select>
                                @if ($errors->has('asset_type'))
                                    <div class="alert alert-danger">{{$errors->first('asset_type')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Category:</strong>
                                <select class="form-control" name="category_id" id="category_id2">
                                    <option value="">Select</option>
                                    @foreach($assets_category as $cat)
                                        <option value="{{$cat->id}}" {{ $cat->id == old('category_id') ? 'selected' : '' }}>{{$cat->cat_name}}</option>
                                    @endforeach
                                    <option value="-1" {{ old('category_id') == '-1'? 'selected' : '' }}>Other</option>
                                </select>
                                @if ($errors->has('category_id'))
                                    <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 othercatedit" style="display: none;">
                            <div class="form-group">
                                <strong>Other Category:</strong>
                                <input type="text" name="other" class="form-control" value="{{ old('other') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Provider Name:</strong>
                                <input type="text" name="provider_name" class="form-control" value="{{ old('provider_name') }}" id="provider_name" required>

                                @if ($errors->has('provider_name'))
                                    <div class="alert alert-danger">{{$errors->first('provider_name')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Purchase Type:</strong>
                                <select class="form-control" name="purchase_type" id="asset_purchase_type">
                                    <option value="">Select</option>
                                    <option value="Owned" {{ old('purchase_type') == 'Owned'? 'selected' : '' }}>Owned</option>
                                    <option value="Rented" {{ old('purchase_type') == 'Rented'? 'selected' : '' }}>Rented</option>
                                    <option value="Subscription" {{ old('purchase_type') == 'Subscription'? 'selected' : '' }}>Subscription</option>
                                </select>
                                @if ($errors->has('purchase_type'))
                                    <div class="alert alert-danger">{{$errors->first('purchase_type')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Payment Cycle:</strong>
                                <select class="form-control" name="payment_cycle" id="asset_payment_cycle">
                                    <option value="">Select</option>
                                    <option value="Daily" {{ old('payment_cycle') == 'Daily'? 'selected' : '' }}>Daily</option>
                                    <option value="Weekly" {{ old('payment_cycle') == 'Weekly'? 'selected' : '' }}>Weekly</option>
                                    <option value="Bi-Weekly" {{ old('payment_cycle') == 'Bi-Weekly'? 'selected' : '' }}>Bi-Weekly</option>
                                    <option value="Monthly" {{ old('payment_cycle') == 'Monthly'? 'selected' : '' }}>Monthly</option>
                                    <option value="Yearly" {{ old('payment_cycle') == 'Yearly'? 'selected' : '' }}>Yearly</option>
                                    <option value="One time" {{ old('payment_cycle') == 'One time'? 'selected' : '' }}>One time</option>
                                </select>

                                @if ($errors->has('payment_cycle'))
                                    <div class="alert alert-danger">{{$errors->first('payment_cycle')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Amount:</strong>
                                <input type="number" name="amount" id="asset_amount" class="form-control" value="{{ old('amount') }}" step=".01">

                                @if ($errors->has('amount'))
                                    <div class="alert alert-danger">{{$errors->first('amount')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Start Date:</strong>
                                    <input type="date" name="start_date" id="start_date" class="form-control start_date" value="{{ old('start_date') }}" required>
                                    <input type="hidden" name="old_start_date" id="old_start_date" class="form-control" value="{{ old('old_start_date') }}" required>

                                @if ($errors->has('start_date'))
                                    <div class="alert alert-danger">{{$errors->first('start_date')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Currency:</strong>
                                <input type="text" name="currency" class="form-control" value="{{ old('currency') }}" id="currency" required>

                                @if ($errors->has('currency'))
                                    <div class="alert alert-danger">{{$errors->first('currency')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Location:</strong>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}" id="location" required>

                                @if ($errors->has('location'))
                                    <div class="alert alert-danger">{{$errors->first('location')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Usage:</strong>
                                <input type="text" id="usage" name="usage" class="form-control" value="{{ old('usage') }}">

                                @if ($errors->has('usage'))
                                    <div class="alert alert-danger">{{$errors->first('usage')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Client Id:</strong>
                                <input type="text" id="client_id" name="client_id" class="form-control" value="{{ old('client_id') }}">

                                @if ($errors->has('client_id'))
                                    <div class="alert alert-danger">{{$errors->first('client_id')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">                         
                            <div class="form-group">
                                <strong>Account Username:</strong>
                                <input type="text" id="account_username" name="account_username" class="form-control" value="{{ old('account_username') }}">

                                @if ($errors->has('account_username'))
                                <div class="alert alert-danger">{{$errors->first('account_username')}}</div>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Account Password:</strong>
                                <input type="text" id="account_password" name="account_password" class="form-control" value="{{ old('account_password') }}">

                                @if ($errors->has('account_password'))
                                    <div class="alert alert-danger">{{$errors->first('account_password')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Monit Api URL:</strong>
                                <input type="text" name="monit_api_url" id="monit_api_url" class="form-control" value="{{ old('monit_api_url') }}" >

                                @if ($errors->has('monit_api_url'))
                                    <div class="alert alert-danger">{{$errors->first('monit_api_url')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Monit Api Username:</strong>
                                <input type="text" name="monit_api_username" id="monit_api_username" class="form-control" value="{{ old('monit_api_username') }}">

                                @if ($errors->has('monit_api_username'))
                                    <div class="alert alert-danger">{{$errors->first('monit_api_username')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Monit Api Password:</strong>
                                <input type="text" name="monit_api_password" id="monit_api_password" class="form-control" value="{{ old('monit_api_password') }}">

                                @if ($errors->has('monit_api_password'))
                                    <div class="alert alert-danger">{{$errors->first('monit_api_password')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>VNC Ip:</strong>
                                <input type="text" name="vnc_ip" id="vnc_ip" class="form-control" value="{{ old('vnc_ip') }}">

                                @if ($errors->has('vnc_ip'))
                                    <div class="alert alert-danger">{{$errors->first('vnc_ip')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>VNC Port:</strong>
                                <input type="text" name="vnc_port" id="vnc_port" class="form-control" value="{{ old('vnc_port') }}">

                                @if ($errors->has('vnc_port'))
                                    <div class="alert alert-danger">{{$errors->first('vnc_port')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>VNC Password:</strong>
                                <input type="text" name="vnc_password" id="vnc_password" class="form-control" value="{{ old('vnc_password') }}">

                                @if ($errors->has('vnc_password'))
                                    <div class="alert alert-danger">{{$errors->first('vnc_password')}}</div>
                                @endif
                            </div>
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

<div id="showAssetsHistoryLogModel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Asset Manament Log</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="mt-3 col-md-12">
        <div class="infinite-scroll">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th width="4%">ID</th>
                <th width="9%">Change by</th>
                <th width="8%">Old User name</th>
                <th width="6%">Old Pwd</th>
                <th width="6%">Date</th>
              </tr>
            </thead>
  
            <tbody id="showAssetsHistoryLogView">
              
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div id="showAssetsManagementUsersModel" class="modal fade" role="dialog">
    <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asset Manament Users Access</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="mt-3 col-md-12">
                <div class="infinite-scroll">
                    <style type="text/css">
                        #showAssetsManagementUsersModel .select2-container{width: 100% !important;}
                        .ui-widget.ui-widget-content {z-index: 9999;}
                    </style>

                    <div id="myDiv">
                        <img id="loading-image-modal" src="/images/pre-loader.gif" style="display:none;"/>
                   </div>

                    <form action="" method="POST" id="createUserAccess">

                        <div class="row">
                            <input type="hidden" name="assets_management_id" id="assets_management_id">
                            <input type="hidden" name="assets_management_ip_address" id="assets_management_ip_address">
                            <div class="col-md-3"> 
                                <div class="form-group">
                                    <strong>Select Users:</strong>            
                                    {{ html()->select("ua_user_ids", \App\User::orderBy('name')->pluck('name', 'id')->toArray(), request('ua_user_ids'))->class("form-control ua_user_ids")->placeholder("Select User") }}
                                    <!-- <input class="form-control ua_user_ids" type="text" id="tag-input" name="ua_user_ids" placeholder="Select User" style="width: 100%;" value="{{request()->get('ua_user_ids')}}"> -->
                                    <span class="text-danger text-danger-access"></span>
                                </div>
                            </div>

                            <div class="col-md-3"> 
                                <div class="form-group">
                                    <strong>User Role:</strong>                    
                                    <select class="form-control ua_user_role" name="user_role" id="user_role">
                                        <option value="user">Readonly</option>
                                        <option value="magento">Developer</option>
                                        <option value="super">Super</option>
                                    </select>
                                    <span class="text-danger text-danger-access"></span>
                                </div>
                            </div>

                            <div class="col-md-3"> 
                                <div class="form-group">
                                    <strong>Login Type:</strong>                    
                                    <select class="form-control ua_login_type" name="login_type" id="login_type" onchange="showKeyType(this.value)">
                                        <option value="password">Password</option>
                                        <option value="key">Key</option>
                                    </select>
                                    <span class="text-danger text-danger-access"></span>
                                </div>
                            </div>

                            <div class="col-md-3" id="keyTypeDiv" style="display:none;"> 
                                <div class="form-group">
                                    <strong>Key Type:</strong>                    
                                    <select class="form-control ua_key_type" name="key_type" id="key_type">
                                        <option value="generate">Generate</option>
                                        <option value="regenerate">Regenerate</option>
                                    </select>
                                    <span class="text-danger text-danger-access"></span>
                                </div>
                            </div>

                            <div class="col-md-3"> 
                                <div class="form-group">
                                    <strong>User Name:</strong>                    
                                    <input class="form-control ua_username" type="text" id="ua_username" name="ua_username" placeholder="Enter User Name" style="width: 100%;" value="{{request()->get('ua_username')}}">
                                    <span class="text-danger text-danger-access"></span>
                                </div>
                            </div>

                            <div class="col-md-3"> 
                                <div class="form-group">
                                    <strong>Password:</strong>                    
                                    <input class="form-control ua_password" type="text" id="ua_password" name="ua_password" placeholder="Enter Password" style="width: 100%;" value="{{request()->get('ua_password')}}" readonly>
                                    <span class="text-danger text-danger-access"></span>
                                </div>
                            </div>

                            <div class="col-md-3"> 
                                <button type="button" id="create-user-acccess-btn" class="btn btn-secondary" style=" margin-top: 18px;">Create</button>
                            </div>

                            <div class="col-md-12"> 
                                <span class="text-danger text-danger-all"></span>
                            </div>
                        </div>
                    </form>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="4%">ID</th>
                                <th width="9%">Selected User</th>
                                <th width="9%">User Name</th>
                                <th width="9%">Password</th>
                                <th width="8%">Created Date</th>
                                <th width="8%">Request Data</th>
                                <th width="8%">Response Data</th>
                                <th width="8%">Action</th>
                            </tr>
                        </thead>
              
                        <tbody id="showAssetsManagementUsersView">
                          
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="showAssetsManagementTerminalUsersModel" class="modal fade" role="dialog">
    <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asset Manament Terminal Users Access</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="mt-3 col-md-12">
                <div class="infinite-scroll">
                    <style type="text/css">
                        #showAssetsManagementUsersModel .select2-container{width: 100% !important;}
                        .ui-widget.ui-widget-content {z-index: 9999;}
                    </style>

                    <div id="myDiv">
                        <img id="loading-image-modal" src="/images/pre-loader.gif" style="display:none;"/>
                   </div>

                    <form action="" method="POST" id="createTerminalUserAccess">

                        <div class="row">
                            <input type="hidden" name="tua_assets_management_id" id="tua_assets_management_id">

                            <div class="col-md-3"> 
                                <div class="form-group">
                                    <strong>User Name:</strong>                    
                                    <input class="form-control tua_username" type="text" id="tua_username" name="tua_username" placeholder="Enter User Name" style="width: 100%;" value="{{request()->get('tua_username')}}">
                                    <span class="text-danger text-danger-access"></span>
                                </div>
                            </div>

                            <div class="col-md-3"> 
                                <div class="form-group">
                                    <strong>Password:</strong>  <a href="javascript:void(0);" class="generatepasswordaddterminal" style="float: right;">Generate Password</a>                  
                                    <input class="form-control tua_password" type="text" id="tua_password" name="tua_password" placeholder="Enter Password" style="width: 100%;" value="{{request()->get('tua_password')}}">
                                    <span class="text-danger text-danger-access"></span>
                                </div>
                            </div>

                            <div class="col-md-3"> 
                                <button type="button" id="create-terminal-user-acccess-btn" class="btn btn-secondary" style=" margin-top: 18px;">Create</button>
                            </div>

                            <div class="col-md-12"> 
                                <span class="text-danger text-danger-all"></span>
                            </div>
                        </div>
                    </form>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="20%">User Name</th>
                                <th width="20%">Password</th>
                                <th width="35%">Remarks</th>
                                <th width="15%">Created Date</th>
                                <th width="5%">Action</th>
                            </tr>
                        </thead>
              
                        <tbody id="showAssetsManagementTerminalUsersView">
                          
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function showKeyType(login_type) {
        if(login_type=='key'){
            $('#keyTypeDiv').css("display", "block");
        } else {
            $('#keyTypeDiv').css("display", "none");
        }        
    }
</script>