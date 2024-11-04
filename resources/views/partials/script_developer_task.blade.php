<script type="text/javascript">

    $(document).on("click",".get-product-history",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '{{ route("scrap.product-hisotry")}}',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-product-history-table");
                model.find(".modal-title").html("Product History List");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
    });

     $(document).on("click",".get-tasks-remote",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '{{ route("scrap.task-list")}}',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Task List");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
    });

    $(document).on("click",".btn-create-task",function (e){
            e.preventDefault();
            var $this = $(this).closest("form");
            $.ajax({
                url: $this.attr("action"),
                type: $this.attr("method"),
                data: $this.serialize(),
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Task List");
                model.find(".modal-body").html(response);
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });


        $(document).on("click",".scraper-log-details",function(e) {
            var $this = $(this);
            $.ajax({
                type: 'GET',
                url: '{{ route('scrap.log-details') }}',
                data: {
                    scrapper_id : $this.data("scrapper-id")
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Log History");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });
        

        

        $(document).on("click","#show-content-model-table li",function (e){
            e.preventDefault();
            var a = $(this).find("a");
            if(typeof a != "undefined") {
                $.ajax({
                    url: a.attr("href"),
                    type: 'GET',
                    beforeSend: function () {
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                     $("#loading-image").hide();
                    var model  = $("#show-content-model-table");
                    model.find(".modal-body").html(response);
                }).fail(function() {
                    $("#loading-image").hide();
                    alert('Please check laravel log for more information')
                });
            }
        });

        $(document).on("click", ".delete_quick_comment-scrapp", function (e) {
            var deleteAuto = $(this).closest(".d-flex").find(".quickComments").find("option:selected").val();
            if (typeof deleteAuto != "undefined") {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: BASE_URL+"/scrap/statistics/reply/delete",
                    dataType: "json",
                    method: "POST",
                    data: {id: deleteAuto}
                }).done(function (data) {
                    if (data.code == 200) {
                        // $(".quickComment ")
                        //     .find('option').not(':first').remove();

                        $(".quickComment").each(function(){
                        var selecto=  $(this)
                            $(this).children("option").not(':first').each(function(){
                            $(this).remove();


                            });
                            $.each(data.data, function (k, v) {
                                $(selecto).append("<option  value='" + k + "'>" + v + "</option>");
                            });
                            $(selecto).select2({tags: true});
                        });
                    }

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            }
        });

        $(document).on('click', '.send-message1', function () {
            var thiss = $(this);
            var data = new FormData();
            var task = $(this).data('task-id');
            var message = $("#messageid_"+task).val();
            data.append("issue_id", task);
            data.append("message", message);
            data.append("status", 1);
            data.append("sendTo", $(".send-message-number-"+task).val());

            if (message.length > 0) {
                if (!$(this).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/issue',
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                            $("#loading-image").show();
                        }
                    }).done(function (response) {
                        //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                        $("#message-chat-txt-"+task).html(response.message.message);
                        $("#messageid_"+task).val('');
                        $("#loading-image").hide();
                        $(this).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(this).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                        $("#loading-image").hide();
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });

</script>