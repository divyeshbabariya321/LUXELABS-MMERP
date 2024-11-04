@if(!empty($VendorFlowchart))
    @foreach ($VendorFlowchart as $vendor)
        <table class="table table-bordered table-striped">            
            @if($vendor_flow_charts)
                @foreach($vendor_flow_charts as $flow_chart)
                    <tr>
                        <th>
                            {{ $flow_chart->name }}
                        </th>
                        <td>
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <input style="margin-top: 0px;width:40% !important;" type="text" class="form-control " name="message" placeholder="Remarks" id="remark_header_{{ $vendor_id }}_{{ $flow_chart->id }}" data-vendorid="{{ $vendor_id }}" data-flow_chart_id="{{ $flow_chart->id }}">

                                <div style="margin-top: 0px;" class="d-flex p-0">
                                    <button type="button" class="btn pr-0 btn-xs btn-image " onclick="saveRemarksHeaderFc({{ $vendor_id }}, {{ $flow_chart->id }})"><img src="/images/filled-sent.png"></button>

                                    <button type="button" data-vendorid="{{ $vendor_id }}" data-flow_chart_id="{{ $flow_chart->id }}" class="btn btn-image remarks-history-show-header-fc p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <select style="margin-top: 0px;width:40% !important;" class="form-control status-dropdown-header-fc" name="status" data-id="{{$vendor_id}}" data-flow_chart_id="{{$flow_chart->id}}">
                                    <option value="">Select Status</option>
                                    @foreach ($status as $stat)
                                        <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-id="{{ $vendor_id  }}" data-flow_chart_id="{{$flow_chart->id}}" class="btn btn-image status-history-show-header-fc p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    @endforeach
@endif