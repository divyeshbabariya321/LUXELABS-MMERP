<div id="rqadatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('vendors.rqa.column.update') }}" method="POST" id="vendors-rqa-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>Vendor</td>
                                <td>
                                    <input type="checkbox" value="Vendor" id="Vendor" name="column_vendorsfc[]" @if (!empty($dynamicColumnsToShowVendorsrqa) && in_array('Vendor', $dynamicColumnsToShowVendorsrqa)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Categgory</td>
                                <td>
                                    <input type="checkbox" value="Categgory" id="Categgory" name="column_vendorsfc[]" @if (!empty($dynamicColumnsToShowVendorsrqa) && in_array('Categgory', $dynamicColumnsToShowVendorsrqa)) checked @endif>
                                </td>
                            </tr>                            
                            @if($rating_questions)
                                @foreach($rating_questions as $question_data)
                                    <tr>
                                        <td>{{$question_data->question}}</td>
                                        <td>
                                            <input type="checkbox" value="{{$question_data->id}}" id="{{$question_data->id}}" name="column_vendorsfc[]" @if (!empty($dynamicColumnsToShowVendorsrqa) && in_array($question_data->id, $dynamicColumnsToShowVendorsrqa)) checked @endif>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </div>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>