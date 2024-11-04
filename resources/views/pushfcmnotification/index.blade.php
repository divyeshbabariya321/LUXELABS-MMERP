@extends('layouts.app')
@section('favicon' , 'fcmnotification-management.png')

@section('title', 'FCM Notifications')

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
            <h2 class="page-heading">FCM Notification Listing (<span id="fcmnotification_count">{{ $data->total() }}</span>)</h2>
            <div class="pull-left">
                <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <input name="term" type="text" class="form-control"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Search fcmnotification Program" id="term">
                            </div>
                            <div class="col-md-2">
                               <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png"/></button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png"/></button>    
                            </div>
                        </div>
                    </div>
            </div>
            <div class="pull-right">
                <button  class="btn btn-secondary new-notification" data-toggle="modal" data-target="#create-notification">+</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="fcmnotifications-table">
              <thead>
            <tr>
                <th>No</th>
                <th>Notification Title</th>
                <th>Url</th>
                <th>Text</th>
                <th>Sent At</th>
                <th>Sent On</th>
                <th>Expired Day</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
                @include('pushfcmnotification.partials.list-notification')
            </tbody>
        </table>
    </div>

    {!! $data->render() !!}


    <div id="show-content-model-table" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                       
                    </div>
                </div>
            </div>
      </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
               50% 50% no-repeat;display:none;">
    </div>

    <div id="create-notification" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create Notification</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    {{ html()->form('POST', route('pushfcmnotification.store'))->open() }}
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row subject-field">
                                <div class="row m-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <strong>Notification Title:</strong>
                                            {{ html()->text('title')->placeholder('Name')->class('form-control') }}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <strong>Notification Website:</strong>
                                            <select name="url" class="form-control">
                                                <option value="">Select Website</option>
                                                @foreach($StoreWebsite as $website)
                                                <option value="{{$website->website}}" {{ ($website->website == old('url'))?'selected':''}}>{{$website->website}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <strong>Notification body:</strong>
                                            {{ html()->textarea('body')->placeholder('body')->class('form-control') }}
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <strong>Sent At:</strong>
                                            {{ html()->text('sent_at')->placeholder('time to send notification at')->class('form-control')->id('sent_at_fcm_create') }}
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <strong>Expired Day:</strong>
                                            {{ html()->number('expired_day')->attribute('placeholder', 'Expired day in number')->class('form-control')->attribute('min', 0) }}
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


@endsection

@section('scripts')
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
    $('.select-multiple').select2({width: '100%'});

    function submitSearch(){
        src = "{{route('pushfcmnotification.list')}}"
        term = $('#term').val()
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                term : term,
            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#fcmnotifications-table tbody").empty().html(data.tbody);
            $("#fcmnotification_count").text(data.count);
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
        src = "{{route('pushfcmnotification.list')}}"
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
            $('#fcmnotification-select').val('')
            $("#fcmnotifications-table tbody").empty().html(data.tbody);
            $("#fcmnotification_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }
    $(document).on('click','.readmore',function(){
    	$(this).parent('.lesstext').hide();
    	$(this).parent('.lesstext').next('.alltext').show();

    });
    $(document).on('click','.readless',function(){
    	$(this).parent('.alltext').hide();
    	$(this).parent('.alltext').prev('.lesstext').show();

    });

    $(document).on("click",".fcm-notification-list",function (e){
        e.preventDefault();
        var id = $(this).data("id");
        $.ajax({
            url: '/pushfcmnotification/error-list',
            type: 'GET',
            data: {id: id},
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            var model  = $("#show-content-model-table");
            model.find(".modal-title").html("Error notification log");
            model.find(".modal-body").html(response);
            model.modal("show");
        }).fail(function() {
            $("#loading-image").hide();
            alert('Please check laravel log for more information')
        });
    });

    $(function () {
        $('#sent_at_fcm_create').datetimepicker({
            format: 'Y-MM-DD HH:mm',
            stepping: 5
        });
    });

</script>

@endsection
