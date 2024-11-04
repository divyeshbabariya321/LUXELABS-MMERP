<div id="config-refactor-duplicate" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="config-refactor-duplicate-form" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Config Refactor Duplicate</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                        <?php echo csrf_field(); ?>
                        <div class="from-group mt-3">
                            <label for="">Select Website:</label>
                            <select name="store_website_id[]" id="assign-new-website" class="form-control select2" style="width: 100%!important" multiple>
                                <option value="" selected disabled>-- Select a Website --</option>
                                @forelse($store_websites as $website_id => $website_name)
                                    <option value="{{ $website_id }}">{{ $website_name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary duplicate-config-refactor">Duplicate</butto>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>