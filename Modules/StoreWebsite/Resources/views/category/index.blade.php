@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}
	.dropdown.bootstrap-select.show-tick.form-control {
		min-width: 200px;
	}
</style>

@php 
$columns_array = [
	['id' => 'id', 'name' => 'ID'],
	['id' => 'category_id', 'name' => 'Category'],
];
@endphp 
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
    	<div class="row">
	    	<div class="">
		    	<div class="row">
	    			{{-- <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button> --}}
				 </div> 		
		    </div>
		    <div class="col">
		    	<div class="h" style="margin-bottom:10px;">
					<div class="row pl-4">
		    			<form class="form-inline message-search-handler" method="get">
					  		<div class="col">
					  			<div class="form-group">
								   <select name="category_id[]" class="form-control selectpicker" placholder="Categories:" multiple data-live-search="true" data-none-selected-text>
								   	@foreach($allCategories as $key => $value)
								   		<option value="{{ $value->id }}" <?php echo (isset($_GET['category_id'])&& in_array($value->id,$_GET['category_id'])) ?'selected' : ""; ?>  >{{ $value->title }}</option>
								   	@endforeach
								   </select> 	
							  	</div>
							  	<div class="form-group">
								   <select name="website_id[]" class="form-control selectpicker2" placholder="Websites:" multiple data-live-search="true" data-none-selected-text>
								   	@foreach($allStoreWebsite as $k => $value)
								   		<option value="{{ $value->id }}"  <?php echo (isset($_GET['website_id'])&& in_array($value->id,$_GET['website_id'])) ?'selected' : ""; ?>>{{ $value->website }}</option>
								   	@endforeach
								   </select> 
							  	</div>
					  			<div class="form-group">
                                  {{ html()->text('keyword', request('keyword'))->class('form-control')->placeholder('Enter keyword') }}
							  	</div>
							  	<div class="form-group">
							  		<label for="button">&nbsp;</label>
							  		<button type="submit" style="margin-top: -23px;" class="btn btn-sm btn-image btn-search-action">
							  			<img src="{{asset('/images/search.png')}}" style="cursor: default;">
							  		</button>
									<button type="button" style="margin-top: -23px;" id="btnReset" class="btn btn-secondary ml-3">
										Clear
							  		</button>
							  	</div>		
					  		</div>
				  		</form>
					</div>
		    	</div>
		    </div>			
		</div>	
		<div class="row">
			<div class="col-md-12 ml-4">
			  <ul class="nav nav-tabs">
				  <li><button class="btn btn-xs btn-secondary my-3" style="color:white;" data-toggle="modal" data-target="#storeWebsiteCategoryColumnvisibilityList"> Column Visiblity</button></li>
			  </ul>
			</div>
		  </div>
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-success" id="alert-msg" style="display: none;">
					<p></p>
				</div>
			</div>
		</div>
		<div class="col-md-12 margin-tb pl-5 pr-5" id="page-view-result">
			<div class="row" style="width:100%; overflow: auto;">
				<table class="table table-bordered">
				    <thead>
				      <tr>
						
				      	<th class=" {{ (!empty($dynamicColumnsToShowb) && in_array('ID', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">Id</th>
				        <th class=" {{ (!empty($dynamicColumnsToShowb) && in_array('Category', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">Category</th>
				        <?php foreach($storeWebsite as $sw) { 
							$columns_array[] = ['id' => $sw->title, 'name' => $sw->title];
						?>
				        	<th class="Website-task {{ (!empty($dynamicColumnsToShowb) && in_array($sw->title, $dynamicColumnsToShowb)) ? 'd-none' : ''}}" title="<?php echo $sw->title; ?>"><?php echo $sw->title; ?></th>
				        <?php } ?>	
				      </tr>
				    </thead>
				    <tbody>
				    	<?php foreach($categories as $category) { ?>
 					      <tr>
					      	<td class="{{ (!empty($dynamicColumnsToShowb) && in_array('ID', $dynamicColumnsToShowb)) ? 'd-none' : ''}}"><?php echo $category->id; ?></td>
					      	<td class="Website-task {{ (!empty($dynamicColumnsToShowb) && in_array('Category', $dynamicColumnsToShowb)) ? 'd-none' : ''}}"><?php echo $category->title; ?>
								<span href="javascript:void(0);" class="checkinglog" data-id="{{ $category->id }}" >
									<i class="fa fa-history"></i></span>
							</td>
					      	<?php foreach($storeWebsite as $sw) { 
								$checked = "";
								$catName = "";
								$remote_id = "";
							?>

							  @if(isset($resultSw[$sw->id]) && isset($resultSw[$sw->id]['category'][$category->id]))
								  @if($resultSw[$sw->id]['sw_id'] == $sw->id && $resultSw[$sw->id]['category'][$category->id]['id'] == $category->id)
									  @php
										$checked = "checked";
										$catName = $category->title;
										$remote_id = $resultSw[$sw->id]['category'][$category->id]['remote_id']
									  @endphp
								  @endif
							  @endif
							<td class="{{ (!empty($dynamicColumnsToShowb) && in_array($sw->title, $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
								<div class=" d-flex w-100 custom-checkbox">
									<input data-category="{{ $category->id }}" data-sw="{{ $sw->id }}" <?php echo $checked; ?> class="push-category " type="checkbox" name="category_website">
									<label class="d-flex">
										  {{ $remote_id }}
										<input data-category="{{ $category->id }}" data-sw="{{ $sw->id }}" class="rename-category ml-1" type="text" name="category_name" value="{{ $catName }}" style="width:172px !important;">
									</label>
								</div>
							</td>
					        <?php } ?>
					      </tr>
					    <?php } ?>
				    </tbody>
				</table>
				<?php echo $categories->appends(request()->except('page'))->links(); ?>
			</div>
		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
  	<div class="modal-dialog" role="document">
  	</div>	
</div>

@include('storewebsite::category.partials.store-website-category-column-visibility-modal', ['columns_array' => $columns_array])

<div id="category-history-modal" class="modal fade" role="dialog">
  	<div class="modal-dialog modal-lg">
    	<div class="modal-content">
	      	<div class="modal-body">
	        	<div class="col-md-12">
	          		<table class="table table-bordered">
	            		<thead>
			              	<tr>
			                	<th>Sl no</th>
			                	<th>Log case id</th>
			                	<th>Category id</th>
			                	<th>Store id</th>
			                	<th>Log detail</th>
			                	<th>Description</th>
			              	</tr>
			            </thead>
	            		<tbody class="category-history-list-view">
	                    </tbody>
	          		</table>
	        	</div>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      	</div>
    	</div>
  	</div>
</div>


<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>

<script>
	$('.selectpicker').selectpicker({noneSelectedText : 'Choose a category'});
	$('.selectpicker2').selectpicker({noneSelectedText : 'Choose a website'});

	$("#btnReset").bind("click", function () {
		$(".selectpicker").val('default').selectpicker("refresh");
		$(".selectpicker2").val('default').selectpicker("refresh");
	});

$(document).on('change', '.push-category', function() {
	var catId = $(this).attr('data-category');
	var swId = $(this).attr('data-sw');
	var check = 0;
	if($(this).is(":checked")) {
		check = 1;
	} else {
		$(this).parent('td').find('.rename-category').val('');
	}
	var catName = $(this).parent('td').find('.rename-category').val();
	ajaxCall(catId, swId, check, catName);
});

$(document).on('blur', '.rename-category', function() {
	var catId = $(this).attr('data-category');
	var swId = $(this).attr('data-sw');
	var check = 0;
	if($(this).parent('td').find('.push-category').is(":checked")) {
		check = 1;
	}
	var catName = $(this).val();
	ajaxCall(catId, swId, check, catName);
});

function ajaxCall(catId, swId, check, catName) {
	$.ajax({
		url: "{{ route('store-website.save.store.category') }}",
		type: 'POST',
		data:{category_id: catId, store: swId, check: check, category_name: catName, '_token': "{{ csrf_token()}}"},
		beforeSend :  function() {
			$("#loading-image").show();
		},
		success: function(data) {
			$.each(data.storeWebsites, function (index, item) {
				let ele = $('.push-category[data-category="' + catId + '"][data-sw="'+item+'"]');
				if(check && swId !== item) {
					ele.prop('checked', true);
				} else if(!check && swId !== item) {
					ele.prop('checked', false);
				}
			});
			$("#loading-image").hide();
			if(data.message) {
				$('#alert-msg p').text(data.message);
				$('#alert-msg').show();
			}
		}
	})
}

$(document).on('click','.checkinglog',function(){
	var category_id = $(this).data('id');
	$.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('store-website.category,categoryHistory') }}",
        data: {
          category_id:category_id,
        },
    }).done(response => {
      $('#category-history-modal').find('.category-history-list-view').html('');
        if(response.success==true){
          $('#category-history-modal').find('.category-history-list-view').html(response.html);
          $('#category-history-modal').modal('show');
        }

    }).fail(function(response) {

      alert('Could not fetch payments');
    });
});

</script>

@endsection

