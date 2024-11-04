<!-- Modal -->
<div id="create-jenkins-status-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">JenkinsBuild Failure Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">S.No</th>
                            <th width="5%">Project</th>
                            <th width="20%">Failure Status</th>
                        </tr>
                    </thead>
                    <tbody class="show-search-password-list" id="jenkins-status-modal-html">
                        <!-- Table rows will be dynamically populated here -->
                    </tbody>
                </table>
                <!-- Pagination links -->
                <div id="jenkins-status-modal-table-paginationLinks">
                    <!-- Pagination links will be dynamically populated here -->
                </div>
           </div>
        </div>
    </div>
</div>