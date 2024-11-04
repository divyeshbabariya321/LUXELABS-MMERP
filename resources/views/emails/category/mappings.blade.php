@extends('layouts.app')
@section('large_content')
@section('styles')
<style type="text/css">
  #loading-image {
  position: fixed;
  top: 50%;
  left: 50%;
  margin: -50px 0px 0px -50px;
  z-index: 60;
  }
  .nav-item a{
  color:#555;
  }
  a.btn-image{
  padding:2px 2px;
  }
  .text-nowrap{
  white-space:nowrap;
  }
  .search-rows .btn-image img{
  width: 12px!important;
  }
  .search-rows .make-remark
  {
  border: none;
  background: none
  }
  .table-responsive select.select {
  width: 110px !important;
  }
</style>
@endsection
<div id="myDiv">
  <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
</div>
<div class="row">
  <div class="col-md-12 p-0">
    <h2 class="page-heading">Emails Mappings List</h2>
  </div>
</div>
@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif
@if ($message = Session::get('danger'))
<div class="alert alert-danger">
  <p>{{ $message }}</p>
</div>
@endif

<div class="row">
  <div class="col-12 mb-3 mt-4">
    <div class="pull-left">
      <form class="form-inline" >
        <div class="form-group ">
          <input id="term" name="term" type="text" class="form-control"
            value="<?php if(Request::get('term')) {echo Request::get('term');} ?>"
            placeholder="Search by Keyword">
        </div>
        <div class="form-group px-2">
          <select class="form-control sender_select" name="sender" id="sender" style="width: 208px !important;" multiple data-email-sender-dropdown>
            <option value="">Select Sender</option>
          </select>
        </div>
        <div class="form-group px-2">
          <select class="form-control receiver_select" name="receiver" id="receiver" style="width: 208px !important;" multiple data-email-receiver-dropdown>
            <option value="">Select Receiver</option>
          </select>
        </div>
        <div class="form-group px-2">
          <select class="form-control select_category" name="category" id="category" multiple>
            <option value="">Select Category</option>
            <?php
              foreach ($email_categories as $category) { ?>
            <option value="<?php echo $category->id;?>" <?php if($category->id == Request::get('category')) {echo "selected";} ?>><?php echo $category->category_name;?></option>
            <?php } 
              ?>
          </select>
        </div>
        <div class="form-group px-2">
          <select class="form-control model_type_select" name="email_model_type" id="email_model_type" multiple>
            @foreach($emailModelTypes as $m => $module)
            <option value="{{$m}}">{{$module}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group px-2">
          <select class="form-control email_box_select" name="email_box_id" id="email_box_id" multiple>
            @foreach($emailBoxes as $emailBox)
              <option value="{{ $emailBox['id'] }}">{{$emailBox['box_name']}}</option>
            @endforeach
          </select>
        </div>
        <input type='hidden' class="form-control" id="type" name="type" value="" />
        <button type="submit" class="btn btn-image ml-3 search-btn"><i class="fa fa-filter" aria-hidden="true"></i></button>
      </form>
    </div>
  </div>
</div>

<div class="table-responsive mt-3" style="margin-top:20px;">
  <table class="table table-bordered" style="border: 1px solid #ddd;" id="email-table">
    <thead>
      <tr>
        <th width="5%">Date</th>
        <th width="5%">Sender</th>
        <th width="5%">Receiver</th>
        <th width="4%">Mail <br> Type</th>
        <th width="5%">Subject</th>
        <th width="14%">Body</th>
        <th width="1%">Draft</th>
        <th width="6%">Error <br> Message</th>
        <th width="8%">Category</th>
      </tr>
    </thead>
    <tbody>
    @foreach ($userEmails as $key => $email)
        <tr id="{{ $email->id }}-email-row" class="search-rows">
            <td>{{ Carbon\Carbon::parse($email->created_at)->format('d-m-Y H:i:s') }}</td>
            <td data-toggle="modal" data-target="#viewMore"  onclick="opnModal('{{$email->from}}')"> 
                {{ substr($email->from, 0,  20) }} {{strlen($email->from) > 20 ? '...' : '' }}
            </td>
            <td  data-toggle="modal" data-target="#viewMore"  onclick="opnModal('{{$email->to}}')">
                {{ substr($email->to, 0,  15) }} {{strlen($email->to) > 10 ? '...' : '' }}
            </td>
            <td>{{ $email->type }}</td>
            <td data-toggle="modal" data-target="#viewMail"  onclick="opnMsg({{$email}})" style="cursor: pointer;">{{ substr($email->subject, 0,  15) }} {{strlen($email->subject) > 10 ? '...' : '' }}</td>
            <td class="table-hover-cell p-2" onclick="toggleMsgView({{$email->id}})">
                <span id="td-mini-container-{{$email->id}}" class="">
                {{ substr($email->message, 0,  25) }} {{strlen($email->message) > 20 ? '...' : '' }}
                </span>
                <span id ="td-full-container-{{$email->id}}" class="hidden">
                <iframe src="" id="listFrame-{{$email->id}}" scrolling="no" style="width:100%;" frameborder="0" onload="autoIframe('listFrame-{{$email->id}}');"></iframe>
                </span>
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
                @if($email_category->id == (int)$email->email_category_id)
                <option  value="{{ $email_category->id }}" data-id="{{$email->id}}"   selected>{{ $email_category->category_name }}</option>
                @else
                <option  value="{{ $email_category->id }}" data-id="{{$email->id}}" >{{$email_category->category_name }}</option>
                @endif
                @endforeach
                </select>
            </td>
        </tr>
        @endforeach
    </tbody>
  </table>
  <div class="pagination-custom">
    {{ $userEmails->links() }}
  </div>
</div>
<div id="viewMore" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">View More</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p><span id="more-content"></span> </p>
      </div>
    </div>
  </div>
</div>
<div id="viewMail" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">View Email</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p><strong>Subject : </strong> <span id="emailSubject"></span> </p>
        <p><strong>Message : </strong> <span id="emailMsg"></span> </p>
        <iframe src="" id="eFrame" scrolling="no" style="width:100%;" frameborder="0" onload="autoIframe('eFrame');"></iframe>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
  $(window).scroll(function() {
      if($(window).scrollTop() == $(document).height() - $(window).height()) {
        console.log('ajax call or some other logic to show data here');
        $(".pagination-custom").find(".pagination").find(".active").next().find("a").click();
      }
  });

  function opnMsg(email) {
    console.log(email);
    $('#eFrame').attr('src', "");
    var emailUrl = '/email/email-frame/'+email.id;
    $('#emailSubject').html(email.subject);
    $('#eFrame').attr('src', emailUrl);
    // Mark email as seen as soon as its opened
    if(email.seen ==0 || email.seen=='0'){
      // Mark email as read
      var $this = $(this);
          $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/email/'+email.id+'/mark-as-read',
            type: 'put'
          }).done( function(response) {
  
          }).fail(function(errObj) {
  
          });
    }
  
  }

  // To set the iframe height based on content
  function autoIframe(frameId) {
    try {
      frame = document.getElementById(frameId);
      innerDoc = (frame.contentDocument) ?
                frame.contentDocument : frame.contentWindow.document;
      objToResize = (frame.style) ? frame.style : frame;
      objToResize.height = innerDoc.body.scrollHeight + 10 + 'px';
    }
    catch (err) {
      window.status = err.message;
    }
  }
  // toggle iframe and short message view
  function toggleMiniFullView(emailId){
    $('#td-mini-container-'+emailId).toggleClass('hidden');
    $('#td-full-container-'+emailId).toggleClass('hidden');
  }
  function toggleMsgView(emailId){
    var divID = '#' + $(this).attr('id') + '_div';
    $('#listFrame-'+emailId).attr('src', "");
    var emailUrl = '/email/email-frame/'+emailId;
    $('#listFrame-'+emailId).attr('src', emailUrl);
    toggleMiniFullView(emailId);
  }
  // identify click event within iframe
  let interval = window.setInterval(trackClick, 100);
  function trackClick() {
    var elem = document.activeElement;
    if(elem && elem.tagName == 'IFRAME'){
      var emailId = elem.id.split('-')[1];
      toggleMiniFullView(emailId);
      window.focus();
    }
  }

  function opnModal(message){
      console.log(message);
    $(document).find('#more-content').html(message);
  }

  $(document).on('change','.email-category',function(e){
      if($(this).val() != "" && ($('option:selected', this).attr('data-id') != "" || $('option:selected', this).attr('data-id') != undefined)){
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type : "POST",
          url : "{{ route('changeEmailCategory') }}",
          data : {
            category_id : $('option:selected', this).val(),
            email_id : $('option:selected', this).attr('data-id')
          },
          success : function (response){
             location.reload();
          },
          error : function (response){
            //
          }
        })
      }
  });

  $('.model_type_select').select2({
      placeholder:"Select Model Type",
  });
  $('.sender_select').select2({
      placeholder:"Select sender",
  });
  $('.receiver_select').select2({
      placeholder:"Select Receiver",
  });
  $('.select_category').select2({
      placeholder:"Select Category",
  });

  $('.email_box_select').select2({
      placeholder:"Select Mailbox",
  });

  $('.select2-search__field').css('width', '100%')

  $(document).on('click', '[data-reply-add-receiver-btn]', function (){
    $('.reply-row-receiver-items').remove();
    
    $('#addReceiverReplyModal').modal('show');
  });
</script>
@endsection