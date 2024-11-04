@extends('layouts.app')
@section('content')
    <div class="row" xmlns="http://www.w3.org/1999/html">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Manage Twilio Numbers</h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12">
            <div class="row">
                <a href="{{ route('twilio-get-numbers', $account_id) }}">
                    <button type="button" class="btn btn-secondary">Get Twilio Numbers</button>
                </a>

                <a class="ml-2">
                    <button type="button" data-toggle="modal" data-target="#workspaceModal" class="btn btn-secondary">Twilio Workspace</button>
                </a>

                <a class="ml-2">
                    <button type="button" data-toggle="modal" data-target="#workerModal" class="btn btn-secondary">Twilio Worker</button>
                </a>

                <a class="ml-2">
                    <button type="button" data-toggle="modal" data-target="#priorityModal" class="btn btn-secondary">TWillio Agents Priority</button>
                </a>

                <a class="ml-2">
                    <button type="button" data-toggle="modal" data-target="#workflowModal" class="btn btn-secondary">Twilio Workflow</button>
                </a>

                <a class="ml-2">
                    <button type="button" data-toggle="modal" data-target="#activityModal" class="btn btn-secondary">Twilio Activity</button>
                </a>

                <a class="ml-2">
                    <button type="button" data-toggle="modal" data-target="#taskQueueModal" class="btn btn-secondary">Twilio Task Queue</button>
                </a>
            </div>
        </div>
    </div>
    <div class="row  no-gutters mt-3">
        <div class="col-md-12 col-sm-12" style="overflow-x: auto;" >
            <div class="">
                <div class="">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th scope="col" class="text-center">Sr. no.</th>
                            <th scope="col" class="text-center">Number</th>
                            <th scope="col" class="text-center">Friendly Name</th>
                            <th scope="col" class="text-center">SID</th>
                            <th scope="col" class="text-center">Voice url</th>
                            <th scope="col" class="text-center">Date Created</th>
                            <th scope="col" class="text-center">Date Updated</th>
                            <th scope="col" class="text-center">SMS url</th>
                            <th scope="col" class="text-center">Voice Receive Mode</th>
                            <th scope="col" class="text-center">Voice Application SID</th>
                            <th scope="col" class="text-center">SMS Application SID</th>
                            <th scope="col" class="text-center">Trunk SID</th>
                            <th scope="col" class="text-center">Emergency Status</th>
                            <th scope="col" class="text-center">Emergency Address SID</th>
                            <th scope="col" class="text-center">Store Website</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        @if(isset($numbers))
                            @foreach($numbers as $number)
                                <tr>
                                    <td>1</td>
                                    <td>{{ $number->phone_number }}</td>
                                    <td>{{ $number->friendly_name }}</td>
                                    <td>{{ $number->sid }}</td>
                                    <td>{{ $number->voice_url }}</td>
                                    <td>{{ \Carbon\Carbon::parse($number->date_created)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($number->date_updated)->format('d-m-Y') }}</td>
                                    <td>{{ $number->sms_url }}</td>
                                    <td>{{ $number->voice_receive_mode }}</td>
                                    <td>{{ $number->voice_application_sid }}</td>
                                    <td>{{ $number->sms_application_sid }}</td>
                                    <td>{{ $number->trunk_sid }}</td>
                                    <td>{{ $number->emergency_status }}</td>
                                    <td>{{ $number->emergency_address_sid }}</td>
                                    <td>{{ @$number->assigned_stores->store_website->title }}</td>
                                    <td>{{ $number->status }}</td>
                                    <td>
                                        <a href="javascript:void(0);" type="button" id="1" data-workspace-sid="{{ $number->workspace_sid }}" data-workflow-sid="{{ $number->workflow_sid }}" class="btn btn-image open_row">
                                            <img src="/images/forward.png" style="cursor: default;" width="2px;">
                                        </a>
                                        <a href="javascript:void(0);" class="call_forwarding btn d-inline btn-image" data-attr="1" title="Call Forwarding" ><img src="/images/remark.png" style="cursor: default;"></a>
                                        <a href="{{ route('twilio-call-recording', $account_id) }}" class="btn d-inline btn-image" title="Call Recording" ><img src="/images/view.png" style="cursor: default;"></a>
                                    </td>
                                </tr>
                                <tr class="hidden_row_1" data-eleid="1" style="display:none;">
                                    <td colspan="3">
                                        <label>Store website:</label>
                                        <div class="input-group">
                                            <select class="form-control store_websites" id="store_website_1">
                                                <option value="">Select store website</option>
                                                @if(isset($store_websites))
                                                    @foreach($store_websites as $websites)
                                                        <option value="{{ $websites->id }}" @if(isset($number->assigned_stores)) @if($number->assigned_stores->store_website_id == $websites->id) selected @endif @endif>{{ $websites->title }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    <td colspan="1">
                                        <label>Workspace:</label>
                                        <div class="input-group">
                                            <select class="form-control change-workspace" id="workspace_sid_1">
                                                <option value="">Select Workspace</option>
                                                @if(isset($workspace))
                                                    @foreach($workspace as $wsp)
                                                        <option value="{{ $wsp->workspace_sid }}"@if($number->workspace_sid == $wsp->workspace_sid) selected @endif>{{ $wsp->workspace_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <label>Workflow:</label>
                                        <div class="input-group">
                                            <select class="form-control change-workflow" id="workflow_sid_1">
                                                @if(isset($workflow))
                                                    @foreach($workflow as $wsp)
                                                        <option value="{{ $wsp->workflow_sid }}" @if($number->workflow_sid == $wsp->workflow_sid) selected @endif>{{ $wsp->workflow_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    <td colspan="1">
                                        <label>Message when agent available</label>
                                        <input type="text" class="form-control" name="message_available" id="message_available_1" value="{{ @$number->assigned_stores->message_available }}"/>
                                    </td>
                                    <td colspan="2">
                                        <label>Message when agent not available</label>
                                        <input type="text" class="form-control" name="message_not_available" id="message_not_available_1" value="{{ @$number->assigned_stores->message_not_available }}"/>
                                    </td>
                                    <td colspan="1">
                                        <label>Message when agent is busy</label>
                                        <input type="text" class="form-control" name="message_busy" id="message_busy_1" value="{{ @ $number->assigned_stores->message_busy }}"/>
                                    </td>
                                    <td colspan="2">
                                        <label>Message when Working Hours is Over</label>
                                        <input type="text" class="form-control" name="end_work_message" id="end_work_message_1" value="{{ @ $number->assigned_stores->end_work_message }}"/>
                                    </td>
									
									<td colspan="2">
                                        <label>Category Menu Message</label>
                                        <input type="text" class="form-control" name="category_menu_message" id="category_menu_message_1" value="{{ @ $number->assigned_stores->category_menu_message }}"/>
                                    </td>
									
									<td colspan="2">
                                        <label>Sub Category Menu Message</label>
                                        <input type="text" class="form-control" name="sub_category_menu_message" id="sub_category_menu_message_1" value="{{ @ $number->assigned_stores->sub_category_menu_message }}"/>
                                    </td>
									
									
									<td colspan="2">
                                        <label>Message if Speech Response not available</label>
                                        <input type="text" class="form-control" name="speech_response_not_available_message" id="speech_response_not_available_message_1" value="{{ @ $number->assigned_stores->speech_response_not_available_message }}"/>
                                    </td>
									
                                    <td colspan="1">
                                        <button class="btn btn-sm btn-image save-number-to-store" id="save_1" data-number-id="{{ @ $number->id }}"><img src="/images/filled-sent.png" style="cursor: default;"></button>
                                    </td>

                                </tr>
                                <tr class="call_forwarding_1" style="display:none;">
                                    <td colspan="3">
                                        <label>Select Agent</label>
                                        <div class="input-group">
                                            <select class="form-control" id="agent_1">
                                                <option value="">Select agent</option> 
                                                @if(isset($customer_role_users))
                                                    @foreach($customer_role_users as $user)
														@if(isset($user->user))
															<option value="{{ $user->user->id }}">{{ $user->user->name }}</option>
														@endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    <td colspan="3">
                                        <button class="btn btn-sm btn-image call_forwarding_save" id="forward_1"><img src="/images/filled-sent.png" style="cursor: default;"></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <!-- WorkSpace Modal -->
    <div class="modal fade" id="workspaceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title">Twilio WorkSpace</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">                
                <div class="row">
					{{ html()->form('POST', route('twilio-work-space'))->id('save-workspace')->open() }}
						<div class="col-md-4">
							<input type="text" class="form-control" name="workspace_name" placeholder="Enter Workespace Name"/>
						</div>
						<div class="col-md-4">
							<input type="text" class="form-control" name="callback_url" placeholder="Enter Callback Name" />
						</div>
						<div class="col-md-4">
							<input type="hidden" name="account_id" value="{{ $account_id}}">
							<button type="submit" class="btn btn-secondary create_twilio_workspace">Create Twilio Workspace</button>
						</div>
					</form>
                </div>                

                <table class="table table-bordered table-hover mt-5">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Workspace Name</th>
                            <th scope="col" class="text-center">Workspace Sid</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($workspace as $key => $val)
                        <tr class="row_{{$val->id}}">
                            <td>{{$val->workspace_name}}</td>
                            <td>{{$val->workspace_sid}}</td>
                            <td><i style="cursor: pointer;" class="fa fa-trash delete_workspace" data-id="{{$val->id}}" aria-hidden="true"></i></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div> -->
            </div>
        </div>
    </div>

     <!-- Twilio priority-->
    <div class="modal fade" id="priorityModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title">Twilio Priority</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">                
                <div class="row">
                    <div class="col-md-3">
                        <input type="hidden" class="form-control account_id" id="account_id" name="account_id" value="{{ $account_id }}"/>
                        <input type="number" class="form-control priority_no" name="priority_no" placeholder="Priority Number"/>
                    </div>
                   
                    <div class="col-md-3">
                        <input type="text" class="form-control priority_name" name="priority_name" placeholder="Priority Name"/>
                    </div>

                    <a class="ml-2" >
                        <button type="button" class="btn btn-secondary create_twilio_priority">Create</button>
                    </a>
                </div>                

                <table class="table table-bordered table-hover mt-5">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Priority No</th>
                            <th scope="col" class="text-center">Priority Name</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center priority_list">
                        @if($priority)
                            @foreach($priority as $key => $val)
                            <tr class="priority_row_{{$val->id}}">
                                <td>{{$val->priority_no}}</td>
                                <td>{{$val->priority_name}}</td>
                                <td><i style="cursor: pointer;" class="fa fa-trash delete_twilio_priority" data-id="{{$val->id}}" aria-hidden="true"></i></td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                
            </div>
            </div>
        </div>
    </div>

    <!-- Worker Modal -->
    <div class="modal fade" id="workerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title">Twilio Worker</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">                
                <div class="row">
                    <div class="col-md-3">    
                        <select class="form-control worker_workspace_id" id="">
                            <option value="">Select Workspace</option>
                            @if(isset($workspace))
                                @foreach($workspace as $val)
                                    <option value="{{ $val->id }}" >{{ $val->workspace_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">                        
                        <select class="form-control worker_user_id" name="worker_user_id">
                            <option value="0">Select User</option>
                            @foreach($twilio_user_list as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <select class="form-control worker_priority" name="worker_priority" multiple>
                            @foreach($priority as $key => $val)
                            <option value="{{$val->priority_no}}">{{$val->priority_name}}</option>
                            @endforeach
                        </select>
                    </div>
                   
                    <div class="col-md-3">
                        <input type="text" class="form-control worker_phone" name="worker_phone" placeholder="Worker Phone Number"/>
                    </div>

                    <a class="ml-2" >
                        <button type="button" class="btn btn-secondary create_twilio_worker">Create</button>
                    </a>
                </div>                

                <table class="table table-bordered table-hover mt-5">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Workspace Name</th>
                            <th scope="col" class="text-center">Worker Name</th>
                            <th scope="col" class="text-center">Worker Phone</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center worker_list">
                        @if($worker)
                            @foreach($worker as $key => $val)
                            <tr class="worker_row_{{$val->id}}">
                                <td>{{$val->workspace_name}}</td>
                                <td>{{$val->worker_name}}</td>
                                <td>{{$val->worker_phone}}</td>
                                <td><i style="cursor: pointer;" class="fa fa-trash delete_worker" data-id="{{$val->id}}" aria-hidden="true"></i></td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                
            </div>
            </div>
        </div>
    </div>
	
	 <!-- Workflow Modal -->
    <div class="modal fade" id="workflowModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title">Twilio Workflow Model</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
					{{ html()->form('POST', route('create-twilio-workflow'))->id('create-twilio-workflow')->class('ajax-submit')->open() }}
						<div class="col-md-4">
						<label>Select Workspace</label>
							<select class="form-control workflow_workspace_id" name="workspace_id">
								<option value="">Select Workspace</option>
								@if(isset($workspace))
									@foreach($workspace as $val)
										<option value="{{ $val->id }}" >{{ $val->workspace_name }}</option>
									@endforeach
								@endif
							</select>
						</div>
						<div class="col-md-4">
						<label>Task Queue Name</label>
							<select class="form-control" name="task_queue" id="task_queue">
								   
							</select>
						</div>
						<div class="col-md-4">
						<label>Workflow Name</label>
							<input type="hidden" name="account_id" value="{{ $account_id}}">
							<input type="text" class="form-control " name="workflow_name" placeholder="Workflow Name"/>
						</div>
						<div class="col-md-4">
						<label>Fallback Assignment</label>
							<input type="text" class="form-control " name="fallback_assignment_callback_url" placeholder="Fallback Assignment Callback Url"/>
						</div>
						<div class="col-md-4">
						<label>Assignment Callback Url</label>
							<input type="text" class="form-control " name="assignment_callback_url" placeholder="Assignment Callback Url"/>
						</div>
						<div class="col-md-2">
                        <label>Task TimeOut</label>
                            <input type="number" class="form-control " name="task_timeout" value="300"/>
                        </div>
                        <div class="col-md-2">
                        <label>Worker Reservation TimeOut</label>
                            <input type="number" class="form-control " name="worker_reservation_timeout" value="120"/>
                        </div>
						<div class="Twilio_Workflow_Model_btn"><button type="submit" class="btn btn-secondary">Create</button></div>
					</form>
                </div>

                <table class="table table-bordered table-hover mt-5">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Workspace Name</th>
                            <th scope="col" class="text-center">Workflow Name</th>
                            <th scope="col" class="text-center">Fallback url</th>
                            <th scope="col" class="text-center">Callback url</th>
                            <th scope="col" class="text-center">Task TimeOut</th>
                            <th scope="col" class="text-center">Worker Reservation TimeOut</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center workflow_list">
                        @if($workflows)
                            @foreach($workflows as $key => $val)
                            <tr class="worker_row_{{$val->id}}">
                                <td>{{$val->workspace_name}}</td>
                                <td>{{$val->workflow_name}}</td>
                                <td>{{$val->fallback_assignment_callback_url}}</td>
                                <td>{{$val->assignment_callback_url}}</td>
                                <td>
                                    <input type="number" class="time-timeout-{{$val->id}}" value="{{$val->task_timeout}}">
                                </td>
                                <td>
                                    <input type="number" class="worker-reservation-timeout-{{$val->id}}" value="{{$val->worker_reservation_timeout}}">
                                </td>
                                <td>
                                    <i style="cursor: pointer;" class="fa fa-edit trigger-edit" data-route="{{route('edit-twilio-workflow')}}" data-id="{{$val->id}}" aria-hidden="true"></i>
                                    <i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-route="{{route('delete-twilio-workflow')}}" data-id="{{$val->id}}" aria-hidden="true"></i>
                                </td>
                           </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                
            </div>
            </div>
        </div>
    </div>
	
	 <!-- Worker Modal -->
    <div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title">Twilio Activities</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
					{{ html()->form('POST', route('create-twilio-activity'))->id('create-twilio-activity')->class('ajax-submit')->open() }}
						<div class="col-md-4">
							<select class="form-control worker_workspace_id" name="workspace_id">
								<option value="">Select Workspace</option>
								@if(isset($workspace))
									@foreach($workspace as $val)
										<option value="{{ $val->id }}" >{{ $val->workspace_name }}</option>
									@endforeach
								@endif
							</select>
						</div>
						
						<div class="col-md-3">
							<input type="text" class="form-control " name="activity_name" placeholder="Activity Name"/>
						</div>
						<div class="col-md-3">
							<input type="radio" name="availability" value="1" checked/> True
							<input type="radio" name="availability" value="0"/> False
							<input type="hidden" name="account_id" value="{{ $account_id}}">
						</div>
						<button type="submit" class="btn btn-secondary">Create</button>
					</form>
                </div>

                <table class="table table-bordered table-hover mt-5">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Workspace Name</th>
                            <th scope="col" class="text-center">Activity Name</th>
                            <th scope="col" class="text-center">Availabilty</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center activities_list">
                        @if($activities)
                            @foreach($activities as $key => $val)
                            <tr class="activity_row_{{$val->id}}">
                                <td>{{$val->workspace_name}}</td>
                                <td>{{$val->activity_name}}</td>
                                <td>{{$val->availability}}</td>
                                <td><i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-route="{{route('delete-twilio-activity')}}" data-id="{{$val->id}}" aria-hidden="true"></i></td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                
            </div>
            </div>
        </div>
    </div>

	<div class="modal fade" id="taskQueueModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title">Twilio Task Queue Model</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
					{{ html()->form('POST', route('create-twilio-task-queue'))->id('create-twilio-task-queue')->class('ajax-submit')->open() }}
						<div class="col-md-4">
						<label>Select Workspace</label>
							<select class="form-control task_queue_workspace_id" name="workspace_id">
								<option value="">Select Workspace</option>
								@if(isset($workspace))
									@foreach($workspace as $val)
										<option value="{{ $val->id }}" >{{ $val->workspace_name }}</option>
									@endforeach
								@endif
							</select>
						</div>
						<div class="col-md-4">
						<label>Task Queue Name</label>
							<input type="hidden" name="account_id" value="{{ $account_id}}">
							<input type="text" class="form-control " name="task_queue_name" placeholder="Task Queue Name"/>
						</div>
						<div class="col-md-4">
						<label>First In First Out</label>
							<select class="form-control " name="task_order">
								<option value="FIFO">First In First Out</option>
								<option value="LIFO">Last In First Out</option>
							</select>
						</div>
						<div class="col-md-4">
						<label>Select Reservation Activity</label>
							<select class="form-control " name="reservation_activity_id" id="reservation_activity_id">
								<option value="">Select Reservation Activity</option>
							</select>
						</div>
						<div class="col-md-4">
						<label>Select Reservation Activity</label>
							<select class="form-control " name="assignment_activity_id" id="assignment_activity_id">
								<option value="">Select Assignment Activity</option>
							</select>
						</div>
						<div class="col-md-4">
						<label>Quantity</label>
							<input type="number" class="form-control " name="max_reserved_workers" placeholder="Max reserved workers" value="1"/>
						</div>
						<div class="col-md-4">
						<label>QUEUE EXPRESSION</label>
							<input type="text" class="form-control " name="queue_expression" placeholder="QUEUE EXPRESSION"/>
						</div>
						<div class="Twilio_Task_Queue_btn"><button type="submit" class="btn btn-secondary">Create</button></div>
					</form>
                </div>

                <table class="table table-bordered table-hover mt-5">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Work space Name</th>
                            <th scope="col" class="text-center">Task Queue Name</th>
                            <th scope="col" class="text-center">Target Workers</th>
                            <th scope="col" class="text-center">Max Reserved Workers</th>
                            <th scope="col" class="text-center">Task Order</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center task_queue_list">
                        @if(isset($taskqueue))
                            @foreach($taskqueue as $key => $val)
                            <tr class="worker_row_{{$val->id}}">
								<td>{{$val->workspace_name}}</td>
                                <td>{{$val->task_queue_name}}</td>
                                <td>{{$val->target_workers}}</td>
                                <td>{{$val->max_reserved_workers}}</td>
                                <td>{{$val->task_order}}</td>
                                <td><i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-route="{{route('delete-twilio-task-queue')}}" data-id="{{$val->id}}" aria-hidden="true"></i></td>
                           </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                
            </div>
            </div>
        </div>
    </div>


    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
               50% 50% no-repeat;display:none;">
    </div>

    <script type="text/javascript">
	var baseUrl = window.location.origin+'/';
		$('.task_queue_workspace_id').on('change', function(e){
			var workspaceId = $(this).val();
			$.get(baseUrl+"fetch-activities/"+workspaceId, function(data){
				$('#reservation_activity_id option').remove();
				$('#assignment_activity_id option').remove();
				
				$.each(data[0], function( index, value ) {
					var option1 = '<option value="'+index+'">'+value+'</option>';
					$('#assignment_activity_id').append(option1);
				}); console.log(data[1]);
				$.each(data[1], function( index, value ) {
					var option2 = '<option value="'+index+'">'+value+'</option>';
					$('#reservation_activity_id').append(option2);
				});
				
			});
		});
		
		$('.workflow_workspace_id').on('change', function(e){
			var workspaceId = $(this).val();
			$.get(baseUrl+"fetch-task-queue/"+workspaceId, function(data){
				 $('#task_queue option').remove();
				
				$.each(data, function( index, value ) {
					var option2 = '<option value="'+index+'">'+value+'</option>';
					$('#task_queue').append(option2);
				});
				
			});
		});

        $('.trigger-edit').on('click', function(e) {
			var id = $(this).attr('data-id');
            var taskTimeout = $(`.time-timeout-${id}`).val();
            var workerReservationTimeout = $(`.worker-reservation-timeout-${id}`).val();
            e.preventDefault(); 
			var option = { _token: "{{ csrf_token() }}", id:id, taskTimeout:taskTimeout, workerReservationTimeout:workerReservationTimeout};
			var route = $(this).attr('data-route');
			$("#loading-image").show();
			$.ajax({
				type: 'post',
				url: route,
				data: option,
				success: function(response) {
					$("#loading-image").hide();
					if(response.code == 200) {
                        toastr["success"](response.message); 
                    }else if(response.statusCode == 500){
                        toastr["error"](response.message);
                    }
				},
				error: function(data) {
					$("#loading-image").hide();
					alert('An error occurred.');
				}
			});
        });
	
		$('.trigger-delete').on('click', function(e) {
			var id = $(this).attr('data-id');
			//console.log($(this).closest('tr')); $(this).closest('tr').remove();
			e.preventDefault(); 
			var option = { _token: "{{ csrf_token() }}", _method: 'DELETE', id:id };
			var route = $(this).attr('data-route');
			$("#loading-image").show();
			$.ajax({
				type: 'post',
				url: route,
				data: option,
				success: function(response) {
					$("#loading-image").hide();
					if(response.code == 200) {
						$(this).closest('tr').remove();
                        toastr["success"](response.message); 
                    }else if(response.statusCode == 500){
                        toastr["error"](response.message);
                    }
					setTimeout(function(){
                          location.reload();
                        }, 1000);
				},
				error: function(data) {
					$("#loading-image").hide();
					alert('An error occurred.');
				}
			}); 
		});
		
	
		$('#save-workspace').on('submit', function(e) { 
			e.preventDefault(); 
			$.ajax({
                type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: new FormData(this),
				processData: false,
				contentType: false,
				success: function(data) {
					if(data.statusCode == 500) { 
						toastr["error"](data.message);
					} else {
						toastr["success"](data.message);
						setTimeout(function(){
                          location.reload();
                        }, 1000);
					}
				},
				done:function(data) {
					console.log('success '+data);
				}
            });
		});
		
		$('.ajax-submit').on('submit', function(e) { 
			e.preventDefault(); 
			$.ajax({
                type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: new FormData(this),
				processData: false,
				contentType: false,
				success: function(data) { console.log(data);
					if(data.statusCode == 500) { 
						toastr["error"](data.message);
					} else {
						toastr["success"](data.message);
						if(data.type == "activityList") { 
							var response = data;
							var html = '<tr>';
                            html += '<td>'+response.data.workspace_name+'</td>';
                            html += '<td>'+response.data.activity_name+'</td>';
                            html += '<td>'+response.data.availability+'</td>';
                            html += '<td><i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-id="'+response.data.id+'" data-id="'+response.data.id+'" data-route="{{route('delete-twilio-activity')}}" aria-hidden="true"></i></td>';
                            html += '</tr>';
                            $('.activities_list').append(html);
						}else if(data.type == "workflowList") { 
							var response = data;
							var html = '<tr>';
                            html += '<td>'+response.data.workspace_name+'</td>';
                            html += '<td>'+response.data.workflow_name+'</td>';
                            html += '<td>'+response.data.fallback_assignment_callback_url+'</td>';
                            html += '<td>'+response.data.assignment_callback_url+'</td>';
                            html += '<td>'+response.data.task_timeout+'</td>';
                            html += '<td>'+response.data.worker_reservation_timeout+'</td>';
                            html += '<td><i style="cursor: pointer;" class="fa fa-edit trigger-edit" data-id="'+response.data.id+'" data-id="'+response.data.id+'" data-route="{{route('edit-twilio-workflow')}}" aria-hidden="true"></i>';
                            html += '<i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-id="'+response.data.id+'" data-id="'+response.data.id+'" data-route="{{route('delete-twilio-workflow')}}" aria-hidden="true"></i></td>';
                            html += '</tr>';
                            $('.workflow_list').append(html);
						}else if(data.type == "taskQueueList") { 
							var response = data;
							var html = '<tr>';
                            html += '<td>'+response.data.workspace_name+'</td>';
                            html += '<td>'+response.data.task_queue_name+'</td>';
                            html += '<td>'+response.data.target_workers+'</td>';
                            html += '<td>'+response.data.max_reserved_workers+'</td>';
                            html += '<td>'+response.data.task_order+'</td>';
                            html += '<td><i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-id="'+response.data.id+'" data-id="'+response.data.id+'" data-route="{{route('delete-twilio-task-queue')}}" aria-hidden="true"></i></td>';
                            html += '</tr>';
                            $('.task_queue_list').append(html);
						}
						setTimeout(function(){
                          location.reload();
                        }, 1000);
 
					}
				},
				done:function(data) {
					console.log('success '+data);
				}
            });
		});
		
        $(document).ready(function(){
            var counter_one, counter_two;
            $('.open_row').on("click", function(){
                var row_id = $(this).attr('id');
                if(counter_one == 1){
                    counter_one = 0;
                    $('.hidden_row_'+row_id).hide();
                }else{
                    counter_one = 1;
                    $('.hidden_row_'+row_id).show();
                }
                changeWorkflow($(this).attr('data-workspace-sid'), $(this).attr('data-workflow-sid'))
            });

            
			
            $('.save-number-to-store').on("click", function(){
                var pathname = window.location.pathname;

                path_arr = pathname.split('/');
                var credential_id = path_arr[path_arr.length-1];

                var selected_no = $(this).attr('id');
                selected_no = selected_no.split('_');
                var num_id = selected_no[1];
                $.ajax({
                    url: '{{ route('assign-number-to-store-website') }}',
                    method: 'POST',
                    data: {
                        '_token' : "{{ csrf_token() }}",
                        'twilio_number_id' : $(this).data('number-id'),
                        'store_website_id' : $('#store_website_'+num_id).val(),
                        'message_available' : $('#message_available_'+num_id).val(),
                        'message_not_available' : $('#message_not_available_'+num_id).val(),
                        'message_busy' : $('#message_busy_'+num_id).val(),
                        'end_work_message' : $('#end_work_message_'+num_id).val(),
                        'category_menu_message' : $('#category_menu_message_'+num_id).val(),
                        'sub_category_menu_message' : $('#sub_category_menu_message_'+num_id).val(),
                        'speech_response_not_available_message' : $('#speech_response_not_available_message_'+num_id).val(),
                        'credential_id' : credential_id,
                        "workspace_sid" :$('#workspace_sid_'+num_id).val(),
                        "workflow_sid" :$('#workflow_sid_'+num_id).val()
                    }
                }).done(function(response){
                    if(response.status == 1){
                        toastr['success'](response.message);
                        location.reload();
                    }else{
                        toastr['error'](response.message);

                    }
                    console.log(response);
                });
            });

            $('.call_forwarding').on("click", function(){
                var num_id = $(this).data('attr');
                $('.call_forwarding_'+num_id).show();
                if(counter_two == 1){
                    counter_two = 0;
                    $('.call_forwarding_'+num_id).hide();
                }else{
                    counter_two = 1;
                    $('.call_forwarding_'+num_id).show();
                }
                $('.call_forwarding_save').on("click", function(){
                    var agent_id = $('#agent_'+num_id).val();
                    if(agent_id == ''){
                        alert('Please select agent');
                    }
                    $.ajax({
                        url: '{{ route('manage-twilio-call-forward') }}',
                        method: 'POST',
                        data: {
                            '_token' : "{{ csrf_token() }}",
                            'twilio_account_id' : '{{ $account_id }}',
                            'twilio_number_id' : num_id,
                            'agent_id' : agent_id
                        }
                    }).done(function(response){
                        if(response.status == 1){
                            toastr['success'](response.message);
                        }else{
                            toastr['error'](response.message);
                        }
                    });
                });


            });

            $('.delete_workspace').on("click", function(){
                var id = $(this).attr('data-id');

                $.ajax({
                    url: "{{ route('delete-twilio-work-space') }}",
                    type: 'POST',
                    data : {
                        workspace_id: id,
                        _token : "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                    $("#loading-image").show();
                    },
                    success: function(response) {
                        $("#loading-image").hide();
                        $(".row_"+id).css("display", "none");
                        if(response.code == 200) {
                            toastr["success"](response.message);
                        }                       
                    },
                    error: function(response) {
                        $("#loading-image").hide();
                        toastr["error"]("Oops, something went wrong");
                    }
                });
                
            });

            $('.create_twilio_priority').on("click", function(){
                var priority_no = $('.priority_no').val();
                var priority_name = $('.priority_name').val();
                var account_id = $('#account_id').val();
                
                $.ajax({
                    url: "{{ route('create.twilio.priority') }}",
                    type: 'POST',
                    data : {
                        priority_no : priority_no,
                        priority_name : priority_name,
                        account_id : account_id,
                        _token : "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    },
                    success: function(response) {
                        $("#loading-image").hide();
                        if(response.statusCode == 200) {
                            toastr["success"](response.message);
                            var html = '<tr class="priority_row_'+response.data.id+'">';
                            html += '<td>'+response.data.priority_no+'</td>';
                            html += '<td>'+response.data.priority_name+'</td>';
                            html += '<td><i style="cursor: pointer;" class="fa fa-trash delete_twilio_priority" data-id="'+response.data.id+'" aria-hidden="true"></i></td>';
                            html += '</tr>';
                            $('.priority_list').append(html);

                        }else if(response.statusCode == 500){
                            toastr["error"](response.message);
                        }
                        
                    },
                    error: function(response) {
                        $("#loading-image").hide();
                        toastr["error"]("Oops, something went wrong");
                    }
                });
                
            });

            $('.create_twilio_worker').on("click", function(){
                var workspace_id = $('.worker_workspace_id').val();
                // var worker_name = $('.twilio_worker_name').val();
                var user_id = $('.worker_user_id').val();
                var worker_phone = $('.worker_phone').val();
                var worker_priority = $('.worker_priority').val();

                $.ajax({
                    url: "{{ route('create-twilio-worker') }}",
                    type: 'POST',
                    data : {
                        workspace_id: workspace_id,
                        // worker_name: worker_name,
                        user_id: user_id,
                        worker_phone: worker_phone,
                        worker_priority: worker_priority,
                        account_id: '{{ $account_id }}',
                        _token : "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                    $("#loading-image").show();
                    },
                    success: function(response) {
                        $("#loading-image").hide();

                        if(response.statusCode == 200) {
                            toastr["success"](response.message);

                            var html = '<tr>';
                            html += '<td>'+response.data.workspace_name+'</td>';
                            html += '<td>'+response.data.worker_name+'</td>';
                            html += '<td>'+response.data.worker_phone+'</td>';
                            html += '<td><i style="cursor: pointer;" class="fa fa-trash delete_worker" data-id="'+response.data.id+'" aria-hidden="true"></i></td>';
                            html += '</tr>';
                            $('.worker_list').append(html);

                        }else if(response.statusCode == 500){
                            toastr["error"](response.message);
                        }
                        
                    },
                    error: function(response) {
                        $("#loading-image").hide();
                        toastr["error"]("Oops, something went wrong");
                    }
                });
                
            });

            $(".change-workspace").on("change", function() {
                changeWorkflow($(this).val(), null)
            })

            function changeWorkflow(workspace, workflow) {
                $.ajax({
                    url: "{{ route('get-workflow-list') }}",
                    type: 'POST',
                    data : {
                        _token :  "{{ csrf_token() }}",
                        workspace_sid : workspace,
                    },
                    success: (res) => {
                        let html = '<option>Select Workflow</option>';
                        res.workflows.forEach((item) => {
                            html += `<option value="${item.workflow_sid}" ${item.workflow_sid == workflow ? 'selected' : ''}>${item.workflow_name}</option>`
                        })
                        $('.change-workflow').html(html)
                    }
                })
            }

            $('.delete_worker').on("click", function(){
                var id = $(this).attr('data-id');

                $.ajax({
                    url: "{{ route('delete-twilio-worker') }}",
                    type: 'POST',
                    data : {
                        worker_id: id,
                        _token : "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                    $("#loading-image").show();
                    },
                    success: function(response) {
                        $("#loading-image").hide();

                        $(".worker_row_"+id).css("display", "none");

                        if(response.code == 200) {
                            toastr["success"](response.message);
                        }
                        
                    },
                    error: function(response) {
                        $("#loading-image").hide();
                        toastr["error"]("Oops, something went wrong");
                    }
                });
                
            });

            $('.delete_twilio_priority').on("click", function(){
                var id = $(this).attr('data-id');

                $.ajax({
                    url: "{{ route('delete.twilio.priority') }}",
                    type: 'POST',
                    data : {
                        priority_id: id,
                        _token : "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                    $("#loading-image").show();
                    },
                    success: function(response) {
                        $("#loading-image").hide();

                        $(".priority_row_"+id).css("display", "none");

                        if(response.code == 200) {
                            toastr["success"](response.message);
                        }
                        
                    },
                    error: function(response) {
                        $("#loading-image").hide();
                        toastr["error"]("Oops, something went wrong");
                    }
                });
                
            });

        });
    </script>
@endsection