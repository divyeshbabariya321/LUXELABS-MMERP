<input type="hidden" id="path_id" name="path_id" value="{{$flowPathId}}">
@if(!isset($con))  @php $con=0;@endphp @endif
@foreach($flowActions as $key=>$flowAction)
	@if($flowAction['type'] == 'Time Delay')
		<div class="col-md-12 cross cross_first border-bottom bg-light text-dark pt-3 pb-3  m-0 " condtion_{{$con.$flowAction["type"]}}>
			<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
			<div class="form-group row m-0">
				<div class="@if($con==1) action_con @else action_con_2  @endif one">
					<label  class="col-lg-2 col-form-label">Time Delay</label>
					<div class="col-lg-3">
						{{ html()->number("time_delay[" . $flowAction['id'] . "]", $flowAction['time_delay'])->class('form-control')->attribute('required', ) }}
						<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="Time Delay">
					</div>
				</div>
				<div class="@if($con==1) action_con @else action_con_2   @endif two">
					<label  class="col-lg-3 col-form-label">Time Delay Type</label>
					<div class="col-lg-3">
					{{ html()->select("time_delay_type[" . $flowAction['id'] . "]", ['days' => 'Days', 'hours' => 'Hours', 'minutes' => 'Minutes'], $flowAction['time_delay_type'])->class('form-control') }}
					</div>
				</div>
				<div class="col-lg-1 text-right pt-3">
					<i style="cursor: pointer;" class="fa fa-trash trigger-delete fa-lg" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
				</div>  
			</div>
		</div>
		@elseif($flowAction['type'] == 'Send Message') 
			@php $messageDetail = \App\Helpers\DevelopmentHelper::getFlowMessage($flowAction['id']); @endphp
			<div class="col-md-12 cross cross_sec border-bottom bg-white text-dark pt-3 pb-3  m-0">
				<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
				<div class="col-md-10  text-dark">
					<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="Send Message">
					<label><i class="fa fa-envelope"></i> Email  <a href="{{url('link_template')}}"></a></label>
					<div class="cross_sub_label">
						<label><i class="fa fa-envelope"></i> @if(isset($messageDetail) && $messageDetail['subject']) {{$messageDetail['subject']}} @else Email #1 Subject @endif <a href="{{url('link_template')}}"></a></label>
					</div>
				</div>
				<div class="col-md-2 cross_sec_remove pt-3 text-right">    
					<i class="fa fa-pencil-square-o fa-lg p-0" aria-hidden="true" onclick="showMessagePopup('{{$flowAction['id']}}')"></i>
					<i style="cursor: pointer;" class="fa fa-trash trigger-delete fa-lg cross_first_remove" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
				</div>
			</div>
		@elseif($flowAction['type'] == 'Whatsapp') 
			<div class="col-md-12 cross cross_first cross_task border-bottom bg-light text-dark pt-3 pb-3  m-0 " >
				<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
				<div class="form-group row m-0">
					<label  class="col-lg-3 col-form-label">Whatsapp Message</label>
					<div class="col-lg-8">
						{{ html()->text("message_title[" . $flowAction['id'] . "]", $flowAction['message_title'])->class('form-control')->required() }}
						<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="Whatsapp">
					</div>
					<div class="col-lg-1 text-right pt-3">
						<i style="cursor: pointer;" class="fa fa-trash fa-lg trigger-delete" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
					</div>
				</div>
			</div>
		@elseif($flowAction['type'] == 'SMS') 
			<div class="col-md-12 cross cross_first  border-bottom bg-light text-dark pt-3 pb-3  m-0" >
				<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
				<div class="form-group row m-0">
					<label  class="col-lg-3 col-form-label">SMS</label>
					<div class="col-lg-8">
						{{ html()->text("message_title[" . $flowAction['id'] . "]", $flowAction['message_title'])->class('form-control')->required() }}
						<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="SMS">
					</div>
					<div class="col-lg-1 text-right pt-3">
					<i style="cursor: pointer;" class="fa fa-trash fa-lg trigger-delete" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
					</div>
				</div>
			</div>
		@elseif($flowAction['type'] == 'Condition') 
		     
			<div class="col-md-12 {{$con}} cross cross_first yes_no_conditions bg-light pt-3 pb-3 m-0 " data-type="condition" id="collector_{{$flowAction['id']}}" data-action_id="{{$flowAction['id']}}">
				<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
				
				<div class="col-md-11 cross_first_label_time p-0 pl-3">
					{{ html()->select("condition[" . $flowAction['id'] . "]", ['' => 'Select Condition', 'customer has ordered before' => 'Customer has ordered before', 'check_if_pr_merged' => 'Check if PR merged', 'check_scrapper_error_logs' => 'Check If scrapper has errors', 'check_if_design_task_done' => 'Check If design task done', 'check_if_development_task_done' => 'Check If development task done'], $flowAction['condition'])->class('form-control condition_select')->required()->data('action_id', $flowAction['id']) }}
					<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="SMS">													
				</div>			
				<div class="col-md-1 cross_first_remove text-left pl-3 pt-3 p-0">
					<i style="cursor: pointer;" class="fa fa-trash fa-lg trigger-delete" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
				</div>
				
				<div class="col-md-12 erp_yes_no_condition actions mt-0"> 
					<div class="erp_yes_no_condition_inner p-1">
						@php $pathId =  \App\Helpers\DevelopmentHelper::getFlowPath($flowAction['id']); @endphp
						<div class="col-md-6 erp_yes_no_condition_left " style="border-right:1px solid black;" id="yes_{{$flowAction['id']}}" data-path_id="{{$pathId}}">
							<label class="mb-0">
								Yes
							</label>
							
							<!-- Action Icons : S -->
							<a href="javascript:void(0)" data-flowId="{{$flowAction->id}}" data-pathId="{{$pathId}}" data-action_type="Time Delay" data-action_id="{{$flowAction['id']}}" data-yes_no="yes" class="actions_inside_condition">
								<i class="fa fa-clock-o" aria-hidden="true" title="Add time delay"></i>
							</a>
							
							<a href="javascript:void(0)" data-flowId="{{$flowAction->id}}" data-pathId="{{$pathId}}" data-action_type="Send Message" data-action_id="{{$flowAction['id']}}" data-yes_no="yes" class="actions_inside_condition">
								<i class="fa fa-envelope-o" aria-hidden="true" title="Add email"></i>
							</a>

							<a href="javascript:void(0)" data-flowId="{{$flowAction->id}}" data-pathId="{{$pathId}}" data-action_type="Whatsapp" data-action_id="{{$flowAction['id']}}" data-yes_no="yes" class="actions_inside_condition">
								<i class="fa fa-whatsapp" aria-hidden="true" title="Add Whatsapp"></i>
							</a>	

							<a href="javascript:void(0)" data-flowId="{{$flowAction->id}}" data-pathId="{{$pathId}}" data-action_type="SMS" data-action_id="{{$flowAction['id']}}" data-yes_no="yes" class="actions_inside_condition">
								<i class="fa fa-comment" aria-hidden="true" title="Add sms"></i>
							</a>

							@if($con == 0)
							<a href="javascript:void(0)" data-flowId="{{$flowAction->id}}" data-pathId="{{$pathId}}" data-action_type="Condition" data-action_id="{{$flowAction['id']}}" data-yes_no="yes" class="actions_inside_condition">
								<i class="fa fa-cog" aria-hidden="true" title="Add condition"></i>
							</a>
							@endif				
							<!-- Action Icons : E -->
							
							@if($pathId != null)	
								@php 
									$actionDataYes = \App\Helpers\DevelopmentHelper::getActionData($pathId);
								@endphp
								@include('flow.actions', ['flowActions'=>$actionDataYes, 'flowPathId'=>$pathId, 'con'=>1])
							@endif
						</div>
						@php $pathId =  \App\Helpers\DevelopmentHelper::getFlowPath($flowAction['id'],'no'); @endphp
						<div class="col-md-6 erp_yes_no_condition_right " id="no_{{$flowAction['id']}}" data-path_id="{{$pathId}}">
							<label class="mb-0">
								No
							</label>

							<!-- Action Icons : S -->
							<a href="javascript:void(0)" data-flowId="{{$flowAction->id}}" data-pathId="{{$pathId}}" data-action_type="Time Delay" data-action_id="{{$flowAction['id']}}" data-yes_no="no" class="actions_inside_condition">
								<i class="fa fa-clock-o" aria-hidden="true" title="Add time delay"></i>
							</a>
							
							<a href="javascript:void(0)" data-flowId="{{$flowAction->id}}" data-pathId="{{$pathId}}" data-action_type="Send Message" data-action_id="{{$flowAction['id']}}" data-yes_no="no" class="actions_inside_condition">
								<i class="fa fa-envelope-o" aria-hidden="true" title="Add email"></i>
							</a>

							<a href="javascript:void(0)" data-flowId="{{$flowAction->id}}" data-pathId="{{$pathId}}" data-action_type="Whatsapp" data-action_id="{{$flowAction['id']}}" data-yes_no="no" class="actions_inside_condition">
								<i class="fa fa-whatsapp" aria-hidden="true" title="Add Whatsapp"></i>
							</a>	

							<a href="javascript:void(0)" data-flowId="{{$flowAction->id}}" data-pathId="{{$pathId}}" data-action_type="SMS" data-action_id="{{$flowAction['id']}}" data-yes_no="no" class="actions_inside_condition">
								<i class="fa fa-comment" aria-hidden="true" title="Add sms"></i>
							</a>

							@if($con == 0)
							<a href="javascript:void(0)" data-flowId="{{$flowAction->id}}" data-pathId="{{$pathId}}" data-action_type="Condition" data-action_id="{{$flowAction['id']}}" data-yes_no="no" class="actions_inside_condition">
								<i class="fa fa-cog" aria-hidden="true" title="Add condition"></i>
							</a>		
							@endif			
							<!-- Action Icons : E -->

							@if($pathId != null)	
							    @php	
									$actionDataNo = \App\Helpers\DevelopmentHelper::getActionData($pathId);
								@endphp
								@include('flow.actions', ['flowActions'=>$actionDataNo, 'flowPathId'=>$pathId, 'con'=>1])
							@endif
						</div>
					</div>
				</div>			
			</div>
		@endif	
	@endforeach