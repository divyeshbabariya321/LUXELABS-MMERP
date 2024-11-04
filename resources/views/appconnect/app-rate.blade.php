@extends('layouts.app')

@section('title', 'App Store Ratings')


@section('styles')
<style type="text/css">
    .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
 #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
</style>
@endsection
@section('content')
 <div id="myDiv">
      <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-md-12">
           
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <script>
                    alert("{{Session::get('message')}}")
                </script>
            @endif
            <div class="row">
                <div class="col-lg-12 margin-tb mb-3">
        <h2 class="page-heading">IOS App Ratings Report </h2>
            </div>
          
     <br>
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col">
                    <form class="form-inline anr-search-handler" action="/appconnect/ratingsfilter" method="get">
                       
                        <div class="col-lg-2">
                            <label for="amount">App name:</label>
                            <input class="form-control" type="text" name="app_name" placeholder="Enter App Name">
                        </div>
                        <div class="col-lg-2">
                            <label for="date">From Date:</label>
                            <input class="form-control" type="date" name="fdate">
                        </div>
                      <div class="col-lg-2">
                            <label for="date">To Date:</label>
                            <input class="form-control" type="date" name="edate">
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="button">&nbsp;</label>
                                <button type="submit" style="display: inline-block;width: 10%"
                                    class="btn btn-sm btn-image">
                                    <img src="/images/search.png" style="cursor: default;">
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
      
        <div class="col-md-12">
 <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
                  <th width="5%">ID</th>
            <th width="5%">App Name</th>
            <th width="5%">Start Date</th>
            <th width="10%">End Date</th>
          <th width="10%">average</td> <th width="10%">total</td> <th width="10%">new_average</td> <th width="10%">new_total</td> <th width="10%">positive</td> <th width="10%">negative</td> <th width="10%">neutral</td> <th width="10%">new_positive</td> <th width="10%">new_negative</td> <th width="10%">new_neutral</th>
            
            
          </tr>
        </thead>

        <tbody>
   



@foreach ($reports as $report)
 
<tr>
<td>{{ $id+=1 }}</td>
<td>{{ $report->product_id }}</td>
<td>{{ $report->start_date }}</td>
<td>{{ $report->end_date }}</td>

<td>{{ $report->average}}</td> <td>{{ $report->total}}</td> <td>{{ $report->new_average}}</td> <td>{{ $report->new_total}}</td> <td>{{ $report->positive}}</td> <td>{{ $report->negative}}</td> <td>{{ $report->neutral}}</td> <td>{{ $report->new_positive}}</td> <td>{{ $report->new_negative}}</td> <td>{{ $report->new_neutral}}</td>

</tr>
@endforeach
 </tbody>
</table>   


    
        </div>
    </div>
    <div style="height: 600px;">
    </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/media-card.css') }} ">
@endsection

@section('scripts')

@endsection