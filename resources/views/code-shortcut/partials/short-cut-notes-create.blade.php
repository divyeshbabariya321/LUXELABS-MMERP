 <!-- Platform Modal content-->
	 <div id="code-shortcut-platform" class="modal fade in" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-header">
				<h4 class="modal-title">Add Platform</h4>
				<button type="button" class="close" data-dismiss="modal">×</button>
			  </div>
				<form action="{{route('code-shortcuts.platform.store')}}" method="POST" id="code-shortcut-platform-form">
					@csrf
					  <div class="modal-body">
						  <div class="form-group">
							  {{ html()->label('Name', 'platform_name')->class('form-control-label') }}
							  {{ html()->text('platform_name')->class('form-control')->required()->attribute('rows', 3) }}
						</div>
						<div class="modal-footer">
						  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						  <button type="submit" class="btn btn-primary">Save</button>
					  </div>
					</div>
				</form>
			</div>

		</div>
	</div>


     <!-- code Modal content-->

    <div class="modal fade" id="create_code_shortcut" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create Code Shortcut</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" id="code-shortcut-from" action="{{route('code-shortcuts.store')}}">
                    @csrf
                    <div class="modal-body">

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Supplier</label>
                                    {{ html()->select("supplier", ['' => ''])->class("form-control globalSelect2")->style("width:100%;")->data('ajax', route('select2.shortcutsuplliers'))->data('placeholder', 'supplier') }}

                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Platform</label>
                                {{ html()->select("platform_id", ['' => ''])->class("form-control globalSelect2")->style("width:100%;")->data('ajax', route('select2.shortcutplatform'))->data('placeholder', 'Platforms') }}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Folder Name</label>
                                {{ html()->select("folder_id", ['' => ''])->class("form-control globalSelect2")->style("width:100%;")->data('ajax', route('select2.shortcutfolders'))->data('placeholder', 'Folders') }}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <strong>Upload File</strong>
                                <input type="file" name="notesfile" id="shortnotefileInput" >
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Code</label>
                                {{ html()->text('code', old('code'))
                                ->class('form-control code') }}
                                                                                        </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Title</label>
                                {{ html()->text('title', old('title'))
                                ->class('form-control title') }}
                                                        </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Solution</label>
                                {{ html()->text('solution', old('solution'))
                                ->class('form-control solution') }}
                                                        </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Description</label>
                                {{ html()->text('description', old('description'))
                                ->class('form-control description') }}
                                                        </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" id="show_full_log_modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Full Log</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="show_full_log_modal_content">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#create_code_shortcut').on('show.bs.modal', function (e) {
            $('.globalSelect2').select2({
                ajax: {
                    url: function (params) {
                        return $(this).data('ajax');
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term // search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.items
                        };
                    },
                    cache: true
                },
                placeholder: $(this).data('placeholder')
            });
        });

        $('#code-shortcut-platform form').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{route('code-shortcuts.platform.store')}}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    switch (response.status) {
                        case 'success':
                            toastr.success(response.msg);
                            break;
                        default:
                            toastr.error(response.msg);
                            break;

                    }
                },
                error: function(error) {
                    toastr.error(error.responseJSON.msg);
                },
            });
        });

        $('#code-shortcut-from').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{route('code-shortcuts.store')}}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    switch (response.status) {
                        case 'success':
                            toastr.success(response.msg);
                            getShortcutNotes();
                            break;
                        default:
                            toastr.error(response.msg);
                            break;

                    }
                },
                error: function(error) {
                    toastr.error(error.responseJSON.msg);
                },
            });
        });
    </script>
