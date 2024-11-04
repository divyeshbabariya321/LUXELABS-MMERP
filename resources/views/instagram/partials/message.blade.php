<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th width="5%">Account Name</th>
            <th width="5%">Platform</th>
            <th>Website</th>
            <th>Message (Newest)</th>
            <th>Translated Message (Newest)</th>
            <th>Translated Score</th>
            <th>Translation Language</th>
            <th width="20%">Message Box</th>
            <th>From</th>
            <th width="15%">Shortcuts</th>
            <th>Message Received Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($socialContact as $contact)
            <tr class="customer-raw-line">
                <td>{{ $contact->socialConfig->name }}</td>
                <td>{{ ucfirst($contact->socialConfig->platform) }}</td>
                <td>{{ $contact->socialConfig->storeWebsite->title }}</td>
                @if($contact->getLatestSocialContactThread)
                    <td class="log-message-popup"
                        data-log_message="{{ $contact->getLatestSocialContactThread->text }}">
                        {{ substr($contact->getLatestSocialContactThread->text, 0, 15) }}...
                    </td>
                @else
                    <td><small class="text-secondary"> <small>No conversations found</small></td>
                @endif
                @if($contact->getLatestSocialContactThread)
                    {{-- @php
                        $translated_message = get_translation($contact->socialConfig->page_language ? $contact->socialConfig->page_language : 'en', $contact->getLatestSocialContactThread->text)
                    @endphp --}}
                    <td class="log-message-popup"
                        data-log_message="{{ $contact->getLatestSocialContactThread->translated_text }}">
                        {{ substr($contact->getLatestSocialContactThread->translated_text, 0, 15) }}...
                    </td>
                    <td>
                        @if ($contact->getLatestSocialContactThread->translated_text && !$contact->getLatestSocialContactThread->translated_text_score)
                            <button type="button" title="Get translated message score" data-id="{{$contact->getLatestSocialContactThread->id}}" class="btn btn-get-translate-score"style="padding: 0px 1px !important;">
                                <i class="fa fa-dashboard" aria-hidden="true"></i>
                            </button>
                        @else
                            {{ $contact->getLatestSocialContactThread->translated_text_score }}
                        @endif
                    </td>
                @else
                    <td><small class="text-secondary"> <small>No conversations found</small></td>
                    <td></td>
                @endif
                <td>{{ $contact->socialConfig->page_language }}</td>
                <td class="message-input p-0 pt-2 pl-3">
                    <div class="cls_textarea_subbox">
                        <div class="btn-toolbar" role="toolbar">
                            <div class="w-75">
                                <textarea rows="1"
                                          class="form-control quick-message-field cls_quick_message addToAutoComplete"
                                          name="message" placeholder="Message" id="textareaBox_{{ $contact->id }}"
                                          data-customer-id="{{ $contact->id }}"></textarea>
                            </div>
                            <div class="w-25 pl-2" role="group" aria-label="First group">
                                <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image send-message1"
                                    data-id="textareaBox_{{ $contact->id }}">
                                        <img src="/images/filled-sent.png">
                                </button>
                                <button type="button"
                                        class="btn btn-sm m-0 p-0 mr-1 btn-image load-contact-communication-modal"
                                        data-object="social-contact" data-id="{{ $contact->id }}" data-load-type="text"
                                        data-all="1" title="Load messages">
                                    <img src="{{ asset('images/chat.png') }}" alt="">
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    {{ $contact->name }}
                </td>
                <td id="shortcutsIds">
                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image" onclick="ShowShortcuts('{{ $contact->id }}')">
                        <img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;">
                    </button>
                    <div class="shortcuts_{{ $contact->id }} d-none">
                        @include('instagram.reply-shortcuts')
                    </div>
                </td>
                <td>
                    @if($contact->getLatestSocialContactThread)
                        {{ $contact->getLatestSocialContactThread->created_at?->diffForHumans() }}
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!--Log Messages Modal -->
<div id="logMessageModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">User Input</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>
  $(document).on("click", ".log-message-popup", function() {
    $("#logMessageModel p").text($(this).data("log_message"));
    $("#logMessageModel").modal("show");
  });

  $(document).on("click", ".send-message1", function() {
    const textareaId = $(this).data("id");
    const value = $(`#${textareaId}`).val();
    const contactId = $(`#${textareaId}`).data("customer-id");
    if (value.trim()) {
      $("#loading-image").show();
      $.ajax({
        url: "{{ route('social.message.send') }}",
        method: "POST",
        async: true,
        data: {
          _token: '{{ csrf_token() }}',
          input: value,
          contactId: contactId
        },
        success: function(res) {
          alert(res.message);
          $(`#${textareaId}`).val("");
          $("#loading-image").hide();
        },
        error: function(error) {
          console.log(error.responseJSON);
          alert("Counldn't send messages");
          $("#loading-image").hide();
        }
      });
    } else {
      alert("Please enter a message");
    }
  });

    function ShowShortcuts(id){
        $(".shortcuts_"+id).toggleClass('d-none')
    }
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
            let msg_id = ele.data("id");
            var params = {
                url: "/social/" +  msg_id + "/get-translated-text-score",
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