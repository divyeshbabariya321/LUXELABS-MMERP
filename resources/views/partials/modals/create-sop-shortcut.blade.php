@auth
    <div id="Create-Sop-Shortcut" class="modal fade z-4" role="dialog">
        <div class="modal-dialog">


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Shortcut Model</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="createShortcutForm">
                    <td><input type="file" name="image" hidden></td>
                    <td><input type="text" name="tags[0][name]" hidden></td>
                    <td><input type="text" name="tags[0][value]" hidden></td>
                    <div class="modal-body add_sop_modal">
                        <div class="mb-3">
                            <select class="form-control sop_drop_down ">
                                <option value="sop">Sop</option>
                                <option value="knowledge_base">Knowledge Base</option>
                                <option value="code_shortcut">Code Shortcut</option>
                                <option value="reply_shortcut">Reply Shortcut</option>
                                <option value="devoops_modules">Devoops Modules</option>
                                <option value="todo_shortcut">Todo Shortcut</option>
                            </select>
                        </div>
                        <input type="hidden" name="chat_message_id" value=""
                            class="chat_message_id" />
                        <div class="add_sop_div mt-3">
                            <div>
                                <select class="form-control knowledge_base mb-3" name="sop_knowledge_base"
                                    hidden>
                                    <option value="">Select</option>
                                    <option value="book">Book</option>
                                    <option value="chapter">Chapter</option>
                                    <option value="page">Page</option>
                                    <option value="shelf">Shelf</option>
                                </select>
                            </div>
                            <div>
                                <span class="books_error" style="color:red;"></span>
                            </div>
                            <div class="input-tag-container other-input-tags-container">
                                <div>
                                    <td>Name:</td>
                                    <td><input type="text" name="name"
                                            class="form-control mb-3 name" placeholder="Enter Name"></td>
                                </div>
                                <div>
                                    <td>Category:</td>
                                    <td><input type="text" name="category"
                                            class="form-control mb-3 category" placeholder="Enter Category"
                                            value="Sop"></td>
                                </div>
                                <div>
                                    <td>Description:</td>
                                    <td>
                                        <textarea name="description" id="" cols="30" rows="10" class="form-control sop_description"
                                            placeholder="Enter Description"></textarea>
                                    </td>
                                </div>
                                <div class="sop_solution hidden">
                                    <td>Solution:</td>
                                    <td>
                                        <textarea name="solution" id="" cols="30" rows="10" class="form-control"
                                            placeholder="Enter Solution"></textarea>
                                    </td>
                                </div>
                            </div>
                            <div class="input-tag-container reply-shortcut-input-tags-container hidden">
                                <div class="form-group">
                                    <strong>Quick Reply</strong>
                                    <textarea class="form-control" name="reply" placeholder="Quick Reply" required></textarea>
                                </div>

                                <div class="form-group">
                                    <strong>Model</strong>
                                    <select class="form-control" name="model" required>
                                        <option value="">Select Model</option>
                                        <option value="Approval Lead">Approval Lead</option>
                                        <option value="Internal Lead">Internal Lead</option>
                                        <option value="Approval Order">Approval Order</option>
                                        <option value="Internal Order">Internal Order</option>
                                        <option value="Approval Purchase">Approval Purchase</option>
                                        <option value="Internal Purchase">Internal Purchase</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <strong>Category</strong>
                                    <select class="form-control" name="category_id" required
                                        id="category_id_dropdown">
                                        
                                    </select>
                                </div>

                                <div class="form-group">
                                    <strong>Sub category</strong>
                                    <select class="form-control" name="sub_category_id" id="subcategory">
                                        <option value="">Select Subcategory</option>
                                    </select>
                                </div>
                            </div>
                            <div class="input-tag-container add-devoops-modules-input-tags-container hidden">
                                <div class="form-group">
                                    <strong>Category :</strong>
                                    <input type="hidden" value="1" name="category_type"
                                        id="category_type">
                                    <input id="category_name" placeholder="Enter Category"
                                        class="form-control" required="required" autocomplete="off"
                                        name="category_name" type="text">
                                </div>
                            </div>

                            <div class="input-tag-container add-todo-shortcut-input-tags-container hidden">
                                <div class="form-group">
                                    <div>
                                        <td>Name:</td>
                                        <td><input type="text" name="todo_title"
                                                class="form-control mb-3" placeholder="Enter Title"></td>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <span>Subject:</span>
                                    <input type="text" name="todo_subject" class="form-control"
                                        placeholder="Enter Subject">
                                </div>

                                <div class="form-group">
                                    <span>Category:</span>
                                    <select name="todo_category_id" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach ($todoCategories as $todoCategory)
                                            <option value="{{ $todoCategory->id }}">
                                                {{ $todoCategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <span>Status:</span>
                                    <select name="todo_status" class="form-control">
                                        @foreach ($todoStatus as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" style="margin-bottom: 0px;">
                                    <span>Date:</span>
                                    <div class='input-group date todo-date' id='todo-date-shortcut'>
                                        <input type="text" class="form-control global add_todo_date"
                                            name="todo_date" placeholder="Date">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-top: 15px;">
                                    <span>Remark:</span>
                                    <input type="text" name="todo_remark" class="form-control">
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-default create_shortcut_submit">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endauth