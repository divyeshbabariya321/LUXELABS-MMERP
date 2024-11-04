<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-6 d-inline form-inline">
                <input style="width: 87%" type="text" name="category_name" placeholder="Enter New Category" class="form-control mb-3 quick_category">
                <button class="btn btn-secondary quick_category_add" style="position: absolute;  margin-left: 8px;">+</button>
            </div>
            <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                <div style="float: left; width: 86%">
                    <select name="quickCategory" class="form-control mb-3 quickCategory">
                        <option value="">Select Category</option>

                        @if(isset($replyCategories) && $replyCategories != null)
                            @foreach($replyCategories as $category)
                                <option value="{{ $category->approval_leads }}" data-id="{{$category->id}}">{{ $category->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div style="float: right; width: 14%;">
                    <a class="btn btn-image delete_category"><img src="/images/delete.png"></a>
                </div>
            </div>
            <div class="col-6 d-inline form-inline">
                <input style="width: 87%" type="text" name="quick_comment" placeholder="Enter New Quick Comment" class="form-control mb-3 quick_comment">
                <button class="btn btn-secondary quick_comment_add" style="position: absolute;  margin-left: 8px;">+</button>
            </div>
            <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                <div style="float: left; width: 86%">
                    <select name="quickComment" class="form-control quickCommentEmail">
                        <option value="">Quick Reply</option>
                    </select>
                </div>
                <div style="float: right; width: 14%;">
                    <a class="btn btn-image delete_quick_comment"><img src="/images/delete.png"></a>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  $(document).ready(function () {
        window.buildDialog = {};
        window.pageLocation = "autoreply";
        $(document).on('click', '.expand-row-btn', function () {
            $(this).closest("tr").find(".expand-row").toggleClass('dis-none');
        });
        var pageType = '{{!empty($pageType) ? $pageType : 0 }}';
        $(window).on('hashchange', function () {
            if (window.location.hash) {
                var page = window.location.hash.replace('#', '');
                if (page == Number.NaN || page <= 0) {
                    return false;
                } else {
                    getData(page);
                }
            }
        });
        $('#reply-message').on('focus', function() {
            $(this).attr('rows', '6');
        });
        $('#reply-message').on('blur', function() {
            $(this).attr('rows', '1');
        });
        $('.multiselect-2').select2({width:'92%'});
        $('.select-multiple').select2({width: '100%'});
        // this is helper class we need to move to another location
        // @todo
        var siteHelpers = {
            verifyInstruction :  function(ele) {
                let instructionId = ele.attr('data-instructionId');
                var params = {
                    data : {id: instructionId,_token  : $('meta[name="csrf-token"]').attr('content')},
                    url: '/instruction/verify',
                    method : 'post',
                    dataType: "html"
                }
                siteHelpers.sendAjax(params,"afterVerifyInstrunction",ele);
            },
            afterVerifyInstrunction : function (ele) {
                toastr['success']('Instruction verified successfully', 'success');
                $(ele).html('Verified');
            },
            completeInstruction :  function(ele) {
                var params = {
                    data : {id: ele.data('id'), _token  : $('meta[name="csrf-token"]').attr('content')},
                    url: '/instruction/complete',
                    method : 'post',
                    beforeSend : function() {
                        ele.text('Loading');
                    },
                    doneAjax : function(response) {
                        ele.parent().append(moment(response.time).format('DD-MM HH:mm'));
                        ele.remove();
                    },
                }
                siteHelpers.sendAjax(params);
            },
            changeMessageStatus : function(ele) {
                var params = {
                    url: ele.data('url'),
                    dataType: "html"
                };
                siteHelpers.sendAjax(params,"afterChangeMessageStatus", ele);
            },
            afterChangeMessageStatus : function(ele) {
                ele.closest('tr').removeClass('text-danger');
                ele.closest('td').html('Read');
                ele.remove();
            },
            approveMessage : function(ele) {
                var params = {
                    method : 'post',
                    data : {messageId: ele.data('id'), _token  : $('meta[name="csrf-token"]').attr('content')},
                    url: "/whatsapp/approve/customer"
                };
                siteHelpers.sendAjax(params,"afterApproveMessage", ele);
            },
            afterApproveMessage : function(ele) {
                ele.parent().html('Approved');
                ele.closest('tr').removeClass('row-highlight');
            },
            changeLeadStatus :  function(ele) {
                var lead_id = ele.data('leadid');
                var params = {
                    method : 'post',
                    data : {status: ele.data('id'), _token  : $('meta[name="csrf-token"]').attr('content')},
                    url: "/leads/" + lead_id + "/changestatus",
                    dataType: "html"
                };
                siteHelpers.sendAjax(params,"afterChangeLeadStatus", ele);
            },
            afterChangeLeadStatus : function(ele) {
                ele.parent('div').children().each(function (index) {
                    $(this).removeClass('active-bullet-status');
                });
                ele.addClass('active-bullet-status');
            },
            changeOrderStatus :  function(ele) {
                var orderId = ele.data('orderid');
                var params = {
                    method : 'post',
                    data : {status: ele.attr('title'), _token  : $('meta[name="csrf-token"]').attr('content')},
                    url: "/order/" + orderId + "/changestatus",
                    dataType: "html"
                };
                siteHelpers.sendAjax(params,"afterChangeLeadStatus", ele);
            },
            afterChangeOrderStatus : function(ele) {
                toastr['success']('Status changed successfully!', 'Success');
                ele.siblings('.change-order-status').removeClass('active-bullet-status');
                ele.addClass('active-bullet-status');
                if (ele.attr('title') == 'Product shiped to Client') {
                    $('#tracking-wrapper-' + id).css({'display': 'block'});
                }
            },
            sendPdf : function(ele) {
                var selectedBox = ele.closest(".send_pdf_selectbox_box");
                var allPdfs = selectedBox.find(".send_pdf_selectbox").select2("val");
                    if(allPdfs.length > 0) {
                        var params = {
                            method : 'post',
                            data : {
                                _token : $('meta[name="csrf-token"]').attr('content'),
                                send_pdf: true,
                                customer_id : ele.data("customerid"),
                                images: JSON.stringify([allPdfs]),
                                status: 1,
                                json:1
                            },
                            url: "/attachImages/queue"
                        };
                        siteHelpers.sendAjax(params,"afterSendPdf", ele);
                    }
            },
            afterSendPdf : function(response) {
                var closestSelect = response.closest(".send_pdf_selectbox_box");
                    if(closestSelect.length > 0) {
                        var selectbox = closestSelect.find(".send_pdf_selectbox");
                        /*selectbox.val("");
                        selectbox.select2("val", "");*/
                    }
                toastr["success"]("Message sent successfully!", "Message");
            },
            sendGroup : function(ele, send_pdf) {
                $("#confirmPdf").modal("hide");
                var customerId = ele.data('customerid');
                var groupId = $('#group' + customerId).val();
                var params = {
                    method : 'post',
                    data : {
                        groupId: groupId,
                        customerId: customerId,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        status: 1,
                        send_pdf: send_pdf
                    },
                    url: "/whatsapp/sendMessage/quicksell_group_send"
                };
                siteHelpers.sendAjax(params,"afterSendGroup", ele);
            },
            afterSendGroup : function(ele) {
                $('#group' + ele.data('customerid')).val('').trigger('change');
                toastr["success"]("Group Message sent successfully!", "Message");
            },
            quickCategoryAdd : function(ele) {
                var textBox = ele.closest("div").find(".quick_category");
                if (textBox.val() == "") {
                    alert("Please Enter Category!!");
                    return false;
                }
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        name : textBox.val()
                    },
                    url: "/add-reply-category"
                };
                siteHelpers.sendAjax(params,"afterQuickCategoryAdd");
            },
            afterQuickCategoryAdd : function(response) {
                $(".quick_category").val('');
                $(".quickCategory").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
            },
            deleteQuickCategory : function(ele) {
              const quickCategory = ele.closest("#view-quick-email").find(".quickCategory");
              if (quickCategory.val() === "") {
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
            deleteQuickComment : function(ele) {
                var quickComment = ele.closest("#view-quick-email").find(".quickCommentEmail");
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
                var quickCategory = ele.closest("#view-quick-email").find(".quickCategory");
                if (textBox.val() == "") {
                    alert("Please Enter New Quick Comment!!");
                    return false;
                }
                if (quickCategory.val() == "") {
                    alert("Please Select Category!!");
                    return false;
                }
                var quickCategoryId = quickCategory.children("option:selected").data('id');
                var formData = new FormData();
                formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
                formData.append("reply", textBox.val());
                formData.append("category_id", quickCategoryId);
                formData.append("model", 'Approval Lead');
                formData.append("type", 'with-extra-attributes');
                var params = {
                    method : 'post',
                    data : formData,
                    url: "/reply"
                };
                siteHelpers.sendFormDataAjax(params,"afterQuickCommentAdd");
            },
            afterQuickCommentAdd : function(reply) {
                let selectedOption = $('.quickCategory option:selected');
                let selectedOptionId = selectedOption.data('id');
                let data = JSON.parse(selectedOption.val());
                data.push({
                    id : reply.id,
                    category_id: selectedOptionId,
                    reply : reply.reply,
                    model: 'Approval Lead',
                });
                selectedOption.val(JSON.stringify(data));
                console.log(data);
                $(".quick_comment").val('');
                $('.quickCommentEmail').append($('<option>', {
                    value: reply.reply,
                    text: reply.reply,
                    'data-id': reply.id,
                }));
            },
            changeQuickCategory : function (ele) {
                if (ele.val() != "") {
                    var replies = JSON.parse(ele.val());
                    console.log(replies);
                    ele.closest("#view-quick-email").find('.quickCommentEmail').empty();
                    ele.closest("#view-quick-email").find('.quickCommentEmail').append($('<option>', {
                        value: '',
                        text: 'Quick Reply'
                    }));
                    replies.forEach(function (reply) {
                        ele.closest("#view-quick-email").find('.quickCommentEmail').append($('<option>', {
                            value: reply.reply,
                            text: reply.reply,
                            'data-id': reply.id
                        }));
                    });
                }
            },
            changeQuickComment : function (ele) {
                ele.closest('#view-quick-email').find('#reply-message').val(ele.val());

                var userEmaillUrl = '/email/email-frame-info/'+$('#reply_email_id').val();;
                var senderName = 'Hello '+$('#sender_email_address').val().split('@')[0]+',';

                //$("#reply-message").val(senderName)
                addTextToEditor(senderName);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: userEmaillUrl,
                    type: 'get',
                }).done( function(response) {
                    //$("#reply-message").val(senderName+'\n\n'+ele.val()+'\n\n'+response)
                  addTextToEditor('<p>'+senderName+'</p><p>'+ele.val()+'</p><p>'+response+'</p>');
                }).fail(function(errObj) {
                })

            },
            leadsChart : function () {
                var params = {
                    url: '/erp-customer/lead-data?pageType='+pageType,
                };
                siteHelpers.sendAjax(params,"afterLeadsChart");
            },
            afterLeadsChart : function(datasets) {
                var leadsChart = $('#leadsChart');
                if (Chart !== undefined && leadsChart) {
                var leadsChartExample = new Chart(leadsChart, {
                    type: 'horizontalBar',
                    data: {
                        labels: [
                            'Status'
                        ],
                        datasets: datasets
                    },
                    options: {
                        scaleShowValues: true,
                        responsive: true,
                        scales: {
                            xAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    fontFamily: "'Open Sans Bold', sans-serif",
                                    fontSize: 11
                                },
                                stacked: true
                            }],
                            yAxes: [{
                                ticks: {
                                    fontFamily: "'Open Sans Bold', sans-serif",
                                    fontSize: 11
                                },
                                stacked: true
                            }]
                        },
                        tooltips: {
                            enabled: false
                        },
                        animation: {
                            onComplete: function () {
                                var chartInstance = this.chart;
                                var ctx = chartInstance.ctx;
                                ctx.textAlign = "left";
                                ctx.fillStyle = "#fff";
                                Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
                                    var meta = chartInstance.controller.getDatasetMeta(i);
                                    Chart.helpers.each(meta.data.forEach(function (bar, index) {
                                        data = dataset.data[index];
                                        if (i == 0) {
                                            ctx.fillText(data, 50, bar._model.y + 4);
                                        } else {
                                            ctx.fillText(data, bar._model.x - 25, bar._model.y + 4);
                                        }
                                    }), this)
                                }), this);
                            }
                        },
                    }
                });
            }
            },
            orderStatusChart : function () {
                var params = {
                    url: '/erp-customer/order-status-chart?pageType='+pageType,
                    dataType: "html"
                };
                siteHelpers.sendAjax(params,"afterOrderStatusChart");
            },
            afterOrderStatusChart : function (html) {
                $('.order-status-chart').html(html);
            },
            blockTwilio : function(ele) {
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/erp-customer/block/"+ele.data('id'),
                    beforeSend : function() {
                        ele.text('Blocking...');
                    },
                    doneAjax : function(response) {
                        if (response.is_blocked == 1) {
                            ele.html('<img src="/images/blocked-twilio.png" />');
                        } else {
                            ele.html('<img src="/images/unblocked-twilio.png" />');
                        }
                    },
                };
                siteHelpers.sendAjax(params);
            },
            customerSearch : function(ele) {
                ele.select2({
                    tags: true,
                    width : '100%',
                    ajax: {
                        url: '/erp-leads/customer-search',
                        dataType: 'json',
                        delay: 750,
                        data: function (params) {
                            return {
                                q: params.term, // search term
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            };
                        },
                    },
                    placeholder: 'Search for Customer by id, Name, No',
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                    minimumInputLength: 1,
                    templateResult: function (customer) {
                        if (customer.loading) {
                            return customer.name;
                        }
                        if (customer.name) {
                            return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                        }
                    },
                    templateSelection: (customer) => customer.text || customer.name,
                });
            },
            userSearch : function(ele) {
                ele.select2({
                    ajax: {
                        url: '/user-search',
                        dataType: 'json',
                        delay: 750,
                        data: function (params) {
                            return {
                                q: params.term, // search term
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            };
                        },
                    },
                    placeholder: 'Search for User by Name',
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                    minimumInputLength: 2,
                    width: '100%',
                    templateResult: function (user) {
                        return user.name;
                    },
                    templateSelection: function (user) {
                        return user.name;
                    },
                });
            },
            productSearch : function (ele) {
                ele.select2({
                    ajax: {
                        url: '/productSearch/',
                        dataType: 'json',
                        delay: 750,
                        data: function (params) {
                            return {
                                q: params.term, // search term
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            };
                        },
                    },
                    placeholder: 'Search for Product by id, Name, Sku',
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                    minimumInputLength: 2,
                    width: '100%',
                    templateResult: function (product) {
                        if (product.loading) {
                            return product.sku;
                        }
                        if (product.sku) {
                            return "<p> <b>Id:</b> " + product.id + (product.name ? " <b>Name:</b> " + product.name : "") + " <b>Sku:</b> " + product.sku + " </p>";
                        }
                    },
                    templateSelection: function (product) {
                        return product.text || product.name;
                    },
                });
            },
            loadCustomers : function (ele) {
                var first_customer = $('#first_customer').val();
                var second_customer = $('#second_customer').val();
                if (first_customer == second_customer) {
                    alert('You selected the same customers');
                    return;
                }
                var params = {
                    data : {
                        first_customer: first_customer,
                        second_customer: second_customer
                    },
                    url: "/customers-load",
                    beforeSend : function() {
                        ele.text('Loading...');
                    },
                    doneAjax : function(response) {
                        $('#first_customer_id').val(response.first_customer.id);
                        $('#second_customer_id').val(response.second_customer.id);
                        $('#first_customer_name').val(response.first_customer.name);
                        $('#first_customer_email').val(response.first_customer.email);
                        $('#first_customer_phone').val(response.first_customer.phone ? (response.first_customer.phone).replace(/[\s+]/g, '') : '');
                        $('#first_customer_instahandler').val(response.first_customer.instahandler);
                        $('#first_customer_rating').val(response.first_customer.rating);
                        $('#first_customer_address').val(response.first_customer.address);
                        $('#first_customer_city').val(response.first_customer.city);
                        $('#first_customer_country').val(response.first_customer.country);
                        $('#first_customer_pincode').val(response.first_customer.pincode);
                        $('#second_customer_name').val(response.second_customer.name);
                        $('#second_customer_email').val(response.second_customer.email);
                        $('#second_customer_phone').val(response.second_customer.phone ? (response.second_customer.phone).replace(/[\s+]/g, '') : '');
                        $('#second_customer_instahandler').val(response.second_customer.instahandler);
                        $('#second_customer_rating').val(response.second_customer.rating);
                        $('#second_customer_address').val(response.second_customer.address);
                        $('#second_customer_city').val(response.second_customer.city);
                        $('#second_customer_country').val(response.second_customer.country);
                        $('#second_customer_pincode').val(response.second_customer.pincode);
                        $('#customers-data').show();
                        $('#mergeButton').prop('disabled', false);
                        ele.text('Load Data');
                    },
                };
                siteHelpers.sendAjax(params);
            },
            createBroadcast : function (model_id) {
                var customers = [];
                $(".customer_message").each(function () {
                    if ($(this).prop("checked") == true) {
                        customers.push($(this).val());
                    }
                });
                if (all_customers.length != 0) {
                    customers = all_customers;
                }
                if (customers.length == 0) {
                    alert('Please select customer');
                    return false;
                }
                $("#"+model_id).modal("show");
            },
            erpLeadsSendMessage : function () {
                var customers = [];
                $(".customer_message").each(function () {
                    if ($(this).prop("checked") == true) {
                        customers.push($(this).val());
                    }
                });
                if (all_customers.length != 0) {
                    customers = all_customers;
                }
                if (customers.length == 0) {
                    alert('Please select customer');
                    return false;
                }

                if ($("#send_message").find("#name").val() == "") {
                    alert('Please type name ');
                    return false;
                }

                if ($("#send_message").find("#message_to_all_field").val() == "") {
                    alert('Please type message ');
                    return false;
                }
                if ($("#send_message").find(".ddl-select-product").val() == "" && $("#send_message").find("#product_start_date").val() == "") {
                    alert('Please select product');
                    return false;
                }
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        products: $("#send_message").find(".ddl-select-product").val(),
                        sending_time: $("#send_message").find("#sending_time_field").val(),
                        message: $("#send_message").find("#message_to_all_field").val(),
                        name: $("#send_message").find("#name").val(),
                        product_start_date:$("#send_message").find("#product_start_date").val(),
                        product_end_date:$("#send_message").find("#product_end_date").val(),
                        customers: customers
                    },
                    url: "/erp-leads-send-message",
                    doneAjax : function(response) {
                        window.location.reload();
                    },
                };
                siteHelpers.sendAjax(params);
            },
            instructionStore : function(ele) {
                var customer_id = ele.closest('form').find('input[name="customer_id"]').val();
                var instruction = ele.closest('form').find('input[name="instruction"]').val();
                var category_id = ele.closest('form').find('input[name="category_id"]').val();
                var assigned_to = ele.closest('form').find('input[name="assigned_to"]').val();
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        customer_id: customer_id,
                        instruction: instruction,
                        category_id: category_id,
                        assigned_to: assigned_to,
                    },
                    url: ele.closest('form').attr('action')
                };
                siteHelpers.sendAjax(params);
            },
            updateBroadCastList : function (customerId, needtoShowModel) {
                var params = {
                    data : {
                        customer_id: customerId
                    },
                    url: "/customer/broadcast",
                    doneAjax : function(response) {
                        var html = "Sorry, There is no available broadcast";
                        if (response.code == 1) {
                            html = "";
                            if (response.data.length > 0) {
                                $.each(response.data, function (k, v) {
                                    html += '<button class="badge badge-default broadcast-list-rndr" data-customer-id="' + customerId + '" data-id="' + v.id + '">' + v.id + '</button>';
                                });
                            } else {
                                html = "Sorry, There is no available broadcast";
                            }
                        }
                        $("#broadcast-list").find(".modal-body").html(html);
                        if (needtoShowModel && typeof needtoShowModel != "undefined") {
                            $("#broadcast-list").modal("show");
                        }
                    },
                };
                siteHelpers.sendAjax(params);
            },
            broadcastListCreateLead : function(ele) {
                var $this = ele;
                var checkedProducts = $("#broadcast-list").find("input[name='selecte_products_lead[]']:checked");
                var checkedProdctsArr = [];
                if (checkedProducts.length > 0) {
                    $.each(checkedProducts, function (e, v) {
                        checkedProdctsArr += "," + $(v).val();
                    })
                }
                var selectionLead = $("#broadcast-list").find(".selection-broadcast-list").first();
                $("#broadcast-list-approval").find(".broadcast-list-approval-btn").data("customer-id", selectionLead.data("customer-id"));
                $("#broadcast-list-approval").modal("show");
                $(".broadcast-list-approval-btn").unbind().on("click", function () {
                    var $this = $(this);
                    var params = {
                        data : {
                            customer_id: $this.data("customer-id"),
                            product_to_be_run: checkedProdctsArr
                        },
                        url: "/customer/broadcast-send-price",
                        beforeSend : function() {
                            $this.html('Sending Request...');
                        },
                        doneAjax : function(response) {
                            $this.html('Yes');
                            $("#broadcast-list-approval").modal("hide");
                            $("#broadcast-list").modal("hide");
                        },
                    };
                    siteHelpers.sendAjax(params);
                });
            },
            sendInstock : function(ele) {
                var customer_id = ele.data('id');
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        customer_id: customer_id
                    },
                    url: "/customer/send/instock",
                    beforeSend : function() {
                        ele.text('Sending...');
                    },
                    doneAjax : function(response) {
                        ele.text('Send In Stock');
                    },
                };
                siteHelpers.sendAjax(params);
            },
            sendScraped : function (ele) {
                var formData = $('#categoryBrandModal').find('form').serialize();
                var thiss = ele;
                if (!ele.is(':disabled')) {
                    var params = {
                        method : 'post',
                        dataType: "html",
                        data : formData,
                        url: "/customer/sendScraped/images",
                        beforeSend : function() {
                            ele.text('Sending...');
                            ele.attr('disabled', true);
                        },
                        doneAjax : function(response) {
                            $('#categoryBrandModal').find('.close').click();
                            ele.text('Send');
                            ele.attr('disabled', false);
                        },
                    };
                    siteHelpers.sendAjax(params);
                }
            },
            changeStatus : function (ele) {
                var status = ele.val();
                if (ele.hasClass('order_status')) {
                    var id = ele.data('orderid');
                    var url = '/order/' + id + '/changestatus';
                } else {
                    var id = ele.data('leadid');
                    var url = '/erp-leads/' + id + '/changestatus';
                }
                var params = {
                    method : 'POST',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        status: status
                    },
                    dataType: "html",
                    url: url,
                    doneAjax : function(response) {
                        if (ele.hasClass('order_status') && status == 'Product shiped to Client') {
                            $('#tracking-wrapper-' + id).css({'display': 'block'});
                        }
                        ele.siblings('.change_status_message').fadeIn(400);
                        setTimeout(function () {
                            ele.siblings('.change_status_message').fadeOut(400);
                        }, 2000);
                    },
                };
                siteHelpers.sendAjax(params);
            },
            sendMessage : function(ele){
                var message = ele.siblings('textarea').val();
                var customer_id = ele.data('customerid');
                if (message.length > 0 && !ele.is(':disabled')) {
                    var data = new FormData();
                    data.append("_token", $('meta[name="csrf-token"]').attr('content'));
                    data.append("customer_id", customer_id);
                    data.append("message", message);
                    data.append("status", 1);
                    var params = {
                        method : 'post',
                        data : data,
                        url: '/whatsapp/sendMessage/customer',
                        beforeSend : function() {
                            ele.attr('disabled', true);
                        },
                        doneAjax : function(response) {
                            ele.siblings('textarea').val('');
                            ele.attr('disabled', false);
                        }
                    };
                    siteHelpers.sendFormDataAjax(params);
                }
            },
            createTwilioGroup : function(ele){
                var customers = [];
                $(".customer_message").each(function () {
                    if ($(this).prop("checked") == true) {
                        customers.push($(this).val());
                    }
                });
                if (all_customers.length != 0) {
                    customers = all_customers;
                }
                if (customers.length == 0) {
                    alert('Please select customer');
                    return false;
                }
                var form = ele.closest('form');
                var name = form.find('#name-field').val();
                if (!ele.is(':disabled')) {
                    var data = new FormData();
                    data.append("_token", $('meta[name="csrf-token"]').attr('content'));
                    data.append("name", name);
                    data.append("customers_id", customers.join());
                    data.append("store_website_id", form.find("#store_website_id").val());
                    data.append("service_id", form.find("#service_id").val());
                    var params = {
                        method : 'post',
                        data : data,
                        url: '/selected_customer/createGroup',
                        beforeSend : function() {
                            ele.attr('disabled', true);
                        },
                        doneAjax : function(response) {
                            if(response.msg == 'success'){
                                ele.attr('disabled', false);
                                toastr['success']('Customers successfully assigned to new group created!');
                                $("#createTwilio").modal("hide");
                            }else{
                                ele.attr('disabled', false);
                                toastr['error']('Please try again!');
                                $("#createTwilio").modal("hide");
                            }

                        }
                    };
                    siteHelpers.sendFormDataAjax(params);
                }
            },
            assignTwilioUsers : function(ele){
                var customers = [];
                $(".customer_message").each(function () {
                    if ($(this).prop("checked") == true) {
                        customers.push($(this).val());
                    }
                });
                if (all_customers.length != 0) {
                    customers = all_customers;
                }
                if (customers.length == 0) {
                    alert('Please select customer');
                    return false;
                }
                var form = ele.closest('form');
                if (!ele.is(':disabled')) {
                    var data = new FormData();
                    data.append("_token", $('meta[name="csrf-token"]').attr('content'));
                    data.append("customers_id", customers.join());
                    data.append("sms_group_id", form.find("#twilio-group").val());
                    var params = {
                        method : 'post',
                        data : data,
                        url: '/selected_customer/assignGroup',
                        beforeSend : function() {
                            ele.attr('disabled', true);
                        },
                        doneAjax : function(response) {
                            if(response.msg == 'success'){
                                ele.attr('disabled', false);
                                toastr['success']('Customers successfully assigned!');
                                $("#assignTwilio").modal("hide");
                            }else{
                                ele.attr('disabled', false);
                                toastr['error']('Please try again!');
                                $("#assignTwilio").modal("hide");
                            }
                        }
                    };
                    siteHelpers.sendFormDataAjax(params);
                }
            },
            sendMessageMaltiCustomer : function(ele){
                var customers = [];
                $(".customer_message").each(function () {
                    if ($(this).prop("checked") == true) {
                        customers.push($(this).val());
                    }
                });
                if (all_customers.length != 0) {
                    customers = all_customers;
                }
                if (customers.length == 0) {
                    alert('Please select customer');
                    return false;
                }
                var form = ele.closest('form');
                var message = form.find('#reply-message').val();
                if (!ele.is(':disabled')) {
                    var data = new FormData();
                    data.append("_token", $('meta[name="csrf-token"]').attr('content'));
                    data.append("customers_id", customers.join());
                    data.append("message", message);
                    data.append("status", 1);
                    data.append("brand", form.find("#product-brand").val());
                    data.append("category", form.find("#category").val());
                    data.append("number_of_products", form.find("#number_of_products").val());
                    data.append("quick_sell_groups", form.find("#product-quick-sell-groups").val());
                    data.append("product_ids", form.find("#insert-product-ids").val());
                    data.append("skus", form.find("#insert-skus").val());
                    var params = {
                        method : 'post',
                        data : data,
                        url: '/selected_customer/sendMessage',
                        beforeSend : function() {
                            ele.attr('disabled', true);
                        },
                        doneAjax : function(response) {
                            ele.attr('disabled', false);
                            $("#sendCustomerMessage").modal("hide");
                        }
                    };
                    siteHelpers.sendFormDataAjax(params);
                }
            },
            flagCustomer : function (ele) {
                var customer_id = ele.data('id');
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        customer_id: customer_id
                    },
                    url: "/customer/flag",
                    beforeSend : function() {
                        ele.text('Flagging...');
                    },
                    doneAjax : function(response) {
                        if (response.is_flagged == 1) {
                            ele.html('<img src="/images/flagged.png" />');
                        } else {
                            ele.html('<img src="/images/unflagged.png" />');
                        }
                    },
                };
                siteHelpers.sendAjax(params);
            },
            addInWhatsappList : function (ele) {
                var customer_id = ele.data('id');
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        customer_id: customer_id
                    },
                    url: "/customer/in-w-list",
                    beforeSend : function() {
                        ele.text('Sending...');
                    },
                    doneAjax : function(response) {
                        if (response.in_w_list == 1) {
                            ele.html('<img src="/images/completed-green.png" />');
                        } else {
                            ele.html('<img src="/images/completed.png" />');
                        }
                    },
                };
                siteHelpers.sendAjax(params);
            },
            priorityCustomer : function (ele) {
                var customer_id = ele.data('id');
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        customer_id: customer_id
                    },
                    url: "/customer/prioritize",
                    beforeSend : function() {
                        ele.text('Prioritizing...');
                    },
                    doneAjax : function(response) {
                        if (response.is_priority == 1) {
                            ele.html('<img src="/images/customer-priority.png" />');
                        } else {
                            ele.html('<img src="/images/customer-not-priority.png" />');
                        }
                    },
                };
                siteHelpers.sendAjax(params);
            },
            storeReminder : function (ele) {
                var reminderModal = $('#reminderModal');
                var customerIdToRemind = reminderModal.find('input[name="customer_id"]').val();
                var frequency = reminderModal.find('#frequency').val();
                var message = reminderModal.find('#reminder_message').val();
                var reminder_from = reminderModal.find('#reminder_from').val();
                var reminder_last_reply = (reminderModal.find('#reminder_last_reply').is(":checked")) ? 1 : 0;
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        customer_id: customerIdToRemind,
                        frequency: frequency,
                        message: message,
                        reminder_from: reminder_from,
                        reminder_last_reply : reminder_last_reply
                    },
                    url: "/customer/reminder",
                    doneAjax : function(response) {
                        $(".set-reminder[data-id='" + customerIdToRemind + "']").data('frequency', frequency);
                        $(".set-reminder[data-id='" + customerIdToRemind + "']").data('reminder_message', message);
                        toastr['success']('Reminder updated successfully!');
                        $("#reminderModal").modal("hide");
                    },
                };
                siteHelpers.sendAjax(params);
            },
            sendContactUser : function (ele) {
                var $form = $("#send-contact-to-user");
                var params = {
                    method : 'post',
                    data : $form.serialize(),
                    url: "/customer/send-contact-details",
                    beforeSend : function() {
                        ele.text('Sending message...');
                    },
                    doneAjax : function(response) {
                        ele.html('<img style="width: 17px;" src="/images/filled-sent.png">');
                        $("#sendContacts").modal("hide");
                    },
                };
                siteHelpers.sendAjax(params);
            },
            approveMessageSession : function (ele) {
                var params = {
                    method : 'post',
                    data : {_token : $('meta[name="csrf-token"]').attr('content'), text : ele.text()},
                    url: "/erp-customer/approve-message-session",
                    doneAjax : function(response) {
                        ele.text(response.text);
                        ele.removeClass('btn-success').removeClass('btn-default').addClass(response.class);
                    },
                };
                siteHelpers.sendAjax(params);
            },
            selectAllCustomer : function(ele){
                if (ele.text() == 'Unselect All Customers') {
                    all_customers = [];
                    $(".customer_message").prop("checked", false);
                    ele.text('Select All Customers');
                    return false;
                }
                var params = {
                    method : 'get',
                    data : $('#search_frm').serialize(),
                    url: "/erp-customer/customer-ids?get_customer_ids=1&pageType="+pageType,
                    beforeSend : function() {
                        ele.text('Select...');
                        ele.attr('disabled', true);
                    },
                    doneAjax : function(response) {
                        $(".customer_message").prop("checked", true);
                        all_customers = response;
                        ele.text('Unselect All Customers');
                        ele.attr('disabled', false);
                    },
                };
                siteHelpers.sendAjax(params);
            },
            updatedShoeSize : function(ele) {
                var params = {
                    method : 'post',
                    data : {_token : $('meta[name="csrf-token"]').attr('content'), shoe_size : ele.val()},
                    url: "/erp-customer/"+ele.data('id')+"/update",
                    beforeSend : function() {
                        ele.attr('disabled', true);
                    },
                    doneAjax : function(response) {
                        ele.attr('disabled', false);
                    },
                };
                siteHelpers.sendAjax(params);
            },
            addErpLead : function (ele, thiss) {
                var url = ele.attr('action');
                if (ele.find('.multi_brand').val() == "") {
                    alert('Please Select Brand');
                    return false;
                }
                if (ele.find('input[name="category_id"]').val() == "") {
                    alert('Please Select Category');
                    return false;
                }
                if (ele.find('input[name="lead_status_id"]').val() == "") {
                    alert('Please Select Status');
                    return false;
                }
                var formData = new FormData(thiss);
                var params = {
                    method : 'POST',
                    data : formData,
                    url: url,
                    doneAjax : function(response) {
                        toastr['success']('Lead add successfully!');
                        $('#add_lead').modal('hide');
                        if ($('#add_lead').find('input[name="product_id"]').length > 0 && $('#add_lead').find('input[name="product_id"]').val()) {
                            var dataSending = $('#add_lead').find('input[name="product_id"]').data('object');
                            if (typeof dataSending != 'object'){
                                dataSending = {};
                            }
                            var params = {
                                method : 'post',
                                data : $.extend({
                                    _token:  $('meta[name="csrf-token"]').attr('content'),
                                    customer_id: $('#add_lead').find('input[name="customer_id"]').val(),
                                    selected_product: [$('#add_lead').find('input[name="product_id"]').val()],
                                    auto_approve: 1
                                },dataSending),
                                url: "/leads/sendPrices",
                            };
                            siteHelpers.sendAjax(params);
                        }
                    }
                };
                siteHelpers.sendFormDataAjax(params);
            },
            addNextAction : function(ele) {
                var textBox = ele.closest(".row_next_action").find(".add_next_action_txt");
                if (textBox.val() == "") {
                    alert("Please Enter New Next Action!!");
                    return false;
                }
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        name : textBox.val()
                    },
                    doneAjax : function(response) {
                        toastr['success']('Successfully add!');
                        textBox.val('');
                        $(".next_action").append('<option value="'+response.id+'">' + response.name + '</option>');
                    },
                    url: "/erp-customer/add-next-actions"
                };
                siteHelpers.sendAjax(params);
            },
            deleteNextAction : function(ele) {
                var nextAction = ele.closest(".row_next_action").find(".next_action");
                if (nextAction.val() == "") {
                    alert("Please Select Next Action!!");
                    return false;
                }
                var nextActionId = nextAction.val();
                if (!confirm("Are sure you want to delete Next Action?")) {
                    return false;
                }
                var params = {
                    method : 'post',
                    data : {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                        id : nextActionId
                    },
                    url: "/erp-customer/destroy-next-actions"
                };
                siteHelpers.sendAjax(params,"pageReload");
            },
            changeNextAction :  function(ele) {
                var params = {
                    method : 'post',
                    data : {
                        customer_next_action_id: ele.val(),
                        _token  : $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/erp-customer/"+ele.data('id')+"/update",
                    doneAjax : function(response) {
                        toastr['success']('Next Action changed successfully!', 'Success');
                    },
                };
                siteHelpers.sendAjax(params);
            },
            addOrderFrm : function (ele, thiss) {
                var url = ele.attr('action');
                var formData = new FormData(thiss);
                var params = {
                    method : 'POST',
                    data : formData,
                    url: url,
                    doneAjax : function(response) {
                        toastr['success']('Order add successfully!');
                        if ($('#add_order').find('input[name="selected_product[]"]').length > 0 && $('#add_order').find('input[name="selected_product[]"]').val()) {
                            var params = {
                                method : 'post',
                                data : {
                                    _token:  $('meta[name="csrf-token"]').attr('content'),
                                    customer_id: $('#add_order').find('input[name="customer_id"]').val(),
                                    order_id: response.order.id,
                                    selected_product: [$('#add_order').find('input[name="selected_product[]"]').val()]
                                },
                                url: "/order/send/Delivery",
                            };
                            siteHelpers.sendAjax(params);
                        }
                        $('#add_order').modal('hide');
                    }
                };
                siteHelpers.sendFormDataAjax(params);
            }
        };
        $.extend(siteHelpers, common);

        $(document).ready(function() {
            setTimeout(function() {
                siteHelpers.leadsChart(); // should remove on page load
                siteHelpers.orderStatusChart(); // should remove on page load
            }, 4000);
        })

        siteHelpers.customerSearch($('#first_customer'));
        siteHelpers.customerSearch($('#second_customer'));
        siteHelpers.customerSearch($('#forword_customer'));
        siteHelpers.productSearch($('.ddl-select-product'));
        siteHelpers.userSearch($('.select-user-wha-list'));
        $(".multi_brand_select").change(function() {
            var brand_segment = [];
            $(this).find(':selected').each(function() {
                if ($(this).data('brand-segment') && brand_segment.indexOf($(this).data('brand-segment')) == '-1') {
                  brand_segment.push($(this).data('brand-segment'));
                }
            })
            $(this).closest('form').find(".brand_segment_select").val(brand_segment).trigger('change');
        });
        $('#customer-search').select2({
            tags: true,
            width : '100%',
            ajax: {
                url: '/erp-leads/customer-search',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    for (var i in data) {
                        data[i].id = data[i].id ? data[i].id : data[i].text;
                    }
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search for Customer by id, Name, No',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {
                if (customer.loading) {
                    return customer.name;
                }
                if (customer.name) {
                    return "<p> " + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                }
                console.log(customer);
            },
            templateSelection: (customer) => customer.text || customer.name,
        });
        $('.select-instruction-search').select2({
            ajax: {
                width : "100%",
                url: '/erp-customer/instruction-search/',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    searhData = [];
                    $.each(data, function(i, value){
                        searhData.push({id:value.instruction, name:value.instruction});
                    })
                    params.page = params.page || 1;
                    return {
                        results: searhData,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search for User by Name',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 2,
            width: '100%',
            templateResult: function (instruction) {
                return instruction.name;
            },
            templateSelection: function (instruction) {
                return instruction.name;
            }
        });
        var all_customers = [];
        var isTextMessageFocused = false;
        $(document).on("focusin",".chatbot-comment",function() {
            isTextMessageFocused = true;
        });
        $('#schedule-datetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        $('#reminder_from').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        $('.dd-datepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        $('.datepicker-block').datetimepicker({
          format: 'YYYY-MM-DD'
        });
        // here is trigger of functions
        // verify instructions
        $(document).on('click', '.complete-instruction', function () {
            siteHelpers.completeInstruction($(this));
        });
        $(document).on('click', '.change_message_status', function () {
            siteHelpers.changeMessageStatus($(this));
        });
        $(document).on('click', '.approve-message', function () {
            siteHelpers.approveMessage($(this));
        });
        $(document).on('click', '.change-lead-status', function () {
             siteHelpers.changeLeadStatus($(this));
        });
        $(document).on('click', '.change-order-status', function () {
            siteHelpers.changeOrderStatus($(this));
        });
        $(document).on('click', '.send-pdf', function () {
            siteHelpers.sendPdf($(this));
        });
        $(document).on('click', '.send-group', function () {
            $(".btn-approve-pdf").data('customerid', $(this).data('customerid'));
            $(".btn-ignore-pdf").data('customerid', $(this).data('customerid'));
            $("#confirmPdf").modal("show");
        });
        $(".btn-approve-pdf").on("click",function() {
            siteHelpers.sendGroup($(this), 1);
        });
        $(".btn-ignore-pdf").on("click",function() {
            siteHelpers.sendGroup($(this), 0);
        });
        $(document).on('click', '.quick_category_add', function () {
            siteHelpers.quickCategoryAdd($(this));
        });
        $(document).on('click', '.delete_category', function () {
            siteHelpers.deleteQuickCategory($(this));
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
        $(document).on('click', '.call-select', function () {
            var id = $(this).data('id');
            $('#show' + id).toggle();
        });
        $(document).on('click', '.block-twilio', function () {
            siteHelpers.blockTwilio($(this));
        });
        $(document).on('click', '.load-customers', function () {
            siteHelpers.loadCustomers($(this));
        });
        $(document).on('click', '.create_broadcast', function () {
            siteHelpers.createBroadcast('create_broadcast');
        });
        $(document).on('submit', "#send_message", function (e) {
            e.preventDefault();
            siteHelpers.erpLeadsSendMessage();
        });
        $(document).on('click', ".quick-shortcut-button", function (e) {
            e.preventDefault();
            siteHelpers.instructionStore($(this));
        });
        $(document).on('click', '.btn-broadcast-send', function () {
            siteHelpers.updateBroadCastList($(this).data("id"), true);
        });
        $(document).on("click", ".broadcast-list-create-lead", function () {
            siteHelpers.broadcastListCreateLead();
        });
        $(document).on('click', '.send-instock-shortcut', function () {
            siteHelpers.sendInstock($(this));
        });
        $(document).on('click', '.latest-scraped-shortcut', function () {
            var id = $(this).data('id');
            $('#categoryBrandModal').find('input[name="customer_id"]').val(id);
        });


        $(document).on('click', '.send-to-approval-btn', function (e) {
            e.preventDefault();
            var id = $('#categoryBrandModal').find('input[name="customer_id"]').val();
            $('#categoryBrandModal').find('input[name="submit_type"]').val('send-to-approval');
            $('#customerSendScrap').attr('action', '/attachImages/customer/' + id);
            $("#customerSendScrap").submit();
        });

        $("#customerSendScrap").on('submit', function(e) {
                e.preventDefault();
                var url = $('#customerSendScrap').attr('action');
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: $(this).serialize(),
                    beforeSend: function () {
                    $("#loading-image").show();
                    },
                    success: function (response) {
                        $("#loading-image").hide();
                        $("#categoryBrandModal").modal('hide');
                        toastr["success"](response.message);
                    },
                    error: function (error) {
                        toastr["error"](error.responseJSON.message);
                        $("#loading-image").hide();
                    }
                });

        });

        $(document).on('click', '.old-send-btn', function (e) {
            e.preventDefault();
            var id = $('#categoryBrandModal').find('input[name="customer_id"]').val();
            $('#categoryBrandModal').find('input[name="submit_type"]').val('old-submit');
            $('#customerSendScrap').attr('action', '/attachImages/customer/' + id);
            $("#customerSendScrap").submit();
        });



        // $('#attachImages1').click(function () {
        //     var id = $('input[name="customer_id"]').val();
        //     $('#customerSendScrap').attr('action', '/attachImages/customer/' + id);
        // });

        // $('#attachImages2').click(function () {
        //     var id = $('input[name="customer_id"]').val();
        //     $('#customerSendScrap').attr('action', '/attached-images-grid/customer/' + id);
        // });

        $(document).on('click', "#sendScrapedButton", function (e) {
            e.preventDefault();
            siteHelpers.sendScraped($(this));
        });
        $(document).on('change', '.change_status', function () {
            siteHelpers.changeStatus($(this));
        });
        $(document).on('click', '.send-message', function () {
            siteHelpers.sendMessage($(this));
        });
        $(document).on('click', '.flag-customer', function () {
            siteHelpers.flagCustomer($(this));
        });
        $(document).on('click', '.in-w-list', function () {
            siteHelpers.addInWhatsappList($(this));
        });
        $(document).on('click', '.priority-customer', function () {
            siteHelpers.priorityCustomer($(this));
        });
        $(document).on('click', '.set-reminder', function () {
            $('#reminderModal').find('#frequency').val($(this).data('frequency')).trigger('change');
            $('#reminderModal').find('#reminder_message').val($(this).data('reminder_message')).trigger('change');
            $('#reminderModal').find('input[name="customer_id"]').val($(this).data('id'));
        });
        $(document).on('click', '.save-reminder', function () {
             siteHelpers.storeReminder($(this));
        });
        $(document).on('click', '.send-contact-modal-btn', function () {
            var $this = $(this);
            $("#customer_id_attr").val($this.data("id"));
            $("#sendContacts").modal("show");
        });
        $(document).on('click', '.send-contact-user-btn', function () {
             siteHelpers.sendContactUser($(this));
        });
        $(document).on('click', '.send-whatsapp', function () {
            siteHelpers.createBroadcast('sendCustomerMessage');
        });
        $(document).on('click', '.assign-twilio', function () {
            siteHelpers.createBroadcast('assignTwilio');
        });
        $(document).on('click', '.create-twilio', function () {
            $("#assignTwilio").modal("hide");
            siteHelpers.createBroadcast('createTwilio');
        });
        $(document).on('click', '.send-message-malti-customer', function () {
            siteHelpers.sendMessageMaltiCustomer($(this));
        });
        $(document).on('click', '.assign-multi-user-twilio', function () {
            siteHelpers.assignTwilioUsers($(this));
        });
        $(document).on('click', '.create-twilio-group', function () {
            siteHelpers.createTwilioGroup($(this));
        });
        $(document).on('click', '.send-message-with-attach-images', function () {
            var customers = [];
            $(".customer_message").each(function () {
                if ($(this).prop("checked") == true) {
                    customers.push($(this).val());
                }
            });
            if (all_customers.length != 0) {
                customers = all_customers;
            }
            if (customers.length == 0) {
                alert('Please select costomer');
                return false;
            }
            var message = $(this).closest('form').find('#reply-message').val();
            window.location.href = "/attachImages/selected_customer/"+customers.join()+"/1?return_url=erp-customer&message="+message;
        });
         $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });
        $(document).on('click', '.quick-add-instruction', function (e) {
            var id = $(this).data('id');
            $(this).siblings('.quick-add-instruction-textarea').removeClass('hidden');
            $(this).siblings('.quick-priority-check').removeClass('hidden');
            $(this).siblings('.quick-add-instruction-textarea').keypress(function (e) {
                var key = e.which;
                var thiss = $(this);
                let priority = $('#instruction_priority_' + id).is(':checked') ? 'on' : '';
                if (key == 13) {
                    e.preventDefault();
                    var instruction = $(thiss).val();
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('instruction.store') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            instruction: instruction,
                            category_id: 1,
                            customer_id: id,
                            assigned_to: 7,
                            is_priority: priority
                        }
                    }).done(function () {
                        $(thiss).addClass('hidden');
                        $('#instruction_priority_' + id).addClass('hidden');
                        $(thiss).val('');
                    }).fail(function (response) {
                        alert('Could not create instruction');
                    });
                }
            });
        });
        $(document).on('click', '.approve-message-session', function (e) {
            siteHelpers.approveMessageSession($(this));
        });
        $(document).on('click', '.select-all-customer', function (e) {
            siteHelpers.selectAllCustomer($(this));
        });
        $(document).on('keypress', '.update-shoe-size', function (e) {
            var key = e.which;
            if (key == 13) {
                e.preventDefault();
                siteHelpers.updatedShoeSize($(this));
            }
        });
        //$('ul.pagination').hide();
        $('.infinite-scroll').jscroll({
            debug: false,
            autoTrigger: true,
            loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
            padding: 2500,
            nextSelector: '.pagination li.active + li a',
            contentSelector: '.infinite-scroll',
            callback: function (response) {
                try {
                   var originalCustomerIds = $.parseJSON($(".paginate_customer_ids").first().val());
                   var lastCustomerIds = $.parseJSON($(".paginate_extra_customer_ids").last().val());
                   var allIds = originalCustomerIds.concat(lastCustomerIds);
                   $(".paginate_customer_ids").first().val(JSON.stringify(allIds));
                }catch(err) {
                    console.log("Error : ", err);
                }
                $('ul.pagination:visible:first').remove();
                var next_page = $('.pagination li.active + li a');
                var page_number = next_page.attr('href').split('page=');
                var current_page = page_number[1] - 1;
                $('#page-goto option[data-value="' + current_page + '"]').attr('selected', 'selected');
                $('.multiselect-2').select2({width:'92%'});
                $('.send_pdf_selectbox').select2({
                  tags : true,
                  allowClear: true,
                  ajax: {
                    url: '/erp-customer/search-pdf',
                    dataType: 'json',
                    processResults: function (data) {
                      return {
                        results: data.items
                      };
                    }
                  }
                });
                //$(".customer_message").prop("checked", all_customers.length != 0);
            }
        });
        $(document).on('click', '.quick_add_lead', function (e) {
            $('#add_lead').find('.customer_id').val($(this).data('id'));
            if ($('#add_lead').find('input[name="product_id"]').length > 0) {
                $('#add_lead').find('input[name="product_id"]').val('');
            }
            $('#add_lead').find('.show-product-image').html('');
            $('#add_lead').modal('show');
        });
        $(document).on('click', '.quick_add_order', function (e) {
            $('#add_order').find('.customer_id').val($(this).data('id'));
            if ($('#add_order').find('input[name="selected_product[]"]').length > 0) {
                $('#add_order').find('input[name="selected_product[]"]').val('');
            }
            if ($('#add_order').find('input[name="convert_order"]').length > 0) {
                $('#add_order').find('input[name="convert_order"]').val('');
            }
            $('#add_order').modal('show');
        });
        // started the return exchange code
        // need to move on partial
        // @todo use for multiple place
        $(document).on('click', '.quick_return_exchange', function (e) {
            let $this       = $(this),
                $modelData  = $(document).find(".return-exchange-model-data");
            $('#return-exchange-modal').modal('show');
            $.ajax({
                type: "GET",
                url: "/return-exchange/model/" + $this.data("id"),
            }).done(function (response) {
                if (response.code == 200) {
                    $modelData.html(response.html);

                    $('.due-date').datetimepicker({
                        minDate:new Date(),
                        format: 'YYYY-MM-DD'
                    });

                    $modelData.find(".select-multiple").select2();
                    $modelData.find(".select-multiple-product").select2({
                        ajax: {
                            url: "/productSearch",
                            type: "GET",
                            dataType: 'json',
                            data: function (params) {
                                return {
                                    q: params.term
                                };
                            },
                            processResults: function (data) {
                                let $results = [];
                                if (Object.keys(data).length > 0) {
                                    $.each(data, function(index) {
                                        $results.push({'id': data[index].id, 'text': data[index].sku + ' ' + data[index].name});
                                    });
                                }
                                return {
                                    results: $results
                                };
                            },
                            cache: true
                        }
                    });
                }
            }).fail(function (response) {});
        });
        $(document).on("click","#return-exchange-form input[name='type']",function() {
            if($(this).val() == "refund") {
                $("#return-exchange-form").find(".refund-section").show();
            }else{
                $("#return-exchange-form").find(".refund-section").hide();
            }
        });
        $(document).on("click","#btn-return-exchage-request",function(e) {
            e.preventDefault();
            var form = $("#return-exchange-form");
            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                data: form.serialize(),
                dataType:"json"
            }).done(function (response) {
                toastr[(response.code == 200) ?'success' : 'error'](response.message);
                $('#return-exchange-modal').modal('hide');
                document.getElementById("return-exchange-form").reset();
            }).fail(function (response) {
                console.log(response);
            });
        });
        //ended return exchange code
        $('.erp_lead_frm').on('submit', function(e) {
          e.preventDefault();
          siteHelpers.addErpLead($(this), this);
          return false;
        });
        $('.add_next_action').on('click', function(e) {
            siteHelpers.addNextAction($(this));
        });
        $('.delete_next_action').on('click', function(e) {
            siteHelpers.deleteNextAction($(this));
        });
        $('.next_action').on('change', function(e) {
            siteHelpers.changeNextAction($(this));
        });
        $('.add_order_frm').on('submit', function(e) {
          e.preventDefault();
          siteHelpers.addOrderFrm($(this), this);
          return false;
        });
        $(document).on('click', '.order_next_action', function (e) {
            $('#order_next_action').find('.order_page').html('<iframe class="embed-responsive-item" src="/order/'+$(this).data('id')+'"></iframe>');
            $('#order_next_action').modal('show');
        });
        $(document).on('click', '.remove-pdf', function (e) {
            var $this = $(this);
            $.ajax({
                type: "GET",
                url: "/erp-customer/destroy-chat",
                data: {
                    id: $this.data("id")
                }
            }).done(function (response) {
                $this.closest(".approve-pdf-block").remove();
            }).fail(function (response) {
            });
        });
        $(document).on('change', '.change-whatsapp-no', function () {
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('customer.change.whatsapp') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    customer_id: $this.data("customer-id"),
                    number: $this.val(),
                    type : $this.data("type")
                }
            }).done(function () {
                alert('Number updated successfully!');
            }).fail(function (response) {
                console.log(response);
            });
        });
        $(document).on('change', '.update-customer-field', function () {
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('customer.update.field') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    field: $this.data("type"),
                    value: $this.val(),
                    customer_id: $this.data("customer-id")
                }
            }).done(function (response) {
                alert(response.message);
            }).fail(function (response) {
                console.log(response);
            });
        });
        $(document).on("click",".btn-event-order",function(e) {
            e.preventDefault();
            var form  = $(this).closest("form");
            $.ajax({
                type: "POST",
                url: "/erp-customer/move-order",
                data : form.serialize(),
                dataType : "json",
                beforeSend : function() {
                    $(this).text('Loading...');
                    },
            }).done(function (response) {
                if(response.code == 1) {
                    window.location = "/order/create?key="+response.key;
                }
            }).fail(function (response) {
                console.log(response);
            });
        });
        $('.send_pdf_selectbox').select2({
          tags : true,
          allowClear: true,
          ajax: {
            url: '/erp-customer/search-pdf',
            dataType: 'json',
            processResults: function (data) {
              return {
                results: data.items
              };
            }
          }
        });
        $(document).on("click",".chatbot-comment-store",function() {
            var form = $(this).closest("form");
            var chatComment = form.find(".chatbot-comment");
            var messageId   = chatComment.data("id");
            var message     = chatComment.val();
            $.ajax({
                type: "POST",
                url: "/chatbot/edit-message",
                data : {
                    _token: "{{ csrf_token() }}",
                    id : messageId,
                    message : message
                },
                dataType : "json",
            }).done(function (response) {
                toastr['success']('Message dialog update successfully', 'success');
            }).fail(function (response) {
                toastr['error']('Oops, Something went wrong!', 'success');
            });
        });
        $(document).on("click",".add-chat-phrases",function(e) {
            e.preventDefault();
            $("#addPhrases").find(".question").val($(this).data("message"));
            $("#addPhrases").modal("show");
        });
        $('.select-phrase-group').select2({
          tags : true,
          allowClear: true,
          placeholder: "",
          ajax: {
            url: '/chatbot/question/search',
            dataType: 'json',
            processResults: function (data) {
              return {
                results: data.items
              };
            }
          }
        });
        $(document).on("click","#add-phrases-btn",function() {
            var form  = $(this).closest("form");
            $.ajax({
                type: "POST",
                url: form.attr("action"),
                data :form.serialize(),
                dataType : "json",
            }).done(function (response) {
                toastr['success']('Message dialog update successfully', 'success');
            }).fail(function (response) {
                toastr['error']('Oops, Something went wrong!', 'success');
            });
        });
        $(document).on('click', '.edit-message', function(e) {
            e.preventDefault();
            var thiss = $(this);
            var message_id = $(this).data('messageid');
            $('#message_body_' + message_id).css({'display': 'none'});
            $('#edit-message-textarea' + message_id).css({'display': 'block'});
            $('#edit-message-textarea' + message_id).unbind().keypress(function(e) {
              var key = e.which;
              if (key == 13) {
                e.preventDefault();
                var token = "{{ csrf_token() }}";
                var url = "{{ url('message') }}/" + message_id;
                var message = $('#edit-message-textarea' + message_id).val();
                if ($(thiss).hasClass('whatsapp-message')) {
                  var type = 'whatsapp';
                } else {
                  var type = 'message';
                }
                $.ajax({
                  type: 'POST',
                  url: url,
                  data: {
                    _token: token,
                    body: message,
                    type: type
                  },
                  success: function(data) {
                    $('#edit-message-textarea' + message_id).css({'display': 'none'});
                    $('#message_body_' + message_id).text(message);
                    $('#message_body_' + message_id).css({'display': 'block'});
                  }
                });
              }
            });
          });
        $(document).on("click", ".create-dialog",function() {
            $("#leaf-editor-model").modal("show");
            var myTmpl = $.templates("#add-dialog-form");
            var question = $(this).closest(".filter-message").data("message");
            var assistantReport = [];
                assistantReport.push({"response" : "" , "condition_sign" : "" , "condition_value" : "" , "condition" : "","id" : 0});
            var suggestedObj = <?php echo json_encode(\App\ChatbotDialog::allSuggestedOptions()) ?>;
            var json = {
                "create_type": "intents_create",
                "intent"  : {
                    "question" : question,
                },
                "assistant_report" : assistantReport,
                "response" :  "",
                "allSuggestedOptions" : suggestedObj
            };
            var html = myTmpl.render({
                "data": json
            });
            window.buildDialog = json;
            $("#leaf-editor-model").find(".modal-body").html(html);
            $("[data-toggle='toggle']").bootstrapToggle('destroy')
            $("[data-toggle='toggle']").bootstrapToggle();
            $(".search-alias").select2({width : "100%"});
            var eleLeaf = $("#leaf-editor-model");
            searchForIntent(eleLeaf);
            searchForCategory(eleLeaf);
            searchForDialog(eleLeaf);
            previousDialog(eleLeaf);
            parentDialog(eleLeaf);
        });
        /*$(document).on("focusin","#reply-message",function(){
            $(".message-strong").removeClass("message-strong");
            $(this).addClass("message-strong");
        });*/
        $(document).on("click",".send-with-audio-message",function() {
            if($(this).hasClass("mic-active") == false) {
                if($(".mic-button-input").hasClass("mic-active") == false) {
                    $(".mic-button-input").trigger("click");
                }else{
                    $(".mic-button-input").trigger("click");
                    $(".mic-button-input").trigger("click");
                }
                $(".message-strong").removeClass("message-strong");
                $(this).closest(".infinite-scroll").find(".mic-active").removeClass("mic-active");
                $(this).closest("#view-quick-email").find("#reply-message").addClass("message-strong");
                $(this).addClass("mic-active");
            }else{
                if($(".mic-button-input").hasClass("mic-active") == false) {
                }else{
                    $(".mic-button-input").trigger("click");
                }
                $(".message-strong").removeClass("message-strong");
                $(this).removeClass("mic-active");
            }
        });
        $(document).on('click', '.do_not_disturb', function() {
            var id = $(this).data('id');
            var thiss = $(this);
            $.ajax({
                type: "POST",
                url: "{{ url('customer') }}/" + id + '/updateDND',
                data: {
                    _token: "{{ csrf_token() }}",
                    // do_not_disturb: option
                },
                beforeSend: function() {
                    $(thiss).text('DND...');
                }
            }).done(function(response) {
              if (response.do_not_disturb == 1) {
                $(thiss).html('<img src="/images/do-not-disturb.png" />');
              } else {
                $(thiss).html('<img src="/images/do-disturb.png" />');
              }
            }).fail(function(response) {
              alert('Could not update DND status');
              console.log(response);
            })
      });
        $('[data-toggle="popover"]').popover();
        $(document).on("click",".select-all-customer-direct",function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.text() == 'De-Select All Customers Direct') {
                all_customers = [];
                $this.text('Select All Customers Direct');
                return false;
            }else{
            }
            $.ajax({
              type: "GET",
              url: $this.attr("href"),
              dataType : "json",
              success: function(data) {
                toastr['success']('All customer selected successfully', 'success');
                $this.text('De-Select All Customers Direct');
                all_customers = data
              }
            });
        });
        $(document).on("click",".change-whatsapp",function(){
            $("#modal-change-whatsapp").modal("show");
        });
        $(document).on("click",".modal-change-whatsapp-btn",function(){
            var customers = [];
            $(".customer_message").each(function () {
                if ($(this).prop("checked") == true) {
                    customers.push($(this).val());
                }
            });
            if (all_customers.length != 0) {
                customers = all_customers;
            }
            if (customers.length == 0) {
                alert('Please select Customer');
                return false;
            }
            var form = $(this).closest("form");
            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                dataType : "json",
                data : {
                    _token : $('meta[name="csrf-token"]').attr('content'),
                    customers: customers.join(),
                    whatsapp_no: form.find(".whatsapp_no").val()
                },
                success: function(data) {
                    toastr['success'](data.total + ' record has been update successfully', 'success');
                    location.reload();
                }
            });
        });
        $(document).on("click",".customer-suggestion",function() {
            var $this = $(this);
            var suggestionModal = $("#suggestionModal");
                suggestionModal.find("input[name='customer_id']").val($this.data("id"));
                suggestionModal.modal("show");
        });
        $("#suggestionModal").on("click",".submit-suggestion-modal",function(e){
            e.preventDefault();
            var form = $(this).closest("form");
            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                dataType : "json",
                data : form.serialize(),
                success: function(data) {
                    if(data.code == 200) {
                        toastr['success']('Record has been update successfully', 'success');
                        $("#suggestionModal").modal("hide");
                    }
                }
            });
        });
        $(document).on("click",".create-customer-related-task",function() {
            var $this = $(this);
            var user_id = $(this).closest("tr").find(".ucfuid").val();
            var customer_id = $(this).data("id");
            var modalH = $("#quick-create-task");
                modalH.find(".task_asssigned_to").select2('destroy');
                modalH.find(".task_asssigned_to option[value='"+user_id+"']").prop('selected', true);
                modalH.find(".task_asssigned_to").select2({});
                modalH.find("#task_subject").val("Customer #"+customer_id+" : ");
                modalH.find("#hidden-category-id").remove();
                modalH.find("form").append('<input id="hidden-category-id" type="hidden" name="category_id" value="42" />');
                modalH.find("form").append('<input id="hidden-customer-id" type="hidden" name="customer_id" value="'+customer_id+'" />');
                modalH.modal("show");
        });
        $(document).on("click",".count-customer-tasks",function() {

            var $this = $(this);
            // var user_id = $(this).closest("tr").find(".ucfuid").val();
            var customer_id = $(this).data("id");
            $.ajax({
                type: 'get',
                url: '/erp-customer/task/count/'+customer_id,
                dataType : "json",
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(data) {
                    $("#task_statistics").modal("show");
                    var table = '<div class="table-responsive"><table class="table table-bordered table-striped"><tr><th>Task type</th><th>Assigned to</th><th>Description</th><th>Status</th><th>Action</th></tr>';
                    for(var i=0;i< data.taskStatistics.length;i++) {
                        var str = data.taskStatistics[i].subject;
                        var res = str.substr(0, 100);
                        table = table + '<tr><td>'+data.taskStatistics[i].task_type+'</td><td>'+data.taskStatistics[i].assigned_to_name+'</td><td>'+res+'</td><td>'+data.taskStatistics[i].status+'</td><td><button type="button" class="btn btn-xs btn-image load-communication-modal load-body-class" data-object="'+data.taskStatistics[i].message_type+'" data-id="'+data.taskStatistics[i].id+'" title="Load messages" data-dismiss="modal"><img src="/images/chat.png" alt=""></button>';
                            if(data.taskStatistics[i].task_type === 'Task'){
                                table = table + '| <a href="javascript:void(0);" data-id="'+data.taskStatistics[i].id+'" class="delete_tasks btn btn-image pd-5"><img title="Delete Task" src="/images/delete.png" /></a></td>';
                            }
                            table = table + '</tr>';
                    }
                    table = table + '</table></div>';
                    $("#loading-image").hide();
                    $(".modal").css( "overflow-x", "hidden" );
                    $(".modal").css( "overflow-y", "auto" );
                    $("#task_statistics_content").html(table);
                },
                error: function(error) {
                    console.log(error);
                    $("#loading-image").hide();
                }
            });


        });
        $(document).on("click",".customer-order-summary",function() {
            var $this = $(this);
            var customer_id = $(this).data("id");
            $.ajax({
                type: 'get',
                url: '/erp-customer/order/summary/'+customer_id,
                dataType : "html",
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(data) {
                    $("#order_summary").modal("show");
                    $("#order_summary_list").html(data);
                },
                error: function(error) {
                    console.log(error);
                    $("#loading-image").hide();
                }
            });
        });
        $(document.body).on("click", ".delete_order", function(){
            var x = window.confirm("Are you sure you want to delete this order ?");
            if(!x) {
                return;
            }
            var currentElement = $(this);
            var order_id = currentElement.attr('data-id');
             $.ajax({
                type: 'get',
                url: '/erp-customer/delete-erp-order',
                dataType : "json",
                data: { order_id: order_id},
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(data)
                {
                    if (data.status == 1)
                    {
                        currentElement.parent().parent().remove();
                        toastr['success'](data.msg, 'success');
                    }
                    else
                    {
                        toastr['error'](data.msg, 'success');
                    }
                    // $("#order_summary").modal("show");
                    // $("#order_summary_list").html(data);
                    $("#loading-image").hide();
                },
                error: function(error) {
                    toastr['error']('Something went wrong', 'success');
                    $("#loading-image").hide();
                }
            });
        });
        $(document.body).on("click", ".delete_erp_leads", function(){
            var x = window.confirm("Are you sure you want to delete this erp lead ?");
            if(!x) {
                return;
            }
            var currentElement = $(this);
            var erp_id = currentElement.attr('data-id');
            // console.log(erp_id);return;
             $.ajax({
                type: 'get',
                url: '/erp-customer/delete-erp-leads',
                dataType : "json",
                data: { erp_id: erp_id},
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(data)
                {
                    if (data.status == 1)
                    {
                        currentElement.parent().parent().remove();
                        toastr['success'](data.msg, 'success');
                    }
                    else
                    {
                        toastr['error'](data.msg, 'success');
                    }
                    // $("#order_summary").modal("show");
                    // $("#order_summary_list").html(data);
                    $("#loading-image").hide();
                },
                error: function(error) {
                    toastr['error']('Something went wrong', 'success');
                    $("#loading-image").hide();
                }
            });
        });
        $(document).on("click",".order-return-summary",function() {
            var $this = $(this);
            var customer_id = $(this).data("id");
            $.ajax({
                type: 'get',
                url: '/erp-customer/order/return-summary/'+customer_id,
                dataType : "html",
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(data) {
                    $("#order_return_summary").modal("show");
                    $("#order_return_summary_list").html(data);
                },
                error: function(error) {
                    console.log(error);
                    $("#loading-image").hide();
                }
            });
        });
        $(document.body).on("click", ".btn-delete-template", function(){
            var x = window.confirm("Are you sure you want to delete this ?");
            if(!x) {
                return;
            }
            var currentElement = $(this);
            var exchange_id = currentElement.attr('data-id');
            // console.log(order_id);return;
             $.ajax({
                type: 'get',
                url: '/erp-customer/delete-erp-exchange',
                dataType : "json",
                data: { exchange_id: exchange_id},
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(data)
                {
                    if (data.status == 1)
                    {
                        currentElement.parent().parent().remove();
                        toastr['success'](data.msg, 'success');
                    }
                    else
                    {
                        toastr['error'](data.msg, 'success');
                    }
                    // $("#order_summary").modal("show");
                    // $("#order_summary_list").html(data);
                    $("#loading-image").hide();
                },
                error: function(error) {
                    toastr['error']('Something went wrong', 'success');
                    $("#loading-image").hide();
                }
            });
        });
        $(document.body).on("click", ".delete_tasks", function(){
            var x = window.confirm("Are you sure you want to delete this ?");
            if(!x) {
                return;
            }
            var currentElement = $(this);
            var task_id = currentElement.attr('data-id');
            // console.log(order_id);return;
             $.ajax({
                type: 'get',
                url: '/erp-customer/delete-erp-tasks',
                dataType : "json",
                data: { task_id: task_id},
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(data)
                {
                    if (data.status == 1)
                    {
                        currentElement.parent().parent().remove();
                        toastr['success'](data.msg, 'success');
                    }
                    else
                    {
                        toastr['error'](data.msg, 'success');
                    }
                    // $("#order_summary").modal("show");
                    // $("#order_summary_list").html(data);
                    $("#loading-image").hide();
                },
                error: function(error) {
                    toastr['error']('Something went wrong', 'success');
                    $("#loading-image").hide();
                }
            });
        });
        // $(document).on("click",".btn-delete-template",function(e){
        //     var x = window.confirm("Are you sure you want to delete this request ?");
        //     if(!x) {
        //         return;
        //     }
        //     var id = $(this).data("id");
        //     e.preventDefault();
        //     $.ajax({
        //         type: 'get',
        //         url: "/return-exchange/"+id+"/delete",
        //         dataType : "json",
        //         beforeSend: function () {
        //             $("#loading-image").show();
        //         },
        //         success: function(data) {
        //             $("#order_return_summary").modal("hide");
        //             toastr['success']('Successful', 'success');
        //         },
        //         error: function(error) {
        //             toastr['error']('Something went wrong', 'success');
        //             $("#loading-image").hide();
        //         }
        //     });
        // });
        $(document).on("click",".btn-history-template",function(e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                type: 'get',
                url: "/return-exchange/"+id+"/history",
                dataType : "json",
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(response) {
                    if(response.code == 200) {
                        $("#loading-image").hide();
                        $("#order_return_summary").modal("hide");
                        $("#order_return_history_summary").modal("show");
                        var tr = '<tr>';
                        $.each(response.data, function (k, v) {
                            tr += '<td>'+v.id+'</td><td>'+v.status+'</td><td>'+v.comment+'</td><td>'+v.user_name+'</td><td>'+v.created_at+'</td>';
                        });
                        $("#order_return_history_summary_list").html(tr);
                    }
                },
                error: function(error) {
                    toastr['error']('Something went wrong', 'success');
                    $("#loading-image").hide();
                }
            });
        });
        $(document).on("click",".btn-edit-template",function(e) {
            e.preventDefault();
            var ele = $(this);
            var id = ele.data("id");
            $.ajax({
                    type: 'get',
                    url: "/return-exchange/"+id+"/detail?from=erp-customer",
                    dataType : "html",
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                    success: function(response) {
                        $("#loading-image").hide();
                        $("#order_return_summary").modal("hide");
                        $("#order_return_summary_edit").modal("show");
                        $("#order_return_summary_edit_list").html(response);
                },
                error: function(error) {
                    toastr['error']('Something went wrong', 'success');
                    $("#loading-image").hide();
                }
            });
        });
        $(document).on("click","#return-exchange-update-form input[name='type']",function() {
            console.log($(this).val());
            if($(this).val() == "refund") {
                $("#return-exchange-update-form").find(".refund-section").show();
            }else{
                $("#return-exchange-update-form").find(".refund-section").hide();
            }
        });
        $(document).on("click","#btn-return-exchage-update-request",function(e) {
            e.preventDefault();
            var form = $(this).closest("form");
            $.ajax({
                url: form.attr("action"),
                method: form.attr("method"),
                data: form.serialize(),
                beforeSend : function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    $("#order_return_summary_edit").modal("hide");
                    toastr['success']('Success', 'success');
                },
                error: function(error) {
                    toastr['error']('Something went wrong', 'success');
                    $("#loading-image").hide();
                }
            });
        });
        $(document).on("click",".lead_summary",function(e) {
            e.preventDefault();
            var customer_id = $(this).data("id");
            $.ajax({
                type: 'get',
                url: '/erp-customer/order/lead-summary/'+customer_id,
                dataType : "html",
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(data) {
                    $("#lead_summary_modal").modal("show");
                    $("#lead_summary_modal_list").html(data);
                },
                error: function(error) {
                    console.log(error);
                    $("#loading-image").hide();
                }
            });
        });
        $(document).on('click', '.create_lead_broadcast', function () {
            var customer_id = $("#lead_summary_modal_list tr").find(".customer_message").val();
            customers = [customer_id];
            $("#create_lead_broadcast").modal("show");
        });
        function formatProduct (product) {
        if (product.loading) {
            return product.sku;
        }
        if(product.sku) {
            return "<p> <b>Id:</b> " +product.id  + (product.name ? " <b>Name:</b> "+product.name : "" ) +  " <b>Sku:</b> "+product.sku+" </p>";
        }
    }
        jQuery('.ddl-select-product').select2({
        ajax: {
          url: '/productSearch/',
          dataType: 'json',
          delay: 750,
          data: function (params) {
            return {
              q: params.term, // search term
            };
          },
          processResults: function (data,params) {
            params.page = params.page || 1;
            return {
              results: data,
              pagination: {
                more: (params.page * 30) < data.total_count
              }
            };
          },
        },
        placeholder: 'Search for Product by id, Name, Sku',
        escapeMarkup: function (markup) { return markup; },
        minimumInputLength: 5,
        width: '100%',
        templateResult: formatProduct,
        templateSelection:function(product) {
          return product.text || product.name;
        },
      });
      $("#send_broadcast_message").submit(function(e){
          e.preventDefault();
          var formData = new FormData($(this)[0]);

          if (customers.length == 0) {
            alert('Please select costomer');
            return false;
          }
          if ($("#send_broadcast_message").find("#message_to_all_field").val() == "") {
            alert('Please type message ');
            return false;
          }
          /*if ($("#send_message").find(".ddl-select-product").val() == "") {
            alert('Please select product');
            return false;
          }*/
          for (var i in customers) {
            formData.append("customers[]", customers[i]);
          }
          $.ajax({
            type: "POST",
            url: "{{ route('erp-leads-send-message') }}",
            data: formData,
            contentType : false,
            processData:false
          }).done(function() {
            window.location.reload();
          }).fail(function(response) {
            $(thiss).text('No');
            alert('Could not say No!');
            console.log(response);
          });
      });
      $(document).on('click', '.images_attach', function (e) {
          e.preventDefault();
          var customer_id = $("#lead_summary_modal_list tr").find(".customer_message").val();
            customers = [customer_id];
          if (customers.length == 0) {
            alert('Please select costomer');
            return false;
          }
          url = "{{ route('attachImages', ['selected_customer', 'CUSTOMER_IDS', 1]) }}";
          url = url.replace("CUSTOMER_IDS", customers.toString());
          window.location.href = url;
      });

      $(document).on("click", ".save-intent", function(e) {
        e.preventDefault();
        $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "post",
        url: '/chatbot/question/submit',
        data: {
            "group_id": $('.search-intent').val(),
            "question": $(".question-insert").val(),
            "category_id" : $(".search-category").val(),
            "suggested_reply" : $(".reply-insert").val(),
        },
        dataType: "json",
        success: function(response) {
            if (response.code != 200) {
                toastr['error']('Can not store intent please review or use diffrent name!');
            } else {
                $(".question-insert").val('');
                $(".reply-insert").val('');
                toastr['success']('Success!');
                var aliasTemplate = $.templates("#search-alias-template");
                var aliasTemplateHtml = aliasTemplate.render({
                    "allSuggestedOptions": response.allSuggestedOptions
                });
                $("#leaf-editor-model").find(".search-alias").html(aliasTemplateHtml);
            }
        },
        error: function() {
            toastr['error']('Can not store intent name please review!');
        }
    });
    });


      $(document).on('click', '.add-customer-info', function (e) {
          e.preventDefault();
          var id = $(this).data('id');
          $('#hidden_edit_customer_id').val(id);
          $('#add-customer-info-modal').modal('show');
      });


      $(document).on('click','.add-more-in-whatsapp-list',function(e) {
        var ele = $(this);
        $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            url: '/erp-customer/add-whatsapp-list',
            dataType: "json",
            beforeSend : function() {
                ele.attr('disabled', true);
            },
            success: function(response) {
                ele.attr('disabled', false);
                if(response.code == 200) {
                    toastr['success'](response.message);
                }
            },
            error: function() {
                toastr['error']('Can not add more in whatsapp list please review!');
            }
        });
      });

      $(document).on('click','.add-whatsapp-list',function(e) {

        var ele = $(this);

        var customers = [];
        $(".customer_message").each(function () {
            if ($(this).prop("checked") == true) {
                customers.push($(this).val());
            }
        });
        if (all_customers.length != 0) {
            customers = all_customers;
        }
        if (customers.length == 0) {
            alert('Please select Customer');
            return false;
        }

        $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: {ids:customers},
            url: '/erp-customer/add-customer-whatsapp-list',
            dataType: "json",
            beforeSend : function() {
                ele.attr('disabled', true);
            },
            success: function(response) {
                ele.attr('disabled', false);
                if(response.code == 200) {
                    toastr['success'](response.message);
                }
            },
            error: function() {
                toastr['error']('Can not add in whatsapp list please review!');
            }
        });
      });
      $(document).on('click','.resendMessage',function(event){
        var customers = [];

        $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: {ids: $(this).siblings('div.modal-header').children('#chat_obj_id').val()},
            url: 'erp-customer/resend-message',
            dataType: "json",
            beforeSend : function() {
                //ele.attr('disabled', true);
            },
            success: function(response) {
                //ele.attr('disabled', false);
                if(response.code == 200) {
                    toastr['success'](response.message);
                }
            },
            error: function() {
                toastr['error']('Error occured please try again later!');
            }
        });
      });
      $(document).on('click','.search-image',function(event){
          var customer_id = $(this).attr('data-object_type_id');
        $.ajax({
            type: "GET",
            url: "/erp-customer/search-image/"+$(this).attr('data-id')+'/'+$(this).attr('data-media-url').replaceAll('/', '|'),
            data:{'customer_id': customer_id},
            beforeSend : function() {
                toastr['success']('Image find process is started, you will get whatsapp notification as this process will be completed.');
            },
            success: function(response) {

            },
            error: function() {
                toastr['error']('Error occured please try again later!');
            }
        });
      });
let customer_id__ = null;


$(document).on('click','.set-vendor-id',function(e){
    e.preventDefault()
    customer_id__ = $(this).data("customer_id")
    console.log(customer_id__)
})





      $(document).on("submit","#generate-vendor",function(e) {
          e.preventDefault()
        var $this = $(this);

        console.log($(this).serialize())
            $.ajax({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: 'erp-customer/generate-vendor/'+customer_id__ ,
                dataType: "json",
                data:$(this).serialize(),
                beforeSend : function() {
                    //ele.attr('disabled', true);
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();

                    $('#set-vendor-category-and-name').modal('hide')


                    //ele.attr('disabled', false);
                    if(response.code == 200) {
                        $('#generate-vendor')[0].reset()
                        toastr['success'](response.message);
                    }else{
                        toastr['error'](response.message);
                    }
                },
                error: function() {
                    $('#generate-vendor')[0].reset()

                    $("#loading-image").hide();
                    toastr['error']('Error occured please try again later!');
                }
            });

      });

    let customer_priority = null;

    $(document).on('click','.set-priority',function(e){
        e.preventDefault()
        customer_id__ = $(this).data("customer_id");
        var store_website_id = $(this).data("store_website_id");
        $("#store_website_id").val(store_website_id);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{route('erp-customer.priority_count')}}/"+customer_id__+"/"+store_website_id ,
            dataType: "json",
            data:$(this).serialize(),
            beforeSend : function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if(response.code == 200) {
                    //toastr['success'](response.message);
                    $("#priority").val(response.data.priority) ;
                    var htmlData = '<tr><td>1</td><td>Lead Point</td><td>'+response.data.lead_count+'</td></tr><tr><td>2</td><td>Orders Point</td><td>'+response.data.orders_count+'</td></tr><tr><td>3</td><td>Refund Point</td><td>'+response.data.refund_count+'</td></tr><tr><td>4</td><td>Tickets Point</td><td>'+response.data.tickets_count+'</td></tr><tr><td>5</td><td>Return Point</td><td>'+response.data.returns_count+'</td></tr>';
                    $('.info_priority').html(htmlData);

                }else{
                    var htmlData = '<tr><td>1</td><td>Lead Point</td><td>0</td></tr><tr><td>2</td><td>Orders Point</td><td>0</td></tr><tr><td>3</td><td>Refund Point</td><td>0</td></tr><tr><td>4</td><td>Tickets Point</td><td>0</td></tr><tr><td>5</td><td>Return Point</td><td>0</td></tr>';
                    $('#priority-form')[0].reset()
                    $('.info_priority').html(htmlData);
                    toastr['error'](response.message);
                }
            },
            error: function() {
                $('#priority-form')[0].reset()
                var htmlData = '<tr><td>1</td><td>Lead Point</td><td>0</td></tr><tr><td>2</td><td>Orders Point</td><td>0</td></tr><tr><td>3</td><td>Refund Point</td><td>0</td></tr><tr><td>4</td><td>Tickets Point</td><td>0</td></tr><tr><td>5</td><td>Return Point</td><td>0</td></tr>';
                $('.info_priority').html(htmlData);
                toastr['error']('Error occured please try again later!');

            }
        });
    });

    $(document).on("submit","#priority-form",function(e) {
        e.preventDefault()
        var $this = $(this);
        var store_website_id = $("#store_website_id").val();
        console.log($(this).serialize())
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{route('erp-customer.priority')}}/"+customer_id__+"/"+store_website_id,
            dataType: "json",
            data:$(this).serialize(),
            beforeSend : function() {
                //ele.attr('disabled', true);
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                $('#set-priority').modal('hide');

                if(response.code == 200) {
                    //$('#priority-form')[0].reset()
                    $("#priority").val(response.data.priority) ;
                    toastr['success'](response.message);
                    var htmlData = '<tr><td>1</td><td>Lead Point</td><td>'+response.data.lead_count+'</td></tr><tr><td>2</td><td>Orders Point</td><td>'+response.data.orders_count+'</td></tr><tr><td>3</td><td>Refund Point</td><td>'+response.data.refund_count+'</td></tr><tr><td>4</td><td>Tickets Point</td><td>'+response.data.tickets_count+'</td></tr><tr><td>5</td><td>Return Point</td><td>'+response.data.returns_count+'</td></tr>';
                    $('.info_priority').html(htmlData);

                }else{
                    toastr['error'](response.message);
                }
            },
            error: function() {
                $('#priority-form')[0].reset()
                $("#loading-image").hide();
                toastr['error']('Error occured please try again later!');
            }
        });
      });
    });
    </script>
