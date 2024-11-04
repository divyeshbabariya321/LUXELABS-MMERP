@extends('layouts.app')


@section('favicon' , 'productstats.png')


@section('title', 'Product Description')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<style type="text/css">
  .modal-lg{
            max-width: 1500px !important; 
  }
        </style>
@endsection
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Scraped Products</h2>
        </div>
    </div>
    
    <div class="col-md-12 ">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered" id="quick-reply-list" style="table-layout: fixed;">
                <tr>
                    <th width="2%">#</th>
                    <th width="6%">Product Sku</th>
                    <th width="5%">Count</th>
                    <th width="5%">Action</th>
                </tr>
                @foreach($products as $key=>$pro)
                    <tr>
                        <td>{{ $key + 1 + ($products->currentPage() - 1) * $products->perPage() }}</td>
                        <td class="Website-task visible-app">
                            {{$pro->sku}}
                        </td>
                        <td class="Website-task visible-app">
                            {{$pro->count}}
                        </td>
                        <td>
                            <button style="padding: 1px" data-id="{{ $pro->sku }}" type="button" class="btn btn-image d-inline get-multiple-description" title="multiple-description">
                                <i class="fa fa-tasks"></i>
                           </button>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $products->links() }}
        </div>
    </div>
    <div id="show-content-model-table" class="modal fade scrp-task-list" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                   
                </div>
            </div>
        </div>
  </div>
@endsection


@section('scripts')
<script type="text/javascript">
    $(document).on("click",".get-multiple-description",function (e){
           e.preventDefault();
           var id = $(this).data("id");
           $.ajax({
               url: '{{ route("products.multidescription.sku")}}',
               type: 'GET',
               data: {id: id},
               beforeSend: function () {
                   $("#loading-image").show();
               }
           }).done(function(response) {
            setTimeout(function () {
                $("#loading-image").hide();
            }, 1000);
               var model  = $("#show-content-model-table");
               model.find(".modal-title").html("Product Multi Description");
               model.find(".modal-body").html(response);
               model.modal("show");
           }).fail(function() {
               $("#loading-image").hide();
               alert('Please check laravel log for more information')
           });
   });
</script>
@endsection