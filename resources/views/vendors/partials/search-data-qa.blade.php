@if(!empty($VendorQuestionAnswer))
    @foreach ($VendorQuestionAnswer as $vendor)
        <table class="table table-bordered table-striped">            
            @if($vendor_questions)
                @foreach($vendor_questions as $question_data)
                    <tr>
                        <th>
                            {{ $question_data->question }}
                        </th>
                        <td>
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <input style="margin-top: 0px;width:40% !important;" type="text" class="form-control " name="message" placeholder="Answer" id="answer_header_{{ $vendor_id }}_{{ $question_data->id }}" data-vendorid="{{ $vendor_id }}" data-qa_id="{{ $question_data->id }}">

                                <div style="margin-top: 0px;" class="d-flex p-0">
                                    <button type="button" class="btn pr-0 btn-xs btn-image " onclick="saveAnswerHeaderQa({{ $vendor_id }}, {{ $question_data->id }})"><img src="/images/filled-sent.png"></button>

                                    <button type="button" data-vendorid="{{ $vendor_id }}" data-qa_id="{{ $question_data->id }}" class="btn btn-image answer-history-show-header-qa p-0 ml-2" title="Answer Histories"><i class="fa fa-info-circle"></i></button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <select style="margin-top: 0px;width:40% !important;" class="form-control status-dropdown-header-qa" name="status" data-id="{{$vendor_id}}" data-qa_id="{{$question_data->id}}">
                                    <option value="">Select Status</option>
                                    @foreach ($status_q as $stat)
                                        <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-id="{{ $vendor_id  }}" data-qa_id="{{$question_data->id}}" class="btn btn-image status-history-show-header-qa p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    @endforeach
@endif