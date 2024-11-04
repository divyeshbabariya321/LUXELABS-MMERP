<div id="shortcut-header-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6 d-inline form-inline">
                        <input style="width: 87%" type="text" name="category_name" placeholder="Enter New Category"
                            class="form-control mb-3 quick_category">
                        <button class="btn custom-button quick_category_addHeader"
                            style="position: absolute; padding: 5px;"><i class="fa fa-plus"
                                aria-hidden="true"></i></button>
                    </div>
                    <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                        <div style="float: left; width: 86%">
                            <select name="quickCategoryHeader" class="form-control mb-3 quickCategoryHeader">
                                <option value="">Select Category</option>
                                @if(isset($replyCategories) && $replyCategories != null)
                                    @foreach($replyCategories as $category)
                                        <option value="{{ $category->approval_leads }}" data-id="{{$category->id}}" data-value="{{ $category->sub_categories }}">{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div style="float: right; width: 14%;">
                            <a class="btn custom-button delete_categoryHeader" style="padding: 5px;"><i
                                    class="fa fa-trash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 d-inline form-inline">
                        <input style="width: 87%" type="text" name="quick_comment"
                            placeholder="Enter New Quick Comment" class="form-control mb-3 quick_comment">
                        <button class="btn custom-button quick_comment_addHeader"
                            style="position: absolute; padding: 5px;"><i class="fa fa-plus"
                                aria-hidden="true"></i></button>
                    </div>
                    <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                        <div style="float: left; width: 86%">
                            <select name="quickSubCategoryHeader" class="form-control quickSubCategoryHeader">
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                        <div style="float: right; width: 14%;">
                            <a class="btn custom-button delete_sub_categoryHeader" style="padding: 5px;"><i
                                    class="fa fa-trash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 d-inline form-inline p-0" style="padding-left: 0px;">
                    </div>
                    <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                        <div style="float: left; width: 86%">
                            <select name="quickComment" class="form-control quickCommentEmailHeader">
                                <option value="">Quick Reply</option>
                            </select>
                        </div>
                        <div style="float: right; width: 14%;">
                            <a class="btn custom-button delete_quick_commentHeader" style="padding: 5px;"><i
                                    class="fa fa-trash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
<script type="text/javascript">
   

    $(document).ready(function() {
        var siteHelpersHeader = {
            quickCategoryHeaderAdd: function(ele) {
                var $modal = ele.closest("#shortcut-header-modal"),
                    quickCategoryHeader = $modal.find(".quickCategoryHeader option:selected"),
                    quickCategoryHeaderId = quickCategoryHeader.data('id'),
                    textBox = ele.closest("div").find(".quick_category"),
                    categoryName = textBox.val();

                if (categoryName === "") {
                    alert("Please Enter Category!!");
                    return;
                }

                var params = {
                    method: 'post',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        name: categoryName,
                        quickCategoryHeaderId: quickCategoryHeaderId
                    },
                    url: "/add-reply-category"
                };

                var callback = quickCategoryHeaderId ? "afterquickSubCategoryHeaderAdd" :
                    "afterquickCategoryHeaderAdd";
                this.sendAjax(params, callback);
            },

            afterquickSubCategoryHeaderAdd: function(response) {
                $(".quick_category").val('');
                $(".quickSubCategoryHeader").append(
                    `<option value="[]" data-id="${response.data.id}">${response.data.name}</option>`
                );
            },

            afterquickCategoryHeaderAdd: function(response) {
                $(".quick_category").val('');
                $(".quickCategoryHeader").append(
                    `<option value="[]" data-id="${response.data.id}">${response.data.name}</option>`
                );
            },

            deleteCategory: function(ele, type) {
                var $modal = ele.closest("#shortcut-header-modal"),
                    $category = $modal.find(type),
                    categoryId = $category.children("option:selected").data('id');

                if ($category.val() === "") {
                    alert(
                        `Please Select ${type === '.quickCategoryHeader' ? 'Category' : 'Sub Category'}!!`
                        );
                    return;
                }

                if (!confirm(
                        `Are you sure you want to delete this ${type === '.quickCategoryHeader' ? 'category' : 'sub category'}?`
                    )) {
                    return;
                }

                var params = {
                    method: 'post',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: categoryId
                    },
                    url: "/destroy-reply-category"
                };

                this.sendAjax(params, "pageReload");
            },

            deleteQuickCommentHeader: function(ele) {
                var $modal = ele.closest("#shortcut-header-modal"),
                    quickCommentId = $modal.find(".quickCommentEmailHeader option:selected").data('id');

                if (!quickCommentId) {
                    alert("Please Select Quick Comment!!");
                    return;
                }

                if (!confirm("Are you sure you want to delete this comment?")) {
                    return;
                }

                var params = {
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/reply/" + quickCommentId
                };

                this.sendAjax(params, "pageReload");
            },

            quickCommentAddHeader: function(ele) {
                var $modal = ele.closest("#shortcut-header-modal"),
                    textBox = ele.closest("div").find(".quick_comment"),
                    quickCategoryHeaderId = $modal.find(".quickCategoryHeader option:selected").data(
                        'id'),
                    quickSubCategoryHeaderId = $modal.find(".quickSubCategoryHeader option:selected")
                    .data('id'),
                    comment = textBox.val();

                if (!comment) {
                    alert("Please Enter New Quick Comment!!");
                    return;
                }

                if (!quickCategoryHeaderId) {
                    alert("Please Select Category!!");
                    return;
                }

                var formData = new FormData();
                formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
                formData.append("reply", comment);
                formData.append("category_id", quickCategoryHeaderId);
                formData.append("sub_category_id", quickSubCategoryHeaderId);
                formData.append("model", 'Approval Lead');

                var params = {
                    method: 'post',
                    data: formData,
                    url: "/reply"
                };

                this.sendFormDataAjax(params, "afterquickCommentAddHeader");
            },

            afterquickCommentAddHeader: function(reply) {
                $(".quick_comment").val('');
                $('.quickCommentEmailHeader').append($('<option>', {
                    value: reply,
                    text: reply
                }));
            },

            changequickCategoryHeader: function(ele) {
                var $modal = ele.closest("#shortcut-header-modal"),
                    selectedOption = ele.find('option:selected'),
                    dataValue = selectedOption.data('value'),
                    replies = JSON.parse(ele.val() || "[]");

                var $subCategoryHeader = $modal.find('.quickSubCategoryHeader').empty().append(
                    '<option value="">Select Sub Category</option>');
                if (dataValue) {
                    dataValue.forEach(function(category) {
                        $subCategoryHeader.append(
                            `<option value="${category.name}" data-id="${category.id}">${category.name}</option>`
                        );
                    });
                }

                var $commentEmailHeader = $modal.find('.quickCommentEmailHeader').empty().append(
                    '<option value="">Quick Reply</option>');
                if (replies) {
                    replies.forEach(function(reply) {
                        $commentEmailHeader.append(
                            `<option value="${reply.reply}" data-id="${reply.id}">${reply.reply}</option>`
                        );
                    });
                }
            },

            changeQuickComment: function(ele) {
                var textToCopy = ele.val();
                $("<input>").val(textToCopy).appendTo("body").select();
                document.execCommand("copy");
                alert("Text copied");
            },

            changequickSubCategoryHeader: function(ele) {
                var selectedId = ele.find('option:selected').data('id');
                var url = '/email/email-replise/' + selectedId;

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'GET'
                }).done(function(response) {
                    if (response) {
                        var replies = JSON.parse(response),
                            $commentEmailHeader = ele.closest("#shortcut-header-modal").find(
                                '.quickCommentEmailHeader').empty().append(
                                '<option value="">Quick Reply</option>');
                        replies.forEach(function(reply) {
                            $commentEmailHeader.append(
                                `<option value="${reply.reply}" data-id="${reply.id}">${reply.reply}</option>`
                            );
                        });
                    }
                }).fail(function() {
                    console.error("Failed to load replies.");
                });
            },

            pageReload: function() {
                location.reload();
            },

            sendAjax: function(params, callback) {
                $.ajax(params).done(function(response) {
                    if (callback && typeof siteHelpersHeader[callback] === 'function') {
                        siteHelpersHeader[callback](response);
                    }
                }).fail(function(err) {
                    console.error("Ajax request failed:", err);
                });
            },

            sendFormDataAjax: function(params, callback) {
                $.ajax({
                    url: params.url,
                    type: params.method,
                    data: params.data,
                    processData: false,
                    contentType: false
                }).done(function(response) {
                    if (callback && typeof siteHelpersHeader[callback] === 'function') {
                        siteHelpersHeader[callback](response);
                    }
                }).fail(function(err) {
                    console.error("Form data Ajax request failed:", err);
                });
            }
        };

        $.extend(siteHelpersHeader, common);

        $(document).on('click', '.quick_category_addHeader', function() {
            siteHelpersHeader.quickCategoryHeaderAdd($(this));
        });
        $(document).on('click', '.delete_categoryHeader', function() {
            siteHelpersHeader.deleteCategory($(this), '.quickCategoryHeader');
        });
        $(document).on('click', '.delete_sub_categoryHeader', function() {
            siteHelpersHeader.deleteCategory($(this), '.quickSubCategoryHeader');
        });
        $(document).on('click', '.delete_quick_commentHeader', function() {
            siteHelpersHeader.deleteQuickCommentHeader($(this));
        });
        $(document).on('click', '.quick_comment_addHeader', function() {
            siteHelpersHeader.quickCommentAddHeader($(this));
        });
        $(document).on('change', '.quickCategoryHeader', function() {
            siteHelpersHeader.changequickCategoryHeader($(this));
        });
        $(document).on('change', '.quickCommentEmailHeader', function() {
            siteHelpersHeader.changeQuickComment($(this));
        });
        $(document).on('change', '.quickSubCategoryHeader', function() {
            siteHelpersHeader.changequickSubCategoryHeader($(this));
        });
    });
</script>
