@extends('layouts.app')

@section('title', 'Post man Request')

@section('styles')
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">Big Query ({{count($bigData)}})</h2>
          </div>

          <div class="col-12 mb-3">
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <!-- <a title="add new domain" class="btn btn-secondary add-new-btn">+</a> -->
            </div>
        </div>
    </div>
    <div class="pull-left">
      <form class="form-inline" action="{{route('google.bigdata.search')}}" method="GET">
        <div class="col">
          <div class="form-group">
            <div class="input-group">
                <select name="project_id[]" id="project_id" class="form-control" size="8" multiple="multiple">
                    @foreach($google_project_ids as $project_id)
                        @if(request()->get('project_id') != null)
                            @if(in_array($project_id->google_project_id,request()->get('project_id')))
                                <option value="{{ $project_id->google_project_id }}" selected>{{ $project_id->google_project_id }}</option>
                            @else
                                <option value="{{ $project_id->google_project_id }}">{{ $project_id->google_project_id }}</option>
                            @endif
                        @else
                            <option value="{{ $project_id->google_project_id }}">{{ $project_id->google_project_id }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <div class="input-group">
                <select name="platform[]" id="platform" class="form-control" size="8" multiple="multiple">
                    @foreach($platforms as $platform)
                    @if(request()->get('platform') != null)
                        @if(in_array($platform->platform,request()->get('platform')))
                            <option value="{{ $platform->platform }}" selected>{{ $platform->platform }}</option>
                        @else
                            <option value="{{ $platform->platform }}">{{ $platform->platform }}</option>
                        @endif
                    @else
                            <option value="{{ $platform->platform }}">{{ $platform->platform }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <div class="input-group">
                <select name="event_id[]" id="event_id" class="form-control" size="8" multiple="multiple">
                    @foreach($event_ids as $event)
                        @if(request()->get('event_id') != null)
                            @if(request()->get('event_id') && in_array($event->event_id,request()->get('event_id')))
                                <option value="{{ $event->event_id }}" selected>{{ $event->event_id }}</option>
                            @else
                                <option value="{{ $event->event_id }}">{{ $event->event_id }}</option>
                            @endif
                        @else
                            <option value="{{ $event->event_id }}">{{ $event->event_id }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
          </div>
        </div>
        <div class="col">
          <button type="submit" class="btn btn-image"><img src="{{asset('/images/filter.png')}}"></button>
          <a href="/google/bigData/bigQuery" class="btn btn-image" id=""><img src="{{asset('/images/resend2.png')}}" style="cursor: nwse-resize;"></a>
        </div>
      </form>
      
    </div>
    <div class="col-12">
      <div class="col-md-12">
        <ul class="nav nav-tabs">
            <li><button class="btn btn-xs btn-secondary my-3" style="color:white;" data-toggle="modal" data-target="#columnvisibilityList"> Column Visiblity</button></li>
        </ul>
      </div>
    </div>
	</br>

      @php 

    $columns_array = [
        ['id' => 'id', 'name' => 'Id'],
        ['id' => 'google_project_id', 'name' => 'Google Project Id'],
        ['id' => 'platform', 'name' => 'Platform'],
        ['id' => 'bundle_identifier', 'name' => 'Bundle Identifier'],
        ['id' => 'event_id', 'name' => 'Event Id'],
        ['id' => 'is_fatal', 'name' => 'Is Fatal'],
        ['id' => 'issue_id', 'name' => 'Issue Id'],
        ['id' => 'issue_title', 'name' => 'Issue Title'],
        ['id' => 'issue_subtitle', 'name' => 'Issue Subtitle'],
        ['id' => 'event_timestamp', 'name' => 'Event Timestamp'],
        ['id' => 'received_timestamp', 'name' => 'Received Timestamp'],
        ['id' => 'device', 'name' => 'Device'],
        ['id' => 'memory', 'name' => 'Memory'],
        ['id' => 'storage', 'name' => 'Storage'],
        ['id' => 'operating_system', 'name' => 'Operating System'],
        ['id' => 'application', 'name' => 'Application'],
        ['id' => 'user', 'name' => 'User'],
        // ['id' => 'custom_keys', 'name' => 'Custom Keys'],
        ['id' => 'installation_uuid', 'name' => 'Installation Uuid'],
        ['id' => 'crashlytics_sdk_version', 'name' => 'Crashlytics Sdk Version'],
        ['id' => 'app_orientation', 'name' => 'App Orientation'],
        ['id' => 'device_orientation', 'name' => 'Device Orientation'],
        ['id' => 'process_state', 'name' => 'Process State'],
        // ['id' => 'logs', 'name' => 'Logs'],
        ['id' => 'breadcrumbs', 'name' => 'Breadcrumbs'],
        ['id' => 'blame_frame', 'name' => 'Blame Frame'],
        ['id' => 'exceptions', 'name' => 'Exceptions'],
        ['id' => 'errors', 'name' => 'Errors'],
        ['id' => 'threads', 'name' => 'Threads'],
        ['id' => 'website_id', 'name' => 'Website Id'],
        ['id' => 'created_at', 'name' => 'Created At'],
        // ['id' => 'updated_at', 'name' => 'Updated At'],
    ];
 
      @endphp 
    <div class="infinite-scroll">
	<div class="table-responsive mt-2">
      <table class="table table-bordered">
        <thead>
          <tr>
            @foreach($columns_array as $k=>$v)
              <th class="{{ (!empty($dynamicColumnsToShowb) && in_array($v['name'], $dynamicColumnsToShowb)) ? 'd-none' : ''}}" >{{$v['name']}}</th>
            @endforeach
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
			  @foreach ($bigData as $key => $bigDatar)
            <tr>
              @foreach($columns_array as $k=>$v)
                @php
                  $text_to_show = $bigDatar->{$v['id']};
                  if($v['id'] == 'event_timestamp' || 
                    $v['id'] == 'received_timestamp' ||
                    $v['id'] == 'created_at'){
                    $text_to_show = date('Y-m-d H:i:s', strtotime($bigDatar->{$v['id']}));
                  }
                @endphp

                <td class="{{ (!empty($dynamicColumnsToShowb) && in_array($v['name'], $dynamicColumnsToShowb)) ? 'd-none' : ''}}">{{ $text_to_show }}</td>
              @endforeach
            <td>
              <a class="btn delete-bigData-btn"  data-id="{{ $bigDatar->id }}" href="#"><img  data-id="{{ $bigDatar->id }}" src="{{asset('/images/delete.png')}}" style="cursor: nwse-resize; width: 16px;"></a>
            </td>
            </tr>
            @endforeach
        </tbody>
      </table>
      <div class="text-center">
        {!! $bigData->appends(Request::except('page'))->links() !!}
    </div>
	</div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
   </div>

   @include('google.big_data.partials.google-bigdata-bigquery-column-visibility-modal', ['columns_array' => $columns_array])

@endsection





<div id="view-domain" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
        <div id="view-domain-content">

        </div>
      </div>
    </div>
</div>

@section('scripts')
  <script type="text/javascript">

      $('#project_id').select2({
          placeholder:'Select Project Id'
      });

      $('#platform').select2({
          placeholder:'Select Platform'
      });

      $('#event_id').select2({
          placeholder:'Enter Event ID'
      });
    // $('ul.pagination').hide();
    //   $('.infinite-scroll').jscroll({
    //     autoTrigger: true,
    //     // debug: true,
    //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
    //     padding: 0,
    //     nextSelector: '.pagination li.active + li a',
    //     contentSelector: 'div.infinite-scroll',
    //     callback: function () {
    //       $('ul.pagination').first().remove();
    //       $('ul.pagination').hide();
    //     }
		// });



    $(document).on("click",".delete-bigData-btn",function(e){
        e.preventDefault();
        if (confirm("Are you sure?")) {
          var $this = $(this);
          var id = $this.data('id');
          $.ajax({
            url: "/google/bigData/delete",
            type: "delete",
            headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
            data:{
              id:id
            }
          }).done(function(response) {
            if(response.code = '200') {
              toastr['success'](response.message, 'success');
              location.reload();
            } else {
              toastr['error'](response.message, 'error');
            }
          }).fail(function(errObj) {
            $('#loading-image').hide();
            $("#addPostman").hide();
            toastr['error'](errObj.message, 'error');
          });
          }
      });




  </script>
@endsection
