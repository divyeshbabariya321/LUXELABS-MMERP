<div class="modal fade" id="create-magento-backend-docs" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Create Magento Backend Documentation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" id="magento-backend-create" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group custom-select2">
                            <label>Categories
                            </label>
                            <select class="globalSelect2 form-control" id="site_category"
                                name="site_development_category" multiple="true" >
                                <option value="" class="form-control" required>Select Categories</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Features</label>
                            {{ html()->text('features')->id('features')->placeholder('Magento Backend features')->class('form-control features')->required() }}

                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Template File</label>
                            {{ html()->text('template_file')->id('template_file')->placeholder('Magento Backend Template File')->class('form-control template_file')->required() }}

                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group custom-select2">
                            <label>Api</label>
                            <select class="globalSelect2 form-control" id="post_man_api"
                                name="post_man_api_id"  multiple="true"  required>
                                <option value="" class="form-control" required>Select Api</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group custom-select2">
                            <label>Extension Used</label>
                            <select class="globalSelect2 form-control" multiple="true" id="mageneto_module"
                                name="mageneto_module_id" required>
                                <option value="" class="form-control">Select Extension</option>
                            </select>
                        </div>
                    </div>
                    @if (auth()->user() &&
                            auth()->user()->isAdmin())
                        @php
                            $users = \App\User::select('id', 'name', 'email', 'gmail')
                                ->whereNotNull('gmail')
                                ->get();
                        @endphp
                        <div class="col-sm-12">
                            <div class="form-group custom-select2">
                                <label>Read Permission for Users
                                </label>
                                <select class="w-100 js-example-basic-multiple js-states" id="id_label_permission_read"
                                    multiple="multiple" name="read[]" required>
                                    @foreach ($users as $val)
                                        <option value="{{ $val->gmail }}" class="form-control">{{ $val->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group custom-select2">
                                <label>Write Permission for Users
                                </label>
                                <select class="w-100 js-example-basic-multiple js-states" id="id_label_permission_write"
                                    multiple="multiple" name="write[]" required>
                                    @foreach ($users as $val)
                                        <option value="{{ $val->gmail }}" class="form-control">{{ $val->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="col-sm-12">
                        <div class="form-group custom-select2">
                        <label>Bug</label>
                        <select class="form-control select2" name="bug" id="bug" required>
                            <option value="">Select Log Status</option>
                            <option value="yes" {{ request('verify') == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ request('verify') == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Bug Details</label>
                            {{ html()->text('bug_details')->id('bug_details')->placeholder('Magento Bug Details')->class('form-control bug_details')->required() }}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Bug Resolution </label>
                            {{ html()->text('bug_resolution')->id('bug_resolution')->placeholder('Bug Resolution ')->class('form-control bug_resolution')->required() }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    // $('#id_label_categories').select2({
    //     minimumInputLength: 3 // only start searching when the user has input 3 or more characters
    $('#site_category').select2();
    $('#post_man_api').select2();
    $('#mageneto_module').select2();

    $(document).ready(function() {

    $("#id_label_permission_write").select2();
    $("#id_label_permission_read").select2();
    
        $.ajax({
            url: "{{ route('getBackendDropdownDatas') }}",
            type: "GET",
            dataType: "json",
            success: function(response) {

                var storecategories = response.storecategories;
                var postManAPi = response.postManAPi;
                var magentoModules = response.magentoModules;

                var $taskSelect = $("#site_category");
                var $userReadSelect = $("#post_man_api");
                var $userWriteSelect = $("#mageneto_module");

                $taskSelect.empty();
                $userReadSelect.empty();
                $userWriteSelect.empty();

                $taskSelect.append(
                '<option value="" class="form-control">Select Category</option>');
                $userReadSelect.append(
                '<option value="" class="form-control">Select Api</option>');
                $userWriteSelect.append(
                '<option value="" class="form-control">Select Modules</option>');

                storecategories.forEach(function(task) {
                    $taskSelect.append('<option value="' + task.id + '">' + task.title +
                        '</option>');
                });

                postManAPi.forEach(function(api) {
                    $userReadSelect.append('<option value="' + api.id + '">' + api
                        .request_url + '</option>');
                });

                magentoModules.forEach(function(moduleNames) {
                    $userWriteSelect.append('<option value="' + moduleNames.id + '">' +
                        moduleNames.module + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });


    $(document).ready(function() {
        $('select[name="mageneto_module_id"]').select2({
            minimumInputLength: 2, // Set your minimum input length
            maximumSelectionLength: 1, // Limit selection to a single value
        });

        // Add an event listener for the change event
        $('select[name="mageneto_module_id"]').on('change', function (event) {
            var selectedValue = $(this).val(); // Get the selected value
        });
   });

   $(document).ready(function() {
        $('select[name="post_man_api_id"]').select2({
            minimumInputLength: 2, // Set your minimum input length
            maximumSelectionLength: 1, // Limit selection to a single value
        });

        // Add an event listener for the change event
        $('select[name="post_man_api_id"]').on('change', function (event) {
            var selectedValue = $(this).val(); // Get the selected value
        });
   });

   $(document).ready(function() {
        $('select[name="site_development_category"]').select2({
            minimumInputLength: 2, // Set your minimum input length
            maximumSelectionLength: 1, // Limit selection to a single value
        });

        // Add an event listener for the change event
        $('select[name="site_development_category"]').on('change', function (event) {
            var selectedValue = $(this).val(); // Get the selected value
        });
   });

    $(document).on('submit', '#magento-backend-create', function(e) {
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento-backend-create"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route('magento-backend-store') }}',
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
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
            success: function(data) {
                toastr["success"](data.message);
                $('#create-magento-backend-docs').modal('hide');
                window.location.reload();
            },
            error: function(xhr, status, error) { // if error occured
                if (xhr.status == 422) {
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                } else {
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });
</script>
