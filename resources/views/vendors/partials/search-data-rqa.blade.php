@if(!empty($VendorQuestionRAnswer))
    @foreach ($VendorQuestionRAnswer as $vendor)
        <table class="table table-bordered table-striped">            
            @if($vendor_r_questions)
                @foreach($vendor_r_questions as $question_data)
                    <tr>
                        <th>
                            {{ $question_data->question }}
                        </th>
                        <td>
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">

                                <select class="form-control " name="answer" id="answerr_header_{{ $vendor_id }}_{{ $question_data->id }}" data-vendorid="{{ $vendor->id }}" data-rqa_id="{{ $question_data->id }}">
                                    <option>-Select rating-</option>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>

                                <div style="margin-top: 0px;" class="d-flex p-0">
                                    <button type="button" class="btn pr-0 btn-xs btn-image " onclick="saveAnswerHeaderRQa({{ $vendor_id }}, {{ $question_data->id }})"><img src="/images/filled-sent.png"></button>

                                    <button type="button" data-vendorid="{{ $vendor_id }}" data-rqa_id="{{ $question_data->id }}" class="btn btn-image ranswer-history-show-header-rqa p-0 ml-2" title="Answer Histories"><i class="fa fa-info-circle"></i></button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <select style="margin-top: 0px;width:40% !important;" class="form-control status-dropdown-header-rqa" name="status" data-id="{{$vendor_id}}" data-rqa_id="{{$question_data->id}}">
                                    <option value="">Select Status</option>
                                    @foreach ($status_r as $stat)
                                        <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-id="{{ $vendor_id  }}" data-rqa_id="{{$question_data->id}}" class="btn btn-image status-history-show-header-rqa p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    @endforeach
@endif