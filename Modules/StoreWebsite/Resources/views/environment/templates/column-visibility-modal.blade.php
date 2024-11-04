<div id="sedatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('store-website.environment.column.update') }}" method="POST" id="store-website-environment-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>Env Path</td>
                                <td>
                                    <input type="checkbox" value="Env Path" id="Env Path" name="column_se[]" @if (!empty($dynamicColumnsToShowse) && in_array('Env Path', $dynamicColumnsToShowse)) checked @endif>
                                </td>
                            </tr>

                            @foreach($storeWebsites as $id => $title)
                                <tr>
                                    <td>{{$title}}</td>
                                    <td>
                                        <input type="checkbox" value="{{$id}}" id="{{$id}}" name="column_se[]" @if (!empty($dynamicColumnsToShowse) && in_array($id, $dynamicColumnsToShowse)) checked @endif>
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