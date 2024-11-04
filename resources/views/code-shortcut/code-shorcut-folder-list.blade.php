@extends('layouts.app')

@section('title', 'Codeshortcut')

@section('styles')
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">codeShortcut Folder</h2>
          </div>

          <div class="col-12 mb-3">
            <div class="pull-left">
            </div>
            <div class="pull-right">
            </div>
        </div>
    </div>
    <div class="row m-0">
      <div class="col-12">
     
      <button type="button" class="btn custom-button float-right mr-3 openmodelcodeaddfolder" data-toggle="modal" data-target="#addCodeShorCutFolder">Add Folder</button>
    </div>
     
    </div>
    
    <div class="row m-0" >
  <div class="col-12" style="border: 1px solid;border-color: #dddddd;">
	<div class="table-responsive mt-2"  style="overflow-x: auto !important;">
      <table class="table table-bordered text-nowrap">
        <thead>
          <tr>
            <th>ID</th>
            <th>Folder Name</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
			  @foreach ($folders as $key => $folder)
            <tr>
              <td>{{$folder->id}}</td>
              <td>{{$folder->name}}</td>
              <td>{{$folder->created_at}}</td>
              <td>
                <a class="btn btn-image edit-postman-folder-btn" data-id="{{ $folder->id }}"><img data-id="{{ $folder->id }}" src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
                <a class="btn delete-codeshortcut-folder-btn"  data-id="{{ $folder->id }}" href="#"><img  data-id="{{ $folder->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
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

<div id="addCodeShorCutFolder" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><span id="titleUpdate">Add</span> Code Shorcode Folder</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="codeShortFolderform" method="post">
                @csrf
                <input type="hidden" id="id" name="id" value=""/>
                <div class="form-row">
                  <div class="form-group col-md-12">
                    <label for="folder_name">Folder Name</label>
                    <input type="text" name="folder_name" value="" class="form-control" id="folder_name" placeholder="Enter folder name">
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

    $(document).on("click",".openmodelcodeaddfolder",function(e){
      $('#titleUpdate').html("Add");
        $('#postmanform').find("input[type=text], textarea").val("");
    });
    $(document).on("click",".delete-codeshortcut-folder-btn",function(e){
        e.preventDefault();
        if (confirm("Are you sure?")) {
          var $this = $(this);
          var id = $this.data('id');
          $.ajax({
            url: "/code-shortcuts/folder/delete",
            type: "delete",
            headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
            data:{
              id:id
            }
          }).done(function(response) {
            if(response.code = '200') {
              toastr['success']('CodeShortCut deleted successfully!!!', 'success'); 
              location.reload();
            } else {
              toastr['error'](response.message, 'error'); 
            }
          }).fail(function(errObj) {
            $('#loading-image').hide();
            $("#addCodeShorCutFolder").hide();
            toastr['error'](errObj.message, 'error');
          });
          }
      });
      $(document).on("click",".submit-folder-form",function(e){
        e.preventDefault();
        var $this = $(this);
        if($('#titleUpdate').text() == 'Add')
          $("#id").val("");
        $.ajax({
          url: "/code-shortcuts/folder/create",
          type: "post",
          data:$('#codeShortFolderform').serialize()
        }).done(function(response) {
          if(response.code = '200') {
            $('#loading-image').hide();
            $('#addCodeShorCutFolder').modal('hide');
            toastr['success']('Folder added successfully!!!', 'success'); 
            location.reload();
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           toastr['error'](errObj.message, 'error');
        });
      });

    $(document).on("click",".edit-postman-folder-btn",function(e){
        e.preventDefault();
        $('#titleUpdate').html("Update");
        var $this = $(this);
        var id = $this.data('id');
        $("#id").val(id);
        $.ajax({
          url: "/code-shortcuts/folder/edit",
          type: "post",
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          data:{
            id:id
          }
        }).done(function(response) {
          if(response.code = '200') {
            form = $('#codeShortFolderform');
            $.each(response.data, function(key, v) {
              if(key == 'name'){
                  form.find('[name="folder_name"]').val(v);
              } else if(form.find('[name="'+key+'"]').length){
                  form.find('[name="'+key+'"]').val(v);
              }      
            });
            $('#addCodeShorCutFolder').modal('show');
            toastr['success']('CodeShortcut edited successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#addCodeShorCutFolder").hide();
           toastr['error'](errObj.message, 'error');
        });
    });


  </script>
@endsection
