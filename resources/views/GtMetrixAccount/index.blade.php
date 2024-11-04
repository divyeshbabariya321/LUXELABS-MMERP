@extends('layouts.app')

@section('title','GT Metrix Accounts')

@section('content')

<style>
    .model-width{
        max-width: 1250px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
         <h2 class="page-heading">GTMetrix Accounts</h2>
    </div>
</div>

<div class="row mt-1 pr-5" style="margin-top: 5rem;">
    <div class="col-lg-12 margin-tb">
        <div class="pull-right">
            <button  class="btn btn-secondary new-plan" data-toggle="modal" data-target="#create_gt_metrix_ac"> Create New Account</button>
        </div>
    </div>
</div>

@include('partials.flash_messages')

<div class="row mt-1 pr-5 pl-5">
    <div class="col-lg-12 margin-tb">
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="panel mt-3 panel-default table-responsive">
                <table class="table table-bordered table-striped site-gtMetrix-account"style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th width="18%">Email</th>
                            <th width="16%">Password</th>
                            <th width="30%">Api Keys</th>
                            <th width="4%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Accounts as $key)
                        
                            <tr>
                                <td>{{ $key->email }}</td>
                                <td>{{ $key->password }}</td>
                                <td class="Website-task" title="{{ $key->account_id }}">{{ $key->account_id }}</td>
                                <td>

                                    <!-- <a href="{{ route('account.show',$key->id) }}" title="show">
                                        <i class="fa fa-eye text-success  fa-lg"></i>
                                    </a> -->

                                    <a href="{{ route('account.edit',$key->id) }}"style="color: #aaaa;">
                                        <i class="fa fa-pencil  fa-lg"></i>
                                    </a>

                                    {{ html()->form('DELETE', route('gtmetrix.account.destroy', [$key->id]))->style('display:inline')->open() }} 
                                    <button type="submit"  style="border: none; background-color:transparent;">
                                        <i class="fa fa-trash fa-lg text-danger"style="color: gray !important;"></i>
                                    </button>
                                    {{ html()->form()->close() }}
                                </td>
        
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $Accounts->links() }}
    </div>
</div>

<div id="account_return_summary_edit" class="modal fade" role="dialog" style="padding: 0 !important;">
    <div class="modal-dialog modal-lg" id="account_return_summary_edit_list">
    </div>
    </div>

<!-- The Modal -->
<div class="modal fade" id="create_gt_metrix_ac" tabindex="-1" role="dialog" aria-labelledby="create_gt_metrix_ac" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="create_gt_metrix_ac">Create New GT Metrix Account</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        {{ html()->form('POST', route('gtmetrix.account.store'))->open() }}
        <div class="modal-body">
            <div class="container-fluid">
                <div class="row subject-field">
                    <div class="row m-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ html()->text('email')->placeholder('Email')->class('form-control') }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ html()->text('password')->placeholder('Password')->class('form-control') }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ html()->text('account_id')->placeholder('Api Key')->class('form-control') }}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                {{ html()->select('status', ["active" => "Active", "error" => "Error", "in-active" => "In-Active"], request('status'))->class('form-control') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Save</button>
        </div>
{{ html()->form()->close() }}
    </div>
  </div>
</div>

@section('scripts')
<script>
$(document).on("click","#btn-account-update-request",function(e) {
            e.preventDefault();
            var form = $(this).closest("form");
            $.ajax({
                url: form.attr("action"),
                method: form.attr("method"),
                data: form.serialize(),
                beforeSend : function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    $("#account_return_summary_edit").modal("hide"); 
                    toastr['success']('Success', 'success');
                },
                error: function(error) {
                    toastr['error']('Something went wrong', 'success');
                    $("#loading-image").hide();
                }
            });
        });


        </script>
@endsection


@include('gtmetrix.setSchedule')
@endsection
