<div id="JsRequireDataAddModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="magento_module_js_require_form" class="form mb-15" >
            {{ html()->hidden('magento_module_id')->id('magento_module_id') }}
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Add JsRequire Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="row ml-2 mr-2">
                    <div class="col-xs-11 col-sm-11">
                            <label for="files_include">files are included only on the pages where they are required</label>
                    </div>
                    <div class="col-xs-1 col-sm-1">
                        
                            {{ html()->checkbox('files_include', null, 1)->id('files_include')->attribute('placeholder', 'Magento standards')->class('') }}
                            @if ($errors->has('files_include'))
                                <span style="color:red">{{ $errors->first('files_include') }}</span>
                            @endif
                    </div>
                </div>
                
                <div class="row ml-2 mr-2">
                    <div class="col-xs-11 col-sm-11">
                        <div class="form-group">
                            <label for="native_functionality">Loaded through the native Magento functionality known as RequireJs</label>
                        </div>
                    </div>
                    <div class="col-xs-1 col-sm-1">
                        <div class="form-group">
                            {{ html()->checkbox('native_functionality', null, 1)->id('native_functionality')->attribute('placeholder', 'native functionality')->class('') }}
                            @if ($errors->has('native_functionality'))
                                <span style="color:red">{{ $errors->first('native_functionality') }}</span>
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

    $(document).on('submit', '#magento_module_js_require_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_js_require_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);

        $.ajax({
            url: '{{ route("magento_module_js_require_histories.store") }}',
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
                $('#JsRequireDataAddModal #magento_module_js_require_form').trigger('reset');
                $('#JsRequireDataAddModal #magento_module_js_require_form').find('.error-help-block').remove();
                $('#JsRequireDataAddModal #magento_module_js_require_form').find('.invalid-feedback').remove();
                $('#JsRequireDataAddModal #magento_module_js_require_form').find('.alert').remove();
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