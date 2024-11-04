@extends('layouts.app')
@section('title', 'Affiliate Marketing')
@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

        .btn-secondary, .btn-secondary:focus, .btn-secondary:hover {
            background: #fff;
            color: #757575;
            border: 1px solid #ddd;
            height: 32px;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: 100;
            line-height: 10px;
        }

        .link-button, .link-button:hover, .link-button:focus {
            text-decoration: none;
            line-height: 1.4;
        }
    </style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                {!! $provider->provider->provider_name !!} Groups (<span
                        id="affiliate_count">{{ $providersGroups->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('affiliate-marketing.provider.index', ['provider_account' => $provider->id])}}">
                    <div class="form-group">
                        <input type="hidden" name="provider_account" value="{!! $provider->id !!}">
                        <div class="row">
                            <div class="col-md-6">
                                <input name="group_name" type="text" class="form-control"
                                       value="{{ request('group_name') }}" placeholder="Search group name">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png" />
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('affiliate-marketing.provider.index', ['provider_account' => $provider->id])}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png" />
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 pl-0 float-right">
                <button data-toggle="modal" data-target="#create-group" type="button"
                        class="float-right mb-3 mr-2 btn-secondary">New Group
                </button>
                <a href="{!! route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $provider->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Affiliates
                </a>
                <a href="{!! route('affiliate-marketing.provider.program.index', ['provider_account' => $provider->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Programs
                </a>
                <a href="{!! route('affiliate-marketing.provider.payments.index', ['provider_account' => $provider->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Payments
                </a>
                <a href="{!! route('affiliate-marketing.provider.commission.index', ['provider_account' => $provider->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Commissions
                </a>
                <a href="{!! route('affiliate-marketing.provider.conversion.index', ['provider_account' => $provider->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Conversions
                </a>
                <a href="{!! route('affiliate-marketing.provider.customer.index', ['provider_account' => $provider->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">View Customers
                </a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="affiliates-table">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>{!! $provider->provider->provider_name !!} group id</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($providersGroups as $key => $providersGroup)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $providersGroup->title }}</td>
                    <td>{{ $providersGroup->affiliate_provider_group_id }}</td>
                    <td>
                        <button type="button" data-toggle="modal" data-target="#create-group"
                                onclick="editData('{!! $providersGroup->id !!}')"
                                class="btn btn-image"><img src="/images/edit.png"></button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $providersGroups->render() !!}
    <div class="modal fade" id="create-group" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Affiliate Group</h2>
                    </div>
                    <form id="add-group-form" method="POST"
                          action="{{route('affiliate-marketing.provider.createGroup', ['provider_account' => $provider->id])}}">
                        @csrf
                        <input type="hidden" id="provider_id" name="affiliate_account_id" value="{!! $provider->id !!}">
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">
                                Title
                                <small style="color:red">*</small>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="group_title" name="title"
                                       placeholder="Title" value="{{ old('title') }}">
                                <span id="group_title_err"
                                      class="text-danger">{{($errors->has('title'))? $errors->first('title'):''}}</span>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                                    aria-label="Close">Close
                            </button>
                            <button type="submit" onclick="return validateForm()" class="float-right custom-button btn">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

    <script type="text/javascript">
      let showPopup;
      @if(Session::get('create_popup'))
        showPopup = true;
      @endif

      if (showPopup) {
        $("#create-group").modal("show");
      }

      $("#create-group").on("show.bs.modal", function() {
        $("#create-group .page-header h2").text("Create Affiliate Group");
        $("#add-group-form").attr("action", "{{ route('affiliate-marketing.provider.createGroup', ['provider_account' => $provider->id]) }}");
        $("#add-group-form button[type=\"submit\"]").text("Create");
      });

      $("#create-group").on("hidden.bs.modal", function() {
        $("#add-group-form").get(0).reset();
      });

      function editData(id) {
        let url = "{{ route('affiliate-marketing.provider.getGroup', [":id", 'provider_account' => $provider->id]) }}";
        url = url.replace(":id", id);
        $.ajax({
          url,
          type: "GET",
          params: { id },
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(response) {
            $("#loading-image").hide();
            if (!response.status) {
              toastr["error"](response.message);
              $("#create-group").modal("hide");
            } else {
              let url = "{{ route('affiliate-marketing.provider.updateGroup', [":id", 'provider_account' => $provider->id]) }}";
              url = url.replace(":id", id);
              $("#add-group-form").attr("action", url);
              $("#add-group-form button[type=\"submit\"]").text("Update");
              $("#create-group .page-header h2").text("Update Affiliate Group");
              $("#add-group-form [name=\"title\"]").val(response.data.title);
              $("#add-group-form [name=\"provider_id\"]").val(response.data.affiliate_account_id);
            }
          }
        });
      }

      function validateForm() {
        let title = $("#group_title").val();
        if (title == "") {
          $("#group_title_err").text("The title field is required.");
          return false;
        }
        return true;
      }
    </script>
@endsection
