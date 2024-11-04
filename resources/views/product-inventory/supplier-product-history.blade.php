@extends('layouts.app')

@section('title', 'Supplier Inventory History')

@section('large_content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Supplier Inventory History ({{$total_rows}})</h2>
        </div>

        <div class="col-12">
          <div class="pull-left"></div>

          <div class="pull-right">
            <div class="form-group">
              &nbsp;
            </div>
          </div>
        </div>
    </div>

    @include('partials.flash_messages')



<form method="get" action="{{route('supplier.product.history')}}" class="handle-search">

     <div class="form-group">
                        <div class="row">
                        
                            
                            

                            <div class="col-md-3">
                               <select class="form-control select-multiple" id="supplier-select" tabindex="-1" aria-hidden="true" name="supplier" onchange="//showStores(this)">
                                    <option value="">Select Supplier</option>

                                    @forelse($supplier_droupdown as $supplier)
                                     <option value="{{$supplier->id}}" {{$supplier->id == request()->supplier?'selected':''}}>{{$supplier->supplier}}</option>
                                     @empty    
                                     @endforelse
                                        </select>
                            </div>

                            
                            <div class="col-md-1 d-flex justify-content-between">
                               <button type="submit" class="btn btn-image" ><img src="/images/filter.png"></button><button type="button" onclick="resetForm(this)" class="btn btn-image" id=""><img src="/images/resend2.png"></button>  
                            </div>
                          <!--   <div class="col-md-1">
                                  
                            </div> -->
                        </div>

                    </div>

</form>


    <div class="row">
        <div class="col-md-12">
          
            <table id="table" class="table table-striped table-bordered">
                <thead>
                
                      
         

                    <tr>
                       
                        <th>Supplier Name</th> 
                        <th>Last scrapped on</th>                       
                        <th>Products</th>
                        <th> Brands </th>
                        @foreach($range as $date) 
                                    <td>{{$date->format("Y-m-d")}}</td>
                                            @endforeach 
                        <th>Summary</th>
                    </tr>
                </thead>
                <tbody id="product_history">
                    @include("product-inventory.partials.supplier-product-history-data")
                </tbody>
            </table>
			<img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
        </div>
    </div>

 <div id="brand-history-model" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Brand History</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
      </div>
  </div>
</div>


@endsection

@section('scripts')

<script type="text/javascript">
 

    function resetForm(selector)
        {
            
           $(selector).closest('form').find('input,select').val('');

           $(selector).closest('form').submit();
        }


     $(document).on("click",".brand-result-page",function() {
          var $this = $(this);
          $.ajax({
              url:'/product/history/by/supplier-brand',
              data:{
                supplier_id : $this.data("supplier-id")
              },
              beforeSend: function () {
                  $("#loading-image").show();
              },
              success:function(result){
                $("#loading-image").hide();
                var brandModel = $("#brand-history-model");
                    brandModel.find(".modal-body").html(result);
                    brandModel.modal("show");
              },
              error:function(exx){
                $("#loading-image").hide();
              }
          });
     });
	 
//START - Load More functionality
	var isLoading = false;
	var page = 1;
	
	$(document).ready(function () {
		//loadMore();
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
				url: "/product/history/by/supplier?page="+page,
				type: 'GET',
				data: $('.handle-search').serialize(),
				beforeSend: function() {
					$loader.show();
				},
				success: function (data) {
					$loader.hide();					
					$('#product_history').append(data.tbody);
					isLoading = false;
					if(data.tbody == "") {
						isLoading = true;
					} else {
						if(page < 25 ){
							loadMore();
						}
					}
				},
				error: function () {
					$loader.hide();
					isLoading = false;
				}
			});
		}
	});
	//End load more functionality
</script>

@endsection



