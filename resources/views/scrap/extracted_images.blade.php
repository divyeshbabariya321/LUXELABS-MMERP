@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if (isset($downloaded))
                <div class="image-extracted-title mb-5">
                    <a cx href="{{ url()->previous() }}"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                    <h1>Images are Downloaded Successfully and saved on lifestyle grid.</h1>
                </div>
                @else
                <div class="image-extracted-title">
                    <a href="{{ url()->previous() }}"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                    <h1>Images To be Extracted</h1>
                </div>
                <p class="image-extracted-content">The following images will be extracted.</p>
                @endif
            </div>
            <div class="col-md-12">
                @if (isset($downloaded))
                <div class="row">
                    <div class="col-12">
                        <div class="img-wrapper-grid">
                            @foreach($images as $image)
                            <div>
                                <div class="img-wrapper">
                                <img src="{{ asset('uploads/social-media/'. $image) }}" class="img-responsive">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <form action="{{ action([\App\Http\Controllers\ScrapController::class, 'downloadImages']) }}" method="post">
                    @csrf
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <h2 class="img-section-title pb-3">Google Images</h2>
                            </div>
                            <div class="col-12">
                                <div class="img-wrapper-grid">
                                    @foreach($googleData as $key=>$image)
                                    <div class="cursor-pointer">
                                        <label class="position-relative mb-0 d-block google-image" for="google_{{$key}}">
                                    <span class="img-wrapper">
                                        <img src="{{ $image }}" class="img-responsive">
                                    </span>
                                            <input id="google_{{$key}}" type="checkbox" value="{{ $image }}" name="data[]">
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h2 class="img-section-title py-3">Pinterest Images</h2>
                            </div>
                            <div class="col-12">
                                <div class="img-wrapper-grid">
                                    @foreach($pinterestData as $key=>$image)
                                    <label class="position-relative mb-0 d-block google-image" for="pin_{{$key}}">
                                    <span class="img-wrapper">
                                        <img src="{{ $image }}" class="img-responsive">
                                    </span>
                                        <input id="pin_{{$key}}" type="checkbox" value="{{ $image }}" name="data[]">
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-4">
                                <button class="btn btn-primary my-4">Download Selected Images</button>
                            </div>
                        </div>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/media-card.css') }} ">
@endsection

@section('scripts')
    <script>
        var cid = null;
        $(function(){

            $('.show-details').on('click',function() {
                let id = $(this).attr('data-pid');
                let post_id = $(this).attr('data-media-id');

                $.ajax({
                    url: "{{ action([\App\Http\Controllers\InstagramController::class, 'getComments']) }}",
                    data: {
                        post_id: post_id
                    },
                    success: function(response) {
                        $('.reveal-'+id+' .comments-content').html('');
                        response.forEach(function (comment) {
                            var commentHTML = '<div class="comment text-justify m-2 mb-3" data-cid="'+comment.id+'">';
                            commentHTML += '<span><button data-pid="'+id+'" data-username="'+comment.username+'" data-cid="'+comment.id+'" type="button" class="close reply-to-comment" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>';
                            commentHTML+= '<span class="text-info">@'+comment.username+'</span>';
                            commentHTML += '<span style="display: block">'+comment.text+'</span>';
                            let repliesHTML = '<div class="replies-'+comment.id+'" style="margin: 5px 0 5px 10px; border-left:2px solid #DDD;">';
                            if (comment.replies !== []) {
                                comment.replies.forEach(function(reply) {
                                    repliesHTML += '<p style="margin: 5px 20px 5px 5px;">';
                                    repliesHTML += '<span class="text-info">@'+reply.username+'</span>';
                                    repliesHTML += '<span>'+reply.text+'</span>';
                                    repliesHTML += '</p>';
                                });
                            }
                            repliesHTML += '</div>';
                            commentHTML += repliesHTML;
                            commentHTML += '</div>';
                            $('.reveal-'+id+' .comments-content').prepend(commentHTML);
                        })
                    },
                    error: function() {
                        $('.reveal-'+id+' .comments-content').html('<p style="text-align: center;font-weight: bolder">We could not load comments at the moment. Please try again later.</p>');
                    },
                    beforeSend: function () {
                        $('.reveal-'+id).slideToggle('slow');
                        $('.reveal-'+id+' .comments-content').html('<p style="text-align: center"><img style="width:50px" src="/images/loading2.gif">Loading Comments...</p>');
                    }
                });


            });

            $('body').on('click', '.reply-to-comment', function() {
                let commentId = $(this).attr('data-cid');
                let username = $(this).attr('data-username');
                let pid = $(this).attr('data-pid');
                cid = commentId;
                $('.reply-'+pid).val('@'+username);
                $('.reply-'+pid).focus();
            });

            $('.reply').keypress(function (event) {
                if (event.keyCode == 13) {
                    let reply = $(this).val();
                    let comment_id = cid;
                    cid = null;
                    $(this).val('');
                    let id = $(this).attr('data-pid');
                    let self = this;
                    let postId = $(this).attr('data-post-id');
                    $.ajax({
                        url: "{{ action([\App\Http\Controllers\InstagramController::class, 'postComment']) }}",
                        type: 'post',
                        dataType: 'json',
                        data: {
                            message: reply,
                            post_id: postId,
                            comment_id: comment_id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                if (comment_id == null) {
                                    var commentHTML = '<div class="comment text-justify m-2 mb-3" data-cid="'+response.id+'">';
                                    commentHTML += '<span><button data-username="'+response.username+'" data-cid="'+response.id+'" type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>';
                                    commentHTML+= '<span class="text-info">@'+response.username+'</span>';
                                    commentHTML += '<span style="display: block">'+response.text+'</span></div>';
                                    $('.reveal-'+id+' .comments-content').append(commentHTML);
                                } else {
                                    let repliesHTML = '<p style="margin: 5px 20px 5px 5px;">';
                                    repliesHTML += '<span class="text-info">@'+response.username+'</span>';
                                    repliesHTML += '<span>'+response.text+'</span>';
                                    repliesHTML += '</p>';
                                    $('.replies-'+comment_id).append(repliesHTML);
                                    comment_id = null;
                                }
                                $('.count-for-'+id).html(parseInt($('.count-for-'+id).html())+1);
                                $(".s-d-"+id).attr('data-comment-ids', $(".s-d-"+id).attr('data-comment-ids')+','+response.id);
                            }
                        },
                        error: function() {
                            alert("There was an unknown error saving this reply.");
                            $('.s-d-'+id).click();
                        },
                        complete: function () {
                            $(self).removeAttr('disabled');
                        },
                        beforeSend: function () {
                            $(self).attr('disabled', 'disabled');
                        }
                    });
                }
            });

            $('.card-reveal .close').on('click',function(){
                $(this).parent().slideToggle('slow');
            });
        });


    </script>
@endsection