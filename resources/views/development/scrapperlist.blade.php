@extends('layouts.app')

@section('favicon' , 'development-issue.png')

@section('title', 'Development Scrapper List')

<style> 
    .status-selection .btn-group {
        padding: 0;
        width: 100%;
    }
    .status-selection .multiselect {
        width : 100%;
    }
    .pd-sm {
        padding: 0px 8px !important;
    }
    tr {
        background-color: #f9f9f9;
    }
    .mr-t-5 {
        margin-top:5px !important;
    }
    /* START - Pupose : Set Loader image - DEVTASK-4359*/
    #myDiv{
        position: fixed;
        z-index: 99;
        text-align: center;
    }
    #myDiv img{
        position: fixed;
        top: 50%;
        left: 50%;
        right: 50%;
        bottom: 50%;
    }

    .tablescrapper .btn-sm{padding: 2px;}
    .tablescrapper td{color: grey;font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;font-size: 14px;}
    /* END - DEVTASK-4359*/

    /* Add scrollbar position to top of table div. DEVTASK-24690 */
    .tablescrapper .infinite-scroll {
        overflow-x: scroll;
        transform: rotateX(180deg);
    }

    .tablescrapper .infinite-scroll table {
        transform: rotateX(180deg);
    }

    .tablescrapper .table {
        width: 100%;
        display: block; 
        overflow-x: scroll;
        position: relative;
        height: 750px;
    }

    /* Modal and iframe load. DEVTASK-24690 */
    #iframe_modal .modal-dialog .modal-header {
        position: absolute;
        z-index: 99999;
        right: 50px;
        top: 0;
        border: none;
    }

    #dev_scrapper_statistics { position: absolute; left: 0; padding-left: 12px; }
    #iframe_modal { right: 0; float: right; left: auto; padding-left: 12px; }
</style>
@section('content')

    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb pr-0">
                    <h2 class="page-heading">Scrapper Verification Data <button type="button" class="btn custom-button float-right ml-10" data-toggle="modal" data-target="#scrapperdatatablecolumnvisibilityList">Column Visiblity</button></h2>
                    <div class="pull-left cls_filter_box">
                        {{ html()->modelForm([], 'GET', url()->current())->class('form-inline')->open() }}

                            <div class="form-group ml-3 cls_filter_inputbox">
                                {{ html()->text('keywords', @$inputs['keywords'])->class('form-control')->placeholder('Enter Keywords') }}
                            </div>

                            <div class="form-group px-2">
                                <select class="form-control" name="status" id="status">
                                    <option value="">Select Status</option>
                                    <option value="Approve" <?php if (Request::get('status') == 'Approve') {
                                        echo 'selected';
                                    } ?>>Approve</option>
                                    <option value="Unapprove" <?php if (Request::get('status') == 'Unapprove') {
                                        echo 'selected';
                                    } ?>>Unapprove</option>
                                    <option value="Uncheck" <?php if (Request::get('status') == 'Uncheck') {
                                        echo 'selected';
                                    } ?>>Uncheck</option>
                                </select>
                            </div>

                            <div class="form-group  cls_filter_inputbox">
                                <button type="submit" class="btn custom-button ml-3" style="width:100px">Search</button>
                            </div>

                            <div class="form-group  cls_filter_inputbox">
                                <button type="button" class="btn custom-button ml-3 reset" style="width:100px">Reset</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3 col-md-12 tablescrapper">
    	<div class="infinite-scroll" style="overflow-y: auto">
		    <table class="table table-bordered table-striped" style="width: 150%; max-width:initial">
                <thead>
                    <tr>
                        @if(!empty($dynamicColumnsToShowscrapper))
                            @if (!in_array('Id', $dynamicColumnsToShowscrapper))
                                <th width="5%">Id</th>
                            @endif

                            @if (!in_array('Task Id', $dynamicColumnsToShowscrapper))
                                <th width="6%">Task Id</th>
                            @endif

                            @if (!in_array('Scrapper', $dynamicColumnsToShowscrapper))
                                <th width="5%">Scrapper</th>
                            @endif

                            @if (!in_array('Title', $dynamicColumnsToShowscrapper))
                                <th width="5%">Title</th>
                            @endif

                            @if (!in_array('Website', $dynamicColumnsToShowscrapper))
                                <th width="5%">Website</th>
                            @endif

                            @if (!in_array('Sku', $dynamicColumnsToShowscrapper))
                                <th width="5%">Sku</th>
                            @endif

                            @if (!in_array('Url', $dynamicColumnsToShowscrapper))
                                <th width="5%">Url</th>
                            @endif

                            @if (!in_array('Images', $dynamicColumnsToShowscrapper))
                                <th width="4%">Images</th>
                            @endif

                            @if (!in_array('Description', $dynamicColumnsToShowscrapper))
                                <th width="5%">Description</th>
                            @endif

                            @if (!in_array('Dimension', $dynamicColumnsToShowscrapper))
                                <th width="5%">Dimension</th>
                            @endif

                            @if (!in_array('Sizes', $dynamicColumnsToShowscrapper))
                                <th width="7%">Sizes</th>
                            @endif

                            @if (!in_array('Material Used', $dynamicColumnsToShowscrapper))
                                <th width="7%">Material Used</th>
                            @endif

                            @if (!in_array('Category', $dynamicColumnsToShowscrapper))
                                <th width="5%">Category</th>
                            @endif

                            @if (!in_array('Color', $dynamicColumnsToShowscrapper))
                                <th width="7%">Color</th>
                            @endif

                            @if (!in_array('Country', $dynamicColumnsToShowscrapper))
                                <th width="7%">Country</th>
                            @endif

                            @if (!in_array('Currency', $dynamicColumnsToShowscrapper))
                                <th width="5%">Currency</th>
                            @endif

                            @if (!in_array('Size System', $dynamicColumnsToShowscrapper))
                                <th width="4%">Size System</th>
                            @endif

                            @if (!in_array('Price', $dynamicColumnsToShowscrapper))
                                <th width="3%">Price</th>
                            @endif

                            @if (!in_array('Discounted Price', $dynamicColumnsToShowscrapper))
                                <th width="5%">Discounted Price</th>
                            @endif

                            @if (!in_array('Discounted Percentage', $dynamicColumnsToShowscrapper))
                                <th width="5%">Discounted Percentage</th>
                            @endif

                            @if (!in_array('B2b Price', $dynamicColumnsToShowscrapper))
                                <th width="3%">B2b Price</th>
                            @endif

                            @if (!in_array('Brand', $dynamicColumnsToShowscrapper))
                                <th width="9%">Brand Value</th>
                            @endif

                            @if (!in_array('Is Sale', $dynamicColumnsToShowscrapper))
                                <th width="5%">Is Sale</th>
                            @endif

                            @if (!in_array('Date', $dynamicColumnsToShowscrapper))
                                <th width="7%">Created Date</th>
                            @endif
                            @if (!in_array('Action', $dynamicColumnsToShowscrapper))
                                <th width="5%">Action</th>
                            @endif
                        @else 
                            <th width="5%">Id</th>
                            <th width="6%">Task Id</th>
                            <th width="5%">Scrapper</th>
                            <th width="5%">Title</th>
                            <th width="5%">Website</th>
                            <th width="5%">Sku</th>
                            <th width="5%">Url</th>
                            <th width="4%">Images</th>
                            <th width="5%">Description</th>
                            <th width="5%">Dimension</th>
                            <th width="7%">Sizes</th>
                            <th width="7%">Material Used</th>
                            <th width="5%">Category</th>
                            <th width="7%">Color</th>
                            <th width="5%">Country</th>
                            <th width="5%">Currency</th>
                            <th width="4%">Size System</th>
                            <th width="3%">Price</th>
                            <th width="5%">Discounted Price</th>
                            <th width="5%">Discounted Percentage</th>
                            <th width="3%">B2b Price</th>
                            <th width="9%">Brand Value</th>
                            <th width="5%">Is Sale</th>
                            <th width="7%">Created Date</th> 
                            <th width="5%">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-center task_queue_list">
                    @foreach($records as $i=>$record) 

                        @php
                        $returnData = [];
                        $jsonString = $record['scrapper_values'];
                        $phpArray = json_decode($jsonString, true);
                        if(!empty($phpArray)){
                            if(!empty($phpArray)){
                                foreach ($phpArray as $key_json => $value_json) {
                                    $returnData[$key_json] = $value_json;         
                                }
                            }                                   
                        }   

                        $columnArray = ['title', 'website', 'sku', 'url', 'images', 'description', 'dimension', 'sizes', 'material_used', 'category', 'color', 'country', 'currency', 'size_system', 'price', 'discounted_price', 'discounted_percentage', 'b2b_price', 'brand', 'is_sale'];

                        $titleRecord= $websiteRecord= $skuRecord= $urlRecord= $imagesRecord= $descriptionRecord= $dimensionRecord= $sizesRecord= $material_usedRecord= $categoryRecord= $colorRecord= $currencyRecord= $countryRecord= $size_systemRecord= $priceRecord= $discounted_priceRecord= $discounted_percentageRecord= $b2b_priceRecord= $brandRecord= $is_saleRecord= [];

                        $titleVar= $websiteVar= $skuVar= $urlVar= $imagesVar= $descriptionVar= $dimensionVar= $sizesVar= $material_usedVar= $categoryVar= $colorVar= $currencyVar= $countryVar= $size_systemVar= $priceVar= $discounted_priceVar= $discounted_percentageVar= $b2b_priceVar= $brandVar= $is_saleVar= 'btn-default';

                        $titleIcon= $websiteIcon= $skuIcon= $urlIcon= $imagesIcon= $descriptionIcon= $dimensionIcon= $sizesIcon= $material_usedIcon= $categoryIcon= $colorIcon= $currencyIcon= $countryIcon= $size_systemIcon= $priceIcon= $discounted_priceIcon= $discounted_percentageIcon= $b2b_priceIcon= $brandIcon= $is_saleIcon= 'fa fa-check';

                        foreach ($records->scrappervalueshistory as $data) {
                            if (in_array($data['column_name'],$columnArray)) {

                                if($data['status']=='Approve'){
                                    ${$data['column_name'] . 'Var'} = 'btn-success';
                                    ${$data['column_name'] . 'Icon'} = 'fa fa-check'; 
                                } else {
                                    ${$data['column_name'] . 'Var'} = 'btn-danger';
                                    ${$data['column_name'] . 'Icon'} = 'fa fa-times';
                                }
                            }
                        }

                        foreach ($records->scrappervaluesremarkshistory as $data) {
                            if (in_array($data['column_name'],$columnArray)) {
                               ${$data['column_name'] . 'Record'} = $data;
                            }
                        }
                        @endphp       

                        @if(!empty($dynamicColumnsToShowscrapper))
                            <tr>
                                @if (!in_array('Id', $dynamicColumnsToShowscrapper))
                                    <td>
                                        {{ $record['max_id'] }}
                                        </br>

                                        @php
                                            $ScrapperValuesHistoryCount = $records->flatMap(function ($record) {
                                                return $record->scrappervalueshistory->where('status', 'Approve');
                                            })->count();

                                            $checkBoX = '';
                                            if($ScrapperValuesHistoryCount==20){
                                                $checkBoX = 'checked';
                                            }
                                        @endphp
                                        <input type="checkbox" class="approveAll approveAll_{{ $record['max_id'] }}" title="Approve All Values" name="approveAll" id="approveAll" data-id="{{ $record['max_id'] }}" style="padding: 0; margin: 0; height: 15px;" {{$checkBoX}}>

                                        <button style="padding-left: 0;padding-left:3px;" type="button"
                                            class="btn btn-image d-inline count-dev-scrapper count-dev-scrapper_{{ $record['max_id'] }}"
                                            title="View scrapper" data-id="{{ $record['max_id'] }}" data-category="{{ $record['max_id'] }}">
                                                <i class="fa fa-list"></i>
                                        </button>

                                        @if(!empty($returnData['url']))
                                            <button style="padding-left: 0;padding-left:3px;" type="button"
                                                class="btn btn-image d-inline load-iframe-scrapper"
                                                title="Open both" data-id="{{ $record['max_id'] }}" data-category="{{ $record['max_id'] }}"
                                                data-iframe-url={{ $returnData['url'] }}>
                                                    <i class="fa fa-list"></i>
                                            </button>
                                        @endif
                                    </td>
                                @endif

                                @if (!in_array('Task Id', $dynamicColumnsToShowscrapper))
                                    <td class="expand-row-msg" data-name="task_id" data-id="{{$i}}">
                                        <span class="show-short-task_id-{{$i}}">{{ Str::limit('#DEVTASK-'.$record['task_id'], 10, '...')}}</span>
                                        <span style="word-break:break-all;" class="show-full-task_id-{{$i}} hidden">#DEVTASK-{{ $record['task_id'] }}</span>
                                    </td>
                                @endif

                                @if (!in_array('Scrapper', $dynamicColumnsToShowscrapper))
                                    <td class="expand-row-msg" data-name="subject" data-id="{{$i}}">
                                        @if(!empty($record['tasks']['subject']))
                                            <span class="show-short-subject-{{$i}}">{{ Str::limit($record['tasks']['subject'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-subject-{{$i}} hidden">{{ $record['tasks']['subject'] }}</span>
                                        @endif
                                    </td>
                                @endif


                                @if (!in_array('Title', $dynamicColumnsToShowscrapper))
                                    <td class="expand-row-msg" data-name="title" data-id="{{$i}}">
                                        @if(!empty($returnData['title']))
                                            <span class="show-short-title-{{$i}}">{{ Str::limit($returnData['title'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-title-{{$i}} hidden">{{ $returnData['title'] }}</span>
                                        @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'title', 'taskss_id' => $record['task_id']])

                                        <!-- </br>
                                        <button type="button" class="btn {{$titleVar}} btn-sm update-scrapper-status" title="Status" data-task_id="{{$record['task_id']}}" data-column_name="title">
                                            <i class="fa {{$titleIcon}}" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm update-scrapper-remarks" title="Remarks" data-task_id="{{ $record['task_id'] }}" data-column_name="title">
                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                        </button>

                                        @if(!empty($titleRecord))
                                            <button type="button" class="btn btn-default btn-sm view-scrapper-remarks" title="Remarks" data-task_id="{{ $record['task_id'] }}" data-column_name="title" data-remarks="{{$titleRecord['remarks']}}">
                                                <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                                            </button>
                                        @endif -->
                                    </td>
                                @endif


                                @if (!in_array('Website', $dynamicColumnsToShowscrapper))
                                    <td class="expand-row-msg" data-name="website" data-id="{{$i}}">
                                        @if(!empty($returnData['website']))
                                            <span class="show-short-website-{{$i}}">{{ Str::limit($returnData['website'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-website-{{$i}} hidden">{{ $returnData['website'] }}</span>
                                        @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'website', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif


                                @if (!in_array('Sku', $dynamicColumnsToShowscrapper))
                                    <td class="expand-row-msg" data-name="sku" data-id="{{$i}}">
                                        @if(!empty($returnData['sku']))
                                            <span class="show-short-sku-{{$i}}">{{ Str::limit($returnData['sku'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-sku-{{$i}} hidden">{{ $returnData['sku'] }}</span>
                                        @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'sku', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif


                                @if (!in_array('Url', $dynamicColumnsToShowscrapper))
                                    <td class="expand-row-msg" data-name="url" data-id="{{$i}}">
                                        @if(!empty($returnData['url']))
                                            <span class="show-short-url-{{$i}}"><a class="iframe-url" href="#" data-iframe-url={{ $returnData['url'] }}>{{ Str::limit($returnData['url'], 10, '...')}}</a></span>
                                            <span style="word-break:break-all;" class="show-full-url-{{$i}} hidden"><a class="iframe-url" href="#" data-iframe-url={{ $returnData['url'] }}>{{ $returnData['url'] }}</a></span>
                                        @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'url', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('Images', $dynamicColumnsToShowscrapper))
                                    <td>
                                        <button type="button" data-id="<?php echo $record['max_id']; ?>" class="btn scrapper-images" style="padding:1px 0px;">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>

                                        @include('development.partials.dynamic-column', ['columnname' => 'images', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('Description', $dynamicColumnsToShowscrapper))
                                    <td class="expand-row-msg" data-name="description" data-id="{{$i}}">
                                        @if(!empty($returnData['description']))
                                            <span class="show-short-description-{{$i}}">{{ Str::limit($returnData['description'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-description-{{$i}} hidden">{{ $returnData['description'] }}</span>
                                        @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'description', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('Dimension', $dynamicColumnsToShowscrapper))
                                    <td class="expand-row-msg" data-name="dimension" data-id="{{$i}}">
                                        @if(!empty($returnData['properties']))
                                            @if(!empty($returnData['properties']['dimension']))
                                                @if(is_array($returnData['properties']['dimension'])) 
                                                    <span class="show-short-dimension-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['dimension']), 10, '...')}}</span>
                                                    <span style="word-break:break-all;" class="show-full-dimension-{{$i}} hidden">{{ implode("," , $returnData['properties']['dimension']) }}</span>
                                                @else
                                                    <span class="show-short-dimension-{{$i}}">{{ Str::limit($returnData['properties']['dimension'], 10, '...')}}</span>
                                                    <span style="word-break:break-all;" class="show-full-dimension-{{$i}} hidden">{{ $returnData['properties']['dimension'] }}</span>
                                                @endif
                                            @endif
                                        @endif

                                        <!-- <button type="button" data-id="<?php echo $record['max_id']; ?>" class="btn scrapper-properties" style="padding:1px 0px;">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button> -->

                                        @include('development.partials.dynamic-column', ['columnname' => 'dimension', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('Sizes', $dynamicColumnsToShowscrapper))
                                <td class="expand-row-msg" data-name="sizes" data-id="{{$i}}">
                                    @if(!empty($returnData['properties']))
                                        @if(!empty($returnData['properties']['sizes']))
                                            @if(is_array($returnData['properties']['sizes'])) 
                                                <span class="show-short-sizes-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['sizes']), 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-sizes-{{$i}} hidden">{{ implode("," , $returnData['properties']['sizes']) }}</span>
                                            @else
                                                <span class="show-short-sizes-{{$i}}">{{ Str::limit($returnData['properties']['sizes'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-sizes-{{$i}} hidden">{{ $returnData['properties']['sizes'] }}</span>
                                            @endif
                                        @endif
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'sizes', 'taskss_id' => $record['task_id']])
                                </td>
                                @endif

                                @if (!in_array('Material Used', $dynamicColumnsToShowscrapper))
                                <td class="expand-row-msg" data-name="material_used" data-id="{{$i}}">
                                    @if(!empty($returnData['properties']))
                                        @if(!empty($returnData['properties']['material_used']))
                                            @if(is_array($returnData['properties']['material_used'])) 
                                                <span class="show-short-material_used-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['material_used']), 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-material_used-{{$i}} hidden">{{ implode("," , $returnData['properties']['material_used']) }}</span>
                                            @else
                                                <span class="show-short-material_used-{{$i}}">{{ Str::limit($returnData['properties']['material_used'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-material_used-{{$i}} hidden">{{ $returnData['properties']['material_used'] }}</span>
                                            @endif
                                        @endif
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'material_used', 'taskss_id' => $record['task_id']])
                                </td>
                                @endif

                                @if (!in_array('Category', $dynamicColumnsToShowscrapper))
                                <td class="expand-row-msg" data-name="category" data-id="{{$i}}">
                                    @if(!empty($returnData['properties']))
                                        @if(!empty($returnData['properties']['category']))
                                            @if(is_array($returnData['properties']['category'])) 
                                                <span class="show-short-category-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['category']), 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-category-{{$i}} hidden">{{ implode("," , $returnData['properties']['category']) }}</span>
                                            @else
                                                <span class="show-short-category-{{$i}}">{{ Str::limit($returnData['properties']['category'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-category-{{$i}} hidden">{{ $returnData['properties']['category']}}</span>
                                            @endif
                                        @endif
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'category', 'taskss_id' => $record['task_id']])
                                </td>
                                @endif

                                @if (!in_array('Color', $dynamicColumnsToShowscrapper))
                                <td class="expand-row-msg" data-name="color" data-id="{{$i}}">
                                    @if(!empty($returnData['properties']))
                                        @if(!empty($returnData['properties']['color']))
                                            @if(is_array($returnData['properties']['color'])) 
                                                <span class="show-short-color-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['color']), 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-color-{{$i}} hidden">{{ implode("," , $returnData['properties']['color']) }}</span>
                                            @else
                                                <span class="show-short-color-{{$i}}">{{ Str::limit($returnData['properties']['color'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-color-{{$i}} hidden">{{ $returnData['properties']['color'] }}</span>
                                            @endif
                                        @endif
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'color', 'taskss_id' => $record['task_id']])
                                </td>
                                @endif

                                @if (!in_array('Country', $dynamicColumnsToShowscrapper))
                                <td class="expand-row-msg" data-name="country" data-id="{{$i}}">
                                    @if(!empty($returnData['properties']))
                                        @if(!empty($returnData['properties']['country']))
                                            @if(is_array($returnData['properties']['country'])) 
                                                <span class="show-short-country-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['country']), 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-country-{{$i}} hidden">{{ implode("," , $returnData['properties']['country']) }}</span>
                                            @else
                                                <span class="show-short-country-{{$i}}">{{ Str::limit($returnData['properties']['country'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-country-{{$i}} hidden">{{ $returnData['properties']['country'] }}</span>
                                            @endif
                                        @endif
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'country', 'taskss_id' => $record['task_id']])
                                </td>
                                @endif

                                @if (!in_array('Currency', $dynamicColumnsToShowscrapper))
                                    <td>
                                        @if(!empty($returnData['currency'])) {{ $returnData['currency'] }} @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'currency', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('Size System', $dynamicColumnsToShowscrapper))
                                    <td class="expand-row-msg" data-name="size_system" data-id="{{$i}}">
                                        @if(!empty($returnData['size_system']))
                                            <span class="show-short-size_system-{{$i}}">{{ Str::limit($returnData['size_system'], 10, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-size_system-{{$i}} hidden">{{ $returnData['size_system'] }}</span>
                                        @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'size_system', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('Price', $dynamicColumnsToShowscrapper))
                                    <td>
                                        @if(!empty($returnData['price'])) {{ $returnData['price'] }} @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'price', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('Discounted Price', $dynamicColumnsToShowscrapper))
                                    <td>
                                        @if(!empty($returnData['discounted_price'])) {{ $returnData['discounted_price'] }} @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'discounted_price', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('Discounted Percentage', $dynamicColumnsToShowscrapper))
                                    <td>
                                        @if(!empty($returnData['discounted_percentage'])) {{ $returnData['discounted_percentage'] }} @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'discounted_percentage', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('B2b Price', $dynamicColumnsToShowscrapper))
                                    <td>
                                        @if(!empty($returnData['b2b_price'])) {{ $returnData['b2b_price'] }} @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'b2b_price', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('Brand', $dynamicColumnsToShowscrapper))
                                    <td class="expand-row-msg" data-name="brand" data-id="{{$i}}">
                                        @if(!empty($returnData['brand']))
                                            <span class="show-short-brand-{{$i}}">{{ Str::limit($returnData['brand'], 5, '...')}}</span>
                                            <span style="word-break:break-all;" class="show-full-brand-{{$i}} hidden">{{ $returnData['brand'] }}</span>
                                        @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'brand', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('Is Sale', $dynamicColumnsToShowscrapper))
                                    <td>
                                        @if(!empty($returnData['is_sale'])) {{ $returnData['is_sale'] }} @endif

                                        @include('development.partials.dynamic-column', ['columnname' => 'is_sale', 'taskss_id' => $record['task_id']])
                                    </td>
                                @endif

                                @if (!in_array('Date', $dynamicColumnsToShowscrapper))
                                    <td class="expand-row-msg" data-name="created_at" data-id="{{$i}}">
                                        <span class="show-short-created_at-{{$i}}">{{ Str::limit($record['created_at'], 5, '...')}}</span>
                                        <span style="word-break:break-all;" class="show-full-created_at-{{$i}} hidden">{{ $record['created_at'] }}</span>
                                    </td>
                                @endif
                                @if (!in_array('Action', $dynamicColumnsToShowscrapper))
                                    <td><a target="_blank" href="{{ route('development.scrapper_hisotry', ['id' => $record['max_id']]) }}"><i class="fa fa-info-circle" aria-hidden="true"></i></a></td>
                                @endif
                            </tr>
                        @else  
                            <tr>
								<td>
                                    {{ $record['max_id'] }}
                                    </br>

                                    @php
                                        $ScrapperValuesHistoryCount = $records->flatMap(function ($record) {
                                                return $record->scrappervalueshistory->where('status', 'Approve');
                                            })->count();    
                                        
                                        $checkBoX = '';
                                        if($ScrapperValuesHistoryCount==20){
                                            $checkBoX = 'checked';
                                        }
                                    @endphp

                                    <input type="checkbox" class="approveAll approveAll_{{ $record['max_id'] }}" title="Approve All Values" name="approveAll" id="approveAll" data-id="{{ $record['max_id'] }}" style="padding: 0; margin: 0; height: 15px;" {{$checkBoX}}>

                                    {{-- Add button to view specific scrapper DEVTASK-24690 --}}
                                    <button style="padding-left: 0;padding-left:3px;" type="button"
                                        class="btn btn-image d-inline count-dev-scrapper count-dev-scrapper_{{ $record['max_id'] }}"
                                        title="View scrapper" data-id="{{ $record['max_id'] }}" data-category="{{ $record['max_id'] }}">
                                            <i class="fa fa-list"></i>
                                    </button>

                                    @if(!empty($returnData['url']))
                                        <button style="padding-left: 0;padding-left:3px;" type="button"
                                            class="btn btn-image d-inline load-iframe-scrapper"
                                            title="Open both" data-id="{{ $record['max_id'] }}" data-category="{{ $record['max_id'] }}"
                                            data-iframe-url={{ $returnData['url'] }}>
                                                <i class="fa fa-list"></i>
                                        </button>
                                    @endif
                                </td>
                                <td class="expand-row-msg" data-name="task_id" data-id="{{$i}}">
                                    <span class="show-short-task_id-{{$i}}">{{ Str::limit('#DEVTASK-'.$record['task_id'], 10, '...')}}</span>
                                    <span style="word-break:break-all;" class="show-full-task_id-{{$i}} hidden">#DEVTASK-{{ $record['task_id'] }}</span>
                                </td>
                                <td class="expand-row-msg" data-name="subject" data-id="{{$i}}">
                                    @if(!empty($record['tasks']['subject']))
                                        <span class="show-short-subject-{{$i}}">{{ Str::limit($record['tasks']['subject'], 10, '...')}}</span>
                                        <span style="word-break:break-all;" class="show-full-subject-{{$i}} hidden">{{ $record['tasks']['subject'] }}</span>
                                    @endif
                                </td>
                                <td class="expand-row-msg" data-name="title" data-id="{{$i}}">
                                    @if(!empty($returnData['title']))
                                        <span class="show-short-title-{{$i}}">{{ Str::limit($returnData['title'], 10, '...')}}</span>
                                        <span style="word-break:break-all;" class="show-full-title-{{$i}} hidden">{{ $returnData['title'] }}</span>
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'title', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="website" data-id="{{$i}}">
                                    @if(!empty($returnData['website']))
                                        <span class="show-short-website-{{$i}}">{{ Str::limit($returnData['website'], 10, '...')}}</span>
                                        <span style="word-break:break-all;" class="show-full-website-{{$i}} hidden">{{ $returnData['website'] }}</span>
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'website', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="sku" data-id="{{$i}}">
                                    @if(!empty($returnData['sku']))
                                        <span class="show-short-sku-{{$i}}">{{ Str::limit($returnData['sku'], 10, '...')}}</span>
                                        <span style="word-break:break-all;" class="show-full-sku-{{$i}} hidden">{{ $returnData['sku'] }}</span>
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'sku', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="url" data-id="{{$i}}">
                                    @if(!empty($returnData['url']))
                                        <span class="show-short-url-{{$i}}"><a class="iframe-url" href="#" data-iframe-url={{ $returnData['url'] }}>{{ Str::limit($returnData['url'], 10, '...')}}</a></span>
                                        <span style="word-break:break-all;" class="show-full-url-{{$i}} hidden"><a href="#" class="iframe-url" data-iframe-url={{ $returnData['url'] }}>{{ $returnData['url'] }}</a></span>
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'url', 'taskss_id' => $record['task_id']])
                                </td>
                                <td>
                                    <button type="button" data-id="<?php echo $record['max_id']; ?>" class="btn scrapper-images" style="padding:1px 0px;">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>

                                    @include('development.partials.dynamic-column', ['columnname' => 'images', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="description" data-id="{{$i}}">
                                    @if(!empty($returnData['description']))
                                        <span class="show-short-description-{{$i}}">{{ Str::limit($returnData['description'], 10, '...')}}</span>
                                        <span style="word-break:break-all;" class="show-full-description-{{$i}} hidden">{{ $returnData['description'] }}</span>
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'description', 'taskss_id' => $record['task_id']]) 
                                </td>
                                <td class="expand-row-msg" data-name="dimension" data-id="{{$i}}">
                                    @if(!empty($returnData['properties']))
                                        @if(!empty($returnData['properties']['dimension']))
                                            @if(is_array($returnData['properties']['dimension'])) 
                                                <span class="show-short-dimension-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['dimension']), 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-dimension-{{$i}} hidden">{{ implode("," , $returnData['properties']['dimension']) }}</span>
                                            @else
                                                <span class="show-short-dimension-{{$i}}">{{ Str::limit($returnData['properties']['dimension'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-dimension-{{$i}} hidden">{{ $returnData['properties']['dimension'] }}</span>
                                            @endif
                                        @endif
                                    @endif
                                    <!-- <button type="button" data-id="<?php echo $record['max_id']; ?>" class="btn scrapper-properties" style="padding:1px 0px;">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button> -->

                                    @include('development.partials.dynamic-column', ['columnname' => 'dimension', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="sizes" data-id="{{$i}}">
                                    @if(!empty($returnData['properties']))
                                        @if(!empty($returnData['properties']['sizes']))
                                            @if(is_array($returnData['properties']['sizes'])) 
                                                <span class="show-short-sizes-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['sizes']), 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-sizes-{{$i}} hidden">{{ implode("," , $returnData['properties']['sizes']) }}</span>
                                            @else
                                                <span class="show-short-sizes-{{$i}}">{{ Str::limit($returnData['properties']['sizes'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-sizes-{{$i}} hidden">{{ $returnData['properties']['sizes'] }}</span>
                                            @endif
                                        @endif
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'sizes', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="material_used" data-id="{{$i}}">
                                    @if(!empty($returnData['properties']))
                                        @if(!empty($returnData['properties']['material_used']))
                                            @if(is_array($returnData['properties']['material_used'])) 
                                                <span class="show-short-material_used-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['material_used']), 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-material_used-{{$i}} hidden">{{ implode("," , $returnData['properties']['material_used']) }}</span>
                                            @else
                                                <span class="show-short-material_used-{{$i}}">{{ Str::limit($returnData['properties']['material_used'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-material_used-{{$i}} hidden">{{ $returnData['properties']['material_used'] }}</span>
                                            @endif
                                        @endif
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'material_used', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="category" data-id="{{$i}}">
                                    @if(!empty($returnData['properties']))
                                        @if(!empty($returnData['properties']['category']))
                                            @if(is_array($returnData['properties']['category'])) 
                                                <span class="show-short-category-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['category']), 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-category-{{$i}} hidden">{{ implode("," , $returnData['properties']['category']) }}</span>
                                            @else
                                                <span class="show-short-category-{{$i}}">{{ Str::limit($returnData['properties']['category'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-category-{{$i}} hidden">{{ $returnData['properties']['category'] }}</span>
                                            @endif
                                        @endif
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'category', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="color" data-id="{{$i}}">
                                    @if(!empty($returnData['properties']))
                                        @if(!empty($returnData['properties']['color']))
                                            @if(is_array($returnData['properties']['color'])) 
                                                <span class="show-short-color-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['color']), 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-color-{{$i}} hidden">{{ implode("," , $returnData['properties']['color']) }}</span>
                                            @else
                                                <span class="show-short-color-{{$i}}">{{ Str::limit($returnData['properties']['color'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-color-{{$i}} hidden">{{ $returnData['properties']['color'] }}</span>
                                            @endif
                                        @endif
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'color', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="country" data-id="{{$i}}">
                                    @if(!empty($returnData['properties']))
                                        @if(!empty($returnData['properties']['country']))
                                            @if(is_array($returnData['properties']['country'])) 
                                                <span class="show-short-country-{{$i}}">{{ Str::limit(implode("," , $returnData['properties']['country']), 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-country-{{$i}} hidden">{{ implode("," , $returnData['properties']['country']) }}</span>
                                            @else
                                                <span class="show-short-country-{{$i}}">{{ Str::limit($returnData['properties']['country'], 10, '...')}}</span>
                                                <span style="word-break:break-all;" class="show-full-country-{{$i}} hidden">{{ $returnData['properties']['country'] }}</span>
                                            @endif
                                        @endif
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'country', 'taskss_id' => $record['task_id']])
                                </td>
                                <td>
                                    @if(!empty($returnData['currency'])) {{ $returnData['currency'] }} @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'currency', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="size_system" data-id="{{$i}}">
                                    @if(!empty($returnData['size_system']))
                                        <span class="show-short-size_system-{{$i}}">{{ Str::limit($returnData['size_system'], 10, '...')}}</span>
                                        <span style="word-break:break-all;" class="show-full-size_system-{{$i}} hidden">{{ $returnData['size_system'] }}</span>
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'size_system', 'taskss_id' => $record['task_id']])
                                </td>
                                <td>
                                    @if(!empty($returnData['price'])) {{ $returnData['price'] }} @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'price', 'taskss_id' => $record['task_id']])
                                </td>
                                <td>
                                    @if(!empty($returnData['discounted_price'])) {{ $returnData['discounted_price'] }} @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'discounted_price', 'taskss_id' => $record['task_id']])
                                </td>
                                <td>
                                    @if(!empty($returnData['discounted_percentage'])) {{ $returnData['discounted_percentage'] }} @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'discounted_percentage', 'taskss_id' => $record['task_id']])
                                </td>
                                <td>
                                    @if(!empty($returnData['b2b_price'])) {{ $returnData['b2b_price'] }} @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'b2b_price', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="brand" data-id="{{$i}}">
                                    @if(!empty($returnData['brand']))
                                        <span class="show-short-brand-{{$i}}">{{ Str::limit($returnData['brand'], 5, '...')}}</span>
                                        <span style="word-break:break-all;" class="show-full-brand-{{$i}} hidden">{{ $returnData['brand'] }}</span>
                                    @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'brand', 'taskss_id' => $record['task_id']])
                                </td>
                                <td>
                                    @if(!empty($returnData['is_sale'])) {{ $returnData['is_sale'] }} @endif

                                    @include('development.partials.dynamic-column', ['columnname' => 'is_sale', 'taskss_id' => $record['task_id']])
                                </td>
                                <td class="expand-row-msg" data-name="created_at" data-id="{{$i}}">
                                    <span class="show-short-created_at-{{$i}}">{{ Str::limit($record['created_at'], 5, '...')}}</span>
                                    <span style="word-break:break-all;" class="show-full-created_at-{{$i}} hidden">{{ $record['created_at'] }}</span>
                                </td>
                                <td><a target="_blank" href="{{ route('development.scrapper_hisotry', ['id' => $record['max_id']]) }}"><i class="fa fa-info-circle" aria-hidden="true"></i></a></td>
							</tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
			{{$records->links()}}
        </div>
    </div>    
    @include('development.partials.scrapper-properties')
    @include('development.partials.scrapper-images')
    @include('development.partials.column-visibility-scrapper-modal')

    <div id="update-scrapper-status-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="<?php echo route('development.updatescrapperdata'); ?>" method="post" id="update-scrapper-status-modal-form">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title text-left">Update Status & Remarks</h4>
                    </div>
                    <div class="modal-body">

                        <input class="form-control" type="hidden" name="task_id" id="scrapper_task_id" />
                        <input class="form-control" type="hidden" name="column_name" id="scrapper_title" />
                    
                        <div class="form-group">
                            <select class="form-control" name="status" onchange="changeStatus(this.value)" id="status_div_id">
                                <option>--Select Status--</option>
                                <option value="Approve">Approve</option>
                                <option value="Unapprove">Unapprove</option>
                            </select>
                        </div>

                        <div class="form-group" id="remarks_div" style="display:none;">
                            <textarea class="form-control" name="remarks" id="remarks_div_id" placeholder="Enter Remarks" s></textarea>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-default update-scrapper-status-data">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="update-scrapper-remarks-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="<?php echo route('development.updatescrapperremarksdata'); ?>" method="post">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title text-left">Update Remarks</h4>
                    </div>
                    <div class="modal-body">

                        <input class="form-control" type="hidden" name="task_id" id="scrapper_task_id" />
                        <input class="form-control" type="hidden" name="column_name" id="scrapper_title" />
                        
                        <textarea class="form-control" name="remarks" placeholder="Enter Remarks"></textarea>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-default update-scrapper-remarks-data">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="view-scrapper-remarks-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-left">Remarks</h4>
                </div>
                <div class="modal-body">
                    <p id="view-remarks-data"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Scrapper view modal and iframe. DEVTASK-24690 --}}
    <div>
        <div id="dev_scrapper_statistics" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="p-0 m-0">Scrapper Statistics</h4>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body" id="dev_scrapper_statistics_content">
                    </div>
                </div>
            </div>
        </div>
    
        <div id="iframe_modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-content">
                    <div class="modal-body">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script>
$(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
});

$(document).on('click','.scrapper-properties',function(){
    id = $(this).data('id');
    $.ajax({
        method: "GET",
        url: `{{ route('development.scrapper_data', [""]) }}/` + id,
        dataType: "json",
        success: function(response) {
           
            $("#scrapper-properties-data").find(".scrapper-properties-data-view").html(response.html);
            $("#scrapper-properties-data").modal("show");
     
        }
    });
});

$(document).on('click','.scrapper-images',function(){
    id = $(this).data('id');
    $.ajax({
        method: "GET",
        url: `{{ route('development.scrapper_images_data', [""]) }}/` + id,
        dataType: "json",
        success: function(response) {
           
            $("#scrapper-images-data").find(".scrapper-images-data-view").html(response.html);
            $("#scrapper-images-data").modal("show");
     
        }
    });
});

$(document).on('click', '.update-scrapper-status', function() {

    //$('#update-scrapper-status-modal-form')[0].reset();

    $('#update-scrapper-status-modal #remarks_div').css('display', 'none');
    $('#update-scrapper-status-modal #remarks_div_id').val('');
    //$('#status_div_id option[value=""]').attr("selected", "selected");

    $('#status_div_id').prop('selectedIndex', 0);


    var $this = $(this);
    column_name = $(this).data("column_name");
    task_id = $(this).data("task_id");

    $.ajax({
        url: "{{route('development.getscrapperdata')}}",
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            'task_id' :task_id,
            'column_name' :column_name
        },
        beforeSend: function() {
            $(this).text('Loading...');
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();

            if(response.ScrapperValuesHistory.id!=''){
                if(response.ScrapperValuesHistory.status=='Unapprove'){

                    $('#update-scrapper-status-modal #remarks_div').css('display', '');
                    
                    if(response.ScrapperValuesRemarksHistory.id!=''){
                        $('#update-scrapper-status-modal #remarks_div_id').val(response.ScrapperValuesRemarksHistory.remarks);
                    }

                    //$('#status_div_id option[value="Unapprove"]').attr("selected", "selected");
                    $('#status_div_id').prop('selectedIndex', 2);
                } else {
                    //$('#status_div_id option[value="Approve"]').attr("selected", "selected");
                    $('#status_div_id').prop('selectedIndex', 1);
                }
            }
        }
    }).fail(function(response) {
        $("#loading-image").hide();
        toastr['error'](response.responseJSON.message);
    });

    $("#update-scrapper-status-modal").modal("show");
    $("#update-scrapper-status-modal #scrapper_task_id").val(task_id);
    $('#update-scrapper-status-modal #scrapper_title').val(column_name);
});

$(document).on("click", ".update-scrapper-status-data", function(e) {
    e.preventDefault();
    var form = $(this).closest("form");
    $.ajax({
        url: form.attr("action"),
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: form.serialize(),
        beforeSend: function() {
            $(this).text('Loading...');
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                form[0].reset();
                toastr['success'](response.message);
                $("#update-scrapper-status-modal").modal("hide");
                location.reload();
            } else {
                toastr['error'](response.message);
            }
        }
    }).fail(function(response) {
        $("#loading-image").hide();
        toastr['error'](response.responseJSON.message);
    });
});

$(document).on('click', '.update-scrapper-remarks', function() {
    var $this = $(this);
    column_name = $(this).data("column_name");
    task_id = $(this).data("task_id");
    
    $("#update-scrapper-remarks-modal").modal("show");
    $("#update-scrapper-remarks-modal #scrapper_task_id").val(task_id);
    $('#update-scrapper-remarks-modal #scrapper_title').val(column_name);
});

$(document).on("click", ".update-scrapper-remarks-data", function(e) {
    e.preventDefault();
    var form = $(this).closest("form");
    $.ajax({
        url: form.attr("action"),
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: form.serialize(),
        beforeSend: function() {
            $(this).text('Loading...');
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                form[0].reset();
                toastr['success'](response.message);
                $("#update-scrapper-remarks-modal").modal("hide");
            } else {
                toastr['error'](response.message);
            }
        }
    }).fail(function(response) {
        $("#loading-image").hide();
        toastr['error'](response.responseJSON.message);
    });
});

$(document).on('click', '.view-scrapper-remarks', function() {
    var $this = $(this);
    remarks = $(this).data("remarks");
    
    $("#view-scrapper-remarks-modal").modal("show");
    $("#view-scrapper-remarks-modal #view-remarks-data").text(remarks);
});

function changeStatus(value){

    if(value=='Unapprove'){
        $('#update-scrapper-status-modal #remarks_div').css('display', '');
    } else {
        $('#update-scrapper-status-modal #remarks_div').css('display', 'none');
    }
    
}

$(document).ready(function() {
    // Attach change event handler to the checkbox
    $('.approveAll').change(function() {

        var dataIdValue = $(this).data('id');

        // Check if the checkbox is checked
        if ($(this).is(':checked')) {

            var confirmed = confirm('Are you sure you want to approved the values?');

            if (confirmed) {

                $.ajax({
                    url: "{{route('development.updateallstatusdata')}}",
                    type: 'POST',
                    headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'scrapper_id' : dataIdValue,
                        'type' : 1
                    },
                    beforeSend: function() {
                        $(this).text('Loading...');
                        $("#loading-image").show();
                    },
                    success: function(response) {
                        $("#loading-image").hide();
                        if (response.status == true) {
                            location.reload();
                            toastr['success'](response.message);
                        } else {
                            toastr['error'](response.message);
                        }
                    }
                }).fail(function(response) {
                    $("#loading-image").hide();
                    toastr['error'](response.responseJSON.message);
                });

            } else {    
                $('.approveAll approveAll_'+dataIdValue).prop('checked', false);
            }
        } else {

            var confirmed = confirm('Are you sure you want to remove the updated status?');

            if (confirmed) {

                $.ajax({
                    url: "{{route('development.updateallstatusdata')}}",
                    type: 'POST',
                    headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'scrapper_id' : dataIdValue,
                        'type' : 2
                    },
                    beforeSend: function() {
                        $(this).text('Loading...');
                        $("#loading-image").show();
                    },
                    success: function(response) {
                        $("#loading-image").hide();
                        if (response.status == true) {
                            location.reload();
                            toastr['success'](response.message);
                        } else {
                            toastr['error'](response.message);
                        }
                    }
                }).fail(function(response) {
                    $("#loading-image").hide();
                    toastr['error'](response.responseJSON.message);
                });

            } else {    
                $('.approveAll approveAll_'+dataIdValue).prop('checked', true);
            }
        }        
    });
});

// Add scrapper view and update functions, open URL in iframe functions. DEVTASK-24690
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

$(document).on("click", ".count-dev-scrapper", function() {
    $("#dev_scrapper_statistics").css("width", "100%");
    var task_id = $(this).data("id");
    openScrapperModal(task_id);
});

$(document).on("change", ".add-scrapper-status", function(e) {

    let task_id = $(this).data("taskid");
    let column_name = $(this).data("value");
    var status = $(this).val();

    $.ajax({
        url: "{{route('development.updatescrapperdata')}}",
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
        },
        data: {
            "task_id": task_id,
            "column_name": column_name,
            "status": status
        },
        beforeSend: function() {
            $(this).text("Loading...");
            $("#loading-image").show();
        },
        success: function(response) {
            $(".count-dev-scrapper_" + task_id).trigger("click");
            $("#loading-image").hide();
            if (response.code == 200) {
            toastr["success"](response.message);
            } else {
            toastr["error"](response.message);
            }

            window.location.reload();
        }
    }).fail(function(response) {
        $("#loading-image").hide();
        toastr["error"](response.responseJSON.message);
    });
});

$(document).on("click", ".add-scrapper-remarks", function() {

    let task_id = $(this).data("taskid");
    let column_name = $(this).data("value");
    var remarks = $("#remarks_values_" + task_id + "_" + column_name).val();

    $.ajax({
        url: "{{route('development.updatescrapperremarksdata')}}",
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
        },
        data: {
            "task_id": task_id,
            "column_name": column_name,
            "remarks": remarks
        },
        beforeSend: function() {
            $(this).text("Loading...");
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();
            if (response.code == 200) {
            toastr["success"](response.message);
            } else {
            toastr["error"](response.message);
            }
        }
    }).fail(function(response) {
        $("#loading-image").hide();
        toastr["error"](response.responseJSON.message);
    });
});

$('.iframe-url').click(function() {
    $("#iframe_modal").css("width", "100%");
    var iframe_url = $(this).data("iframe-url");
    openURLModal(iframe_url);
})

$('.load-iframe-scrapper').click(function() {
    $("#iframe_modal").css("width", "50%");
    $("#dev_scrapper_statistics").css("width", "50%");
    var iframe_url = $(this).data("iframe-url");
    var task_id = $(this).data("id");
    openURLModal(iframe_url);
    openScrapperModal(task_id);
})

function openURLModal(iframe_url){
    var load_iframe = '<iframe src="'+iframe_url+'" frameborder="0" allowfullscreen style="width: -webkit-fill-available;height: 500px;"></iframe>';
    $("#iframe_modal .modal-body").html(load_iframe);

    $('#iframe_modal').modal({show:true})
}

function openScrapperModal(task_id){
    $.ajax({
        type: "get",
        url: "/api/development/view-scrapper/" + task_id,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
        },
        beforeSend: function() {
            $("#loading-image").show();
        },
        success: function(data) {

            $("#dev_scrapper_statistics").modal("show");
            var table = `<div class="table-responsive">
                        <table class="table table-bordered table-striped table-scrapper" style="font-size:14px;">`;
            table = table + "<tr>";
            table = table + "<th width=\"10%\">Column Name</th>";
            table = table + "<th width=\"30%\">Values</th>";
            table = table + "<th width=\"15%\">Status</th>";
            table = table + "<th width=\"45%\">Remarks</th>";
            table = table + "</tr>";
            if (data.values != "") {

                $.each(data.values, function(key, value) {
                    table = table + "<tr>";
                    table = table + "<th>" + capitalizeFirstLetter(key.replace("_", " "));
                    table = table + "</th>";

                    if (key == "properties") {

                        var iiii = 1;
                        if (data.values.properties != "") {
                            table = table + "<td><table class=\"table table-bordered table-striped\">";
                            $.each(data.values.properties, function(key, value) {
                                if (key == "category") {
                                    if (data.values.properties.category) {

                                        $.each(data.values.properties.category, function(keyC, valueC) {

                                            table = table + "<tr>";
                                            table = table + "<th>" + capitalizeFirstLetter(key.replace("_", " ")) + " " + iiii;
                                            table = table + "</th>";
                                            table = table + "<td>" + valueC;
                                            table = table + "</td>";
                                            table = table + "</tr>";

                                            iiii++;
                                        });
                                    }
                                } else {
                                    table = table + "<tr>";
                                    table = table + "<th>" + capitalizeFirstLetter(key.replace("_", " "));
                                    table = table + "</th>";
                                    table = table + "<td>" + value;
                                    table = table + "</td>";
                                    table = table + "</tr>";
                                }
                            });
                            table = table + "</table></td>";

                            var approveValue = "";
                            var unapproveValue = "";
                            var StatusValue = "";
                            for (var i = 0; i < data.ScrapperValuesHistory.length; i++) {

                                if (data.ScrapperValuesHistory[i].column_name == key) {

                                    StatusValue = data.ScrapperValuesHistory[i].status;

                                    if (StatusValue == "Approve") {
                                        approveValue = "selected";
                                    }

                                    if (StatusValue == "Unapprove") {
                                        unapproveValue = "selected";
                                    }
                                }
                            }

                            var remarksValue = "";
                            for (var i = 0; i < data.ScrapperValuesRemarksHistory.length; i++) {
                                if (data.ScrapperValuesRemarksHistory[i].column_name == key) {
                                    remarksValue = data.ScrapperValuesRemarksHistory[i].remarks;
                                }
                            }

                            @if (Auth::user()->isAdmin())
                                table = table + "<td>";
                                table = table + "<select class=\"add-scrapper-status form-control\" id=\"status_values_" + data.task_id + "_" + key + "\" data-value=\"" + key + "\" data-taskid=\"" + data.task_id + "\">";
                                table = table + "<option>Select Status</option>";
                                table = table + "<option " + approveValue + " value=\"Approve\">Approve</option>";
                                table = table + "<option " + unapproveValue + " value=\"Unapprove\">Unapprove</option>";
                                table = table + "</select>";
                                table = table + "</td>";

                                table = table + "<td>";

                                if (unapproveValue == "selected") {
                                    table = table + "<textarea rows=\"1\" class=\"add-scrapper-textarea form-control\" id=\"remarks_values_" + data.task_id + "_" + key + "\">" + remarksValue + "</textarea>";

                                    table = table + "<button class=\"btn btn-sm btn-image add-scrapper-remarks\"  title=\"Send approximate\" data-taskid=\"" + data.task_id + "\" data-value=\"" + key + "\"><i class=\"fa fa-paper-plane\" aria-hidden=\"true\"></i></button></button>";
                                }

                                table = table + "</td>";
                            @else
                                table = table + "<td>" + StatusValue + "</td>";

                                table = table + "<td>";
                                if (unapproveValue == "selected") {
                                    table = table + remarksValue;
                                }
                                table = table + "</td>";
                            @endif
                        }
                    } else if (key == "images") {
                        if (data.values.images != "") {
                            table = table + "<td><table class=\"table table-bordered table-striped\">";
                            table = table + "<tr><td>";
                            $.each(data.values.images, function(key, value) {
                                table = table + "<img src=\"" + value + "\" width=\"50px\" style=\"cursor: default;margin-right: 10px;\">";
                            });
                            table = table + "</td></tr>";
                            table = table + "</table></td>";

                            var approveValue = "";
                            var unapproveValue = "";
                            var StatusValue = "";
                            for (var i = 0; i < data.ScrapperValuesHistory.length; i++) {

                                if (data.ScrapperValuesHistory[i].column_name == key) {

                                    StatusValue = data.ScrapperValuesHistory[i].status;

                                    if (StatusValue == "Approve") {
                                    approveValue = "selected";
                                    }

                                    if (StatusValue == "Unapprove") {
                                    unapproveValue = "selected";
                                    }

                                }
                            }

                            var remarksValue = "";
                            for (var i = 0; i < data.ScrapperValuesRemarksHistory.length; i++) {
                                if (data.ScrapperValuesRemarksHistory[i].column_name == key) {
                                    remarksValue = data.ScrapperValuesRemarksHistory[i].remarks;
                                }
                            }

                            @if (Auth::user()->isAdmin())

                                table = table + "<td>";
                                table = table + "<select class=\"add-scrapper-status form-control\" id=\"status_values_" + data.task_id + "_" + key + "\" data-value=\"" + key + "\" data-taskid=\"" + data.task_id + "\">";
                                table = table + "<option>--Select Status--</option>";
                                table = table + "<option " + approveValue + " value=\"Approve\">Approve</option>";
                                table = table + "<option " + unapproveValue + " value=\"Unapprove\">Unapprove</option>";
                                table = table + "</select>";
                                table = table + "</td>";

                                table = table + "<td>";
                                if (unapproveValue == "selected") {
                                    table = table + "<textarea rows=\"1\" class=\"add-scrapper-textarea form-control\" id=\"remarks_values_" + data.task_id + "_" + key + "\">" + remarksValue + "</textarea>";

                                    table = table + "<button class=\"btn btn-image add-scrapper-remarks\"  title=\"Send approximate\" data-taskid=\"" + data.task_id + "\" data-value=\"" + key + "\"><i class=\"fa fa-paper-plane\" aria-hidden=\"true\"></i></button>";
                                }

                                table = table + "</td>";
                            @else
                                table = table + "<td>" + StatusValue + "</td>";

                                table = table + "<td>";
                                if (unapproveValue == "selected") {
                                    table = table + remarksValue;
                                }
                                table = table + "</td>";
                            @endif
                        }
                    } else {

                        if (key == "url") {
                            table = table + "<td><a href=\"" + value + "\" target=\"_blank\">" + value + "</a>";
                            table = table + "</td>";
                        } else {
                            table = table + "<td>" + value;
                            table = table + "</td>";
                        }

                        var approveValue = "";
                        var unapproveValue = "";
                        var StatusValue = "";
                        for (var i = 0; i < data.ScrapperValuesHistory.length; i++) {

                            if (data.ScrapperValuesHistory[i].column_name == key) {

                            StatusValue = data.ScrapperValuesHistory[i].status;

                            if (StatusValue == "Approve") {
                                approveValue = "selected";
                            }

                            if (StatusValue == "Unapprove") {
                                unapproveValue = "selected";
                            }

                            }
                        }

                        var remarksValue = "";
                        for (var i = 0; i < data.ScrapperValuesRemarksHistory.length; i++) {

                            if (data.ScrapperValuesRemarksHistory[i].column_name == key) {

                            remarksValue = data.ScrapperValuesRemarksHistory[i].remarks;

                            }
                        }

                        @if (Auth::user()->isAdmin())
                            table = table + "<td>";
                            table = table + "<select class=\"add-scrapper-status form-control\" id=\"status_values_" + data.task_id + "_" + key + "\" data-value=\"" + key + "\" data-taskid=\"" + data.task_id + "\">";
                            table = table + "<option>--Select Status--</option>";
                            table = table + "<option " + approveValue + " value=\"Approve\">Approve</option>";
                            table = table + "<option " + unapproveValue + " value=\"Unapprove\">Unapprove</option>";
                            table = table + "</select>";
                            table = table + "</td>";

                            table = table + "<td>";

                            if (unapproveValue == "selected") {
                                table = table + "<textarea rows=\"1\" class=\"add-scrapper-textarea form-control\" id=\"remarks_values_" + data.task_id + "_" + key + "\">" + remarksValue + "</textarea> ";

                                table = table + "<button class=\"btn btn-image add-scrapper-remarks\"  title=\"Send approximate\" data-taskid=\"" + data.task_id + "\" data-value=\"" + key + "\"><i class=\"fa fa-paper-plane\" aria-hidden=\"true\"></i></button>";
                            }

                            table = table + "</td>";
                        @else
                            table = table + "<td>" + StatusValue + "</td>";

                            table = table + "<td>";
                            if (unapproveValue == "selected") {
                                table = table + remarksValue;
                            }
                            table = table + "</td>";
                        @endif
                    }
                    table = table + "</tr>";
                });
            }

            table = table + "</table></div>";
            $("#loading-image").hide();
            $(".modal").css("overflow-x", "hidden");
            $(".modal").css("overflow-y", "auto");
            $("#dev_scrapper_statistics_content").html(table);
        },
        error: function(error) {
            console.log(error);
            $("#loading-image").hide();
        }
    });
}
</script>
@endsection
