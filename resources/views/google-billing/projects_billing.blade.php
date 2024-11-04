@extends('layouts.app')
@section('title', 'Google Billing accounts')
@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Billing Accounts</h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-12">
            <a href="{{  }}">
                <button class="btn btn-secondary pull-right ml-2" data-target="#addAccount" data-toggle="modal">Back</button>
            </a>

            <button class="btn btn-secondary pull-right" data-target="#addAccount" data-toggle="modal">+</button>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">

            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="border: 1px solid #ddd;">
                    <thead>
                    <tr>
                        <th style="width:5%;" class="text-center">Sl no</th>
                        <th style="width:15%;" class="text-center">Service</th>
                        <th style="width:15%;" class="text-center">Project Id</th>
                        <th style="width:15%;" class="text-center">Email</th>
                        <th style="width:15%;" class="text-center">Created</th>
                        <th style="width:5%;" class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-center" style="word-wrap: break-word;">
                    @foreach($google_billing_accounts as $key => $google_billing_account)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{ config('constants.GOOGLE_SERVICE_ACCOUNTS.'.$google_billing_account->service_type) }}</td>
                            <td>{{ $google_billing_account->project_id }}</td>
                            <td>{{ $google_billing_account->email }}</td>
                            <td>{{ $google_billing_account->created_at }}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('google.billing.detail', $google_billing_account->id) }}" class="btn btn-sm edit_account"
                                        style="padding:3px;" data-title="View billing info">
                                         <i class="fa fa-file" aria-hidden="true"></i>
                                     </a>
                                    
                                    <a onclick="editData('{{ $google_billing_account->id }}')" class="btn btn-sm edit_account"
                                       style="padding:3px;">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                    </a>

                                    <a href="{{ route('google.billing.delete', $google_billing_account->id) }}" data-id="1"
                                       class="btn btn-delete-template"
                                       onclick="return confirm('Are you sure you want to delete this account ?');"
                                       style="padding:3px;">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!--Add Account Modal -->
        <div class="modal fade" id="addAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Add Google Billing Account</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @include('google-billing._partial.add-google-billing-account')
                </div>
            </div>
        </div>

        <!--Update Account Modal -->
        <div class="modal fade" id="updateAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Google Billing Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @include('google-billing._partial.update-google-billing-account')
                </div>
            </div>
        </div>

    </div>
    <script>
        function editData(id) {
            let url = "{{ route('google.billing.get', [":id"]) }}";
            url = url.replace(':id', id);
            $.ajax({
                url,
                type: 'GET',
                success: function (response) {
                    if (!response.status) {
                        toastr["error"](response.message);
                        $('#updateAccount').modal('hide');
                    } else {
                        $('#updateAccount').modal('show');
                        $('#updateAccount-group-form [name="id"]').val(id);
                        $('#updateAccount-group-form [name="edit_project_id"]').val(response.data.project_id);
                        $('#updateAccount-group-form [name="edit_service_type"]').val(response.data.service_type);
                        $('#updateAccount-group-form [name="edit_email"]').val(response.data.email);
                        if(response.data.default_selected) {
                            $('#updateAccount-group-form [name="default_account"]').prop( "checked", true );
                        } else {
                            $('#updateAccount-group-form [name="default_account"]').prop( "checked", false );
                        }
                    }
                }
            })
        }
    </script>
@endsection
