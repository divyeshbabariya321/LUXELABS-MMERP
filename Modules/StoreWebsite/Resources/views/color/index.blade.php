@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)
@section('content')
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}
</style>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
     @if(session()->has('success'))
	    <div class="col-lg-12 margin-tb">
		    <div class="alert alert-success">
		        {{ session()->get('success') }}
		    </div>
		</div>    
	@endif
	<div class="mt-3 col-md-12">
		<form action="{{route('store-website.color.list')}}" method="get" class="search">
			<div class="col-md-2 pd-sm">
				<strong>Search Websites:</strong>
				{{ html()->multiselect("website_ids[]", \App\StoreWebsite::pluck('title', 'id')->toArray(), request('website_ids'))->class("form-control globalSelect2")->data('placeholder', "Select Website") }}
			</div>
			<div class="col-md-2 pd-sm">
				<strong>Search Store Colours:</strong>
				<select class="form-control globalSelect2" multiple="true" id="erp_colour" name="erp_colour[]" placeholder="Select Platform">
					<option value="">Select Erp color</option>
					@foreach($erp_colors as $erp_color)
					<option value="{{ $erp_color }}" @if(is_array($reqStorecolour) && in_array($erp_color, $reqStorecolour)) selected @endif>{{ $erp_color }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-2 pd-sm">
				<strong>Store Color:</strong>
				<input class="form-control" type="text" id="Store_Color	" placeholder="Search Store Color" name="Store_Color" value="{{ $reqsstorecolor ?? '' }}">
			</div>
			<div class="col-md-2 pd-sm">				
				<label for="keyword">Keyword:</label>
				{{ html()->text('keyword', request('keyword'))->class('form-control')->placeholder('Enter keyword') }}			  </div>
			<div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
				<br><button type="submit" style="" class="btn btn-image pl-0"><img src="/images/filter.png"></button>
				<a href="{{route('store-website.color.list')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
    <div class="col-lg-12 margin-tb">
    	<div class="row">
	    	<div class="col col-md-9">
		    	<div class="row">
	    			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action" data-toggle="modal" data-target="#colorCreateModal">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button>
		  			<form class="form-inline message-search-handler" action="?" method="get">
		  				<input type="hidden" name="push" value="1">
				  		<div class="form-group">
						    <label for="keyword">Store Wesbite:</label>
							{{ html()->select('store_website_id', \App\StoreWebsite::pluck('title', 'id')->toArray())->class('form-control select2')->placeholder('Select Website')->value(request('store_website_id')) }}
					  	</div>
					  	&nbsp;
						<div class="form-group">
					  		<label for="button">&nbsp;</label>
					  		<button type="submit" class="btn btn-secondary">
					  			Push Color
					  		</button>
					  	</div>		
			  		</form>
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
			<div class="row">
				<table class="table table-bordered">
				    <thead>
				      <tr>
				      	<th>Id</th>
						<th>ERP Color</th>
						<th>Store Website</th>
						<th>Store Color</th>
						<th>Actions</th>

				      </tr>
				    </thead>
				    <tbody>
				    	<?php foreach ($store_colors as $color) { ?>
 					      <tr>
					      	<td><?php echo $color->id; ?></td>
					      	<td><?php echo $color->erp_color; ?></td>
					      	<td><?php echo ($color->storeWebsite) ? $color->storeWebsite->title : 'N/A'; ?></td>
					      	<td><?php echo $color->store_color; ?></td>
					      	<td>
									<button type="button" class="btn btn-image edit-color d-inline" data-toggle="modal" data-target="#colorEditModal" data-color="{{ json_encode($color) }}"><img src="/images/edit.png" /></button>
									
									<button type="button" class="btn btn-image push-to-store d-inline" data-id="<?php echo $color->id; ?>" data-color="{{ json_encode($color) }}">
										<img src="/images/icons-refresh.png" />
									</button>

									{{ html()->form('DELETE', route('store-website.color.destroy', [$color->id]))->style('display:inline')->open() }}
									<button type="submit" class="btn btn-image d-inline"><img src="/images/delete.png" /></button>
									{{ html()->form()->close() }}
								</td>
					      </tr>
					    <?php } ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
          50% 50% no-repeat;display:none;">
</div>

@include('storewebsite::color.partials.modals')


<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>

<script>

$(document).on('click', '.edit-color', function() {
      var color = $(this).data('color');
      var url = "{{ url('store-website/color') }}/" + color.id;

      $('#colorEditModal form').attr('action', url);
      $('#store_website_id').val(color.store_website_id);
      $('#erp_color').val(color.erp_color);
      $('#store_color').val(color.store_color);
    });

$(document).on("click",".push-to-store",function() {
	var id = $(this).data("id");
	$.ajax({
		url: '/store-website/color/push-to-store',
		type: 'POST',
		dataType: 'json',
		data: {
			_token: "{{ csrf_token() }}",
			id: id,
		},
	}).done(function (response) {
		if(response.code == 200) {
			toastr['success']('Color Pushed successfully', 'success');
		}else{
			toastr['error']('Something went wrong', 'error');
		}
	}).fail(function () {
	});
});
</script>

@endsection