<div id="remark-create" class="modal fade in" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Add Remark</h4>
      <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <form  method="POST" id="remark-create-form" action="{{route('postman.addRemark')}}">
        @csrf
        @method('POST')
          <div class="modal-body">
            <div class="form-group">
              {{ html()->label('Name', 'remark')->class('form-control-label') }}
              {{ html()->text('remark')->class('form-control')->required() }}
              <input type="hidden" name="id" id="remarkId" value="">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary remark-save-btn">Save</button>
          </div>
        </div>
      </form>
    </div>

  </div>
</div>