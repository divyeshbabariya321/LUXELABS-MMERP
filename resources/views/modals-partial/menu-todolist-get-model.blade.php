<div id="menu-todolist-get-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Todo List</h5>
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
                                        <div class="row">
                                            <div class="col-4 pr-0">
                                                <label for="todolist_search">Search Keyword:</label>
                                                <input type="text" name="todolist_search"
                                                    class="dev-todolist-table w-100" class="form-control"
                                                    placeholder="Search Keyword">

                                            </div>
                                            <div class="col-3 pr-0">
                                                <div class="form-group">
                                                    <label for="start_date">Start Date:</label>
                                                    <input type="date" class="form-control"
                                                        id="todolist_start_date" name="start_date">
                                                </div>
                                            </div>
                                            <div class="col-3 pr-0">
                                                <div class="form-group">
                                                    <label for="end_date">End Date:</label>
                                                    <input type="date" class="form-control"
                                                        id="todolist_end_date" name="end_date">
                                                </div>
                                            </div>
                                            <div class="col-2 pr-0">
                                                <div class="form-group">
                                                    <label for="button" class="w-100">&nbsp;</label>

                                                    <button type="button"
                                                        class="btn btn-secondary btn-todolist-search-menu"><i
                                                            class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Subject</th>
                                                    <th>Category</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody class="show-search-todolist-list">
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