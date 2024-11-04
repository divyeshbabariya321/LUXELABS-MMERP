
<style>
   .commandhistory-table th:not(:first-child) {
        overflow-wrap: anywhere;
    }
</style>
<div id="commandResponseHistoryModelHeader" class="modal fade" role="dialog" style="z-index:2000">
        <div class="modal-dialog modal-lg" style="max-width: 100%;width: 90% !important;">
            <div class="modal-content ">
                <div id="add-mail-content">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Command Response History</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered commandhistory-yable">
                                <thead>
                                    <tr>
                                        <th style="width: 3%;">ID</th>
                                        <th style="width: 5%;">User Name</th>
                                        <th style="width: 5%;">Command Name</th>
                                        <th style="width: 5%;">Status</th>
                                        <th style="width: 5%;">Response</th>
                                        <th style="width: 5%;">Request</th>
                                        <th style="width: 5%;">Job ID</th>
                                        <th style="width: 4%;">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="tbodayCommandResponseHistory">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>