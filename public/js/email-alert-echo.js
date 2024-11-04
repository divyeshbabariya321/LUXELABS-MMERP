var modalContentQueue = [];
var isShowEmailAlertModal = false;
let email_data_alert = null;

function processMinMax(module) {
    $modalCon = module.closest(".mymodal").attr("id");
    $apnData = module.closest(".mymodal");
    $modal = "#" + $modalCon;
    $(".modal-backdrop").addClass("d-none");
    $($modal).toggleClass("min");

    var htmal_data = $apnData.find("iframe").contents().find('body').html();
    if ($($modal).hasClass("min")) {
        $(".minmaxCon").append($apnData);
        $(".minmaxCon").find("iframe").contents().find('body').html(htmal_data);
        module.find("i").toggleClass('fa-minus').toggleClass('fa-clone');
    } else {
        $(".container").append($apnData);
        $(".container").find("iframe").contents().find('body').html(htmal_data);
        module.find("i").toggleClass('fa-clone').toggleClass('fa-minus');
    };
}

function loadModal() {
    if (modalContentQueue.length > 0) {
        let firstEvent = modalContentQueue[0];
        email_data_alert = firstEvent.email;
        var myFrame = $("#emailAlert-modal-body-myframe").contents().find('body');
        var textareaValue = firstEvent.email.message;
        myFrame.html(textareaValue);
        $("#emailAlert-modal-subject").html(firstEvent.email.subject);
        $("#modal-container-1").load("/emailAlertModal", function () {
            $("#emailAlertModal").modal('show');
        });
        $('#emailAlertModal').css('display', 'block');
        isShowEmailAlertModal = true;
    } else {
        isShowEmailAlertModal = false;
    }
}

if (isPusherInitialized()) {

    window.Echo.private('emails')
        .listen('.email.received', (e) => {
            modalContentQueue.push(e);
            if (!($("#emailAlertModal").data('bs.modal')?.isShown)) {
                loadModal();
            }
        });

    window.Echo.private('emails')
        .listenForWhisper('alert-modal-closed', (e) => {
            if (isShowEmailAlertModal) {
                $('#emailAlertModal').modal('hide');
                $('.modal-backdrop').hide();
                isShowEmailAlertModal = false;
            }
        })
        .listenForWhisper('alert-modal-min_max', (e) => {
            if (e.id) {
                let module_ = $("#" + e.id + " .modalMinimize");
                processMinMax(module_);
            }
        });

    $("#emailAlertModal").on("hidden.bs.modal", function (e) {
        if (isShowEmailAlertModal) {
            window.Echo.private('emails')
                .whisper('alert-modal-closed', {});
        }
        modalContentQueue.shift();
        loadModal();

    });

    $("#emailAlertModal .btn").on("click", function (e) {
        window.Echo.private('emails')
            .whisper('alert-modal-closed', {});
    });

    $(document).on("click","#emailAlert-reply", function () {
        openQuickMsg(email_data_alert);
        $("#view-quick-email").modal('show');
    });
}

$(document).ready(function () {
    var $content, $modal, $apnData, $modalCon;
    $content = $(".min");
    $(".mdlFire").click(function (e) {

        e.preventDefault();
        var $id = $(this).attr("data-target");

        $($id).modal({
            backdrop: false,
            keyboard: false
        });

    });

    $(".modalMinimize").on("click", function () {
        $modalCon = $(this).closest(".mymodal").attr("id");
        processMinMax($(this));
        window.Echo.private('emails')
            .whisper('alert-modal-min_max', { "id": $modalCon });
    });

});
