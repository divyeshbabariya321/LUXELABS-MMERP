@extends('layouts.app')
@section('content')
	<div id="myDiv">
		<img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
	</div>
	<div class="row">
		<div class="col-lg-12 margin-tb">
			<div class="row">
				<div class="col-lg-12 margin-tb">
					<h2 class="page-heading">Backlink Report</h2>
				</div>
			</div>
		</div>
	</div>
	@if($viewTypeName == 'ascore')
		<div class="row mx-1">
			<div class="col-lg-12 margin-tb p-3">
				<div class="col-md-2">
					<div class='input-group date' id='filter'>
						<input type='text' class="form-control" id="search_database" name="search_database" value="{{old('search_database')}}" placeholder="Enter Database" />
					</div>
				</div>
				<div class="col-md-2">
					<div class='input-group date' id='filter'>
						<input type='text' class="form-control" id="search_domain" name="search_domain" value="{{old('search_domain')}}" placeholder="Enter Domain" />
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
					<th style="width:7%">Backlink report</th>       
					<th style="width:7%">database</th>       
					<th style="width:7%">domain</th>       
					<th style="width:7%">domain_ascore</th>       
					<th style="width:7%">domain_backlinks_num</th>        
				</tr>
			</thead>
			<tbody id="keywordData">
				@include('seo-tools.partials.backlink-data')
			</tbody>
			
			</table>
		</div>
	@endif

	@if($viewTypeName == 'follows_num')
		<div class="row mx-1">
			<div class="col-lg-12 margin-tb p-3">
				<div class="col-md-2">
					<div class='input-group date' id='filter'>
						<input type='text' class="form-control" id="search_database" name="search_database" value="{{old('search_database')}}" placeholder="Enter Database" />
					</div>
				</div>
				<div class="col-md-2">
					<div class='input-group date' id='filter'>
						<input type='text' class="form-control" id="search_anchor" name="search_anchor" value="{{old('search_anchor')}}" placeholder="Enter Anchor" />
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
					<th style="width:7%">Backlink Anchor report</th>       
					<th style="width:7%">database</th>       
					<th style="width:7%">anchor</th>       
					<th style="width:7%">domains_num</th>       
					<th style="width:7%">anchor_backlinks_num</th>        
				</tr>
			</thead>
			<tbody id="keywordData">
				@include('seo-tools.partials.backlinkanchor-data')
			</tbody>
			
			</table>
		</div>
	@endif

	@if($viewTypeName == 'nofollows_num')
		<div class="row mx-1">
			<div class="col-lg-12 margin-tb p-3">
				<div class="col-md-2">
					<div class='input-group date' id='filter'>
						<input type='text' class="form-control" id="source_url" name="source_url" value="{{old('source_url')}}" placeholder="Enter Source url" />
					</div>
				</div>
				<div class="col-md-2">
					<div class='input-group date' id='filter'>
						<input type='text' class="form-control" id="source_title" name="source_title" value="{{old('source_title')}}" placeholder="Enter Source title" />
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
					<th style="width:7%">Backlink Indexed Page</th>       
					<th style="width:7%">source_url</th>       
					<th style="width:7%">source_title</th>       
					<th style="width:7%">response_code</th>
					<th style="width:7%">backlinks_num</th>       
					<th style="width:7%">domains_num</th>       
					<th style="width:7%">last_seen</th>
					<th style="width:7%">external_num</th>       
					<th style="width:7%">internal_num</th>			  
				</tr>
			</thead>
			<tbody id="keywordData">
				@include('seo-tools.partials.backlinkindexedpage-data')
			</tbody>
			</table>
		</div>
	@endif
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
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
			var src = "{{url('seo/backlink-details/search/')}}/"+websiteId+"/"+viewId+"/"+viewTypeName;
			var searchDatabase = $('#search_database').val();
			var searchDomain = $('#search_domain').val();
			var searchAnchor = $('#search_anchor').val();
			var sourceUrl = $('#source_url').val();
			var sourceTitle = $('#source_title').val();
			data = {
				"search_websiteId" : websiteId,
				"search_database" : searchDatabase,
				"search_domain" : searchDomain,
				"search_anchor" : searchAnchor,
				"source_url" : sourceUrl,
				"source_title" : sourceTitle
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