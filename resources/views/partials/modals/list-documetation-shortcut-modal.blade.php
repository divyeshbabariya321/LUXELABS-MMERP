<div id="short-cut-documentation-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title">Documentation listing</h5>
                <br>
                <form method="get" id="screen_cast_search" style="margin-left:auto">
                    <input id="search-input_documents" type="text" placeholder="Search..">       
                    <button type="button" class="btn btn-secondary" data-toggle="modal" onclick="showdocumentCreateModal()">+</button>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </form>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="list-documentation-shortcut-modal-html">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$("#search-input_documents").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#document_list_table tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
</script>