@extends('layouts.app')

@section('title', 'Problem List')

@section('large_content')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-typeahead.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style type="text/css">
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
	.nav-item a{
		color:#555;
	}
  Route::resource('zabbix', 'ZabbixController');
	a.btn-image{
		padding:2px 2px;
	}
	.text-nowrap{
		white-space:nowrap;
	}
	.search-rows .btn-image img{
		width: 12px!important;
	}
	.search-rows .make-remark
	{
		border: none;
		background: none
	}
  .table-responsive select.select {
    width: 110px !important;
  }

  @media (max-width: 1280px) {
    table.table {
        width: 0px;
        margin:0 auto;
    }

    /** only for the head of the table. */
    table.table thead th {
        padding:10px;
    }

    /** only for the body of the table. */
    table.table tbody td {
        padding:10 px;
    }

    .text-nowrap{
      white-space: normal !important;
    }
  }

</style>
@endsection


<div class="container " style="max-width: 100%;width: 100%;">
    <div class="row">
        <div class="col-md-12 p-0">
            <h2 class="page-heading">Problem List(<span id="ads_account_count">{{ $totalentries }}</span>)</h2>
        </div>
    </div>

    <div class="col-md-12">
        <form action="{{ route('zabbix.problem') }}" method="GET">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-2 pl-0 pr-3">
                        <select class="form-control select-multiple" name="host_name">
                            <option value="" selected>Host Name</option>
                            @foreach($search_data->unique('hostname') as $key => $hostname)
                                @if($hostname->hostname != '')
                                    <option value="{{ $hostname->hostname }}" {{ request()->get('host_name') == $hostname->hostname ? 'selected' : '' }}>{{ $hostname->hostname }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 pl-0 pr-0">
                        <select class="form-control select-multiple" name="event_id">
                            <option value="" selected>Event ID</option>
                            @foreach($search_data->unique('eventid') as $key => $event_id)
                                @if($event_id->eventid != '')
                                    <option value="{{ $event_id->eventid }}" {{ request()->get('event_id') == $event_id->eventid ? 'selected' : '' }}>{{ $event_id->eventid }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 pr-2">
                        <select class="form-control select-multiple" name="object_id">
                            <option value="" selected>Object ID</option>
                            @foreach($search_data->unique('objectid') as $key => $object_id)
                                @if($object_id->objectid != '')
                                    <option value="{{ $object_id->objectid }}" {{ request()->get('object_id') == $object_id->objectid ? 'selected' : '' }}>{{ $object_id->objectid }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 pr-2">
                        <select class="form-control select-multiple" name="problem">
                            <option value="" selected>Problem</option>
                            @foreach($search_data->unique('name') as $key => $problem)
                                @if($problem->name != '')
                                <option value="{{ $problem->name }}" {{ request()->get('problem') == $problem->name ? 'selected' : '' }}>{{ $problem->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mt-2 pl-0 d-flex">
                        <button type="submit" class="btn btn-image"><img src="{{asset('/images/filter.png')}}"></button>

                        <a href="{{route('zabbix.problem')}}" type="button" class="btn btn-image pl-0" id="resetFilter"><img src="{{asset('/images/resend2.png')}}" /></a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive mt-3">
        {{ $problems->links() }}
        <table class="table table-bordered w-100" id="adsaccount-table">
            <thead>
            <tr>
                <th>Id</th>
                <th>Hostname</th>
                <th>Event Id</th>
                <th>Object ID</th>
                <th>Problem</th>
                <th>DateTime</th>
                <th>Recovery Time</th>
                <th>Time Duration</th>
                <th>Severity</th>
                <th>Acknowledged</th>
            </tr>
            </thead>

            <tbody>
            @foreach($problems as $problem)
                <tr>
                    <td>{{$problem->id}}</td>
                    <td>{{$problem->hostname}}</td>
                    <td>{{$problem->eventid}}</td>
                    <td>{{$problem->objectid}}</td>
                    <td>{{$problem->name}}</td>
                    <td>{{$problem->datetime ? date('Y-m-d H:i:s', $problem->datetime) : ''}}</td>
                    <td>{{$problem->recovery_time ? date('Y-m-d H:i:s', $problem->recovery_time) : ''}}</td>
                    <td>{{$problem->time_duration ? date('Y-m-d H:i:s', $problem->time_duration) : ''}}</td>
                    <td>{{\App\Problem::SEVERITY[$problem->severity] ?? ''}}</td>
                    <td>{{$problem->acknowledged ? 'Yes' : 'No'}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>


@endsection
@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
   var oTable;
        $(document).ready(function() {
            oTable = $('#problem-table').DataTable({
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                sScrollX:false,
                searching: true,
               
                targets: 'no-sort',
                bSort: false,
                ajax: {
                    "url": "{{ route('zabbix.problem') }}",
                    data: function(d) {
                       
                    },
                },
                columnDefs: [{
                    targets: [],
                    orderable: false,
                    searchable: false
                }],
                columns: [
                  {
                      data: 'id',                                             
                      render: function(data, type, row, meta) {
                        return data;
                      }
                    },
                  {
                      data: 'hostname',                                             
                      render: function(data, type, row, meta) {
                        return data;
                      }
                    },    
                {
                      data: 'eventid',                                             
                      render: function(data, type, row, meta) {
                        return data;
                      }
                    },
                    {
                      data: 'objectid',                                             
                      render: function(data, type, row, meta) {
                        return data;
                      }
                    },
                    {
                      data: 'name',                                             
                      render: function(data, type, row, meta) {
                        return data;
                      }
                    },
                    {
                        data: 'datetime',
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'recovery_time',
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'time_duration',
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'severity',
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'acknowledged',
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    }
                    
                ],
            });
        });

    </script>


@endsection

