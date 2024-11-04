@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Jobs / Queues({{$count}})</h2>
        </div>
    </div>
   <div class="row">
        <div class="col-md-12 ">
            <div class=" panel-default">
                <div class="panel-body p-0">
                    <div class="row p-3">
                        <div class ="col-md-12 pl-2">
                            <form action="{{ route('jobs.list') }}" method="GET">
                                <div class="col-md-2">

                                    {{ html()->select('queue', ["" => "-- Select Queue--"] + $listQueues, request('queue'))->class("form-control") }}
                                </div>
                                <div class="col-md-2">
                                   
                                    <input type="text" class="form-control" id="payload" placeholder="Payload" name="payload" value="{{ request('payload') }}">
                                </div>
                                <div class="col-md-1">
                                  
                                    <input type="date" class="form-control" id="reserved_date" style="width: auto" name="reserved_date" value="{{ request('reserved_date') ? date('m/d/Y',strtotime(old('reserved_date'))) : null }}">
                                </div>
                                <div class="col-md-1">
                                   
                                    <input type="date" class="form-control" id="available_date"style="width: auto"  name="available_date" value="{{ request ('available_date') ? date('m/d/Y',strtotime(old('available_date'))) : null }}">
                                </div>
                                <button class="btn btn-light col-md-1 ml-2" id="submit">
                                    <span class="fa fa-filter"></span> Filter Results
                                </button>
                                <div class="col-md-2 pull-right">
                                  @csrf
                                     <button  style="background-color:#ffc9c9;" class="btn btn-light" id="checkboxsubmit">
                                        <span class="fa fa-trash"></span> Delete All
                                    </button>
                                     <div class="col-md-2">
                                        <button class="btn btn-secondary" id="delete-selected">
                                <span class="fa fa-trash"></span> Delete Selected
                            </button>
                                     </div>
                                </div>
                           </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 mt-3">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover"style="table-layout: fixed;">
                    <thead>
                        <th width="1%"></th>
                        <th width="5%">Queue</th>
                        <th width="15%">Payload</th>
                        <th width="4%">Attempts</th>
                        <th width="5%">Reserved Date</th>
                        <th width="5%">Available Date</th>
                        <th width="2%">Action</th>
                    </thead>
                    <tbody>
                        @foreach($jobs as $item)
                            <tr>
                                <td><input class="check-jobs" type="checkbox" name="ids[]" value="{{ $item->id }}"></td>
                                <td> {{ $item->queue }} </td>
                                <td>
                                    @if(strlen($item->payload)>60)
                                        {{substr($item->payload,0,60)}} ....<a href="#" data-toggle="modal" data-target="#detail_modal" data-id="{{ $item}}" class="job-details"style="color: black;">View Detail</a>
                                    @else
                                        {{$item->payload}}
                                    @endif
                                </td>
                                <td> {{ $item->attempts }} </td>
                                <td>
                                    @if($item->attempts!=0)
                                        {{ date('M d, Y',$item->reserved_at)}}
                                    @endif
                                </td>
                                <td> 
                                    @if($item->attempts!=0)
                                        {{ date('M d, Y',$item->reserved_at)}}
                                    @endif
                                </td>
                                <td>
                                    <i class="fa fa-eye"></i>
                                    <a onclick="return confirm('Are you sure you want to delete job ?')" href="{{ route('jobs.delete',[$item->id]) }}"style="color: black;">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach()
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-center">
            {!! $jobs->appends($filters)->links() !!}
        </div>
    </div>
   </div>
    <div id="detail_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Job Detail</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" id="payload-detail">
                    
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection()
@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
   
    $(document).on("click","#checkboxsubmit",function() {
        var yes = confirm("Are you sure you want to delete all jobs ?");
        if(yes) {
      var data = {{$checkbox}};
      $.ajax({
                        type: 'POST',
                        url: "/jobs/alldelete/{{$checkbox}}",
                        data: {
                          _token: "{{ csrf_token() }}",
                          data: data,
                        }
                    }).done(function(response) {
                        alert('Job Deleted');
                        if(response.code == 200) {
                            window.location.href = '/jobs';
                        }
                    }).fail(function(response) {
                        alert('Sorry , we can not delete the jobs due to some internal error.');
                       
                    });
        }
    });
        $(document).on("click","#delete-selected",function() {
                var yes = confirm("Are you sure you want to delete selected jobs ?");
                if(yes) {
                    
                    var jobIds = [];
                    $('.check-jobs:checkbox:checked').each(function (k,v) {
                        jobIds.push($(v).val());
                    });
                    $.ajax({
                        type: 'POST',
                        url: "/jobs/delete-multiple",
                        data: {
                          _token: "{{ csrf_token() }}",
                          jobIds: jobIds,
                        }
                    }).done(function(response) {
                        alert('Job Deleted');
                        if(response.code == 200) {
                            location.reload();
                        }
                    }).fail(function(response) {
                        alert('Sorry , we can not delete the jobs due to some internal error.');
                    }); 
                }
        });
        
        $(document).on("click", ".job-details", function () {
            var detail = $(this).data('id');

            var payload=detail['payload'];
            var type=typeof payload;
            try {
                json=JSON.parse(payload);
                type= typeof json;
                console.log(type)
                console.log(json)


                if (type == "object")
                {
                    var html="<b> Display Name </b>"+json['displayName']+"<br><b>Job</b> "+json['job']+"<br><b>maxTries</b> "+json['maxTries']+"<br><b>timeout</b> "+json['timeout']+"<br><b>timeoutAt</b> "+json['timeoutAt']+"<br><b>Data</b><div style='word-break: break-all'>"+JSON.stringify(json['data'])+"<div>";
                    $("#payload-detail").html( html);
                }
            }catch(err) {
                $("#payload-detail").html(payload);
            }
        });

    </script>
    @if (Session::has('errors'))
        <script>
            toastr["error"]("{{ $errors->first() }}", "Message")
        </script>
    @endif
@endsection