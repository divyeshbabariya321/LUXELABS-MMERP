@extends('layouts.app')

@section('title', 'Google Server List')
@section('styles')
<style>
.slider:before {
  left: 8px !important;
}
</style>
@endsection
@section('content')
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;"></div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Translation Settings List</h2>
            <div class="pull-left">
                <div class="row">
                    <div class="col-md-4">
                        <input name="term" type="text" class="form-control" value="" placeholder="Search " id="term">
                    </div>
                    <div class="col-md-2">
                      <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png" style="cursor: default; width: 15px;"></button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png"></button>    
                    </div>
                </div>
            </div>
            <div class="pull-right">
              <label class="switchAN mr-4" style="width: 60px !important;">
                <input type="checkbox" id="translationToggle" @if($is_free==0)
                {{'checked'}}
                @endif >
                <span class="slider round"></span>
                <span class="text @if($is_free==0) {{'textLeft'}} @else {{'textRight'}} @endif" id="translationText">@if($is_free==0)
                    {{'Free'}}
                    @else
                    {{'Paid'}}
                    @endif</span>
              </label>
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#addGooleSetting"><i class="fa fa-plus"></i></button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')
    <div id="ErrorLogModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Translation Error Log Detail</h4>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover" style="table-layout:fixed;">
                <thead>
                  <th>Error Code</th>
                  <th>Project Id</th>
                  <th>Message </th>
                  <th>Error code</th>
                  <th>Domain</th>
                  <th>Reason</th>
                  <th>Created at</th>
                  <th>Updated at</th>
                  <th>Action</th>
                </thead>
                <tbody class="error-log-data">
    
                </tbody>
              </table>
    
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
    <div id="addGooleSetting" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content ">
                <form class="add_translation_language" action="{{ route('google-traslation-settings.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Add Google Translation Setting</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                         <!-- email , account_json , status, last_note , created_at -->
                        <div class="form-group">
                            <strong>Project ID:</strong>
                            <input type="text" name="project_id" class="form-control" value="{{ old('project_id') }}">

                            @if ($errors->has('project_id'))
                            <div class="alert alert-danger">{{$errors->first('project_id')}}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Email:</strong>
                            <input type="text" name="email" class="form-control" value="{{ old('email') }}">

                            @if ($errors->has('email'))
                            <div class="alert alert-danger">{{$errors->first('email')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Account JSON:</strong>
                            <textarea class="form-control" name="account_json" value="{{ old('account_json') }}" required>{{ old('account_json') }}</textarea>
                            
                            @if ($errors->has('account_json'))
                            <div class="alert alert-danger">{{$errors->first('account_json')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Status:</strong>
                            <input type="text" name="status" class="form-control" value="{{ old('status') }}" required>

                            @if ($errors->has('status'))
                            <div class="alert alert-danger">{{$errors->first('status')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Last Note:</strong>
                            <input type="text" name="last_note" class="form-control" value="{{ old('last_note') }}" required>

                            @if ($errors->has('last_note'))
                            <div class="alert alert-danger">{{$errors->first('last_note')}}</div>
                            @endif
                        </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Store</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="mt-3 col-md-12">
      <table class="table table-bordered table-striped" id="google-traslation-settings-table">
        <thead>
          <tr>
            <th width="5%">ID</th>
            <th width="10%">Project ID</th>
            <th width="10%">Email</th>
            <th width="30%">Account JSON</th>
            <th width="5%">status</th>
            <th width="5%">Last Note</th>
            <th width="10%">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($settings as $setting)
            <tr>
              <td>{{ $setting->id }}</td>
              <td>{{ $setting->project_id ?? "-" }}</td>
              <td>
                {{ $setting->email }}
              </td>
              <td>
                {!! ($setting->account_json )?"<span class='lesstext'>".(\Illuminate\Support\Str::limit($setting->account_json , 10, '<a href="javascript:void(0)" class="readmore btn btn-xs text-dark">...<i class="fa fa-plus" aria-hidden="true"></i></a>'))."</span>":"-" !!}
                {!! ($setting->account_json )?"<span class='alltext' style='display:none;'>".$setting->account_json ."<a href='javascript:void(0)' class='readless btn btn-xs text-dark'>...<i class='fa fa-minus' aria-hidden='true'></i></a></span>":"-" !!}
                <?php if(!empty($setting->account_json)){ ?>
                  <button class="btn btn-xs text-dark" onclick="copyDataToClipBoard('<?php echo $setting->account_json; ?>')"><i class="fa fa-copy"></i></button>
                <?php } ?>
              </td>
              <td>
                @if ($setting->status == 1)
                    Enable
                @else
                    Disabled
                @endif
              </td>
              <td>
                {{ $setting->last_note }}
              </td>
              <td>
                <a href="{{ route('google-traslation-settings.edit',$setting->id) }}" class="btn btn-xs text-dark pull-left"><i class="fa fa-edit"></i></a>
                <form action="{{ route('google-traslation-settings.destroy', $setting->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                    <input type="hidden" name="setting" value="{{ $setting->id }}">
                    @method('DELETE')
                    @csrf
                    <button type="submit" class="btn btn-xs text-dark pull-left"><i class="fa fa-trash"></i></button>
                </form>
                <button class="btn btn-xs btn-none-border show_error_logs" data-id="{{ $setting->id}}"><i class="fa fa-eye"></i></button>
                
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
@endsection

@section('scripts')
<script>

    // submit filter button handler
    function submitSearch(){
      src = "{{url('google-traslation-settings')}}"
      let term = $('#term').val()
      $.ajax({
          url: src,
          method: "GET",
          dataType: "json",
          data: {
              term : term
          },
          beforeSend: function () {
              $("#loading-image").show();
          },

      }).done(function (data) {
          $("#loading-image").hide();
          $("#google-traslation-settings-table tbody").empty().html(data.tbody);
      }).fail(function (jqXHR, ajaxOptions, thrownError) {
          alert('No response from server');
      });
      
    }


    // reset filter button handler
    function resetSearch(){
        src = "{{url('google-traslation-settings')}}"
        blank = ''
        $.ajax({
            url: src,
            dataType: "json",
            data: {},
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $('#term').val('')
            // $('#affiliate-select').val('')
            $("#google-traslation-settings-table tbody").empty().html(data.tbody);
            // $("#affiliate_count").text(data.count);
            // if (data.links.length > 10) {
            //     $('ul.pagination').replaceWith(data.links);
            // } else {
            //     $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            // }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }


    $(document).on('click', '.readmore', function() {
        $(this).parent('.lesstext').hide();
        $(this).parent('.lesstext').next('.alltext').show();
    });
    $(document).on('click', '.readless', function() {
        $(this).parent('.alltext').hide();
        $(this).parent('.alltext').prev('.lesstext').show();
    });
    function copyDataToClipBoard(data) {
      navigator.clipboard.writeText(data);
    }
    $(document).ready(function () {
          
      $(document).on("click", ".show_error_logs", function() {
          var id = $(this).data('id');
          $.ajax({
                method: "GET",
                url: "{{ route('translation.log') }}" ,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "account_id" : id,
                },
                dataType: 'html'
              }).done(function(result) {
                $('#ErrorLogModal').modal('show');
                result = JSON.parse(result);
                $('.error-log-data').html(result.tbody);
          });

      });

      $(document).on("change", '.mark-as-resolve', function () {
          if($(this).is(":checked")){
            $.ajax({
              method: "POST",
              url: "{{ route('translation.log.markasresolve') }}" ,
              data: {
                  "_token": "{{ csrf_token() }}",
                  "id" : $(this).val(),
              },
            }).done(function(result) { 
              toastr['success']("Marked as resolved", 'Success');
            });
          }
      });
    });
</script>
@endsection
