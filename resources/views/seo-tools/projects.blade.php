@extends('layouts.app')
@section('content')
	<div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Projects</h2>
                </div>
            </div>
        </div>
    </div>
	<div class="table-responsive" >	
		<table class="table table-striped table-bordered"> 
			<thead>
				<tr>
				  <th style="width:7%">Project Id</th>       
				  <th style="width:7%">Project Name</th>       
				  <th style="width:7%">Url</th>       
				  <th style="width:7%">Tools</th>        
				  <th style="width:7%">Action</th>        
			   </tr>
		   </thead>
		   <tbody id="projects">
			<tr>
				<td> {{$project['project_id']}} </td>
				<td> {{$project['project_name']}} </td>
				<td> {{$project['url']}} </td>
				<td>  </td>
				<td> 
					<a href="#" data-projectId="{{$project['project_id']}}" class="runAudit"> Site Audit </a>  
					<!-- Button trigger modal -->
					<button type="button" class="btn btn-primary" onclick="showAddKeywordModal('{{$project['project_id']}}')">
					  Add Keyword
					</button>
				</td>
			</tr>
		   </tbody>
		  
		</table>
	</div>

				  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Add Keyword</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						  </div>
						 {{ html()->form('POST', url('seo/save-keyword'))->open() }}
							  <div class="modal-body">
								  <div class="form-group">
									 <input type="text" name="keyword" class="form-control" id="keyword" placeholder="Keyword" required>
								  </div>
									<div class="form-group">
										<input type="text" name="tags" class="form-control" id="tags" required placeholder="tags">
										<input type="hidden" name="projectId" id="projectId" required>
									</div>
							  </div>
							  <div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary">Save changes</button>
							  </div>
						  </form>
						</div>
					  </div>
					</div>

					<div id="siteAuditInfo" class="modal fade in" role="dialog">
						<div class="modal-dialog">
							<div class="modal-content" style="padding: 0 10px 10px" id="siteAuditInfoData">
								
							</div>
						</div>
					</div>

@endsection

@section('scripts')
   <script>
	   $('.runAudit').on('click', function() { 
            var projectId = $(this).attr('data-projectId');
            $.ajax({
                url : "{{ url('seo/site-audit') }}"+'/'+projectId,
                type : "GET",
				data: {'project_id': projectId},
                success : function (data){ 
					$('#siteAuditInfoData').html(data); 
					$('#siteAuditInfo').modal('show');          
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

	 function showAddKeywordModal(projectId) {
		$('#projectId').val(projectId);
		$('#keyword').val('');
		$('#tags').val('');
		$('#exampleModal').modal('show');
	 } 
    </script>
@endsection