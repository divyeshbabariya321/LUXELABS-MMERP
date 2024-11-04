<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Category : </strong> {{ $magento_module->module_category->category_name }}
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Location : </strong> {{ $magento_module->module_location?->magento_module_locations }}
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Return Type error : </strong> {{ $magento_module->module_error_status_type?->return_type_name }}
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Website : </strong> {{ isset($magento_module->store_website) && !empty($magento_module->store_website)? $magento_module->store_website->website: '' }}
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Name : </strong> {{ $magento_module->module }}
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Current Version : </strong> {{ $magento_module->current_version }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Type : </strong> {{ $magento_module->module_type_data ?$magento_module->module_type_data->magento_module_type : '-' }}
        </div>
    </div>
    
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Payment Status : </strong> {{ $magento_module->payment_status == 'Free' ? 'Free' : 'Paid' }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Status : </strong> {{ $magento_module->status == 1 ? 'Enable' : 'Disabled' }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            @php
                $apiOptions = ['0' => 'No', '1' => 'Yes', '2' => 'API Error', '3' => 'API Error Resolve'];
            @endphp
            <strong>API : </strong> 
            @if(array_key_exists($magento_module->api, $apiOptions))
                {{ $apiOptions[$magento_module->api] }}
            @else
                -
            @endif
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Cron : </strong> {{ $magento_module->cron_job == 1 ? 'Yes' : 'No' }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Javascript/css Require : </strong> {{ $magento_module->is_js_css == 1 ? 'Yes' : 'No' }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Third Party JS Require : </strong> {{ $magento_module->is_third_party_js == 1 ? 'Yes' : 'No' }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Sql Query : </strong> {{ $magento_module->is_sql == 1 ? 'Yes' : 'No' }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Third Party Plugin : </strong> {{ $magento_module->is_third_party_plugin == 1 ? 'Yes' : 'No' }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Developer Name : </strong> {{ $magento_module->developer_name_data ? $magento_module->developer_name_data->name : '-' }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Customized : </strong> {{ $magento_module->is_customized == 1 ? 'Yes' : 'No' }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Site Impact : </strong> {{ $magento_module->site_impact == 1 ? 'Yes' : 'No' }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Review Standard : </strong> {{ $magento_module->module_review_standard == 1 ? 'Yes' : 'No' }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Used At : </strong> {{ $magento_module->used_at }}
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Created At : </strong> {{ $magento_module->created_at->format('d-m-Y') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <strong>Module Description : </strong> {{ $magento_module->module_description }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <strong>Module Dependency : </strong> {{ $magento_module->dependency }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <strong>Module Composer.json : </strong> {{ $magento_module->composer }}
        </div>
    </div>
    
</div>
