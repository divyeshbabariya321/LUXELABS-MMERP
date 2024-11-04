@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')
<?php $base_url = URL::to('/');?>
    <div class="container_fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>Upteam logs</h3>
            </div>
            <div class="pull-left cls_filter_box pb-4 pt-4">
               <form class="form-inline" action="{{ route('product-inventory.upteam.logs') }}" method="GET">
                <span>From</span>
                  <div class='input-group date mr-3 ml-3' id='datetimepicker'>
                     <input name="from_date" type='text' class="form-control" placeholder="Search From Date & Time" id="from_date">
                     <span class="input-group-addon">
                     <span class="glyphicon glyphicon-calendar"></span>
                     </span>
                  </div>
                  <span>To</span>
                  <div class='input-group date ml-3' id='datetimepicker2'>
                     <input name="to_date" type='text' class="form-control" placeholder="Search To Date & Time" id="to_date">
                     <span class="input-group-addon">
                     <span class="glyphicon glyphicon-calendar"></span>
                     </span>
                  </div>
                  <div class="form-group ml-3 cls_filter_inputbox">
                     <input name="upteam_log" type="text" class="form-control" placeholder="Search log" id="log-search">
                  </div>
                  <button type="submit" style="padding: 5px;" class="btn btn-image"><img src="<?php echo $base_url;?>/images/filter.png"/></button>
               </form>
            </div>

            <div class="panel-body">
                <table class="table table-bordered table-hover" style="table-layout:fixed;">
                    <thead>
                    <tr>
						<th width="25%">Date</th>
						<th width="75%">log</th>
					</tr>
                    
                    </thead>
                    <tbody>
						@foreach($logs as $log)
							<tr>
								<td width="25%">{{ $log->created_at }}</td>
								<td width="75%">{{ $log->log_description }}</td>
							</tr>
						@endforeach
                    </tbody>
                </table>
				{{$logs->links()}}
            </div>
        </div>
    </div>


@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
  $('#datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss'
  });
  $('#datetimepicker2').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss'
  });

});

</script>
@endsection
