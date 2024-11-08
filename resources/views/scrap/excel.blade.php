@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Scrap Excel Files</h2>
            <p>Please Select File You Want To Import</p>
            @if(Session::has('message'))
                <br>
                <div class="alert alert-info">
                    {{ Session::get('message') }}
                </div>
            @endif
        </div>
        <div class="col-md-12">
            <form method="post" action="{{ action([\App\Http\Controllers\ScrapController::class, 'excel_store']) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="query">Select Excel File</label>
                    <input class="form-control" type="file" id="file" name="file" placeholder="Select File...">
                </div>

                <div class="form-group">
                    <button class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2>Previous Imports</h2>
        </div>
        <div class="col-md-12">
            <div class="text-center">
                <div class="text-center">
                    {!! $products->links() !!}
                </div>
            </div>
            <div class="row">
                @if($products->count())
                    @foreach($products as $product)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-image">
                                    <img style="width: 100%;" src="{!! $product->images[0] ? asset('uploads/social-media/'.$product->images[0]) : 'http://lorempixel.com/555/300/black' !!}">
                                </div><!-- card image -->

                                <div class="card-content">
                                    <span class="card-title">
                                        <div style="font-size: 18px;">
                                            {{ $product->title }}
                                        </div>
                                        <div style="font-size: 14px;">
                                            <strong>{{ $product->brand ? $product->brand->name : 'N/A' }}</strong>
                                        </div>
                                        <div style="font-size: 14px;">
                                            <strong>{!! $product->price ?? 'N/A' !!}</strong>
                                        </div>
                                    </span>
                                </div>
                                <div class="card-action">
                                    <p><a href="{{$product->url}}">Visit Product Page</a></p>
                                    <p>
                                        {!! $product->description !!}
                                    </p>
                                    @if ($product->properties)
                                        <strong>Properties</strong>
                                        <ul style="list-style: none;padding-left: 10px">
                                            @foreach($product->properties as $key=>$property)
                                                <li><strong>{{ ucfirst($key) }}</strong>: <strong class="text-info">
                                                        @if (is_array($property))
                                                            @foreach($property as $item)
                                                                @if ($loop->last)
                                                                    {{ $item }}
                                                                @else
                                                                    {{ $item . ', ' }}
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            {{ ucfirst($property)  }}
                                                        @endif
                                                    </strong></li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <h3 class="text-center m-5">
                        There are no scraped images for this website at the moment.
                    </h3>
                @endif
            </div>
            <div class="text-center">
                {!! $products->links() !!}
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
