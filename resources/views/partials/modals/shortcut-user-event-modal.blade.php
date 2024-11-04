<!-- Modal -->
<div id="shortcut-user-event-model" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">User Event</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="shortcut_create_event_form" action="/calendar/events" method="POST">
                <input id="e_type" type="hidden" name="type" value="event">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input id="e_date" required class="form-control" type="text" />
                    </div>
                    <div class="form-group">
                        <label for="time">Time</label>
                        <input id="e_time" required class="form-control" type="text" />
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input id="e_subject" required class="form-control" type="text" />
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input id="e_description" required class="form-control" type="text" />
                    </div>
                    <div class="form-group">
                        <label for="contacts">Vendors</label>
                        {{ html()->multiselect("vendors[]", $vendors->pluck("name", "id")->toArray())->id("e_vendors")->class("form-control e_select2-vendor")->required()->style("width:100%") }}
                    </div>
                    <div class="form-group">
                        <label for="contacts">Contacts (Comma separated for multiple)</label>
                        <input id="e_contacts" required class="form-control" type="text" />
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-default" value="Save" />
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#e_date').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $('#e_time').datetimepicker({
            format: 'HH:mm'
        });
        $(".e_select2-vendor").select2({ tags : true});
    });

    $("#shortcut_create_event_form").submit(function (e) {
        e.preventDefault();
        const modal = document.getElementById('shortcut-user-event-model');
        const date = modal.querySelector('#e_date').value;
        const time = modal.querySelector('#e_time').value;
        const subject = modal.querySelector('#e_subject').value;
        const description = modal.querySelector('#e_description').value;
        const contacts = modal.querySelector('#e_contacts').value;
        const type = modal.querySelector('#e_type').value;
        const vendors = [];
        $("#e_vendors :selected").each(function(){
            vendors.push($(this).val());
        });
        $.post(
            '/calendar/events', {
                date,
                time,
                subject,
                description,
                contacts,
                type,
                vendors
            },
            function(result) {
                toastr["success"](result.message);
                $('#shortcut-user-event-model').modal('hide');
            }
        );
    });

</script>
