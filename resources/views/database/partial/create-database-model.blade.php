<div id="menu-create-database-model" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width:500px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Database</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="database-form">
                            @csrf
                            <input type="hidden" name="database_user_id" class="app-database-user-id"
                                id="database-user-id" value="">
                            <div class="row">
                                <div class="col">
                                    <select class="form-control choose-db" name="connection">
                                        @if(isset($storeWebsiteConnections))
                                            @foreach ($storeWebsiteConnections as $key => $storeWebsiteConnection)
                                                <option {{ $storeWebsiteConnection == $key ? "selected='selected'" : '' }}
                                                    value="{{ $key }}">{{ $storeWebsiteConnection }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-control choose-username" name="username">
                                        <option value="">Select User</option>
                                        @if(isset($users))
                                            @foreach ($users as $key => $user)
                                                <option value="{{ $user->id }}"
                                                    data-name="{{ $user->name }}">{{ $user->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col">
                                    <input type="text" name="password" class="database_password"
                                        class="form-control" placeholder="Enter password">
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-secondary btn-database-add"
                                        data-id="">ADD</button>

                                    <button type="button"
                                        class="btn btn-secondary btn-delete-database-access d-none"
                                        data-connection="" data-id="">DELETE ACCESS</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-5">
                    <form>
                        @csrf
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col">
                                    <input type="hidden" name="connection" value="">
                                    <input type="text" name="search"
                                        class="form-control app-search-table" placeholder="Search Table name">
                                </div>
                                <div class="col">
                                    <div class="form-group col-md-5">
                                        <select class="form-control assign-permission-type"
                                            name="assign_permission">
                                            <option value="read">Read</option>
                                            <option value="write">Write</option>
                                        </select>
                                    </div>
                                    <button type="button"
                                        class="btn btn-secondary btn-assign-permission assign-permission"
                                        data-id="">Assign Permission</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-2">
                            <table class="table table-bordered" id="database-table-list1">
                                <thead>
                                    <tr>
                                        <th width="5%"></th>
                                        <th width="95%">Table name</th>
                                    </tr>
                                </thead>
                                <tbody class="menu_tbody">
                                    @if(isset($database_table_name))
                                        @foreach (json_decode($database_table_name) as $name)
                                            <tr>
                                                <td><input type="checkbox" name="tables[]"
                                                        value="{{ $name->TABLE_NAME }}"></td>
                                                <td>{{ $name->TABLE_NAME }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>