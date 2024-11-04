@extends('layouts.app')

@section("styles")
<style>
/* CSS to make specific modal body scrollable */
#show_full_error_modal .modal-body {
  max-height: 400px; /* Maximum height for the scrollable area */
  overflow-y: auto; /* Enable vertical scrolling when content exceeds the height */
}
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">quick emails ({{$emails->total()}})</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<form action="{{route('quick.email.list')}}" method="get" class="search">
            <div class="col-md-2 pd-sm">
                <label for="brand">Search Sender</label>
                <div class="form-group">
                    <select class="form-control globalSelect2" multiple="true" id="sender_ids" name="sender_ids[]" placeholder="select-brands">
                        @foreach($senderEmailIds as $senderEmailId)
                        <option value="{{ $senderEmailId->from}}" 
                        @if(is_array(request('sender_ids')) && in_array($senderEmailId->from, request('sender_ids')))
                            selected
                        @endif >{{ $senderEmailId->from}}</option>
                        @endforeach
                    </select> 
                </div>
            </div>    
			<div class="col-lg-2">
                <label for="brand">Search Receiver</label>
				<div class="form-group">
                    <select class="form-control globalSelect2" multiple="true" id="receiver_ids" name="receiver_ids[]" placeholder="select-brands">
                        @foreach($receiverEmailIds as $receiverEmailId)
                        <option value="{{ $receiverEmailId->to}}" 
                        @if(is_array(request('receiver_ids')) && in_array($receiverEmailId->to, request('receiver_ids')))
                            selected
                        @endif >{{$receiverEmailId->to}}</option>
                        @endforeach
                    </select> 
                </div>
			</div>
			<div class="col-lg-2">
                <label for="brand">Search Model Type</label>
				<div class="form-group">
                    <select class="form-control globalSelect2" multiple="true" id="model_types" name="model_types[]" placeholder="select-brands">
                        @foreach($modelsTypes as $modelsType)
                        <option value="{{ $modelsType->model_type}}" 
                        @if(is_array(request('model_types')) && in_array($modelsType->model_type, request('model_types')))
                            selected
                        @endif >{{ $modelsType->model_type}}</option>
                        @endforeach
                    </select> 
                </div>
			</div>
            <div class="col-lg-2">
                <label for="brand">Search Mail Type</label>
				<div class="form-group">
                    <select class="form-control globalSelect2" multiple="true" id="mail_types" name="mail_types[]" placeholder="select-brands">
                        @foreach($mailTypes as $mailType)
                        <option value="{{ $mailType->type}}" 
                        @if(is_array(request('mail_types')) && in_array($mailType->type, request('mail_types')))
                            selected
                        @endif >{{ $mailType->type}}</option>
                        @endforeach
                    </select> 
                </div>
			</div>
            <div class="col-lg-2">
                <label for="brand">Search Category</label>
				<div class="form-group">
                    <select class="form-control globalSelect2" multiple="true" id="cat_ids" name="cat_ids[]" placeholder="select-brands">
                        @foreach($email_categories as $email_category)
                        <option value="{{ $email_category->id}}" 
                        @if(is_array(request('cat_ids')) && in_array($email_category->id, request('cat_ids')))
                            selected
                        @endif >{{ $email_category->category_name}}</option>
                        @endforeach
                    </select> 
                </div>
			</div>
            <div class="col-lg-2">
                <label for="brand">Search Status</label>
				<div class="form-group">
                    <select class="form-control globalSelect2" multiple="true" id="status" name="status[]" placeholder="select-brands">
                        @foreach($emailStatuses as $status)
                        <option value="{{ $status->status}}" 
                        @if(is_array(request('status')) && in_array($status->status, request('status')))
                            selected
                        @endif >{{ $status->status}}</option>
                        @endforeach
                    </select> 
                </div>
			</div>
			<div class="col-lg-2">
                <label for="brand">Search Date</label>
				<input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
			</div>
            <br>
			<div class="col-lg-2"><br><br>
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('quick.email.list')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
        <div class="pull-right mt-3">
            <button type="button" class="btn  custom-button" data-toggle="modal" data-target="#statusModel">Create Status</button>
        </div>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
                    <th width="2%">Read</th>
			    	<th width="7%">Date</th>
			        <th width="12%">Sender</th>
			        <th width="12%">Receiver</th>
			        <th width="8%">Model Type</th>
			        <th width="8%">Mail Type</th>
                    <th width="10%">Subject & Body</th>
                    <th width="10%">Status</th>
                    <th width="5%">Draft</th>
                    <th width="14%">Error Message</th>
                    <th width="20%">Category</th>
                </tr>
		    	<tbody>
                    @foreach ($emails as $email)
                        <tr>
                            <td>
                             @if($email->seen == 0)
                             <input type="checkbox" name="email_read" id="is_email_read" value="1" data-id="{{ $email->id }}" onclick="updateReadEmail(this)"></td>
                             @endif
                            <td>{{$email->created_at}}</td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($email->from) > 30 ? substr($email->from, 0, 10).'...' :  $email->from }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $email->from }}
                                </span>
                            </td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($email->from) > 30 ? substr($email->from, 0, 10).'...' :  $email->from }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $email->from }}
                                </span>
                            </td>
                            <td>{{$email->model_type}}</td>
							<td>{{$email->type}}</td>
                            <td data-toggle="modal" data-target="#view-quick-email" onclick="openQuickMsg({{$email}})" style="cursor: pointer;">{{ substr($email->subject, 0,  15) }} {{strlen($email->subject) > 10 ? '...' : '' }}</td>
                            <td width="1%">
                               @if($email->status) 
                               {{$email->status}}
                               @else
                               <select class="form-control selecte2 status-change">
                                <option  value="" >Please select</option>
                                @foreach($email_status as $status)
                                @if($status->id == (int)$email->status)
                                <option  value="{{ $status->id }}" data-id="{{$email->id}}"  {{ $status->id->id == $email->status ? 'selected' : '' }}>{{ $status->email_status }}</option>
                                @else
                                <option  value="{{ $status->id }}" data-id="{{$email->id}}" >{{$status->email_status }}</option>
                                @endif
                                @endforeach
                              </select>
                               @endif
                            </td>
                            <td>{{ ($email->is_draft == 1) ? "Yes" : "No" }}</td>
                            <td style="word-break: break-all">
                                @if($email->error_message)
                                <span class="td-mini-container">
                                   {{ strlen($email->error_message) > 10 ? substr($email->error_message, 0, 20).'...' :  $email->error_message }}
                                   <i class="fa fa-eye show_logs show-full-error-message" data-full-error="{{ nl2br($email->error_message) }}" style="color: #808080;float: right;"></i>
                                </span>
                                @endif
                            </td>
                            <td>
                            <select class="form-control selecte2 email-category">
                                <option  value="" >Please select</option>
                                @foreach($email_categories as $email_category)
                                    <option  value="{{ $email_category->id }}" data-id="{{$email->id}}" {{ $email_category->id == $email->email_category_id ? 'selected' : '' }}>{{$email_category->category_name }}</option>
                                @endforeach
                              </select>
                            </td>
                           
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $emails->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>
@endsection

<div class="modal" tabindex="-1" role="dialog" id="show_full_error_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Full Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="show_full_error_modal_content">
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
  
            <div class="form-group">
              <select class="form-control" id="status_type" name="type">
                  <option value="read">Read</option>
                  <option value="unread">Unread</option>
                  <option value="sent">Sent</option>
                  <option value="trash">Trash</option>
                  <option value="draft">Draft</option>
                  <option value="queue">Queue</option>
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
  
@section('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
   $(document).on('click', '.show-full-error-message', function() {
        var fullLog = $(this).data('full-error');
        $('#show_full_error_modal').modal('show');
        $('#show_full_error_modal_content').html(fullLog);
    });

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
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

  $(document).on('change','.status-change',function(e){
      if($(this).val() != "" && ($('option:selected', this).attr('data-id') != "" || $('option:selected', this).attr('data-id') != undefined)){
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type : "POST",
          url : "{{ route('changeEmailStatus') }}",
          data : {
            status_id : $('option:selected', this).val(),
            email_id : $('option:selected', this).attr('data-id')
          },
          success : function (response){
            toastr['success']("status updated");
          },
          error : function (response){
            alert(response);
          }
        })
      }
  });

</script> 
@endsection
    