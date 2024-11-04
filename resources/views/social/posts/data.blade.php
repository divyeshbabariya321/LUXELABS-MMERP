<style>
    .modal-content .modal-header .close { margin: 0 0 0 auto !important; padding: 0; }
</style>


@foreach ($posts as $post)
    <tr>
        <td>
            <button type="button" class="btn post-images" title="View Images" data-id="{{ $post->id }}">
                <i class="fa fa-eye"></i>
            </button>
        </td>
        <td>
            {{$post->account->name}}
        </td>
        <td>@if(isset($post->account->storeWebsite))
                {{ $post->account->storeWebsite->title }}
            @endif
        </td>
        <td>{{ $post->account->platform }}</td>
        <td>{{ $post->post_body }}</td>
        <td>{{ $post->translated_caption }}</td>
        <td>{{ $post->translated_caption_score }}</td>
        {{-- <td>{{ get_translation($post->account->page_language ? $post->account->page_language : 'en', $post->post_body) }}</td> --}}
        <td>{{ $post->hashtag }}</td>
        <td>{{ $post->translated_hashtag }}</td>
        {{-- <td></td> --}}
        {{-- <td>{{ $post->hashtag ? get_translation($post->account->page_language ? $post->account->page_language : 'en', $post->hashtag) : null }}</td> --}}
        <td>{{ $post->account->page_language }}</td>
        <td>{{ $post->translation_approved_by ??'-' }}</td>
        <td>{{ \Carbon\Carbon::parse($post->created_at)->format('d-m-y h:m') }}</td>
        <td>
            @if (isset($post->status) && $post->status == 1)
                Posted
            @elseif (isset($post->status) && $post->status == 2)
                Hold For Approval
            @else
                Error
            @endif
        </td>
        <td>
            @if ($post->status == 2)
                <a href="javascript:" data-id="{{ $post->id }}" class="translation-approval">
                    <i class="fa fa-check" title="Approve"></i>
                </a>
            @endif
            <a href="javascript:" data-id="{{ $post->id }}" class="account-history">
                <i class="fa fa-history" title="History"></i>
            </a>
            @if($post->ref_post_id)
                <a href="{{ route('social.account.comments',$post->id) }}">
                    <i class="fa fa-envelope" aria-hidden="true" title="comment"></i>
                </a>
            @endif

            <a href="javascript:" data-id="{{ $post->id }}" class="post-delete">
                <i class="fa fa-trash-o" title="Delete Post"></i>
            </a>

            @if (($post->translated_caption) && !$post->translated_caption_score)
                <a href="javascript::void(0);" data-id="{{ $post->id }}" class="btn-get-translate-score">
                    <i class="fa fa-dashboard" aria-hidden="true"></i>
                </a>
            @endif
        </td>
    </tr>
@endforeach
{{$posts->appends(request()->except("page"))->links()}}

<div id="post-image-modal" class="modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Post Images</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body" id="record-content">

            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click','.post-images',function(){
        id = $(this).data('id');
        $.ajax({
            method: "GET",
            url: "{{ url('social/post/get-post-images/') }}/" + id,
            dataType: "html",
            success: function(response) {
            
                $("#post-image-modal").modal('show');
                $("#record-content").html(response);
        
            }
        });
    });
</script>