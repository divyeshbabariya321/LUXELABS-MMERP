
    @foreach ($leadOrder_array as $key => $leadOrder) 
    
        <tr class="">
            <td>{{ isset($leadOrder['id'])? $leadOrder['id'] : '' }}</td>
            <td class="expand-row-msg" data-name="customerName" data-id="{{$key.'-'.$leadOrder['id']}}">
                <span class="show-short-customerName-{{$key.'-'.$leadOrder['id']}}">{{ isset($leadOrder['customer_name'])? Str::limit($leadOrder['customer_name'], 25, '..') : '' }}</span>
                <span style="word-break:break-all;" class="show-full-customerName-{{$key.'-'.$leadOrder['id']}} hidden">{{ isset($leadOrder['customer_name'])? $leadOrder['customer_name'] : '' }}</span>
            </td>
            <td>{{ isset($leadOrder['order_date'])? Carbon\Carbon::parse($leadOrder['order_date'])->format('d-m-Y') : '' }}</td>
            <td>{{ isset($leadOrder['product_id'])? $leadOrder['product_id'] : ''}}</td>
            <td class="expand-row-msg Website-task" data-name="leadOrder" data-id="{{$key.'-'.$leadOrder['id']}}">
                <span class="Website-task show-short-leadOrder-{{$key.'-'.$leadOrder['id']}}">{{ isset($leadOrder['name'])? Str::limit($leadOrder['name'], 25, '..') : '' }}</span>
                <span style="word-break:break-all;" class="Website-task show-full-leadOrder-{{$key.'-'.$leadOrder['id']}} hidden">{{ isset($leadOrder['name'])? $leadOrder['name'] : ''}}</span>
            </td>

            <td class="Website-task">{{ isset($leadOrder['brand_name'])? $leadOrder['brand_name'] : ''}}</td>
            @if($orderOrLead == 'order')
            <td>{{ isset($leadOrder->storeWebsiteOrder->storeWebsiteProductPrice['default_price'])? $leadOrder->storeWebsiteOrder->storeWebsiteProductPrice['default_price'] : 'N/A'}}</td>
            <td>{{ isset($leadOrder->storeWebsiteOrder->storeWebsiteProductPrice['segment_discount'])? $leadOrder->storeWebsiteOrder->storeWebsiteProductPrice['segment_discount'] : 'N/A'}}</td>
            <td>{{ isset($leadOrder->storeWebsiteOrder->storeWebsiteProductPrice['duty_price'])? $leadOrder->storeWebsiteOrder->storeWebsiteProductPrice['duty_price'] : 'N/A'}}</td>
            <td>{{ isset($leadOrder->storeWebsiteOrder->storeWebsiteProductPrice['override_price'])? $leadOrder->storeWebsiteOrder->storeWebsiteProductPrice['override_price'] : 'N/A'}}</td>

            @else

            <td>{{ isset($leadOrder->storeWebsite->storeWebsiteProductPrice['default_price'])? $leadOrder->storeWebsite->storeWebsiteProductPrice['default_price'] : 'N/A'}}</td>
            <td>{{ isset($leadOrder->storeWebsite->storeWebsiteProductPrice['segment_discount'])? $leadOrder->storeWebsite->storeWebsiteProductPrice['segment_discount'] : 'N/A'}}</td>
            <td>{{ isset($leadOrder->storeWebsite->storeWebsiteProductPrice['duty_price'])? $leadOrder->storeWebsite->storeWebsiteProductPrice['duty_price'] : 'N/A'}}</td>
            <td>{{ isset($leadOrder->storeWebsite->storeWebsiteProductPrice['override_price'])? $leadOrder->storeWebsite->storeWebsiteProductPrice['override_price'] : 'N/A'}}</td>

            @endif
           
            <td>{{ isset($leadOrder['price'])? $leadOrder['price'] : ''}}</td>
            <td>{{ isset($leadOrder['price_eur_discounted'])? $leadOrder['price_eur_discounted'] : ''}}</td>
            <td>
            @php 
             
             if($leadOrder['price'])  {
              if($leadOrder['price_eur_discounted']) {
                $discount = $leadOrder['price']*($leadOrder['price_eur_discounted']/100);
                $final_price = $leadOrder['price'] - $discount;
              } 
              else {
               $final_price = $leadOrder['price'];
              } 
            }
            else {
              $final_price = 0;
            }
            
            $gmu = 0;
            if($final_price) {
              $gmu = $final_price/1.22;
            }            
            @endphp
            {{ isset($final_price)? $final_price : ''}}</td>
            <td>{{number_format($gmu,2)}}</td>
            <td><button title="show Log" type="button" class="btn btn-xs btn-image load-log mr-3" data-cust_id="{{$leadOrder['customer_id']}}"  data-prod_id="{{$leadOrder['product_id']}}"><img src="/images/chat.png" alt="" style="cursor: nwse-resize; width: 0px;"></button>
              <button title="show Calculation" type="button" class="btn btn-xs btn-image load-calculation mr-3" data-cust_id="{{$leadOrder['customer_id']}}"  data-prod_id="{{$leadOrder['product_id']}}"><i class="fa fa-calculator" aria-hidden="true"></i></button>
            </td>
        </tr>
    @endforeach
	