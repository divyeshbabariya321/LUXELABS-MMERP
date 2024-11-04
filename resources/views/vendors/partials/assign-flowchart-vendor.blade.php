<div id="assignFlowchartVendorModal" class="modal fade" role="dialog">
    <div class="modal-dialog">


        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Flowcharts</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form id="flowChartForm">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Select Flow Charts:</label>
                            <div class="form-group">
                                <select id="flowChartSelect" style="margin-top: 5px; width: 100%;" class="form-control js-select2-flowchart" multiple="multiple">
                                    @foreach ($flowchart_master as $master_flowchart)
                                        <option value="{{ $master_flowchart->id }}">{{ $master_flowchart->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label checkbox-inline mt-3" for="selectAll" id="selectAllLabel">
                                    Select All
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <input type="hidden" id="flowChartFormVendorId">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-default update-flowchart-date-btn">Save</button>
            </div>
        </div>

    </div>
</div>
