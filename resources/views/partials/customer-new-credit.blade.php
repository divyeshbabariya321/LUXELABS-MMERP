@php
$assigned_to = \App\User::with('roles')->get();
$statuses = \App\ticketStatuses::all();
@endphp
<style>
    #show_tickes_table {
        table-layout: fixed; 
        width: 100%
    }

    #show_tickes_table td {
        word-wrap: break-word;
    }
</style>
<!--form modal-->
<div class="modal fade" id="create-customer-credit-modal" role="dialog" aria-labelledby="create-customer-credit-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-customer-credit-modal-label">Create Credit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="credit_form">
                    <input type="hidden" id="credit_customer_id" name="credit_customer_id">
                    <input type="hidden" id="source_of_credit" name="source_of_credit" value="customer">
                    <div class="form-group">
                        <label for="credit" class="col-form-label">Credit:</label>
                        <input type="number" min="0" class="form-control" name="credit" id="credit">
                        <span class="text-danger" id="credit_error"></span>
                    </div>
                    <div class="form-group">
                        <input type="radio" class="d-inline" name="credit_type" value="PLUS" checked id="">PLUS
                        <input type="radio" class="d-inline" name="credit_type" value="MINUS" id="">MINUS
                    </div>
                    <div class="form-group">
                        <label for="currency" class="col-form-label">Currency:</label>
                        {{ html()->select('currency', \App\Currency::pluck('name', 'code')->toArray(), request('currency', 'EUR'))->class('form-control select2')->style("width:250px;")->attribute('tabindex', 1) }}
                        <span class="text-danger" id="currency_error"></span>
                    </div>
                    <div class="form-group email_template">
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="submit_credit_form" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

<!--table modal-->
<div class="modal fade" id="show-customer-credits-modal" tabindex="-1" role="dialog" aria-labelledby="show-customer-credits-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="show-customer-credits-modal-label">Show credits</h5>
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span class="pull-right" id="show-customer-current-credits-modal-label" style="    font-size: 18px;
    font-weight: bold;"></span>

                <table class="table table-striped-custom" id="show_tickes_table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Credit</th>
                            <th scope="col">Add/Deduction From</th>
                            <th scope="col">Transaction Type</th>
                            <th scope="col">Created At</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('body').on('click', '.create-customer-credit-modal', function () {
            $('#credit_customer_id').val($(this).attr('data-customer_id'));
            $.ajax({
                type: "POST",
                url: window.location.origin + '/livechat/create-credit',
                data: $('#credit_form').serialize(), // serializes the form's elements.
                success: function (data)
                {
                    if (data.status == 'success') {
                        alert('credit updated successfully.');
                        $('#credit_form').trigger("reset");
                        $('#create-customer-credit-modal').modal('toggle');
                    }else{
                        //console.log(data[0]);
                        //var msg = JSON.parse(JSON.parse(data[0]));
                        alert(data.msg);
                    }
                }, error: function (jqXHR, exception) {
                    var msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    alert(msg);
                }
            });
        });

        var inputBox = document.getElementById("credit");

        var invalidChars = [
        "-",
        "+",
        "e",
        ];

        inputBox.addEventListener("keydown", function(e) {
        if (invalidChars.includes(e.key)) {
            e.preventDefault();
        }
        });

        $('#submit_credit_form').click(function (e) {
            e.preventDefault();
            if ($('#credit').val() == '') {
                $('#credit_error').text('Credit filed is required.');
                return false;
            } else {
                $('#credit_error').text('');
            }
            
            $.ajax({
                type: "POST",
                url: window.location.origin + '/livechat/create-credit',
                data: $('#credit_form').serialize(), // serializes the form's elements.
                success: function (data)
                {
                    if (data.status == 'success') {
                        alert('credit updated successfully.');
                        $('#credit_form').trigger("reset");
                        $('#create-customer-credit-modal').modal('toggle');
                    }else{
                        //console.log(data[0]);
                        //var msg = JSON.parse(JSON.parse(data[0]));
                        alert(data.msg);
                    }
                }, error: function (jqXHR, exception) {
                    var msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    alert(msg);
                }
            });

        });

        $('body').on('click', '.show-customer-credits-modal', function () {
            $("#show-customer-current-credits-modal-label").text('');
            $.ajax({
                type: "GET",
                url: window.location.origin + '/livechat/get-credits-data',
                data: {customer_id: $(this).attr('data-customer_id')},
                success: function (response)
                {
                    if (response.status == 'success') {
                        var c = [];
                        $('#show_tickes_table tbody').html('');
                        $("#show-customer-current-credits-modal-label").text('Remaining Credit : '+response.currentcredit);
                        $.each(response.data, function (i, item) {
                            c.push("<tr><td>" + (parseInt(i) + 1) + "</td>");
                            c.push("<td>" + item.used_credit + "</td>");
                            c.push("<td>" + item.used_in + "</td>");
                            c.push("<td>" + item.type + "</td>");
                            c.push("<td>" + item.created_at + "</td></tr>");
                        });
                        $('#show_tickes_table tbody').html(c.join(""));
                    }
                }, error: function (jqXHR, exception) {
                    var msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    alert(msg);
                }
            });
        });
    });
</script>