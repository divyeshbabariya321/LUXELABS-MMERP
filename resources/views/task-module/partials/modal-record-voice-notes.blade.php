<div id="record-voice-notes" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Record & Send Voice Message</h4>
            </div>
            <div class="modal-body">
                <Style>
                    #rvn_status:after {
                        overflow: hidden;
                        display: inline-block;
                        vertical-align: bottom;
                        -webkit-animation: ellipsis steps(4, end) 900ms infinite;
                        animation: ellipsis steps(4, end) 900ms infinite;
                        content: "\2026";
                        /* ascii code for the ellipsis character */
                        width: 0px;
                    }

                    @keyframes ellipsis {
                        to {
                            width: 40px;
                        }
                    }

                    @-webkit-keyframes ellipsis {
                        to {
                            width: 40px;
                        }
                    }
                </style>
                <input type="hidden" name="rvn_id" id="rvn_id" value="">
                <input type="hidden" name="rvn_tid" id="rvn_tid" value="">
                <button id="rvn_recordButton" class="btn btn-s btn-secondary">Start Recording</button>
                <button id="rvn_pauseButton" class="btn btn-s btn-secondary" disabled>Pause Recording</button>
                <button id="rvn_stopButton" class="btn btn-s btn-secondary" disabled>Stop Recording</button>
                <div id="formats">Format: start recording to see sample rate</div>
                <div id="rvn_status">Status: Not started...</div>
                <div id="recordingsList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="rvn-btn-close-modal" class="btn btn-default" data-dismiss="modal">Close
                </button>
            </div>
        </div>
    </div>
</div>