@extends('layouts.app')
@section('content')
<style type="text/css">
	.imagePreview {
	    width: 100%;
	    height: 180px;
	    background-position: center center;
	  background:url(http://cliquecities.com/assets/no-image-e3699ae23f866f6cbdf8ba2443ee5c4e.jpg);
	  background-color:#fff;
	    background-size: cover;
	  background-repeat:no-repeat;
	    display: inline-block;
	  box-shadow:0px -3px 6px 2px rgba(0,0,0,0.2);
	}
	.btn-primary
	{
	  display:block;
	  border-radius:0px;
	  box-shadow:0px 4px 6px 2px rgba(0,0,0,0.2);
	  margin-top:-5px;
	}
	.imgUp
	{
	  margin-bottom:15px;
	}
	.del
	{
	  position:absolute;
	  top:0px;
	  right:15px;
	  width:30px;
	  height:30px;
	  text-align:center;
	  line-height:30px;
	  background-color:rgba(255,255,255,0.6);
	  cursor:pointer;
	}
	.imgAdd
	{
	  width:30px;
	  height:30px;
	  border-radius:50%;
	  background-color:#4bd7ef;
	  color:#fff;
	  box-shadow:0px 0px 2px 1px rgba(0,0,0,0.2);
	  text-align:center;
	  line-height:30px;
	  margin-top:0px;
	  cursor:pointer;
	  font-size:15px;
	}
	.error {
		color: #FF0000;
	}
</style>
<div class="row" id="product-template-page">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Product Templates</h2>
        <div class="pull-right">
            <button type="button" class="btn btn-secondary create-product-template-btn"style="margin-right: 9px;">Add Product Template</button>
        </div>
		<div class="pull-right">
            <a href="{{route('product.templates.log')}}" class="btn btn-secondary website-template-btn"style="margin-right: 9px;">Website Template Log</a>
        </div>
        <div class="pull-left">
        	<form action="?" method="get">
	            <div class="form-group">
				    <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control keyword-text" placeholder="Enter keyword">
				</div>
			</form>
        </div>
		<div class="pull-left">

		<input type="date" name="log_date" id="log_date" class="form-control mr-2"style="width:250px !important">
		<button  class="btn  btn-info btn-log-instances">
			Get Python Logs
		</button>
                            

		</div>

    </div>
    <br>
    <div class="row" style="margin:10px;">
		<div class="col-md-12" id="page-view-result">

		</div>
	</div>
</div>
<div id="display-area"></div>

<div id="manage-log-instance" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Instagram Logs</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@include("product-template.partials.list-template")
@include("product-template.partials.create-form-template")
@include("partials.modals.large-image-modal")
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsrender/1.0.5/jsrender.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/product-template.js') }} "></script>
<script type="text/javascript">
	productTemplate.init({
		bodyView : $("#product-template-page"),
		baseUrl : "<?php echo url('/'); ?>",
		isOpenCreateFrom : '<?php echo ! empty($productArr) ? 'true' : 'false'; ?>',
		ddlSelectProduct : @json($productArr),
	});

	function bigImg(img){
        $('#image_crop').attr('src',img);
        $('#largeImageModal').modal('show');
    }

    $(document).on('click','.reload-image',function(){
    	var uid = $(this).data('uid');
    	var btn = $(this);
    	if( uid == '' ){
    		return false;
    	}

    	$.ajax({

	        type: 'POST',
	        url: '/product-templates/reload-image',
	        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
	        data: {
	            uid: uid,
	        },

	        beforeSend: function () {
	            btn.find('i').addClass('fa-spin');
	        },
	        success: function (data) {
	        	btn.find('i').removeClass('fa-spin');
	            if (data.code == 0){
	                toastr['error'](data.message, 'Error');
	            }
	            else{
	                toastr['success'](data.message, 'Success');
	                bigImg(data.image);
	            }
	        },
	        complete: function () {
	            btn.find('i').removeClass('fa-spin');
	        },
	    });
	});

	$(document).ready(function()
	{	
		
		$(document).on('change','#generate_image_from_input',function(){
			var value =$(this).val();	
			console.log( value );
			if ( value == 'banner-bear' ) {
				$('div.default').hide();
			} else {
				$('div.default').show();
			}
		});

		$(document).on('change','.template-dropdown-function',function()
		{
            var id=$(this).val();
    	
    	var templateRow={};
    	$('.special').html('');
    	$.each(templatesData,(function(k,v)
    	{
    		if(v.id==id)
    		{
    			templateRow=v;
    		}
    	})
    	)

    	if(templateRow.modifications.length)
    	{
    		$.each(templateRow.modifications,function(k,v)
    		{ 
    			
           
                if(typeof templateRow.modifications[k+1] !=='undefined' &&v.row_index==templateRow.modifications[k+1].row_index && templateRow.modifications[k+1].tag=='image_url')
                {
                	
                	return;
                }
                  

                    //console.log(v);

    			if(v.tag !=='image_url')
    			{
    				$('.special').append('<div class="form-group row"> <label for="'+v.tag+'" class="col-sm-3 col-form-label">'+v.tag+'</label><div class="col-sm-6"> <input type="text" name="modifications_array['+v.row_index+']['+v.tag+']" class="form-control" id="'+v.tag+v.row_index+'" value="'+v.value+'"></div></div>');
    			}

    			

            

    			
    		$('.special').append('<hr>');


    		})
    	}
		})
	})

	var templatesData=JSON.parse('@json($templatesJSON)');

    console.log(templatesData);

	$(document).on("click",".btn-log-instances",function(e) {
            e.preventDefault();
            var $date=  $('#log_date').val();
            $.ajax({
                url: '{{url("product-templates/get-log")}}',
                method:"get",
                data : {
                    date : $date
                },
                success: function (data) {
                    if(data.type=="success"){
                          $("#manage-log-instance").find(".modal-body").html(data.response);
                          $("#manage-log-instance").modal('show'); 
                    }else{
                        alert(data.response)                     }
                },
            });
        });
</script>

@endsection