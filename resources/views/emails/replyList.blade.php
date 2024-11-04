<select class="form-control" name="" id="reply" onchange="setAsReply(this);">
    <option value="">Select Reply</option>
    @foreach ($data as $reply)
        <option value="{{ $reply->reply }}">{{ $reply->reply }}</option>
    @endforeach
</select>