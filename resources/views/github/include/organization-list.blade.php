<div class="modal fade" id="viewOrganizationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 999999;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select Organization</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @php  $githubOrganizations = \App\Github\GithubOrganization::get(); @endphp
      <form action="#" method="POST" id="githubOrganizationForm">
        <input type="hidden" name="submit_form_input_id" id="submit_form_input_id">
        <input type="hidden" name="submit_organization_input_id" id="submit_organization_input_id">
        <input type="hidden" name="submit_organization_action_type" id="submit_organization_action_type">

        <div class="modal-body">
            <div class="form-group">
                <label for="recipient-name" class="col-form-label">Organization</label>
                <select class="form-control" id="organization_id" name="organization_id" required>
                    @foreach($githubOrganizations as $githubOrganization)
                        <option value="{{ $githubOrganization->id }}">{{ $githubOrganization->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
       <form>
    </div>
  </div>
</div>
<script>
    $('#githubOrganizationForm').submit(function(e){
      e.preventDefault();

      var submitFormId = $('#submit_form_input_id').val();
      var submitOrganizationInputId = $('#submit_organization_input_id').val();
      var submitOrganizationActionType = $('#submit_organization_action_type').val();
      var organizationId = $('#organization_id').val();
      
      $('#'+submitOrganizationInputId).val(organizationId);

      if(submitOrganizationActionType == 'function'){
          $('#createUser').modal('hide');
          const email = $('#createUser').attr('data-email');

          $.ajax({
            type: "POST",
            url: "/vendors/inviteGithub",
            data: {
              _token: "{{ csrf_token() }}",
              email: email,
              organizationId : organizationId
            }
          })
          .done(function(data) {
            alert(data.message);
          })
          .fail(function(error) {
            alert(error.responseJSON.message);
          });
      }else{
        $('#'+submitFormId).unbind().submit();
      }
    });
</script>