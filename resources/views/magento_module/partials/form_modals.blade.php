<div id="moduleCreateModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="magento_module_form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Add {{ $title }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @include('magento_module.partials.form')

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Add</button>
            </div>
            </form>
            
        </div>
    </div>
</div>

<div id="moduleEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="magento_module_edit_form" method="POST">
                @csrf
                @method('PUT')
                {{ html()->hidden('id')->id('id') }}
                <div class="modal-header">
                    <h4 class="modal-title">Edit {{ $title }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    @include('magento_module.partials.form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>

    $(document).on('submit', '#magento_module_form', function(e){
        e.preventDefault();

        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_form"));
        var button = $(this).find('[type="submit"]');
        console.log("Submit magento_module_form");
        $.ajax({
            url: '{{ route("magento_modules.store") }}',
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
                $('#moduleCreateModal #magento_module_form').trigger('reset');
                $('#moduleCreateModal #magento_module_form').find('.error-help-block').remove();
                $('#moduleCreateModal #magento_module_form').find('.invalid-feedback').remove();
                $('#moduleCreateModal #magento_module_form').find('.alert').remove();
                $('#magento_module_form').modal('hide');
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

    //edit module
    $(document).on('click', '.edit-magento-module', function() {
        var moduleId = $(this).data("row");
        var url = "{{ route('magento_module.module-edit', ['id' => ':id']) }}";
        url = url.replace(':id', moduleId);          
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            url: url,
        }).done(function(response) {
            $("#magento_module_edit_form #id").val(response.data.id);
            $("#magento_module_edit_form #module").val(response.data.module);
            $("#magento_module_edit_form #module_category_id").val(response.data.module_category_id);
            $("#magento_module_edit_form #magneto_location_id").val(response.data.magneto_location_id);
            $("#magento_module_edit_form #return_type_error_status").val(response.data.return_type_error_status);
            $("#magento_module_edit_form #store_website_id").val(response.data.store_website_id);
            $("#magento_module_edit_form #current_version").val(response.data.current_version);
            $("#magento_module_edit_form #module_type").val(response.data.module_type);
            $("#magento_module_edit_form #payment_status").val(response.data.payment_status);
            $("#magento_module_edit_form #status").val(response.data.status);
            $("#magento_module_edit_form #api").val(response.data.api);
            $("#magento_module_edit_form #cron_job").val(response.data.cron_job);
            $("#magento_module_edit_form #is_js_css").val(response.data.is_js_css);
            $("#magento_module_edit_form #is_third_party_js").val(response.data.is_third_party_js);
            $("#magento_module_edit_form #is_sql").val(response.data.is_sql);
            $("#magento_module_edit_form #is_third_party_plugin").val(response.data.is_third_party_plugin);
            $("#magento_module_edit_form #developer_name").val(response.data.developer_name);
            $("#magento_module_edit_form #is_customized").val(response.data.is_customized);
            $("#magento_module_edit_form #module_review_standard").val(response.data.module_review_standard);
            $("#magento_module_edit_form #used_at").val(response.data.used_at);
            $("#magento_module_edit_form #module_description").val(response.data.module_description);
            $("#magento_module_edit_form #dependency").val(response.data.dependency);
            $("#magento_module_edit_form #composer").val(response.data.composer);
            $("#magento_module_edit_form #site_impact").val(response.data.site_impact);
            $("#moduleEditModal").modal("show");
        }).fail(function (response) {
            $("#loading-image-preview").hide();
            console.log("Sorry, something went wrong");
        });
    });

    $(document).on('submit', '#magento_module_edit_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_edit_form"));
        var magento_module_id = $('#magento_module_edit_form #id').val();
        console.log(formData, magento_module_id);
        var button = $(this).find('[type="submit"]');
        console.log("#magento_module_edit_form submit");
        $.ajax({
            url: '{{ route("magento_modules.update", '') }}/' + magento_module_id,
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
                button.html('Update');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#moduleCreateModal #magento_module_edit_form').find('.error-help-block').remove();
                $('#moduleCreateModal #magento_module_edit_form').find('.invalid-feedback').remove();
                $('#moduleCreateModal #magento_module_edit_form').find('.alert').remove();
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