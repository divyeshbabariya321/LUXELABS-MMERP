<div id="globalDatatableColumnVisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('global.column.update') }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $section_name }}" name="section_name">
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            @foreach ( $dynamic_columns_arr as $row )
                                <tr>
                                    <td>{{ $row }}</td>
                                    <td>
                                        <input type="checkbox" value="{{ $row }}" name="column_scrapper[]" @if (!empty($dynamicColumnsToShow) && in_array($row, $dynamicColumnsToShow)) checked @endif>
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