@foreach ($postmans as $key => $postman)

    @php
        $userAccessArr = explode(",",$postman->user_permission);
        array_push($userAccessArr, $addAdimnAccessID)
    @endphp
    @if (in_array($userID, $userAccessArr))
        @if(!empty($dynamicColumnsToShowPostman))
            <tr style="background-color: {{$postman?->postmanStatus?->postman_color ?? ""}}!important;">
                @if (!in_array('ID', $dynamicColumnsToShowPostman))
                    <td>{{$postman->id}}</td>
                @endif

                @if (!in_array('Folder Name', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="name" data-id="{{$postman->id}}">
                        <span class="show-short-name-{{$postman->id}}">{{ Str::limit($postman->name, 5, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-name-{{$postman->id}} hidden">{{$postman->name}}</span>
                    </td>
                @endif

                @if (!in_array('PostMan Status', $dynamicColumnsToShowPostman))
                    <td style="width: 25%;">
                        <div class="d-flex align-items-center">
                            <select name="status" class="status-dropdown" data-id="{{$postman->id}}">
                                <option value="">Select Status</option>
                                @foreach ($status as $stat)
                                    <option value="{{$stat->id}}" {{$postman->status_id == $stat->id ? 'selected' : ''}}>{{$stat->status_name}}</option>
                                @endforeach
                            </select>
                            <button type="button" data-id="{{ $postman->id  }}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                        </div>
                    </td>
                @endif

                @if (!in_array('API Issue Fix Done', $dynamicColumnsToShowPostman))
                    <td style="width: 15%;">
                        <div class="d-flex align-items-center">
                            <select name="api_issue_fix_done" class="api-issue-fix-done-dropdown" data-id="{{$postman->id}}">
                                <option value="">Select</option>
                                <option value="0" {{$postman->api_issue_fix_done === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$postman->api_issue_fix_done === 1 ? 'selected' : ''}}>Yes</option>
                                <option value="2" {{$postman->api_issue_fix_done === 2 ? 'selected' : ''}}>Lead Verified</option>
                            </select>
                            <button type="button" data-id="{{ $postman->id  }}" class="btn btn-image api-issue-fix-done-history-show p-0 ml-2"  title="Api Issue Fix Done Histories" ><i class="fa fa-info-circle"></i></button>
                        </div>
                    </td>
                @endif
            
                @if (!in_array('Controller Name', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="controller_name" data-id="{{$postman->id}}">
                        <span class="show-short-controller_name-{{$postman->id}}">{{ Str::limit($postman->controller_name, 5, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-controller_name-{{$postman->id}} hidden">{{$postman->controller_name}}</span>
                    </td>
                @endif

                @if (!in_array('Method Name', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="method_name" data-id="{{$postman->id}}">
                        <span class="show-short-method_name-{{$postman->id}}">{{ Str::limit($postman->method_name, 5, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-method_name-{{$postman->id}} hidden">{{$postman->method_name}}</span>
                    </td>
                @endif

                @if (!in_array('Request Name', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="request_name" data-id="{{$postman->id}}">
                        <span class="show-short-request_name-{{$postman->id}}">{{ Str::limit($postman->request_name, 5, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-request_name-{{$postman->id}} hidden">{{$postman->request_name}}</span>
                    </td>
                @endif

                @if (!in_array('Type', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="request_type" data-id="{{$postman->id}}">
                        <span class="show-short-request_type-{{$postman->id}}">{{ Str::limit($postman->request_type, 5, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-request_type-{{$postman->id}} hidden">{{$postman->request_type}}</span>
                    </td>
                @endif

                @if (!in_array('URL', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="url" data-id="{{$postman->id}}">
                        <span class="show-short-url-{{$postman->id}}">{{ Str::limit($postman->request_url, 5, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-url-{{$postman->id}} hidden">{{$postman->request_url}}</span>
                    </td>
                @endif

                @if (!in_array('Request Parameter', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="paramiters" data-id="{{$postman->id}}">
                        <span class="show-short-paramiters-{{$postman->id}}">@if($postman->body_json && $postman->body_json != NULL){{ Str::limit($postman->body_json, 5, '..')}}@else{{"None"}}@endif</span>
                        <span style="word-break:break-all;" class="show-full-paramiters-{{$postman->id}} hidden">@if($postman->body_json && $postman->body_json != NULL){{$postman->body_json}}@else{{"None"}}@endif</span>
                    </td>
                @endif

                @if (!in_array('Params', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="params" data-id="{{$postman->id}}">
                        <span class="show-short-params-{{$postman->id}}">{{ Str::limit($postman->params, 5, '...')}}</span>
                        <span style="word-break:break-all;" class="show-full-params-{{$postman->id}} hidden">{{$postman->params}}</span>
                    </td>
                @endif

                @if (!in_array('Headers', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="headers" data-id="{{$postman->id}}">
                        <span class="show-short-headers-{{$postman->id}}">{{ Str::limit($postman->request_headers, 5, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-headers-{{$postman->id}} hidden">{{$postman->request_headers}}</span>
                    </td>
                @endif

                @if (!in_array('Request type', $dynamicColumnsToShowPostman))
                    <td>{{$postman->request_type}}</td>
                @endif

                @if (!in_array('Request Response', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="response" data-id="{{$postman->id}}">
                        <span class="show-short-response-{{$postman->id}}">{{ Str::limit($postman->response, 12, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-response-{{$postman->id}} hidden">{{$postman->response}}</span>
                    </td>
                @endif

                @if (!in_array('Response Code', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="response_code" data-id="{{$postman->id}}">
                        <span class="show-short-response_code-{{$postman->id}}">{{ Str::limit($postman->response_code  , 5, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-response_code-{{$postman->id}} hidden">{{$postman->response_code}}</span>
                    </td>
                @endif

                @if (!in_array('Grumphp Errors', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="grumphp_errors" data-id="{{$postman->id}}">
                        <span class="show-short-grumphp_errors-{{$postman->id}}">{{ Str::limit($postman->grumphp_errors  , 8, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-grumphp_errors-{{$postman->id}} hidden">{{$postman->grumphp_errors}}</span>
                    </td>
                @endif

                @if (!in_array('Magento API Standards', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="magento_api_standards" data-id="{{$postman->id}}">
                        <span class="show-short-magento_api_standards-{{$postman->id}}">{{ Str::limit($postman->magento_api_standards  , 15, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-magento_api_standards-{{$postman->id}} hidden">{{$postman->magento_api_standards}}</span>
                    </td>
                @endif

                @if (!in_array('Swagger DocBlock', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="swagger_doc_block" data-id="{{$postman->id}}">
                        <span class="show-short-swagger_doc_block-{{$postman->id}}">{{ Str::limit($postman->swagger_doc_block  , 15, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-swagger_doc_block-{{$postman->id}} hidden">{{$postman->swagger_doc_block}}</span>
                    </td>
                @endif

                @if (!in_array('Used for', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="used_for" data-id="{{$postman->id}}">
                        <span class="show-short-used_for-{{$postman->id}}">{{ Str::limit($postman->used_for  , 5, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-used_for-{{$postman->id}} hidden">{{$postman->used_for}}</span>
                    </td>
                @endif

                @if (!in_array('Used in', $dynamicColumnsToShowPostman))
                    <td class="expand-row-msg" data-name="user_in" data-id="{{$postman->id}}">
                        <span class="show-short-user_in-{{$postman->id}}">{{ Str::limit($postman->user_in  , 5, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-user_in-{{$postman->id}} hidden">{{$postman->user_in}}</span>
                    </td>
                @endif

                @if (!in_array('Action', $dynamicColumnsToShowPostman))
                    <td>
                        <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$postman->id}}')"><i class="fa fa-arrow-down"></i></button>
                    </td>
                @endif
            </tr>

            @if (!in_array('Action', $dynamicColumnsToShowPostman))
                <tr class="action-btn-tr-{{$postman->id}} d-none">
                    <td class="font-weight-bold">Action</td>
                    <td colspan="11" class="cls-actions">
                        <div>
                            <div class="row cls_action_box" style="margin:0px;">
                                <a title="Send Request" class="btn btn-image abtn-pd postman-list-url-btn postman-send-request-btn1 pd-5 btn-ht" data-id="{{ $postman->id }}" data-toggle="modal" data-target="#postmanmulUrlDetailsModel" href="javascript:;">
                                <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                </a>
                                <a title="Edit Request" class="btn btn-image edit-postman-btn abtn-pd" data-id="{{ $postman->id }}"><img data-id="{{ $postman->id }}" src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
                                @if (Auth::user()->isAdmin())
                                <a title="Delete Request" class="btn delete-postman-btn abtn-pd padding-top-action" data-id="{{ $postman->id }}" href="#"><img data-id="{{ $postman->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
                                <a title="Edit History" class="btn abtn-pd preview_edit_history padding-top-action" data-id="{{ $postman->id }}" href="javascript:;"><i class="fa fa-tachometer" aria-hidden="true"></i></a>
                                @endif
                                <a title="History" class="btn postman-history-btn abtn-pd padding-top-action" data-id="{{ $postman->id }}" href="#"><i class="fa fa-history" aria-hidden="true"></i></a>
                                <a title="Preview Response" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_response pd-5 btn-ht" href="javascript:;"><i class="fa fa-product-hunt" aria-hidden="true"></i></a>
                                <a title="Preview Requested" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_requested pd-5 btn-ht" href="javascript:;"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                <a title="Preview Remark History" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_remark_history pd-5 btn-ht" href="javascript:;"><i class="fa fa-history" aria-hidden="true"></i></a>
                                <a title="Preview Error" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_postman_error pd-5 btn-ht" href="javascript:;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></a>

                                <button style="padding:3px;" title="create quick task" type="button" class="btn btn-image d-inline create-quick-task " data-id="@if ($postman) {{ $postman->id }} @endif"  data-category_title="Postman Page" data-title="@if ($postman) {{$postman->request_name.' - Postman Page - '.$postman->id  }} @endif"><i class="fa fa-plus" aria-hidden="true"></i></button>

                                <button style="padding-left: 0;padding-left:3px;" type="button" class="btn btn-image d-inline count-dev-customer-tasks" title="Show task history" data-id="@if ($postman) {{ $postman->id }} @endif" data-category="{{ $postman->id }}"><i class="fa fa-info-circle"></i></button>
                                <button type="button" title="Add Remark" style="padding:3px;" class="btn  btn-image d-inline add-remark" data-id="{{ $postman->id }}"><i class="fa fa-comment" aria-hidden="true"></i></button>
                                <button type="button" title="View Request History" style="padding:3px;" class="btn  btn-image d-inline responses-history" data-id="{{ $postman->id }}"><i class="fa fa-history" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </td>
                </tr>
            @endif
        @else
            <tr style="background-color: {{$postman?->postmanStatus?->postman_color ?? ""}}!important;">
            <td>{{$postman->id}}</td>
            <td class="expand-row-msg" data-name="name" data-id="{{$postman->id}}">
                <span class="show-short-name-{{$postman->id}}">{{ Str::limit($postman->name, 5, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-name-{{$postman->id}} hidden">{{$postman->name}}</span>
            </td>
            <td style="width: 25%;">
                <div class="d-flex align-items-center">
                <select name="status" class="status-dropdown" data-id="{{$postman->id}}">
                    <option value="">Select Status</option>
                    @foreach ($status as $stat)
                    <option value="{{$stat->id}}" {{$postman->status_id == $stat->id ? 'selected' : ''}}>{{$stat->status_name}}</option>
                    @endforeach
                </select>
                <button type="button" data-id="{{ $postman->id  }}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                </div>
            </td>
            <td style="width: 15%;">
                <div class="d-flex align-items-center">
                <select name="api_issue_fix_done" class="api-issue-fix-done-dropdown" data-id="{{$postman->id}}">
                    <option value="">Select</option>
                    <option value="0" {{$postman->api_issue_fix_done === 0 ? 'selected' : ''}}>No</option>
                    <option value="1" {{$postman->api_issue_fix_done === 1 ? 'selected' : ''}}>Yes</option>
                    <option value="2" {{$postman->api_issue_fix_done === 2 ? 'selected' : ''}}>Lead Verified</option>
                </select>
                <button type="button" data-id="{{ $postman->id  }}" class="btn btn-image api-issue-fix-done-history-show p-0 ml-2"  title="Api Issue Fix Done Histories" ><i class="fa fa-info-circle"></i></button>
                </div>
            </td>
            
            <td class="expand-row-msg" data-name="controller_name" data-id="{{$postman->id}}">
                <span class="show-short-controller_name-{{$postman->id}}">{{ Str::limit($postman->controller_name, 5, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-controller_name-{{$postman->id}} hidden">{{$postman->controller_name}}</span>
            </td>
            <td class="expand-row-msg" data-name="method_name" data-id="{{$postman->id}}">
                <span class="show-short-method_name-{{$postman->id}}">{{ Str::limit($postman->method_name, 5, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-method_name-{{$postman->id}} hidden">{{$postman->method_name}}</span>
            </td>
            <td class="expand-row-msg" data-name="request_name" data-id="{{$postman->id}}">
                <span class="show-short-request_name-{{$postman->id}}">{{ Str::limit($postman->request_name, 5, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-request_name-{{$postman->id}} hidden">{{$postman->request_name}}</span>
            </td>
            <td class="expand-row-msg" data-name="request_type" data-id="{{$postman->id}}">
                <span class="show-short-request_type-{{$postman->id}}">{{ Str::limit($postman->request_type, 5, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-request_type-{{$postman->id}} hidden">{{$postman->request_type}}</span>
            </td>
            <td class="expand-row-msg" data-name="url" data-id="{{$postman->id}}">
                <span class="show-short-url-{{$postman->id}}">{{ Str::limit($postman->request_url, 5, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-url-{{$postman->id}} hidden">{{$postman->request_url}}</span>
            </td>
            <td class="expand-row-msg" data-name="paramiters" data-id="{{$postman->id}}">
                <span class="show-short-paramiters-{{$postman->id}}">@if($postman->body_json && $postman->body_json != NULL){{ Str::limit($postman->body_json, 5, '..')}}@else{{"None"}}@endif</span>
                <span style="word-break:break-all;" class="show-full-paramiters-{{$postman->id}} hidden">@if($postman->body_json && $postman->body_json != NULL){{$postman->body_json}}@else{{"None"}}@endif</span>
            </td>
            <td class="expand-row-msg" data-name="params" data-id="{{$postman->id}}">
                <span class="show-short-params-{{$postman->id}}">{{ Str::limit($postman->params, 5, '...')}}</span>
                <span style="word-break:break-all;" class="show-full-params-{{$postman->id}} hidden">{{$postman->params}}</span>
            </td>
            <td class="expand-row-msg" data-name="headers" data-id="{{$postman->id}}">
                <span class="show-short-headers-{{$postman->id}}">{{ Str::limit($postman->request_headers, 5, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-headers-{{$postman->id}} hidden">{{$postman->request_headers}}</span>
            </td>
            <td>{{$postman->request_type}}</td>
            <td class="expand-row-msg" data-name="response" data-id="{{$postman->id}}">
                <span class="show-short-response-{{$postman->id}}">{{ Str::limit($postman->response, 12, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-response-{{$postman->id}} hidden">{{$postman->response}}</span>
            </td>
            <td class="expand-row-msg" data-name="response_code" data-id="{{$postman->id}}">
                <span class="show-short-response_code-{{$postman->id}}">{{ Str::limit($postman->response_code  , 5, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-response_code-{{$postman->id}} hidden">{{$postman->response_code}}</span>
            </td>
            <td class="expand-row-msg" data-name="grumphp_errors" data-id="{{$postman->id}}">
                <span class="show-short-grumphp_errors-{{$postman->id}}">{{ Str::limit($postman->grumphp_errors  , 8, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-grumphp_errors-{{$postman->id}} hidden">{{$postman->grumphp_errors}}</span>
            </td>
            <td class="expand-row-msg" data-name="magento_api_standards" data-id="{{$postman->id}}">
                <span class="show-short-magento_api_standards-{{$postman->id}}">{{ Str::limit($postman->magento_api_standards  , 15, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-magento_api_standards-{{$postman->id}} hidden">{{$postman->magento_api_standards}}</span>
            </td>
            <td class="expand-row-msg" data-name="swagger_doc_block" data-id="{{$postman->id}}">
                <span class="show-short-swagger_doc_block-{{$postman->id}}">{{ Str::limit($postman->swagger_doc_block  , 15, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-swagger_doc_block-{{$postman->id}} hidden">{{$postman->swagger_doc_block}}</span>
            </td>
            <td class="expand-row-msg" data-name="used_for" data-id="{{$postman->id}}">
                <span class="show-short-used_for-{{$postman->id}}">{{ Str::limit($postman->used_for  , 5, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-used_for-{{$postman->id}} hidden">{{$postman->used_for}}</span>
            </td>
            <td class="expand-row-msg" data-name="user_in" data-id="{{$postman->id}}">
                <span class="show-short-user_in-{{$postman->id}}">{{ Str::limit($postman->user_in  , 5, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-user_in-{{$postman->id}} hidden">{{$postman->user_in}}</span>
            </td>
            <td>
                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$postman->id}}')"><i class="fa fa-arrow-down"></i></button>
            </td>
                </tr>
                <tr class="action-btn-tr-{{$postman->id}} d-none">
                <td class="font-weight-bold">Action</td>
                <td colspan="11" class="cls-actions">
                    <div>
                        <div class="row cls_action_box" style="margin:0px;">
                            <a title="Send Request" class="btn btn-image abtn-pd postman-list-url-btn postman-send-request-btn1 pd-5 btn-ht" data-id="{{ $postman->id }}" data-toggle="modal" data-target="#postmanmulUrlDetailsModel" href="javascript:;">
                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                            </a>
                            <a title="Edit Request" class="btn btn-image edit-postman-btn abtn-pd" data-id="{{ $postman->id }}"><img data-id="{{ $postman->id }}" src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
                            @if (Auth::user()->isAdmin())
                            <a title="Delete Request" class="btn delete-postman-btn abtn-pd padding-top-action" data-id="{{ $postman->id }}" href="#"><img data-id="{{ $postman->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
                            <a title="Edit History" class="btn abtn-pd preview_edit_history padding-top-action" data-id="{{ $postman->id }}" href="javascript:;"><i class="fa fa-tachometer" aria-hidden="true"></i></a>
                            @endif
                            <a title="History" class="btn postman-history-btn abtn-pd padding-top-action" data-id="{{ $postman->id }}" href="#"><i class="fa fa-history" aria-hidden="true"></i></a>
                            <a title="Preview Response" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_response pd-5 btn-ht" href="javascript:;"><i class="fa fa-product-hunt" aria-hidden="true"></i></a>
                            <a title="Preview Requested" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_requested pd-5 btn-ht" href="javascript:;"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a title="Preview Remark History" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_remark_history pd-5 btn-ht" href="javascript:;"><i class="fa fa-history" aria-hidden="true"></i></a>
                            <a title="Preview Error" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_postman_error pd-5 btn-ht" href="javascript:;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></a>

                            <button style="padding:3px;" title="create quick task" type="button" class="btn btn-image d-inline create-quick-task " data-id="@if ($postman) {{ $postman->id }} @endif"  data-category_title="Postman Page" data-title="@if ($postman) {{$postman->request_name.' - Postman Page - '.$postman->id }} @endif"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            <button type="button" title="Add Remark" style="padding:3px;" class="btn  btn-image d-inline add-remark" data-id="{{ $postman->id }}"><i class="fa fa-comment" aria-hidden="true"></i></button>
                            <button type="button" title="View Request History" style="padding:3px;" class="btn  btn-image d-inline responses-history" data-id="{{ $postman->id }}"><i class="fa fa-history" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </td>
                </tr>
        @endif        
    @endif

@endforeach
