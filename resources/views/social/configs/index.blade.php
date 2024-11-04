@php
$dynamic_columns_arr = ['Website', 'Platform', 'Name', 'UserName', 'Phone Number', 'Email', 'Password', 'API key', 'API Secret', 'Page ID', 'Account ID', 'Page Language', 'Ad Account', 'Status','Started At','Actions'];
$section_name = 'development-section-social-config';
@endphp
@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="{{config('app.url')}}/images/pre-loader.gif" style="display:none;" alt="" />
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h2 class="page-heading"> Configs
                  <button type="button" class="btn custom-button float-right ml-10" data-toggle="modal" data-target="#globalDatatableColumnVisibilityList">Column Visiblity</button>
                </h2>
                <div class="col-lg-12">
                    <form action="{{route('social.config.index')}}" method="GET" class="form-inline align-items-start">
                        <div class="row mr-3 mb-3">
                            <div class="form-group">
                                <select id="store_website_id" class="form-control store_website_id"
                                        name="store_website_id[]" multiple>
                                    @foreach ($websites as $id => $website)
                                        <option value="{{ $website->id }}" {{ in_array($website->id,$selected_website?? []) ? 'selected' : '' }}>{{ $website->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ml-3">
                                <select id="user_name" class="form-control user_name" name="user_name[]" multiple>
                                    @foreach ($user_names as $id => $user_name)
                                        <option value="{{ $user_name->user_name }}" {{ in_array($user_name->user_name,$selected_user_name?? []) ? 'selected' : '' }}>{{ $user_name->user_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ml-3">
                                <select id="platform" class="form-control platform" name="platform[]" multiple>
                                    @foreach ($platforms as $id => $platform)
                                        <option value="{{ $platform->platform }}" {{ in_array($platform->platform,$selected_platform?? []) ? 'selected' : '' }}>{{ $platform->platform }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ml-3">
                              <select id="ad_account" class="form-control ad_account" name="ad_account[]" multiple>
                                  @foreach ($ad_accounts as $id => $ad_account)
                                      <option value="{{ $ad_account['id'] }}" {{ in_array($ad_account['id'],$selected_ad_account?? []) ? 'selected' : '' }}>{{ $ad_account['name'] }}</option>
                                  @endforeach
                              </select>
                            </div>
                            <button type="submit" class="btn btn-image"><img src="{{asset('images/filter.png')}}" />
                            </button>
                        </div>
                    </form>
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary" data-toggle="modal"
                            data-target="#ConfigCreateModal"><i class="fa fa-plus"></i>&nbsp;Add FB/Insta Account
                    </button>
                    <button type="button" class="btn btn-secondary" data-toggle="modal"
                            data-target="#AdConfigCreateModal"><i class="fa fa-plus"></i>&nbsp;Add Ad Account
                    </button>
                </div>
            </div>
        </div>

        @include('partials.flash_messages')
        @include("social.header_menu")
        <div class="table-responsive mt-3">
            <table class="table table-bordered" id="passwords-table">
                <thead>
                <tr>
                  @if(!empty($dynamicColumnsToShow))
                    @if (!in_array('Website', $dynamicColumnsToShow))
                      <th width="5%">Website</th>
                    @endif
                    @if (!in_array('Platform', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">Platform</th>
                    @endif
                    @if (!in_array('Name', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">Name</th>
                    @endif
                    @if (!in_array('UserName', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">User Name</th>
                    @endif
                    @if (!in_array('Phone Number', $dynamicColumnsToShow))
                      <th style="width: 5% !important">Phone Number</th>
                    @endif
                    @if (!in_array('Email', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">Email</th>
                    @endif
                    @if (!in_array('Password', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">Password</th>
                    @endif
                    @if (!in_array('API key', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">API key</th>
                    @endif
                    @if (!in_array('API Secret', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">API Secret</th>
                    @endif
                    @if (!in_array('Page ID', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">Page ID</th>
                    @endif
                    @if (!in_array('Account ID', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">Account ID</th>
                    @endif
                    @if (!in_array('Page Language', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">Page Language</th>
                    @endif
                    @if (!in_array('Ad Account', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">Ad Account</th>
                    @endif
                    @if (!in_array('Status', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">Status</th>
                    @endif
                    @if (!in_array('Started At', $dynamicColumnsToShow))
                      <th style="width: 5% !important;">Started At</th>
                    @endif
                    @if (!in_array('Actions', $dynamicColumnsToShow))
                      <th style="width: 10% !important;">Actions</th>
                    @endif
                  @else
                    <th width="5%">Website</th>
                    <th style="width: 5% !important;">Platform</th>
                    <th style="width: 5% !important;">Name</th>
                    <th style="width: 5% !important;">User Name</th>
                    <th style="width: 5% !important">Phone Number</th>
                    <th style="width: 5% !important;">Email</th>
                    <th style="width: 5% !important;">Password</th>
                    <th style="width: 5% !important;">API key</th>
                    <th style="width: 5% !important;">API Secret</th>
                    <th style="width: 5% !important;">Page ID</th>
                    <th style="width: 5% !important;">Account ID</th>
                    <th style="width: 5% !important;">Page Language</th>
                    <th style="width: 5% !important;">Ad Account</th>
                    <th style="width: 5% !important;">Status</th>
                    <th style="width: 5% !important;">Started At</th>
                    <th style="width: 10% !important;">Actions</th>
                  @endif
                </tr>
                </thead>

                <tbody>

                @include('social.configs.partials.data')
                {!! $socialConfigs->render() !!}
                </tbody>
            </table>
        </div>
    </div>
    @include('social.configs.partials.add-modal')
    @include('social.configs.partials.add-adaccount-modal')
    @include('development.partials.column-visibility-global-modal')
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
      $("#token").focusout(function() {
        let token = $("#token").val();
        if (!token) {
          alert("please enter token first");
        }
        src = "{{ route('social.config.adsmanager') }}";

        $.ajax({
          url: '{{route("social.config.adsmanager")}}',
          dataType: "json",
          data: {
            token: token
          },
          success: function(result) {
            if (result) {
              $("#loading-image").hide();
              let html = `<option value="">-----Select Adsets-----</option>`;
              if (result) {
                console.log("come toadsets adsets ");
                console.log(result);
                $.each(result, function(key, value) {
                  html += `<option value="${value.id}" rel="${value.name}" >${value.name}</option>`;
                });
              }
              $("#adset_id").html(html);

            } else {
              $("#loading-image").hide();
              alert("token Expired");
            }
          },
          error: function(exx) {

          }
        });

      });

      $(document).ready(function() {
        $(".select-multiple").multiselect();
        $(".select-multiple2").select2();
        $(".store_website_id").select2({
          placeholder: "Select Store Website"
        });
        $(".user_name").select2({
          placeholder: "Select User Name"
        });
        $(".platform").select2({
          placeholder: "Select Platform"
        });
        $(".ad_account").select2({
          placeholder: "Select Ad Account"
        });
      });


      $("#filter-date").datetimepicker({
        format: "YYYY-MM-DD"
      });

      $("#filter-whats-date").datetimepicker({
        format: "YYYY-MM-DD"
      });

      function changesocialConfig(config) {
        $("#ConfigEditModal" + config.id + "").modal("show");

        let token = $("#edit_token").val();

        if (!token) {
          alert("please enter token first");
        }
        src = "{{ route('social.config.adsmanager') }}";
        $.ajax({
          url: '{{route("social.config.adsmanager")}}',
          dataType: "json",
          data: {
            token: token
          },
          success: function(result) {
            //console.log(result);
            if (result) {
              $("#loading-image").hide();
              let htmledit = `<option value="">-----Select Ad-Manager-Account-----</option>`;
              if (result) {
                console.log("come toadsets adsets ");
                console.log(result);
                $.each(result, function(key, value) {
                  console.log("-----------dieedit", value.name);
                  if (config.ads_manager) {
                    if (value.id == config.ads_manager) {
                      htmledit += `<option value="${value.id}" selected>${value.name}</option>`;
                    } else {
                      htmledit += `<option value="${value.id}" rel="${value.name}" >${value.name}</option>`;
                    }

                  } else {
                    htmledit += `<option value="${value.id}" rel="${value.name}" >${value.name}</option>`;
                  }


                });
                $(".adsmanager").html(htmledit);
              }


            } else {
              $("#loading-image").hide();
              alert("token Expired");
            }
          },
          error: function(exx) {

          }
        });

      }

      function deleteConfig(config_id) {
        event.preventDefault();
        if (confirm("Are you sure?")) {
          $.ajax({
            type: "POST",
            url: "{{ route('social.config.delete') }}",
            data: { "_token": "{{ csrf_token() }}", "id": config_id },
            dataType: "json",
            success: function(message) {
              alert("Deleted Config");
              location.reload(true);
            }, error: function() {
              alert("Something went wrong");
            }

          });
        }
        return false;

      }

      $(document).ready(function() {
        src = "{{ route('social.config.index') }}";
        $(".search").autocomplete({
          source: function(request, response) {
            // number = $('#number').val();
            // username = $('#username').val();
            // provider = $('#provider').val();
            // customer_support = $('#customer_support').val();


            $.ajax({
              url: src,
              dataType: "json",
              data: {
                // number : number,
                // username : username,
                // provider : provider,
                // customer_support : customer_support,

              },
              beforeSend: function() {
                $("#loading-image").show();
              }

            }).done(function(data) {
              $("#loading-image").hide();
              console.log(data);
              $("#passwords-table tbody").empty().html(data.tbody);
              if (data.links.length > 10) {
                $("ul.pagination").replaceWith(data.links);
              } else {
                $("ul.pagination").replaceWith("<ul class=\"pagination\"></ul>");
              }

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
              alert("No response from server");
            });
          },
          minLength: 1

        });
      });
    </script>
@endsection
