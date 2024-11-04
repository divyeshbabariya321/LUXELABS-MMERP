<style>
    .ui-front {
        z-index: 99999;
    }
</style>

<div id="postCreateModal" class="modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="record-content">
<form id="postCreateForm" method="POST" enctype="multipart/form-data" >
    @csrf
    <input type="hidden" id="configid" name="config_id" value="{{$id}}" />
    <div class="modal-header">
        <h4 class="modal-title">Social Post</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
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
                    @if($website['id'] != $id)
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
            <label>Hashtags</label>
            <input type="text" name="hashtags" id="hashtags" class="form-control" placeholder="#Hashtags...">
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
        <button type="submit" class="btn btn-secondary" id="saveAndPreviewBtn">Save</button>
    </div>
</form>
        </div>
    </div>
</div>

@include('social.posts.modals.preview')
        
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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

    $(document).ready(function() {
        $("#hashtags").autocomplete({
            source: function(request, response) {
                // Send an AJAX request to the server-side script
                $.ajax({
                    url: '{{ route('social.posts.get_hashtags') }}',
                    dataType: 'json',
                    data: {
                        term: request.term // Pass user input as 'term' parameter
                    },
                    success: function (data) {
                        data.forEach((element) => {
                            Object.values(element).map(function(key) {
                                
                            });  
                        });
                        var transformedData = Object.values(data).map(function(key) {
                            console.log(key)
                            return {
                                label: key,
                                value: key,
                                id: key
                            };
                        });
                        response(transformedData); // Populate autocomplete suggestions with label, value, and id
                    }
                });
            },
            select: function(event, ui) {
                $('#hashtags').val(ui.item.value);
            }

        });
       
    });

    $(document).ready(function() {
        $('#postCreateForm').on('submit', function(e){
            e.preventDefault();

            // Send AJAX request to create post and get preview content
            $.ajax({
                url: "{{ route('social.post.preview') }}",
                method: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    $('#postCreateModal').modal('hide');
                    openPreviewModal(response.previewContent);
                },
                error: function(error) {
                    console.error('Error creating post:', error);
                }
            });
        });
    });

    function openPreviewModal(previewContent) {
        $('#previewContent').html(previewContent);
        $('#postPreviewModal').modal('show');
    }

    $(document).ready(function() {
        $('#submitPostButton').click(function(e) {
            e.preventDefault();

            // Send AJAX request to create post and get preview content
            $.ajax({
                url: "{{ route('social.post.store') }}",
                method: 'POST',
                data: new FormData($('#postCreateForm')[0]),
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    location.reload();
                },
                error: function(error) {
                    console.error('Error creating post:', error);
                }
            });
        });
    });
</script>