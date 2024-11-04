@forelse($storeWebsites as $storeWebsite)
    @php
        // Check if $storeWebsite->users_id is not null before decoding the JSON
        $userIdsArray = !is_null($storeWebsite->users_id) ? json_decode($storeWebsite->users_id) : [];
    @endphp
    <tr>
        <input type="hidden" class="update_website_api_id" name="update_website_api_id[]" value="">
        <td class="text-center">
            @if (in_array(auth()->user()->id, $userIdsArray) ||
                    (auth()->user() &&
                        auth()->user()->isAdmin()))
                <span class="td-mini-container">
                    <input type="checkbox" class="selectedStoreWebsite" name="selectedStoreWebsite"
                        value="{{ $storeWebsite->id }}">
                </span>
            @endif
        </td>
        <td>
            {{ $storeWebsite->id }}
        </td>
        <td width="15%">{{ $storeWebsite->title }}</td>


        <td width="45%">
            <div style="display: flex">
                <input type="text" class="form-control" id="api_token_{{ $storeWebsite->id }}"
                    name="api_token[{{ $storeWebsite->id }}]" value="{{ $storeWebsite->api_token }}">
                <button type="button" data-id="" class="btn btn-copy-api-token btn-sm"
                    data-value="{{ $storeWebsite->api_token }}">
                    <i class="fa fa-clone" aria-hidden="true"></i>
                </button>
            </div>
        </td>
        <td width="30%">
            <div style="display: flex">
                <input type="text" class="form-control" name="server_ip[{{ $storeWebsite->id }}]"
                    value="{{ $storeWebsite->server_ip }}">
                <button type="button" data-id="" class="btn btn-copy-server-ip btn-sm"
                    data-value="{{ $storeWebsite->server_ip }}">
                    <i class="fa fa-clone" aria-hidden="true"></i>
                </button>
            </div>
        </td>
        <td width="30%">
            <div style="display: flex">
                @if (auth()->user() &&
                        auth()->user()->isAdmin())
                    @php
                        $dataArray = json_decode($storeWebsite->users_id, true);
                        
                        if (is_array($dataArray) && count($dataArray) > 0) {
                            $value = implode(',', $dataArray);
                        } else {
                            $value = null;
                        }
                    @endphp
                    <button title="User Permission" data-id="{{ $storeWebsite->id }}"
                        data-seletedUsers="{{ $value }}" type="button" class="btn user-api-token"
                        style="padding:1px 5px;" data-toggle="modal"
                        data-target="#generate-user-permission-token-modal">
                        <a href="javascript:;" style="color:gray;"><i class="fa fa-plus"></i></a>
                    </button>
                @endif


                @if (in_array(auth()->user()->id, $userIdsArray) ||
                        (auth()->user() &&
                            auth()->user()->isAdmin()))
                    <button title="Generate API Token" data-id="{{ $storeWebsite->id }}" type="button"
                        class="btn generate-api-token" style="padding:1px 5px;" data-toggle="modal"
                        data-target="#generate-api-token-modal">
                        <a href="javascript:;" style="color:gray;"><i class="fa fa-refresh"></i></a>
                    </button>

                    <button title="API Token Logs" data-id="{{ $storeWebsite->id }}" type="button"
                        class="btn api-token-logs" style="padding:1px 5px;">
                        <a href="javascript:;" style="color:gray;"><i class="fa fa-history"></i></a>
                    </button>
                    <button title="Generate token History" data-id="{{ $storeWebsite->id }}" type="button"
                        class="btn api-token-history" style="padding:1px 5px;">
                        <a href="javascript:;" style="color:gray;"><i class="fa fa-info-circle"></i></a>
                    </button>

                    <button title="Test API Token" data-id="{{ $storeWebsite->id }}" type="button"
                        class="btn btn-test-api-token" style="padding:1px 5px;">
                        <a href="javascript:;" style="color:gray;"><i class="fa fa-plane"></i></a>
                    </button>
                    <button title="Update Api Token" data-id="{{ $storeWebsite->id }}" type="submit" class="btn update-api-token-button" style="padding:1px 5px;"><a 
                        href="javascript:;" style="color:gray;"><i class="fa fa-save"></i></a></button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" style="text-align: center">
            <h4>No Data Found </h4>
        </td>
    </tr>
@endforelse

<script>
    $(document).on('click', '.update-api-token-button', function() {
        var Updatestorwebsitevalue = $(this).data('id');

        // Find the corresponding hidden input field and set its value
        $(this).closest('tr').find('.update_website_api_id').val(Updatestorwebsitevalue);
    });
</script>
