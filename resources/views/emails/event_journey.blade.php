@extends('layouts.app')

@section('title', 'Email Journey')

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
@section('large_content')
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
<div class="row">
	<div class="col-md-12 p-0">
		<h2 class="page-heading">Sendgrid Emails Event Journey</h2>
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
        <input id="sender-email" name="sender_email" type="text" class="form-control"
               value="<?php if(Request::get('sender_email')) {echo Request::get('sender_email');} ?>"
               placeholder="Sender Email">
      </div>
        <div class="form-group px-2">
          <input id="email" name="email" type="text" class="form-control"
                 value="<?php if(Request::get('email')) {echo Request::get('email');} ?>"
                 placeholder="Receiver Email">
        </div>
        
		
		<div class="form-group px-2">
            <select class="form-control" name="event" id="event" style="width: 208px !important;">
                <option value="">Select Event</option>
                <option value="processed">Processed</option>
                <option value="dropped">Dropped</option>
                <option value="deferred">Deferred</option>
                <option value="bounced">Bounced</option>
                <option value="delivered">Delivered</option>
                <option value="open">Opened</option>
                <option value="clicked">Clicked</option>
                <option value="unsubscribed">Unsubscribed</option>
                <option value="spam reports">Spam Reports</option>
                <option value="group unsubscribed">Group Unsubscribed</option>
                <option value="group resubscribes">Group Resubscribes</option>
            </select>
        </div>
		    <input type='hidden' class="form-control" id="type" name="type" value="" />
        <input type='hidden' class="form-control" id="seen" name="seen" value="1" />
        <a href="{{route('email.event.journey')}}" class="btn btn-image ml-3 search-btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
        <button type="submit" class="btn btn-image ml-3 search-btn"><i class="fa fa-filter" aria-hidden="true"></i></button>
      </form>
      <button class="btn btn-secondary my-3" data-toggle="modal" data-target="#eventColor"> Event Color</button>
  </div>
</div>
</div>
<div class="table-responsive mt-3" style="margin-top:20px;">
      <table class="table table-bordered text-nowrap" style="border: 1px solid #ddd;" id="email-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Sender</th>
            <th>Receiver</th>
            <th>Processed</th>
            <th>Dropped</th>
            <th>Deferred</th>
            <th>Bounced</th>
            <th>Delivered</th>
            <th>Opened</th>
            <th>Clicked</th>
            <th>Unsubscribed</th>
            <th>Spam Reports</th>
            <th>Group Unsubscribed</th>
            <th>Group Resubscribes</th>
            
          </tr>
        </thead>
        <tbody>
          @foreach ($events as $key => $email)
          
            <tr style="background-color: {{$email->event_color}}!important;">
              <td>{{ $email->id }}</td>
              <td>{{ $email->sender?->from }}</td>
              <td>{{ $email->email }}</td>
              
                @if ($email->event == 'processed')
                <td>{{ Carbon\Carbon::parse($email->timestamp)->format('d-m-Y H:s:i') }}</td>
                @else
                  <td></td>
                @endif
                @if ($email->event == 'dropped')
                <td>{{ Carbon\Carbon::parse($email->timestamp)->format('d-m-Y H:s:i') }}</td>
                @else
                  <td></td>
                @endif
                @if ($email->event == 'deferred')
                <td>{{ Carbon\Carbon::parse($email->timestamp)->format('d-m-Y H:s:i') }}</td>
                @else
                  <td></td>
                @endif
                @if ($email->event == 'bounced')
                <td>{{ Carbon\Carbon::parse($email->timestamp)->format('d-m-Y H:s:i') }}</td>
                @else
                  <td></td>
                @endif
                @if ($email->event == 'delivered')
                <td>{{ Carbon\Carbon::parse($email->timestamp)->format('d-m-Y H:s:i') }}</td>
                @else
                  <td></td>
                @endif
                @if ($email->event == 'opened' || $email->event == 'open')
                <td>{{ Carbon\Carbon::parse($email->timestamp)->format('d-m-Y H:s:i') }}</td>
                @else
                  <td></td>
                @endif
                @if ($email->event == 'clicked')
                <td>{{ Carbon\Carbon::parse($email->timestamp)->format('d-m-Y H:s:i') }}</td>
                @else
                  <td></td>
                @endif
                @if ($email->event == 'unsubscribed')
                <td>{{ Carbon\Carbon::parse($email->timestamp)->format('d-m-Y H:s:i') }}</td>
                @else
                  <td></td>
                @endif
                @if ($email->event == 'spam reports')
                <td>{{ Carbon\Carbon::parse($email->timestamp)->format('d-m-Y H:s:i') }}</td>
                @else
                  <td></td>
                @endif
                
                @if ($email->event == 'group unsubscribed')
                <td>{{ Carbon\Carbon::parse($email->timestamp)->format('d-m-Y H:s:i') }}</td>
                @else
                  <td></td>
                @endif
              
                @if ($email->event == 'group resubscribes')
                <td>{{ Carbon\Carbon::parse($email->timestamp)->format('d-m-Y H:s:i') }}</td>
                @else
                  <td></td>
                @endif
              
            </tr>
          @endforeach 
        </tbody>
      </table>
      <div class="pagination-custom">
        {{$events->links()}}
      </div> 
</div>
@include("emails.modal-event-color")
@endsection
@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
    /*$(window).scroll(function() {
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
      }*/
      
		    </script>


@endsection

