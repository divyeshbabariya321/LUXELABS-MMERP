@if(!empty($VendorFlowchart))
    @foreach ($VendorFlowchart as $vendor)
        <form action="{{ route('vendors.flowchartupdatesorting') }}" method="POST">
            @csrf
            <div class="form-group col-md-12">
                <table class="table table-bordered table-striped">   
                    <thead>
                        <th>Category</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Sorting</th>
                    </thead>
                    @if($vendor_flow_charts)
                        <tbody>
                        @foreach($vendor_flow_charts as $flow_chart)
                            <tr>
                                <th>
                                    {{ $flow_chart->flowchart->name }}
                                </th>
                                <td>
                                    <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                        <input style="margin-top: 0px;width:40% !important;" type="text" class="form-control " name="message" placeholder="Remarks" id="remark_header_{{ $vendor_id }}_{{ $flow_chart->flowchart->id }}" data-vendorid="{{ $vendor_id }}" data-flow_chart_id="{{ $flow_chart->flowchart->id }}">

                                        <div style="margin-top: 0px;" class="d-flex p-0">
                                            <button type="button" class="btn pr-0 btn-xs btn-image " onclick="saveRemarksHeaderFc({{ $vendor_id }}, {{ $flow_chart->flowchart->id }})"><img src="/images/filled-sent.png"></button>

                                            <button type="button" data-vendorid="{{ $vendor_id }}" data-flow_chart_id="{{ $flow_chart->flowchart->id }}" class="btn btn-image remarks-history-show-header-fc p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                        <select style="margin-top: 0px;width:40% !important;" class="form-control status-dropdown-header-fc" name="status" data-id="{{$vendor_id}}" data-flow_chart_id="{{$flow_chart->flowchart->id}}">
                                            <option value="">Select Status</option>
                                            @foreach ($status as $stat)
                                                <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" data-id="{{ $vendor_id  }}" data-flow_chart_id="{{$flow_chart->flowchart->id}}" class="btn btn-image status-history-show-header-fc p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="updatesorting[<?php echo $flow_chart->id; ?>]" class="form-control" value="<?php echo $flow_chart->sorting_f; ?>">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endif
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update Sorting</button>
            </div>
        </form>
    @endforeach
@endif