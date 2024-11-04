@extends('layouts.app')
@section('content')
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Compitetors Report</h2>
                </div>
            </div>
        </div>
    </div>
<div class="table-responsive" >	
	<table class="table table-striped table-bordered"> 
		<thead>
			<tr>
			  <th style="width:7%">Compitetors report</th>       
			  <th style="width:7%">database</th>
              <th style="width:7%">
			     <select id="keyword_type" class=""><option value="organic" selected>Organic</option><option value="paid">Paid</option></select>
			  </th>     			  
			  <th style="width:7%">domain</th>       
			  <th style="width:7%">common_keywords</th>       
			  <th style="width:7%">keywords</th> 
			  <th style="width:7%">traffic</th> 			  
		   </tr>
	   </thead>
	   <tbody id="keywordData">
		@include('seo-tools.partials.compitetors-data')
	   </tbody>
	  
	</table>
</div>
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
    </script>
@endsection