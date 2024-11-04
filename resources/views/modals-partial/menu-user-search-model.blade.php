<div id="menu-user-search-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User Search</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex" id="search-bar">
                                <input type="text" value="" name="search" id="menu_user_search"
                                    class="form-control" placeholder="Search Here.." style="width: 30%;">
                                <a title="User Search" type="button" id="menu-user-search-btn"
                                    class="menu-user-search-btn btn btn-sm btn-image " style="padding: 10px"><span>
                                        <img src="{{ asset('images/search.png') }}" alt="Search"></span></a>
                                <span class="processing-txt d-none">{{ __('Loading...') }}</span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="table table-bordered table-responsive mt-3">
                                <table class="table table-bordered page-notes"
                                    style="font-size:13.8px;border:0px !important; table-layout:fixed"
                                    id="NameTable-app-layout">
                                    <thead>
                                        <tr>
                                            <th width="10%">ID</th>
                                            <th width="30%">Name</th>
                                            <th width="30%">Email</th>
                                            <th width="30%">Phone</th>
                                        </tr>
                                    </thead>
                                    <tbody class="user_search_global_result">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>