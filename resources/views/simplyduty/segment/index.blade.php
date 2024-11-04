
<div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">SimplyDuty Segment</h2>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" onclick="showaddedit('0')">Add New</button>
              </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="category-table">
            <thead>
            <tr>
                 <th style="width:10%">ID</th>
                <th style="width:10%">Segment</th>
                <th style="width:60%">Price</th>
                <th>Action</th>
                
            </tr>
           
            </thead>
             {!! $segments->appends(Request::except('page'))->links() !!}
            <tbody>
            @include('simplyduty.segment.partials.data')
            </tbody>
        </table>
    </div>
    {!! $segments->appends(Request::except('page'))->links() !!}

    <div id="segmentadd" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
           <form action="{{ url('duty/segment/add') }}" method="POST" >
	            <input type="hidden" name="segment_id" id="segment_id" value="0">
	            <div class="modal-header">
	                <h4 class="modal-title">Add/Edit Segment</h4>
	            </div>
	            <div class="modal-body" >
				    	@csrf
					    <div class="form-group">
					        <label for="document">Segment Name</label>
					        <input class="form-control" type="text" name="segment" id="segment_txt" value="" required>
					    </div>
                        <div class="form-group">
					        <label for="document">Price</label>
					        <input  class="form-control"  type="number" step="any"  name="price" id="price" value="" required>
					    </div>
						

	            </div>
	            <div class="modal-footer">
	                <button type="button" onclick="savesegment();" class="btn btn-default btn-save-documents">Save</button>
                    <button type="button" class="btn btn-default" onclick='$("#segmentadd").modal("hide");'>Close</button>
	               
	            </div>
			</form>
        </div>
    </div>
</div>





<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
 function showaddedit(id)
 {
     $('#segment_id').val(id);
     if (id>0)
     {
        $('#segment_txt').val($('#segment_'+id).html()); 
        $('#price').val($('#price_'+id).html());
     }
     $("#segmentadd").modal("show");  
 }     
        </script>
