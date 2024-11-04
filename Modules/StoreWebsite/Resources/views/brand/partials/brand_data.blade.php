<?php 
foreach($brands as $brand) { 
	if(request()->get('brd_store_website_id')){
		if(request()->get('no_brand')){
			if(isset($apppliedResult[$brand->id]) && in_array(request()->get('brd_store_website_id'), $apppliedResult[$brand->id])){
				continue;
			}
		}else{
			if(isset($apppliedResult[$brand->id]) && !in_array(request()->get('brd_store_website_id'), $apppliedResult[$brand->id])){
				continue;
			}
		}	
	} ?>

	<tr>
		@if(!empty($dynamicColumnsToShow))
			@if (!in_array('Id', $dynamicColumnsToShow))
				<td><?php echo $brand->id; ?></td>
			@endif

			@if (!in_array('Brand', $dynamicColumnsToShow))
				<td><a class="text-dark" target="_blank" href="{{ route('product-inventory.new') }}?brand[]={{ $brand->id }}">{{ $brand->name }}  ( {{ $brand->counts }} )</a></td>
			@endif

			@if (!in_array('Min Price', $dynamicColumnsToShow))
				<td><?php echo $brand->min_sale_price; ?></td>
			@endif

			@if (!in_array('Max Price', $dynamicColumnsToShow))
				<td><?php echo $brand->max_sale_price; ?></td>
			@endif

			<?php 
			foreach($storeWebsite as $swid => $sw) { 
				if(!in_array($swid, $dynamicColumnsToShow)){
					$checked = (isset($apppliedResult[$brand->id]) && in_array($swid, $apppliedResult[$brand->id])) ? "checked" : ""; ?>
					<td>
						<div style="display: flex; align-items: center; gap: 5px;">
						<input style="margin: 0;" id="<?php echo $brand->id.$swid; ?>" data-brand="<?php echo $brand->id; ?>" data-sw="<?php echo $swid; ?>" <?php echo $checked; ?> class="push-brand" type="checkbox" name="brand_website">
						<a href="javascript:;" data-href="{!! route('store-website.brand.history',['brand'=>$brand->id,'store'=>$swid]) !!}" class="log_history btn p-0">
							<i style="margin: 0 !important;" class="fa fa-info-circle icon-log-history" aria-hidden="true"></i>
						</a>
						<br>
						<span>
							@php $magentoStoreBrandId = $brand->storewebsitebrand($swid); @endphp
							{{ $magentoStoreBrandId ? $magentoStoreBrandId : '' }}
						</span>
						<div>
					</td>
				<?php 
				}
			} ?>
		@else 

			<td><?php echo $brand->id; ?></td>

			<td><a class="text-dark" target="_blank" href="{{ route('product-inventory.new') }}?brand[]={{ $brand->id }}">{{ $brand->name }}  ( {{ $brand->counts }} )</a></td>

			<td><?php echo $brand->min_sale_price; ?></td>

			<td><?php echo $brand->max_sale_price; ?></td>

			<?php 
			foreach($storeWebsite as $swid => $sw) { 
				$checked = (isset($apppliedResult[$brand->id]) && in_array($swid, $apppliedResult[$brand->id])) ? "checked" : ""; ?>
				<td>
					<div style="display: flex; align-items: center; gap: 5px;">
					<input style="margin: 0;" id="<?php echo $brand->id.$swid; ?>" data-brand="<?php echo $brand->id; ?>" data-sw="<?php echo $swid; ?>" <?php echo $checked; ?> class="push-brand" type="checkbox" name="brand_website">
					<a href="javascript:;" data-href="{!! route('store-website.brand.history',['brand'=>$brand->id,'store'=>$swid]) !!}" class="log_history btn p-0">
						<i style="margin: 0 !important;" class="fa fa-info-circle icon-log-history" aria-hidden="true"></i>
					</a>
					<br>
					<span>
						@php $magentoStoreBrandId = $brand->storewebsitebrand($swid); @endphp
						{{ $magentoStoreBrandId ? $magentoStoreBrandId : '' }}
					</span>
					<div>
				</td>
			<?php 
			} ?>

		@endif
	</tr>
<?php 
} ?>