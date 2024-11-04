@extends('layouts.app')
@section('favicon' , 'scrapproduct.png')
@section('title', 'Server History')
@section('large_content')
    <div class="row">
       <div class="col-lg-12 margin-tb">
          <h2 class="page-heading">Server history</h2>
          <div class="pull-left">
             <form action="?" class="form-inline" method="GET">
                <div class="form-group ml-3">
                   <div class='input-group date' id='planned-datetime'>
                      <input type='text' class="form-control input-sm date-type" name="planned_at" value="{{ $requestedDate }}" />
                      <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                   </div>
                </div>
                <button type="submit" class="btn btn-xs"><i class="fa fa-filter ml-3"></i></button>
             </form>
          </div>
       </div>
    </div>
    <div class="row no-gutters mt-3">
       <div class="col-xs-12 col-md-12" id="plannerColumn">
          <div class="table-responsive">
             <table class="table table-bordered table-sm">
                <thead>
                   <tr>
                      <th width="5%">Time</th>
                      <?php foreach ($totalServers as $totalServer) { ?>
                            <th width="8%">{{ $totalServer }}</th>
                      <?php } ?>
                   </tr>
                </thead>
                <tbody>
                   <?php foreach ($timeSlots as $k => $timeSlot) { ?> 
                       <tr>
                          <td>{{ date("g:i A",strtotime($timeSlot.":00")) }}</td>
                          <?php foreach ($totalServers as $s => $totalServer) {
                              $rndid = $totalServer.'_'.rand(10, 10000000);
                              ?>
                              <td class="p-2 expand-row-msg" data-name="error" data-id="{{$rndid}}">
                                  <?php
                                    if (isset($listOfServerUsed[$k]) && isset($listOfServerUsed[$k][$totalServer])) {
                                        $loops = $listOfServerUsed[$k][$totalServer];
                                        foreach ($loops as $l) {
                                            ?>
                                            <span class="show-short-error-{{$rndid}}">{{ Str::limit($l['memory_string'], 13, '..')}}</span>
                                            <span style="word-break:break-all;" class="show-full-error-{{$rndid}} hidden">{{$l['memory_string']}}</span>
                                            <?php
                                            break;
                                        }

                                        ?>
                                        <button class="btn btn-sm p-0" data-toggle="modal" data-target="#scrapers-{{$totalServer}}-{{$k}}"><i class="fa fa-info-circle"></i></button>
                                        <div class="modal fade" id="scrapers-{{$totalServer}}-{{$k}}" tabindex="-1" role="dialog" aria-labelledby="scrapers" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header p-0 pt-2 pl-2 pr-2">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">All Scrapers</h5>
                                                        <button type="button" class="close btn-xs p-0 mr-2" data-dismiss="modal" aria-label="Close">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <ul class="list-group">
                                                            <?php
                                                            $loops = $listOfServerUsed[$k][$totalServer];
                                        foreach ($loops as $l) {
                                            $deleteBtn = '';
                                            if (! empty($l['pid'])) {
                                                $deleteBtn = '<button class="btn btn-xs pull-right"> <i class="fa fa-trash stop-job" data-server-id="'.$totalServer.'" data-p-id="'.$l['pid'].'"></i> </button>';
                                            }
                                            echo '<li class="list-group-item">'.$l['scraper_name'].$deleteBtn.'</li>';

                                        }
                                        ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                              ?>
                              </td>
                          <?php } ?>
                       </tr>
                   <?php } ?> 
                </tbody>
             </table>
          </div>
       </div>
    </div>
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/media-card.css') }} ">
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.date-type').datetimepicker({
                format: 'YYYY-MM-DD'
            });
        });

        $(document).on('click', '.expand-row-msg', function () {
            var name = $(this).data('name');
            var id = $(this).data('id');
            console.log(name);
            var full = '.expand-row-msg .show-short-'+name+'-'+id;
            var mini ='.expand-row-msg .show-full-'+name+'-'+id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });

        $(document).on("click",".stop-job",function(e) {
            e.preventDefault();
            var $this = $(this);
            var serverID = $this.data("server-id");
            var pID = $this.data("p-id");

            if(serverID == "" || pID == "") {
              toastr['error']("Server id or PID is not setup", 'error');
              return false;
            }

            if(confirm("Are you sure you want to do kill job?")) {
                
                $.ajax({
                    type: 'GET',
                    url: '{{ route('statistics.server-history.close-job') }}',
                    data: {
                      pid : pID,
                      server_id : serverID
                    },
                    dataType:"json"
                }).done(response => {
                    toastr['success'](response.message, 'success');
                });
            }
        });

    </script>
@endsection