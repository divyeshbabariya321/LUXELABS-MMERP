@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}
</style>

<div class="row" id="common-page-layout" data-id="{{ $id }}">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
    	<div class="row">
	    	<div class="col col-md-9">
		    	<div class="row">
	    			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button>
				 </div> 		
		    </div>
		    <div class="col">
		    	<div class="h" style="margin-bottom:10px;">
					<div class="row">
		    			<form class="form-inline message-search-handler" method="post">
					  		<div class="col">
					  			<div class="form-group">
								    <label for="keyword">Keyword:</label>
								    {{ html()->text("keyword", request("keyword"))->class("form-control")->placeholder("Enter keyword") }}
							  	</div>
							  	<div class="form-group">
							  		<label for="button">&nbsp;</label>
							  		<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
							  			<img src="/images/search.png" style="cursor: default;">
							  		</button>
							  	</div>		
					  		</div>
				  		</form>	
				  		<form class="form-inline usp-handler-form" method="post">
					  		<?php echo csrf_field(); ?>
					  		<div class="col">
					  			<div class="form-group">
								    <label for="usp">USP:</label>
								    {{ html()->text("name", request("name"))->class("form-control")->placeholder("Enter usp") }}
							  	</div>
							  	<div class="form-group">
							  		<label for="button">&nbsp;</label>
							  		<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-usp">
							  			<img src="/images/add.png" style="cursor: default;">
							  		</button>
							  	</div>		
					  		</div>
					  	</form>
					</div>
		    	</div>
		    </div>
	    </div>	
		<div class="col-md-12 margin-tb" id="page-view-result">

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

@include("digital-marketing.solution.templates.list-template")
@include("digital-marketing.solution.templates.create-solution-template")
<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/digital-marketing-solution.js') }} "></script>

<script type="text/javascript">
	$(document).ready(function() {
		setTimeout(() => {
			page.init({
				bodyView : $("#common-page-layout"),
				baseUrl : "<?php echo url('/'); ?>",
				digitalId : "<?php echo $id; ?>"
			});
		}, 2000);
	});

	$(document).ready(function(){

$(document).on("change",".upload_file_solution",function(){
	//alert(0);
	 var fd = new FormData();
	 var files = $(this)[0].files;
	 var fileArray = []
	console.log(files)
	// // Check file selected or not
	 if(files.length > 0 ){

		$.each(files,function(i,e){
			console.log(e)
			fd.append('file[]',e);
		})
		fd.append('id',$(this).data("id"))
		fd.append('_token',"{{ csrf_token() }}");
		fd.append('type',"solution")
		console.log(fd)
		$.ajax({
			url: '{{route("digital-marketing.saveimages")}}',
			type: 'post',
			data: fd,
			contentType: false,
			processData: false,
			success: function(response){
				console.log(response)
				toastr['success'](response.msg, 'Success');
			},
		});
	 }else{
	 alert("Please select a file.");
	 }
});
});
</script>

@endsection

