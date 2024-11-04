@extends('layouts.app')

@section('link-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.jqueryui.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/scroller/2.0.1/css/scroller.jqueryui.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection
@section('content')

<style>
table tbody tr td{
  word-wrap: break-word;
}
</style>

<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Keyword Response Logs</h2>
    </div>
</div>
 <div class="col-md-12 ">
    <form action="{{ route('keywordreponse.logs') }}" method="get"style="margin-bottom: -30px;">
        <div class="row mb-5">
            <div class="col-md-3">
                <input type="text" name="keyword" class="form-control" id="keyword" placeholder="Enter Keyword" value="">
            </div>
            <div class="col-md-3">
                <div class='input-group date' id='keyword-due-datetime'>
                                
                    <input type='text' class="form-control input-sm" name="keyword_duedate" id="keyword_duedate"  />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="col-md-1">
                <button class="btn btn-image" ><img src="/images/filter.png"></button>
                <a style="color: #000;" href="{{ route('keywordreponse.logs') }}"><i class="fa fa-refresh" aria-hidden="true"></i></a>
            </div>
        </div>
    </form>
</div>
  <div class="row ">
    <div class="col-lg-12 p-5"style="padding-top: 10px !important;">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" style="width:100%; table-layout:fixed;" id="keywordassign_table">
            <thead>
                <tr>
                    <th width="5%">Model</th>
                    <th width="4%">Model Id</th>
                    <th width="4%">Keyword</th>
                    <th width="6%">Keyword Match</th>
                    <th width="6%">Message Send Id</th>
                    <th width="30%">Comment</th>
                    <th width="7%">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($keywordlogs as $key => $value)
                    <tr>
                        <td class="Website-task"title="{{$value->model}}">{{$value->model}}</td>
                        <td>{{$value->model_id}}</td>
                        <td class="Website-task" title="{{$value->keyword}}">{{$value->keyword}}</td>
                        <td class="Website-task"title="{{$value->keyword_match}}">{{$value->keyword_match}}</td>
                        <td>{{$value->message_sent_id}}</td>
                        <td class="Website-task" title="{{ $value->comment }}">{{ $value->comment }}</td>
                        <td>{{$value->created_at}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
  </div>

    <div class="row">
        <div class="col-md-12 text-center">
            {{ $keywordlogs->appends($request->except('page'))->links() }}.
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script type="text/javascript">
   
   $(document).ready(function () {

        $('#keyword-due-datetime').datetimepicker({
            format: 'YYYY-MM-DD'
        }); 
   });
</script>
@endsection