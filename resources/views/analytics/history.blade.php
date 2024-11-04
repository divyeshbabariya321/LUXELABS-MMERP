<div id="category-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="height: 800%; width: 115%">
        <div class="modal-header">
          <h4 class="modal-title">History</span></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="col-sm-2 pull-right pr-0">
            <select class="form-control mb-3 category-history-filter">
              <option value="">Select Search</option>
              <option value="error">Error</option>
              <option value="success">Success</option>
            </select>
          </div>
          <table class="table table-striped table-bordered" id="latest-remark-records">
            <thead>
              <tr>
                <th scope="col" width="6%">#</th>
                <th scope="col" width="15%">Website</th>
                <th scope="col" width="8%">Title</th>
                <th scope="col" width="54%">Description</th>
                <th scope="col" width="17%">Created At</th>
              </tr>
            </thead>
            <tbody class="show-list-records">

            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>