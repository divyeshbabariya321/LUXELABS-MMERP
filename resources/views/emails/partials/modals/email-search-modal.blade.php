<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }} ">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Email Search</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex" id="search-bar">
                        <input type="text" value="" name="search" id="menu_email_search"
                            class="form-control" placeholder="Search Here.." style="width: 30%;">
                        <a title="Email Search" type="button"
                            class="email_search_menu btn btn-sm btn-image " style="padding: 10px"><span>
                                <img src="{{ asset('images/search.png') }}" alt="Search"></span></a>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered page-notes"
                            style="font-size:13.8px;border:0px !important;" id="emailNameTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Sender</th>
                                    <th>Receiver</th>
                                    <th>Subject & Body</th>
                                    <th>Action</th>
                                    <th>Read</th>
                                </tr>
                            </thead>
                            <tbody class="email_search_result">
                                @include('emails.email-search-modal-content', ['userEmails' => $userEmails])
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var configsesm = {
        routes : {
            'menu_email_search' : "{{ route('menu.email.search') }}",
        }
    };
    $(document).ready(function () {
        $(document).on("click", ".email_search_menu", function (e) {
            let $this = $("#menu_email_search").val();
            var q = $this;
            $.ajax({
                url: configsesm.routes.menu_email_search,
                type: "GET",
                data: {
                search: q,
                },
                headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    $(".email_search_result").empty();
                    $(".email_search_result").append(response);
                    toastr["success"]("Data updated successfully", "success");
                },
                error: function () {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
                },
            });
        });
    });
</script>