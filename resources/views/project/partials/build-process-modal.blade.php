<div id="build-process-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Build Process</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="build-process">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" class="build_process_project_id" name="project_id" value="">
                            <div class="row">
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <strong>Job Name :</strong>
                                        {{ html()->text('job_name')->placeholder('Job Name')->id('job_name')->class('form-control')->required() }}
                                    </div>
                                    <div class="form-group">
                                        <strong>Organizations:</strong>
                                        <select name="organization" id="build_organization" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Organizations --</option>
                                            @forelse($organizations as $organization)
                                            <option value="{{ $organization->id }}" {{ $organization->id === 2 ? 'selected' : '' }}>
                                                {{ $organization->name }}
                                            </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <strong>Repository:</strong>
                                        <select name="repository" id="build_repository" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Repository --</option>
                                        </select>
                                    </div>
                                </div>                            
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <strong>Branch Name:</strong>
                                        <select name="branch_name" id="build_branch_name" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Branch --</option>
                                        </select>
                                    </div>
                                </div>                        
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="hidden" name="initiate_from" value="Project Page - Build Process">
                                        <button data-id=""class="btn btn-secondary update-build-process">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>

    $(document).on('submit', 'form#build-process', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("build-process"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("project.buildProcess") }}',
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $("#loading-image-preview").show();
            },
            complete: function() {
                $("#loading-image-preview").hide();
            },
            success: function(response) {
                if(response.code=='200'){
                    toastr["success"](response.message);
                    $('#build-process-modal').modal('hide');
                }else{
                    toastr["error"](response.message);
                }
                $("#loading-image-preview").hide();
            },
            error: function(xhr, status, error) { // if error occured
                $("#loading-image-preview").hide();
            },
        });
    });
    </script>

@endpush