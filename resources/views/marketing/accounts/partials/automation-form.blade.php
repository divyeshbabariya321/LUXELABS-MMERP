<div id="automationForm" class="modal fade" role="dialog">
    	<div class="modal-dialog">

    		<!-- Modal content-->
    		<div class="modal-content">
    			<form action="{{ route('automation.form.store') }}" method="POST">
    				@csrf

    				<div class="modal-header">
    					<h4 class="modal-title">Instagram Automation form</h4>
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
    				</div>
    				<div class="modal-body"> 
                        <div class="form-group">
                            <strong>Post per day</strong>
                             <select class="form-control" name="posts_per_day">
                                <option value="1" {{ $automation_form['posts_per_day'] == 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $automation_form['posts_per_day'] == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $automation_form['posts_per_day'] == 3 ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $automation_form['posts_per_day'] == 4 ? 'selected' : '' }}>4</option>
                                <option value="5" {{ $automation_form['posts_per_day'] == 5 ? 'selected' : '' }}>5</option>
                                <option value="6" {{ $automation_form['posts_per_day'] == 6 ? 'selected' : '' }}>6</option>
                                <option value="7" {{ $automation_form['posts_per_day'] == 7 ? 'selected' : '' }}>7</option>
                                <option value="8" {{ $automation_form['posts_per_day'] == 8 ? 'selected' : '' }}>8</option>
                                <option value="9" {{ $automation_form['posts_per_day'] == 9 ? 'selected' : '' }}>9</option>
                                <option value="10" {{ $automation_form['posts_per_day'] == 10 ? 'selected' : '' }}>10</option>
                             </select> 
                        </div>
                        <div class="form-group">
                            <strong>Likes per day</strong>
                             <select class="form-control" name="likes_per_day">
                                <option value="1" {{ $automation_form['likes_per_day'] == 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $automation_form['likes_per_day'] == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $automation_form['likes_per_day'] == 3 ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $automation_form['likes_per_day'] == 4 ? 'selected' : '' }}>4</option>
                                <option value="5" {{ $automation_form['likes_per_day'] == 5 ? 'selected' : '' }}>5</option>
                                <option value="6" {{ $automation_form['likes_per_day'] == 6 ? 'selected' : '' }}>6</option>
                                <option value="7" {{ $automation_form['likes_per_day'] == 7 ? 'selected' : '' }}>7</option>
                                <option value="8" {{ $automation_form['likes_per_day'] == 8 ? 'selected' : '' }}>8</option>
                                <option value="9" {{ $automation_form['likes_per_day'] == 9 ? 'selected' : '' }}>9</option>
                                <option value="10" {{ $automation_form['likes_per_day'] == 10 ? 'selected' : '' }}>10</option>
                             </select> 
                        </div>
                        <div class="form-group">
                            <strong>Send requests per day</strong>
                             <select class="form-control" name="send_requests_per_day">
                                <option value="1" {{ $automation_form['send_requests_per_day'] == 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $automation_form['send_requests_per_day'] == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $automation_form['send_requests_per_day'] == 3 ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $automation_form['send_requests_per_day'] == 4 ? 'selected' : '' }}>4</option>
                                <option value="5" {{ $automation_form['send_requests_per_day'] == 5 ? 'selected' : '' }}>5</option>
                                <option value="6" {{ $automation_form['send_requests_per_day'] == 6 ? 'selected' : '' }}>6</option>
                                <option value="7" {{ $automation_form['send_requests_per_day'] == 7 ? 'selected' : '' }}>7</option>
                                <option value="8" {{ $automation_form['send_requests_per_day'] == 8 ? 'selected' : '' }}>8</option>
                                <option value="9" {{ $automation_form['send_requests_per_day'] == 9 ? 'selected' : '' }}>9</option>
                                <option value="10" {{ $automation_form['send_requests_per_day'] == 10 ? 'selected' : '' }}>10</option>
                             </select> 
                        </div>
                        <div class="form-group">
                            <strong>Accept requests per day</strong>
                             <select class="form-control" name="accept_requests_per_day">
                                <option value="1" {{ $automation_form['accept_requests_per_day'] == 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $automation_form['accept_requests_per_day'] == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $automation_form['accept_requests_per_day'] == 3 ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $automation_form['accept_requests_per_day'] == 4 ? 'selected' : '' }}>4</option>
                                <option value="5" {{ $automation_form['accept_requests_per_day'] == 5 ? 'selected' : '' }}>5</option>
                                <option value="6" {{ $automation_form['accept_requests_per_day'] == 6 ? 'selected' : '' }}>6</option>
                                <option value="7" {{ $automation_form['accept_requests_per_day'] == 7 ? 'selected' : '' }}>7</option>
                                <option value="8" {{ $automation_form['accept_requests_per_day'] == 8 ? 'selected' : '' }}>8</option>
                                <option value="9" {{ $automation_form['accept_requests_per_day'] == 9 ? 'selected' : '' }}>9</option>
                                <option value="10" {{ $automation_form['accept_requests_per_day'] == 10 ? 'selected' : '' }}>10</option>
                             </select> 
                        </div>
                        <div class="form-group">
                            <strong>Image Per Post</strong>
                             <select class="form-control" name="image_per_post">
                                <option value="1" {{ $automation_form['image_per_post'] == 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $automation_form['image_per_post'] == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $automation_form['image_per_post'] == 3 ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $automation_form['image_per_post'] == 4 ? 'selected' : '' }}>4</option>
                                <option value="5" {{ $automation_form['image_per_post'] == 5 ? 'selected' : '' }}>5</option>
                                <option value="6" {{ $automation_form['image_per_post'] == 6 ? 'selected' : '' }}>6</option>
                                <option value="7" {{ $automation_form['image_per_post'] == 7 ? 'selected' : '' }}>7</option>
                                <option value="8" {{ $automation_form['image_per_post'] == 8 ? 'selected' : '' }}>8</option>
                                <option value="9" {{ $automation_form['image_per_post'] == 9 ? 'selected' : '' }}>9</option>
                                <option value="10" {{ $automation_form['image_per_post'] == 10 ? 'selected' : '' }}>10</option>
                             </select> 
                        </div>
    				</div>
    				<div class="modal-footer">
    					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    					<button type="submit" class="btn btn-secondary">Store</button>
    				</div>
    			</form>
    		</div>

    	</div>
    </div>