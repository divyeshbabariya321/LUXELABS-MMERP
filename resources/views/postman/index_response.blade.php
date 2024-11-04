@extends('layouts.app')

@section('title', 'Post man Request')

@section('styles')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <h2 class="page-heading">Postman Response ({{$counter}})</h2>
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
  <div class="col-12" style="border: 1px solid;border-color: #dddddd;">
    <div class="table-responsive mt-2" style="overflow-x: auto !important;">

        @if ($message = Session::get('success'))
            <div class="col-lg-12">
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        
      <table class="table table-bordered text-nowrap">
        <thead>
          <tr> 
            <th>ID</th>
              <th>User Name</th>
              <th>Response</th>
              <th>Response code</th>
              <th>Request</th>
              <th>Parameters</th>
              <th>Date</th>
          </tr>
        </thead>

        <tbody>
            @foreach ($postHis as $key => $history)    
                <tr>
                    <td>{{$history->id}}</td>
                    <td>{{$history->userName}}</td>
                    <td class="expand-row-msg" data-name="response" data-id="{{$history->id}}">
                        <span class="show-short-response-{{$history->id}}">{{ Str::limit($history->response, 20, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-response-{{$history->id}} hidden">{{$history->response}}</span>
                    </td>
                    <td>{{$history->response_code}}</td>
                    <td class="expand-row-msg" data-name="request_url" data-id="{{$history->id}}">
                        <span class="show-short-request_url-{{$history->id}}">{{ Str::limit($history->request_url, 20, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-request_url-{{$history->id}} hidden">{{$history->request_url}}</span>
                    </td>
                    <td class="expand-row-msg" data-name="request_data" data-id="{{$history->id}}">
                        <span class="show-short-request_data-{{$history->id}}">{{ Str::limit($history->request_data, 20, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-request_data-{{$history->id}} hidden">{{$history->request_data}}</span>
                    </td>
                    <td>{{$history->created_at}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

      <div class="text-center">
        {!! $postHis->appends(Request::except('page'))->links() !!}
      </div>
    </div>
  </div>
  <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
  </div>
</div>

<div id="responseShowFullTextModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Full text view</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body responseShowFullTextBody">
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
 

<link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.min.css')}}">
{{-- <link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.css')}}"> --}}
@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>

<script type="text/javascript" src="{{ mix('webpack-dist/js/mock.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.dropdown.min.js') }} "></script>
{{-- <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.dropdown.js') }} "></script> --}}

</div>

<script type="text/javascript">
   

  $(document).on('click', '.expand-row-msg', function() {
    $('#responseShowFullTextModel').modal('toggle');
    $(".responseShowFullTextBody").html("");
    var id = $(this).data('id');
    var name = $(this).data('name');
    var full = '.expand-row-msg .show-full-' + name + '-' + id;
    var fullText = $(full).html();
    $(".responseShowFullTextBody").html(fullText);
  });

</script>
@endsection