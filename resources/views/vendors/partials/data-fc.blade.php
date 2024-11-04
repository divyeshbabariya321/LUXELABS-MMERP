@foreach ($VendorFlowchart as $vendor)
    @if(!empty($dynamicColumnsToShowVendorsfc))
        <tr>
            @if (!in_array('Vendor', $dynamicColumnsToShowVendorsfc))
                <td>
                    {{ $vendor->name }}

                    <button type="button" data-vendorid="{{ $vendor->id }}" data-vendorname="{{ $vendor->name }}" class="btn btn-image flowchart-history-show p-0 ml-2" title="Flowchart Histories"><i class="fa fa-info-circle"></i></button>
                </td>
            @endif

            @if (!in_array('Categgory', $dynamicColumnsToShowVendorsfc))
                <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
            @endif
            
            @if($vendor_flow_charts)
                @foreach($vendor_flow_charts as $flow_chart)
                    @php
                        $status_color = new stdClass();
                        $status_hcolor = \App\Helpers\VendorHelper::getVendorFlowChartStatusHistoryByFlowChartAndVendorId($flow_chart->id, $vendor->id);
                        if (!empty($status_hcolor->new_value)) {
                            $status_color = \App\Helpers\VendorHelper::getVendorFlowChartStatusById($status_hcolor->new_value);
                        }
                    @endphp
                    @if (!in_array($flow_chart->id, $dynamicColumnsToShowVendorsfc))
                        <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <input style="margin-top: 0px;width:40% !important;" type="text" class="form-control " name="message" placeholder="Remarks" id="remark_{{ $vendor->id }}_{{ $flow_chart->id }}" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}">
                                <div style="margin-top: 0px;" class="d-flex p-0">
                                    <button type="button" class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{ $vendor->id }}, {{ $flow_chart->id }})"><img src="/images/filled-sent.png"></button>
                                    <button type="button" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}" class="btn btn-image remarks-history-show p-0 ml-2" title="Remarks Histories"><i class="fa fa-info-circle"></i></button>
                                </div>

                                <select style="margin-top: 0px;width:40% !important;" class="form-control status-dropdown" name="status" data-id="{{$vendor->id}}" data-flow_chart_id="{{$flow_chart->id}}">
                                    <option value="">Select Status</option>
                                    @foreach ($status as $stat)
                                        <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-id="{{ $vendor->id  }}" data-flow_chart_id="{{$flow_chart->id}}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>

                                <button type="button" class="btn btn-image add-note-flowchart" title="Add Flow chart Note" data-id="{{$vendor->id}}" data-flow_chart_id="{{$flow_chart->id}}"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                            </div>
                        </td>
                    @endif
                @endforeach
            @endif
        </tr>
    @else
        <tr>
            <td>
                {{ $vendor->name }}

                <button type="button" data-vendorid="{{ $vendor->id }}" data-vendorname="{{ $vendor->name }}" class="btn btn-image flowchart-history-show p-0 ml-2" title="Flowchart Histories"><i class="fa fa-info-circle"></i></button>
            </td>
            <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
            @if($vendor_flow_charts)
                @foreach($vendor_flow_charts as $flow_chart)
                    @php
                        $status_color = new stdClass();
                        $status_hcolor = \App\Helpers\VendorHelper::getVendorFlowChartStatusHistoryByFlowChartAndVendorId($flow_chart->id, $vendor->id);
                        if (!empty($status_hcolor->new_value)) {
                            $status_color = \App\Helpers\VendorHelper::getVendorFlowChartStatusById($status_hcolor->new_value);
                        }
                    @endphp
                    <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                        <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                            <input style="margin-top: 0px;width:40% !important;" type="text" class="form-control " name="message" placeholder="Remarks" id="remark_{{ $vendor->id }}_{{ $flow_chart->id }}" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}">
                            <div style="margin-top: 0px;" class="d-flex p-0">
                                <button type="button" class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{ $vendor->id }}, {{ $flow_chart->id }})"><img src="/images/filled-sent.png"></button>
                                <button type="button" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}" class="btn btn-image remarks-history-show p-0 ml-2" title="Remarks Histories"><i class="fa fa-info-circle"></i></button>
                            </div>

                            <select style="margin-top: 0px;width:40% !important;" class="form-control status-dropdown" name="status" data-id="{{$vendor->id}}" data-flow_chart_id="{{$flow_chart->id}}">
                                <option value="">Select Status</option>
                                @foreach ($status as $stat)
                                    <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                @endforeach
                            </select>
                            <button type="button" data-id="{{ $vendor->id  }}" data-flow_chart_id="{{$flow_chart->id}}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>

                            <button type="button" class="btn btn-image add-note-flowchart" title="Add Flow chart Note" data-id="{{$vendor->id}}" data-flow_chart_id="{{$flow_chart->id}}"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                        </div>
                    </td>
                @endforeach
            @endif
        </tr>
    @endif
@endforeach