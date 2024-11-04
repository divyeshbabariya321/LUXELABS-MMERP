@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">OTHER EMAIL ACCOUNTS ({{count($accounts)}})</h2>
        </div>
    </div>
    <div class="row">

        <div class="col-md-12 mt-4">
            <form action="{{ action([\App\Http\Controllers\PreAccountController::class, 'store']) }}" method="post">
                @csrf
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>S.N</th>
                        <th>First name</th>
                        <th>Last name</th>
                        <th>Email</th>
                        <th>Password</th>
                    </tr>
                    @foreach($firstName as $key=>$fn)
                        <tr>
                            <th>{{$key+1}}</th>
                            <th><input type="hidden" name="first_name[{{$key}}]" value="{{$fn->name}}">
                                {{ $fn->name }}
                            </th>
                            <th>
                                {{ $lastName[$key]['name'] }}
                                <input type="hidden"name="last_name[{{$key}}]" value="{{ $lastName[$key]['name'] }}">
                            </th>
                            <th><input type="text" name="email[{{$key}}]" placeholder="E-mail" class="form-control"></th>
                            <th><input type="text" name="password[{{$key}}]" placeholder="Password" class="form-control"></th>
                        </tr>
                        @php $key++ @endphp
                    @endforeach
                    @foreach($accounts as $account)
                        <tr>
                            <th>{{$key+1}}</th>
                            <th>{{ $account->first_name }}</th>
                            <th>{{ $account->last_name }}</th>
                            <th>{{ $account->email }}</th>
                            <th>{{ $account->password }}</th>
                        </tr>
                        @php $key++ @endphp
                    @endforeach
                </table>
                <div class="text-center">
                    <button class="btn btn-default">Add Accounts</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <style>
        thead input {
            width: 100%;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // var table = $('#table').dataTable({
            //     // orderCellsTop: true,
            //     fixedHeader: true
            // });
            // $('#table thead tr').clone(true).appendTo( '#table thead' );
            // $('#table thead tr:eq(1) th').each( function (i) {
            //     var title = $(this).text();
            //     $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //
            //     $( 'input', this ).on( 'keyup change', function () {
            //         if ( table.column(i).search() !== this.value ) {
            //             table
            //                 .column(i)
            //                 .search( this.value )
            //                 .draw();
            //         }
            //     } );
            // } );

            // $('#table thead tr').clone(true).appendTo( '#table thead' );
            // $('#table thead tr:eq(1) th').each( function (i) {
            //     var title = $(this).text();
            //     $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //
            //     $( 'input', this ).on( 'keyup change', function () {
            //         if ( table.column(i).search() !== this.value ) {
            //             table
            //                 .column(i)
            //                 .search( this.value )
            //                 .draw();
            //         }
            //     } );
            // } );
            //
            // var table = $('#table').DataTable({
            //     orderCellsTop: true,
            //     fixedHeader: true
            // });
            //
            //
            // $("#table").addClass('table-bordered');
        });
    </script>
    @if (Session::has('message'))
        <script>
            toastr["success"]("{{ Session::get('message') }}", "Message")
        </script>
    @endif
@endsection