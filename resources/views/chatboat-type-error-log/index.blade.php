@extends('layouts.app')

@section('title', 'Twillio Missing Keywrods')

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
    <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Twillio Missing Keywords
            {!! Session::has('msg') ? Session::get("msg") : '' !!}
            <div class="pull-right">
            </div>
        </h2>
      <div class="mt-3 col-md-12">
          <form action="{{route('chatbot.type.error.log')}}" method="get" class="search">
              <div class="form-group col-md-2 pd-sm">
                  <h5>Store website</h5>
                  <select class="form-control globalSelect2" multiple="true" id="storeweb_id" name="storeweb_id[]">
                      @foreach($storeWebsites as $storeWebsite)
                      <option value="{{ $storeWebsite->id}}" 
                          @if(is_array(request('storeweb_id')) && in_array($storeWebsite->id, request('storeweb_id')))
                          selected
                          @endif >{{ $storeWebsite->website }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="form-group col-lg-2">
                  <h5>Missing word</h5>
                  <input class="form-control" type="text" id="missiong_word" placeholder="Search Missing word" name="missiong_word" value="{{ (request('missiong_word') ?? "" )}}">
              </div>
              <div class="form-group col-lg-2">
                  <h5>Call SID</h5>
                  <input class="form-control" type="text" id="call_sid" placeholder="Search Call SID" name="call_sid" value="{{ (request('call_sid') ?? "" )}}">
              </div>
              <div class="form-group col-lg-2">
                  <h5>Phone Number</h5>
                  <input class="form-control" type="text" id="phone_number" placeholder="Search Phone Number" name="phone_number" value="{{ (request('phone_number') ?? "" )}}">
              </div>
              <div class="form-group col-lg-2"><br><br>
                  <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                    <img src="/images/filter.png">
                  </button>
                  <a href="{{route('chatbot.type.error.log')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
              </div>
          </form>
      </div>
  </div>
</div>

  <div class="col-lg-12 margin-tb">
    <div class="tab-content ">
      <table id="magento_list_tbl_895" class="table table-bordered table-hover">
        <thead>
          <th >ID</th>
          <th>Missing word</th>
          <th>Store Website</th>
          <th>Call sid</th>
          <th>Phone Number</th>
          
        </thead>
        <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
          
          @foreach($data as $item)
            <tr id="tr_{{$item->chatId}}">
              <td>{{ $item->chatId }}</td>
              <td>{{$item->type_error}}</td>
              <td>{{$item->website}} </td>
              <td>{{$item->call_sid}}</td>
              <td>{{$item->phone_number}}</td>
              
            </tr>
          @endforeach
          
        </tbody>
      </table>
      {!! $data->render() !!}
    </div>   
   </div>
</div>
<div class="row">
  <div class="col-md-12">
      <div class="alert alert-success" id="alert-msg" style="display: none;">
          <p></p>
      </div>
  </div>
</div>
<div class="col-md-12 margin-tb" id="page-view-result">

</div>
@endsection

@section('scripts')
<script type="text/javascript">
 
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
  });

	}

</script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery-ui.js') }} "></script>
<script type="text/javascript">
  page.init({
      bodyView : $("#common-page-layout"),
      baseUrl : "<?php echo url('/'); ?>"
  });
</script>

@endsection
