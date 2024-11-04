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
<div class="modal fade" id="shortcut_addresource" tabindex="-1" role="dialog">
	   	<div class="modal-dialog modal-md">
		    <div class="modal-content">
		    	{{ html()->form('POST', route('add.resource'))->acceptsFiles()->id('create-resource-center-form')->open() }}
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
				  		                		<select class="form-control" name="cat_id" data-live-search="true" id="shortcut_category_id">

					  		                	@foreach($resorcecategory as $s_r_c)
					  		                		<option value="{{ $s_r_c->id }}">{{ $s_r_c->title }}</option>
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
					  		                	<select id="shortcut_sub_cat_id" name="sub_cat_id" class="form-control">
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
					  		                <textarea class="form-control shortcut_can_id" id="shortcut_can_id" placeholder="Paste Image" style="resize: none"></textarea>
					  		                <span class="msg"></span>
					  		                <img src="" id="shortcut_src_img" style="width:100%">
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


	<script type="text/javascript">
		$("#shortcut_src_img").css('height', '0');
        function ShortcutPasteImage() {
            var e = document.getElementById("my_canvas").toDataURL();
            $("#cpy_img").val(e), $("#save_img").fadeIn(200), $(".msg").empty(), $(".msg").css("color", "green"), $(".msg")
                .text("Image Loaded Successfully."), $(".shortcut_can_id").attr("placeholder",
                    "Image Loaded Successfully, Paste another to change."), $("#shortcut_src_img").attr("src", e).css('height', 'auto')
        }
        var CLIPBOARD = new SHORTCUT_CLIPBOARD_CLASS("my_canvas", !0);

        function SHORTCUT_CLIPBOARD_CLASS(e, t) {
            var a = this,
                n = document.getElementById(e),
                i = document.getElementById(e).getContext("2d");
            document.addEventListener("paste", function(e) {
                "shortcut_can_id" == e.target.id && (console.log(e), a.paste_auto(e))
            }, !1), this.paste_auto = function(e) {
                if (e.clipboardData) {
                    var t = e.clipboardData.items;
                    if (!t) return;
                    for (var a = !1, n = 0; n < t.length; n++)
                        if ($("#cpy_img").val(""), -1 !== t[n].type.indexOf("image")) {
                            var i = t[n].getAsFile(),
                                c = (window.URL || window.webkitURL).createObjectURL(i);
                            this.paste_createImage(c), a = !0
                        } 1 == a ? (e.preventDefault(), $(".msg").text("Image Loading, Please Wait."), $(".msg").css(
                        "color", "red"), setTimeout(ShortcutPasteImage, 5e3)) : (e.preventDefault(), $(".shortcut_can_id").attr(
                        "placeholder", "Please paste only image."))
                }
            }, this.paste_createImage = function(e) {
                var a = new Image;
                a.onload = function() {
                    1 == t ? (n.width = a.width, n.height = a.height) : i.clearRect(0, 0, n.width, n.height), i
                        .drawImage(a, 0, 0)
                }, a.src = e
            }
        }

		$(document).ready(function () {
			$('#shortcut_category_id').select2({ width: "100%" });
            $('#shortcut_category_id').val(null).trigger('change');
            $('#shortcut_category_id').change(function (e) {
                e.preventDefault();
                $('#shortcut_sub_cat_id').html('');
                var selected = $(this).val();
                if (selected.length > 0) {
                    $.ajax({
                        url: "{{ url('/api/values-as-per-category') }}",
                        method: "POST",
                        data: {
                            selected: selected,
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(data) {

                            $('#shortcut_sub_cat_id').html(data);
                            $("#shortcut_sub_cat_id").select2("destroy").select2({width: "100%"});
                            $("#shortcut_sub_cat_id").val(null).trigger('change');
                        }
                    })
                }
            });

			$('#shortcut_sub_cat_id').select2({
                width: "100%"
            });
		});

        $(document).on('submit', '#create-resource-center-form', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('add.resource') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function (resp) {
                    switch (resp.status) {
                        case 'success':
                            toastr.success(resp.msg);
                            break;
                    }
                },
                error: function (error) {
                    for (let msg of Object.values(error.responseJSON.errors)) {
                        toastr.error(msg);
                    }
                }
            });
        });
    </script>
