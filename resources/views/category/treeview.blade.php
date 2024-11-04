@extends('layouts.app')

@section('content')
    <style>
        .btn-secondary {
            color: #757575;
            border: 1px solid #ddd;
            background-color: #fff;
        }

        table {
            word-break: break-all;
        }

    </style>

    <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">
        Category
        <div class="margin-tb" style="flex-grow: 1;">
            <div class="pull-right ">


                <div class="d-flex justify-content-between  mx-3">
                    <button type="button" class="btn btn-secondary my-0 mr-3" data-toggle="modal" data-target="#copy-category-popup">
                        Copy category data
                    </button>
                    <a href="{{ route('category.map-category') }}" class="btn btn-secondary my-0 mr-3">Edit
                        References</a>
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#category-popup">
                        Add category
                    </button>
                </div>
            </div>
        </div>
    </h2>
    <form action="{{ route('category', ['filter' => true]) }}" class="d-block filter_category_form mx-4"
        id="filter_category_form">
        <div class="form-group mb-3">
            <input style="border: 1px solid #ddd;height:30px;border-radius: 4px; padding: 0 5px;" type="text"
                placeholder="Enter name" name="filter" id="filter_all_category" value="{{ $selected_value }}">
            <button class="btn"><img src="/images/filter.png" style="width:16px"></button>
            <a href="#" onclick="location.reload();
                    " type="button" class="btn btn-image" id=""><img src="/images/resend2.png"
                    style="cursor: nwse-resize;"></a>
        </div>
    </form>


    {{-- <!-- Add category modal --> --}}
    <div class="modal fade" id="copy-category-popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Copy Category Items</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                <form method="POST" action="{{ route('category.storeCopy') }}" id="copy_category_data">
                    @csrf
                    <div class="form-group mb-0">
                        <label for="sourceCategoryId">Source Category Id</label>
                        <select class="form-control" name="sourceCategoryId" id="sourceCategoryId" required>
                                <option value=''>Select Source Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="targetCategoryId">Target Category Id</label>
                        <select class="form-control" name="targetCategoryId" id="targetCategoryId" required>
                                <option value=''>Select Target Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                    </div>
                        
                    <div class="form-group d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-secondary">Start Copy</button>
                    </div>
                </form>
            </div>

            </div>
        </div>
    </div>

    {{-- <!-- Add category modal --> --}}
    <div class="modal fade" id="category-popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Add New Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                    <form method="POST" action="{{ route('add.category') }}">
                        @csrf
                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }} mb-0">
                            <label for="title_category">New Title</label>
                            <input type="text" class="form-control" id="title_category" name="title"
                                placeholder="Enter title" required>


                        </div>

                        <div class="form-group {{ $errors->has('magento_id') ? 'has-error' : '' }}">

                            <label for="category_magento_id">Magento Id:</label>
                            <input type="number" class="form-control" id="category_magento_id" name="magento_id"
                                placeholder="Enter Magento Id" required>
                        </div>


                        <div class="form-group {{ $errors->has('show_all_id') ? 'has-error' : '' }}">

                            <label for="category_show_all_id">Show All Id:</label>
                            <input type="number" class="form-control" id="category_show_all_id" name="show_all_id"
                                placeholder="Enter Show All Id" required>

                        </div>

                        <div class="form-group">
                            <label for="cat_category_segment_id">Select Category Segment:</label>

                            <select class="form-control" name="category_segment_id" id="category_segment_id">
                                <option>Select Category Segment</option>
                                @foreach ($category_segments as $k => $catSeg)

                                    <option value="{{ $k }}">{{ $catSeg }}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                            {{ html()->label('Category:') }}

                            {{ $allCategoriesDropdown }}
                            <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                        </div>
                        <div
                            class="d-flex justify-content-between form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                            <div>
                                <input type="checkbox" id="need_to_check_measurement" name="need_to_check_measurement"
                                    value="1">
                                <label for="need_to_check_measurement"> Need to Check Measurement</label>
                            </div>
                            <div>
                                <input type="checkbox" id="need_to_check_size" name="need_to_check_size" value="1">
                                <label for="need_to_check_size"> Need to Check Size</label>
                            </div>
                        </div>


                        <div class="form-group d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-secondary">Create</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <div class="col-md-12">
        @if ($message = Session::get('error-remove'))
            <div class="alert alert-danger alert-block py-1">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        @if ($message = Session::get('success-remove'))
            <div class="alert alert-success alert-block py-1">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif


        <div class="infinite-scroll-new">
            {!! $categories->appends(request()->query())->links() !!}
            <div class="table-responsive">
                <table class="table table-bordered" style="table-layout:fixed;">
                    <thead>
                        <tr>

                            <th style="width:6%">Level-1 </th>
                            <th style="width:6%">Level-2</th>
                            <th style="width:6%">Level-3</th>
                            <th style="width:6%">Level-4</th>

                            <th style="width:8%"> Magento id</th>
                            <th style="width:8%"> Days Cancelation</th>
                            <th style="width:8%"> Days Refund</th>
                            <th style="width:8%"> Show all id </th>
                            <th style="width:9%"> SH code </th>
                            <th style="width:7%"> Select category segmanet </th>
                            <th style="width:9%"> Parent category </th>

                            <th style="width:4%"> Check Measurement </th>
                            <th style="width:4%"> Check Size </th>
                            <th style="width:4%"> Size Chart </th>

                            <th style="width:7%"> Push Type</th>

                            <th style="width:3%">Action</th>
                        </tr>
                    </thead>
                  
                    @foreach ($categories as $key => $cat)
                        <tr class="parent-cat">
                       
                                <td class="pb-0">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id={{ $cat->id }}>
                                        @csrf
                                        <div class="d-flex align-items-baseline">
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control" name="title"
                                                    placeholder="Enter Show All Id" value="{{ $cat->title }}">
                                            </div>
                                            <button class="btn btn-xs" hidden>
                                                <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                            </button>
                                        </div>
                                    </form>
                                </td>
                                <td class="pb-0"></td>
                                <td class="pb-0"></td>
                                <td class="pb-0"></td>
                                <td class="pb-0">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id="{{ $cat->id }}">
                                        @csrf
                                        <div class="d-flex align-items-baseline">
                                            <div class="form-group mb-0">
                                                <input type="number" class="form-control" name="magento_id"
                                                    placeholder="Enter Magento Id" required value="{{ $cat->magento_id }}">
                                            </div>
                                            <button class="btn btn-xs" hidden>
                                                <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                            </button>
                                        </div>
                                    </form>
                                </td>

                                <td class="pb-0">
                                    <form method="POST"
                                        action="{{ route('category.update-cancelation-policy', ['edit' => $cat->id]) }}"
                                        class="edit_category_cancelation_days" data-id="{{ $cat->id }}" data-day_old="{{$cat->days_cancelation}}">
                                        @csrf
                                        <input type="hidden" name="day_old" value="{{$cat->days_cancelation}}" />
                                        <div class="d-flex align-items-baseline">
                                            <div class="form-group mb-0">
                                                <select name="days_cancelation" class="form-control days_cancelation" data-day_can="{{$cat->days_cancelation}}">

                                                </select>
                                            </div>
                                            <button class="btn btn-xs" hidden>
                                                <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                            </button>
                                        </div>
                                    </form>
                                    <button type="button" title="Order Email Send Log" class="btn  btn-xs btn-image pd-5 " data-id="{{$cat->id}}" data-day_type="days_cancelation">
                                        <i style="color:#6c757d;" class="fa fa-info-circle category_cancle_policy" data-id="{{ $cat->id }}" data-day_type="days_cancelation" aria-hidden="true"></i>
                                    </button>
                                </td>
                                <td class="pb-0">
                                    <form method="POST"
                                        action="{{ route('category.update-cancelation-policy', ['edit' => $cat->id]) }}"
                                        class="edit_category_cancelation_days" data-id="{{ $cat->id }}" data-day_old="{{$cat->days_refund}}">
                                        @csrf
                                        <input type="hidden" name="day_old" value="{{$cat->days_refund}}" />
                                        <div class="d-flex align-items-baseline">
                                            <div class="form-group mb-0">
                                                <select name="days_refund" class="form-control days_refund" data-day_can="{{$cat->days_refund}}" >

                                                </select>
                                            </div>
                                            <button class="btn btn-xs" hidden>
                                                <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                            </button>
                                        </div>
                                    </form>
                                    <button type="button" title="Order Email Send Log" class="btn  btn-xs btn-image pd-5 " data-id="{{$cat->id}}"  data-day_type="days_refund">
                                        <i style="color:#6c757d;" class="fa fa-info-circle category_cancle_policy" data-id="{{ $cat->id }}" data-day_type="days_refund" aria-hidden="true"></i>
                                    </button>
                                </td>
                            
                            
                            <td class="pb-0">
                                <div class="form-group mb-2">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id={{ $cat->id }}>
                                        @csrf
                                        <div class="d-flex align-items-baseline">
                                            <div class="form-group mb-0 pb-0">

                                                <input type="text" class="form-control" name="show_all_id"
                                                    placeholder="Enter Show All Id" value="{{ $cat->show_all_id }}">
                                            </div>
                                            <button class="btn btn-xs" hidden>
                                                <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                            <td class="pb-0">
                                <form method="post"
                                    action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                    class="edit_category_data" data-id={{ $cat->id }}>
                                    @csrf

                                    <input type="text" class="form-control" placeholder="Entery HS code"
                                        name="simplyduty_code" value="{{ $cat->simplyduty_code }}">
                                    <button type="button" class="btn btn-secondary btn-block mt-2" hidden>Save</button>
                                </form>
                            </td>



                            <td class="pb-0">
                                <div class="form-group mb-0 pb-0">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id={{ $cat->id }}>
                                        @csrf

                                        <select class="form-control submit_on_change globalSelect2 segmanet" data-category_segment_id="{{$cat->category_segment_id}}" 
                                            name="category_segment_id">
                                            <option>Select Category Segment</option>
                                            @foreach ($category_segments as $k => $catSeg)
                                                <option value="{{ $k }}"
                                                    {{ $k == $cat->category_segment_id ? 'selected' : '' }}>
                                                    {{ $catSeg }}</option>
                                            @endforeach
                                        </select>

                                    </form>

                                </div>
                            </td>
                            <td class="pb-0">
                                <form method="POST"
                                    action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                    class="edit_category_data" data-id={{ $cat->id }}>
                                    @csrf
                                    <div
                                        class="mb-0 pb-0  form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">

                                        <select name="parent_id" class="submit_on_change globalSelect2" data-cat_id="{{$cat->parent_id}}">
                                            <option>Select Category </option>



                                        </select>
                                        <span class="text-danger">{{ $errors->first('parent_id') }}</span>

                                    </div>
                                </form>
                            </td>


                            <td class="pb-0">
                                <div
                                    class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id={{ $cat->id }}>
                                        @csrf
                                        <input type="text" name="measurment" value="true" hidden>
                                        <div class="d-flex">

                                            <input type="checkbox" id="edit_need_to_check_measuremen{{ $cat->id }}"
                                                name="need_to_check_measurement" class="submit_on_change"
                                                {{ $cat->need_to_check_measurement ? 'checked' : '' }}>
                                            
                                        </div>

                                    </form>

                                </div>

                            </td>
                            <td class="pb-0">
                                <div
                                    class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id={{ $cat->id }}>
                                        @csrf
                                        <input type="text" name="checkSize" value="true" hidden>

                                        <div class="d-flex">
                                            <input type="checkbox" id="edit_need_to_check_size{{ $cat->id }}"
                                                name="need_to_check_size" {{ $cat->need_to_check_size ? 'checked' : '' }}
                                                class="submit_on_change">

                                        </div>
                                    </form>
                                </div>
                            </td>

                            <td class="pb-0">
                                <div
                                    class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id={{ $cat->id }}>
                                        @csrf
                                        <input type="text" name="checkSizeChart" value="true" hidden>
                                        <div class="d-flex">
                                            <input type="checkbox" id="edit_need_to_check_size_chart{{ $cat->id }}"
                                                name="size_chart_needed" {{ $cat->size_chart_needed ? 'checked' : '' }}
                                                class="submit_on_change">
                                        </div>
                                    </form>
                                </div>
                            </td>

                            <td class="pb-0">
                                {{ html()->select('push_type', [null => '- Select -'] + \App\Category::PUSH_TYPE, $cat->push_type)->class('form-control push-type-change')->data('id', $cat->id) }}
                            </td>

                            <td class="pb-0">

                                <form style="display: inline-block" action="{{ route('category.remove') }}" method="POST"
                                    class="category_deleted">
                                    @csrf
                                    <input type="text" name="edit_cat" value={{ $cat->id }} hidden>
                                    <button type="submit" class="btn btn-xs" data-id="{{ $cat->id }}">
                                        <img src="/images/delete.png" style="cursor: pointer; width: 16px;">
                                    </button>
                                </form>

                            </td>
                        </tr>
                        
                        @foreach ($cat->childsOrderByTitle as $firstChild)
                        <tr class="parent-cat">
                            <td></td>
                            <td class="pb-0">
                                <form method="POST"
                                    action="{{ route('category.child-update-category', ['edit' => $firstChild->id]) }}"
                                    class="edit_category_data" data-id={{ $firstChild->id }}>
                                    @csrf
                                    <div class="d-flex align-items-baseline">
                                        <div class="form-group mb-0">

                                            <input type="text" class="form-control" name="title"
                                                placeholder="Enter Show All Id" value="{{ $firstChild->title }}">
                                        </div>
                                        <button class="btn btn-xs" hidden>
                                            <img src="/images/filled-sent.png"
                                                style="cursor: pointer; width:16px;">
                                        </button>
                                    </div>
                                </form>
                            </td>

                            <td></td>
                            <td></td>
                            <td class="pb-0">
                                <form method="POST"
                                    action="{{ route('category.child-update-category', ['edit' => $firstChild->id]) }}"
                                    class="edit_category_data" data-id="{{ $firstChild->id }}">
                                    @csrf
                                    <div class="d-flex align-items-baseline">
                                        <div class="form-group mb-0">
                                            <input type="number" class="form-control" name="magento_id"
                                                placeholder="Enter Magento Id" required value="{{ $firstChild->magento_id }}">
                                        </div>
                                        <button class="btn btn-xs" hidden>
                                            <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                        </button>
                                    </div>
                                </form>
                            </td>
                            <td class="pb-0">
                                <form method="POST"
                                    action="{{ route('category.update-cancelation-policy', ['edit' => $firstChild->id]) }}"
                                    class="edit_category_cancelation_days" data-id="{{ $firstChild->id }}" data-day_old="{{$firstChild->days_cancelation}}">
                                    @csrf
                                    <input type="hidden" name="day_old" value="{{$firstChild->days_cancelation}}" />
                                    <div class="d-flex align-items-baseline">
                                        <div class="form-group mb-0">
                                            <select name="days_cancelation" class="form-control days_cancelation" data-day_can="{{$firstChild->days_cancelation}}">
 
                                            </select>
                                        </div>
                                        <button class="btn btn-xs" hidden>
                                            <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                        </button>
                                    </div>
                                </form>
                                <button type="button" title="Order Email Send Log" class="btn  btn-xs btn-image pd-5 " data-id="{{$firstChild->id}}" data-day_type="days_cancelation">
                                    <i style="color:#6c757d;" class="fa fa-info-circle category_cancle_policy" data-id="{{ $firstChild->id }}"  data-day_type="days_cancelation" aria-hidden="true"></i>
                                </button>
                            </td>
                            <td class="pb-0">
                                <form method="POST"
                                    action="{{ route('category.update-cancelation-policy', ['edit' => $firstChild->id]) }}"
                                    class="edit_category_cancelation_days" data-id="{{ $firstChild->id }}" data-day_old="{{$firstChild->days_refund}}">
                                    @csrf
                                    <input type="hidden" name="day_old" value="{{$firstChild->days_refund}}" />
                                    <div class="d-flex align-items-baseline">
                                        <div class="form-group mb-0">
                                            <select name="days_refund" class="form-control days_refund" data-day_can="{{$firstChild->days_refund}}">
 
                                            </select>
                                        </div>
                                        <button class="btn btn-xs" hidden>
                                            <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                        </button>
                                    </div>
                                </form>
                                <button type="button" title="Order Email Send Log" class="btn  btn-xs btn-image pd-5 " data-id="{{$firstChild->id}}" data-day_type="days_refund">
                                    <i style="color:#6c757d;" class="fa fa-info-circle category_cancle_policy" data-id="{{ $firstChild->id }}" data-day_type="days_refund" aria-hidden="true"></i>
                                </button>
                            </td>
                            <td class="pb-0">
                                <div class="form-group mb-2">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $firstChild->id]) }}"
                                        class="edit_category_data" data-id={{ $firstChild->id }}>
                                        @csrf
                                        <div class="d-flex align-items-baseline">
                                            <div class="form-group mb-0 pb-0">

                                                <input type="text" class="form-control" name="show_all_id"
                                                    placeholder="Enter Show All Id" value="{{ $firstChild->show_all_id }}">
                                            </div>
                                            <button class="btn btn-xs" hidden>
                                                <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                            <td class="pb-0">
                                <form method="post"
                                    action="{{ route('category.child-update-category', ['edit' => $firstChild->id]) }}"
                                    class="edit_category_data" data-id={{ $firstChild->id }}>
                                    @csrf

                                    <input type="text" class="form-control" placeholder="Entery HS code"
                                        name="simplyduty_code" value="{{ $firstChild->simplyduty_code }}">
                                    <button type="button" class="btn btn-secondary btn-block mt-2" hidden>Save</button>
                                </form>
                            </td>

                            <td class="pb-0">
                                <div class="form-group mb-0 pb-0">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $firstChild->id]) }}"
                                        class="edit_category_data" data-id={{ $firstChild->id }}>
                                        @csrf

                                        <select class="form-control submit_on_change globalSelect2 segmanet" data-category_segment_id="{{$firstChild->category_segment_id}}" 
                                            name="category_segment_id">
                                            <option>Select Category Segment</option>
                                            @foreach ($category_segments as $k => $catSeg)
                                                <option value="{{ $k }}"
                                                    {{ $k == $firstChild->category_segment_id ? 'selected' : '' }}>
                                                    {{ $catSeg }}</option>
                                            @endforeach
                                        </select>

                                    </form>

                                </div>
                            </td>
                            <td class="pb-0">
                                <form method="POST"
                                    action="{{ route('category.child-update-category', ['edit' => $firstChild->id]) }}"
                                    class="edit_category_data" data-id={{ $firstChild->id }}>
                                    @csrf
                                    <div
                                        class="mb-0 pb-0  form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">

                                        <select name="parent_id" class="submit_on_change globalSelect2" data-cat_id="{{$firstChild->parent_id}}">
                                            <option>Select Category </option>

 

                                        </select>
                                        <span class="text-danger">{{ $errors->first('parent_id') }}</span>

                                    </div>
                                </form>
                            </td>


                            <td class="pb-0">
                                <div
                                    class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $firstChild->id]) }}"
                                        class="edit_category_data" data-id={{ $firstChild->id }}>
                                        @csrf
                                        <input type="text" name="measurment" value="true" hidden>
                                        <div class="d-flex">

                                            <input type="checkbox" id="edit_need_to_check_measuremen{{ $firstChild->id }}"
                                                name="need_to_check_measurement" class="submit_on_change"
                                                {{ $firstChild->need_to_check_measurement ? 'checked' : '' }}>
                                        </div>

                                    </form>

                                </div>

                            </td>
                            <td class="pb-0">
                                <div
                                    class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $firstChild->id]) }}"
                                        class="edit_category_data" data-id={{ $firstChild->id }}>
                                        @csrf
                                        <input type="text" name="checkSize" value="true" hidden>

                                        <div class="d-flex">
                                            <input type="checkbox" id="edit_need_to_check_size{{ $firstChild->id }}"
                                                name="need_to_check_size" {{ $firstChild->need_to_check_size ? 'checked' : '' }}
                                                class="submit_on_change">
 
                                        </div>
                                    </form>
                                </div>
                            </td>

                            <td class="pb-0">
                                <div
                                    class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $firstChild->id]) }}"
                                        class="edit_category_data" data-id={{ $firstChild->id }}>
                                        @csrf
                                        <input type="text" name="checkSizeChart" value="true" hidden>
                                        <div class="d-flex">
                                            <input type="checkbox" id="edit_need_to_check_size_chart{{ $firstChild->id }}"
                                                name="size_chart_needed" {{ $firstChild->size_chart_needed ? 'checked' : '' }}
                                                class="submit_on_change">
                                        </div>
                                    </form>
                                </div>
                            </td>

                            <td class="pb-0">
                                {{ html()->select('push_type', [null => '- Select -'] + \App\Category::PUSH_TYPE, $firstChild->push_type)->class('form-control push-type-change')->data('id', $firstChild->id) }}
                            </td>

                            <td class="pb-0">

                                <form style="display: inline-block" action="{{ route('category.remove') }}" method="POST"
                                    class="category_deleted">
                                    @csrf
                                    <input type="text" name="edit_cat" value={{ $firstChild->id }} hidden>
                                    <button type="submit" class="btn btn-xs" data-id="{{ $firstChild->id }}">
                                        <img src="/images/delete.png" style="cursor: pointer; width: 16px;">
                                    </button>
                                </form>

                            </td>
                        </tr>
                                @if ($firstChild->childsOrderByTitle->count())

                                                @foreach ($firstChild->childsOrderByTitle as $secondChild)
                                                <tr class="parent-cat">
                                                    <td></td>
                                                    <td></td>
                                                    <td class="pb-0">
                                                        <form method="POST"
                                                            action="{{ route('category.child-update-category', ['edit' => $secondChild->id]) }}"
                                                            class="edit_category_data" data-id={{ $secondChild->id }}>
                                                            @csrf
                                                            <div class="d-flex align-items-baseline">
                                                                <div class="form-group mb-0">
            
                                                                    <input type="text" class="form-control" name="title"
                                                                        placeholder="Enter Show All Id" value="{{ $secondChild->title }}">
                                                                </div>
                                                                <button class="btn btn-xs" hidden>
                                                                    <img src="/images/filled-sent.png"
                                                                        style="cursor: pointer; width:16px;">
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </td>
            
                                                    <td></td>
                                                    <td class="pb-0">
                                                        <form method="POST"
                                                            action="{{ route('category.child-update-category', ['edit' => $secondChild->id]) }}"
                                                            class="edit_category_data" data-id="{{ $secondChild->id }}">
                                                            @csrf
                                                            <div class="d-flex align-items-baseline">
                                                                <div class="form-group mb-0">
                                                                    <input type="number" class="form-control" name="magento_id"
                                                                        placeholder="Enter Magento Id" required value="{{ $secondChild->magento_id }}">
                                                                </div>
                                                                <button class="btn btn-xs" hidden>
                                                                    <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td class="pb-0">
                                                        <form method="POST"
                                                            action="{{ route('category.update-cancelation-policy', ['edit' => $secondChild->id]) }}"
                                                            class="edit_category_cancelation_days" data-id="{{ $secondChild->id }}"  data-day_old="{{$secondChild->days_cancelation}}">
                                                            @csrf
                                                            <input type="hidden" name="day_old" value="{{$secondChild->days_cancelation}}" />
                                                            <div class="d-flex align-items-baseline">
                                                                <div class="form-group mb-0">
                                                                    <select name="days_cancelation" class="form-control days_cancelation" data-day_can="{{$secondChild->days_cancelation}}">
 
                                                                    </select>
                                                                </div>
                                                                <button class="btn btn-xs" hidden>
                                                                    <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                                </button>
                                                            </div>
                                                        </form>
                                                        <button type="button" title="Order Email Send Log" class="btn  btn-xs btn-image pd-5 " data-id="{{$secondChild->id}}" data-day_type="days_cancelation">
                                                            <i style="color:#6c757d;" class="fa fa-info-circle category_cancle_policy" data-id="{{ $secondChild->id }}"  data-day_type="days_cancelation" aria-hidden="true"></i>
                                                        </button>
                                                    </td>
                                                    <td class="pb-0">
                                                        <form method="POST"
                                                            action="{{ route('category.update-cancelation-policy', ['edit' => $secondChild->id]) }}"
                                                            class="edit_category_cancelation_days" data-id="{{ $secondChild->id }}" data-day_old="{{$secondChild->days_refund}}">
                                                            @csrf
                                                            <input type="hidden" name="day_old" value="{{$secondChild->days_refund}}" />
                                                            <div class="d-flex align-items-baseline">
                                                                <div class="form-group mb-0">
                                                                    <select name="days_refund" class="form-control days_refund" data-day_can="{{$secondChild->days_refund}}">

                                                                    </select>
                                                                </div>
                                                                <button class="btn btn-xs" hidden>
                                                                    <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                                </button>
                                                            </div>
                                                        </form>
                                                        <button type="button" title="Order Email Send Log" class="btn  btn-xs btn-image pd-5 " data-id="{{$secondChild->id}}" data-day_type="days_refund">
                                                            <i style="color:#6c757d;" class="fa fa-info-circle category_cancle_policy" data-id="{{ $secondChild->id }}" data-day_type="days_refund" aria-hidden="true"></i>
                                                        </button>
                                                    </td>
                                                    <td class="pb-0">
                                                        <div class="form-group mb-2">
                                                            <form method="POST"
                                                                action="{{ route('category.child-update-category', ['edit' => $secondChild->id]) }}"
                                                                class="edit_category_data" data-id={{ $secondChild->id }}>
                                                                @csrf
                                                                <div class="d-flex align-items-baseline">
                                                                    <div class="form-group mb-0 pb-0">
                        
                                                                        <input type="text" class="form-control" name="show_all_id"
                                                                            placeholder="Enter Show All Id" value="{{ $secondChild->show_all_id }}">
                                                                    </div>
                                                                    <button class="btn btn-xs" hidden>
                                                                        <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </td>
                                                    <td class="pb-0">
                                                        <form method="post"
                                                            action="{{ route('category.child-update-category', ['edit' => $secondChild->id]) }}"
                                                            class="edit_category_data" data-id={{ $secondChild->id }}>
                                                           @csrf
                        
                                                            <input type="text" class="form-control" placeholder="Entery HS code"
                                                                name="simplyduty_code" value="{{ $secondChild->simplyduty_code }}">
                                                            <button type="button" class="btn btn-secondary btn-block mt-2" hidden>Save</button>
                                                        </form>
                                                    </td>
                        
                        
                        
                                                    <td class="pb-0">
                                                        <div class="form-group mb-0 pb-0">
                                                            <form method="POST"
                                                                action="{{ route('category.child-update-category', ['edit' => $secondChild->id]) }}"
                                                                class="edit_category_data" data-id={{ $secondChild->id }}>
                                                                @csrf
                        
                                                                <select class="form-control submit_on_change globalSelect2 segmanet" data-category_segment_id="{{$secondChild->category_segment_id}}" 
                                                                    name="category_segment_id">
                                                                    <option>Select Category Segment</option>
                                                                    @foreach ($category_segments as $k => $catSeg)
                                                                        <option value="{{ $k }}"
                                                                            {{ $k == $secondChild->category_segment_id ? 'selected' : '' }}>
                                                                            {{ $catSeg }}</option>
                                                                    @endforeach
                                                                </select>
                        
                                                            </form>
                        
                                                        </div>
                                                    </td>
                                                    <td class="pb-0">
                                                        <form method="POST"
                                                            action="{{ route('category.child-update-category', ['edit' => $secondChild->id]) }}"
                                                            class="edit_category_data" data-id={{ $secondChild->id }}>
                                                            @csrf
                                                            <div
                                                                class="mb-0 pb-0  form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                        
                                                                <select name="parent_id" class="submit_on_change globalSelect2" data-cat_id="{{$secondChild->parent_id}}">
                                                                    <option>Select Category </option>
                        
                        
                                                                </select>
                                                                <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                        
                                                            </div>
                                                        </form>
                                                    </td>
                        
                        
                                                    <td class="pb-0">
                                                        <div
                                                            class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                                            <form method="POST"
                                                                action="{{ route('category.child-update-category', ['edit' => $secondChild->id]) }}"
                                                                class="edit_category_data" data-id={{ $secondChild->id }}>
                                                                @csrf
                                                                <input type="text" name="measurment" value="true" hidden>
                                                                <div class="d-flex">
                        
                                                                    <input type="checkbox" id="edit_need_to_check_measuremen{{ $secondChild->id }}"
                                                                        name="need_to_check_measurement" class="submit_on_change"
                                                                        {{ $secondChild->need_to_check_measurement ? 'checked' : '' }}>

                                                                </div>
                        
                                                            </form>
                        
                                                        </div>
                        
                                                    </td>
                                                    <td class="pb-0">
                                                        <div
                                                            class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                                            <form method="POST"
                                                                action="{{ route('category.child-update-category', ['edit' => $secondChild->id]) }}"
                                                                class="edit_category_data" data-id={{ $secondChild->id }}>
                                                                @csrf
                                                                <input type="text" name="checkSize" value="true" hidden>
                        
                                                                <div class="d-flex">
                                                                    <input type="checkbox" id="edit_need_to_check_size{{ $secondChild->id }}"
                                                                        name="need_to_check_size" {{ $secondChild->need_to_check_size ? 'checked' : '' }}
                                                                        class="submit_on_change">

                                                                </div>
                                                            </form>
                                                        </div>
                                                    </td>
                        
                                                    <td class="pb-0">
                                                        <div
                                                            class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                                            <form method="POST"
                                                                action="{{ route('category.child-update-category', ['edit' => $secondChild->id]) }}"
                                                                class="edit_category_data" data-id={{ $secondChild->id }}>
                                                                @csrf
                                                                <input type="text" name="checkSizeChart" value="true" hidden>
                                                                <div class="d-flex">
                                                                    <input type="checkbox" id="edit_need_to_check_size_chart{{ $secondChild->id }}"
                                                                        name="size_chart_needed" {{ $secondChild->size_chart_needed ? 'checked' : '' }}
                                                                        class="submit_on_change">
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </td>
                        
                                                    <td class="pb-0">
                                                       {{ html()->select('push_type', [null => '- Select -'] + \App\Category::PUSH_TYPE, $secondChild->push_type)->class('form-control push-type-change')->data('id', $secondChild->id) }}
                                                    </td>
                        
                                                    <td class="pb-0">
                        
                                                        <form style="display: inline-block" action="{{ route('category.remove') }}" method="POST"
                                                            class="category_deleted">
                                                            @csrf
                                                            <input type="text" name="edit_cat" value={{ $secondChild->id }} hidden>
                                                            <button type="submit" class="btn btn-xs" data-id="{{ $secondChild->id }}">
                                                                <img src="/images/delete.png" style="cursor: pointer; width: 16px;">
                                                            </button>
                                                        </form>
                        
                                                    </td>
                                                </tr>

                                                    @if ($secondChild->childsOrderByTitle->count())

                                                        @foreach ($secondChild->childsOrderByTitle  as $thirdChild )
                                                        <tr class="parent-cat">
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="pb-0">
                                                                <form method="POST"
                                                                    action="{{ route('category.child-update-category', ['edit' => $thirdChild->id]) }}"
                                                                    class="edit_category_data" data-id={{ $thirdChild->id }}>
                                                                    @csrf
                                                                    <div class="d-flex align-items-baseline">
                                                                        <div class="form-group mb-0">
                    
                                                                            <input type="text" class="form-control" name="title"
                                                                                placeholder="Enter Show All Id" value="{{ $thirdChild->title }}">
                                                                        </div>
                                                                        <button class="btn btn-xs" hidden>
                                                                            <img src="/images/filled-sent.png"
                                                                                style="cursor: pointer; width:16px;">
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </td>
                                                            <td class="pb-0">
                                                                <form method="POST"
                                                                    action="{{ route('category.child-update-category', ['edit' => $thirdChild->id]) }}"
                                                                    class="edit_category_data" data-id="{{ $thirdChild->id }}">
                                                                    @csrf
                                                                    <div class="d-flex align-items-baseline">
                                                                        <div class="form-group mb-0">
                                                                            <input type="number" class="form-control" name="magento_id"
                                                                                placeholder="Enter Magento Id" required value="{{ $thirdChild->magento_id }}">
                                                                        </div>
                                                                        <button class="btn btn-xs" hidden>
                                                                            <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </td>
                                                            <td class="pb-0">
                                                                <form method="POST"
                                                                    action="{{ route('category.update-cancelation-policy', ['edit' => $thirdChild->id]) }}"
                                                                    class="edit_category_cancelation_days" data-id="{{ $thirdChild->id }}" data-day_old="{{$thirdChild->days_cancelation}}">
                                                                    @csrf
                                                                    <input type="hidden" name="day_old" value="{{$cat->days_cancelation}}" />
                                                                    <div class="d-flex align-items-baseline">
                                                                        <div class="form-group mb-0">
                                                                            <select name="days_cancelation" class="form-control days_cancelation" width="100%" data-day_can="{{$thirdChild->days_cancelation}}">

                                                                            </select>
                                                                        </div>
                                                                        <button class="btn btn-xs" hidden>
                                                                            <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                                <button type="button" title="Order Email Send Log" class="btn  btn-xs btn-image pd-5 " data-id="{{$cat->id}}" data-day_type="days_cancelation">
                                                                    <i style="color:#6c757d;" class="fa fa-info-circle category_cancle_policy" data-id="{{ $cat->id }}"  data-day_type="days_cancelation" aria-hidden="true"></i>
                                                                </button>
                                                            </td>
                                                            <td class="pb-0">
                                                                <form method="POST"
                                                                    action="{{ route('category.update-cancelation-policy', ['edit' => $thirdChild->id]) }}"
                                                                    class="edit_category_cancelation_days" data-id="{{ $thirdChild->id }}" data-day_old="{{$thirdChild->days_refund}}">
                                                                    @csrf
                                                                    <input type="hidden" name="day_old" value="{{$thirdChild->days_refund}}" />
                                                                    <div class="d-flex align-items-baseline">
                                                                        <div class="form-group mb-0">
                                                                            <select name="days_refund" class="form-control days_refund" width="100%" data-day_can="{{$thirdChild->days_refund}}">

                                                                            </select>
                                                                        </div>
                                                                        <button class="btn btn-xs" hidden>
                                                                            <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                                <button type="button" title="Order Email Send Log" class="btn  btn-xs btn-image pd-5 " data-id="{{$thirdChild->id}}" data-day_type="days_refund">
                                                                    <i style="color:#6c757d;" class="fa fa-info-circle category_cancle_policy" data-id="{{ $thirdChild->id }}"  data-day_type="days_refund" aria-hidden="true"></i>
                                                                </button>
                                                            </td>
                                                            <td class="pb-0">
                                                                <div class="form-group mb-2">
                                                                    <form method="POST"
                                                                        action="{{ route('category.child-update-category', ['edit' => $thirdChild->id]) }}"
                                                                        class="edit_category_data" data-id={{ $thirdChild->id }}>
                                                                        @csrf
                                                                        <div class="d-flex align-items-baseline">
                                                                            <div class="form-group mb-0 pb-0">
                                
                                                                                <input type="text" class="form-control" name="show_all_id"
                                                                                    placeholder="Enter Show All Id" value="{{ $thirdChild->show_all_id }}">
                                                                            </div>
                                                                            <button class="btn btn-xs" hidden>
                                                                                <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                            <td class="pb-0">
                                                                <form method="post"
                                                                    action="{{ route('category.child-update-category', ['edit' => $thirdChild->id]) }}"
                                                                    class="edit_category_data" data-id={{ $thirdChild->id }}>
                                                                    @csrf
                                
                                                                    <input type="text" class="form-control" placeholder="Entery HS code"
                                                                        name="simplyduty_code" value="{{ $thirdChild->simplyduty_code }}">
                                                                    <button type="button" class="btn btn-secondary btn-block mt-2" hidden>Save</button>
                                                                </form>
                                                            </td>
                                
                                
                                
                                                            <td class="pb-0">
                                                                <div class="form-group mb-0 pb-0">
                                                                    <form method="POST"
                                                                        action="{{ route('category.child-update-category', ['edit' => $thirdChild->id]) }}"
                                                                        class="edit_category_data" data-id={{ $thirdChild->id }}>
                                                                        @csrf
                                
                                                                        <select class="form-control submit_on_change globalSelect2 segment" data-category_segment_id="{{$thirdChild->category_segment_id}}"
                                                                            name="category_segment_id">
                                                                            <option>Select Category Segment</option>
                                                                            @foreach ($category_segments as $k => $catSeg)
                                                                                <option value="{{ $k }}"
                                                                                    {{ $k == $thirdChild->category_segment_id ? 'selected' : '' }}>
                                                                                    {{ $catSeg }}</option>
                                                                            @endforeach
                                                                        </select>
                                
                                                                    </form>
                                
                                                                </div>
                                                            </td>
                                                            <td class="pb-0">
                                                                <form method="POST"
                                                                    action="{{ route('category.child-update-category', ['edit' => $thirdChild->id]) }}"
                                                                    class="edit_category_data" data-id={{ $thirdChild->id }}>
                                                                    @csrf
                                                                    <div
                                                                        class="mb-0 pb-0  form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                
                                                                        <select name="parent_id" class="submit_on_change globalSelect2 segmanet" data-cat_id="{{$thirdChild->parent_id}}">
                                                                            <option>Select Category </option>
                                

                                
                                                                        </select>
                                                                        <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                                
                                                                    </div>
                                                                </form>
                                                            </td>
                                
                                
                                                            <td class="pb-0">
                                                                <div
                                                                    class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                                                    <form method="POST"
                                                                        action="{{ route('category.child-update-category', ['edit' => $thirdChild->id]) }}"
                                                                        class="edit_category_data" data-id={{ $thirdChild->id }}>
                                                                        @csrf
                                                                        <input type="text" name="measurment" value="true" hidden>
                                                                        <div class="d-flex">
                                
                                                                            <input type="checkbox" id="edit_need_to_check_measuremen{{ $thirdChild->id }}"
                                                                                name="need_to_check_measurement" class="submit_on_change"
                                                                                {{ $thirdChild->need_to_check_measurement ? 'checked' : '' }}>

                                                                        </div>
                                
                                                                    </form>
                                
                                                                </div>
                                
                                                            </td>
                                                            <td class="pb-0">
                                                                <div
                                                                    class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                                                    <form method="POST"
                                                                        action="{{ route('category.child-update-category', ['edit' => $thirdChild->id]) }}"
                                                                        class="edit_category_data" data-id={{ $thirdChild->id }}>
                                                                        @csrf
                                                                        <input type="text" name="checkSize" value="true" hidden>
                                
                                                                        <div class="d-flex">
                                                                            <input type="checkbox" id="edit_need_to_check_size{{ $thirdChild->id }}"
                                                                                name="need_to_check_size" {{ $thirdChild->need_to_check_size ? 'checked' : '' }}
                                                                                class="submit_on_change">

                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </td>
                                
                                                            <td class="pb-0">
                                                                <div
                                                                    class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                                                    <form method="POST"
                                                                        action="{{ route('category.child-update-category', ['edit' => $thirdChild->id]) }}"
                                                                        class="edit_category_data" data-id={{ $thirdChild->id }}>
                                                                        @csrf
                                                                        <input type="text" name="checkSizeChart" value="true" hidden>
                                                                        <div class="d-flex">
                                                                            <input type="checkbox" id="edit_need_to_check_size_chart{{ $thirdChild->id }}"
                                                                                name="size_chart_needed" {{ $thirdChild->size_chart_needed ? 'checked' : '' }}
                                                                                class="submit_on_change">
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </td>
                                
                                                            <td class="pb-0">
                                                                {{ html()->select('push_type', [null => '- Select -'] + \App\Category::PUSH_TYPE, $thirdChild->push_type)->class('form-control push-type-change')->data('id', $thirdChild->id) }}
                                                            </td>
                                
                                                            <td class="pb-0">
                                
                                                                <form style="display: inline-block" action="{{ route('category.remove') }}" method="POST"
                                                                    class="category_deleted">
                                                                    @csrf
                                                                    <input type="text" name="edit_cat" value={{ $thirdChild->id }} hidden>
                                                                    <button type="submit" class="btn btn-xs" data-id="{{ $thirdChild->id }}">
                                                                        <img src="/images/delete.png" style="cursor: pointer; width: 16px;">
                                                                    </button>
                                                                </form>
                                
                                                            </td>
                    
                                                        </tr>
                                                            
                                                            @endforeach
                                                        
                                                    @else
                                                    

                                                    @endif
                                                    
                                                @endforeach


                                @endif
                              
                            @endforeach

                        @endforeach
                            
                                                        <tr class="add-childs">
                                                        </tr>
                        
                </table>
            </div>
        </div>
    </div>
    <div id="category_cancle_policy" class="modal fade" role="  dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cancellation Policy Log</h4>
                </div>
                <div class="modal-body">
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Category</th>
                          <th>Day Type</th>
                          <th>Change Days</th>
                          <th>Old Days</th>
                          <th>Date</th>
                        </tr>
                      </thead>
    
                      <tbody id="category_cancle_policylogtd">
                       
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
      </div>
      <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    <script type="text/javascript">
        window.onload = function(e){ 
            setTimeout(() => {
                $.each($(".days_cancelation"), function() {
                    var opt = '';
                    var days_can_id = $(this).attr("data-day_can");
                    //console.log(days_can_id);
                    for (var n = 0; n < 32; ++ n){
                        var selected = "";
                        if(n == days_can_id) {
                            selected = "selected"
                        }
                        opt += "<option value="+n+" "+selected+">"+n+"</option>";
                    } 
                    $(this).html(opt);
                });
                
                $.each($(".days_refund"), function() {
                    var opt = '';
                    var days_ref_id = $(this).attr("data-day_can");
                    //console.log(days_ref_id);
                    for (var n = 0; n < 32; ++ n){
                        var selected = "";
                        if(n == days_ref_id) {
                            selected = "selected"
                        }
                        opt += "<option value="+n+" "+selected+">"+n+"</option>";
                    } 
                    $(this).html(opt);
                });
                //debugger;
                $.each($(".submit_on_change"), function() {
                    var opta = '';
                    var cat_id = $(this).attr("data-cat_id");
                    var catArrs = @json($allCategories);
                    $.each(catArrs, function(i, v) {
                        //console.log('index : '+i+ '==> Value'+v.id);
                        var selectedCat = "";
                        if(v.id == cat_id) {
                            selectedCat = "selected";
                        }
                        opta += "<option value="+i+" "+selectedCat+">"+v.title+"</option>";
                    });
                    $(this).html(opta);
                });

                // var catArr = @json($category_segments);
                // console.log(catArr);
                // $.each($(".segmanet"), function() {
                //     var opts = '';
                //     var cat_ids = $(this).attr("data-category_segment_id");
                //     $.each(catArr, function(i, v) {
                //         //console.log('index : '+i+ '==> Value'+v.id);
                //         var selectedCats = "";
                //         if(i == cat_ids) {
                //             selectedCats = "selected";
                //         }
                //         opts += "<option value="+v.id+" "+selectedCats+">"+v.name+"</option>";
                //     });
                //     $(this).html(opts);
                // });

                
            }, 3000);            
        }
        
        $(document).on("click",".category_cancle_policy",function() { 
			  console.log(this);
            var category_id = $(this).data("id");
            var day_type = $(this).data("day_type");
            $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{route("category.get_cancelation_policy_log")}}',
            type: "post",
            data : { category_id: category_id,
                    day_type : day_type,
                 },
            beforeSend: function() {
                $("loading-image").show();
            }
            }).done( function(response) {
            if(response.code == 200 && response.data !='') {
                var t = '';
                $.each(response.data,function(k,v) {
                t += `<tr><td>`+v.id+`</td>`;
                t += `<td>`+v.category_id+`</td>`;
                t += `<td>`+v.day_type+`</td>`;
                t += `<td>`+v.day_change+`</td>`;
                t += `<td>`+v.day_old+`</td>`;
                t += `<td>`+v.created_at+`</td></tr>`;
                });

                $("#category_cancle_policy").find("#category_cancle_policylogtd").html(t);
                $('#category_cancle_policy').modal("show");
                $("loading-image").hide();
            } else if(response.code == 500){
                toastr['error']('Could not find any error Log', 'Error');
            } else {
                toastr['error']('Could not find any error Log', 'Error');
            }
            }).fail(function(error) {
                toastr['error']('Could not find any error Log', 'Error');
            });
        });

        //$('ul.pagination').hide();
        // $(function() {
        //     $('.infinite-scroll').jscroll({
        //         autoTrigger: true,
        //         loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
        //         padding: 2500,
        //         nextSelector: '.pagination li.active + li a',
        //         contentSelector: 'div.infinite-scroll',
        //         callback: function() {

        //             $('ul.pagination').remove();
        //             $(".select-multiple").select2();
        //             initialize_select2();
        //         }
        //     });
        // });

        $(document).on('click', '.show-sub-category', function(e) {
            var subCat = $(this).data('name');
            $this = $(this)

            $this.hasClass('show_child') ? $this.removeClass('show_child') : $this.addClass('show_child')

            if ($this.hasClass('show_child')) {

                $.ajax({
                    url: "{{ route('category.child-category') }}",
                    method: 'GET',
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        'subCat': subCat,
                    },
                    success: function(response) {
                        console.log(response, 'aaaaaaaaaaaaaa')

                        if (response.length) {

                            let html =
                                '<td colspan="4" class="px-3" style="color: #757575;"><h5 style="color: #000;" class="pl-2 mt-0">Child category</h5><table style="width:100%; ">';

                            response.forEach((element, key) => {
                                html += `
        
                                        <tr class="parent-cat" colspan="4">
                                    
                                            <td class="index" style="width: 10%"> ${key+1} </td>
                                            <td class="brand_name  style="width: 50%" data-id="${element.id}">${element.title} (${element.child_level_sencond.length})</td>
                                            <td class="created_at" style="width: 20%">${element.created_at}</td>
                                        
                                            <td  style="width: 20%">
                                                <button type="button" class="btn btn-xs no-pd show-sub-category"
                                                            data-id="${ element.id }" data-name="${ element.id }">
                                                            <img src="/images/forward.png" style="cursor: pointer" width="16px">
                                                </button>
                                                <button type="button" class="btn btn-xs category_edit_popup" data-id="${element.id}"
                                                >
                                                    <img src="/images/edit.png" style="cursor: pointer; width: 16px;">
                                                </button>
                                                <form style="display: inline-block" action="{{ route('category.remove') }}" method="POST" class="category_deleted">
                                                    @csrf
                                                    <input type="text" name="edit_cat" value="${element.id}" hidden>
                                                    <button type="submit" class="btn btn-xs" data-id="${element.id}">
                                                    <img src="/images/delete.png" style="cursor: pointer; width: 16px;">
                                                        </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <tr class="add-childs"> 
                                                        
                                                </tr>   
`
                            });
                            html += '</table></td>'
                            $this.closest('.parent-cat').next('.add-childs:first').html(html)
                        } else {
                            let html =
                                ' <td colspan="4"> <h5 class="text-center mt-0"> No child category available for this<h5> </td>'
                            $this.closest('.parent-cat').next('.add-childs:first').html(html)

                        }

                    },
                    error: function(response) {}
                });
            } else {
                $this.closest('.parent-cat').next('.add-childs:first').empty()
            }

        });


        let edited_data_id = null
        $(document).on('click', '.category_edit_popuppp', function(e) {
            e.preventDefault()
            const dataId = $(this).data('id')

            $.ajax({
                url: "{{ route('category.child-edit-category') }}",
                method: 'GET',
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                    'dataId': dataId,
                },
                success: function(response) {
                    console.log(response, 'respose')
                    edited_data_id = response.id
                    $('#edit_title_category').val(response.title)
                    $('#edit_category_magento_id').val(response.magento_id)
                    $('#edit_category_show_all_id').val(response.show_all_id);
                    $('#edit_category_segment_id').val(response.category_segment_id?.id ?? null);
                    $('#edit_need_to_check_measurement').attr('checked', response
                        .need_to_check_measurement ? true : false);
                    $('#edit_need_to_check_size').attr('checked', response.need_to_check_size ? true :
                        false);
                    $('#editCategoryModal').modal('show')
                },
                error: function(response) {}
            });
        })

        $(document).on('submit', '.edit_category_data', function(e) {
            e.preventDefault()
            const dataId = $(this).data('id')
            console.log(dataId)
            const form_data = $(this).serialize();
            console.log(form_data, 'form-data')

            $.ajax({
                url: '/category/' + dataId + '/edit-category',
                method: 'POST',
                dataType: "json",
                data: form_data,
                success: function(response) {
                    // location.reload()
                    toastr["success"](response['success-remove']);
                    $('#editCategoryModal').modal('hide')

                },
                error: function(response) {
                    toastr["error"]("Oops,something went wrong");

                }
            });

        });

        $(document).on('change', '.edit_category_cancelation_days', function(e) {
            e.preventDefault()
            const dataId = $(this).data('id')
            var form_data = $(this).serialize();
            console.log(form_data, 'form-data')

            $.ajax({
                url: '/category/' + dataId + '/update-days-cancelation',
                method: 'POST',
                dataType: "json",
                data: form_data,
                success: function(response) {
                    // location.reload()
                    toastr["success"](response['success-remove']);
                    $('#editCategoryModal').modal('hide')
                    location.reload();
                },
                error: function(response) {
                    toastr["error"]("Oops,something went wrong");

                }
            });

        });

        $(document).on('submit', '.category_deleted', function(e) {
            e.preventDefault()
            $this = $(this);
            const form_data = $(this).serialize();
            // console.log(form_data,'dataid')

            if (confirm('Do you really want to delete this category?')) {
                $.ajax({
                    url: 'category/remove',
                    method: 'POST',
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log(response)
                        
                        // location.reload()
                        if (response['error-remove']) {
                            toastr["error"](response['error-remove']);
                        }
                        
                        
                        if (response['success-remove']){
                            $this.closest('.parent-cat').remove()
                            toastr["success"](response['success-remove']);
                        }
                        // $('#editCategoryModal').modal('hide')
                    },
                    error: function(response) {
                        toastr["error"]("Oops,something went wrong");
                    }
                });
            }

        })

        // $(document).on('submit','#filter_category_form',function(e){
        //     e.preventDefault()
        //     console.log('its wokting')
        // })

        $(document).on('change', '.submit_on_change', function() {
            $(this).closest('form').submit()
        })

        $(document).on('change', '.push-type-change', function() {
            var category_id = $(this).data("id");
            var value = $(this).val();
            $.ajax({
                url: 'category/change-push-type',
                method: 'POST',
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                    category_id: category_id,
                    value: value
                },
                success: function(response) {
                    if (response.code == 200) {
                        toastr["success"](response.message);
                    } else {
                        toastr["error"](response.message);
                    }

                },
                error: function(response) {
                    toastr["error"]("Oops,something went wrong");
                }
            });
        });

        $( "#copy_category_data" ).submit(function( e ) {
            if($("#sourceCategoryId").val() != $("#targetCategoryId").val()){
                $(this).closest('form').submit()
            }  else {
                alert("Source & target category is same.")
                e.preventDefault();
                return false;
            }    
        });
    </script>
@endsection
