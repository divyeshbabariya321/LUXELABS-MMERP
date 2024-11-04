<?php
// die(var_dump($socialHistoryData[0]));
?>
@if(isset($socialHistoryData))
@foreach($socialHistoryData as $product)
<?php
?>
	<tr>
		<td>
			<img class="img-responsive " width="80" height="80" src="{{isset($product->thumb_image)?$product->thumb_image:''}}" alt="Not found...">
		</td>
		<td>
			{{isset($product->ad_name)?$product->ad_name:'-'}}
		</td>
		<td>
			{{isset($product->status)?$product->status:'-'}}
		</td>
		<td>
			{{isset($product->account_id)?$product->account_id:'-'}}
		</td>
		<td>
			{{isset($product->campaign_name)?$product->campaign_name:'-'}}
		</td>
		<td>
			{{isset($product->adset_name)?$product->adset_name:'-'}}
		</td>
		<td>
			{{isset($product->action_type)?$product->action_type:'-'}}
		</td>
		<td>
			{{isset($product->reach)?$product->reach:'-'}}
		</td>
		<td>
			{{isset($product->Impressions)?$product->Impressions:'-'}}
		</td>
		<td>
			{{isset($product->amount)?$product->amount:'-'}}
		</td>
		<td>
			{{isset($product->cost_p_result)?$product->cost_p_result:'-'}}
		</td>
		<td>
			{{isset($product->end_time)?$product->end_time:'-'}}
		</td>
		<td>
			{{isset($product->created_at)?$product->created_at:'-'}}
		</td>
		
	</tr>

@endforeach
@endif