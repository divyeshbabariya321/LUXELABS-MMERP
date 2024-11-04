@extends('layouts.app')


@section('title', 'Store Website Analytics Index')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
@section('content')
<div class="row mb-5">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Store Website Analytics List</h2>

        <div class="pull-left">
            <input id="filter" type="text" class="form-control" placeholder="Type here...">
        </div>

        <div class="pull-right">
            <button type="button" class="float-right btn btn-secondary btn mb-3 mr-3" data-toggle="modal" data-target="#websiteanalyticsmodel">+</button>
        </div>
    </div>
</div>

@include('partials.flash_messages')

<div class="table-responsive">
    <table class="table table-bordered" id="store_website-analytics-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Website</th>
                <th>Email</th>
                <th>Account Id</th>
                <th>View Id</th>
                <th>Store Website</th>
                <th width="280px">Action</th>
            </tr>
        </thead>
        <tbody class="searchable">
            @foreach($storeWebsiteAnalyticsData as $key => $record)
            @php
            $count = $key + 1;
            @endphp
            <tr>
                <td>{{$count}}</td>
                <td>{{$record->website ?? '--'}}</td>
                <td>{{$record->email}}</td>
                <td>{{$record->account_id}}</td>
                <td>{{$record->view_id}}</td>
                <td>{{$record->storeWebsiteDetails->title ?? $record->storeWebsiteDetails->website ?? '--' }}</td>
                <td>
                    <a href="{{url('/store-website-analytics/edit/'.$record->id)}}" class="btn btn-xs btn-image" title="Edit Record"><img src="/images/edit.png" ></a>
                    <a href="{{url('/store-website-analytics/delete/'.$record->id)}}" class="btn btn-image" title="Delete Record"><img src="/images/delete.png"></a>
                    <a href="javascript:;" class="btn btn-image find-records" data-id="{{$record->id}}" title="Check Report"><img src="/images/archive.png"></a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade bd-report-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">

       </div>
    </div>
  </div>
</div>

<div class="modal fade" id="websiteanalyticsmodel" role="dialog" style="z-index: 3000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create storeWebsites </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="{{ url('/store-website-analytics/create') }}" class="mb-0" enctype="multipart/form-data">
                    @csrf
                        <div class="col-12">
                    <div class="form-group">
                        <label for="website" class="col-form-label">{{ __('Website') }}</label>
                            <input id="website" name="website" placeholder="Website" type="text" class="form-control {{ $errors->has('website') ? ' is-invalid' : '' }}" value="{{old('website')}}" required="required" autofocus>
                            @if ($errors->has('website'))
                                <span class="invalid-feedback">
                      <strong>{{ $errors->first('website') }}</strong>
                    </span>
                            @endif
                        </div>
                    </div>
                        <div class="col-12">
                    <div class="form-group">
                        <label for="account_id" class="col-form-label">{{ __('Account Id') }}</label>
                            <input id="account_id" name="account_id" placeholder="Account Id" type="text" class="form-control {{ $errors->has('account_id') ? ' is-invalid' : '' }}" value="{{old('account_id')}}" required="required" autofocus>
                            @if ($errors->has('account_id'))
                                <span class="invalid-feedback">
                    <strong>{{ $errors->first('account_id') }}</strong>
                  </span>
                            @endif
                        </div>
                    </div>
                        <div class="col-12">
                    <div class="form-group">
                        <label for="email" class="col-form-label">{{ __('Email') }}</label>
                            <input id="email" name="email" placeholder="Email" type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{old('email')}}" required="required" autofocus>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback">
                    <strong>{{ $errors->first('email') }}</strong>
                  </span>
                            @endif
                        </div>
                    </div>
                        <div class="col-12">
                    <div class="form-group">
                        <label for="view_id" class="col-form-label">{{ __('View Id') }}</label>
                            <input id="view_id" name="view_id" placeholder="View Id" type="text" class="form-control {{ $errors->has('view_id') ? ' is-invalid' : '' }}" value="{{old('view_id')}}" autofocus>
                            @if ($errors->has('view_id'))
                                <span class="invalid-feedback">
                      <strong>{{ $errors->first('view_id') }}</strong>
                    </span>
                            @endif
                        </div>
                    </div>
                        <div class="col-12">
                    <div class="form-group">
                        <label for="select" class="col-form-label">Store Website Id</label>
                            <select id="select" name="store_website_id" class="form-control">
                                @foreach($storeWebsites as $website)
                                    @php
                                        $selected = '';
                                      if(old('store_website_id') == $website->id){
                                        $selected = 'selected';
                                      }
                                    @endphp
                                    <option value="{{$website->id}}" {{$selected}}>{{$website->id .' - '. $website->website}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                        <div class="col-12">
                    <div class="form-group">
                        <label for="view_id" class="col-form-label">{{ __('google service account json') }}</label>
                            <input id="google_service_account_json" name="google_service_account_json" placeholder="Google Service Account Json" type="file" class="{{ $errors->has('google_service_account_json') ? ' is-invalid' : '' }}" value="{{old('google_service_account_json')}}" required="required" autofocus>
                            @if ($errors->has('google_service_account_json'))
                                <span class="invalid-feedback">
                      <strong>{{ $errors->first('google_service_account_json') }}</strong>
                      </span>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                    <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    </div>
                    </div>
                </form>
        </div>
    </div>
</div>

@endsection

<script>
$(document).ready(function () {
    (function ($) {
        $('#filter').keyup(function () {
            var rex = new RegExp($(this).val(), 'i');
            $('.searchable tr').hide();
            $('.searchable tr').filter(function () {
                return rex.test($(this).text());
            }).show();
        })

        $(document).on("click",".find-records",function(e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: "/store-website-analytics/report/"+id,
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function (data) {
                $("#loading-image").hide();
                $(".bd-report-modal-lg .modal-body").empty().html(data);
                $(".bd-report-modal-lg").modal("show");
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        });

    }(jQuery));
});
</script>
