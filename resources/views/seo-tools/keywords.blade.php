@extends('layouts.app')
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
@section('content')
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Keyword Report ({{ $website['website'] }})</h2>
                </div>
            </div>
        </div>
    </div>
	<div class="container-fluid">
	<form method="get" action="{{route('keyword-search')}}">
					<div class="form-group">
						<div class="row">
							
							<div class="col-md-2">
							  <select data-placeholder="Select Country" multiple class="chosen-select" name="countries[]" >
								<option value="">Select Country</option>
								@foreach($countries as $row)
								  <option value="{{$row->database}}" @if(isset($filter['countries']))) @if( in_array($row->database, $filter['countries']) ) SELECTED  @endif @endif >{{$row->database}}</option>
								@endforeach
							  </select>
							</div>
							  <div class="col-md-2">			
							  <select data-placeholder="Select Keyword" multiple class="chosen-select" name="keywords[]" >
								<option value="">Select Keyword</option>
								@foreach($keywords as $row)
								  <option value="{{$row->keyword}}" @if(isset($filter['keywords'])) @if( in_array($row->keyword, $filter['keywords']) ) SELECTED  @endif @endif >{{$row->keyword}}</option>
								@endforeach
							  </select>
							  </div>
							  
							   <div class="col-md-2">			
							   <select data-placeholder="Select website"  class="chosen-select" name="website" >
								<option value="">Select Website</option>
								@foreach($allWebsites as $website)
								  <option value="{{$website['id']}}" @if(isset($filter['website']) and $filter['website'] == $website['id']) SELECTED  @endif>{{$website['website']}}</option>
								@endforeach
							  </select>
							  </div>
							
							<div class="col-md-1 d-flex justify-content-between">
								<button type="submit" class="btn btn-image" ><img src="/images/filter.png"></button> 
							</div>
							
							<div class="col-md-3">
								<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#myModal">Add Keyword Idea</button>
							</div>
							
							<div class="col-md-3">
								<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#keywordIdeas">View Keyword Ideas</button>
							</div>
						</div>
					</div>
				</form>
				
<div class="table-responsive" >	
	<table class="table table-striped table-bordered"> 
		<thead>
			<tr>
			  <th style="width:10%">Keyword</th>       
			  <th style="width:10%">Country</th>     			  
			  <th style="width:10%">Difficulty </th>       
			  <th style="width:10%">C P C</th>       
			  <th style="width:10%">Traffic</th> 
			  <th style="width:10%">Volume</th>
			  <th style="width:10%">Competitors</th>
			  <th style="width:10%">Paid traffic</th>
              <!--<th style="width:7%">
			     <select id="keyword_type" class=""><option value="organic" selected>Organic</option><option value="paid">Paid</option></select>
			  </th>--> 			  
		   </tr>
	   </thead>
	   <tbody id="keywordData">
		@include('seo-tools.partials.keywords-analysis')
	   </tbody>
	  
	</table>
</div>
</div>


<!-- Modal -->
<div id="keywordIdeas" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
		<h4 class="modal-title">Keyword Ideas</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
         <div class="table-responsive" >	
			<table class="table table-striped table-bordered"> 
				<thead>
					<tr>
					   <th style="width:10%">#</th>		  
					   <th style="width:10%">Keyword Ideas</th>		  
					</tr>
			   </thead>
			   <tbody id="keywordData">
					@foreach($seoKeywordIdeas as $key=>$seoKeywordIdea) 
						<tr>
							<td>{{$key+1}}</td>
							<td>{{$seoKeywordIdea['idea']}}</td>
						</tr>
					@endforeach
			   </tbody>
			  
			</table>
		</div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
		<h4 class="modal-title">Keyword Ideas</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
		{{ html()->form('POST', route('save.keyword.idea'))->open() }}
          <div class="form-group col-md-12">
		      <label for="inputEmail4">Ideas</label>
			  {{ html()->textarea('idea')->placeholder('Enter Idea')->class('form-control')->rows(2) }}
		   </div>
	       <div class="form-group col-md-12">
				<input type="hidden" name="store_website_id" value="{{ $website['id'] }}">
		  		<button type="submit" class="btn btn-primary">Submit</button>
	       </div>
		 </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.jquery.min.js"></script>


    <script>
	  
	  $(".chosen-select").chosen({
		  no_results_text: "Oops, nothing found!"
	  });
	  
	  $(document).on('click', '.expand-row-msg', function () {
		var name = $(this).data('name');
		var id = $(this).data('id');
		var full = '.expand-row-msg .show-short-'+name+'-'+id;
		var mini ='.expand-row-msg .show-full-'+name+'-'+id;
		$(full).toggleClass('hidden');
		$(mini).toggleClass('hidden');
	  });
	  
    </script>
@endsection