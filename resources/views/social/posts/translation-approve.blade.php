<div id="TranslationApproval" class="modal fade" role="dialog">
    	<div class="modal-dialog">

    		<!-- Modal content-->
    		<div class="modal-content">
    			<form action="{{ route('social.post.approvepost') }}" method="POST">
    				@csrf

    				<div class="modal-header">
    					<h4 class="modal-title">Need approval for post!!</h4>
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
    				</div>
    				<div class="modal-body">
                        <div class="form-group">
    						<strong>Caption:</strong>
    						<input type="text" id="caption" name="caption" class="form-control" value="">
    						<input type="hidden" id="post_id" name="post_id" class="form-control" value="">
                        </div>
                        <div class="form-group">
    						<strong>Caption in translate:</strong>
    						<input type="text" id="caption_trans" name="caption_trans" class="form-control" value="">
                        </div>
                        <div class="form-group">
    						<strong>Hashtag:</strong>
    						<input type="text" id="hashtag" name="hashtag" class="form-control" value="">
                        </div>
                        <div class="form-group">
    						<strong>Hashtag in translate:</strong>
    						<input type="text" id="hashtag_trans" name="hashtag_trans" class="form-control" value="">
  						</div>
    				</div>
    				<div class="modal-footer">
    					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    					<button type="submit" class="btn btn-secondary">Approve & Post</button>
    				</div>
    			</form>
    		</div>

    	</div>
    </div>