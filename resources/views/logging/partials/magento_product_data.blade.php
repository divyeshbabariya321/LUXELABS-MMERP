@foreach($logListMagentos as $item)
    @php
        $syncStatus = $item->logListMagentoSyncStatus;
    @endphp

    @if(!empty($dynamicColumnsToShowListmagento))

        <tr style="background-color: {{$syncStatus?->color}}!important;">
            @if (!in_array('ID', $dynamicColumnsToShowListmagento))
                <td>
                    <a class="show-product-information text-dark" data-id="{{ $item->product_id }}" href="/products/{{ $item->product_id }}" target="__blank">{{ $item->product_id }}</a>
                </td>
            @endif

            @if (!in_array('SKU', $dynamicColumnsToShowListmagento))
                <td class="expand-row-msg" data-name="sku" data-id="{{$item->id}}">
                    <span class="show-short-sku-{{$item->id}}">{{ Str::limit($item->sku, 8 ,'..')}}</span>
                    <span style="word-break:break-all;" class="show-full-sku-{{$item->id}} hidden"><a class="text-dark" href="{{ $item->website_url }}/default/catalogsearch/result/?q={{ $item->sku }}" target="__blank">{{$item->sku}}</a></span>
                </td>
            @endif

            @if (!in_array('Brand', $dynamicColumnsToShowListmagento))
                <td class="expand-row-msg" data-name="brand_name" data-id="{{$item->id}}">
                    <span class="show-short-brand_name-{{$item->id}}">{{ Str::limit($item->brand_name, 12, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-brand_name-{{$item->id}} hidden">{{$item->brand_name}}</span>
                </td>
            @endif

            @if (!in_array('Category', $dynamicColumnsToShowListmagento))
                <td class="expand-row-msg" data-name="category_title" data-id="{{$item->id}}">
                    <span class="show-short-category_title-{{$item->id}}">{{ Str::limit($item->category_home, 12, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-category_title-{{$item->id}} hidden">{{$item->category_home}}</span>
                </td>
            @endif

            @if (!in_array('Price', $dynamicColumnsToShowListmagento))
                <td> {{$item->price}} </td>
            @endif

            @if (!in_array('Message', $dynamicColumnsToShowListmagento))
                <td class="expand-row-msg" data-name="message" data-id="{{$item->id}}">
                    <span class="show-short-message-{{$item->id}}">{{ Str::limit($item->message, 6, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-message-{{$item->id}} hidden">{{$item->message}}</span>
                </td>
            @endif

            @if (!in_array('D&T', $dynamicColumnsToShowListmagento))
                <td class="expand-row-msg" data-name="log_created_at" data-id="{{$item->id}}">
                    <span class="show-short-log_created_at-{{$item->id}}">
                    @if(isset($item->log_created_at))
                    {{ Str::limit(date('M d, Y',strtotime($item->log_created_at)), 12, '..') }}
                    @endif
                    </span>
                    <span style="word-break:break-all;" class="show-full-log_created_at-{{$item->id}} hidden">
                    @if(isset($item->log_created_at))
                    {{ date('M d, Y',strtotime($item->log_created_at))}}
                    @endif
                    </span>
                </td>
            @endif

            @if (!in_array('Website', $dynamicColumnsToShowListmagento))
                <td class="expand-row-msg" data-name="website_title" data-id="{{$item->id}}">
                    <span class="show-short-website_title-{{$item->id}}">{{ Str::limit($item->website_title, 12, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-website_title-{{$item->id}} hidden">{{$item->website_title}}</span>
                </td>
            @endif

            @if (!in_array('Status', $dynamicColumnsToShowListmagento))
                <td class="">
                    {{ (isset($item->stock) && $item->stock > 0) ? 'Available' : 'No Stk' }}
                </td>
            @endif

            @if (!in_array('Lang Id', $dynamicColumnsToShowListmagento))
                <td class="expand-row-msg" data-name="languages" data-id="{{$item->id}}"> 
                    <i data-id="{{$item->product_id}}" class="fa fa-info get-translation-product"></i>
                    <span class="show-short-languages-{{$item->id}}">
                    @if(!empty($item->languages)) {{ Str::limit(implode(", ",json_decode($item->languages)), 2, '..')}} @endif
                    </span>
                    <span style="word-break:break-all;" class="show-full-languages-{{$item->id}} hidden">
                    @if(!empty($item->languages)) {{ implode(", ",json_decode($item->languages)) }} @endif
                    </span>
                </td>
            @endif

            @if (!in_array('Sync Sts', $dynamicColumnsToShowListmagento))
                <td class="expand-row-msg" data-name="sync_status" data-id="{{$item->id}}">
                    <span class="show-short-sync_status-{{$item->id}}">{{ Str::limit($item->sync_status, 6, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-sync_status-{{$item->id}} hidden">{{$item->sync_status}}</span>
                </td>
            @endif

            @if (!in_array('Job Start', $dynamicColumnsToShowListmagento))
                <td class="expand-row-msg" data-name="job_start_time" data-id="{{$item->id}}">
                    <span class="show-short-job_start_time-{{$item->id}}">{{ Str::limit($item->job_start_time, 12, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-job_start_time-{{$item->id}} hidden">{{$item->job_start_time}}</span>
                </td>
            @endif

            @if (!in_array('Job End', $dynamicColumnsToShowListmagento))
                <td class="expand-row-msg" data-name="job_end_time" data-id="{{$item->id}}">
                    <span class="show-short-job_end_time-{{$item->id}}">{{ Str::limit($item->job_end_time, 12, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-job_end_time-{{$item->id}} hidden">{{$item->job_end_time}}</span>
                </td>
            @endif

            @if (!in_array('Total', $dynamicColumnsToShowListmagento))
                <td>{{$item->total_request_assigned}} </td>
            @endif

            @if (!in_array('Success', $dynamicColumnsToShowListmagento))
                <td>{{$item->total_success}} </td>
            @endif

            @if (!in_array('Failure', $dynamicColumnsToShowListmagento))
                <td> {{$item->total_error}}</td>
            @endif

            @if (!in_array('User', $dynamicColumnsToShowListmagento))
                <td class="expand-row-msg" data-name="log_user_name" data-id="{{$item->id}}">
                    <span class="show-short-log_user_name-{{$item->id}}">{{ Str::limit($item->log_user_name, 12, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-log_user_name-{{$item->id}} hidden">{{$item->log_user_name}}</span>
                </td>   
            @endif

            @if (!in_array('Time', $dynamicColumnsToShowListmagento))
                <td>{{Carbon\Carbon::parse($item->log_created_at)->format('H:i')}}</td>
            @endif

            @if (!in_array('Size', $dynamicColumnsToShowListmagento))
                <td>@if(!empty($item->size_chart_url)) <a class="text-dark" href="{{$item->size_chart_url}}" target="__blank">Yes</a> @else No @endif</td>
            @endif

            @if (!in_array('Queue', $dynamicColumnsToShowListmagento))
                <td>{{$item->queue}}</td>
            @endif

            @if (!in_array('Try', $dynamicColumnsToShowListmagento))
                <td>{{$item->tried}}</td>
            @endif

            @if (!in_array('Action', $dynamicColumnsToShowListmagento))
                <td>
                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$item->id}}')"><i class="fa fa-arrow-down"></i></button>
                </td>
            @endif

        </tr>

        @if (!in_array('Action', $dynamicColumnsToShowListmagento))
            <tr class="action-btn-tr-{{$item->id}} d-none">
                <td class="font-weight-bold">Action</td>
                <td colspan="20" class="cls-actions">
                    <span style="display:flex;">
                        <button data-toggle="modal" data-target="#update_modal" class="btn btn-xs btn-none-border update_modal" data-id="{{ $item}}" title="Update Product"><i class="fa fa-edit"></i></button>

                        <button class="btn btn-xs btn-none-border show_error_logs" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Magento product push error logs"><i class="fa fa-eye"></i></button>

                        <button class="btn btn-xs btn-none-border show_product_push_logs" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Magento product push logs"><i class="fa fa-eye"></i></button>

                        <button class="btn btn-xs btn-none-border push_journey" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Product push journey"><i class="fa fa-info-circle"></i></button>

                        <button class="btn btn-xs btn-none-border push_journey_horizontal" data-product_id="{{ $item->product_id }}" data-sku="{{$item->sku}}" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Product push journey horizontal"><i class="fa fa-info-circle"></i></button>

                        <button class="btn btn-xs btn-none-border show_prices" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" data-product="{{ $item->product_id}}" title="Price details"><i class="fa fa-money"></i></button>

                        <button class="btn btn-xs btn-product-screenshot" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Live Screenshot"><i style="font-size:13px;" class="fa fa-image"></i></button>

                        <a target="__blank" href="{{$item->website_url}}/admin/?sku={{$item->getWebsiteSku()}}"><button class="btn btn-xs" title="Store website"><i class="fa fa-globe"></i></button></a>

                        <input style="width:14px;height:15px;margin-left:7px;margin-top:5px;" type="checkbox" class="form-control selectProductCheckbox_class" value="{{ $item->sku }}{{ $item->color }}" websiteid="{{$item->store_website_id}}" name="selectProductCheckbox"/>

                        <i style="cursor: pointer;" class="ml-2 btn btn-xs fa fa-upload upload-single" data-id="{{ $item->product_id }}" title="push to magento" aria-hidden="true"></i>
                    </span>
                </td>
            </tr>
        @endif
    @else
        
        <tr style="background-color: {{$syncStatus?->color}}!important;">
            <td>
                <a class="show-product-information text-dark" data-id="{{ $item->product_id }}" href="/products/{{ $item->product_id }}" target="__blank">{{ $item->product_id }}</a>
            </td>

            <td class="expand-row-msg" data-name="sku" data-id="{{$item->id}}">
                <span class="show-short-sku-{{$item->id}}">{{ Str::limit($item->sku, 8 ,'..')}}</span>
                <span style="word-break:break-all;" class="show-full-sku-{{$item->id}} hidden"><a class="text-dark" href="{{ $item->website_url }}/default/catalogsearch/result/?q={{ $item->sku }}" target="__blank">{{$item->sku}}</a></span>
            </td>

            <td class="expand-row-msg" data-name="brand_name" data-id="{{$item->id}}">
                <span class="show-short-brand_name-{{$item->id}}">{{ Str::limit($item->brand_name, 12, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-brand_name-{{$item->id}} hidden">{{$item->brand_name}}</span>
            </td>

            <td class="expand-row-msg" data-name="category_title" data-id="{{$item->id}}">
                <span class="show-short-category_title-{{$item->id}}">{{ Str::limit($item->category_home, 12, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-category_title-{{$item->id}} hidden">{{$item->category_home}}</span>
            </td>

            <td> {{$item->price}} </td>

            <td class="expand-row-msg" data-name="message" data-id="{{$item->id}}">
                <span class="show-short-message-{{$item->id}}">{{ Str::limit($item->message, 6, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-message-{{$item->id}} hidden">{{$item->message}}</span>
            </td>

            <td class="expand-row-msg" data-name="log_created_at" data-id="{{$item->id}}">
                <span class="show-short-log_created_at-{{$item->id}}">
                @if(isset($item->log_created_at))
                {{ Str::limit(date('M d, Y',strtotime($item->log_created_at)), 12, '..') }}
                @endif
                </span>
                <span style="word-break:break-all;" class="show-full-log_created_at-{{$item->id}} hidden">
                @if(isset($item->log_created_at))
                {{ date('M d, Y',strtotime($item->log_created_at))}}
                @endif
                </span>
            </td>

            <td class="expand-row-msg" data-name="website_title" data-id="{{$item->id}}">
                <span class="show-short-website_title-{{$item->id}}">{{ Str::limit($item->website_title, 12, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-website_title-{{$item->id}} hidden">{{$item->website_title}}</span>
            </td>

            <td class="">
                {{ (isset($item->stock) && $item->stock > 0) ? 'Available' : 'No Stk' }}
            </td>

            <td class="expand-row-msg" data-name="languages" data-id="{{$item->id}}"> 
                <i data-id="{{$item->product_id}}" class="fa fa-info get-translation-product"></i>
                <span class="show-short-languages-{{$item->id}}">
                @if(!empty($item->languages)) {{ Str::limit(implode(", ",json_decode($item->languages)), 2, '..')}} @endif
                </span>
                <span style="word-break:break-all;" class="show-full-languages-{{$item->id}} hidden">
                @if(!empty($item->languages)) {{ implode(", ",json_decode($item->languages)) }} @endif
                </span>
            </td>

            <td class="expand-row-msg" data-name="sync_status" data-id="{{$item->id}}">
                <span class="show-short-sync_status-{{$item->id}}">{{ Str::limit($item->sync_status, 6, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-sync_status-{{$item->id}} hidden">{{$item->sync_status}}</span>
            </td>

            <td class="expand-row-msg" data-name="job_start_time" data-id="{{$item->id}}">
                <span class="show-short-job_start_time-{{$item->id}}">{{ Str::limit($item->job_start_time, 12, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-job_start_time-{{$item->id}} hidden">{{$item->job_start_time}}</span>
            </td>

            <td class="expand-row-msg" data-name="job_end_time" data-id="{{$item->id}}">
                <span class="show-short-job_end_time-{{$item->id}}">{{ Str::limit($item->job_end_time, 12, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-job_end_time-{{$item->id}} hidden">{{$item->job_end_time}}</span>
            </td>

            <td>{{$item->total_request_assigned}} </td>

            <td>{{$item->total_success}} </td>

            <td> {{$item->total_error}}</td>

            <td class="expand-row-msg" data-name="log_user_name" data-id="{{$item->id}}">
                <span class="show-short-log_user_name-{{$item->id}}">{{ Str::limit($item->log_user_name, 12, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-log_user_name-{{$item->id}} hidden">{{$item->log_user_name}}</span>
            </td>

            <td>{{Carbon\Carbon::parse($item->log_created_at)->format('H:i')}}</td>

            <td>@if(!empty($item->size_chart_url)) <a class="text-dark" href="{{$item->size_chart_url}}" target="__blank">Yes</a> @else No @endif</td>

            <td>
                {{$item->queue}}
            </td>

            <td>{{$item->tried}}</td>

            <td>
                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$item->id}}')"><i class="fa fa-arrow-down"></i></button>
            </td>

        </tr>

        <tr class="action-btn-tr-{{$item->id}} d-none">
            <td class="font-weight-bold">Action</td>
            <td colspan="20" class="cls-actions">
                <span style="display:flex;">
                    <button data-toggle="modal" data-target="#update_modal" class="btn btn-xs btn-none-border update_modal" data-id="{{ $item}}" title="Update Product"><i class="fa fa-edit"></i></button>

                    <button class="btn btn-xs btn-none-border show_error_logs" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Magento product push error logs"><i class="fa fa-eye"></i></button>

                    <button class="btn btn-xs btn-none-border show_product_push_logs" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Magento product push logs"><i class="fa fa-eye"></i></button>

                    <button class="btn btn-xs btn-none-border push_journey" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Product push journey"><i class="fa fa-info-circle"></i></button>

                    <button class="btn btn-xs btn-none-border push_journey_horizontal" data-product_id="{{ $item->product_id }}" data-sku="{{$item->sku}}" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Product push journey horizontal"><i class="fa fa-info-circle"></i></button>

                    <button class="btn btn-xs btn-none-border show_prices" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" data-product="{{ $item->product_id}}" title="Price details"><i class="fa fa-money"></i></button>

                    <button class="btn btn-xs btn-product-screenshot" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Live Screenshot"><i style="font-size:13px;" class="fa fa-image"></i></button>

                    <a target="__blank" href="{{$item->website_url}}/admin/?sku={{$item->getWebsiteSku()}}"><button class="btn btn-xs" title="Store website"><i class="fa fa-globe"></i></button></a>

                    <input style="width:14px;height:15px;margin-left:7px;margin-top:5px;" type="checkbox" class="form-control selectProductCheckbox_class" value="{{ $item->sku }}{{ $item->color }}" websiteid="{{$item->store_website_id}}" name="selectProductCheckbox"/>

                    <i style="cursor: pointer;" class="ml-2 btn btn-xs fa fa-upload upload-single" data-id="{{ $item->product_id }}" title="push to magento" aria-hidden="true"></i>
                </span>
            </td>
        </tr>
    @endif
@endforeach()