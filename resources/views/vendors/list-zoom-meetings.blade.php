@extends('layouts.app')

@section('title', 'Vendor Meeting Info')

@section('styles')
<style>
    .update_description{
        margin-top: 0.50rem;
    }
    .float-right-addbtn{
        float: right !important;
        margin-top: 1%;
        margin-right: 0.095rem;
    }
</style>
@endsection
@section('content')
    <button type="button" class="btn btn-danger float-right-addbtn" id="refresh_recordings"> Refresh Recordings</button>
    <div class="table-responsive">
        <table class="table table-bordered" id="users-table">
            <thead>
            <tr>
                <th style="width:10%;">No</th>
                <th style="width:20%;">File Name</th>
                <th style="width:60%;">Description</th>
                <th style="width:10%;">Action</th>
            </tr>
            </thead>
            <tbody>
                @include('vendors.partials.list-meetings')
            </tbody>
        </table>
    </div>

@endsection

@section('scripts')
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on("click", ".update_description", function(){
        var meetingId = $(this).attr('data-id');
        var description = $(this).parents('td').find('.description').val();
        if(description != ''){
            $.ajax({
                type: "POST",
                url: "{{ route('vendor.meeting.update') }}",
                data: {'_token': "{{ csrf_token() }}",id:meetingId,description:description},
                success: function(response) {
                  if(response.code == 200){
                    toastr['success'](response.message, 'success');
                  } else {
                    toastr['error'](response.message, 'error');
                  }              
                }
            });
        } else {
            toastr['success'](response.message, 'success');
        }
    });

    $(document).on('click', '#refresh_recordings', function(e){
      $.ajax({
        type: "POST",
        url: "{{ route('vendor.meeting.refresh') }}",          
        success: function(response) {
          toastr['success'](response.message, 'success');
          window.location.reload();
        }
    });
  });  
    
</script>

@endsection
