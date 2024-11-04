<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Category :</strong>
            {{ html()->select('module_category_id', $module_categories)->id('module_category_id')->placeholder('Select Module Category')->class('form-control')->required() }}
            @if ($errors->has('module_category_id'))
                <span style="color:red">{{ $errors->first('module_category_id') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Location :</strong>
            {{ html()->select('magneto_location_id', $module_locations)->id('magneto_location_id')->placeholder('Select Module location')->class('form-control')->required() }}
            @if ($errors->has('magneto_location_id'))
                <span style="color:red">{{ $errors->first('magneto_location_id') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Return Type error :</strong>
            {{ html()->select('return_type_error_status', $module_return_type_statuserrors)->placeholder('Select Module Return type Error')->class('form-control filter-module_return_type_status_error') }}
           
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Website :</strong>
            {{ html()->select('store_website_id', $store_websites)->id('store_website_id')->placeholder('Select Module Category')->class('form-control')->required() }}
            @if ($errors->has('store_website_id'))
                <span style="color:red">{{ $errors->first('store_website_id') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Name:</strong>
            {{ html()->text('module')->id('module')->placeholder('Module Name')->class('form-control')->required() }}
            @if ($errors->has('module'))
                <span style="color:red">{{ $errors->first('module') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Current Version:</strong>
            {{ html()->text('current_version')->id('current_version')->placeholder('Current Version')->class('form-control') }}
            @if ($errors->has('current_version'))
                <span style="color:red">{{ $errors->first('current_version') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Type:</strong>
            {{ html()->select('module_type', $magento_module_types)->id('module_type')->placeholder('Select Module Type')->class('form-control')->required() }}
            @if ($errors->has('module_type'))
                <span style="color:red">{{ $errors->first('module_type') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Payment Status:</strong>
            {{ html()->select('payment_status', ['Free' => 'Free', 'Paid' => 'Paid'])->id('payment_status')->placeholder('Select Payment Status')->class('form-control')->required() }}
            @if ($errors->has('payment_status'))
                <span style="color:red">{{ $errors->first('payment_status') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-4 col-sm-4">
        <div class="form-group">
            <strong>Status:</strong>
            {{ html()->select('status', ['Disabled', 'Enable'])->id('status')->placeholder('Select Status')->class('form-control')->required() }}
            @if ($errors->has('status'))
                <span style="color:red">{{ $errors->first('status') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-4 col-sm-4">
        <div class="form-group">
            <strong>API:</strong>
            {{ html()->select('api', ['0' => 'No', '1' => 'Yes', '2' => 'API Error', '3' => 'API Error Resolve'])->id('api')->placeholder('Select API')->class('form-control')->required() }}
            @if ($errors->has('api'))
                <span style="color:red">{{ $errors->first('api') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-4 col-sm-4">
        <div class="form-group">
            <strong>Cron :</strong>
            {{ html()->select('cron_job', ['0' => 'No', '1' => 'Yes'])->id('cron_job')->placeholder('Select Cron Job')->class('form-control')->required() }}
            @if ($errors->has('cron_job'))
                <span style="color:red">{{ $errors->first('cron_job') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Javascript/css Require :</strong>
            {{ html()->select('is_js_css', ['0' => 'No', '1' => 'Yes'])->id('is_js_css')->placeholder('Select Javascript/css Require')->class('form-control')->required() }}
            @if ($errors->has('is_js_css'))
                <span style="color:red">{{ $errors->first('is_js_css') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Third Party JS Require :</strong>
            {{ html()->select('is_third_party_js', ['0' => 'No', '1' => 'Yes'])->id('is_third_party_js')->placeholder('Select Third Third Party JS Require ')->class('form-control')->required() }}
            @if ($errors->has('is_third_party_js'))
                <span style="color:red">{{ $errors->first('is_third_party_js') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Sql Query :</strong>
            {{ html()->select('is_sql', ['0' => 'No', '1' => 'Yes'])->id('is_sql')->placeholder('Select Sql Query Status')->class('form-control')->required() }}
            @if ($errors->has('is_sql'))
                <span style="color:red">{{ $errors->first('is_sql') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Third Party Plugin :</strong>
            {{ html()->select('is_third_party_plugin', ['0' => 'No', '1' => 'Yes'])->id('is_third_party_plugin')->placeholder('Select Third Party Plugin')->class('form-control')->required() }}
            @if ($errors->has('is_third_party_plugin'))
                <span style="color:red">{{ $errors->first('is_third_party_plugin') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Developer Name :</strong>
            {{ html()->select('developer_name', $users)->id('developer_name')->placeholder('Select developer name')->class('form-control') }}
            @if ($errors->has('developer_name'))
                <span style="color:red">{{ $errors->first('developer_name') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-3 col-sm-3">
        <div class="form-group">
            <strong>Customized:</strong>
            {{ html()->select('is_customized', ['No', 'Yes'])->id('is_customized')->placeholder('Customized')->class('form-control')->required() }}
            @if ($errors->has('is_customized'))
                <span style="color:red">{{ $errors->first('is_customized') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-3 col-sm-3">
        <div class="form-group">
            <strong>Site Impact:</strong>
            {{ html()->select('site_impact', ['No', 'Yes'])->id('site_impact')->placeholder('Site Impact')->class('form-control')->required() }}
            @if ($errors->has('site_impact'))
                <span style="color:red">{{ $errors->first('site_impact') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-3 col-sm-3">
        <div class="form-group">
            <strong>Review Standard :</strong>
            {{ html()->select('module_review_standard', ['No', 'Yes'], 'No')->id('module_review_standard')->class('form-control') }}
            @if ($errors->has('module_review_standard'))
                <span style="color:red">{{ $errors->first('module_review_standard') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Used At:</strong>
            {{ html()->text('used_at')->id('used_at')->placeholder('Used At')->class('form-control') }}
            @if ($errors->has('used_at'))
                <span style="color:red">{{ $errors->first('used_at') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <strong>Module Description:</strong>
            {{ html()->textarea('module_description')->id('module_description')->placeholder('Module Description')->class('form-control')->required()->rows(2)->cols(40) }}
            @if ($errors->has('module_description'))
                <span style="color:red">{{ $errors->first('module_description') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <strong>Module dependency:</strong>
            {{ html()->textarea('dependency')->id('dependency')->placeholder('Module Dependency')->class('form-control')->rows(2)->cols(40) }}
            @if ($errors->has('dependency'))
                <span style="color:red">{{ $errors->first('dependency') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <strong>Module Composer.json File:</strong>
            {{ html()->textarea('composer')->id('composer')->placeholder('Module Composer')->class('form-control')->rows(2)->cols(40) }}
            @if ($errors->has('composer'))
                <span style="color:red">{{ $errors->first('composer') }}</span>
            @endif
        </div>
    </div>
    
</div>
