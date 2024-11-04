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
	#task_Tables td {
            word-break: break-all;
        }
</style>
<div class="row" id="load-test-page">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Load Testing</h2>
        <div class="pull-right">
            <button type="button" class="btn btn-secondary create-product-template-btn"style="margin-right: 9px;">Add Load Testing Data</button>
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
	<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
          50% 50% no-repeat;display:none;">
@include("load-testing.partials.list-record")
@include("load-testing.partials.create-form-record")
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsrender/1.0.5/jsrender.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
{{-- <script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script> --}}
{{-- <script type="text/javascript" src="{{ mix('webpack-dist/js/load-testing.js') }} "></script> --}}
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }} "></script>
<script type="text/javascript" src="{{ asset('/js/load-testing.js') }}"></script>
<script type="text/javascript">
	productTemplate.init({
		bodyView : $("#load-test-page"),
		baseUrl : "<?php echo url('/'); ?>",
		isOpenCreateFrom : '<?php echo ! empty($productArr) ? 'true' : 'false'; ?>',
		ddlSelectProduct : @json($loadTestJSON),
	});

	var templatesData=JSON.parse('@json($loadTestJSON)');

	function Showactionbtn(id) {
        $(".action-btn-tr-" + id).toggleClass("d-none");
    }
	function submitJmeterRequest(id){
		$.ajax({
                type: 'GET',
				url: "/load-testing/submit-request/"+id,
                beforeSend: function () {
                    $("#loading-image").show();
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
				if(response.code == '1'){
					toastr['success'](response.message, 'success');
				}else{
					toastr['error'](response.message, 'error');
				}
            });
	}
</script>

@endsection