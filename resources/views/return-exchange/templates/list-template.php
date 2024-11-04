<script type="text/x-jsrender" id="template-result-block">
	<div class="page-template-{{:page}} table-responsive">
		<table class="table table-bordered" style="">
		    <thead>
		      <tr>
		      	<?php if (! empty($dynamicColumnsToShowPostman)) { ?>
					<th style="width:3%;"><input type="checkbox" class="select-all-records"></th>
					<?php if (! in_array('ID', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:5%;">ID</th>
					<?php } ?>		
					<?php if (! in_array('Customer', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:8%;">Customer</th>
					<?php } ?>	
					<?php if (! in_array('Product', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:5%;">Product</th>
					<?php } ?>	
					<?php if (! in_array('Website', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:5%;">Website</th>
					<?php } ?>	
					<?php if (! in_array('Type', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:5%;">Type</th>
					<?php } ?>
					<?php if (! in_array('Refund', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:5%;">Refund</th>
					<?php } ?>
					<?php if (! in_array('Refund Reason', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:5%;">Refund Reason</th>
					<?php } ?>
					<?php if (! in_array('Status', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:15%;">Status</th>
					<?php } ?>
					<?php if (! in_array('Pickup Address', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:5%;">Pickup Address</th>
					<?php } ?>
					<?php if (! in_array('DOR', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:6%;">DOR</th>
					<?php } ?>
					<?php if (! in_array('DOI', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:6%;">DOI</th>
					<?php } ?>
					<?php if (! in_array('DOD', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:6%;">DOD</th>
					<?php } ?>
					<?php if (! in_array('Credited', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:6%;">Credited</th>
					<?php } ?>
					<?php if (! in_array('Est Refund / Ex. date', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:14%;">Est Refund / Ex. date</th>
					<?php } ?>
					<?php if (! in_array('Remarks', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:5%;">Remarks</th>
					<?php } ?>
					<?php if (! in_array('Created At', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:5%;">Created At</th>
					<?php } ?>
					<?php if (! in_array('Action', $dynamicColumnsToShowPostman)) { ?>
						<th style="width:3%;">Action</th>
					<?php } ?>
				<?php } else { ?>
					<th style="width:3%;"><input type="checkbox" class="select-all-records"></th>
					<th style="width:5%;">ID</th>
					<th style="width:8%;">Customer</th>
					<th style="width:5%;">Product</th>
					<th style="width:5%;">Website</th>
					<th style="width:5%;">Type</th>
					<th style="width:5%;">Refund</th>
					<th style="width:5%;">Refund Reason</th>
					<th style="width:15%;">Status</th>
					<!-- <th>Change Status</th> -->
					<th style="width:5%;">Pickup Address</th>
					<!-- <th style="width:6%;">Refund details</th> -->
					<th style="width:6%;">DOR</th>
					<th style="width:6%;">DOI</th>
					<th style="width:6%;">DOD</th>
					<th style="width:6%;">Credited</th>
					<th style="width:14%;">Est Refund / Ex. date</th>
					<th style="width:5%;">Remarks</th>
					<th style="width:5%;">Created At</th>
					<th style="width:3%;">Action</th>
				<?php } ?>
		      </tr>
		    </thead>
		    <tbody>
	    		<?php if (! empty($dynamicColumnsToShowPostman)) { ?>
    				{{props data ~oslist = order_status_list}}
						<tr style="background-color: {{:prop.return_exchange_color}} !important;">
							<td><input class="select-id-input" type="checkbox" name="ids[]" value="{{:prop.id}}"></td>

							<?php if (! in_array('ID', $dynamicColumnsToShowPostman)) { ?>
								<td>{{:prop.id}}</td>
							<?php } ?>

							<?php if (! in_array('Customer', $dynamicColumnsToShowPostman)) { ?>
								<td class="expand-row-msg" data-name="customer" data-id="{{:prop.id}}">
									<span class="show-short-customer-{{:prop.id}}">{{:~trimlength(prop.customer_name, 10)}}</span>
									<span class="show-full-customer-{{:prop.id}} hidden">{{:prop.customer_name}}</span>
								</td>
							<?php } ?>

							<?php if (! in_array('Product', $dynamicColumnsToShowPostman)) { ?>
								<td class="expand-row-msg" data-name="product" data-id="{{:prop.id}}">
									<span class="show-short-product-{{:prop.id}}">{{:~trimlength(prop.name, 4)}}</span>
									<span class="show-full-product-{{:prop.id}} hidden">{{:prop.name}}</span>
								</td>
							<?php } ?>

							<?php if (! in_array('Website', $dynamicColumnsToShowPostman)) { ?>
								<td class="expand-row-msg" data-name="website" data-id="{{:prop.id}}">
									<span class="show-short-website-{{:prop.id}}">{{:~trimlength(prop.website, 10)}}</span>
									<span class="show-full-website-{{:prop.id}} hidden">{{:prop.website}}</span>
								</td>
							<?php } ?>

							<?php if (! in_array('Type', $dynamicColumnsToShowPostman)) { ?>
								<td>{{:prop.type}}</td>
							<?php } ?>

							<?php if (! in_array('Refund', $dynamicColumnsToShowPostman)) { ?>
								<td>{{:prop.refund_amount}}</td>
							<?php } ?>

							<?php if (! in_array('Refund Reason', $dynamicColumnsToShowPostman)) { ?>
								<td class="expand-row-msg" data-name="reason" data-id="{{:prop.id}}">
									<span class="show-short-reason-{{:prop.id}}">{{:~trimlength(prop.reason_for_refund, 10)}} at {{:prop.date_of_refund_formated}} </span>
									<span class="show-full-reason-{{:prop.id}} hidden"><span style="word-break:break-all;">{{:prop.reason_for_refund}}</span></span>
								</td>
							<?php } ?>

							<?php if (! in_array('Status', $dynamicColumnsToShowPostman)) { ?>
								<td class="expand-row-msg" data-name="statusName" data-id="{{:prop.id}}" >
									<div class="form-group" style="margin-bottom:0px;">
										<div class="pt-2" style="display:flex;">
											<div  class='input-group pl-2'>
												<select name="return_exchange_status" data-old_status_name="{{:prop.status_name}}" data-old_status_id="{{:prop.status}}" data-id="{{:prop.id}}" class="form-control return_exchange_status" style="width: 100%;">
												{{for ~oslist ~selectedStatus = prop.status}}
												<option {{if ~selectedStatus == id}} selected="selected" {{/if}} value="{{:id}}">{{:status_name}}</option>
												{{/for}}
												</select>
											</div>

											<div style="display:flex">
												<button style="padding-right:0px;" type="button" class="btn btn-xs return_exchange_status_info mr-2 mt-1" title="Show Date History" data-id="{{>prop.id}}"><i class="fa fa-info-circle"></i></button>
											</div>
										</div>
									</div>
								</td>
							<?php } ?>

							<?php if (! in_array('Pickup Address', $dynamicColumnsToShowPostman)) { ?>
								<td class="expand-row-msg" data-name="pickupAdd" data-id="{{:prop.id}}">
									<span class="show-short-pickupAdd-{{:prop.id}}">{{:~trimlength(prop.pickup_address, 10)}}</span>
									<span class="show-full-pickupAdd-{{:prop.id}} hidden"><span style="word-break:break-all;">{{:prop.pickup_address}}</span></span>
								</td>
							<?php } ?>

							<?php if (! in_array('DOR', $dynamicColumnsToShowPostman)) { ?>
								<td class="expand-row-msg" data-name="DOR" data-id="{{:prop.id}}">
									<span class="show-short-DOR-{{:prop.id}}">{{:~trimlength(prop.date_of_request_formated, 5)}}</span>
									<span class="show-full-DOR-{{:prop.id}} hidden">{{:prop.date_of_request_formated}}</span>
								</td>
							<?php } ?>

							<?php if (! in_array('DOI', $dynamicColumnsToShowPostman)) { ?>
								<td class="expand-row-msg" data-name="DOI" data-id="{{:prop.id}}">
									<span class="show-short-DOI-{{:prop.id}}">{{:~trimlength(prop.date_of_issue_formated, 5)}}</span>
									<span class="show-full-DOI-{{:prop.id}} hidden">{{:prop.date_of_issue_formated}}</span>
								</td>
							<?php } ?>

							<?php if (! in_array('DOD', $dynamicColumnsToShowPostman)) { ?>
								<td class="expand-row-msg" data-name="DOI" data-id="{{:prop.id}}">
									<span class="show-short-DOD-{{:prop.id}}">{{:~trimlength(prop.dispatch_date_formated, 5)}}</span>
									<span class="show-full-DOD-{{:prop.id}} hidden">{{:prop.dispatch_date_formated}}</span>
								</td>
							<?php } ?>

							<?php if (! in_array('Credited', $dynamicColumnsToShowPostman)) { ?>
								<td class="expand-row" data-id="{{>prop.id}}">
									{{if prop.credited}} Yes {{else}} No {{/if}}
								</td>
							<?php } ?>

							<?php if (! in_array('Est Refund / Ex. date', $dynamicColumnsToShowPostman)) { ?>
								<td style="padding:1px;">
									<div class="form-group" style="margin-bottom:0px;">
										<div class="pt-2" style="display:flex;">
											<div  class='input-group pl-2'>
												<input style="min-width: 30px;" placeholder="E.Date" value="{{>prop.est_completion_date}}" type="text" class="form-control estimate-date" name="estimate_date_{{>prop.id}}" data-id="{{>prop.id}}" id="estimate_date_{{>prop.id}}">
											</div>

											<div style="display:flex">
												<button style="padding: 0px;" class="btn btn-sm btn-image estimate-date-submit ml-2" data-id="{{>prop.id}}"><img src="images/filled-sent.png" style="cursor: nwse-resize;"></button>
												<button style="padding-right:0px;" type="button" class="btn btn-xs show-date-history mr-2 mt-1" title="Show Date History" data-id="{{>prop.id}}"><i class="fa fa-info-circle"></i></button>
											</div>
										</div>
									</div>
								</td>
							<?php } ?>

							<?php if (! in_array('Remarks', $dynamicColumnsToShowPostman)) { ?>
								<td class="expand-row-msg" data-name="remarks" data-id="{{:prop.id}}">
									<span class="show-short-remarks-{{:prop.id}}">{{:~trimlength(prop.remarks, 5)}}</span>
									<span class="show-full-remarks-{{:prop.id}} hidden">{{:prop.remarks}}</span>
								</td>
							<?php } ?>

							<?php if (! in_array('Created At', $dynamicColumnsToShowPostman)) { ?>
								<td>{{:prop.created_at_formated}}</td>
							<?php } ?>

							<?php if (! in_array('Action', $dynamicColumnsToShowPostman)) { ?>
								<td>
									<button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{>prop.id}}')"><i class="fa fa-arrow-down"></i></button>
								</td>
							<?php } ?>
						</tr>

						<?php if (! in_array('Action', $dynamicColumnsToShowPostman)) { ?>
							<tr class="action-btn-tr-{{>prop.id}} d-none">
								<td>Action</td>
								<td colspan="17">
									<div class="cls_action_btn">
									<button type="button" class="btn re-generate-coupon no_pd" data-id="{{>prop.id}}" title="re-generate coupon"><i class="fa fa-gift"></i></button>
									<button type="button" class="btn btn-delete-template no_pd" onClick='return confirm("Are you sure you want to delete this request ?")' data-id="{{>prop.id}}"><img width="15px" src="/images/delete.png"></button>
									<button type="button" class="btn btn-edit-template no_pd" data-id="{{>prop.id}}"><img width="15px" src="/images/edit.png"></button>
									<button type="button" class="btn btn-history-template no_pd" data-id="{{>prop.id}}" ><img width="15px" src="/images/list-128x128.png"></button>
									<button type="button" class="btn send-email-to-customer no_pd" data-id="{{>prop.customer_id}}"><i class="fa fa-envelope-square"></i></button>
									{{if prop.product_id}}
									<button type="button" class="btn show-product no_pd" data-id="{{>prop.product_id}}"><i class="fa fa-product-hunt"></i></button>
									<button type="button" data-id="{{>prop.product_id}}" class="btn btn-product-info-template no_pd"><img width="15px" src="/images/view.png"></button>
									{{/if}}
									{{if !prop.credited}}
									<button type="button" data-id="{{>prop.id}}" class="btn create-update-refund no_pd" title="Create or update refund"><i class="fa fa-exchange"></i></button>
									{{/if}}
									<button type="button" class="btn resend-confirmation-email no_pd" data-id="{{>prop.customer_id}}">
									<i class="fa fa-paper-plane" aria-hidden="true"></i>
									</button>
									<button type="button" class="btn resend-refund-pdf-download no_pd" data-id="{{>prop.id}}">
									<i class="fa fa-file-pdf-o" aria-hidden="true"></i>
									</button>
									</div>
								</td>
							</tr>
						<?php } ?>
					{{/props}}
				<?php } else { ?>
					{{props data ~oslist = order_status_list}}
						<tr style="background-color: {{:prop.return_exchange_color}} !important;">
							<td><input class="select-id-input" type="checkbox" name="ids[]" value="{{:prop.id}}"></td>

							<td>{{:prop.id}}</td>

							<td class="expand-row-msg" data-name="customer" data-id="{{:prop.id}}">
								<span class="show-short-customer-{{:prop.id}}">{{:~trimlength(prop.customer_name, 10)}}</span>
								<span class="show-full-customer-{{:prop.id}} hidden">{{:prop.customer_name}}</span>
							</td>

							<td class="expand-row-msg" data-name="product" data-id="{{:prop.id}}">
								<span class="show-short-product-{{:prop.id}}">{{:~trimlength(prop.name, 4)}}</span>
								<span class="show-full-product-{{:prop.id}} hidden">{{:prop.name}}</span>
							</td>

							<td class="expand-row-msg" data-name="website" data-id="{{:prop.id}}">
								<span class="show-short-website-{{:prop.id}}">{{:~trimlength(prop.website, 10)}}</span>
								<span class="show-full-website-{{:prop.id}} hidden">{{:prop.website}}</span>
							</td>

							<td>{{:prop.type}}</td>

							<td>{{:prop.refund_amount}}</td>

							<td class="expand-row-msg" data-name="reason" data-id="{{:prop.id}}">
								<span class="show-short-reason-{{:prop.id}}">{{:~trimlength(prop.reason_for_refund, 10)}} at {{:prop.date_of_refund_formated}} </span>
								<span class="show-full-reason-{{:prop.id}} hidden"><span style="word-break:break-all;">{{:prop.reason_for_refund}}</span></span>
							</td>

							<td class="expand-row-msg" data-name="statusName" data-id="{{:prop.id}}" >
								<div class="form-group" style="margin-bottom:0px;">
									<div class="pt-2" style="display:flex;">
										<div  class='input-group pl-2'>
											<select name="return_exchange_status" data-old_status_name="{{:prop.status_name}}" data-old_status_id="{{:prop.status}}" data-id="{{:prop.id}}" class="form-control return_exchange_status" style="width: 100%;">
											{{for ~oslist ~selectedStatus = prop.status}}
											<option {{if ~selectedStatus == id}} selected="selected" {{/if}} value="{{:id}}">{{:status_name}}</option>
											{{/for}}
											</select>
										</div>

										<div style="display:flex">
											<button style="padding-right:0px;" type="button" class="btn btn-xs return_exchange_status_info mr-2 mt-1" title="Show Date History" data-id="{{>prop.id}}"><i class="fa fa-info-circle"></i></button>
										</div>
									</div>
								</div>
							</td>

							<td class="expand-row-msg" data-name="pickupAdd" data-id="{{:prop.id}}">
								<span class="show-short-pickupAdd-{{:prop.id}}">{{:~trimlength(prop.pickup_address, 10)}}</span>
								<span class="show-full-pickupAdd-{{:prop.id}} hidden"><span style="word-break:break-all;">{{:prop.pickup_address}}</span></span>
							</td>

							<td class="expand-row-msg" data-name="DOR" data-id="{{:prop.id}}">
								<span class="show-short-DOR-{{:prop.id}}">{{:~trimlength(prop.date_of_request_formated, 5)}}</span>
								<span class="show-full-DOR-{{:prop.id}} hidden">{{:prop.date_of_request_formated}}</span>
							</td>
							
							<td class="expand-row-msg" data-name="DOI" data-id="{{:prop.id}}">
								<span class="show-short-DOI-{{:prop.id}}">{{:~trimlength(prop.date_of_issue_formated, 5)}}</span>
								<span class="show-full-DOI-{{:prop.id}} hidden">{{:prop.date_of_issue_formated}}</span>
							</td>
							
							<td class="expand-row-msg" data-name="DOI" data-id="{{:prop.id}}">
								<span class="show-short-DOD-{{:prop.id}}">{{:~trimlength(prop.dispatch_date_formated, 5)}}</span>
								<span class="show-full-DOD-{{:prop.id}} hidden">{{:prop.dispatch_date_formated}}</span>
							</td>

							<td class="expand-row" data-id="{{>prop.id}}">
								{{if prop.credited}} Yes {{else}} No {{/if}}
							</td>

							<td style="padding:1px;">
								<div class="form-group" style="margin-bottom:0px;">
									<div class="pt-2" style="display:flex;">
										<div  class='input-group pl-2'>
											<input style="min-width: 30px;" placeholder="E.Date" value="{{>prop.est_completion_date}}" type="text" class="form-control estimate-date" name="estimate_date_{{>prop.id}}" data-id="{{>prop.id}}" id="estimate_date_{{>prop.id}}">
										</div>

										<div style="display:flex">
											<button style="padding: 0px;" class="btn btn-sm btn-image estimate-date-submit ml-2" data-id="{{>prop.id}}"><img src="images/filled-sent.png" style="cursor: nwse-resize;"></button>
											<button style="padding-right:0px;" type="button" class="btn btn-xs show-date-history mr-2 mt-1" title="Show Date History" data-id="{{>prop.id}}"><i class="fa fa-info-circle"></i></button>
										</div>
									</div>
								</div>
							</td>

							<td class="expand-row-msg" data-name="remarks" data-id="{{:prop.id}}">
								<span class="show-short-remarks-{{:prop.id}}">{{:~trimlength(prop.remarks, 5)}}</span>
								<span class="show-full-remarks-{{:prop.id}} hidden">{{:prop.remarks}}</span>
							</td>

							<td>{{:prop.created_at_formated}}</td>

							<td>
								<button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{>prop.id}}')"><i class="fa fa-arrow-down"></i></button>
							</td>
						</tr>
						<tr class="action-btn-tr-{{>prop.id}} d-none">
							<td>Action</td>
							<td colspan="17">
								<div class="cls_action_btn">
								<button type="button" class="btn re-generate-coupon no_pd" data-id="{{>prop.id}}" title="re-generate coupon"><i class="fa fa-gift"></i></button>
								<button type="button" class="btn btn-delete-template no_pd" onClick='return confirm("Are you sure you want to delete this request ?")' data-id="{{>prop.id}}"><img width="15px" src="/images/delete.png"></button>
								<button type="button" class="btn btn-edit-template no_pd" data-id="{{>prop.id}}"><img width="15px" src="/images/edit.png"></button>
								<button type="button" class="btn btn-history-template no_pd" data-id="{{>prop.id}}" ><img width="15px" src="/images/list-128x128.png"></button>
								<button type="button" class="btn send-email-to-customer no_pd" data-id="{{>prop.customer_id}}"><i class="fa fa-envelope-square"></i></button>
								{{if prop.product_id}}
								<button type="button" class="btn show-product no_pd" data-id="{{>prop.product_id}}"><i class="fa fa-product-hunt"></i></button>
								<button type="button" data-id="{{>prop.product_id}}" class="btn btn-product-info-template no_pd"><img width="15px" src="/images/view.png"></button>
								{{/if}}
								{{if !prop.credited}}
								<button type="button" data-id="{{>prop.id}}" class="btn create-update-refund no_pd" title="Create or update refund"><i class="fa fa-exchange"></i></button>
								{{/if}}
								<button type="button" class="btn resend-confirmation-email no_pd" data-id="{{>prop.customer_id}}">
								<i class="fa fa-paper-plane" aria-hidden="true"></i>
								</button>
								<button type="button" class="btn resend-refund-pdf-download no_pd" data-id="{{>prop.id}}">
								<i class="fa fa-file-pdf-o" aria-hidden="true"></i>
								</button>
								</div>
							</td>
						</tr>
					{{/props}}	
				<?php } ?> 
		    </tbody>
		</table>

	</div>
	{{:pagination}}
</script>
<script type="text/x-jsrender" id="return_exchange_status_info_list">
	<div class="modal-content">
		<div class="modal-body">
			<div class="col-md-12">
				<table class="table table-bordered">
				    <thead>
				      <tr>
				      	<th>Id</th>
				        <th>Status</th>
				        <th>Updated By</th>
				        <th>Created at</th>
				      </tr>
				    </thead>
				    <tbody>
				    	{{props data}}
					      <tr>
					      	<td>{{:prop.id}}</td>
					      	<td>{{:prop.status_name}}</td>
					      	<td>{{:prop.updatedby_name}}</td>
					      	<td>{{:prop.created_at}}</td>
					      </tr>
					    {{/props}}  
				    </tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div> 		
	</div>
</script>
<script type="text/x-jsrender" id="template-history-block">
	<div class="modal-content">
		<div class="modal-body">
			<div class="col-md-12">
				<table class="table table-bordered">
				    <thead>
				      <tr>
				      	<th>Id</th>
				        <th>Status</th>
				        <th>Comment</th>
				        <th>Updated By</th>
				        <th>Created at</th>
				      </tr>
				    </thead>
				    <tbody>
				    	{{props data}}
					      <tr>
					      	<td>{{:prop.id}}</td>
					      	<td>{{:prop.status}}</td>
					      	<td>{{:prop.comment}}</td>
					      	<td>{{:prop.user_name}}</td>
					      	<td>{{:prop.created_at}}</td>
					      </tr>
					    {{/props}}  
				    </tbody>
				</table>
			</div>
		</div> 		
	</div>
</script>

<script type="text/x-jsrender" id="date-history-block">
	<div class="modal-content">
		<div class="modal-body">
			<div class="col-md-12">
				<table class="table table-bordered">
				    <thead>
				      <tr>
				      	<th>Id</th>
				        <th>Old value</th>
				        <th>New value</th>
				        <th>Updated By</th>
				        <th>Created at</th>
				      </tr>
				    </thead>
				    <tbody>
				    	{{props data}}
					      <tr>
					      	<td>{{:prop.id}}</td>
					      	<td>{{:prop.old_value}}</td>
					      	<td>{{:prop.new_value}}</td>
					      	<td>{{:prop.user_name}}</td>
					      	<td>{{:prop.created_at}}</td>
					      </tr>
					    {{/props}}  
				    </tbody>
				</table>
			</div>
		</div> 		
	</div>
</script>


<script type="text/x-jsrender" id="template-edit-block">
	<div class="modal-content">
	<div class="modal-body">
	    <form action="/return-exchange/{{:data.return_exchange.id}}/update" method="POST" enctype="multipart/form-data" class="" id="return-exchange-update-form" data-reload='1'>
		    <?php echo csrf_field(); ?>
		    <div class="row">
		        <div class="col">
		            <div class="form-group">
		                <strong>Type&nbsp;:&nbsp;</strong>
		                <span><input type="radio" {{if data.return_exchange.type == "refund"}} checked="checked" {{/if}} name="type" value="refund" />Refund</span>
		                <span><input type="radio" {{if data.return_exchange.type == "exchange"}} checked="checked" {{/if}} name="type" value="exchange" />Exchange</span>
		            </div>
		        </div>
		    </div>
		    <div class="row refund-section" style="display: none">
		        <div class="col">
		            <div class="form-group">
		                <strong>Reason for refund&nbsp;:&nbsp;</strong>
		                <input type="text" class="form-control" value="{{:data.return_exchange.reason_for_refund}}" name="reason_for_refund"></textarea>
		            </div>
		        </div>
		        <div class="col">
		            <div class="form-group">
		                <strong>Refund Amount&nbsp;:&nbsp;</strong>
		                <input type="text" class="form-control" value="{{:data.return_exchange.refund_amount}}" name="refund_amount"></textarea>
		            </div>
		        </div>
		    </div>

		    <div class="row">
		        <div class="col">
		            <div class="form-group">
		                <strong>Status&nbsp;:&nbsp;</strong>
		                <select name="status" class="form-control select-multiple" style="width: 100%;">
		                    {{props data.status ~selectedStatus=data.return_exchange.status}}
		                        <option {{if selectedStatus == key}} selected="selected" {{/if}} value="{{>key}}">{{>prop}}</option>
		                    {{/props}}
		                </select>
		            </div>
		        </div>
		    </div>

		    <div class="row">
		        <div class="col">
		            <div class="form-group">
		                <strong>Pickup Address&nbsp;:&nbsp;</strong>
		                <textarea class="form-control" name="pickup_address">{{:data.return_exchange.pickup_address}}</textarea>
		            </div>
		        </div>
		    </div>

		    <div class="row">
		        <div class="col">
		            <div class="form-group">
		                <strong>Remarks&nbsp;:&nbsp;</strong>
		                <textarea class="form-control" name="remarks">{{:data.return_exchange.remarks}}</textarea>
		            </div>
		        </div>
		    </div>

		    <div class="row">
		        <div class="col">
		            <div class="form-group">
		                <strong>Send Message&nbsp;:&nbsp;</strong>
		                <span><input type="radio" name="send_message" value="1" />Yes</span>
		                <span><input type="radio" name="send_message" value="0" />No</span>
		            </div>
		        </div>
		    </div>

		    <div class="row">
		        <div class="col">
		            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		            <button type="submit" class="btn btn-secondary" id="btn-return-exchage-request">Submit</button>
		      	</div>
		    </div>
		</form>
	</div> 		
</div>
</script>
<script type="text/x-jsrender" id="template-product-block">
	<div class="modal-content">
		<div class="modal-body">
			<div class="col-md-12">
				<table class="table table-bordered">
				    <thead>
				      <tr>
						<th>Order number</th>
						<th>Brand</th>
						<th>Product Name</th>
						<th>Image</th>
						<th>Price</th>
				      </tr>
				    </thead>
				    <tbody>
				    	{{props data}}
					      <tr>
							<td>{{:prop.order_number}}</td>
							<td>{{:prop.product_brand}}</td>
							<td>{{:prop.product_name}}</td>
							<td><img width="30px" src="{{:prop.product_image}}"></td>
							<td>{{:prop.product_price}}</td>
					      </tr>
					    {{/props}}  
				    </tbody>
				</table>
			</div>
		</div> 		
	</div>
</script>
