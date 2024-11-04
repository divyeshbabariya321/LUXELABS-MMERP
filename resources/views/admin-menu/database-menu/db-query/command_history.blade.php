@extends('layouts.app')

@section('title', 'Command Execution History')

@section('styles')
   
@endsection
@section('content')


    <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">Command Execution History</h2>


    <div class="row m-0">
        <div class="infinite-scroll">
	        <div class="table-responsive mt-2">
                <table class="table table-bordered order-table" style="border: 1px solid #ddd !important; color:black;table-layout:fixed">
                    <thead>
                        <tr>
                            <th width="2%">#</th>
                            <th width="30%">Commannd Name</th>
                            <th width="50%">Command Response</th>
                            <th width="18%">Execute By</th>
                            <th width="20%">Executed At</th>
                           
                        </tr>
                    </thead>
                    
                    <tbody>
                    @foreach($command_history as $key => $value)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$value->command_name}}</td>

                            <td class="view_detail_command_answer" style="cursor: pointer;" data-command_start_time="{{$value->created_at}}" data-command_end_time="{{$value->updated_at}}"  data-command="{{$value->command_name}}" data-answer="{{$value->command_answer}}">{{ (strlen($value->command_answer) > 100 ) ? substr($value->command_answer,0,100).'....' : $value->command_answer}}</td>

                            <td>{{$value->user_name}}</td>
                            <td>{{$value->created_at}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
	        </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $command_history->appends($request->except('page'))->links() }}.
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="command_response" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title command_name"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label class="command_start_time"></label> <br/>
                <label class="command_end_time"></label><br/>

                <div class="command_answer" style="word-wrap: break-word;"></div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
            </div>
        </div>
    </div>  
    


@endsection

@section('scripts')

<script>  
$(document).on("click",".view_detail_command_answer",function(e) {
    var command_name  = $(this).data('command');
    var command_answer  = $(this).data('answer');
    var command_start_time  = $(this).data('command_start_time');
    var command_end_time  = $(this).data('command_end_time');

    $('.command_name').html(command_name);
    $('.command_answer').html(command_answer);
    $('.command_start_time').html('Command Start Time = '+command_start_time);
    $('.command_end_time').html('Command End Time = '+command_end_time);

    $('#command_response').modal('show');
});
</script>

@endsection

