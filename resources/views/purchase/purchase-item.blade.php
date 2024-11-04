<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th width="5%"></th>
            <th width="5%"><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}" class="ajax-sort-link">ID</a></th>
            <th width="5%"><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=date{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}" class="ajax-sort-link">Date</a></th>
            <th width="5%"><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=purchase_handler{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}" class="ajax-sort-link">Purchase Handler</a></th>
            <th width="10%"><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=supplier{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}" class="ajax-sort-link">Supplier Name</a></th>
            <th width="10%"><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}" class="ajax-sort-link">Supplier Purchase Status</a></th>
            <th width="20%">Customer Names</th>
            <th width="5%">Products</th>            
            <th width="5%">Retail Price</th>            
            <th width="5%">Buying Price</th>
            <th width="5%">Gross Profit</th>            
            <th width="10%">Action</th>
        </tr>

        @foreach ($purchases_array as $key => $purchase)
            @php
                $products_count = 1;
                if (count($purchase['products']) > 0) {
                  // foreach ($purchase['products'] as $product) {
                  //   $products_count += count($product['orderproducts']);
                  // }

                  $products_count = count($purchase['products']) + 1;
                }

            @endphp
            <tr>
                <td rowspan="{{ $products_count }}">
                    <input type="checkbox" name="select" class="export-checkbox" data-id="{{ $purchase['id'] }}">
                </td>
                <td rowspan="{{ $products_count }}">{{ $purchase['id'] }}</td>
                <td rowspan="{{ $products_count }}">{{ Carbon\Carbon::parse($purchase['created_at'])->format('d-m-Y') }}</td>
                <td rowspan="{{ $products_count }}">{{ isset($users[$purchase['purchase_handler']]) ? $users[$purchase['purchase_handler']] : 'nil' }}</td>
                <td rowspan="{{ $products_count }}">{{ isset($purchase['purchase_supplier']['supplier']) ? $purchase['purchase_supplier']['supplier'] : '' }}</td>
                <td rowspan="{{ $products_count }}">{{ $purchase['status']}}</td>
            </tr>
            @php
                $qty = 0;
                $sold_price = 0;
            @endphp
            @if($purchase['order_products'])
                @foreach ($purchase['order_products'] as $order_product)
                    <tr>
                        <td>
                            <ul>
                                <li>
                                    @if ($order_product['order'])
                                        @if ($order_product['order']['customer'])
                                            {{ $order_product['order']['customer']['name'] }}
                                        @else
                                            No Customer
                                        @endif
                                    @else
                                        No Order
                                    @endif
    
                                    - Qty. <strong>{{ $qty = $order_product['qty'] }}</strong>
                                    - Sold Price: <strong>{{ $order_product['product_price'] }}</strong>
    
                                    @php
                                        $sold_price += $order_product['product_price'];
                                    @endphp
                                </li>
                            </ul>
                        </td>
                        <td>
                            @php
                                $special_product = getProduct(isset($order_product['product']['id']) ? $order_product['product']['id'] : '');
                            @endphp
                            @if(!empty($special_product))
                                @if ($special_product->hasMedia($media_tags))
                                    <img src="{{ getMediaUrl($special_product->getMedia($media_tags)->first()) }}" class="img-responsive" width="50px">
                                @endif
                            @endif
                        </td>
                        <td>{{ isset($order_product['product']['price']) ? $order_product['product']['price'] : ''  }}</td>
                        <td>
                            @php $actual_price = 0; @endphp
                            @php $actual_price += isset($order_product['product']['price']) ? $order_product['product']['price'] : 0 @endphp

                            {{ isset($order_product['product']['price']) ? $order_product['product']['price'] * 78 : '' }}
                        </td>
                        <td>
                            {{ $sold_price - ($actual_price * 78) }}
                        </td>
                    </tr>
                @endforeach

            @elseif ($purchase['products'])
                @foreach ($purchase['products'] as $product)
                    <tr>
                        <td>
                            @if ($product['orderproducts'])                                
                                <ul>
                                    @foreach ($product['orderproducts'] as $order_product)
                                        <li>
                                            @if ($order_product['order'])
                                                @if ($order_product['order']['customer'])
                                                    {{ $order_product['order']['customer']['name'] }}
                                                @else
                                                    No Customer
                                                @endif
                                            @else
                                                No Order
                                            @endif

                                            - Qty. <strong>{{ $qty = $order_product['qty'] }}</strong>
                                            - Sold Price: <strong>{{ $order_product['product_price'] }}</strong>

                                            @php
                                                $sold_price += $order_product['product_price'];
                                            @endphp
                                        </li>
                                        @php $qty = 0; @endphp
                                    @endforeach
                                </ul>                            
                            @else
                                <ul>
                                    <li>No Order Product</li>
                                </ul>
                            @endif
                        </td>
                        <td>
                            @php
                                $special_product = getProduct($product['id']);
                            @endphp
                            @if ($special_product->hasMedia($media_tags))
                                <img src="{{ getMediaUrl($special_product->getMedia($media_tags)->first()) }}" class="img-responsive" width="50px">
                            @endif
                        </td>
                        <td>{{ $product['price'] }}</td>
                        <td>
                            @php $actual_price = 0; @endphp
                            @php $actual_price += $product['price'] @endphp

                            {{ $product['price'] * 78 }}
                        </td>
                        <td>
                            {{ $sold_price - ($actual_price * 78) }}
                        </td>

                    </tr>
                @endforeach
            @endif
            <tr>
                <td colspan="12">
                    <div class="pull-right">
                        <a class="btn btn-image" href="{{ route('purchase.show',$purchase['id']) }}"><img src="/images/view.png" /></a>

                        {{ html()->form('DELETE', route('purchase.destroy', [$purchase['id']]))->style('display:inline')->open() }}
                        <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                        {{ html()->form()->close() }}

                        {{ html()->form('DELETE', route('purchase.permanentDelete', [$purchase['id']]))->style('display:inline')->open() }}
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                        {{ html()->form()->close() }}
                    </div>
                </td>
            </tr>
            </tr>
        @endforeach
    </table>
</div>

{!! $purchases_array->appends(Request::except('page'))->links() !!}
