@extends('layouts.app')

@section('styles')
<style>
    .instagram-post{
        display: flex; 
        width: auto; height: 
        auto; margin: 0px auto; 
        border: 1px solid #eeeeee;
    }
</style>
@endsection
@section('content')

<h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">
    Instagram message queue
    <div class="margin-tb" style="flex-grow: 1;">
        <div class="pull-right ">


            
        </div>
    </div>
</h2>


<form method="get" action="{{route('instagram.message-queue.approve')}}">

    <div class="form-group">
        <div class="row ml-2">
    
   
            <div class="col-md-2">
                <input class="form-control" placeholder="Enter Influencer name" type="text" name="filterFullName" value="{{ $filterFullName }}" >
            </div>


            <div class="col-md-2">
                <input class="form-control" placeholder="Enter message"  type="text" name="filterMessage" value="{{ $filterMessage }}">
            </div>

           
            <div class="col-md-1 d-flex justify-content-between">
                <button type="submit" class="btn btn-image" ><img src="/images/filter.png"></button>
            </div>
        </div>
    </div>
</form>



<div class="modal fade" id="instagram-account-popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Instagram account</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form  method="POST" action="{{ route('instagram.message-queue.settings') }}" id="instagram-message-rate-update">
                @csrf
                <div class="form-group d-flex flex-wrap">
                  
                </div>
                <div class="form-group d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary mr-5" data-dismiss="modal">Close</button>
                    <button class="btn btn-secondary">Update</button>
                </div>
                            
            </form>
            </div>

        </div>
    </div>
</div>

<div class="infinite-scroll col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered" style="table-layout:fixed;">
            <thead>
                <tr>
                    <th style="width:6%">Id </th>
                    <th style="width:6%">Influencer Name</th>
                    <th style="width:6%"> From account</th>
                    <th style="width:6%"> To account</th>
                    <th style="width:8%"> Message</th>
                    <th style="width:8%"> Created At</th>
                    <th style="width:8%"> Action</th>
                
                </tr>
            </thead>
            <tbody>
                        @foreach ($approveQueueList as $key => $queue)
                            <tr>
                                <td>{{ $queue->chat_message_id }}</td>
                                <td>{{ $queue->fullname }}</td>
                                <td>{{ $queue->getSenderUsername ? $queue->getSenderUsername->last_name: '' }}</td>
                                <td>{{ $queue->username }}</td>
                                <td>{{ $queue->chat_message_message }}</td>
                                <td>{{ $queue->chat_message_created_at }}</td>
                                <td>
                                    <form action="" method="POST" class="instagram-message-approved"> 
                                        @csrf
                                        <input type="hidden" name="chat_id" value={{  $queue->chat_message_id }}>
                                        <button class="btn btn-default">Approve </button>
                                    </form>
                                </td>
                            </tr>

                        @endforeach
            </tbody>
        </table>
    </div>
</div>

{{$approveQueueList->links()  }}

<script type="text/javascript">


$('.instagram-message-approved').submit(function(e){
    e.preventDefault()
    $this = $(this)


    let   formData = $(this).serialize()
                 $.ajax({
                    url: '{{ route("instagram.message-queue.approved") }}',
                    method: 'POST',
                    dataType: "json",
                    data: formData ,
                    success: function(response) {
                        if(response.message){
                            toastr["success"](response.message);
                            $this.closest('tr').remove()
                        }
                    },
                    error: function(response) {
                        toastr["error"]("Oops,something went wrong");
                    }
                });

})

</script>
@endsection
