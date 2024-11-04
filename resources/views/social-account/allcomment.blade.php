@extends('layouts.app')

@section('styles')

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
        table {
table-layout: fixed !important;
}

table tr td {
max-width: 100% !important;
overflow-x: auto !important;
}

.cls_textarea_subbox { display: flex; justify-content: space-between; align-items: center; }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Comments ({{$comments->count()}})</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 margin-tb d-flex align-items-center justify-content-between px-5">
            <form class="form-inline" method="GET">
                <div class="form-group">
                    {{ html()->text("search", request()->get("search", ""))->class("form-control")->placeholder("Enter keyword for search") }}
                </div>
                <br>
                <button type="submit" class="btn ml-2"><i class="fa fa-filter"></i></button>
            </form>
        </div>
    </div>
    <div class="mt-3 col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="3%">#</th>
                    <th width="15%">Comment</th>
                    <th width="10%">Translated Comment</th>
                    <th width="7%">Translated Comment score</th>
                    <th>Translation Language</th>
                    <th>Author</th>
                    <th>Comment Date</th>
                    <th>Website</th>
                    <th>Platform</th>
                    <th>Config Account</th>
                    <th>Post ID</th>
                    <th width="10%">Reply to comment</th>
                    <th width="15%">Shortcuts</th>
                    <th width="15%">Comment Threads</th>
            </thead>
            <tbody>
                @forelse($comments as $key => $value)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            <div style="word-break: break-word;">
                                <p class="mb-0">{{ $value->message }}</p>
                            </div>
                            @if ($value->photo)
                              <img src="{{ $value->photo }}" width="100" alt="{{ $value->message }}">
                            @endif
                        </td>
                        <td>
                            <div style="word-break: break-word;">
                                <p class="mb-0">
                                    {{ $value->translated_message }}
                                </p>
                            </div>
                            @if ($value->photo)
                              <img src="{{ $value->photo }}" width="100" alt="{{ $value->message }}">
                            @endif
                        </td>
                        <td>
                            @if ($value->translated_message_score)
                                {{ $value->translated_message_score }}
                            @else
                                <a href="javascript::void(0);" data-id="{{ $value->id }}" class="btn-get-translate-score">
                                    <i class="fa fa-dashboard" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td>{{ $value->page_language }}</td>
                        <td>{{ $value->commented_by_user }}</td>
                        <td>{{ $value->created_at->diffForHumans() }}</td>
                        <td>{{ $value->post->config?->storeWebsite->title }}</td>
                        <td>{{ $value->post->config?->platform }}</td>
                        <td>{{ $value->post->config?->name }}</td>
                        <td>
                            <a href="{{ route('social.post.view', $value->post_id) }}">{{ $value->post_id }}</a></td>
                        <td>
                            @if($value->can_comment)
                                <div class="cls_textarea_subbox">
                                    <div class="btn-toolbar" role="toolbar">
                                        <div class="w-75">
                                            <textarea rows="1"
                                                    class="form-control quick-message-field cls_quick_message addToAutoComplete"
                                                    name="message" placeholder="Message"
                                                    id="textareaBox_{{ $value->id }}"
                                                    data-customer-id="{{ $value->id }}"></textarea>
                                        </div>
                                        <div class="w-25 pl-2 d-flex align-items-center" role="group" aria-label="First group">
                                            <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image send-message1"
                                                    data-id="textareaBox_{{ $value->id }}">
                                                <img src="/images/filled-sent.png">
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            @else
                                <span>Cannot reply to this comment</span>
                            @endif
                        </td>
                        <td id="shortcutsIds">
                            <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image" onclick="ShowShortcuts('{{$value->id}}')">
                                <img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;">
                            </button>
                            <div class="shortcuts_{{ $value->id }} d-none">
                                @include('social-account.shortcuts-all-comments')
                            </div>
                        </td>
                        <td>
                            @if($value->sub_comments->count() > 0)
                                <div class="expand-comment-threads" data-id="{{$key}}">
                                    <a href="#" class="hide-thread-{{$key}}">View Threads</a>
                                    <div class="show-thread-{{$key}} hidden">
                                        @foreach ($value->sub_comments as $comment)
                                            <ul>
                                            <li>
                                                <strong>{{ $comment->commented_by_user }}</strong>
                                                <p class="mb-0">{{ $comment->message }}</p>
                                                <small>{{ $comment->created_at->diffForHumans() }}</small>
                                            </li>
                                            </ul>                                
                                        @endforeach
                                    </div>
                                </div>
                            @else
                              <p>No replies</p>  
                            @endempty
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" align="center">No Comments found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if (isset($comments))
            {{ $comments->links() }}
        @endif
    </div>
@endsection

@section('scripts')
<script>

    $(document).on('click', '.expand-comment-threads', function () {
          var id = $(this).data('id');
          var full = '.expand-comment-threads .show-thread-'+id;
          var mini ='.expand-comment-threads .hide-thread-'+id;
          $(full).toggleClass('hidden');
          $(mini).toggleClass('hidden');
    });

    $(document).on("change", "#quick-reply", function(e) {
        var message = $(this).val();
        var select = $(this);

        var comment_id = $(this).data('id');
        
        $("textarea#textareaBox_" + comment_id).val($.trim(message));
    });
function ShowShortcuts(id){
    $(".shortcuts_"+id).toggleClass('d-none')
}

$('#social_config').select2({
    placeholder: 'Select Platform',
});
$('#store_website_id').select2({
    placeholder: 'Select Website',
});


$(document).on('click', '.send-message1', function() {
    const textareaId = $(this).data('id');
    const value = $(`#${textareaId}`).val();
    //const configId = document.getElementById("config-id").value;  
    const configId = $(this).data('post_id');
    const contactId = $(`#${textareaId}`).data('customer-id');
    if (value.trim()) {
        $("#loading-image").show();
        $.ajax({
            url: "{{ route('social.dev.reply.comment') }}",
            method: 'POST',
            async: true,
            data: {
                _token: '{{ csrf_token() }}',
                input: value,
                contactId: contactId,
                configId: configId
            },
            success: function(res) {
                $("#loading-image").hide();
                document.getElementById("textareaBox_"+contactId).value = '';
                toastr["success"]("Message successfully send!", "Message")
            },
            error: function(error) {
                console.log(error.responseJSON);
                alert("Counldn't send messages")
                $("#loading-image").hide();
            }
        })
    } else {
        alert("Please enter a message")
    }
})

</script>
<script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
<script type="text/javascript">
var siteHelpers = {
            
    quickCategoryAdd : function(ele) {
        var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
        var quickCategoryId = quickCategory.children("option:selected").data('id');
        var textBox = ele.closest("div").find(".quick_category");
        if (textBox.val() == "") {
            alert("Please Enter Category!!");
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                name : textBox.val(),
                quickCategoryId : quickCategoryId
            },
            url: "/add-reply-category"
        };

        if(quickCategoryId!=''){
            siteHelpers.sendAjax(params,"afterQuickSubCategoryAdd");
        } else {
            siteHelpers.sendAjax(params,"afterQuickCategoryAdd");
        }
    },
    afterQuickSubCategoryAdd : function(response) {
        $(".quick_category").val('');
        $(".quickSubCategory").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
    },
    afterQuickCategoryAdd : function(response) {
        $(".quick_category").val('');
        $(".quickCategory").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
    },
    deleteQuickCategory : function(ele) {
        var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
        if (quickCategory.val() == "") {
            alert("Please Select Category!!");
            return false;
        }
        var quickCategoryId = quickCategory.children("option:selected").data('id');
        if (!confirm("Are sure you want to delete category?")) {
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                id : quickCategoryId
            },
            url: "/destroy-reply-category"
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    deleteQuickSubCategory : function(ele) {
        var quickSubCategory = ele.closest("#shortcutsIds").find(".quickSubCategory");
        if (quickSubCategory.val() == "") {
            alert("Please Select Sub Category!!");
            return false;
        }
        var quickSubCategoryId = quickSubCategory.children("option:selected").data('id');
        if (!confirm("Are sure you want to delete sub category?")) {
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                id : quickSubCategoryId
            },
            url: "/destroy-reply-category"
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    deleteQuickComment : function(ele) {
        var quickComment = ele.closest("#shortcutsIds").find(".quickCommentEmail");
        if (quickComment.val() == "") {
            alert("Please Select Quick Comment!!");
            return false;
        }
        var quickCommentId = quickComment.children("option:selected").data('id');
        if (!confirm("Are sure you want to delete comment?")) {
            return false;
        }
        var params = {
            method : 'DELETE',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content')
            },
            url: "/reply/" + quickCommentId,
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    pageReload : function(response) {
        location.reload();
    },
    quickCommentAdd : function(ele) {
        var textBox = ele.closest("div").find(".quick_comment");
        var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
        var quickSubCategory = ele.closest("#shortcutsIds").find(".quickSubCategory");
        if (textBox.val() == "") {
            alert("Please Enter New Quick Comment!!");
            return false;
        }
        if (quickCategory.val() == "") {
            alert("Please Select Category!!");
            return false;
        }
        var quickCategoryId = quickCategory.children("option:selected").data('id');
        var quickSubCategoryId = quickSubCategory.children("option:selected").data('id');
        var formData = new FormData();
        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
        formData.append("reply", textBox.val());
        formData.append("category_id", quickCategoryId);
        formData.append("sub_category_id", quickSubCategoryId);
        formData.append("model", 'Approval Lead');
        var params = {
            method : 'post',
            data : formData,
            url: "/reply"
        };
        siteHelpers.sendFormDataAjax(params,"afterQuickCommentAdd");
    },
    afterQuickCommentAdd : function(reply) {
        $(".quick_comment").val('');
        $('.quickCommentEmail').append($('<option>', {
            value: reply,
            text: reply
        }));
    },
    changeQuickCategory : function (ele) {

        var selectedOption = ele.find('option:selected');
        var dataValue = selectedOption.data('value');

        ele.closest("#shortcutsIds").find('.quickSubCategory').empty();
        ele.closest("#shortcutsIds").find('.quickSubCategory').append($('<option>', {
            value: '',
            text: 'Select Sub Category'
        }));
        dataValue.forEach(function (category) {
            ele.closest("#shortcutsIds").find('.quickSubCategory').append($('<option>', {
                value: category.name,
                text: category.name,
                'data-id': category.id
            }));
        });

        if (ele.val() != "") {
            var replies = JSON.parse(ele.val());
            ele.closest("#shortcutsIds").find('.quickCommentEmail').empty();
            ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                value: '',
                text: 'Quick Reply'
            }));
            replies.forEach(function (reply) {
                ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                    value: reply.reply,
                    text: reply.reply,
                    'data-id': reply.id
                }));
            });
        }
    },
    changeQuickComment : function (ele) {
        $('#textareaBox_'+ele.attr('data-id')).val(ele.val());        
    },
    changeQuickSubCategory : function (ele) {
        var selectedOption = ele.find('option:selected');
        var dataValue = selectedOption.data('id');

        var userEmaillUrl = '/social/email-replise/'+dataValue;

        $.ajax({        
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: userEmaillUrl,
            type: 'get',
        }).done( function(response) {

            if(response!=''){
                var replies = JSON.parse(response);
                ele.closest("#shortcutsIds").find('.quickCommentEmail').empty();
                ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                    value: '',
                    text: 'Quick Reply'
                }));
                replies.forEach(function (reply) {
                    ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                        value: reply.reply,
                        text: reply.reply,
                        'data-id': reply.id
                    }));
                });
            }
            
        }).fail(function(errObj) {
        })
    },
    getTranslatedTextScore: function (ele) {
        let comment_id = ele.data("id");
        var params = {
            url: "/social/comments/" +  comment_id + "/get-translated-text-score",
            method: "get",
            beforeSend: function () {
                $("#loading-image").show();
            },
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
};

$.extend(siteHelpers, common);

$(document).on('click', '.quick_category_add', function () {
    siteHelpers.quickCategoryAdd($(this));
});
$(document).on('click', '.delete_category', function () {
    siteHelpers.deleteQuickCategory($(this));
});
$(document).on('click', '.delete_sub_category', function () {
    siteHelpers.deleteQuickSubCategory($(this));
});
$(document).on('click', '.delete_quick_comment', function () {
    siteHelpers.deleteQuickComment($(this));
});
$(document).on('click', '.quick_comment_add', function () {
    siteHelpers.quickCommentAdd($(this));
});
$(document).on('change', '.quickCategory', function () {
    siteHelpers.changeQuickCategory($(this));
});
$(document).on('change', '.quickCommentEmail', function () {
    siteHelpers.changeQuickComment($(this));
});
$(document).on('change', '.quickSubCategory', function () {
    siteHelpers.changeQuickSubCategory($(this));
});
$(document).on('click', '.btn-get-translate-score', function () {
    siteHelpers.getTranslatedTextScore($(this));
});
</script>
@endsection
