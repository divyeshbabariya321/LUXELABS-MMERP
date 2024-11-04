<div class="modal fade" id="setCron" tabindex="-1" role="dialog" aria-labelledby="translateModel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <form id="create_mailable" action="{{ url('gtmetrix/savegtmetrixcron') }}" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Gtmetrix</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning new-mailable-alerts d-none" role="alert"></div>
                
                <div class="form-group">
                <label >Select website </label>
                    <input name="website" class="form-control" required>
                    
                </div>
                <div class="form-group">
                <label >Select Status</label>
                    <select name="status"  class="form-control" required>
                        <label >Select Status</label>
                        <option value="not_queued">not_queued</option>
                       
                    </select>
                </div>

                <div class="form-group">
                <label >Select Store View </label>
                    <select name="store_view"  class="form-control" required>
                    <option value="0">Select Store View </option>
                        @foreach( $storeViewList as $s)
                        <option value="{{$s->id}}">{{$s->code}}</option>
                        @endforeach
                       
                    </select>
                </div>


               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </div>
    </form>
  </div>
</div>