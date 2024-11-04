@foreach ($VendorQuestionAnswer as $vendor)
    @if(!empty($dynamicColumnsToShowVendorsrqa))
        <tr>
            @if (!in_array('Vendor', $dynamicColumnsToShowVendorsrqa))
                <td>{{ $vendor->name }}</td>
            @endif

            @if (!in_array('Categgory', $dynamicColumnsToShowVendorsrqa))
                <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
            @endif
            
            @if($rating_questions)
                @foreach($rating_questions as $question_data)
                    @if (!in_array($question_data->id, $dynamicColumnsToShowVendorsrqa))
                        @php
                            $status_color = new stdClass();
                            $status_hcolor = \App\Helpers\VendorHelper::getVendorRatingQAStatusHistoryByQidAndVendorId($question_data->id, $vendor->id);
                            if (!empty($status_hcolor->new_value)) {
                                $status_color = \App\Helpers\VendorHelper::getVendorRatingQAStatusById($status_hcolor->new_value);
                            }
                        @endphp
                        <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <select style="margin-top: 0px;width:10% !important;" class="form-control " name="answer" id="answerr_{{ $vendor->id }}_{{ $question_data->id }}" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}">
                                    <option>-Select rating-</option>
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                                </select>
                                <div style="margin-top: 0px; margin-right: 10px;" class="d-flex p-0">
                                    <button type="button" class="btn pr-0 btn-xs btn-image " onclick="saverAnswer({{ $vendor->id }}, {{ $question_data->id }})"><img src="/images/filled-sent.png"></button>
                                    <button type="button" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}" class="btn btn-image ranswer-history-show p-0 ml-2" title="Answer Histories"><i class="fa fa-info-circle"></i></button>
                                </div>
                            
                                <select style="margin-top: 0px;width:10% !important;" class="form-control status-dropdown" name="status" data-id="{{$vendor->id}}" data-question_id="{{$question_data->id}}">
                                    <option value="">Select Status</option>
                                    @foreach ($status as $stat)
                                        <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-id="{{ $vendor->id  }}" data-question_id="{{$question_data->id}}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>

                                <button type="button" class="btn btn-image add-note-answer" title="Add Rating Question Answer Note" data-id="{{$vendor->id}}" data-question_id="{{$question_data->id}}"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
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
            @if($rating_questions)
                @foreach($rating_questions as $question_data)
                    @php
                        $status_color = new stdClass();
                        $status_hcolor = \App\Helpers\VendorHelper::getVendorRatingQAStatusHistoryByQidAndVendorId($question_data->id, $vendor->id);
                        if (!empty($status_hcolor->new_value)) {
                            $status_color = \App\Helpers\VendorHelper::getVendorRatingQAStatusById($status_hcolor->new_value);
                        }
                    @endphp
                    <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                        <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                            <select style="margin-top: 0px;width:10% !important;" class="form-control " name="answer" id="answerr_{{ $vendor->id }}_{{ $question_data->id }}" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}">
                                <option>-Select rating-</option>
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                            </select>
                            <div style="margin-top: 0px; margin-right: 10px;" class="d-flex p-0">
                                <button type="button" class="btn pr-0 btn-xs btn-image " onclick="saverAnswer({{ $vendor->id }}, {{ $question_data->id }})"><img src="/images/filled-sent.png"></button>
                                <button type="button" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}" class="btn btn-image ranswer-history-show p-0 ml-2" title="Answer Histories"><i class="fa fa-info-circle"></i></button>
                            </div>
                        
                            <select style="margin-top: 0px;width:10% !important;" class="form-control status-dropdown" name="status" data-id="{{$vendor->id}}" data-question_id="{{$question_data->id}}">
                                <option value="">Select Status</option>
                                @foreach ($status as $stat)
                                    <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                @endforeach
                            </select>
                            <button type="button" data-id="{{ $vendor->id  }}" data-question_id="{{$question_data->id}}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>

                            <button type="button" class="btn btn-image add-note-answer" title="Add Rating Question Answer Note" data-id="{{$vendor->id}}" data-question_id="{{$question_data->id}}"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                        </div>
                    </td>
                @endforeach
            @endif
        </tr>
    @endif
@endforeach