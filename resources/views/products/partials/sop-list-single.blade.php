<tr id="sid{{ $appendsop->id }}" class="parent_tr" data-id="{{ $appendsop->id }}">
    <td> <input type="checkbox" class="checkbox" name="select_user" data-id="{{ $appendsop->id }}"  data-toemail="@if ($appendsop->user){{$appendsop->user->email}}@endif" value="" style="height:auto;"></td>
    <td class="sop_table_id">
        {{ $appendsop->id }}
    </td>
    <td class="expand-row-msg" data-name="name" data-id="{{$appendsop->id}}">
        <span class="show-short-name-{{$appendsop->id}}">{{ Str::limit($appendsop->name, 17, '..')}}</span>
        <span style="word-break:break-all;" class="show-full-name-{{$appendsop->id}} hidden">{{$appendsop->name}}</span>
    </td>
    <td class="expand-row-msg" data-name="category" data-id="{{$appendsop->id}}">
        @if (isset($appendsop->sopCategory) && count($appendsop->sopCategory) > 0)
            {{ implode(',', $appendsop->sopCategory->pluck('category_name')->toArray() ?? []) }}
        @else
            -
        @endif
        <span class="show-short-category-{{$appendsop->id}}">{{ Str::limit($appendsop->category, 17, '..')}}</span>
        <span style="word-break:break-all;" class="show-full-category-{{$appendsop->id}} hidden">{{$appendsop->category}}</span>
    </td>
    <td class="expand-row-msg" data-name="content" data-id="{{$appendsop->id}}">
        <span class="show-content" id="show_content_{{$appendsop->id}}" data-content="{!! $appendsop->content !!}" data-id="{{$appendsop->id}}">{{ strip_tags(Str::limit($appendsop->content, 50, '..')) }}</span>
    </td>
    <td class="table-hover-cell p-1">
        <div class="select_table">
            <div class="w-50-25-main">
                <div class="w-100">
                    <select name="sop_user_id" class="form-control select2-for-user" id="user_{{$appendsop->id}}">
                        <option value="">Select User</option>
                        @foreach ($users as $user)
                            @if (!$user->isAdmin())
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="w-50 pull-left" style="display:none;">
                    <textarea rows="1" class="form-control" id="messageid_{{ $appendsop->id }}" name="message" placeholder="Message">{!! strip_tags($appendsop->content) !!}</textarea>
                </div>
                <div class="w-25 pull-left pull_button">
                    <div class=" pull_button_inner d-flex">
                        <button class="btn btn-xs send-message-open pull-left" data-user_id="{{ $appendsop->user_id }}" data-id="{{ $appendsop->id }}">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                         <button type="button"
                                class="btn btn-image btn-xs load-communication-modal pull-left"
                                data-id="{{$appendsop->user_id}}" title="Load messages"
                                data-object="SOP">                                
                                <img src="/images/chat.png" alt="" style="cursor: nwse-resize;">
                        </button>
                    </div>
                </div>
            </div>
       </div>
    </td>
    <td>{{ date('yy-m-d', strtotime($appendsop->created_at)) }}</td>
    <td class="p-1">
        <a href="javascript:;" data-id="{{ $appendsop->id }}" class="editor_edit btn btn-xs p-2" >
            <i class="fa fa-edit"></i>
        </a>

        <a class="btn btn-image deleteRecord p-2 text-secondary" data-id="{{ $appendsop->id }}">
            <i class="fa fa-trash" ></i>
        </a>
        <a class="btn btn-xs view_log p-2 text-secondary" title="status-log"
            data-name="{{ $appendsop->purchaseProductOrderLogs ? $appendsop->purchaseProductOrderLogs->header_name : '' }}"
            data-id="{{ $appendsop->id }}" data-toggle="modal" data-target="#ViewlogModal">
            <i class="fa fa-info-circle"></i>
        </a>
        <a title="Download Invoice" class="btn btn-xs p-2" href="{{ route('sop.download',$appendsop->id) }}">
                <i class="fa fa-download downloadpdf"></i>
        </a>
        <button type="button" class="btn send-email-common-btn p-2" data-content="{{$appendsop->content}}" data-toemail="@if ($appendsop->user){{$appendsop->user->email}} @endif" data-object="Sop" data-id="{{$appendsop->user_id}}">
            <i class="fa fa-envelope-square"></i>
        </button>
        <button data-target="#Sop-User-Permission-Modal" data-toggle="modal" class="btn btn-secondaryssss sop-user-list p-2" title="Sop User" data-sop_id="{{ $appendsop->id }}">
            <i class="fa fa-user-o"></i>
        </button>
        <a href="javascript:;" data-id="{{ $appendsop->id }}" data-content="{{$appendsop->content}}" class="menu_editor_copy btn btn-xs p-2" >
            <i class="fa fa-copy"></i>
        </a>
    </td>
</tr>