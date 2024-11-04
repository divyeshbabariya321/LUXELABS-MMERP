@extends('layouts.app')

@section('title', 'Chat Bot Message Log')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <style type="text/css">

  #loading-image {
    position: fixed;
    top: 50%;
    left: 50%;
    margin: -50px 0px 0px -50px;
  }
  input {
    width: 100px;
  }
  .btn-primary, .btn-primary:hover{
    background: #fff;
    color:#757575;
    border: 1px solid #ddd;
  }
  .select2-container{
    width: 100% !important;
  }
  .select2-container--default .select2-selection--multiple{
    border: 1px solid #ddd !important;
  }
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 5px;
  }
 label{
   margin-top:5px;
   margin-bottom:4px;
   margin-left:3px;
 }
.export_btn {
	margin: 27px 0 0 0;
}
@media(max-width:767px){
	div#Export_popup .col-xs-12{padding:0;}
	.export_btn{margin:10px 0 0 0;}
}
    .ac-btns button{
      height: 20px;
        width: auto;
    }
	.border-red {
		border: 1px solid red;
	}
</style>
@endsection

@section('content')
  <div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
  </div>
  <div class="row m-0">
    <div class="col-lg-12 margin-tb p-0">
      <h2 class="page-heading">Chat Bot Message Log ({{ $total_count }})
      {!! Session::has('msg') ? Session::get("msg") : '' !!}
      <div class="pull-right">
        <button type="button" class="btn btn-image pr-0" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
      </div>
      </h2>
  </div>

  <div class="row">
       <div class="col-lg-12 margin-tb mb-3">
          
           <div class="pull-left">
             <form class="form-inline" action="{{url('chatbot-message-log')}}" method="GET">
               <div class="col">
                 <div class="form-group">
                   <div class='input-group'>
                     <input type='text' placeholder="Search name" class="form-control" name="name"  value="{{ isset($_GET['name'])?$_GET['name']:''}}" />
                   
 
                   
                   </div>
                 </div>
               </div>
               <div class="col">
                 <div class="form-group">
                   <div class='input-group'>
                    
                     <input type='text' placeholder="Search Email" class="form-control" name="email"  value="{{ isset($_GET['email'])?$_GET['email']:''}}"  />
                    
 
                   
                   </div>
                 </div>
               </div>
               <div class="col">
                 <div class="form-group">
                   <div class='input-group'>
                    
                    
                     <input type='text' placeholder="Search phone" class="form-control" name="phone"  value="{{ isset($_GET['phone'])?$_GET['phone']:''}}"  />
 
                   
                   </div>
                 </div>
               </div>
 
              
              
 
               <div class="col">
                 <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
               </div>
             </form>
           </div>
           <div class="pull-right">
          
           </div>
       </div>
   </div>

  </div>

  <div class="col-md-12 pl-3 pr-3">
    <div class="mb-3">

     <div class="row m-0">
        <div class="table-responsive">
          <table id="magento_list_tbl_895" class="table table-bordered table-hover">
            <thead>
              <th >ID</th>
              <th >Customer Name</th>
              <th style="width:6%">Message</th>
              <th style="width:8%">Type</th>
              <th >Intent / Entity / ERP Entity</th>
              <th >Suggestion</th>
              <th style="width:7%">Category</th>
              <th >Status</th>
              <th>Push to</th>
              <th>Account</th>
              <th >Action</th>
            </thead>
            
            <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">

              @foreach($logListMagentos as $item)
           <tr id="tr_{{$item->id}}">
		  {{ html()->form('POST', url('pushwaston'))->id('form_' . $item->id)->open() }}
                 <td>
                    <a class="show-product-information" data-id="{{ $item->id }}" href="/products/{{ $item->id }}" target="__blank" style="color:#333">{{ $item->id }}</a>
                  </td>
                  <td> {{$item->cname}} </td>
                  
                  
                 
                  <td class="expand-row-msg" data-name="message" data-id="{{$item->id}}">
                    @php
                         $message=$item->message;
                         $msg=json_decode($message);

                         if ($msg)
                         {
                           if ($msg->message)
                             $message=$msg->message;
                             
                         }
                    @endphp
                     <input type="hidden" name="question[]" value="{{$message}}" >
                     <input type="hidden" name="log_id" value="{{$item->id}}" >
                    <span class="show-short-message-{{$item->id}}">{{ Str::limit($message, 36, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-message-{{$item->id}} hidden">{{$message}}</span>
                  </td>
                  <td> <select name="keyword_or_question" id="" class="form-control view_details_div">
                <option value="intent">Intent</option>
                <option value="entity">Entity</option>
                <option value="simple">Simple Text</option>
                <option value="priority-customer">Priority Customer</option>
            </select> </td>
            <td> 
            <input type="text" name="value"  placeholder="Enter your value" required style="width:unset;" class="form-control">
           </td>
           <td> 
            <input type="text" name="suggested_reply"  placeholder="Suggested Reply" required style="width:unset;" class="form-control">
           </td>
           
                  <td>  <select name="category_id" id="" class="form-control">
                <option value="">Select</option>
                @foreach($allCategoryList as $cat)
                    <option value="{{$cat['id']}}">{{$cat['text']}}</option>
                @endforeach
            </select> </td>
                 
                  
                  <td> {{$item->status}} </td>
                  <td> <select name="erp_or_watson" id="" class="form-control">
                <option value="watson">Watson</option>
                <option value="erp">ERP</option>
            </select> </td>
            
                  <td> <select name="watson_account" class="form-control" required>
                <option value="0">All account </option>
                @if(!empty($watson_accounts))
                    @foreach($watson_accounts as $acc)
                        <option value="{{$acc->id}}" > {{$acc->id}} - {{$acc->storeWebsite->title}}</option>
                    @endforeach
                @endif
            </select></td>
                  
                  <td class="align-middle">
                    <button class="btn btn-xs btn-none-border chatbot-log-list" data-id="{{$item->id}}"><i class="fa fa-eye"></i></button>
                     <button class="btn btn-xs btn-none-border" type="button" onclick="submitForm('{{$item->id}}')"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>  
                  </td>
                  </form>       
                </tr>
              @endforeach()
            </tbody>
          </table>


        </div>
        
     </div>

     <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />

     <div id="show-log-list-chat-error" class="modal fade" role="dialog" style="margin: 150px;">
      <div class="modal-dialog " style="margin: 0px;">
        <div class="modal-content" style="width: 1000px">
          <div class="modal-header">
            <h4 class="modal-title">Log message</h4>
          </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover" style="table-layout:fixed;">
                  <thead>
                    <th>ID</th>
                    <th>Request</th>
                    <th>Response</th>
                    <th>Status</th>
                    <th>Created at</th>
                  </thead>
                  <tbody class="show-log-list-chat-error-data">

                  </tbody>
                </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
      </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
  $(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  });

  $(document).on("click",".chatbot-log-list",function(e) {
      var id = $(this).data("id");
      $.ajax({
        url: '{{url('chatbot-message-log')}}/' + id + '/history',
        type: 'GET',
        beforeSend: function() {
          $("#loading-image").show();
        }
      }).done(function(response) {
        $("#loading-image").hide();
        $(".show-log-list-chat-error-data").html(response);
        $("#show-log-list-chat-error").modal("show");
      }).fail(function(jqXHR, ajaxOptions, thrownError) {
        toastr["error"]("Oops,something went wrong");
        $("#loading-image").hide();
      });
  });
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
	function submitForm(form_id) {
		var formId = 'form_'+form_id;
        $('#'+formId).find(".error").remove();
        $('#'+formId).find(".border-red").removeClass('border-red');
		let myForm =  document.getElementById(formId);
        var form = $('#tr_'+form_id);
        $.ajax({
            type: 'POST',
            url: "{{url('pushwaston')}}",
            data: new FormData(myForm),
			dataType:'json',
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.success)
                {
                    window.location.href = data.extra.redirect;
                } else {
                   
                    $.each(data.error, function(i, v) {
                        var error = '<div class="error">' + v + '</div>';
                        var split = i.split('.'); 
                        if (split[2]) {
                            var ind = split[0] + '[' + split[1] + ']' + '[' + split[2] + ']';
                            form.find("[name='" + ind + "']").addClass('border-red');
                            form.find("[name='" + ind + "']").parent().append(error);
                        } else if (split[1]) {
                            var ind = split[0] + '[' + split[1] + ']';
                            form.find("[name='" + ind + "']").addClass('border-red');
                            form.find("[name='" + ind + "']").parent().append(error);
                        } else {console.log(split);console.log(i);
                            form.find("[name='" + i + "']").addClass('border-red');
                            form.find("[name='" + i + "']").parent().append(error);
                        }
                    });
                }
            },
            error: function(data) {
                console.log('An error occurred.');
            }
        });
	}

</script>

<script>
      
      var isLoading = false;
      var page = 1;
      $(document).ready(function () {
         
          $(window).scroll(function() {
              if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
                  loadMore();
              }
          });

          function loadMore() {
              if (isLoading)
                  return;
              isLoading = true;
              var $loader = $('.infinite-scroll-products-loader');
              page = page + 1;
              $.ajax({
                  url: "{{url('chatbot-message-log')}}?ajax=1&page="+page,
                  type: 'GET',
                  data: $('.form-search-data').serialize(),
                  beforeSend: function() {
                      $loader.show();
                  },
                  success: function (data) {
                     
                      $loader.hide();
                      if('' === data.trim())
                          return;
                      $('.infinite-scroll-cashflow-inner').append(data);
                     

                      isLoading = false;
                  },
                  error: function () {
                      $loader.hide();
                      isLoading = false;
                  }
              });
          }           
      });

    

</script>  
@if (Session::has('errors'))
  <script>
  toastr["error"]("{{ $errors->first() }}", "Message")
</script>
@endif
@if (Session::has('success'))
  <script>
  toastr["success"]("{{Session::get('success')}}", "Message")
</script>
@endif

@endsection
