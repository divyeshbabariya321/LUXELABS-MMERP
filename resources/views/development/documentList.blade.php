@extends('layouts.app')

@section('title', 'Document Upload list')

@section("styles")
  <style>
    #coupon_rules_table_length select, input{
      height: 14px;
    }
  </style>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection

@section('content')
<div class="col-md-12">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Document List({{$totalCount}})</h2>
        </div>
    </div>
    <div class="row mb-3">
      <div class="col-md-10 col-sm-12">
        <form action="{{ route('development.document.list') }}" method="GET" class="form-inline align-items-start" id="searchForm">
          <div class="row full-width" style="width: 100%;">
            <div class="col-md-2 col-sm-12 pd-2">
              <div class="form-group cls_task_subject">
                <input type="text" name="term_id" placeholder="Search Task Id / Dev Task Id"  class="form-control input-sm" value="{{ !empty($_GET['term_id'])? $_GET['term_id'] : '' }}">
              </div>
            </div>
            <div class="col-md-2 col-sm-12 pd-2">
              <div class="form-group cls_task_subject">
                <input type="text" class="form-control input-sm" name="task_subject" placeholder="Task Subject" id="task_subject" value="{{ !empty($_GET['task_subject'])? $_GET['task_subject'] : '' }}" />
                @if ($errors->has('task_subject'))
                  <div class="alert alert-danger">{{$errors->first('task_subject')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-3 col-sm-12">
              <div class="form-group mr-3">
                <select class="form-control" name="user_id">
                <option value="" selected disabled>select</option>
                  
                  @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{!empty($_GET['user_id']) ? $user->id == $_GET['user_id'] ? 'selected' : ''  : '' }}>{{ $user->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-3 col-sm-12 pr-0">
              <div class='input-group date mr-3' id="date-datetime">
                <input type='date' value="{{ !empty($_GET['date'])? $_GET['date'] : '' }}" class="form-control" name="date" />

               
              </div>
            </div>
            <div class="pl-0 pt-2"><button class="btn btn-image"><a style="color:#212529" href="{{ route('development.document.list') }}" <i class="fa fa-refresh" aria-hidden="true"></i></a></button></div>
            <div class="pl-0 pt-2"><button type="submit" class="btn btn-image"><img src="{{asset('/images/search.png')}}" /></button></div>
          </div>
        </form>
      </div>
    </div>

   

   

    <div class="tab-content ">
      <div class="tab-pane active mt-3" id="pending-tasks">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
            <tr>
              <th width="2%">Id</th>
              <th width="2%">Type</th>
              <th width="8%">Subject</th>
              <th width="8%">Description</th>
              <th width="5%">Created By</th>
              <th width="5%">Created At</th>
             
            </tr>

         
           @foreach($uploadDocData as $key=> $value)
            <tr>
            <td>{{$value->developer_task_id}}</td>
            <td>{{$value->type}}</td>
            <td> 
              @php
              $limitedTextSubject = substr($value->subject, 0, 50); 
              @endphp

              @if(strlen($value->subject) > 50)

              <span class="more-detail"  data-id="{{$key}}" >{{$limitedTextSubject}} <button class="btn btn-sm">More Detail..</button></span>

              @else
              <span  >{{$value->subject}}</span>

              @endif
              <span id="showdata_{{$key}}" style="display:none;">{{$value->subject}}</span>
            
            </td>
            <td>
              @php
              $link = asset($value->disk.'/'.$value->directory.'/'.$value->filename.'.'.$value->extension);
              $fileName = "$value->filename.$value->extension";

              
              @endphp

              @php
              $limitedTextDesc = substr($value->description, 0, 50); 
              @endphp

              @if(strlen($value->description) > 50)

              <span class="more-detail-desc"  data-id="{{$key}}" >{{$limitedTextDesc}} <button class="btn btn-sm">More Detail..</button></span>

              @else
              <span>{{$value->description}}</span>

              @endif
              <span id="showdatadesc_{{$key}}" style="display:none; text-align:justify">{{$value->description}}</span>

             

              <br>

              <a style="color:#212529" href="{{$link}}" target="_blank">{{$fileName}}</a></td>
            </td>
            <td>{{$value->username}}</td>
            <td>{{$value->created_at}}</td>
            
            
            </tr>
           @endforeach
           

         
           
                   
          </table>

        </div>

          {{ $uploadDocData->links() }}
       
      </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
  $(document).on('click','.more-detail',function(){
    var id =$(this).attr('data-id');
    $('#showdata_'+id).show();
    obj = $(this).closest('tr');
    obj.find('.more-detail').hide();
  });

  $(document).on('click','.more-detail-desc',function(){
    var id =$(this).attr('data-id');
    $('#showdatadesc_'+id).show();
    obj = $(this).closest('tr');
    obj.find('.more-detail-desc').hide();
  });

  
  
  </script>  
@endsection

