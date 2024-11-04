<form action="{{route('reply.update',$ReplyNotes->id)  }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Quick Reply</strong>
                <textarea class="form-control" name="reply" placeholder="Quick Reply" required>{{$ReplyNotes->reply}}</textarea>
                @if ($errors->has('reply'))
                    <div class="alert alert-danger">{{$errors->first('reply')}}</div>
                @endif
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Model</strong>
                <select class="form-control" name="model" required>
                  <option value="">Select Model</option>
                  <option value="Approval Lead" {{ $ReplyNotes->model == 'Approval Lead' ? 'selected' : '' }}>Approval Lead</option>
                  <option value="Internal Lead" {{ $ReplyNotes->model == 'Internal Lead' ? 'selected' : '' }}>Internal Lead</option>
                  <option value="Approval Order" {{ $ReplyNotes->model == 'Approval Order' ? 'selected' : '' }}>Approval Order</option>
                  <option value="Internal Order" {{ $ReplyNotes->model == 'Internal Order' ? 'selected' : '' }}>Internal Order</option>
                  <option value="Approval Purchase" {{ $ReplyNotes->model == 'Approval Purchase' ? 'selected' : '' }}>Approval Purchase</option>
                  <option value="Internal Purchase" {{ $ReplyNotes->model == 'Internal Purchase' ? 'selected' : '' }}>Internal Purchase</option>
                </select>
                @if ($errors->has('model'))
                    <div class="alert alert-danger">{{$errors->first('model')}}</div>
                @endif
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Category</strong>
                <select class="form-control" name="category_id" required id="category_id_dropdown_edit">
                  @foreach ($reply_categories as $category)
                    <option value="{{ $category->id }}" {{ $category->id == $category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                  @endforeach
                </select>
                @if ($errors->has('model'))
                    <div class="alert alert-danger">{{$errors->first('model')}}</div>
                @endif
            </div>

            <div class="form-group">
                <strong>Sub category</strong>
                <select class="form-control" name="sub_category_id" id="subcategory_edit">
                    <option value="">Select Subcategory</option>
                    @foreach ($reply_sub_categories as $category)
                        <option value="{{ $category->id }}" {{ $category->id == $sub_category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                      @endforeach
                </select>
            </div>
        </div>
        <hr style=" width: 100%;">
        <div class="col-xs-12 col-sm-12 col-md-12 text-right">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Update</button>
        </div>

    </div>
</form>

<script type="text/javascript">
$('#category_id_dropdown_edit').on('change', function () {
    var categoryId = $(this).val();
    if (categoryId) {
        $.ajax({
            url: '/get-subcategories',
            type: 'GET',
            data: { category_id: categoryId },
            success: function (data) {
                $('#subcategory_edit').empty();
                $.each(data, function (key, value) {
                    $('#subcategory_edit').append('<option value="' + key + '">' + value + '</option>');
                });
            }
        });
    } else {
        $('#subcategory_edit').empty();
    }
});
</script>