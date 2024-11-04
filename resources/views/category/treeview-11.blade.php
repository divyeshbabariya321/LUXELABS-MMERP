

    @extends('layouts.app')

    @section('content')

    <link rel="stylesheet" href="{{ mix('webpack-dist/css/treeview.css') }} ">
    <div class="panel panel-primary">
        <div class="panel-heading">Manage Category</div>
        <div class="panel-body">

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <h3>
                        <img style="width: 15px;" src="{{ asset('images/edit.png') }}" alt="">
                        <a href="{{ action([\App\Http\Controllers\CategoryController::class, 'mapCategory']) }}">Edit References</a>
                    </h3>
                    <h3>Category List</h3>
                    <ul id="tree1">
                        @foreach($categories as $category)
                            <li>
                                {{ $category->title }} ({{$category->id}}) <a href="javascript:;" data-id="{{ $category->id }}" data-name="{{ $category->title }}" data-simply-duty-code="{{ $category->simplyduty_code }}" class="edit-modal-window"><i class="fa fa-edit"></i></a> 
                                @if(count($category->childs))
                                    @include('category.manageChild',['childs' => $category->childs])
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6">
                        <h3>Add New Category</h3>

                        {{ html()->form('POST', route('add.category'))->open() }}

                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                            {{ html()->label('Title:') }}
                            {{ html()->text('title', old('title'))->class('form-control')->placeholder('Enter Title') }}
                            <span class="text-danger">{{ $errors->first('title') }}</span>
                        </div>

                        <div class="form-group {{ $errors->has('magento_id') ? 'has-error' : '' }}">
                            {{ html()->label('Magento Id:') }}
                            {{ html()->text('magento_id', old('magento_id'))->class('form-control')->placeholder('Enter Magento Id') }}
                            <span class="text-danger">{{ $errors->first('magento_id') }}</span>
                        </div>


                        <div class="form-group {{ $errors->has('show_all_id') ? 'has-error' : '' }}">
                            {{ html()->label('Show all Id:') }}
                            {{ html()->text('show_all_id', old('show_all_id'))->class('form-control')->placeholder('Enter Show All Id') }}
                            <span class="text-danger">{{ $errors->first('show_all_id') }}</span>
                        </div>

                        <div class="form-group">
                            {{ html()->label('Category Segment:') }}
                            {{ html()->select('category_segment_id', $category_segments, old('category_segment_id'))->class('form-control')->placeholder('Select Category Segment') }}
                        </div>

                        <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                            {{ html()->label('Category:') }}

                            {{ $allCategoriesDropdown }}
                            <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                        </div>


                        <div class="form-group">
                            <button class="btn btn-secondary">+</button>
                        </div>

                        {{ html()->form()->close() }}

                        <h3>Modify Category</h3>
                        @if ($message = Session::get('error-remove'))
                            <div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        @if ($message = Session::get('success-remove'))
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        {{ html()->form('POST', route('category.remove'))->open() }}
                        <div class="form-group">
                            {{ html()->label('Category:') }}
			                {{ $allCategoriesDropdownEdit }}
                            <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                        </div>
                        <div class="form-group">
                            <button id="btn-edit-cat" class="btn btn-image"><img src="/images/edit.png" /></button>
                            <button id="btn-delete-cat" class="btn btn-image"><img src="/images/delete.png" /></button>
                        </div>
                        {{ html()->form()->close() }}
                </div>
            </div>



            <div class="row">


            </div>

        </div>
    </div>
    <div id="edit-category-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title"></h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <form method="post" action="/category/save-form" id="category-save-form">
              @csrf
              <input type="hidden" name="id" value="" id="category-id"> 
              <input type="text" class="form-control" placeholder="Entery HS code" name="simplyduty_code" value="" id="simplyduty-code-field">
              <button type="button" class="btn btn-secondary btn-block mt-2" id="save-form">Save</button>`
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/treeview.js') }} "></script>
    <script type="text/javascript">
       $(document).on("click",".edit-modal-window",function(e) {
            var modal = $("#edit-category-modal");
            var $this = $(this);
            modal.find(".modal-title").html("Edit " + $this.data("name"));
            modal.find("#category-id").val($this.data("id"));
            modal.find("#simplyduty-code-field").val($this.data("simply-duty-code"));
            modal.modal("show");
       });

       $(document).on("click","#save-form",function() {
            var form = $(this).closest("form");
            $.ajax({
                type: 'POST',
                url: "/category/save-form",
                data: form.serialize(),
                beforeSend : function() {
                    $("#loading-image").show();
                },
                dataType:"json"
              }).done(function(response) {
                    $("#loading-image").hide();
                    toastr["success"](response.message);
              }).fail(function(response) {
                    $("#loading-image").hide();
                    toastr["error"]("Oops,something went wrong");
              });
       });

    </script>
    @endsection
