<div id="ui-check-modal-{{ $category->id }}" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ui Check Mapping</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="ui-check-body-{{ $category->id }}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary" onclick="setCheckUi({{ $category->id }}, {{ $category->site_development_id }})">Save</button>
            </div>
        </div>
    </div>
</div>