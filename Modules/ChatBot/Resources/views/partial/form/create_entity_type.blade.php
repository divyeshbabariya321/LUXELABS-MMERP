<div class="modal" id="add-entity-type" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?php echo route('chatbot.entity.type.create'); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Create Entity Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="value">Name </label>
                        {{ html()->text('name', null)->class('form-control')->placeholder('Enter your value') }}
                    </div>
                    <div class="form-group">
                        <label for="value">Display Name</label>
                        {{ html()->text('display_name', null)->class('form-control')->placeholder('Enter your value') }}
                    </div>
                    <div class="form-group">
                        <label for="value">Kind (Indicates the kind of entity type.)</label>
                        <select name="kind_name" id="" class="form-control">
                            <option value="">Select</option>
                            <option value="1">KIND_MAP</option>
                            <option value="2">KIND_LIST</option>
                            <option value="3">KIND_REGEXP</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary add-entity-type-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
