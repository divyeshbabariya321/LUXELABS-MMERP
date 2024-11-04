<style>
  .tbodayPostmanResponsesHistory td {
    word-break: break-all;
  }
</style>
<div id="responses-history-modal" class="modal fade in" role="dialog">
  <div class="modal-dialog modal-lg" style="width: 90%;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Reponses History</h4>
        <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-bordered ">
              <thead>
                  <tr>
                  <th >ID</th>
                  <th >Request</th>
                  <th>Paramers</th>
                  <th>Response</th>
                  <th>Response Code</th>
                  <th>User Name</th>
                  <th>Date</th>
                  </tr>
              </thead>
              <tbody class="tbodayPostmanResponsesHistory">
              </tbody>
              </table>

            </div>
          </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
     
    </div>

  </div>
</div>