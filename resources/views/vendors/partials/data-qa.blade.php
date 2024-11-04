@foreach ($VendorQuestionAnswer as $vendor)
    @if(!empty($dynamicColumnsToShowVendorsqa))
        <tr>
            @if (!in_array('Vendor', $dynamicColumnsToShowVendorsqa))
                <td>{{ $vendor->name }}</td>
            @endif

            @if (!in_array('Categgory', $dynamicColumnsToShowVendorsqa))
                <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
            @endif
            
            @if($vendor_questions)
                @foreach($vendor_questions as $question_data)
                    @if (!in_array($question_data->id, $dynamicColumnsToShowVendorsqa))
                        @php
                            $status_color = new stdClass();
                            $status_hcolor = \App\Helpers\VendorHelper::getVendorQuestionStatusHistoryByQidAndVendorId($question_data->id, $vendor->id);
                            if (!empty($status_hcolor->new_value)) {
                                $status_color = \App\Helpers\VendorHelper::getVendorQuestionStatusById($status_hcolor->new_value);
                                \App\Models\VendorQuestionStatus::where('id',$status_hcolor->new_value)->first();
                            }
                        @endphp

                        <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="answer" placeholder="Answer" id="answer_{{ $vendor->id }}_{{ $question_data->id }}" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}">
                                <div style="margin-top: 0px;" class="d-flex p-0">
                                    <button type="button" class="btn pr-0 btn-xs btn-image " onclick="saveAnswer({{ $vendor->id }}, {{ $question_data->id }})"><img src="/images/filled-sent.png"></button>
                                    <button type="button" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}" class="btn btn-image answer-history-show p-0 ml-2" title="Answer Histories"><i class="fa fa-info-circle"></i></button>
                                </div>

                                <select style="margin-top: 0px;width:10% !important;" class="form-control status-dropdown" name="status" data-id="{{$vendor->id}}" data-question_id="{{$question_data->id}}">
                                    <option value="">Select Status</option>
                                    @foreach ($status_q as $stat)
                                        <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-id="{{ $vendor->id  }}" data-question_id="{{$question_data->id}}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>

                            </div>
                        </td>
                    @endif
                @endforeach
            @endif
        </tr>
    @else
        <tr>
            <td>{{ $vendor->name }}</td>
            <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
            @if($vendor_questions)
                @foreach($vendor_questions as $question_data)
                    @php
                        $status_color = new stdClass();
                        $status_hcolor = \App\Helpers\VendorHelper::getVendorQuestionStatusHistoryByQidAndVendorId($question_data->id, $vendor->id);
                        if (!empty($status_hcolor->new_value)) {
                            $status_color = \App\Helpers\VendorHelper::getVendorQuestionStatusById($status_hcolor->new_value);
                            \App\Models\VendorQuestionStatus::where('id',$status_hcolor->new_value)->first();
                        }
                    @endphp

                    <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                        <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                            <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="answer" placeholder="Answer" id="answer_{{ $vendor->id }}_{{ $question_data->id }}" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}">
                            <div style="margin-top: 0px;" class="d-flex p-0">
                                <button type="button" class="btn pr-0 btn-xs btn-image " onclick="saveAnswer({{ $vendor->id }}, {{ $question_data->id }})"><img src="/images/filled-sent.png"></button>
                                <button type="button" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}" class="btn btn-image answer-history-show p-0 ml-2" title="Answer Histories"><i class="fa fa-info-circle"></i></button>
                            </div>

                            <select style="margin-top: 0px;width:10% !important;" class="form-control status-dropdown" name="status" data-id="{{$vendor->id}}" data-question_id="{{$question_data->id}}">
                                <option value="">Select Status</option>
                                @foreach ($status_q as $stat)
                                    <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                @endforeach
                            </select>
                            <button type="button" data-id="{{ $vendor->id  }}" data-question_id="{{$question_data->id}}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>

                        </div>
                    </td>
                @endforeach
            @endif
        </tr>
    @endif
@endforeach