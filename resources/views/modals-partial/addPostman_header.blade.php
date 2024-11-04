<div id="addPostman_header" tabindex="-2" class="modal fade" role="dialog" style="z-index: 5000; ">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                <div id="add-mail-content">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><span id="titleUpdate">Add</span> Command</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="magentoForm" method="post">
                                @csrf

                                <div class="form-row">
                                    <input type="hidden" id="command_id" name="id" value="" />

                                    @auth
                                        @if ($isAdmin)
                                            <div class="form-group col-md-12">
                                                <label for="title">User Name</label>
                                                <select name="user_permission[]" multiple
                                                    class="form-control dropdown-mul-1" style="width: 100%"
                                                    id="user_permission" required>
                                                    <option>--Users--</option>
                                                    @foreach ($users as $key => $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    @endauth

                                    <div class="form-group col-md-12">
                                        <label for="title">Website</label>
                                        <div class="dropdown-sin-1">

                                            <select name="websites_ids[]"
                                                class="websites_ids form-control dropdown-mul-1"
                                                style="width: 100%;" id="websites_ids" required>
                                                <option>--Website--</option>
                                                <option value="ERP">ERP</option>
                                                @foreach ($websites as $website)
                                                    <option value="{{ $website->id }}"
                                                        data-website="{{ $website->website }}">
                                                        {{ $website->title }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="assets_manager_id">Assets Manager <span
                                                id="am-client-id"></span></label>
                                        <div class="dropdown-sin-1">

                            <div class="form-group col-md-12">
                                <label for="command_name">Command Name</label>
                                

                            </div>
                            <div class="form-group col-md-12">
                                <label for="command_type">Command</label>
                                

                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="working_directory">Working Directory</label>
                                        <input type="text" name="working_directory" value=""
                                            class="form-control" id="working_directory"
                                            placeholder="Enter the working directory" required>

                                    </div>
                                </div>
                            </div>
                        </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-secondary submit-form">Save</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>