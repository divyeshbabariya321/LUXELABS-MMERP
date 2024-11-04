<div id="estiate_del-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="pt-4 pl-4 pr-4">
            <h4 class="modal-title">Estimated Delivery Date History</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="col-md-12">
                <div class="row">
                <input type="hidden" name="lead_developer_task_id" id="lead_developer_task_id">
                    <div class="col-md-12" id="lead_time_history_div">
                        <table class="table" style="table-layout: fixed;">
                            <thead>
                                <tr>
                                    <th width="20%">Date</th>
                                    <th width="20%">Old Value</th>
                                    <th width="20%">2022-02-14</th>
                                    <th width="20%">Updated by</th>
                                    <th width="20%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($estimated_delivery_histories))
                                @foreach($estimated_delivery_histories as $delDateHistory)
                                <tr>
                                <td>{{$delDateHistory->created_at}}</td>
                                <td class="Website-task" title="{{$delDateHistory->old_value}}">{{$delDateHistory->old_value}}</td>
                                <td class="Website-task" title="{{$delDateHistory->new_value}}">{{$delDateHistory->new_value}}</td>
                                <td class="Website-task" title="{{$delDateHistory->name}}">{{$delDateHistory->name}}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                <div class=" mt-4 mb-5 pl-4"style="margin-left: 707px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>