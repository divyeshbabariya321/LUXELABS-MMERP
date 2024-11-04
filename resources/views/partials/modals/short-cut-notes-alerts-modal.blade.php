<div id="short-cut-notes-alerts-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">List Code Shortcut</h4>
                <div class="pull-right pr-7">
                    <button type="button" class="btn btn-secondary create-platform-btn" onclick="showCodeShortcutPlatformModal()">+ Add Platform</button>
                    <button type="button" class="btn btn-secondary create-product-template-btn" onclick="showCreateCodeShortcutModal()">+ Add Code</button>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="mt-3 col-md-12">
                <div class="pull-right pr-4">
                    <input type="text" id="search_short_notes_input" placeholder="Search By Type.....">
                </div>
            </div>
            <div class="modal-body">
                <table class="table"  id="short_cut_notes_table">
                    <thead class="thead-light">
                        <tr>
                            <th>S.No</th>
                            <th>Platform name</th>
                            <th>Title</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Solution</th>
                            <th>User Name</th>
                            <th>Supplier Name </th>
                            <th>Image </th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody class="short-cut-notes-alerts-list">

                    </tbody>
                </table>
                <!-- Pagination links -->
                <div class="pagination-container-short-cut-notes-alerts"></div>
            </div>
        </div>
    </div>
</div>
{{-- @include('code-shortcut.partials.short-cut-notes-create') --}}


<script type="text/javascript">

    $(document).on("keydown", "#search_short_notes_input", function() {
		var query = $(this).val().toLowerCase();
			$("#short_cut_notes_table tr").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(query) > -1)
		});
	});
 </script>