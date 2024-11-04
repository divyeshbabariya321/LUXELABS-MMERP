@if ($isAdmin)
        <div id="emailAlertModal" class="modal fade mymodal" role="dialog">
            <div class="modal-dialog modal-lg">

                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="emailAlert-modal-subject">Subject</h4>
                        <button class="close modalMinimize"> <i class='fa fa-minus'></i> </button>
                        <button type="button" class=" btn" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p id="emailAlert-modal-body">
                            <iframe style="width: 100%;border:none;height:70vh;"
                                id="emailAlert-modal-body-myframe" frameborder="0"></iframe>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button id="emailAlert-reply" type="button" class="btn btn-default"
                            data-dismiss="modal">Reply
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="minmaxCon"></div>
    @endif