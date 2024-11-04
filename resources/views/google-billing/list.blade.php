@extends('layouts.app')
@section('title', 'Google Billing Projects Detail')
@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Billing Projects Detail</h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-12">
            <div style="float:right">
                <a href="{{ route('google.billing.index') }}"><button class="btn btn-secondary accounts" >Back</button></a>

                <button class="btn btn-secondary ml-2" data-target="#addProject" data-toggle="modal">+</button>
            </div>
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
                        <th style="width:15%;" class="text-center">Dataset Id</th>
                        <th style="width:15%;" class="text-center">Table Id</th>
                        <th style="width:15%;" class="text-center">Billing Amount</th>
                        <th style="width:5%;" class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-center" style="word-wrap: break-word;">
                    @foreach($googleBillingProjects as $key => $googleBillingProject)
                        <tr>
                            <td>{{ $loop->index+1 }}</td>
                            <td>{{ config('constants.GOOGLE_SERVICE_ACCOUNTS.'.$googleBillingProject['service_type']) }}</td>
                            <td>{{ $googleBillingProject['project_id'] }}</td>
                            <td>{{ $googleBillingProject['dataset_id'] }}</td>
                            <td>{{ $googleBillingProject['table_id'] }}</td>
                            <td>
                                @if (!empty($googleBillingProject['billing_detail']))
                                    @foreach ($googleBillingProject['billing_detail'] as $row)
                                        {{ number_format($row['total_cost'], 2) }}
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                   
                                    <a onclick="editProject('{{ $googleBillingProject->id }}')" class="btn btn-sm edit_project"
                                       style="padding:3px;">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                    </a>

                                    <a href="{{ route('google.billing.delete', $googleBillingProject->id) }}" data-id="1"
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
        {{ $googleBillingProjects->links()->withPath('/google-billing/project-list') }}
        <!--Add Project Modal -->
        <div class="modal fade" id="addProject" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Add Google Billing Project</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @include('google-billing._partial.add-google-billing-project')
                </div>
            </div>
        </div>

        <!--Update Project Modal -->
        <div class="modal fade" id="updateProject" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Google Billing Project</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @include('google-billing._partial.update-google-billing-project')
                </div>
            </div>
        </div>
    </div>
    <script>
        function editProject(id) {
            let url = "{{ route('google.billing.get.project', [":id"]) }}";
            url = url.replace(':id', id);
            $.ajax({
                url,
                type: 'GET',
                success: function (response) {
                    if (!response.status) {
                        toastr["error"](response.message);
                        $('#updateProject').modal('hide');
                    } else {
                        console.log(response.data);
                        $('#updateProject').modal('show');
                        $('#updateProject-group-form [name="id"]').val(id);
                        $('#updateProject-group-form [name="edit_project_id"]').val(response.data.project_id);
                        $('#updateProject-group-form [name="edit_google_billing_master_id"]').val(response.data.google_billing_master_id);
                        $('#updateProject-group-form [name="edit_service_type"]').val(response.data.service_type);
                        $('#updateProject-group-form [name="edit_dataset_id"]').val(response.data.dataset_id);
                        $('#updateProject-group-form [name="edit_table_id"]').val(response.data.table_id);
                    }
                }
            })
        }
    </script>
@endsection
