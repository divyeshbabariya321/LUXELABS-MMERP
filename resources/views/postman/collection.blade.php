@extends('layouts.app')

@section('title', 'Post man Request')

@section('styles')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <h2 class="page-heading">Postman Collection</h2>
  </div>

  <div class="col-12 mb-3">
    <div class="pull-left">
    </div>
    <div class="pull-right">
      <!-- <a title="add new domain" class="btn btn-secondary add-new-btn">+</a> -->
    </div>
  </div>
</div>
<div class="row m-0">
  <div class="col-12">
    <form class="form-inline" action="/postman/folder/search" method="GET">
      <div class="form-group">
        <div class="input-group">
          <?php $folderNamrArr = []; ?>
          @foreach ($folders as $key => $folder)
          <?php array_push($folderNamrArr, $folder->name); ?>
          @endforeach
          <?php $folderNamrArr = array_unique($folderNamrArr); ?>
          <select name="folder_name" class="form-control" id="folder_name">
            <option value="">--Select Request Name--</option>
            @foreach ($folderNamrArr as $key => $folderName)
            <?php $selected  = '';
            if ($folderName == request('folder_name')) {
              $selected  = 'selected = "selected"';
            }
            ?>
            <option {{$selected}} value="{{$folderName}}">{{$folderName}}</option>
            @endforeach
          </select>
          
        </div>
      </div>
      <div class="col">
        <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
        <a href="/postman/folder" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
      </div>
    </form>
    <button type="button" class="btn custom-button float-right mr-3 openmodeladdpostmanfolder" data-toggle="modal" data-target="#addPostmanFolder">Add Collection</button>
  </div>

</div>


</br>
<div class="row m-0">
  <div class="col-12" style="border: 1px solid;border-color: #dddddd;">
    <div class="table-responsive mt-2" style="overflow-x: auto !important;">
      <table class="table table-bordered text-nowrap">
        <thead>
          <tr>
            <th>ID</th>
            <th>Collection Name</th>
            <th>Workspace Name</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($folders as $key => $folder)
          <tr>
            <td>{{$folder->id}}</td>
            <td>{{$folder->collection_name}}</td>
            <td>{{$folder->workspace_name}}</td>

            <td>{{$folder->created_at}}</td>
            <td>
              <button class="btn btn-default btn-image edit-postman-folder-btn" data-id="{{ $folder->collection_id }}"><i class="fa fa-pencil"></i></a>
                <button class="btn btn-default delete-postman-folder-btn" data-id="{{ $folder->id }}"><i class="fa fa-trash"></i></a>
                  <button class="btn btn-default view-postman-collection-folder-btn" data-id="{{ $folder->id }}"><i class="fa fa-eye"></i></button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="text-center">
        {!! $folders->appends(Request::except('page'))->links() !!}
      </div>
    </div>
  </div>
  <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
  </div>
</div>
@endsection

<div id="addPostmanFolder" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><span id="titleUpdate">Add</span> Postman Collection</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="postmanFolderform" method="post">
              @csrf
              <input type="hidden" id="collection_id" name="collection_id" value="" />
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="folder_name">Collection Name</label>
                  <input type="text" name="collection_name" value="" class="form-control" id="collection_name" placeholder="Enter collection name">
                </div>
                <div class="form-group col-md-12">
                  <label for="folder_name">Collection Description</label>
                  <input type="text" name="collection_description" value="" class="form-control" id="collection_description" placeholder="Enter collection description">
                </div>
                <div class="form-group col-md-12">
                  <label for="workspace_id">Workspace</label>
                  <select id="workspace_id" name="workspace_id" class="form-control">
                    @foreach($workspaces as $key => $value)
                    <option value="{{ $value->workspace_id }}">{{ $value->workspace_name }}</option>
                    @endforeach
                  </select>

                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-secondary submit-folder-form">Save</button>
          </div>

        </div>

      </div>
    </div>
  </div>
</div>

<div id="viewCollectionFolder" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">View Collection Folder</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="folder_collection_id" name="folder_collection_id">

        <button type="button" class="btn btn-default float-right mb-4" data-add-collection-folder><i class="fa fa-plus"></i> Add Folder</button>
        <div class="table-responsive mt-4" style="overflow-x: auto !important;">
          <table class="table table-bordered text-nowrap" id="collection_folder_table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Folder Name</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="addCollectionFolder" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Collection Folder</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="collectionFolderform" method="post">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="add_folder_collection_id" name="collection_id" value="" />
          <input type="hidden" id="folder_id" name="folder_id" value="" />

          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="folder_name">Folder Name</label>
              <input type="text" name="folder_name" value="" class="form-control" id="folder_name" placeholder="Enter folder name">
            </div>
          </div>
        </div>
    
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="view-domain" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="view-domain-content">

      </div>
    </div>
  </div>
</div>

@section('scripts')
<script type="text/javascript">
  // $('ul.pagination').hide();
  //   $('.infinite-scroll').jscroll({
  //     autoTrigger: true,
  //     // debug: true,
  //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
  //     padding: 0,
  //     nextSelector: '.pagination li.active + li a',
  //     contentSelector: 'div.infinite-scroll',
  //     callback: function () {
  //       $('ul.pagination').first().remove();
  //       $('ul.pagination').hide();
  //     }
  // });

  $(document).on("click", ".openmodeladdpostmanfolder", function(e) {
    $('#titleUpdate').html("Add");
    $('#postmanform').find("input[type=text], textarea").val("");
  });
  $(document).on("click", ".delete-postman-folder-btn", function(e) {
    e.preventDefault();
    if (confirm("Are you sure?")) {
      var $this = $(this);
      var id = $this.data('id');
      $.ajax({
        url: "/postman/folder/delete",
        type: "delete",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          id: id
        }
      }).done(function(response) {
        if (response.code = '200') {
          toastr['success']('Postman deleted successfully!!!', 'success');
          location.reload();
        } else {
          toastr['error'](response.message, 'error');
        }
      }).fail(function(errObj) {
        $('#loading-image').hide();
        $("#addPostmanFolder").hide();
        toastr['error'](errObj.message, 'error');
      });
    }
  });
  $(document).on("click", ".submit-folder-form", function(e) {
    e.preventDefault();
    var $this = $(this);
    if (!$("#collection_name").val()) {
      toastr['error']('Please enter collection name.', 'error');
      return;
    }
    if (!$("#collection_description").val()) {
      toastr['error']('Please enter collection description.', 'error');
      return;
    }
    if (!$("#workspace_id").val()) {
      toastr['error']('Please select workspace.', 'error');
      return;
    }
    if ($('#titleUpdate').text() == 'Add')
      $("#id").val("");
    $.ajax({
      url: "/postman/collection/create",
      type: "post",
      data: $('#postmanFolderform').serialize()
    }).done(function(response) {
      if (response.code === 200) {
        $('#loading-image').hide();
        $('#addPostmanFolder').modal('hide');
        toastr['success']('Folder added successfully!!!', 'success');
        location.reload();
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      //$("#addMail").hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".edit-postman-folder-btn", function(e) {
    e.preventDefault();
    $('#titleUpdate').html("Update");
    var $this = $(this);
    var id = $this.data('id');
    $("#id").val(id);
    $.ajax({
      url: "/postman/collection/edit/",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        form = $('#postmanFolderform');
        $.each(response.data, function(key, v) {
          if (key == 'name') {
            form.find('[name="folder_name"]').val(v);
          } else if (form.find('[name="' + key + '"]').length) {
            form.find('[name="' + key + '"]').val(v);
          }
        });
        $("#collection_description").val(response.data.description);
        $('#addPostmanFolder').modal('show');
        //toastr['success']('Postman edited successfully!!!', 'success');

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      $("#addPostmanFolder").hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".view-postman-collection-folder-btn", function() {
    // e.preventDefault();

    var collectionId = $(this).attr('data-id');
    $('#folder_collection_id').val(collectionId);

    getCollectionFolders();
  });

  function getCollectionFolders(){
    $('#collection_folder_table tbody').empty();
    var collectionId = $('#folder_collection_id').val();

    $.ajax({
      url: "/postman/collection/folders",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        collectionId: collectionId
      }
    }).done(function(folders) {
        $('#loading-image').hide();

        if(folders.length > 0){
          $.each(folders, function(k,v){
            var folderHtml = '<tr>';
            folderHtml += '<td>'+(k+1)+'</td>';
            folderHtml += '<td>'+v.folder_name+'</td>';
            folderHtml += '<td>'+v.created_at+'</td>';
            folderHtml += '<td><button type="btn btn-default" data-collection-folder-edit data-id="'+v.id+'" data-name="'+v.folder_name+'"><i class="fa fa-edit"></i></button><button type="btn btn-default" data-collection-folder-delete data-id="'+v.id+'"><i class="fa fa-trash"></i></button></td>';
            folderHtml += '</tr>';

            $('#collection_folder_table tbody').append(folderHtml);
          });
        }

        $('#viewCollectionFolder').modal('show');
    }).fail(function(errObj) {
      $('#loading-image').hide();
    });
  }

  $('[data-add-collection-folder]').click(function (){
    var collectionId = $('#folder_collection_id').val();

    $('#add_folder_collection_id').val(collectionId);
    $('#folder_id').val('');
    $('#addCollectionFolder').modal('show');
  });

  $("#collectionFolderform").submit(function(e){
    e.preventDefault();

    $.ajax({
      url: "/postman/collection/folder/upsert",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: $('#collectionFolderform').serialize()
    }).done(function(response) {
      if (response.code = '200') {
        getCollectionFolders();

        $('#addCollectionFolder').modal('hide');
      
        toastr['success']('Success! Collection has been updated.', 'success');
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", "[data-collection-folder-edit]", function(e) {
    var folderId = $(this).attr('data-id');
    var folderName = $(this).attr('data-name');
    var collectionId = $('#folder_collection_id').val();

    $('#folder_id').val(folderId);
    $('#folder_name').val(folderName);
    $('#add_folder_collection_id').val(collectionId);
    $('#addCollectionFolder').modal('show');
  });

  $(document).on("click", "[data-collection-folder-delete]", function(e) {
    var folderId = $(this).attr('data-id');
    var collectionId = $('#folder_collection_id').val();

    $.ajax({
      url: "/postman/collection/folder/delete",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        folder_id: folderId,
        collection_id: collectionId
      }
    }).done(function(folders) {
      if (response.code = '200') {
        getCollectionFolders();

        toastr['success']('Success! Collection has been updated.', 'success');
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });
</script>
@endsection