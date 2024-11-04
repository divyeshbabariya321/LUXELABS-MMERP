<div id="cronJobDataAddModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="magento_module_cron_job_form" class="form mb-15" >
                @csrf
                {{ html()->hidden('magento_module_id')->id('magento_module_id') }}
            <div class="modal-header">
                <h4 class="modal-title">Add Cron Job Details  </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            {{ html()->text('cron_time')->id('cron_time')->placeholder('Cron Time')->class('form-control')->required() }}
                            @if ($errors->has('cron_time'))
                                <span style="color:red">{{ $errors->first('cron_time') }}</span>
                            @endif
                        </div>
                    </div>
                
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            {{ html()->text('frequency')->id('frequency')->placeholder('Cron frequency')->class('form-control')->required() }}
                            @if ($errors->has('frequency'))
                                <span style="color:red">{{ $errors->first('frequency') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            {{ html()->text('cpu_memory')->id('cpu_memory')->placeholder('CPU Memory')->class('form-control')->required() }}
                            @if ($errors->has('cpu_memory'))
                                <span style="color:red">{{ $errors->first('cpu_memory') }}</span>
                            @endif
                        </div>
                    </div>
                
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            {{ html()->text('comments')->id('comments')->placeholder('Comments')->class('form-control')->required() }}
                            @if ($errors->has('comments'))
                                <span style="color:red">{{ $errors->first('comments') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Add</button>
            </div>
            </form>
            
        </div>
    </div>
</div>

@push('scripts')
    <script>

    $(document).on('submit', '#magento_module_cron_job_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_cron_job_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento_module_cron_job_histories.store") }}',
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                button.html(spinner_html);
                button.prop('disabled', true);
                button.addClass('disabled');
            },
            complete: function() {
                button.html('Add');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#cronJobDataAddModal #magento_module_cron_job_form').trigger('reset');
                $('#cronJobDataAddModal #magento_module_cron_job_form').find('.error-help-block').remove();
                $('#cronJobDataAddModal #magento_module_cron_job_form').find('.invalid-feedback').remove();
                $('#cronJobDataAddModal #magento_module_cron_job_form').find('.alert').remove();
                oTable.draw();
                toastr["success"](response.message);
            },
            error: function(xhr, status, error) { // if error occured
                if(xhr.status == 422){
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                }
                else{
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });

    </script>

@endpush