		<table class="table table-bordered">
		    <thead>
		      <tr>
				<th id="sno" width="1%">
					<input type="checkbox" class="checkbox-select-all">
				</th>
				<th width="3%">ID</th>
				<th width="1%">Store Website</th>
				<th width="2%">Name</th>
				<th width="1%">Mobile</th>
				<th width="15%">Next Action</th>
				<th width="20%">Shortcuts</th>
				<th width="20%">Communication</th>
				<th width="1%">Actions</th>
		      </tr>
		    </thead>
		    <tbody>
				@foreach($items as $item)
				<tr>
					<td><input type="checkbox" name="items[]" value="{{$item->id}}" class="items-id"></td>
					<td>{{$item->id}}</td>
					<td>{{!empty($item->storeWebsite)?$item->storeWebsite->title:''}}</td>
					<td>{{$item->short_name}}</td>
					<td>{{ $item->phone }}</td>
					<td id="new_action_td h-100">
						<div class="row row_next_action">
							<div class="col-6 form-inline pr-0 m-0">
								<input type="text" name="add_next_action" placeholder="Add new Action" class="form-control add_next_action_txt w-75">
								<button class="btn btn-xs add_next_action_btn w-25 m-0"><i class="fa fa-plus"></i></button>
							</div>
							<div class="col-6 form-inline next_action_div pl-0 m-0">
								<select name="next_action" class="form-control next_action w-75" data-id="{{$item->id}}">
									<option value="">Select Action</option> 
										@foreach ($nextActionArr as $option) 
										<option {{ ($item->customer_next_action_id ==$option->id)?'selected':''}} value="{{$option->id}}">{{$option->name}}</option>
									@endforeach
								</select>
								<a class="btn btn-xs delete_next_action w-25"><i class="fa fa-trash"></i></a>
							</div>
						</div>
					</td>
					
					<td class="communication">			        	
						<div class="row pl-3 pr-3">
						    <div class="col-md-12">
						        <div class="row mb-1">
						            <div class="col-3 form-inline  p-0 m-0">
						                <input type="text" name="category_name" placeholder="Add New Category" class="form-control w-75 quick_category">
						                <button class="btn btn-xs quick_category_add w-25 m-0"><i class="fa fa-plus"></i></button>
						            </div>
						            <div class="col-3 form-inline p-0 m-0">
						                    <select name="quickCategory" class="form-control quickCategory w-75">
						                        <option value="">Select Category</option>
													@foreach($reply_categories as $category)
						                            <option value="{{$category->approval_leads}}" data-id="{{$category->id}}">{{$category->name}}</option>
						                        	@endforeach
						                    </select>
						                    <a class="btn btn-xs delete_category w-25"><i class="fa fa-trash"></i></a>
						            </div>
								
						            <div class="col-3 form-inline  p-0 m-0">
						                <input type="text" name="quick_comment" placeholder="Enter New Comment" class="form-control quick_comment w-75">
						                <button class="btn btn-xs quick_comment_add w-25 m-0"><i class="fa fa-plus"></i></button>
						            </div>
						            <div class="col-3 form-inline  p-0 m-0">
										<select name="quickComment" class="form-control quickComment w-75">
											<option value="">Select Reply</option>
										</select>
										<a class="btn btn-xs delete_quick_comment w-25"><i class="fa fa-trash"></i></a>
						            </div>
						        </div>
						    </div>
						    <div class="col-md-12 expand-row dis-none">
								<?php 
									$csrf_str='@csrf';
									$action="{{ route('instruction.store') }}";
									$customer_id='{{$customer->id}}';
									$settingShortCuts='{{$settingShortCuts["image_shortcut"]}}';
								?>
						        <form class="d-inline" action="<?php echo $action; ?>" method="POST">
						            <?php echo $csrf_str; ?>
						            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
						            <input type="hidden" name="instruction" value="Send images">
						            <input type="hidden" name="category_id" value="6">
						            <input type="hidden" name="assigned_to" value="<?php echo $settingShortCuts; ?>">
						            <button type="submit" class="btn btn-image quick-shortcut-button" title="Send Images"><img src="/images/attach.png"/></button>
						        </form>
								<form class="d-inline" action="<?php echo $action; ?>" method="POST">
								<?php
									$settingShortCuts='{{$settingShortCuts["price_shortcut"]}}';
								?>
						            <?php echo $csrf_str; ?>
						            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
						            <input type="hidden" name="instruction" value="Send price">
						            <input type="hidden" name="category_id" value="3">
						            <input type="hidden" name="assigned_to" value="<?php echo $settingShortCuts; ?>">

						            <button type="submit" class="btn btn-image quick-shortcut-button" title="Send Price"><img src="/images/price.png"/></button>
						        </form>

								<form class="d-inline" action="<?php echo $action; ?>" method="POST">
								<?php
									$user_settingShortCuts='{{$users_array[$settingShortCuts["call_shortcut"]]}} call this client';
									$settingShortCuts='{{$settingShortCuts["call_shortcut"]}}';
								?>
						            <?php echo $csrf_str; ?>
						            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
						            <input type="hidden" name="instruction" value="<?php echo $user_settingShortCuts; ?>">
						            <input type="hidden" name="category_id" value="10">
						            <input type="hidden" name="assigned_to" value="<?php echo $settingShortCuts; ?>">

						            <button type="submit" class="btn btn-image quick-shortcut-button" title="Call this Client"><img src="/images/call.png"/></button>
						        </form>

								<form class="d-inline" action="<?php echo $action; ?>" method="POST">
								<?php  $settingShortCuts='{{$settingShortCuts["screenshot_shortcut"]}}'; ?>
						            <?php echo $csrf_str; ?>
						            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
						            <input type="hidden" name="instruction" value="Attach image">
						            <input type="hidden" name="category_id" value="8">
						            <input type="hidden" name="assigned_to" value="<?php echo $settingShortCuts;  ?>">

						            <button type="submit" class="btn btn-image quick-shortcut-button" title="Attach Images"><img src="/images/upload.png"/></button>
						        </form>

								<form class="d-inline" action="<?php echo $action; ?>" method="POST">
								<?php  $settingShortCuts='{{ $settingShortCuts["screenshot_shortcut"] }}'; ?>
						            <?php echo $csrf_str; ?>
						            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
						            <input type="hidden" name="instruction" value="Attach screenshot">
						            <input type="hidden" name="category_id" value="12">
						            <input type="hidden" name="assigned_to" value="<?php echo $settingShortCuts; ?>">

						            <button type="submit" class="btn btn-image quick-shortcut-button" title="Attach Screenshot"><img src="/images/screenshot.png"/></button>
						        </form>

								<form class="d-inline" action="<?php echo $action; ?>" method="POST">
								<?php $settingShortCuts='{{ $settingShortCuts["details_shortcut"] }}'; ?>
						            <?php echo $csrf_str; ?>
						            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
						            <input type="hidden" name="instruction" value="Give details">
						            <input type="hidden" name="category_id" value="14">
						            <input type="hidden" name="assigned_to" value="<?php echo $settingShortCuts; ?>">

						            <button type="submit" class="btn btn-image quick-shortcut-button" title="Give Details"><img src="/images/details.png"/></button>
						        </form>

								<form class="d-inline" action="<?php echo $action; ?>" method="POST">
								<?php $settingShortCuts='{{ $settingShortCuts["purchase_shortcut"] }}'; ?>
						            <?php echo $csrf_str; ?>
						            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
						            <input type="hidden" name="instruction" value="Check for the Purchase">
						            <input type="hidden" name="category_id" value="7">
						            <input type="hidden" name="assigned_to" value="<?php echo $settingShortCuts; ?>">

						            <button type="submit" class="btn btn-image quick-shortcut-button" title="Check for the Purchase"><img src="/images/purchase.png"/></button>
						        </form>

								<form class="d-inline" action="<?php echo $action; ?>" method="POST">
								<?php $settingShortCuts='{{ $settingShortCuts["purchase_shortcut"] }}'; ?>
						            <?php echo $csrf_str; ?>
						            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
						            <input type="hidden" name="instruction" value="Please Show Client Chat">
						            <input type="hidden" name="category_id" value="13">
						            <input type="hidden" name="assigned_to" value="<?php echo $settingShortCuts; ?>">

						            <button type="submit" class="btn btn-image quick-shortcut-button" title="Show Client Chat"><img src="/images/chat.png"/></button>
						        </form>
						        <div class="d-inline">
						            <button type="button" class="btn btn-image btn-broadcast-send" data-id="<?php echo $customer_id; ?>">
						                <img src="/images/broadcast-icon.png"/>
						            </button>
						        </div>
						        <div class="d-inline">
									<?php $a_href='{{ route("customer.download.contact-pdf",[$customer->id]) }}';?>
						            <a href="<?php echo $a_href; ?>" target="_blank">
						              <button type="button" class="btn btn-image"><img src="/images/download.png" /></button>
						            </a>
						        </div>
						        <div class="d-inline">
						            <button type="button" class="btn btn-image send-instock-shortcut" data-id="<?php echo $customer_id; ?>">Send In Stock</button>
						        </div>
						        <div class="d-inline">
						            <button type="button" class="btn btn-image latest-scraped-shortcut" data-id="<?php echo $customer_id; ?>" data-toggle="modal" data-target="#categoryBrandModal" style="padding: 6px 0px !important">Send 20 Scraped</button>
						        </div>
						    </div>    
						</div>  
					</td>

					<td class="communication">
						<div class="btn-toolbar" role="toolbar">
							<div class="w-100">
								<textarea rows="1" class="form-control quick-message-field" name="message" placeholder="Message"></textarea>
							</div>
								<button class="btn btn-sm btn-xs send-message pull-left mt-2" data-customerid="{{$item->id}}">
									<i class="fa fa-paper-plane"></i>
								</button>
								<button type="button" class="btn btn-xs load-communication-modal pull-left mt-2" data-object="customer" data-limit="10" data-id="{{$item->id}}" data-is_admin="1" data-is_hod_crm="" data-load-type="text" data-all="1" title="Load messages">
									<i class="fa fa-comments-o"></i>
								</button>
							<div class="communication-div-{{$item->id}} pull-left" style="margin-top: 8px !important;">
								<span class="message-chat-txt" data-toggle="popover" data-placement="top" data-content="{{$item->message}}" data-original-title="" title="">{{$item->short_message}}</span>
							</div>
						</div>
					</td>
					<td><button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn({{$item->id}})"><i class="fa fa-arrow-down"></i></button></td>
				</tr>
				<tr class="action-btn-tr-{{$item->id}} d-none">
					<td></td>
					<td width="2%">Actions</td>
					<td colspan="6">
						<div class="btn-toolbar" role="toolbar">
							<button type="button" class="btn btn-xs load-communication-modal mr-1" data-object="customer" data-id="{{$item->id}}" data-attached="1" data-limit="10" data-load-type="images" data-all="1" data-is_admin="1" data-is_hod_crm="" title="Load Auto Images attacheds">
								<i class="fa fa-folder"></i>
							</button>
							<button type="button" class="btn btn-xs load-communication-modal mr-1" data-object="customer" data-id="{{$item->id}}" data-attached="1" data-load-type="pdf" data-all="1" data-is_admin="1" data-is_hod_crm="" title="Load PDF">
								<i class="fa fa-file"></i>
							</button>
							<button type="button" class="btn btn-xs load-communication-modal mr-1" data-object="customer" data-id="{{$item->id}}" data-attached="1" data-load-type="broadcast" data-all="1" data-is_admin="1" data-is_hod_crm="" title="Load Broadcast">
								<i class="fa fa-image"></i>
							</button>
							@if($item->do_not_disturb==1)
								<a class="btn btn-xs cls_dnt_btn do_not_disturb mr-1" href="javascript:;" data-id="{{$item->id}}" data-user-id="">
									<i class="fa fa-ban"></i>
								</a>
							@else
								<a class="btn btn-xs cls_dnt_btn do_not_disturb mr-1" href="javascript:;" data-id="{{$item->id}}" data-user-id="">
									<i class="fa fa-ban"></i>
								</a>
							@endif
							<a class="btn btn-xs create-customer-related-task mr-1" title="Task" href="javascript:;" data-id="{{$item->id}}" data-user-id="">
									<i class="fa fa-plus"></i>
								</a>
							<a class="btn btn-xs count-customer-tasks mr-1" title="Task Count" href="javascript:;" data-id="{{$item->id}}" data-user-id="">
								<i class="fa fa-clipboard"></i>
							</a>
							@if($item->in_w_list==1)
								<a class="btn btn-xs mr-1" href="javascript:;" data-id="{{$item->id}}" data-user-id="">
									<i class="fa fa-check-double"></i>
								</a>
							@endif
							<button type="button" class="btn btn-xs create-customer-ticket-modal mr-1" title="Create Ticket" data-toggle="modal" data-customer_id="{{$item->id}}" data-target="#create-customer-ticket-modal">
								<i class="fa fa-file"></i>
							</button>
							<button type="button" class="btn btn-xs show-customer-tickets-modal mr-1" title="Show Tickets" data-toggle="modal" data-customer_id="{{$item->id}}" data-target="#show-customer-tickets-modal">
								<i class="fa fa-tags"></i>
							</button>
							<a href="javascript:;" class="btn btn-xs add-chat-phrases mr-1" title="Create Question">
								<i class="fa fa-plus"></i>
								<a href="javascript:;" class="btn btn-xs latest-scraped-shortcut" data-toggle="modal" data-target="#categoryBrandModal" data-id="{{$item->id}}" title="Send Latest Scrapped">
									<i class="fa fa-paper-plane" aria-hidden="true"></i>
								</a>
							</a>
						</div>
					</td>
				</tr>
				@endforeach
		    </tbody>
		</table>


