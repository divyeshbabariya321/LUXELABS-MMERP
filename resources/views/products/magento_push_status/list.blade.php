<table class="table table-bordered table-striped" style="table-layout:fixed;">
    <thead>
    <tr>
        <th style="width:2%"><input type="checkbox" id="main_checkbox" name="choose_all"></th>
        <th style="width:8%">Product ID</th>
        <th style="width:4%">Store Website</th>
        <th style="width:4%">Image</th>
        <th style="width:7%">Brand</th>
        <th style="width:15%">Category</th>
        <th style="width:8%">Title</th>
        <th style="width:8%">Composition</th>
        <th style="width:8%">Color</th>
        <th style="width:5%">Price</th>
        <th style="width:5%">Status</th>
        <th style="width:5%">Log</th>
    </tr>
    </thead>
    <tbody>
    @if($products->count() == 0)
        <tr>
            <td colspan="13" class="text-center">No records found!</td>
        </tr>
    @endif
    @foreach ($products as $key => $product)
        <tr style="display: none" id="product{{ $product->id }}">
            <td colspan="14">
                <div class="row">
                    <div class="col-md-3">
                        <p class="same-color">{{ strtoupper($product->name) }}</p>
                        <br/>
                        <p class="same-color" style="font-size: 18px;">
                            <span style="text-decoration: line-through">EUR {{ number_format($product->price) }}</span>
                            EUR {{ number_format($product->price_eur_special) }}
                        </p>
                        <?php
                        // check brand sengment
                        if ($product->brands) {
                            $segmentPrice = getSegmentPrice($product->brands->brand_segment, $product->category);
                            if ($segmentPrice) {
                                echo "<p class='same-color'>Min Segment Price : ".$segmentPrice->min_price.'<br>
                                        Max Segment Price : '.$segmentPrice->max_price.'</p>';
                            }
                        }
                        ?>
                        <p>
                            <strong class="same-color" style="text-decoration: underline">Description</strong>
                            <br/>
                            <span id="description{{ $product->id }}" class="same-color">
                                {{ ucwords(strtolower(html_entity_decode($product->short_description))) }}
                            </span>
                        </p>
                        <br/>
                        @php
                            $descriptions = \App\ScrapedProducts::select('description','website')->where('sku', $product->sku)->get();
                        @endphp
                        @if ( $descriptions->count() > 0 )
                            @foreach ( $descriptions as $description )
                                @if ( !empty(trim($description->description)) && trim($description->description) != trim($product->short_description) )
                                    <hr/>
                                    <span class="same-color">
                                        {{ ucwords(strtolower(html_entity_decode($description->description))) }}
                                    </span>
                                    <p>
                                        <button class="btn btn-default btn-sm use-description"
                                                data-id="{{ $product->id }}"
                                                data-description="{{ str_replace('"', "'", html_entity_decode($description->description)) }}">
                                            Use this description ({{ $description->website }})
                                        </button>
                                        <button class="btn btn-default btn-sm set-description-site"
                                                data-id="{{ $product->id }}"
                                                data-description="{{ str_replace('"', "'", html_entity_decode($description->description)) }}">
                                            Set Description
                                        </button>
                                    </p>
                                @endif
                            @endforeach
                            <hr/>
                        @endif
                        @php
                            //getting proper composition and hscode
                            $composition = $product->commonComposition($product->category , $product->composition);
                            $hscode =  $product->hsCode($product->category , $product->composition);
                        @endphp
                        <p>
                            <strong class="same-color" style="text-decoration: underline;">HsCode</strong>
                            <br/>
                            <span class="same-color flex-column">{{ strtoupper($hscode) }}</span>
                        </p>
                        <p>
                            <strong>Sizes</strong>: {{ $product->size_eu }}<br/>
                            <strong>Dimension</strong>: {{ \App\Helpers\ProductHelper::getMeasurements($product) }}
                            <br/>
                        </p>
                        <p>
                            <span class="sololuxury-button">ADD TO BAG</span>
                            <span class="sololuxury-button"><i class="fa fa-heart"></i> ADD TO WISHLIST</span>
                        </p>
                        <p class="same-color">
                            View All:
                            <strong>{{ isset($product->product_category->id) ? \App\Category::getCategoryPathById($product->product_category->id)  : '' }}</strong>
                            <br/>
                            View All:
                            <strong>{{ $product->brands ? $product->brands->name : 'N/A' }}</strong>
                        </p>
                        <p class="same-color">
                            <strong>Style ID</strong>: {{ $product->sku }}
                            <br/>
                            <strong class="text-danger">{{ $product->is_on_sale ? 'On Sale' : '' }}</strong>
                        </p>
                    </div>
                    <div class="col-md-4">
                        @if(auth()->user()->isReviwerLikeAdmin('final_listing'))
                            <p class="text-right mt-5">
                                <button class="btn btn-xs btn-default edit-product-show" data-id="{{$product->id}}">Toggle Edit</button>
                                @if ($product->status_id == 9)
                                    <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="list">List
                                    </button>
                                @elseif ($product->status_id == 12)
                                    <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="update">Update</button>
                                @endif
                            </p>
                        @endif
                        @php
                            $logScrapers = \App\ScrapedProducts::where('sku', $product->sku)->where('validated', 1)->get();
                        @endphp
                        @if ($logScrapers)
                            <div>
                                <br/>
                                Successfully scraped on the following sites:<br/>
                                <ul>
                                    @foreach($logScrapers as $logScraper)
                                        @if($logScraper->url != "N/A")
                                            <li><a href="<?= $logScraper->url ?>"
                                                   target="_blank"><?= $logScraper->website ?></a>
                                                ( <?= $logScraper->last_inventory_at ?> )
                                            </li>
                                        @else
                                            <li><?= $logScraper->website ?></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
        <tr id="product_{{ $product->id }}" class="">
            <td> <input type="checkbox" class="affected_checkbox" name="products_to_update[]" data-id="{{$product->id}}"></td>
            @php
                $websiteArraysForProduct = \App\Helpers\ProductHelper::getStoreWebsiteName($product->id);
            @endphp
            <td class="table-hover-cell">
                {{ $product->id }}
                @if($product->croppedImages()->count() == count($websiteArraysForProduct))
                    <span class="badge badge-success" >&nbsp;</span>
                @else
                    <span class="badge badge-warning" >&nbsp;</span>
                @endif
                @if(count($product->more_suppliers()) > 1)
                    <button style="padding:0px;" type="button" class="btn-link"
                            data-id="{{ $product->id }}" data-target="#product_suppliers_{{ $product->id }}"
                            data-toggle="modal">View
                    </button>
                @endif
                <div>
                    @if($product->supplier_link)
                        <a target="_new" title="{{ $product->sku }}" href="{{ $product->supplier_link }}">{{ substr($product->sku, 0, 5) . (strlen($product->sku) > 5 ? '...' : '') }}</a>
                    @else
                        <a title="{{ $product->sku }}" href="javascript:;">{{ substr($product->sku, 0, 5) . (strlen($product->sku) > 5 ? '...' : '') }}</a>
                    @endif
                </div>
            </td>
            <td>{{ $product->sw_title . ' - '. $product->sw_id  }}</td>
            <td style="word-break: break-all; word-wrap: break-word">
                <button type="button" class="btn-link quick-view_image__"
                        data-id="{{ $product->id }}" data-target="#product_image_{{ $product->id }}"
                        data-toggle="modal">View
                </button>
            </td>

            <td>
                @if($product->brands)
                    <a title="{{ $product->brands->name }}" href="javascript:;">{{ substr($product->brands->name, 0, 5) . (strlen($product->brands->name) > 5 ? '...' : '') }}</a>
                @else
                    N/A
                @endif
            </td>

            <td class="table-hover-cell">
                <?php
                $cat = [];
                        $catM = $product->categories;
                        if ($catM) {
                            $parentM = $catM->parent;
                            $cat[] = $catM->title;
                            if ($parentM) {
                                $gparentM = $parentM->parent;
                                $cat[] = $parentM->title;
                                if ($gparentM) {
                                    $cat[] = $gparentM->title;
                                }
                            }
                        }
                        ?>
                @if (!$imageCropperRole)
                    @if ( isset($product->log_scraper_vs_ai) && $product->log_scraper_vs_ai->count() > 0 )
                        @foreach ( $product->log_scraper_vs_ai as $resultAi )
                            @php $resultAi = json_decode($resultAi->result_ai); @endphp
                            @if ( !empty($resultAi->category) )
                                <button id="ai-category-{{ $product->id }}" data-id="{{ $product->id }}"
                                        data-category="{{ \App\LogScraperVsAi::getCategoryIdByKeyword( $resultAi->category, $resultAi->gender, null ) }}"
                                        class="btn btn-default btn-sm mt-2 ai-btn-category">{{ ucwords(strtolower($resultAi->category)) }}
                                    (AI)
                                </button>
                            @endif
                        @endforeach
                    @endif
                @else
                @endif
                {{ implode(">",array_reverse($cat)) }}
            </td>
            <td class="table-hover-cell quick-edit-name quick-edit-name-{{ $product->id }}" data-id="{{ $product->id }}">
                @if (!$imageCropperRole)
                    <span class="quick-name">{{ $product->name }}</span>
                    <input name="text" class="form-control quick-edit-name-input hidden" placeholder="Product Name" value="{{ $product->name }}">
                @else
                    <span>{{ $product->name }}</span>
                @endif
            </td>
            <td class="table-hover-cell" data-id="{{ $product->id }}">
                @if (!$imageCropperRole)
                    {{ $product->composition }}
                @else
                    <span class="quick-composition">{{ $product->composition }}</span>
                @endif
            </td>
            <td class="table-hover-cell">

                {{ $product->color }}
            </td>

            <td class="table-hover-cell quick-edit-price" data-id="{{ $product->id }}">
                @if (!$imageCropperRole)
                    <span class="quick-price">{{ $product->price }}</span>
                    <input type="number" name="price" class="form-control quick-edit-price-input hidden" placeholder="100" value="{{ $product->price }}">
                @else
                    <span>EUR {{ $product->price }}</span>
                @endif
            </td>
            <td>
                @php
                    $logList = \App\Loggers\LogListMagento::select('message')
                          ->where('product_id', $product->id)->where('store_website_id', $product->sw_id)->orderBy('id', 'desc')->first();
                    if(isset($logList) && !empty($logList)) {
                      echo $logList->message;
                    }
                @endphp
            </td>
            <td>                
                <a onclick="getConditionCheckLog({{ $product->id }}, {{ $product->sw_id }})" class="btn" title="View log">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $products->links() }}