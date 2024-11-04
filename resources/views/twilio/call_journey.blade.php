@extends('layouts.app')

@section('styles')
    {{-- <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection
@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Twilio Call Journey</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-body">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>WebSite</th>
                                <th>Phone</th>
                                <th>Store Id</th>
                                <th>Account Sid</th>
                                <th>Call Sid</th>
                                <th>Call Entered</th>
                                <th>Handled by chatbot</th>
                                <th>Called in working hours</th>
                                <th>Agent Available</th>
                                <th>Agent online</th>
                                <th>Call Answered</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th><input type="text" class="search form-control tbInput" name="customer_name"
                                        id="customer_name"></th>
                                <th><input type="text" class="search form-control tbInput" name="website" id="website"></th>
                                <th width="10%"><input type="text" class="search form-control tbInput" name="phone"
                                        id="phone"></th>
                                <th width="5%"><input type="text" class="search form-control tbInput" name="store_id"
                                        id="store_id"></th>
                                <th><input type="text" class="search form-control tbInput" name="account_id"
                                    id="account_id"></th>
                                <th><input type="text" class="search form-control tbInput" name="call_id"
                                    id="call_id"></th>
                                <th><input type="text" class="search form-control tbInput" name="call_entered"
                                    id="call_entered"></th>
                                <th><input type="text" class="search form-control tbInput" name="handled_by_chatbot"
                                    id="handled_by_chatbot"></th>
                                <th><input type="text" class="search form-control tbInput" name="called_working_hours"
                                    id="called_working_hours"></th>
                                <th><input type="text" class="search form-control tbInput" name="avaiable_agent"
                                    id="avaiable_agent"></th>
                                <th><input type="text" class="search form-control tbInput" name="agent_online"
                                    id="agent_online"></th>
                                <th><input type="text" class="search form-control tbInput" name="call_answered"
                                    id="call_answered"></th>
                            </tr>

                            <tbody id="content_data" class="tableLazy">
                                @include('twilio.partials.call_journey_data')
                            </tbody>

                        </table>
                        {{ $call_Journeies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
                                                  50% 50% no-repeat;display:none;"></div>
@endsection


@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
        //Ajax Request For Search
        $(document).ready(function() {


            $(document).on('keyup', '.tbInput', function() {
                filterResults();
            })


            function filterResults() {
                $('#noresult_tr').remove();
                var row = getFilterValues();

                $.ajax({
                    url: '{{ route('twilio.call_journey') }}',
                    dataType: "json",
                    data: row,
                    method: 'post',
                    beforeSend: function() {
                        $("#loading-image").show();
                    },

                }).done(function(res) {
                    $("#loading-image").hide();
                    $('#noresult_tr').remove();


                    if (res.status) {
                        $('.tableLazy').html(res.html);
                        $(".page-total").html(res.count);
                    } else
                        $('.tableLazy').html(res.html)
                })
            }

            function getFilterValues() {
                var row = {};
                $('.tbInput').each(function() {
                    var name = $(this).attr('name');
                    row[name] = $(this).val();
                })

                row['created_at'] = $('[name="created_at"]').val();
                row['_token'] = '{{ csrf_token() }}';
                return row;
            }

        });
    </script>
@endsection
