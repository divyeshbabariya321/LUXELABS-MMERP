@extends('layouts.app')
@section('content')
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Domain Report</h2>
                </div>
            </div>
        </div>
    </div>
	
@if($viewTypeName =='organic_keywords')
	<div class="row mx-1">
		<div class="col-lg-12 margin-tb p-3">
			<div class="col-md-2">
				<div class='input-group date' id='filter'>
					<input type='text' class="form-control" id="search_url" name="search_url" value="{{old('search_url')}}" placeholder="Enter url" />
				</div>
			</div>
			<div class="col-md-2">
				<div class='input-group date' id='filter'>
					<input type='text' class="form-control" id="search_keyword" name="search_keyword" value="{{old('search_keyword')}}" placeholder="Enter Keyword" />
				</div>
			</div>
			
			<div class="col-md-2">
				<button type="button" class="btn btn-image" onclick="submitSearch();"><img src="{{ asset('images/filter.png')}}"/></button>
				<button type="button" class="btn btn-image pl-0" id="resetFilter" onclick="resetSearch()"><img src="{{ asset('images/resend2.png')}}"/></button>
			</div>
		</div>
	</div>
	<div class="table-responsive" >	
		<table class="table table-striped table-bordered"> 
			<thead>
				<tr>
				<th style="width:7%">Domain report</th>       
				<th style="width:7%">
				<select id="keyword_type" class=""><option value="organic" selected>Organic</option><option value="paid">Paid</option></select>
				</th>       
				<th style="width:7%">Keyword</th>       
				<th style="width:7%">Position</th>       
				<th style="width:7%">Previous Position</th>       
				<th style="width:7%">Position Difference</th>       
				<th style="width:7%">Search Volume</th>       
				<th style="width:7%">CPC</th>       
				<th style="width:7%">Url</th>       
				<th style="width:7%">Traffic (%)</th>       
				<th style="width:7%">Traffic Cost (%)</th>       
				<th style="width:7%">Competition</th>       
				<th style="width:7%">Number of Results</th>       
				<th style="width:7%">Trends</th>       
			</tr>
		</thead>
		<tbody id="keywordData">
			@include('seo-tools.partials.domain-data')
		</tbody>
		
		</table>
	</div>
@endif

@if($viewTypeName =='organic_traffic') 
<div class="row mx-1">
	<div class="col-lg-12 margin-tb p-3">
		<div class="col-md-2">
			<div class='input-group date' id='filter'>
				<input type='text' class="form-control" id="search_url" name="search_url" value="{{old('search_url')}}" placeholder="Enter url" />
			</div>
		</div>
		<div class="col-md-2">
			<div class='input-group date' id='filter'>
				<input type='text' class="form-control" id="search_keyword" name="search_keyword" value="{{old('search_keyword')}}" placeholder="Enter Number of keyword" />
			</div>
		</div>
		
		<div class="col-md-2">
			<button type="button" class="btn btn-image" onclick="submitSearch();"><img src="{{ asset('images/filter.png')}}"/></button>
			<button type="button" class="btn btn-image pl-0" id="resetFilter" onclick="resetSearch()"><img src="{{ asset('images/resend2.png')}}"/></button>
		</div>
	</div>
</div>
<div class="table-responsive" >	
	<table class="table table-striped table-bordered"> 
		<thead>
			<tr>
			  <th style="width:7%">Domain Organic Page report</th>       
			  <th style="width:7%">Url</th>       
			  <th style="width:7%">Number_of_Keywords</th>       
			  <th style="width:7%">Traffic</th>       
			  <th style="width:7%">Traffic (%)</th>           
		   </tr>
	   </thead>
	   <tbody id="keywordData">
		@include('seo-tools.partials.domain-organic-page')
	   </tbody>
	  
	</table>
</div>
@endif

@if($viewTypeName =='organic_cost') 
<div class="row mx-1">
	<div class="col-lg-12 margin-tb p-3">
		<div class="col-md-2">
			<div class='input-group date' id='filter'>
				<input type='text' class="form-control" id="target_url" name="target_url" value="{{old('target_url')}}" placeholder="Enter Target url" />
			</div>
		</div>
		<div class="col-md-2">
			<div class='input-group date' id='filter'>
				<input type='text' class="form-control" id="times_seen" name="times_seen" value="{{old('times_seen')}}" placeholder="Enter Times seen" />
			</div>
		</div>
		<div class="col-md-2">
			<div class='input-group date' id='filter'>
				<input type='text' class="form-control" id="ads_count" name="ads_count" value="{{old('ads_count')}}" placeholder="Enter ads seen" />
			</div>
		</div>
		
		<div class="col-md-2">
			<button type="button" class="btn btn-image" onclick="submitSearch();"><img src="{{ asset('images/filter.png')}}"/></button>
			<button type="button" class="btn btn-image pl-0" id="resetFilter" onclick="resetSearch()"><img src="{{ asset('images/resend2.png')}}"/></button>
		</div>
	</div>
</div>
<div class="table-responsive" >	
	<table class="table table-striped table-bordered"> 
		<thead>
			<tr>
			  <th style="width:7%">Domain Landing Page report</th>       
			  <th style="width:7%">target_url</th>       
			  <th style="width:7%">first_seen</th>       
			  <th style="width:7%">last_seen</th>       
			  <th style="width:7%">times_seen</th>
              <th style="width:7%">ads_count</th>			  
		   </tr>
	   </thead>
	   <tbody id="keywordData">
		@include('seo-tools.partials.domain-landing-page')
	   </tbody>
	  
	</table>
</div>	 
@endif

<!-- Model Add Seo tool START -->
    <div id="compModal" class="modal fade in" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" style="padding: 0 10px 10px">
                <div class="modal-header">
                    <h3>Competitors Analysis</h3>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="table-responsive" >	
					<table class="table table-striped table-bordered"> 
						<thead>
							<tr>
						     <th>Domain</th>
							 <th>Common Keywords</th>
							 <th>Organic Keywords</th>
							 <th>Organic Traffic</th>
							</tr>
						</thead>
						<tbody>
						 @include('seo-tools.partials.compitetors-analysis-page')
						</tbody>
				    </table>		
				</div>
            </div>
        </div>
    </div>
    <!-- Model Add Seo tool END -->

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
		$('.select2').select2();
		$('#keyword_type').change(function(){ 
				var type = $(this).val();
				$.ajax({
					url : "{{ url('seo/domain-report/2/') }}"+'/'+type,
					type : "GET",
					success : function (data){ 
						$('#keywordData').html(data);              
					},
					error : function (response){

					}
				});
			});
		$(document).on('click', '.expand-row-msg', function () {
			var name = $(this).data('name');
			var id = $(this).data('id');
			var full = '.expand-row-msg .show-short-'+name+'-'+id;
			var mini ='.expand-row-msg .show-full-'+name+'-'+id;
			$(full).toggleClass('hidden');
			$(mini).toggleClass('hidden');
		});

		//This function use for search record by ajax
		function submitSearch() 
		{
			var websiteId = "{{$id}}";
			var viewId = "{{$viewId}}";
			var viewTypeName = "{{$viewTypeName}}";
			var removeviewIDWhite = viewId.replace(" ", "_");
			var src = "{{url('seo/domain-report/search/')}}/"+websiteId+"/organic/"+removeviewIDWhite+"/"+viewTypeName;
			var searchDatabase = $('#search_database').val();
			var searchDomain = $('#search_domain').val();
			var searchAnchor = $('#search_anchor').val();
			var sourceUrl = $('#source_url').val();
			var sourceTitle = $('#source_title').val();
			var searchUrl = $("#search_url").val();
			var searchKeyword = $("#search_keyword").val();
			var targetUrl = $("#target_url").val();
			var timesSeen = $("#times_seen").val();
			var adsCount = $("#ads_count").val();
			data = {
				"search_websiteId" : websiteId,
				"search_database" : searchDatabase,
				"search_domain" : searchDomain,
				"search_anchor" : searchAnchor,
				"source_url" : sourceUrl,
				"source_title" : sourceTitle,
				"search_url" : searchUrl,
				"search_keyword" : searchKeyword,
				"target_url" : targetUrl,
				"times_seen" : timesSeen,
				"ads_count" : adsCount
			};
			$.ajax({
				url: src,
				type: "post",
				dataType: "json",
				data: data,
				headers: {
					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
				},
				beforeSend: function () {
					$("#loading-image").show();
				},
			}).done(function (message) {
				$("#loading-image").hide();
				$("#keywordData").html(message.tbody);
				var rendered = renderMessage(message, tobottom);
			}).fail(function (jqXHR, ajaxOptions, thrownError) {
				$("#loading-image").hide();
				alert(jqXHR.message);
			});
		}
		
		//This function use for reset filter data
		function resetSearch() 
		{
			location.reload();
		}
	</script>
@endsection