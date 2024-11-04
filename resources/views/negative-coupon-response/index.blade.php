@extends('layouts.app')



@section('styles')
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection

@section('large_content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Negative Coupon Response</h2>
    </div>
</div>
<div class="col-lg-12 margin-tb">
    <form action="{{route('negative.coupon.response.search')}}" method="get" class="form-inline">
        <div class="col-12 col-lg-6 ">
            <div class="form-group">
            <b>Search : </b> 
                {{ html()->text('website', !empty(request()->website) ? request()->website : null)->class('form-control')->placeholder('Search Website') }}
            </div> &nbsp;&nbsp;&nbsp;
            <div class="form-group">
              {{ html()->text('response_text', !empty(request()->website) ? request()->response_text : null)->class('form-control')->placeholder('Select a Response') }}
          </div>&nbsp;&nbsp;&nbsp;
          <a href="{{ route('negative.coupon.response')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
          <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
          <br/><br/>
        </div>
        
        <div class="col-md-4 col-lg-2 col-xl-2">
            
        </div>
    </form>
</div>
<div class="text-center">
    {!! $negativeCouponsData->links() !!}
</div>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
            <th width="5%">Sr.No.</th>
            <th width="10%" class="text-center">Created Date</th>
            <th width="10%" class="text-center">Website</th>
            <th width="70%" class="text-center">Response</th>
            </tr>
        </thead>
        <tbody>
            @foreach($negativeCouponsData as $key => $value)
                @php
                   // $user = \App\User::Find($value['user_id']);
                @endphp
                <tr>
                    <td>{{$value->id}}</td>
                    <td>{{\Carbon\Carbon::parse($value->created_at)->format('d M, Y')}}</td>
                    <td>{{$value->website}}</td>
                    
                    <td class="response_{{$value->id}} show-more-content-btn" data-text="{{$value->response}}">
                      {{ strlen($value->response) > 150 ? substr($value->response, 0, 150) . '...' : $value->response }}
                  </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="text-center">
        {!! $negativeCouponsData->links() !!}
    </div>
</div>
<div id="show-more-content" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
          <div class="modal-body">
           </div> 
    </div>
  </div>
</div>
<script>
$(document).on("click",".show-more-content-btn",function (){
  var text  = $(this).data("text"); 
  $("#show-more-content").find(".modal-body").html(JSON.stringify(text));
  $("#show-more-content").modal("show");
});

</script>

@endsection