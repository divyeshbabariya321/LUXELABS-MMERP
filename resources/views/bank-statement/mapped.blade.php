@extends('layouts.app')
@section('favicon' , 'user-management.png')

@section('title', 'Bank statement')

@section('styles')

<style type="text/css">
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
</style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Bank statements >> List - (File: {{$bankStatementFile->filename}}) (<span id="user_count">{{ $data->total() }}</span>)</h2>
        </div>
        <div class="col-lg-12 margin-tb ml-2 mb-2">
            <a href="{{ route('bank-statement.index') }}" class="btn btn-default">
                {{__('Imported file listing')}}
            </a>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th class="text-center">Bank name</th>
                    <th class="text-center">Bank account</th>
                    <th class="text-center">Account type</th>
                    <th class="text-center">Transaction date</th>
                    <th class="text-center">Transaction reference no</th>
                    <th class="text-center">Debit amount</th>
                    <th class="text-center">Credit amount</th>
                    <th class="text-center">Balance</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Created at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $detail)
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td class="text-center">{{$detail->bank_name}}</td>
                        <td class="text-center">{{$detail->bank_account}}</td>
                        <td class="text-center">{{$detail->account_type}}</td>
                        <td class="text-center">{{$detail->transaction_date}}</td>
                        <td class="text-center">{{$detail->transaction_reference_no}}</td>
                        <td class="text-center">{{$detail->debit_amount}}</td>
                        <td class="text-center">{{$detail->credit_amount}}</td>
                        <td class="text-center">{{$detail->balance}}</td>
                        <td class="text-center">{{$detail->description}}</td>
                        <td class="text-center">{{$detail->created_at}}</td>
                    </tr>    
                @endforeach
            </tbody>
        </table>
        <div class="text-center">
            <div class="text-center">
                {!! $data->links() !!}
            </div>
        </div>
    </div> 
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
    $('.select-multiple').select2({width: '100%'});

    function submitSearch(){
        src = '/users'
        term = $('#term').val()
        id = $('#user-select').val()
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                term : term,
                id : id,

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#users-table tbody").empty().html(data.tbody);
            $("#user_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
        
    }

    function resetSearch(){
        src = '/users'
        blank = ''
        $.ajax({
            url: src,
            dataType: "json",
            data: {
               
               blank : blank, 

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $('#term').val('')
            $('#user-select').val('')
            $("#users-table tbody").empty().html(data.tbody);
            $("#user_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }
    $(".number .whatsapp_number").change(function(e){        
            e.preventDefault();
            $("#loading-image").show();
            $.ajax({
                type:"POST",
                url:"{{ route('user.changewhatsapp') }}",
                data:{
                    "_token": "{{ csrf_token() }}",
                    user_id: $(this).attr('data-user-id'),
                    whatsapp_number:$(this).val()
                },
                success:function(response){
                    $("#loading-image").hide();
                }
            });
        });
</script>

@endsection
