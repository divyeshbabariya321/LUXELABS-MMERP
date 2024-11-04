<div id="SearchGoogleDocModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Google Docs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="database-form">
                            @csrf
                            <div class="row">
                                <div class="col-12 pb-3">
                                    <input type="text" name="task_search" class="google-doc-search-table form-control" placeholder="Enter File Name">
                                    <button type="button" class="btn btn-secondary btn-google-doc-search-menu" ><i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-12">
                                    <table class="table table-sm table-bordered show-search-google-doc-list">
                                        <thead>
                                        <tr>
                                            <th width="2%">ID</th>
                                            <th width="4%">No</th>
                                            <th width="20%">File Name</th>
                                            <th width="10%">Category</th>
                                            <th width="10%">Task</th>
                                            <th width="10%">Created By</th>
                                            <th width="10%">Created Date</th>
                                            <th width="10%">URL</th>
                                            <th width="4%">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function load_google_doc_search_table(url) {
        var keyword = $('.google-doc-search-table').val();

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                subject: keyword,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function (response) {
                $("#loading-image").hide();
                $('.show-search-google-doc-list').html(response);
            },
            error: function () {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    }

    $(".btn-google-doc-search-menu").on("click", function(){
        load_google_doc_search_table("{{route('google-docs.google.module.search')}}");
    });

    $(document).ready(function(){
        $(document).on('click', '.pagination a', function(event){
            // event.preventDefault(); 
            var page = $(this).attr('href').split('page=')[1];
            load_google_doc_search_table(`{{route('google-docs.google.module.search')}}?page=${page}`);
        });
    });
</script>
