<div id="searchPassswordModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Password Manager</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="database-form">
                            @csrf
                            <div class="row">
                                <div class="col-12 pb-3">
                                    <input type="text" name="task_search" class="password-search-table" class="form-control" placeholder="Enter Website">
                                    <button type="button" class="btn btn-secondary btn-password-search-menu" ><i class="fa fa-search"></i></button>
                                      <button type="button" class="btn btn-secondary" onclick="showPasswordCreateModal(1)">+</button>
                                </div>
                                <div class="col-12">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="3%" class="text-center">#</th>
                                                <th width="8%">Website</th>
                                                <th width="10%">Username</th>
                                                <th width="10%">Password</th>
                                                <th width="10%">Registered With</th>
                                                <th width="15%">Remark</th>
                                                <th width="8%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="show-search-password-list">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('passwords.password-gethistory')
@include('passwords.password-previewtask')
@include('passwords.password-sendtowhatsapp')
@include('passwords.password-editmodal')
{{-- @include('partials.modals.password-create-modal') --}}

<script>

    var passHistory = "{{ route('password.history') }}";
    var passGetRemark = "{{route('password.create.get.remark')}}";
    var passwordIndex = "{{ route('password.index') }}";
    var showPasswordEdit = "{{ route('password.show.edit-data') }}";

    $(".btn-password-search-menu").on("click", function(){
        var keyword = $('.password-search-table').val();

        $.ajax({
            url: '{{route('password.search')}}',
            type: 'GET',
            data: {
                subject: keyword,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function (response) {
                $("#loading-image").hide();
                $('.show-search-password-list').html(response);
            },
            error: function () {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });
</script>
<script type="text/javascript" defer src="{{asset('js/custom-passwords.js')}}"></script>
