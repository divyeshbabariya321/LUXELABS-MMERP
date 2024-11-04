@extends('layouts.app')

@section('favicon' , 'lead.png')

@section('title', 'Lead and Order Pricing')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Lead and Order Pricing</h2>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                  <div class="col-10" style="padding-left:0px;">
                        <div>
                            <form class="form-inline form-search-data" action="{{ route('lead-order.index') }}" method="GET">
                                <div class="form-group col-md-4 pd-3">
                                  <input style="width:100%;" name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}" 
                                         placeholder="Customer Name, Lead or Order Id, Product Id">
                                </div>
                                <div class="form-group col-md-3 pd-3">
                                    {{ html()->select("brand_id", ["" => "-- Select Brands --"] + $brandList, request('brand_id', []))->class("form-control select2") }}
                                </div>                                
                                <div class="form-group col-md-3 pd-3">
                                    <select class="form-control select2" name="order_or_lead" tabindex="-1" aria-hidden="true">
                                        <option value="">-- Lead Or Order --</option>
                                        <option value="lead" {{ (isset($orderOrLead) && $orderOrLead == 'lead') ? 'selected' : '' }} >Lead</option>
                                        <option value="order" {{ (isset($orderOrLead) && $orderOrLead == 'order') ? 'selected' : '' }} >Order</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-1 pd-3">
                                    <button type="submit" class="btn btn-xs"><i class="fa fa-filter"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12 p-5 pt-lg-0">
    <div class="productGrid" id="productGrid">
	<div class="table-responsive-lg">
		<table class="table table-bordered" style="margin-top: 25px; table-layout: fixed;">
		<thead>
			<tr>
				<th style="width: 5%">ID</th>
				<th style="width: 11%">Customer</th>
				<th style="width: 10%">Date</th>
				<th style="width: 9%">Prod ID</th>
				<th style="width: 17%">Prod</th>
				<th style="width: 13%">Brand</th>
				<th style="width: 5%">Def P</th>
				<th style="width: 5%">S Disc</th>
				<th style="width: 5%">Duty</th>
				<th style="width: 6%">Override</th>
				<th style="width: 6%">P Price</th>
				<th style="width: 5%">Disc</th>
				<th style="width: 6%">F Price</th>
				<th style="width: 5%">GMU</th>
                <th style="width: 5%">Actions</th>
			</tr>
			</thead>
			<tbody class="infinite-scroll-api-inner">
			  @include('lead-order.lead-order-item')
			</tbody>
		</table>
	</div>
    </div>
</div>
 </div>
 
 
 <div id="suggetsedLog" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2><p>Product Price Log</p></h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="overflow-x: scroll;">
                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <th>Order ID</th>
                        <th>Prod ID</th>
                        <th>Stage</th>
                        <th>Product Price</th>
                        <th>Product Total Price</th>
                        <th>Product Price Dis.</th>
                        <th>Log Info.</th>
                    </tr>
                    <tbody id="logtr">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="CalLog" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Lead Product Price Calculation Log</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="overflow-x: scroll;">
                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <th>Prod ID</th>
                        <th>Original Price</th>
                        <th>Promotion Percentage</th>
                        <th>Promotion</th>
                        <th>Segment Discount</th>
                        <th>Segment Discount Percentage</th>
                        <th>IVA Price</th>
                        <th>Euro Price</th>
                    </tr>
                    <tbody id="callogtr">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      $('#order-datetime').datetimepicker({
        format: 'YYYY-MM-DD'
      });
      $(".select2").select2({tags:true});
    });
      /*$(document).on('click', '.pagination a, th a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        getProducts(url);
      });*/
      $(document).on('click', '.check-lead', function() {
        var id = $(this).data('leadid');
        if ($(this).prop('checked') == true) {
          // $(this).data('attached', 1);
          attached_leads.push(id);
        } else {
          var index = attached_leads.indexOf(id);
          // $(this).data('attached', 0);
          attached_leads.splice(index, 1);
        }
        console.log(attached_leads);
      });
    </script>
<script>   
        
        $(document).on("click", ".expand-text", function (e) {
            if($(e.target).closest("span").css('height') == '24px')
                $(e.target).closest("span").css('height', 'auto');
            else
                $(e.target).closest("span").css('height', '24px');
           
        });
        $(document).on("click", ".load-log", function (event) {
            var prodId = $(this).data("prod_id");
            var custId = $(this).data("cust_id");
             $.ajax({
                    url: '{{route("lead.order.product.log")}}',
                    type:"get",
                    data: { 
                            "_token": $('meta[name="csrf-token"]').attr('content'),
                            prod_id : prodId,
                            cust_id : custId
                            },
                dataType: 'json'
            }).done(function (response) {
                if(response.code == 200) {
                    //$loader.hide();
                    $("#suggetsedLog").modal('show');
                    $('#logtr').html('');
                    $('#logtr').html(response.data);
                    toastr['success'](response.message);
                }else{
                    //$loader.hide();
                    errorMessage = response.message ? response.message : 'Log not found!';
                    $('#logtr').html("<td colspan='8'>No Log found</td>");
                    toastr['error'](errorMessage);
                }        
            }).fail(function (response) {
                //$loader.hide();
                $('#logtr').html("<td colspan='8'>No Log found</td>");
                toastr['error'](response.message);
            });
        });

        $(document).on("click", ".load-calculation", function (event) {
            var prodId = $(this).data("prod_id");
            var custId = $(this).data("cust_id");
             $.ajax({
                    url: '{{route("lead.product.cal.log")}}',
                    type:"get",
                    data: { 
                            "_token": $('meta[name="csrf-token"]').attr('content'),
                            prod_id : prodId,
                            cust_id : custId
                            },
                dataType: 'json'
            }).done(function (response) {
                if(response.code == 200) {
                    //$loader.hide();
                    $("#CalLog").modal('show');
                    $('#callogtr').html('');
                    $('#callogtr').html(response.data);
                    toastr['success'](response.message);
                }else{
                    //$loader.hide();
                    errorMessage = response.message ? response.message : 'Log not found!';
                    $('#callogtr').html("<td colspan='8'>No Log found</td>");
                    toastr['error'](errorMessage);
                }        
            }).fail(function (response) {
                //$loader.hide();
                $('#callogtr').html("<td colspan='8'>No Log found</td>");
                toastr['error'](response.message);
            });
        });


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
                    url: "{{url('lead-order')}}?page="+page,
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function (data) {
                        
                        $loader.hide();
                        if('' === data.trim())
                            return;
                        $('.infinite-scroll-api-inner').append(data);
                        

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
@endsection
@section('scripts')
  
@endsection
