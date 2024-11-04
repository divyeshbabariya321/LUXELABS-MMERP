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
    <h2 class="page-heading">Mailbox</h2>
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
  <div class="col-lg-12 margin-tb">
  </div>
  <div class="col-md-12">
    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="read" role="tabpanel" aria-labelledby="read-tab">
      </div>
      <div class="tab-pane fade" id="unread" role="tabpanel" aria-labelledby="unread-tab">
      </div>
      <div class="tab-pane fade" id="sent" role="tabpanel" aria-labelledby="sent-tab">
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 mb-3 mt-4">
    <div class="pull-left">
      <form class="form-inline" >
        <div class="form-group ">
          <input id="term" name="term" type="text" class="form-control"
            value="<?php if (Request::get('term')) {
                echo Request::get('term');
            } ?>"
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
            <option value="<?php echo $category->id; ?>" <?php if ($category->id == Request::get('category')) {
                echo 'selected';
            } ?>><?php echo $category->category_name; ?></option>
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
        <div class="form-group px-2">
          <select class="form-control" name="email_type" id="email_type">
            <option value="">Select Type</option>
            <option value="Read" <?php if (Request::get('email_type') == 'Read') {
                echo 'selected';
            } ?>>Read</option>
            <option value="Unread" <?php if (Request::get('email_type') == 'Unread') {
                echo 'selected';
            } ?>>Unread</option>
            <option value="Sent" <?php if (Request::get('email_type') == 'Sent') {
                echo 'selected';
            } ?>>Sent</option>
            <option value="Trash" <?php if (Request::get('email_type') == 'Trash') {
                echo 'selected';
            } ?>>Trash</option>
            <option value="Draft" <?php if (Request::get('email_type') == 'Draft') {
                echo 'selected';
            } ?>>Draft</option>
            <option value="Queue" <?php if (Request::get('email_type') == 'Queue') {
                echo 'selected';
            } ?>>Queue</option>
          </select>
        </div>
        
        <input type='hidden' class="form-control" id="type" name="type" value="" />
        <input type='hidden' class="form-control" id="seen" name="seen" value="1" />
        <button type="submit" class="btn btn-image ml-3 search-btn"><i class="fa fa-filter" aria-hidden="true"></i></button>
      </form>
    </div>
  </div>
</div>

<div class="table-responsive mt-3" style="margin-top:20px;">
  <table class="table table-bordered" style="border: 1px solid #ddd;" id="email-table">
    <thead>
      <tr>
        <th width="1%">Bulk <br> Action</th>
        <th width="5%">Date</th>
        <th width="5%">Sender</th>
        <th width="5%">Receiver</th>
        <th width="4%">Model <br> Type</th>
        <th width="4%">Mail <br> Type</th>
        <th width="5%">Subject & Body</th>
        <!-- <th width="14%">Body</th> -->
        <th width="8%">Status</th>
        <th width="1%">Draft</th>
        <th width="6%">Error <br> Message</th>
        <th width="8%">Category</th>
        <th width="8%">Action</th>
      </tr>
    </thead>
    <tbody>
      <!-- @foreach ($emails as $key => $email)
        <tr>
          <td>{{ Carbon\Carbon::parse($email->created_at)->format('d-m-Y') }}</td>
          <td>{{ $email->from }}</td>
          <td>{{ $email->to }}</td>
          <td>{{ $email->type }}</td>
          <td>
            {{$email->subject}}
          </td>
          <td>
            {{$email->message}}
          </td>
          <td>
          </td>
        </tr>
        @endforeach -->
      @include('emails.search_email')
    </tbody>
  </table>
  <div class="pagination-custom">
    {{$emails->links()}}
  </div>
</div>
<div id="replyMail" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg ">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Email reply</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div id="reply-mail-content">
      </div>
    </div>
  </div>
</div>
<div id="replyAllMail" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg ">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Email reply all</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div id="reply-all-mail-content">
      </div>
    </div>
  </div>
</div>
<div id="forwardMail" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Email forward</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div id="forward-mail-content">
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
<div id="createEmailCategorytModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Create Email Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="{{ url('email/category') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <input type="text" name="category_name" value="{{ old('category_name') }}" class="form-control" placeholder="Category Name" required>
          </div>

          <div class="form-group">
            <select class="form-control" id="category_priority" name="priority">
                <option value="HIGH">High</option>
                <option value="MEDIUM">Medium</option>
                <option value="LOW">Low</option>
                <option value="UNDEFINED">Undefined</option>
            </select>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Create</button>
          </div>
      </form>
      </div>
    </div>
  </div>
</div>
<div id="getCronEmailModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cron Email</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Signature</th>
                <th>Status</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
              @if(empty($reports))
              <tr>
                <td colspan="5">
                  No Result Found
                </td>
              </tr>
              @else
              @foreach ($reports as $report)
              <tr>
                <td>
                  {{ $report->signature }}
                </td>
                <td>
                  {{ !empty($report->last_error) ? 'Failed' : 'Success' }}
                </td>
                <td>
                  {{ $report->start_time }}
                </td>
                <td>{{ $report->end_time }}</td>
                <td>{{ $report->updated_at->format('Y-m-d H:i:s')  }}</td>
              </tr>
              @endforeach
              @if ($reports->lastPage() > 1)
              <ul class="pagination cronEmailPagination">
                @for ($i = 1; $i <= $reports->lastPage(); $i++)
                <li class="cronEmailActive{{ $i }} {{ ($i == 1) ? ' active' : '' }}">
                  <a class="cronEmailPage" data-id= "{{ $i }}" href="#">{{ $i }}</a>
                </li>
                @endfor
              </ul>
              @endif
              @endif            
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<div id="statusModel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Create Email Status</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="{{ url('email/status') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <input type="text" name="email_status" value="{{ old('email_status') }}" class="form-control" placeholder="Status">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Create</button>
          </div>
      </form>
      </div>
    </div>
  </div>
</div>
<div id="UpdateMail" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Email List</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="{{ url('email/update_email') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <input type="hidden" name="email_id" id = "email_id">
            <select class="form-control" name="status" id="email_status">
              <option value="">Select Status</option>
              <?php
                foreach ($email_status as $status) { ?>
              <option value="<?php echo $status->id; ?>"><?php echo $status->email_status; ?></option>
              <?php }
                ?>
            </select>
          </div>
          <div class="form-group">
            <select class="form-control" name="category" id="email_category">
              <option value="">Select Category</option>
              <?php
                foreach ($email_categories as $category) { ?>
              <option value="<?php echo $category->id; ?>"><?php echo $category->category_name; ?></option>
              <?php }
                ?>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Store</button>
          </div>
      </form>
      </div>
    </div>
  </div>
</div>
{{-- Showing file status models --}}
<div id="showFilesStatusModel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Files status</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="Status">Files status :</label>
          <div id="filesStatus" class="form-group">  </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="labelingModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Assign Platform</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="{{ action([\App\Http\Controllers\EmailController::class, 'platformUpdate']) }}" method="POST" class="form-group labeling-form">
        @csrf
        <input type="hidden" name="id" value="">
        <div class="modal-body">
          <div class="form-group">
            <div class="col-md-12">
              <label for="Status" class="form-control">Platform</label>
            </div>
            <div class="col-md-12 mb-5">
              <select name="platform" class="form-control select2">
                <option value="">Select Platforms</option>
                @foreach($digita_platfirms as $digita_platfirm)
                <option value="{{ $digita_platfirm->id }}"> {{ $digita_platfirm->platform }} --> {{ $digita_platfirm->sub_platform }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- <div class="form-group">
            <label for="Status">Sub Platform</label>
            <select name="sub-platform" class="form-control">
              
            </select>
            </div> -->
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary" >Submit</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="excelImporter" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Excel Importer</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <select name="supplier" class="form-control" id="supplier_excel_import">
          <option value="">Select a supplier</option>
          <option value="birba_excel">Birba</option>
          <option value="brunarosso_excel">Bruna Rosso</option>
          <option value="colognese_excel">Colognese (Dior)</option>
          <option value="cologneseSecond_excel">Colognese (Balenciaga, Chloe, Valentino)</option>
          <option value="cologneseThird_excel">Colognese (Saint Laurent)</option>
          <option value="cologneseFourth_excel">Colognese (SS20 Shoes)</option>
          <option value="distributionet_excel">Distributionet</option>
          <option value="gru_excel">Gruppo Pritelli</option>
          <option value="maxim_gucci_excel">Maxim Gucci</option>
          <option value="ines_excel">Ines</option>
          <option value="le-lunetier_excel">Le Lunetier</option>
          <option value="lidia_excel">Lidia</option>
          <option value="lidiafirst_excel">Lidia (Salvatore)</option>
          <option value="modes_excel">Modes</option>
          <option value="mv1_excel">MV1</option>
          <option value="master">Master</option>
          <option value="tory_excel">Tory Outlet</option>
          <option value="tessabit_excel">Tessabit</option>
          <option value="valenti_excel">Valenti</option>
          <option value="valentisecond_excel">Valenti New Format</option>
          <option value="dna_excel">DNA Excel</option>
        </select>
        <input type="hidden" id="excel_import_email_id">
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary" onclick="importExcel()">Store</button>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="emailEvents" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg ">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Email reply</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Date</th>
                <th>Event</th>
              </tr>
            </thead>
            <tbody id="emailEventData">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="emailLogs" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg ">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Email Logs</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Date</th>
                <th>Message</th>
                <th>Details</th>
              </tr>
            </thead>
            <tbody id="emailLogsData">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="addReceiverReplyModal" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg ">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Receiver Email</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form action="#" method="post" id="addReceiverReplyForm">
          @csrf

          <div class="table-responsive">
            <table class="table">
              <tbody id="replyReceiverEmailBody">
                  <tr id="reply_receiver_email_row_id_1">
                    <td><input type="email" id="reply_receiver_email_id_1"  name="reply_receiver_emails[]" class="form-control" required></td>
                    <td><button type="button" class="btn btn-primary" data-reply-add-receiver-email-btn><i class="fa fa-plus"></i></button></td>
                  </tr>
              </tbody>
            </table>

            <button class="btn btn-primary" type="submit">Add</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div id="addReceiverReplyAllModal" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg ">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Receiver Email</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form action="#" method="post" id="addReceiverReplyAllForm">
          @csrf

          <div class="table-responsive">
            <table class="table">
              <tbody id="replyAllReceiverEmailBody">
                  <tr id="reply_all_receiver_email_row_id_1">
                    <td><input type="email" id="reply_all_receiver_email_id_1"  name="reply_all_receiver_emails[]" class="form-control" required></td>
                    <td><button type="button" class="btn btn-primary" data-reply-all-add-receiver-email-btn><i class="fa fa-plus"></i></button></td>
                  </tr>
              </tbody>
            </table>

            <button class="btn btn-primary" type="submit">Add</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@include('partials.modals.remarks')
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
  $('.model_type_select').select2({
      placeholder:"Select Model Type",
  });
  $('.sender_select').select2({
      placeholder:"Select sender",
  });
  $('.receiver_select').select2({
      placeholder:"Select Receiver",
  });
  $('.mail_box_select').select2({
      placeholder:"Select Mailbox",
  });
  $('.email_status_select').select2({
      placeholder:"Select Status",
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

  var replyReceiverCount = 1;

  $(document).on('click', '[data-reply-add-receiver-email-btn]', function (){
    replyReceiverCount += 1;
    var receiverHtml = '<tr class="reply-row-receiver-items" id="reply_receiver_email_row_id_'+replyReceiverCount+'">';
    receiverHtml += '<td><input type="email" id="reply_receiver_email_id_'+replyReceiverCount+'" name="reply_receiver_emails[]" class="form-control" required></td>';
    receiverHtml += '<td><button type="button" class="btn btn-danger" data-reply-remove-receiver-email-btn row-id="'+replyReceiverCount+'"><i class="fa fa-close"></i></button></td>';
    receiverHtml += '</tr>';

    $('#replyReceiverEmailBody').append(receiverHtml);
  });

  $(document).on('click', '[data-reply-remove-receiver-email-btn]', function (){
    var rowId = Number($(this).attr('row-id'));

    $('#reply_receiver_email_row_id_'+rowId).remove();
  });

  //option A
  $("#addReceiverReplyForm").submit(function(e){
    e.preventDefault();

    var receiverEmails = [];

    $("input[name='reply_receiver_emails[]']").each(function (){
      var receiverEmail = $(this).val();

      receiverEmails.push(receiverEmail);
    });

    var currentReceiverEmails = $('#reply_receiver_email').val();
    var currentReceiverEmailsArr = currentReceiverEmails.split('');

    if(currentReceiverEmailsArr[0] == '['){
      currentReceiverEmails = currentReceiverEmails.replace(/'/g, '"');
      currentReceiverEmails = JSON.parse(currentReceiverEmails);
    }else{
      currentReceiverEmails = currentReceiverEmails.split(',');
    }

    if(receiverEmails.length > 0){
      $.each(receiverEmails, function (k, v){
        currentReceiverEmails.push(v);
      });
    }

    $('#reply_receiver_email').val("[" + currentReceiverEmails.map(value => `"${value}"`).join(',') + "]");
    
    $('#addReceiverReplyModal').modal('hide');
  });

  //////////////////////////////////////////////////////////////////////////////

  $(document).on('click', '[data-reply-all-add-receiver-btn]', function (){
    $('.reply-all-row-receiver-items').remove();
    
    $('#addReceiverReplyAllModal').modal('show');
  });

  var replyAllReceiverCount = 1;

  $(document).on('click', '[data-reply-all-add-receiver-email-btn]', function (){
    replyAllReceiverCount += 1;
    var receiverHtml = '<tr class="reply-all-row-receiver-items" id="reply_all_receiver_email_row_id_'+replyAllReceiverCount+'">';
    receiverHtml += '<td><input type="email" id="reply_all_receiver_email_id_'+replyAllReceiverCount+'" name="reply_all_receiver_emails[]" class="form-control" required></td>';
    receiverHtml += '<td><button type="button" class="btn btn-danger" data-reply-all-remove-receiver-email-btn row-id="'+replyAllReceiverCount+'"><i class="fa fa-close"></i></button></td>';
    receiverHtml += '</tr>';

    $('#replyAllReceiverEmailBody').append(receiverHtml);
  });

  $(document).on('click', '[data-reply-all-remove-receiver-email-btn]', function (){
    var rowId = Number($(this).attr('row-id'));

    $('#reply_all_receiver_email_row_id_'+rowId).remove();
  });

  //option A
  $("#addReceiverReplyAllForm").submit(function(e){
    e.preventDefault();

    var receiverEmails = [];

    $("input[name='reply_all_receiver_emails[]']").each(function (){
      var receiverEmail = $(this).val();

      receiverEmails.push(receiverEmail);
    });

    var currentReceiverEmails = $('#reply_all_receiver_email').val();
    var currentReceiverEmailsArr = currentReceiverEmails.split('');

    if(currentReceiverEmailsArr[0] == '['){
      currentReceiverEmails = currentReceiverEmails.replace(/'/g, '"');
      currentReceiverEmails = JSON.parse(currentReceiverEmails);
    }else{
      currentReceiverEmails = currentReceiverEmails.split(',');
    }

    if(receiverEmails.length > 0){
      $.each(receiverEmails, function (k, v){
        currentReceiverEmails.push(v);
      });
    }

    $('#reply_all_receiver_email').val("[" + currentReceiverEmails.map(value => `"${value}"`).join(',') + "]");
    
    $('#addReceiverReplyAllModal').modal('hide');
  });

  /////////////////////////////////////////////////////////////////////////////

  $(document).on('click', '.expand-row', function() {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
          $(this).find('.td-mini-container').toggleClass('hidden');
          $(this).find('.td-full-container').toggleClass('hidden');
      }
  });
  
  $(".pagination-custom").on("click", ".page-link", function (e) {
          e.preventDefault();
  
          var activePage = $(this).closest(".pagination").find(".active").text();
          var clickedPage = $(this).text();
          console.log(activePage+'--'+clickedPage);
          if (clickedPage == "‹" || clickedPage < activePage) {
              $('html, body').animate({scrollTop: ($(window).scrollTop() - 50) + "px"}, 200);
              get_data_pagination($(this).attr("href"));
          } else {
              get_data_pagination($(this).attr("href"));
          }
  
      });
  
  function get_data_pagination(url){
   console.log(window.url);
      $.ajax({
        url: url,
        type: 'get',
          beforeSend: function () {
              $("#loading-image").show();
          },
      }).done( function(response) {
        $("#loading-image").hide();
          $("#email-table tbody").empty().append(response.tbody);
          $(".pagination-custom").html(response.links);
  
      }).fail(function(errObj) {
        $("#loading-image").hide();
      });
  }
  
  function fetchEvents(originId) {
  if(originId == ''){
  $('#emailEventData').html('<tr><td>No Data Found.</td></tr>');
  $('#emailEvents').modal('show');
  return;
  } else{
  $.get(window.location.origin+"/email/events/"+originId, function(data, status){
  	$('#emailEventData').html(data);
  	$('#emailEvents').modal('show');
  });
  }
  }
  
  function fetchEmailLog(email_id) {
  if(email_id == ''){
  $('#emailLogsData').html('<tr><td>No Data Found.</td></tr>');
  $('#emailLogs').modal('show');
  return;
  } else{
  $.get(window.location.origin+"/email/emaillog/"+email_id, function(data, status){
  	$('#emailLogsData').html(data);
  	$('#emailLogs').modal('show');
  });
  }
  }
  
      //$("#unread-tab").trigger("click");
  
      var searchSuggestions = {!! json_encode(array_values($search_suggestions), true) !!};
      var _parentElement = $("#forwardMail")
  
      // Limit dropdown to 10 emails and use appenTo to view dropdown on top of modal window.
      var options = {
          source: function (request, response) {
                  var results = $.ui.autocomplete.filter(searchSuggestions, request.term);
                  response(results.slice(0, 10));
              },
          appendTo : _parentElement
      };
  
      // Following is required to load autocomplete on dynamic DOM
      var selector = '#forward-email';
      $(document).on('keydown.autocomplete', selector, function() {
          $(this).autocomplete(options);
      });
  
      $(document).ready(function() {
        $('#email-datetime').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $("select[name='platform']").select2();
      });
  
  
  $(document).on('click', '.search-btn', function(e) {
    e.preventDefault();
    get_data();
  });
  
  function get_data(){
    var term = $("#term").val();
    var sender_name = $("#sender").val();
    var sender = sender_name.toString();
    var receiver_name = $("#receiver").val();
    var receiver = receiver_name.toString();
    var category_name = $("#category").val();
    var category = category_name.toString();
    var email_model_type = $('#email_model_type').val().toString();
    var email_box_id = $('#email_box_id').val().toString();
    var email_type = $('#email_type').val().toString();
  
   console.log(window.url);
      $.ajax({
        url: 'mailbox',
        type: 'get',
        data:{
              term:term,
              sender:sender,
              receiver:receiver,
              category:category,
              email_model_type : email_model_type,
              email_box_id : email_box_id,
              email_type : email_type
          },
          beforeSend: function () {
              $("#loading-image").show();
          },
      }).done( function(response) {
        $("#loading-image").hide();
          $("#email-table tbody").empty().html(response.tbody);
          $(".pagination-custom").html(response.links);
          $('#email_list_count_id').html(response.count);
      }).fail(function(errObj) {
        $("#loading-image").hide();
      });
  }
  
  $(document).ready(function() {
    getEmailFilterOptions();
  });
  
  function getEmailFilterOptions(){
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: 'email/filter-options',
      type: 'post',
      // beforeSend: function () {
      //   $("#loading-image").show();
      // },
    }).done( function(response) {
      var sender = '@php echo Request::get('sender'); @endphp';
      var senderDropdownHtml = '';
  
      $.each(response.senderDropdown, function(k, v){
        senderDropdownHtml += "<option value='"+v.from+"' "+(sender == v.from ? 'selected' : '')+">"+v.from+"</option>";
      });
      $('[data-email-sender-dropdown]').append(senderDropdownHtml);
  
      var receiverName = '@php echo $receiver; @endphp';
      var from = '@php echo $from; @endphp';
      var receiver = '@php echo Request::get('receiver'); @endphp';
      var receiverDropdownHtml = '';
  
      $.each(response.receiverDropdown, function(k, v){
        if(receiverName.length != 0 && from == 'order_data'){
          receiverDropdownHtml += "<option value='"+v.to+"' "+(receiverName == v.to ? 'selected' : '')+">"+v.to+"</option>";
        }else{
          receiverDropdownHtml += "<option value='"+v.to+"' "+(receiver == v.to ? 'selected' : '')+">"+v.to+"</option>";
        }
      });
      $('[data-email-receiver-dropdown]').append(receiverDropdownHtml);
  
      var mailBox = '@php echo Request::get('mail_box'); @endphp';
      var mailboxDropdownHtml = '';
  
      $.each(response.mailboxDropdown, function(k, v){
        mailboxDropdownHtml += "<option value='"+v+"' "+(mailBox == v ? 'selected' : '')+">"+v+"</option>";
      });
      $('[data-email-mailbox-dropdown]').append(mailboxDropdownHtml);

      $('[data-email-sender-dropdown]').select2('destroy').select2({
        placeholder:"Select sender"
      });

      $('[data-email-receiver-dropdown]').select2('destroy').select2({
        placeholder:"Select Receiver"
      });

      $('[data-email-mailbox-dropdown]').select2('destroy').select2({
        placeholder:"Select Mailbox"
      });

      $('.select2-search__field').css('width', '100%')
      // toastr['success'](response.message);
      // $("#loading-image").hide();
    }).fail(function(errObj) {
      // $("#loading-image").hide();
    });
  }
  
  
  $(document).on('click', '.resend-email-btn', function(e) {
    e.preventDefault();
    var $this = $(this);
    var type = $(this).data('type');
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/email/resendMail/'+$this.data("id"),
        type: 'post',
        data: {
          type:type
        },
          beforeSend: function () {
              $("#loading-image").show();
          },
      }).done( function(response) {
        toastr['success'](response.message);
        $("#loading-image").hide();
      }).fail(function(errObj) {
        $("#loading-image").hide();
      });
  });
  
  $(document).on('click', '.reply-email-btn', function(e) {
    e.preventDefault();
    var $this = $(this);
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/email/replyMail/'+$this.data("id"),
        type: 'get',
        beforeSend: function () {
            $("#loading-image").show();
        },
      }).done( function(response) {
        $("#loading-image").hide();
        // toastr['success'](response.message);
        $("#reply-mail-content").html(response);
      }).fail(function(errObj) {
        $("#loading-image").hide();
      });
  });

  $(document).on('click', '.reply-all-email-btn', function(e) {
    e.preventDefault();
    var $this = $(this);
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/email/replyAllMail/'+$this.data("id"),
        type: 'get',
        beforeSend: function () {
            $("#loading-image").show();
        },
      }).done( function(response) {
        $("#loading-image").hide();
        // toastr['success'](response.message);
        $("#reply-all-mail-content").html(response);
      }).fail(function(errObj) {
        $("#loading-image").hide();
      });
  });

  
  
  $(document).on('click', '.forward-email-btn', function(e) {
    e.preventDefault();
    var $this = $(this);
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/email/forwardMail/'+$this.data("id"),
        type: 'get',
          // beforeSend: function () {
          //     $("#loading-image").show();
          // },
      }).done( function(response) {
        $("#forward-mail-content").html(response);
      }).fail(function(errObj) {
        // $("#loading-image").hide();
      });
  });
  
  $(document).on('click', '.submit-reply', function(e) {
    e.preventDefault();
    var receiverEmail = $("#reply_receiver_email").val();
    var replySubject = $("#reply_subject").val();
    var message = $("#reply-message").val();
    var reply_email_id = $("#reply_email_id").val();

      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/email/replyMail',
        type: 'post',
        data: {
          'receiver_email': receiverEmail,
          'subject': replySubject,
          'message': message,
          'reply_email_id': reply_email_id
        },
        beforeSend: function () {
            $("#loading-image").show();
        },
      }).done( function(response) {
        $("#replyMail").modal('hide');
        $("#loading-image").hide();
        toastr['success'](response.message);
      }).fail(function(errObj) {
        $("#replyMail").modal('hide');
        $("#loading-image").hide();
        toastr['error'](response.errors[0]);
  
      });
  });

  $(document).on('click', '.submit-reply-all', function(e) {
    e.preventDefault();
    var receiverEmail = $("#reply_all_receiver_email").val();
    var replySubject = $("#reply_all_subject").val();
    var message = $("#reply-all-message").val();
    var reply_email_id = $("#reply_all_email_id").val();

      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/email/replyAllMails',
        type: 'post',
        data: {
          'receiver_email': receiverEmail,
          'subject': replySubject,
          'message': message,
          'reply_email_id': reply_email_id
        },
        beforeSend: function () {
            $("#loading-image").show();
        },
      }).done( function(response) {
        $("#replyAllMail").modal('hide');
        $("#loading-image").hide();
        toastr['success'](response.message);
      }).fail(function(errObj) {
        $("#replyAllMail").modal('hide');
        $("#loading-image").hide();
        toastr['error'](response.errors[0]);
      });
  });
  
  $(document).on('click', '.submit-forward', function(e) {
    e.preventDefault();
    email = $("#forward-email").val();
    forward_email_id = $("#forward_email_id").val();
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/email/forwardMail',
        type: 'post',
        data: {
          email: email,
          forward_email_id: forward_email_id
        },
        beforeSend: function () {
            $("#loading-image").show();
        },
      }).done( function(response) {
        $("#forwardMail").modal('hide');
        $("#loading-image").hide();
        toastr['success'](response.message);
  
      }).fail(function(errObj) {
        $("#forwardMail").modal('hide');
        $("#loading-image").hide();
        toastr['error'](response.errors[0]);
  
  
      });
  });
  
  $(document).on('click', '.mailupdate', function (e) {
  
  $("#UpdateMail #email_category").val("").trigger('change');
  $("#UpdateMail #email_status").val("").trigger('change');
  
  var email_id = $(this).data('id');
  var status = $(this).data('status');
  var category = $(this).data('category');
  if(category)
  {
  $("#UpdateMail #email_category").val(category).trigger('change');
  }
  if(status)
  {
  $("#UpdateMail #email_status").val(status).trigger('change');
  }
  
  $('#email_id').val(email_id);
  
  });
  
  
  $(document).on('click', '.make-remark', function (e) {
          e.preventDefault();
  
          var email_id = $(this).data('id');
  
          console.log(email_id)
  
          $('#add-remark input[name="id"]').val(email_id);
         
  
          $.ajax({
              type: 'GET',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: '{{ route('email.getremark') }}',
              data: {
                email_id: email_id
              },
              beforeSend: function () {
                  $("#loading-image").show();
              },
          }).done(response => {
              var html = '';
              var no = 1;
              $.each(response, function (index, value) {
                  html += '<tr><th scope="row">' + no + '</th><td>' + value.remarks + '</td><td>' + value.user_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td></tr>';
                  no++;
              });
              $("#makeRemarkModal").find('#remark-list').html(html);
              $("#loading-image").hide();
          }).fail(function (response) {
            $("#loading-image").hide();
            toastr['error'](response.errors[0]);
          });;
      });
  
      $('#addRemarkButton').on('click', function () {
          var id = $('#add-remark input[name="id"]').val();
          var remark = $('#add-remark').find('textarea[name="remark"]').val();
  
          $.ajax({
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: '{{ route('email.addRemark') }}',
              data: {
                  id: id,
                  remark: remark
              },
          }).done(response => {
              $('#add-remark').find('textarea[name="remark"]').val('');
              var no = $("#remark-list").find("tr").length + 1;
              html = '<tr><th scope="row">' + no + '</th><td>' + remark + '</td><td>You</td><td>' + moment().format('DD-M H:mm') + '</td></tr>';
              $("#makeRemarkModal").find('#remark-list').append(html);
          }).fail(function (response) {
              alert('Could not fetch remarks');
          });
  
      });
  
      $(document).on('click', '.bin-email-btn', function(e) {
        e.preventDefault();
        var $this = $(this);
          $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/email/'+$this.data("id"),
            type: 'delete',
              beforeSend: function () {
                  $("#loading-image").show();
              },
          }).done( function(response) {
  
            // Delete current row from UI
            $('#'+$this.data("id")+"-email-row").remove()
  
            $("#loading-image").hide();
            toastr['success'](response.message);
          }).fail(function(errObj) {
            $("#loading-image").hide();
            toastr['error'](response.errors[0]);
          });
      });
  
  $(document).on('click', '.cronEmailPage', function(e) {
      var page = $(this).attr('data-id');
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/cron/gethistory/'+page,
        dataType: 'json',
        type: 'post',
          beforeSend: function () {
              $("#loading-image").show();
          },
      }).done( function(response) {
        console.log(response.data);
        // Show data in modal
        $('#getCronEmailModal tbody').html(response.data);
        $('.cronEmailPagination li').removeClass('active');
        $('.cronEmailActive'+page).addClass('active');
        $('#getCronEmailModal').modal('show');
  
        $("#loading-image").hide();
      }).fail(function(errObj) {
        $("#loading-image").hide();
      });
  });
  
  $(document).on('click', '.readmore', function() {
      $(this).parent('.lesstext').hide();
      $(this).parent('.lesstext').next('.alltext').show();
  });
  $(document).on('click', '.readless', function() {
      $(this).parent('.alltext').hide();
      $(this).parent('.alltext').prev('.lesstext').show();
  });
  
  $(document).on('change','.status',function(e){
    if($(this).val() != "" && ($('option:selected', this).attr('data-id') != "" || $('option:selected', this).attr('data-id') != undefined)){
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type : "POST",
        url : "{{ route('changeStatus') }}",
        data : {
          status_id : $('option:selected', this).val(),
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
  // To set the iframe height based on content
  if (autoIframe == undefined || (autoIframe !== undefined && typeof autoIframe !== 'function')) {
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
  
  function markEmailRead(email_id){
  
  }
  
  function load_data(type,seen){
    $('#type').val(type);
    $('#seen').val(seen);
  
    get_data();
  }
  
  function excelImporter(id) {
      $('#excel_import_email_id').val(id)
      $('#excelImporter').modal('toggle');
  }
  
  function showFilesStatus(id) {
  
      if( id ){
  $.ajax({
  headers: {
  	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  },
  data : {id},
  url: '/email/'+id+'/get-file-status',
  type: 'post',
  
  beforeSend: function () {
  		$("#loading-image").show();
  	},
  }).done( function(response) {
  	if (response.status === true) {
  		$("#filesStatus").html(response.mail_status);
  		$('#showFilesStatusModel').modal('toggle');
  	}else{
  		alert('Something went wrong')
  	}
  	
  	$("#loading-image").hide();
  }).fail(function(errObj) {
  	$("#loading-image").hide();
  	alert('Something went wrong')
  });
  }else{
  alert('Something went wrong')
  }
  
      // $('#excelImporter').modal('toggle');
  }
  
  function importExcel() {
      id = $('#excel_import_email_id').val()
      supplier = $('#supplier_excel_import option:selected').val()
      if(supplier){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data : {
              supplier,
              id
            },
            url: '/email/'+id+'/excel-import',
            type: 'post'
          }).done( function(response) {
            $('#excelImporter').modal('toggle');
            toastr['success'](response.message);
          }).fail(function(errObj) {
            $('#excelImporter').modal('toggle');
            alert('Something went wrong')
          });
      }else{
        alert('Please Select Supplier')
        
      }
  }
  
  function bulkAction(ele,type){
    let action_type = type;
    var val = [];
    $(':checkbox:checked').each(function(i){
      val[i] = $(this).val();
    });
    
    if(val.length > 0){
        $.ajax({
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type : "POST",
          url : "{{ route('bluckAction') }}",
          data : {
              action_type : action_type,
              ids : val,
              status : $('#bluck_status').val()
          },
          success : function (response){
                location.reload();
          },
          error : function (response){
  
          }
        })
        
    }
      
  }
  
  function opnModal(message){
      console.log(message);
    $(document).find('#more-content').html(message);
  }
  $(document).on('click','.make-label',function(event){
    event.preventDefault();
    $('.labeling-form input[name="id"]').val($(this).data('id'));
  })

  function Showactionbtn(id){
    $(".action-btn-tr-"+id).toggleClass('d-none')
  }
</script>
@endsection