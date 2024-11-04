@extends('layouts.app')

@section('title', 'App Store Sales')


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
        <h2 class="page-heading">IOS App Sales Report </h2>
            </div>
          
     <br>
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col">
                    <form class="form-inline anr-search-handler" action="/appconnect/salesfilter" method="get">
                       
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
                <div class="col-12">
                  <div class="col-md-12">
                    <ul class="nav nav-tabs">
                        <li><button class="btn btn-xs btn-secondary my-3" style="color:white;" data-toggle="modal" data-target="#appsalescolumnvisibilityList"> Column Visiblity</button></li>
                    </ul>
                  </div>
                </div>
            </div>
      
            @php 
            $columns_array = [
                ['id' => 'product_id', 'name' => 'App Name', 'width' => '2%'],
                ['id' => 'start_date', 'name' => 'Start Date', 'width' => '2%'],
                ['id' => 'end_date', 'name' => 'End Date', 'width' => '2%'],
                ['id' => 'downloads', 'name' => 'Downloads', 'width' => '2%'],
                ['id' => 're_downloads', 'name' => 'Re Downloads', 'width' => '2%'],
                ['id' => 'uninstalls', 'name' => 'Uninstalls', 'width' => '2%'],
                ['id' => 'updates', 'name' => 'Updates', 'width' => '2%'],
                ['id' => 'returns', 'name' => 'Returns', 'width' => '2%'],
                ['id' => 'net_downloads', 'name' => 'Net Downloads', 'width' => '2%'],
                ['id' => 'promos', 'name' => 'Promos', 'width' => '2%'],
                ['id' => 'revenue', 'name' => 'Revenue', 'width' => '2%'],
                ['id' => 'returns_amount', 'name' => 'Returns Amount', 'width' => '2%'],
                ['id' => 'edu_downloads', 'name' => 'Edu Downloads', 'width' => '2%'],
                ['id' => 'gifts', 'name' => 'Gifts', 'width' => '2%'],
                ['id' => 'gift_redemptions', 'name' => 'Gift Redemptions', 'width' => '2%'],
                ['id' => 'edu_revenue', 'name' => 'Edu Revenue', 'width' => '2%'],
                ['id' => 'gross_revenue', 'name' => 'Gross Revenue', 'width' => '2%'],
                ['id' => 'gross_returns_amount', 'name' => 'Gross Returns Amount', 'width' => '2%'],
                ['id' => 'gross_edu_revenue', 'name' => 'Gross Edu Revenue', 'width' => '2%'],
                ['id' => 'business_downloads', 'name' => 'Business Downloads', 'width' => '2%'],
                ['id' => 'business_revenue', 'name' => 'Business Revenue', 'width' => '2%'],
                ['id' => 'gross_business_revenue', 'name' => 'Gross Business Revenue', 'width' => '2%'],
                ['id' => 'standard_downloads', 'name' => 'Standard Downloads', 'width' => '2%'],
                ['id' => 'standard_revenue', 'name' => 'Standard Revenue', 'width' => '2%'],
                ['id' => 'gross_standard_revenue', 'name' => 'Gross Standard Revenue', 'width' => '2%'],
                ['id' => 'app_downloadsapp_returns', 'name' => 'App Downloadsapp Returns', 'width' => '2%'],
                ['id' => 'iap_amount', 'name' => 'Iap Amount', 'width' => '2%'],
                ['id' => 'iap_returns', 'name' => 'Iap Returns', 'width' => '2%'],
                ['id' => 'subscription_purchases', 'name' => 'Subscription Purchases', 'width' => '2%'],
                ['id' => 'subscription_returns', 'name' => 'Subscription Returns', 'width' => '2%'],
                ['id' => 'app_revenue', 'name' => 'App Revenue', 'width' => '2%'],
                ['id' => 'app_returns_amount', 'name' => 'App Returns Amount', 'width' => '2%'],
                ['id' => 'gross_app_revenue', 'name' => 'Gross App Revenue', 'width' => '2%'],
                ['id' => 'gross_app_returns_amount', 'name' => 'Gross App Returns Amount', 'width' => '2%'],
                ['id' => 'iap_revenue', 'name' => 'Iap Revenue', 'width' => '2%'],
                ['id' => 'iap_returns_amount', 'name' => 'Iap Returns Amount', 'width' => '2%'],
                ['id' => 'gross_iap_revenue', 'name' => 'Gross Iap Revenue', 'width' => '2%'],
                ['id' => 'gross_iap_returns_amount', 'name' => 'Gross Iap Returns Amount', 'width' => '2%'],
                ['id' => 'subscription_revenue', 'name' => 'Subscription Revenue', 'width' => '2%'],
                ['id' => 'subscription_returns_amount', 'name' => 'Subscription Returns Amount', 'width' => '2%'],
                ['id' => 'gross_subscription_revenue', 'name' => 'Gross Subscription Revenue', 'width' => '2%'],
                ['id' => 'gross_subscription_returns_amount', 'name' => 'Gross Subscription Returns Amount', 'width' => '2%'],
                ['id' => 'pre_orders', 'name' => 'Pre Orders', 'width' => '2%']
            ];
            @endphp

        <div class="col-md-12">
 <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="5%">ID</th>
            @foreach($columns_array as $k=>$v)
              <th width="{{$v['width']}}" class="{{ (!empty($dynamicColumnsToShowb) && in_array($v['name'], $dynamicColumnsToShowb)) ? 'd-none' : ''}}" >{{$v['name']}}</th>
            @endforeach
          </tr>
        </thead>

        <tbody>
   



@foreach ($reports as $report)
 
<tr>
<td>{{ $id+=1 }}</td>
@foreach($columns_array as $k=>$v)
<td class="{{ (!empty($dynamicColumnsToShowb) && in_array($v['name'], $dynamicColumnsToShowb)) ? 'd-none' : ''}}">{{ $report->{$v['id']} }}</td>
@endforeach

</tr>
@endforeach
 </tbody>
</table>   


    
        </div>
    </div>
    <div style="height: 600px;">
    </div>
    </div>

    @include('appconnect.partials.app-sales-column-visibility-modal', ['columns_array' => $columns_array])

@endsection

@section('styles')
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/media-card.css') }} ">
@endsection

@section('scripts')

@endsection