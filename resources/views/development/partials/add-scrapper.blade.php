<div id="addScrapperModel" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Scrapper</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form action="{{route('development.add-scrapper')}}" method="POST">
                    @csrf
                    <input type="hidden" name="task_id">
                    <input type="hidden" name="task_type">
                    <label for="">Enter Scrapper Values</label>
                    <div>
                        <textarea name="scrapper_values" id="scrapper_values" rows="10" class="form-control"></textarea>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-default mt-4 create-scrapper">Submit</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>