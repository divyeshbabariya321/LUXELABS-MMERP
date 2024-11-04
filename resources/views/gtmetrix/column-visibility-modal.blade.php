<div id="gtdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('gtmetrix.column.update') }}" method="POST" id="postman-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>ID</td>
                                <td>
                                    <input type="checkbox" value="ID" id="ID" name="column_gt[]" @if (!empty($dynamicColumnsToShowgt) && in_array('ID', $dynamicColumnsToShowgt)) checked @endif>
                                </td>
                            </tr>
                            @foreach ($catArr as $catN)
                                <tr>
                                    <td>{{$catN}}</td>
                                    <td>
                                        <input type="checkbox" value="{{$catN}}" id="{{$catN}}" name="column_gt[]" @if (!empty($dynamicColumnsToShowgt) && in_array($catN, $dynamicColumnsToShowgt)) checked @endif>
                                    </td>
                                </tr>
                            @endforeach
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