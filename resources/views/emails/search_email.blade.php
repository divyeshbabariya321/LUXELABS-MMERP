@foreach ($emails as $key => $email)
<tr id="{{ $email->id }}-email-row" class="search-rows">
  <td>@if($email->status != 'bin')
    <input name="selector[]" id="ad_Checkbox_{{ $email->id }}" class="ads_Checkbox" type="checkbox" value="{{ $email->id }}" style="margin: 0px; height: auto;" />
    @endif
  </td>
  <td>{{ Carbon\Carbon::parse($email->created_at)->format('d-m-Y H:i:s') }}</td>
  <td data-toggle="modal" data-target="#viewMore"  onclick="opnModal('{{$email->from}}')">
    {{ substr($email->from, 0,  20) }} {{strlen($email->from) > 20 ? '...' : '' }}
  </td>
  <td  data-toggle="modal" data-target="#viewMore"  onclick="opnModal('{{$email->to}}')">
    {{ substr($email->to, 0,  15) }} {{strlen($email->to) > 10 ? '...' : '' }}
  </td>
  <td>
    @if(array_key_exists($email->model_type, $emailModelTypes))
    {{$email->model_type? $emailModelTypes[$email->model_type] : 'N/A' }}
    @else
    {{ $email->model_type }}
    @endif
  </td>
  <td>{{ $email->type }}</td>
  <td data-toggle="modal" data-target="#view-quick-email"  onclick="openQuickMsg({{$email}})" style="cursor: pointer;">{{ substr($email->subject, 0,  15) }} {{strlen($email->subject) > 10 ? '...' : '' }}</td>

  <td width="1%">
    @if($email->status != 'bin')
    <select class="form-control selecte2 status">
      <option  value="" >Please select</option>
      @foreach($email_status as $status)
      @if($status->id == (int)$email->status)
      <option  value="{{ $status->id }}" data-id="{{$email->id}}"   selected>{{ $status->email_status }}</option>
      @else
      <option  value="{{ $status->id }}" data-id="{{$email->id}}" >{{$status->email_status }}</option>
      @endif
      @endforeach
    </select>
    @else
    Deleted
    @endif
  </td>
  <td class="chat-msg">{{ ($email->is_draft == 1) ? "Yes" : "No" }}</td>
  <td class="expand-row table-hover-cell p-2">
    <span class="td-mini-container">
    {{ strlen($email->error_message) > 20 ? substr($email->error_message, 0, 20).'...' : $email->error_message }}
    </span>
    <span class="td-full-container hidden">
    {{ $email->error_message }}
    </span>
  </td>
  <td>
    <select class="form-control selecte2 email-category">
      <option  value="" >Please select</option>
      @foreach($email_categories as $email_category)
          <option  value="{{ $email_category->id }}" data-id="{{$email->id}}" {{ $email_category->id == $email->email_category_id ? 'selected' : '' }}>{{$email_category->category_name }}</option>
      @endforeach
    </select>
  </td>
  <td>
    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn({{ $email->id }})"><i class="fa fa-arrow-down"></i></button>
  </td>
</tr>
<tr class="action-btn-tr-{{ $email->id }} d-none">
  <th>Action</th>
  <td colspan="11">
    @if($email->type != "incoming")
    <a title="Resend"  class="btn-image resend-email-btn" data-type="resend" data-id="{{ $email->id }}" >
    <i class="fa fa-repeat"></i>
    </a>
    @endif
    <a title="Reply" class="btn-image reply-email-btn" data-toggle="modal" data-target="#replyMail" data-id="{{ $email->id }}" >
    <i class="fa fa-reply"></i>
    </a>
    <a title="Reply All" class="btn-image reply-all-email-btn" data-toggle="modal" data-target="#replyAllMail" data-id="{{ $email->id }}" >
      <i class="fa fa-reply-all"></i>
    </a>
    <a title="Forward" class="btn-image forward-email-btn" data-toggle="modal" data-target="#forwardMail" data-id="{{ $email->id }}" >
    <i class="fa fa-share"></i>
    </a>
    <a title="Bin" class="btn-image bin-email-btn" data-id="{{ $email->id }}" >
    <i class="fa fa-trash"></i>
    </a>
    <button style="padding:3px;" type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $email->id }}"><img width="2px;" src="/images/remark.png"/></button>
    <button style="padding:3px;" type="button" class="btn btn-image make-remark d-inline mailupdate border-0" data-toggle="modal" data-status="{{ $email->status }}" data-category="{{ $email->email_category_id}}" data-target="#UpdateMail" data-id="{{ $email->id }}"><img width="2px;" src="images/edit.png"/></button>
    <a title="Import Excel Imported" href="javascript:void(0);">  <i class="fa fa-cloud-download" aria-hidden="true" onclick="excelImporter({{ $email->id }})"></i></a>
    <button style="padding:3px;" type="button" class="btn btn-image d-inline" onclick="showFilesStatus({{ $email->id }})">  <i class="fa fa-history" aria-hidden="true" ></i></button>
    @if($email->email_excel_importer == 1)
    <a href="javascript:void(0);">  <i class="fa fa-check"></i></a>
    @endif
    @if($email->approve_mail == 1)
    <a title="Approve and send watson reply" class="btn-image resend-email-btn" data-id="{{ $email->id }}" data-type="approve" href="javascript:void(0);">  <i class="fa fa-check-circle"></i></a>
    @endif
    <a class="btn btn-image btn-ht" href="{{route('order.generate.order-mail.pdf', ['order_id' => 'empty', 'email_id' => $email->id])}}">
    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
    </a>
    <button style="padding:3px;" type="button" class="btn btn-image make-label d-inline" data-toggle="modal" data-target="#labelingModal" data-id="{{ $email->id }}"><i class="fa fa-tags" aria-hidden="true"></i></button>
    <a class="btn btn-image btn-ht" onclick="fetchEvents('{{$email['id']}}')">
    <i class="fa fa-eye" aria-hidden="true"></i>
    </a>
    <a class="btn btn-image btn-ht" title="View Email Log" onclick="fetchEmailLog('{{$email['id']}}')">
    <i class="fa fa-history" aria-hidden="true"></i>
    </a>

    @if(empty($email->module_type) && $email->is_unknow_module == 1)
      <a style="padding:3px;" type="button" title="Assign Model" class="btn btn-image make-label d-inline" data-id="{{ $email->id }}" onclick="openAssignModelPopup(this);"> <i class="fa fa-envelope" aria-hidden="true"></i> </a>
    @endif


    <a style="padding:3px;" type="button" title="Email Category Change Logs" class="btn btn-image make-label d-inline" data-id="{{ $email->id }}" onclick="openEmailCategoryChangeLogModelPopup(this);"> <i class="fa fa-calendar" aria-hidden="true"></i> </a>

    <a style="padding:3px;" type="button" title="Email Status Change Logs" class="btn btn-image make-label d-inline" data-id="{{ $email->id }}" onclick="openEmailStatusChangeLogModelPopup(this);"> <i class="fa fa-calendar" aria-hidden="true"></i> </a>
      @if($email->customer_id > 0)
        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image"
                onclick="changeSimulatorSetting('customer', {{ $email->customer_id }}, {{ $email->customer_auto_simulator == 0 }})">
          <i style="color: #757575c7;" class="fa fa-{{$email->customer_auto_simulator == 0 ? 'play' : 'pause'}}"
             aria-hidden="true"></i>
        </button>
        <a href="{{  route('simulator.message.list', ['object' => 'customer', 'object_id' =>  $email->customer_id]) }}"
           title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></a>
      @elseif($email->vendor_id > 0)
        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image"
                onclick="changeSimulatorSetting('vendor', {{ $email->vendor_id }}, {{ $email->vendor_auto_simulator == 0 }})">
          <i style="color: #757575c7;" class="fa fa-{{$email->vendor_auto_simulator == 0 ? 'play' : 'pause'}}"
             aria-hidden="true"></i>
        </button>
        <a href="{{  route('simulator.message.list', ['object' => 'customer', 'object_id' =>  $email->vendor_id]) }}"
           title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></a>
      @elseif($email->supplier_id > 0)
        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image"
                onclick="changeSimulatorSetting('vendor', {{ $email->supplier_id }}, {{ $email->supplier_auto_simulator == 0 }})">
          <i style="color: #757575c7;" class="fa fa-{{$email->supplier_auto_simulator == 0 ? 'play' : 'pause'}}"
             aria-hidden="true"></i>
        </button>
        <a href="{{  route('simulator.message.list', ['object' => 'customer', 'object_id' =>  $email->supplier_id]) }}"
           title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></a>
      @endif
  </td>
</tr>
@endforeach
