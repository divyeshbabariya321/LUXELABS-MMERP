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
        <h2 class="page-heading">Category Groups ({{count($categoryAll)}})</h2>
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
  <div class="col-md-12 mt-3 pl-4 category">
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
              <button type="submit" class="btn btn-default ml-2 small-field-btn category" id="searchButton"><i class="fa fa-search"></i></button>
          </div>
      </div>
  </div>
  </div>
</div>  
<div class="row">
    <div class="col-md-12 ml-1 category">
    <div class="table-responsive mt-3">
      <table class="table table-new-references-group" id="table-new-references-group">
        <thead>
          <tr>
            <th>ID</th>
            <th>Category Groups</th>
            <th>Search %</th>            
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($categoryAll as $key=>$cron)
            <tr>
            	<td>
            		{{$key+1}}
            	</td>
              <td>
                {{ $cron[0] }}
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
                  <a href="javascript:;" data-id="{{$key+1}}" data-name="{{$cron[0]}}" class="btn btn-secondary btn-sm" onclick="redirectToPage(this)">View</a>
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
@endsection

@section('scripts')

    <script>
         $(document).ready(function() {

          $('#table-new-references-group').DataTable();
            // Handle button click
            $('#searchButton').on('click', function() {
              $("#loading-image").show();
              var keywordValue = $('#name').val().trim();
              var dropdownValue = $('#Searchdropdownsearch').val();
              if(keywordValue == ''){
                  toastr['error']('Sorry, Pleaase Enter keyword', 'error');
                  $("#loading-image").hide();
              }else{
                var redirectUrl = '/category/group/' + keywordValue + '/' + dropdownValue;
                window.location.href = redirectUrl;
              }
              
            });
        });
            
        function redirectToPage(element) {
          $("#loading-image").show();
          var dataId = element.getAttribute('data-id');
          var selectedValue = $('#Searchdropdown-'+dataId).val();
          var dataName = element.getAttribute('data-name');
          var redirectUrl = '/category/group/' + dataName + '/' + selectedValue;
          window.location.href = redirectUrl;
      }


    </script>
@endsection