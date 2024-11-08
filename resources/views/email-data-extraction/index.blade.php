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


  @media (max-width: 1280px) {
    table.table {
        width: 0px;
        margin:0 auto;
    }

    /** only for the head of the table. */
    table.table thead th {
        padding:10px;
    }

    /** only for the body of the table. */
    table.table tbody td {
        padding:10 px;
    }

    .text-nowrap{
      white-space: normal !important;
    }
  }

</style>
@endsection
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
<div class="row">
	<div class="col-md-12 p-0">
		<h2 class="page-heading">Custom Emails List</h2>
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
		<div class="pull-right mt-3">
     </div>

    <div class="pull-left mt-3" style="margin-bottom:10px;margin-right:5px;">
        <select class="form-control" name="" id="bluck_status" onchange="bulkAction(this,'status');">
            <option value="">Change Status</option>
            <?php
            foreach ($email_status as $status) { ?>
              <option value="<?php echo $status->id;?>" <?php if($status->id == Request::get('status')) {echo "selected";} ?>><?php echo $status->email_status;?></option>
            <?php } 
            ?>
          </select>
    </div>

    <div class="pull-left mt-3" style="margin-bottom:10px;margin-right:5px;">
        <button type="button" class="btn custom-button bulk-dlt" onclick="bulkAction(this,'delete');">Bulk Delete</button>
    </div>
     <div class="pull-left " style="margin-left:50px;">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item <?php echo (request('type') == 'incoming' && request('seen') == '1') ? 'active' : '' ?>" ><!-- Purpose : Add Turnary -  DEVTASK-18283 -->
              <a class="nav-link" id="read-tab" data-toggle="tab" href="#read" role="tab" aria-controls="read" aria-selected="true" onclick="load_data('incoming',1)">Read</a>
          </li>
          <li class="nav-item <?php echo ((request('type') == 'incoming' && request('seen') == '0') || empty(request('type'))) ? 'active' : '' ?>">
              <a class="nav-link" id="unread-tab" data-toggle="tab" href="#unread" role="tab" aria-controls="unread" aria-selected="false" onclick="load_data('incoming',0)">Unread</a>
          </li>
          <li class="nav-item <?php echo (request('type') == 'outgoing' && request('seen') == 'both') ? 'active' : '' ?>"><!-- Purpose : Add Turnary -  DEVTASK-18283 -->
              <a class="nav-link" id="sent-tab" data-toggle="tab" href="#sent" role="tab" aria-controls="sent" aria-selected="false" onclick="load_data('outgoing','both')">Sent</a>
          </li>
          <li class="nav-item <?php echo (request('type') == 'bin' && request('seen') == 'both') ? 'active' : '' ?>">
            <a class="nav-link" id="sent-tab" data-toggle="tab" href="#bin" role="tab" aria-controls="bin" aria-selected="false" onclick="load_data('bin','both')">Trash</a>
          </li>
          <li class="nav-item <?php echo (request('type') == 'draft' && request('seen') == 'both') ? 'active' : '' ?>">
            <a class="nav-link" id="sent-tab" data-toggle="tab" href="#bin" role="tab" aria-controls="bin" aria-selected="false" onclick="load_data('draft','both')">Draft</a>
          </li>
          <li class="nav-item <?php echo (request('type') == 'pre-send' && request('seen') == 'both') ? 'active' : '' ?>">
            <a class="nav-link" id="sent-tab" data-toggle="tab" href="#bin" role="tab" aria-controls="bin" aria-selected="false" onclick="load_data('pre-send','both')">Queue</a>
          </li>
      </ul>
    </div>
	</div>   
  <div class="col-md-12"> <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="read" role="tabpanel" aria-labelledby="read-tab">
          </div>
          <div class="tab-pane fade" id="unread" role="tabpanel" aria-labelledby="unread-tab">

          </div>
          <div class="tab-pane fade" id="sent" role="tabpanel" aria-labelledby="sent-tab">
          </div>
      </div>
  </div>
</div>
<form>
  <div class="row">
    <div class="col-md-12 mb-3 mt-4">
        <div class="row mt-2">
            <div class="col-md-3">
              <input id="term" name="term" type="text" class="form-control" value="<?php if(Request::get('term')) {echo Request::get('term');} ?>" placeholder="Search by Keyword">
            </div>
            <div class="col-md-3">
              <select class="form-control" name="sender" id="sender">
                  <option value="">Select Sender</option>
                  @foreach($sender_drpdwn as $sender)
                      <option value="{{ $sender['from'] }}" {{ (Request::get('sender') && strcmp(Request::get('sender'),$sender['from']) == 0) ? "selected" : ""}}>{{ $sender['from'] }}</option>
                  @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-control" name="receiver" id="receiver">
                  <option value="">Select Receiver</option>
                  @foreach($receiver_drpdwn as $sender)
                      <!-- Purpose : Add If condition -  DEVTASK-18283 -->
                      @if($receiver != '' && $from == 'order_data')
                      <option value="{{ $sender['to'] }}" {{ ($sender['to'] == $receiver) ? "selected" : ""}}>{{ $sender['to'] }}</option>
                      @else
                      <option value="{{ $sender['to'] }}" {{ (Request::get('to') && strcmp(Request::get('receiver'),$sender['to']) == 0) ? "selected" : ""}}>{{ $sender['to'] }}</option>
                      @endif
                  @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-control" name="mail_box" id="mail_box">
                <option value="">Select Mailbox</option>
                @foreach($mailboxdropdown as $sender)
                    <option value="{{ $sender }}" {{ (Request::get('mail_box') == $sender) ? "selected" : ""}}>{{ $sender }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div style="clear:both"></div>
          <div class="row mt-3">
            <div class="col-md-3">
              <select class="form-control" name="status" id="email_status">
                <option value="">Select Status</option>
                <?php
                foreach ($email_status as $status) { ?>
                  <option value="<?php echo $status->id;?>" <?php if($status->id == Request::get('status')) {echo "selected";} ?>><?php echo $status->email_status;?></option>
                <?php } 
                ?>
            </select>
            </div>
		        <div class="col-md-3">
            <select class="form-control" name="category" id="category">
              <option value="">Select Category</option>
              <?php
              foreach ($email_categories as $category) { ?>
                <option value="<?php echo $category->id;?>" <?php if($category->id == Request::get('category')) {echo "selected";} ?>><?php echo $category->category_name;?></option>
              <?php } 
              ?>
            </select>
            </div>
            <div class="col-md-3">
            <select class="form-control" name="email_type" id="email_type">
              <option value="">Select Email Type</option>
              <option value="referr-coupon">Referal Coupon</option>
              <option value="coupons">Coupon</option>
            </select>
            </div>
            <div class="col-md-3">
              <input type='hidden' class="form-control" id="type" name="type" value="" />
              <input type='hidden' class="form-control" id="seen" name="seen" value="1" />
              <button type="submit" class="btn btn-image ml-3 search-btn"><i class="fa fa-filter" aria-hidden="true"></i></button>
            </div>
        </div>       
      </div>
    </div>
  </form>
<div class="table-responsive mt-3" style="margin-top:20px;">
      <table class="table table-bordered text-nowrap" style="border: 1px solid #ddd;" id="email-table">
        <thead>
          <tr>
            <th>Bulk <br> Action</th>
            <th>Date</th>
            <th>Sender</th>
            <th>Receiver</th>
            <th>Mail <br> Type</th>
            <th>Subject</th>
            <th>Body</th>
            <th>Status</th>
            <th>Draft</th>
            <th>Error <br> Message</th>
            <th>Category</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @include('email-data-extraction.search')
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


<div id="UpdateMail" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email List</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form action="{{ url('email-data-extraction/update_email') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<input type="hidden" name="email_id" id = "email_id">
						<select class="form-control" name="status" id="email_status">
                            <option value="">Select Status</option>
                            <?php
                            foreach ($email_status as $status) { ?>
                                <option value="<?php echo $status->id;?>"><?php echo $status->email_status;?></option>
                            <?php } 
                            ?>
                        </select>
					</div>
					<div class="form-group">
						<select class="form-control" name="category" id="email_category">
                            <option value="">Select Category</option>
                            <?php
                            foreach ($email_categories as $category) { ?>
                                <option value="<?php echo $category->id;?>"><?php echo $category->category_name;?></option>
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
            $("#email-table tbody").append(response.tbody);
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
				$.get(window.location.origin+"/email-data-extraction/events/"+originId, function(data, status){
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
				$.get(window.location.origin+"/email-data-extraction/emaillog/"+email_id, function(data, status){
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
      var date = $("#date").val();
      var type = $("#type").val();
      var seen = $("#seen").val();
      var sender = $("#sender").val();
      var receiver = $("#receiver").val();
      var status = $("#email_status").val();
      var category = $("#category").val();
      var mail_box = $("#mail_box").val();
      var email_type = $("#email_type").val();
        $.ajax({
          url: 'email-data-extraction',
          type: 'get',
          data:{
              term:term,
              date:date,
              type:type,
              seen:seen,
              sender:sender,
              receiver:receiver,
              status:status,
              category:category,
              mail_box : mail_box,
              email_type : email_type
            },
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          $("#loading-image").hide();
            $("#email-table tbody").empty().html(response.tbody);
            $(".pagination-custom").html(response.links);

        }).fail(function(errObj) {
          $("#loading-image").hide();
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
          url: '/email-data-extraction/resendMail/'+$this.data("id"),
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
          url: '/email-data-extraction/replyMail/'+$this.data("id"),
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

    $(document).on('click', '.forward-email-btn', function(e) {
      e.preventDefault();
      var $this = $(this);
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email-data-extraction/forwardMail/'+$this.data("id"),
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
      var message = $("#reply-message").val();
      var reply_email_id = $("#reply_email_id").val();
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email-data-extraction/replyMail',
          type: 'post',
          data: {
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

    $(document).on('click', '.submit-forward', function(e) {
      e.preventDefault();
      email = $("#forward-email").val();
      forward_email_id = $("#forward_email_id").val();
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email-data-extraction/forwardMail',
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

            $('#add-remark input[name="id"]').val(email_id);
           

            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('email-data-extraction.getremark') }}',
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
                url: '{{ route('email-data-extraction.addRemark') }}',
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
              url: '/email-data-extraction/'+$this.data("id"),
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
                  url : "{{ route('email-data-extraction.changeStatus') }}",
                  data : {
                    status_id : $('option:selected', this).val(),
                    email_id : $('option:selected', this).attr('data-id')
                  },
                  success : function (response){
                        location.reload();
                  },
                  error : function (response){

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
                  url : "{{ route('email-data-extraction.changeEmailCategory') }}",
                  data : {
                    category_id : $('option:selected', this).val(),
                    email_id : $('option:selected', this).attr('data-id')
                  },
                  success : function (response){
                       location.reload();
                  },
                  error : function (response){

                  }
            })
        }
    });

    function opnMsg(email) {
      $('#emailSubject').html(email.subject);
      $('#emailMsg').html(email.message);

      // Mark email as seen as soon as its opened
      if(email.seen ==0 || email.seen=='0'){
        // Mark email as read
        var $this = $(this);
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: '/email-data-extraction/'+email.id+'/mark-as-read',
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
				url: '/email-data-extraction/'+id+'/get-file-status',
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
              url: '/email-data-extraction/'+id+'/excel-import',
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
            url : "{{ route('email-data-extraction.bluckAction') }}",
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
      $(document).find('#more-content').html(message);
    }
    $(document).on('click','.make-label',function(event){
      event.preventDefault();
      $('.labeling-form input[name="id"]').val($(this).data('id'));
    })
    </script>


@endsection

