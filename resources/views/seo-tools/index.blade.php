@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
		  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
		.seo_content {     width: 100%;     float: left; padding:50px 0;}
		.seo_content .seo_content_inner {     width: 100%;     float: left;}
		.seo_content .seo_content_inner .seo_text {     padding: 0 30px;     border-right: 2px solid #bfbfbf; }
		.seo_content .seo_content_inner .seo_text:last-child {     border-right: none; }
		.seo_content .seo_content_inner .seo_text_inner {     width: 100%;     float: left; }
		.seo_content .seo_content_inner .seo_text_inner h6 {     width: 100%;     float: left;     margin: 0; text-transform:capitalize;    font-size: 18px;     line-height: 28px;     font-weight: normal;     color: #000;     padding: 0 0 4px 0; font-family: 'Poppins', sans-serif;}
		.seo_content .seo_content_inner .seo_text_inner span {     width: 100%;     float: left;     font-size: 24px;     font-weight: 600;     line-height: 32px; font-family: 'Poppins', sans-serif; position:relative;}
		.seo_content .seo_content_inner .seo_text_inner span::after {     position: absolute;     content: '';     width: 100px;     height: 2px;     background: red;     left: 0;     bottom: -10px; }
		.seo_content .seo_content_inner sub {     width: auto;     color: #d23636;     font-weight: 600;     font-size: 12px;     line-height: 24px; font-family: 'Poppins', sans-serif;}
		.seo_text.seo_fir {     padding: 0 !important; }
		.row.seo_select .form-group {     padding: 0; }
		.seo_select_inner {     padding: 0; }
		ul#select2-search-results li.select2-results__option {     width: 100%;     float: left;     font-family: 'Poppins', sans-serif; }
		.row.seo_select .select2-container--default .select2-selection--single .select2-selection__arrow {     height: 40px !important; }
		.row.seo_select label {     width: 100%;     float: left;     margin: 0;     text-transform: capitalize;     font-size: 20px;     line-height: 32px;     font-weight: normal;     color: #000;     padding: 0 0 4px 0;     font-family: 'Poppins', sans-serif; }
		.row.seo_select .select2-container--default .select2-selection--single .select2-selection__rendered {     text-align: left;     font-family: 'Poppins', sans-serif;     font-size: 14px;     padding: 0 10px;     width: 100%;     float: left; }
		.row.seo_select .select2-container--default .select2-selection--single .select2-selection__rendered {     line-height: 40px !important;     color: #757575; }
		.row.seo_select .select2-container .select2-selection--single {     height: 40px !important;     border: 1px solid #ddd !important;     color: #757575;     padding-left: 6px;     width: 100%;     float: left; }
		.row.seo_select .select2-hidden-accessible option {     font-family: 'Poppins', sans-serif;     width: 100%;     float: left; }
		.seo_tabs{width:100%; float:left; padding:0 15px;}
		.seo_tabs.nav-tabs>li>a{margin-right:0; font-family: 'Poppins', sans-serif;   color: #000;}
		.seo_tabs.nav-tabs>li.active>a, .seo_tabs.nav-tabs>li.active>a:focus, .seo_tabs.nav-tabs>li.active>a:hover {     color: #555;     cursor: default;     background-color: #fff;     border: 1px solid #ddd;     border-bottom-color: transparent;     font-family: 'Poppins', sans-serif;     font-weight: bold; }
		.row.seo_select {     padding: 30px 0 0 0; }
		
		table td {
			overflow-wrap: break-word;
		}
		
		@media(max-width:1199.5px){
			div.container{width:95%; max-width:95%;}  
			}
		@media(max-width:991.5px){
			.seo_text {     width: 33%;     float: left; } 		
			}
		@media(max-width:767.5px){	
			.seo_text {     width: 50%; }
			.seo_content .seo_content_inner .seo_text:nth-child(3n) {     border-right: none; }
			.seo_content .seo_content_inner .seo_text:last-child {     border-right: none;     padding: 40px 0 0; }
		} 
		@media(max-width:480.5px){	
		.seo_content .seo_content_inner .seo_text_inner {     padding: 0 0 50px 0; }
		.seo_text {     width: 100%; }
		.seo_content .seo_content_inner .seo_text{border:none;}
		.seo_content .seo_content_inner .seo_text_inner span::after {     left: 50%;     transform: translateX(-50%);     bottom: -20px; }
		.seo_content .seo_content_inner .seo_text:last-child {     border-right: none;     padding: 0; }
		.seo_content .seo_content_inner{text-align:center;}
		}
		
		/******/
.short_report >* { padding: 0 !important; float: left; width: 100%; margin: 0; }
.short_report { padding: 0 0 50px; float: left; width: 100%; border-bottom: 1px solid #efefef69; border-radius: 3px; }
.short_report h4 { font-size: 22px; font-weight: 500; margin: 30px 0 20px; }
.short_report .seo_content .container { box-shadow: -2px 0px 9px #00000029; padding: 10px 20px 30px; border-radius: 10px; }
.short_report h3 {
    font-size: 24px;
    line-height: 36px;
    font-weight: 600;
    /* padding: 10px 15px; */
    color: #000;
    margin: 30px 0 0;
    display: inline-block;
    /* box-shadow: -4px -3px 9px #00000029; */
}
		/******/
    </style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">SEO Dashboard</h2>
                    <!--button type="button" class="btn btn-secondary float-right mr-3" data-toggle="modal"
                            data-target="#addToolModal">
                        Add Tool
                    </button-->
                </div>
            </div>
        </div>
    </div>

    <!-- Model Add Seo tool START -->
    <div id="addToolModal" class="modal fade in" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" style="padding: 0 10px 10px">
                <div class="modal-header">
                    <h3>Add new tool</h3>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form name="add-seo-tool" style="padding:10px;"
                      action="{{ route('save.seo-tool') }}" method="POST">
                    @csrf
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" name="tool" placeholder="Tool" value="" required="">
                    </div>
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" name="api_key" placeholder="Api Key" value="" required="">
                    </div>
                    <button type="submit" class="btn btn-secondary">Add Tool</button>
                </form>
            </div>
        </div>
    </div>
	<div class="col-lg-12 margin-tb pl-3">
		<div class="form-group mb-3">
			<br/>
			<div class="row">
				<div class="col-md-2 pl-3 pr-0">
					<div class='input-group date' id='filter_date'>
						<input type='text' class="form-control" id="search_website" name="search_website" value="{{old('search_website')}}" placeholder="Website" />
					</div>
				</div>
				<div>
					<button type="button" class="btn btn-image" onclick="submitSearch()"><img src="{{ asset('images/filter.png')}}"/></button>
				</div>
				<div >
					<button type="button" class="btn btn-image pl-0" id="resetFilter" onclick="resetSearch()"><img src="{{ asset('images/resend2.png')}}"/></button>
				</div>
			</div>
		</div>
	</div>

    <!-- Model Add Seo tool END -->
<div class="form-group col-md-12" style="display:none;">
				<div class="seo_select_inner col-md-4 ">
					<label for="with_archived">Select Website</label>
					{{ html()->select('search', $websites)->class('search select2')->placeholder('Seletc Website')->id('search') }}
				</div>
			</div>		
		<p id="myTabContent"></p>
    @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
    @endif
	
	<div class="space-right infinite-scroll chat-list-table">
		<div class="table-responsive">
			<table class="table table-bordered" style="font-size: 14px;table-layout: fixed">
                <thead>
                <tr>
                    <th style="width: 7%;">Website</th>
                    <th style="width: 3%;">Pages Crawled</th>
                    <th style="width: 3%;">Errors</th>
                    <th style="width: 3%;">Warnings</th>
                    <th style="width: 3%;">Keywords</th>
                    <th style="width: 3%;">Traffic</th>
                    <th style="width: 3%;">Traffic cost</th>
                    <th style="width: 3%;">Ascore</th>
                    <th style="width: 3%;">Follows</th>
                    <th style="width: 3%;">No Follows</th>
                </tr>
                </thead>
                <tbody id="content_data" class="infinite-scroll-pending-inner">
					@foreach($websites as $websiteId=>$website) 
						@if(isset($domainOverview[$websiteId]) || isset($backlinkreports[$websiteId]) || isset($siteAudits[$websiteId]))
						<tr>
							@if(isset($domainOverview[$websiteId]) || isset($backlinkreports[$websiteId]) || isset($siteAudits[$websiteId]))
								<td>{{$website}}</td>
							@else
								<td>---</td>
							@endif
							@if(isset($siteAudits[$websiteId]))
								@php $siteAudit = $siteAudits[$websiteId]; @endphp
								<td><a href="{{url('seo/site-audit-details', ['websiteId'=>$websiteId,'viewId'=>$siteAudit['pages_crawled'], 'viewTypeName' => 'pages_crawled'])}}"><span>{{$siteAudit['pages_crawled']}}</span></a></td>
								<td><a href="{{url('seo/site-audit-details', ['websiteId'=>$websiteId,'viewId'=>$siteAudit['errors'], 'viewTypeName' => 'errors'])}}"><span> {{$siteAudit['errors']}}</span></a></td>
								<td><a href="{{url('seo/site-audit-details', ['websiteId'=>$websiteId,'viewId'=>$siteAudit['warnings'], 'viewTypeName' => 'warnings'])}}"><span>{{$siteAudit['warnings']}}</span></a></td>
							@else
								<td>---</td>
								<td>---</td>
								<td>---</td>
							@endif
							@if(isset($domainOverview[$websiteId]))
								@php $overview = $domainOverview[$websiteId]; @endphp
								<td><a href="{{url('seo/domain-report', ['websiteId'=>$websiteId, 'type' => 'organic', 'viewId' => $overview['organic_keywords'], 'viewTypeName' => 'organic_keywords'])}}"><span>{{$overview['organic_keywords']}}</span></a></td>
								<td><a href="{{url('seo/domain-report', ['websiteId'=>$websiteId, 'type' => 'organic', 'viewId' => $overview['organic_traffic'], 'viewTypeName' => 'organic_traffic'])}}"><span> {{$overview['organic_traffic']}}</span></a></td>
								<td><a href="{{url('seo/domain-report', ['websiteId'=>$websiteId, 'type' => 'organic', 'viewId' => $overview['organic_cost'], 'viewTypeName' => 'organic_cost'])}}"><span>{{$overview['organic_cost']}}</span></a></td>
							@else
								<td>---</td>
								<td>---</td>
								<td>---</td>
							@endif
							@if(isset($backlinkreports[$websiteId]))
								@php $backlinkreport = $backlinkreports[$websiteId]; @endphp
								<td><a href="{{url('seo/backlink-details', ['websiteId'=>$websiteId, 'viewId' => $backlinkreport['ascore'], 'viewTypeName' => 'ascore'])}}"><span>{{$backlinkreport['ascore']}}</span></a></td>
								<td><a href="{{url('seo/backlink-details', ['websiteId'=>$websiteId, 'viewId' => $backlinkreport['follows_num'], 'viewTypeName' => 'follows_num'])}}"><span> {{$backlinkreport['follows_num']}}</span></a></td>
								<td><a href="{{url('seo/backlink-details', ['websiteId'=>$websiteId, 'viewId' => $backlinkreport['nofollows_num'], 'viewTypeName' => 'nofollows_num'])}}"><span>{{$backlinkreport['nofollows_num']}}</span></a></td>
							@else
								<td>---</td>
								<td>---</td>
								<td>---</td>
							@endif
						</tr>
						@endif
					@endforeach
                </tbody>
            </table>
        </div>
    </div>




		
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
		$('.select2').select2();
        $('#fetch_details').click(function(){ 
          //  var websiteId = $(this).val();
            //var website = $('.search').select2('data'); 
			 $.ajax({
                url : "{{ route('fetch-seo-details') }}",
                type : "GET",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
				  beforeSend: function() {
                    $("#loading-image").show();
                    },
             /*   data : {
                    websiteId : websiteId,
                    website : website[0].text
                },*/
                success : function (data){ console.log(data);console.log(data.status_code);
				 $("#loading-image").hide();
					if(data.status_code == 200) {
						$('#myTabContent').html(data.response);
					}               
                },
                error : function (response){

                }
            });
        });
		function submitSearch(){
			src = "{{url('seo/search')}}";
			var search_website = $('#search_website').val();
			$.ajax({
				url: src,
				type: "GET",
				dataType: "json",
				data: {
					search_website : search_website,
				},
				beforeSend: function () {
					$("#loading-image").show();
				},
			}).done(function (message) {
				$("#loading-image").hide();
				$("#content_data").html(message.tbody);
				var rendered = renderMessage(message, tobottom);
			}).fail(function (jqXHR, ajaxOptions, thrownError) {
				$("#loading-image").hide();
				alert(jqXHR.message);
			});
		}
 $(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  });
  function resetSearch(){
        // $("#loading-image").hide();
        // $('#term').val('');
        // $('#serach_inquiry_type').val('');
        // $('#search_country').val('');
        // $('#search_order_no').val('');
        // $('#search_phone_no').val('');
        // $('#ticket').val('');
        // $('#users_id').val('');
        location.reload();
    }
    </script>
@endsection