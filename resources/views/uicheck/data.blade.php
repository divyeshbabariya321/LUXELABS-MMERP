    @php
        $isAdmin = auth()
            ->user()
            ->isAdmin();
        $isHod = auth()
            ->user()
            ->hasRole('HOD of CRM');
        $hasSiteDevelopment = auth()
            ->user()
            ->hasRole('Site-development');
        $userId = auth()->user()->id;
        $pagrank = $categories->perPage() * ($categories->currentPage() - 1) + 1;
    @endphp
    @foreach ($site_development_categories as $key => $category)
        <?php
    $site = $category->getDevelopment($category->id, isset($website) ? $website->id : $category->website_id, $category->site_development_id); //
        $site = $category->getDevelopment($category->id, $category->website_id, $category->site_development_id);
        $uiCheck = \App\Helpers\DevelopmentHelper::getFirstUicheckBySiteDevelopmentCatId($category->id);
        //if ($isAdmin || $hasSiteDevelopment || ($site && $site->developer_id == $userId)) {
        ?>

        <tr>
            <td>
                {{ $pagrank++ }}
            </td>
            <td class="expand-row-msg" data-name="title" data-id="{{$category->id}}">
                
                    <span class="show-short-title-{{$category->id}}">{{ Str::limit($category->title, 15, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-title-{{$category->id}} hidden">{{$category->title}}</span>
            </td>
            <td>
                @if (Auth::user()->hasRole('Admin'))                
                <select name="website_id"  class="save-item-select globalSelect2 website_id" data-category="{{$category->id ?? '0'}}" data-id="{{$uiCheck->id ?? '0' }}" data-site_development_id="{{$category->site_id }}">
                    <option value="">--Select--</option>
                    @foreach ($all_store_websites as $website )
                        <?php $selected = '';
        $uiWebsiteId = $uiCheck->website_id ?? '';
        if ($uiWebsiteId == $website->id) {
            $selected = "selected='selected'";
        }
        ?>
                        <option value="{{$website->id}}" {{$selected}} >{{$website->website}}</option>
                    @endforeach
                </select>
                @endif
            </td>            
                <td>
                    <select name="user_id"  class="save-item-select globalSelect2 website_id">
                        <option value="">--Select--</option>
                        @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </td>            
            <td class="pt-0 pr-2">
                <div class="col-md-12 mb-1 p-0 d-flex pt-2 mt-1">
                    <input style="margin-top: 0px;width:87% !important;" type="text"
                        class="form-control " id="issue-{{$uiCheck->id ?? ''}}" name="issue-{{$uiCheck->id ?? ''}}" placeholder="Issues" value="{{$uiCheck->issue ?? ''}}"
                        >
                    <div style="margin-top: 0px;" class="d-flex p-0">
                        <button class="btn pr-0 btn-xs btn-image issue" 
                        data-category="{{$category->id ?? ''}}" data-id="{{$uiCheck->id ?? '' }}" data-site_development_id="{{$category->site_id }}"
                        ><img src="/images/filled-sent.png" /></button>
                    </div>
                
                    <button type="button" class="btn btn-xs show-issue-history" title="Show Issue History" data-id="{{$uiCheck->id ?? ''}}"><i data-id="{{$uiCheck->id ?? ''}}" class="fa fa-info-circle"></i></button>
                </div>
            </td>

            <td>
                <div class="col-md-12 mb-1 p-0 d-flex pl-4 pt-2 mt-1 msg" style="width: 100%;">
                    @if (Auth::user()->hasRole('Admin'))
                    <input type="text" style="width: 100%; float: left;"
                        class="form-control quick-message-field input-sm" name="message"
                        placeholder="Message" value="">
                    <div class="d-flex p-0">
                        <button style="float: left;padding: 0 0 0 5px"
                            class="btn btn-sm btn-image send-message" title="Send message"
                            data-category="{{$category->id ?? ''}}" data-taskid="{{$uiCheck->id ?? '' }}"  data-id="{{$uiCheck->id ?? '' }}" data-site_development_id="{{$category->site_id }}"
                            ><img src="/images/filled-sent.png"
                                style="cursor: default;"></button>
                    </div>
                    @endif
                    <button type="button"
                        class="btn btn-xs btn-image load-communication-modal load-body-class"
                        data-object="uicheck"
                        data-id="{{ $uiCheck->id ?? '' }}" title="Load messages"
                        data-category="{{$category->id ?? ''}}" data-site_development_id="{{$category->site_id }}"
                        data-dismiss="modal"><img src="/images/chat.png" alt=""></button>
                </div>
                
            </td>

            
            <td class="pt-1">
                {{ html()->select('developer_status', ['' => '-- Select --'] + $allStatus, $uiCheck->dev_status_id ?? '')->class('form-control save-item-select width-auto globalSelect2 developer_status')->data('type', 'developer_status')->data('category', $category->id ?? '')->data('id', $uiCheck->id ?? '')->data('site_development_id', $category->site_id) }}
                <button type="button" class="btn btn-xs show-dev-status-history" title="Show Developer Status History" data-id="{{$uiCheck->id ?? ''}}"><i data-id="{{$uiCheck->id ?? ''}}" class="fa fa-info-circle"></i></button>
            </td>

            <td class="pt-1">
                @if (Auth::user()->hasRole('Admin'))
                {{ html()->select('admin_status', ['' => '-- Select --'] + $allStatus, $uiCheck->admin_status_id ?? '')->class('form-control save-item-select width-auto globalSelect2 admin_status')->data('type', 'admin_status')->data('category', $category->id ?? '')->data('id', $uiCheck->id ?? '')->data('site_development_id', $category->site_id) }}
                @endif
                <button type="button" class="btn btn-xs show-admin-status-history" title="Show" data-id="{{$uiCheck->id ?? ''}}"><i data-id="{{$uiCheck->id ?? ''}}" class="fa fa-info-circle"></i></button>
            </td>
        </tr>
  
    @endforeach
