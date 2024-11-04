@extends('layouts.app')

@section('title', 'User Timesheet')

@section("styles")
<!-- START - Purpose : Add CSS - DEVTASK-4289 -->
<style type="text/css">
  table tr td{
    overflow-wrap: break-word;
  }
    .page-note{
        font-size: 14px;
    }
    .flex{
        display: flex;
    }
</style>
<!-- END - DEVTASK-4289 -->
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">User Timesheet</h2>
    </div>

    <div class="col-md-12">
        <!-- START - Purpose : Get Page Note - DEVTASK-4289 -->
        <form method="get" action="{{ route('user-management.user-schedules.report') }}">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" name="user_id">
                        @if (!Auth::user()->isAdmin()) 
                            @foreach (\App\User::where('id', Auth::user()->id)->get() as $user)
                                <option value="{{ $user->id }}" <?php if($user->id == Request::get('user_id')) echo "selected"; ?>>{{ $user->name }}</option>
                            @endforeach
                        @else
                            <option value="">Select user</option>
                            @foreach (\App\User::get() as $user)
                                <option value="{{ $user->id }}" <?php if($user->id == Request::get('user_id')) echo "selected"; ?>>{{ $user->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="month" id="month">
                        <option value="">Select Month</option>
                        @foreach($months as $key => $month)
                            <option value="{{$key}}" <?php if($key == Request::get('month')) echo "selected"; ?>>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="year" id="year">
                        <option value="">Select Year</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" <?php if($year == Request::get('year')) echo "selected"; ?>>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-image" onclick="setHiddenValue('submit')">
                        <img src="/images/search.png" style="cursor: default;">
                    </button>
                    <a href="{{route('user-management.user-schedules.report')}}" type="button" class="btn btn-image" id=""><img src="/images/resend2.png"></a>

                    <input type="hidden" name="clickedButton" id="clickedButton" value="">

                    <!-- @if(!empty(Request::get('user_id')) && !empty(Request::get('month')) && !empty(Request::get('year')))
                        <button type="submit" class="btn btn-sm btn-primary" onclick="setHiddenValue('export')">
                            <i class="fa fa-download" aria-hidden="true"></i> Export
                        </button>
                    @endif -->
                </div>
            </div>
        </form>
    </div>
    <!-- END - DEVTASK-4289 -->
 
    <div class="col-md-12">
        <div class="table-responsive">
            <table cellspacing="0" role="grid" class="table table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th width="5%">S Number</th>
                        <th width="10%">Date</th>
                        <th width="10%">Time Dr. Hours</th>
                        <th width="10%">Task Id</th>
                        <th width="20%">Estimated Time</th>
                        <th width="20%">Time Taken</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        use Carbon\CarbonInterval;
                    @endphp
                    @if(!empty($dataMainArray))
                        <?php $totalMinutes = 0; ?>
                        @foreach ($dataMainArray as $k => $data)
                            <tr>
                                <td rowspan="{{count($data)+1}}">{{ $loop->iteration }}</td>
                                <td rowspan="{{count($data)+1}}">{{$k}}</td>
                                <td rowspan="{{count($data)+1}}">-</td>
                            </tr>
                            @if(!empty($data))      
                                <?php $subTotalMinutes = 0; ?>

                                @foreach ($data as $key => $dataValue)    
                                    <tr>                                            
                                        <td style="border-left: 1px solid #ddd !important;">{{$dataValue->task_type}} - {{$dataValue->id}}</td>
                                        <td>
                                            @if(!empty($dataValue->totalTime))
                                                @php
                                                $intervaltT = CarbonInterval::minutes($dataValue->totalTime);

                                                // Get the formatted duration as hours and minutes
                                                echo $intervaltT->cascade()->forHumans(['parts' => 2]);

                                                @endphp
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($dataValue->totalMinutes))
                                                @php
                                                $interval = CarbonInterval::minutes($dataValue->totalMinutes);

                                                // Get the formatted duration as hours and minutes
                                                echo $interval->cascade()->forHumans(['parts' => 2]);

                                                @endphp
                                                <?php $subTotalMinutes += $dataValue->totalMinutes; ?>
                                            @endif</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                                <?php $totalMinutes += $subTotalMinutes; ?>
                            @endif                                
                        @endforeach
                        <tr>
                            <td colspan="4"></td>
                            <td><b>Total</b></td>
                            <td>
                                @php
                                $intervalT = CarbonInterval::minutes($totalMinutes);

                                // Get the formatted duration as hours and minutes
                                echo $intervalT->cascade()->forHumans(['parts' => 2]);

                                @endphp
                            </td>
                            <td></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div> 
    </div>
</div> 
@endsection

@section('scripts')
<script type="text/javascript">
function setHiddenValue(value) {
    document.getElementById('clickedButton').value = value;
}
</script>
@endsection
