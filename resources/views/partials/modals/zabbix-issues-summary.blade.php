<!-- Modal -->
<div id="zabbix-issues-summary-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Zabbix Issues</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th width="10%">Subject</th>
                            <th width="10%">Message</th>
                            <th width="10%">Event Start</th>
                            <th width="10%">Event Name</th>
                            <th width="10%">Host</th>
                            <th width="10%">Severity</th>
                            <th width="10%">Operational Data</th>
                            <th width="10%">Event Id</th>
                        </tr>
                    </thead>
                    <tbody class="show-search-password-list" id="zabbix-issues-summary-modal-html">
                    </tbody>
                </table>
                <div id="zabbix-issues-summary-modal-table-paginationLinks">
                </div>
           </div>
        </div>
    </div>
</div>
