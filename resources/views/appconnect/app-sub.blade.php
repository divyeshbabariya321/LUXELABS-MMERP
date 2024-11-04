@extends('layouts.app')

@section('title', 'App Store Subscription')


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
        <h2 class="page-heading">IOS App Subscription Report </h2>
            </div>
          
     <br>
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col">
                    <form class="form-inline anr-search-handler" action="/appconnect/subscriptionfilter" method="get">
                       
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
             <th width="10%">Active Subscriptions</th> <th width="10%">Active Free Trials</th> <th width="10%">New Subscriptions</th> <th width="10%">Cancelled Subscriptions</th> <th width="10%">New Trials</th> <th width="10%">Trial Conversion Rate</th> <th width="10%">MRR</th> <th width="10%">Actual Revenue</th> <th width="10%">Renewals</th> <th width="10%">First Year Subscribers</th> <th width="10%">Non First Year Subscribers</th> <th width="10%">Reactivations</th> <th width="10%">Transitions Out</th> <th width="10%">Trial Cancellations</th> <th width="10%">Transitions in</th> <th width="10%">Activations</th> <th width="10%">Cancellations</th> <th width="10%">Trial conversions</th> <th width="10%">Churn</th> <th width="10%">Gross revenue</th> <th width="10%">Gross mrr</th> <th width="10%">Active grace</th> <th width="10%">New grace</th> <th width="10%">Grace drop off</th> <th width="10%">Grace recovery</th> <th width="10%">New trial grace</th> <th width="10%">Trial grace drop off</th> <th width="10%">Trial grace recovery</th> <th width="10%">Active trials</th> <th width="10%">Active discounted subscriptions</th> <th width="10%">All active subscriptions</th> <th width="10%">Paying subscriptions</th> <th width="10%">All Subscribers</th>
            
            
          </tr>
        </thead>

        <tbody>
   



@foreach ($reports as $report)
 
<tr>
<td>{{ $id+=1 }}</td>
<td>{{ $report->product_id }}</td>
<td>{{ $report->start_date }}</td>
<td>{{ $report->end_date }}</td>

  <td>{{ $report->active_subscriptions }}</td> <td>{{ $report->active_free_trials }}</td> <td>{{ $report->new_subscriptions }}</td> <td>{{ $report->cancelled_subscriptions }}</td> <td>{{ $report->new_trials }}</td> <td>{{ $report->trial_conversion_rate }}</td> <td>{{ $report->mrr }}</td> <td>{{ $report->actual_revenue }}</td> <td>{{ $report->renewals }}</td> <td>{{ $report->first_year_subscribers }}</td> <td>{{ $report->non_first_year_subscribers }}</td> <td>{{ $report->reactivations }}</td> <td>{{ $report->transitions_out }}</td> <td>{{ $report->trial_cancellations }}</td> <td>{{ $report->transitions_in }}</td> <td>{{ $report->activations }}</td> <td>{{ $report->cancellations }}</td> <td>{{ $report->trial_conversions }}</td> <td>{{ $report->churn }}</td> <td>{{ $report->gross_revenue }}</td> <td>{{ $report->gross_mrr }}</td> <td>{{ $report->active_grace }}</td> <td>{{ $report->new_grace }}</td> <td>{{ $report->grace_drop_off }}</td> <td>{{ $report->grace_recovery }}</td> <td>{{ $report->new_trial_grace }}</td> <td>{{ $report->trial_grace_drop_off }}</td> <td>{{ $report->trial_grace_recovery }}</td> <td>{{ $report->active_trials }}</td> <td>{{ $report->active_discounted_subscriptions }}</td> <td>{{ $report->all_active_subscriptions }}</td> <td>{{ $report->paying_subscriptions }}</td> <td>{{ $report->all_subscribers}}</td>

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