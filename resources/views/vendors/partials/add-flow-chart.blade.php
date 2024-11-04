<div id="newFlowChartModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mt-0">Add new flow chart</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form style="padding:10px;" action="{{ route('vendor.flowchart.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <select id="new-flow-chart-master-ids" name="master_id" class="form-control" required @if($flowchart_master->count() == 1) {{'readonly'}} @endif>
                        <option id="new-flow-chart-master-ids-default" @if($flowchart_master->count() == 1) {{'disabled'}} @endif value="" >Select Type</option>
                        @foreach($flowchart_master as $flowchart_master_record)
                        <option @if($flowchart_master->count() == 1) {{'selected'}} @endif value="{{$flowchart_master_record->id}}">{{$flowchart_master_record->title}}</option>
                        @endforeach

                    </select>

                    @if ($errors->has('master_id'))
                        <div class="alert alert-danger">{{$errors->first('master_id')}}</div>
                    @endif
                </div>

                <div class="form-group">
                    <input type="text" class="form-control" name="name" placeholder="Name" value="{{ old('name') }}" required>

                    @if ($errors->has('name'))
                        <div class="alert alert-danger">{{$errors->first('name')}}</div>
                    @endif
                </div>

                <div class="form-group">
                    <input type="number" class="form-control" name="sorting" placeholder="Sorting" value="{{ old('sorting') }}" required>

                    @if ($errors->has('sorting'))
                        <div class="alert alert-danger">{{$errors->first('sorting')}}</div>
                    @endif
                </div>

                <button type="submit" class="btn btn-secondary">Add Flow Chart</button>
            </form>

            <div class="form-group col-md-12">
                <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                    <tr>
                        <td class="text-center"><b>Type</b></td>
                        <td class="text-center"><b>Flow Chart</b></td>
                        <td class="text-center"><b>Sorting</b></td>
                    </tr>
                    @foreach($vendor_flow_charts as $vendorflowchart)
                        <tr>
                            <td class="vendorflowchart-master-title-{{ $vendorflowchart->master_id }}">
                                {{($vendorflowchart->master)?$vendorflowchart->master->title:''}}
                            </td>
                            <td>
                                {{$vendorflowchart->name}}
                            </td>
                            <td>
                                {{$vendorflowchart->sorting}}
                            </td>                              
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>