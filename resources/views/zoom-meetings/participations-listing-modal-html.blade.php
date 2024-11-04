<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th> No</th>
            <th width="8%">Name</th>
            <th width="12%">Email</th>
            <th width="20%">Join Time</th>
            <th width="25%">Leave Time</th>
            <th width="25%">Descrption</th>
            <th width="25%">Created Date</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list">
        @foreach($participants as $participant)
        <tr>
            <td>{{ $participant->id }}</td>
            <td>{{ $participant->name }}</td>
            <td>{{ $participant->email}}</td>
            <td>{{ $participant->join_time}}</td>
            <td>{{ $participant->leave_time}}</td>
            <td>
                <div class="d-flex align-items-center">
                    <input type="text" name="description" class="form-control description" placeholder="Description" value="{{ ($participant->description ?? "" )}}">
                    <button class="btn btn-xs btn-image update_participant_description" data-id="{{ $participant->id }}"><i class="fa fa-pencil"></i></button>
                 </div>
            </td>
            <td>{{ $participant->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="pagination-container-participation"></div>


<script>
    $(document).off('click').on("click", ".update_participant_description", function(){
        var participantId = $(this).attr('data-id');
        var description = $(this).parents('td').find('.description').val();
        if(description != ''){
            $.ajax({
                type: "POST",
                url: "{{ route('participant.description.update') }}",
                data: {'_token': "{{ csrf_token() }}",id:participantId,description:description},
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
    </script>