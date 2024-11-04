<div id="google-drive-screen-cast-alerts-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title">Google drive Screen Cast</h5>
                <br>
                <form method="get" id="screen_cast_search" style="margin-left:auto">
                    <input id="search-input" type="text" placeholder="Search..">       
                    <button type="button" class="btn btn-secondary" onclick="uploadeScreencastModal()">+</button>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </form>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="google-drive-screen-cast-modal-html">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- @include('googledrivescreencast.partials.upload') --}}

<script>
$("#search-input").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#google_screen_cast tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
</script>
