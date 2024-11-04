<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Category :</strong>
            {{ html()->select('module_category_id', $module_categories)->placeholder('Select Module Category')->class('form-control') }}
            @if ($errors->has('module_category_id'))
                <span style="color:red">{{ $errors->first('module_category_id') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Location :</strong>
            {{ html()->select('magneto_location_id', $module_locations)->placeholder('Select Module location')->class('form-control') }}
            @if ($errors->has('magneto_location_id'))
                <span style="color:red">{{ $errors->first('magneto_location_id') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Name:</strong>
            {{ html()->text('module')->placeholder('Module Name')->class('form-control') }}
            @if ($errors->has('module'))
                <span style="color:red">{{ $errors->first('module') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Current Version:</strong>
            {{ html()->text('current_version')->placeholder('Current Version')->class('form-control') }}
            @if ($errors->has('current_version'))
                <span style="color:red">{{ $errors->first('current_version') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Type:</strong>
            {{ html()->select('module_type', $magento_module_types)->placeholder('Select Module Type')->class('form-control') }}
            @if ($errors->has('module_type'))
                <span style="color:red">{{ $errors->first('module_type') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Payment Status:</strong>
            {{ html()->select('payment_status', ['Free' => 'Free', 'Paid' => 'Paid'])->placeholder('Select Payment Status')->class('form-control') }}
            @if ($errors->has('payment_status'))
                <span style="color:red">{{ $errors->first('payment_status') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Status:</strong>
            {{ html()->select('status', ['Disabled', 'Enable'])->placeholder('Select Status')->class('form-control') }}
            @if ($errors->has('status'))
                <span style="color:red">{{ $errors->first('status') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Task Status:</strong>
            {{ html()->select('task_status', $task_statuses)->placeholder('Select Payment Status')->class('form-control') }}
            @if ($errors->has('task_status'))
                <span style="color:red">{{ $errors->first('task_status') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Cron Time (Min) :</strong>
            {{ html()->text('cron_time')->placeholder('Cron Time')->class('form-control') }}
            @if ($errors->has('cron_time'))
                <span style="color:red">{{ $errors->first('cron_time') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Javascript/css Require :</strong>
            {{ html()->select('is_js_css', ['0' => 'No', '1' => 'Yes'])->placeholder('Select Javascript/css Require')->class('form-control') }}
            @if ($errors->has('is_js_css'))
                <span style="color:red">{{ $errors->first('is_js_css') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Third Party JS Require :</strong>
            {{ html()->select('is_third_party_js', ['0' => 'No', '1' => 'Yes'])->placeholder('Select Third Third Party JS Require ')->class('form-control') }}
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
            {{ html()->select('is_sql', ['0' => 'No', '1' => 'Yes'])->placeholder('Select Sql Query Status')->class('form-control') }}
            @if ($errors->has('is_sql'))
                <span style="color:red">{{ $errors->first('is_sql') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Third Party Plugin :</strong>
            {{ html()->select('is_third_party_plugin', ['0' => 'No', '1' => 'Yes'])->placeholder('Select Third Party Plugin')->class('form-control') }}
            @if ($errors->has('is_third_party_plugin'))
                <span style="color:red">{{ $errors->first('is_third_party_plugin') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Developer Name:</strong>
            {{ html()->text('developer_name')->placeholder('Developer Name')->class('form-control') }}
            @if ($errors->has('developer_name'))
                <span style="color:red">{{ $errors->first('developer_name') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Customized:</strong>
            {{ html()->select('is_customized', ['No', 'Yes'])->class('form-control') }}
            @if ($errors->has('is_customized'))
                <span style="color:red">{{ $errors->first('is_customized') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Review Standard :</strong>
            {{ html()->select('module_review_standard', ['No' => '0', 'Yes' => '1'], 'No')->placeholder('Customized')->class('form-control') }}
            @if ($errors->has('module_review_standard'))
                <span style="color:red">{{ $errors->first('module_review_standard') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <strong>Module Description:</strong>
            {{ html()->textarea('module_description')->placeholder('Module Description')->class('form-control')->rows(2)->cols(40) }}
            @if ($errors->has('module_description'))
                <span style="color:red">{{ $errors->first('module_description') }}</span>
            @endif
        </div>
    </div>

    
</div>
