@extends('layouts.app')

@section('title', 'Scrape Log')

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
</style>
@endsection

@section('content')
  <div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
  </div>
  <div class="row m-0">
    <div class="col-lg-12 margin-tb p-0">
      <h2 class="page-heading">Scrape Missing Log ({{ $total_count }})
      <div class="pull-right">
        <button type="button" class="btn btn-image pr-0" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
      </div>
      </h2>
  </div>

  <div class="row">
       <div class="col-lg-12 margin-tb">
          
           <div class="pull-left">
            
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
          <table id="magento_list_tbl_895" class="table table-bordered table-hover" style="table-layout: fixed">
            <thead>
            <th>Date</th>
              <th >Website</th>
              <th >Total Product</th>
              <th >Missing Category</th>
              <th >Missing Color</th>
              <th >Missing Composition</th>
              <th >Missing Name</th>
              <th >Missing Short Description</th>
              <th >Missing Price</th>
              <th >Missing Size</th>
             
            </thead>
            <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">

              @foreach($logs as $item)
          <tr>
          <td>
                    @if(isset($item->created_at))
                      {{ date('M d, Y',strtotime($item->created_at))}}
                    @endif
                  </td>        
          <td> {{$item->website}} </td>
                  <td> {{$item->total_product}} </td>
                  <td> {{$item->missing_category}} </td>
                  <td> {{$item->missing_color}} </td>
                                
                  <td> {{$item->missing_composition}} </td>
                  <td> {{$item->missing_name}} </td>
                  <td> {{$item->missing_short_description}} </td>
                  <td> {{$item->missing_price}} </td>
                  <td> {{$item->missing_size}} </td>
                 
                  
                  
                </tr>
              @endforeach()
            </tbody>
          </table>


        </div>
        
     </div>

     <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />

     
@endsection

@section('scripts')
<script type="text/javascript">
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
                  url: "{{url('productinventory/scrape-log')}}?ajax=1&page="+page,
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
