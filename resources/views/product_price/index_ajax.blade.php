
                       @php $i=$count; @endphp
                       @foreach ($product_list as $key)
                           <tr data-storeWebsitesID="{{$key['storeWebsitesID']}}" data-id="{{$i}}" data-country_code="{{$key['country_code']}}" class="tr_{{$i++}}">
                           
                               <td class="expand-row" style="word-break: break-all">



                                   <span class="td-mini-container">
                                                {{ strlen( $key['sku']) > 9 ? substr( $key['sku'], 0, 9).'...' :  $key['sku'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                                {{  $key['sku'] }}
                                            </span>



                               </td>
                               <td class="product_id">{{ $key['id'] }}</td>
                               <td class="expand-row" style="word-break: break-all">
                                      <span class="td-mini-container">
                                                {{ strlen( $key['countries']) > 9 ? substr( $key['countries'], 0, 8).'...' :  $key['countries'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                               {{ $key['countries'] }}
                                            </span>
                                   </td>
                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $key['brand']) > 15 ? substr( $key['brand'], 0, 15).'...' :  $key['brand'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                               {{ $key['brand'] }}
                                            </span>
                                   </td>
                               <td>{{ $key['segment'] }}</td>
                               <td class="expand-row" style="word-break: break-all">

                                   <span class="td-mini-container">
                                                {{ strlen( $key['website']) > 22 ? substr( $key['website'], 0, 22).'...' :  $key['website'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                                {{  $key['website'] }}
                                            </span>
                               </td>

                               <td>{{ $key['default_price'] }}</td>
                               <td>{{ $key['segment_discount'] }}</td>
                               <td>{{ $key['duty_price'] }}</td>
                               <td>{{ $key['override_price'] }}</td>
                               <td>{{ $key['final_price'] }}</td>
                               <td> @if($key["status"]==1)
               approved
             @else
               pending 
             @endif     
        </td>
        <td>
        <a  class="btn btn-secondary" onclick="openhistory({{ $key['store_website_product_prices_id'] }});" >Histrory</a>   
        </td>
                              <!-- <td>{{ $key['eur_price'] }}</td>
                               
                               
                                   @foreach($category_segments as $category_segment)
                                    <td>
                                        @php
                                            $category_segment_discount = (isset($key['category_segment_discount_array'][$key['brand_id']]) && isset($key['category_segment_discount_array'][$key['brand_id']][$category_segment->id])) ? $key['category_segment_discount_array'][$key['brand_id']][$category_segment->id] : null;
                                        @endphp

                                        @if($category_segment_discount)
                                            <input type="text" class="form-control" value="{{ $category_segment_discount->amount }}" onchange="store_amount({{$key['brand_id'] }}, {{ $category_segment->id }})"></th>
                                        @else
                                            <input type="text" class="form-control" value="" onchange="store_amount({{ $key['brand_id']}}, {{ $category_segment->id }})"></th>
                                        @endif
                                    


                                    </td>
                                @endforeach 
                               
                               <td>{{ $key['iva'] }}</td>
                               <td>{{ $key['net_price'] }}</td>
                               <td>
                                   <div class="form-group">
                                       <div class="input-group">
                                           <input style="width: 75%;border-radius: 4px;" placeholder="add duty" data-ref="{{str_replace(' ', '_', $key['country_name'])}}" value="{{ str_replace('%', '', $key['add_duty']) }}" type="text" class="form-control add_duty {{str_replace(' ', '_', $key['country_name'])}}" name="add_duty">
                                           <div class="ml-2" style="float: left;">%</div>
                                       </div>
                                   </div>
                               </td>
                               <td>
                                  <div style="align-items: center">
                                      <span style="min-width:50px;">{{ $key['add_profit'] }}</span>
                                      <div class="ml-2" style="float: right;">%</div>
                                      <div style="float: right;width:50%;">
                                          <input style="padding: 6px" placeholder="add profit" data-ref="web_{{ $key['storeWebsitesID']}}" value="{{ $key['add_profit_per'] }}" type="text" class="form-control add_profit web_{{ $key['storeWebsitesID']}}" name="add_profit">
                                      </div>
                                  </div>
                               </td>
                               <td>{{ $key['final_price'] }}</td>
                               !-->
                           </tr>
                       @endforeach