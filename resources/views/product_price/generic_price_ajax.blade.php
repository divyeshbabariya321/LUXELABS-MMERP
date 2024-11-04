@php $i=1; @endphp
@foreach ($product_list as $product) 
   <tr data-country_code="{{$product['country']['country_code']}}" class="tr_{{$i}}">
                               <td class="expand-row" style="word-break: break-all">
                                   <span class="td-mini-container">
                                      {{ strlen( $product['categoryName']) > 9 ? substr( $product['categoryName'], 0, 8).'...' :  $product['categoryName'] }}
                                   </span>

                                   <span class="td-full-container hidden">
                                       {{ $product['categoryName'] }}
                                   </span>
                                </td>

                                <td class="expand-row" style="word-break: break-all">
                                   <span class="td-mini-container">
                                      {{ strlen( $product['product_website']) > 9 ? substr( $product['product_website'], 0, 8).'...' :  $product['product_website'] }}
                                   </span>

                                   <span class="td-full-container hidden">
                                       {{ $product['product_website'] }}
                                   </span>
                                </td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $product['brandSegment']) > 15 ? substr( $product['brandSegment'], 0, 15).'...' :  $product['brandSegment'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $product['brandSegment'] }}
                                            </span>
                               </td>
                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $product['product']) > 15 ? substr( $product['product'], 0, 15).'...' :  $product['product'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $product['product'] }}
                                            </span>
                               </td>
                             
                               <td class="expand-row" style="word-break: break-all">

                                   <span class="td-mini-container">
                                        {{ strlen( $product['country']['country_segment']) > 9 ? substr( $product['country']['country_segment'], 0, 9).'...' :  $product['country']['country_segment'] }}
                                   </span>

                                   <span class="td-full-container hidden">
                                       {{ $product['country']['country_segment'] }}
                                   </span>
                               </td>
                            
                               <td>{{ $product['product_price'] }}</td>
                                @php
                                    $j=1;
                                @endphp
                                  @foreach($category_segments as $category_segment)
                                    <td>
                                        @php
                                            $category_segment_discount = (isset($product['category_segment_discount_array'][$product['brandId']]) && isset($product['category_segment_discount_array'][$product['brandId']][$category_segment->id])) ? $product['category_segment_discount_array'][$product['brandId']][$category_segment->id] : null;
                                        @endphp

                                        @if($category_segment_discount)
                                            <input type="text" class="form-control seg_discount1 segment{{$j}}" data-row="{{$i}}" data-name="{{'seg_discount'.$i}}" value="{{ $category_segment_discount->amount }}" = data-ref="{{ $category_segment->id }}" onKeyUp="checkFinalPriceBeforeUpdate(this)" data-less_IVA="{{ $product['less_IVA'] }}" data-cate_segment_discount="{{ $product['cate_segment_discount'] }}" data-cate_segment_discount_type="{{ $product['cate_segment_discount_type'] }}" data-product_price="{{ $product['product_price'] }}" data-default_duty="{{ $product['country']['default_duty'] }}"></th>
                                        @else
                                            <input type="text" class="form-control seg_discount segment{{$j}}" data-row="{{$i}}"  data-name="{{'seg_discount'.$i}}" value="" data-ref="{{ $category_segment->id }}" onKeyUp="checkFinalPriceBeforeUpdate(this)" data-less_IVA="{{ $product['less_IVA'] }}" data-cate_segment_discount="{{ $product['cate_segment_discount'] }}" data-cate_segment_discount_type="{{ $product['cate_segment_discount_type'] }}" data-product_price="{{ $product['product_price'] }}" data-default_duty="{{ $product['country']['default_duty'] }}"></th>
                                        @endif
                                    </td>
                                    @php
                                    $j++;
                                    @endphp
                                  @endforeach 
                                  
                               <td>
                                   <div class="form-group">
                                       <div class="input-group">
                                           <input style="width: 75%;border-radius: 4px;" data-row="{{$i}}" data-name="{{'add_duty'}}" placeholder="add duty" data-ref="{{str_replace(' ', '_', $product['country']['country_name'])}}" value="{{ str_replace('%', '', $product['country']['default_duty']) }}" type="text" class="form-control add_duty {{str_replace(' ', '_', $product['country']['country_name'])}}" name="add_duty" onKeyUp="checkFinalPriceBeforeUpdate(this)" data-less_IVA="{{ $product['less_IVA'] }}" data-cate_segment_discount="{{ $product['cate_segment_discount'] }}" data-cate_segment_discount_type="{{ $product['cate_segment_discount_type'] }}" data-product_price="{{ $product['product_price'] }}" data-default_duty="{{ $product['country']['default_duty'] }}">
                                           <div class="ml-2" style="float: left;">%</div>
                                       </div>
                                   </div>
                               </td>
                               <td>
                                  <div style="align-items: center">
                                      <span style="min-width:50px;">{{ isset($product['add_profit']) ? $product['add_profit'] : ''  }}</span>
                                      <div class="ml-2" style="float: right;">%</div>
                                      <div style="float: right;width:50%;">
                                          <input style="padding: 6px" placeholder="add profit" data-ref="web_{{ $product['store_websites_id']}}" value="{{ $product['add_profit_per'] }}" type="text" class="form-control add_profit web_{{ $product['store_websites_id']}}" name="add_profit" onKeyUp="checkFinalPriceBeforeUpdate(this)" data-less_IVA="{{ $product['less_IVA'] }}">
                                      </div>
                                  </div>
                               </td>
                               <td>{{ $product['less_IVA'] }} <input type="hidden" class="less_iva" value="{{ str_replace('%','',$product['less_IVA']) }}"></td>
                               <td id="cost_a{{$i}}" class="row_cost_a">{{ $product['cost1'] }}</td>
                               <td id="cost_b{{$i}}" class="row_cost_b">{{ $product['cost2'] }}</td>
                               <td id="final_price_row_a{{$i}}" class="row_final_price_a">{{ $product['final_price1'] }}</td>
                               <td id="final_price_row_b{{$i}}" class="row_final_price_b">{{ $product['final_price2'] }}</td>
                               <td><button class="btn btn-secondary UpdateProduct"  data-brandId ="{{$product['brandId']}}" data-websiteId ="{{$product['store_websites_id']}}" data-catid ="{{$product['catId']}}" data-countryid ="{{$product['country']['id']}}" data-brand-segment ="{{$product['brandSegment']}}">Update</button></td>
                           </tr> 
@endforeach
                   