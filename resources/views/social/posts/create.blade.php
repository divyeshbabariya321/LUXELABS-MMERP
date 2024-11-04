@extends('layouts.app')

@section('title', __('Posts'))
<style>
    .carousel-inner.maincarousel img {
        margin-top: 20px;
    }

    form#create-form {
        box-shadow: 0 0 20px 1px rgb(0 0 0 / 10%);
        padding: 15px;
        margin: 30px;
    }
</style>
@section('content')
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <form id="create-form" action="{{ route('social.post.store') }}" method="post"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="configid" name="config_id" value="{{$id->id}}" />

                <h2 class="page-heading">Create Page Posts<span class="count-text"></span></h2>

                <div class="modal-body">
                    <label>Picture from content management</label>
                    <div class="form-group">
                        <a class="btn btn-secondary btn-sm mr-3 openmodalphoto" title="attach media from all content">
                            <i class="fa fa-paperclip"></i>
                        </a>
                    </div>
                    <div id="contextimage" class="form-group"></div>
                    <label>Except this page you can choose same website other pages</label>
                    <select class="form-control input-sm select-multiple" name="webpage[]" multiple>
                        @if($socialWebsiteAccount)
                            @foreach ($socialWebsiteAccount as $website)
                                @if($website['id'] != $id->id)
                                    <option value="{{ $website['id'] }}">{{ $website['name'] }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <div class="form-group"></div>
                    <div class="form-group">
                        <label>Picture <small class="text-danger">* You can select multiple images only </small></label>
                        <input type="file" multiple="multiple" name="source[]" class="form-control-file">
                        @if ($errors->has('source.*'))
                            <p class="text-danger">{{$errors->first('source.*')}}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Video</label>
                        <input type="file" name="video1" class="form-control-file">
                        @if ($errors->has('video'))
                            <p class="text-danger">{{$errors->first('video')}}</p>
                        @endif
                    </div>
                    <div class="form-group" id="update_hashtag_auto"></div>

                    <div class="form-group">
                        <label for="">Message(Caption)</label>
                        <input type="text" name="message" class="form-control" placeholder="Type your message">
                        @if ($errors->has('message'))
                            <p class="text-danger">{{$errors->first('message')}}</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="">Post on
                            <small class="text-danger">
                                * Can be Scheduled too </small>
                            <input type="date" name="date" class="form-control">
                        </label>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Post</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript">
      $(".select-multiple").select2({ width: "100%" });
      $(document).on("click", ".openmodalphoto", function(e) {
        e.preventDefault();
        const $action_url = "{{ route('social.post.getimage',$id) }}";
        jQuery.ajax({
          type: "GET",
          url: $action_url,
          dataType: "html",
          success: function(data) {
            const div = document.getElementById("contextimage");
            div.innerHTML = data;
          }
        });
      });

    </script>

@endsection
