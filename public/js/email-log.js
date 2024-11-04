$(function () {

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    today = yyyy + '-' + mm + '-' + dd;
    $('#from_date').val(today);
    $('#to_date').val(today);
    $(".select-multiple2").select2({
        width: '100%'
    });

    var status_id =  0;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var table = $('#crop-rejected-table').DataTable({
        columnDefs: [{
            "defaultContent": "-",
            "targets": "_all"
        }],
        processing: true,
        serverSide: true,
        searching: false,
        info: false,
        paging: true,
        lengthChange: true,
        responsive: true,
        ajax: {
            url: "/email-log/ajax-list",
            method: "POST",
            data: function (d) {
                d.global_search = $('#global_search').val();
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.module = $('#module').val();
            }
        },
        columns: [{
                data: 'id',
                name: 'emails.id',
                orderable: false
            },
            {
                data: 'created_at',
                name: 'emails.created_at',
                render: function (data, type, full, meta) {
                    // Assuming data is in standard MySQL datetime format
                    var date = new Date(data);
                    var day = date.getDate().toString().padStart(2, '0');
                    var month = (date.getMonth() + 1).toString().padStart(2, '0');
                    var year = date.getFullYear();
                    var hours = date.getHours().toString().padStart(2, '0');
                    var minutes = date.getMinutes().toString().padStart(2, '0');
                    var seconds = date.getSeconds().toString().padStart(2, '0');
                    return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes + ':' + seconds;
                }
            },
            {
                data: 'message',
                name: 'emails.message',
            },
            {
                data: 'model_type',
                name: 'emails.model_type'
            },
            {
                data: 'from',
                name: 'emails.from'
            },
            {
                data: 'to',
                name: 'emails.to'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false
            },
        ],
        drawCallback: function () {
            var api = this.api();
            var recordsTotal = api.page.info().recordsTotal;
            $("#lbl_total_record_count").text(recordsTotal);
        },
    });

    $('#filter').click(function () {
        redrawTable(table);
    });

    $('#refresh').click(function () {
        $('#global_search').val('');
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();
        today = yyyy + '-' + mm + '-' + dd;
        $('#from_date').val(today);
        $('#to_date').val(today);
        $('#module').val($('#module option:first').val()).trigger('change');
        redrawTable(table);
    });
    
    $('#delete-selected').click(function () {
        var selectedIds = [];
        $('.select-checkbox:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });

        // Check if any items are selected
        if(selectedIds.length > 0) {
            // Display confirmation popup
            var confirmation = confirm("Are you sure you want to delete the selected items?");
            if (confirmation) {
                // If user confirms, proceed with deletion
                $.ajax({
                    url: "/email-log/remove",
                    method: "POST",
                    data: { deleteLogId: selectedIds },
                    success: function(response) {
                        redrawTable(table);
                    }
                });
            }
        } else {
            // If no items are selected, display a message
            toastr["error"]("Please select items to delete.", "error");
        }
    });

    $('#delete-all').click(function () {
        // Display confirmation popup
        var confirmation = confirm("Are you sure you want to delete all items?");
        if (confirmation) {
            // If user confirms, proceed with deletion
            $.ajax({
                url: "/email-log/remove",
                method: "POST",
                data: { isEmptyLog: 1 },
                success: function(response) {
                    redrawTable(table);
                }
            });
        }
    });

    function redrawTable(table) {
        table.draw();
    }
});
