@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
@endsection

<style type="text/css">
  #loading-image {
          position: fixed;
          top: 50%;
          left: 50%;
          margin: -50px 0px 0px -50px;
          z-index: 60;
      }
      .select2{
          width: 100% !important;
      }
</style>
@section('content')
<div id="myDiv">
  <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
</div>
<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">Compositions Groups ({{count($listcompostions)}})</h2>
    </div>
    @if ($message = Session::get('success'))
    <div class="col-md-12">
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    </div>
    @endif
</div>
<div class="row change-data">
  <div class="col-md-12 mt-3 pl-4 compositions">
    <div class="col-md-12">
      <div class="col-md-12 d-flex align-items-center p-1">
          <div class="col-md-3 p-1">
              <div class="form-group change-data">
                  <input type="text" name="keyword" class="form-control" id="name" placeholder="Enter keyword" value="{{ old('keyword') ? old('keyword') : request('keyword') }}" />
              </div>
          </div>
          <div class="col-md-1 p-1">
              <div class="form-group change-data">
                <select name="Searchdropdown" id="Searchdropdownsearch" class="form-control change-list-compostion select2">
                  <option value="0.9">90(%)</option>
                  <option value="0.8">80(%)</option>
                  <option value="0.7">70(%)</option>
                  <option value="0.6">60(%)</option>
                  <option value="0.5">50(%)</option>
                </select>
              </div>
          </div>
          <div class="form-group">
              <button type="submit" class="btn btn-default ml-2 small-field-btn compositions " id="searchButton"><i class="fa fa-search"></i></button>
          </div>
      </div>
  </div>
  </div>
</div>  
<div class="row">
    <div class="col-md-12 ml-1 compositions">
    <div class="table-responsive mt-3">
      <table class="table table-compositions" id="table-compositions">
        <thead>
          <tr>
            <th>ID</th>
            <th>Compositions Groups</th>
            <th>Search %</th>            
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($listcompostions as $key=>$cron)
            <tr>
            	<td>
            		{{$key+1}}
            	</td>
              <td>
                {{ $cron }}
              </td>
              <td>
                <select name="Searchdropdown" id="Searchdropdown-{{$key+1}}" class="form-control change-list-compostion select2">
                  <option value="0.9">90(%)</option>
                  <option value="0.8">80(%)</option>
                  <option value="0.7">70(%)</option>
                  <option value="0.6">60(%)</option>
                  <option value="0.5">50(%)</option>
                </select>
              </td>
              <td>
                  <a href="javascript:;" data-id="{{$key+1}}" data-name="{{$cron}}" class="btn btn-secondary btn-sm" onclick="redirectToPage(this)">View</a>
                | <a href="javascript:;" data-r="{{ $cron }}" data-toggle="modal" data-target="#deleteConfirmationModal" class="btn btn-secondary btn-sm btn-run-command">Delete</button></a>
            </tr>
            @endforeach
        </tbody>
      </table>
    </div>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Confirmation</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            Are you sure you want to delete this item : <strong><span id="cron-value"></span></strong>?
          </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" onclick="deleteItem()">Delete</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')

    <script>
      $(document).ready(function() {
      // Handle button click
      $('#searchButton').on('click', function() {
        $("#loading-image").show();
        var keywordValue = $('#name').val().trim();
        var dropdownValue = $('#Searchdropdownsearch').val();
        if(keywordValue == ''){
            toastr['error']('Sorry, Pleaase Enter keyword', 'error');
            $("#loading-image").hide();
        }else{
          var redirectUrl = '/compositions/group/' + dropdownValue +'?search='+keywordValue;
          window.location.href = redirectUrl;
        }
        
      });
    });
            
              function redirectToPage(element) {
                $("#loading-image").show();
                var dataId = element.getAttribute('data-id');
                var selectedValue = $('#Searchdropdown-'+dataId).val();
                var dataName = element.getAttribute('data-name');
                var redirectUrl = '/compositions/group/' + selectedValue +'?search='+dataName;
                window.location.href = redirectUrl;
            }

            function deleteItem() {
                // Access the value of 'data-r' attribute
                var nameValue = $('#deleteConfirmationModal').data('r');
                $.ajax({
                type: 'POST', // or 'DELETE' depending on your server-side route
                url: '/compositions/delete-composition',
                beforeSend: function() {
                    $("#loading-image").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    name: nameValue,
                },
                success: function(response) {
                    toastr["success"]('Item deleted successfully', "Message");
                    $("#loading-image").hide();
                    console.log('Item deleted successfully');
                    location.reload();
                },
                error: function(error) {
                    $("#loading-image").hide();
                    console.error('Error deleting item', error);
                }
                });
                $('#deleteConfirmationModal').modal('hide');
            }

          $('#deleteConfirmationModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var cronValue = button.data('r');
            $('#cron-value').text(cronValue);

            $('#deleteConfirmationModal').data('r', cronValue);
        });

        $(document).ready(function() {
            $('#table-compositions').DataTable();
        });

    </script>
@endsection
