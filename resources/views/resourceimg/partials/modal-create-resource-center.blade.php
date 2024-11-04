<style>
	.multiselect-container > li > a,
	.multiselect-container > li > a:focus {
		outline: 0;
		outline-offset: 0;
	}
	.multiselect-container > li  > a > label.radio{
		position: relative;
		padding: 8px 10px;
	}
	.multiselect-container > li  > a > label.radio input[type="radio"]{
		opacity: 0;
		margin: 0;
		outline: 0;
		outline-offset: 0;
		visibility: hidden;
		left: 0;
		top: 0;
		right: 0;
		bottom: 0;
	}
</style>
<div class="modal fade" id="addresource" tabindex="-1" role="dialog">
	   	<div class="modal-dialog modal-md">
		    <div class="modal-content">
		    	{{ html()->form('POST', route('add.resource'))->acceptsFiles()->open() }}
					<div class="modal-header">
						<h2 class="modal-title" style="font-size: 24px;">Create Resource Center</h2>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
				                <div class="row">
	        	                	<div class="col-12">
	        			                <div class="form-group {{ $errors->has('image1') ? 'has-error' : '' }}">
	        			                     {{ html()->label('Select Category:') }}
					  		                	<div class="input-group {{ $errors->has('cat_id') ? 'has-error' : '' }}">
				  		                		<select class="form-control" name="cat_id" data-live-search="true" id="category_id">

					  		                	@foreach($categories as $category)
					  		                		<option value="{{ $category->id }}">{{ $category->title }}</option>
					  		                	@endforeach
					  		                	</select>
	        			                </div>
	        			            </div>
				                </div>
				            </div>
				                <div class="row">
	        	                	<div class="col-12">
	        			                <div class="form-group {{ $errors->has('image1') ? 'has-error' : '' }}">
												{{ html()->label('Select Sub Category:') }}
					  		                	<select id="sub_cat_id" name="sub_cat_id" class="form-control">
												</select>
	        			                </div>
	        			            </div>
				                </div>
				                <div class="row">
	        	                	<div class="col-12">
	        			                <div class="form-group {{ $errors->has('image1') ? 'has-error' : '' }}">
	        			                    {{ html()->label('Upload Image:') }}
	        			                    <input type="file" name="image[]" placeholder="Upload Image" multiple>
	        			                    <span class="text-danger">{{ $errors->first('image1') }}</span>
	        			                </div>
	        			            </div>
				                </div>
				                <div class="row">
	        	                	<div class="col-12">
				  		                <div class="form-group">
					  		                {{ html()->label('Paste Image:') }}
					  		                <textarea class="form-control can_id" id="can_id" placeholder="Paste Image" style="resize: none"></textarea>
					  		                <span class="msg"></span>
					  		                <img src="" id="src_img" style="width:100%">
				  		                </div>
				                	</div>
	        	                </div>
				                <div class="row">
						        	<div class="col-12">
				  		                <div class="form-group">
					  		                {{ html()->label('Url:') }}
					  		                <input type="text" class="form-control" name="url" placeholder="Resource Url">
				  		                </div>
				                	</div>
				                </div>
				                <div class="row">
				                	<div class="col-12">
				  		                <div class="form-group">
					  		                {{ html()->label('Description:') }}
					  		                <textarea class="form-control" name="description" placeholder="Resource Description"></textarea>
				  		                </div>
				                	</div>
				                </div>
				            </div>
						</div>
				    </div>
				    <div class="modal-footer">
			            <canvas style="border:none;display: none;" id="my_canvas"></canvas>
				    	<input type="hidden" autocomplete="off" class="form-control" name="image2" id="cpy_img">
			            <button type="submit" style="display: block;" id="save_img" class="btn btn-secondary"><i class="fa fa-plus"></i></button>
				        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i></button>
				    </div>
				{{ html()->form()->close() }}
			</div>	
	  	</div>
	</div>