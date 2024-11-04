<script type="text/x-jsrender" id="template-result-block">
	<div class="table-responsive mt-3">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>Id</th>
		      	<th>Store Website Id</th>
				<th>Attribute Key</th>
				<th>Attribute Val</th>
				<th>Store Website</th>
				<th>Actions</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			      	<td>{{:prop.store_website_id}}</td>
			        <td>{{:prop.attribute_key}}</td>
			        <td>{{:prop.attribute_val}}</td>
			        <td>{{:prop.website}}</td>
			        <td>
			        	<button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
			        		<i class="fa fa-trash" aria-hidden="true"></i>
			        	</button>
				        	<button type="button" title="Attributes history" class="btn attributes-history-btn " data-id="{{>prop.id}}">
	                      <i class="fa fa-history"></i>
	                  </button>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>
    <div id="attribute-history-modal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-body">
            <div class="col-md-12">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Sl no</th>
                    <th>Log Case Id</th>
                    <th>Attribute Id</th>
                    <th>Attribute Key</th>
                    <th>Attribute Val</th>
                    <th>Store Website Id</th>
                    <th>Log Msg</th>
                    <th>Date / Time</th>
                  </tr>
                </thead>
                <tbody class="attribute-history-list-view">
                            </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<script type="text/javascript">
	$(document).on('click','.attributes-history-btn', function(){

		var id = $(this).data('id');
          $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "<?php echo route('store-website.site-attributes-views.attributeshistory'); ?>",
            data: {
              id:id,
            },
        }).done(response => {
          $('#attribute-history-modal').find('.attribute-history-list-view').html('');
            if(response.success==true){
              $('#attribute-history-modal').find('.attribute-history-list-view').html(response.html);
              $('#attribute-history-modal').modal('show');
            }

        }).fail(function(response) {

          alert('Could not fetch payments');
        });

	});
</script>