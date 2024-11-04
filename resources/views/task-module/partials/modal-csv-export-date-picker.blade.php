<div class="modal fade" id="csv-date-range-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content csv-date-range-form">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div style="display: flex">
                        <label for="csv-date-range" class="col-form-label">Created Date: &nbsp;&nbsp;</label>
                        <input type="checkbox" name="is-filter-created" id="is-filter-created" checked style="margin: 0px;">
                    </div> 
                    <input type="text" class="form-control" id="csv-date-range" name="csv-date-range"
                        autocomplete="off">
                </div>
                <div class="form-group">
                    <div style="display: flex">
                        <label for="csv-date-range" class="col-form-label">Tracked Date: &nbsp;&nbsp;</label>
                        <input type="checkbox" name="is-filter-tracked" id="is-filter-tracked" checked style="margin: 0px;">
                    </div>
                    <input type="text" class="form-control" id="csv-date-range-tracked" name="csv-date-range-tracked"
                        autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="user" class="col-form-label">User:</label>
                    <select class="form-control" id="user" name="user" autocomplete="off">
                        @foreach ($usersForExport as $id => $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Load</button>
            </div>
        </form>
    </div>
</div>


<script>
    let startDate = null;
    let endDate = null;
    let startDateTracked = null;
    let endDateTracked = null;
    let currentPage = 'csv-download-page';
    let dataTaskType;

    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    $(document).ready(function() {
        $("button[page='csv-download-page']").click(function() {
            currentPage = 'csv-download-page';
        });

        $("button[page='view-all-task-page']").click(function() {
            currentPage = 'view-all-task-page';
        });

        $(function() {
            $('#csv-date-range').daterangepicker({
                timePicker: true,
                opens: 'left',
                autoApply: true,
                locale: {
                    cancelLabel: 'clear'
                },
            }, function(start, end, label) {
                startDate = start.format('YYYY-MM-DD HH:mm:ss');
                endDate = end.format('YYYY-MM-DD HH:mm:ss')
            });

            $('#csv-date-range-tracked').daterangepicker({
                timePicker: true,
                opens: 'left',
                autoApply: true,
                locale: {
                    cancelLabel: 'clear'
                },
            }, function(start, end, label) {
                startDateTracked = start.format('YYYY-MM-DD HH:mm:ss');
                endDateTracked = end.format('YYYY-MM-DD HH:mm:ss')
            });

        });

        $("[name='user']").select2({});

        $(".select2-container").css('width', '100%');

        $('#csv-date-range').on('cancel.daterangepicker', function(ev, picker) {
            $('#csv-date-range').val('');
            startDate = null;
            endDate = null;
        });
    });

    $(document).on('show.bs.modal', "#csv-date-range-modal", function() {
        console.log("Modal is about to be shown");
        dataTaskType = $('.csv-date-range-modal-btn').data('task-type');
    });

    $(document).on('submit', '.csv-date-range-form', function(e) {
        e.preventDefault();
        let pathname = '';

        let userInput = $("[name='user']");

        switch (currentPage) {
            case 'csv-download-page':
                pathname = "{{ route('development.task.exportTask') }}";
                break;
            case 'view-all-task-page':
                pathname = "{{ route('development.task.viewAllTasks') }}";
                break;
        }

        const url = new URL(pathname);
        const params = new URLSearchParams();
        params.append('dataTaskType', dataTaskType);

        if ($('#is-filter-created').prop('checked')) {
            params.append('startDate', startDate ? startDate : moment().startOf('day')
                .format('YYYY-MM-DD HH:mm:ss'));
            params.append('endDate', endDate ? endDate : moment().endOf('day')
                .format('YYYY-MM-DD HH:mm:ss'));
        }

        if ($('#is-filter-tracked').prop('checked')) {
            params.append('startDateTracked', startDateTracked ? startDateTracked : moment().startOf('day')
                .format('YYYY-MM-DD HH:mm:ss'));
            params.append('endDateTracked', endDateTracked ? endDateTracked : moment().endOf('day')
                .format('YYYY-MM-DD HH:mm:ss'));
        }

        if (userInput)
            params.append('assigned_to', userInput.val());

        if (params.toString() != '')
            url.search = params.toString();
        // console.log(url);
        open(url, "_blank");
    });
</script>
