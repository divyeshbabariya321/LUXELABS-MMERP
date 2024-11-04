

<form action="{{ route('content-management.social.post') }}" method="POST">
  @csrf

  <div class="modal-header">
    <h4 class="modal-title">Create social post</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <div class="modal-body">
    
    <div class="form-group">
      <strong>Choose media page</strong>
      <select class="form-control" name="store_website_id" id="" required>
          <option value="">Select</option>
          @foreach($query as $w)
          <option value="{{$w->id}}">{{$w->name}}</option>
          @endforeach
      </select>
    </div>

    <div class="form-group">
      <input type="hidden" value="{{ $imageUrl }}" name="imageurl">
    </div>

    <div class="form-group">
        <label for="">Message</label>
        <input type="text" name="message" class="form-control" placeholder="Type your message">
        @if ($errors->has('message'))
        <p class="text-danger">{{$errors->first('message')}}</p>
        @endif
    </div>
    
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-secondary">Post</button>
  </div>
</form>