@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Vouchers Coupon Code ({{$vouCode->total()}})</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<form action="{{route('list.voucher.coupon.code')}}" method="get" class="search">
			<div class="col-1">
				<b>Search</b> 
			</div>			
			<div class="col-lg-2">
				<input class="form-control" type="text" id="coupon_code" placeholder="Search coupon code" name="coupon_code" value="{{ $search_coupon_code ?? '' }}">
			</div>
			<div class="col-lg-2">
				{{ html()->multiselect("coupon_types_ids[]", \App\CouponType::pluck('name', 'id')->toArray(), request('coupon_types_ids'))->class("form-control globalSelect2")->data('placeholder', "Select coupon Types") }}
			</div>
            <div class="col-md-2 pd-sm">
				{{ html()->multiselect("username_ids[]", \App\User::pluck('name', 'id')->toArray(), request('username_ids'))->class("form-control globalSelect2")->data('placeholder', "Select Users") }}
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="date" name="date">
			</div>

			<div class="col-lg-2">
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                   <a href="{{route('list.voucher.coupon.code')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">S.No</th>
			    	<th width="20%">Coupon code</th>
			        <th width="20%">Platform Name</th>
			        <th width="10%">Coupon Type</th>
			        <th width="20%">Added By</th>
			        <th width="10%">Valid Date</th>
                    <th width="10%">Remarks</th>
                    <th width="10%">Action</th>
                </tr>
		    	<tbody>
                    @foreach ($vouCode as $key =>$data)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$data->coupon_code}}</td>
                            <td>{{!empty($data->voucherCoupon) && !empty($data->voucherCoupon->platform) ? $data->voucherCoupon->platform->name : ''}}</td>
                            <td>{{$data->coupon_type ? $data->coupon_type->name : ''}}</td>
                            <td>{{$data->user->name}}</td>
                            <td>{{$data->valid_date}}</td>
                            <td>{{$data->remark}}</td>
                            @if(Auth::user()->isAdmin())
                             <td><a class="code-delete" data-type="code" data-id={{$data->id}}><i class="fa fa-trash" aria-hidden="true"></i></a></td>
						    @endif
                        </tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $vouCode->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

@endsection

@section('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
 $(document).on("click",".code-delete",function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var $this = $(this);
        if(confirm("Are you sure you want to delete records ?")) {
          $.ajax({
            url:'{{route("voucher.code.delete")}}',
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
              dataType:"json",
            data: { id : id},
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (data) {
            $("#loading-image").hide();
            toastr["success"]("Record deleted successfully");
            $this.closest("tr").remove();
          }).fail(function (jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
          });
        }
      });
</script> 
@endsection
    